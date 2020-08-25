<?php

namespace App\Jobs;

use App\Models\File;
use App\Models\View;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ViewChain implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var View
     */
    protected $view;

    /**
     * Create a new job instance.
     *
     * @param View $view
     * @return void
     */
    public function __construct(View $view)
    {
        $this->queue = 'view_chain';
        $this->view = $view;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws
     */
    public function handle(): void
    {
        $this->view->folkImages()->each(static function (File $file) {
            $chain = ImageThumbnail::withChain([
                new ImageWebP($file),
                new ImageOptimize($file),
            ]);

            $chain->dispatch($file);
        });
    }
}
