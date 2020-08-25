<?php

namespace App\Services;

use App\Models\File;
use App\Models\View;
use Bavix\Glow\Adapters\Contain;
use Bavix\Glow\Adapters\Cover;
use Bavix\Glow\Adapters\Fit;
use Bavix\Glow\Adapters\None;
use Bavix\Glow\Adapters\Resize;
use Bavix\Glow\DriverInterface;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class GlowService
{

    /**
     * @param View $view
     * @return DriverInterface
     */
    public function makeGrow(View $view): DriverInterface
    {
        /**
         * @var ImageManager $imageManager
         */
        $imageManager = resolve(ImageManager::class);

        switch ($view->type) {
            case 'contain':
                return new Contain($imageManager);
            case 'cover':
                return new Cover($imageManager);
            case 'fit':
                return new Fit($imageManager);
            case 'none':
                return new None($imageManager);
            case 'resize':
                return new Resize($imageManager);
            default:
                throw new \InvalidArgumentException('Unknown adapter');
        }
    }

    /**
     * @param File $file
     * @param string $view
     * @return string
     */
    public function thumbnailPath(File $file, string $view): string
    {
        $thumbnailUrn = $this->thumbnailUrn($file, $view);
        $urnExplode = \explode(':', $thumbnailUrn, 2);
        return \implode('/', $urnExplode);
    }

    /**
     * @param File $file
     * @param string $view
     * @return string
     */
    public function thumbnailRealPath(File $file, string $view): string
    {
        return Storage::disk('thumbs')
            ->path($this->thumbnailPath($file, $view));
    }

    /**
     * @param File $file
     * @param string $view
     * @return string
     */
    public function thumbnailUrn(File $file, string $view): string
    {
        $urnExplode = \explode(DIRECTORY_SEPARATOR, $file->route, 2);
        $bucket = \current($urnExplode); // _glow
        $dirname = \dirname(\end($urnExplode));
        $basename = \basename($file->route);

        return \sprintf('%s:%s/%s/%s', $bucket, $view, $dirname, $basename);
    }

}
