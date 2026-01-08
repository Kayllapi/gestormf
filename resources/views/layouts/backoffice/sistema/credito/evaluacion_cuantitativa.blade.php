<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/credito/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'evaluacion_cuantitativa',
              evaluacion_meses : json_evaluacion_meses(),
              balance_general: json_balance(),
              ganancia_perdida: json_ganancia_perdida(),
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
      $evaluacion_meses = $credito_evaluacion_cuantitativa ? ( $credito_evaluacion_cuantitativa->evaluacion_meses == "" ? [] : json_decode($credito_evaluacion_cuantitativa->evaluacion_meses) ) : [];
      $venta_mensual = $credito_cuantitativa_margen_venta ? ($credito_cuantitativa_margen_venta->venta_mensual + $credito_cuantitativa_margen_venta->venta_total_mensual) : '0.00';
  
      $balance_general = $credito_evaluacion_cuantitativa ? ( $credito_evaluacion_cuantitativa->balance_general == "" ? [] : json_decode($credito_evaluacion_cuantitativa->balance_general) ) : [];
      $ganancia_perdida = $credito_evaluacion_cuantitativa ? ( $credito_evaluacion_cuantitativa->ganancia_perdida == "" ? [] : json_decode($credito_evaluacion_cuantitativa->ganancia_perdida) ) : [];
      
      $resumen_deuda = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->resumen == "" ? [] : json_decode($credito_cuantitativa_deudas->resumen) ) : [];
      $resumen_adicionales = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->balance_general == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->balance_general) ) : [];
      $ganancia_adicional = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->ganancias_perdidas == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->ganancias_perdidas) ) : [];
    @endphp
    @php
      $balance_general_anterior = $credito_evaluacion_cuantitativa_anterior ? ( $credito_evaluacion_cuantitativa_anterior->balance_general == "" ? [] : json_decode($credito_evaluacion_cuantitativa_anterior->balance_general) ) : [];
      $ganancia_perdida_anterior = $credito_evaluacion_cuantitativa_anterior ? ( $credito_evaluacion_cuantitativa_anterior->ganancia_perdida == "" ? [] : json_decode($credito_evaluacion_cuantitativa_anterior->ganancia_perdida) ) : [];
      $resumen_deuda_anterior = $credito_cuantitativa_deudas_anterior ? ( $credito_cuantitativa_deudas_anterior->resumen == "" ? [] : json_decode($credito_cuantitativa_deudas_anterior->resumen) ) : [];
      $ganancia_adicional_anterior = $credito_cuantitativa_ingreso_adicional_anterior ? ( $credito_cuantitativa_ingreso_adicional_anterior->ganancias_perdidas == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional_anterior->ganancias_perdidas) ) : [];
     
    @endphp
    <div class="modal-header" style="border-bottom: 0;">
        <h5 class="modal-title">EVALUACIÓN CUANTITATIVA </h5>
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
        </div>
        <div class="col-sm-12 col-md-3">
          <div class="row">
            <label class="col-sm-3 col-form-label" style="text-align: right;">FECHA:</label>
            <div class="col-sm-7">
              <input type="date" step="any" class="form-control" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->fecha : date_format(date_create($credito->fecha),'Y-m-d') }}" disabled>
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
              <input type="text" step="any" class="form-control" value="C{{ str_pad($credito->id, 6, '0', STR_PAD_LEFT)  }}" disabled>
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
        <span class="badge d-block">III. EVALUACIÓN ECONÓMICA FINANCIERA (CUANTITATIVA):</span>
      </div>
      
      
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">3.1 CICLO DEL NEGOCIO (Actual =100%, Alta > 100%, Baja < 100%)</span>
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
              <?php $irow=0 ?>
              @foreach ($evaluacion_meses as $row)
        
                <tr>
                    <td>{{ $row->Mes }}</td>
                    <?php $icount=0 ?>
                    @foreach ($row as $header => $cellData)
                        @if ($header !== 'Mes')
                            @php
                                $value = $cellData->value;
                                $disabled = $cellData->disabled ? 'disabled' : ($view_detalle=='false' ? 'disabled' : '');
                                $color_cajatexto = $cellData->disabled ?  : ($view_detalle=='false' ? '' : 'color_cajatexto');
                            @endphp
                            <td><input type='text' valida_input_vacio 
                                       class='form-control campo_moneda color_cajatexto' 
                                       onkeyup="calcula_monto_meses(this)" 
                                       value='{{ $value }}' {{ $disabled }}></td>
                        @endif
                    <?php $icount++ ?>
                    @endforeach
                    @for($i=$icount;$i<=12;$i++)
                            @php
                                $value = '0.00';
                                $disabled = $irow==0 ? 'disabled' : ($view_detalle=='false' ? 'disabled' : '');
                                $color_cajatexto = $irow==0 ?  : ($view_detalle=='false' ? '' : 'color_cajatexto');
                            @endphp
                            <td><input type='text' valida_input_vacio 
                                       class='form-control campo_moneda color_cajatexto' 
                                       onkeyup="calcula_monto_meses(this)" 
                                       value='{{ $value }}' {{ $disabled }}></td>
                    @endfor
                </tr>
              <?php $irow++ ?>
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
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" onkeyup="calcula_monto_meses(this)" value="100" disabled></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcula_monto_meses(this)" value="0.00"></td>
              </tr>
            @endif
            </tbody>
          </table>
          <div class="row mt-2 d-none">
            <label class="col-sm-4 col-form-label" style="text-align: right;">MARGEN DE VENTAS TOTAL CALCULADO:</label>
            <div class="col-sm-1">
              <input type="text" step="any" class="form-control" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->margen_venta_calculado : '0.00' }}" id="margen_venta_calculado" disabled>
            </div>
            <div class="col-sm-1">
              <input type="text" step="any" class="form-control" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->margen_giro_economico : '0.00'}}" id="margen_venta_giro_economico" disabled>
            </div>
            <div id="error_margen_venta" class="col-sm-12 alert alert-danger mt-2 d-none" style="background-color: #ff6666;border-color: #ff6666;color: #000;font-weight: bold;">EL MARGEN DE VENTA CALCULADO NO PUEDE SER SUPERIOR AL DEL GIRO ECONÓMICO ({{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->margen_giro_economico : '0.00'}}%)</div>
          </div>
        </div>  
      </div>
        
        
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">3.2 ESTADOS FINANCIEROS</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-6">
          <table class="table table-bordered" id="table-balance-general">
            <thead>
              <tr>
                <th colspan=2></th>
                <th>Evaluación Anterior ({{ $credito_evaluacion_cuantitativa_anterior ? $credito_evaluacion_cuantitativa_anterior->fecha : date_format(date_create($credito->fecha),'d-m-Y') }})</th>
                <th>Evaluación Actual</th>
                <th></th>
                <th></th>
              </tr>
              <tr>
                <th width="200px" colspan=2>BALANCE GENERAL</th>
                <th>Soles (S/. )</th>
                <th>Soles (S/. )</th>
                <th>Análisis Vertical(%)</th>
                <th>Análisis Horizontal (%)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan=2>Caja</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_caja', $balance_general_anterior) }}" id="evaluacion_actual_caja_anterior"  disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ encontrar_valor('evaluacion_actual_caja', $balance_general) }}" id="evaluacion_actual_caja"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_caja', $balance_general) }}" id="analisis_vertical_caja" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_caja" disabled></td>
              </tr>
              <tr>
                <td colspan=2>Bancos</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_bancos', $balance_general_anterior) }}" id="evaluacion_actual_bancos_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ encontrar_valor('evaluacion_actual_bancos', $balance_general) }}" id="evaluacion_actual_bancos"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_bancos', $balance_general) }}" id="analisis_vertical_bancos" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_bancos" disabled></td>
              </tr>
              <tr>
                <td colspan=2>Cuentas por cobrar a clientes</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_cuentas_cobrar', $balance_general_anterior) }}" id="evaluacion_actual_cuentas_cobrar_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ encontrar_valor('evaluacion_actual_cuentas_cobrar', $balance_general) }}" id="evaluacion_actual_cuentas_cobrar"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_cuentas_cobrar', $balance_general) }}" id="analisis_vertical_cuentas_cobrar" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_bancos" disabled></td>
              </tr>
              <tr>
                <td colspan=2>Adelanto a proveedores</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_adelanto_prove', $balance_general_anterior) }}" id="evaluacion_actual_adelanto_prove_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ encontrar_valor('evaluacion_actual_adelanto_prove', $balance_general) }}" id="evaluacion_actual_adelanto_prove"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_adelanto_prove', $balance_general) }}" id="analisis_vertical_adelanto_prove" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_adelanto_prove" disabled></td>
              </tr>
              <tr>
                <td colspan=2>Inventarios</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ $credito_cuantitativa_inventario_anterior ? $credito_cuantitativa_inventario_anterior->total_inventario : '0.00' }}" id="evaluacion_actual_inventario_anterior" disabled></td>
                <td evaluacion_actual><input type="text" class="form-control campo_moneda" value="{{ $credito_cuantitativa_inventario ? $credito_cuantitativa_inventario->total_inventario : '0.00' }}" id="evaluacion_actual_inventario" disabled></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_inventario', $balance_general) }}" id="analisis_vertical_inventario" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_inventario" disabled></td>
              </tr>
              <tr>
                <td colspan=2><b><u>ACTIVO CORRIENTE</u></b></td>
                <td evaluacion_anterior><u><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_activo_corriente', $balance_general_anterior) }}" id="evaluacion_actual_activo_corriente_anterior" disabled><u></td>
                <td evaluacion_actual><u><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_activo_corriente', $balance_general) }}" id="evaluacion_actual_activo_corriente" disabled><u></td>
                <td analisis_vertical><u><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_activo_corriente', $balance_general) }}" id="analisis_vertical_activo_corriente" disabled><u></td>
                <td analisis_horizontal><u><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_activo_corriente" disabled><u></td>
              </tr>
              <tr>
                <td colspan=2>Activo inmueble</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ $credito_cuantitativa_inventario_anterior ? $credito_cuantitativa_inventario_anterior->total_inmuebles : '0.00' }}" id="evaluacion_actual_activo_inmueble_anterior" disabled></td>
                <td evaluacion_actual><input type="text" class="form-control campo_moneda" value="{{ $credito_cuantitativa_inventario ? $credito_cuantitativa_inventario->total_inmuebles : '0.00' }}" id="evaluacion_actual_activo_inmueble" disabled></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_activo_inmueble', $balance_general) }}" id="analisis_vertical_activo_inmueble" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_activo_inmueble" disabled></td>
              </tr>
              <tr>
                <td colspan=2>Activo mueble</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ $credito_cuantitativa_inventario_anterior ? $credito_cuantitativa_inventario_anterior->total_muebles : '0.00' }}" id="evaluacion_actual_activo_mueble_anterior" disabled></td>
                <td evaluacion_actual><input type="text" class="form-control campo_moneda" value="{{ $credito_cuantitativa_inventario ? $credito_cuantitativa_inventario->total_muebles : '0.00' }}" id="evaluacion_actual_activo_mueble" disabled></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_activo_mueble', $balance_general) }}" id="analisis_vertical_activo_mueble" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_activo_mueble" disabled></td>
              </tr>
              <tr>
                <td colspan=2><b><u>ACTIVO NO CORRIENTE</u></b></td>
                <td evaluacion_anterior><u><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_activo_nocorriente', $balance_general_anterior) }}" id="evaluacion_actual_activo_nocorriente_anterior" disabled></u></td>
                <td evaluacion_actual><u><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_activo_nocorriente', $balance_general) }}" id="evaluacion_actual_activo_nocorriente" disabled></u></td>
                <td analisis_vertical><u><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_activo_nocorriente', $balance_general) }}" id="analisis_vertical_activo_nocorriente" disabled></u></td>
                <td analisis_horizontal><u><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_activo_nocorriente" disabled></u></td>
              </tr>
              <tr>
                <td colspan=2 style="background-color: #c8c8c8 !important;
                color: #000 !important;"><b class="doble-subrayado">TOTAL ACTIVO</b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" evaluacion_anterior><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_total_activo', $balance_general_anterior) }}" id="evaluacion_actual_total_activo_anterior" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" evaluacion_actual><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_total_activo', $balance_general) }}" id="evaluacion_actual_total_activo" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" analisis_vertical><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_total_activo', $balance_general) }}" id="analisis_vertical_total_activo" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" analisis_horizontal><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_total_activo" disabled></b></td>
              </tr>
              <tr>
                <td colspan=2>Cuentas por pagar a proveedores</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_pagos_proveedor', $balance_general_anterior) }}" id="evaluacion_actual_pagos_proveedor_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ encontrar_valor('evaluacion_actual_pagos_proveedor', $balance_general) }}" id="evaluacion_actual_pagos_proveedor"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_pagos_proveedor', $balance_general) }}" id="analisis_vertical_pagos_proveedor" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_pagos_proveedor" disabled></td>
              </tr>
              <tr>
                <td rowspan=2>Pasivos financieros a corto plazo</td>
                <td >E. Reguladas</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ number_format(encontrar_valor('comercial_er_cplazo', $resumen_deuda_anterior) + encontrar_valor('mes_er_cplazo', $resumen_deuda_anterior), 2, '.', '') }}" id="evaluacion_actual_pasivo_corto_regulada_anterior" disabled></td>
                <td evaluacion_actual><input type="text" class="form-control campo_moneda" value="{{ number_format(encontrar_valor('comercial_er_cplazo', $resumen_deuda) + encontrar_valor('mes_er_cplazo', $resumen_deuda), 2, '.', '') }}" id="evaluacion_actual_pasivo_corto_regulada" disabled></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_pasivo_corto_regulada', $balance_general) }}" id="analisis_vertical_pasivo_corto_regulada" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_pasivo_corto_regulada" disabled></td>
              </tr>
              <tr>
                <td>E. No Reguladas</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ number_format(encontrar_valor('comercial_enr_cplazo', $resumen_deuda_anterior) + encontrar_valor('mes_enr_cplazo', $resumen_deuda_anterior), 2, '.', '') }}" id="evaluacion_actual_pasivo_corto_noregulada_anterior" disabled></td>
                <td evaluacion_actual><input type="text" class="form-control campo_moneda" value="{{ number_format(encontrar_valor('comercial_enr_cplazo', $resumen_deuda) + encontrar_valor('mes_enr_cplazo', $resumen_deuda), 2, '.', '') }}" id="evaluacion_actual_pasivo_corto_noregulada" disabled></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_pasivo_corto_noregulada', $balance_general) }}" id="analisis_vertical_pasivo_corto_noregulada" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_pasivo_corto_noregulada" disabled></td>
              </tr>
              <tr>
                <td colspan=2>Impuestos por pagar</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_impuestos', $balance_general_anterior) }}" id="evaluacion_actual_impuestos_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ encontrar_valor('evaluacion_actual_impuestos', $balance_general) }}" id="evaluacion_actual_impuestos"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_impuestos', $balance_general) }}" id="analisis_vertical_impuestos" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_impuestos" disabled></td>
              </tr>
              <tr>
                <td colspan=2>Otras cuentas por pagar</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_otras_cuentas', $balance_general_anterior) }}" id="evaluacion_actual_otras_cuentas_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ encontrar_valor('evaluacion_actual_otras_cuentas', $balance_general) }}" id="evaluacion_actual_otras_cuentas"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_otras_cuentas', $balance_general) }}" id="analisis_vertical_otras_cuentas" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_otras_cuentas" disabled></td>
              </tr>
              <tr>
                <td colspan=2><b><u>PASIVO CORRIENTE</u></b></td>
                <td evaluacion_anterior><u><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_pasivo_corriente', $balance_general_anterior) }}" id="evaluacion_actual_pasivo_corriente_anterior" disabled></u></td>
                <td evaluacion_actual><u><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_pasivo_corriente', $balance_general) }}" id="evaluacion_actual_pasivo_corriente" disabled></u></td>
                <td analisis_vertical><u><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_pasivo_corriente', $balance_general) }}" id="analisis_vertical_pasivo_corriente" disabled></u></td>
                <td analisis_horizontal><u><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_pasivo_corriente" disabled></u></td>
              </tr>
              <tr>
                <td rowspan=2>Pasivo Fin. a Largo.Plazo</td>
                <td>E. Reguladas</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ number_format(encontrar_valor('comercial_er_lplazo', $resumen_deuda_anterior) + encontrar_valor('mes_er_lplazo', $resumen_deuda_anterior), 2, '.', '') }}" id="evaluacion_actual_pasivo_largo_regulada_anterior" disabled></td>
                <td evaluacion_actual><input type="text" class="form-control campo_moneda" value="{{ number_format(encontrar_valor('comercial_er_lplazo', $resumen_deuda) + encontrar_valor('mes_er_lplazo', $resumen_deuda), 2, '.', '') }}" id="evaluacion_actual_pasivo_largo_regulada" disabled></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_pasivo_largo_regulada', $balance_general) }}" id="analisis_vertical_pasivo_largo_regulada" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_pasivo_largo_regulada" disabled></td>
              </tr>
              <tr>
                <td>E. No Reguladas</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ number_format(encontrar_valor('comercial_enr_lplazo', $resumen_deuda_anterior) + encontrar_valor('mes_enr_lplazo', $resumen_deuda_anterior), 2, '.', '') }}" id="evaluacion_actual_pasivo_largo_noregulada_anterior" disabled></td>
                <td evaluacion_actual><input type="text" class="form-control campo_moneda" value="{{ number_format(encontrar_valor('comercial_enr_lplazo', $resumen_deuda) + encontrar_valor('mes_enr_lplazo', $resumen_deuda), 2, '.', '') }}" id="evaluacion_actual_pasivo_largo_noregulada" disabled></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_pasivo_largo_noregulada', $balance_general) }}" id="analisis_vertical_pasivo_largo_noregulada" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_pasivo_largo_noregulada" disabled></td>
              </tr>
              <tr>
                <td colspan=2><b><u>PASIVO NO CORRIENTE</u></b></td>
                <td evaluacion_anterior><u><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_pasivo_nocorriente', $balance_general_anterior) }}" id="evaluacion_actual_pasivo_nocorriente_anterior" disabled></u></td>
                <td evaluacion_actual><u><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_pasivo_nocorriente', $balance_general) }}" id="evaluacion_actual_pasivo_nocorriente" disabled></u></td>
                <td analisis_vertical><u><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_pasivo_nocorriente', $balance_general) }}" id="analisis_vertical_pasivo_nocorriente" disabled></u></td>
                <td analisis_horizontal><u><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_pasivo_nocorriente" disabled></u></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" colspan=2><b class="doble-subrayado">TOTAL PASIVO</b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" evaluacion_anterior><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_total_pasivo', $balance_general_anterior) }}" id="evaluacion_actual_total_pasivo_anterior" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" evaluacion_actual><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_total_pasivo', $balance_general) }}" id="evaluacion_actual_total_pasivo" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" analisis_vertical><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_total_pasivo', $balance_general) }}" id="analisis_vertical_total_pasivo" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" analisis_horizontal><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_total_pasivo" disabled></b></td>
              </tr>
              <tr>
                <td colspan=2>Capital social</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_capital_social', $balance_general_anterior) }}" id="evaluacion_actual_capital_social_anterior" disabled></td>
                <td evaluacion_actual><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_capital_social', $balance_general) }}" id="evaluacion_actual_capital_social" disabled></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_capital_social', $balance_general) }}" id="analisis_vertical_capital_social" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_capital_social" disabled></td>
              </tr>
              <tr>
                <td colspan=2>Utilidades acumuladas</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_utilidad_acumulada', $balance_general_anterior) }}" id="evaluacion_actual_utilidad_acumulada_anterior" disabled></td>
                <td evaluacion_actual><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_utilidad_acumulada', $balance_general) }}" id="evaluacion_actual_utilidad_acumulada" disabled></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_utilidad_acumulada', $balance_general) }}" id="analisis_vertical_utilidad_acumulada" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_utilidad_acumulada" disabled></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" colspan=2><b class="doble-subrayado">TOTAL PATRIMONIO</b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" evaluacion_anterior><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_patrimonio', $balance_general_anterior) }}" id="evaluacion_actual_patrimonio_anterior" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" evaluacion_actual><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_patrimonio', $balance_general) }}" id="evaluacion_actual_patrimonio" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" analisis_vertical><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_patrimonio', $balance_general) }}" id="analisis_vertical_patrimonio" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" analisis_horizontal><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_patrimonio"  disabled></b></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" colspan=2><b class="doble-subrayado">TOTAL PASIVO + PATRIMONIO</b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" evaluacion_anterior><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_pasivo_patrimonio', $balance_general_anterior) }}" id="evaluacion_actual_pasivo_patrimonio_anterior" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" evaluacion_actual><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_pasivo_patrimonio', $balance_general) }}" id="evaluacion_actual_pasivo_patrimonio" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" analisis_vertical><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_pasivo_patrimonio', $balance_general) }}" id="analisis_vertical_pasivo_patrimonio" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" analisis_horizontal><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_pasivo_patrimonio" disabled></b></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-sm-12 col-md-6">
          <table class="table table-bordered nohover" id="table-estado-ganancias-perdidas">
            <thead>
              <tr>
                <th></th>
                <th>Evaluación Anterior ({{ $credito_evaluacion_cuantitativa_anterior ? $credito_evaluacion_cuantitativa_anterior->fecha : date_format(date_create($credito->fecha),'d-m-Y') }})</th>
                <th>Evaluación Actual</th>
                <th></th>
                <th></th>
              </tr>
              <tr>
                <th width="200px">ESTADO DE GANANCIAS Y PERDIDAS</th>
                <th>Soles (S/. )</th>
                <th>Soles (S/. )</th>
                <th>Análisis Vertical(%)</th>
                <th>Análisis Horizontal (%)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" descripcion><b><u>VENTAS MENSUALES</u></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" evaluacion_anterior><u><input type="text" class="form-control campo_moneda" value="{{ number_format(($credito_cuantitativa_margen_venta_anterior ? $credito_cuantitativa_margen_venta_anterior->venta_mensual : 0) + ($credito_cuantitativa_margen_venta_anterior ? $credito_cuantitativa_margen_venta_anterior->venta_total_mensual : 0), 2, '.', '') }}" id="evaluacion_actual_ganancia_ventamensual_anterior" disabled></u></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" evaluacion_actual><u><input type="text" class="form-control campo_moneda" value="{{ number_format(($credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->venta_mensual : 0) + ($credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->venta_total_mensual : 0), 2, '.', '') }}" id="evaluacion_actual_ganancia_ventamensual" disabled></u></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" analisis_vertical><u><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_ventamensual', $ganancia_perdida) }}" id="analisis_vertical_ventamensual" disabled></u></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" analisis_horizontal><u><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_ventamensual" disabled></u></td>
              </tr>
              <tr>
                <td descripcion>Costo de venta (C. de producción)</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_costo_venta', $ganancia_perdida_anterior) }}" id="evaluacion_actual_ganancia_costo_venta_anterior" disabled></td>
                <td evaluacion_actual><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_costo_venta', $ganancia_perdida) }}" id="evaluacion_actual_ganancia_costo_venta" disabled></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_costo_venta', $ganancia_perdida) }}" id="analisis_vertical_costo_venta" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_costo_venta" disabled></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" descripcion><b class="doble-subrayado">UTILIDAD BRUTA</b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" evaluacion_anterior><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" id="evaluacion_actual_ganancia_utilidad_bruta_anterior" value="{{ number_format(( $credito_cuantitativa_margen_venta_anterior ? $credito_cuantitativa_margen_venta_anterior->margen_ventas : 0 ) + ( $credito_cuantitativa_margen_venta_anterior ? $credito_cuantitativa_margen_venta_anterior->margen_ventas_mensual : 0 ), 2, '.', '') }}" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" evaluacion_actual><b class="doble-subrayado"><input type="text" class="form-control campo_moneda"  id="evaluacion_actual_ganancia_utilidad_bruta" value="{{ number_format(( $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->margen_ventas : 0 ) + ( $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->margen_ventas_mensual : 0 ), 2, '.', '') }}" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" analisis_vertical><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_utilidad_bruta', $ganancia_perdida) }}" id="analisis_vertical_utilidad_bruta" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" analisis_horizontal><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_utilidad_bruta" disabled></b></td>
              </tr>
              <tr>
                <td descripcion>Gastos de personal administrativo</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_gasto_admin', $ganancia_perdida_anterior) }}" id="evaluacion_actual_ganancia_gasto_admin_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_gasto_admin', $ganancia_perdida) }}" id="evaluacion_actual_ganancia_gasto_admin"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_gasto_admin', $ganancia_perdida) }}" id="analisis_vertical_gasto_admin" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_gasto_admin" disabled></td>
              </tr>
              <tr>
                <td descripcion>Gastos de personal de ventas</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_gasto_personal', $ganancia_perdida_anterior) }}" id="evaluacion_actual_ganancia_gasto_personal_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_gasto_personal', $ganancia_perdida) }}" id="evaluacion_actual_ganancia_gasto_personal"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_gasto_personal', $ganancia_perdida) }}" id="analisis_vertical_gasto_personal" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_gasto_personal" disabled></td>
              </tr>
              <tr>
                <td descripcion><b>Servicios:</b></td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_servicios', $ganancia_perdida_anterior) }}" id="evaluacion_actual_ganancia_servicios_anterior" disabled></td>
                <td evaluacion_actual><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_servicios', $ganancia_perdida) }}" id="evaluacion_actual_ganancia_servicios" disabled></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_servicios', $ganancia_perdida) }}" id="analisis_vertical_servicios" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_servicios" disabled></td>
              </tr>
              <tr>
                <td descripcion>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Luz</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_servicio_luz', $ganancia_perdida_anterior) }}" id="evaluacion_actual_ganancia_servicio_luz_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_servicio_luz', $ganancia_perdida) }}" id="evaluacion_actual_ganancia_servicio_luz"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_servicio_luz', $ganancia_perdida) }}" id="analisis_vertical_servicio_luz" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_servicio_luz" disabled></td>
              </tr>
              <tr>
                <td descripcion>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Agua</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_servicio_agua', $ganancia_perdida_anterior) }}" id="evaluacion_actual_ganancia_servicio_agua_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_servicio_agua', $ganancia_perdida) }}" id="evaluacion_actual_ganancia_servicio_agua"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_servicio_agua', $ganancia_perdida) }}" id="analisis_vertical_servicio_agua" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_servicio_agua" disabled></td>
              </tr>
              <tr>
                <td descripcion>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Telefono/internet</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_servicio_internet', $ganancia_perdida_anterior) }}" id="evaluacion_actual_ganancia_servicio_internet_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_servicio_internet', $ganancia_perdida) }}" id="evaluacion_actual_ganancia_servicio_internet"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_servicio_internet', $ganancia_perdida) }}" id="analisis_vertical_servicio_internet" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_servicio_internet" disabled></td>
              </tr>
              <tr>
                <td descripcion>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- T. celular</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_servicio_celular', $ganancia_perdida_anterior) }}" id="evaluacion_actual_ganancia_servicio_celular_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_servicio_celular', $ganancia_perdida) }}" id="evaluacion_actual_ganancia_servicio_celular"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_servicio_celular', $ganancia_perdida) }}" id="analisis_vertical_servicio_celular" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_servicio_celular" disabled></td>
              </tr>
              <tr>
                <td descripcion>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Cable</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_servicio_cable', $ganancia_perdida_anterior) }}" id="evaluacion_actual_ganancia_servicio_cable_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_servicio_cable', $ganancia_perdida) }}" id="evaluacion_actual_ganancia_servicio_cable"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_servicio_cable', $ganancia_perdida) }}" id="analisis_vertical_servicio_cable" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_servicio_cable" disabled></td>
              </tr>
              <tr>
                <td descripcion>Alquiler de local</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_alquiler', $ganancia_perdida_anterior) }}" id="evaluacion_actual_ganancia_alquiler_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" id="evaluacion_actual_ganancia_alquiler" value="{{ encontrar_valor('evaluacion_actual_ganancia_alquiler', $ganancia_perdida) }}"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_alquiler', $ganancia_perdida) }}" id="analisis_vertical_alquiler" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_alquiler" disabled></td>
              </tr>
              <tr>
                <td descripcion>Autoavalúo, serenazgo, parques y J.</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_autovaluo', $ganancia_perdida_anterior) }}" id="evaluacion_actual_ganancia_autovaluo_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" id="evaluacion_actual_ganancia_autovaluo" value="{{ encontrar_valor('evaluacion_actual_ganancia_autovaluo', $ganancia_perdida) }}"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_autovaluo', $ganancia_perdida) }}" id="analisis_vertical_autovaluo" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_autovaluo" disabled></td>
              </tr>
              <tr>
                <td descripcion>Transporte</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_transporte', $ganancia_perdida_anterior) }}" id="evaluacion_actual_ganancia_transporte_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_transporte', $ganancia_perdida) }}" id="evaluacion_actual_ganancia_transporte"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_transporte', $ganancia_perdida) }}" id="analisis_vertical_transporte" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_transporte" disabled></td>
              </tr>
              <tr>
                <td descripcion>Cuota de préstamo E. Reguladas</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ number_format(encontrar_valor('comercial_er_couta', $resumen_deuda_anterior) + encontrar_valor('mes_er_couta', $resumen_deuda_anterior), 2, '.', '') }}" id="evaluacion_actual_ganancia_cuota_regulada_anterior" disabled></td>
                <td evaluacion_actual><input type="text" class="form-control campo_moneda" id="evaluacion_actual_ganancia_cuota_regulada" value="{{ number_format(encontrar_valor('comercial_er_couta', $resumen_deuda) + encontrar_valor('mes_er_couta', $resumen_deuda), 2, '.', '') }}" disabled></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_cuota_regulada', $ganancia_perdida) }}" id="analisis_vertical_cuota_regulada" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_cuota_regulada" disabled></td>
              </tr>
              <tr>
                <td descripcion>Cuota de préstamo E. No Reguladas</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" id="evaluacion_actual_ganancia_cuota_noregulada_anterior" value="{{ number_format(encontrar_valor('comercial_enr_couta', $resumen_deuda_anterior) + encontrar_valor('mes_enr_couta', $resumen_deuda_anterior), 2, '.', '') }}" disabled></td>
                <td evaluacion_actual><input type="text" class="form-control campo_moneda" id="evaluacion_actual_ganancia_cuota_noregulada" value="{{ number_format(encontrar_valor('comercial_enr_couta', $resumen_deuda) + encontrar_valor('mes_enr_couta', $resumen_deuda), 2, '.', '') }}" disabled></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_cuota_noregulada', $ganancia_perdida) }}" id="analisis_vertical_cuota_noregulada" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_cuota_noregulada" disabled></td>
              </tr>
              <tr>
                <td descripcion>Sunat</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_sunat', $ganancia_perdida_anterior) }}" id="evaluacion_actual_ganancia_sunat_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_sunat', $ganancia_perdida) }}" id="evaluacion_actual_ganancia_sunat"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_sunat', $ganancia_perdida) }}" id="analisis_vertical_sunat" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_sunat" disabled></td>
              </tr>
              <tr>
                <td descripcion>Otros gastos</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_otros_gastos', $ganancia_perdida_anterior) }}" id="evaluacion_actual_ganancia_otros_gastos_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_otros_gastos', $ganancia_perdida) }}" id="evaluacion_actual_ganancia_otros_gastos"></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_otros_gastos', $ganancia_perdida) }}" id="analisis_vertical_otros_gastos" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_otros_gastos" disabled></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" descripcion><b class="doble-subrayado">TOTAL DE GASTOS OPERATIVOS</b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" evaluacion_anterior><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_gastos_op', $ganancia_perdida_anterior) }}" id="evaluacion_actual_ganancia_gastos_op_anterior" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" evaluacion_actual><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_gastos_op', $ganancia_perdida) }}" id="evaluacion_actual_ganancia_gastos_op" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" analisis_vertical><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_gastos_op', $ganancia_perdida) }}" id="analisis_vertical_gastos_op" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;" analisis_horizontal><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_gastos_op" disabled></b></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;
                color: #000 !important;" descripcion><b class="doble-subrayado">UTILIDAD NETA</b></td>
                <td style="background-color: #c8c8c8 !important;
                           color: #000 !important;" evaluacion_anterior><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_utilidad_neta', $ganancia_perdida_anterior) }}" id="evaluacion_actual_ganancia_utilidad_neta_anterior" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;
                           color: #000 !important;" evaluacion_actual><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_utilidad_neta', $ganancia_perdida) }}" id="evaluacion_actual_ganancia_utilidad_neta" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;
                           color: #000 !important;" analisis_vertical><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_utilidad_neta', $ganancia_perdida) }}" id="analisis_vertical_utilidad_neta" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;
                           color: #000 !important;" analisis_horizontal><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_utilidad_neta" disabled></b></td>
              </tr>
              <tr>
                <td descripcion>NEGOCIO ADICIONAL</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('ganancias_excedente_mensual', $ganancia_adicional_anterior) }}" id="evaluacion_actual_ganancia_negocio_adicional_anterior" disabled></td>
                <td evaluacion_actual><input type="text" valida_input_vacio class="form-control campo_moneda" value="{{ encontrar_valor('ganancias_excedente_mensual', $ganancia_adicional) }}" id="evaluacion_actual_ganancia_negocio_adicional" disabled></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_negocio_adicional', $ganancia_perdida) }}" id="analisis_vertical_negocio_adicional" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_negocio_adicional" disabled></td>
              </tr>
              <tr>
                <td descripcion>INGRESOS FIJOS</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" id="evaluacion_actual_ganancia_ingreso_fijo_anterior" value="{{ number_format($credito_cuantitativa_ingreso_adicional_anterior ? $credito_cuantitativa_ingreso_adicional_anterior->total_ingreso_adicional : 0, 2, '.', '') }}" disabled></td>
                <td evaluacion_actual><input type="text" class="form-control campo_moneda" id="evaluacion_actual_ganancia_ingreso_fijo" value="{{ number_format($credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_ingreso_adicional : 0, 2, '.', '') }}" disabled></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_ingreso_fijo', $ganancia_perdida) }}" id="analisis_vertical_ingreso_fijo" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_ingreso_fijo" disabled></td>
              </tr>
              <tr>
                <td descripcion>GASTOS FAMILIARES</td>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" id="evaluacion_actual_ganancia_gasto_familiar_anterior" value="{{ number_format($credito_evaluacion_cualitativa_anterior ? $credito_evaluacion_cualitativa_anterior->gasto_total : 0, 2, '.', '') }}" disabled></td>
                <td evaluacion_actual><input type="text" class="form-control campo_moneda" id="evaluacion_actual_ganancia_gasto_familiar" value="{{ number_format($credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_total : 0, 2, '.', '') }}" disabled></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_gasto_familiar', $ganancia_perdida) }}" id="analisis_vertical_gasto_familiar" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_gasto_familiar" disabled></td>
              </tr>
              <tr>
                <td descripcion>Cuota de Préstamos de Consumo e Hipotecarios para Vivienda (Reg. y no Reg.)</td>
                <?php
                  $consumo_total_couta_anterior = encontrar_valor('consumo_total_couta', $resumen_deuda_anterior);
                  $vivienda_total_couta_anterior = encontrar_valor('vivienda_total_couta', $resumen_deuda_anterior);
                  $total_resumen_cuotas_linea_credito2_anterior = $credito_cuantitativa_deudas_anterior ? $credito_cuantitativa_deudas_anterior->total_resumen_cuotas_linea_credito2 : 0;
                  $evaluacion_actual_ganancia_cuota_vivienda_anterior = $consumo_total_couta_anterior + $vivienda_total_couta_anterior + $total_resumen_cuotas_linea_credito2_anterior;
                ?>
                <td evaluacion_anterior><input type="text" class="form-control campo_moneda" id="evaluacion_actual_ganancia_cuota_vivienda_anterior" value="{{ number_format($evaluacion_actual_ganancia_cuota_vivienda_anterior, 2, '.', '') }}" disabled></td>
                <?php
                  $consumo_total_couta = encontrar_valor('consumo_total_couta', $resumen_deuda);
                  $vivienda_total_couta = encontrar_valor('vivienda_total_couta', $resumen_deuda);
                  $total_resumen_cuotas_linea_credito2 = $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_resumen_cuotas_linea_credito2 : 0;
                  $evaluacion_actual_ganancia_cuota_vivienda = $consumo_total_couta + $vivienda_total_couta + $total_resumen_cuotas_linea_credito2;
                ?>
                <td evaluacion_actual><input type="text" class="form-control campo_moneda" id="evaluacion_actual_ganancia_cuota_vivienda" value="{{ number_format($evaluacion_actual_ganancia_cuota_vivienda, 2, '.', '') }}" disabled></td>
                <td analisis_vertical><input type="text" class="form-control campo_moneda" value="{{ number_format(encontrar_valor('analisis_vertical_cuota_vivienda', $ganancia_perdida), 2, '.', '') }}" id="analisis_vertical_cuota_vivienda" disabled></td>
                <td analisis_horizontal><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_cuota_vivienda" disabled></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;
                color: #000 !important;" descripcion><b class="doble-subrayado">EXCEDENTE MENSUAL</b></td>
                <td style="background-color: #c8c8c8 !important;
                           color: #000 !important;" evaluacion_anterior><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_excedente_mensual', $ganancia_perdida_anterior) }}" id="evaluacion_actual_ganancia_excedente_mensual_anterior" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;
                           color: #000 !important;" evaluacion_actual><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('evaluacion_actual_ganancia_excedente_mensual', $ganancia_perdida) }}" id="evaluacion_actual_ganancia_excedente_mensual" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;
                           color: #000 !important;" analisis_vertical><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('analisis_vertical_excedente_mensual', $ganancia_perdida) }}" id="analisis_vertical_excedente_mensual" disabled></b></td>
                <td style="background-color: #c8c8c8 !important;
                           color: #000 !important;" analisis_horizontal><b class="doble-subrayado"><input type="text" class="form-control campo_moneda" value="0.00" id="analisis_horizontal_excedente_mensual" disabled></b></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">3.3 MOVIMIENTO COMERCIAL</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-6">
          <?php 
          $dias_ventas_mensual = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->dias_ventas_mensual : '';
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
                <td width="100px"><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->credito_cobrando_venta_mensual : '0.00' }}" id="credito_cobrando_venta_mensual" onkeyup="calc_movimiento_comercial()"></td>
                <td width="100px"><div class="input-group">
                        <input type="text" class="form-control campo_moneda" 
                           value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->credito_porcentaje_venta_mensual : '0.00' }}" 
                           id="credito_porcentaje_venta_mensual" disabled>
                        <span class="input-group-text">%</span>
                      </div>
                  
                </td>
              </tr>
              <tr>
                <td colspan=3>Al Contado</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->contado_cobrando_venta_mensual : '0.00' }}" id="contado_cobrando_venta_mensual" disabled></td>
                <td>
                  <div class="input-group">
                        <input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->contado_porcentaje_venta_mensual : '0.00' }}" 
                         id="contado_porcentaje_venta_mensual" disabled>
                        <span class="input-group-text">%</span>
                      </div>
                  
                </td>
              </tr>
            </tbody>
          </table>  
        </div>  
        <div class="col-sm-12 col-md-6">
          <?php 
          $dias_compras_mensual = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->dias_compras_mensual : '';
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
                <td width="100px">total al mes</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->credito_cobrando_compra_mensual : '0.00' }}" id="credito_cobrando_compra_mensual" onkeyup="calc_movimiento_comercial()"></td>
                <td width="100px">
                  <div class="input-group">
                        <input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->credito_porcentaje_compra_mensual : '0.00' }}" 
                         id="credito_porcentaje_compra_mensual" disabled>
                        <span class="input-group-text">%</span>
                      </div>
                  
                </td>
              </tr>
              <tr>
                <td colspan=3>Al Contado</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->contado_cobrando_compra_mensual : '0.00' }}" id="contado_cobrando_compra_mensual" disabled></td>
                <td>
                  <div class="input-group">
                        <input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->contado_porcentaje_compra_mensual : '0.00' }}" 
                         id="contado_porcentaje_compra_mensual" disabled>
                        <span class="input-group-text">%</span>
                      </div>
                  
                </td>
              </tr>
            </tbody>
          </table>
        </div>  
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">3.4 PRINCIPALES RATIOS FINANCIEROS</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-6">
          <table class="table">
            <tbody>
              <tr>
                <td>Rentabilidad del negocio</td>
                <td>%</td>
                <td><input type="text" class="form-control campo_moneda" id="ratio_re_negocio" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_negocio : '0.00' }}" disabled></td>
              </tr>
              <tr>
                <td>Rentabilidad de la unidad familiar</td>
                <td>Veces</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_unidadfamiliar : '0.00' }}" id="ratio_re_unidadfamiliar" disabled></td>
              </tr>
              <tr>
                <td>Rentabilidad patrimonial (ROE)</td>
                <td>%</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_patrimonial : '0.00' }}" id="ratio_re_patrimonial" disabled></td>
              </tr>
              <tr>
                <td>Rentabilidad de los activos (ROA)</td>
                <td>%</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_activos : '0.00' }}" id="ratio_re_activos" disabled></td>
              </tr>
              <tr>
                <td>Rentabilidad de las ventas (ROS)</td>
                <td>%</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_ventas : '0.00' }}" id="ratio_re_ventas" disabled></td>
              </tr>
              <tr>
                <td>Préstamo / capital de trabajo Neto</td>
                <td>%</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_prestamo : '0.00' }}" id="ratio_re_prestamo" disabled></td>
              </tr>
              <tr>
                <td>Capital de trabajo</td>
                <td>S/</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_capital : '0.00' }}" id="ratio_re_capital" disabled></td>
              </tr>
              <tr>
                <td>Liquidez</td>
                <td>Veces</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_liquidez : '0.00' }}" id="ratio_re_liquidez" disabled></td>
              </tr>
            </tbody>
          </table>
          <script>
            cal_ratios_finacieros();
            function cal_ratios_finacieros(){
              let evaluacion_actual_ganancia_utilidad_neta = parseFloat($('#evaluacion_actual_ganancia_utilidad_neta').val());
              let evaluacion_actual_activo_corriente = parseFloat($('#evaluacion_actual_activo_corriente').val());
              let evaluacion_actual_pasivo_corriente = parseFloat($('#evaluacion_actual_pasivo_corriente').val());
              
              let ratio_re_negocio = ( evaluacion_actual_ganancia_utilidad_neta / (evaluacion_actual_activo_corriente - evaluacion_actual_pasivo_corriente) ) * 100;
              if(isNaN(ratio_re_negocio) ||  ratio_re_negocio == Infinity || ratio_re_negocio == -Infinity){
                  ratio_re_negocio = 0;
              }
              $('#ratio_re_negocio').val(ratio_re_negocio.toFixed(2));
              
              let evaluacion_actual_ganancia_negocio_adicional = parseFloat($('#evaluacion_actual_ganancia_negocio_adicional').val());
              let evaluacion_actual_ganancia_ingreso_fijo = parseFloat($('#evaluacion_actual_ganancia_ingreso_fijo').val());
              let evaluacion_actual_ganancia_gasto_familiar = parseFloat($('#evaluacion_actual_ganancia_gasto_familiar').val());
              
              let ratio_re_unidadfamiliar = ( evaluacion_actual_ganancia_utilidad_neta + evaluacion_actual_ganancia_negocio_adicional + evaluacion_actual_ganancia_ingreso_fijo ) / evaluacion_actual_ganancia_gasto_familiar;
              if(isNaN(ratio_re_unidadfamiliar) ||  ratio_re_unidadfamiliar == Infinity || ratio_re_unidadfamiliar == -Infinity){
                  ratio_re_unidadfamiliar = 0;
              }
              $('#ratio_re_unidadfamiliar').val(ratio_re_unidadfamiliar.toFixed(2));
              
              let evaluacion_actual_patrimonio = parseFloat($('#evaluacion_actual_patrimonio').val());
              let ratio_re_patrimonial = ( evaluacion_actual_ganancia_utilidad_neta/evaluacion_actual_patrimonio ) * 100;
              if(isNaN(ratio_re_patrimonial) ||  ratio_re_patrimonial == Infinity || ratio_re_patrimonial == -Infinity){
                  ratio_re_patrimonial = 0;
              }
              $('#ratio_re_patrimonial').val(ratio_re_patrimonial.toFixed(2));
              
              let evaluacion_actual_total_activo = parseFloat($('#evaluacion_actual_total_activo').val());
              let ratio_re_activos = ( evaluacion_actual_ganancia_utilidad_neta/evaluacion_actual_total_activo ) * 100;
              if(isNaN(ratio_re_activos) ||  ratio_re_activos == Infinity || ratio_re_activos == -Infinity){
                  ratio_re_activos = 0;
              }
              $('#ratio_re_activos').val(ratio_re_activos.toFixed(2));
              
              let evaluacion_actual_ganancia_ventamensual = parseFloat($('#evaluacion_actual_ganancia_ventamensual').val());
              let ratio_re_ventas = ( evaluacion_actual_ganancia_utilidad_neta/evaluacion_actual_ganancia_ventamensual ) * 100;
              if(isNaN(ratio_re_ventas) ||  ratio_re_ventas == Infinity || ratio_re_ventas == -Infinity){
                  ratio_re_ventas = 0;
              }
              $('#ratio_re_ventas').val(ratio_re_ventas.toFixed(2));
              
              let monto_propuesta = parseFloat('{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->propuesta_monto : 0 }}')
              
              let ratio_re_prestamo = ( monto_propuesta / (evaluacion_actual_activo_corriente - evaluacion_actual_pasivo_corriente) ) * 100;
              if(isNaN(ratio_re_prestamo) ||  ratio_re_prestamo == Infinity || ratio_re_prestamo == -Infinity){
                  ratio_re_prestamo = 0;
              }
              $('#ratio_re_prestamo').val(ratio_re_prestamo.toFixed(2));
              
              
              let ratio_re_capital = (evaluacion_actual_activo_corriente - evaluacion_actual_pasivo_corriente);
              if(isNaN(ratio_re_capital) ||  ratio_re_capital == Infinity || ratio_re_capital == -Infinity){
                  ratio_re_capital = 0;
              }
              $('#ratio_re_capital').val(ratio_re_capital.toFixed(2));
              
              let ratio_re_liquidez = (evaluacion_actual_activo_corriente / evaluacion_actual_pasivo_corriente);
              if(isNaN(ratio_re_liquidez) ||  ratio_re_liquidez == Infinity || ratio_re_liquidez == -Infinity){
                  ratio_re_liquidez = 0;
              }
              $('#ratio_re_liquidez').val(ratio_re_liquidez.toFixed(2));
              
              
              let evaluacion_actual_inventario = parseFloat($('#evaluacion_actual_inventario').val());
              let evaluacion_actual_adelanto_prove = parseFloat($('#evaluacion_actual_adelanto_prove').val());
              
              let ratio_re_liquidez_acida = (evaluacion_actual_activo_corriente - evaluacion_actual_inventario) / evaluacion_actual_pasivo_corriente;
              if(isNaN(ratio_re_liquidez_acida) ||  ratio_re_liquidez_acida == Infinity || ratio_re_liquidez_acida == -Infinity){
                  ratio_re_liquidez_acida = 0;
              }
              $('#ratio_re_liquidez_acida').val(ratio_re_liquidez_acida.toFixed(2));
              
              let evaluacion_actual_total_pasivo = parseFloat($('#evaluacion_actual_total_pasivo').val());
              let ratio_re_endeudamiento_actual = evaluacion_actual_total_pasivo / evaluacion_actual_patrimonio;
              if(isNaN(ratio_re_endeudamiento_actual) ||  ratio_re_endeudamiento_actual == Infinity || ratio_re_endeudamiento_actual == -Infinity){
                  ratio_re_endeudamiento_actual = 0;
              }
              $('#ratio_re_endeudamiento_actual').val(ratio_re_endeudamiento_actual.toFixed(2));
              
              
              let total_saldo_capital_deducciones = parseFloat('{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_saldo_capital_deducciones : 0 }}');
              let total_noregulada_saldo_capital_deducciones = parseFloat('{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_noregulada_saldo_capital_deducciones : 0 }}');
              
              let ratio_re_endeudamiento_propuesta = (evaluacion_actual_total_pasivo + monto_propuesta - total_saldo_capital_deducciones - total_noregulada_saldo_capital_deducciones) / evaluacion_actual_patrimonio ;
              if(isNaN(ratio_re_endeudamiento_propuesta) ||  ratio_re_endeudamiento_propuesta == Infinity || ratio_re_endeudamiento_propuesta == -Infinity){
                  ratio_re_endeudamiento_propuesta = 0;
              }
              $('#ratio_re_endeudamiento_propuesta').val(ratio_re_endeudamiento_propuesta.toFixed(2)); 
              
              
              let evaluacion_actual_ganancia_costo_venta = parseFloat($('#evaluacion_actual_ganancia_costo_venta').val());
              let ratio_re_rotacion_inventario = (evaluacion_actual_inventario / evaluacion_actual_ganancia_costo_venta) * 30;
              if(isNaN(ratio_re_rotacion_inventario) ||  ratio_re_rotacion_inventario == Infinity || ratio_re_rotacion_inventario == -Infinity){
                  ratio_re_rotacion_inventario = 0;
              }
              $('#ratio_re_rotacion_inventario').val(ratio_re_rotacion_inventario.toFixed(2));
              
              
              let evaluacion_actual_cuentas_cobrar = parseFloat($('#evaluacion_actual_cuentas_cobrar').val());
              let credito_cobrando_venta_mensual = parseFloat($('#credito_cobrando_venta_mensual').val());
              let ratio_re_promedio_cobranza = ( evaluacion_actual_cuentas_cobrar / credito_cobrando_venta_mensual ) * 30;

              if(isNaN(ratio_re_promedio_cobranza) ||  ratio_re_promedio_cobranza == Infinity || ratio_re_promedio_cobranza == -Infinity){
                  ratio_re_promedio_cobranza = 0;
              }
              $('#ratio_re_promedio_cobranza').val(ratio_re_promedio_cobranza.toFixed(2));
              
              let evaluacion_actual_pagos_proveedor = parseFloat($('#evaluacion_actual_pagos_proveedor').val());
              let credito_cobrando_compra_mensual = parseFloat($('#credito_cobrando_compra_mensual').val());
              let ratio_re_primedio_pago = ( evaluacion_actual_pagos_proveedor / credito_cobrando_compra_mensual ) * 30;
              if(isNaN(ratio_re_primedio_pago) ||  ratio_re_primedio_pago == Infinity || ratio_re_primedio_pago == -Infinity){
                ratio_re_primedio_pago = 0;
              }
              $('#ratio_re_primedio_pago').val(ratio_re_primedio_pago.toFixed(2));
              
            }
          </script>
        </div>   
        <div class="col-sm-12 col-md-6">
          <table class="table">
            <tbody>
              <tr>
                <td>Liquidez Ácida</td>
                <td>Veces</td>
                <td style="width:280px;"><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_liquidez_acida : '0.00' }}" id="ratio_re_liquidez_acida" disabled></td>
              </tr>
              <tr>
                <td>Endeudamiento patrimonial actual</td>
                <td>Veces</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_endeudamiento_actual : '0.00' }}" id="ratio_re_endeudamiento_actual" disabled></td>
              </tr>
              <tr>
                <td>Endeudamiento Patrim. con propuesta</td>
                <td>Veces</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_endeudamiento_propuesta : '0.00' }}" id="ratio_re_endeudamiento_propuesta" disabled></td>
              </tr>
              
              <tr>
                <td>Plazo prom.rotación de invent.</td>
                <td>Días</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_rotacion_inventario : '0.00' }}" id="ratio_re_rotacion_inventario" disabled></td>
              </tr>
              <tr>
                <td>Plazo promedio de cobranza</td>
                <td>Días</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_promedio_cobranza : '0.00' }}" id="ratio_re_promedio_cobranza" disabled></td>
              </tr>
              <tr>
                <td>Plazo promedio de pago</td>
                <td>Días</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_primedio_pago : '0.00' }}" id="ratio_re_primedio_pago" disabled></td>
              </tr>
            </tbody>
          </table>
          <table class="table">
            <tbody>
              <tr style="background-color: #efefef;">
                <td colspan=3 style="background-color: #efefef;"></td>
              </tr>
              <tr>
                <td style="border: 1px solid #a6a9ab;">CUOTA TOTAL/EXCEDENTE TOTAL. Antes de Propuesta (%)</td>
                <td style="border: 1px solid #a6a9ab;width:280px;"><input type="text" class="form-control campo_moneda" disabled value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->excedente_antes_propuesta : '0.00' }}" id="excedente_antes_propuesta"></td>
              </tr>
              <tr>
                <td style="border: 1px solid #a6a9ab;">CUOTA TOTAL/EXCEDENTE TOTAL. En Propuesta sin Deducción en Ampliacion o Compra de deuda (%)</td>
                <td style="border: 1px solid #a6a9ab;"><input type="text" class="form-control campo_moneda" disabled value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->excedente_propuesta_sin_deduccion : '0.00' }}" id="excedente_propuesta_sin_deduccion"></td>
              </tr>
              <tr>
                <td style="border: 1px solid #a6a9ab;">CUOTA TOTAL/EXCEDENTE TOTAL. En Propuesta CON Deducción en Ampliacion o Compra de deuda (%)</td>
                <td style="border: 1px solid #a6a9ab;"><input type="text" class="form-control campo_moneda" disabled value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->excedente_propuesta_con_deduccion : '0.00' }}" id="excedente_propuesta_con_deduccion"></td>
              </tr>
            </tbody>
          </table>
                  <input type="hidden" class="form-control" disabled value="{{ configuracion($tienda->id,'rango_menor')['valor'] }}" id="rango_menor">
                  <input type="hidden" class="form-control" disabled value="{{ configuracion($tienda->id,'rango_diferencia')['valor'] }}" id="rango_diferencia">
                  <input type="hidden" class="form-control" disabled value="{{ configuracion($tienda->id,'rango_tope')['valor'] }}" id="rango_tope">
                  <input type="text" class="form-control bg-success text-center" value="{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->estado_credito : '' }}" disabled id="estado_credito">
        </div>   
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">3.5 COMENTARIOS DE ASPECTOS RESALTANTES </span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <label>A. Analisis Vertical y Horizontal</label>
          <textarea {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" cols="30" rows="3" id="comentario">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->comentario : '' }}</textarea>
        </div>
      </div>

      <div class="row mt-1">
        @if($view_detalle!='false')
        <div class="col" style="flex: 0 0 0%;">
          <button type="submit" class="btn btn-success" id="btn-save-cuantitativa"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS <b>({{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->cantidad_update : 0 }})</b></button>
        </div>
        @endif
        <div class="col" style="flex: 0 0 0%;">
          <button type="button" 
                  class="btn btn-dark" 
                  onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=solicitud_cuantitativa')}}', size: 'modal-fullscreen' })"
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
  .modal-body-cualitativa .form-check-input[type=checkbox],
  .modal-body-cualitativa .select2-container--bootstrap-5 .select2-selection {
      background-color: #ffffb5;
  }
  .form-check-input:checked {
      background-color: #585858 !important;
      border-color: #585858 !important;
  }
</style>
<script>
  valida_input_vacio();
  $('input[valida_input_vacio]').on('blur', function() {
      total_balance_general();
      total_ganancias_perdidas();
  });
  $('input').on('blur', function() {
      $('#boton_imprimir').attr('disabled',true);
  });
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
  $("#table-evaluacion-meses tbody tr:eq(0) td:eq(1) input").val('{{ $venta_mensual }}');
  
  $('#table-evaluacion-meses input[onkeyup*="calcula_monto_meses(this)"]').each(function() {
    // Comprueba si el input no está en la primera columna
    if ($(this).closest('td').index() !== 0) {
      // Ejecuta la función en el input actual
      calcula_monto_meses(this);
    }
  });
  function calcula_monto_meses(e){
    //let valorBase = parseFloat($("#table-evaluacion-meses tbody tr:eq(0) td:eq(1) input").val());
    let valorBase = parseFloat('{{ $venta_mensual }}');
    let porcentaje_maximo = parseFloat("{{ configuracion($tienda->id,'ciclo_negocio_maximo')['valor'] }}");
    if (!isNaN(valorBase)) {
        let row = $(e).closest('tr');
        // Encuentra el índice de la celda en la que se hizo el cambio
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
  $("#table-balance-general tbody tr td input").on("keyup", function() {
      total_balance_general();
  });
  function total_balance_general(){
    let evaluacion_actual_caja_anterior = parseFloat($('#evaluacion_actual_caja_anterior').val());
    let evaluacion_actual_bancos_anterior = parseFloat($('#evaluacion_actual_bancos_anterior').val());
    let evaluacion_actual_cuentas_cobrar_anterior = parseFloat($('#evaluacion_actual_cuentas_cobrar_anterior').val());
    let evaluacion_actual_adelanto_prove_anterior = parseFloat($('#evaluacion_actual_adelanto_prove_anterior').val());
    let evaluacion_actual_inventario_anterior = parseFloat($('#evaluacion_actual_inventario_anterior').val());
    let evaluacion_actual_activo_corriente_anterior = parseFloat($('#evaluacion_actual_activo_corriente_anterior').val());
    let evaluacion_actual_activo_inmueble_anterior = parseFloat($('#evaluacion_actual_activo_inmueble_anterior').val());
    let evaluacion_actual_activo_mueble_anterior = parseFloat($('#evaluacion_actual_activo_mueble_anterior').val());
    let evaluacion_actual_activo_nocorriente_anterior = parseFloat($('#evaluacion_actual_activo_nocorriente_anterior').val());
    let evaluacion_actual_total_activo_anterior = parseFloat($('#evaluacion_actual_total_activo_anterior').val());
    
    let evaluacion_actual_caja = parseFloat($('#evaluacion_actual_caja').val());
    let evaluacion_actual_bancos = parseFloat($('#evaluacion_actual_bancos').val());
    let evaluacion_actual_cuentas_cobrar = parseFloat($('#evaluacion_actual_cuentas_cobrar').val());
    let evaluacion_actual_adelanto_prove = parseFloat($('#evaluacion_actual_adelanto_prove').val());
    let evaluacion_actual_inventario = parseFloat($('#evaluacion_actual_inventario').val());

    let evaluacion_actual_activo_corriente = evaluacion_actual_caja + evaluacion_actual_bancos + evaluacion_actual_cuentas_cobrar + evaluacion_actual_adelanto_prove + evaluacion_actual_inventario;
    $('#evaluacion_actual_activo_corriente').val(evaluacion_actual_activo_corriente.toFixed(2))


    let evaluacion_actual_activo_inmueble = parseFloat($('#evaluacion_actual_activo_inmueble').val());
    let evaluacion_actual_activo_mueble = parseFloat($('#evaluacion_actual_activo_mueble').val());

    let evaluacion_actual_activo_nocorriente = evaluacion_actual_activo_inmueble + evaluacion_actual_activo_mueble;
    $('#evaluacion_actual_activo_nocorriente').val(evaluacion_actual_activo_nocorriente.toFixed(2))

    let patrimonio_ingresos_adicionales = parseFloat({{ encontrar_valor('balance_total_patrimonio', $resumen_adicionales) }})
    let evaluacion_actual_total_activo = evaluacion_actual_activo_corriente + evaluacion_actual_activo_nocorriente + patrimonio_ingresos_adicionales;
    $('#evaluacion_actual_total_activo').val(evaluacion_actual_total_activo.toFixed(2))


    let evaluacion_actual_pagos_proveedor_anterior = parseFloat($('#evaluacion_actual_pagos_proveedor_anterior').val());
    let evaluacion_actual_pasivo_corto_regulada_anterior = parseFloat($('#evaluacion_actual_pasivo_corto_regulada_anterior').val());
    let evaluacion_actual_pasivo_corto_noregulada_anterior = parseFloat($('#evaluacion_actual_pasivo_corto_noregulada_anterior').val());
    let evaluacion_actual_impuestos_anterior = parseFloat($('#evaluacion_actual_impuestos_anterior').val());
    let evaluacion_actual_otras_cuentas_anterior = parseFloat($('#evaluacion_actual_otras_cuentas_anterior').val());
    let evaluacion_actual_pasivo_corriente_anterior = parseFloat($('#evaluacion_actual_pasivo_corriente_anterior').val());
    let evaluacion_actual_pasivo_largo_regulada_anterior = parseFloat($('#evaluacion_actual_pasivo_largo_regulada_anterior').val());
    let evaluacion_actual_pasivo_largo_noregulada_anterior = parseFloat($('#evaluacion_actual_pasivo_largo_noregulada_anterior').val());
    let evaluacion_actual_pasivo_nocorriente_anterior = parseFloat($('#evaluacion_actual_pasivo_nocorriente_anterior').val());
    let evaluacion_actual_total_pasivo_anterior = parseFloat($('#evaluacion_actual_total_pasivo_anterior').val());
    let evaluacion_actual_utilidad_acumulada_anterior = parseFloat($('#evaluacion_actual_utilidad_acumulada_anterior').val());
    let evaluacion_actual_capital_social_anterior = parseFloat($('#evaluacion_actual_capital_social_anterior').val());
    let evaluacion_actual_patrimonio_anterior = parseFloat($('#evaluacion_actual_patrimonio_anterior').val());
    let evaluacion_actual_pasivo_patrimonio_anterior = parseFloat($('#evaluacion_actual_pasivo_patrimonio_anterior').val());

    let evaluacion_actual_pagos_proveedor = parseFloat($('#evaluacion_actual_pagos_proveedor').val());
    let evaluacion_actual_pasivo_corto_regulada = parseFloat($('#evaluacion_actual_pasivo_corto_regulada').val());
    let evaluacion_actual_pasivo_corto_noregulada = parseFloat($('#evaluacion_actual_pasivo_corto_noregulada').val());
    let evaluacion_actual_impuestos = parseFloat($('#evaluacion_actual_impuestos').val());
    let evaluacion_actual_otras_cuentas = parseFloat($('#evaluacion_actual_otras_cuentas').val());

    let evaluacion_actual_pasivo_corriente = evaluacion_actual_pagos_proveedor + evaluacion_actual_pasivo_corto_regulada + evaluacion_actual_pasivo_corto_noregulada + evaluacion_actual_impuestos + evaluacion_actual_otras_cuentas;
    $('#evaluacion_actual_pasivo_corriente').val(evaluacion_actual_pasivo_corriente.toFixed(2))

    let evaluacion_actual_pasivo_largo_regulada = parseFloat($('#evaluacion_actual_pasivo_largo_regulada').val());
    let evaluacion_actual_pasivo_largo_noregulada = parseFloat($('#evaluacion_actual_pasivo_largo_noregulada').val());

    let evaluacion_actual_pasivo_nocorriente = evaluacion_actual_pasivo_largo_regulada + evaluacion_actual_pasivo_largo_noregulada;
    $('#evaluacion_actual_pasivo_nocorriente').val(evaluacion_actual_pasivo_nocorriente.toFixed(2))

    let evaluacion_actual_total_pasivo = evaluacion_actual_pasivo_corriente + evaluacion_actual_pasivo_nocorriente;
    $('#evaluacion_actual_total_pasivo').val(evaluacion_actual_total_pasivo.toFixed(2))


    let evaluacion_actual_ganancia_utilidad_neta = parseFloat($('#evaluacion_actual_ganancia_utilidad_neta').val());
    let evaluacion_actual_utilidad_acumulada = evaluacion_actual_ganancia_utilidad_neta;
    $('#evaluacion_actual_utilidad_acumulada').val(evaluacion_actual_utilidad_acumulada.toFixed(2))


    let evaluacion_actual_capital_social = evaluacion_actual_total_activo - evaluacion_actual_total_pasivo - evaluacion_actual_utilidad_acumulada;
    $('#evaluacion_actual_capital_social').val(evaluacion_actual_capital_social.toFixed(2))

    let evaluacion_actual_patrimonio = evaluacion_actual_capital_social + evaluacion_actual_utilidad_acumulada;
    $('#evaluacion_actual_patrimonio').val(evaluacion_actual_patrimonio.toFixed(2))

    let evaluacion_actual_pasivo_patrimonio = evaluacion_actual_total_pasivo + evaluacion_actual_patrimonio;
    $('#evaluacion_actual_pasivo_patrimonio').val(evaluacion_actual_pasivo_patrimonio.toFixed(2))

    //Análisis Vertical (%)
    
    let analisis_vertical_caja = ( evaluacion_actual_caja / evaluacion_actual_total_activo ) * 100;
    if(isNaN(analisis_vertical_caja)){ analisis_vertical_caja = 0; }
    $('#analisis_vertical_caja').val(analisis_vertical_caja.toFixed(2))
    let analisis_vertical_bancos = ( evaluacion_actual_bancos / evaluacion_actual_total_activo ) * 100;
    if(isNaN(analisis_vertical_bancos)){ analisis_vertical_bancos = 0; }
    $('#analisis_vertical_bancos').val(analisis_vertical_bancos.toFixed(2))
    let analisis_vertical_cuentas_cobrar = ( evaluacion_actual_cuentas_cobrar / evaluacion_actual_total_activo ) * 100;
    if(isNaN(analisis_vertical_cuentas_cobrar)){ analisis_vertical_cuentas_cobrar = 0; }
    $('#analisis_vertical_cuentas_cobrar').val(analisis_vertical_cuentas_cobrar.toFixed(2))
    let analisis_vertical_adelanto_prove = ( evaluacion_actual_adelanto_prove / evaluacion_actual_total_activo ) * 100;
    if(isNaN(analisis_vertical_adelanto_prove)){ analisis_vertical_adelanto_prove = 0; }
    $('#analisis_vertical_adelanto_prove').val(analisis_vertical_adelanto_prove.toFixed(2))
    let analisis_vertical_inventario = ( evaluacion_actual_inventario / evaluacion_actual_total_activo ) * 100;
    if(isNaN(analisis_vertical_inventario)){ analisis_vertical_inventario = 0; }
    $('#analisis_vertical_inventario').val(analisis_vertical_inventario.toFixed(2))
    let analisis_vertical_activo_corriente = ( evaluacion_actual_activo_corriente / evaluacion_actual_total_activo ) * 100;
    if(isNaN(analisis_vertical_activo_corriente)){ analisis_vertical_activo_corriente = 0; }
    $('#analisis_vertical_activo_corriente').val(analisis_vertical_activo_corriente.toFixed(2))
    let analisis_vertical_activo_inmueble = ( evaluacion_actual_activo_inmueble / evaluacion_actual_total_activo ) * 100;
    if(isNaN(analisis_vertical_activo_inmueble)){ analisis_vertical_activo_inmueble = 0; }
    $('#analisis_vertical_activo_inmueble').val(analisis_vertical_activo_inmueble.toFixed(2))
    let analisis_vertical_activo_mueble = ( evaluacion_actual_activo_mueble / evaluacion_actual_total_activo ) * 100;
    if(isNaN(analisis_vertical_activo_mueble)){ analisis_vertical_activo_mueble = 0; }
    $('#analisis_vertical_activo_mueble').val(analisis_vertical_activo_mueble.toFixed(2))
    let analisis_vertical_activo_nocorriente = ( evaluacion_actual_activo_nocorriente / evaluacion_actual_total_activo ) * 100;
    if(isNaN(analisis_vertical_activo_nocorriente)){ analisis_vertical_activo_nocorriente = 0; }
    $('#analisis_vertical_activo_nocorriente').val(analisis_vertical_activo_nocorriente.toFixed(2))
    let analisis_vertical_total_activo = ( evaluacion_actual_total_activo / evaluacion_actual_total_activo ) * 100;
    if(isNaN(analisis_vertical_total_activo)){ analisis_vertical_total_activo = 0; }
    $('#analisis_vertical_total_activo').val(analisis_vertical_total_activo.toFixed(2))

    let analisis_vertical_pagos_proveedor = ( evaluacion_actual_pagos_proveedor / evaluacion_actual_pasivo_patrimonio ) * 100;
    if(isNaN(analisis_vertical_pagos_proveedor)){ analisis_vertical_pagos_proveedor = 0; }
    $('#analisis_vertical_pagos_proveedor').val(analisis_vertical_pagos_proveedor.toFixed(2))
    let analisis_vertical_pasivo_corto_regulada = ( evaluacion_actual_pasivo_corto_regulada / evaluacion_actual_pasivo_patrimonio ) * 100;
    if(isNaN(analisis_vertical_pasivo_corto_regulada)){ analisis_vertical_pasivo_corto_regulada = 0; }
    $('#analisis_vertical_pasivo_corto_regulada').val(analisis_vertical_pasivo_corto_regulada.toFixed(2))
    let analisis_vertical_pasivo_corto_noregulada = ( evaluacion_actual_pasivo_corto_noregulada / evaluacion_actual_pasivo_patrimonio ) * 100;
    if(isNaN(analisis_vertical_pasivo_corto_noregulada)){ analisis_vertical_pasivo_corto_noregulada = 0; }
    $('#analisis_vertical_pasivo_corto_noregulada').val(analisis_vertical_pasivo_corto_noregulada.toFixed(2))
    let analisis_vertical_impuestos = ( evaluacion_actual_impuestos / evaluacion_actual_pasivo_patrimonio ) * 100;
    if(isNaN(analisis_vertical_impuestos)){ analisis_vertical_impuestos = 0; }
    $('#analisis_vertical_impuestos').val(analisis_vertical_impuestos.toFixed(2))
    let analisis_vertical_otras_cuentas = ( evaluacion_actual_otras_cuentas / evaluacion_actual_pasivo_patrimonio ) * 100;
    if(isNaN(analisis_vertical_otras_cuentas)){ analisis_vertical_otras_cuentas = 0; }
    $('#analisis_vertical_otras_cuentas').val(analisis_vertical_otras_cuentas.toFixed(2))
    let analisis_vertical_pasivo_corriente = ( evaluacion_actual_pasivo_corriente / evaluacion_actual_pasivo_patrimonio ) * 100;
    if(isNaN(analisis_vertical_pasivo_corriente)){ analisis_vertical_pasivo_corriente = 0; }
    $('#analisis_vertical_pasivo_corriente').val(analisis_vertical_pasivo_corriente.toFixed(2))
    let analisis_vertical_pasivo_largo_regulada = ( evaluacion_actual_pasivo_largo_regulada / evaluacion_actual_pasivo_patrimonio ) * 100;
    if(isNaN(analisis_vertical_pasivo_largo_regulada)){ analisis_vertical_pasivo_largo_regulada = 0; }
    $('#analisis_vertical_pasivo_largo_regulada').val(analisis_vertical_pasivo_largo_regulada.toFixed(2))
    let analisis_vertical_pasivo_largo_noregulada = ( evaluacion_actual_pasivo_largo_noregulada / evaluacion_actual_pasivo_patrimonio ) * 100;
    if(isNaN(analisis_vertical_pasivo_largo_noregulada)){ analisis_vertical_pasivo_largo_noregulada = 0; }
    $('#analisis_vertical_pasivo_largo_noregulada').val(analisis_vertical_pasivo_largo_noregulada.toFixed(2))
    let analisis_vertical_pasivo_nocorriente = ( evaluacion_actual_pasivo_nocorriente / evaluacion_actual_pasivo_patrimonio ) * 100;
    if(isNaN(analisis_vertical_pasivo_nocorriente)){ analisis_vertical_pasivo_nocorriente = 0; }
    $('#analisis_vertical_pasivo_nocorriente').val(analisis_vertical_pasivo_nocorriente.toFixed(2))
    let analisis_vertical_total_pasivo = ( evaluacion_actual_total_pasivo / evaluacion_actual_pasivo_patrimonio ) * 100;
    if(isNaN(analisis_vertical_total_pasivo)){ analisis_vertical_total_pasivo = 0; }
    $('#analisis_vertical_total_pasivo').val(analisis_vertical_total_pasivo.toFixed(2))
    let analisis_vertical_capital_social = ( evaluacion_actual_capital_social / evaluacion_actual_pasivo_patrimonio ) * 100;
    if(isNaN(analisis_vertical_capital_social)){ analisis_vertical_capital_social = 0; }
    $('#analisis_vertical_capital_social').val(analisis_vertical_capital_social.toFixed(2))
    let analisis_vertical_utilidad_acumulada = ( evaluacion_actual_utilidad_acumulada / evaluacion_actual_pasivo_patrimonio ) * 100;
    if(isNaN(analisis_vertical_utilidad_acumulada)){ analisis_vertical_utilidad_acumulada = 0; }
    $('#analisis_vertical_utilidad_acumulada').val(analisis_vertical_utilidad_acumulada.toFixed(2))
    let analisis_vertical_patrimonio = ( evaluacion_actual_patrimonio / evaluacion_actual_pasivo_patrimonio ) * 100;
    if(isNaN(analisis_vertical_patrimonio)){ analisis_vertical_patrimonio = 0; }
    $('#analisis_vertical_patrimonio').val(analisis_vertical_patrimonio.toFixed(2))
    let analisis_vertical_pasivo_patrimonio = ( evaluacion_actual_pasivo_patrimonio / evaluacion_actual_pasivo_patrimonio ) * 100;
    if(isNaN(analisis_vertical_pasivo_patrimonio)){ analisis_vertical_pasivo_patrimonio = 0; }
    $('#analisis_vertical_pasivo_patrimonio').val(analisis_vertical_pasivo_patrimonio.toFixed(2))
    
    //Análisis Horizontal (%)
    
    let analisis_horizontal_caja = ( evaluacion_actual_caja - evaluacion_actual_caja_anterior ) / evaluacion_actual_caja_anterior * 100;
    if(isNaN(analisis_horizontal_caja)){ analisis_horizontal_caja = 0; }
    $('#analisis_horizontal_caja').val(analisis_horizontal_caja.toFixed(2))
    let analisis_horizontal_bancos = ( evaluacion_actual_bancos - evaluacion_actual_bancos_anterior ) / evaluacion_actual_bancos_anterior * 100;
    if(isNaN(analisis_horizontal_bancos)){ analisis_horizontal_bancos = 0; }
    $('#analisis_horizontal_bancos').val(analisis_horizontal_bancos.toFixed(2))
    let analisis_horizontal_cuentas_cobrar = ( evaluacion_actual_cuentas_cobrar - evaluacion_actual_cuentas_cobrar_anterior ) / evaluacion_actual_cuentas_cobrar_anterior * 100;
    if(isNaN(analisis_horizontal_cuentas_cobrar)){ analisis_horizontal_cuentas_cobrar = 0; }
    $('#analisis_horizontal_cuentas_cobrar').val(analisis_horizontal_cuentas_cobrar.toFixed(2))
    let analisis_horizontal_adelanto_prove = ( evaluacion_actual_adelanto_prove - evaluacion_actual_adelanto_prove_anterior ) / evaluacion_actual_adelanto_prove_anterior * 100;
    if(isNaN(analisis_horizontal_adelanto_prove)){ analisis_horizontal_adelanto_prove = 0; }
    $('#analisis_horizontal_adelanto_prove').val(analisis_horizontal_adelanto_prove.toFixed(2))
    let analisis_horizontal_inventario = ( evaluacion_actual_inventario - evaluacion_actual_inventario_anterior ) / evaluacion_actual_inventario_anterior * 100;
    if(isNaN(analisis_horizontal_inventario)){ analisis_horizontal_inventario = 0; }
    $('#analisis_horizontal_inventario').val(analisis_horizontal_inventario.toFixed(2))
    let analisis_horizontal_activo_corriente = ( evaluacion_actual_activo_corriente - evaluacion_actual_activo_corriente_anterior ) / evaluacion_actual_activo_corriente_anterior * 100;
    if(isNaN(analisis_horizontal_activo_corriente)){ analisis_horizontal_activo_corriente = 0; }
    $('#analisis_horizontal_activo_corriente').val(analisis_horizontal_activo_corriente.toFixed(2))
    let analisis_horizontal_activo_inmueble = ( evaluacion_actual_activo_inmueble - evaluacion_actual_activo_inmueble_anterior ) / evaluacion_actual_activo_inmueble_anterior * 100;
    if(isNaN(analisis_horizontal_activo_inmueble)){ analisis_horizontal_activo_inmueble = 0; }
    $('#analisis_horizontal_activo_inmueble').val(analisis_horizontal_activo_inmueble.toFixed(2))
    let analisis_horizontal_activo_mueble = ( evaluacion_actual_activo_mueble - evaluacion_actual_activo_mueble_anterior ) / evaluacion_actual_activo_mueble_anterior * 100;
    if(isNaN(analisis_horizontal_activo_mueble)){ analisis_horizontal_activo_mueble = 0; }
    $('#analisis_horizontal_activo_mueble').val(analisis_horizontal_activo_mueble.toFixed(2))
    let analisis_horizontal_activo_nocorriente = ( evaluacion_actual_activo_nocorriente - evaluacion_actual_activo_nocorriente_anterior ) / evaluacion_actual_activo_nocorriente_anterior * 100;
    if(isNaN(analisis_horizontal_activo_nocorriente)){ analisis_horizontal_activo_nocorriente = 0; }
    $('#analisis_horizontal_activo_nocorriente').val(analisis_horizontal_activo_nocorriente.toFixed(2))
    let analisis_horizontal_total_activo = ( evaluacion_actual_total_activo - evaluacion_actual_total_activo_anterior ) / evaluacion_actual_total_activo_anterior * 100;
    if(isNaN(analisis_horizontal_total_activo)){ analisis_horizontal_total_activo = 0; }
    $('#analisis_horizontal_total_activo').val(analisis_horizontal_total_activo.toFixed(2))

    let analisis_horizontal_pagos_proveedor = ( evaluacion_actual_pagos_proveedor - evaluacion_actual_pagos_proveedor_anterior ) / evaluacion_actual_pagos_proveedor_anterior * 100;
    if(isNaN(analisis_horizontal_pagos_proveedor)){ analisis_horizontal_pagos_proveedor = 0; }
    $('#analisis_horizontal_pagos_proveedor').val(analisis_horizontal_pagos_proveedor.toFixed(2))
    let analisis_horizontal_pasivo_corto_regulada = ( evaluacion_actual_pasivo_corto_regulada - evaluacion_actual_pasivo_corto_regulada_anterior ) / evaluacion_actual_pasivo_corto_regulada_anterior * 100;
    if(isNaN(analisis_horizontal_pasivo_corto_regulada)){ analisis_horizontal_pasivo_corto_regulada = 0; }
    $('#analisis_horizontal_pasivo_corto_regulada').val(analisis_horizontal_pasivo_corto_regulada.toFixed(2))
    let analisis_horizontal_pasivo_corto_noregulada = ( evaluacion_actual_pasivo_corto_noregulada - evaluacion_actual_pasivo_corto_noregulada_anterior ) / evaluacion_actual_pasivo_corto_noregulada_anterior * 100;
    if(isNaN(analisis_horizontal_pasivo_corto_noregulada)){ analisis_horizontal_pasivo_corto_noregulada = 0; }
    $('#analisis_horizontal_pasivo_corto_noregulada').val(analisis_horizontal_pasivo_corto_noregulada.toFixed(2))
    let analisis_horizontal_impuestos = ( evaluacion_actual_impuestos - evaluacion_actual_impuestos_anterior ) / evaluacion_actual_impuestos_anterior * 100;
    if(isNaN(analisis_horizontal_impuestos)){ analisis_horizontal_impuestos = 0; }
    $('#analisis_horizontal_impuestos').val(analisis_horizontal_impuestos.toFixed(2))
    let analisis_horizontal_otras_cuentas = ( evaluacion_actual_otras_cuentas - evaluacion_actual_otras_cuentas_anterior ) / evaluacion_actual_otras_cuentas_anterior * 100;
    if(isNaN(analisis_horizontal_otras_cuentas)){ analisis_horizontal_otras_cuentas = 0; }
    $('#analisis_horizontal_otras_cuentas').val(analisis_horizontal_otras_cuentas.toFixed(2))
    let analisis_horizontal_pasivo_corriente = ( evaluacion_actual_pasivo_corriente - evaluacion_actual_pasivo_corriente_anterior ) / evaluacion_actual_pasivo_corriente_anterior * 100;
    if(isNaN(analisis_horizontal_pasivo_corriente)){ analisis_horizontal_pasivo_corriente = 0; }
    $('#analisis_horizontal_pasivo_corriente').val(analisis_horizontal_pasivo_corriente.toFixed(2))
    let analisis_horizontal_pasivo_largo_regulada = ( evaluacion_actual_pasivo_largo_regulada - evaluacion_actual_pasivo_largo_regulada_anterior ) / evaluacion_actual_pasivo_largo_regulada_anterior * 100;
    if(isNaN(analisis_horizontal_pasivo_largo_regulada)){ analisis_horizontal_pasivo_largo_regulada = 0; }
    $('#analisis_horizontal_pasivo_largo_regulada').val(analisis_horizontal_pasivo_largo_regulada.toFixed(2))
    let analisis_horizontal_pasivo_largo_noregulada = ( evaluacion_actual_pasivo_largo_noregulada - evaluacion_actual_pasivo_largo_noregulada_anterior ) / evaluacion_actual_pasivo_largo_noregulada_anterior * 100;
    if(isNaN(analisis_horizontal_pasivo_largo_noregulada)){ analisis_horizontal_pasivo_largo_noregulada = 0; }
    $('#analisis_horizontal_pasivo_largo_noregulada').val(analisis_horizontal_pasivo_largo_noregulada.toFixed(2))
    let analisis_horizontal_pasivo_nocorriente = ( evaluacion_actual_pasivo_nocorriente - evaluacion_actual_pasivo_nocorriente_anterior ) / evaluacion_actual_pasivo_nocorriente_anterior * 100;
    if(isNaN(analisis_horizontal_pasivo_nocorriente)){ analisis_horizontal_pasivo_nocorriente = 0; }
    $('#analisis_horizontal_pasivo_nocorriente').val(analisis_horizontal_pasivo_nocorriente.toFixed(2))
    let analisis_horizontal_total_pasivo = ( evaluacion_actual_total_pasivo - evaluacion_actual_total_pasivo_anterior ) / evaluacion_actual_total_pasivo_anterior * 100;
    if(isNaN(analisis_horizontal_total_pasivo)){ analisis_horizontal_total_pasivo = 0; }
    $('#analisis_horizontal_total_pasivo').val(analisis_horizontal_total_pasivo.toFixed(2))
    let analisis_horizontal_capital_social = ( evaluacion_actual_capital_social - evaluacion_actual_capital_social_anterior ) / evaluacion_actual_capital_social_anterior * 100;
    if(isNaN(analisis_horizontal_capital_social)){ analisis_horizontal_capital_social = 0; }
    $('#analisis_horizontal_capital_social').val(analisis_horizontal_capital_social.toFixed(2))
    let analisis_horizontal_utilidad_acumulada = ( evaluacion_actual_utilidad_acumulada - evaluacion_actual_utilidad_acumulada_anterior ) / evaluacion_actual_utilidad_acumulada_anterior * 100;
    if(isNaN(analisis_horizontal_utilidad_acumulada)){ analisis_horizontal_utilidad_acumulada = 0; }
    $('#analisis_horizontal_utilidad_acumulada').val(analisis_horizontal_utilidad_acumulada.toFixed(2))
    let analisis_horizontal_patrimonio = ( evaluacion_actual_patrimonio - evaluacion_actual_patrimonio_anterior ) / evaluacion_actual_patrimonio_anterior * 100;
    if(isNaN(analisis_horizontal_patrimonio)){ analisis_horizontal_patrimonio = 0; }
    $('#analisis_horizontal_patrimonio').val(analisis_horizontal_patrimonio.toFixed(2))
    let analisis_horizontal_pasivo_patrimonio = ( evaluacion_actual_pasivo_patrimonio - evaluacion_actual_pasivo_patrimonio_anterior ) / evaluacion_actual_pasivo_patrimonio_anterior * 100;
    if(isNaN(analisis_horizontal_pasivo_patrimonio)){ analisis_horizontal_pasivo_patrimonio = 0; }
    $('#analisis_horizontal_pasivo_patrimonio').val(analisis_horizontal_pasivo_patrimonio.toFixed(2))
    
    cal_ratios_finacieros()
  }
  $("#table-estado-ganancias-perdidas tbody tr td input").on("keyup", function() {
      total_ganancias_perdidas();
  });
  total_ganancias_perdidas();
  function total_ganancias_perdidas(){
    
    let evaluacion_actual_ganancia_ventamensual_anterior = parseFloat($('#evaluacion_actual_ganancia_ventamensual_anterior').val());
    let evaluacion_actual_ganancia_costo_venta_anterior = parseFloat($('#evaluacion_actual_ganancia_costo_venta_anterior').val());
    let evaluacion_actual_ganancia_utilidad_bruta_anterior = parseFloat($('#evaluacion_actual_ganancia_utilidad_bruta_anterior').val());
    let evaluacion_actual_ganancia_servicio_luz_anterior = parseFloat($('#evaluacion_actual_ganancia_servicio_luz_anterior').val());
    let evaluacion_actual_ganancia_servicio_agua_anterior = parseFloat($('#evaluacion_actual_ganancia_servicio_agua_anterior').val());
    let evaluacion_actual_ganancia_servicio_internet_anterior = parseFloat($('#evaluacion_actual_ganancia_servicio_internet_anterior').val());
    let evaluacion_actual_ganancia_servicio_celular_anterior = parseFloat($('#evaluacion_actual_ganancia_servicio_celular_anterior').val());
    let evaluacion_actual_ganancia_servicio_cable_anterior = parseFloat($('#evaluacion_actual_ganancia_servicio_cable_anterior').val());
    let evaluacion_actual_ganancia_servicios_anterior = parseFloat($('#evaluacion_actual_ganancia_servicios_anterior').val());
    let evaluacion_actual_ganancia_gasto_admin_anterior = parseFloat($('#evaluacion_actual_ganancia_gasto_admin_anterior').val());
    let evaluacion_actual_ganancia_gasto_personal_anterior = parseFloat($('#evaluacion_actual_ganancia_gasto_personal_anterior').val());
    let evaluacion_actual_ganancia_alquiler_anterior = parseFloat($('#evaluacion_actual_ganancia_alquiler_anterior').val());
    let evaluacion_actual_ganancia_autovaluo_anterior = parseFloat($('#evaluacion_actual_ganancia_autovaluo_anterior').val());
    let evaluacion_actual_ganancia_transporte_anterior = parseFloat($('#evaluacion_actual_ganancia_transporte_anterior').val());
    let evaluacion_actual_ganancia_cuota_regulada_anterior = parseFloat($('#evaluacion_actual_ganancia_cuota_regulada_anterior').val());
    let evaluacion_actual_ganancia_cuota_noregulada_anterior = parseFloat($('#evaluacion_actual_ganancia_cuota_noregulada_anterior').val());
    let evaluacion_actual_ganancia_sunat_anterior = parseFloat($('#evaluacion_actual_ganancia_sunat_anterior').val());
    let evaluacion_actual_ganancia_otros_gastos_anterior = parseFloat($('#evaluacion_actual_ganancia_otros_gastos_anterior').val());
    let evaluacion_actual_ganancia_gastos_op_anterior = parseFloat($('#evaluacion_actual_ganancia_gastos_op_anterior').val());
    let evaluacion_actual_ganancia_utilidad_neta_anterior = parseFloat($('#evaluacion_actual_ganancia_utilidad_neta_anterior').val());
    let evaluacion_actual_ganancia_negocio_adicional_anterior = parseFloat($('#evaluacion_actual_ganancia_negocio_adicional_anterior').val());
    let evaluacion_actual_ganancia_ingreso_fijo_anterior = parseFloat($('#evaluacion_actual_ganancia_ingreso_fijo_anterior').val());
    let evaluacion_actual_ganancia_gasto_familiar_anterior = parseFloat($('#evaluacion_actual_ganancia_gasto_familiar_anterior').val());
    let evaluacion_actual_ganancia_cuota_vivienda_anterior = parseFloat($('#evaluacion_actual_ganancia_cuota_vivienda_anterior').val());
    let evaluacion_actual_ganancia_excedente_mensual_anterior = parseFloat($('#evaluacion_actual_ganancia_excedente_mensual_anterior').val());
    
    
    let venta_mensual = parseFloat("{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->venta_mensual : 0 }}");
    let venta_total_mensual = parseFloat("{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->venta_total_mensual : 0 }}");

    let evaluacion_actual_ganancia_ventamensual = venta_mensual + venta_total_mensual;
    $('#evaluacion_actual_ganancia_ventamensual').val(evaluacion_actual_ganancia_ventamensual.toFixed(2));

    let evaluacion_actual_ganancia_utilidad_bruta = parseFloat($('#evaluacion_actual_ganancia_utilidad_bruta').val());
    let evaluacion_actual_ganancia_costo_venta = evaluacion_actual_ganancia_ventamensual - evaluacion_actual_ganancia_utilidad_bruta;
    $('#evaluacion_actual_ganancia_costo_venta').val(evaluacion_actual_ganancia_costo_venta.toFixed(2));

    
    let margen_venta_calculado = (evaluacion_actual_ganancia_utilidad_bruta/evaluacion_actual_ganancia_ventamensual)*100;
    if(isNaN(margen_venta_calculado)){
      margen_venta_calculado = 0;
    }
    $('#margen_venta_calculado').val(margen_venta_calculado.toFixed(2));
    
    let evaluacion_actual_ganancia_servicio_luz = parseFloat($('#evaluacion_actual_ganancia_servicio_luz').val());
    let evaluacion_actual_ganancia_servicio_agua = parseFloat($('#evaluacion_actual_ganancia_servicio_agua').val());
    let evaluacion_actual_ganancia_servicio_internet = parseFloat($('#evaluacion_actual_ganancia_servicio_internet').val());
    let evaluacion_actual_ganancia_servicio_celular = parseFloat($('#evaluacion_actual_ganancia_servicio_celular').val());
    let evaluacion_actual_ganancia_servicio_cable = parseFloat($('#evaluacion_actual_ganancia_servicio_cable').val());

    let evaluacion_actual_ganancia_servicios = evaluacion_actual_ganancia_servicio_luz + evaluacion_actual_ganancia_servicio_agua + evaluacion_actual_ganancia_servicio_internet + evaluacion_actual_ganancia_servicio_celular + evaluacion_actual_ganancia_servicio_cable;
    $('#evaluacion_actual_ganancia_servicios').val(evaluacion_actual_ganancia_servicios.toFixed(2));

    let evaluacion_actual_ganancia_gasto_admin = parseFloat($('#evaluacion_actual_ganancia_gasto_admin').val());
    let evaluacion_actual_ganancia_gasto_personal = parseFloat($('#evaluacion_actual_ganancia_gasto_personal').val());
    let evaluacion_actual_ganancia_alquiler = parseFloat($('#evaluacion_actual_ganancia_alquiler').val());
    let evaluacion_actual_ganancia_autovaluo = parseFloat($('#evaluacion_actual_ganancia_autovaluo').val());
    let evaluacion_actual_ganancia_transporte = parseFloat($('#evaluacion_actual_ganancia_transporte').val());
    let evaluacion_actual_ganancia_cuota_regulada = parseFloat($('#evaluacion_actual_ganancia_cuota_regulada').val());
    let evaluacion_actual_ganancia_cuota_noregulada = parseFloat($('#evaluacion_actual_ganancia_cuota_noregulada').val());
    let evaluacion_actual_ganancia_sunat = parseFloat($('#evaluacion_actual_ganancia_sunat').val());
    let evaluacion_actual_ganancia_otros_gastos = parseFloat($('#evaluacion_actual_ganancia_otros_gastos').val());


    let evaluacion_actual_ganancia_gastos_op =  evaluacion_actual_ganancia_gasto_admin +
                                                evaluacion_actual_ganancia_gasto_personal +
                                                evaluacion_actual_ganancia_servicios +
                                                evaluacion_actual_ganancia_alquiler +
                                                evaluacion_actual_ganancia_autovaluo +
                                                evaluacion_actual_ganancia_transporte +
                                                evaluacion_actual_ganancia_cuota_regulada +
                                                evaluacion_actual_ganancia_cuota_noregulada +
                                                evaluacion_actual_ganancia_sunat +
                                                evaluacion_actual_ganancia_otros_gastos;
    $('#evaluacion_actual_ganancia_gastos_op').val(evaluacion_actual_ganancia_gastos_op.toFixed(2));


    let evaluacion_actual_ganancia_utilidad_neta = evaluacion_actual_ganancia_utilidad_bruta - evaluacion_actual_ganancia_gastos_op;
    $('#evaluacion_actual_ganancia_utilidad_neta').val(evaluacion_actual_ganancia_utilidad_neta.toFixed(2));



    let evaluacion_actual_ganancia_negocio_adicional = parseFloat($('#evaluacion_actual_ganancia_negocio_adicional').val());
    let evaluacion_actual_ganancia_ingreso_fijo = parseFloat($('#evaluacion_actual_ganancia_ingreso_fijo').val());
    let evaluacion_actual_ganancia_gasto_familiar = parseFloat($('#evaluacion_actual_ganancia_gasto_familiar').val());
    let evaluacion_actual_ganancia_cuota_vivienda = parseFloat($('#evaluacion_actual_ganancia_cuota_vivienda').val());

    let evaluacion_actual_ganancia_excedente_mensual =  evaluacion_actual_ganancia_utilidad_neta +
                                                        evaluacion_actual_ganancia_negocio_adicional +
                                                        evaluacion_actual_ganancia_ingreso_fijo -
                                                        evaluacion_actual_ganancia_gasto_familiar -
                                                        evaluacion_actual_ganancia_cuota_vivienda;
    $('#evaluacion_actual_ganancia_excedente_mensual').val(evaluacion_actual_ganancia_excedente_mensual.toFixed(2));

    //Análisis Vertical(%)	 
    
    let analisis_vertical_ventamensual = ( evaluacion_actual_ganancia_ventamensual / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_ventamensual) ||  analisis_vertical_ventamensual == Infinity ||  analisis_vertical_ventamensual == -Infinity){ analisis_vertical_ventamensual = 0; }
    $('#analisis_vertical_ventamensual').val(analisis_vertical_ventamensual.toFixed(2));
    let analisis_vertical_costo_venta = ( evaluacion_actual_ganancia_costo_venta / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_costo_venta) ||  analisis_vertical_costo_venta == Infinity ||  analisis_vertical_costo_venta == -Infinity){ analisis_vertical_costo_venta = 0; }
    $('#analisis_vertical_costo_venta').val(analisis_vertical_costo_venta.toFixed(2));
    let analisis_vertical_utilidad_bruta = ( evaluacion_actual_ganancia_utilidad_bruta / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_utilidad_bruta) ||  analisis_vertical_utilidad_bruta == Infinity ||  analisis_vertical_utilidad_bruta == -Infinity){ analisis_vertical_utilidad_bruta = 0; }
    $('#analisis_vertical_utilidad_bruta').val(analisis_vertical_utilidad_bruta.toFixed(2));
    let analisis_vertical_gasto_admin = ( evaluacion_actual_ganancia_gasto_admin / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_gasto_admin) ||  analisis_vertical_gasto_admin == Infinity ||  analisis_vertical_gasto_admin == -Infinity){ analisis_vertical_gasto_admin = 0; }
    $('#analisis_vertical_gasto_admin').val(analisis_vertical_gasto_admin.toFixed(2));
    let analisis_vertical_gasto_personal = ( evaluacion_actual_ganancia_gasto_personal / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_gasto_personal) ||  analisis_vertical_gasto_personal == Infinity ||  analisis_vertical_gasto_personal == -Infinity){ analisis_vertical_gasto_personal = 0; }
    $('#analisis_vertical_gasto_personal').val(analisis_vertical_gasto_personal.toFixed(2));
    let analisis_vertical_servicios = ( evaluacion_actual_ganancia_servicios / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_servicios) ||  analisis_vertical_servicios == Infinity ||  analisis_vertical_servicios == -Infinity){ analisis_vertical_servicios = 0; }
    $('#analisis_vertical_servicios').val(analisis_vertical_servicios.toFixed(2));
    let analisis_vertical_servicio_luz = ( evaluacion_actual_ganancia_servicio_luz / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_servicio_luz) ||  analisis_vertical_servicio_luz == Infinity ||  analisis_vertical_servicio_luz == -Infinity){ analisis_vertical_servicio_luz = 0; }
    $('#analisis_vertical_servicio_luz').val(analisis_vertical_servicio_luz.toFixed(2));
    let analisis_vertical_servicio_agua = ( evaluacion_actual_ganancia_servicio_agua / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_servicio_agua) ||  analisis_vertical_servicio_agua == Infinity ||  analisis_vertical_servicio_agua == -Infinity){ analisis_vertical_servicio_agua = 0; }
    $('#analisis_vertical_servicio_agua').val(analisis_vertical_servicio_agua.toFixed(2));
    let analisis_vertical_servicio_internet = ( evaluacion_actual_ganancia_servicio_internet / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_servicio_internet) ||  analisis_vertical_servicio_internet == Infinity ||  analisis_vertical_servicio_internet == -Infinity){ analisis_vertical_servicio_internet = 0; }
    $('#analisis_vertical_servicio_internet').val(analisis_vertical_servicio_internet.toFixed(2));
    let analisis_vertical_servicio_celular = ( evaluacion_actual_ganancia_servicio_celular / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_servicio_celular) ||  analisis_vertical_servicio_celular == Infinity ||  analisis_vertical_servicio_celular == -Infinity){ analisis_vertical_servicio_celular = 0; }
    $('#analisis_vertical_servicio_celular').val(analisis_vertical_servicio_celular.toFixed(2));
    let analisis_vertical_servicio_cable = ( evaluacion_actual_ganancia_servicio_cable / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_servicio_cable) ||  analisis_vertical_servicio_cable == Infinity ||  analisis_vertical_servicio_cable == -Infinity){ analisis_vertical_servicio_cable = 0; }
    $('#analisis_vertical_servicio_cable').val(analisis_vertical_servicio_cable.toFixed(2));
    let analisis_vertical_alquiler = ( evaluacion_actual_ganancia_alquiler / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_alquiler) ||  analisis_vertical_alquiler == Infinity ||  analisis_vertical_alquiler == -Infinity){ analisis_vertical_alquiler = 0; }
    $('#analisis_vertical_alquiler').val(analisis_vertical_alquiler.toFixed(2));
    let analisis_vertical_autovaluo = ( evaluacion_actual_ganancia_autovaluo / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_autovaluo) ||  analisis_vertical_autovaluo == Infinity ||  analisis_vertical_autovaluo == -Infinity){ analisis_vertical_autovaluo = 0; }
    $('#analisis_vertical_autovaluo').val(analisis_vertical_autovaluo.toFixed(2));
    let analisis_vertical_transporte = ( evaluacion_actual_ganancia_transporte / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_transporte) ||  analisis_vertical_transporte == Infinity ||  analisis_vertical_transporte == -Infinity){ analisis_vertical_transporte = 0; }
    $('#analisis_vertical_transporte').val(analisis_vertical_transporte.toFixed(2));
    let analisis_vertical_cuota_regulada = ( evaluacion_actual_ganancia_cuota_regulada / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_cuota_regulada) ||  analisis_vertical_cuota_regulada == Infinity ||  analisis_vertical_cuota_regulada == -Infinity){ analisis_vertical_cuota_regulada = 0; }
    $('#analisis_vertical_cuota_regulada').val(analisis_vertical_cuota_regulada.toFixed(2));
    let analisis_vertical_cuota_noregulada = ( evaluacion_actual_ganancia_cuota_noregulada / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_cuota_noregulada) ||  analisis_vertical_cuota_noregulada == Infinity ||  analisis_vertical_cuota_noregulada == -Infinity){ analisis_vertical_cuota_noregulada = 0; }
    $('#analisis_vertical_cuota_noregulada').val(analisis_vertical_cuota_noregulada.toFixed(2));
    let analisis_vertical_sunat = ( evaluacion_actual_ganancia_sunat / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_sunat) ||  analisis_vertical_sunat == Infinity ||  analisis_vertical_sunat == -Infinity){ analisis_vertical_sunat = 0; }
    $('#analisis_vertical_sunat').val(analisis_vertical_sunat.toFixed(2));
    let analisis_vertical_otros_gastos = ( evaluacion_actual_ganancia_otros_gastos / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_otros_gastos) ||  analisis_vertical_otros_gastos == Infinity ||  analisis_vertical_otros_gastos == -Infinity){ analisis_vertical_otros_gastos = 0; }
    $('#analisis_vertical_otros_gastos').val(analisis_vertical_otros_gastos.toFixed(2));
    let analisis_vertical_gastos_op = ( evaluacion_actual_ganancia_gastos_op / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_gastos_op) ||  analisis_vertical_gastos_op == Infinity ||  analisis_vertical_gastos_op == -Infinity){ analisis_vertical_gastos_op = 0; }
    $('#analisis_vertical_gastos_op').val(analisis_vertical_gastos_op.toFixed(2));
    let analisis_vertical_utilidad_neta = ( evaluacion_actual_ganancia_utilidad_neta / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_utilidad_neta) ||  analisis_vertical_utilidad_neta == Infinity ||  analisis_vertical_utilidad_neta == -Infinity){ analisis_vertical_utilidad_neta = 0; }
    $('#analisis_vertical_utilidad_neta').val(analisis_vertical_utilidad_neta.toFixed(2));
    let analisis_vertical_negocio_adicional = ( evaluacion_actual_ganancia_negocio_adicional / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_negocio_adicional) ||  analisis_vertical_negocio_adicional == Infinity ||  analisis_vertical_negocio_adicional == -Infinity){ analisis_vertical_negocio_adicional = 0; }
    $('#analisis_vertical_negocio_adicional').val(analisis_vertical_negocio_adicional.toFixed(2));
    let analisis_vertical_ingreso_fijo = ( evaluacion_actual_ganancia_ingreso_fijo / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_ingreso_fijo) ||  analisis_vertical_ingreso_fijo == Infinity ||  analisis_vertical_ingreso_fijo == -Infinity){ analisis_vertical_ingreso_fijo = 0; }
    $('#analisis_vertical_ingreso_fijo').val(analisis_vertical_ingreso_fijo.toFixed(2));
    let analisis_vertical_gasto_familiar = ( evaluacion_actual_ganancia_gasto_familiar / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_gasto_familiar) ||  analisis_vertical_gasto_familiar == Infinity ||  analisis_vertical_gasto_familiar == -Infinity){ analisis_vertical_gasto_familiar = 0; }
    $('#analisis_vertical_gasto_familiar').val(analisis_vertical_gasto_familiar.toFixed(2));
    let analisis_vertical_cuota_vivienda = ( evaluacion_actual_ganancia_cuota_vivienda / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_cuota_vivienda) ||  analisis_vertical_cuota_vivienda == Infinity ||  analisis_vertical_cuota_vivienda == -Infinity){ analisis_vertical_cuota_vivienda = 0; }
    $('#analisis_vertical_cuota_vivienda').val(analisis_vertical_cuota_vivienda.toFixed(2));
    let analisis_vertical_excedente_mensual = ( evaluacion_actual_ganancia_excedente_mensual / evaluacion_actual_ganancia_ventamensual ) * 100;
    if(isNaN(analisis_vertical_excedente_mensual) ||  analisis_vertical_excedente_mensual == Infinity ||  analisis_vertical_excedente_mensual == -Infinity){ analisis_vertical_excedente_mensual = 0; }
    $('#analisis_vertical_excedente_mensual').val(analisis_vertical_excedente_mensual.toFixed(2));

    // Análisis Horizontal (%)

    let analisis_horizontal_ventamensual = ( evaluacion_actual_ganancia_ventamensual - evaluacion_actual_ganancia_ventamensual_anterior ) / evaluacion_actual_ganancia_ventamensual_anterior * 100;;
    if(isNaN(analisis_horizontal_ventamensual) ||  analisis_horizontal_ventamensual == Infinity ||  analisis_horizontal_ventamensual == -Infinity){ analisis_horizontal_ventamensual = 0; }
    $('#analisis_horizontal_ventamensual').val(analisis_horizontal_ventamensual.toFixed(2));
    let analisis_horizontal_costo_venta = ( evaluacion_actual_ganancia_costo_venta - evaluacion_actual_ganancia_costo_venta_anterior ) / evaluacion_actual_ganancia_costo_venta_anterior * 100;;
    if(isNaN(analisis_horizontal_costo_venta) ||  analisis_horizontal_costo_venta == Infinity ||  analisis_horizontal_costo_venta == -Infinity){ analisis_horizontal_costo_venta = 0; }
    $('#analisis_horizontal_costo_venta').val(analisis_horizontal_costo_venta.toFixed(2));
    let analisis_horizontal_utilidad_bruta = ( evaluacion_actual_ganancia_utilidad_bruta - evaluacion_actual_ganancia_utilidad_bruta_anterior ) / evaluacion_actual_ganancia_utilidad_bruta_anterior * 100;;
    if(isNaN(analisis_horizontal_utilidad_bruta) ||  analisis_horizontal_utilidad_bruta == Infinity ||  analisis_horizontal_utilidad_bruta == -Infinity){ analisis_horizontal_utilidad_bruta = 0; }
    $('#analisis_horizontal_utilidad_bruta').val(analisis_horizontal_utilidad_bruta.toFixed(2));
    let analisis_horizontal_gasto_admin = ( evaluacion_actual_ganancia_gasto_admin - evaluacion_actual_ganancia_gasto_admin_anterior ) / evaluacion_actual_ganancia_gasto_admin_anterior * 100;;
    if(isNaN(analisis_horizontal_gasto_admin) ||  analisis_horizontal_gasto_admin == Infinity ||  analisis_horizontal_gasto_admin == -Infinity){ analisis_horizontal_gasto_admin = 0; }
    $('#analisis_horizontal_gasto_admin').val(analisis_horizontal_gasto_admin.toFixed(2));
    let analisis_horizontal_gasto_personal = ( evaluacion_actual_ganancia_gasto_personal - evaluacion_actual_ganancia_gasto_personal_anterior ) / evaluacion_actual_ganancia_gasto_personal_anterior * 100;;
    if(isNaN(analisis_horizontal_gasto_personal) ||  analisis_horizontal_gasto_personal == Infinity ||  analisis_horizontal_gasto_personal == -Infinity){ analisis_horizontal_gasto_personal = 0; }
    $('#analisis_horizontal_gasto_personal').val(analisis_horizontal_gasto_personal.toFixed(2));
    let analisis_horizontal_servicios = ( evaluacion_actual_ganancia_servicios - evaluacion_actual_ganancia_servicios_anterior ) / evaluacion_actual_ganancia_servicios_anterior * 100;;
    if(isNaN(analisis_horizontal_servicios) ||  analisis_horizontal_servicios == Infinity ||  analisis_horizontal_servicios == -Infinity){ analisis_horizontal_servicios = 0; }
    $('#analisis_horizontal_servicios').val(analisis_horizontal_servicios.toFixed(2));
    let analisis_horizontal_servicio_luz = ( evaluacion_actual_ganancia_servicio_luz - evaluacion_actual_ganancia_servicio_luz_anterior ) / evaluacion_actual_ganancia_servicio_luz_anterior * 100;;
    if(isNaN(analisis_horizontal_servicio_luz) ||  analisis_horizontal_servicio_luz == Infinity ||  analisis_horizontal_servicio_luz == -Infinity){ analisis_horizontal_servicio_luz = 0; }
    $('#analisis_horizontal_servicio_luz').val(analisis_horizontal_servicio_luz.toFixed(2));
    let analisis_horizontal_servicio_agua = ( evaluacion_actual_ganancia_servicio_agua - evaluacion_actual_ganancia_servicio_agua_anterior ) / evaluacion_actual_ganancia_servicio_agua_anterior * 100;;
    if(isNaN(analisis_horizontal_servicio_agua) ||  analisis_horizontal_servicio_agua == Infinity ||  analisis_horizontal_servicio_agua == -Infinity){ analisis_horizontal_servicio_agua = 0; }
    $('#analisis_horizontal_servicio_agua').val(analisis_horizontal_servicio_agua.toFixed(2));
    let analisis_horizontal_servicio_internet = ( evaluacion_actual_ganancia_servicio_internet - evaluacion_actual_ganancia_servicio_internet_anterior ) / evaluacion_actual_ganancia_servicio_internet_anterior * 100;;
    if(isNaN(analisis_horizontal_servicio_internet) ||  analisis_horizontal_servicio_internet == Infinity ||  analisis_horizontal_servicio_internet == -Infinity){ analisis_horizontal_servicio_internet = 0; }
    $('#analisis_horizontal_servicio_internet').val(analisis_horizontal_servicio_internet.toFixed(2));
    let analisis_horizontal_servicio_celular = ( evaluacion_actual_ganancia_servicio_celular - evaluacion_actual_ganancia_servicio_celular_anterior ) / evaluacion_actual_ganancia_servicio_celular_anterior * 100;;
    if(isNaN(analisis_horizontal_servicio_celular) ||  analisis_horizontal_servicio_celular == Infinity ||  analisis_horizontal_servicio_celular == -Infinity){ analisis_horizontal_servicio_celular = 0; }
    $('#analisis_horizontal_servicio_celular').val(analisis_horizontal_servicio_celular.toFixed(2));
    let analisis_horizontal_servicio_cable = ( evaluacion_actual_ganancia_servicio_cable - evaluacion_actual_ganancia_servicio_cable_anterior ) / evaluacion_actual_ganancia_servicio_cable_anterior * 100;;
    if(isNaN(analisis_horizontal_servicio_cable) ||  analisis_horizontal_servicio_cable == Infinity ||  analisis_horizontal_servicio_cable == -Infinity){ analisis_horizontal_servicio_cable = 0; }
    $('#analisis_horizontal_servicio_cable').val(analisis_horizontal_servicio_cable.toFixed(2));
    let analisis_horizontal_alquiler = ( evaluacion_actual_ganancia_alquiler - evaluacion_actual_ganancia_alquiler_anterior ) / evaluacion_actual_ganancia_alquiler_anterior * 100;;
    if(isNaN(analisis_horizontal_alquiler) ||  analisis_horizontal_alquiler == Infinity ||  analisis_horizontal_alquiler == -Infinity){ analisis_horizontal_alquiler = 0; }
    $('#analisis_horizontal_alquiler').val(analisis_horizontal_alquiler.toFixed(2));
    let analisis_horizontal_autovaluo = ( evaluacion_actual_ganancia_autovaluo - evaluacion_actual_ganancia_autovaluo_anterior ) / evaluacion_actual_ganancia_autovaluo_anterior * 100;;
    if(isNaN(analisis_horizontal_autovaluo) ||  analisis_horizontal_autovaluo == Infinity ||  analisis_horizontal_autovaluo == -Infinity){ analisis_horizontal_autovaluo = 0; }
    $('#analisis_horizontal_autovaluo').val(analisis_horizontal_autovaluo.toFixed(2));
    let analisis_horizontal_transporte = ( evaluacion_actual_ganancia_transporte - evaluacion_actual_ganancia_transporte_anterior ) / evaluacion_actual_ganancia_transporte_anterior * 100;;
    if(isNaN(analisis_horizontal_transporte) ||  analisis_horizontal_transporte == Infinity ||  analisis_horizontal_transporte == -Infinity){ analisis_horizontal_transporte = 0; }
    $('#analisis_horizontal_transporte').val(analisis_horizontal_transporte.toFixed(2));
    let analisis_horizontal_cuota_regulada = ( evaluacion_actual_ganancia_cuota_regulada - evaluacion_actual_ganancia_cuota_regulada_anterior ) / evaluacion_actual_ganancia_cuota_regulada_anterior * 100;;
    if(isNaN(analisis_horizontal_cuota_regulada) ||  analisis_horizontal_cuota_regulada == Infinity ||  analisis_horizontal_cuota_regulada == -Infinity){ analisis_horizontal_cuota_regulada = 0; }
    $('#analisis_horizontal_cuota_regulada').val(analisis_horizontal_cuota_regulada.toFixed(2));
    let analisis_horizontal_cuota_noregulada = ( evaluacion_actual_ganancia_cuota_noregulada - evaluacion_actual_ganancia_cuota_noregulada_anterior ) / evaluacion_actual_ganancia_cuota_noregulada_anterior * 100;;
    if(isNaN(analisis_horizontal_cuota_noregulada) ||  analisis_horizontal_cuota_noregulada == Infinity ||  analisis_horizontal_cuota_noregulada == -Infinity){ analisis_horizontal_cuota_noregulada = 0; }
    $('#analisis_horizontal_cuota_noregulada').val(analisis_horizontal_cuota_noregulada.toFixed(2));
    let analisis_horizontal_sunat = ( evaluacion_actual_ganancia_sunat - evaluacion_actual_ganancia_sunat_anterior ) / evaluacion_actual_ganancia_sunat_anterior * 100;;
    if(isNaN(analisis_horizontal_sunat) ||  analisis_horizontal_sunat == Infinity ||  analisis_horizontal_sunat == -Infinity){ analisis_horizontal_sunat = 0; }
    $('#analisis_horizontal_sunat').val(analisis_horizontal_sunat.toFixed(2));
    let analisis_horizontal_otros_gastos = ( evaluacion_actual_ganancia_otros_gastos - evaluacion_actual_ganancia_otros_gastos_anterior ) / evaluacion_actual_ganancia_otros_gastos_anterior * 100;;
    if(isNaN(analisis_horizontal_otros_gastos) ||  analisis_horizontal_otros_gastos == Infinity ||  analisis_horizontal_otros_gastos == -Infinity){ analisis_horizontal_otros_gastos = 0; }
    $('#analisis_horizontal_otros_gastos').val(analisis_horizontal_otros_gastos.toFixed(2));
    let analisis_horizontal_gastos_op = ( evaluacion_actual_ganancia_gastos_op - evaluacion_actual_ganancia_gastos_op_anterior ) / evaluacion_actual_ganancia_gastos_op_anterior * 100;;
    if(isNaN(analisis_horizontal_gastos_op) ||  analisis_horizontal_gastos_op == Infinity ||  analisis_horizontal_gastos_op == -Infinity){ analisis_horizontal_gastos_op = 0; }
    $('#analisis_horizontal_gastos_op').val(analisis_horizontal_gastos_op.toFixed(2));
    let analisis_horizontal_utilidad_neta = ( evaluacion_actual_ganancia_utilidad_neta - evaluacion_actual_ganancia_utilidad_neta_anterior ) / evaluacion_actual_ganancia_utilidad_neta_anterior * 100;;
    if(isNaN(analisis_horizontal_utilidad_neta) ||  analisis_horizontal_utilidad_neta == Infinity ||  analisis_horizontal_utilidad_neta == -Infinity){ analisis_horizontal_utilidad_neta = 0; }
    $('#analisis_horizontal_utilidad_neta').val(analisis_horizontal_utilidad_neta.toFixed(2));
    let analisis_horizontal_negocio_adicional = ( evaluacion_actual_ganancia_negocio_adicional - evaluacion_actual_ganancia_negocio_adicional_anterior ) / evaluacion_actual_ganancia_negocio_adicional_anterior * 100;;
    if(isNaN(analisis_horizontal_negocio_adicional) ||  analisis_horizontal_negocio_adicional == Infinity ||  analisis_horizontal_negocio_adicional == -Infinity){ analisis_horizontal_negocio_adicional = 0; }
    $('#analisis_horizontal_negocio_adicional').val(analisis_horizontal_negocio_adicional.toFixed(2));
    let analisis_horizontal_ingreso_fijo = ( evaluacion_actual_ganancia_ingreso_fijo - evaluacion_actual_ganancia_ingreso_fijo_anterior ) / evaluacion_actual_ganancia_ingreso_fijo_anterior * 100;;
    if(isNaN(analisis_horizontal_ingreso_fijo) ||  analisis_horizontal_ingreso_fijo == Infinity ||  analisis_horizontal_ingreso_fijo == -Infinity){ analisis_horizontal_ingreso_fijo = 0; }
    $('#analisis_horizontal_ingreso_fijo').val(analisis_horizontal_ingreso_fijo.toFixed(2));
    let analisis_horizontal_gasto_familiar = ( evaluacion_actual_ganancia_gasto_familiar - evaluacion_actual_ganancia_gasto_familiar_anterior ) / evaluacion_actual_ganancia_gasto_familiar_anterior * 100;;
    if(isNaN(analisis_horizontal_gasto_familiar) ||  analisis_horizontal_gasto_familiar == Infinity ||  analisis_horizontal_gasto_familiar == -Infinity){ analisis_horizontal_gasto_familiar = 0; }
    $('#analisis_horizontal_gasto_familiar').val(analisis_horizontal_gasto_familiar.toFixed(2));
    let analisis_horizontal_cuota_vivienda = ( evaluacion_actual_ganancia_cuota_vivienda - evaluacion_actual_ganancia_cuota_vivienda_anterior ) / evaluacion_actual_ganancia_cuota_vivienda_anterior * 100;;
    if(isNaN(analisis_horizontal_cuota_vivienda) ||  analisis_horizontal_cuota_vivienda == Infinity ||  analisis_horizontal_cuota_vivienda == -Infinity){ analisis_horizontal_cuota_vivienda = 0; }
    $('#analisis_horizontal_cuota_vivienda').val(analisis_horizontal_cuota_vivienda.toFixed(2));
    let analisis_horizontal_excedente_mensual = ( evaluacion_actual_ganancia_excedente_mensual - evaluacion_actual_ganancia_excedente_mensual_anterior ) / evaluacion_actual_ganancia_excedente_mensual_anterior * 100;;
    if(isNaN(analisis_horizontal_excedente_mensual) ||  analisis_horizontal_excedente_mensual == Infinity ||  analisis_horizontal_excedente_mensual == -Infinity){ analisis_horizontal_excedente_mensual = 0; }
    $('#analisis_horizontal_excedente_mensual').val(analisis_horizontal_excedente_mensual.toFixed(2));

    cal_ratios_finacieros()
    total_balance_general()
    calcular_excedente()
  }
  function json_balance(){
    let jsonData = [];
    $("#table-balance-general input").each(function () {
        let input = $(this);
        let id = input.attr("id");
        let valor = input.val();
        if(typeof input.attr("id") !== "undefined"){
          jsonData.push({ 
            id: id,
            valor: valor,
          });
        }

    });
    return JSON.stringify(jsonData);
  }
  function json_ganancia_perdida(){
    let jsonData = [];
    $("#table-estado-ganancias-perdidas input").each(function () {
        let input = $(this);
        let id = input.attr("id");
        let valor = input.val();
        if(typeof input.attr("id") !== "undefined"){
          jsonData.push({ 
            id: id,
            valor: valor,
          });
        }

    });
    return JSON.stringify(jsonData);
  }
  
  calc_movimiento_comercial();
  function calc_movimiento_comercial(){
    //VENTAS
    let ganancias_venta_mensual = parseFloat($('#evaluacion_actual_ganancia_ventamensual').val());
    let credito_cobrando_venta_mensual = parseFloat($('#credito_cobrando_venta_mensual').val());

    let credito_porcentaje_venta_mensual = (credito_cobrando_venta_mensual / ganancias_venta_mensual)*100;
    if(isNaN(credito_porcentaje_venta_mensual) ||  credito_porcentaje_venta_mensual == Infinity ||  credito_porcentaje_venta_mensual == -Infinity){
      credito_porcentaje_venta_mensual = 0;
    }
    $('#credito_porcentaje_venta_mensual').val(credito_porcentaje_venta_mensual.toFixed(2))


    let contado_cobrando_venta_mensual = ganancias_venta_mensual - credito_cobrando_venta_mensual;
    if(isNaN(contado_cobrando_venta_mensual) ||  contado_cobrando_venta_mensual == Infinity ||  contado_cobrando_venta_mensual == -Infinity){
      contado_cobrando_venta_mensual = 0;
    }
    $('#contado_cobrando_venta_mensual').val(contado_cobrando_venta_mensual.toFixed(2))

    let contado_porcentaje_venta_mensual = (contado_cobrando_venta_mensual/ganancias_venta_mensual) * 100;
    if(isNaN(contado_porcentaje_venta_mensual) ||  contado_porcentaje_venta_mensual == Infinity ||  contado_porcentaje_venta_mensual == -Infinity){
      contado_porcentaje_venta_mensual = 0;
    }
    $('#contado_porcentaje_venta_mensual').val(contado_porcentaje_venta_mensual.toFixed(2))
    //COMPRAS

    let ganancias_costo_venta = parseFloat($('#evaluacion_actual_ganancia_costo_venta').val());
    let credito_cobrando_compra_mensual = parseFloat($('#credito_cobrando_compra_mensual').val());

    let credito_porcentaje_compra_mensual = (credito_cobrando_compra_mensual / ganancias_costo_venta)*100;
    if(isNaN(credito_porcentaje_compra_mensual) ||  credito_porcentaje_compra_mensual == Infinity ||  credito_porcentaje_compra_mensual == -Infinity){
      credito_porcentaje_compra_mensual = 0;
    }
    $('#credito_porcentaje_compra_mensual').val(credito_porcentaje_compra_mensual.toFixed(2))

    let contado_cobrando_compra_mensual = ganancias_costo_venta - credito_cobrando_compra_mensual;
    if(isNaN(contado_cobrando_compra_mensual) ||  contado_cobrando_compra_mensual == Infinity ||  contado_cobrando_compra_mensual == -Infinity){
      contado_cobrando_compra_mensual = 0;
    }
    $('#contado_cobrando_compra_mensual').val(contado_cobrando_compra_mensual.toFixed(2))

    let contado_porcentaje_compra_mensual = (contado_cobrando_compra_mensual/ganancias_costo_venta) * 100;
    if(isNaN(contado_porcentaje_compra_mensual) ||  contado_porcentaje_compra_mensual == Infinity ||  contado_porcentaje_compra_mensual == -Infinity){
      contado_porcentaje_compra_mensual = 0;
    }
    $('#contado_porcentaje_compra_mensual').val(contado_porcentaje_compra_mensual.toFixed(2))

  }
  
  
  calcular_excedente();
  function calcular_excedente(){
    let total_cuota = parseFloat({{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_cuota : '0.00' }});
    let total_lc_cuotas = parseFloat({{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_lc_cuotas : '0.00' }});
    let total_noregulada_cuota = parseFloat({{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_noregulada_cuota : '0.00' }});
    let evaluacion_actual_ganancia_excedente_mensual = parseFloat($('#evaluacion_actual_ganancia_excedente_mensual').val());

    let suma_cuotas = ( total_cuota + total_lc_cuotas + total_noregulada_cuota );
    let excedente_antes_propuesta = (suma_cuotas / ( evaluacion_actual_ganancia_excedente_mensual + suma_cuotas )) * 100;
    if(isNaN(excedente_antes_propuesta) ||  excedente_antes_propuesta == Infinity ||  excedente_antes_propuesta == -Infinity){
        excedente_antes_propuesta = 0;
    }
    $('#excedente_antes_propuesta').val(excedente_antes_propuesta.toFixed(2));


    let total_propuesta = parseFloat({{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_propuesta : '0.00' }});
    let excedente_propuesta_sin_deduccion = ( (total_propuesta + suma_cuotas ) / ( evaluacion_actual_ganancia_excedente_mensual + suma_cuotas )  ) * 100;
    if(isNaN(excedente_propuesta_sin_deduccion) ||  excedente_propuesta_sin_deduccion == Infinity ||  excedente_propuesta_sin_deduccion == -Infinity){
        excedente_propuesta_sin_deduccion = 0;
    }
    $('#excedente_propuesta_sin_deduccion').val(excedente_propuesta_sin_deduccion.toFixed(2));

    let total_cuota_deducciones = parseFloat({{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_cuota_deducciones : '0.00' }});
    let total_noregulada_cuota_deducciones = parseFloat({{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_noregulada_cuota_deducciones : '0.00' }});
    let suma_deducciones = total_cuota_deducciones + total_noregulada_cuota_deducciones;

    let excedente_propuesta_con_deduccion = ( ( (total_propuesta + suma_cuotas ) - suma_deducciones ) / ( evaluacion_actual_ganancia_excedente_mensual + suma_cuotas - suma_deducciones ) ) * 100;
    if(isNaN(excedente_propuesta_con_deduccion) ||  excedente_propuesta_con_deduccion == Infinity ||  excedente_propuesta_con_deduccion == -Infinity){
        excedente_propuesta_con_deduccion = 0;
    }
    $('#excedente_propuesta_con_deduccion').val(excedente_propuesta_con_deduccion.toFixed(2));


    evaluarCredito();
  }
  
  function evaluarCredito() {
    let excedente_propuesta_con_deduccion = parseFloat($('#excedente_propuesta_con_deduccion').val());
    
    let rango_menor = parseFloat($('#rango_menor').val());
    let rango_diferencia = parseFloat($('#rango_diferencia').val());
    let rango_tope = parseFloat($('#rango_tope').val());
    let estado_credito = '';
    if (excedente_propuesta_con_deduccion < 0) {
      estado_credito = "CREDITO NO VIABLE";
      $('#estado_credito').removeClass('bg-success');
      $('#estado_credito').addClass('bg-danger');
      
      $('#excedente_propuesta_con_deduccion').removeClass('bg-success');
      $('#excedente_propuesta_con_deduccion').addClass('bg-danger');
      $('#btn-guardar-cambios-deudas').attr('disabled',true)
    } else if (excedente_propuesta_con_deduccion <= rango_tope) {
      estado_credito = "CREDITO VIABLE";
      $('#estado_credito').removeClass('bg-danger');
      $('#estado_credito').addClass('bg-success');
      $('#excedente_propuesta_con_deduccion').removeClass('bg-danger');
      $('#excedente_propuesta_con_deduccion').addClass('bg-success');
      $('#btn-guardar-cambios-deudas').attr('disabled',false)
      // validar boton
      let estado_regulada_noregulada =$('#estado_regulada_noregulada').val();
      if(estado_regulada_noregulada == 'ERROR'){
        $('#btn-guardar-cambios-deudas').attr('disabled',true)
      }else{
        $('#btn-guardar-cambios-deudas').attr('disabled',false)
      }
    } else if (excedente_propuesta_con_deduccion > rango_tope){
      estado_credito = "CREDITO NO VIABLE";
      $('#estado_credito').removeClass('bg-success');
      $('#estado_credito').addClass('bg-danger');
      $('#excedente_propuesta_con_deduccion').removeClass('bg-success');
      $('#excedente_propuesta_con_deduccion').addClass('bg-danger');
      $('#btn-guardar-cambios-deudas').attr('disabled',true)
    }
    $('#estado_credito').val(estado_credito);
  }
</script>    