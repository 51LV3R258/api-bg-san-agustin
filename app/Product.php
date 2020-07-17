<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use TeamTNT\TNTSearch\Indexer\TNTIndexer;

/**
 * App\Product
 *
 * @property int $id
 * @property string $nombre
 * @property array|null $other_names
 * @property string|null $imagen
 * @property int|null $unit_id
 * @property float|null $purchase_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $status
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\HistorialPrice[] $historial_prices
 * @property-read int|null $historial_prices_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SalePrice[] $sale_prices
 * @property-read int|null $sale_prices_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read \App\Unit|null $unit
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
    protected $fillable = ['nombre', 'other_names', 'imagen', 'unit_id', 'purchase_price', 'status'];

    protected $hidden = ['updated_at', 'created_at', 'unit_id'];

    protected $casts = [
        'status' => 'boolean',
        'other_names' => 'array'
    ];

    protected $with = [
        'tags', 'sale_prices', 'unit'
    ];

    public function sale_prices()
    {
        return $this->hasMany('App\SalePrice');
    }

    public function units()
    {
        return $this->belongsToMany('App\Unit', 'sale_prices')->as('sale_prices')->withPivot(['detalle']);
    }

    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }

    public function unitsForHistorial()
    {
        return $this->belongsToMany('App\Unit', 'historial_prices')->as('historial_prices')->withPivot(['id', 'detalle', 'type'])->withTimestamps();
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
        $unwanted_array = array(
            'Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
            'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y'
        );

        $product_values = [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'formatted-name' => strtr($this->nombre, $unwanted_array),
            'tri-grams' => utf8_encode((new TNTIndexer)->buildTrigrams(strtr($this->nombre, $unwanted_array))),
        ];
        if (isset($this->other_names)) {
            $product_values = array_merge($product_values, $this->other_names);
            foreach ($this->other_names as $other_name) {
                array_push($product_values, strtr($other_name, $unwanted_array));
            }
        }
        if (isset($this->tags)) {
            foreach ($this->tags as $tag) {
                array_push($product_values, $tag->nombre);
                array_push($product_values, strtr($tag->nombre, $unwanted_array));
            }
        }

        return $product_values;
    }
}
