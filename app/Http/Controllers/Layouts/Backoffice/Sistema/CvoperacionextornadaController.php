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
                $where1[] = ['cvcompra.idtienda',$request->idagencia];
                $where2[] = ['cvventa.idtienda',$request->idagencia];
                $where3[] = ['cvgastoadministrativooperativo.idtienda',$request->idagencia];
                $where4[] = ['cvingresoextraordinario.idtienda',$request->idagencia];
            }
            if($request->fecha_inicio!=''){
                $where1[] = ['cvcompra.fechaeliminado','>=',$request->fecha_inicio.' 00:00:00'];
                $where2[] = ['cvventa.fechaeliminado','>=',$request->fecha_inicio.' 00:00:00'];
                $where3[] = ['cvgastoadministrativooperativo.fecha_eliminado','>=',$request->fecha_inicio.' 00:00:00'];
                $where4[] = ['cvingresoextraordinario.fecha_eliminado','>=',$request->fecha_inicio.' 00:00:00'];
            }
            if($request->fecha_fin!=''){
                $where1[] = ['cvcompra.fechaeliminado','<=',$request->fecha_fin.' 23:59:59'];
                $where2[] = ['cvventa.fechaeliminado','<=',$request->fecha_fin.' 23:59:59'];
                $where3[] = ['cvgastoadministrativooperativo.fecha_eliminado','<=',$request->fecha_fin.' 23:59:59'];
                $where4[] = ['cvingresoextraordinario.fecha_eliminado','<=',$request->fecha_fin.' 23:59:59'];
            }

            $cvcompras = DB::table('cvcompra')
                ->leftJoin('users as responsable','responsable.id','cvcompra.eliminado_idresponsable')
                ->leftJoin('tienda','tienda.id','cvcompra.idtienda')
                ->where('cvcompra.idestadoeliminado',2)
                ->where($where1)
                ->select(
                    DB::raw('CONCAT("ELIM. COMPRA") as operacion'),
                    'cvcompra.compra_cuenta as cuenta',
                    'cvcompra.fechaeliminado as fechaextorno',
                    DB::raw('CONCAT("--") as pago_cuota'),
                    'cvcompra.valorcompra as total_pagar',
                    'cvcompra.compra_banco as banco',
                    'cvcompra.compra_numerooperacion as numerooperacion',
                    'cvcompra.descripcion as nombrecliente',
                    'responsable.codigo as codigoresponsable',
                    'tienda.nombre as tiendanombre',
                )
                ->orderBy('fechaextorno','asc');

            $cvventas = DB::table('cvventa')
                ->leftJoin('users as responsable','responsable.id','cvventa.eliminado_idresponsable')
                ->leftJoin('tienda','tienda.id','cvventa.idtienda')
                ->where('cvventa.idestadoeliminado',2)
                ->where($where2)
                ->select(
                    DB::raw('CONCAT("ELIM. VENTA") as operacion'),
                    'cvcompra.compra_cuenta as cuenta',
                    'cvventa.fechaeliminado as fechaextorno',
                    DB::raw('CONCAT("--") as pago_cuota'),
                    'cvventa.venta_montoventa as total_pagar',
                    'cvventa.venta_banco as banco',
                    'cvventa.venta_numerooperacion as numerooperacion',
                    DB::raw('CONCAT("") as nombrecliente'),
                    'responsable.codigo as codigoresponsable',
                    'tienda.nombre as tiendanombre',
                )
                ->orderBy('fechaextorno','asc');
            
            $ingresoextraordinarios = DB::table('cvingresoextraordinario')
                ->leftJoin('users as responsable','responsable.id','cvingresoextraordinario.idresponsable')
                ->leftJoin('tienda','tienda.id','cvingresoextraordinario.idtienda')
                ->where('cvingresoextraordinario.idestadoeliminado',2)
                ->where($where4)
                ->select(
                    DB::raw('CONCAT("ELIM. INGRESO") as operacion'),
                    'cvingresoextraordinario.cuenta as cuenta',
                    'cvingresoextraordinario.fecha_eliminado as fechaextorno',
                    DB::raw('CONCAT("--") as pago_cuota'),
                    'cvingresoextraordinario.monto as total_pagar',
                    'cvingresoextraordinario.banco as banco',
                    'cvingresoextraordinario.numerooperacion as numerooperacion',
                    'cvingresoextraordinario.descripcion as nombrecliente',
                    'responsable.codigo as codigoresponsable',
                    'tienda.nombre as tiendanombre',
                )
                ->orderBy('fechaextorno','asc');
            
            $gastoadministrativooperativos = DB::table('cvgastoadministrativooperativo')
                ->leftJoin('users as responsable','responsable.id','cvgastoadministrativooperativo.idresponsable')
                ->leftJoin('tienda','tienda.id','cvgastoadministrativooperativo.idtienda')
                ->where('cvgastoadministrativooperativo.idestadoeliminado',2)
                ->union($cvcompras)
                ->union($cvventas)
                ->union($ingresoextraordinarios)
                ->where($where3)
                ->select(
                    DB::raw('CONCAT("ELIM. GASTO") as operacion'),
                    'cvgastoadministrativooperativo.cuenta as cuenta',
                    'cvgastoadministrativooperativo.fecha_eliminado as fechaextorno',
                    DB::raw('CONCAT("--") as pago_cuota'),
                    'cvgastoadministrativooperativo.monto as total_pagar',
                    'cvgastoadministrativooperativo.banco as banco',
                    'cvgastoadministrativooperativo.numerooperacion as numerooperacion',
                    'cvgastoadministrativooperativo.descripcion as nombrecliente',
                    'responsable.codigo as codigoresponsable',
                    'tienda.nombre as tiendanombre',
                )
                ->orderBy('fechaextorno','asc')
                ->get();

            $agencia = DB::table('tienda')->whereId($request->idagencia)->first();
            
            $pdf = PDF::loadView(sistema_view().'/cvoperacionextornada/pdf_extorno',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'gastoadministrativooperativos' => $gastoadministrativooperativos,
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
