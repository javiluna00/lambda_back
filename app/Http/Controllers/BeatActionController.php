<?php

namespace App\Http\Controllers;

use App\Http\Resources\BeatResource;
use App\Http\Resources\BeatSaveResource;
use App\Models\BeatAction;
use App\Models\Beat;
use App\Models\BeatClick;
use App\Models\BeatPlay;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;



class BeatActionController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ["storeClick", "storePlay"]]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     */
    public function toogleSave(Request $request)
    {
        // Obtén el ID del usuario autenticado
        $user = auth()->user();
        $userId = $user->id;
        // Guarda el save en la tabla pivot
        $beat = Beat::find($request->input('beat_id'));

        if($beat == null){
            return response()->json(['message' => 'Beat not found!'], 404);
        }


        $saveExistente = $user->savedBeats()->where('beat_id', $beat->id)->first();
        if(!$saveExistente){
            $user->savedBeats()->attach($beat->id);
            $beatsIdsGuardados = DB::table('beat_saves')->where('user_id', auth()->user()->id)->get()->pluck('beat_id');
            $beatsGuardados = Beat::whereIn('id', $beatsIdsGuardados)->get();
            return response()->json(['message' => 'Save added successfully', 'saves' => BeatResource::collection($beatsGuardados)], 200);
        }
        else{
            $user->savedBeats()->detach($beat->id);
            $beatsIdsGuardados = DB::table('beat_saves')->where('user_id', auth()->user()->id)->get()->pluck('beat_id');
            $beatsGuardados = Beat::whereIn('id', $beatsIdsGuardados)->get();
            return response()->json(['message' => 'Save removed successfully', 'saves' => BeatResource::collection($beatsGuardados)], 200);
        }





    }

    public function storeClick(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'beat_id' => 'required|integer|max:255',
            'user_id' => 'integer',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        if(Beat::find($request->input('beat_id')) == null){
            return response()->json(['message' => 'Beat not found!'], 404);
        }

        if($request->has('user_id') && User::find($request->input('user_id')) == null){
            return response()->json(['message' => 'User not found!'], 404);
        }

        $beat_id = $request->input('beat_id');
        $user_id = $request->has('user_id') ? $request->input('user_id') : null;



        $click = new BeatClick();
        $click->beat_id = $beat_id;
        if ($user_id) $click->user_id = $user_id;
        $click->save();

        return response()->json(['message' => 'Click action created!'], 200);


    }

    public function storePlay(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'beat_id' => 'required|integer|max:255',
            'user_id' => 'integer',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        if(Beat::find($request->input('beat_id')) == null){
            return response()->json(['message' => 'Beat not found!'], 404);
        }

        if($request->has('user_id') && User::find($request->input('user_id')) == null){
            return response()->json(['message' => 'User not found!'], 404);
        }

        $beat_id = $request->input('beat_id');
        $user_id = $request->has('user_id') ? $request->input('user_id') : null;



        $play = new BeatPlay();
        $play->beat_id = $beat_id;
        if ($user_id) $play->user_id = $user_id;
        $play->save();

        return response()->json(['message' => 'Play action created!'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Beat $beat)
    {
        //
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
    public function update(Request $request, Beat $beat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Beat $beat)
    {
        //
    }
}
