<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReportekardexExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReportekardexController extends Controller
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
         $productosaldos = DB::table('s_productosaldo')
            ->where('s_productosaldo.idtienda',$idtienda)
            ->where('s_productosaldo.s_idproducto',$request->input('idproducto'))
            ->orderBy('s_productosaldo.id','desc')
            ->get();
        $tabla =[];
            foreach($productosaldos as $value){
              
              if($value->venta_tipodocumento=='03'){ 
                      $comprobante=  'BOLETA';
                 }elseif($value->venta_tipodocumento=='01'){ 
                      $comprobante = 'FACTURA';
                 }else{ 
                      $comprobante  = 'TICKET';
                 }
              $class_dato = 'td_dato';
              
        $class_es = 'td_es';
        $class_saldo = 'td_saldo';
        if($value->concepto=='SALDO INICIAL'){
            $class_dato = 'td_reset_dato';
            $class_es = 'td_reset_es';
            $class_saldo = 'td_reset_saldo';
        }
    
                $tabla[] = [
                    'id' => $value->id,
                    'titulo' => 'FECHA/HORA<br>CONCEPTO<br>PRODUCTO<br>CANT.<br>',
                    'nombre' => ': '.$comprobante.'<br>: '.$serie_corre.'<br>: '.$value->saldo_cantidad.'<br>: '. $value->cantidad.'-'.$value->cantidadrestante.'<br>',
                    'titulo2' => 'P. UNITARIO<br>P. TOTAL<br>SALDO<br>RESTANTE<br>',
                    'nombre2' => ': '.$value->preciounitario.'<br>: '.$value->preciototal.'<br>: '.'<br>: '.$value->saldo_cantidad.'<br>: '.$value->saldo_cantidadrestante,
                    'option'    => '<div class="option3">
                                       <a style="width:100px" href="javascript:;" onclick="table_modal('.$value->id.',\'Exportar Excel\',\'excel\')" class="btn-tabla"><div class="btn-tabla-edit"></div>Exportar Excel</a>
                                        <a style="width:100px" href="javascript:;" onclick="table_modal('.$value->id.',\'Exportar SUNAT\',\'sunat\')" class="btn-tabla"><div class="btn-tabla-edit"></div>Exportar Excel SUNAT</a>

                                    </div>',
                ];
            }

             json_create($idtienda,$name_modulo,$tabla);
      
       
        
      
        return view('layouts/backoffice/tienda/nuevosistema/reportekardex/index',[
            'tienda' => $tienda,
            'productosaldos' => $productosaldos,
        ]);
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
      
        if($id=='showlistarproducto'){
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
       if($request->input('tipo')=='excel'){
         $productosaldos = DB::table('s_productosaldo')
            ->where('s_productosaldo.idtienda',$idtienda)
            ->where('s_productosaldo.s_idproducto',$request->input('idproducto'))
            ->orderBy('s_productosaldo.id','desc')
            ->get();
          
        
            $titulo = 'Reporte de Stock';
         

            return Excel::download(new 
                                    ReportekardexExport($productosaldos, $titulo),
                                    $titulo.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
            
        }
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
