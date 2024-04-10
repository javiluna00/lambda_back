<?php

namespace App\Http\Controllers;

use App\Jobs\CreateWatermarkOnBeat;
use App\Http\Resources\BeatResource;
use App\Models\Beat;
use App\Models\BeatLicense;
use App\Models\License;
use App\Models\Mood;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Storage;


class BeatController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['getActives', 'getOne', 'testWaterMark']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function getAll(Request $request)
    {
        if(!auth()->user()->isAdmin())
        {
            return response()->json(['message' => "No autorizado"], 401);
        }
        $beats = Beat::all();
        return response()->json(BeatResource::collection($beats), 200);
    }

    public function getActives()
    {
        $beats = Beat::where('active', 1)->get();
        return response()->json(BeatResource::collection($beats), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'scale' => 'string|max:255',
            'active' => 'required',
            'cover_file' => 'required',
            'stock' => 'required|integer',
            'bpm' => 'required|integer',
            'mp3_file' => 'required',
            'tagged_file' => 'required',
            'mp3_price' => 'required',
            'wav_file' => 'required',
            'wav_price' => 'required',
            'stems_file' => 'required',
            'stems_price' => 'required',
            'exclusive' => 'required',
            'moods' => 'required',
            'genres' => 'required',
        ]);




        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $portada = $request->cover_file;

        $beat = new Beat();

        if($portada != null){
            $portada = $request->cover_file->store('covers', 'public');
            $image_url = asset('storage/' . $portada);
            $beat->cover_path = $image_url;
        }

        $beat->name = $request->name;
        $beat->scale = $request->scale;
        $beat->active = $request->active;
        $beat->stock = $request->stock;
        $beat->bpm = $request->bpm;
        $beat->still_exclusive = $request->exclusive == true ? 1 : 0;


        $tagged_file = $request->tagged_file->store('tagged', 'public');
        $tagged_url = asset('storage/' . $tagged_file);
        $beat->tagged_path = $tagged_url;


        $beat->save();

        $mp3License = new BeatLicense();
        $mp3License->beat_id = $beat->id;
        $mp3License->license_id = 1;
        $mp3License->price = $request->mp3_price;
        $mp3License->download_key = Uuid::uuid4();
        $mp3_file = $request->mp3_file->store('mp3', 'public');
        $mp3_url = asset('storage/' . $mp3_file);
        $mp3_url = str_replace("http://localhost:8000/storage", "", $mp3_url);
        $mp3License->file_url = $mp3_url;
        $mp3License->save();


        $wavLicense = new BeatLicense();
        $wavLicense->beat_id = $beat->id;
        $wavLicense->license_id = 2;
        $wavLicense->price = $request->wav_price;
        $wavLicense->download_key = Uuid::uuid4();
        $wav_file = $request->wav_file->store('wav', 'public');
        $wav_url = asset('storage/' . $wav_file);
        $wav_url = str_replace("http://localhost:8000/storage", "", $wav_url);
        $wavLicense->file_url = $wav_url;
        $wavLicense->save();

        $stemsLicense = new BeatLicense();
        $stemsLicense->beat_id = $beat->id;
        $stemsLicense->license_id = 3;
        $stemsLicense->price = $request->stems_price;
        $stemsLicense->download_key = Uuid::uuid4();
        $stems_file = $request->stems_file->store('stems', 'public');
        $stems_url = asset('storage/' . $stems_file);
        $stems_url = str_replace("http://localhost:8000/storage", "", $stems_url);
        $stemsLicense->file_url = $stems_url;
        $stemsLicense->save();



        if($request->exclusive == "true"){
            $exclusiveLicense = new BeatLicense();
            $exclusiveLicense->beat_id = $beat->id;
            $exclusiveLicense->license_id = 4;
            $exclusiveLicense->price = $request->exclusive_price;
            $exclusiveLicense->download_key = Uuid::uuid4();
            $exclusiveLicense->file_url = $stems_url;
            $exclusiveLicense->save();
        }

        if($request->moods){
            foreach($request->moods as $mood){
                $currentMood = Mood::where('name', $mood)->first();
                if($currentMood){
                    $beat->moods()->attach($currentMood->id);
                }
            }
        }
        if($request->genres){
            foreach($request->genres as $genre){
                $currentGenre = Genre::where('name', $genre)->first();
                if($currentGenre){
                    $beat->genres()->attach($currentGenre->id);
                }
            }
        }



        return response()->json([
            'message' => 'Beat creado correctamente',
            'beat' => $beat
        ], 201);

    }

    public function testWaterMark($id)
    {
        $beat = Beat::where('id', $id)->first();

        $mp3_license = BeatLicense::where('beat_id', $beat->id)->where('license_id', 1)->first();

        CreateWatermarkOnBeat::dispatch($mp3_license->file_url, $beat->name);
    }

    /**
     * Display the specified resource.
     */
    public function getOne(Request $request, string $id)
    {
        if($id){

            if($request->input('_mode') == "full" && auth()->user() && auth()->user()->isAdmin())
            {
                $beat = Beat::where('id', $id)->first();
                if($beat){

                    $returnedBeat = [
                        'id' => $beat->id,
                        'name' => $beat->name,
                        'active' => $beat->active,
                        'scale' => $beat->scale,
                        'bpm' => $beat->bpm,
                        'cover_path' => $beat->cover_path,
                        'tagged_path' => $beat->tagged_path,
                        'mp3_file' => $beat->mp3_path(),
                        'mp3_download_key' => $beat->mp3_download_key(),
                        'mp3_price' => $beat->mp3_price(),
                        'wav_file' => $beat->wav_path(),
                        'wav_download_key' => $beat->wav_download_key(),
                        'wav_price' => $beat->wav_price(),
                        'stems_file' => $beat->stems_path(),
                        'stems_download_key' => $beat->stems_download_key(),
                        'stems_price' => $beat->stems_price(),
                        'still_exclusive' => $beat->still_exclusive,
                        'exclusive_price' => $beat->exclusive_price(),
                        'stock' => $beat->stock,
                        'moods' => $beat->moods->pluck('name'),
                        'genres' => $beat->genres->pluck('name')
                    ];

                    return response()->json($returnedBeat, 200);
                }
                else{
                    return response()->json([
                        'message' => 'Beat no encontrado'
                    ], 404);
                }
            }
            else if ($request->input('_mode') == "client"){
                $beat = Beat::where('id', $id)->first();

                if($beat && $beat->active == 1){
                    return response()->json(BeatResource::make($beat), 200);
                }
                else{
                    return response()->json([
                        'message' => 'Beat no encontrado'
                    ], 404);
                }
            }
            else{
                return response()->json([
                    'message' => 'Error al obtener el beat'
                ], 404);
            }

        }
        else{
            return response()->json([
                'message' => 'Id no suministrado'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Beat $beat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if(!auth()->user()->isAdmin()){
            return response()->json([
                'message' => 'No autorizado'
            ], 401);
        }
        $beat = Beat::where('id', $id)->first();
        if(!$beat){
            return response()->json([
                'message' => 'Beat no encontrado'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'active' => 'integer',
            'scale' => 'string',
            'bpm' => 'integer',
            'stock' => 'boolean',
            'still_exclusive' => 'integer',
            'moods' => 'array',
            'genres' => 'array',
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors(), 400);
        }


        $beat->update($request->all());

        if($request->moods)
        {
            $mood_ids = Mood::whereIn('name', $request->moods)->pluck('id')->toArray();
            $beat->moods()->sync($mood_ids);
        }

        if($request->genres)
        {
            $genre_ids = Genre::whereIn('name', $request->genres)->pluck('id')->toArray();
            $beat->genres()->sync($genre_ids);
        }

        if($request->cover_file){
            $path = str_replace(asset('storage/'), '', $beat->cover_path);
            Storage::disk('public')->delete($path);
            $cover_path = $request->cover_file->store('covers', 'public');
            $cover_url = asset("storage/{$cover_path}");
            $beat->update([
                'cover_path' => $cover_url
            ]);
        }

        if($request->tagged_file){
            $path = str_replace(asset('storage/'), '', $beat->tagged_path);
            Storage::disk('public')->delete($path);
            $tagged_path = $request->tagged_file->store('tagged', 'public');
            $tagged_generated_url = asset("storage/{$tagged_path}");
            $beat->update([
                'tagged_path' => $tagged_generated_url
            ]);

        }

        if($request->mp3_file){
            $licenciaMP3 = BeatLicense::where('beat_id', $beat->id)->where('license_id', 1)->first();
            Storage::disk('public')->delete(str_replace(asset('storage/'), '', $licenciaMP3->file_url));
            $mp3_path = $request->mp3_file->store('mp3', 'public');
            $mp3_url = str_replace("http://localhost:8000/storage", "", asset("storage/{$mp3_path}"));
            $licenciaMP3->update([
                'file_url' => $mp3_url
            ]);
        }


        if($request->wav_file){
            $licenciaWAV = BeatLicense::where('beat_id', $beat->id)->where('license_id', 2)->first();
            Storage::disk('public')->delete(str_replace(asset('storage/'), '', $licenciaWAV->file_url));
            $wav_path = $request->wav_file->store('wav', 'public');
            $wav_url = str_replace("http://localhost:8000/storage", "", asset("storage/{$wav_path}"));
            $licenciaWAV->update([
                'file_url' => $wav_url
            ]);
        }

        if($request->stems_file){
            $licenciaStems = BeatLicense::where('beat_id', $beat->id)->where('license_id', 3)->first();
            Storage::disk('public')->delete(str_replace(asset('storage/'), '', $licenciaStems->file_url));
            $stems_path = $request->stems_file->store('stems', 'public');
            $stems_url = str_replace("http://localhost:8000/storage", "", asset("storage/{$stems_path}"));
            $licenciaStems->update([
                'file_url' => $stems_url
            ]);
            $licenciaExclusiva = BeatLicense::where('beat_id', $beat->id)->where('license_id', 4)->first();
            $licenciaExclusiva->update([
                'file_url' => $stems_url
            ]);
        }


        return response()->json([
            'message' => 'Beat actualizado',
            'beat' => $beat
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if($id){
            $beat = Beat::where('id', $id)->first();
            if($beat){
                $beat->delete();

                Storage::disk('public')->delete(str_replace(asset('storage/'), '', $beat->cover_path));
                Storage::disk('public')->delete(str_replace(asset('storage/'), '', $beat->mp3_path()));
                Storage::disk('public')->delete(str_replace(asset('storage/'), '', $beat->tagged_path));
                Storage::disk('public')->delete(str_replace(asset('storage/'), '', $beat->mp3_path()));
                Storage::disk('public')->delete(str_replace(asset('storage/'), '', $beat->wav_path()));
                Storage::disk('public')->delete(str_replace(asset('storage/'), '', $beat->stems_path()));

                return response()->json([
                    'message' => 'Beat eliminado'
                ], 200);
            }
            else{
                return response()->json([
                    'message' => 'Beat no encontrado'
                ], 404);
            }
        }
        else{
            return response()->json([
                'message' => 'Id no suministrado'
            ], 404);
        }
    }
}
