<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Invite
 *
 * @property string $id
 * @property int $bucket_id
 * @property int $file_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon $expires_at
 * @property-read \App\Models\Bucket $bucket
 * @property-read \App\Models\File $file
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite whereBucketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int|null $user_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite whereUserId($value)
 * @property-read string $route
 * @property-read string $uri
 * @property-read string $urn
 */
class Invite extends Model
{

    /**
     * @inheritdoc
     */
    public $incrementing = false;

    /**
     * @inheritdoc
     */
    protected $keyType = 'string';

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'id',
        'bucket_id',
        'file_id',
        'expires_at',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'expires_at',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'urn',
        'uri',
    ];

    /**
     * @return BelongsTo
     */
    public function bucket(): BelongsTo
    {
        return $this->belongsTo(Bucket::class);
    }

    /**
     * @return BelongsTo
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    /**
     * @return string
     */
    public function getUrnAttribute(): string
    {
        return $this->file->urn . '?key=' . $this->getKey();
    }

    /**
     * @return string
     */
    public function getUriAttribute(): string
    {
        return $this->file->uri . '?key=' . $this->getKey();
    }

}
