<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class ConfiguracionFacturacionController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/configuracionfacturacion/tabla', [
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
            configuracion_update($idtienda,'facturacion_igv',$request->facturacion_igv);
            configuracion_update($idtienda,'facturacion_monedapordefecto',$request->facturacion_monedapordefecto);
            configuracion_update($idtienda,'facturacion_clientepordefecto',$request->facturacion_clientepordefecto);
            configuracion_update($idtienda,'facturacion_empresapordefecto',$request->facturacion_empresapordefecto);
            configuracion_update($idtienda,'facturacion_comprobantepordefecto',$request->facturacion_comprobantepordefecto);
          
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
