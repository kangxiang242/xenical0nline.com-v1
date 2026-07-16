<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Observer extends Model
{
    protected $fillable = [
        'host', 'uri', 'section', 'ip', 'ipcountry', 'explain',
        'event', 'event_type', 'event_name', 'device',
        'session_id', 'visitor_id', 'page_view_id', 'page_type',
        'referer', 'referer_original',
        'utm_source', 'utm_medium', 'utm_campaign',
        'metadata', 'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
