<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleLog extends Model
{
    public $table = 'schedule_log';
    
    public static function getLogs($paginate)
    {
        return self::orderBy('id', 'desc')->paginate($paginate);
    }
    
    public static function setDataLog($type, $return = '')
    {
        $logs = new ScheduleLog;
        
        $logs->type = $type;
        $logs->return = $return;
        $logs->save();
    }
}
