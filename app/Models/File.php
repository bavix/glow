<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\File
 *
 * @property int $id
 * @property string $route
 * @property string $type
 * @property bool $visibility
 * @property bool $extracted
 * @property string|null $extracted_at
 * @property bool $processed
 * @property string|null $processed_at
 * @property bool $optimized
 * @property string|null $optimized_at
 * @property mixed|null $extra
 * @property mixed|null $thumbs
 * @property int $bucket_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Color[] $colors
 * @property-read int|null $colors_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invite[] $invites
 * @property-read int|null $invites_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Color[] $palette
 * @property-read int|null $palette_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereBucketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereExtracted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereExtractedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereOptimized($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereOptimizedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereThumbs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereVisibility($value)
 * @mixin \Eloquent
 */
class File extends Model
{

    const TYPE_IMAGE = 'image';
    const TYPE_FILE = 'file';

    /**
     * @return HasMany
     */
    public function invites(): HasMany
    {
        return $this->hasMany(Invite::class);
    }

    /**
     * @return HasMany
     */
    public function colors(): HasMany
    {
        return $this->hasMany(Color::class);
    }

    /**
     * @return HasMany
     */
    public function palette(): HasMany
    {
        return $this->colors()->where('marked', 1);
    }

}
