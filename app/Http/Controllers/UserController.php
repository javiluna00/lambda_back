<?php

namespace App\Http\Controllers;

use App\Http\Resources\PurchaseResource;
use App\Http\Resources\UserResource;
use App\Models\BeatLicense;
use App\Models\User;
use Illuminate\Http\Request;


class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => []]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $loggedUser = auth()->user();

        if(!$loggedUser->isAdmin()){
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $users = User::all();

        return response()->json(UserResource::collection(($users)), 201);
    }


    /**
     * Display the specified resource.
     */
    public function getOne(string $id)
    {
        $authUser = auth()->user();
        if(!$authUser->isAdmin()){
            return response()->json(['message' => 'No autorizado'], 403);
        }
        $user = User::findOrFail($id);
        return response()->json([
            'message' => 'Usuario encontrado',
            'user' => UserResource::make($user)
        ], 201);
    }

    public function getPurchases()
    {

        $user = auth()->user();
        $purchases = $user->purchases;
        return response()->json(PurchaseResource::collection($purchases), 201);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = User::findOrFail($request->id);
        $roles = $request->roles;
        $user->syncRoles($roles);

        return response()->json(["message" => "Usuario actualizado"], 201);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
