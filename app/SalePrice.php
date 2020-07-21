<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\SalePrice
 *
 * @property int $product_id
 * @property int $unit_id
 * @property float $detalle
 * @property bool $calculate
 * @property-read \App\Unit $unit
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SalePrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SalePrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SalePrice query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SalePrice whereCalculate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SalePrice whereDetalle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SalePrice whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SalePrice whereUnitId($value)
 * @mixin \Eloquent
 */
class SalePrice extends Model
{
    protected $primaryKey = 'product_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['product_id', 'unit_id', 'detalle', 'calculate'];

    protected $hidden = ['product_id', 'unit_id'];

    protected $with = ['unit'];

    protected $casts = [
        'calculate' => 'boolean',
    ];

    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }
}
