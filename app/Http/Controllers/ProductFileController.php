<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFile\UpdateRequest;
use App\Http\Requests\ProductFile\StoreRequest;
use App\Http\Requests\ProductFile\ListRequest;
use App\Http\Requests\ProductFile\RequestRequest;
use App\Http\Resources\ProductFileCollection;
use App\Http\Resources\ProductFileResource;
use App\Models\FilePermission;
use App\Services\ProductFile\CreateService;
use App\Services\ProductFile\FileHandler;
use App\Services\ProductFile\HandleMessage;
use App\Services\ProductFile\InfoService;
use App\Services\ProductFile\ListService;
use App\Services\ProductFile\UpdateService;
use App\Services\User\GetFileCreatorByFile;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;


class ProductFileController extends Controller
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
            $client_resource = new ProductFileResource((new InfoService($new_client->id))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - ' . $exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'New Product File created', 'data' =>  $client_resource], 201);
    }

    public function get(ListRequest $request)
    {
        $validated = $request->validated();
        try {
            $client_collection = new ProductFileCollection((new ListService($validated['product_id'], $validated['category']))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request ' . $exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'List of products', 'data' =>  $client_collection], 200);
    }

    public function show($id)
    {
        try {
            $client_resource = new ProductFileResource((new InfoService($id))->run());
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - ' . $exception->getMessage(), 'data' => $exception], $exception->getCode());
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
            return response()->json(['status' => false, 'mesage' => 'Error processing request - ' . $exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'Product update successful', 'data' =>  $update], 200);
    }


    public function requestAccess(RequestRequest $request, $id)
    {
        $user = \Auth::user();
        $company_id = ($user->companies)[0]->id;
        $permissions = FilePermission::where('product_file_id', $id)->where('company_id', $company_id)->get()->count();
        if ($permissions > 0) {
            return response()->json(['status' => true, 'message' => 'Somebody in your organisation has requested for this file'], 200);
        }
        try {

            $permission = FilePermission::create([
                'company_id' => $company_id,
                'product_file_id' =>  $id,
            ]);
            $creator = (new GetFileCreatorByFile($id))->run();
            $message = (new HandleMessage($creator, $permission, $user))->run();
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - ' . $exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'Access to the file has been requested'], 200);
    }


    public function requestGranted($id)
    {
        $company_id = (\Auth::user()->companies)[0]->id;
        try {
            $permission = FilePermission::where('id', $id)->where('company_id', $company_id)->get()[0];
            $permission->access = 'granted';
            $permission->save();
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['status' => false, 'mesage' => 'Error processing request - ' . $exception->getMessage(), 'data' => $exception], 500);
        }
        return response()->json(['status' => true, 'message' => 'Access to the procuct has been granted'], 200);
    }


    public function creatFile(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlx,xls,pdf,jpeg,png,jpg,mp4,mov,gif|max:9048',
            'folder' => 'required|string'
        ]);

        if ($request->file()) {
            $fileName = time() . '_' . $request->file->getClientOriginalName();
            // save file to azure blob virtual directory uplaods in your container
            $filePath = $request->file('file')->storeAs($request->folder . '/', $fileName, 'azure');
            $uri = preg_replace('#//+#', '/', $filePath);
            $url = 'https://swinfile.blob.core.windows.net/files/' . $uri;
            return response()->json(['status' => true, 'message' => 'File uploaded', 'url' => $url], 200);
        } else {
            return response()->json(['status' => true, 'message' => 'File not uploaded'], 500);
        }
    }

    public function upload_image(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt,xlx,xls,pdf,jpeg,png,jpg,mp4,mov,gif|max:9048',
            'folder' => 'required',
            // 'identifier' => 'required',
        ]);
        //$user = Auth::user();
        $error = $validator->errors()->first();
        if ($validator->fails()) {
            return response()->json(['message' => $error, 'status' => false], 200);
        }
      
        $file_handler = new FileHandler();
        $image_url = $file_handler->store_image($request);
        if ($image_url == null)
            return response()->json(['message' => "Could not upload image", 'status' => false], 200);
        return response()->json(['message' => "File uploaded", 'url' => 'uploads'.chr(47).$image_url, 'status' => true], 200);
    }
}
