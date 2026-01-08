<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReportePrestamoMoraExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportePrestamoMoraController extends Controller
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
        
        $where = [];
//         if($request->input('idproveedor')!=''){
//             $where[] = ['users.id',$request->input('idproveedor')];
//         }
//         if($request->input('idcomprobante')!=''){
//             $where[] = ['s_tipocomprobante.id',$request->input('idcomprobante')];
//         }
//         if($request->input('seriecorrelativo')!=''){
//             $where[] = ['s_compra.seriecorrelativo','LIKE','%'.$request->input('seriecorrelativo').'%'];
//         }
//         if($request->input('idestado')!=''){
//             $where[] = ['s_compra.s_idestado',$request->input('idestado')];
//         }
//         if($request->input('fechainicio')!=''){
//             $where[] = ['s_compra.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
//         }
//         if($request->input('fechafin')!=''){
//             $where[] = ['s_compra.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
//         }
      $fecha_actual = date('Y-m-d');
      dump($fecha_actual);
      
      $credito = DB::table('s_prestamo_credito as credito')
        ->where([
          ['credito.fechadesembolsado', '<>', null],
          ['credito.idestadocredito', 4] //desembolsado
        ])
        ->orderBy('credito.id', 'desc');
      
      if($request->input('tipo')=='excel'){
             $s_compra = DB::table('s_compra')
                  ->join('users','users.id','s_compra.s_idusuarioproveedor')
                  ->join('users as responsable','responsable.id','s_compra.s_idusuarioresponsable')
                  ->join('s_tipocomprobante','s_tipocomprobante.id','s_compra.s_idcomprobante')
                  ->where('s_compra.idtienda',$idtienda)
                  ->where($where)
                  ->select(
                      's_compra.*',
                      'users.nombre as nombreProveedor',
                      'users.apellidos as apellidoProveedor',
                      's_tipocomprobante.nombre as nombreComprobante',
                      'responsable.nombre as responsablenombre'
                  )
                  ->orderBy('s_compra.id','desc')
                  ->get();
          
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Compras';
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
            return Excel::download(new ReportecompraExport($s_compra, $inicio, $fin, $titulo), $titulo.' '.$fecha.'.xls');
        }
      else{
        $credito = $credito->paginate(10);

        foreach ($credito as $value) {
          $creditodetalle = DB::table('s_prestamo_creditodetalle as creditodetalle')
            ->where([
              ['creditodetalle.idprestamo_credito', $value->id],
              ['creditodetalle.idestadocobranza', 1] // cuota pendiente
            ])
            ->get();
          foreach ($creditodetalle as $item) {
            dump($item->fechavencimiento);
          } 
        }
        dd('fin');
        return view('layouts/backoffice/tienda/sistema/reporteprestamomora/index',[
          'tienda' => $tienda,
          'credito' => $credito
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
