<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reviewer\ListRequest;
use App\Http\Requests\Reviewer\ReviewProductRequest;
use App\Http\Requests\Reviewer\StoreRequest;
use App\Http\Requests\Reviewer\UpdateRequest;
use App\Http\Requests\Reviewer\UpdateRRequest;
use App\Http\Resources\ReviewerCollection;
use App\Http\Resources\ReviewerResource;
use App\Models\Review;
use App\Models\Reviewer;
use App\Models\Stage;
use App\Services\Review\NotiifyStage;
use App\Services\Reviewer\CreateService;
use App\Services\Reviewer\HandleMessage;
use App\Services\Reviewer\InfoService;
use App\Services\Reviewer\ListService;
use App\Services\Reviewer\UpdateRService;
use App\Services\Reviewer\UpdateService;
use App\Services\User\GetReviewCreatorByReviewer;

class ReviewerController extends Controller
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
            $new_reviewer = (new CreateService($validated,  $user))->run();
            $client_resource = new ReviewerResource((new InfoService($new_reviewer->id))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - ' . $exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'New Reviewer created', 'data' =>  $client_resource], 201);
    }

    public function get(ListRequest $request)
    {
        $validated = $request->validated();
        try {
            $review_collection = new ReviewerCollection((new ListService($validated))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request ' . $exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'List of Reviewers', 'data' =>  $review_collection], 200);
    }

    public function show($id)
    {
        try {
            $client_resource = new ReviewerResource((new InfoService($id))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - ' . $exception->getMessage(), 'data' => $exception], $exception->getCode());
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
            return response()->json(['status' => false, 'mesage' => 'Error processing request - ' . $exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'Review update successful', 'data' =>  $update], 200);
    }

    public function updateR(UpdateRRequest $request, $id)
    {
        $validated = $request->validated();
        $user = \Auth::user();
        try {

            $reviewer = Reviewer::where('user_id', $user->id)->where('id', $id)->firstOrFail();

            $creator = (new GetReviewCreatorByReviewer($reviewer))->run();
            $update = (new UpdateRService($reviewer, $validated))->run();

            $message = (new HandleMessage($creator, $user))->run();
            $reviewer_now = Reviewer::where('user_id', $user->id)->where('id', $id)->where('review', '==', null)->get()->count();
            if ($reviewer_now > 0) {
                $all_reviewer = Reviewer::where('stage_id', $reviewer->stage_id)->get()->count();
                $complete_reviewer = Reviewer::where('stage_id', $reviewer->stage_id)->where('review', '!=', null)->get()->count();
                if ($all_reviewer == $complete_reviewer) {
                    $this->handleSendToanotherStage($creator);
                } else {
                }
            }
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - ' . $exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'Your review have been submited', 'data' =>  $update], 200);
    }


    public function reviewProduct(ReviewProductRequest $request, $id)
    {
        $validated = $request->validated();
        $user = \Auth::user();
        $company_id = (\Auth::user()->companies)[0]->id;
        try {
            $Ureviewer = null;
            $review = Review::with(['review_stages', 'review_stages.reviewers'])->where('company_id', $company_id)->where('product_id', $id)->firstOrFail();
            foreach ($review->review_stages as $stage) {
                foreach ($stage->reviewers as $reviewer) {
                    if ($reviewer->user_id == $user->id) {
                        $Ureviewer = $reviewer;
                    }
                }
            }
            if ($Ureviewer == null) {
                return response()->json(['status' => true, 'message' => 'Sorry but you are not part of this review process'], 200);
            }
            $creator = (new GetReviewCreatorByReviewer($Ureviewer))->run();
            
            $update = (new UpdateRService($reviewer, $validated))->run();

            $message = (new HandleMessage($creator, $user))->run();
            $reviewer_now = Reviewer::where('user_id', $user->id)->where('id', $id)->where('review', '==', null)->get()->count();
            if ($reviewer_now > 0) {
                $all_reviewer = Reviewer::where('stage_id', $reviewer->stage_id)->get()->count();
                $complete_reviewer = Reviewer::where('stage_id', $reviewer->stage_id)->where('review', '!=', null)->get()->count();
                if ($all_reviewer == $complete_reviewer) {
                    $this->handleSendToanotherStage($creator);
                } else {
                }
            }
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - ' . $exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'Your review have been submited', 'data' =>  $update], 200);
    }



    public function getReviewProduct($id)
    {
        $user = \Auth::user();
        $company_id = (\Auth::user()->companies)[0]->id;
        try {
            $Ureviewers = [];
            $review = Review::with(['review_stages', 'review_stages.reviewers'])->where('company_id', $company_id)->where('product_id', $id)->firstOrFail();
            foreach ($review->review_stages as $stage) {
                foreach ($stage->reviewers as $reviewer) {
                    if($reviewer->review !== null || $reviewer->review !== null){
                        array_push($Ureviewers, $reviewer);
                    }
                }
            }   
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - ' . $exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'List of reviews', 'data' =>  $Ureviewers], 200);
    }



    public function handleSendToanotherStage($creator)
    {
        $user = \Auth::user();
        $current_stage = $creator->stage;
        $current_position = 0;
        $stages = Stage::where('review_id', $creator->review->id)->get()->reverse()->values();
        for ($x = 0; $x <= $creator->review->stages; $x++) {
            if ($stages[$x]->id == $current_stage->id) {
                $current_position =  $x;
                break;
            }
        }
        if ($current_position + 1 ==  $creator->review->stages) {
        } else if ($current_position + 1 <  $creator->review->stages) {
            $send_mail = (new NotiifyStage($creator->review->id, $current_position + 1, $creator->creator))->run();
        } else {
        }
    }
}
