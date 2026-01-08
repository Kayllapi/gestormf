<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class ConfiguracionInventarioController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/configuracioninventario/tabla', [
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
          
            configuracion_update($idtienda,'sistema_tipocodigoproducto',$request->sistema_tipocodigoproducto);
            configuracion_update($idtienda,'sistema_moneda_usar',$request->sistema_moneda_usar);
            if($request->sistema_moneda_usar==1 or $request->sistema_moneda_usar==2){
                configuracion_update($idtienda,'sistema_moneda_pordefecto',$request->sistema_moneda_usar);
            }elseif($request->sistema_moneda_usar==3){
                configuracion_update($idtienda,'sistema_moneda_pordefecto',$request->sistema_moneda_pordefecto);
            }

            configuracion_update($idtienda,'sistema_tipounidadmedida',$request->sistema_tipounidadmedida);
            configuracion_update($idtienda,'sistema_estadostock',$request->sistema_estadostock);
            configuracion_update($idtienda,'sistema_estadopreciominimo',$request->sistema_estadopreciominimo);
            configuracion_update($idtienda,'sistema_estadodescuento',$request->sistema_estadodescuento);
          
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
