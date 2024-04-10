<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $table = 'beat_license_user_purchases';

    protected $fillable = [
        'user_id',
        'beat_license_id',
        'price',
        'email',
        'created_at',
        'updated_at'
    ];


    public function license()
    {
        return $this->belongsTo(License::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
