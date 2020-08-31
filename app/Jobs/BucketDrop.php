<?php

namespace App\Jobs;

use App\Models\Bucket;
use App\Models\File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BucketDrop implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Bucket
     */
    protected $bucket;

    /**
     * Create a new job instance.
     *
     * @param Bucket $bucket
     * @return void
     */
    public function __construct(Bucket $bucket)
    {
        $this->queue = 'bucket_drop';
        $this->bucket = $bucket;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws
     */
    public function handle(): void
    {
        $this->bucket->files()->each(static function (File $file) {
            $file->delete();
        });

        Bucket::withoutEvents(function () {
            $this->bucket->delete();
        });
    }
}
