<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductAsset\UpdateRequest;
use App\Http\Requests\ProductAsset\StoreRequest;
use App\Http\Requests\ProductAsset\ListRequest;
use App\Http\Requests\ProductAsset\RequestRequest;
use App\Http\Resources\ProductAssetCollection;
use App\Http\Resources\ProductAssetResource;
use App\Models\FilePermission;
use App\Services\ProductAsset\CreateService;
use App\Services\ProductAsset\HandleMessage;
use App\Services\ProductAsset\InfoService;
use App\Services\ProductAsset\ListService;
use App\Services\ProductAsset\UpdateService;
use App\Services\User\GetFileCreatorByFile;
use Illuminate\Http\Request;
class ProductAssetController extends Controller
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
            $new_client = (new CreateService($validated,  $validated['product_id']))->run();
            $client_resource = new ProductAssetResource((new InfoService($new_client->id))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'New Product File created', 'data' =>  $client_resource], 201);

    }

    public function get(ListRequest $request)
    {
        $validated = $request->validated();
        try {
            $client_collection = new ProductAssetCollection((new ListService($validated['product_id']))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request '.$exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'List of products', 'data' =>  $client_collection], 200);

    }

    public function show($id){
        try {
            $client_resource = new ProductAssetResource((new InfoService($id))->run());
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
