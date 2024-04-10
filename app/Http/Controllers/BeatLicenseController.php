<?php

namespace App\Http\Controllers;

use App\Http\Resources\BeatLicenseResource;
use App\Models\Beat;
use App\Models\BeatLicense;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use ZipArchive;


class BeatLicenseController extends Controller
{
    public function getOne(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'beat_id' => 'required|exists:beats,id',
            'license_id' => 'required|exists:licenses,id'
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->messages()], 422);
        }

        $beat_license = BeatLicense::where('beat_id', $request->beat_id)->where('license_id', $request->license_id)->first();
        return response()->json(BeatLicenseResource::make($beat_license), 201);

    }

    public function download($id)
    {
        $beat_license = BeatLicense::where('download_key', $id)->first();

        if(!$beat_license){
            return response()->json(['errors' => 'Beat license not found'], 404);
        }

        $license_path = storage_path('app/public/' . $beat_license->file_url);
        $beat = Beat::find($beat_license->beat_id);

        if(!$beat){
            return response()->json(['errors' => 'Beat not found'], 404);
        }



        $zip = new ZipArchive;
        $zipFileName = $beat->name . "_" . str_replace("Licencia ", "", License::where('id', $beat_license->license_id)->first()->name) .  '.zip';
        $zip->open($zipFileName, ZipArchive::CREATE);
        $zip->addFile($license_path, $beat->name . License::where('id', $beat_license->license_id)->first()->extension);
        $zip->close();


        return response()->download($zipFileName, $zipFileName)->deleteFileAfterSend(true);
    }

}
