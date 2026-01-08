<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use Image;
use Intervention\Image\ImageManager;

class ProductoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        json_producto($idtienda,$request->name_modulo);

        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/nuevosistema/producto/index',[
            'tienda'      => $tienda
        ]);
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda       = DB::table('tienda')->whereId($idtienda)->first();
        $marcas       = DB::table('s_marca')->where('idtienda',$idtienda)->get();
        $categorias = DB::table('s_categoria')
                ->where('s_categoria.idtienda',$idtienda)
                ->where('s_categoria.s_idcategoria',0)
                ->orderBy('s_categoria.nombre','asc')
                ->get();
        $configuracion = configuracion_comercio($idtienda);

        return view('layouts/backoffice/tienda/nuevosistema/producto/create',[
            'tienda' => $tienda,
            'marcas' => $marcas,
            'categorias' => $categorias,
            'configuracion' => $configuracion
        ]);
        
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'nombre' => 'required', 
                'precioalpublico'    => 'required', 
                'idcategoria' => 'required',
            ];
          
            $messages = [
                'nombre.required'   => 'El "Nombre" es Obligatorio.',
                'nombre.precioalpublico'   => 'El "Precio al Público" es Obligatorio.',
                'idcategoria.required'   => 'La "Categoría" es Obligatorio.',
            ];
      
            $this->validate($request,$rules,$messages);
          
            $s_producto = DB::table('s_producto')
                ->where('codigo',$request->input('codigo'))
                ->where('idtienda',$idtienda)
                ->first();
            if($s_producto!='' and $request->input('codigo')!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "Código de Producto" ya existe, Ingrese Otro por favor.'
                ]);
            }  
          
            $s_idcategoria1 = 0; 
            $s_idcategoria2 = 0; 
            $s_idcategoria3 = 0;
          
            $categoria1 = DB::table('s_categoria')->whereId($request->input('idcategoria'))->first();
            if($categoria1!=''){
                $s_idcategoria1 = $categoria1->id;
                $categoria2 = DB::table('s_categoria')->whereId($categoria1->s_idcategoria)->first();
                if($categoria2!=''){
                    $s_idcategoria2 = $categoria1->id;
                    $s_idcategoria1 = $categoria2->id;
                    $categoria3 = DB::table('s_categoria')->whereId($categoria2->s_idcategoria)->first();
                    if($categoria3!=''){
                        $s_idcategoria3 = $categoria1->id;
                        $s_idcategoria2 = $categoria2->id;
                        $s_idcategoria1 = $categoria3->id;
                    }
                }
            }

            $idproducto = DB::table('s_producto')->insertGetId([
                'fecharegistro'  => Carbon::now(),
                'orden'  => 0,
                'codigo'         => $request->input('codigo')!=null ? $request->input('codigo') : '',
                'nombre'         => $request->input('nombre'),
                'descripcion'    => '',
                'preciopormayor' => '0.00',
                'precioalpublico' => $request->input('precioalpublico'),
                'por' => 1,
                'stockminimo' => 0,
                'alertavencimiento'=> 0,
                's_idproducto'  => 0,
                's_idcategoria1'  => $s_idcategoria1,
                's_idcategoria2'  => $s_idcategoria2,
                's_idcategoria3'  => $s_idcategoria3,
                's_idmarca'      => $request->input('idmarca')!=null ? $request->input('idmarca') : 0,
                's_idestadodetalle' => 2,
                's_idestado'     => 1,
                's_idestadotiendavirtual' => 2,
                'idunidadmedida'  => 1,
                'idproductopresentacion'  => 0,
                'idtienda'     => $idtienda
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'actualizarstock') {
            $rules = [
                'actualizarstock_stockacambiar' => 'required'
            ];
            $messages = [
                'actualizarstock_stockacambiar.required'   => 'El "Stock a cambiar" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
            
            if($request->input('actualizarstock_stockacambiar')<0){
                 return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Stock a cambiar debe ser mayor a 0!.'
                ]);
            }
          
            $presentaciones = producto_presentaciones($idtienda,$request->input('idproducto'));
            $agrupar_productocodigo = '';
            $agrupar_productonombre = '';
            $agrupar_productounidadmedida = '';
            $agrupar_productopornombre = '';
            $agrupar_stock = 0;
            foreach($presentaciones as $value){
                if($value['nivel']==1){
                    $agrupar_stock = $value['por']*$value['cantidad'];
                }
                $agrupar_productocodigo = $agrupar_productocodigo.'/<br>/'.$value['productocodigo'];
                $agrupar_productonombre = $agrupar_productonombre.'/<br>/'.$value['productonombre'];
                $agrupar_productounidadmedida = $agrupar_productounidadmedida.'/<br>/'.$value['productounidadmedida'];
                $agrupar_productopornombre = $agrupar_productopornombre.'/<br>/'.$value['por'];
            }
            if($agrupar_stock == $request->input('actualizarstock_stockacambiar')){
                 return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "Stock a cambiar" debe ser diferente al "Stock Actual"!.'
                ]);
            }
            //stock actual
            foreach($presentaciones as $value){
                $productosaldo_calcular = productosaldo_calcular($request->input('actualizarstock_stockacambiar'),$value['productopor']);
                productosaldo_actualizar(
                    $idtienda,
                    'SALDO ACTUALIZADO',
                    $agrupar_productocodigo,
                    $agrupar_productonombre,
                    $agrupar_productounidadmedida,
                    $agrupar_productopornombre,
                    0,
                    0,
                    0,
                    $value['idproducto'],
                    0,
                    $productosaldo_calcular['stock'],
                    $productosaldo_calcular['stock_restante']
                );
            }
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if($id=='showtotaldescuento'){
            $productos = explode('/&&&/', $request->input('descuento_seleccionarproducto'));
            $total = 0;
            for($i = 1;$i <  count($productos);$i++){
                $item = explode('/,,,/', $productos[$i]);
                if($item[0]!=''){
                    $productoprecio = DB::table('s_producto')->whereId($item[0])->first();
                    $total = $total+$productoprecio->precioalpublico;
                }
            }
            return [
              'total' => number_format($total, 2, '.', '')
            ];
        }

        elseif($id=='showstockimagenproducto'){
            $s_productogaleria = DB::table('s_productogaleria')
                                  ->join('s_producto','s_producto.id','s_productogaleria.s_idproducto')
                                  ->where('s_producto.idtienda',$idtienda)
                                  ->where('s_productogaleria.s_idproducto',$request->input('idproducto'))
                                  ->orderBy('s_productogaleria.orden','asc')
                                  ->limit(1)
                                  ->first();
          
            
            $imagenes = '<div class="gallery-items grid-small-pad  list-single-gallery three-coulms lightgallery">
                                <div class="">
                                      <div class="grid-item-holder">
                                          <div class="box-item" 
                                              style="background-image: url('.url('public/backoffice/sistema/sin_imagen_cuadrado.png').');
                                                        background-repeat: no-repeat;
                                                        background-size: contain;
                                                        background-position: center;
                                                        height: 131px;">
                                              <a href="javascript:;" id="modal-tiendaproducto" class="gal-link">
                                              <i class="fa fa-search"></i></a>
                                          </div>
                                      </div>
                                  </div>
                             </div>';
          
                
          
            $rutaimagen = getcwd().'/public/backoffice/sistema/sin_imagen_cuadrado.png';
            if($s_productogaleria!=''){
                $rutaimagen = getcwd().'/public/backoffice/tienda/'.$idtienda.'/producto/'.$s_productogaleria->imagen; 
                if(file_exists($rutaimagen) AND $s_productogaleria->imagen!=''){
                      $imagenes = '<div class="gallery-items grid-small-pad  list-single-gallery three-coulms lightgallery">
                                  <div class="mx-gallery-item">
                                      <div class="grid-item-holder">
                                          <div class="box-item" onclick="$(\'#imggaleria1\').click()"
                                              style="background-image: url('.url('public/backoffice/tienda/'.$idtienda.'/producto/'.$s_productogaleria->imagen).');
                                                        background-repeat: no-repeat;
                                                        background-size: contain;
                                                        background-position: center;
                                                        height: 131px;">
                                              <a href="'.url('public/backoffice/tienda/'.$idtienda.'/producto/'.$s_productogaleria->imagen).'" id="imggaleria1"  class="gal-link popup-image-detalle">
                                              <i class="fa fa-search"></i></a>
                                          </div>
                                      </div>
                                  </div>';
                                  $s_productogalerias = DB::table('s_productogaleria')
                                      ->join('s_producto','s_producto.id','s_productogaleria.s_idproducto')
                                      ->where('s_producto.idtienda',$idtienda)
                                      ->where('s_productogaleria.s_idproducto',$request->input('idproducto'))
                                      ->where('s_productogaleria.id','<>',$s_productogaleria->id)
                                      ->orderBy('s_productogaleria.orden','asc')
                                      ->get();
                      
                                  foreach($s_productogalerias as $value){
                                      $imagenes = $imagenes.'<div class="gallery-items" style="display: none;">
                                            <div class="grid-item-holder">
                                                <div class="box-item">
                                                  <a href="'.url('public/backoffice/tienda/'.$idtienda.'/producto/'.$value->imagen).'" class="gal-link popup-image-detalle">
                                                    <i class="fa fa-search"></i></a>
                                                </div>
                                            </div>
                                        </div>';
                                  }
                      $imagenes = $imagenes.'</div><script>
                                  //   lightGallery------------------
                                  $(".lightgallery").lightGallery({
                                      selector: ".lightgallery a.popup-image-detalle",
                                      cssEasing: "cubic-bezier(0.25, 0, 0.25, 1)",
                                      download: false,
                                      loop: false,
                                      counter: false
                                  });
                                  </script>';
                }
            }
                
            $presentaciones = producto_presentaciones($idtienda,$request->input('idproducto'));
            $presentaciones = collect($presentaciones)->sortByDesc('nivel');
         
            $htmlstock = '<table class="table">
                <thead class="thead-dark">
                  <tr>
                    <th width="10px">N°</th>
                    <th style="height: 30px;padding-top: 0;padding-bottom: 0;">U. Medida</th>
                    <th style="height: 30px;padding-top: 0;padding-bottom: 0;" width="10px">Precio</th>
                    <th style="height: 30px;padding-top: 0;padding-bottom: 0;" width="10px">Stock</th>
                  </tr>
                </thead>
                <tbody>';
            foreach($presentaciones as $value){
           
                $style = 'padding: 6px;border-right: 1px solid #000;border-bottom: 1px solid #000;color: #fff;'.($value['idproducto']==$request->input('idproducto')?'background-color: #008cea;':'background-color: #6f7373;');
                $htmlstock = $htmlstock.'<tr>
                        <th style="'.$style.'text-align: center;" rowspan="2" width="5px">'.$value['nivel'].'</th>
                        <th style="'.$style.'" colspan="3">'.$value['productonombre'].'</th>
                      </tr><tr>
                        <th style="'.$style.'">'.$value['unidadmedidanombre'].' x '.$value['por'].'</th>
                        <th style="'.$style.'">'.$value['precioalpublico'].'</th>
                        <th style="'.$style.'text-align: center;">'.$value['cantidad'].'</th>
                      </tr>';
            }
          
            $htmlstock = $htmlstock.'</tbody></table>';
            
            return [
                'imagenes' => $imagenes,
                'stock' => $htmlstock
            ];
        }
        elseif($id=='show_mostrarpresentacion'){
            $producto = DB::table('s_producto')
                ->join('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->where('s_producto.idtienda',$idtienda)
                ->where('s_producto.id',$request->input('idproducto'))
                ->where('s_producto.s_idestado',1)
                ->select(
                        's_producto.*',
                        'unidadmedida.nombre as unidadmedidanombre',
                )
                ->first();

            //stock
            $stock_productosaldo = productosaldo($idtienda,$producto->id);
            return [
                'producto' => $producto,
                'stock' => $stock_productosaldo['stock'].' ('.$stock_productosaldo['stock_restante'].')'
            ];
        }
        if ($id == 'show-moduloactualizar'){
               json_producto($idtienda,$request->name_modulo);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $idproducto)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $producto = DB::table('s_producto')
                ->leftJoin('s_categoria as s_categoria1','s_categoria1.id','s_producto.s_idcategoria1')
                ->leftJoin('s_categoria as s_categoria2','s_categoria2.id','s_producto.s_idcategoria2')
                ->leftJoin('s_marca','s_marca.id','s_producto.s_idmarca')
                ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->where('s_producto.id',$idproducto)
                ->select(
                    's_producto.*',
                    's_categoria1.nombre as categoria1nombre',
                    's_categoria2.nombre as categoria2nombre',
                    's_marca.nombre as marcanombre',
                    'unidadmedida.nombre as unidadmedidanombre'
                )
                ->first();
      
        if($request->input('view') == 'editar') {
          
            $productomenor = DB::table('s_producto')
                ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->where('s_idproducto',$producto->id)
                ->select(
                    's_producto.*',
                    'unidadmedida.nombre as unidadmedidanombre'
                )
                ->first();
          
            $productomayor = DB::table('s_producto')
                ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->where('s_producto.id',$producto->s_idproducto)
                ->select(
                    's_producto.*',
                    'unidadmedida.nombre as unidadmedidanombre'
                )
                ->first();
          
            $marcas = DB::table('s_marca')->where('idtienda',$idtienda)->get();
            $categorias = DB::table('s_categoria')
                ->where('s_categoria.idtienda',$idtienda)
                ->where('s_categoria.s_idcategoria',0)
                ->orderBy('s_categoria.nombre','asc')
                ->get();
            $unidadmedidas  = DB::table('unidadmedida')->get();
            $configuracion = configuracion_comercio($idtienda);
          
            return view('layouts/backoffice/tienda/nuevosistema/producto/edit',[
                'producto' => $producto,
                'productomenor' => $productomenor,
                'productomayor' => $productomayor,
                'tienda' => $tienda,
                'marcas' => $marcas,
                'categorias' => $categorias,
                'unidadmedidas' => $unidadmedidas,
                'configuracion' => $configuracion,
                'asociarproductos' => descuento_producto($idtienda,$producto->id)['data']
            ]);
        }
        elseif($request->input('view') == 'detalle') {
            return view('layouts/backoffice/tienda/nuevosistema/producto/detalle',[
              'tienda' => $tienda,
              'producto' => $producto,
            ]);
        }
        elseif($request->input('view') == 'eliminar') {
            $productomenor = DB::table('s_producto')
                ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->where('s_idproducto',$producto->id)
                ->select(
                    's_producto.*',
                    'unidadmedida.nombre as unidadmedidanombre'
                )
                ->first();
          
            $productomayor = DB::table('s_producto')
                ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->where('s_producto.id',$producto->s_idproducto)
                ->select(
                    's_producto.*',
                    'unidadmedida.nombre as unidadmedidanombre'
                )
                ->first();
          
            $marcas = DB::table('s_marca')->where('idtienda',$idtienda)->get();
            $categorias = DB::table('s_categoria')
                ->where('s_categoria.idtienda',$idtienda)
                ->where('s_categoria.s_idcategoria',0)
                ->orderBy('s_categoria.nombre','asc')
                ->get();
            $unidadmedidas  = DB::table('unidadmedida')->get();
            $configuracion = configuracion_comercio($idtienda);
          
            return view('layouts/backoffice/tienda/nuevosistema/producto/delete',[
                'producto' => $producto,
                'productomenor' => $productomenor,
                'productomayor' => $productomayor,
                'tienda' => $tienda,
                'marcas' => $marcas,
                'categorias' => $categorias,
                'unidadmedidas' => $unidadmedidas,
                'configuracion' => $configuracion,
                'asociarproductos' => descuento_producto($idtienda,$producto->id)['data']
            ]);
        }
        elseif($request->input('view') == 'imagen') {
          
            $productogalerias = DB::table('s_productogaleria')->where('s_idproducto',$idproducto)->get();
          
            return view('layouts/backoffice/tienda/nuevosistema/producto/imagen',[
                'producto' => $producto,
                'tienda' => $tienda,
                'productogalerias' => $productogalerias
            ]);
        }
        /*elseif($request->input('view') == 'eliminar') {
          
            $marcas = DB::table('s_marca')->where('idtienda',$idtienda)->get();
            $categorias = DB::table('s_categoria')->where('idtienda',$idtienda)->get();
            return view('layouts/backoffice/tienda/nuevosistema/producto/delete',[
                'producto' => $producto,
                'tienda' => $tienda,
                'marcas' => $marcas,
                'categorias' => $categorias
            ]);
        }*/
        elseif($request->input('view') == 'ticketprecio') {
            return view('layouts/backoffice/tienda/nuevosistema/producto/ticketprecio',[
                'tienda' => $tienda,
                'producto' => $producto
            ]);
        }
        elseif($request->input('view') == 'ticketpreciopdf') {
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $marcas = DB::table('s_marca')->where('idtienda',$idtienda)->get();
            $categorias = DB::table('s_categoria')
                ->where('s_categoria.idtienda',$idtienda)
                ->where('s_categoria.s_idcategoria',0)
                ->orderBy('s_categoria.nombre','asc')
                ->get();
            $unidadmedidas  = DB::table('unidadmedida')->get();
            $configuracion = configuracion_comercio($idtienda);
            $pdf = PDF::loadView('layouts/backoffice/tienda/nuevosistema/producto/ticketpreciopdf',[
                'producto' => $producto,
                'tienda' => $tienda,
                'marcas' => $marcas,
                'categorias' => $categorias,
                'unidadmedidas' => $unidadmedidas,
                'configuracion' => $configuracion
            ]);
            return $pdf->stream('download.pdf');

        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idtienda, $idproducto)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'editar') {
            if($request->input('estadoeliminar')=='on') {
                /*$s_ventadetalles = DB::table('s_ventadetalle')
                  ->where('s_ventadetalle.s_idproducto',$idproducto)
                  ->count();
                $s_compradetalles = DB::table('s_compradetalle')
                  ->where('s_compradetalle.s_idproducto',$idproducto)
                  ->count();
              
                if($s_ventadetalles>0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Producto no se puede eliminar, ya que tiene '.$s_ventadetalles.' ventas.'
                    ]);
                }
                if($s_compradetalles>0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Producto no se puede eliminar, ya que tiene '.$s_compradetalles.' compras.'
                    ]);
                }*/
                DB::table('s_producto')->whereId($idproducto)->update([
                    's_idestado'  => 3
                ]);
                return response()->json([
                  'resultado' => 'CORRECTO',
                  'mensaje'   => 'Se ha eliminado correctamente.'
                ]);
            }else{
                $rules = [
                    'nombre'          => 'required', 
                    'precioalpublico' => 'required',  
                    'idcategoria'     => 'required', 
                    'stockminimo'     => 'required',
                    'alertavencimiento'=> 'required',
                    'idestadodetalle' => 'required', 
                    'idestado'        => 'required'
                ];

                $configuracion = configuracion_comercio($idtienda);
              
                    if($configuracion['estadounidadmedida']==1){
                        $rules = array_merge($rules,[
                            'idunidadmedida' => 'required', 
                            'por' => 'required',
                        ]);
                        if($request->input('estadoagruparunidadmedida')=='on'){
                            $rules = array_merge($rules,[
                                'idproducto' => 'required',
                            ]);
                        }
                    }
              

                $messages = [
                    'nombre.required'           => 'El "Nombre" es Obligatorio.',
                    'nombre.precioalpublico'    => 'El "Precio al Público" es Obligatorio.',
                    'idcategoria.required'      => 'La "Categoría" es Obligatorio.',
                    'idunidadmedida.required'   => 'La "Unidad de Medida" es Obligatorio.',
                    'por.required'              => 'El "Por (Cantidad)" es Obligatorio.',
                    'stockminimo.required'      => 'El "Stock Mínimo" es Obligatorio.',
                    'alertavencimiento.required'=> 'La "Alerta de Vencimiento" es Obligatorio.',
                    'idproducto.required'       => 'El "Producto a agrupar" es Obligatorio.',
                    'idestadodetalle.required'  => 'El "Estado de Detalle" es Obligatorio.',
                    'idestado.required'         => 'El "Estado" es Obligatorio.',
                ];
                $this->validate($request,$rules,$messages);
              
                $s_producto = DB::table('s_producto')
                    ->where('s_producto.id','<>',$idproducto)
                    ->where('codigo',$request->input('codigo'))
                    ->where('idtienda',$idtienda)
                    ->first();
                if($s_producto!='' and $request->input('codigo')!=''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Código de Producto" ya existe, Ingrese Otro por favor.'
                    ]);
                }
              
                if($request->input('stockminimo')<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Stock Mínimo", debe ser mayor o igual 0.'
                    ]);
                }
                if($request->input('alertavencimiento')<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La "Alerta de Vencimiento", debe ser mayor o igual 0.'
                    ]);
                }
              
              
                    if($configuracion['estadounidadmedida']==1){   
                        if($request->input('por')<=0){
                            return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje'   => 'El "Por (Cantidad de Unidades)" debe ser mayor a 0.'
                            ]);
                        }
                        
                        if($request->input('estadoagruparunidadmedida')=='on'){
                          
                            if($request->input('idunidadmedida')==1){
                                return response()->json([
                                    'resultado' => 'ERROR',
                                    'mensaje'   => 'Debe cambiar la "Unidad de Medida".'
                                ]);
                            }
                            if($request->input('por')<=1 && $request->input('estadoagruparunidadmedida')=='on'){
                                return response()->json([
                                    'resultado' => 'ERROR',
                                    'mensaje'   => 'El "Por (Cantidad de Unidades)" debe ser mayor a 1.'
                                ]);
                            }
                            if($request->input('idproducto')=='null'){
                                return response()->json([
                                    'resultado' => 'ERROR',
                                    'mensaje'   => 'El "Producto a agrupar" es Obligatorio.'
                                ]);
                            }
                            elseif($request->input('idproducto')==$idproducto){
                                return response()->json([
                                    'resultado' => 'ERROR',
                                    'mensaje'   => 'El producto a agrupar no puede ser el mismo producto, ingrese otro por favor.'
                                ]);
                            } 
                            $productomenor = DB::table('s_producto')
                                ->where('s_producto.id',$request->input('idproducto'))
                                ->where('s_producto.s_idproducto','<>',0)
                                ->first();
                            if($productomenor!=''){
                                return response()->json([
                                    'resultado' => 'ERROR',
                                    'mensaje'   => 'El "Producto a agrupar" ya esta en uso, ingrese otro por favor.'
                                ]);
                            }
                              
                            $producto = DB::table('s_producto')->whereId($idproducto)->first();
                            $stockproducto = productosaldo($idtienda,$producto->id)['stock'];
                            if($stockproducto<0){
                                return response()->json([
                                    'resultado' => 'ERROR',
                                    'mensaje'   => 'Para agrupar la presentación, el stock del producto actual no debe estar negativo.'
                                ]);
                            }
                        }
                        if($request->input('estadodesagruparunidadmedida')=='on'){
                            $productomayor = DB::table('s_producto')
                                ->where('s_producto.id',$idproducto)
                                ->where('s_producto.s_idproducto','<>',0)
                                ->first();
                            if($productomayor!=''){
                                return response()->json([
                                    'resultado' => 'ERROR',
                                    'mensaje'   => 'El "Producto actual" esta en uso, debe quitar primero el producto mayor.'
                                ]);
                            }
                        }   
                    }
                    if($configuracion['estadodescuento']==1){

                        $productosmaster = explode('/&&/', $request->input('descuento_seleccionarproductomaster'));
                        for($i = 1;$i <  count($productosmaster);$i++){
                            $itemmater = explode('/,,/', $productosmaster[$i]);

                            $productos = explode('/&&&/', $itemmater[1]);
                            for($ii = 1;$ii <  count($productos);$ii++){
                                $item = explode('/,,,/', $productos[$ii]);
                                if($item[0]==''){
                                    return response()->json([
                                        'resultado' => 'ERROR',
                                        'mensaje'   => 'El Producto es Obligarotio.'
                                    ]);
                                    break;
                                }
                            }

                            if($itemmater[0]==''){
                                return response()->json([
                                    'resultado' => 'ERROR',
                                    'mensaje'   => 'Es Obligatorio ingresar el Producto de descuento.'
                                ]);
                                break;
                            }elseif($itemmater[0]<=0){
                                return response()->json([
                                    'resultado' => 'ERROR',
                                    'mensaje'   => 'El Total a Descontar debe ser mayor a 0.00.'
                                ]);
                                break;
                            }
                        } 
                    }
           

                $s_idcategoria1 = 0; 
                $s_idcategoria2 = 0; 
                $s_idcategoria3 = 0; 

                $categoria1 = DB::table('s_categoria')->whereId($request->input('idcategoria'))->first();
                if($categoria1!=''){
                    $s_idcategoria1 = $categoria1->id;
                    $categoria2 = DB::table('s_categoria')->whereId($categoria1->s_idcategoria)->first();
                    if($categoria2!=''){
                        $s_idcategoria2 = $categoria1->id;
                        $s_idcategoria1 = $categoria2->id;
                        $categoria3 = DB::table('s_categoria')->whereId($categoria2->s_idcategoria)->first();
                        if($categoria3!=''){
                            $s_idcategoria3 = $categoria1->id;
                            $s_idcategoria2 = $categoria2->id;
                            $s_idcategoria1 = $categoria3->id;
                        }
                    }
                }

                DB::table('s_producto')->whereId($idproducto)->update([
                    'codigo'          => $request->input('codigo')!=null ? $request->input('codigo') : '',
                    'nombre'          => $request->input('nombre'),
                    'descripcion'     => $request->input('descripcion')!=null ? $request->input('descripcion') : '',
                    'precioalpublico' => $request->input('precioalpublico'),
                    'por'             => $request->input('por')!=null ? $request->input('por') : 1,
                    'stockminimo'     => $request->input('stockminimo'),
                    'alertavencimiento'=> $request->input('alertavencimiento'),
                    's_idcategoria1'  => $s_idcategoria1,
                    's_idcategoria2'  => $s_idcategoria2,
                    's_idcategoria3'  => $s_idcategoria3,
                    's_idmarca'       => $request->input('idmarca')!=null ? $request->input('idmarca') : 0,
                    's_idestadodetalle'     => $request->input('idestadodetalle'),
                    's_idestado'     => $request->input('idestado'),
                    's_idestadotiendavirtual'     => $request->input('idestadotiendavirtual'),
                    'idunidadmedida'  => $request->input('idunidadmedida')!=null ? $request->input('idunidadmedida') : 1
                    //'idproductopresentacion'  => $request->input('idproducto')!=null ? $request->input('idproducto') : 0
                ]);

            
                    // ESTADO UNIDAD DE MEDIDA
                    if($configuracion['estadounidadmedida']==1){
                        $productomenor = DB::table('s_producto')
                            ->where('s_idproducto',$idproducto)
                            ->first();
                        if($request->input('estadoagruparunidadmedida')=='on' && $request->input('estadodesagruparunidadmedida')!='on' && $productomenor=='' && $request->input('por')>1){
                          
                            DB::table('s_producto')->whereId($request->input('idproducto'))->update([
                                's_idproducto'  => $idproducto,
                                'idproductopresentacion'  => 1000000000
                            ]);
                          
                            DB::table('s_producto')->whereId($idproducto)->update([
                                'idproductopresentacion'  => $request->input('idproducto')
                            ]);
           
                            $presentaciones = producto_presentaciones($idtienda,$idproducto);
                            $agrupar_stock = 0;
                            $agrupar_productocodigo = '';
                            $agrupar_productonombre = '';
                            $agrupar_productounidadmedida = '';
                            $agrupar_productopornombre = '';
                            $agrupar_productopor = 0;
                            foreach($presentaciones as $value){
                                if($value['nivel']==1){
                                    $agrupar_stock = $value['por']*$value['cantidad'];
                                }
                                if($value['idproducto']==$idproducto){
                                    $agrupar_productopor = $value['productopor'];
                                }
                                $agrupar_productocodigo = $agrupar_productocodigo.'/<br>/'.$value['productocodigo'];
                                $agrupar_productonombre = $agrupar_productonombre.'/<br>/'.$value['productonombre'];
                                $agrupar_productounidadmedida = $agrupar_productounidadmedida.'/<br>/'.$value['productounidadmedida'];
                                $agrupar_productopornombre = $agrupar_productopornombre.'/<br>/'.$value['por'];
                            }
                            //stock actual
                            $stock = $agrupar_productopor*productosaldo($idtienda,$idproducto)['stock'];
                            $stock_agrupado = $stock+$agrupar_stock;
                            foreach($presentaciones as $value){
                              
                                $productosaldo_calcular = productosaldo_calcular($stock_agrupado,$value['productopor']);
                              
                                productosaldo_actualizar(
                                    $idtienda,
                                    'SALDO AGRUPADO',
                                    $agrupar_productocodigo,
                                    $agrupar_productonombre,
                                    $agrupar_productounidadmedida,
                                    $agrupar_productopornombre,
                                    0,
                                    0,
                                    0,
                                    $value['idproducto'],
                                    0,
                                    $productosaldo_calcular['stock'],
                                    $productosaldo_calcular['stock_restante']
                                );
                            }
                        }
                      
                        // desagrupar unidad de medida
                        if($request->input('estadoagruparunidadmedida')!='on' && $request->input('estadodesagruparunidadmedida')=='on') {
                          
                            $presentaciones = producto_presentaciones($idtienda,$idproducto);
                            $agrupar_productocodigo = '';
                            $agrupar_productonombre = '';
                            $agrupar_productounidadmedida = '';
                            $agrupar_productopornombre = '';
                            foreach($presentaciones as $value){
                                $agrupar_productocodigo = $agrupar_productocodigo.'/<br>/'.$value['productocodigo'];
                                $agrupar_productonombre = $agrupar_productonombre.'/<br>/'.$value['productonombre'];
                                $agrupar_productounidadmedida = $agrupar_productounidadmedida.'/<br>/'.$value['productounidadmedida'];
                                $agrupar_productopornombre = $agrupar_productopornombre.'/<br>/'.$value['por'];
                              
                                productosaldo_actualizar(
                                    $idtienda,
                                    'SALDO DESAGRUPADO',
                                    $agrupar_productocodigo,
                                    $agrupar_productonombre,
                                    $agrupar_productounidadmedida,
                                    $agrupar_productopornombre,
                                    0,
                                    0,
                                    0,
                                    $value['idproducto'],
                                    0,
                                    0,
                                    0
                                );
                            }  
                          
                            DB::table('s_producto')->whereId($idproducto)->update([
                                'por'  => 1,
                                'idproductopresentacion'  => 0
                            ]);
                            DB::table('s_producto')->where('s_idproducto',$idproducto)->update([
                                's_idproducto'  => 0,
                                'idproductopresentacion'  => 0
                            ]);
                        } 
                    }
                    // ESTADO DESCUENTO
                    if($configuracion['estadodescuento']==1){

                        $productodescuentos = DB::table('s_productodescuento')
                            ->join('s_productodescuentodetalle','s_productodescuentodetalle.s_idproductodescuento','s_productodescuento.id')
                            ->where('s_productodescuentodetalle.s_idproductoasociado',$idproducto)
                            ->select(
                                's_productodescuento.*'
                            )
                            ->orderBy('s_productodescuento.id','asc')
                            ->distinct()
                            ->get();

                        foreach($productodescuentos as $value){
                            DB::table('s_productodescuentodetalle')
                                ->join('s_producto as producto','producto.id','s_productodescuentodetalle.s_idproductoasociado')
                                ->where('s_productodescuentodetalle.s_idproductodescuento',$value->id)
                                ->delete();

                            DB::table('s_productodescuento')->whereId($value->id)->delete();
                        }

                        $productosmaster = explode('/&&/', $request->input('descuento_seleccionarproductomaster'));
                        for($i = 1;$i <  count($productosmaster);$i++){
                            $itemmater = explode('/,,/', $productosmaster[$i]);
                            $idproductodescuento = DB::table('s_productodescuento')->insertGetId([
                                'fecharegistro'  => Carbon::now(),
                                'codigo'  => Carbon::now()->format('dmYhms').rand(100000, 999999),
                                'total'  => '0.00',
                                'montodescuento'  => $itemmater[0],
                                'totalpack'  => '0.00',
                            ]);

                            $productos = explode('/&&&/', $itemmater[1]);
                            $total = 0;
                            for($ii = 1;$ii <  count($productos);$ii++){
                                $item = explode('/,,,/', $productos[$ii]);
                                DB::table('s_productodescuentodetalle')->insert([
                                    's_idproducto'  => $idproducto,
                                    's_idproductoasociado'  => $item[0],
                                    's_idproductodescuento'  => $idproductodescuento
                                ]);

                                $productoprecio = DB::table('s_producto')->whereId($item[0])->first();
                                $total = $total+$productoprecio->precioalpublico;
                            }
                            DB::table('s_productodescuento')->whereId($idproductodescuento)->update([
                                'total'  => $total,
                                'totalpack'  => $total-$itemmater[0]
                            ]);
                        } 
                    }
            

                DB::table('s_productodetalle')->where('s_idproducto',$idproducto)->delete();
                $listatitulo = explode('/-/',$request->input('contenido'));
                foreach($listatitulo as $value){
                  if($value!=''){
                    $listasubtitulo = explode('/,/',$value);
                    $idproductodetalle = explode('.',$listasubtitulo[0]);
                    if(count($idproductodetalle)>2){
                      $orden = $idproductodetalle[0];
                      $suborden = $idproductodetalle[1];
                      $subsuborden = $idproductodetalle[2];

                    }elseif(count($idproductodetalle)>1){
                      $orden = $idproductodetalle[0];
                      $suborden = $idproductodetalle[1];
                      $subsuborden = 0;
                    }else{
                      $orden = $listasubtitulo[0];
                      $suborden = 0;
                      $subsuborden = 0;
                    }
                    DB::table('s_productodetalle')->insert([
                        'orden' => $orden,
                        'suborden' => $suborden,
                        'subsuborden' => $subsuborden,
                        'nombre' => $listasubtitulo[1],
                        's_idproducto' => $idproducto
                    ]);
                  }
                }
            }
                
          
                      
            // cargar productos JSON
            //load_json_productos($idtienda);
          
            /*if($configuracion!=''){
                if($configuracion['estadodescuento']==1){
                    load_json_productos_descuento($idtienda);
                }
            }*/
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif($request->input('view')=='imagen'){

            $imagen = uploadfile('','',$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/producto/');

            $countproductogaleria = DB::table('s_productogaleria')->where('s_idproducto',$idproducto)->count();
            if($countproductogaleria==0){
                $make = getcwd().'/public/backoffice/tienda/'.$idtienda.'/producto/'.$imagen;
                resize_img($make,null,40,getcwd().'/public/backoffice/tienda/'.$idtienda.'/producto/40/',$imagen);
                resize_img($make,null,250,getcwd().'/public/backoffice/tienda/'.$idtienda.'/producto/250/',$imagen);
            }
            DB::table('s_productogaleria')->insert([
                'fecharegistro' => Carbon::now(),
                'orden' => $countproductogaleria+1,
                'imagen' => $imagen,
                's_idproducto' => $idproducto,
                's_idestado' => 1
            ]);
          
            
            // cargar productos JSON
            //load_json_productos($idtienda);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha registrado correctamente.'
            ]);
          
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda, $idproducto)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        /*if($request->input('view') == 'eliminar') {
            DB::table('s_productodescuentodetalle')
                ->join('s_producto as producto','producto.id','s_productodescuentodetalle.s_idproductoasociado')
                ->where('s_productodescuentodetalle.s_idproductodescuento',$idproducto)
                ->delete();
                      
            DB::table('s_productodescuento')->whereId($idproducto)->delete();
          
            $s_productogaleria = DB::table('s_productogaleria')->whereId($idproducto)->first();
            if($s_productogaleria!=''){
                uploadfile_eliminar($s_productogaleria->imagen,'/public/backoffice/tienda/'.$idtienda.'/producto/');
                uploadfile_eliminar($s_productogaleria->imagen,'/public/backoffice/tienda/'.$idtienda.'/producto/40/');
            }
            DB::table('s_productogaleria')->whereId($idproducto)->delete();
            DB::table('s_producto')
                ->whereId($idproducto)
                ->delete();
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }else*/
          if($request->input('view')=='eliminarimagen'){
          
            $s_productogaleria = DB::table('s_productogaleria')->whereId($idproducto)->first();
            if($s_productogaleria!=''){
                uploadfile_eliminar($s_productogaleria->imagen,'/public/backoffice/tienda/'.$idtienda.'/producto/');
                uploadfile_eliminar($s_productogaleria->imagen,'/public/backoffice/tienda/'.$idtienda.'/producto/40/');
            }
            DB::table('s_productogaleria')->whereId($idproducto)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
