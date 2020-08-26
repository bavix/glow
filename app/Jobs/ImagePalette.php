<?php

namespace App\Jobs;

use App\Models\Color;
use App\Models\File;
use App\Services\FileService;
use Bavix\Glow\Adapters\Fit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;

class ImagePalette implements ShouldQueue
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
        $this->queue = 'image_palette';
        $this->image = $image;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        if ($this->image->extracted && $this->image->colors()->exists()) {
            return;
        }

        /**
         * @var string $diskName
         */
        $diskName = app(FileService::class)
            ->getDisk($this->image->visibility);

        $originalPath = Storage::disk($diskName)
            ->path($this->image->route);

        /**
         * @var ImageManager $imageManager
         */
        $imageManager = resolve(ImageManager::class);
        $originalImage = $imageManager->make($originalPath);
        $fit = new Fit($imageManager);
        $fitImage = $fit->apply($originalImage, [
            'width' => 400,
            'height' => 400,
        ]);

        $gdImageManager = new ImageManager(['driver' => 'gd']);
        $gdImage = $gdImageManager->make(
            (string)$fitImage->encode('jpg')
        );
        $originalImage->destroy();
        $fitImage->destroy();

        $fitGDImage = $gdImage->getCore();
        $palette = Palette::fromGD($fitGDImage);
        $extractor = new ColorExtractor($palette);

        $values = [];
        $representative = $extractor->extract(25);
        $mostUsedColors = $palette->getMostUsedColors(100);
        foreach ($mostUsedColors as $decimal => $occurrences) {
            $values[] = [
                'file_id' => $this->image->getKey(),
                'decimal' => $decimal,
                'occurrences' => $occurrences,
                'marked' => \in_array($decimal, $representative, true),
                'created_at' => now(),
            ];
        }

        $colors = collect($values)
            ->sortBy('marked', SORT_REGULAR, true);

        // free image
        $gdImage->destroy();

        Color::insert($colors->splice(0, 50)->toArray());
        $this->image->extracted = true;
        $this->image->extracted_at = now();
        $this->image->save();
    }
}
