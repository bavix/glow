<?php

namespace App\Services;

use App\Models\Bucket;
use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileService
{

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function getFileType(UploadedFile $file): string
    {
        return Validator::make(\compact('file'), ['file' => 'image'])->passes() ?
            File::TYPE_IMAGE :
            File::TYPE_FILE;
    }

    /**
     * @param bool $visibility
     * @return string
     */
    public function getDisk(bool $visibility): string
    {
        return $visibility ? 'public' : 'private';
    }

    /**
     * @param File $file
     * @param bool $visibility
     * @return bool
     */
    public function moveTo(File $file, bool $visibility): bool
    {
        if ($file->visibility === $visibility) {
            return true;
        }

        try {
            $stream = Storage::disk($this->getDisk($file->visibility))
                ->readStream($file->route);

            $results = Storage::disk($this->getDisk($visibility))
                ->writeStream($file->route, $stream);

            if ($results) {
                $results = Storage::disk($this->getDisk($file->visibility))
                    ->delete($file->route);
            }
        } catch (\Throwable $throwable) {
            return false;
        }

        if ($results) {
            $file->visibility = $visibility;
            return $file->save();
        }

        return false;
    }

    /**
     * @param UploadedFile $file
     * @param string $route
     * @param array $options
     * @return string|null
     */
    public function storeAs(UploadedFile  $file, string $route, array $options = []): ?string
    {
        $route = \ltrim($route, '/');
        $info = pathinfo($route);

        $basename = $info['basename'];
        $force = $options['force'] ?? false;
        $dirname = $info['dirname'] === '.' ? '' : $info['dirname'];

        $visibility = $options['visibility'] ?? true;
        $disk = $this->getDisk($visibility);
        if (Storage::disk($disk)->exists($route)) {
            \abort_if(!$force, 409, sprintf('Route %s already exists', $route));
            $exists = File::whereRoute($route)
                ->where('user_id', Auth::id())
                ->where('visibility', $visibility)
                ->exists();

            \abort_if(!$exists, 403, 'You do not have permission to overwrite the file');
        }

        if ($fullRoute = $file->storeAs($dirname, $basename, \compact('disk'))) {
            return $fullRoute;
        }

        return null;
    }

}
