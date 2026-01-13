<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class MargendescuentoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        if(request('view') == 'tabla'){
            return view(sistema_view().'/margendescuento/tabla', compact('tienda'));
        }
    }

    public function create(Request $request,$idtienda)
    {
    }

    public function store(Request $request, $idtienda)
    {
    }

    public function show(Request $request, $idtienda, $id)
    {
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $agencias = DB::table('tienda')->get();

        if(request('view') == 'editar') {
            return view(sistema_view().'/margendescuento/edit', compact(
                'tienda',
                'agencias',
            ));
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        if(request('view') == 'editar'){
            $rules = [
                'margen_previsto' => 'required',
                'valor_descuento' => 'required',
            ];
            $messages = [
                'margen_previsto.required' => 'El "Margen Previsto" es Obligatorio.',
                'valor_descuento.required' => 'El "Valor de Descuento" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            if(request('margen_previsto') <= request('valor_descuento')){
               return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El margen previsto debe ser mayor al valor de descuento.'
                ]); 
            }

            configuracion_update($idtienda,'valor_descuento', request('valor_descuento'));
            configuracion_update($idtienda,'margen_previsto', request('margen_previsto'));

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]); 
        }
    } 

    public function destroy(Request $request, $idtienda, $id)
    {
    }
}
