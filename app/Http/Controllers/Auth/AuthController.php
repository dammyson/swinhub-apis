<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Http\Requests\Auth\CreateRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Services\User\RegistrationService;
use App\Http\Resources\UserResource;
use App\Services\User\ForgetPassword;
use App\Support\Enum\UserStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\Profile\UpdatePassword;

class AuthController extends Controller
{
    /**
     * Create a new authentication controller instance.
     * @param UserRepository $users
     */

    public function create(CreateRequest $request)
    {
        $validated = $request->validated();
        try {
            $new_user = new RegistrationService($validated);
            $registered_user = $new_user->run();
            $user = new UserResource($registered_user);
            return response()->json(['status' => true, 'data' => $user, 'message' => 'registration successful'], 201);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false,  'message' => 'Error processing request'], 500);
        }
    }
    /**
     * Handle a login request to the application.
     * @param LoginRequest $request
     */
    public function postLogin(LoginRequest $request)
    {
        $validated = $request->validated();
        if (auth()->attempt(['email' =>  $validated['email'], 'password' =>  $validated['password']])) {
            $user = Auth::user();
            $token = $user->createToken($validated['email'])->accessToken;
            $user->last_login = \Carbon\Carbon::now();
            $user->save();
            $data=[
                "user" => $user,
                "company" => (Auth::user()->companies)[0],
                "role" => Auth::user()->getRoleNames() 
            ];
           
            $first_time_login = false;
            if ($user->first_time_login) {
                $first_time_login = true;
            }
            return response()->json(['status' => true, 'message' => 'Login successful','token' => $token, 'data' => $user,  'first_time_login' => $first_time_login, ], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'UnAuthorised'], 401);
        }
    }

    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $validated = $request->validated();
        try {
            $forget_password = new ForgetPassword($validated);
            $send_mail_user = $forget_password->run();
            return response()->json(['status' => true, 'message' => $send_mail_user], 200);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false,  'message' => 'Error processing request'], 500);
        }
    }

    public function CheckCanUpdatePassword(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            return response()->json(['status' => false, 'message' => 'Invalid/Expired link, contact admin'], 401);
        }
        $user = User::findOrFail($id);
        if ($user->status !== UserStatus::ACTIVE) {
            return response()->json(['status' => false, 'message' => 'You can not chane password your account has not been completed'], 401);
        }
        return response()->json(['status' => true, 'message' => 'This link is valid'], 200);
    }


    public function updatePassword(UpdatePasswordRequest $request, $id)
    {
        $validated = $request->validated();
        $user = User::findOrFail($id);
        try {
            (new UpdatePassword($user, $validated))->run();
            return response()->json(['status' => true, 'message' => 'Password changed not proceed to login'], 200);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false,  'message' => 'Error processing request'], 500);
        }
    }

    /**
     * Create a new authentication controller instance.
     * @param UserRepository $users
     */

    public function activate($id)
    {
        $user = User::findOrFail($id);
        $user->status = UserStatus::ACTIVE;
        $user->save();
        return response()->json(['status' => true, 'data' => $user, 'message' => 'Activation successful'], 200);
    }
}
