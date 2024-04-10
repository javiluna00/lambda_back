<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Resources\CartResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Action;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'artist_name',
        'email',
        'password',
        'google_id',
        'email_verified_at',
        'email_verification_token',
        'reset_password_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'roles' => $this->getRoleIDs(),
        ];
    }

    public function clickedBeats()
    {
        return $this->belongsToMany(Beat::class, 'clicks')->withPivot('created_at', 'updated_at');
    }

    public function playedBeats()
    {
        return $this->belongsToMany(Beat::class, 'plays')->withPivot('created_at', 'updated_at');
    }

    public function savedBeats()
    {
            return $this->belongsToMany(Beat::class, 'beat_saves')->withTimestamps();
    }


    public function purchases()
    {

        return $this->hasMany(Purchase::class);

    }

    public function getRoleIDs()
    {
        return $this->roles()->pluck('id')->toArray();
    }

    public function isAdmin()
    {
        return in_array(1, $this->getRoleIDs());
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }
    public function cartBeatLicenses()
    {
        return $this->hasManyThrough(BeatLicense::class, Cart::class);
    }
}
