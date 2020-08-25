<?php

namespace App\Jobs;

use App\Models\File;
use App\Services\FileService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageExtra implements ShouldQueue
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
        $this->queue = 'image_extra';
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

        $original = Image::make($originalPath);

        $this->image->extra = [
            'width' => $original->width(),
            'height' => $original->height(),
            'filesize' => $original->filesize(),
            'mime' => $original->mime(),
            'exif' => $original->exif(),
            'iptc' => $original->iptc(),
        ];

        $this->image->save();

        $original->destroy();;
    }
}
