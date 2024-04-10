<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeatPlay extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'beat_id',
    ];

    public function beat(){
        return $this->belongsTo(Beat::class);
    }
}
