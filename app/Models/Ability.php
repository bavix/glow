<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Ability
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Ability newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ability newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ability query()
 * @method static \Illuminate\Database\Eloquent\Builder|Ability whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ability whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ability whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ability whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ability whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Ability extends Model
{
    //
}
