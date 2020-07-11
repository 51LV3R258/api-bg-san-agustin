<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\HistorialPrice
 *
 * @property int $id
 * @property int $product_id
 * @property int $unit_id
 * @property float $detalle
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $type
 * @property-read \App\Product $product
 * @property-read \App\Unit $unit
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorialPrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorialPrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorialPrice query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorialPrice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorialPrice whereDetalle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorialPrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorialPrice whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorialPrice whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorialPrice whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistorialPrice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HistorialPrice extends Model
{
    protected $table = 'historial_prices';

    protected $fillable = ['product_id', 'unit_id', 'detalle', 'type'];

    protected $hidden = ['updated_at'];

    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
    protected $attributes = [
        'type' => 'SELL'
    ];
}
