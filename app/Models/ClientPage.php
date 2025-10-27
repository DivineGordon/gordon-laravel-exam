<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientPage extends Model
{
    protected $fillable = [
        'user_id',
        'slug',
        'content',
        'logo_path',
        'background_image_path',
        'theme_id',
        'is_published',
    ];

    protected $casts = [
        'content' => 'array', // Automatically converts JSON to array
        'is_published' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function theme()
    {
        return $this->belongsTo(PageTheme::class);
    }

    public function analytics()
    {
        return $this->hasMany(PageAnalytic::class);
    }
}