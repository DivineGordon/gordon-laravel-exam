<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageAnalytic extends Model
{
    protected $fillable = [
        'client_page_id',
        'visitor_ip',
        'session_id',
        'user_agent',
        'referer',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    public function clientPage()
    {
        return $this->belongsTo(ClientPage::class);
    }
}