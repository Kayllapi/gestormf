<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReportetransferenciaExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReportetransferenciaController extends Controller
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
 
        $where   = [];
        $where[] = ['s_productotransferencia.idtiendaorigen',$idtienda];
      
        if($request->input('codigo')!=''){
            $where[] = ['s_productotransferencia.codigo',$request->input('codigo')];
        }
      
        if($request->input('motivo')!=''){
            $where[] = ['s_productotransferencia.motivo','LIKE','%'.$request->input('motivo').'%'];
        }
      
        if($request->input('idestado') != ''){
            $where[] = ['s_productotransferencia.idestadotransferencia','=',$request->input('idestado')];
        }
      
        if($request->input('fechainicio')!=''){
            $where[] = ['s_productotransferencia.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }
        if($request->input('fechafin')!=''){
            $where[] = ['s_productotransferencia.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
        }
      
        $where1 = [];
        $where1[] = ['s_productotransferencia.idtiendadestino',$idtienda];
      
        if($request->input('codigo')!=''){
            $where1[] = ['s_productotransferencia.codigo',$request->input('codigo')];
        }
      
        if($request->input('motivo')!=''){
            $where1[] = ['s_productotransferencia.motivo','LIKE','%'.$request->input('motivo').'%'];
        }
      
        if($request->input('idestado') != ''){
            $where1[] = ['s_productotransferencia.idestadotransferencia','=',$request->input('idestado')];
        }
      
        if($request->input('fechainicio')!=''){
            $where1[] = ['s_productotransferencia.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }
        if($request->input('fechafin')!=''){
            $where1[] = ['s_productotransferencia.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
        }
       if($request->input('tipo')=='excel'){
           $productotransferencia = DB::table('s_productotransferencia')
                ->join('tienda as tienda_origen','tienda_origen.id' ,'s_productotransferencia.idtiendaorigen')
                ->join('tienda as tienda_destino','tienda_destino.id' ,'s_productotransferencia.idtiendadestino')
                ->leftJoin('users as user_origen','user_origen.id' ,'s_productotransferencia.idusersorigen')
                ->leftJoin('users as user_destino','user_destino.id' ,'s_productotransferencia.idusersdestino')
                ->where($where)
                ->orWhere($where1)
                ->select(
                  's_productotransferencia.*',
                  'user_origen.nombre as user_origen_nombre',
                  'user_destino.nombre as user_destino_nombre',
                  'tienda_origen.nombre as tienda_origen_nombre',
                  'tienda_destino.id as id_tienda_destino',
                  'tienda_destino.nombre as tienda_destino_nombre'
                )
                ->orderBy('s_productotransferencia.id','desc')
                ->get();
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Transferencia';
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
                                    ReportetransferenciaExport($productotransferencia, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
            
        }else{
        $productotransferencia = DB::table('s_productotransferencia')
                ->join('tienda as tienda_origen','tienda_origen.id' ,'s_productotransferencia.idtiendaorigen')
                ->join('tienda as tienda_destino','tienda_destino.id' ,'s_productotransferencia.idtiendadestino')
                ->leftJoin('users as user_origen','user_origen.id' ,'s_productotransferencia.idusersorigen')
                ->leftJoin('users as user_destino','user_destino.id' ,'s_productotransferencia.idusersdestino')
                ->where($where)
                ->orWhere($where1)
                ->select(
                  's_productotransferencia.*',
                  'user_origen.nombre as user_origen_nombre',
                  'user_destino.nombre as user_destino_nombre',
                  'tienda_origen.nombre as tienda_origen_nombre',
                  'tienda_destino.id as id_tienda_destino',
                  'tienda_destino.nombre as tienda_destino_nombre'
                )
                ->orderBy('s_productotransferencia.id','desc')
                ->paginate(10);     
                  $tiendas= DB::table('tienda')->where('id',$idtienda)->get();

      
        return view('layouts/backoffice/tienda/sistema/reportetransferencia/index',[
            'tienda' => $tienda,
            'productotransferencia' => $productotransferencia,
            'tiendas' => $tiendas

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
