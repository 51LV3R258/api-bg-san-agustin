<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Unit
 *
 * @property int $id
 * @property string $nombre
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Unit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Unit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Unit query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Unit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Unit whereNombre($value)
 * @mixin \Eloquent
 */
class Unit extends Model
{
    public $timestamps = false;

    protected $fillable = ['nombre'];
}
