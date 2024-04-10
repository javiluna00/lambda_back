<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Action;
use Illuminate\Database\Eloquent\SoftDeletes;

class Beat extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'name',
        'scale',
        'bpm',
        'cover_path',
        'stock',
        'still_exclusive',
        'active',
        'tagged_path'
    ];

    public function clicks()
    {
        return $this->hasMany(BeatClick::class);
    }

    public function plays()
    {
        return $this->hasMany(BeatPlay::class);
    }

    public function saves()
    {
        return $this->belongsToMany(User::class, 'beat_saves')->withTimestamps();
    }

    public function purchases()
    {
        $beatLicenses = $this->licenses->pluck('id');
        $numberOfPurchases = Purchase::whereIn('beat_license_id', $beatLicenses)->count();
        return $numberOfPurchases;
    }


    public function mp3_path()
    {
        return BeatLicense::where('beat_id', $this->id)->where('license_id', 1)->first()->file_url;
    }

    public function wav_path()
    {
        return BeatLicense::where('beat_id', $this->id)->where('license_id', 2)->first()->file_url;
    }

    public function stems_path()
    {
        return BeatLicense::where('beat_id', $this->id)->where('license_id', 3)->first()->file_url;
    }

    public function mp3_price()
    {
        return BeatLicense::where('beat_id', $this->id)->where('license_id', 1)->first()->price;
    }

    public function wav_price()
    {
        return BeatLicense::where('beat_id', $this->id)->where('license_id', 2)->first()->price;
    }

    public function stems_price()
    {
        return BeatLicense::where('beat_id', $this->id)->where('license_id', 3)->first()->price;
    }

    public function exclusive_price()
    {
        return BeatLicense::where('beat_id', $this->id)->where('license_id', 4)->first()->price;
    }

    public function mp3_download_key()
    {
        return BeatLicense::where('beat_id', $this->id)->where('license_id', 1)->first()->download_key;
    }

    public function wav_download_key()
    {
        return BeatLicense::where('beat_id', $this->id)->where('license_id', 2)->first()->download_key;
    }

    public function stems_download_key()
    {
        return BeatLicense::where('beat_id', $this->id)->where('license_id', 3)->first()->download_key;
    }

    public function licenses()
    {
        return $this->belongsToMany(License::class, 'beat_licenses')->withPivot('price', 'file_url')->withTimestamps();
    }

    public function moods()
    {
        return $this->belongsToMany(Mood::class, 'beat_mood')->withTimestamps();
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'beat_genre')->withTimestamps();
    }
}
