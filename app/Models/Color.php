<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use League\ColorExtractor\Color as ColorExtractor;

/**
 * App\Models\Color
 *
 * @property int $id
 * @property int $file_id
 * @property int $decimal
 * @property int $occurrences
 * @property bool $marked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\File $file
 * @property-read string $hexadecimal
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Color newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Color newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Color query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Color whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Color whereDecimal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Color whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Color whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Color whereMarked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Color whereOccurrences($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Color whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Color extends Model
{

    /**
     * @var string[]
     */
    protected $fillable = [
        'file_id',
        'decimal',
        'occurrences',
        'marked',
    ];

    /**
     * @var string[]
     */
    protected $appends = ['hexadecimal'];

    /**
     * @var array
     */
    protected $casts = [
        'file_id' => 'int',
        'decimal' => 'int',
        'occurrences' => 'int',
        'marked' => 'bool',
    ];

    /**
     * @return string
     */
    public function getHexadecimalAttribute(): string
    {
        return ColorExtractor::fromIntToHex($this->decimal);
    }

    /**
     * @return BelongsTo
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

}
