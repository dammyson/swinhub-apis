<?php

namespace App\Services\ProductFile;

use GuzzleHttp\Exception\GuzzleException;
use App\OTP;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


class FileHandler
{
    public function store_image($request)
    {
        try{
            $type = $request->type;
            $user_id = \Auth::id();
            $cover = $request->file('file');
            $extension = $cover->getClientOriginalExtension();

            $directory = '/'.$request->folder;
            
            $filename = $type.'_'.$user_id.'_'.time().'.'.$extension;

            \Storage::disk('public_uploads')->put($directory.'/'.$filename,  \File::get($cover));


            return  $directory.'/'.$filename;
        }catch(\Exception $ex){
            \Log::error('Something is really going wrong.'. $ex);
            return null;
        }
    }
}