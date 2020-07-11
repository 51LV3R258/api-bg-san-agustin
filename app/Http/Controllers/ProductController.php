<?php

namespace App\Http\Controllers;

use App\Product;
use App\Rules\NoNegativeOrZero;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $products = new Product();

        if ($request->get('status')) {
            //Convirtiendo status a boolean
            $status = filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN);
            // return Product::whereStatus($status)->get();
            $products = $products->whereStatus($status);
        }

        if ($request->get('tags')) {
            $products = $products->whereHas('tags', function ($q) {
                $q->whereIn('id', json_decode(request('tags'), true));
            });
        }
        if ($request->get('units')) {
            $products = $products->whereHas('units', function ($q) {
                $q->whereIn('id', json_decode(request('units'), true));
            });
        }

        $data = [
            'code' => 200,
            'products' => $products->get()
        ];

        return response()->json($data, $data['code']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'nombre' => 'string|required|unique:products',
            'other_names' => 'array|nullable',
            'unit_id' => 'integer|nullable|exists:units,id',
            'purchase_price' => ['numeric', 'nullable', new NoNegativeOrZero, 'max:9999'],
            'imagen' => 'string|nullable',
            'tag_ids' => 'array|nullable',
            'tag_ids.*' => 'integer|distinct|exists:tags,id',
            'sale_prices' => 'array|required',
            'sale_prices.*.unit_id' => 'required|integer|distinct|exists:units,id',
            'sale_prices.*.detalle' => ['required', 'numeric', new NoNegativeOrZero, 'max:9999'],
        ], $this->messages);

        $product = new Product();
        $product->nombre = ucwords(strtolower($request->nombre));
        $product->other_names = $request->other_names;
        $product->imagen = $request->imagen;

        if ($product->save()) {
            $product->tags()->sync($request->tag_ids);
            //Vincular precios de producto
            $product->units()->attach($request->sale_prices);
            //Vincular a historial de precios
            //type: SELL el array de ventas
            $product->unitsForHistorial()->attach($request->sale_prices);
            if (isset($request->unit_id) && isset($request->purchase_price)) {
                //Data de costo del producto
                $product->unit_id = $request->unit_id;
                $product->purchase_price = $request->purchase_price;

                //type: BUY es el costo de compra
                $product->unitsForHistorial()->attach([array('unit_id' => $request->unit_id, 'detalle' => $request->purchase_price, 'type' => 'BUY')]);
            }
            $product = $product->fresh();
            $data = [
                'code' => 200,
                'message' => 'Producto guardado'
                // 'product' => $product
            ];
        } else {
            $data = [
                'code' => 400,
                'error' => 'Error al guardar producto'
            ];
        }

        return response()->json($data, $data['code']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Producto no encontrado'
            ], 404);
        }

        $data = [
            'code' => 200,
            'product' => $product
        ];
        return response()->json($data, $data['code']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Producto no encontrado'
            ], 404);
        }

        $request->validate([
            'nombre' => 'required|unique:products,nombre,' . $id,
            'other_names' => 'array|nullable',
            'unit_id' => 'integer|nullable|exists:units,id',
            'purchase_price' => ['numeric', 'nullable', new NoNegativeOrZero, 'max:9999'],
            'imagen' => 'string|nullable',
            'tag_ids' => 'array|nullable',
            'tag_ids.*' => 'integer|distinct|exists:tags,id',
            'sale_prices' => 'array|required',
            'sale_prices.*.unit_id' => 'required|integer|distinct|exists:units,id',
            'sale_prices.*.detalle' => ['required', 'numeric', new NoNegativeOrZero, 'max:9999']
        ], $this->messages);

        $product->nombre = ucwords(strtolower($request->nombre));
        $product->other_names = $request->other_names;
        $product->imagen = $request->imagen;


        $product->tags()->sync($request->tag_ids);
        $product->units()->sync($request->sale_prices);

        /* Determinar si se agregará el precio de venta al historial*/
        $sell_prices_to_update = array();
        foreach ($request->sale_prices as $sale_price) {
            $latest_sell_price = $product->historial_prices
                ->where('unit_id', $sale_price['unit_id'])->where('type', 'SELL')->sortBy('id')
                ->last();

            if (isset($latest_sell_price)) {
                if ($latest_sell_price->detalle != $sale_price['detalle']) {
                    array_push($sell_prices_to_update, $sale_price);
                }
            } else {
                array_push($sell_prices_to_update, $sale_price);
            }
        }
        $product->unitsForHistorial()->attach($sell_prices_to_update);

        if (isset($request->purchase_price) && isset($request->unit_id)) {
            //Data de costo del producto
            $product->unit_id = $request->unit_id;
            $product->purchase_price = $request->purchase_price;

            /* Determinar si se agregará el precio de compra al historial */
            $latest_purchase_price = $product->historial_prices->where('unit_id', $request->unit_id)->where('type', 'BUY')->sortBy('id')->last();
            if (isset($latest_purchase_price)) {
                if ($latest_purchase_price->detalle != $request->purchase_price) {
                    $product->unitsForHistorial()->attach([array('unit_id' => $request->unit_id, 'detalle' => $request->purchase_price, 'type' => 'BUY')]);
                }
            } else {
                $product->unitsForHistorial()->attach([array('unit_id' => $request->unit_id, 'detalle' => $request->purchase_price, 'type' => 'BUY')]);
            }
        } else {
            $product->unit_id = null;
            $product->purchase_price = null;
        }

        if ($product->touch()) {
            $product = $product->fresh();
            $data = [
                'code' => 200,
                'message' => 'Producto actualizado'
                // 'product' => $product->fresh()
            ];
        } else {
            $data = [
                'code' => 400,
                'error' => 'Error al actualizar el producto'
            ];
        }
        return response()->json($data, $data['code']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Producto no encontrado'
            ], 404);
        }
        $product->tags()->detach();
        $product->units()->detach();
        $product->unitsForHistorial()->detach();

        if ($product->delete()) {
            $data = [
                'code' => 200,
                'message' => 'Producto eliminado correctamente'
            ];
        } else {
            $data = [
                'code' => 400,
                'error' => 'No se pudo eliminar el producto'
            ];
        }
        return response()->json($data, $data['code']);
    }


    /**
     * Search for resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {

        $request->validate([
            'query' => 'required|string',
            'tags' => 'sometimes|json',
            'units' => 'sometimes|json'
        ]);

        $products = new Product();
        $products = $products->search($request->get('query'));

        //No funcional el filtro n por el momento
        /* if ($request->get('tags') || $request->get('units')) {
            $productsIds = $products->raw()['ids'];
            // $cleanProducts = new Product();
            $products = Product::whereIn('id', $productsIds);
            if ($request->get('tags')) {
                $products = $products->whereHas('tags', function ($q) {
                    $q->whereIn('id', json_decode(request('tags'), true));
                });
            }
            if ($request->get('units')) {
                $products = $products->whereHas('units', function ($q) {
                    $q->whereIn('id', json_decode(request('units'), true));
                });
            }
        } */

        $data = [
            'code' => 200,
            'products' => $products->paginate(10)
        ];

        return response()->json($data, $data['code']);
    }


    public $messages = [
        'purchase_price.max' => 'El precio de compra es demasiado alto',
        'sale_prices.*.detalle.max' => 'Algún precio de venta es demasiado alto'
    ];
}
