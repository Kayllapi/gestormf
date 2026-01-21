<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class CvoperacionextornadaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            
            $agencias = DB::table('tienda')->get();
            return view(sistema_view().'/cvoperacionextornada/tabla',[
                'tienda' => $tienda,
                'agencias' => $agencias,
            ]);
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
        if($request->input('view') == 'pdf_extorno'){
            
          $where1 = [];
          $where2 = [];
          $where3 = [];
          $where4 = [];
          if($request->idagencia!=''){
              $where1[] = ['credito_cobranzacuota.idtienda',$request->idagencia];
              $where2[] = ['credito.idtienda',$request->idagencia];
              $where3[] = ['gastoadministrativooperativo.idtienda',$request->idagencia];
              $where4[] = ['ingresoextraordinario.idtienda',$request->idagencia];
          }
          if($request->fecha_inicio!=''){
              $where1[] = ['credito_cobranzacuota.fechaextorno','>=',$request->fecha_inicio.' 00:00:00'];
              $where2[] = ['credito.fecha_eliminado','>=',$request->fecha_inicio.' 00:00:00'];
              $where3[] = ['gastoadministrativooperativo.fecha_eliminado','>=',$request->fecha_inicio.' 00:00:00'];
              $where4[] = ['ingresoextraordinario.fecha_eliminado','>=',$request->fecha_inicio.' 00:00:00'];
          }
          if($request->fecha_fin!=''){
              $where1[] = ['credito_cobranzacuota.fechaextorno','<=',$request->fecha_fin.' 23:59:59'];
              $where2[] = ['credito.fecha_eliminado','<=',$request->fecha_fin.' 23:59:59'];
              $where3[] = ['gastoadministrativooperativo.fecha_eliminado','<=',$request->fecha_fin.' 23:59:59'];
              $where4[] = ['ingresoextraordinario.fecha_eliminado','<=',$request->fecha_fin.' 23:59:59'];
          }
          
          $ingresoextraordinarios = DB::table('ingresoextraordinario')
              ->leftJoin('users as responsable','responsable.id','ingresoextraordinario.idresponsable')
              ->leftJoin('tienda','tienda.id','ingresoextraordinario.idtienda')
              ->where('ingresoextraordinario.idestadoeliminado',2)
              ->where($where4)
              ->select(
                  DB::raw('CONCAT("ELIM. INGRESO") as operacion'),
                  DB::raw('CONCAT(ingresoextraordinario.codigoprefijo,ingresoextraordinario.codigo) as cuenta'),
                  'ingresoextraordinario.fecha_eliminado as fechaextorno',
                  DB::raw('CONCAT("--") as pago_cuota'),
                  'ingresoextraordinario.monto as total_pagar',
                  'ingresoextraordinario.banco as banco',
                  'ingresoextraordinario.numerooperacion as numerooperacion',
                  'ingresoextraordinario.descripcion as nombrecliente',
                  //DB::raw('CONCAT("--") as nombrecliente'),
                  'responsable.codigo as codigoresponsable',
                  'tienda.nombre as tiendanombre',
              )
              ->orderBy('fechaextorno','asc');
          
          $gastoadministrativooperativos = DB::table('gastoadministrativooperativo')
              ->leftJoin('users as responsable','responsable.id','gastoadministrativooperativo.idresponsable')
              ->leftJoin('tienda','tienda.id','gastoadministrativooperativo.idtienda')
              ->where('gastoadministrativooperativo.idestadoeliminado',2)
              ->where($where3)
              ->select(
                  DB::raw('CONCAT("ELIM. GASTO") as operacion'),
                  DB::raw('CONCAT(gastoadministrativooperativo.codigoprefijo,gastoadministrativooperativo.codigo) as cuenta'),
                  'gastoadministrativooperativo.fecha_eliminado as fechaextorno',
                  DB::raw('CONCAT("--") as pago_cuota'),
                  'gastoadministrativooperativo.monto as total_pagar',
                  'gastoadministrativooperativo.banco as banco',
                  'gastoadministrativooperativo.numerooperacion as numerooperacion',
                  'gastoadministrativooperativo.descripcion as nombrecliente',
                  //DB::raw('CONCAT("--") as nombrecliente'),
                  'responsable.codigo as codigoresponsable',
                  'tienda.nombre as tiendanombre',
              )
              ->orderBy('fechaextorno','asc');
          
          $creditos = DB::table('credito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->leftJoin('credito_formapago','credito_formapago.idcredito','credito.id')
              ->leftJoin('users as responsable','responsable.id','credito.idadministrador')
              ->leftJoin('tienda','tienda.id','credito.idtienda')
              ->where('credito.estado','ELIMINADO')
              ->where($where2)
              ->select(
                  DB::raw('CONCAT("ELIM. CRÉDITO") as operacion'),
                  DB::raw('CONCAT("C",credito.cuenta) as cuenta'),
                  'credito.fecha_eliminado as fechaextorno',
                  'credito.cuotas as pago_cuota',
                  'credito.monto_solicitado as total_pagar',
                  'credito_formapago.banco as banco',
                  'credito_formapago.numerooperacion as numerooperacion',
                  'cliente.nombrecompleto as nombrecliente',
                  'responsable.codigo as codigoresponsable',
                  'tienda.nombre as tiendanombre',
              )
              ->orderBy('fechaextorno','asc');
          
          $creditos_extornados = DB::table('credito_cobranzacuota')
              ->join('credito','credito.id','credito_cobranzacuota.idcredito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->leftJoin('users as responsable','responsable.id','credito_cobranzacuota.idresponsableextorno')
              ->leftJoin('tienda','tienda.id','credito_cobranzacuota.idtienda')
              ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
              ->where('credito_cobranzacuota.idestadoextorno',2)
              ->where($where1)
              ->union($creditos)
              ->union($gastoadministrativooperativos)
              ->union($ingresoextraordinarios)
              ->select(
                  DB::raw('CONCAT("EXT. CUOTAS") as operacion'),
                  DB::raw('CONCAT("C",credito.cuenta) as cuenta'),
                  'credito_cobranzacuota.fechaextorno as fechaextorno',
                  'credito_cobranzacuota.pago_cuota as pago_cuota',
                  'credito_cobranzacuota.total_pagar as total_pagar',
                  'credito_cobranzacuota.banco as banco',
                  'credito_cobranzacuota.numerooperacion as numerooperacion',
                  'cliente.nombrecompleto as nombrecliente',
                  'responsable.codigo as codigoresponsable',
                  'tienda.nombre as tiendanombre',
              )
              ->orderBy('fechaextorno','asc')
              ->get();
          
          $agencia = DB::table('tienda')->whereId($request->idagencia)->first();
          
          $pdf = PDF::loadView(sistema_view().'/cvoperacionextornada/pdf_extorno',[
              'tienda' => $tienda,
              'agencia' => $agencia,
              'creditos_extornados' => $creditos_extornados,
              'fecha_inicio' => date("d-m-Y",strtotime(date($request->fecha_inicio))),
              'fecha_fin' => date("d-m-Y",strtotime(date($request->fecha_fin))),
          ]); 
          $pdf->setPaper('A4', 'landscape');
          return $pdf->stream('OPERACIONES_EXTORNADAS.pdf');
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        
    }


    public function destroy(Request $request, $idtienda, $id)
    {

    }
}
