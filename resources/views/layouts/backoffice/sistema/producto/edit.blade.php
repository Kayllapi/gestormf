@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Producto</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/producto') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/producto/{{ $producto->id }}',
        method: 'PUT',
        data:{
            view: 'editar',
            contenido:seleccionartabla(),
            @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
            descuento_seleccionarproductomaster: descuento_seleccionarproductomaster()
            @endif
        }
    },
    function(resultado){
            location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/producto') }}';                                                           
    },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="tabs-container" id="tab-producto">
              <ul class="tabs-menu">
                  <li class="current"><a href="#tab-producto-0" id="tab-general">General</a></li>
                  <li><a href="#tab-producto-1" id="tab-detalle">Detalle</a></li>
                  @if(configuracion($tienda->id,'sistema_estadounidadmedida')['valor']==1)
                  <li><a href="#tab-producto-4" id="tab-descuento">Presentación</a></li>
                  @endif
                  @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
                  <li><a href="#tab-producto-2" id="tab-descuento">Descuentos</a></li>
                  @endif
                  <li><a href="#tab-producto-3" id="tab-estado">Estado</a></li>
              </ul>
              <div class="tab">
                  <div id="tab-producto-0" class="tab-content" style="display: block;">
                      <div class="row">
                         <div class="col-md-6">
                            @if(configuracion($tienda->id,'sistema_tipocodigoproducto')['valor']==2)
                                  <label>Código de Producto</label>
                                  <table class="table">
                                          <tr>
                                              <td style="background-color: #eae7e7;padding: 10px;text-align:center; width: 40px;font-weight: bold;">01</td>
                                              <td style="background-color: #eae7e7;"><input type="text" id="codigo1" value="{{$producto->codigo}}"></td>
                                              <td style="background-color: #eae7e7;padding: 10px;text-align:center; width: 40px;font-weight: bold;">06</td>
                                              <td style="background-color: #eae7e7;"><input type="text" id="codigo6" value="{{$producto->codigo6}}"></td>
                                          </tr>
                                          <tr>
                                              <td style="background-color: #eae7e7;padding: 10px;text-align:center;font-weight: bold;">02</td>
                                              <td style="background-color: #eae7e7;"><input type="text" id="codigo2"value="{{$producto->codigo2}}" ></td>
                                              <td style="background-color: #eae7e7;padding: 10px;text-align:center;font-weight: bold;">07</td>
                                              <td style="background-color: #eae7e7;"><input type="text" id="codigo7"value="{{$producto->codigo7}}" ></td>
                                          </tr>
                                          <tr>
                                              <td style="background-color: #eae7e7;padding: 10px;text-align:center;font-weight: bold;">03</td>
                                              <td style="background-color: #eae7e7;"><input type="text" id="codigo3" value="{{$producto->codigo3}}"></td>
                                              <td style="background-color: #eae7e7;padding: 10px;text-align:center;font-weight: bold;">08</td>
                                              <td style="background-color: #eae7e7;"><input type="text" id="codigo8"value="{{$producto->codigo8}}" ></td>
                                          </tr>
                                          <tr>
                                              <td style="background-color: #eae7e7;padding: 10px;text-align:center;font-weight: bold;">04</td>
                                              <td style="background-color: #eae7e7;"><input type="text" id="codigo4" value="{{$producto->codigo4}}"></td>
                                              <td style="background-color: #eae7e7;padding: 10px;text-align:center;font-weight: bold;">09</td>
                                              <td style="background-color: #eae7e7;"><input type="text" id="codigo9"value="{{$producto->codigo9}}" ></td>
                                          </tr>
                                          <tr>
                                              <td style="background-color: #eae7e7;padding: 10px;text-align:center;font-weight: bold;">05</td>
                                              <td style="background-color: #eae7e7;"><input type="text" id="codigo5" value="{{$producto->codigo5}}"></td>
                                              <td style="background-color: #eae7e7;padding: 10px;text-align:center;font-weight: bold;">10</td>
                                              <td style="background-color: #eae7e7;"><input type="text" id="codigo10"value="{{$producto->codigo10}}" ></td>
                                          </tr>
                                  </table>
                            @else
                            <label>Código de Producto</label>
                            <input type="text" id="codigo" value="{{$producto->codigo}}" <?php echo configuracion($tienda->id,'sistema_estadodescuento')['valor']==1?'onkeyup="descuento_actualizar()"':'' ?>/>
                            @endif
                            <label>Nombre Producto *</label>
                            <input type="text" id="nombre" value="{{$producto->nombre}}" <?php echo configuracion($tienda->id,'sistema_estadodescuento')['valor']==1?'onkeyup="descuento_actualizar()"':'' ?> onkeyup="texto_mayucula(this)"/>
                            <label>Precio Público *</label>
                            <input type="number" value="{{$producto->precioalpublico}}" id="precioalpublico" step="0.01" min="0" <?php echo configuracion($tienda->id,'sistema_estadodescuento')['valor']==1?'onclick="descuento_actualizartotal()" onkeyup="descuento_actualizar()"':'' ?>/>
                            <label>Categoría *</label>
                            <select id="idcategoria">
                                <option></option>
                                @foreach($categorias as $value)
                                <option value="{{$value->id}}">{{ $value->nombre }}</option>
                                    <?php
                                    $subcategorias = DB::table('s_categoria')
                                        ->where('s_categoria.s_idcategoria',$value->id)
                                        ->orderBy('s_categoria.nombre','asc')
                                        ->get();
                                    ?>
                                    @foreach($subcategorias as $subvalue)
                                    <option value="{{$subvalue->id}}">{{ $value->nombre }} / {{ $subvalue->nombre }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                            <label>Marca</label>
                            <select id="idmarca">
                                <option></option>
                                @foreach($marcas as $value)
                                <option  value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                         <div class="col-md-6">
                            <label>Stock Mínimo en Cantidades (0=Ilimitado) *</label>
                            <input type="number" value="{{$producto->stockminimo}}" id="stockminimo" step="0.01" min="0"/>
                            <label>Fecha de Vencimiento</label>
                            <input type="date" id="fechavencimiento" value="{{$producto->fechavencimiento}}"/>
                            <label>Alerta de Vencimiento en Días *</label>
                            <input type="number" value="{{$producto->alertavencimiento}}" id="alertavencimiento" min="0"/>
                            <label>Estado de Detalle *</label>
                            <select id="idestadodetalle">
                                <option></option>
                                <option value="1">Activado</option>
                                <option value="2">Desactivado</option>
                            </select>
                            <label>Estado Sistema *</label>
                            <select id="idestado">
                                <option></option>
                                <option value="1">Activado</option>
                                <option value="2">Desactivado</option>
                            </select>
                            <label>Estado Tienda Virtual *</label>
                            <select id="idestadotiendavirtual">
                                <option></option>
                                <option value="1">Activado</option>
                                <option value="2">Desactivado</option>
                            </select>
                        </div>
                       </div>
                  </div>
                  <div id="tab-producto-1" class="tab-content" style="display: none;">
                      <div class="row">
                        <div class="col-md-12">
                          <label>Descripción (Descripción del Producto)</label>
                          <textarea id="descripcion">{{$producto->descripcion}}</textarea>
                          <table class="table" id="tabla-contenido" style="margin-bottom: 5px;">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col" width="40px">#</th>
                                <th scope="col" width="100%">Texto</th>
                                <th scope="col" colspan="2" style="padding: 0px;">
                                    <a href="javascript:;" class="btn  color-bg flat-btn" onclick="agregartitulo()"><i class="fa fa-plus"></i> Agregar</a>
                                </th>
                              </tr>
                            </thead>
                            <tbody>
                            </tbody>
                          </table>
                        </div>
                      </div>
                  </div>
                  @if(configuracion($tienda->id,'sistema_estadounidadmedida')['valor']==1)
                    <div id="tab-producto-4" class="tab-content" style="display: none;">
                      <div class="row">
                        <div class="col-md-6">
                            <div class="list-single-main-wrapper fl-wrap">
                                <div class="breadcrumbs gradient-bg fl-wrap">
                                  <span>Presentación Actual</span>
                                </div>
                            </div>
                            @if($productomenor!='' or $productomayor!='')
                            @else
                                <div class="mensaje-info">
                                  <i class="fa fa-exclamation-circle" style="font-size: 30px;margin-bottom: 5px;"></i><br>
                                  <b>¿Dese agrupar la presentación con otro producto?</b><br>
                                  Al agrupar las presentaciones, ya no podras editar el campo <b>"por (cantidad de unidades)"</b> de ambos productos, <br>así mismo se juntara el stock convirtiendo solo un stock, para ambos productos.
                                </div>
                                <div style="width: 100%;text-align: center;float: left;margin-bottom: 5px;">
                                <div class="onoffswitch" style="margin: auto;">
                                    <input type="checkbox" class="onoffswitch-checkbox estadoagruparunidadmedida" id="estadoagruparunidadmedida">
                                    <label class="onoffswitch-label" for="estadoagruparunidadmedida">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-md-7">
                                    <label>Unidad de Medida *</label>
                                    <select id="idunidadmedida">
                                        <option></option>
                                        @foreach($unidadmedidas as $value)
                                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label>Por (Cantidad de Unidades) *</label>
                                    <input type="number" value="{{$producto->por}}" id="por" disabled/>
                                </div>
                            </div>
                            <label>Stock (unidades)</label>
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="text" value="{{productosaldo($tienda->id,$producto->id)['stock']}}" id="stockactual" disabled/>
                                </div>
                            </div>
                            
                        
                            <div id="cont-presentacion-producto" <?php echo $productomenor!=''?'':'style="display:none;"'?>>
                              <div class="list-single-main-wrapper fl-wrap">
                                  <div class="breadcrumbs gradient-bg fl-wrap">
                                    <span>Agrupar Presentación</span>
                                  </div>
                              </div>
                              <label>Producto a agrupar *</label>
                              <select id="idproducto" <?php echo $productomenor!=''?'disabled':''?>>
                                  @if($productomenor!='')
                                  <option value="{{$productomenor->id}}">{{$productomenor->codigo!=''?$productomenor->codigo.' - ':''}}{{$productomenor->nombre}} / {{$productomenor->unidadmedidanombre}} x {{$productomenor->por}}</option>
                                  @else
                                  <option></option>
                                  @endif
                              </select>
                              <div id="tdcargaproducto"></div>
                              @if($productomenor!='')
                              <div id="cont-agruparpresentacion">
                                  <label>Stock (unidades)</label>
                                  <div class="row">
                                      <div class="col-md-12">
                                          <input type="text" value="{{productosaldo($tienda->id,$productomenor->id)['stock']}}" id="stockactualagrupar" disabled/>
                                      </div>
                                  </div>
                              </div>
                              @else
                              <div id="cont-agruparpresentacion" style="display:none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Unidad de Medida</label>
                                        <input type="text" id="unidadmedidaagrupar" disabled/>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Stock (unidades)</label>
                                        <input type="text" id="stockactualagrupar" disabled/>
                                    </div>
                                </div>
                              </div>
                              @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php
                            $presentaciones = producto_presentaciones($tienda->id,$producto->id);
                            //$presentaciones = collect($presentaciones)->sortByDesc('nivel');

                            $htmlstock = '<table class="table" style="margin-bottom: 10px;">
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
                            $i=0;
                            foreach($presentaciones as $value){
                                if($i==0){
                                    $cantidad = $value['cantidad'];
                                }
                                $cantidad = number_format(($cantidad/$value['por']), 3, '.', '');
                              
                                $style = 'padding: 10px;border-right: 1px solid #000;border-bottom: 1px solid #000;color: #fff;'.($value['idproducto']==$producto->id?'background-color: #008cea;':'background-color: #6f7373;');
                                $htmlstock = $htmlstock.'<tr>
                                        <th style="'.$style.'text-align: center;">'.$value['nivel'].'</th>
                                        <th style="'.$style.'">'.$value['productonombre'].'</th>
                                        <th style="'.$style.'">'.$value['unidadmedidanombre'].' x '.$value['por'].'</th>
                                        <th style="'.$style.'">'.$value['precioalpublico'].'</th>
                                        <th style="'.$style.'text-align: center;border-right: 0px solid #000;">'.$cantidad.'</th>
                                      </tr>';
                              $i++;
                            }

                            $htmlstock = $htmlstock.'</tbody></table>';
                            echo $htmlstock;        
                            ?>
                            <a href="javascript:;" id="modal-actualizarstock" class="btn btn-warning" style="margin-bottom: 10px;"><i class="fa fa-sync-alt"></i> Restaurar Stock</a>
                        @if($productomenor!='' or $productomayor!='')
                            <?php
                            $productomayor = DB::table('s_producto')
                                ->where('s_producto.id',$producto->id)
                                ->where('s_producto.s_idproducto','<>',0)
                                ->first();
                            ?>
                            @if($productomayor=='')
                            <div class="mensaje-info">
                                <i class="fa fa-exclamation-circle" style="font-size: 30px;margin-bottom: 5px;"></i><br>
                                <b>¿Desea desagrupar la presentación?</b><br>
                              Al desagrupar la presentación volvera a 0 el stock del producto:<br>
                              <b>"{{$producto->codigo!=''?$producto->codigo.' - ':''}}{{$producto->nombre}} / {{$producto->unidadmedidanombre}} x {{$producto->por}}"</b><br>
                              
                            </div>
                            <div style="width: 100%;text-align: center;float: left;margin-bottom: 5px;">
                            <div class="onoffswitch" style="margin: auto;">
                                <input type="checkbox" class="onoffswitch-checkbox estadodesagruparunidadmedida" id="estadodesagruparunidadmedida">
                                <label class="onoffswitch-label" for="estadodesagruparunidadmedida">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                            </div>
                            @else
                            <?php
                            $productomayor = DB::table('s_producto')
                                ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                                ->where('s_producto.id',$productomayor->s_idproducto)
                                ->select(
                                    's_producto.*',
                                    'unidadmedida.nombre as unidadmedidanombre'
                                )
                                ->first();
                            ?>
                            <div class="mensaje-info">
                                <i class="fa fa-exclamation-circle" style="font-size: 30px;margin-bottom: 5px;"></i><br>
                                <b>¿Desea desagrupar la presentación?</b><br>
                                Para desagrupar la presentación, debe desagrupar primero el producto mayor:<br>
                                <b>"{{$productomayor->codigo!=''?$productomayor->codigo.' - ':''}}{{$productomayor->nombre}} / {{$productomayor->unidadmedidanombre}} x {{$productomayor->por}}"</b><br>
                            </div>
                            @endif
                        @endif
                      </div>
                      </div>
                    </div>
                  @endif
                  @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
                  <div id="tab-producto-2" class="tab-content" style="display: none;">
                      <div class="table-responsive">
                      <table class="table" id="tabla-contenido-descuento-master" style="margin-bottom: 5px;">
                        <thead class="thead-dark">
                          <tr>
                            <th scope="col" width="100%">Producto</th>
                            <th scope="col" width="100px">Total</th>
                            <th scope="col" width="100px">Descuento</th>
                            <th scope="col" width="100px">T. Pack</th>
                            <th scope="col" width="10px" style="padding: 0px;">
                                <a href="javascript:;" class="btn  color-bg flat-btn" onclick="descuento_agregarproductomaster()"><i class="fa fa-plus"></i></a>
                            </th>
                          </tr>
                        </thead>
                        <tbody num="0">
                        </tbody>
                      </table>
                      </div>       
                  </div>   
                  @endif
                  <div id="tab-producto-3" class="tab-content" style="display: none;">
                      <div class="mensaje-danger">
                        <i class="fa fa-warning"></i> Esta seguro Eliminar el Producto <b>"{{ $producto->nombre }} "</b>!.
                      </div>
                      <div style="width: 100%;text-align: center;float: left;margin-bottom: 5px;">
                      <div class="onoffswitch" style="margin: auto;">
                          <input type="checkbox" class="onoffswitch-checkbox estadoeliminar" id="estadoeliminar">
                          <label class="onoffswitch-label" for="estadoeliminar">
                          <span class="onoffswitch-inner"></span>
                          <span class="onoffswitch-switch"></span>
                          </label>
                      </div>
                      </div>
                  </div>   
              </div>
          </div>     
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width: 100%;">Guardar Cambios</button>
        </div>
    </div> 
</form>                             
@endsection
@section('htmls')
@if(configuracion($tienda->id,'sistema_estadounidadmedida')['valor']==1)
<!--  modal actualizarstock --> 
<div class="main-register-wrap modal-actualizarstock">
    <div class="main-overlay"></div>
    <div class="main-register-holder">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Restaurar Stock</h3>
            <div class="mx-modal-cuerpo" id="contenido-actualizarstock">
              <div id="mx-carga-actualizarstock">
              <form class="js-validation-signin px-30" 
                  action="javascript:;" 
                  onsubmit="callback({
                    route: 'backoffice/tienda/sistema/{{ $tienda->id }}/producto',
                    method: 'POST',
                    carga: '#mx-carga-actualizarstock',
                    data:{
                        view: 'actualizarstock',
                        idproducto: {{$producto->id}}
                    }
                },
                function(resultado){
                    location.reload();      
                },this)">
                <div class="profile-edit-container">
                    <div class="custom-form">
                        <?php
                            $agrupar_stock = 0;
                            foreach($presentaciones as $value){
                                if($value['nivel']==1){
                                    $agrupar_stock = $value['por']*$value['cantidad'];
                                }
                            }
                        ?>
                        <label>Stock Actual en unidades</label>
                        <input type="text" value="{{$agrupar_stock}}" id="actualizarstock_stockaactual" disabled>
                        <label>Stock a Cambiar (poner en unidades) *</label>
                        <input type="number" value="0" id="actualizarstock_stockacambiar" step="0.01" min="0" onkeyup="mostrar_resultado()">
                    </div>
                              <div class="list-single-main-wrapper fl-wrap">
                                  <div class="breadcrumbs gradient-bg fl-wrap">
                                    <span>RESULTADO</span>
                                  </div>
                              </div>
                    <?php
                            $htmlstock = '<table class="table" style="margin-bottom: 10px;" id="tabla-contenido-presentacion">
                                <thead class="thead-dark">
                                  </tr>
                                    <th width="5px">N°</th>
                                    <th style="height: 30px;padding-top: 0;padding-bottom: 0;">U. Medida</th>
                                    <th style="height: 30px;padding-top: 0;padding-bottom: 0;">Stock</th>
                                    <th style="height: 30px;padding-top: 0;padding-bottom: 0;">Restante</th>
                                  </tr>
                                </thead>
                                <tbody>';
                            foreach($presentaciones as $value){

                                $style = 'padding: 10px;border-right: 1px solid #000;border-bottom: 1px solid #000;color: #fff;'.($value['idproducto']==$producto->id?'background-color: #008cea;':'background-color: #6f7373;');
                                $style1 = 'padding: 10px;border-right: 1px solid #000;border-bottom: 1px solid #000;color: #fff;'.($value['idproducto']==$producto->id?'background-color: #01558e;':'background-color: #3e4040;');
                                $htmlstock = $htmlstock.'<tr id="'.$value['nivel'].'" productopor="'.$value['productopor'].'">
                                        <th style="'.$style.'text-align: center;" rowspan="2">'.$value['nivel'].'</th>
                                        <th style="'.$style.'border-right: 0px solid #000;" colspan="3">'.$value['productonombre'].'</th>
                                      </tr><tr>
                                        <th style="'.$style.'">'.$value['unidadmedidanombre'].' x '.$value['por'].'</th>
                                        <th style="'.$style1.'text-align: center;" id="thresultadostock'.$value['nivel'].'">0</th>
                                        <th style="'.$style1.'text-align: center;border-right: 0px solid #000;" id="thresultadorestante'.$value['nivel'].'">0</th>
                                      </tr>';
                            }

                            $htmlstock = $htmlstock.'</tbody></table>';
                            echo $htmlstock;   
                    ?>
                                <div class="mensaje-info">
                                  <i class="fa fa-exclamation-circle" style="font-size: 30px;margin-bottom: 5px;"></i><br>
                                  Recuerda que el <b>"Stock a Cambiar"</b>, sera el stock actual del producto.<br>
                                  <b>¿Esta seguro de cambiar el stock?</b>
                                </div>
                </div>
                <div class="profile-edit-container">
                    <div class="custom-form">
                        <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width: 100%;">Cambiar Stock</button>
                    </div>
                </div> 
            </form> 
            </div>
            </div>
            <div class="mx-modal-cuerpo" id="contenido-confirmar-actualizarstock"></div>
        </div>
    </div>
</div>
<!--  fin modal actualizarstock --> 
@endif
@endsection
@section('subscripts')
<style>
.item,
.subitem,
.subsubitem {
    color: #fff;
    margin-left: 5px;
    font-weight: bold;
}
  
/* descuento */
  .car_cont{
    overflow: hidden;
    padding-bottom: 1px;
    padding-top: 1px;
    background-color: #c1c0c0;
    margin-bottom: 1px;
    border-radius: 5px;
  }
  .car_cantidad{
    color: #f9f9f9;
    background-color: #0964a0;
    padding: 5px;
    border-radius: 5px;
    float: left;
    height: 28px;
    text-align: center;
    margin-right: 5px;
  }
  .car_producto{
    color: #ffffff;
    float: left;
    margin-right: 5px;
    background-color: #31353d;
    padding: 5px;
    border-radius: 5px;
  }
  .car_subtotal{
    float: left;
    background-color: #00a044;
    padding: 5px;
    border-radius: 5px;
    color: white;
    margin-right: 5px;
  }
  .car_total{
    float: left;
    background-color: #908907;
    padding: 5px;
    border-radius: 5px;
    color: white;
  }
  .tdcargaproducto > div {
      text-align: center;
  }
  .tdcargaproducto > div > img{
      height: 38px;
  }
</style>
<script>
modal({click:'#modal-actualizarstock'});
tab({click:'#tab-producto'});
tab({click:'#tab-productodescuentoasociado'});
$("#idcategoria").select2({
    placeholder: "---  Seleccionar ---"
}).val(<?php echo ($producto->s_idcategoria2!=0? $producto->s_idcategoria2:$producto->s_idcategoria1) ?>).trigger("change");
 
@if($producto->idunidadmedida!=0)
$("#idunidadmedida").select2({
    placeholder: "---  Seleccionar ---"
}).val({{$producto->idunidadmedida}}).trigger("change");
@else
$("#idunidadmedida").select2({
    placeholder: "---  Seleccionar ---"
});
@endif

function mostrar_resultado(){
    $("#tabla-contenido-presentacion > tbody > tr").each(function() {
        var num = $(this).attr('id');  
        var productopor = $(this).attr('productopor');  
        var stock_acambiar = $('#actualizarstock_stockacambiar').val();
        var stock_agrupado = stock_acambiar*100;
        if(num!=undefined){
            var stock_agrupadopor2 = productopor*100;
                                var stock_agrupadoporvalid2 = stock_agrupadopor2;
                                var stock_agrupadoactual2 = 0;
                                var stock_agrupadoactualrestante2 = 0;
                                for(var i=1;i<=stock_agrupado;i++){
                                  if(i==stock_agrupadoporvalid2){
                                      stock_agrupadoactual2++;
                                      stock_agrupadoporvalid2=stock_agrupadoporvalid2+stock_agrupadopor2;
                                      stock_agrupadoactualrestante2 = 0;
                                  }else{
                                      stock_agrupadoactualrestante2++;
                                  }
                                }
                                stock_agrupadoactualrestante2=stock_agrupadoactualrestante2/100;
          
            $("#thresultadostock"+num).html(stock_agrupadoactual2);    
            $("#thresultadorestante"+num).html(stock_agrupadoactualrestante2);
          
        }
    });
}

@if($producto->s_idmarca!='')
$("#idmarca").select2({
    placeholder: "---  Seleccionar ---",
    allowClear: true
}).val({{$producto->s_idmarca}}).trigger("change");
@else
$("#idmarca").select2({
    placeholder: "---  Seleccionar ---",
    allowClear: true
});
@endif
  
$("#idestado").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$producto->s_idestado}}).trigger("change");

$("#idestadodetalle").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$producto->s_idestadodetalle!=0?$producto->s_idestadodetalle:2}}).trigger("change");

$("#idestadotiendavirtual").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$producto->s_idestadotiendavirtual!=0?$producto->s_idestadotiendavirtual:1}}).trigger("change");

  
@if(configuracion($tienda->id,'sistema_estadounidadmedida')['valor']==1)
$('#por').keyup( function(e) {
    if($(this).val()>1){
        //$('#cont-agruparpresentacion-actual').css('display','block');
    }else{
        $('#idproducto').html('');
        $('#unidadmedidaagrupar').val('');
        $('#stockactualagrupar').val('');
        $('#cont-agruparpresentacion').css('display','none');
        //$('#cont-agruparpresentacion-actual').css('display','none');
    }
})
$("#estadoagruparunidadmedida").click(function(){
    $('#cont-presentacion-producto').css('display','none');
    var checked = $("#estadoagruparunidadmedida:checked").val();
    $('#por').attr('disabled','true');
    
    if(checked=='on'){
        $('#cont-presentacion-producto').css('display','block');
        $('#por').removeAttr('disabled');
    }else{
        $('#por').val('1');
    }
});
$("#estadodesagruparunidadmedida").click(function(){
    $('#cont-presentacion-producto').css('display','block');
    var checked = $("#estadodesagruparunidadmedida:checked").val();
    $('#por').attr('disabled','true');
    
    if(checked=='on'){
        $('#cont-presentacion-producto').css('display','none');
        $('#por').removeAttr('disabled');
    }
});
  
$("#idproducto").select2({
    @include('app.select2_producto')
}).on("change", function(e) {
    $('#cont-agruparpresentacion').css('display','none');
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/producto/show_mostrarpresentacion')}}",
        type:'GET',
        data: {
            idproducto : e.currentTarget.value
        },
        beforeSend: function (data) {
            load('#tdcargaproducto');
        },
        success: function (respuesta){
            $('#tdcargaproducto').html('');
            $('#unidadmedidaagrupar').val(respuesta["producto"].unidadmedidanombre+' x '+respuesta["producto"].por);
            $('#stockactualagrupar').val(respuesta["stock"]);
            $('#cont-agruparpresentacion').css('display','block');
        }
    })
});
@endif
<?php 
$productodetalles = DB::table('s_productodetalle')
    ->where('s_idproducto',$producto->id)
    ->orderBy('orden','asc')
    ->orderBy('suborden','asc')
    ->orderBy('subsuborden','asc')
    ->get();
?>
@foreach($productodetalles as $value)
    @if($value->orden!='' && $value->suborden==0 && $value->subsuborden==0)
        agregartitulo('{{ $value->nombre }}');
    @elseif($value->orden!='' && $value->suborden!='' && $value->subsuborden==0)
        agregarsubtitulo('{{ $value->orden }}','{{ $value->nombre }}');
    @elseif($value->orden!='' && $value->suborden!='' && $value->subsuborden!='')
        agregarsubsubtitulo('{{ $value->orden }}','{{ $value->suborden }}','{{ $value->nombre }}');
    @endif
@endforeach
  
/* descuento */  
@if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
var descuento = <?php echo json_encode($asociarproductos) ?>;

$.each(descuento, function( keydesc, valuedesc ) {
    $.each(valuedesc.lista_descuento, function( key, value ) {
        descuento_agregarproductomaster(
          '',
          value.total,
          value.montodescuento,
          value.totalpack,
          value.detalle,
          'editar');
    });
});
function descuento_agregarproductomaster(
    codigo='',
    total='{{$producto->precioalpublico}}',
    montodescuento='0.00',
    totalpack='{{$producto->precioalpublico}}',
    detalle = {
      "idproducto":{{$producto->id}},
      "productocodigo":"{{$producto->codigo}}",
      "productonombre":"{{$producto->nombre}}",
      "precioalpublico":"{{$producto->precioalpublico}}"},
    estado = 'nuevo'
  ){
      
      var num = $("#tabla-contenido-descuento-master > tbody").attr('num');
      $('#tabla-contenido-descuento-master > tbody').append('<tr id="'+num+'" style="background-color:#1e8dd8;">'+
                             '<td>'+
                                 '<table class="table" id="tabla-contenido-descuento'+num+'" style="margin-bottom: 0px;">'+
                                   '<tbody num="0">'+
                                   '</tbody>'+
                                 '</table>'+
                             '</td>'+
                             '<td>'+
                               '<div id="resultado-total'+num+'" style="margin-top: -7px;position: absolute;"></div>'+
                               '<input type="text" value="'+total+'" id="total'+num+'" style="width: 100px;" disabled/>'+
                             '</td>'+
                             '<td>'+
                               '<input type="number" value="'+montodescuento+'" id="descuentototal'+num+'" step="0.01" min="0" onkeyup="descuento_calculartotalpack('+num+')" onclick="descuento_calculartotalpack('+num+')" style="width: 100px;"/>'+
                             '</td>'+
                             '<td>'+
                               '<input type="text" value="'+totalpack+'" id="totalpack'+num+'" style="width: 100px;" disabled/>'+
                             '</td>'+
                             '<td>'+
                               '<a href="javascript:;" id="btneliminar'+num+'" class="btn btn-danger color-bg flat-btn" onclick="descuento_eliminarproductomaster('+num+')"><i class="fa fa-close"></i></a>'+
                             '</td>'+
                           '</tr>');
      $("#tabla-contenido-descuento-master > tbody").attr('num',parseInt(num)+1); 

      if(estado=='nuevo'){
          var text = (detalle.productocodigo!=''?detalle.productocodigo+' - ':'')+detalle.productonombre+' / '+detalle.precioalpublico;
          descuento_agregarproducto(num,detalle.idproducto,text,'disabled');
          descuento_agregarproducto(num);
      }else if(estado=='editar'){
          $.each(detalle, function( key, value ) {
                var text = (value.productocodigo!=''?value.productocodigo+' - ':'')+value.productonombre+' / '+value.precioalpublico;
                descuento_agregarproducto(num,value.idproducto,text,value.estado);
          });  
      }
      
      
}
function descuento_seleccionarproductomaster(){
    var data = '';
    $("#tabla-contenido-descuento-master > tbody > tr").each(function() {
        var num = $(this).attr('id');      
        var descuentototal = $("#descuentototal"+num).val(); 
        data = data+'/&&/'+descuentototal+'/,,/'+descuento_seleccionarproducto(num);
    });
    return data;
}
function descuento_eliminarproductomaster(num){
    $('#tabla-contenido-descuento-master > tbody > tr#'+num).remove();
}
function descuento_agregarproducto(num_master,id='',text='',estado = ''){
    var num = $("#tabla-contenido-descuento"+num_master+" > tbody").attr('num');
    var btneliminar = '<a href="javascript:;" id="btneliminar'+num+'" class="btn btn-danger color-bg flat-btn" onclick="descuento_eliminarproducto('+num_master+','+num+')"><i class="fa fa-close"></i></a>';
    if($('#tabla-contenido-descuento'+num_master+' > tbody > tr').length<1){
        btneliminar = '<a href="javascript:;" class="btn  color-bg flat-btn" onclick="descuento_agregarproducto('+num_master+')"><i class="fa fa-plus"></i></a>';
        
    }else if($('#tabla-contenido-descuento'+num_master+' > tbody > tr').length==1){
        btneliminar = '';
    }
    $('#tabla-contenido-descuento'+num_master+' > tbody').append('<tr id="'+num+'" productonombre="'+text+'">'+
      '<td>'+
          '<select id="idproductodescuento'+num_master+'_'+num+'" '+estado+'>'+
              '<option value="'+id+'">'+text+'</option>'+
          '</select>'+
      '</td>'+
      '<td width="10px">'+btneliminar+'</td>'+
    '</tr>');
  
    $("#tabla-contenido-descuento"+num_master+" > tbody").attr('num',parseInt(num)+1); 

  
    $("#idproductodescuento"+num_master+'_'+num).select2({
        @include('app.select2_producto',[
            'idtienda'=>$tienda->id
        ])
    }).on("change", function(e) {
        descuento_actualizartotal(num_master);
    });
}
function descuento_eliminarproducto(num_master,num){
    $('#tabla-contenido-descuento'+num_master+' > tbody > tr#'+num).remove();
    descuento_actualizartotal(num_master);
    /*$('#cont-descuento-producto').css('display','none');
    if($('#tabla-contenido-descuento > tbody > tr').length>0){
        $('#cont-descuento-producto').css('display','block');
    }*/
}
function descuento_actualizartotal(num_master){
    load('#resultado-total'+num_master);
    $("#total"+num_master).val('');
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/producto/showtotaldescuento')}}",
        type:'GET',
        data: {
            descuento_seleccionarproducto : descuento_seleccionarproducto(num_master)
        },
        success: function (respuesta){
            $('#resultado-total'+num_master).html('');
            $("#total"+num_master).val(respuesta["total"]);
            /* descuento */
            var descuentototal = parseFloat($("#descuentototal"+num_master).val());
            $("#totalpack"+num_master).val((parseFloat(respuesta["total"])-descuentototal).toFixed(2));
        }
    })
}
function descuento_actualizar(){
    var codigo = $("#codigo").val();
    var nombre =  $("#nombre").val();
    var precioalpublico =  $("#precioalpublico").val();
    $("#cont-productotextodescuento").html((codigo!=''?codigo+' - ':'')+nombre+' / '+precioalpublico);
    $("#total").val(precioalpublico);
}
function descuento_calculartotalpack(num_master){
    var total = parseFloat($("#total"+num_master).val());
    var descuentototal = parseFloat($("#descuentototal"+num_master).val());
    $("#totalpack"+num_master).val((total-descuentototal).toFixed(2));
}
function descuento_seleccionarproducto(num_master){
    var data = '';
    $("#tabla-contenido-descuento"+num_master+" > tbody > tr").each(function() {
        var num = $(this).attr('id');        
        var idproductodescuento = $("#idproductodescuento"+num_master+'_'+num).val();
        var productonombre = $(this).attr('productonombre');
        //if(idproductodescuento!=''){
            data = data+'/&&&/'+idproductodescuento+'/,,,/'+productonombre;
        //}
    });
    return data;
}
@endif
// tabla
function agregartitulo(text=''){
    $('#tabla-contenido > tbody').append('<tr style="background-color: #1e8dd8;" menu="true">'+
      '<td scope="row" width="40px">0</td>'+
      '<td><input value="'+text+'" type="text"></td>'+
      '<td width="10px"></td>'+
      '<td width="10px"></td>'+
    '</tr>');
    actualizaritem();
}
function agregarsubtitulo(num,text=''){
    var countsubtitulo = $('#tabla-contenido > tbody > tr.subnum'+num).length;
    if(countsubtitulo==0){
        var idtr = num;
    }else{
        var i = $('#tabla-contenido > tbody > tr#'+num).attr('i');
        var countsubsubtitulo = $('#tabla-contenido > tbody > tr.subsubnum'+i).length;
        if(countsubsubtitulo==0){
            var idtr = i;
        }else{
            var subi = $('#tabla-contenido > tbody > tr#'+i).attr('i');
            var idtr = subi;
        }  
    }
  
    $('#tabla-contenido > tbody tr#'+idtr).after('<tr class="subnum'+num+'" submenu="true" style="background-color: #0fb50f;">'+
      '<td scope="row" width="40px">0.0</td>'+
      '<td style="padding-left: 20px;"><input type="text" value="'+text+'"></td>'+
      '<td></td>'+
      '<td></td>'+
    '</tr>');
    ocultarbtneliminarsubtitulo(num);
    actualizaritem()
}
function agregarsubsubtitulo(subnum,num,text=''){
    var countsubtitulo = $('#tabla-contenido > tbody > tr.subsubnum'+subnum+'-'+num).length;
    if(countsubtitulo==0){
        var idtr = subnum+'-'+num;
    }else{
        var i = $('#tabla-contenido > tbody > tr#'+subnum+'-'+num).attr('i');
        var idtr = i;
    }
    $('#tabla-contenido > tbody tr#'+idtr).after('<tr class="subsubnum'+subnum+'-'+num+'" subsubmenu="true" style="background-color: #f1c40f;">'+
      '<td scope="row" width="40px">0.0</td>'+
      '<td style="padding-left: 40px;"><input type="text" value="'+text+'"></td>'+
      '<td></td>'+
      '<td></td>'+
    '</tr>');
    ocultarbtneliminarsubsubtitulo(subnum,num);
    actualizaritem()
}
function aliminartitulo(num){
    $('#tabla-contenido > tbody tr#'+num).remove();
    actualizaritem();
}
function aliminarsubtitulo(subnum,num){
    $('#tabla-contenido > tbody tr#'+subnum+'-'+num).remove();
    ocultarbtneliminarsubtitulo(subnum);
    actualizaritem()
}
function aliminarsubsubtitulo(subsubnum,subnum,num){
    $('#tabla-contenido > tbody tr#'+subsubnum+'-'+subnum+'-'+num).remove();
    ocultarbtneliminarsubsubtitulo(subsubnum,subnum);
    actualizaritem()
}
function seleccionartabla(){
    var data = '';
    $('#tabla-contenido > tbody > tr').each(function() {
        var item = '';
        var nombre = '';
            $(this).find('td').each (function( column, td) {
                if(column==0){
                    item = $(td).find('div').html();
                }else if(column==1){
                    nombre = $(td).find('input').val();
                }else if(column==2){
                    
                }else if(column==3){
                    
                }
            });
        data = data+'/-/'+item+'/,/'+nombre;
    });
    return data;
}
function actualizaritem(){
    var i = 1;
    var ii = 1;
    var iii = 1;
    
    var ulunum = '';
  
    $('#tabla-contenido > tbody > tr').each(function() {
        var menu = $(this).attr('menu');
        var submenu = $(this).attr('submenu');
        var subsubmenu = $(this).attr('subsubmenu');
        if(menu!=undefined && submenu==undefined && subsubmenu==undefined){
            $(this).attr('id',i);
            $(this).find('td').each (function( column, td) {
                if(column==0){
                    $(td).html('<div class="item">'+i+'</div>');
                }else if(column==1){
                    //
                }else if(column==2){
                    $(td).html('<a href="javascript:;" class="btn color-bg flat-btn" onclick="agregarsubtitulo('+i+')"><i class="fa fa-plus"></i></a>');
                }else if(column==3){
                    $(td).html('<a href="javascript:;" id="btneliminar'+i+'" class="btn btn-danger color-bg flat-btn" onclick="aliminartitulo('+i+')"><i class="fa fa-close"></i></a>');
                    ocultarbtneliminarsubtitulo(i);
                }
            }); 
            i++;
            ii = 1;
        }else if(menu==undefined && submenu!=undefined && subsubmenu==undefined){
            var num = i-1;
            $(this).attr('id',num+'-'+ii);
            $(this).find('td').each (function( column, td) {
                if(column==0){
                    $(td).html('<div class="subitem">'+num+'.'+ii+'</div>');
                }else if(column==1){
                    //
                }else if(column==2){
                    $(td).html('<a href="javascript:;" class="btn color-bg flat-btn" onclick="agregarsubsubtitulo('+num+','+ii+')"><i class="fa fa-plus"></i></a>');
                }else if(column==3){
                    $(td).html('<a href="javascript:;" id="btneliminar'+num+'-'+ii+'" class="btn btn-danger color-bg flat-btn" onclick="aliminarsubtitulo(\''+num+'\',\''+ii+'\')"><i class="fa fa-close"></i></a>');
                    ocultarbtneliminarsubsubtitulo(num,ii);
                }
            });
            ulunum = num+'-'+ii;
            ii++;
            iii = 1;
            $('#tabla-contenido > tbody > tr#'+num).attr('i',ulunum);
        }else if(menu==undefined && submenu==undefined && subsubmenu!=undefined){
            var subnum = i-1;
            var num = ii-1;
            $(this).attr('id',subnum+'-'+num+'-'+iii);
            $(this).find('td').each (function( column, td) {
                if(column==0){
                    $(td).html('<div class="subsubitem">'+subnum+'.'+num+'.'+iii+'</div>');
                }else if(column==1){
                    //
                }else if(column==2){
                    //
                }else if(column==3){
                    $(td).html('<a href="javascript:;" class="btn btn-danger color-bg flat-btn" onclick="aliminarsubsubtitulo(\''+subnum+'\',\''+num+'\',\''+iii+'\')"><i class="fa fa-close"></i></a>');
                }
            });
            ulunum = subnum+'-'+num+'-'+iii;
            iii++;
            $('#tabla-contenido > tbody > tr#'+subnum+'-'+num).attr('i',ulunum);
        }
        
    });
  
    
}
function ocultarbtneliminarsubtitulo(num){
    var countsubtitulo = $('#tabla-contenido > tbody > tr.subnum'+num).length;
    if(countsubtitulo>0){
        $('#btneliminar'+num).css('display','none');
    }else{
        $('#btneliminar'+num).css('display','block');
    }
}
function ocultarbtneliminarsubsubtitulo(subnum,num){
  
    var countsubtitulo = $('#tabla-contenido > tbody > tr.subsubnum'+subnum+'-'+num).length;
    if(countsubtitulo>0){
        $('#btneliminar'+subnum+'-'+num).css('display','none');
    }else{
        $('#btneliminar'+subnum+'-'+num).css('display','block');
    }
}
</script>
@endsection