<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\View
 *
 * @property int $id
 * @property string $name
 * @property int $bucket_id
 * @property string $type
 * @property int|null $width
 * @property int|null $height
 * @property int $quality
 * @property string|null $color
 * @property bool $optimize
 * @property bool $webp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bucket $bucket
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\View newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\View newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\View query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\View whereBucketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\View whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\View whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\View whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\View whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\View whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\View whereOptimize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\View whereQuality($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\View whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\View whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\View whereWebp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\View whereWidth($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\File[] $files
 * @property-read int|null $files_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\File[] $folkFiles
 * @property-read int|null $folk_files_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\File[] $folkImages
 * @property-read int|null $folk_images_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\File[] $folkOthers
 * @property-read int|null $folk_others_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\File[] $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\File[] $others
 * @property-read int|null $others_count
 */
class View extends Model
{

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'bucket_id',
        'type',
        'width',
        'height',
        'quality',
        'color',
        'optimize',
        'webp',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'bucket_id' => 'int',
        'width' => 'int',
        'height' => 'int',
        'quality' => 'int',
        'optimize' => 'bool',
        'webp' => 'bool',
    ];

    /**
     * @param int|null $quality
     *
     * @return int
     */
    public function getQualityAttribute(?int $quality): int
    {
        return $quality ?: 100;
    }

    /**
     * @return BelongsTo
     */
    public function bucket(): BelongsTo
    {
        return $this->belongsTo(Bucket::class);
    }

    /**
     * @return HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'bucket_id', 'bucket_id');
    }

    /**
     * @return HasMany
     */
    public function folkFiles(): HasMany
    {
        return $this->files()
            ->where('visibility', true);
    }

    /**
     * @return HasMany
     */
    public function images(): HasMany
    {
        return $this->files()
            ->where('type', File::TYPE_IMAGE);
    }

    /**
     * @return HasMany
     */
    public function others(): HasMany
    {
        return $this->files()
            ->where('type', '!=', File::TYPE_IMAGE);
    }

    /**
     * @return HasMany
     */
    public function folkImages(): HasMany
    {
        return $this->folkFiles()
            ->where('type', File::TYPE_IMAGE);
    }

    /**
     * @return HasMany
     */
    public function folkOthers(): HasMany
    {
        return $this->folkFiles()
            ->where('type', '!=', File::TYPE_IMAGE);
    }

}
