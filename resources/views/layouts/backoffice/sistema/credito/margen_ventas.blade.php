<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/credito/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'margen_ventas',
              productos: json_productos(),
              productos_mensual: json_productos_mensual(),
              dias: json_dias(),
              semanas: json_semanas(),
              subproducto: json_subproducto(),
              subproductomensual: json_subproducto('mensual'),
          }
      },
      function(res){
        removecarga({input:'#mx-carga'})
        $('#success-message').removeClass('d-none');
        $('#success-message').text(res.mensaje);
        setTimeout(function() {
          $('#success-message').addClass('d-none');
        }, 5000);
        lista_credito();
        load_nuevo_credito();
        $('#boton_imprimir').attr('disabled',false);
      },this)"> 
    @php
 
      $productos = $credito_cuantitativa_margen_venta ? ( $credito_cuantitativa_margen_venta->productos == "" ? [] : json_decode($credito_cuantitativa_margen_venta->productos) ) : [];
      $productos_mensual = $credito_cuantitativa_margen_venta ? ( $credito_cuantitativa_margen_venta->productos_mensual == "" ? [] : json_decode($credito_cuantitativa_margen_venta->productos_mensual) ) : [];
      $dias = $credito_cuantitativa_margen_venta ? ( $credito_cuantitativa_margen_venta->dias == "" ? [] : json_decode($credito_cuantitativa_margen_venta->dias) ) : [];
      $semanas = $credito_cuantitativa_margen_venta ? ( $credito_cuantitativa_margen_venta->semanas == "" ? [] : json_decode($credito_cuantitativa_margen_venta->semanas) ) : [];
      $subproducto = $credito_cuantitativa_margen_venta ? ( $credito_cuantitativa_margen_venta->subproducto == "" ? [] : json_decode($credito_cuantitativa_margen_venta->subproducto) ) : [];
      $subproductomensual = $credito_cuantitativa_margen_venta ? ( $credito_cuantitativa_margen_venta->subproductomensual == "" ? [] : json_decode($credito_cuantitativa_margen_venta->subproductomensual) ) : [];
  
    @endphp
  
    <div class="modal-header" style="border-bottom: 0;">
        <h5 class="modal-title">MARGEN DE VENTAS </h5>
        <button type="button" class="btn-close text-white" id="modal-close-garantia-cliente" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body modal-body-cualitativa">
      <div class="row">
        <div class="col-sm-12 col-md-5">
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">AGENCIA/OFICINA:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $tienda->nombreagencia }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">CLIENTE/RAZON SOCIAL:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $credito->nombreclientecredito }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">GIRO ECONÓMICO:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->nombregiro_economico_evaluacion : '' }}" disabled>
            </div>
          </div>
          
        </div>
        <div class="col-sm-12 col-md-3">
          <div class="row">
            <label class="col-sm-3 col-form-label" style="text-align: right;">FECHA:</label>
            <div class="col-sm-7">
              <input type="date" step="any" class="form-control" value="{{ $credito_cuantitativa_margen_venta!=''?date_format(date_create($credito_cuantitativa_margen_venta->fecha),'Y-m-d'):'' }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label" style="text-align: right;">DNI/RUC:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->docuementocliente }}" disabled>
            </div>
          </div>
          @if($users_prestamo->dni_pareja!='' or $users_prestamo->nombrecompleto_pareja!='')
          <div class="row">
            <label class="col-sm-3 col-form-label" style="text-align: right;">DNI:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $users_prestamo->dni_pareja }}" disabled>
            </div>
          </div>
          @endif
          
          <div class="row">
            <label class="col-sm-3 col-form-label" style="text-align: right;">EJERCICIO:</label>
            <div class="col-sm-7">
              @if($users_prestamo->db_idforma_ac_economica!='')
                <input type="text" step="any" class="form-control" id="ejercicio_giro_economico" value="{{ $users_prestamo->db_idforma_ac_economica }}" disabled>
              @else
                <input type="text" step="any" class="form-control" id="ejercicio_giro_economico" value="{{ $users_prestamo->db_idforma_ac_economica }}" disabled>
              @endif
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-4">
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">NRO SOLICITUD:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="S{{ str_pad($credito->id, 8, '0', STR_PAD_LEFT)  }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">PRODUCTO:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->nombreproductocredito }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">TIPO DE CAMBIO:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ configuracion($tienda->id,'tipo_cambio_dolar')['valor'] }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">TIPO DE CLIENTE:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->tipo_operacion_credito_nombre }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">MODALIDAD:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->modalidad_credito_nombre }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">DESTINO DE CRÉDITO:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->tipo_destino_credito_nombre}}" disabled>
            </div>
          </div>
        </div>
    </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block">IV. CÁLCULO DE MARGEN Y NIVEL VENTAS</span>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">4.1 VENTAS DENTRO DE LA SEMANA (VENTAS CON FRECUENCIA DIARIA Y SEMANAL)</span>
      </div>
      <div class="row">
        <div class="col-sm-9">
          
          <div class="row mt-2">
            <label class="col-sm-4 col-form-label" style="text-align: right;">MARGEN DE VENTAS TOTAL CALCULADO:</label>
            <div class="col-sm-1">
              <div class="input-group">
                <input type="text" step="any" class="form-control campo_moneda" value="{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->margen_venta_calculado : '0.00' }}" id="margen_venta_calculado" disabled>
                <span class="input-group-text">%</span>
              </div>
            </div>
            <div class="col-sm-1" style="display:none;">
              <input type="text" step="any" class="form-control" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->margen_giro_economico : '0.00'}}" id="margen_venta_giro_economico" disabled>
            </div>
            <input type="hidden" id="estado_error_margen_venta">
            <div id="error_margen_venta" class="col-sm-12 alert alert-danger mt-2 d-none" 
                 style="background-color: #ff6666;border-color: #ff6666;color: #000;font-weight: bold;">
              EL MARGEN DE VENTA CALCULADO NO PUEDE SER SUPERIOR AL DEL GIRO ECONÓMICO ({{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->margen_giro_economico : '0.00'}}%)</div>
          </div>
          <br>
          <table class="table table-bordered" id="tabla-producto">
              <thead>
                <tr>
                  <th rowspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;">VENTA MUESTRA: De productos(de mayor rotación) que comercializa, produce o presta servicio</th>
                  <th rowspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;" width="100px">U. de Med.</th>
                  <th rowspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;">Cantidad</th>
                  <th rowspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;">P. de venta</th>
                  <th rowspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;">P. de Compra /Costo de Produc.</th>
                  <th colspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;text-align:center;">TOTAL (S/.)</th>
                  <th rowspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;">Marg. x Producto</th>
                  @if($view_detalle!='false')
                  <th rowspan=2 width="10px" style="background-color: #c8c8c8 !important;color: #000 !important;">
                    <a href="javascript:;" class="btn btn-success" onclick="agregar_producto()">
                      <i class="fa-solid fa-plus"></i>
                    </a>
                  </th>
                  @endif
                </tr>
                <tr>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;">VENTAS</th>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;">Costo: Vent./Prod.</th>
                </tr>
              </thead>
              <tbody num="0">
                @foreach($productos as $key => $value)
                  <tr id="{{ $value->id }}">
                    <td producto><input type="text" onkeyup="actualizarOpcionesSelect()" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" value="{{ $value->producto }}"></td>
                    <td unidadmedida>
                      <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                          <option></option>
                        @foreach($unidadmedida_credito as $unidad)
                          <option value="{{ $unidad->nombre }}" {{ $unidad->nombre == $value->unidadmedida ? "selected" : "" }}>{{ $unidad->nombre }}</option>
                        @endforeach
                      </select>
                      
                    </td>
                    <td cantidad><input type="text" valida_input_vacio onkeyup="calcula_subtotales(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $value->cantidad }}"></td>
                    <td precioventa><input type="text" valida_input_vacio onkeyup="calcula_subtotales(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $value->precioventa }}"></td>
                    <td preciocompra><input type="text" valida_input_vacio onkeyup="calcula_subtotales(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $value->preciocompra }}"></td>
                    <td subtotalventa><input type="number" step="any" class="form-control campo_moneda" disabled value="{{ $value->subtotalventa }}"></td>
                    <td subtotalcompra><input type="number" step="any" class="form-control campo_moneda" disabled value="{{ $value->subtotalcompra }}"></td>
                    <td margen>
                      <div class="input-group">
                        <input type="text" step="any" disabled class="form-control campo_moneda" value="{{ $value->margen }}">
                        <span class="input-group-text">%</span>
                      </div>
                    </td>
                    @if($view_detalle!='false')
                    <td><button type="button" onclick="eliminar_producto(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                    @endif
                 </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <td colspan=5 align="right">TOTAL (S/.)</td>
                  <td><input type="number" class="form-control campo_moneda" disabled id="total_venta" value="{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->total_venta : '0.00' }}"></td>
                  <td><input type="number" class="form-control campo_moneda" disabled id="total_compra" value="{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->total_compra : '0.00' }}"></td>
                  <td></td>
                  <td></td>
                </tr>
                <tr>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;" colspan=5 align="right">Mg. de Venta</th>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;">
                    <div class="input-group">
                        <input type="text" step="any" disabled id="porcentaje_margen" class="form-control campo_moneda" value="{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->porcentaje_margen : '0.00' }}">
                        <span class="input-group-text">%</span>
                      </div>
                  </th>
                  <td style="background-color: #c8c8c8 !important;color: #000 !important;"></td>
                  <td style="background-color: #c8c8c8 !important;color: #000 !important;"></td>
                  <td style="background-color: #c8c8c8 !important;color: #000 !important;"></td>
                </tr>
              </tfoot>
          </table>
          
        </div>
        <div class="col-sm-3">
          <table class="table table-bordered mb-2">
            <thead>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" colspan=2>CÁLCULO DE VENTAS</th>
              </tr>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" width="100px">FRECUENCIA</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" >
                  <input type="text" disabled id="frecuencia_ventas" class="form-control" 
                         value="{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->frecuencia_ventas : 'DIARIO' }}">
                </th>
              </tr>
            </thead>
          </table>
          <table class="table table-bordered" id="tabla-dias">
            <thead>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" width="10px">N°</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" width="140px">Dias</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;">Ventas</th>
              </tr>
            </thead>
            <tbody>
              @if(count($dias) > 0)
                @foreach($dias as $value)
                  <tr>
                    <td numero>{{ $value->numero }}</td>
                    <td dia>{{ $value->dia }}</td>
                    <td valor><input onkeyup="calcula_total_dia()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $value->valor }}"></td>
                  </tr>
                @endforeach
              @else
              <tr>
                <td numero>1</td>
                <td dia>Lunes</td>
                <td valor><input onkeyup="calcula_total_dia()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
              </tr>
              <tr>
                <td numero>2</td>
                <td dia>Martes</td>
                <td valor><input onkeyup="calcula_total_dia()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
              </tr>
              <tr>
                <td numero>3</td>
                <td dia>Miércoles</td>
                <td valor><input onkeyup="calcula_total_dia()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
              </tr>
              <tr>
                <td numero>4</td>
                <td dia>Jueves</td>
                <td valor><input onkeyup="calcula_total_dia()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
              </tr>
              <tr>
                <td numero>5</td>
                <td dia>Viernes</td>
                <td valor><input onkeyup="calcula_total_dia()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
              </tr>
              <tr>
                <td numero>6</td>
                <td dia>Sábado</td>
                <td valor><input onkeyup="calcula_total_dia()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
              </tr>
              <tr>
                <td numero>7</td>
                <td dia>Domingo</td>
                <td valor><input onkeyup="calcula_total_dia()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
              </tr>
              @endif
              <tr total>
                <th colspan="2" style="background-color: #c8c8c8 !important;color: #000 !important;">Venta Semanal (S/.)</th>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;"><input type="text" valida_input_vacio id="venta_total_dias" step="any" class="form-control campo_moneda" value="{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->venta_total_dias : '0.00' }}" disabled></td>
              </tr>
            </tbody>
          </table>
         
          
          <table class="table table-bordered mb-2 mt-2">
            <thead>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" width="150px">N° de Días</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" ><input type="text" disabled id="numero_dias" class="form-control campo_moneda" value="{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->numero_dias : '0' }}"></th>
              </tr>
            </thead>
          </table>
          <table class="table table-bordered mb-2 mt-2">
            <thead>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" width="150px">Venta mensual (S/.)</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" ><input type="text" disabled id="venta_mensual" value="{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->venta_mensual : '0' }}" class="form-control campo_moneda"></th>
              </tr>
            </thead>
          </table>
          <table class="table table-bordered mb-2 mt-2">
            <thead>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" width="40px">N°</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" width="130px">Día/Recabo Datos</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" >Ventas</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><input type="text" class="form-control campo_moneda" disabled id="recabo_dato_numero" value="{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->recabo_dato_numero : '1' }}"></td>
                <td>
                  <select id="recabo_dato_dia" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                    <option></option>
                    <option value="Lunes">Lunes</option>
                    <option value="Martes">Martes</option>
                    <option value="Miércoles">Miércoles</option>
                    <option value="Jueves">Jueves</option>
                    <option value="Viernes">Viernes</option>
                    <option value="Sábado">Sábado</option>
                    <option value="Domingo">Domingo</option>
                  </select>
                </td>
                <td><input type="text" class="form-control campo_moneda" disabled id="recabo_dato_monto" value="{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->recabo_dato_monto : '0.00' }}"></td>
              </tr>
            </tbody>
          </table>
          <table class="table table-bordered mb-2 mt-2">
            <thead>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" colspan="2">Estado de muestra de DATOS</th>
              </tr>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" colspan="2"><input type="text" disabled id="estado_muestra" value="{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->estado_muestra : '0.00' }}" class="form-control"></th>
              </tr>
            </thead>
          </table>
          <table class="table table-bordered mb-2 mt-2">
            <thead>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" width="150px">Mg. De venta al mes(1) (S/.)</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" >
                  <input type="text" disabled id="margen_ventas" value="{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->margen_ventas : '0.00' }}" class="form-control campo_moneda"></th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
      
      @if($view_detalle!='false')
      <div class="row">
        <div class="col-sm-12 col-md-4">
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">PRODUCTO:</label>
            <div class="col-sm-8">
              <select class="form-control" id="producto_detalle">
                <option value=""></option>
              </select>
            </div>
          </div>
        </div>
      </div>
      @endif
      <div class="row mt-3" id="container-producto-secundario">
        @foreach($subproducto as $value)
          <div table-subproducto class="col-sm-12 col-md-4">
            <table class="table table-bordered m-2" id="table-producto-{{ $value->idtable }}" idproducto="{{ $value->idtable }}">
              <thead>
                <tr>
                  <th colspan="2">Costeo x unidad de medida (muestra)</th>
                  <th colspan="2" nombre_producto>{{ $value->nombre_producto }}</th>
                
                  @if($view_detalle!='false')
                  <th ><button class="btn btn-danger" type="button" onclick="eliminar_producto_secundario(this)"><i class="fa fa-trash"></i></button></th>
                  @endif
                </tr>
                <tr>  
                  <th >Materia prima (en U., Doc. Etc) M. Obra y otros</th>
                  <th width="70px">Cantidad</th>
                  <th width="70px">Costo x U., Doc. Etc.</th>
                  <th width="70px">Total (S/.)</th>
                  @if($view_detalle!='false')
                  <th width="10px"><button class="btn btn-success" type="button" onclick="agregar_subproducto(this)"><i class="fa fa-plus"></i></button></th>
                  @endif
                </tr>
              </thead>
              <tbody>
                @foreach($value->producto as $key => $items)
                <tr>
                  <td producto><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" value="{{ $items->producto }}"></td>
                  <td cantidad><input type="text" valida_input_vacio onkeyup="subtotal_subproducto(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $items->cantidad }}"></td>
                  <td costo><input type="text" valida_input_vacio onkeyup="subtotal_subproducto(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $items->costo }}"></td>
                  <td total><input type="text" class="form-control campo_moneda" value="{{ $items->total }}" disabled></td>
                  
                  @if($view_detalle!='false')
                  <td><button class="btn btn-danger" {{ $key == 0 ? 'disabled' : '' }} type="button" onclick="remove_subproducto(this)"><i class="fa fa-trash"></i></button></td>
                  @endif
                </tr>
                @endforeach
                
              </tbody>
              <tfoot>
                <tr>
                  <td colspan=3>Costo de Materia Prima</td>
                  <td costo_materia_prima><input type="text"  class="form-control campo_moneda" disabled value="{{ $value->costo_materia_prima }}"></td>
                  @if($view_detalle!='false')
                  <td></td>
                  @endif
                </tr>
                <tr>
                  <td colspan=3>Costo de mano de obra</td>
                  <td costo_mano_obra><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $value->costo_mano_obra }}" 
                                             onkeyup="total_costo_produccion(this)" onclick="total_costo_produccion(this)"></td>
                  @if($view_detalle!='false')
                  <td></td>
                  @endif
                </tr>
                <tr>
                  <td colspan=3>Otros costos</td>
                  <td costo_otros><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $value->costo_otros }}" 
                                         onkeyup="total_costo_produccion(this)" onclick="total_costo_produccion(this)"></td>
                  @if($view_detalle!='false')
                  <td></td>
                  @endif
                </tr>
                <tr>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;" colspan=3>Costo Total (S/.)</th>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;" costo_total><input type="text" class="form-control" disabled value="{{ isset($value->costo_total)?$value->costo_total:'0.00' }}"></th>
                  @if($view_detalle!='false')
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;"></th>
                  @endif
                </tr>
              </tfoot>
            </table>
          </div>
        @endforeach
      </div>
      
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">4.2 VENTAS EN MAS DE UNA SEMANA (VENTAS CON FRECUENCIA MENSUAL)</span>
      </div>
      <div class="row">
        <div class="col-sm-9">
          <table class="table table-bordered" id="tabla-producto-mensual">
              <thead>
                <tr>
                  <th rowspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;">VENTA MUESTRA: De productos(de mayor rotación) que comercializa, produce o presta servicio</th>
                  <th rowspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;" width="100px">U. de Med.</th>
                  <th rowspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;">Cantidad</th>
                  <th rowspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;">P. de venta</th>
                  <th rowspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;">P. de Compra /Costo de Produc.</th>
                  <th colspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;text-align:center;">TOTAL (S/.)</th>
                  <th rowspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;">Marg. x Producto</th>
                  @if($view_detalle!='false')
                  <th rowspan=2 width="10px" style="background-color: #c8c8c8 !important;color: #000 !important;">
                    <a href="javascript:;" class="btn btn-success" onclick="agregar_producto_mensual()">
                      <i class="fa-solid fa-plus"></i>
                    </a>
                  </th>
                  @endif
                </tr>
                <tr>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;">VENTAS</th>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;">Costo: Vent./Prod.</th>
                </tr>
              </thead>
              <tbody num="0">
                @foreach($productos_mensual as $key => $value)
                  <tr id="{{ $value->id }}">
                    <td producto><input type="text" onkeyup="actualizarOpcionesSelect()" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" value="{{ $value->producto }}"></td>
                    <td unidadmedida>
                      <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                        <option></option>
                        @foreach($unidadmedida_credito as $unidad)
                          <option value="{{ $unidad->nombre }}" {{ $unidad->nombre == $value->unidadmedida ? "selected" : "" }}>{{ $unidad->nombre }}</option>
                        @endforeach
                      </select>
                      
                    </td>
                    <td cantidad><input type="text" valida_input_vacio onkeyup="calcula_subtotales_mensual(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $value->cantidad }}"></td>
                    <td precioventa><input type="text" valida_input_vacio onkeyup="calcula_subtotales_mensual(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $value->precioventa }}"></td>
                    <td preciocompra><input type="text" valida_input_vacio onkeyup="calcula_subtotales_mensual(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $value->preciocompra }}"></td>
                    <td subtotalventa><input type="number" step="any" class="form-control campo_moneda" disabled value="{{ $value->subtotalventa }}"></td>
                    <td subtotalcompra><input type="number" step="any" class="form-control campo_moneda" disabled value="{{ $value->subtotalcompra }}"></td>
                    <td margen>
                      <div class="input-group">
                        <input type="text" step="any" disabled class="form-control campo_moneda" value="{{ $value->margen }}">
                        <span class="input-group-text">%</span>
                      </div>
                    </td>
                    @if($view_detalle!='false')
                    <td><button type="button" onclick="eliminar_producto_mensual(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                    @endif
                    
                 </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <td colspan=5 align="right">TOTAL (S/.)</td>
                  <td><input type="number" class="form-control campo_moneda" disabled id="total_venta_mensual" value="{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->total_venta_mensual : '0.00' }}"></td>
                  <td><input type="number" class="form-control campo_moneda" disabled id="total_compra_mensual" value="{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->total_compra_mensual : '0.00' }}"></td>
                  <td></td>
                  <td></td>
                </tr>
                <tr>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;" colspan=5 align="right">Mg. de Venta</th>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;">
                    <div class="input-group">
                        <input type="text" step="any" disabled id="porcentaje_margen_mensual" class="form-control campo_moneda" value="{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->porcentaje_margen_mensual : '0.00' }}">
                        <span class="input-group-text">%</span>
                      </div>
                  </th>
                  <td style="background-color: #c8c8c8 !important;color: #000 !important;"></td>
                  <td style="background-color: #c8c8c8 !important;color: #000 !important;"></td>
                  <td style="background-color: #c8c8c8 !important;color: #000 !important;"></td>
                </tr>
              </tfoot>
          </table>
        </div>
        <div class="col-sm-3">
          <table class="table table-bordered mb-2">
            <thead>
                <tr>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;" colspan=2>CÁLCULO DE VENTAS</th>
                </tr>
                <tr>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;" width="100px">FRECUENCIA</th>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;" >
                    <input type="text" disabled class="form-control" value="MENSUAL">
                  </th>
                </tr>
              </thead>
          </table>
          <table class="table table-bordered" id="tabla-semanas">
            <thead>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" width="140px">Semanas</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;">Ventas</th>
              </tr>
            </thead>
            <tbody>
              @if(count($semanas) > 0)
                  @foreach($semanas as $value)
                    <tr>
                      <td semana>{{ $value->semana }}</td>
                      <td valor><input onkeyup="calcula_total_mes()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $value->valor }}"></td>
                    </tr>
                  @endforeach
                @else
                <tr>
                  <td semana>SEMANA 1</td>
                  <td valor><input onkeyup="calcula_total_mes()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
                </tr>
                <tr>
                  <td semana>SEMANA 2</td>
                  <td valor><input onkeyup="calcula_total_mes()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
                </tr>
                <tr>
                  <td semana>SEMANA 3</td>
                  <td valor><input onkeyup="calcula_total_mes()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
                </tr>
                <tr>
                  <td semana>SEMANA 4</td>
                  <td valor><input onkeyup="calcula_total_mes()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
                </tr>
                @endif
              
              <tr total>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;">Venta Mensual (S/.)</th>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;"><input type="text" id="venta_total_mensual" step="any" class="form-control campo_moneda" value="{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->venta_total_mensual : '0.00' }}" disabled></td>
              </tr>
            </tbody>
          </table>
          <table class="table table-bordered mb-2 mt-2">
            <thead>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" colspan="2">Estado de muestra de DATOS</th>
              </tr>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" colspan="2">
                  <input type="text" disabled id="estado_muestra_mensual" value="{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->estado_muestra_mensual : '' }}" 
                         class="form-control text-center"></th>
              </tr>
            </thead>
          </table>
          <table class="table table-bordered mb-2 mt-2">
            <thead>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" width="150px">Mg. De venta al mes(2) (S/.)</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" >
                  <input type="text" disabled id="margen_ventas_mensual" value="{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->margen_ventas_mensual : '0.00' }}" class="form-control campo_moneda"></th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
      @if($view_detalle!='false')
      <div class="row">
        <div class="col-sm-12 col-md-4">
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">PRODUCTO:</label>
            <div class="col-sm-8">
              <select class="form-control" id="producto_detalle_mensual">
                <option value=""></option>
              </select>
            </div>
          </div>
        </div>
      </div>
      @endif
      
      <div class="row mt-3" id="container-producto-secundario-mensual">
        @foreach($subproductomensual as $value)
          <div table-subproducto class="col-sm-12 col-md-4">
            <table class="table table-bordered m-2" id="table-producto-{{ $value->idtable }}" idproducto="{{ $value->idtable }}">
              <thead>
                <tr>
                  <th colspan="2">Costeo x unidad de medida (muestra)</th>
                  <th colspan="2" nombre_producto>{{ $value->nombre_producto }}</th>
                
                  @if($view_detalle!='false')
                  <th ><button class="btn btn-danger" type="button" onclick="eliminar_producto_secundario(this)"><i class="fa fa-trash"></i></button></th>
                  @endif
                </tr>
                <tr>  
                  <th>Materia prima (en U., Doc. Etc) M. Obra y otros</th>
                  <th width="70px">Cantidad</th>
                  <th width="70px">Costo x U., Doc. Etc.</th>
                  <th width="70px">Total (S/.)</th>
                  @if($view_detalle!='false')
                  <th width="10px"><button class="btn btn-success" type="button" onclick="agregar_subproducto(this)"><i class="fa fa-plus"></i></button></th>
                  @endif
                </tr>
              </thead>
              <tbody>
                @foreach($value->producto as $key => $items)
                <tr>
                  <td producto><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" value="{{ $items->producto }}"></td>
                  <td cantidad><input type="text" valida_input_vacio onkeyup="subtotal_subproducto(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $items->cantidad }}"></td>
                  <td costo><input type="text" valida_input_vacio onkeyup="subtotal_subproducto(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $items->costo }}"></td>
                  <td total><input type="text" class="form-control campo_moneda" value="{{ $items->total }}" disabled></td>
                  @if($view_detalle!='false')
                  <td><button class="btn btn-danger" {{ $key == 0 ? 'disabled' : '' }} type="button" onclick="remove_subproducto(this)"><i class="fa fa-trash"></i></button></td>
                  @endif
                </tr>
                @endforeach
                
              </tbody>
              <tfoot>
                <tr>
                  <td colspan=3>Costo de Materia Prima</td>
                  <td costo_materia_prima><input type="text" class="form-control campo_moneda" disabled value="{{ $value->costo_materia_prima }}"></td>
                  @if($view_detalle!='false')
                  <td></td>
                  @endif
                </tr>
                <tr>
                  <td colspan=3>Costo de mano de obra</td>
                  <td costo_mano_obra><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $value->costo_mano_obra }}" onkeyup="total_costo_produccion(this)" 
                                             onclick="total_costo_produccion(this)"></td>
                  @if($view_detalle!='false')
                  <td></td>
                  @endif
                </tr>
                <tr>
                  <td colspan=3>Otros costos</td>
                  <td costo_otros><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $value->costo_otros }}" onkeyup="total_costo_produccion(this)" 
                                         onclick="total_costo_produccion(this)"></td>
                  @if($view_detalle!='false')
                  <td></td>
                  @endif
                </tr>
                <tr>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;" colspan=3>Costo Total (S/.)</th>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;" costo_total><input type="text" class="form-control campo_moneda" disabled value="{{ isset($value->costo_total)?$value->costo_total:'0.00' }}"></th>

                  @if($view_detalle!='false')
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;"></th>
                  @endif
                </tr>
              </tfoot>
            </table>
          </div>
        @endforeach
      </div>
      <div class="row mt-1">
        @if($view_detalle!='false')
        <div class="col" style="flex: 0 0 0%;">
          <button type="submit" class="btn btn-success" id="boton_guardar"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS <b>({{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->cantidad_update : 0 }})</b></button>
        </div>
        @endif
        <div class="col" style="flex: 0 0 0%;">
          <button type="button" 
                  class="btn btn-dark" 
                  onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=solicitud_margen_ventas')}}', size: 'modal-fullscreen' })"
                  id="boton_imprimir"
                  >
            <i class="fa-solid fa-file-pdf"></i> IMPRIMIR</button>
        </div>
        <div class="col" style="flex: 1 0 0%;">
          <div id="success-message" class="alert alert-success d-none" style="text-align:left;"></div>
        </div>
        <div class="col" style="flex: 0 0 0%;">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i> SALIR</button>
        </div>
      </div>
</form> 
<style>
  .form-check-input:checked {
      background-color: #585858 !important;
      border-color: #585858 !important;
  }
</style>
<script>
 
valida_input_vacio();
  $('input').on('blur', function() {
      $('#boton_imprimir').attr('disabled',true);
  });
sistema_select2({ input:'#recabo_dato_dia' });
sistema_select2({ input:'#producto_detalle' });
sistema_select2({ input:'#producto_detalle_mensual' });
  
$("#recabo_dato_dia").on("change", function() {
  filtro_dia(this);
}).val('{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->recabo_dato_dia : 0 }}').trigger('change');

$("#producto_detalle").on("change", function(e) {
  
  var selectedOption = $(this).find("option:selected"); // Obtener la opción seleccionada
  var producto = selectedOption.attr("producto");
  var id = selectedOption.val();
  
  agregar_producto_secundario(producto, id);
})
$("#producto_detalle_mensual").on("change", function(e) {
  
  var selectedOption = $(this).find("option:selected"); // Obtener la opción seleccionada
  var producto = selectedOption.attr("producto");
  var id = selectedOption.val();
  
  agregar_producto_secundario(producto, id, 'mensual');
})
actualizarOpcionesSelect();
actualizarOpcionesSelectMensual();
function actualizarOpcionesSelect() {
  // Limpiar el select
  $("#producto_detalle").empty();
  // Obtener todos los td en el tbody
  $("#producto_detalle").append(`<option></option>`);
  $("#tabla-producto tbody tr td:first-child").each(function() {
      let id = $(this).closest('tr').attr('id');
      let producto = $(this).find('input').val();
      let disabled_option = $(`#container-producto-secundario > div > #table-producto-${id}`).length > 0 ? 'disabled' : '';
      $("#producto_detalle").append(`<option value="${id}" producto="${producto}" ${disabled_option}>${producto}</option>`);
  });
}
function actualizarOpcionesSelectMensual() {
  // Limpiar el select
  $("#producto_detalle_mensual").empty();
  // Obtener todos los td en el tbody
  $("#producto_detalle_mensual").append(`<option></option>`);
  $("#tabla-producto-mensual tbody tr td:first-child").each(function() {
      let id = $(this).closest('tr').attr('id');
      let producto = $(this).find('input').val();
      let disabled_option = $(`#container-producto-secundario-mensual > div > #table-producto-${id}`).length > 0 ? 'disabled' : '';
      $("#producto_detalle_mensual").append(`<option value="${id}" producto="${producto}" ${disabled_option}>${producto}</option>`);
  });
}
function agregar_producto_secundario(nombre_producto, id, table = ''){
  let producto = `<div table-subproducto class="col-sm-12 col-md-4">
                    <table class="table table-bordered m-2" id="table-producto-${id}" idproducto="${id}">
                      <thead>
                        <tr>
                          <th colspan="2"> Costeo x unidad de medida (muestra)</th>
                          <th colspan="2" nombre_producto>${nombre_producto}</th>
                          <th ><button class="btn btn-danger" type="button" onclick="eliminar_producto_secundario(this)"><i class="fa fa-trash"></i></button></th>
      
                        </tr>
                        <tr>  
                          <th>Materia prima (en U., Doc. Etc) M. Obra y otros</th>
                          <th width="70px">Cantidad</th>
                          <th width="70px">Costo x U., Doc. Etc.</th>
                          <th width="70px">Total (S/.)</th>
                          <th width="10px">
                            <button class="btn btn-success" type="button" onclick="agregar_subproducto(this)"><i class="fa fa-plus"></i></button>
                          </th>
      
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td producto><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto"></td>
                          <td cantidad><input type="text" valida_input_vacio onkeyup="subtotal_subproducto(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
                          <td costo><input type="text" valida_input_vacio onkeyup="subtotal_subproducto(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
                          <td total><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00" disabled></td>
                          <td><button class="btn btn-danger" disabled type="button" onclick="remove_subproducto(this)"><i class="fa fa-trash"></i></button></td>
                        </tr>
                      </tbody>
                      <tfoot>
                        <tr>
                          <td colspan=3>Costo de Materia Prima</td>
                          <td costo_materia_prima><input type="text" class="form-control campo_moneda" disabled value="0.00"></td>
                          <td></td>
                        </tr>
                        <tr>
                          <td colspan=3>Costo de mano de obra</td>
                          <td costo_mano_obra><input type="text" valida_input_vacio class="form-control campo_moneda color_cajatexto" value="0.00" onkeyup="total_costo_produccion(this)" onclick="total_costo_produccion(this)"></td>
                          <td></td>
                        </tr>
                        <tr>
                          <td colspan=3>Otros costos</td>
                          <td costo_otros><input type="text" valida_input_vacio class="form-control campo_moneda color_cajatexto" value="0.00" onkeyup="total_costo_produccion(this)" onclick="total_costo_produccion(this)"></td>
                          <td></td>
                        </tr>
                        <tr>
                          <th style="background-color: #c8c8c8 !important;color: #000 !important;" colspan=3>Costo Total (S/.)</th>
                          <th style="background-color: #c8c8c8 !important;color: #000 !important;" costo_total><input type="text" class="form-control campo_moneda" disabled value="0.00"></th>
                          <th style="background-color: #c8c8c8 !important;color: #000 !important;"></th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>`;
  if(table == 'mensual'){
       
    $('#container-producto-secundario-mensual').append(producto);
  }else{
       
    $('#container-producto-secundario').append(producto);
  }
  actualizarOpcionesSelect();
  actualizarOpcionesSelectMensual();
  valida_input_vacio();
}
function eliminar_producto_secundario(e){
  $(e).closest('div[table-subproducto]').remove();
  actualizarOpcionesSelect();
  actualizarOpcionesSelectMensual();
}
function agregar_subproducto(e){
  let idtable = $(e).closest('table').attr('id');
  let tr = `<tr>
              <td producto><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto"></td>
              <td cantidad><input type="text" valida_input_vacio onkeyup="subtotal_subproducto(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
              <td costo><input type="text" valida_input_vacio onkeyup="subtotal_subproducto(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
              <td total><input type="text" class="form-control campo_moneda" value="0.00" disabled></td>
              <td><button class="btn btn-danger" type="button" onclick="remove_subproducto(this)"><i class="fa fa-trash"></i></button></td>
            </tr>`;
  $(`#${idtable} > tbody`).append(tr);
  valida_input_vacio();
}
function remove_subproducto(e){
  let idtable = $(e).closest('table').attr('id');
  $(e).closest('tr').remove();
  total_materia_prima(idtable)
}
function subtotal_subproducto(e){
  let idtable = $(e).closest('table').attr('id');
  let path = $(e).closest('tr');
  let cantidad = parseFloat($(path).find('td[cantidad] input').val());
  let costo = parseFloat($(path).find('td[costo] input').val());
  let subtotalventa = cantidad * costo;
  $(path).find('td[total] input').val(subtotalventa.toFixed(2));
  total_materia_prima(idtable);
}
function total_materia_prima(idtable){
  
  let total_venta = 0;
  $(`#${idtable} > tbody > tr`).each(function() {
    let subtotalventa = parseFloat($(this).find('td[total] input').val());
    total_venta += subtotalventa;
  });
  $(`#${idtable} > tfoot`).find('td[costo_materia_prima] input').val(total_venta.toFixed(2));
  $(`#${idtable} > tfoot`).find('td[costo_materia_prima] input').click;
    total_costo_produccion($(`#${idtable} > tfoot > tr`));
  
}
function total_costo_produccion(e){
  
  let path = $(e).closest('tfoot');
  
  let costo_materia_prima = parseFloat($(path).find('td[costo_materia_prima] input').val());
  let costo_mano_obra = parseFloat($(path).find('td[costo_mano_obra] input').val());
  let costo_otros = parseFloat($(path).find('td[costo_otros] input').val());
  let total = costo_materia_prima + costo_mano_obra + costo_otros;
  $(path).find('th[costo_total] input').val(total.toFixed(2))
}
  
function filtro_dia(e){
  let diaSeleccionado = $(e).val();

  let fila = $("#tabla-dias tbody tr").filter(function() {
    return $(this).find('td[dia]').text() === diaSeleccionado;
  });
  let numero = fila.find('td[numero]').text();
  let ventas = fila.find('td[valor] input').val();
  $('#recabo_dato_numero').val(numero);
  $('#recabo_dato_monto').val(ventas);
  calcular_estado_muestra();
  calcular_margen_ventas()
  
}
  
function agregar_producto(table = ''){
 
  let btn_eliminar = `<button type="button" onclick="eliminar_producto(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button>` ;
  let option_select = ``;
  @foreach($unidadmedida_credito as $value)
    option_select += `<option value="{{ $value->nombre }}">{{ $value->nombre }}</option>`
  @endforeach
  let id = generarIDUnico();
  let tabla = `<tr id="${id}">
                <td producto><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" onkeyup="actualizarOpcionesSelect()"></td>
                <td unidadmedida>
                  <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                    <option></option>
                    ${option_select}
                  </select>
                </td>
                <td cantidad><input type="text" valida_input_vacio onkeyup="calcula_subtotales(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
                <td precioventa><input type="text" valida_input_vacio onkeyup="calcula_subtotales(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
                <td preciocompra><input type="text" valida_input_vacio onkeyup="calcula_subtotales(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
                <td subtotalventa><input type="number" step="any" class="form-control campo_moneda" disabled value="0.00"></td>
                <td subtotalcompra><input type="number" step="any" class="form-control campo_moneda" disabled value="0.00"></td>
                <td margen>
                  <div class="input-group">
                    <input type="text" step="any" disabled class="form-control campo_moneda" value="0">
                    <span class="input-group-text">%</span>
                  </div>
                </td>
                <td>${btn_eliminar}</td>
               </tr>`;
  
    $("#tabla-producto > tbody").append(tabla);
    actualizarOpcionesSelect();
    valida_input_vacio();
 
  
}
function agregar_producto_mensual(table = ''){
 
  let btn_eliminar = `<button type="button" onclick="eliminar_producto_mensual(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button>` ;
  let option_select = ``;
  @foreach($unidadmedida_credito as $value)
    option_select += `<option value="{{ $value->nombre }}">{{ $value->nombre }}</option>`
  @endforeach
  let id = generarIDUnico();
  let tabla = `<tr id="${id}">
                <td producto><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" onkeyup="actualizarOpcionesSelectMensual()"></td>
                <td unidadmedida>
                  <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                    <option></option>
                    ${option_select}
                  </select>
                </td>
                <td cantidad><input type="text" valida_input_vacio onkeyup="calcula_subtotales_mensual(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
                <td precioventa><input type="text" valida_input_vacio onkeyup="calcula_subtotales_mensual(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
                <td preciocompra><input type="text" valida_input_vacio onkeyup="calcula_subtotales_mensual(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="0.00"></td>
                <td subtotalventa><input type="number" step="any" class="form-control campo_moneda" disabled value="0.00"></td>
                <td subtotalcompra><input type="number" step="any" class="form-control campo_moneda" disabled value="0.00"></td>
                <td margen>
                  <div class="input-group">
                    <input type="text" step="any" disabled class="form-control campo_moneda" value="0">
                    <span class="input-group-text">%</span>
                  </div>
                </td>
                <td>${btn_eliminar}</td>
               </tr>`;
  
    $("#tabla-producto-mensual > tbody").append(tabla);
    actualizarOpcionesSelectMensual();
    valida_input_vacio();
  
}
function eliminar_producto(e){
  let path = $(e).closest('tr');
  let idproducto = path.attr('id');
  $(`#table-producto-${idproducto}`).closest('div[table-subproducto]').remove()
  path.remove();
  calcula_total();
  actualizarOpcionesSelect();
}
function eliminar_producto_mensual(e){
  let path = $(e).closest('tr');
  let idproducto = path.attr('id');
  $(`#table-producto-${idproducto}`).closest('div[table-subproducto]').remove()
  path.remove();
  calcula_total_mensual();
  actualizarOpcionesSelectMensual();
}
function calcula_subtotales(e){
  let path = $(e).closest('tr');
  let cantidad = parseFloat($(path).find('td[cantidad] input').val());
  let precioventa = parseFloat($(path).find('td[precioventa] input').val());
  let preciocompra = parseFloat($(path).find('td[preciocompra] input').val());
  let subtotalventa = cantidad * precioventa;
  let subtotalcompra = cantidad * preciocompra;
  $(path).find('td[subtotalventa] input').val(subtotalventa.toFixed(2));
  $(path).find('td[subtotalcompra] input').val(subtotalcompra.toFixed(2));
  let margen = ((subtotalventa - subtotalcompra) / subtotalventa) * 100;
  $(path).find('td[margen] input').val(margen.toFixed(2));
  // Calculo de Margen
  calcula_total();
}
function calcula_subtotales_mensual(e){
  let path = $(e).closest('tr');
  let cantidad = parseFloat($(path).find('td[cantidad] input').val());
  let precioventa = parseFloat($(path).find('td[precioventa] input').val());
  let preciocompra = parseFloat($(path).find('td[preciocompra] input').val());
  let subtotalventa = cantidad * precioventa;
  let subtotalcompra = cantidad * preciocompra;
  $(path).find('td[subtotalventa] input').val(subtotalventa.toFixed(2));
  $(path).find('td[subtotalcompra] input').val(subtotalcompra.toFixed(2));
  let margen = ((subtotalventa - subtotalcompra) / subtotalventa) * 100;
  $(path).find('td[margen] input').val(margen.toFixed(2));
  // Calculo de Margen
  calcula_total_mensual();
}
function calcula_total(){
  let total_venta = 0;
  let total_compra = 0;
  $("#tabla-producto > tbody > tr").each(function() {
    let subtotalventa = parseFloat($(this).find('td[subtotalventa] input').val());
    let subtotalcompra = parseFloat($(this).find('td[subtotalcompra] input').val());
    total_venta += subtotalventa;
    total_compra += subtotalcompra;
  });
  $('#total_venta').val(total_venta.toFixed(2))
  $('#total_compra').val(total_compra.toFixed(2))
  
  let margen = ( (total_venta-total_compra) / total_venta ) * 100;
  if(isNaN(margen)){
    margen = 0;
  }
  $('#porcentaje_margen').val(margen.toFixed(2));
  calcular_estado_muestra();
  calcular_margen_ventas();
}
function calcula_total_mensual(){
  let total_venta = 0;
  let total_compra = 0;
  $("#tabla-producto-mensual > tbody > tr").each(function() {
    let subtotalventa = parseFloat($(this).find('td[subtotalventa] input').val());
    let subtotalcompra = parseFloat($(this).find('td[subtotalcompra] input').val());
    total_venta += subtotalventa;
    total_compra += subtotalcompra;
  });
  $('#total_venta_mensual').val(total_venta.toFixed(2))
  $('#total_compra_mensual').val(total_compra.toFixed(2))
  
  let margen = ( (total_venta-total_compra) / total_venta ) * 100;
  if(isNaN(margen)){
    margen = 0;
  }
  $('#porcentaje_margen_mensual').val(margen.toFixed(2));
  calcular_estado_muestra_mensual();
  calcular_margen_ventas_mensual();
}
function calcula_total_dia() {
  let total = 0;
  let numero_dias = 0;
  $("#tabla-dias tbody tr:not([total])").each(function() {
    let dia = parseFloat($(this).find('td input').val());
    numero_dias += dia <= 0 ? 0 : 1;
    total += dia;
  });
  let frecuencia = 'SEMANAL';
  if( numero_dias >= 5 ){
    frecuencia = 'DIARIO';
  }
  $('#numero_dias').val(numero_dias);
  $('#frecuencia_ventas').val(frecuencia);
  $('#venta_total_dias').val(total.toFixed(2));
  
  let total_venta_mensual = 0;
  if(numero_dias < 5){
     total_venta_mensual = total * 4;
  }
  else if(numero_dias == 5){
    total_venta_mensual = (total/5)*22 ;
  }
  else if(numero_dias == 6){
    total_venta_mensual = (total/6)*26 ;
  }
  else if(numero_dias == 7){
    total_venta_mensual = (total/7)*26 ;
  }
  $('#venta_mensual').val(total_venta_mensual.toFixed(2));
  
  
  calcular_estado_muestra();
  calcular_margen_ventas();
  filtro_dia($("#recabo_dato_dia"));
}
function calcula_total_mes() {
  let total = 0;
  $("#tabla-semanas tbody tr:not([total])").each(function() {
    let mes = parseFloat($(this).find('td input').val());
    total += mes;
  });
 
  $('#venta_total_mensual').val(total.toFixed(2));
  
  calcular_estado_muestra_mensual();
  calcular_margen_ventas_mensual();
}
function calcular_margen_ventas(){
  
  let porcentaje_margen = parseFloat($('#porcentaje_margen').val());
  let venta_mensual = parseFloat($('#venta_mensual').val());
  let margen = ( porcentaje_margen/100 ) * venta_mensual;
  $('#margen_ventas').val(margen.toFixed(2));
  calcular_porcentaje_margen_venta()
}
calcular_estado_muestra_mensual();
function calcular_margen_ventas_mensual(){
  
  let porcentaje_margen = parseFloat($('#porcentaje_margen_mensual').val());
  let venta_mensual = parseFloat($('#venta_total_mensual').val());
  let margen = ( porcentaje_margen/100 ) * venta_mensual;
  $('#margen_ventas_mensual').val(margen.toFixed(2));
  calcular_porcentaje_margen_venta();
  
}
  
function calcular_estado_muestra(){
  let total_venta_producto = parseFloat($('#total_venta').val());
  let recabo_dato_monto = parseFloat($('#recabo_dato_monto').val());
  let porcentaje_muestra = "{{ configuracion($tienda->id,'porcentaje_min_muestra')['valor'] }}";
  porcentaje_muestra = parseFloat(porcentaje_muestra)/100
  let text_muestra = "Muestra Insuficiente";
  let color_alerta = "bg-danger";
  let estado_muestra = "ERROR";
  if(total_venta_producto <= recabo_dato_monto){
    
    if( (total_venta_producto >= (porcentaje_muestra * recabo_dato_monto)) && (recabo_dato_monto > 0)){
      text_muestra = "Muestra Adecuada Continuar";
      color_alerta = "bg-success";
      estado_muestra = "CORRECTO";
    }
  }
  else if(total_venta_producto > (porcentaje_muestra * recabo_dato_monto)){
     text_muestra = "Muestra Superior al Máximo";      
  }
  
  $('#estado_muestra').attr('estado',estado_muestra);
  $('#estado_muestra').removeClass('bg-danger');
  $('#estado_muestra').removeClass('bg-success');
  $('#estado_muestra').val(text_muestra);
  $('#estado_muestra').addClass(color_alerta);
  determina_estado_button_save();
}
function calcular_estado_muestra_mensual(){
  let venta_total_mensual = parseFloat($('#venta_total_mensual').val());
  let total_venta_mensual = parseFloat($('#total_venta_mensual').val());
  let porcentaje_muestra = "{{ configuracion($tienda->id,'porcentaje_min_muestra')['valor'] }}";
  porcentaje_muestra = parseFloat(porcentaje_muestra)/100
  let text_muestra = "Muestra Insuficiente";
  let color_alerta = "bg-danger";
   let estado_muestra = "ERROR";
  if(total_venta_mensual <= venta_total_mensual){
    
    if( (total_venta_mensual >= (porcentaje_muestra * venta_total_mensual))){
      text_muestra = "Muestra Adecuada Continuar";
      color_alerta = "bg-success";
      estado_muestra = "CORRECTO";
    }
  }
  else if(total_venta_mensual > (porcentaje_muestra * venta_total_mensual)){
     text_muestra = "Muestra Superior al Máximo";      
  }
  $('#estado_muestra_mensual').attr('estado',estado_muestra);
  $('#estado_muestra_mensual').removeClass('bg-danger');
  $('#estado_muestra_mensual').removeClass('bg-success');
  $('#estado_muestra_mensual').val(text_muestra);
  $('#estado_muestra_mensual').addClass(color_alerta);
  determina_estado_button_save();
}
  determina_estado_button_save();
function determina_estado_button_save(){
  let estado_muestra = $('#estado_muestra').attr('estado');
  let estado_muestra_mensual = $('#estado_muestra_mensual').attr('estado');
  $('#button_save').attr('disabled',false)
  $('#button_save').html(`<i class="fa-solid fa-floppy-disk"></i> Guardar Cambios`)
  $('#button_save').removeClass('btn-danger')
  $('#button_save').addClass('btn-primary')
  if(estado_muestra == 'ERROR' || estado_muestra_mensual == "ERROR"){
    $('#button_save').attr('disabled',true)
    $('#button_save').removeClass('btn-primary')
    $('#button_save').addClass('btn-danger')
    $('#button_save').html(`Muestra Invalida`)
  }
}
function calcular_porcentaje_margen_venta(){
  let venta_mensual = parseFloat($('#venta_mensual').val());
  let venta_total_mensual = parseFloat($('#venta_total_mensual').val());

  let margen_ventas = parseFloat($('#margen_ventas').val());
  let margen_ventas_mensual = parseFloat($('#margen_ventas_mensual').val());

  let evaluacion_actual_ganancia_ventamensual = venta_mensual + venta_total_mensual;
  let evaluacion_actual_ganancia_utilidad_bruta = margen_ventas + margen_ventas_mensual;

  let margen_venta_calculado = (evaluacion_actual_ganancia_utilidad_bruta/evaluacion_actual_ganancia_ventamensual)*100;
  $('#margen_venta_calculado').val(margen_venta_calculado.toFixed(2));
  valida_margen_venta();

}
valida_margen_venta();
function valida_margen_venta(){
  let margen_venta_calculado = parseFloat($('#margen_venta_calculado').val());
  let margen_venta_giro_economico = parseFloat($('#margen_venta_giro_economico').val());

  if(margen_venta_calculado > margen_venta_giro_economico){
     $('#error_margen_venta').removeClass('d-none')
     $('#btn-save-cuantitativa').attr('disabled',true)
     $('#estado_error_margen_venta').val('ERROR')
     $('#boton_guardar').attr('disabled',true)

  }else{
      $('#error_margen_venta').addClass('d-none')
      $('#btn-save-cuantitativa').attr('disabled',false)
     $('#estado_error_margen_venta').val('CORRECTO')
     $('#boton_guardar').attr('disabled',false)
  }
}
function json_productos(){
  let data = [];
  $("#tabla-producto > tbody > tr").each(function() {
      let id              = $(this).attr('id');
      let producto        = $(this).find('td[producto] input').val();
      let unidadmedida    = $(this).find('td[unidadmedida] select').val();
      let cantidad        = $(this).find('td[cantidad] input').val();
      let precioventa     = $(this).find('td[precioventa] input').val();
      let preciocompra    = $(this).find('td[preciocompra] input').val();
      let subtotalventa   = $(this).find('td[subtotalventa] input').val();
      let subtotalcompra  = $(this).find('td[subtotalcompra] input').val();
      let margen          = $(this).find('td[margen] input').val();
    
      data.push({ 
          id: id,
          producto: producto,
          unidadmedida: unidadmedida,
          cantidad: cantidad,
          precioventa: precioventa,
          preciocompra: preciocompra,
          subtotalventa: subtotalventa,
          subtotalcompra: subtotalcompra,
          margen: margen,
      });
  });
  return JSON.stringify(data);
}
function json_productos_mensual(){
  let data = [];
  $("#tabla-producto-mensual > tbody > tr").each(function() {
      let id              = $(this).attr('id');
      let producto        = $(this).find('td[producto] input').val();
      let unidadmedida    = $(this).find('td[unidadmedida] select').val();
      let cantidad        = $(this).find('td[cantidad] input').val();
      let precioventa     = $(this).find('td[precioventa] input').val();
      let preciocompra    = $(this).find('td[preciocompra] input').val();
      let subtotalventa   = $(this).find('td[subtotalventa] input').val();
      let subtotalcompra  = $(this).find('td[subtotalcompra] input').val();
      let margen          = $(this).find('td[margen] input').val();
    
      data.push({ 
          id: id,
          producto: producto,
          unidadmedida: unidadmedida,
          cantidad: cantidad,
          precioventa: precioventa,
          preciocompra: preciocompra,
          subtotalventa: subtotalventa,
          subtotalcompra: subtotalcompra,
          margen: margen,
      });
  });
  return JSON.stringify(data);
}
  
function json_dias(){
  let data = [];
  $("#tabla-dias tbody tr:not([total])").each(function() {
      let numero  = $(this).find('td[numero]').text();
      let dia     = $(this).find('td[dia]').text();
      let valor   = parseFloat($(this).find('td[valor] input').val());
      data.push({ 
          numero: numero,
          dia: dia,
          valor: valor,
      });
  });
  return JSON.stringify(data);
}
function json_semanas(){
  let data = [];
  $("#tabla-semanas tbody tr:not([total])").each(function() {
      let numero  = $(this).find('td[numero]').text();
      let semana     = $(this).find('td[semana]').text();
      let valor   = parseFloat($(this).find('td[valor] input').val());
      data.push({ 
          numero: numero,
          semana: semana,
          valor: valor,
      });
  });
  return JSON.stringify(data);
}
  
function json_subproducto(table = ''){
  let data = [];
  let idtable = table == '' ? 'container-producto-secundario' : 'container-producto-secundario-'+table;
  $(`#${idtable} > div[table-subproducto]`).each(function() {
      let idtable             = $(this).find('table').attr('idproducto');
      let nombre_producto     = $(this).find('table thead tr th[nombre_producto]').text();
      let costo_materia_prima = $(this).find('table tfoot tr td[costo_materia_prima] input').val();
      let costo_mano_obra     = $(this).find('table tfoot tr td[costo_mano_obra] input').val();
      let costo_otros         = $(this).find('table tfoot tr td[costo_otros] input').val();
      let costo_total         = $(this).find('table tfoot tr th[costo_total] input').val();
      let producto_list = [];
      $(this).find(`table > tbody > tr`).each(function() {
        
        let producto  = $(this).find('td[producto] input').val();
        let cantidad  = $(this).find('td[cantidad] input').val();
        let costo     = $(this).find('td[costo] input').val();
        let total     = $(this).find('td[total] input').val();
        producto_list.push({ 
            producto: producto,
            cantidad: cantidad,
            costo: costo,
            total: total,
        });
      });
    
      data.push({ 
          idtable: idtable,
          nombre_producto: nombre_producto,
          costo_materia_prima: costo_materia_prima,
          costo_mano_obra: costo_mano_obra,
          costo_otros: costo_otros,
          costo_total: costo_total,
          producto: producto_list
      });
     
  });
  return JSON.stringify(data);
}

  
  
function generarIDUnico() {
    // Generar un ID único utilizando un timestamp y un número aleatorio
    var timestamp = new Date().getTime();
    var numeroAleatorio = Math.floor(Math.random() * 1000);
    var idUnico = "id" + timestamp + numeroAleatorio;
    return idUnico;
}
</script>    