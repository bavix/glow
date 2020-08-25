<?php

namespace App\Jobs;

use App\Models\File;
use App\Services\FileService;
use App\Services\GlowService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class FilePurge implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var array
     */
    protected $thumbs;

    /**
     * @var bool
     */
    protected $drop;

    /**
     * Create a new job instance.
     *
     * @param File $file
     * @param array $thumbs
     * @param bool $drop
     * @return void
     */
    public function __construct(File $file, array $thumbs = [], bool $drop = false)
    {
        $this->queue = 'file_purge';
        $this->file = $file;
        $this->thumbs = $thumbs;
        $this->drop = $drop;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        foreach ($this->thumbs as $thumb) {
            $thumbnailPath = app(GlowService::class)
                ->thumbnailPath($this->file, $thumb);

            Storage::disk('thumbs')->delete([
                $thumbnailPath,
                $thumbnailPath . '.webp',
            ]);
        }

        if ($this->drop) {
            $disk = app(FileService::class)
                ->getDisk($this->file->visibility);

            Storage::disk($disk)
                ->delete($this->file->route);

            $this->file->forceDelete();
            return;
        }

        try {
            if ($this->file->type === File::TYPE_IMAGE) {
                $thumbs = \array_diff((array)$this->file->thumbs, $this->thumbs);
                $this->file->refresh();
                $this->file->thumbs = \array_values($thumbs);
                $this->file->save();
            }
        } catch (\Throwable $throwable) {
            // model deleted
        }
    }
}
