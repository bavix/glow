<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use League\ColorExtractor\Color as ColorExtractor;

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
