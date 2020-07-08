<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

/**
 * App\Product
 *
 * @property int $id
 * @property string $nombre
 * @property array|null $other_names
 * @property string|null $imagen
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $status
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\HistorialPrice[] $historial_prices
 * @property-read int|null $historial_prices_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Price[] $prices
 * @property-read int|null $prices_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Unit[] $units
 * @property-read int|null $units_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Unit[] $unitsForHistorial
 * @property-read int|null $units_for_historial_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereImagen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereOtherNames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
    protected $fillable = ['nombre', 'other_names', 'imagen', 'status'];

    protected $hidden = ['updated_at', 'created_at'];

    protected $casts = [
        'status' => 'boolean',
        'other_names' => 'array'
    ];

    protected $with = [
        'tags', 'prices'
    ];

    public function prices()
    {
        return $this->hasMany('App\Price');
    }

    public function units()
    {
        return $this->belongsToMany('App\Unit', 'prices')->as('prices')->withPivot(['detalle']);
    }

    public function unitsForHistorial(){
        return $this->belongsToMany('App\Unit', 'historial_prices')->as('historial_prices')->withPivot(['id','detalle'])->withTimestamps();
    }

    public function historial_prices()
    {
        return $this->hasMany('App\HistorialPrice');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag', 'product_tags')->as('product_tags');
    }

    use Searchable;
    public $asYouType = true;

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'products_index';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $product_values = [
            'id' => $this->id,
            'nombre' => $this->nombre,
        ];
        if (isset($this->other_names)) {
            $product_values = array_merge($product_values, $this->other_names);
        }

        return $product_values;
    }
}
