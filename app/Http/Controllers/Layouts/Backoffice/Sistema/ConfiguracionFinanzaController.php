<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class ConfiguracionFinanzaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/configuracionfinanza/tabla', [
                'tienda' => $tienda
            ]);
        }
    }

    public function create(Request $request,$idtienda)
    {
        //
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if ($request->input('view') == 'registrar') {
          
            configuracion_update($idtienda,'sistema_estadopreciounitario',$request->sistema_estadopreciounitario);
            configuracion_update($idtienda,'sistema_estadoformapago',$request->sistema_estadoformapago);
            configuracion_update($idtienda,'sistema_estadodescuentoventatotal',$request->sistema_estadodescuentoventatotal);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        //
    }

    public function edit(Request $request, $idtienda, $id)
    {
        //
    }

    public function update(Request $request, $idtienda, $id)
    {
        //
    } 

    public function destroy(Request $request, $idtienda, $id)
    {
        //
    }
}
