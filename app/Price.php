<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Price
 *
 * @property int $product_id
 * @property int $unit_id
 * @property float $detalle
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Unit $unit
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price whereDetalle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Price whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Price extends Model
{
    protected $primaryKey = 'product_id';
    public $incrementing = false;

    protected $fillable = ['product_id', 'unit_id', 'detalle'];

    protected $hidden = ['created_at', 'updated_at', 'product_id', 'unit_id'];

    protected $with = ['unit'];

    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }
}
