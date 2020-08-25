<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Bucket
 *
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\File[] $files
 * @property-read int|null $files_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\File[] $images
 * @property-read int|null $images_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\View[] $views
 * @property-read int|null $views_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bucket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bucket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bucket query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bucket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bucket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bucket whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bucket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bucket whereUserId($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\File[] $others
 * @property-read int|null $others_count
 */
class Bucket extends Model
{

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'user_id'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function views(): HasMany
    {
        return $this->hasMany(View::class);
    }

    /**
     * Get All Files
     *
     * @return HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    /**
     * Get Image Files
     *
     * @return HasMany
     */
    public function images(): HasMany
    {
        return $this->files()->where('type', File::TYPE_IMAGE);
    }

    /**
     * Get Other Files
     *
     * @return HasMany
     */
    public function others(): HasMany
    {
        return $this->files()->where('type', '!=', File::TYPE_IMAGE);
    }

}
