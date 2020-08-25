<?php

namespace App\Jobs;

use App\Models\File;
use App\Models\View;
use App\Services\GlowService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Facades\Image;

class ImageWebP implements ShouldQueue
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
        $this->queue = 'image_webp';
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
         * @var View $view
         */
        foreach ($this->image->views as $view) {
            if (!$view->webp) {
                continue;
            }

            $thumbnailPath = app(GlowService::class)
                ->thumbnailRealPath($this->image, $view->name);

            $thumbnail = Image::make($thumbnailPath);
            $thumbnail->save($thumbnailPath . '.webp', 100, 'webp');
            $thumbnail->destroy();
        }
    }
}
