<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFile\UpdateRequest;
use App\Http\Requests\ProductSpecification\StoreRequest;
use App\Http\Requests\ProductSpecification\ListRequest;


use App\Http\Resources\ProductSpecificationCollection;
use App\Http\Resources\ProductSpecificationResource;

use App\Services\ProductSpecification\CreateService;
use App\Services\ProductSpecification\InfoService;
use App\Services\ProductSpecification\ListService;
use App\Services\ProductSpecification\UpdateService;


class ProductSpecificationController extends Controller
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
            $new_specification = (new CreateService($validated,  $validated['product_id']))->run();
            $client_resource = new ProductSpecificationCollection((new ListService($validated['product_id']))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'New Product Specifications created', 'data' =>  $client_resource], 201);

    }

    public function get(ListRequest $request)
    {
        $validated = $request->validated();
        try {
            $client_collection = new ProductSpecificationCollection((new ListService($validated['product_id']))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'List of specification', 'data' =>  $client_collection], 200);

    }

    public function show($id){
        try {
            $client_resource = new ProductSpecificationResource((new InfoService($id))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - '.$exception->getMessage(), 'data' => $exception], $exception->getCode());
        }
        return response()->json(['status' => true, 'message' => 'showing product  file details', 'data' =>  $client_resource], 200);
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
        return response()->json(['status' => true, 'message' => 'Product update successful', 'data' =>  $update], 200);

    }
   
}
