<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UserInviteRequest;
use App\Services\User\InviteService;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Http\Requests\Users\CompleteRegistrationRequest;
use App\Services\User\CompleteService;
use Auth;
use App\Support\Enum\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\Company;
use App\Http\Resources\UserCollection;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Requests\Users\UpdateUserStatusRequest;
use App\Http\Resources\SaleCollection;
use App\Http\Resources\SaleResource;
use App\Models\Product;
use App\Services\User\UpdateService;
use App\Services\User\UpdateStatusService;
use App\Services\Mail\InviteUserMailFormat;
use App\Services\User\DeleteService;
use App\Models\Role;
use App\Models\SaleManager;
use App\Notifications\ContactNotification;
use App\Notifications\ContactSalesNotification;
use App\Notifications\User\UpdateStatus as UpdateStatusNotification;
use App\Notifications\SendUserInvitationMail;
use Illuminate\Support\Facades\Notification;


class UserController extends Controller
{

    /*******************************
     *  BELOW ARE THE API ACTIONS
     *******************************/

    /**
     * UserInviteRequest $request
     */





    public function create(UserInviteRequest $request)
    {
        $validated = $request->validated();
        $user = \Auth::user();
        try {
            $invite_user = new InviteService($validated, $user);
            $new_user = $invite_user->run();
            return response()->json(['status' => true, 'data' => new UserCollection($new_user), 'message' => 'User invitation successful'], 200);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false,  'message' => 'Error processing request'], 500);
        }
    }


    public function list()
    {
        $user = \Auth::user();
        try {
            $company_user = Company::with('users')->findOrFail($user->companies[0]->id);
            $user_list = new UserCollection($company_user->users);
            return response()->json(['status' => true, 'data' => $user_list, 'message' => 'User list'], 200);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false,  'message' => 'Error processing request'], 500);
        }
    }

    /**
     * Update the user
     */

    public function update(UpdateUserRequest $request, $id)
    {

        $validated = $request->validated();
        try {
            $update_user_service = new UpdateService($validated,  $id);
            $updated_user = $update_user_service->run();
            return response()->json(['status' => true, 'data' => new UserResource($updated_user), 'message' => 'User Updated'], 200);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false,  'message' => 'Error processing request'], 500);
        }
    }

    public function updateStatus(UpdateUserStatusRequest $request, $id)
    {
        $validated = $request->validated();
        try {
            $update_user_service = new UpdateStatusService($validated,  $id);
            $updated_user = $update_user_service->run();
            \Notification::send(\Auth::user(), new UpdateStatusNotification($validated['status']));
            return response()->json(['status' => true, 'data' => new UserResource($updated_user), 'message' => 'User Updated'], 200);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false,  'message' => 'Error processing request'], 500);
        }
    }

    public function CheckCanCompleteAccount(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            return response()->json(['status' => false, 'message' => 'Invalid/Expired link, contact admin'], 401);
        }
        $user = User::findOrFail($id);
        if ($user->status !== UserStatus::UNCONFIRMED) {
            return response()->json(['status' => false, 'message' => 'You have already completed your registration, please login with your credentials'], 401);
        }
        return response()->json(['status' => true, 'message' => 'This link is valid to go'], 200);
    }

    public function completeRegistrationAccount(CompleteRegistrationRequest $request, $id)
    {
        $validated = $request->validated();
        $completed_user = new CompleteService($validated, $id);
        $user = $completed_user->run();
        if (auth()->attempt(['email' =>  $user->email, 'password' =>  $validated['password']])) {
            $user = Auth::user();
            $token = $user->createToken($user->email)->accessToken;
            $user->last_login = \Carbon\Carbon::now();
            $user->save();
            return response()->json(['status' => true, 'data' => $user, 'message' => 'Login successful', 'token' => $token,], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'UnAuthorised'], 401);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        try {
            $delete_action = (new DeleteService($user))->run();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - ' . $exception->getMessage(), 'data' => $exception], $exception->getCode());
        }
        return response()->json(['status' => true, 'message' => 'User deletion successful', 'data' =>  $delete_action], 200);
    }

    /**
     * Return a list of user that the currently logged in user has permission to view
     */
    public function listRoles()
    {
        $roles = Role::get();
        return response()->json(['status' => true, 'data' => $roles, 'message' => 'Role list'], 200);
    }

    public function resend(Request $request, $id)
    {
        $invited_user = User::findOrFail($id);
        $user = \Auth::user();
        try {
            $invite_user_mail_format = new InviteUserMailFormat($invited_user, $user->first_name);
            $new_user = $invite_user_mail_format->run();
            $invited_user->notify(new SendUserInvitationMail($new_user));
            return response()->json(['status' => true, 'data' => $invited_user, 'message' => 'Invitaion Link resent'], 200);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false,  'message' => 'Error processing request'], 500);
        }
    }

    public function addSales(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|string|unique:sale_managers',
            'country' => 'required|string',
            'region' => 'required|string',
        ]);

        try {
            $sales_user_to = User::findOrFail($request->user_id);
            $company_id = (\Auth::user()->companies)[0]->id;
            $sales_manager = new SaleManager();
            $sales_manager->id = uniqid();
            $sales_manager->company_id = $company_id;
            $sales_manager->user_id = $request->user_id;
            $sales_manager->region =  $request->region;
            $sales_manager->country = $request->country;
            $sales_manager->save();
            $sales_resource = new SaleResource($sales_manager);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - ' . $exception->getMessage(), 'data' => $exception], $exception->getCode());
        }
        return response()->json(['status' => true, 'data' => $sales_resource, 'message' => 'Sales manager Added'], 200);
    }

    public function getSales()
    {
        try {
            $company_id = (\Auth::user()->companies)[0]->id;
            $sales = SaleManager::where('company_id',  $company_id)->get();
            $sales_resource = new SaleCollection($sales);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - ' . $exception->getMessage(), 'data' => $exception], $exception->getCode());
        }

        return response()->json(['status' => true, 'data' => $sales_resource, 'message' => 'Sales manager List'], 200);
    }


    public function contactSales(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|string',
            'message' => 'required|string',
            'country' => 'required|string',
            'region' => 'required|string',
        ]);

        $autuser = \Auth::user();
        $product = Product::findorfail($request->product_id);
        $salem = SaleManager::where("company_id", $product->company_id)->get();
      
        $sale_id = "";
        foreach ($salem as $sales) {
            if ($sales->region ==  $request->region) {
                $sale_id = $sales->id;
            }
        }

        if ($sale_id !== "") {
            $user = User::findOrFail($sale_id);
            $mail_content_array = array(
                "sender" =>  $autuser->first_name . " " . $autuser->last_name,
                "message" => $request->message,
                "receiver" => "SWiN Management",
                "mail_subject" => 'Contact Sales Manager',
                "app_subject" =>  'SWiN',
            );

            Notification::send($user, new ContactSalesNotification($mail_content_array));

            return response()->json(['status' => true, 'message' => 'Sales manager Added'], 200);
        } else {
            return response()->json(['status' => true, 'message' => 'Sales manager for this region is not available'], 200);
        }
    }



    public function contactForm(Request $request)
    {
        $this->validate($request, [
            'full_name' => 'required|string',
            'company_name' => 'required|string',
            'email' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'region' => 'required|string',
            'country' => 'required|string',
        ]);


        $user = new User();
        $user->id = uniqid();
        $user->email = 'info@swinhub.com';
        $user->first_name = uniqid();
        $user->last_name = uniqid();


        $mail_content_array = array(
            "sender" =>  $request->full_name,
            "full_name" => $request->full_name,
            "company_name" => $request->company_name,
            "email" =>  $request->email,
            "address" => $request->address,
            "phone" => $request->phone,
            "region" => $request->region,
            "country" => $request->country,
            "receiver" => "SWiN Management",
            "mail_subject" => 'Software Recommendation',
            "app_subject" =>  'SWiN',
        );


        Notification::send($user, new ContactNotification($mail_content_array));

        return response()->json(['status' => true, 'message' => 'Action completed'], 200);
    }
}
