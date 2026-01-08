<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReportefacturacionnotacreditoExport;
use App\Exports\ReportefacturacionnotacreditoSUNATExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReportefacturacionnotacreditoController extends Controller
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
      
        if($request->input('afectado')!= ''){
           $where[] = ['s_facturacionnotacredito.notacredito_numerodocumentoafectado',$request->input('afectado')];
        }
        if($request->input('serie')!= ''){
           $where[] = ['s_facturacionnotacredito.notacredito_serie',$request->input('serie')];
        }
      
        if($request->input('correlativo')!= ''){
          $where[] = ['s_facturacionnotacredito.notacredito_correlativo',$request->input('correlativo')];
        }
      
        if($request->input('idcliente')!=''){
            $where[] = ['cliente.id',$request->input('idcliente')];
        }
      if($request->input('idestado')!=''){
            $where[] = ['s_facturacionrespuesta.estado',$request->input('idestado')];
        }
      if($request->input('idresponsable')!=''){
            $where[] = ['responsable.id',$request->input('idresponsable')];
        }
      
        if($request->input('moneda')!= ''){
          $where[] = ['s_moneda.nombre', $request->input('moneda')];
        }
      if($request->input('idagencia')!=''){
            $where[] = ['s_facturacionnotacredito.idagencia',$request->input('idagencia')];
        }
      
        if($request->input('fechainicio')!=''){
            $where[] = ['s_facturacionnotacredito.notacredito_fechaemision','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
            $where[] = ['s_facturacionnotacredito.notacredito_fechaemision','<=',$request->input('fechafin').' 24:00:00'];
        }
        if($request->input('tipo')=='excel'){
              $facturacionnotacredito = DB::table('s_facturacionnotacredito')
            ->join('s_moneda','s_moneda.codigo','s_facturacionnotacredito.notacredito_tipomoneda')
            ->join('users as responsable','responsable.id','s_facturacionnotacredito.idusuarioresponsable')
            ->join('users as cliente','cliente.id','s_facturacionnotacredito.idusuariocliente')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionnotacredito.idfacturacionrespuesta')
            ->where('s_facturacionnotacredito.idtienda',$idtienda)
            ->where($where)
            ->select(
                's_facturacionnotacredito.*',
                's_facturacionrespuesta.estado as respuestaestado',
                'responsable.nombre as responsablenombre',
                'responsable.apellidos as responsableapellido',
            )
            ->orderBy('s_facturacionnotacredito.id','desc')
            ->get();
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Notas de Crédito';
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

            return Excel::download(new ReportefacturacionnotacreditoExport($facturacionnotacredito, $inicio, $fin, $titulo),$titulo.' '.$fecha.'.xls');

            
        }elseif($request->input('tipo')=='excel_SUNAT'){
            $facturacionnotacredito = DB::table('s_facturacionnotacredito')
                ->where('s_facturacionnotacredito.idtienda',$idtienda)
                ->where($where)
                ->orderBy('s_facturacionnotacredito.notacredito_fechaemision','asc')
                ->get();
          
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Notas de Crédito para la SUNAT';
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

            return Excel::download(new ReportefacturacionnotacreditoSUNATExport($facturacionnotacredito, $inicio, $fin, $titulo, $idtienda),$titulo.' '.$fecha.'.xls');

            
        }else{
        
        $facturacionnotacredito = DB::table('s_facturacionnotacredito')
            ->join('s_moneda','s_moneda.codigo','s_facturacionnotacredito.notacredito_tipomoneda')
            ->join('users as responsable','responsable.id','s_facturacionnotacredito.idusuarioresponsable')
            ->join('users as cliente','cliente.id','s_facturacionnotacredito.idusuariocliente')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionnotacredito.idfacturacionrespuesta')
            ->where('s_facturacionnotacredito.idtienda',$idtienda)
            ->where($where)
            ->select(
                's_facturacionnotacredito.*',
                's_facturacionrespuesta.estado as respuestaestado',
                'responsable.nombre as responsablenombre',
                'responsable.apellidos as responsableapellido'
            )
            ->orderBy('s_facturacionnotacredito.id','desc')
            ->paginate(10);
        
        $agencia = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
        $moneda         = DB::table('s_moneda')->get();
      
        return view('layouts/backoffice/tienda/sistema/reportefacturacionnotacredito/index',[
            'tienda'                   => $tienda,
            'facturacionnotacredito'   => $facturacionnotacredito,
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
