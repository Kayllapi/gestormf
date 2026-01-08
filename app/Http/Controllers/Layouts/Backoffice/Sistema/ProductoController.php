<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class ProductoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/producto/tabla',[
                'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->view == 'registrar') {
            return view(sistema_view().'/producto/create',[
                'tienda' => $tienda,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            /* =================================================  VALIDAR CAMPOS */
            $rules = [];
            $messages = [];
            $db_codigo = [];
            $i = 1;
            foreach(json_decode($request->seleccionar_codigo) as $value){
                $rules = array_merge($rules,[
                    'productocodigo_codigo'.$value->num  => Rule::unique('s_producto', 'codigo')->where(function ($query) use ($idtienda) {
                        return $query->where('s_producto.idestado',1)
                            ->where('s_producto.idtienda',$idtienda);
                    }), 
                ]);
                $messages = array_merge($messages,[
                    'productocodigo_codigo'.$value->num.'.unique' => 'El "Código de Producto" ya existe, Ingrese Otro por favor.',
                ]);
                $db_codigo[] = [
                    'orden'   => $i,
                    'codigo'  => $request->input('productocodigo_codigo'.$value->num)!=null ? $request->input('productocodigo_codigo'.$value->num) : '',
                ];
                $i++;
            }
          
            $rules = array_merge($rules,[
                'productonombre'  => 'required',
                'productoidcategoria'     => 'required',
            ]);
          
            $db_presentacion = [];
            $i = 1;
            foreach(json_decode($request->seleccionar_presentacion) as $value){
              
                $rules = array_merge($rules,[
                    'productopresentacion_idunidadmedida'.$value->num         => 'required',
                    'productopresentacion_por'.$value->num                    => 'required|numeric|integer|gte:1',
                ]);
              
                if(configuracion($idtienda,'sistema_moneda_usar')['valor']==2){
                    if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                        $rules = array_merge($rules,[
                            'productopresentacion_preciominimo_dolares'.$value->num   => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                            'productopresentacion_preciopublico_dolares'.$value->num  => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                        ]);
                    }else{
                        $rules = array_merge($rules,[
                            'productopresentacion_preciopublico_dolares'.$value->num  => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                        ]);
                    }
                }
                elseif(configuracion($idtienda,'sistema_moneda_usar')['valor']==3){
                    if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                        $rules = array_merge($rules,[
                            'productopresentacion_preciominimo'.$value->num           => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                            'productopresentacion_preciopublico'.$value->num          => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                        ]);
                    }else{
                        $rules = array_merge($rules,[
                            'productopresentacion_preciopublico'.$value->num          => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                        ]);
                    }
                    if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                        $rules = array_merge($rules,[
                            'productopresentacion_preciominimo_dolares'.$value->num   => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                            'productopresentacion_preciopublico_dolares'.$value->num  => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                        ]);
                    }else{
                        $rules = array_merge($rules,[
                            'productopresentacion_preciopublico_dolares'.$value->num  => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                        ]);
                    }
                }else{
                    if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                        $rules = array_merge($rules,[
                            'productopresentacion_preciominimo'.$value->num           => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                            'productopresentacion_preciopublico'.$value->num          => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                        ]);
                    }else{
                        $rules = array_merge($rules,[
                            'productopresentacion_preciopublico'.$value->num          => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                        ]);
                    }
                }
              
                $messages = array_merge($messages,[
                    'productopresentacion_idunidadmedida'.$value->num.'.required'        => 'La "Unidad de Medida" es Obligatorio.',
                    'productopresentacion_preciominimo'.$value->num.'.required'          => 'El "Precio Mínimo S/." es Obligatorio.',
                    'productopresentacion_preciominimo'.$value->num.'.numeric'           => 'El "Precio Mínimo S/.", debe ser númerico.',
                    'productopresentacion_preciominimo'.$value->num.'.regex'             => 'El "Precio Mínimo S/.", debe ser máximo de 2 decimales.',
                    'productopresentacion_preciominimo'.$value->num.'.gte'               => 'El "Precio Mínimo S/.", debe ser mayor ó igual 0.',
                    'productopresentacion_preciopublico'.$value->num.'.required'         => 'El "Precio S/." es Obligatorio.',
                    'productopresentacion_preciopublico'.$value->num.'.numeric'          => 'El "Precio S/.", debe ser númerico.',
                    'productopresentacion_preciopublico'.$value->num.'.regex'            => 'El "Precio S/.", debe ser máximo de 2 decimales.',
                    'productopresentacion_preciopublico'.$value->num.'.gte'              => 'El "Precio S/.", debe ser mayor ó igual 0.',
                    'productopresentacion_preciominimo_dolares'.$value->num.'.required'  => 'El "Precio Mínimo $" es Obligatorio.',
                    'productopresentacion_preciominimo_dolares'.$value->num.'.numeric'   => 'El "Precio Mínimo $", debe ser númerico.',
                    'productopresentacion_preciominimo_dolares'.$value->num.'.regex'     => 'El "Precio Mínimo $", debe ser máximo de 2 decimales.',
                    'productopresentacion_preciominimo_dolares'.$value->num.'.gte'       => 'El "Precio Mínimo $", debe ser mayor ó igual 0.',
                    'productopresentacion_preciopublico_dolares'.$value->num.'.required' => 'El "Precio $" es Obligatorio.',
                    'productopresentacion_preciopublico_dolares'.$value->num.'.numeric'  => 'El "Precio $", debe ser númerico.',
                    'productopresentacion_preciopublico_dolares'.$value->num.'.regex'    => 'El "Precio $", debe ser máximo de 2 decimales.',
                    'productopresentacion_preciopublico_dolares'.$value->num.'.gte'      => 'El "Precio $", debe ser mayor ó igual 0.',
                    'productopresentacion_por'.$value->num.'.required'                   => 'La "Cantidad de Unidades" es Obligatorio.',
                    'productopresentacion_por'.$value->num.'.numeric'                    => 'El "Cantidad de Unidades", debe ser númerico.',
                    'productopresentacion_por'.$value->num.'.integer'                    => 'El "Cantidad de Unidades", debe ser entero.',
                    'productopresentacion_por'.$value->num.'.gte'                        => 'La "Cantidad de Unidades" debe ser mayor a 1.',
                ]);

                $db_presentacion[] = [
                    'orden'                   => $i,
                    'preciominimo'            => $request->input('productopresentacion_preciominimo'.$value->num)!=null ? $request->input('productopresentacion_preciominimo'.$value->num) : 0,
                    'preciopublico'           => $request->input('productopresentacion_preciopublico'.$value->num)!=null ? $request->input('productopresentacion_preciopublico'.$value->num) : 0,
                    'preciominimo_dolares'    => $request->input('productopresentacion_preciominimo_dolares'.$value->num)!=null ? $request->input('productopresentacion_preciominimo_dolares'.$value->num) : 0,
                    'preciopublico_dolares'   => $request->input('productopresentacion_preciopublico_dolares'.$value->num)!=null ? $request->input('productopresentacion_preciopublico_dolares'.$value->num) : 0,
                    'por'                     => $request->input('productopresentacion_por'.$value->num)!=null ? $request->input('productopresentacion_por'.$value->num) : 1,
                    'idunidadmedida'          => $request->input('productopresentacion_idunidadmedida'.$value->num)!=null ? $request->input('productopresentacion_idunidadmedida'.$value->num) : 1,
                    //'stock'                   => 0,
                ];
                $i++;
            }
          
            $messages = array_merge($messages,[
                'productonombre.required'           => 'El "Nombre" es Obligatorio.',
                'productoidcategoria.required'      => 'La "Categoría" es Obligatorio.',
            ]);
      
            $this->validate($request,$rules,$messages);
          
            /* =================================================  CATEGORIA */
            $idcategoria = $request->productoidcategoria!='' ? $request->productoidcategoria : 0;
            $db_idcategoria = '';
            if($idcategoria!=0){
                $s_categoria1 = DB::table('s_categoria')
                    ->where('s_categoria.idtienda',$idtienda)
                    ->where('s_categoria.id',$idcategoria)
                    ->first();
                if($s_categoria1!=''){
                    $db_idcategoria = $s_categoria1->nombre;
                    $s_categoria2 = DB::table('s_categoria')
                        ->where('s_categoria.idtienda',$idtienda)
                        ->where('s_categoria.id',$s_categoria1->s_idcategoria)
                        ->first();
                    if($s_categoria2!=''){
                        $db_idcategoria = $s_categoria2->nombre.'/'.$db_idcategoria;
                        $s_categoria3 = DB::table('s_categoria')
                            ->where('s_categoria.idtienda',$idtienda)
                            ->where('s_categoria.id',$s_categoria2->s_idcategoria)
                            ->first();
                        if($s_categoria3!=''){
                              $db_idcategoria = $s_categoria3->nombre.'/'.$db_idcategoria;
                        }
                    }
                }
            }
          
            /* =================================================  MARCA */
            $idmarca = $request->productoidmarca!='' ? $request->productoidmarca : 0;
            $db_idmarca = '';
            if($idmarca!=0){
                $s_marca = DB::table('s_marca')->whereId($idmarca)->first();
                $db_idmarca = $s_marca->nombre;
            }
          
            /* =================================================  UNIDAD DE MEDIDA */
            $por = $request->productopresentacion_por0!='' ? $request->productopresentacion_por0 : 1;
            $idunidadmedida = $request->productopresentacion_idunidadmedida0!='' ? $request->productopresentacion_idunidadmedida0 : 1;
            $db_idunidadmedida = '';
            if($idunidadmedida!=0){
                $s_unidadmedida = DB::table('s_unidadmedida')->whereId($idunidadmedida)->first();
                $db_idunidadmedida = $s_unidadmedida->nombre.' x '.$por;
            }
          
            /* =================================================  INSERT */
            $idproducto = DB::table('s_producto')->insertGetId([
                'fecharegistro'           => Carbon::now(),
                'orden'                   => 0,
                'codigo'                  => $request->productocodigo_codigo0!=null?$request->productocodigo_codigo0:'',
                'nombre'                  => $request->productonombre,
                'descripcion'             => '',
                'imagen'                  => '',
                'preciominimo'            => $request->productopresentacion_preciominimo0!=null?$request->productopresentacion_preciominimo0:0,
                'preciopublico'           => $request->productopresentacion_preciopublico0!=null?$request->productopresentacion_preciopublico0:0,
                'preciominimo_dolares'    => $request->productopresentacion_preciominimo_dolares0!=null?$request->productopresentacion_preciominimo_dolares0:0,
                'preciopublico_dolares'   => $request->productopresentacion_preciopublico_dolares0!=null?$request->productopresentacion_preciopublico_dolares0:0,
                'por'                     => $por,
                'stockminimo'             => 0,
                'alertavencimiento'       => 0,
                'db_idcategoria'          => $db_idcategoria,
                'db_idmarca'              => $db_idmarca,
                'db_idunidadmedida'       => $db_idunidadmedida,
                'db_codigo'               => json_encode($db_codigo),
                'db_imagen'               => '[]',
                'db_presentacion'         => json_encode($db_presentacion),
                'db_stock'                => '[]',
                's_idcategoria'           => $idcategoria,
                's_idmarca'               => $idmarca,
                's_idunidadmedida'        => $idunidadmedida,
                's_idestadodetalle'       => 2,
                's_idestadotiendavirtual' => 2,
                's_idestadosistema'       => 1,
                'idsucursal'              => Auth::user()->idsucursal,
                'idtienda'                => $idtienda,
                'idestado'                => 1,
            ]);
          
            /*foreach(json_decode($request->seleccionar_presentacion) as $value){
                DB::table('s_productocodigo')->insert([
                    'nivel'                   => 1,
                    'codigo'                  => $request->input('productocodigo_codigo'.$value->num)!=null ? $request->input('productocodigo_codigo'.$value->num) : '',
                    's_idproducto'            => $idproducto,
                    'idsucursal'              => Auth::user()->idsucursal,
                    'idtienda'                => $idtienda,
                    'idestado'                => 1,
                ]);
            }
          
            foreach(json_decode($request->seleccionar_presentacion) as $value){
                DB::table('s_productopresentacion')->insert([
                    'nivel'                   => 1,
                    'preciominimo'            => $request->input('productopresentacion_preciominimo'.$value->num)!=null ? $request->input('productopresentacion_preciominimo'.$value->num) : 0,
                    'preciopublico'           => $request->input('productopresentacion_preciopublico'.$value->num)!=null ? $request->input('productopresentacion_preciopublico'.$value->num) : 0,
                    'preciominimo_dolares'    => $request->input('productopresentacion_preciominimo_dolares'.$value->num)!=null ? $request->input('productopresentacion_preciominimo_dolares'.$value->num) : 0,
                    'preciopublico_dolares'   => $request->input('productopresentacion_preciopublico_dolares'.$value->num)!=null ? $request->input('productopresentacion_preciopublico_dolares'.$value->num) : 0,
                    'por'                     => $request->input('productopresentacion_por'.$value->num)!=null ? $request->input('productopresentacion_por'.$value->num) : 1,
                    's_idunidadmedida'        => $request->input('productopresentacion_idunidadmedida'.$value->num)!=null ? $request->input('productopresentacion_idunidadmedida'.$value->num) : 1,
                    's_idproducto'            => $idproducto,
                    'idsucursal'              => Auth::user()->idsucursal,
                    'idtienda'                => $idtienda,
                    'idestado'                => 1,
                ]);
            }*/
            /* ----- FIN INSERTAR DATOS ----- */
          
            json_producto($idtienda,Auth::user()->idsucursal);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        //
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $s_producto = DB::table('s_producto')
            ->where('s_producto.id',$id)
            ->first();
      
        if($request->input('view') == 'editar') {
            return view(sistema_view().'/producto/edit',[
                'tienda' => $tienda,
                'producto' => $s_producto,
            ]);
        }
        elseif($request->input('view') == 'editarpresentacion') {
            return view(sistema_view().'/producto/editarpresentacion',[
                'tienda' => $tienda,
                'producto' => $s_producto,
            ]);
        }
        elseif($request->input('view') == 'imagen') {
            return view(sistema_view().'/producto/imagen',[
                'tienda' => $tienda,
                'producto' => $s_producto
            ]);
        }
        elseif($request->input('view') == 'imagengaleria') {
            //$productogalerias = DB::table('s_productogaleria')->where('s_idproducto',$id)->get();
            return view(sistema_view().'/producto/imagengaleria',[
                'tienda' => $tienda,
                'producto' => $s_producto,
                //'productogalerias' => $productogalerias
            ]);
        }
        elseif($request->input('view') == 'codigobarra') {
            return view(sistema_view().'/producto/codigobarra',[
                'tienda' => $tienda,
                'producto' => $s_producto
            ]);
        }
        elseif($request->input('view') == 'codigobarrapdf') {
            $pdf = PDF::loadView(sistema_view().'/producto/codigobarrapdf',[
                'tienda' => $tienda,
                'producto' => $s_producto,
            ]);
            return $pdf->stream('CÓDIGO_BARRA_'.$s_producto->codigo.'.pdf');
        }
        elseif($request->input('view') == 'eliminar') {
            return view(sistema_view().'/producto/delete',[
                'tienda' => $tienda,
                'producto' => $s_producto,
            ]);
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'editar') {
          
            /* =================================================  VALIDAR CAMPOS */
            $rules = [];
            $messages = [];
            $db_codigo = [];
            $i = 1;
            foreach(json_decode($request->seleccionar_codigo) as $value){
                $rules = array_merge($rules,[
                    'productocodigo_codigo'.$value->num  => Rule::unique('s_producto', 'codigo')->where(function ($query) use ($idtienda, $id) {
                        return $query->where('s_producto.idestado',1)
                            ->where('s_producto.id','<>',$id)
                            ->where('s_producto.idtienda',$idtienda);
                    }), 
                ]);
              
                $messages = array_merge($messages,[
                    'productocodigo_codigo'.$value->num.'.unique' => 'El "Código de Producto" ya existe, Ingrese Otro por favor.',
                ]);
                $db_codigo[] = [
                    'orden'   => $i,
                    'codigo'  => $request->input('productocodigo_codigo'.$value->num)!=null ? $request->input('productocodigo_codigo'.$value->num) : '',
                ];
                $i++;
            }
          
            $rules = array_merge($rules,[
                'productonombre'  => 'required',
                'productoidcategoria'     => 'required',
            ]);
          
            $messages = array_merge($messages,[
                'productonombre.required'           => 'El "Nombre" es Obligatorio.',
                'productoidcategoria.required'      => 'La "Categoría" es Obligatorio.',
            ]);
      
            $this->validate($request,$rules,$messages);
          
            /* =================================================  CATEGORIA */
            $idcategoria = $request->productoidcategoria!='' ? $request->productoidcategoria : 0;
            $db_idcategoria = '';
            if($idcategoria!=0){
                $s_categoria1 = DB::table('s_categoria')
                    ->where('s_categoria.idtienda',$idtienda)
                    ->where('s_categoria.id',$idcategoria)
                    ->first();
                if($s_categoria1!=''){
                    $db_idcategoria = $s_categoria1->nombre;
                    $s_categoria2 = DB::table('s_categoria')
                        ->where('s_categoria.idtienda',$idtienda)
                        ->where('s_categoria.id',$s_categoria1->s_idcategoria)
                        ->first();
                    if($s_categoria2!=''){
                        $db_idcategoria = $s_categoria2->nombre.'/'.$db_idcategoria;
                        $s_categoria3 = DB::table('s_categoria')
                            ->where('s_categoria.idtienda',$idtienda)
                            ->where('s_categoria.id',$s_categoria2->s_idcategoria)
                            ->first();
                        if($s_categoria3!=''){
                              $db_idcategoria = $s_categoria3->nombre.'/'.$db_idcategoria;
                        }
                    }
                }
            }
          
            /* =================================================  MARCA */
            $idmarca = $request->productoidmarca!='' ? $request->productoidmarca : 0;
            $db_idmarca = '';
            if($idmarca!=0){
                $s_marca = DB::table('s_marca')->whereId($idmarca)->first();
                $db_idmarca = $s_marca->nombre;
            }
          
            /* =================================================  UPDATE */
            DB::table('s_producto')->whereId($id)->update([
                'codigo'                  => $request->productocodigo_codigo0!=null?$request->productocodigo_codigo0:'',
                'nombre'                  => $request->productonombre,
                'preciominimo'            => $request->productopresentacion_preciominimo0!=null?$request->productopresentacion_preciominimo0:0,
                'preciopublico'           => $request->productopresentacion_preciopublico0!=null?$request->productopresentacion_preciopublico0:0,
                'preciominimo_dolares'    => $request->productopresentacion_preciominimo_dolares0!=null?$request->productopresentacion_preciominimo_dolares0:0,
                'preciopublico_dolares'   => $request->productopresentacion_preciopublico_dolares0!=null?$request->productopresentacion_preciopublico_dolares0:0,
                'por'                     => $request->productopresentacion_por0!=null?$request->productopresentacion_por0:1,
                'stockminimo'             => $request->stockminimo,
                'alertavencimiento'       => $request->alertavencimiento,
                'fechavencimiento'        => $request->fechavencimiento,
                'db_idcategoria'          => $db_idcategoria,
                'db_idmarca'              => $db_idmarca,
                'db_codigo'               => json_encode($db_codigo),
                's_idcategoria'           => $idcategoria,
                's_idmarca'               => $idmarca,
                's_idestadodetalle'       => $request->idestadodetalle,
                's_idestadotiendavirtual' => $request->idestadotiendavirtual!=null ? $request->idestadotiendavirtual : 2,
                's_idestadosistema'       => $request->idestadosistema,
            ]);
          
            json_producto($idtienda,Auth::user()->idsucursal); 
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'editartienda') {
            /* ----- DETALLE ----- */
            DB::table('s_productodetalle')->where('s_idproducto',$id)->delete();
            $listatitulo = explode('/-/',$request->input('detalle'));
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
                        's_idproducto' => $id,
                        'idtienda'  => $idtienda,
                        'idestado'  => 1,
                    ]);
                }
            }
            /* ----- FIN DETALLE ----- */
        }
        elseif($request->input('view') == 'editarpresentacion') {
            /* =================================================  VALIDAR CAMPOS */
            $rules = [];
            $messages = [];
          
            $db_presentacion = [];
            $i = 1;
            foreach(json_decode($request->seleccionar_presentacion) as $value){
              
                $rules = array_merge($rules,[
                    'productopresentacion_idunidadmedida'.$value->num         => 'required',
                    'productopresentacion_por'.$value->num                    => 'required|numeric|integer|gte:1',
                ]);
              
                if(configuracion($idtienda,'sistema_moneda_usar')['valor']==2){
                    if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                        $rules = array_merge($rules,[
                            'productopresentacion_preciominimo_dolares'.$value->num   => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                            'productopresentacion_preciopublico_dolares'.$value->num  => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                        ]);
                    }else{
                        $rules = array_merge($rules,[
                            'productopresentacion_preciopublico_dolares'.$value->num  => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                        ]);
                    }
                }
                elseif(configuracion($idtienda,'sistema_moneda_usar')['valor']==3){
                    if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                        $rules = array_merge($rules,[
                            'productopresentacion_preciominimo'.$value->num           => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                            'productopresentacion_preciopublico'.$value->num          => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                        ]);
                    }else{
                        $rules = array_merge($rules,[
                            'productopresentacion_preciopublico'.$value->num          => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                        ]);
                    }
                    if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                        $rules = array_merge($rules,[
                            'productopresentacion_preciominimo_dolares'.$value->num   => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                            'productopresentacion_preciopublico_dolares'.$value->num  => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                        ]);
                    }else{
                        $rules = array_merge($rules,[
                            'productopresentacion_preciopublico_dolares'.$value->num  => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                        ]);
                    }
                }else{
                    if(configuracion($idtienda,'sistema_estadopreciominimo')['valor']==1){
                        $rules = array_merge($rules,[
                            'productopresentacion_preciominimo'.$value->num           => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                            'productopresentacion_preciopublico'.$value->num          => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                        ]);
                    }else{
                        $rules = array_merge($rules,[
                            'productopresentacion_preciopublico'.$value->num          => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                        ]);
                    }
                }
              
                $messages = array_merge($messages,[
                    'productopresentacion_idunidadmedida'.$value->num.'.required'        => 'La "Unidad de Medida" es Obligatorio.',
                    'productopresentacion_preciominimo'.$value->num.'.required'          => 'El "Precio Mínimo S/." es Obligatorio.',
                    'productopresentacion_preciominimo'.$value->num.'.numeric'           => 'El "Precio Mínimo S/.", debe ser númerico.',
                    'productopresentacion_preciominimo'.$value->num.'.regex'             => 'El "Precio Mínimo S/.", debe ser máximo de 2 decimales.',
                    'productopresentacion_preciominimo'.$value->num.'.gte'               => 'El "Precio Mínimo S/.", debe ser mayor ó igual 0.',
                    'productopresentacion_preciopublico'.$value->num.'.required'         => 'El "Precio S/." es Obligatorio.',
                    'productopresentacion_preciopublico'.$value->num.'.numeric'          => 'El "Precio S/.", debe ser númerico.',
                    'productopresentacion_preciopublico'.$value->num.'.regex'            => 'El "Precio S/.", debe ser máximo de 2 decimales.',
                    'productopresentacion_preciopublico'.$value->num.'.gte'              => 'El "Precio S/.", debe ser mayor ó igual 0.',
                    'productopresentacion_preciominimo_dolares'.$value->num.'.required'  => 'El "Precio Mínimo $" es Obligatorio.',
                    'productopresentacion_preciominimo_dolares'.$value->num.'.numeric'   => 'El "Precio Mínimo $", debe ser númerico.',
                    'productopresentacion_preciominimo_dolares'.$value->num.'.regex'     => 'El "Precio Mínimo $", debe ser máximo de 2 decimales.',
                    'productopresentacion_preciominimo_dolares'.$value->num.'.gte'       => 'El "Precio Mínimo $", debe ser mayor ó igual 0.',
                    'productopresentacion_preciopublico_dolares'.$value->num.'.required' => 'El "Precio $" es Obligatorio.',
                    'productopresentacion_preciopublico_dolares'.$value->num.'.numeric'  => 'El "Precio $", debe ser númerico.',
                    'productopresentacion_preciopublico_dolares'.$value->num.'.regex'    => 'El "Precio $", debe ser máximo de 2 decimales.',
                    'productopresentacion_preciopublico_dolares'.$value->num.'.gte'      => 'El "Precio $", debe ser mayor ó igual 0.',
                    'productopresentacion_por'.$value->num.'.required'                   => 'La "Cantidad de Unidades" es Obligatorio.',
                    'productopresentacion_por'.$value->num.'.numeric'                    => 'El "Cantidad de Unidades", debe ser númerico.',
                    'productopresentacion_por'.$value->num.'.integer'                    => 'El "Cantidad de Unidades", debe ser entero.',
                    'productopresentacion_por'.$value->num.'.gte'                        => 'La "Cantidad de Unidades" debe ser mayor a 1.',
                ]);
              
                $idunidadmedida = $request->input('productopresentacion_idunidadmedida'.$value->num)!=null ? $request->input('productopresentacion_idunidadmedida'.$value->num) : 1;
                $por = $request->input('productopresentacion_por'.$value->num)!=null ? $request->input('productopresentacion_por'.$value->num) : 1;
                $s_unidadmedida = DB::table('s_unidadmedida')->whereId($idunidadmedida)->first();

                $db_presentacion[] = [
                    'orden'                   => $i,
                    'preciominimo'            => $request->input('productopresentacion_preciominimo'.$value->num)!=null ? $request->input('productopresentacion_preciominimo'.$value->num) : 0,
                    'preciopublico'           => $request->input('productopresentacion_preciopublico'.$value->num)!=null ? $request->input('productopresentacion_preciopublico'.$value->num) : 0,
                    'preciominimo_dolares'    => $request->input('productopresentacion_preciominimo_dolares'.$value->num)!=null ? $request->input('productopresentacion_preciominimo_dolares'.$value->num) : 0,
                    'preciopublico_dolares'   => $request->input('productopresentacion_preciopublico_dolares'.$value->num)!=null ? $request->input('productopresentacion_preciopublico_dolares'.$value->num) : 0,
                    'unidadmedidanombre'      => $s_unidadmedida->nombre.' x '.$por,
                    'por'                     => $por,
                    'idunidadmedida'          => $idunidadmedida,
                ];
                $i++;
            }
          
            $this->validate($request,$rules,$messages);
          
            /* =================================================  UNIDAD DE MEDIDA */
            $por = $request->productopresentacion_por0!='' ? $request->productopresentacion_por0 : 1;
            $idunidadmedida = $request->productopresentacion_idunidadmedida0!='' ? $request->productopresentacion_idunidadmedida0 : 1;
            $db_idunidadmedida = '';
            if($idunidadmedida!=0){
                $s_unidadmedida = DB::table('s_unidadmedida')->whereId($idunidadmedida)->first();
                $db_idunidadmedida = $s_unidadmedida->nombre.' x '.$por;
            }
          
            /* =================================================  UPDATE */
            DB::table('s_producto')->whereId($id)->update([
                'preciominimo'            => $request->productopresentacion_preciominimo0!=null?$request->productopresentacion_preciominimo0:0,
                'preciopublico'           => $request->productopresentacion_preciopublico0!=null?$request->productopresentacion_preciopublico0:0,
                'preciominimo_dolares'    => $request->productopresentacion_preciominimo_dolares0!=null?$request->productopresentacion_preciominimo_dolares0:0,
                'preciopublico_dolares'   => $request->productopresentacion_preciopublico_dolares0!=null?$request->productopresentacion_preciopublico_dolares0:0,
                'por'                     => $por,
                'db_idunidadmedida'       => $db_idunidadmedida,
                'db_presentacion'         => json_encode($db_presentacion),
                's_idunidadmedida'        => $idunidadmedida,
            ]);
          
            json_producto($idtienda,Auth::user()->idsucursal); 

            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'imagen'){

            $imagen = uploadfile('','',$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/producto/');

            //$countproductogaleria = DB::table('s_productogaleria')->where('s_idproducto',$id)->count();
          
            $db_imagen = [];
            $s_producto = DB::table('s_producto')->whereId($id)->first();
            if($s_producto->db_imagen!=''){
                $i=1;
                foreach(json_decode($s_producto->db_imagen) as $value){
                    $db_imagen[] = [
                        'orden'   => $i,
                        'imagen'  => $value->imagen,
                    ];
                    $i++;
                }
                $db_imagen[] = [
                    'orden'   => $i,
                    'imagen'  => $imagen,
                ];
          
                DB::table('s_producto')->whereId($id)->update([
                    'imagen'      => $imagen,
                    'db_imagen'   => json_encode($db_imagen),
                ]);
            }else{
                $db_imagen[] = [
                    'orden'   => 1,
                    'imagen'  => $imagen,
                ];
          
                DB::table('s_producto')->whereId($id)->update([
                    'imagen'      => $imagen,
                    'db_imagen'   => json_encode($db_imagen),
                ]);
            }

            /*DB::table('s_productogaleria')->insert([
                'fecharegistro' => Carbon::now(),
                'orden' => $countproductogaleria+1,
                'imagen' => $imagen,
                's_idproducto' => $id,
                'idtienda' => $idtienda,
                's_idestado' => 1
            ]);*/
           
            json_producto($idtienda,Auth::user()->idsucursal); 
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha registrado correctamente.'
            ]);
          
        }
    }


    public function destroy(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {

            /*DB::table('s_producto')->whereId($id)->update([
                'fechaeliminado'  => Carbon::now(),
                'idestado'      => 2,
            ]);*/
          
            $s_producto = DB::table('s_producto')->whereId($id)->first();
            foreach(json_decode($s_producto->db_imagen) as $value){
                uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/producto/');
            }
          
            DB::table('s_producto')
                ->where('s_producto.idtienda',$idtienda)
                ->whereId($id)
                ->delete();
  
            json_producto($idtienda,Auth::user()->idsucursal); 
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        } 
        elseif($request->input('view') == 'eliminarimagen') {
            
            $s_producto = DB::table('s_productogaleria')->whereId($id)->first();
            uploadfile_eliminar($s_producto->imagen,'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            DB::table('s_productogaleria')
                ->where('idtienda',$idtienda)
                ->where('id',$id)
                ->delete();
  
            json_producto($idtienda,Auth::user()->idsucursal); 
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
