<?php

namespace App\Console\Commands;

use App\Jobs\ImageOptimize;
use App\Jobs\ImageThumbnail;
use App\Jobs\ImageWebP;
use App\Models\File;
use Illuminate\Console\Command;

class GlowRefresh extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'glow:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recreates all preview files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $lazyCollection = File::query()
            ->where('type', File::TYPE_IMAGE)
            ->where('visibility', true)
            ->cursor();

        /**
         * @var File $file
         */
        foreach ($lazyCollection as $file) {
            $chain = ImageThumbnail::withChain([
                new ImageWebP($file),
                new ImageOptimize($file),
            ]);

            $chain->dispatch($file);
        }

        return 0;
    }
}
