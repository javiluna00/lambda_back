<?php

namespace App\Http\Resources;

use App\Models\Beat;
use App\Models\BeatLicense;
use App\Models\License;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $beatLicense = BeatLicense::where('id', $this->beat_license_id)->first();

        return [
            'id' => $this->id,
            'beat_license_id' => $this->beat_license_id,
            'license' => License::where('id', $beatLicense->license_id)->first(),
            'beat' => Beat::where('id', $beatLicense->beat_id)->first(),
            'download_key' => BeatLicense::where('id', $this->beat_license_id)->first()->download_key,
            'user' => $this->user,
            'price' => $this->price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
