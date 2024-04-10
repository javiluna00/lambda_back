<?php

namespace App\Http\Controllers;
use App\Models\Beat;
use App\Jobs\CreateWatermarkOnBeat;
use FFMpeg\FFMpeg;
use FFMpeg\Filters\Audio\AudioFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Process;


class FileController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getTagged']]);
    }

    public function storeCover(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    }

    public function storeBeat(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'file_mp3' => 'required|file|mimes:audio/mpeg,mpga,mp3',
            'file_wav' => 'required|file',
            'file_stems' => 'required|file|mimes:rar,zip',
        ]);

        // if($validator->fails()){
        //     return response()->json($validator->errors(), 422);
        // }

        // $file_mp3 = $request->file('file_mp3');
        // $file_wav = $request->file('file_wav');
        // $file_stems = $request->file('file_stems');


        $proceso = Process::run('"E:\\Program Files (x86)\\ffmpeg-master-latest-win64-gpl\\bin\\ffmpeg" -i ../storage/app/beats/nombredelbeat/nombredelbeat.mp3 -filter_complex "amovie=../storage/app/voicetag/tagvoz2patrones10secs.mp3:loop=0,asetpts=N/SR/TB[beep];
        [0][beep]amix=duration=shortest,volume=2"   ../storage/app/beats/nombredelbeat/tagged.mp3')->throw();

        return $proceso->output();
    }
    public function createFolder(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'folder_name' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        Storage::makeDirectory($request->folder_name);
    }

    public function getTagged($id)
    {
        $beat = Beat::find($id);

        if (!$beat) {
            return response()->json([
                'error' => 'Beat not found'
            ], 404);
        }

        $path = $beat->tagged_path;
        // Asumiendo que $path es una URL completa, necesitas convertirla a una ruta de archivo local.
        // Esto dependerá de cómo estés almacenando los archivos físicamente.
        // Por ejemplo, si están almacenados en "public/storage", podrías hacer algo como esto:
        $localPath = str_replace('http://localhost:8000/storage', public_path('storage'), $path);

        if (!file_exists($localPath)) {
            return response()->json([
                'error' => 'File not found'
            ], 404);
        }

        $file = file_get_contents($localPath);
        $type = mime_content_type($localPath);
        $length = filesize($localPath);

        return Response::make($file, 200)
            ->header('Content-Type', $type)
            ->header('Content-Disposition', 'attachment; filename="' . basename($localPath) . '"')
            ->header('Content-Length', $length)
            ->header('Accept-Ranges', 'bytes')
            ->header('Content-Range', 'bytes 0-' . ($length-1) . '/' . $length);

    }
}
