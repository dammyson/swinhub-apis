<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stage\ListRequest;
use App\Http\Requests\Stage\StoreRequest;
use App\Http\Requests\Stage\UpdateRequest;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\StageCollection;
use App\Http\Resources\StageResource;
use App\Models\Review;
use App\Services\Stage\CreateService;
use App\Services\Stage\InfoService;
use App\Services\Stage\ListService;
use App\Services\Stage\UpdateService;

class StageController extends Controller
{
    // use CompanyIdTrait;

    public function __construct()
    {
    }

    public function create(StoreRequest $request)
    {
        $validated = $request->validated();

        try {
         
            if (\Arr::has($validated, 'stages')) {
                foreach ($validated['stages'] as $stage) {
                    $new_stage = (new CreateService($stage, $validated['review_id']))->run();
                }
            }
             $review= Review::with(['review_stages', 'review_stages.reviewers'])->findorfail($validated['review_id']);
             $review_resource = new ReviewResource($review);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'New Stage(s) created', 'data' =>  $review_resource], 201);

    }

    public function get(ListRequest $request)
    {
        $validated = $request->validated();
        try {
            $review_collection = new StageCollection((new ListService($validated))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'List of Reviews', 'data' =>  $review_collection], 200);
       

    }

    public function show($id){
        try {
            $stage_resource = new StageResource((new InfoService($id))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - '.$exception->getMessage(), 'data' => $exception], $exception->getCode());
        }
        return response()->json(['status' => true, 'message' => 'showing stage details', 'data' =>  $stage_resource], 200);
    }


    public function update(UpdateRequest $request, $id)
    {
        $validated = $request->validated();
        try {
            $stage = (new InfoService($id))->run();
            $update = (new UpdateService($stage, $validated))->run();
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'Stage update successful', 'data' =>  $update], 200);
    }

}
