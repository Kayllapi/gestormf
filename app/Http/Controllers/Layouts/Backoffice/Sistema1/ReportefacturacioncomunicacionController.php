<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReportefacturacioncomunicacionExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReportefacturacioncomunicacionController extends Controller
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
      
      if($request->input('comprobante') != ''){
        $where[] = ['detalle.tipodocumento', $request->input('comprobante')];
       }
       if($request->input('correlativo') != ''){
        $where[] = ['s_facturacioncomunicacionbaja.comunicacionbaja_correlativo', $request->input('correlativo')];
       }
      
      if($request->input('idagencia')!=''){
            $where[] = ['s_facturacioncomunicacionbaja.idagencia',$request->input('idagencia')];
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
      
        if($request->input('fechainicio')!=''){
            $where[] = ['s_facturacioncomunicacionbaja.comunicacionbaja_fechageneracion','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
            $where[] = ['s_facturacioncomunicacionbaja.comunicacionbaja_fechageneracion','<=',$request->input('fechafin').' 24:00:00'];
        }
       if($request->input('tipo')=='excel'){
            $facturacioncomunicacionbaja = DB::table('s_facturacioncomunicacionbaja')
                ->join('users as responsable', 'responsable.id', 's_facturacioncomunicacionbaja.idusuarioresponsable')
                ->join('s_facturacioncomunicacionbajadetalle as detalle', 'detalle.idfacturacioncomunicacionbaja', 's_facturacioncomunicacionbaja.id')
                ->leftJoin('users as cliente','cliente.id','detalle.idusuariocliente')
                ->leftJoin('s_facturacionboletafactura', 's_facturacionboletafactura.id', 'detalle.idfacturacionboletafactura')
                ->leftJoin('s_facturacionnotacredito', 's_facturacionnotacredito.id', 'detalle.idfacturacionnotacredito')
                ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacioncomunicacionbaja.idfacturacionrespuesta')
                ->where('s_facturacioncomunicacionbaja.idtienda', $idtienda)
                ->where($where)
                ->select(
                        's_facturacioncomunicacionbaja.*',
                        'responsable.nombre as nombreresponsable',
                        'detalle.tipodocumento as tipodocumento',
                        'detalle.serie as serie',
                        'detalle.correlativo as correlativo',
                        'detalle.descripcionmotivobaja as motivo',
                        's_facturacionboletafactura.cliente_numerodocumento as factbol_cliente_numerodocumento',
                        's_facturacionboletafactura.cliente_razonsocial as factbol_cliente_razonsocial',
                        's_facturacionnotacredito.cliente_numerodocumento as notacred_cliente_numerodocumento',
                        's_facturacionnotacredito.cliente_razonsocial as notacred_cliente_razonsocial',
                        'cliente.identificacion as clienteidentificacion',
                        DB::raw('IF(cliente.idtipopersona=1,
                        CONCAT(cliente.apellidos,", ",cliente.nombre),
                        CONCAT(cliente.apellidos)) as cliente'),
                       's_facturacionrespuesta.estado as respuestaestado',
                )
                ->orderBy('s_facturacioncomunicacionbaja.id','desc')
                ->get();
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Comunicación de Baja';
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

            return Excel::download(new 
                                    ReportefacturacioncomunicacionExport($facturacioncomunicacionbaja, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
            
        }else{
      
        $facturacioncomunicacionbaja = DB::table('s_facturacioncomunicacionbaja')
                ->join('users as responsable', 'responsable.id', 's_facturacioncomunicacionbaja.idusuarioresponsable')
                ->join('s_facturacioncomunicacionbajadetalle as detalle', 'detalle.idfacturacioncomunicacionbaja', 's_facturacioncomunicacionbaja.id')
                ->leftJoin('users as cliente','cliente.id','detalle.idusuariocliente')
                ->leftJoin('s_facturacionboletafactura', 's_facturacionboletafactura.id', 'detalle.idfacturacionboletafactura')
                ->leftJoin('s_facturacionnotacredito', 's_facturacionnotacredito.id', 'detalle.idfacturacionnotacredito')
                ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacioncomunicacionbaja.idfacturacionrespuesta')
                ->where('s_facturacioncomunicacionbaja.idtienda', $idtienda)
                 ->where($where)
                ->select(
                        's_facturacioncomunicacionbaja.*',
                        'responsable.nombre as nombreresponsable',
                        'detalle.tipodocumento as tipodocumento',
                        'detalle.serie as serie',
                        'detalle.correlativo as correlativo',
                        'detalle.descripcionmotivobaja as motivo',
                        's_facturacionboletafactura.cliente_numerodocumento as factbol_cliente_numerodocumento',
                        's_facturacionboletafactura.cliente_razonsocial as factbol_cliente_razonsocial',
                        's_facturacionnotacredito.cliente_numerodocumento as notacred_cliente_numerodocumento',
                        's_facturacionnotacredito.cliente_razonsocial as notacred_cliente_razonsocial',
                        'cliente.identificacion as clienteidentificacion',
                        DB::raw('IF(cliente.idtipopersona=1,
                        CONCAT(cliente.apellidos,", ",cliente.nombre),
                        CONCAT(cliente.apellidos)) as cliente'),
                       's_facturacionrespuesta.estado as respuestaestado',
                )
                ->orderBy('s_facturacioncomunicacionbaja.id','desc')
                ->paginate(10);
        
        $agencia = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
      
        return view('layouts/backoffice/tienda/sistema/reportefacturacioncomunicacion/index',[
            'tienda'                      => $tienda,
            'facturacioncomunicacionbaja' => $facturacioncomunicacionbaja,
            'agencia'                     => $agencia,

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
