<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReporteventaproductoExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReporteventaproductoController extends Controller
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
       if($request->input('idproducto')!=''){
            $where[] = ['producto.id','LIKE','%'.$request->input('idproducto').'%'];
        }
        if($request->input('idresponsable')!=''){
            $where[] = ['responsable.id','LIKE','%'.$request->input('idresponsable').'%'];
        }
       if($request->input('idcliente')!=''){
            $where[] = ['cliente.id','LIKE','%'.$request->input('idcliente').'%'];
        }
        if($request->input('idcomprobante')!=''){
            $where[] = ['s_tipocomprobante.id',$request->input('idcomprobante')];
        }
        if($request->input('idtipoentrega')!=''){
            $where[] = ['venta.s_idtipoentrega',$request->input('idtipoentrega')];
        }
        if($request->input('idmoneda')!=''){
            $where[] = ['moneda.id',$request->input('idmoneda')];
        }
        if($request->input('fechainicio')!=''){
            $where[] = ['venta.fechaconfirmacion','>=',$request->input('fechainicio').' 00:00:00'];
        }
        if($request->input('fechafin')!=''){
            $where[] = ['venta.fechaconfirmacion','<=',$request->input('fechafin').' 23:59:59'];
        }
        if($request->input('tipo')=='excel'){
          $s_ventadetalle  =   DB::table('s_ventadetalle')
            ->join('s_venta as venta','venta.id','s_ventadetalle.s_idventa')
            ->join('s_producto as producto','producto.id','s_ventadetalle.s_idproducto')
            ->join('users as responsable','responsable.id','venta.s_idusuarioresponsable')
            ->join('users as cliente','cliente.id','venta.s_idusuariocliente')
            ->join('s_moneda as moneda','moneda.id','venta.s_idmoneda')
            ->join('s_tipocomprobante','s_tipocomprobante.id','venta.s_idcomprobante')
            ->join('s_tipoentrega','s_tipoentrega.id','venta.s_idtipoentrega')
            ->join('unidadmedida','unidadmedida.id','producto.idunidadmedida')
            ->where('venta.idtienda',$idtienda)
            ->where($where)
            ->select(
                's_ventadetalle.*',
                'venta.codigo as codigoventa',
                'venta.fechaconfirmacion as fechaventa',
                'producto.nombre as nombreproducto',
                'producto.codigo as codigo',
                'responsable.nombre as nombreresponsable',
                'responsable.apellidos as apellidosresponsable',
                'cliente.nombre as nombrecliente',
                'cliente.apellidos as apellidocliente',
                'moneda.nombre as monedanombre',
                's_tipocomprobante.nombre as nombreComprobante',
                's_tipoentrega.nombre as tipoentreganombre',
                'unidadmedida.nombre as nombreunidadmedida'
            )
            ->orderBy('venta.id','desc')
            ->get();
          
            /* INICIO - Capturando los valores de filtrar para mostrar en el excel */
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Venta por Producto';
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
                                    ReporteventaproductoExport($s_ventadetalle, $inicio, $fin, $titulo),
                                    $titulo.' '.$fecha.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
            
        }else{
          $s_ventadetalle  =   DB::table('s_ventadetalle')
            ->join('s_venta as venta','venta.id','s_ventadetalle.s_idventa')
            ->join('s_producto as producto','producto.id','s_ventadetalle.s_idproducto')
            ->join('users as responsable','responsable.id','venta.s_idusuarioresponsable')
            ->join('users as cliente','cliente.id','venta.s_idusuariocliente')
            ->join('s_moneda as moneda','moneda.id','venta.s_idmoneda')
            ->join('s_tipocomprobante','s_tipocomprobante.id','venta.s_idcomprobante')
            ->join('s_tipoentrega','s_tipoentrega.id','venta.s_idtipoentrega')
            ->join('unidadmedida','unidadmedida.id','producto.idunidadmedida')
            ->where('venta.idtienda',$idtienda)
            ->where($where)
            ->select(
                's_ventadetalle.*',
                'venta.codigo as codigoventa',
                'venta.fechaconfirmacion as fechaventa',
                'producto.nombre as nombreproducto',
                'producto.codigo as codigo',
                'responsable.nombre as nombreresponsable',
                'responsable.apellidos as apellidosresponsable',
                'cliente.nombre as nombrecliente',
                'cliente.apellidos as apellidocliente',
                'moneda.nombre as monedanombre',
                's_tipocomprobante.nombre as nombreComprobante',
                's_tipoentrega.nombre as tipoentreganombre',
                'unidadmedida.nombre as nombreunidadmedida'
            )
            ->orderBy('venta.id','desc')
            ->paginate(10);
          
          $agencia        = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
          $comprobante    = DB::table('s_tipocomprobante')->get();
          $tipoentregas   = DB::table('s_tipoentrega')->get();
          $moneda         = DB::table('s_moneda')->get();
      
        return view('layouts/backoffice/tienda/sistema/reporte/reporteventaproducto/index',[
            'tienda'          => $tienda,
            'agencia'         => $agencia,
            'moneda'          => $moneda,
            'comprobante'     => $comprobante,
            'tipoentregas'    => $tipoentregas,
            's_ventadetalle'  => $s_ventadetalle,
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
