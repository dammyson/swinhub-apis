<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Company\UpdateCompany;
use App\Models\Company;
use App\Http\Requests\Company\UpdateRequest;
use App\Http\Resources\CompanyResource;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function update(UpdateRequest $request)
    {
        $company_id = (\Auth::user()->companies)[0]->id;
        $company =  Company::findOrFail($company_id);
        try {
            $validated = $request->validated();
            $update_company_service = new UpdateCompany($company, $validated);
            $update_company_service->run();
            $company_updated = new CompanyResource(Company::findOrFail($company_id));
            return response()->json(['status' => true, 'data' => $company_updated, 'message' => 'company update successful'], 200);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(['status' => false,  'message' => 'Error processing request'], 500);
        }
    }
}
