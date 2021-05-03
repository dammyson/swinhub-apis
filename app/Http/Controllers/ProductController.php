<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ListRequest;
use App\Http\Requests\Product\SearchRequest;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductPermission;
use App\Models\User;
use App\Notifications\RecommendationNotification;
use App\Services\Product\CanUserReviewService;
use App\Services\Product\CreateService;
use App\Services\Product\InfoService;
use App\Services\Product\ListService;
use App\Services\Product\SearchService;
use App\Services\Product\UpdateService;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use Mail;

use Illuminate\Support\Facades\Notification;

class ProductController extends Controller
{
    // use CompanyIdTrait;

    public function __construct()
    {
    }

    public function create(StoreRequest $request)
    {
        $validated = $request->validated();
        try {
            //$company_id = (\Auth::user()->companies)[0]->id;
            $user = \Auth::user();
            $new_client = (new CreateService($validated,  $user))->run();
            $client_resource = new ProductResource((new InfoService($new_client->id))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'New Product created', 'data' =>  $client_resource], 201);

    }

    public function get(ListRequest $request)
    {
        $validated = $request->validated();
        try {
            $company_id = (\Auth::user()->companies)[0]->id;
            $client_collection = new ProductCollection((new ListService($company_id))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'List of products', 'data' =>  $client_collection], 200);
    }

    public function show($id){
        try {
           $client_resource = new ProductResource((new InfoService($id))->run());
           $can_review = (new CanUserReviewService(\Auth::user(), $id))->run();
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - '.$exception->getMessage(), 'data' => $exception], $exception->getCode());
        }
        return response()->json(['status' => true, 'message' => 'showing product details', 'data' =>  $client_resource, 'can_review' => $can_review], 200);
    }


    public function update(UpdateRequest $request, $id)
    {
        $validated = $request->validated();
        try {
           
            $product = Product::with(['specifications', 'assets'])->findorfail($id);
            $update = (new UpdateService($product, $validated))->run();
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'Product update successful', 'data' =>  $update], 200);
    }

    public function search(SearchRequest $request)
    {
        $validated = $request->validated();
        try {
            $client_collection = new ProductCollection((new SearchService($request))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'search was successful', 'data' =>  $client_collection], 200);
    }


    public function getPublic()
    {
       // $validated = $request->validated();
        try {
            $product_list = Product::latest()->get();
            $client_collection = new ProductCollection($product_list);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'List of products', 'data' =>  $client_collection], 200);
    }


    public function getPublicshow($id)
    {
        try {
            $product = Product::with(['specifications', 'assets'])->findorfail($id);
           
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'List of products', 'data' =>  $product], 200);
    }


    public function recommendProduct(Request $request, $id)
    {
        $this->validate($request, [
            'reciever_name' => 'required|string',
            'reciever_email' => 'required|string',
        ]);

           $autuser = \Auth::user();
           $product = Product::findorfail($id);
          
            $user = new User();
            $user->id = uniqid();
            $user->email = $request->reciever_email;
            $user->first_name = uniqid();
            $user->last_name = uniqid();
            $front_url = env('BASE_LINK', 24).  "softwares/" . $product->id;
            $mail_content_array = array(
                "sender" =>  $autuser->first_name.  " ". $autuser->last_name,
                "action" => "Check",
                "product" =>  $product->name,
                "receiver" => $request->reciever_name,
                "link" => $front_url,
                "mail_subject" =>'Software Recommendation',
                "app_subject" =>  'SWiN',
            );


            Notification::send($user, new RecommendationNotification($mail_content_array));
        
         return response()->json(['status' => true, 'message' => 'Action completed'], 200);
    }


}