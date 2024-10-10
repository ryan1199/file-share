<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ArchiveBox extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'cover',
        'private',
    ];

    public static function generateSlug()
    {
        $slug = 'AB' . now()->year . now()->month . now()->day;
        $archiveBoxes = ArchiveBox::where('slug', 'like', '%' . $slug . '%')->count();
        if ($archiveBoxes > 0) {
            $slug.= $archiveBoxes + 1;
        } else {
            $slug.= '1';
        }
        return $slug;
    }
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->using(ArchiveBoxUser::class)->withPivot('permission');
    }
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }
    public function idsOfJoinedUsers()
    {
        return $this->users()->pluck('users.id');
    }
    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'loggable');
    }
}
