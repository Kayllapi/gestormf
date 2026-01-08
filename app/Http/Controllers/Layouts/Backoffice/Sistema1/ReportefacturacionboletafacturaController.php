<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReportefacturacionboletafacturaExport;
use App\Exports\ReportefacturacionboletafacturaSUNATExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReportefacturacionboletafacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $where  = [];
      
        if($request->input('venta')!= ''){
          $where[] = ['venta.codigo',$request->input('venta')];
        }
      
        if($request->input('tipoCompbrobante')!= ''){
          $where[] = ['s_facturacionboletafactura.venta_tipodocumento', $request->input('tipoCompbrobante')];
        }
      
        if($request->input('serie')!= ''){
           $where[] = ['s_facturacionboletafactura.venta_serie',$request->input('serie')];
        }
      
        if($request->input('correlativo')!= ''){
          $where[] = ['s_facturacionboletafactura.venta_correlativo',$request->input('correlativo')];
        }
      
        if($request->input('idcliente')!=''){
            $where[] = ['cliente.id',$request->input('idcliente')];
        }
       if($request->input('idresponsable')!=''){
            $where[] = ['responsable.id',$request->input('idresponsable')];
        }
         if($request->input('idestado')!=''){
            $where[] = ['s_facturacionrespuesta.estado',$request->input('idestado')];
        }
        if($request->input('moneda')!= ''){
          $where[] = ['s_moneda.nombre', $request->input('moneda')];
        }
      
       if($request->input('idagencia')!=''){
            $where[] = ['s_facturacionboletafactura.idagencia',$request->input('idagencia')];
        }
      
        if($request->input('fechainicio')!=''){
            $where[] = ['s_facturacionboletafactura.venta_fechaemision','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
            $where[] = ['s_facturacionboletafactura.venta_fechaemision','<=',$request->input('fechafin').' 23:59:59'];
        }
        if($request->input('tipo')=='excel'){
            $facturacionboletafactura = DB::table('s_facturacionboletafactura')
            ->join('s_moneda','s_moneda.codigo','s_facturacionboletafactura.venta_tipomoneda')
            ->join('users as responsable','responsable.id','s_facturacionboletafactura.idusuarioresponsable')
            ->join('users as cliente','cliente.id','s_facturacionboletafactura.idusuariocliente')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionboletafactura.idfacturacionrespuesta')
            ->leftJoin('s_facturacioncomunicacionbajadetalle','s_facturacioncomunicacionbajadetalle.idfacturacionboletafactura','s_facturacionboletafactura.id')
            ->leftJoin('s_facturacioncomunicacionbaja','s_facturacioncomunicacionbaja.id','s_facturacioncomunicacionbajadetalle.idfacturacioncomunicacionbaja')
            ->leftJoin('s_facturacionresumendiariodetalle','s_facturacionresumendiariodetalle.idfacturacionboletafactura','s_facturacionboletafactura.id')
            ->leftJoin('s_facturacionresumendiario','s_facturacionresumendiario.id','s_facturacionresumendiariodetalle.idfacturacionresumendiario')
            ->leftJoin('s_venta as venta','venta.id','s_facturacionboletafactura.idventa')
            ->where('s_facturacionboletafactura.idtienda',$idtienda)
            ->where($where)
            ->select(
                's_facturacionboletafactura.*',
                's_facturacionrespuesta.codigo as respuestacodigo',
                's_facturacionrespuesta.estado as respuestaestado',
                'responsable.nombre as responsablenombre',
                'responsable.apellidos as responsableapellido',
                'venta.codigo as ventacodigo',
                's_facturacioncomunicacionbaja.comunicacionbaja_correlativo as comunicacionbaja_correlativo',
                's_facturacionresumendiariodetalle.estado as resumen_estado',
                's_facturacionresumendiario.resumen_correlativo as resumen_correlativo'
            )
            ->orderBy('s_facturacionboletafactura.id','desc')
            ->get();
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Boleta y Factura';
            $fecha  = '';

            if($inicio != '' && $fin != ''){
              $fecha = '('.$inicio.' hasta '.$fin.')';
            }
            elseif($inicio != ''){                
              $fecha = '('.$inicio.')';
            }
            elseif($fin != ''){
              $fecha = '('.$fin.')';
            }
            else{
              $fecha = '';
            }

            return Excel::download(new ReportefacturacionboletafacturaExport($facturacionboletafactura, $inicio, $fin, $titulo), $titulo.' '.$fecha.'.xls');
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
            
        }
        elseif($request->input('tipo')=='excel_SUNAT'){
           $facturacionboletafactura = DB::table('s_facturacionboletafactura')
            ->join('s_moneda','s_moneda.codigo','s_facturacionboletafactura.venta_tipomoneda')
            ->join('users as responsable','responsable.id','s_facturacionboletafactura.idusuarioresponsable')
            ->join('users as cliente','cliente.id','s_facturacionboletafactura.idusuariocliente')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionboletafactura.idfacturacionrespuesta')
            ->leftJoin('s_facturacioncomunicacionbajadetalle','s_facturacioncomunicacionbajadetalle.idfacturacionboletafactura','s_facturacionboletafactura.id')
            ->leftJoin('s_facturacioncomunicacionbaja','s_facturacioncomunicacionbaja.id','s_facturacioncomunicacionbajadetalle.idfacturacioncomunicacionbaja')
            ->leftJoin('s_facturacionresumendiariodetalle','s_facturacionresumendiariodetalle.idfacturacionboletafactura','s_facturacionboletafactura.id')
            ->leftJoin('s_facturacionresumendiario','s_facturacionresumendiario.id','s_facturacionresumendiariodetalle.idfacturacionresumendiario')
            ->leftJoin('s_venta as venta','venta.id','s_facturacionboletafactura.idventa')
            ->where('s_facturacionboletafactura.idtienda',$idtienda)
            ->where($where)
            ->select(
                's_facturacionboletafactura.*',
                's_facturacionrespuesta.codigo as respuestacodigo',
                's_facturacionrespuesta.estado as respuestaestado',
                'responsable.nombre as responsablenombre',
                'responsable.apellidos as responsableapellido',
                'venta.codigo as ventacodigo',
                's_facturacioncomunicacionbaja.comunicacionbaja_correlativo as comunicacionbaja_correlativo',
                's_facturacionresumendiariodetalle.estado as resumen_estado',
                's_facturacionresumendiario.resumen_correlativo as resumen_correlativo'
            )
            ->orderBy('s_facturacionboletafactura.venta_serie','asc')
            ->orderBy('s_facturacionboletafactura.venta_correlativo','asc')
            ->get();
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'REPORTE DE FACTURAS Y BOLETAS';
            $fecha  = '';

            if($inicio != '' && $fin != ''){
              $fecha = '('.$inicio.' hasta '.$fin.')';
            }
            elseif($inicio != ''){                
              $fecha = '('.$inicio.')';
            }
            elseif($fin != ''){
              $fecha = '('.$fin.')';
            }
            else{
              $fecha = '';
            }

            return Excel::download(new ReportefacturacionboletafacturaSUNATExport($facturacionboletafactura, $inicio, $fin, $titulo), $titulo.' '.$fecha.'.xls');
        }
        else{
        
        $facturacionboletafactura = DB::table('s_facturacionboletafactura')
            ->join('s_moneda','s_moneda.codigo','s_facturacionboletafactura.venta_tipomoneda')
            ->join('users as responsable','responsable.id','s_facturacionboletafactura.idusuarioresponsable')
            ->join('users as cliente','cliente.id','s_facturacionboletafactura.idusuariocliente')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionboletafactura.idfacturacionrespuesta')
            ->leftJoin('s_facturacioncomunicacionbajadetalle','s_facturacioncomunicacionbajadetalle.idfacturacionboletafactura','s_facturacionboletafactura.id')
            ->leftJoin('s_facturacioncomunicacionbaja','s_facturacioncomunicacionbaja.id','s_facturacioncomunicacionbajadetalle.idfacturacioncomunicacionbaja')
            ->leftJoin('s_facturacionresumendiariodetalle','s_facturacionresumendiariodetalle.idfacturacionboletafactura','s_facturacionboletafactura.id')
            ->leftJoin('s_facturacionresumendiario','s_facturacionresumendiario.id','s_facturacionresumendiariodetalle.idfacturacionresumendiario')
            ->leftJoin('s_venta as venta','venta.id','s_facturacionboletafactura.idventa')
            ->where('s_facturacionboletafactura.idtienda',$idtienda)
            ->where($where)
            ->select(
                's_facturacionboletafactura.*',
                's_facturacionrespuesta.codigo as respuestacodigo',
                's_facturacionrespuesta.estado as respuestaestado',
                'responsable.nombre as responsablenombre',
                'responsable.apellidos as responsableapellido',
                'venta.codigo as ventacodigo',
                's_facturacioncomunicacionbaja.comunicacionbaja_correlativo as comunicacionbaja_correlativo',
                's_facturacionresumendiariodetalle.estado as resumen_estado',
                's_facturacionresumendiario.resumen_correlativo as resumen_correlativo'
            )
            ->orderBy('s_facturacionboletafactura.venta_serie','asc')
            ->orderBy('s_facturacionboletafactura.venta_correlativo','asc')
            ->paginate(10);
        
        $agencia = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
        $comprobante = DB::table('s_tipocomprobante')->get();
        $tipopersonas = DB::table('tipopersona')->get();
        $tipoentregas = DB::table('s_tipoentrega')->get();
        $moneda         = DB::table('s_moneda')->get();
      
        return view('layouts/backoffice/tienda/sistema/reportefacturacionboletafactura/index',[
            'tienda'                   => $tienda,
            'facturacionboletafactura' => $facturacionboletafactura,
            'comprobante'              => $comprobante,
            'agencia'                  => $agencia,
            'moneda'                   => $moneda,
        ]);
             }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
       if($id=='showlistarusuario'){
            $usuarios = DB::table('users')
                ->where('idtienda',$idtienda)
                ->where('users.nombre','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('idtienda',$idtienda)
                ->where('users.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('idtienda',$idtienda)
                ->where('users.identificacion','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'users.id as id',
                   DB::raw('CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre) as text')
                )
                ->get();
            return $usuarios;
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }
}
