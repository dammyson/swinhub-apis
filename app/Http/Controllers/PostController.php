<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\ListRequest;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Services\Post\CreateService;
use App\Services\Post\InfoService;
use App\Services\Post\ListService;

class PostController extends Controller
{
   

    public function create(StoreRequest $request)
    {
        $validated = $request->validated();

        try {
            $new_client = (new CreateService($validated))->run();
            $client_resource = new PostResource((new InfoService($new_client->id))->run());
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
            $client_collection = new PostCollection((new ListService($validated['product_id']))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'List of products', 'data' =>  $client_collection], 200);
       
    }

    public function show($id){
        try {
            $client_resource = new PostResource((new InfoService($id))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'Post Details', 'data' =>  $client_resource], 200);
    }


    public function update(UpdateRequest $request, $id)
    {
        $validated = $request->validated();
       
    }

  


}