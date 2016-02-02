<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\ScheduleLog;

class LogsController extends Controller
{
    public function getIndex()
    {
        $data = ScheduleLog::getLogs(15);
        
        return view('logs.schedule_log', ['data' => $data]);
    }
}
