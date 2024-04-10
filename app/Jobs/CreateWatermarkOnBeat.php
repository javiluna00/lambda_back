<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;

class CreateWatermarkOnBeat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    public $mp3_path;
    public $beat_name;

    public function __construct($mp3_path, $beat_name)
    {
        $this->mp3_path = str_replace("http://localhost:8000/storage/", "", $mp3_path);
        $this->mp3_path = str_replace("/", "\\", $this->mp3_path);
        $this->beat_name = $beat_name;
    }

    /**
     * Execute the job.
     */
    public function handle(): string
    {
        // "E:\Program Files (x86)\ffmpeg-master-latest-win64-gpl\bin\ffmpeg" -i "E:\laragon\www\tfg\lambda_back\storage\app\public\mp3\BAYyXcGvKa8dhYRMx65i7Ic3oONY5O5BsNQEMl43.mp3" -filter_complex "amovie='E:\laragon\www\tfg\lambda_back\storage\app\voicetag\tagvoz2patrones10secs.mp3':loop=0,asetpts=N/SR/TB[beep]; [0][beep]amix=duration=shortest,volume=2" "E:\laragon\www\tfg\lambda_back\storage\app\public\tagged_beats\444.mp3"
        // "E:\Program Files (x86)\ffmpeg-master-latest-win64-gpl\bin\ffmpeg" -i "E:\laragon\www\tfg\lambda_back\storage\app\public\mp3\BAYyXcGvKa8dhYRMx65i7Ic3oONY5O5BsNQEMl43.mp3" -filter_complex "amovie='E:\laragon\www\tfg\lambda_back\storage\app\voicetag\tagvoz2patrones10secs.mp3':loop=0,asetpts=N/SR/TB[beep]; [0][beep]amix=duration=shortest,volume=2" "E:\laragon\www\tfg\lambda_back\storage\app\public\tagged_beats\444.mp3"

        $inputPath = storage_path('app\public\\' . $this->mp3_path);
        $voiceTagPath = storage_path('app\voicetag\tagvoz2patrones10secs.mp3');
        $outputPath = storage_path('app\public\tagged_beats\\' . $this->beat_name . '.mp3');

        $ffmpegPath = '"E:\\Program Files (x86)\\ffmpeg-master-latest-win64-gpl\\bin\\ffmpeg"';
        $command = "{$ffmpegPath} -i \"{$inputPath}\" -filter_complex \"amovie='{$voiceTagPath}':loop=0,asetpts=N/SR/TB[beep]; [0][beep]amix=duration=shortest,volume=2\" \"{$outputPath}\"";

        dd($command);

        $proceso = Process::run($command)->throw();
        dd($proceso->output());
    }
}
