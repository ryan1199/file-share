<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'slug',
        'avatar',
        'token',
        'email_verified_at',
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

    public static function generateSlug()
    {
        $slug = 'U' . now()->year . now()->month . now()->day;
        $users = User::where('slug', 'like', '%' . $slug . '%')->count();
        if ($users > 0) {
            $slug.= $users + 1;
        } else {
            $slug.= '1';
        }
        return $slug;
    }
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }
    public function archiveBoxes(): BelongsToMany
    {
        return $this->belongsToMany(ArchiveBox::class)->using(ArchiveBoxUser::class)->withPivot('permission')->withTimestamps();
    }
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(File::class, table: 'like')->using(Like::class)->withTimestamps();
    }
    public function log(): HasOne
    {
        return $this->hasOne(Log::class);
    }
    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'loggable');
    }
}
