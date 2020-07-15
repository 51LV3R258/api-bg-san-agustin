<?php

namespace App\Http\Controllers;

use App\Tag;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TagController extends Controller
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
            'tags' => Tag::all()
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
            'nombre' => 'required|string|unique:tags'
        ]);

        $tag = new Tag(['nombre' => $request->nombre]);
        if ($tag->save()) {
            $data = [
                'code' => 200,
                'message' => 'Tag guardado',
                // 'tag' => $tag->fresh(),
            ];
        } else {
            $data = [
                'code' => 400,
                'error' => 'Error al guardar Tag',
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

        $tag = Tag::findOrFail($id);

        $data = [
            'code' => 200,
            'tag' => $tag
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
        $tag = Tag::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|unique:tags,nombre,' . $id
        ]);

        $tag->nombre = $request->nombre;

        if ($tag->save()) {
            $data = [
                'code' => 200,
                'message' => 'Tag actualizado'
            ];
        } else {
            $data = [
                'code' => 400,
                'error' => 'Error al actualizar tag'
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
    }
}
