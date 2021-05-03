<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use App\Http\Requests\Profile\UpdateRequest;
use App\Services\Profile\UpdateService;
use App\Http\Resources\UserResource;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Services\Profile\UpdatePassword;
use App\Http\Requests\Profile\ResetPasswordRequest;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Notifications\User\ChangePassword as ChangePasswordNotification;
use App\Notifications\User\UpdateProfile as UpdateProfileNotification;

class ProfileController extends Controller
{
    // use CompanyIdTrait;

    public function __construct()
    {
    }

    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();
        try {
            (new UpdateService($user, $validated))->run();
            $resource = new UserResource(User::find($user->id));
            \Notification::send(\Auth::user(), new UpdateProfileNotification());
            return response()->json(['status' => true, 'data' => $resource, 'message' => 'profile update successful'], 200);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false,  'message' => 'Error processing request'], 500);
        }
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $validated = $request->validated();
        $user = \Auth::user();
        $company_id = (\Auth::user()->companies)[0]->id;
        try {
            (new UpdatePassword($user, $validated))->run();
            $resource = new UserResource($user);
            \Notification::send(\Auth::user(), new ChangePasswordNotification());
            return response()->json(['status' => true, 'data' => $resource, 'company_id' => $company_id, 'message' => 'profile update successful'], 200);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false,  'message' => 'Error processing request'], 500);
        }
    }

    public function processResetPassword(ResetPasswordRequest $request)
    {
        $validated = $request->validated();
        $user = User::findOrFail(decrypt($validated['token']));
        try {
            (new UpdatePassword($user, $validated))->run();
            return response()->json(['status' => true, 'message' => 'successful'], 200);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false,  'message' => 'Error processing request'], 500);
        }
    }


    public function getAllNotification()
    {
        $user = \Auth::user();
        try {

            $notification  = \DB::table('notifications')->where('read_at', Null)->where([['notifiable_id', \Auth::user()->id], ['read_at', Null]])->get();

           // $notification =  Notification::where('read_at', Null)->where('notifiable_id', \Auth::user()->id)->get();
            return response()->json(['status' => true, 'message' => 'successful', 'data' => $notification], 200);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false,  'message' => 'Error processing request'], 500);
        }
    }


    public function markAsRead()
    {
        $user = \Auth::user();
        $user->unreadNotifications()->update(['read_at' => now()]);
        return response()->json(array(
            'code' =>  200,
        ), 200);
    }

    public function clearNotifications()
    {
        $user = \Auth::user();
        $user->notifications()->delete();
        return response()->json(array(
            'code' =>  204,
        ), 204);
    }


    public function markNotificationAsRead($id)
    {
        $notification =  Notification::where('notifiable_id', \Auth::user()->id)->get()[0];
        $notification->read_at =  \Carbon\Carbon::today();
        $notification->save();
        return $notification;
    }
}
