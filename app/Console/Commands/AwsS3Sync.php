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
        $s3_first = config("awsmanager.s3_bucket_sync_first");
        $s3_second = config("awsmanager.s3_bucket_sync_second");
        $s3_sync = config("awsmanager.s3_sync_profile");

        if (!$s3_first || !$s3_second || !$s3_sync)
        {
            $this->error('s3_bucket_sync_first, s3_bucket_sync_second, or s3_sync_profile not set');
            return 1;
        }

        $this->comment("Sync backups from $s3_first to $s3_second");

        $output = [];
        $return = -1;

        $command = "aws s3 sync s3://{$s3_first} s3://{$s3_second} --delete --profile {$s3_sync}";
        $this->comment("Executing $command");
        exec($command, $output, $return);

        $this->comment(print_r($output, true));
        $this->comment("Return : $return");

        $type = "AWS S3 SYNC completed.";
        ScheduleLog::setDataLog($type, implode(', ', $output));
    }
}
