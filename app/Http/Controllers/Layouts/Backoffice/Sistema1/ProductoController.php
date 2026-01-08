<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

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
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
       
        /*$s_producto  = DB::table('s_producto')
            //->where('s_producto.idtienda',$idtienda)
            ->select(
                's_producto.idtienda as idtienda',
                DB::raw('(SELECT imagen FROM s_productogaleria WHERE s_idproducto=s_producto.id ORDER BY orden ASC LIMIT 1) as imagen')
            )
            ->orderBy('s_producto.id','desc')
            ->limit(7000)
            ->get();
        foreach($s_producto as $value){
            if($value->imagen!=''){
                $estructura = getcwd().'/public/backoffice/tienda/'.$value->idtienda.'/producto/40/';
                if (!file_exists($estructura.$value->imagen)) {
                    $extencion = explode('.',$value->imagen);
                    if(count($extencion)>1){
                        if($extencion[1]=='svg' or 
                           $extencion[1]=='jfif' or 
                           $extencion[1]=='S' or 
                           $extencion[1]=='11 FACE' or 
                           $extencion[1]=='11 WEB' or 
                           $extencion[1]=='37' or 
                           $extencion[1]=='com_1' or 
                           $extencion[1]=='jpg_220x220 'or 
                           $extencion[1]=='jpg_q50' or 
                           $extencion[1]=='56' or 
                           $extencion[1]=='jpg_220x220' or 
                           $extencion[1]=='53'){
                        }else{
                            $make = getcwd().'/public/backoffice/tienda/'.$value->idtienda.'/producto/'.$value->imagen;
                            if (file_exists($make)) {
                                resize_img($make,null,40,$estructura,$value->imagen);
                            }
                        }
                    }     
                }
                $estructura = getcwd().'/public/backoffice/tienda/'.$value->idtienda.'/producto/250/';
                if (!file_exists($estructura.$value->imagen)) {
                    $extencion = explode('.',$value->imagen);
                    if(count($extencion)>1){
                        if($extencion[1]=='svg' or 
                           $extencion[1]=='jfif' or 
                           $extencion[1]=='S' or 
                           $extencion[1]=='11 FACE' or 
                           $extencion[1]=='11 WEB' or 
                           $extencion[1]=='37' or 
                           $extencion[1]=='com_1' or 
                           $extencion[1]=='jpg_220x220'or 
                           $extencion[1]=='jpg_q50' or 
                           $extencion[1]=='56' or 
                           $extencion[1]=='jpg_220x220' or 
                           $extencion[1]=='53'){
                        }else{
                            $make = getcwd().'/public/backoffice/tienda/'.$value->idtienda.'/producto/'.$value->imagen;
                            if (file_exists($make)) {
                                resize_img($make,null,250,$estructura,$value->imagen);
                            }
                        }
                    }       
                }
            }
        }*/
      
        return view('layouts/backoffice/tienda/sistema/producto/index',[
            'tienda'      => $tienda
        ]);
    }

    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda       = DB::table('tienda')->whereId($idtienda)->first();
        $marcas       = DB::table('s_marca')->where('idtienda',$idtienda)->where('idestado',1)->get();
        $categorias = DB::table('s_categoria')
                ->where('idestado',1)
                ->where('s_categoria.idtienda',$idtienda)
                ->where('s_categoria.s_idcategoria',0)
                ->orderBy('s_categoria.nombre','asc')
                ->get();
        $unidadmedidas  = DB::table('unidadmedida')->get();

        return view('layouts/backoffice/tienda/sistema/producto/create',[
            'tienda' => $tienda,
            'marcas' => $marcas,
            'categorias' => $categorias,
            'unidadmedidas' => $unidadmedidas,
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
          
            if(configuracion($idtienda,'sistema_estadounidadmedida')['valor']==1){
                $rules = array_merge($rules,[
                    'idunidadmedida' => 'required',
                    'por' => 'required',
                ]);
              
                if($request->por>1){
                    $rules = array_merge($rules,[
                        'idproducto' => 'required',
                    ]);
                }
            }
          
            $messages = [
                'nombre.required'   => 'El "Nombre" es Obligatorio.',
                'precioalpublico.required'   => 'El "Precio al Público" es Obligatorio.',
                'idcategoria.required'   => 'La "Categoría" es Obligatorio.',
                'idunidadmedida.required'   => 'La "Unidad de Medida" es Obligatorio.',
                'por.required'   => 'El "Por (Cantidad de Unidades)" es Obligatorio.',
                'idproducto.required'   => 'El "Producto a agrupar" es Obligatorio.',
            ];
      
            $this->validate($request,$rules,$messages);
          
            if(configuracion($idtienda,'sistema_estadounidadmedida')['valor']==1){
                if($request->input('por')<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Por (Cantidad de Unidades)" debe ser mayor a 0.'
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
            }
                        
          
            $s_producto = DB::table('s_producto')
                ->where('codigo',$request->input('codigo'))
                ->where('s_producto.s_idestado',1)
                ->where('s_producto.idtienda',$idtienda)
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

            $producto_por = $request->input('por')!=null ? $request->input('por') : 1;
            if(configuracion($idtienda,'sistema_tipocodigoproducto')['valor']==2){
                $idproducto = DB::table('s_producto')->insertGetId([
                    'fecharegistro'  => Carbon::now(),
                    'orden'  => 0,
                    'codigo'         => $request->input('codigo1')!=null ? $request->input('codigo1') : '',
                    'codigo2'         => $request->input('codigo2')!=null ? $request->input('codigo2') : '',
                    'codigo3'         => $request->input('codigo3')!=null ? $request->input('codigo3') : '',
                    'codigo4'         => $request->input('codigo4')!=null ? $request->input('codigo4') : '',
                    'codigo5'         => $request->input('codigo5')!=null ? $request->input('codigo5') : '',
                    'codigo6'         => $request->input('codigo6')!=null ? $request->input('codigo6') : '',
                    'codigo7'         => $request->input('codigo7')!=null ? $request->input('codigo7') : '',
                    'codigo8'         => $request->input('codigo8')!=null ? $request->input('codigo8') : '',
                    'codigo9'         => $request->input('codigo9')!=null ? $request->input('codigo9') : '',
                    'codigo10'         => $request->input('codigo10')!=null ? $request->input('codigo10') : '',
                    'nombre'         => $request->input('nombre'),
                    'descripcion'    => '',
                    'preciopormayor' => '0.00',
                    'precioalpublico' => $request->input('precioalpublico'),
                    'por' => $producto_por,
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
                    's_idestadosistema'     => 1,
                    'idunidadmedida'  => 1,
                    'idproductopresentacion'  => 0,
                    'idtienda'     => $idtienda
                ]);
            }else{
                $idproducto = DB::table('s_producto')->insertGetId([
                    'fecharegistro'  => Carbon::now(),
                    'orden'  => 0,
                    'codigo'         => $request->input('codigo')!=null ? $request->input('codigo') : '',
                    'nombre'         => $request->input('nombre'),
                    'descripcion'    => '',
                    'preciopormayor' => '0.00',
                    'precioalpublico' => $request->input('precioalpublico'),
                    'por' => $producto_por,
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
                    's_idestadosistema'     => 1,
                    'idunidadmedida'  => 1,
                    'idproductopresentacion'  => 0,
                    'idtienda'     => $idtienda
                ]);
            }
                
          
                    // ESTADO UNIDAD DE MEDIDA
                    if(configuracion($idtienda,'sistema_estadounidadmedida')['valor']==1){
                        $productomenor = DB::table('s_producto')
                            ->where('s_idproducto',$idproducto)
                            ->first();
                        if($productomenor=='' && $producto_por>1){
                          
                            DB::table('s_producto')->whereId($request->input('idproducto'))->update([
                                's_idproducto'  => $idproducto,
                                'idproductopresentacion'  => 1000000000
                            ]);
                          
                            DB::table('s_producto')->whereId($idproducto)->update([
                                'idproductopresentacion'  => $request->input('idproducto'),
                                'idunidadmedida'  => $request->input('idunidadmedida'),
                            ]);
                          
                            productosaldo_actualizar(
                                $idtienda,
                                $idproducto,
                                'SALDO AGRUPADO',
                                0,
                                $producto_por,
                                $request->input('idunidadmedida'),
                                $request->input('idproducto'),
                            );
                        }
                    }
          
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
        /*elseif($id=='showseleccionarproducto'){
            return show_producto($idtienda,$request->input('idproducto'));
        }*/
        elseif($id=='showstockimagenproducto'){
            $s_productogaleria = DB::table('s_productogaleria')
                                  ->join('s_producto','s_producto.id','s_productogaleria.s_idproducto')
                                  ->where('s_producto.idtienda',$idtienda)
                                  ->where('s_productogaleria.s_idproducto',$request->input('idproducto'))
                                  ->select('s_productogaleria.*')
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
                      $imagenes = $imagenes.'</div>
                              <script>
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
            //$presentaciones = collect($presentaciones)->sortByDesc('nivel');
            
            $htmlstock = '<table class="table">
                <thead class="thead-dark">
                  <tr>
                    <th width="5px">N°</th>
                    <th style="height: 30px;padding-top: 0;padding-bottom: 0;">Producto</th>
                    <th style="height: 30px;padding-top: 0;padding-bottom: 0;" width="120px">U. Medida</th>
                    <th style="height: 30px;padding-top: 0;padding-bottom: 0;" width="10px">Precio</th>
                    <th style="height: 30px;padding-top: 0;padding-bottom: 0;" width="10px">Stock</th>
                  </tr>
                </thead>
                <tbody>';
            //$cantidad = 0;
            $i=0;
            foreach($presentaciones as $value){
                /*if($i==0){
                    $cantidad = $value['cantidad'];
                }*/
                $style = 'padding: 6px;border-right: 1px solid #000;border-bottom: 1px solid #000;color: #fff;'.($value['idproducto']==$request->input('idproducto')?'background-color: #008cea;':'background-color: #6f7373;');
                $por = $value['por']!=0?$value['por']:1;
                $htmlstock = $htmlstock.'<tr>
                        <th style="'.$style.'text-align: center;">'.$value['nivel'].'</th>
                        <th style="'.$style.'">'.$value['productonombre'].'</th>
                        <th style="'.$style.'">'.$value['unidadmedidanombre'].' x '.$por.'</th>
                        <th style="'.$style.'">'.$value['precioalpublico'].'</th>
                        <th style="'.$style.'text-align: center;">'.$value['cantidad'].'</th>
                      </tr>';
                //$cantidad = number_format(($value['cantidad']/$por), 3, '.', '');
                $i++;
            }
          
            $htmlstock = $htmlstock.'</tbody></table>';
            
            return [
                'imagenes' => $imagenes,
                'stock' => $htmlstock
            ];
        }
        elseif($id=='show_buscarproducto'){
          
            $buscar_estadoProducto = $request->input('columns')[1]['search']['value'];
            $buscar_estadotvProducto = $request->input('columns')[2]['search']['value'];
            $buscar_codigoProducto = $request->input('columns')[3]['search']['value'];
            $buscar_nombreProducto = $request->input('columns')[4]['search']['value'];
            $buscar_nombreCategoria = $request->input('columns')[5]['search']['value'];
            $buscar_nombreMarca = $request->input('columns')[6]['search']['value'];
          
            session(['buscar_estadoProducto' => $buscar_estadoProducto]);
            session(['buscar_estadotvProducto' => $buscar_estadotvProducto]);
            session(['buscar_codigoProducto' => $buscar_codigoProducto]);
            session(['buscar_nombreProducto' => $buscar_nombreProducto]);
            session(['buscar_nombreCategoria' => $buscar_nombreCategoria]);
            session(['buscar_nombreMarca' => $buscar_nombreMarca]);

            $where = [];
            $where[] = ['s_producto.idtienda',$idtienda];
            $where[] = ['s_producto.s_idestado',1];
            if($buscar_nombreProducto!=''){
                $where[] = ['s_producto.nombre','LIKE','%'.$buscar_nombreProducto.'%'];
            }
            if($buscar_estadoProducto!=''){
                $where[] = ['s_producto.s_idestado',$buscar_estadoProducto];
            }
            if($buscar_codigoProducto!=''){
                $where[] = ['s_producto.codigo',$buscar_codigoProducto];
            }
            if($buscar_estadotvProducto!=''){
                $where[] = ['s_producto.s_idestadotiendavirtual',$buscar_estadotvProducto];
            }
            if($buscar_nombreMarca!=''){
                $where[] = ['s_marca.nombre','LIKE','%'.$buscar_nombreMarca.'%'];
            }
            if($buscar_nombreCategoria!=''){
                $where[] = ['s_categoria.nombre','LIKE','%'.$buscar_nombreCategoria.'%'];
            }
          
            $where1 = [];
            $where1[] = ['s_producto.idtienda',$idtienda];
            $where1[] = ['s_producto.s_idestado',1];
            if($buscar_nombreProducto!=''){
                $where1[] = ['s_producto.nombre','LIKE','%'.$buscar_nombreProducto.'%'];
            }
            if($buscar_estadoProducto!=''){
                $where1[] = ['s_producto.s_idestado',$buscar_estadoProducto];
            }
            if($buscar_codigoProducto!=''){
                $where1[] = ['s_producto.codigo2',$buscar_codigoProducto];
            }
            if($buscar_estadotvProducto!=''){
                $where1[] = ['s_producto.s_idestadotiendavirtual',$buscar_estadotvProducto];
            }
            if($buscar_nombreMarca!=''){
                $where1[] = ['s_marca.nombre','LIKE','%'.$buscar_nombreMarca.'%'];
            }
            if($buscar_nombreCategoria!=''){
                $where1[] = ['s_categoria.nombre','LIKE','%'.$buscar_nombreCategoria.'%'];
            }
          
            $where2 = [];
            $where2[] = ['s_producto.idtienda',$idtienda];
            $where2[] = ['s_producto.s_idestado',1];
            if($buscar_nombreProducto!=''){
                $where2[] = ['s_producto.nombre','LIKE','%'.$buscar_nombreProducto.'%'];
            }
            if($buscar_estadoProducto!=''){
                $where2[] = ['s_producto.s_idestado',$buscar_estadoProducto];
            }
            if($buscar_codigoProducto!=''){
                $where2[] = ['s_producto.codigo3',$buscar_codigoProducto];
            }
            if($buscar_estadotvProducto!=''){
                $where2[] = ['s_producto.s_idestadotiendavirtual',$buscar_estadotvProducto];
            }
            if($buscar_nombreMarca!=''){
                $where2[] = ['s_marca.nombre','LIKE','%'.$buscar_nombreMarca.'%'];
            }
            if($buscar_nombreCategoria!=''){
                $where2[] = ['s_categoria.nombre','LIKE','%'.$buscar_nombreCategoria.'%'];
            }
          
            $where3 = [];
            $where3[] = ['s_producto.idtienda',$idtienda];
            $where3[] = ['s_producto.s_idestado',1];
            if($buscar_nombreProducto!=''){
                $where3[] = ['s_producto.nombre','LIKE','%'.$buscar_nombreProducto.'%'];
            }
            if($buscar_estadoProducto!=''){
                $where3[] = ['s_producto.s_idestado',$buscar_estadoProducto];
            }
            if($buscar_codigoProducto!=''){
                $where3[] = ['s_producto.codigo4',$buscar_codigoProducto];
            }
            if($buscar_estadotvProducto!=''){
                $where3[] = ['s_producto.s_idestadotiendavirtual',$buscar_estadotvProducto];
            }
            if($buscar_nombreMarca!=''){
                $where3[] = ['s_marca.nombre','LIKE','%'.$buscar_nombreMarca.'%'];
            }
            if($buscar_nombreCategoria!=''){
                $where3[] = ['s_categoria.nombre','LIKE','%'.$buscar_nombreCategoria.'%'];
            }
          
            $where4 = [];
            $where4[] = ['s_producto.idtienda',$idtienda];
            $where4[] = ['s_producto.s_idestado',1];
            if($buscar_nombreProducto!=''){
                $where4[] = ['s_producto.nombre','LIKE','%'.$buscar_nombreProducto.'%'];
            }
            if($buscar_estadoProducto!=''){
                $where4[] = ['s_producto.s_idestado',$buscar_estadoProducto];
            }
            if($buscar_codigoProducto!=''){
                $where4[] = ['s_producto.codigo5',$buscar_codigoProducto];
            }
            if($buscar_estadotvProducto!=''){
                $where4[] = ['s_producto.s_idestadotiendavirtual',$buscar_estadotvProducto];
            }
            if($buscar_nombreMarca!=''){
                $where4[] = ['s_marca.nombre','LIKE','%'.$buscar_nombreMarca.'%'];
            }
            if($buscar_nombreCategoria!=''){
                $where4[] = ['s_categoria.nombre','LIKE','%'.$buscar_nombreCategoria.'%'];
            }
            
            $where5 = [];
            $where5[] = ['s_producto.idtienda',$idtienda];
            $where5[] = ['s_producto.s_idestado',1];
            if($buscar_nombreProducto!=''){
                $where5[] = ['s_producto.nombre','LIKE','%'.$buscar_nombreProducto.'%'];
            }
            if($buscar_estadoProducto!=''){
                $where5[] = ['s_producto.s_idestado',$buscar_estadoProducto];
            }
            if($buscar_codigoProducto!=''){
                $where5[] = ['s_producto.codigo6',$buscar_codigoProducto];
            }
            if($buscar_estadotvProducto!=''){
                $where5[] = ['s_producto.s_idestadotiendavirtual',$buscar_estadotvProducto];
            }
            if($buscar_nombreMarca!=''){
                $where5[] = ['s_marca.nombre','LIKE','%'.$buscar_nombreMarca.'%'];
            }
            if($buscar_nombreCategoria!=''){
                $where5[] = ['s_categoria.nombre','LIKE','%'.$buscar_nombreCategoria.'%'];
            }
            
            $where6 = [];
            $where6[] = ['s_producto.idtienda',$idtienda];
            $where6[] = ['s_producto.s_idestado',1];
            if($buscar_nombreProducto!=''){
                $where6[] = ['s_producto.nombre','LIKE','%'.$buscar_nombreProducto.'%'];
            }
            if($buscar_estadoProducto!=''){
                $where6[] = ['s_producto.s_idestado',$buscar_estadoProducto];
            }
            if($buscar_codigoProducto!=''){
                $where6[] = ['s_producto.codigo7',$buscar_codigoProducto];
            }
            if($buscar_estadotvProducto!=''){
                $where6[] = ['s_producto.s_idestadotiendavirtual',$buscar_estadotvProducto];
            }
            if($buscar_nombreMarca!=''){
                $where6[] = ['s_marca.nombre','LIKE','%'.$buscar_nombreMarca.'%'];
            }
            if($buscar_nombreCategoria!=''){
                $where6[] = ['s_categoria.nombre','LIKE','%'.$buscar_nombreCategoria.'%'];
            }
            
            $where7 = [];
            $where7[] = ['s_producto.idtienda',$idtienda];
            $where7[] = ['s_producto.s_idestado',1];
            if($buscar_nombreProducto!=''){
                $where7[] = ['s_producto.nombre','LIKE','%'.$buscar_nombreProducto.'%'];
            }
            if($buscar_estadoProducto!=''){
                $where7[] = ['s_producto.s_idestado',$buscar_estadoProducto];
            }
            if($buscar_codigoProducto!=''){
                $where7[] = ['s_producto.codigo8',$buscar_codigoProducto];
            }
            if($buscar_estadotvProducto!=''){
                $where7[] = ['s_producto.s_idestadotiendavirtual',$buscar_estadotvProducto];
            }
            if($buscar_nombreMarca!=''){
                $where7[] = ['s_marca.nombre','LIKE','%'.$buscar_nombreMarca.'%'];
            }
            if($buscar_nombreCategoria!=''){
                $where7[] = ['s_categoria.nombre','LIKE','%'.$buscar_nombreCategoria.'%'];
            }
            
            $where8 = [];
            $where8[] = ['s_producto.idtienda',$idtienda];
            $where8[] = ['s_producto.s_idestado',1];
            if($buscar_nombreProducto!=''){
                $where8[] = ['s_producto.nombre','LIKE','%'.$buscar_nombreProducto.'%'];
            }
            if($buscar_estadoProducto!=''){
                $where8[] = ['s_producto.s_idestado',$buscar_estadoProducto];
            }
            if($buscar_codigoProducto!=''){
                $where8[] = ['s_producto.codigo9',$buscar_codigoProducto];
            }
            if($buscar_estadotvProducto!=''){
                $where8[] = ['s_producto.s_idestadotiendavirtual',$buscar_estadotvProducto];
            }
            if($buscar_nombreMarca!=''){
                $where8[] = ['s_marca.nombre','LIKE','%'.$buscar_nombreMarca.'%'];
            }
            if($buscar_nombreCategoria!=''){
                $where8[] = ['s_categoria.nombre','LIKE','%'.$buscar_nombreCategoria.'%'];
            }
            
            $where9 = [];
            $where9[] = ['s_producto.idtienda',$idtienda];
            $where9[] = ['s_producto.s_idestado',1];
            if($buscar_nombreProducto!=''){
                $where9[] = ['s_producto.nombre','LIKE','%'.$buscar_nombreProducto.'%'];
            }
            if($buscar_estadoProducto!=''){
                $where9[] = ['s_producto.s_idestado',$buscar_estadoProducto];
            }
            if($buscar_codigoProducto!=''){
                $where9[] = ['s_producto.codigo10',$buscar_codigoProducto];
            }
            if($buscar_estadotvProducto!=''){
                $where9[] = ['s_producto.s_idestadotiendavirtual',$buscar_estadotvProducto];
            }
            if($buscar_nombreMarca!=''){
                $where9[] = ['s_marca.nombre','LIKE','%'.$buscar_nombreMarca.'%'];
            }
            if($buscar_nombreCategoria!=''){
                $where9[] = ['s_categoria.nombre','LIKE','%'.$buscar_nombreCategoria.'%'];
            }
            $productos = DB::table('s_producto')
              ->join('tienda','tienda.id','s_producto.idtienda')
              ->leftJoin('s_categoria','s_categoria.id','s_producto.s_idcategoria1')
              ->leftJoin('s_categoria as subcategoria','subcategoria.id','s_producto.s_idcategoria2')
              ->leftJoin('s_marca','s_marca.id','s_producto.s_idmarca')
              ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
              ->where($where)
              ->orWhere($where1)
              ->orWhere($where2)
              ->orWhere($where3)
              ->orWhere($where4)
              ->orWhere($where5)
              ->orWhere($where6)
              ->orWhere($where7)
              ->orWhere($where8)
              ->orWhere($where9)
              ->select(
                's_producto.id as id',
                //'s_producto.codigo as codigo',
                 DB::raw('IF(s_producto.codigo2<>"",
                              IF(s_producto.codigo3<>"",
                                  IF(s_producto.codigo4<>"",
                                      IF(s_producto.codigo5<>"",
                                          IF(s_producto.codigo6<>"",
                                              IF(s_producto.codigo7<>"",
                                                  IF(s_producto.codigo8<>"",
                                                      IF(s_producto.codigo9<>"",
                                                          IF(s_producto.codigo10<>"",
                                                              CONCAT(s_producto.codigo,"<br>",s_producto.codigo2,"<br>",s_producto.codigo3,"<br>",s_producto.codigo4,"<br>",s_producto.codigo5,"<br>",s_producto.codigo6,"<br>",s_producto.codigo7,"<br>",s_producto.codigo8,"<br>",s_producto.codigo9,"<br>",s_producto.codigo10),
                                                              CONCAT(s_producto.codigo,"<br>",s_producto.codigo2,"<br>",s_producto.codigo3,"<br>",s_producto.codigo4,"<br>",s_producto.codigo5,"<br>",s_producto.codigo6,"<br>",s_producto.codigo7,"<br>",s_producto.codigo8,"<br>",s_producto.codigo9)),
                                                          CONCAT(s_producto.codigo,"<br>",s_producto.codigo2,"<br>",s_producto.codigo3,"<br>",s_producto.codigo4,"<br>",s_producto.codigo5,"<br>",s_producto.codigo6,"<br>",s_producto.codigo7,"<br>",s_producto.codigo8)),
                                                      CONCAT(s_producto.codigo,"<br>",s_producto.codigo2,"<br>",s_producto.codigo3,"<br>",s_producto.codigo4,"<br>",s_producto.codigo5,"<br>",s_producto.codigo6,"<br>",s_producto.codigo7)),
                                                  CONCAT(s_producto.codigo,"<br>",s_producto.codigo2,"<br>",s_producto.codigo3,"<br>",s_producto.codigo4,"<br>",s_producto.codigo5,"<br>",s_producto.codigo6)),
                                              CONCAT(s_producto.codigo,"<br>",s_producto.codigo2,"<br>",s_producto.codigo3,"<br>",s_producto.codigo4,"<br>",s_producto.codigo5)),
                                          CONCAT(s_producto.codigo,"<br>",s_producto.codigo2,"<br>",s_producto.codigo3,"<br>",s_producto.codigo4)),
                                      CONCAT(s_producto.codigo,"<br>",s_producto.codigo2,"<br>",s_producto.codigo3)),
                                  CONCAT(s_producto.codigo,"<br>",s_producto.codigo2)),
                              s_producto.codigo) as codigo'),
                's_producto.nombre as nombre',
                's_producto.precioalpublico as precioalpublico',
                's_producto.s_idestadodetalle as idestadodetalle',
                's_producto.s_idestado as idestado',
                's_producto.s_idestadotiendavirtual as idestadotv',
                's_producto.idproductopresentacion as idproductopresentacion',
                's_producto.alertavencimiento as alertavencimiento',
                 DB::raw('TIMESTAMPDIFF(DAY, CURDATE(), s_producto.fechavencimiento) as diasfaltantevencimiento'),
                 DB::raw('CONCAT(unidadmedida.nombre," x ",s_producto.por) as unidadmedida'),
                 DB::raw('CONCAT(s_producto.nombre," / ",s_producto.precioalpublico) as text'),
                 'tienda.id as idtienda',
                 'tienda.nombre as tiendanombre',
                 'tienda.link as tiendalink',
                 's_marca.nombre as marcanombre',
                 's_categoria.nombre as categorianombre',
                 'subcategoria.nombre as subcategorianombre',
                 DB::raw('(SELECT imagen FROM s_productogaleria WHERE s_idproducto=s_producto.id ORDER BY orden ASC LIMIT 1) as imagen')
              )
              ->orderBy('s_producto.id','desc')
              ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));
 
            return json_encode(
              array(
                'draw' => $request->input('draw'),
                'recordsTotal' => $productos->total(),
                'recordsFiltered' => $productos->total(),
                'data' => $productos->items()
              )
            );
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
            $productosaldo = productosaldo($idtienda,$producto->id);
            return [
                'producto' => $producto,
                'stock' => $productosaldo['stock']
            ];
        }
    }
  
    public function edit(Request $request, $idtienda, $idproducto)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $producto = DB::table('s_producto')
                ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->where('s_producto.id',$idproducto)
                ->select(
                    's_producto.*',
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
          
            $marcas = DB::table('s_marca')->where('idtienda',$idtienda)->where('idestado',1)->get();
            $categorias = DB::table('s_categoria')
                ->where('idestado',1)
                ->where('s_categoria.idtienda',$idtienda)
                ->where('s_categoria.s_idcategoria',0)
                ->orderBy('s_categoria.nombre','asc')
                ->get();
            $unidadmedidas  = DB::table('unidadmedida')->get();
          
            return view('layouts/backoffice/tienda/sistema/producto/edit',[
                'producto' => $producto,
                'productomenor' => $productomenor,
                'productomayor' => $productomayor,
                'tienda' => $tienda,
                'marcas' => $marcas,
                'categorias' => $categorias,
                'unidadmedidas' => $unidadmedidas,
                'asociarproductos' => descuento_producto($idtienda,$producto->id)['data']
            ]);
        }
        elseif($request->input('view') == 'imagen') {
          
            $productogalerias = DB::table('s_productogaleria')->where('s_idproducto',$idproducto)->get();
          
            return view('layouts/backoffice/tienda/sistema/producto/imagen',[
                'producto' => $producto,
                'tienda' => $tienda,
                'productogalerias' => $productogalerias
            ]);
        }
        /*elseif($request->input('view') == 'eliminar') {
          
            $marcas = DB::table('s_marca')->where('idtienda',$idtienda)->get();
            $categorias = DB::table('s_categoria')->where('idtienda',$idtienda)->get();
            return view('layouts/backoffice/tienda/sistema/producto/delete',[
                'producto' => $producto,
                'tienda' => $tienda,
                'marcas' => $marcas,
                'categorias' => $categorias
            ]);
        }*/
        elseif($request->input('view') == 'ticketprecio') {
            return view('layouts/backoffice/tienda/sistema/producto/ticketprecio',[
                'tienda' => $tienda,
                'producto' => $producto
            ]);
        }
        elseif($request->input('view') == 'ticketpreciopdf') {
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/producto/ticketpreciopdf',[
                'producto' => $producto,
                'tienda' => $tienda,
            ]);
            return $pdf->stream('download.pdf');

        }
        elseif($request->input('view') == 'codigobarra') {
            return view('layouts/backoffice/tienda/sistema/producto/codigobarra',[
                'tienda' => $tienda,
                'producto' => $producto
            ]);
        }
        elseif($request->input('view') == 'codigobarrapdf') {
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/producto/codigobarrapdf',[
                'producto' => $producto,
                'tienda' => $tienda,
            ]);
            return $pdf->stream('download.pdf');
        }
    }

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
                    's_idestado'  => 2
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

          
              
                    if(configuracion($idtienda,'sistema_estadounidadmedida')['valor']==1){
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
                    'precioalpublico.required'    => 'El "Precio al Público" es Obligatorio.',
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
                    ->where('s_idestado',1)
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
              
              
                    if(configuracion($idtienda,'sistema_estadounidadmedida')['valor']==1){   
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
                            if($stockproducto!=0){
                                return response()->json([
                                    'resultado' => 'ERROR',
                                    'mensaje'   => 'Para agrupar la presentación, el stock del producto actual debe estar en 0.'
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
                    if(configuracion($idtienda,'sistema_estadodescuento')['valor']==1){

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
           
   
                if(configuracion($idtienda,'sistema_tipocodigoproducto')['valor']==2){
                    DB::table('s_producto')->whereId($idproducto)->update([
                        'codigo'         => $request->input('codigo1')!=null ? $request->input('codigo1') : '',
                        'codigo2'         => $request->input('codigo2')!=null ? $request->input('codigo2') : '',
                        'codigo3'         => $request->input('codigo3')!=null ? $request->input('codigo3') : '',
                        'codigo4'         => $request->input('codigo4')!=null ? $request->input('codigo4') : '',
                        'codigo5'         => $request->input('codigo5')!=null ? $request->input('codigo5') : '',
                        'codigo6'         => $request->input('codigo6')!=null ? $request->input('codigo6') : '',
                        'codigo7'         => $request->input('codigo7')!=null ? $request->input('codigo7') : '',
                        'codigo8'         => $request->input('codigo8')!=null ? $request->input('codigo8') : '',
                        'codigo9'         => $request->input('codigo9')!=null ? $request->input('codigo9') : '',
                        'codigo10'         => $request->input('codigo10')!=null ? $request->input('codigo10') : '',
                        'nombre'          => $request->input('nombre'),
                        'descripcion'     => $request->input('descripcion')!=null ? $request->input('descripcion') : '',
                        'precioalpublico' => $request->input('precioalpublico'),
                        'por'             => $request->input('por')!=null ? $request->input('por') : 1,
                        'stockminimo'     => $request->input('stockminimo'),
                        'alertavencimiento'=> $request->input('alertavencimiento'),
                        'fechavencimiento'=> $request->input('fechavencimiento'),
                        's_idcategoria1'  => $s_idcategoria1,
                        's_idcategoria2'  => $s_idcategoria2,
                        's_idcategoria3'  => $s_idcategoria3,
                        's_idmarca'       => $request->input('idmarca')!=null ? $request->input('idmarca') : 0,
                        's_idestadodetalle'     => $request->input('idestadodetalle'),
                        's_idestadotiendavirtual'     => $request->input('idestadotiendavirtual'),
                        's_idestadosistema'     => $request->input('idestado'),
                        'idunidadmedida'  => $request->input('idunidadmedida')!=null ? $request->input('idunidadmedida') : 1
                    ]);
                }else{
                    DB::table('s_producto')->whereId($idproducto)->update([
                        'codigo'          => $request->input('codigo')!=null ? $request->input('codigo') : '',
                        'nombre'          => $request->input('nombre'),
                        'descripcion'     => $request->input('descripcion')!=null ? $request->input('descripcion') : '',
                        'precioalpublico' => $request->input('precioalpublico'),
                        'por'             => $request->input('por')!=null ? $request->input('por') : 1,
                        'stockminimo'     => $request->input('stockminimo'),
                        'alertavencimiento'=> $request->input('alertavencimiento'),
                        'fechavencimiento'=> $request->input('fechavencimiento'),
                        's_idcategoria1'  => $s_idcategoria1,
                        's_idcategoria2'  => $s_idcategoria2,
                        's_idcategoria3'  => $s_idcategoria3,
                        's_idmarca'       => $request->input('idmarca')!=null ? $request->input('idmarca') : 0,
                        's_idestadodetalle'     => $request->input('idestadodetalle'),
                        's_idestadotiendavirtual'     => $request->input('idestadotiendavirtual'),
                        's_idestadosistema'     => $request->input('idestado'),
                        'idunidadmedida'  => $request->input('idunidadmedida')!=null ? $request->input('idunidadmedida') : 1
                        //'idproductopresentacion'  => $request->input('idproducto')!=null ? $request->input('idproducto') : 0
                    ]);
                }
                    

            
                    // ESTADO UNIDAD DE MEDIDA
                    if(configuracion($idtienda,'sistema_estadounidadmedida')['valor']==1){
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
           
                            
                            productosaldo_actualizar(
                                $idtienda,
                                $idproducto,
                                'SALDO AGRUPADO',
                                0,
                                $request->input('por')!=null ? $request->input('por') : 1,
                                $request->input('idunidadmedida')!=null ? $request->input('idunidadmedida') : 1,
                                $request->input('idproducto'),
                            );
                        }
                      
                        // desagrupar unidad de medida
                        if($request->input('estadoagruparunidadmedida')!='on' && $request->input('estadodesagruparunidadmedida')=='on') {
                          
                            productosaldo_actualizar(
                                $idtienda,
                                $idproducto,
                                'SALDO DESAGRUPADO',
                                0,
                                1,
                                $request->input('idunidadmedida')!=null ? $request->input('idunidadmedida') : 1,
                                $idproducto,
                            );
         
                            DB::table('s_producto')->whereId($idproducto)->update([
                                'por'  => 1,
                                'idproductopresentacion'  => 0
                            ]);
                          
                            $presentaciones = producto_presentaciones($idtienda,$idproducto);
                            if(count($presentaciones)>1){
                                DB::table('s_producto')->where('s_idproducto',$idproducto)->update([
                                    's_idproducto'  => 0,
                                ]);
                            }else{
                                DB::table('s_producto')->where('s_idproducto',$idproducto)->update([
                                    's_idproducto'  => 0,
                                    'idproductopresentacion'  => 0,
                                ]);
                            }
                                
                        } 
                    }
                    // ESTADO DESCUENTO
                    if(configuracion($idtienda,'sistema_estadodescuento')['valor']==1){

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
                                'idtienda'  => $idtienda,
                                'idestado'  => 1,
                            ]);

                            $productos = explode('/&&&/', $itemmater[1]);
                            $total = 0;
                            for($ii = 1;$ii <  count($productos);$ii++){
                                $item = explode('/,,,/', $productos[$ii]);
                                DB::table('s_productodescuentodetalle')->insert([
                                    's_idproducto'  => $idproducto,
                                    's_idproductoasociado'  => $item[0],
                                    's_idproductodescuento'  => $idproductodescuento,
                                    'idtienda'  => $idtienda,
                                    'idestado'  => 1,
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
                        's_idproducto' => $idproducto,
                        'idtienda'  => $idtienda,
                        'idestado'  => 1,
                    ]);
                  }
                }
            }
                
          
                      
            // cargar productos JSON
            //load_json_productos($idtienda);
          
            /*if($configuracion!=''){
                if(configuracion($idtienda,'sistema_estadodescuento')['valor']==1){
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
                'idtienda' => $idtienda,
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
