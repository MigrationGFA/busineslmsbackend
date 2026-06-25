<?php

namespace App\Models;

use CodeIgniter\Model;

class PageViewEventModel extends Model
{
    protected $table         = 'page_view_events';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;
    protected $returnType    = 'array';

    protected $allowedFields = [
        'event_type',
        'url',
        'referrer',
        'user_agent',
        'screen_resolution',
        'browser_lang',
        'timestamp_event',
        'session_id',
        'app_name',
        'email',
        'created_at',
    ];
}
