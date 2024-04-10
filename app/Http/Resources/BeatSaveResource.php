<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use App\Models\Beat;

class BeatSaveResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $beatsIdsGuardados = DB::table('beat_saves')->where('user_id', auth()->user()->id)->get()->pluck('beat_id');
        $beatsGuardados = Beat::whereIn('id', $beatsIdsGuardados)->get();
        return [BeatResource::collection($beatsGuardados)];
    }
}
