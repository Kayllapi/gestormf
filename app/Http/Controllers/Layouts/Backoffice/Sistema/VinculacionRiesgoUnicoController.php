<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use App\Exports\ReportecarteracreditoExport;
use Maatwebsite\Excel\Facades\Excel;

class VinculacionRiesgoUnicoController extends Controller
{
    public function __construct()
    {
        //
    }
    public function index(Request $request,$idtienda)
    {
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            
            $agencias = DB::table('tienda')->get();
          
            return view(sistema_view().'/vinculacionriesgounico/tabla',[
              'tienda' => $tienda,
              'agencias' => $agencias,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
    }
  
    public function store(Request $request, $idtienda)
    {
      
        if($request->input('view') == 'registrar') {
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if ($id == 'showtable') {
            $filas = \App\Support\VinculacionRiesgoUnico::filas($request->idcliente);

            return response()->json([
                'html' => view(sistema_view().'/vinculacionriesgounico/filas', compact('filas'))->render(),
            ]);
        }
        else if($id == 'showcliente'){
            $cliente = DB::table('users')
            ->leftJoin('s_users_prestamo','s_users_prestamo.id_s_users','users.id')
              ->leftJoin('f_fuenteingreso','f_fuenteingreso.id','s_users_prestamo.idfuenteingreso')
              ->where('users.id',$request->idcliente)
              ->select(
                'users.id',
                'users.direccion as direcciondomicilio',
                'users.db_idubigeo',
                's_users_prestamo.idfuenteingreso',
                's_users_prestamo.db_idfuenteingreso',
                's_users_prestamo.idgiro_ac_economica',
                's_users_prestamo.db_idgiro_ac_economica',
                's_users_prestamo.cargo_laboral_cliente',
                's_users_prestamo.razonsocial_laboral_cliente',
                'f_fuenteingreso.nombre as fuente_ingreso'
                )
              ->first();
            $s_users_prestamo = DB::table('s_users_prestamo')
              ->where('id_s_users',$request->idcliente)
              ->select('s_users_prestamo.direccion_ac_economica as direccionnegocio','db_idubigeo_ac_economica', 'idubigeo_ac_economica')->first();

            $fuenteingreso = $cliente ? ($cliente->fuente_ingreso != null && $cliente->fuente_ingreso != '' ? $cliente->fuente_ingreso : $cliente->db_idfuenteingreso) : '';
            return response()->json([
                'direcciondomicilio' => $cliente->direcciondomicilio.', '.$cliente->db_idubigeo,
                'direccionnegocio'   => $s_users_prestamo->direccionnegocio.', '.$s_users_prestamo->db_idubigeo_ac_economica,
                'fuenteingreso' => $fuenteingreso,
                'idfuenteingreso' => $cliente->idfuenteingreso ?? null,
                'giro_economico' => [1 => 'COMERCIO', 2 => 'SERVICIO', 3 => 'PRODUCCION'][$cliente->idgiro_ac_economica ?? 0] ?? ($cliente->db_idgiro_ac_economica ?? ''),
                'cargo' => $cliente->cargo_laboral_cliente ?? '',
                'empresa' => $cliente->razonsocial_laboral_cliente ?? '',
            ]);
        }
        
        if($id == 'show_credito'){
          $creditos = DB::table('users')
                            ->where('users.identificacion','LIKE','%'.$request->buscar.'%')
                            ->orWhere('users.nombrecompleto','LIKE','%'.$request->buscar.'%')
                            ->select(
                                'users.id as idcliente',
                                'users.identificacion as identificacion',
                                'users.nombrecompleto as nombrecliente',
                            )
                            ->get();
     
            $data = [];
            foreach($creditos as $value){
                $data[] = [
                    'id' => $value->idcliente,
                    'text' => $value->identificacion.' - '.$value->nombrecliente,
                ];
            }
          return $data;
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
 
      if($request->input('view') == 'exportar') {
            abort_unless(DB::table('users')->where('id', $request->idcliente)->exists(), 404, 'Seleccione un cliente válido para generar el reporte.');
            return view(sistema_view().'/vinculacionriesgounico/exportar',[
                'tienda' => $tienda,
                'idcliente' => $request->idcliente,
            ]);
        }
        else if( $request->input('view') == 'exportar_pdf' ){
          $cliente = DB::table('users')
              ->where('users.id', $request->idcliente)
              ->select('users.id', 'users.identificacion', 'users.nombrecompleto', 'users.direccion as direcciondomicilio', 'db_idubigeo')
              ->first();
          abort_unless($cliente, 404, 'Seleccione un cliente válido para generar el reporte.');
            
            $s_users_prestamo = DB::table('s_users_prestamo')
              ->where('id_s_users', $request->idcliente)
              ->select('direccion_ac_economica as direccionnegocio', 'db_idubigeo_ac_economica')->first();
            $filas = \App\Support\VinculacionRiesgoUnico::filas($request->idcliente);
            $pdf = PDF::loadView(sistema_view().'/vinculacionriesgounico/exportar_pdf',[
                'tienda' => $tienda,
                'filas' => $filas,
                'idcliente' => $request->idcliente,
                's_users_prestamo' => $s_users_prestamo,
                'cliente' => $cliente->identificacion.' - '.$cliente->nombrecompleto,
                'direcciondomicilio' => $cliente->direcciondomicilio.', '.$cliente->db_idubigeo,
                'direccionnegocio'   => trim(($s_users_prestamo->direccionnegocio ?? '').', '.($s_users_prestamo->db_idubigeo_ac_economica ?? ''), ', '),
            ]); 
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('CARTERA_DE_CREDITO.pdf');
        }  
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        
    
    }

    public function destroy(Request $request, $idtienda, $id)
    {
    }
}
