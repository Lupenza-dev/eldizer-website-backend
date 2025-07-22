<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class HomePageAbout extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\HomePageAboutFactory> */
    use HasFactory,SoftDeletes,InteractsWithMedia;

    protected $fillable =[
        'title',
        'content',
        'values',
        'badge',
        'is_published',
        'created_by'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'year' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
