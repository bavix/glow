<?php

namespace App\Jobs;

use App\Models\File;
use App\Models\View;
use App\Services\FileService;
use App\Services\GlowService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var File
     */
    protected $image;

    /**
     * Create a new job instance.
     *
     * @param File $image
     * @return void
     */
    public function __construct(File $image)
    {
        $this->queue = 'image_thumbnail';
        $this->image = $image;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        /**
         * @var string $diskName
         */
        $diskName = app(FileService::class)
            ->getDisk($this->image->visibility);

        $originalPath = Storage::disk($diskName)
            ->path($this->image->route);

        /**
         * @var View $view
         */
        $thumbs = [];
        foreach ($this->image->views as $view) {
            $thumbnailPath = app(GlowService::class)
                ->thumbnailRealPath($this->image, $view->name);

            $original = Image::make($originalPath);
            $thumbnail = app(GlowService::class)
                ->makeGrow($view)
                ->apply($original, $view->toArray());

            $concurrentDirectory = \dirname($thumbnailPath);
            if (!\is_dir($concurrentDirectory) && !\mkdir($concurrentDirectory, 0777, true) && !\is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }

            $thumbnail->save($thumbnailPath, $view->quality);

            $thumbnail->destroy();
            $original->destroy();
            $thumbs[] = $view->name;
        }

        $this->image->thumbs = $thumbs;
        $this->image->processed = true;
        $this->image->processed_at = now();
        $this->image->save();
    }
}
