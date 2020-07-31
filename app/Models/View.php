<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class View extends Model
{

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
