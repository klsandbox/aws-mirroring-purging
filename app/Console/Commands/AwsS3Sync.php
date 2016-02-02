<?php

namespace App\Console\Commands;

use Config;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Console\Command;
use App\Models\ScheduleLog;

class AwsS3Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aws-s3-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start S3 sync';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Schedule $schedule)
    {
        $s3_first = Config::get("app.aws.s3_bucket_sync_first");
        $s3_second = Config::get("app.aws.s3_bucket_sync_second");
        $s3_sync = Config::get("app.aws.s3_sync_profile");
        
        $schedule->exec("aws s3 sync s3://{$s3_first} s3://{$s3_second} --delete --profile {$s3_sync}");
        
        $type = "AWS S3 SYNC completed.";
        ScheduleLog::setDataLog($type);
    }
}
