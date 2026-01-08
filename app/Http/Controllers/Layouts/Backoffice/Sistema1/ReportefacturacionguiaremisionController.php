<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReportefacturacionguiaremisionExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReportefacturacionguiaremisionController extends Controller
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
      
        if($request->input('serie')!= ''){
           $where[] = ['s_facturacionguiaremision.despacho_serie',$request->input('serie')];
        }
      
        if($request->input('correlativo')!= ''){
          $where[] = ['s_facturacionguiaremision.despacho_correlativo',$request->input('correlativo')];
        }
      
      if($request->input('destinatario')!= ''){
          $where[] = ['destinatario.id','LIKE','%'.$request->input('destinatario').'%'];
        }
      if($request->input('idresponsable')!= ''){
          $where[] = ['responsable.id','LIKE','%'.$request->input('idresponsable').'%'];
        }
      if($request->input('idtransportista')!= ''){
          $where[] = ['transportista.id','LIKE','%'.$request->input('idtransportista').'%'];
        }
      
      if($request->input('idagencia')!=''){
            $where[] = ['s_facturacionguiaremision.idagencia',$request->input('idagencia')];
        }
      
        if($request->input('fechainicio')!=''){
            $where[] = ['s_facturacionguiaremision.despacho_fechaemision','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
            $where[] = ['s_facturacionguiaremision.despacho_fechaemision','<=',$request->input('fechafin').' 23:59:59'];
        }
       if($request->input('tipo')=='excel'){
         $facturacionguiaremision = DB::table('s_facturacionguiaremision')
            ->join('users as responsable', 'responsable.id', 's_facturacionguiaremision.idusuarioresponsable')
            ->join('s_sunat_motivotraslado', 's_sunat_motivotraslado.codigo', 's_facturacionguiaremision.envio_modtraslado')
            ->leftJoin('users as transportista', 'transportista.id', 's_facturacionguiaremision.idusuariochofer')
            ->join('users','users.identificacion','s_facturacionguiaremision.despacho_destinatario_numerodocumento')
            ->join('users as destinatario','destinatario.id','users.id')
            ->where('s_facturacionguiaremision.idtienda',$idtienda)
            ->where($where)
            ->select(
                's_facturacionguiaremision.*',
                'responsable.nombre as responsablenombre',
                's_sunat_motivotraslado.nombre as motivotrasladonombre',
                 DB::raw('IF(transportista.idtipopersona=1,
                 CONCAT(transportista.apellidos,", ",transportista.nombre),
                 CONCAT(transportista.apellidos)) as transportista'),
                'users.identificacion'
              )
            ->orderBy('s_facturacionguiaremision.id','desc')
            ->get();
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte Guías de Remisión';
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
                                   ReportefacturacionguiaremisionExport($facturacionguiaremision, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
            
        }else{
      
      
        $facturacionguiaremision = DB::table('s_facturacionguiaremision')
            ->join('users as responsable', 'responsable.id', 's_facturacionguiaremision.idusuarioresponsable')
            ->join('s_sunat_motivotraslado', 's_sunat_motivotraslado.codigo', 's_facturacionguiaremision.envio_modtraslado')
            ->leftJoin('users as transportista', 'transportista.id', 's_facturacionguiaremision.idusuariochofer')
            ->join('users','users.identificacion','s_facturacionguiaremision.despacho_destinatario_numerodocumento')
            ->join('users as destinatario','destinatario.id','users.id')
            ->where('s_facturacionguiaremision.idtienda',$idtienda)
            ->where($where)
            ->select(
                's_facturacionguiaremision.*',
                'responsable.nombre as responsablenombre',
                's_sunat_motivotraslado.nombre as motivotrasladonombre',
                 DB::raw('IF(transportista.idtipopersona=1,
                 CONCAT(transportista.apellidos,", ",transportista.nombre),
                 CONCAT(transportista.apellidos)) as transportista'),
                'users.identificacion'
              )
            ->orderBy('s_facturacionguiaremision.id','desc')
            ->paginate(10);
        $agencia = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
      
        return view('layouts/backoffice/tienda/sistema/reportefacturacionguiaremision/index',[
            'tienda'                   => $tienda,
            'facturacionguiaremision'  => $facturacionguiaremision,
            'agencia'                  => $agencia,

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
        }elseif($id == 'show-seleccionarcliente'){
            $usuario = DB::table('users')
                ->where('users.id',$request->input('idcliente'))
                ->select(
                    'users.*'
                )
                ->first();
          
            return [ 
              'cliente' => $usuario
            ];
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
