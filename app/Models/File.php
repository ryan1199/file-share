<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'archive_box_id',
        'name',
        'description',
        'slug',
        'path',
        'extension',
        'size',
        'views',
        'downloads'
    ];

    public static function generateSlug()
    {
        $slug = 'F' . now()->year . now()->month . now()->day;
        $files = File::where('slug', 'like', '%' . $slug . '%')->count();
        if ($files > 0) {
            $slug.= $files + 1;
        } else {
            $slug.= '1';
        }
        return $slug;
    }
    public function archiveBox(): BelongsTo
    {
        return $this->belongsTo(ArchiveBox::class);
    }
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, table: 'like')->using(Like::class)->withTimestamps();
    }
}
