<?php

namespace App\Http\Controllers;

use App\Product;
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
        $validate = Validator::make($request->all(), [
            'nombre' => 'required',
            'other_names' => 'array|nullable',
            'imagen' => 'required',
            'tag_ids' => 'array|nullable',
            'tag_ids.*' => 'integer|distinct|exists:tags,id',
            'prices' => 'array|required',
            'prices.*.unit_id' => 'required|integer|distinct|exists:units,id',
            'prices.*.detalle' => 'required|numeric'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'error' => $validate->errors()
            ], 400);
        }

        $product = new Product();
        $product->nombre = $request->nombre;
        if (isset($request->other_names)) {
            $product->other_names = $request->other_names;
        }
        $product->imagen = $request->imagen;

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
                'product' => $product
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
        //
    }
}
