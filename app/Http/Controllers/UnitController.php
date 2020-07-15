<?php

namespace App\Http\Controllers;

use App\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'code' => 200,
            'units' => Unit::all()
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
            'nombre' => 'required|string|unique:units'
        ]);

        $unit = new Unit(['nombre' => $request->nombre]);
        if ($unit->save()) {
            $data = [
                'code' => 200,
                'message' => 'Unidad guardada',
                // 'unit' => $unit->fresh(),
            ];
        } else {
            $data = [
                'code' => 400,
                'error' => 'Error al guardar unidad',
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
        $unit = Unit::findOrFail($id);

        $data = [
            'code' => 200,
            'unit' => $unit
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
        $unit = Unit::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|unique:units,nombre,' . $id
        ]);

        $unit->nombre = $request->nombre;

        if ($unit->save()) {
            $data = [
                'code' => 200,
                'message' => 'Unidad actualizada'
            ];
        } else {
            $data = [
                'code' => 400,
                'error' => 'Error al actualizar el unidad'
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
        //
    }
}
