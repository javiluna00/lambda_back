<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'extension',
        'conditions',
        'legal_file_url'
    ];

    public function beats()
    {
        return $this->belongsToMany(Beat::class);
    }
}
