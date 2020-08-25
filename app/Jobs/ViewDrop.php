<?php

namespace App\Jobs;

use App\Models\File;
use App\Models\View;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ViewDrop implements ShouldQueue
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
        $this->queue = 'view_drop';
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
        View::withoutEvents(function () {
            $this->view->folkImages()->each(function (File $file) {
                FilePurge::dispatch($file, [$this->view->name]);
            });

            $this->view->delete();
        });
    }
}
