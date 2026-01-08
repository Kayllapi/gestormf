
<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/credito/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'ingresos_adicionales',
              evaluacion_meses : json_evaluacion_meses(),
              productos: json_productos(),
              productos_mensual: json_productos_mensual(),
              dias: json_dias(),
              semanas: json_semanas(),
              subproducto: json_subproducto(),
              subproductomensual: json_subproducto('mensual'),
              inventario: json_productos_inventario('inventario-producto'),
              inmuebles: json_productos_inventario('activos-inmuebles'),
              muebles: json_productos_inventario('activos-muebles'),
                
              balance_general: json_balance(),
              ganancias_perdidas: json_ganancias_perdidas(),
              adicional_fijo: json_adicional_fijo(),
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
      $venta_mensual = $credito_cuantitativa_margen_venta ? ($credito_cuantitativa_margen_venta->venta_mensual + $credito_cuantitativa_margen_venta->venta_total_mensual) : '0.00';
  
      $evaluacion_meses = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->evaluacion_meses == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->evaluacion_meses) ) : [];
      $productos = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->productos == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->productos) ) : [];
      $productos_mensual = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->productos_mensual == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->productos_mensual) ) : [];
      $dias = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->dias == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->dias) ) : [];
      $semanas = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->semanas == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->semanas) ) : [];
      $subproducto = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->subproducto == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->subproducto) ) : [];
      $subproductomensual = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->subproductomensual == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->subproductomensual) ) : [];
    
      $inventario = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->inventario == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->inventario) ) : [];
      $inmuebles = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->inmuebles == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->inmuebles) ) : [];
      $muebles = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->muebles == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->muebles) ) : [];
  
      $resumen = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->balance_general == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->balance_general) ) : [];
      $ganancia_perdida = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->ganancias_perdidas == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->ganancias_perdidas) ) : [];
  
      $adicional_fijo = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->adicional_fijo == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->adicional_fijo) ) : [];
    @endphp
  
    <div class="modal-header" style="border-bottom: 0;">
        <h5 class="modal-title">ING ADIC-MES Y FIJOS </h5>
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
          @if($users_prestamo->dni_pareja!='' or $users_prestamo->nombrecompleto_pareja!='')
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">PAREJA:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $users_prestamo->nombrecompleto_pareja }}" disabled>
            </div>
          </div>
          @endif
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">GIRO ECONÓMICO:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->nombregiro_economico_evaluacion : '' }}" disabled>
            </div>
          </div>
          
          <div class="row" style="    background-color: #aaaaaa;
    padding: 5px;">
            <label class="col-sm-4 col-form-label" style="text-align: right;">TIPO GIRO ECONÓMICO ADICIONAL:</label>
            <div class="col-sm-8">
              <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" id="idtipo_giro_economico_adiccional">
                @foreach($tipo_giro_economico as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
              </select>
              
            </div>
          </div>
          <div class="row" style="background-color: #aaaaaa;">
            <label class="col-sm-4 col-form-label" style="text-align: right;">GIRO ECONÓMICO ADICIONAL:</label>
            <div class="col-sm-8">
              <select class="form-control color_cajatexto" id="idgiro_economico_evaluacion_adicional" disabled>
               <option value=""></option>
              </select>
              
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-3">
          <div class="row">
            <label class="col-sm-3 col-form-label" style="text-align: right;">FECHA:</label>
            <div class="col-sm-7">
              <input type="date" step="any" class="form-control" value="{{ $credito_cuantitativa_ingreso_adicional!=''?date_format(date_create($credito_cuantitativa_ingreso_adicional->fecha),'Y-m-d'):0 }}" disabled>
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
        <span class="badge d-block">VII. EVALUACIÓN DE INGRESO ADICIONAL INDEPENDIENTE:</span>
      </div>
      
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">7.1 EVALUACIÓN ECONÓMICA FINANCIERA:</span>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">7.1.1 CICLO DEL NEGOCIO: (Actual =100%, Alta > 100%, Baja < 100%) </span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <table class="table table-bordered" id="table-evaluacion-meses">
            <thead>
              <tr>
                <th rowspan=2 width="100px">MESES</th>
              </tr>
              <tr>
              </tr>
            </thead>
            <tbody>
            @if(count($evaluacion_meses) > 0)
              @foreach ($evaluacion_meses as $row)
                <tr>
                    <td>{{ $row->Mes }}</td>
                    @foreach ($row as $header => $cellData)
                        @if ($header !== 'Mes')
                            @php
                                $value = $cellData->value;
                                $disabled = $cellData->disabled ? 'disabled' : ($view_detalle=='false' ? 'disabled' : '');
                                $color_cajatexto = $cellData->disabled ?  : ($view_detalle=='false' ? '' : 'color_cajatexto');
                            @endphp
                            <td><input type='number' valida_input_vacio class='form-control campo_moneda {{$color_cajatexto}}' onkeyup="calcula_monto_meses(this)" value='{{ $value }}' {{ $disabled }}></td>
                        @endif
                    @endforeach
                </tr>
              @endforeach
            @else 
              <tr>
                <td>Ventas (S/.)</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $venta_mensual }}" disabled></td>
                <td><input type="text" class="form-control campo_moneda" value="0.00" disabled></td>
                <td><input type="text" class="form-control campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00" disabled></td>
                <td><input type="text" class="form-control campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00" disabled></td>
                <td><input type="text" class="form-control campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00" disabled></td>
                <td><input type="text" class="form-control campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00" disabled></td>
                <td><input type="text" class="form-control campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00" disabled></td>
                <td><input type="text" class="form-control campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00" disabled></td>
                <td><input type="text" class="form-control campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00" disabled></td>
                <td><input type="text" class="form-control campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00" disabled></td>
                <td><input type="text" class="form-control campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00" disabled></td>
                <td><input type="text" class="form-control campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00" disabled></td>
              </tr>
              <tr>
                <td>% Ventas</td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" onkeyup="calcula_monto_meses(this)" value="100" disabled></td>
                <td><input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
              </tr>
            @endif
            </tbody>
          </table>
          <div class="row mt-2">
            <label class="col-sm-4 col-form-label" style="text-align: right;">MARGEN DE VENTAS TOTAL CALCULADO:</label>
            <div class="col-sm-1">
              <input type="text" step="any" class="form-control" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->margen_venta_calculado : '0.00' }}" id="margen_venta_calculado" disabled>
            </div>
            <div class="col-sm-1" style="display:none;">
              <input type="text" step="any" class="form-control" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->margen_giro_economico : '0.00'}}" id="margen_venta_giro_economico" disabled>
            </div>
            <input type="hidden" id="estado_error_margen_venta">
            <div id="error_margen_venta" class="col-sm-12 alert alert-danger mt-2 d-none" 
                 style="background-color: #ff6666;border-color: #ff6666;color: #000;font-weight: bold;">
              EL MARGEN DE VENTA CALCULADO NO PUEDE SER SUPERIOR AL DEL GIRO ECONÓMICO ({{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->margen_giro_economico : '0.00'}}%)</div>
          </div>
        </div>  
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">7.1.2 ESTADOS FINANCIEROS</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-6">
          <table class="table table-bordered" id="table-balance-general">
            <thead>
              <tr>
                <th colspan=2>BALANCE GENERAL</th>
                <th width="100px">Soles (S/. )</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan=2>Caja</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('balance_caja', $resumen) }}" id="balance_caja"></td>
              </tr>
              <tr>
                <td colspan=2>Bancos</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('balance_bancos', $resumen) }}" id="balance_bancos"></td>
              </tr>
              <tr>
                <td colspan=2>Cuentas por cobrar a clientes</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('balance_cuentas_cobrar', $resumen) }}" id="balance_cuentas_cobrar"></td>
              </tr>
              <tr>
                <td colspan=2>Adelanto a proveedores</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('balance_adelanto_proveedor', $resumen) }}" id="balance_adelanto_proveedor"></td>
              </tr>
              <tr>
                <td colspan=2>Inventarios</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('balance_inventario', $resumen) }}" disabled id="balance_inventario"></td>
              </tr>
              <tr>
                <td colspan=2><b>ACTIVO CORRIENTE</b></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('balance_activo_corriente', $resumen) }}" disabled id="balance_activo_corriente"></td>
              </tr>
              <tr>
                <td colspan=2>Activo inmueble</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('balance_activo_inmueble', $resumen) }}" disabled id="balance_activo_inmueble"></td>
              </tr>
              <tr>
                <td colspan=2>Activo mueble</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('balance_activo_mueble', $resumen) }}" disabled id="balance_activo_mueble"></td>
              </tr>
              <tr>
                <td colspan=2><b>ACTIVO NO CORRIENTE</b></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('balance_activo_nocorriente', $resumen) }}" disabled id="balance_activo_nocorriente"></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;
                color: #000 !important;" colspan=2><b>TOTAL ACTIVO</b></td>
                <td style="background-color: #c8c8c8 !important;
                color: #000 !important;"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('balance_total_activo', $resumen) }}" disabled id="balance_total_activo"></td>
              </tr>
              <tr>
                <td colspan=2>Cuentas por pagar a proveedores</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('balance_cuentas_pagar', $resumen) }}" id="balance_cuentas_pagar"></td>
              </tr>
              <tr>
                <td rowspan=2>Pasivos financieros a corto plazo</td>
                <td >E. Reguladas</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('balance_corto_reguladas', $resumen) }}" id="balance_corto_reguladas"></td>
              </tr>
              <tr>
                <td > E. No Reguladas</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('balance_corto_noreguladas', $resumen) }}" id="balance_corto_noreguladas"></td>
              </tr>
              <tr>
                <td colspan=2>Impuestos por pagar</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('balance_impuesto', $resumen) }}" id="balance_impuesto"></td>
              </tr>
              <tr>
                <td colspan=2>Otras cuentas por pagar</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('balance_otras_cuentas', $resumen) }}" id="balance_otras_cuentas"></td>
              </tr>
               <tr>
                <td colspan=2><b>PASIVO CORRIENTE</b></td>
                <td><input type="text" class="form-control campo_moneda"  disabled value="{{ encontrar_valor('balance_pasivo_corriente', $resumen) }}" id="balance_pasivo_corriente"></td>
              </tr>
              <tr>
                <td rowspan=2>Pasivo Fin. a Largo.Plazo </td>
                <td>E. Reguladas</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('balance_largo_reguladas', $resumen) }}" id="balance_largo_reguladas"></td>
              </tr>
              <tr>
                <td>E. No Reguladas</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('balance_largo_noreguladas', $resumen) }}" id="balance_largo_noreguladas"></td>
              </tr>
              <tr>
                <td colspan=2><b>PASIVO NO CORRIENTE</b></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('balance_pasivo_nocorriente', $resumen) }}" disabled id="balance_pasivo_nocorriente"></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;
                color: #000 !important;" colspan=2><b>TOTAL PASIVO</b></td>
                <td style="background-color: #c8c8c8 !important;
                color: #000 !important;"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('balance_total_pasivo', $resumen) }}" disabled id="balance_total_pasivo"></td>
              </tr>
              <tr>
                <td colspan=2>Capital social</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('balance_capital_social', $resumen) }}" disabled id="balance_capital_social"></td>
              </tr>
              <tr>
                <td colspan=2>Utilidades acumuladas</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('balance_utilidad_acumulada', $resumen) }}" disabled id="balance_utilidad_acumulada"></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;
                color: #000 !important;" colspan=2><b>TOTAL PATRIMONIO</b></td>
                <td style="background-color: #c8c8c8 !important;
                color: #000 !important;"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('balance_total_patrimonio', $resumen) }}" disabled id="balance_total_patrimonio"></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;
                color: #000 !important;" colspan=2><b>TOTAL PASIVO + PATRIMONIO</b></td>
                <td style="background-color: #c8c8c8 !important;
                color: #000 !important;"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('balance_pasivo_patrimonio', $resumen) }}" disabled id="balance_pasivo_patrimonio"></td>
              </tr>
              
              
              
              
            </tbody>
          </table>
        </div>
        <div class="col-sm-12 col-md-6">
          <table class="table table-bordered" id="table-estado-ganancias-perdidas">
            <thead>
              <tr>
                <th>ESTADO DE GANANCIAS Y PERDIDAS</th>
                <th width="100px">Soles (S/. )</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><b>VENTAS MENSUALES</b></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('ganancias_venta_mensual', $ganancia_perdida) }}" disabled id="ganancias_venta_mensual"></td>
              </tr>
              <tr>
                <td>Costo de venta (C. de producción)</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('ganancias_costo_venta', $ganancia_perdida) }}" disabled id="ganancias_costo_venta"></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;
                color: #000 !important;"><b>UTILIDAD BRUTA</b></td>
                <td style="background-color: #c8c8c8 !important;
                color: #000 !important;"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('ganancias_utilidad_bruta', $ganancia_perdida) }}" disabled id="ganancias_utilidad_bruta"></td>
              </tr>
              <tr>
                <td>Gastos de personal administrativo</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('ganancias_gasto_administrativo', $ganancia_perdida) }}" id="ganancias_gasto_administrativo"></td>
              </tr>
              <tr>
                <td>Gastos de personal de ventas</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('ganancias_gasto_ventas', $ganancia_perdida) }}" id="ganancias_gasto_ventas"></td>
              </tr>
              <tr>
                <td><b>Servicios:</b></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('ganancias_total_servicios', $ganancia_perdida) }}" disabled id="ganancias_total_servicios"></td>
              </tr>
              <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Luz</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('ganancias_servicio_luz', $ganancia_perdida) }}" id="ganancias_servicio_luz"></td>
              </tr>
              <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Agua</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('ganancias_servicio_agua', $ganancia_perdida) }}" id="ganancias_servicio_agua"></td>
              </tr>
              <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Telefono/internet</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('ganancias_servicio_internet', $ganancia_perdida) }}" id="ganancias_servicio_internet"></td>
              </tr>
              <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- T. celular</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('ganancias_servicio_celular', $ganancia_perdida) }}" id="ganancias_servicio_celular"></td>
              </tr>
              <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Cable</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('ganancias_servicio_cable', $ganancia_perdida) }}" id="ganancias_servicio_cable"></td>
              </tr>
              <tr>
                <td>Alquiler de local</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('ganancias_alquiler_local', $ganancia_perdida) }}" id="ganancias_alquiler_local"></td>
              </tr>
              <tr>
                <td>Autoavalúo, serenazgo, parques y J.</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('ganancias_autovaluo', $ganancia_perdida) }}" id="ganancias_autovaluo"></td>
              </tr>
              <tr>
                <td>Transporte</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('ganancias_transporte', $ganancia_perdida) }}" id="ganancias_transporte"></td>
              </tr>
              <tr>
                <td>Cuota de préstamo E. Reguladas</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('ganancias_cuota_prestamo_regulada', $ganancia_perdida) }}" id="ganancias_cuota_prestamo_regulada"></td>
              </tr>
              <tr>
                <td>Cuota de préstamo E. No Reguladas</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('ganancias_cuota_prestamo_noregulada', $ganancia_perdida) }}" id="ganancias_cuota_prestamo_noregulada"></td>
              </tr>
              <tr>
                <td>Sunat</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('ganancias_sunat', $ganancia_perdida) }}" id="ganancias_sunat"></td>
              </tr>
              <tr>
                <td>Otros gastos</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('ganancias_otros_gastos', $ganancia_perdida) }}" id="ganancias_otros_gastos"></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;
                color: #000 !important;"><b>TOTAL DE GASTOS OPERATIVOS</b></td>
                <td style="background-color: #c8c8c8 !important;
                color: #000 !important;"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('ganancias_gastos_operativos', $ganancia_perdida) }}" disabled id="ganancias_gastos_operativos"></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;
                color: #000 !important;"><b>UTILIDAD NETA</b></td>
                <td style="background-color: #c8c8c8 !important;
                color: #000 !important;"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('ganancias_utilidad_neta', $ganancia_perdida) }}" disabled id="ganancias_utilidad_neta"></td>
              </tr>
              <tr>
                <td>Cuota de Préstamos de Consumo e Hipotecarios para Vivienda (Reg. y no Reg.)</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ encontrar_valor('ganancias_consumo_hipotecario', $ganancia_perdida) }}" id="ganancias_consumo_hipotecario"></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;
                color: #000 !important;"><b>EXCEDENTE MENSUAL</b></td>
                <td style="background-color: #c8c8c8 !important;
                color: #000 !important;"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('ganancias_excedente_mensual', $ganancia_perdida) }}" disabled id="ganancias_excedente_mensual"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">7.1.3 MOVIMIENTO COMERCIAL</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-6">
          <?php 
          $dias_ventas_mensual = $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->dias_ventas_mensual : '';
          ?>
          <table class="table">
            <thead>
              <tr>
                <th colspan=5>Ventas Mensuales (S/.)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Al crédito para cobro a</td>
                <td>
                  <select id="dias_ventas_mensual" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                    <option value=""></option>
                    <option value="7" <?php echo $dias_ventas_mensual==7?'selected':'' ?>>7 Días</option>
                    <option value="15" <?php echo $dias_ventas_mensual==15?'selected':'' ?>>15 Días</option>
                    <option value="30" <?php echo $dias_ventas_mensual==30?'selected':'' ?>>30 Días</option>
                    <option value="45" <?php echo $dias_ventas_mensual==45?'selected':'' ?>>45 Días</option>
                    <option value="60" <?php echo $dias_ventas_mensual==60?'selected':'' ?>>60 Días</option>
                  </select>
                </td>
                <td>total al mes</td>
                <td width="100px"><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->credito_cobrando_venta_mensual : '0.00' }}" id="credito_cobrando_venta_mensual" onkeyup="calc_movimiento_comercial()"></td>
                <td width="100px">
                  <div class="input-group">
                        <input type="text" class="form-control campo_moneda" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->credito_porcentaje_venta_mensual : '0.00' }}" 
                         disabled id="credito_porcentaje_venta_mensual">
                        <span class="input-group-text">%</span>
                      </div>
                  
                </td>
              </tr>
              <tr>
                <td colspan=3>Al Contado</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->contado_cobrando_venta_mensual : '0.00' }}" disabled id="contado_cobrando_venta_mensual"></td>
                <td>
                  <div class="input-group">
                        <input type="text" class="form-control campo_moneda" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->contado_porcentaje_venta_mensual : '0.00' }}" 
                         disabled id="contado_porcentaje_venta_mensual">
                        <span class="input-group-text">%</span>
                      </div>
                  
                </td>
              </tr>
            </tbody>
          </table>  
        </div>  
        <div class="col-sm-12 col-md-6">
          <?php 
          $dias_compras_mensual = $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->dias_compras_mensual : '';
          ?>
          <table class="table">
            <thead>
              <tr>
                <th colspan=5>Compras Mensuales (S/.)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Al crédito para pago a</td>
                <td>
                  <select id="dias_compras_mensual" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                    <option value=""></option>
                    <option value="7" <?php echo $dias_compras_mensual==7?'selected':'' ?>>7 Días</option>
                    <option value="15" <?php echo $dias_compras_mensual==15?'selected':'' ?>>15 Días</option>
                    <option value="30" <?php echo $dias_compras_mensual==30?'selected':'' ?>>30 Días</option>
                    <option value="45" <?php echo $dias_compras_mensual==45?'selected':'' ?>>45 Días</option>
                    <option value="60" <?php echo $dias_compras_mensual==60?'selected':'' ?>>60 Días</option>
                  </select>
                </td>
                <td>total al mes</td>
                <td width="100px"><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->credito_cobrando_compra_mensual : '0.00' }}" id="credito_cobrando_compra_mensual" onkeyup="calc_movimiento_comercial()"></td>
                <td width="100px">
                  <div class="input-group">
                  <input type="text" class="form-control campo_moneda" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->credito_porcentaje_compra_mensual : '0.00' }}" disabled 
                         id="credito_porcentaje_compra_mensual">
                        <span class="input-group-text">%</span>
                      </div>
                </td>
              </tr>
              <tr>
                <td colspan=3>Al Contado</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->contado_cobrando_compra_mensual : '0.00' }}" disabled id="contado_cobrando_compra_mensual"></td>
                <td>
                  <div class="input-group">
                    <input type="text" class="form-control campo_moneda" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->contado_porcentaje_compra_mensual : '0.00' }}" 
                                   disabled id="contado_porcentaje_compra_mensual">
                        <span class="input-group-text">%</span>
                      </div>
                </td>
              </tr>
            </tbody>
          </table> 
          <script>
            
            function calc_movimiento_comercial(){
              //VENTAS
              let ganancias_venta_mensual = parseFloat($('#ganancias_venta_mensual').val());
              let credito_cobrando_venta_mensual = parseFloat($('#credito_cobrando_venta_mensual').val());
              
              let credito_porcentaje_venta_mensual = (credito_cobrando_venta_mensual / ganancias_venta_mensual)*100;
              if(isNaN(credito_porcentaje_venta_mensual) ||  credito_porcentaje_venta_mensual == Infinity ||  credito_porcentaje_venta_mensual == -Infinity){
                  credito_porcentaje_venta_mensual = 0;
              }
              $('#credito_porcentaje_venta_mensual').val(credito_porcentaje_venta_mensual.toFixed(2))
              
              
              let contado_cobrando_venta_mensual = ganancias_venta_mensual - credito_cobrando_venta_mensual;
              $('#contado_cobrando_venta_mensual').val(contado_cobrando_venta_mensual.toFixed(2))
             
              let contado_porcentaje_venta_mensual = (contado_cobrando_venta_mensual/ganancias_venta_mensual) * 100;
              if(isNaN(contado_porcentaje_venta_mensual) ||  contado_porcentaje_venta_mensual == Infinity ||  contado_porcentaje_venta_mensual == -Infinity){
                  contado_porcentaje_venta_mensual = 0;
              }
              $('#contado_porcentaje_venta_mensual').val(contado_porcentaje_venta_mensual.toFixed(2))
              //COMPRAS
              
              let ganancias_costo_venta = parseFloat($('#ganancias_costo_venta').val());
              let credito_cobrando_compra_mensual = parseFloat($('#credito_cobrando_compra_mensual').val());
              
              let credito_porcentaje_compra_mensual = (credito_cobrando_compra_mensual / ganancias_costo_venta)*100;
              if(isNaN(credito_porcentaje_compra_mensual) ||  credito_porcentaje_compra_mensual == Infinity ||  credito_porcentaje_compra_mensual == -Infinity){
                  credito_porcentaje_compra_mensual = 0;
              }
              $('#credito_porcentaje_compra_mensual').val(credito_porcentaje_compra_mensual.toFixed(2))
              
              let contado_cobrando_compra_mensual = ganancias_costo_venta - credito_cobrando_compra_mensual;
              $('#contado_cobrando_compra_mensual').val(contado_cobrando_compra_mensual.toFixed(2))
             
              let contado_porcentaje_compra_mensual = (contado_cobrando_compra_mensual/ganancias_costo_venta) * 100;
              if(isNaN(contado_porcentaje_compra_mensual) ||  contado_porcentaje_compra_mensual == Infinity ||  contado_porcentaje_compra_mensual == -Infinity){
                  contado_porcentaje_compra_mensual = 0;
              }
              $('#contado_porcentaje_compra_mensual').val(contado_porcentaje_compra_mensual.toFixed(2))
              
            }
          </script>
        </div>  
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">7.1.4 COMENTARIOS </span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <textarea {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" id="comentario" cols="30" rows="3">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->comentario : '' }}</textarea>
        </div>
      </div>
        
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">7.2. CALCULO DE MARGEN Y NIVEL VENTAS</span>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">7.2.1 VENTAS DENTRO DE LA SEMANA (VENTAS CON FRECUENCIA DIARIA Y SEMANAL)</span>
      </div>
      <div class="row">
        <div class="col-sm-9">
          <table class="table table-bordered" id="tabla-producto">
              <thead>
                <tr>
                  <th rowspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;">VENTA MUESTRA: De productos(de mayor rotación) que comercializa, produce o presta servicio</th>
                  <th rowspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;" width="100px">U. de Med.</th>
                  <th rowspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;">Cantidad</th>
                  <th rowspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;">P. de venta</th>
                  <th rowspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;">P. de Compra /Costo de Produc.</th>
                  <th colspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;">TOTAL (S/.)</th>
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
                    <td cantidad><input type="text" valida_input_vacio onkeyup="calcula_subtotales(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto text-center" value="{{ $value->cantidad }}"></td>
                    <td precioventa><input type="text" valida_input_vacio onkeyup="calcula_subtotales(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ $value->precioventa }}"></td>
                    <td preciocompra><input type="text" valida_input_vacio onkeyup="calcula_subtotales(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ $value->preciocompra }}"></td>
                    <td subtotalventa><input type="text" step="any" class="form-control campo_moneda" disabled value="{{ $value->subtotalventa }}"></td>
                    <td subtotalcompra><input type="text" step="any" class="form-control campo_moneda" disabled value="{{ $value->subtotalcompra }}"></td>
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
                  <td><input type="text" class="form-control campo_moneda" disabled id="total_venta" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_venta : '0.00' }}"></td>
                  <td><input type="text" class="form-control campo_moneda" disabled id="total_compra" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_compra : '0.00' }}"></td>
                  <td></td>
                  <td></td>
                </tr>
                <tr>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;" class="campo_moneda" colspan=5 align="right">Mg. de Venta</th>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;">
                    <div class="input-group">
                        <input type="text" step="any" disabled id="porcentaje_margen" class="form-control campo_moneda" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->porcentaje_margen : '0.00' }}">
                        <span class="input-group-text">%</span>
                      </div>
                  </th>
                  <td style="background-color: #c8c8c8 !important;color: #000 !important;"></td>
                  <td style="background-color: #c8c8c8 !important;color: #000 !important;"></td>
      
                    @if($view_detalle!='false')
                    <td style="background-color: #c8c8c8 !important;color: #000 !important;"></td>
                    @endif
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
                  <input type="text" disabled id="frecuencia_ventas" class="form-control" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->frecuencia_ventas : 'DIARIO' }}">
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
                      <td valor><input onkeyup="calcula_total_dia()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control  campo_moneda color_cajatexto" value="{{ $value->valor }}"></td>
                    </tr>
                  @endforeach
                @else
                <tr>
                  <td numero>1</td>
                  <td dia>Lunes</td>
                  <td valor><input onkeyup="calcula_total_dia()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control  campo_moneda color_cajatexto" value="0.00"></td>
                </tr>
                <tr>
                  <td numero>2</td>
                  <td dia>Martes</td>
                  <td valor><input onkeyup="calcula_total_dia()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control  campo_moneda color_cajatexto" value="0.00"></td>
                </tr>
                <tr>
                  <td numero>3</td>
                  <td dia>Miércoles</td>
                  <td valor><input onkeyup="calcula_total_dia()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control  campo_moneda color_cajatexto" value="0.00"></td>
                </tr>
                <tr>
                  <td numero>4</td>
                  <td dia>Jueves</td>
                  <td valor><input onkeyup="calcula_total_dia()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control  campo_moneda color_cajatexto" value="0.00"></td>
                </tr>
                <tr>
                  <td numero>5</td>
                  <td dia>Viernes</td>
                  <td valor><input onkeyup="calcula_total_dia()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control  campo_moneda color_cajatexto" value="0.00"></td>
                </tr>
                <tr>
                  <td numero>6</td>
                  <td dia>Sábado</td>
                  <td valor><input onkeyup="calcula_total_dia()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control  campo_moneda color_cajatexto" value="0.00"></td>
                </tr>
                <tr>
                  <td numero>7</td>
                  <td dia>Domingo</td>
                  <td valor><input onkeyup="calcula_total_dia()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control  campo_moneda color_cajatexto" value="0.00"></td>
                </tr>
                @endif
                <tr total>
                  <th colspan="2" style="background-color: #c8c8c8 !important;color: #000 !important;">Venta Semanal (S/.)</th>
                  <td style="background-color: #c8c8c8 !important;color: #000 !important;"><input type="text" id="venta_total_dias" step="any" class="form-control campo_moneda" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->venta_total_dias : '0.00' }}" disabled></td>
                </tr>
              </tbody>
            </table>
         
          
          <table class="table table-bordered mb-2 mt-2">
            <thead>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" width="150px">N° de Días</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" ><input type="text" disabled id="numero_dias" class="form-control campo_moneda" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->numero_dias : '0' }}"></th>
              </tr>
            </thead>
          </table>
          <table class="table table-bordered mb-2 mt-2">
            <thead>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" width="150px">Venta mensual (S/.)</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" >
                  <input type="text" disabled id="venta_mensual" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->venta_mensual : '0' }}" class="form-control campo_moneda"></th>
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
                <td><input type="text" class="form-control campo_moneda" disabled id="recabo_dato_numero" 
                           value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->recabo_dato_numero : '1' }}"></td>
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
                <td><input type="text" class="form-control campo_moneda" disabled id="recabo_dato_monto" 
                           value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->recabo_dato_monto : '0.00' }}"></td>
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
                  <input type="text" disabled id="estado_muestra" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->estado_muestra : '0.00' }}" 
                         class="form-control text-center"></th>
              </tr>
            </thead>
          </table>
          <table class="table table-bordered mb-2 mt-2">
            <thead>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" width="150px">Mg. De venta al mes (1) (S/.)</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" >
                  <input type="text" disabled id="margen_ventas" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->margen_ventas : '0.00' }}" 
                         class="form-control campo_moneda"></th>
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
                  <th width="60px">Cantidad</th>
                  <th width="60px">Costo x U., Doc. Etc.</th>
                  <th width="60px">Total (S/.)</th>
                  
                  @if($view_detalle!='false')
                    <th width="10px"><button class="btn btn-success" type="button" onclick="agregar_subproducto(this)"><i class="fa fa-plus"></i></button></th>
                  @endif
                </tr>
              </thead>
              <tbody>
                @foreach($value->producto as $key => $items)
                <tr>
                  <td producto><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" value="{{ $items->producto }}"></td>
                  <td cantidad><input type="text" valida_input_vacio onkeyup="subtotal_subproducto(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control text-center color_cajatexto" value="{{ $items->cantidad }}"></td>
                  <td costo><input type="text" valida_input_vacio onkeyup="subtotal_subproducto(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ $items->costo }}"></td>
                  <td total><input type="text" class="form-control campo_moneda" value="{{ $items->total }}" disabled></td>
                  
                  @if($view_detalle!='false')
                    <td><button class="btn btn-danger" '.($key == 0 ? 'disabled' : '').' type="button" onclick="remove_subproducto(this)"><i class="fa fa-trash"></i></button></td>
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
                  <td costo_mano_obra><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ $value->costo_mano_obra }}" 
                                             onkeyup="total_costo_produccion(this)" onclick="total_costo_produccion(this)"></td>
                  @if($view_detalle!='false')
                    <td></td>
                  @endif
                </tr>
                <tr>
                  <td colspan=3>Otros costos</td>
                  <td costo_otros><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ $value->costo_otros }}" 
                                         onkeyup="total_costo_produccion(this)" onclick="total_costo_produccion(this)"></td>
                  
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
      
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">7.2.2 VENTAS EN MAS DE UNA SEMANA (VENTAS CON FRECUENCIA MENSUAL)</span>
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
                  <th colspan=2 style="background-color: #c8c8c8 !important;color: #000 !important;">TOTAL (S/.)</th>
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
                    <td producto><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} onkeyup="actualizarOpcionesSelect()" class="form-control color_cajatexto" value="{{ $value->producto }}"></td>
                    <td unidadmedida>
                      <select class="form-control color_cajatexto" {{ $view_detalle=='false' ? 'disabled' : '' }}>
                        <option></option>
                        @foreach($unidadmedida_credito as $unidad)
                          <option value="{{ $unidad->nombre }}" {{ $unidad->nombre == $value->unidadmedida ? "selected" : "" }}>{{ $unidad->nombre }}</option>
                        @endforeach
                      </select>
                      
                    </td>
                    <td cantidad><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} onkeyup="calcula_subtotales_mensual(this)" step="any" class="form-control text-center color_cajatexto" value="{{ $value->cantidad }}"></td>
                    <td precioventa><input type="text" valida_input_vacio onkeyup="calcula_subtotales_mensual(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ $value->precioventa }}"></td>
                    <td preciocompra><input type="text" valida_input_vacio onkeyup="calcula_subtotales_mensual(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ $value->preciocompra }}"></td>
                    <td subtotalventa><input type="text" step="any" class="form-control campo_moneda" disabled value="{{ $value->subtotalventa }}"></td>
                    <td subtotalcompra><input type="text" step="any" class="form-control campo_moneda" disabled value="{{ $value->subtotalcompra }}"></td>
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
                  <td><input type="text" class="form-control campo_moneda" disabled id="total_venta_mensual" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_venta_mensual : '0.00' }}"></td>
                  <td><input type="text" class="form-control campo_moneda" disabled id="total_compra_mensual" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_compra_mensual : '0.00' }}"></td>
                  <td></td>
                    @if($view_detalle!='false')
                    <td></td>
                    @endif
                </tr>
                <tr>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;" class="campo_moneda" colspan=5 align="right">Mg. de Venta</th>
                  <th style="background-color: #c8c8c8 !important;color: #000 !important;">
                    <div class="input-group">
                        <input type="text" step="any" disabled id="porcentaje_margen_mensual" class="form-control campo_moneda" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->porcentaje_margen_mensual : '0.00' }}">
                        <span class="input-group-text">%</span>
                      </div>
                  </th>
                  <td style="background-color: #c8c8c8 !important;color: #000 !important;"></td>
                  <td style="background-color: #c8c8c8 !important;color: #000 !important;"></td>
                  
                    @if($view_detalle!='false')
                   <td style="background-color: #c8c8c8 !important;color: #000 !important;"></td>
                    @endif
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
                      <td valor><input onkeyup="calcula_total_mes()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ $value->valor }}"></td>
                    </tr>
                  @endforeach
                @else
                <tr>
                  <td semana>SEMANA 1</td>
                  <td valor><input onkeyup="calcula_total_mes()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                </tr>
                <tr>
                  <td semana>SEMANA 2</td>
                  <td valor><input onkeyup="calcula_total_mes()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                </tr>
                <tr>
                  <td semana>SEMANA 3</td>
                  <td valor><input onkeyup="calcula_total_mes()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                </tr>
                <tr>
                  <td semana>SEMANA 4</td>
                  <td valor><input onkeyup="calcula_total_mes()" type="text" valida_input_vacio step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                </tr>
                @endif
              
              <tr total>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;">Venta Mensual (S/.)</th>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;"><input type="text" id="venta_total_mensual" step="any" class="form-control campo_moneda" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->venta_total_mensual : '0.00' }}" disabled></td>
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
                  <input type="text" disabled id="estado_muestra_mensual" 
                         value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->estado_muestra_mensual : '' }}" class="form-control text-center"></th>
              </tr>
            </thead>
          </table>
          <table class="table table-bordered mb-2 mt-2">
            <thead>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" width="150px">Mg. De venta al mes (2) (S/.)</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;" >
                  <input type="text" disabled id="margen_ventas_mensual" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->margen_ventas_mensual : '0.00' }}"
                         class="form-control campo_moneda"></th>
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
                  <th width="60px">Cantidad</th>
                  <th width="60px">Costo x U., Doc. Etc.</th>
                  <th width="60px">Total (S/.)</th>
                  
                @if($view_detalle!='false')
                <th><button class="btn btn-success" type="button" onclick="agregar_subproducto(this)"><i class="fa fa-plus"></i></button></th>
                @endif 
                </tr>
              </thead>
              <tbody>
                @foreach($value->producto as $key => $items)
                <tr>
                  <td producto><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" value="{{ $items->producto }}"></td>
                  <td cantidad><input type="text" valida_input_vacio onkeyup="subtotal_subproducto(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control text-center color_cajatexto" value="{{ $items->cantidad }}"></td>
                  <td costo><input type="text" valida_input_vacio onkeyup="subtotal_subproducto(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ $items->costo }}"></td>
                  <td total><input type="text" class="form-control campo_moneda" value="{{ $items->total }}" disabled></td>
                  
                @if($view_detalle!='false')
                <td><button class="btn btn-danger" '.($key == 0 ? 'disabled' : '').' type="button" onclick="remove_subproducto(this)"><i class="fa fa-trash"></i></button></td>
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
                  <td costo_mano_obra><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ $value->costo_mano_obra }}" onkeyup="total_costo_produccion(this)" 
                                             onclick="total_costo_produccion(this)"></td>
                  @if($view_detalle!='false')
                  <td></td>
                  @endif 
                </tr>
                <tr>
                  <td colspan=3>Otros costos</td>
                  <td costo_otros><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ $value->costo_otros }}" onkeyup="total_costo_produccion(this)" 
                                         onclick="total_costo_produccion(this)"></td>
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
        <span class="badge d-block" style="background-color: #aaa;color: #000;">7.3 INVENTARIO Y  ACTIVOS FIJOS - NEGOCIO ADICIONAL</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-6">
          <table class="table table-bordered" id="table-inventario-producto" table="inventario-producto">
            <thead>
              <tr>
                <th>Inventario de Productos</th>
                <th width="80px">Unid. Med.</th>
                <th width="60px">Cantidad</th>
                <th width="100px">Precio de compra</th>
                <th width="100px">Total</th>
                @if($view_detalle!='false')
                  <th width="10px"><button type="button" class="btn btn-success" onclick="agregar_producto_inventario(this)"><i class="fa fa-plus"></i></button></th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach($inventario as $value)
                <tr id="{{ $value->id }}">
                <td nombre><input type="text" class="form-control color_cajatexto" value="{{ $value->nombre }}" {{ $view_detalle=='false' ? 'disabled' : '' }}></td>
                <td medida>
                  <select class="form-control color_cajatexto" {{ $view_detalle=='false' ? 'disabled' : '' }}>
                    @foreach($unidadmedida_credito as $unidad)
                      <option value="{{ $unidad->nombre }}" {{ $unidad->nombre == $value->medida ? "selected" : "" }}>{{ $unidad->nombre }}</option>
                    @endforeach
                  </select>
                </td>
                <td cantidad><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} valida_input_vacio onkeyup="calcula_subtotales_inventario(this)" class="form-control text-center color_cajatexto" value="{{ $value->cantidad }}"></td>
                <td precio><input type="text" valida_input_vacio onkeyup="calcula_subtotales_inventario(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ $value->precio }}"></td>
                <td subtotalventa><input type="text" class="form-control campo_moneda" disabled value="{{ $value->subtotalventa }}"></td>
                  @if($view_detalle!='false')
                    <td><button type="button" onclick="eliminar_producto_inventario(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                  @endif
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td class="color_totales campo_moneda" colspan=4><b>Inventario total de productos  (S/.)</b></td>
                <td class="color_totales"><input type="text" id="total-inventario-producto" class="form-control campo_moneda" disabled 
                                                 value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_inventario : '0.00' }}"></td>
                  @if($view_detalle!='false')
                    <td class="color_totales"></td>
                  @endif
              </tr>
            </tfoot>
          </table>
        </div>
        <div class="col-sm-12 col-md-6">
          <table class="table table-bordered" id="table-activos-inmuebles" table="activos-inmuebles">
            <thead>
              <tr>
                <th>Activos Inmuebles</th>
                <th width="80px">Unid. Med.</th>
                <th width="60px">Cantidad</th>
                <th width="100px">Valor estimado</th>
                <th width="100px">Total</th>
                @if($view_detalle!='false')
                    <th width="10px"><button type="button" class="btn btn-success" onclick="agregar_producto_inventario(this)"><i class="fa fa-plus"></i></button></th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach($inmuebles as $value)
                <tr id="{{ $value->id }}">
                <td nombre><input type="text" class="form-control color_cajatexto" value="{{ $value->nombre }}" {{ $view_detalle=='false' ? 'disabled' : '' }}></td>
                <td medida>
                  <select class="form-control color_cajatexto" {{ $view_detalle=='false' ? 'disabled' : '' }}>
                    @foreach($unidadmedida_credito as $unidad)
                      <option value="{{ $unidad->nombre }}" {{ $unidad->nombre == $value->medida ? "selected" : "" }}>{{ $unidad->nombre }}</option>
                    @endforeach
                  </select>
                </td>
                <td cantidad><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} valida_input_vacio onkeyup="calcula_subtotales_inventario(this)" class="form-control text-center color_cajatexto" value="{{ $value->cantidad }}"></td>
                <td precio><input type="text" valida_input_vacio onkeyup="calcula_subtotales_inventario(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ $value->precio }}"></td>
                <td subtotalventa><input type="text" class="form-control campo_moneda" disabled value="{{ $value->subtotalventa }}"></td>
                @if($view_detalle!='false')
                    <td><button type="button" onclick="eliminar_producto_inventario(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                @endif
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td class="color_totales campo_moneda" colspan=4><b>Total de activos inmuebles (S/.)</b></td>
                <td class="color_totales">
                  <input type="text" id="total-activos-inmuebles" class="form-control campo_moneda" disabled 
                                                 value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_inmuebles : '0.00' }}">
                </td>
                @if($view_detalle!='false')
                    <td class="color_totales"></td>
                @endif
              </tr>
            </tfoot>
          </table>
          <table class="table table-bordered" id="table-activos-muebles" table="activos-muebles">
            <thead>
              <tr>
                <th>Activos Muebles</th>
                <th width="80px">Unid. Med.</th>
                <th width="60px">Cantidad</th>
                <th width="100px">Valor estimado (como usado)</th>
                <th width="100px">Total</th>
                @if($view_detalle!='false')
                <th width="10px"><button type="button" class="btn btn-success" onclick="agregar_producto_inventario(this)"><i class="fa fa-plus"></i></button></th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach($muebles as $value)
                <tr id="{{ $value->id }}">
                <td nombre><input type="text" class="form-control color_cajatexto" value="{{ $value->nombre }}" {{ $view_detalle=='false' ? 'disabled' : '' }}></td>
                <td medida>
                  <select class="form-control color_cajatexto" {{ $view_detalle=='false' ? 'disabled' : '' }}>
                    @foreach($unidadmedida_credito as $unidad)
                      <option value="{{ $unidad->nombre }}" {{ $unidad->nombre == $value->medida ? "selected" : "" }}>{{ $unidad->nombre }}</option>
                    @endforeach
                  </select>
                </td>
                <td cantidad><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} valida_input_vacio onkeyup="calcula_subtotales_inventario(this)" class="form-control text-center color_cajatexto" value="{{ $value->cantidad }}"></td>
                <td precio><input type="text" valida_input_vacio onkeyup="calcula_subtotales_inventario(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="{{ $value->precio }}"></td>
                <td subtotalventa><input type="text" class="form-control campo_moneda" disabled value="{{ $value->subtotalventa }}"></td>
                @if($view_detalle!='false')
                    <td><button type="button" onclick="eliminar_producto_inventario(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                @endif
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td class="color_totales campo_moneda" colspan=4><b>Total de activos muebles (S/.)</b></td>
                <td class="color_totales"><input type="text" id="total-activos-muebles" class="form-control campo_moneda" disabled 
                                                 value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_muebles : '0.00' }}"></td>

                @if($view_detalle!='false')
                    <td class="color_totales"></td>
                @endif
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
        
      <div class="mb-1 mt-2">
        <span class="badge d-block">VIII. INGRESO ADICIONAL FIJO:</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-6">
          <table class="table table-bordered" id="table-ingreso-adicional-fijo">
            <thead>
              <tr>
                <th width="50px">N°</th>
                <th>Especificación (Con boletas de pago debidamente sustentado)</th>
                <th width="100px">Monto neto (S/.)</th>
                @if($view_detalle!='false')
                    <th width="30px"><button type="button" class="btn btn-success" onclick="agregar_adicional_fijo()"><i class="fa fa-plus"></i></button></th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach($adicional_fijo as $value)
                <tr>
                  <td><input type="text" numeracion disabled class="form-control text-center" value="{{ $value->numeracion }}"></td>
                  <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }}  descripcion class="form-control color_cajatexto" value="{{ $value->descripcion }}"></td>
                  <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} valida_input_vacio monto {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" onkeyup="calc_total_adicion_fijo()" value="{{ $value->monto }}"></td>
                  @if($view_detalle!='false')
                    <td><button type="button" onclick="eliminar_adicional_fijo(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                  @endif
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td class="color_totales campo_moneda" colspan="2" class="text-right"><b>TOTAL</b> (S/.)</td>
                <td class="color_totales"><input type="text" class="form-control campo_moneda" id="total_ingreso_adicional" 
                           value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_ingreso_adicional : '0.00' }}" disabled></td>
                @if($view_detalle!='false')
                    <td class="color_totales"></td>
                @endif
              </tr>
            </tfoot>
          </table>
        </div>    
        <script>
          function agregar_adicional_fijo(){
            let btn_eliminar = `<button type="button" onclick="eliminar_adicional_fijo(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button>` ;
            let tr = `<tr>
                        <td><input type="text" numeracion disabled class="form-control text-center campo_moneda"></td>
                        <td><input type="text" descripcion class="form-control color_cajatexto"></td>
                        <td><input type="text" valida_input_vacio monto class="form-control color_cajatexto campo_moneda" onkeyup="calc_total_adicion_fijo()" value="0.00"></td>
                        <td>${btn_eliminar}</td>
                      </tr>`;
            $('#table-ingreso-adicional-fijo').append(tr);
            item();
            valida_input_vacio();
          }
          function eliminar_adicional_fijo(e){
            $(e).closest('tr').remove();
            calc_total_adicion_fijo()
          }
          function item(){
            let item = 1;
            $('#table-ingreso-adicional-fijo tbody tr').each(function () {
                let str = "" + item;
                let pad = "00";
                let num = pad.substring(0, pad.length - str.length) + str;
                $(this).find('input[numeracion]').val(num)
                item++;
            });
          }
          function calc_total_adicion_fijo(){
            let total = 0;
            $('#table-ingreso-adicional-fijo tbody tr').each(function () {
                
              let monto = parseFloat($(this).find('input[monto]').val());
              total += monto;
                
            });
            $('#total_ingreso_adicional').val(total.toFixed(2))
          }
          function json_adicional_fijo(){
            let data = [];
            $("#table-ingreso-adicional-fijo > tbody > tr").each(function() {
                
                let numeracion = $(this).find('input[numeracion]').val();
                let descripcion = $(this).find('input[descripcion]').val();
                let monto = $(this).find('input[monto]').val();

                data.push({ 
                    numeracion: numeracion,
                    descripcion: descripcion,
                    monto: monto,
                });
            });
            return JSON.stringify(data);
          }
        </script>
      </div>
      <div class="row mt-1">
        
        @if($view_detalle!='false')
        <div class="col" style="flex: 0 0 0%;">
          <button type="submit" class="btn btn-success" id="boton_guardar"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS <b>({{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->cantidad_update : 0 }})</b></button>
        </div>
        @endif
        <div class="col" style="flex: 0 0 0%;">
          <button type="button" 
                  class="btn btn-dark" 
                  onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=solicitud_ingresoadicional')}}', size: 'modal-fullscreen' })"
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
    </div>
</form> 
<style>
  {{ $view_detalle=='false' ? '' : ".modal-body-cualitativa .form-check-input[type=checkbox],
  .modal-body-cualitativa .select2-container--bootstrap-5 .select2-selection {
      background-color: #ffffb5;
  }" }}
  
  .form-check-input:checked {
      background-color: #585858 !important;
      border-color: #585858 !important;
  }
</style>
<script>
  valida_input_vacio();
  $('input[valida_input_vacio]').on('blur', function() {
      calc_balance_general();
      calc_ganancias_perdidas();
  });
  $('input').on('blur', function() {
      $('#boton_imprimir').attr('disabled',true);
  });
  sistema_select2({ input:'#idtipo_giro_economico_adiccional' });
  @if($credito_cuantitativa_ingreso_adicional)
  $("#idtipo_giro_economico_adiccional").on("change", function(e) {
    
    <?php echo $view_detalle=='false' ? '' : "$('#idgiro_economico_evaluacion_adicional').removeAttr('disabled',false)" ?>
    //show_giro_economico(e.currentTarget.value);
    
    $.ajax({
        url:"{{url('backoffice/0/credito/showgiroeconomico')}}",
        type:'GET',
        data: {
            tipogiro : e.currentTarget.value
        },
        success: function (res){

          let option_select = `<option></option>`;
          $.each(res, function( key, value ) {
            option_select += `<option value="${value.id}" >${value.nombre}</option>`;
          });
          $('#idgiro_economico_evaluacion_adicional').html(option_select);
          sistema_select2({ input:'#idgiro_economico_evaluacion_adicional', val:'{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->idgiro_economico_evaluacion_adicional : 0 }}'});

        }
      })
    
  }).val('{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->idtipo_giro_economico_adiccional : 0 }}').trigger('change');
  @else
  $("#idtipo_giro_economico_adiccional").on("change", function(e) {
    
    <?php echo $view_detalle=='false' ? '' : "$('#idgiro_economico_evaluacion_adicional').removeAttr('disabled',false)" ?>
    //show_giro_economico(e.currentTarget.value);
    
    $.ajax({
        url:"{{url('backoffice/0/credito/showgiroeconomico')}}",
        type:'GET',
        data: {
            tipogiro : e.currentTarget.value
        },
        success: function (res){

          let option_select = `<option></option>`;
          $.each(res, function( key, value ) {
            option_select += `<option value="${value.id}" >${value.nombre}</option>`;
          });
          $('#idgiro_economico_evaluacion_adicional').html(option_select);
          sistema_select2({ input:'#idgiro_economico_evaluacion_adicional', val:'{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->idgiro_economico_evaluacion_adicional : 0 }}'});

        }
      })
    
  });
  @endif
  
  function show_giro_economico(id){
    $.ajax({
        url:"{{url('backoffice/0/credito/showgiroeconomico')}}",
        type:'GET',
        data: {
            tipogiro : id
        },
        success: function (res){

          let option_select = `<option></option>`;
          $.each(res, function( key, value ) {
            option_select += `<option value="${value.id}" >${value.nombre}</option>`;
          });
          $('#idgiro_economico_evaluacion_adicional').html(option_select);
          sistema_select2({ input:'#idgiro_economico_evaluacion_adicional', val:'{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->idgiro_economico_evaluacion_adicional : 0 }}'});

        }
      })
  }
  
  generarTabla(new Date("{{ date_format(date_create($credito->fecha),'Y-m-d') }}") );
  function generarTabla(fechaInicio) {
    let tabla = $('#table-evaluacion-meses');
    let filaEncabezado = tabla.find('thead tr:first');
    for (let i = 0; i < 12; i++) {
      let fechaActual = new Date(fechaInicio);
      fechaActual.setMonth(fechaInicio.getMonth() + i);
      let mes = fechaActual.toLocaleString('default', { month: 'short' }).toUpperCase();
      let mesNumero = fechaActual.getMonth() + 1;
      filaEncabezado.append('<th class="text-center">' + mes + '</th>');
      filaEncabezado.next().append('<th class="text-center">' + mesNumero.toString().padStart(2, '0') + '</th>');
    }
  }
  function json_evaluacion_meses(){
    let jsonData = [];
    $("#table-evaluacion-meses > tbody > tr").each(function() {
        let rowData = {};
        rowData["Mes"] = $(this).find("td:first").text();
        $(this).find("td input").each(function(index) {
            let header = $("#table-evaluacion-meses thead th:eq(" + index + ")").text();
            rowData[header] = {
                value: $(this).val(),
                disabled: $(this).prop("disabled")
            };
        });
        jsonData.push(rowData);
    });
    return JSON.stringify(jsonData);
  }
 $('#table-evaluacion-meses input[onkeyup*="calcula_monto_meses(this)"]').each(function() {
    // Comprueba si el input no está en la primera columna
    if ($(this).closest('td').index() !== 0) {
      // Ejecuta la función en el input actual
      calcula_monto_meses(this);
    }
  });
  function calcula_monto_meses(e){
    //let valorBase = parseFloat($("#table-evaluacion-meses tbody tr:eq(0) td:eq(1) input").val());
    let valorBase = parseFloat($('#ganancias_venta_mensual').val());
    let porcentaje_maximo = parseFloat("{{ configuracion($tienda->id,'ciclo_negocio_maximo')['valor'] }}");
    if (!isNaN(valorBase)) {
        let row = $(e).closest('tr');
        let cellIndex = $(e).closest('td').index();
        let porcentaje = parseFloat($(e).val());
        if(porcentaje > porcentaje_maximo){
          porcentaje = porcentaje_maximo;
          $(e).val(porcentaje_maximo.toFixed());
        }
        let porcentaje_aumento = porcentaje/100;
        let monto_venta = parseFloat(valorBase*porcentaje_aumento).toFixed(1);
        $(`#table-evaluacion-meses tbody tr:eq(0) td:eq(${cellIndex}) input`).val(parseFloat(monto_venta).toFixed(2));
    }
  }
  
  
  sistema_select2({ input:'#recabo_dato_dia' });
  sistema_select2({ input:'#producto_detalle' });
  sistema_select2({ input:'#producto_detalle_mensual' });

  $("#recabo_dato_dia").on("change", function() {
    filtro_dia(this);
  }).val('{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->recabo_dato_dia : 0 }}').trigger('change');

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
                            <th>Cantidad</th>
                            <th>Costo x U., Doc. Etc.</th>
                            <th>Total (S/.)</th>
                            <th>
                              <button class="btn btn-success" type="button" onclick="agregar_subproducto(this)"><i class="fa fa-plus"></i></button>
                            </th>

                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td producto><input type="text" class="form-control color_cajatexto"></td>
                            <td cantidad><input type="text" valida_input_vacio onkeyup="subtotal_subproducto(this)" class="form-control color_cajatexto campo_moneda" value="0.00"></td>
                            <td costo><input type="text" valida_input_vacio onkeyup="subtotal_subproducto(this)" class="form-control color_cajatexto campo_moneda" value="0.00"></td>
                            <td total><input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" value="0.00" disabled></td>
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
                            <td costo_mano_obra><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00" onkeyup="total_costo_produccion(this)" onclick="total_costo_produccion(this)"></td>
                            <td></td>
                          </tr>
                          <tr>
                            <td colspan=3>Otros costos</td>
                            <td costo_otros><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00" onkeyup="total_costo_produccion(this)" onclick="total_costo_produccion(this)"></td>
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
                <td producto><input type="text" class="form-control color_cajatexto"></td>
                <td cantidad><input type="text" valida_input_vacio onkeyup="subtotal_subproducto(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td costo><input type="text" valida_input_vacio onkeyup="subtotal_subproducto(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
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
                  <td producto><input type="text" class="form-control color_cajatexto" onkeyup="actualizarOpcionesSelect()"></td>
                  <td unidadmedida>
                    <select class="form-control color_cajatexto">
                      <option></option>
                      ${option_select}
                    </select>
                  </td>
                  <td cantidad><input type="text" valida_input_vacio onkeyup="calcula_subtotales(this)" step="any" class="form-control text-center color_cajatexto" value="0.00"></td>
                  <td precioventa><input type="text" valida_input_vacio onkeyup="calcula_subtotales(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                  <td preciocompra><input type="text" valida_input_vacio onkeyup="calcula_subtotales(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                  <td subtotalventa><input type="text" step="any" class="form-control campo_moneda" disabled value="0.00"></td>
                  <td subtotalcompra><input type="text" step="any" class="form-control campo_moneda" disabled value="0.00"></td>
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
                  <td producto><input type="text" class="form-control color_cajatexto" onkeyup="actualizarOpcionesSelectMensual()"></td>
                  <td unidadmedida>
                    <select class="form-control color_cajatexto">
                      <option></option>
                      ${option_select}
                    </select>
                  </td>
                  <td cantidad><input type="text" valida_input_vacio onkeyup="calcula_subtotales_mensual(this)" step="any" class="form-control text-center color_cajatexto" value="0.00"></td>
                  <td precioventa><input type="text" valida_input_vacio onkeyup="calcula_subtotales_mensual(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                  <td preciocompra><input type="text" valida_input_vacio onkeyup="calcula_subtotales_mensual(this)" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                  <td subtotalventa><input type="text" step="any" class="form-control campo_moneda" disabled value="0.00"></td>
                  <td subtotalcompra><input type="text" step="any" class="form-control campo_moneda" disabled value="0.00"></td>
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
    calc_ganancias_perdidas();
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
    calc_ganancias_perdidas();
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
    calcular_porcentaje_margen_venta()

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
    if(isNaN(margen_venta_calculado) ||  margen_venta_calculado == Infinity){
        margen_venta_calculado = 0;
    }
    $('#margen_venta_calculado').val(margen_venta_calculado.toFixed(2));
    valida_margen_venta();

  }
  valida_margen_venta();
  function valida_margen_venta(){
    let margen_venta_calculado = parseFloat($('#margen_venta_calculado').val());
    let margen_venta_giro_economico = parseFloat($('#margen_venta_giro_economico').val());

    if(margen_venta_calculado > margen_venta_giro_economico){
       $('#error_margen_venta').removeClass('d-none')
       $('#estado_error_margen_venta').val('ERROR')
       //$('#boton_guardar').attr('disabled',true)

    }else{
        $('#error_margen_venta').addClass('d-none')
       $('#estado_error_margen_venta').val('CORRECTO')
       //$('#boton_guardar').attr('disabled',false)
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

  // INVENTARIO
  function agregar_producto_inventario(e){
    let idtable = $(e).closest('table.table').attr('id');
    let btn_eliminar = `<button type="button" onclick="eliminar_producto_inventario(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button>` ;
    let option_select = ``;
    @foreach($unidadmedida_credito as $value)
      option_select += `<option value="{{ $value->nombre }}">{{ $value->nombre }}</option>`
    @endforeach
    let id = generarIDUnico();
    let tabla = `<tr id="${id}">
                  <td nombre><input type="text" class="form-control color_cajatexto"></td>
                  <td medida>
                  <select class="form-control color_cajatexto">
                      <option></option>
                      ${option_select}
                    </select>
                  </td>
                  <td cantidad><input type="text" valida_input_vacio onkeyup="calcula_subtotales_inventario(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                  <td precio><input type="text" valida_input_vacio onkeyup="calcula_subtotales_inventario(this)" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                  <td subtotalventa><input type="text" class="form-control campo_moneda" disabled value="0.00"></td>
                  <td>${btn_eliminar}</td>
                </tr>`;

      $(`#${idtable} > tbody`).append(tabla);
    valida_input_vacio();
  }
  
  function eliminar_producto_inventario(e){
    let idtable = $(e).closest('table').attr('id');
    let path = $(e).closest('tr');
    path.remove();
    calcula_total_inventario(idtable);
  }

  function calcula_subtotales_inventario(e){
    let idtable = $(e).closest('table').attr('id');
    let path = $(e).closest('tr');
    let cantidad = parseFloat($(path).find('td[cantidad] input').val());
    let precioventa = parseFloat($(path).find('td[precio] input').val());
    let subtotalventa = cantidad * precioventa;
    $(path).find('td[subtotalventa] input').val(subtotalventa.toFixed(2));
    calcula_total_inventario(idtable);
  }

  function calcula_total_inventario(idtable){

//     let idtable = $(e).closest('table.table').attr('id');
    let table_name = $(`#${idtable}`).attr('table');
    let total_venta = 0;
    $(`#${idtable} > tbody > tr`).each(function() {
      let subtotalventa = parseFloat($(this).find('td[subtotalventa] input').val());
      total_venta += subtotalventa;
    });
    $(`#total-${table_name}`).val(total_venta.toFixed(2))
    calc_balance_general();
  }
  
  function calc_balance_general(){
    let total_inventario = parseFloat($('#total-inventario-producto').val());
    let total_inmuebles = parseFloat($('#total-activos-inmuebles').val());
    let total_muebles = parseFloat($('#total-activos-muebles').val());
    
    $('#balance_inventario').val(total_inventario.toFixed(2));
    $('#balance_activo_inmueble').val(total_inmuebles.toFixed(2));
    $('#balance_activo_mueble').val(total_muebles.toFixed(2));
    
    let balance_caja = parseFloat($('#balance_caja').val());
    let balance_bancos = parseFloat($('#balance_bancos').val());
    let balance_cuentas_cobrar = parseFloat($('#balance_cuentas_cobrar').val());
    let balance_adelanto_proveedor = parseFloat($('#balance_adelanto_proveedor').val());
    
    let balance_activo_corriente =  balance_caja + balance_bancos + balance_cuentas_cobrar + balance_adelanto_proveedor + total_inventario;
    $('#balance_activo_corriente').val(balance_activo_corriente.toFixed(2));
    
    let balance_activo_nocorriente = total_inmuebles + total_muebles;
    $('#balance_activo_nocorriente').val(balance_activo_nocorriente.toFixed(2));
    
    let balance_total_activo = balance_activo_corriente + balance_activo_nocorriente;
    $('#balance_total_activo').val(balance_total_activo.toFixed(2));
    
    let balance_cuentas_pagar = parseFloat($('#balance_cuentas_pagar').val());
    let balance_corto_reguladas = parseFloat($('#balance_corto_reguladas').val());
    let balance_corto_noreguladas = parseFloat($('#balance_corto_noreguladas').val());
    let balance_impuesto = parseFloat($('#balance_impuesto').val());
    let balance_otras_cuentas = parseFloat($('#balance_otras_cuentas').val());
    let balance_pasivo_corriente = balance_cuentas_pagar + balance_corto_reguladas + balance_corto_noreguladas + balance_impuesto + balance_otras_cuentas;
    $('#balance_pasivo_corriente').val(balance_pasivo_corriente.toFixed(2));
    
    let balance_largo_reguladas = parseFloat($('#balance_largo_reguladas').val());
    let balance_largo_noreguladas = parseFloat($('#balance_largo_noreguladas').val());
    
    let balance_pasivo_nocorriente = balance_largo_reguladas + balance_largo_noreguladas;
    $('#balance_pasivo_nocorriente').val(balance_pasivo_nocorriente.toFixed(2));
    
    let balance_total_pasivo = balance_pasivo_corriente + balance_pasivo_nocorriente;
    $('#balance_total_pasivo').val(balance_total_pasivo.toFixed(2));
    
    let ganancias_utilidad_neta = parseFloat($('#ganancias_utilidad_neta').val());
    $('#balance_utilidad_acumulada').val(ganancias_utilidad_neta.toFixed(2));    

    let balance_capital_social = balance_total_activo - balance_total_pasivo - ganancias_utilidad_neta ;
    $('#balance_capital_social').val(balance_capital_social.toFixed(2));
    
    
    let balance_total_patrimonio = balance_capital_social + ganancias_utilidad_neta ;
    $('#balance_total_patrimonio').val(balance_total_patrimonio.toFixed(2));
    
    let balance_pasivo_patrimonio = balance_total_patrimonio + balance_total_pasivo ;
    $('#balance_pasivo_patrimonio').val(balance_pasivo_patrimonio.toFixed(2));

  }
  
  $("#table-balance-general tbody tr td input").on("keyup", function() {
      calc_balance_general();
  });
  
  $("#table-estado-ganancias-perdidas tbody tr td input").on("keyup", function() {
      calc_ganancias_perdidas();
  });
  
  function calc_ganancias_perdidas(){
    
    let venta_mensual = parseFloat($('#venta_mensual').val());
    let venta_total_mensual = parseFloat($('#venta_total_mensual').val());
    
    let ganancias_venta_mensual = venta_mensual + venta_total_mensual;
    $('#ganancias_venta_mensual').val(ganancias_venta_mensual.toFixed(2));
    
    
    
    let margen_ventas = parseFloat($('#margen_ventas').val());
    let margen_ventas_mensual = parseFloat($('#margen_ventas_mensual').val());
    
    let ganancias_utilidad_bruta = margen_ventas + margen_ventas_mensual;
    $('#ganancias_utilidad_bruta').val(ganancias_utilidad_bruta.toFixed(2)); 
    
    let ganancias_costo_venta = ganancias_venta_mensual - ganancias_utilidad_bruta;
    $('#ganancias_costo_venta').val(ganancias_costo_venta.toFixed(2));
    
    let margen_venta_calculado = (ganancias_utilidad_bruta/ganancias_venta_mensual)*100;
    $('#margen_venta_calculado').val(margen_venta_calculado.toFixed(2));
    
    let ganancias_servicio_luz = parseFloat($('#ganancias_servicio_luz').val());
    let ganancias_servicio_agua = parseFloat($('#ganancias_servicio_agua').val());
    let ganancias_servicio_internet = parseFloat($('#ganancias_servicio_internet').val());
    let ganancias_servicio_celular = parseFloat($('#ganancias_servicio_celular').val());
    let ganancias_servicio_cable = parseFloat($('#ganancias_servicio_cable').val());
    
    let ganancias_total_servicios = ganancias_servicio_luz + ganancias_servicio_agua + ganancias_servicio_internet + ganancias_servicio_celular + ganancias_servicio_cable;
    $('#ganancias_total_servicios').val(ganancias_total_servicios.toFixed(2));


    
    let ganancias_gasto_administrativo = parseFloat($('#ganancias_gasto_administrativo').val());
    let ganancias_gasto_ventas = parseFloat($('#ganancias_gasto_ventas').val());
    
    let ganancias_alquiler_local = parseFloat($('#ganancias_alquiler_local').val());
    let ganancias_autovaluo = parseFloat($('#ganancias_autovaluo').val());
    let ganancias_transporte = parseFloat($('#ganancias_transporte').val());
    let ganancias_cuota_prestamo_regulada = parseFloat($('#ganancias_cuota_prestamo_regulada').val());
    let ganancias_cuota_prestamo_noregulada = parseFloat($('#ganancias_cuota_prestamo_noregulada').val());
    let ganancias_sunat = parseFloat($('#ganancias_sunat').val());
    let ganancias_otros_gastos = parseFloat($('#ganancias_otros_gastos').val());
    
    
    
    let ganancias_gastos_operativos = ( ganancias_gasto_administrativo + ganancias_gasto_ventas ) + ganancias_total_servicios + ( ganancias_alquiler_local + ganancias_autovaluo + ganancias_transporte + ganancias_cuota_prestamo_regulada + ganancias_cuota_prestamo_noregulada + ganancias_sunat + ganancias_otros_gastos );
    $('#ganancias_gastos_operativos').val(ganancias_gastos_operativos.toFixed(2));


    let ganancias_utilidad_neta = ganancias_utilidad_bruta - ganancias_gastos_operativos;
    $('#ganancias_utilidad_neta').val(ganancias_utilidad_neta.toFixed(2));

    let ganancias_consumo_hipotecario = parseFloat($('#ganancias_consumo_hipotecario').val());
    
    let ganancias_excedente_mensual = ganancias_utilidad_neta - ganancias_consumo_hipotecario;
    $('#ganancias_excedente_mensual').val(ganancias_excedente_mensual.toFixed(2));
    
    calc_balance_general();
    
  }

  function json_productos_inventario(table){
    let data = [];
    $(`#table-${table} > tbody > tr`).each(function() {
        let id            = $(this).attr('id');
        let nombre        = $(this).find('td[nombre] input').val();
        let medida        = $(this).find('td[medida] select').val();
        let cantidad      = $(this).find('td[cantidad] input').val();
        let precio        = $(this).find('td[precio] input').val();
        let subtotalventa = $(this).find('td[subtotalventa] input').val();
        data.push({ 
            id: id,
            nombre: nombre,
            medida: medida,
            cantidad: cantidad,
            precio: precio,
            subtotalventa: subtotalventa,
        });
    });
    return JSON.stringify(data);
  }

  
  function json_balance(){
    let jsonData = [];
    $("#table-balance-general input").each(function () {
        let input = $(this);
        let id = input.attr("id");
        let valor = input.val();
        jsonData.push({ 
          id: id,
          valor: valor,
        });
    });
    return JSON.stringify(jsonData);
  }
  function json_ganancias_perdidas(){
    let jsonData = [];
    $("#table-estado-ganancias-perdidas input").each(function () {
        let input = $(this);
        let id = input.attr("id");
        let valor = input.val();
        jsonData.push({ 
          id: id,
          valor: valor,
        });
    });
    return JSON.stringify(jsonData);
  }
  function generarIDUnico() {
      // Generar un ID único utilizando un timestamp y un número aleatorio
      var timestamp = new Date().getTime();
      var numeroAleatorio = Math.floor(Math.random() * 1000);
      var idUnico = "id" + timestamp + numeroAleatorio;
      return idUnico;
  }
</script>    