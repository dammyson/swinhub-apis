<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\ListRequest;
use App\Http\Requests\Review\StoreRequest;
use App\Http\Requests\Review\UpdateRequest;
use App\Http\Resources\ReviewCollection;
use App\Http\Resources\ReviewResource;
use App\Models\Product;
use App\Models\Review;
use App\Models\Reviewer;
use App\Models\Stage;
use App\Models\User;
use App\Services\Review\CreateService;
use App\Services\Review\GetUserByStage;
use App\Services\Review\HandleMessage;
use App\Services\Review\InfoService;
use App\Services\Review\ListService;
use App\Services\Review\NotiifyStage;
use App\Services\Review\UpdateService;

class ReviewController extends Controller
{
    // use CompanyIdTrait;

    public function __construct()
    {
    }

    public function create(StoreRequest $request)
    {
        $validated = $request->validated();
        try {
            $user = \Auth::user();
            $new_client = (new CreateService($validated,  $user))->run();
            $client_resource = new ReviewResource((new InfoService($new_client->id))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'New Review process created', 'data' =>  $client_resource], 201);
    }

    public function get(ListRequest $request)
    {
        $validated = $request->validated();
        try {
            $company_id = (\Auth::user()->companies)[0]->id;
            $review_collection = new ReviewCollection((new ListService($company_id))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'List of Reviews', 'data' =>  $review_collection], 200);
    }

    public function show($id){
        try {
            $client_resource = new ReviewResource((new InfoService($id))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - '.$exception->getMessage(), 'data' => $exception], $exception->getCode());
        }
        return response()->json(['status' => true, 'message' => 'showing review details', 'data' =>  $client_resource], 200);
    }


    public function update(UpdateRequest $request, $id)
    {
        $validated = $request->validated();
        try {
            $client = (new InfoService($id))->run();
            $update = (new UpdateService($client, $validated))->run();
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'Review update successful', 'data' =>  $update], 200);

    }


    public function start($id)
    {
        try {
            $user = \Auth::user();
            $send_mail = (new NotiifyStage($id, 0, $user,))->run();
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'You have started the review process',], 200);

    }




}
