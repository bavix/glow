<?php

namespace App\Jobs;

use App\Models\File;
use App\Services\FileService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileExtra implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var File
     */
    protected $file;

    /**
     * Create a new job instance.
     *
     * @param File $image
     * @return void
     */
    public function __construct(File $image)
    {
        $this->queue = 'file_extra';
        $this->file = $image;
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
            ->getDisk($this->file->visibility);

        $adapter = Storage::disk($diskName);

        $fileExtra = [
            'last_modified' => $adapter->lastModified($this->file->route),
            'filesize' => $adapter->size($this->file->route),
            'mime' => $adapter->mimeType($this->file->route),
        ];

        $imageExtra = $this->getImageExtra($adapter);
        $this->file->extra = \array_merge($fileExtra, $imageExtra);
        $this->file->save();
    }

    /**
     * @param FilesystemAdapter $adapter
     * @return array
     */
    protected function getImageExtra(FilesystemAdapter $adapter): array
    {
        if ($this->file->type !== File::TYPE_IMAGE) {
            return [];
        }

        $image = Image::make(
            $adapter->path($this->file->route)
        );

        $extra = [
            'mime' => $image->mime(),
            'width' => $image->width(),
            'height' => $image->height(),
            'exif' => $image->exif(),
            'iptc' => $image->iptc(),
        ];

        $image->destroy();;

        return $extra;
    }

}
