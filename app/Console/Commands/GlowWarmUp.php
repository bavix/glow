<?php

namespace App\Console\Commands;

use App\Models\File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GlowWarmUp extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'glow:warm-up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warming up the content delivery network cache';

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

        $progressBar = $this->output->createProgressBar();
        $progressBar->setProgressCharacter("\xf0\x9f\x8c\x80");
        $progressBar->setFormat('debug');

        /**
         * @var File $file
         */
        foreach ($progressBar->iterate($lazyCollection) as $file) {
            Http::get($file->uri); // original
            foreach ($file->thumbs_uri as $uri) {
                Http::get($uri); // thumbnail
            }
        }

        $progressBar->finish();

        return 0;
    }

}
