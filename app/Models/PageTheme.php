<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageTheme extends Model
{
    protected $fillable = [
        'name',
        'primary_color',
        'secondary_color',
        'accent_color',
        'text_color',
        'background_color',
    ];

    public function clientPages()
    {
        return $this->hasMany(ClientPage::class, 'theme_id');
    }
}