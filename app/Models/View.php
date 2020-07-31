<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

}
