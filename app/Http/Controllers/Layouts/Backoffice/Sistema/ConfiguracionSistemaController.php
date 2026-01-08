<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class ConfiguracionSistemaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/configuracionsistema/tabla', [
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
      
        if($request->input('view') == 'registrar') {
            configuracion_update($idtienda,'sistema_plantilla',$request->sistema_plantilla);
            configuracion_update($idtienda,'sistema_color',$request->sistema_color);
            configuracion_update($idtienda,'sistema_anchoticket',$request->sistema_anchoticket);
            configuracion_update($idtienda,'sistema_tipoletraticket',$request->sistema_tipoletraticket);
            configuracion_update($idtienda,'sistema_estadotiendavirtual',$request->sistema_estadotiendavirtual);
            configuracion_update($idtienda,'sistema_estadoinventario',$request->sistema_estadoinventario);
            configuracion_update($idtienda,'sistema_estadofinanza',$request->sistema_estadofinanza);
            configuracion_update($idtienda,'sistema_estadofacturacion',$request->sistema_estadofacturacion);
     
            $imagenlogin = uploadfile(configuracion($idtienda,'sistema_imagenfondologin')['valor'],
                                      $request->input('imagenant'),$request->file('imagen'),
                                      '/public/backoffice/tienda/'.$idtienda.'/imagenlogin/');

            configuracion_update($idtienda,'sistema_imagenfondologin',$imagenlogin);

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
