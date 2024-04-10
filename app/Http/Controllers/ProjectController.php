<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['getVisible']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(!auth()->user()->isAdmin())
        {
            return response()->json(['message' => "No autorizado"], 401);
        }
        $proyectos = Project::all();
        return response()->json($proyectos, 200);
    }

    public function getVisible()
    {

        $proyectos = Project::where('active', 1)->get();
        return response()->json($proyectos, 200);
    }

    public function getOne ($id)
    {
        $proyecto = Project::find($id);
        if($proyecto == null){
            return response()->json(['message' => "Proyecto no encontrado"], 404);
        }
        return response()->json($proyecto, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'yt_link' => 'required|url',
            'spotify_link' => 'required|url',
            'image' => 'required',
            'active' => 'required',
        ]);

        $portada = $request->image;



        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($portada != null) {
            $portada = $request->image->store('images', 'public');

            $image_url = asset('storage/' . $portada);
        }

        $proyecto = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'yt_link' => $request->yt_link,
            'spotify_link' => $request->spotify_link,
            'image' => $image_url,
            'active' => $request->active
        ]);

        return response()->json(['message' => "Proyecto creado",'data' => $proyecto], 201);


    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = array_merge($request->all(), ['id' => $id]);
        // Valida el arreglo combinado
        $validator = Validator::make($data, [
            'id' => 'required|exists:projects',
            // Añade aquí las reglas de validación para los otros campos del $request
            'name' => 'string', // Ejemplo para 'name', excluyendo el proyecto actual por su ID
            'description' => 'string',
            'yt_link' => 'url',
            'spotify_link' => 'url',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $proyecto = Project::find($id);

        if($request->image != null){
            $previousImage = str_replace(asset('storage/'), '', $proyecto->image);
            Storage::disk('public')->delete($previousImage);
            $newImage = $request->image->store('images', 'public');
            $image_url = asset('storage/' . $newImage);
            $data['image'] = $image_url;
        }


        $proyecto = Project::find($id);
        $proyecto->update($data);

        return response()->json(['message' => "Proyecto actualizado",'data' => $proyecto], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $proyecto = Project::find($id);
        $proyecto->delete();

        return response()->json(['message' => "Proyecto eliminado"], 200);
    }
}
