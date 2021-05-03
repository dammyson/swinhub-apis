<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\ListRequest;
use App\Http\Requests\Comment\StoreRequest;
use App\Http\Resources\PostCommentCollection;
use App\Http\Resources\PostCommentResource;
use App\Services\Comment\CreateService;
use App\Services\Comment\ListService;

class PostCommentController extends Controller
{
   

    public function create(StoreRequest $request)
    {
        $validated = $request->validated();

        try {
            $new_client = (new CreateService($validated))->run();
            $client_resource = new PostCommentResource($new_client);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'New Post created', 'data' =>  $client_resource], 201);

    }

    public function get(ListRequest $request)
    {

        $validated = $request->validated();
       try {
            $client_collection = new PostCommentCollection((new ListService($validated['post_id']))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'List of comments', 'data' =>  $client_collection], 200);
       
    }



}