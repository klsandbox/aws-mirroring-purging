<?php
namespace App\Console\Commands;

use Storage;
use Config;
use Illuminate\Console\Command;
use App\Models\ScheduleLog;

class AwsS3Purger extends Command
{
    protected $signature = 'aws-s3-purger';
    protected $description = 'Purge backups older than configured days';
    
    public function handle()
    {
        $day_backup = config("awsmanager.backup_date");

        if (!$day_backup)
        {
            $this->error('awsmanager.backup_date not set');
            return 1;
        }

        $this->comment('Purging files older than ' . $day_backup . ' days');
        $this->purgerBucket('s3_first');
        $this->purgerBucket('s3_second');
    }
    
    private function purgerBucket($s3_name)
    {
        $bucket = config("filesystems.disks.$s3_name.bucket");

        $this->comment('Purging ' . $s3_name . ' - ' . $bucket);
        $disk = Storage::disk($s3_name);
        $day_backup = config("awsmanager.backup_date");
        $time = time();
        $obj_delete = [];
        $return = '';

        $command = $disk->getDriver()
                ->getAdapter()
                ->getClient()
                ->listObjects([
                    'Bucket' => $bucket,
                    'Prefix' => ''
                ]);

        foreach ($command as $key => $object) {
            if ($key === 'Contents') {
                foreach ($object as $k => $file) {
                    $this->comment('Processing ' . $file['Key']);

                    $last_modify = strtotime($file['LastModified']) + ($day_backup * 24 * 60 * 60);
                    
                    if ($last_modify < $time && $file['Size']) {
                        $obj_delete[]['Key'] = $file['Key'];
                        $return[] = $file['Key'];
                        $this->comment('Deleted ' . $file['Key']);
                    }
                    else
                    {
                        $this->comment('Keeping ' . $file['Key']);
                    }
                }
            }
        }
        
        if (!empty($obj_delete)) {
            $disk->getDriver()
                    ->getAdapter()
                    ->getClient()
                    ->deleteObjects([
                        'Bucket' => $bucket,
                        'Delete' => [
                            'Objects' => $obj_delete,
                            'Quiet' => true
                        ],
                    ]);
        }
        
        $type = "Purger for AWS S3 Buckets '$s3_name' completed.";
        
        if (!empty($return)) {
            $return = 'Files deleted: ' . implode(', ', $return);
        }
        
        ScheduleLog::setDataLog($type, $return);
    }
}
