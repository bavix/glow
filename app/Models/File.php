<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
