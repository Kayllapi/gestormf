<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReportesunatExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReportesunatController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $where  = [];
        if($request->input('comprobante')== '01' or $request->input('comprobante')== '03' ){
            $where[] = ['s_facturacionboletafactura.venta_tipodocumento',$request->input('comprobante')];
        }elseif($request->input('comprobante')== '07' ){
            $where[] = ['s_facturacionnotacredito.notacredito_tipodocumento',$request->input('comprobante')];
        }elseif($request->input('comprobante')== '08' ){
            $where[] = ['s_facturacionnotadebito.notadebito_tipodocumento',$request->input('comprobante')];
        }elseif($request->input('comprobante')== '09' ){
            $where[] = ['s_facturacionguiaremision.despacho_tipodocumento',$request->input('comprobante')];
        }elseif($request->input('comprobante')== 'RA' ){
            $where[] = ['s_facturacionrespuesta.s_idfacturacioncomunicacionbaja','>',0];
        }elseif($request->input('comprobante')== 'RC' ){
            $where[] = ['s_facturacionrespuesta.s_idfacturacionresumendiario','>',0];
        }
      
        if($request->input('idestado')!=''){
            $where[] = ['s_facturacionrespuesta.estado',$request->input('idestado')];
        }
      
        if($request->input('fechainicio')!=''){
            $where[] = ['s_facturacionrespuesta.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
            $where[] = ['s_facturacionrespuesta.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
        }
      
        if($request->input('idagencia')!=''){
            $where[] = ['s_facturacioncomunicacionbaja.idagencia',$request->input('idagencia')];
        }
      
      $facturacionrespuesta = DB::table('s_facturacionrespuesta')
              ->leftJoin('s_facturacionboletafactura','s_facturacionboletafactura.id','s_facturacionrespuesta.s_idfacturacionboletafactura')
              ->leftJoin('s_facturacionnotacredito','s_facturacionnotacredito.id','s_facturacionrespuesta.s_idfacturacionnotacredito')
              ->leftjoin('s_facturacionnotadebito','s_facturacionnotadebito.id','s_facturacionrespuesta.s_idfacturacionnotadebito')
              ->leftjoin('s_facturacionguiaremision','s_facturacionguiaremision.id','s_facturacionrespuesta.s_idfacturacionguiaremision')
              ->leftjoin('s_facturacionresumendiario','s_facturacionresumendiario.id','s_facturacionrespuesta.s_idfacturacionresumendiario')
              ->leftjoin('s_facturacionresumendiariodetalle','s_facturacionresumendiariodetalle.idfacturacionresumendiario','s_facturacionresumendiario.id')
              ->leftjoin('s_facturacioncomunicacionbaja','s_facturacioncomunicacionbaja.id','s_facturacionrespuesta.s_idfacturacioncomunicacionbaja')
              ->leftjoin('s_facturacioncomunicacionbajadetalle','s_facturacioncomunicacionbajadetalle.idfacturacioncomunicacionbaja','s_facturacioncomunicacionbaja.id')
              ->where('s_facturacionrespuesta.idtienda',$idtienda)
              ->where($where)
              ->select(
                       's_facturacionrespuesta.*',
                       's_facturacionboletafactura.venta_tipodocumento as venta_tipodocumento',
                       's_facturacionnotacredito.notacredito_tipodocumento as notacredito_tipodocumento',
                       's_facturacionnotadebito.notadebito_tipodocumento as notadebito_tipodocumento',
                       's_facturacionguiaremision.despacho_tipodocumento as despacho_tipodocumento',
                       's_facturacionresumendiariodetalle.tipodocumento as tipodocumentoresumen',
                       's_facturacioncomunicacionbajadetalle.tipodocumento as tipodocumentobaja'
                )
             ->orderBy('s_facturacionrespuesta.id','desc');
        
      if($request->input('tipo')=='excel'){
            $facturacionrespuesta = $facturacionrespuesta->get();
          
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de SUNAT';
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
                                    ReportesunatExport($facturacionrespuesta, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            
        }else{
      
            $facturacionrespuesta = $facturacionrespuesta->paginate(10);

            return view('layouts/backoffice/tienda/sistema/reportesunat/index',[
                'tienda'                   => $tienda,
                'facturacionrespuesta'     => $facturacionrespuesta,

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
