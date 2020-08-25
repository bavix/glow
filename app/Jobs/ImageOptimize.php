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
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

class ImageOptimize implements ShouldQueue
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
        $this->queue = 'image_optimize';
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
        $optimized = false;
        foreach ($this->image->views as $view) {
            if (!$view->optimize) {
                continue;
            }

            $thumbnailPath = app(GlowService::class)
                ->thumbnailRealPath($this->image, $view->name);

            ImageOptimizer::optimize($thumbnailPath);
            ImageOptimizer::optimize($thumbnailPath . '.webp');
            $optimized = true;
        }

        if ($optimized) {
            $this->image->optimized = true;
            $this->image->optimized_at = now();
            $this->image->save();
        }
    }
}
