<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class ConfiguracionGeneralController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/configuraciongeneral/tabla', [
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
          
            // actualizado modulos
            DB::table('s_accesorapido')->where('idusers',Auth::user()->id)->delete();
            $list = explode(',',$request->input('idmodulos'));
            $idmodulos = '';
            for ($i=1; $i < count($list); $i++) { 
                $idmodulos = $idmodulos.$list[$i];
                DB::table('s_accesorapido')->insert([
                    'idusers' => Auth::user()->id,
                    'idmodulo' => $list[$i],
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
            }

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
