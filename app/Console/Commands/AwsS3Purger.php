<?php
namespace App\Console\Commands;

use Storage;
use Config;
use Illuminate\Console\Command;
use App\Models\ScheduleLog;

class AwsS3Purger extends Command
{
    protected $signature = 'aws-s3-purger';
    protected $description = 'Display an inspiring quote';
    
    public function handle()
    {
        $this->purgerBucket('s3_first');
        $this->purgerBucket('s3_second');
    }
    
    private function purgerBucket($s3_name)
    {
        $disk = Storage::disk($s3_name);
        $day_backup = (int)Config::get("app.aws.backup_date");
        $time = time();
        $obj_delete = [];
        $return = '';
        
        $command = $disk->getDriver()
                ->getAdapter()
                ->getClient()
                ->listObjects([
                    'Bucket' => Config::get("filesystems.disks.$s3_name.bucket"),
                    'Prefix' => ''
                ]);

        foreach ($command as $key => $object) {
            if ($key === 'Contents') {
                foreach ($object as $k => $file) {
                    $last_modify = strtotime($file['LastModified']) + ($day_backup * 24 * 60 * 60);
                    
                    if ($last_modify < $time && $file['Size']) {
                        $obj_delete[]['Key'] = $file['Key'];
                        $return[] = $file['Key'];
                    }
                }
            }
        }
        
        if (!empty($obj_delete)) {
            $disk->getDriver()
                    ->getAdapter()
                    ->getClient()
                    ->deleteObjects([
                        'Bucket' => Config::get("filesystems.disks.$s3_name.bucket"),
                        'Delete' => [
                            'Objects' => $obj_delete,
                            'Quiet' => true
                        ],
                    ]);
        }
        
        $type = "Purger for AWS S3 Buckets '$s3_name' complited.";
        
        if (!empty($return)) {
            $return = 'Files deleted: ' . implode(', ', $return);
        }
        
        ScheduleLog::setDataLog($type, $return);
    }
}
