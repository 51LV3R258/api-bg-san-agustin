<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->get('status')) {
            //Convirtiendo status a boolean
            $status = filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN);
            return Product::where('status', $status)->get();
        }

        return Product::all();
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
            'nombre' => 'required|unique:products',
            'other_names' => 'array|nullable',
            'imagen' => 'string|nullable',
            'tag_ids' => 'array|nullable',
            'tag_ids.*' => 'integer|distinct|exists:tags,id',
            'prices' => 'array|required',
            'prices.*.unit_id' => 'required|integer|distinct|exists:units,id',
            'prices.*.detalle' => 'required|numeric'
        ]);

        $product = new Product();
        $product->nombre = $request->nombre;
        if (isset($request->other_names)) {
            $product->other_names = $request->other_names;
        }
        if (isset($request->image)) {
            $product->imagen = $request->imagen;
        }

        if ($product->save()) {
            //Si se ha ingresado tags vincularlo
            if (isset($request->tag_ids)) {
                $product->tags()->attach($request->tag_ids);
            }
            //Vincular precios de producto
            $product->units()->attach($request->prices);
            //Vincular a historial de precios
            $product->unitsForHistorial()->attach($request->prices);
            $data = [
                'code' => 200,
                'product' => $product->load(['tags', 'prices'])
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
        //
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
            'nombre' => 'sometimes|unique:products,nombre,' . $id,
            'other_names' => 'array|nullable',
            'imagen' => 'string|nullable',
            'tag_ids' => 'array|nullable',
            'tag_ids.*' => 'integer|distinct|exists:tags,id',
            'prices' => 'array|required',
            'prices.*.unit_id' => 'required|integer|distinct|exists:units,id',
            'prices.*.detalle' => 'required|numeric'
        ]);

        $product->nombre = $request->nombre;
        if (isset($request->other_names)) {
            $product->other_names = $request->other_names;
        }
        if (isset($request->image)) {
            $product->imagen = $request->imagen;
        }

        if (isset($request->tag_ids)) {
            $product->tags()->sync($request->tag_ids);
        }
        $product->units()->sync($request->prices);
        $product->unitsForHistorial()->attach($request->prices);

        if ($product->touch()) {
            $data = [
                'code' => 200,
                'product' => $product->load(['tags', 'prices'])
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
}
