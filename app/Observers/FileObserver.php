<?php

namespace App\Observers;

use App\Jobs\ImageExtra;
use App\Jobs\ImageOptimize;
use App\Jobs\ImagePalette;
use App\Jobs\FilePurge;
use App\Jobs\ImageThumbnail;
use App\Jobs\ImageWebP;
use App\Models\File;
use App\Services\FileService;

class FileObserver
{

    /**
     * @param File $file
     */
    public function created(File $file): void
    {
        /**
         * Determine the type or level of availability
         */
        if (!$file->visibility || $file->type !== File::TYPE_IMAGE) {
            return;
        }

        /**
         * Checking the existence of a file on the server
         */
        if (!app(FileService::class)->exists($file)) {
            return;
        }

        $chain = ImageThumbnail::withChain([
            new ImageWebP($file),
            new ImageOptimize($file),
            new ImageExtra($file),
            new ImagePalette($file),
        ]);

        $chain->dispatch($file);
    }

    /**
     * @param File $file
     */
    public function updating(File $file): void
    {
        if ($file->type !== File::TYPE_IMAGE) {
            return;
        }

        /**
         * Checking the existence of a file on the server
         */
        if (!app(FileService::class)->exists($file)) {
            return;
        }

        if ($file->isDirty('visibility')) {
            $chain = FilePurge::withChain(!$file->visibility ? [] : [
                new ImageThumbnail($file),
                new ImageWebP($file),
                new ImageOptimize($file),
            ]);

            $chain->dispatch($file);
        }
    }

    /**
     * @param File $file
     */
    public function deleting(File $file): bool
    {
        /**
         * Checking the existence of a file on the server
         */
        if (!app(FileService::class)->exists($file)) {
            return true;
        }

        // with drop original file
        FilePurge::dispatch($file, true);
        return false;
    }

}
