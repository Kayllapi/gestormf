<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReportemovimientoproductoExport;
use Maatwebsite\Excel\Facades\Excel;
class  ReportemovimientoproductoController extends Controller
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
        $where[] = ['s_productomovimiento.motivo','LIKE','%'.$request->input('motivo').'%'];
        if($request->input('idtipomovimiento')!=''){
            $where[] = ['s_tipomovimiento.id','LIKE','%'.$request->input('idtipomovimiento').'%'];
        }
        if($request->input('idproducto')!=''){
            $where[] = ['s_producto.id','LIKE','%'.$request->input('idproducto').'%'];
        }
        if($request->input('idresponsable')!=''){
            $where[] = ['responsable.id','LIKE','%'.$request->input('idresponsable').'%'];
        }
        if($request->input('fechainicio')!=''){
            $where[] = ['s_productomovimiento.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }
        if($request->input('fechafin')!=''){
            $where[] = ['s_productomovimiento.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
        }
         if($request->input('tipo')=='excel'){
            $s_productomovimiento  = DB::table('s_productomovimiento')
            ->leftJoin('users as responsable','responsable.id','s_productomovimiento.s_idusuarioresponsable')
            ->leftJoin('s_tipomovimiento','s_tipomovimiento.id','s_productomovimiento.s_idtipomovimiento')
            ->leftJoin('s_producto','s_producto.id','s_productomovimiento.s_idproducto')
            ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
            ->where('s_productomovimiento.idtienda',$idtienda)
            ->where($where)
            ->select(
                's_productomovimiento.*',
                'responsable.nombre as responsablenombre',
                's_tipomovimiento.nombre as nombretipomovimiento',
                's_producto.nombre as productonombre',
                 DB::raw('CONCAT(unidadmedida.nombre," x ",s_producto.por) as unidadmedida')
            )
            ->orderBy('s_productomovimiento.id','desc')
            ->get();
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Movimientos Producto';
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
                                    ReportemovimientoproductoExport($s_productomovimiento, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
            
        }else{
        $s_productomovimiento  = DB::table('s_productomovimiento')
            ->leftJoin('users as responsable','responsable.id','s_productomovimiento.s_idusuarioresponsable')
            ->leftJoin('s_tipomovimiento','s_tipomovimiento.id','s_productomovimiento.s_idtipomovimiento')
            ->leftJoin('s_producto','s_producto.id','s_productomovimiento.s_idproducto')
            ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
            ->where('s_productomovimiento.idtienda',$idtienda)
            ->where($where)
            ->select(
                's_productomovimiento.*',
                'responsable.nombre as responsablenombre',
                's_tipomovimiento.nombre as nombretipomovimiento',
                's_producto.nombre as productonombre',
                 DB::raw('CONCAT(unidadmedida.nombre," x ",s_producto.por) as unidadmedida')
            )
            ->orderBy('s_productomovimiento.id','desc')
            ->paginate(10);
         $tipomovimiento   = DB::table('s_tipomovimiento')->get();
      
      
        return view('layouts/backoffice/tienda/sistema/reportemovimientoproducto/index',[
            'tienda' => $tienda,
            's_productomovimiento' => $s_productomovimiento,
          'tipomovimiento' => $tipomovimiento
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
        }elseif($id=='showlistarproducto'){
            $productos = DB::table('s_producto')
                ->join('tienda','tienda.id','s_producto.idtienda')
                ->leftJoin('s_categoria','s_categoria.id','s_producto.s_idcategoria1')
                ->leftJoin('s_categoria as subcategoria','subcategoria.id','s_producto.s_idcategoria2')
                ->leftJoin('s_marca','s_marca.id','s_producto.s_idmarca')
                ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->where('s_producto.idtienda',$idtienda)
                ->where('s_producto.nombre','LIKE','%'.$request->input('buscar').'%')
                ->where('s_producto.s_idestado',1)
                ->select(
                  's_producto.id as id',
                  's_producto.codigo as codigo',
                  's_producto.nombre as nombre',
                  's_producto.precioalpublico as precioalpublico',
                  's_producto.s_idestadodetalle as idestadodetalle',
                  's_producto.s_idestado as idestado',
                  's_producto.s_idestadotiendavirtual as idestadotv',
                   DB::raw('CONCAT(unidadmedida.nombre," x ",s_producto.por) as unidadmedida'),
                   DB::raw('CONCAT(s_producto.nombre," / ",s_producto.precioalpublico) as text'),
                   'tienda.id as idtienda',
                   'tienda.nombre as tiendanombre',
                   'tienda.link as tiendalink',
                   's_marca.nombre as marcanombre',
                   's_categoria.nombre as categorianombre',
                   DB::raw('(SELECT imagen FROM s_productogaleria WHERE s_idproducto=s_producto.id ORDER BY orden ASC LIMIT 1) as imagen')
                )
                ->limit(20)
                ->get();
            return $productos;
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
