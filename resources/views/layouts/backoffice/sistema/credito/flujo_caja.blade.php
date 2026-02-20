<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/credito/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'flujo_caja',
              encabezado : json_evaluacion_encabezado(),
              evaluacion_meses : json_evaluacion_meses(),
              json_flujo_caja : json_flujo_caja(),
              entidad_regulada : json_individual('entidad_regulada'),
              linea_credito : json_individual('linea_credito'),
              entidad_noregulada : json_individual('entidad_noregulada'),
              comentarios : json_comentarios(),
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
      $balance_general = $credito_evaluacion_cuantitativa ? ( $credito_evaluacion_cuantitativa->balance_general == "" ? [] : json_decode($credito_evaluacion_cuantitativa->balance_general) ) : [];
      $saldo_inicial = encontrar_valor('evaluacion_actual_caja', $balance_general) + encontrar_valor('evaluacion_actual_bancos', $balance_general);
      $ganancia_perdida = $credito_evaluacion_cuantitativa ? ( $credito_evaluacion_cuantitativa->ganancia_perdida == "" ? [] : json_decode($credito_evaluacion_cuantitativa->ganancia_perdida) ) : [];
  
      $ganancia_perdida_ing_adicional = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->ganancias_perdidas == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->ganancias_perdidas) ) : [];
      $entidad_regulada = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->entidad_regulada == "" ? [] : json_decode($credito_cuantitativa_deudas->entidad_regulada) ) : [];
      $linea_credito = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->linea_credito == "" ? [] : json_decode($credito_cuantitativa_deudas->linea_credito) ) : [];
      $entidad_noregulada = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->entidad_noregulada == "" ? [] : json_decode($credito_cuantitativa_deudas->entidad_noregulada) ) : [];
      $venta_mensual = 0;
      $flujo_caja = $credito_flujo_caja ? ( $credito_flujo_caja->flujo_caja == "" ? [] : json_decode($credito_flujo_caja->flujo_caja) ) : [];
      
      $entidad_reguladas_json = $credito_flujo_caja ? ( $credito_flujo_caja->entidad_reguladas == "" ? [] : json_decode($credito_flujo_caja->entidad_reguladas) ) : [];
      $linea_credito_json = $credito_flujo_caja ? ( $credito_flujo_caja->linea_credito == "" ? [] : json_decode($credito_flujo_caja->linea_credito) ) : [];
      $entidad_noregulada_json = $credito_flujo_caja ? ( $credito_flujo_caja->entidad_noregulada == "" ? [] : json_decode($credito_flujo_caja->entidad_noregulada) ) : [];
  
      $comentarios = $credito_flujo_caja ? ( $credito_flujo_caja->comentarios == "" ? [] : json_decode($credito_flujo_caja->comentarios) ) : [];
    @endphp
    <div class="modal-header" style="border-bottom: 0;">
        <h5 class="modal-title">FLUJO DE CAJA</h5>
        <button type="button" class="btn-close text-white" id="modal-close-garantia-cliente" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body modal-body-cualitativa">
      <div class="row">
        <div class="col-sm-12 col-md-5">
          
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">CLIENTE/RAZON SOCIAL:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $credito->nombreclientecredito }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">PRODUCTO:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->nombreproductocredito }}" disabled>
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-3">
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">MODALIDAD:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->modalidad_credito_nombre }}" disabled>
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
            <label class="col-sm-3 col-form-label" style="text-align: right;">FECHA:</label>
            <div class="col-sm-7">
              <input type="date" step="any" class="form-control" value="{{ date_format(date_create($credito->fecha),'Y-m-d') }}" disabled>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-sm-12">
          <table class="table table-bordered" id="table-flujo-caja">
            <thead>
              <tr>
                <th width="150px">PERIODO/DETALLE</th>
              </tr>
              <tr>
                <th>MESES</th>
              </tr>
            </thead>
            <tbody>
              @if(count($evaluacion_meses) > 0)

                @foreach ($evaluacion_meses as $key => $row)
                  @if( $key == 1)
                  <tr porcentaje_cliclo_negocio encabezado>
                    <td>CICLO DEL NEGOCIO <b>(%)</b></td>
                    @php $contador_meses = 1 @endphp
                    @php $primer_mes = 0 @endphp
                    @foreach ($row as $header => $cellData)
                        @if ($header !== 'Mes')
                          @php $contador_meses++ @endphp
                          @php
                            $value = $cellData->value;
                            if($contador_meses == 2){
                              $primer_mes = $value;
                            }
                          @endphp
                          <td><input type='text' valida_input_vacio class='form-control' onkeyup="calcula_monto_meses(this)" value='{{ $value }}' disabled></td>
                        @endif
                    @endforeach
                    <td><input type='text' valida_input_vacio class='form-control' onkeyup="calcula_monto_meses(this)" value='{{$primer_mes}}' disabled></td>
                  </tr>
                  @endif
                @endforeach
              @endif
              <tr>
                <td>Saldo Inicial Caja</td>
                <td><input type="text" valida_input_vacio class="form-control" id="saldo_inicial" value="{{ $saldo_inicial }}" disabled></td>
              </tr>
              <tr>
                <td colspan=14>INGRESOS (S/.)</td>
              </tr>
              <tr fila="ingresos">
                <td>Ventas</td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cero_ventas" value="{{ encontrar_valor('evaluacion_actual_ganancia_ventamensual', $ganancia_perdida) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_uno_ventas" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_dos_ventas" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_tres_ventas" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cuatro_ventas" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cinco_ventas" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_seis_ventas" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_siete_ventas" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_ocho_ventas" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_nueve_ventas" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_diez_ventas" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_once_ventas" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_doce_ventas" value="0.00"></td>
              </tr>
              <tr fila="ingresos" tipo="otras_ventas">
                <td>Otras Ventas</td>
                
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cero_otras_ventas" value="{{ encontrar_valor('mes_cero_otras_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_otras_ventas" value="{{ encontrar_valor('mes_uno_otras_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_otras_ventas" value="{{ encontrar_valor('mes_dos_otras_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_otras_ventas" value="{{ encontrar_valor('mes_tres_otras_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_otras_ventas" value="{{ encontrar_valor('mes_cuatro_otras_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_otras_ventas" value="{{ encontrar_valor('mes_cinco_otras_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_otras_ventas" value="{{ encontrar_valor('mes_seis_otras_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_otras_ventas" value="{{ encontrar_valor('mes_siete_otras_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_otras_ventas" value="{{ encontrar_valor('mes_ocho_otras_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_otras_ventas" value="{{ encontrar_valor('mes_nueve_otras_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_otras_ventas" value="{{ encontrar_valor('mes_diez_otras_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_otras_ventas" value="{{ encontrar_valor('mes_once_otras_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_otras_ventas" value="{{ encontrar_valor('mes_doce_otras_ventas', $flujo_caja) }}"></td>
              </tr>
              <tr fila="ingresos" tipo="cobranza">
                <td>Cobranza de cuentas por cobrar</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cero_cobranza" value="{{ encontrar_valor('mes_cero_cobranza', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_cobranza" value="{{ encontrar_valor('mes_uno_cobranza', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_cobranza" value="{{ encontrar_valor('mes_dos_cobranza', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_cobranza" value="{{ encontrar_valor('mes_tres_cobranza', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_cobranza" value="{{ encontrar_valor('mes_cuatro_cobranza', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_cobranza" value="{{ encontrar_valor('mes_cinco_cobranza', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_cobranza" value="{{ encontrar_valor('mes_seis_cobranza', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_cobranza" value="{{ encontrar_valor('mes_siete_cobranza', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_cobranza" value="{{ encontrar_valor('mes_ocho_cobranza', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_cobranza" value="{{ encontrar_valor('mes_nueve_cobranza', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_cobranza" value="{{ encontrar_valor('mes_diez_cobranza', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_cobranza" value="{{ encontrar_valor('mes_once_cobranza', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_cobranza" value="{{ encontrar_valor('mes_doce_cobranza', $flujo_caja) }}"></td>
              </tr>
              <tr fila="ingresos" tipo="activo_fijo">
                <td>Venta de Activo Fijo</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cero_activo_fijo" value="{{ encontrar_valor('mes_cero_activo_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_activo_fijo" value="{{ encontrar_valor('mes_uno_activo_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_activo_fijo" value="{{ encontrar_valor('mes_dos_activo_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_activo_fijo" value="{{ encontrar_valor('mes_tres_activo_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_activo_fijo" value="{{ encontrar_valor('mes_cuatro_activo_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_activo_fijo" value="{{ encontrar_valor('mes_cinco_activo_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_activo_fijo" value="{{ encontrar_valor('mes_seis_activo_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_activo_fijo" value="{{ encontrar_valor('mes_siete_activo_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_activo_fijo" value="{{ encontrar_valor('mes_ocho_activo_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_activo_fijo" value="{{ encontrar_valor('mes_nueve_activo_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_activo_fijo" value="{{ encontrar_valor('mes_diez_activo_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_activo_fijo" value="{{ encontrar_valor('mes_once_activo_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_activo_fijo" value="{{ encontrar_valor('mes_doce_activo_fijo', $flujo_caja) }}"></td>
              </tr>
              <tr fila="ingresos" tipo="negocio_adicional">
                <td>Ingreso por Negocio Adicional</td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" id="mes_cero_negocio_adicional" value="{{ encontrar_valor('ganancias_excedente_mensual', $ganancia_perdida_ing_adicional) }}" disabled></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_negocio_adicional" value="{{ encontrar_valor('mes_uno_negocio_adicional', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_negocio_adicional" value="{{ encontrar_valor('mes_dos_negocio_adicional', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_negocio_adicional" value="{{ encontrar_valor('mes_tres_negocio_adicional', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_negocio_adicional" value="{{ encontrar_valor('mes_cuatro_negocio_adicional', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_negocio_adicional" value="{{ encontrar_valor('mes_cinco_negocio_adicional', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_negocio_adicional" value="{{ encontrar_valor('mes_seis_negocio_adicional', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_negocio_adicional" value="{{ encontrar_valor('mes_siete_negocio_adicional', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_negocio_adicional" value="{{ encontrar_valor('mes_ocho_negocio_adicional', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_negocio_adicional" value="{{ encontrar_valor('mes_nueve_negocio_adicional', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_negocio_adicional" value="{{ encontrar_valor('mes_diez_negocio_adicional', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_negocio_adicional" value="{{ encontrar_valor('mes_once_negocio_adicional', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_negocio_adicional" value="{{ encontrar_valor('mes_doce_negocio_adicional', $flujo_caja) }}"></td>
              </tr>
              <tr fila="ingresos" tipo="ingreso_fijo">
                <td>Ingresos Fijos</td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" id="mes_cero_ingreso_fijo" value="{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_ingreso_adicional : 0 }}" disabled></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_ingreso_fijo" value="{{ encontrar_valor('mes_uno_ingreso_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_ingreso_fijo" value="{{ encontrar_valor('mes_dos_ingreso_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_ingreso_fijo" value="{{ encontrar_valor('mes_tres_ingreso_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_ingreso_fijo" value="{{ encontrar_valor('mes_cuatro_ingreso_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_ingreso_fijo" value="{{ encontrar_valor('mes_cinco_ingreso_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_ingreso_fijo" value="{{ encontrar_valor('mes_seis_ingreso_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_ingreso_fijo" value="{{ encontrar_valor('mes_siete_ingreso_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_ingreso_fijo" value="{{ encontrar_valor('mes_ocho_ingreso_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_ingreso_fijo" value="{{ encontrar_valor('mes_nueve_ingreso_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_ingreso_fijo" value="{{ encontrar_valor('mes_diez_ingreso_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_ingreso_fijo" value="{{ encontrar_valor('mes_once_ingreso_fijo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_ingreso_fijo" value="{{ encontrar_valor('mes_doce_ingreso_fijo', $flujo_caja) }}"></td>
              </tr>
              <tr fila="ingresos" tipo="ingreso_financiero">
                <td>Ingresos Financieros</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cero_ingreso_financiero" value="{{ encontrar_valor('mes_cero_ingreso_financiero', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_ingreso_financiero" value="{{ encontrar_valor('mes_uno_ingreso_financiero', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_ingreso_financiero" value="{{ encontrar_valor('mes_dos_ingreso_financiero', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_ingreso_financiero" value="{{ encontrar_valor('mes_tres_ingreso_financiero', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_ingreso_financiero" value="{{ encontrar_valor('mes_cuatro_ingreso_financiero', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_ingreso_financiero" value="{{ encontrar_valor('mes_cinco_ingreso_financiero', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_ingreso_financiero" value="{{ encontrar_valor('mes_seis_ingreso_financiero', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_ingreso_financiero" value="{{ encontrar_valor('mes_siete_ingreso_financiero', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_ingreso_financiero" value="{{ encontrar_valor('mes_ocho_ingreso_financiero', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_ingreso_financiero" value="{{ encontrar_valor('mes_nueve_ingreso_financiero', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_ingreso_financiero" value="{{ encontrar_valor('mes_diez_ingreso_financiero', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_ingreso_financiero" value="{{ encontrar_valor('mes_once_ingreso_financiero', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_ingreso_financiero" value="{{ encontrar_valor('mes_doce_ingreso_financiero', $flujo_caja) }}"></td>
              </tr>
              <tr fila="ingresos" tipo="ingreso_extra">
                <td>Ingresos Extraordinarios</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cero_ingreso_extra" value="{{ encontrar_valor('mes_cero_ingreso_extra', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_ingreso_extra" value="{{ encontrar_valor('mes_uno_ingreso_extra', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_ingreso_extra" value="{{ encontrar_valor('mes_dos_ingreso_extra', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_ingreso_extra" value="{{ encontrar_valor('mes_tres_ingreso_extra', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_ingreso_extra" value="{{ encontrar_valor('mes_cuatro_ingreso_extra', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_ingreso_extra" value="{{ encontrar_valor('mes_cinco_ingreso_extra', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_ingreso_extra" value="{{ encontrar_valor('mes_seis_ingreso_extra', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_ingreso_extra" value="{{ encontrar_valor('mes_siete_ingreso_extra', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_ingreso_extra" value="{{ encontrar_valor('mes_ocho_ingreso_extra', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_ingreso_extra" value="{{ encontrar_valor('mes_nueve_ingreso_extra', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_ingreso_extra" value="{{ encontrar_valor('mes_diez_ingreso_extra', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_ingreso_extra" value="{{ encontrar_valor('mes_once_ingreso_extra', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_ingreso_extra" value="{{ encontrar_valor('mes_doce_ingreso_extra', $flujo_caja) }}"></td>
              </tr>
              <tr fila="ingresos">
                <td>Préstamo SOLICITADO</td>
                <td><input type="hidden" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cero_prestamo_solicitado" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" id="mes_uno_prestamo_solicitado" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->propuesta_monto: 0 }}" disabled></td>
                <td><input type="hidden" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_prestamo_solicitado" value="0.00"></td>
                <td><input type="hidden" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_prestamo_solicitado" value="0.00"></td>
                <td><input type="hidden" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_prestamo_solicitado" value="0.00"></td>
                <td><input type="hidden" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_prestamo_solicitado" value="0.00"></td>
                <td><input type="hidden" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_prestamo_solicitado" value="0.00"></td>
                <td><input type="hidden" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_prestamo_solicitado" value="0.00"></td>
                <td><input type="hidden" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_prestamo_solicitado" value="0.00"></td>
                <td><input type="hidden" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_prestamo_solicitado" value="0.00"></td>
                <td><input type="hidden" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_prestamo_solicitado" value="0.00"></td>
                <td><input type="hidden" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_prestamo_solicitado" value="0.00"></td>
                <td><input type="hidden" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_prestamo_solicitado" value="0.00"></td>
              </tr>
              <tr fila="ingresos" tipo="otros_prestamos">
                <td>Otros Préstamos</td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cero_otros_prestamo" value="{{ encontrar_valor('mes_cero_otros_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_otros_prestamo" value="{{ encontrar_valor('mes_uno_otros_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_otros_prestamo" value="{{ encontrar_valor('mes_dos_otros_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_otros_prestamo" value="{{ encontrar_valor('mes_tres_otros_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_otros_prestamo" value="{{ encontrar_valor('mes_cuatro_otros_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_otros_prestamo" value="{{ encontrar_valor('mes_cinco_otros_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_otros_prestamo" value="{{ encontrar_valor('mes_seis_otros_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_otros_prestamo" value="{{ encontrar_valor('mes_siete_otros_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_otros_prestamo" value="{{ encontrar_valor('mes_ocho_otros_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_otros_prestamo" value="{{ encontrar_valor('mes_nueve_otros_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_otros_prestamo" value="{{ encontrar_valor('mes_diez_otros_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_otros_prestamo" value="{{ encontrar_valor('mes_once_otros_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_otros_prestamo" value="{{ encontrar_valor('mes_doce_otros_prestamo', $flujo_caja) }}"></td>
              </tr>
              <tr>
                <td><b>Total Ingresos (A) (S/.)</b></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cero_total_ingreso" value="{{ encontrar_valor('mes_cero_total_ingreso', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_uno_total_ingreso" value="{{ encontrar_valor('mes_uno_total_ingreso', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_dos_total_ingreso" value="{{ encontrar_valor('mes_dos_total_ingreso', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_tres_total_ingreso" value="{{ encontrar_valor('mes_tres_total_ingreso', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cuatro_total_ingreso" value="{{ encontrar_valor('mes_cuatro_total_ingreso', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cinco_total_ingreso" value="{{ encontrar_valor('mes_cinco_total_ingreso', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_seis_total_ingreso" value="{{ encontrar_valor('mes_seis_total_ingreso', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_siete_total_ingreso" value="{{ encontrar_valor('mes_siete_total_ingreso', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_ocho_total_ingreso" value="{{ encontrar_valor('mes_ocho_total_ingreso', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_nueve_total_ingreso" value="{{ encontrar_valor('mes_nueve_total_ingreso', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_diez_total_ingreso" value="{{ encontrar_valor('mes_diez_total_ingreso', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_once_total_ingreso" value="{{ encontrar_valor('mes_once_total_ingreso', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_doce_total_ingreso" value="{{ encontrar_valor('mes_doce_total_ingreso', $flujo_caja) }}"></td>
              </tr>
              <script>
                calc_flujo_caja();
                function calc_flujo_caja(){
                   let path_cliclo_negocio = $('#table-flujo-caja > tbody').find('tr[porcentaje_cliclo_negocio]');

                   let porcentaje_mes_cero_ventas = parseFloat(path_cliclo_negocio.find('td:eq(1) input').val());
                   let porcentaje_mes_uno_ventas = parseFloat(path_cliclo_negocio.find('td:eq(2) input').val());
                   let porcentaje_mes_dos_ventas = parseFloat(path_cliclo_negocio.find('td:eq(3) input').val());
                   let porcentaje_mes_tres_ventas = parseFloat(path_cliclo_negocio.find('td:eq(4) input').val());
                   let porcentaje_mes_cuatro_ventas = parseFloat(path_cliclo_negocio.find('td:eq(5) input').val());
                   let porcentaje_mes_cinco_ventas = parseFloat(path_cliclo_negocio.find('td:eq(6) input').val());
                   let porcentaje_mes_seis_ventas = parseFloat(path_cliclo_negocio.find('td:eq(7) input').val());
                   let porcentaje_mes_siete_ventas = parseFloat(path_cliclo_negocio.find('td:eq(8) input').val());
                   let porcentaje_mes_ocho_ventas = parseFloat(path_cliclo_negocio.find('td:eq(9) input').val());
                   let porcentaje_mes_nueve_ventas = parseFloat(path_cliclo_negocio.find('td:eq(10) input').val());
                   let porcentaje_mes_diez_ventas = parseFloat(path_cliclo_negocio.find('td:eq(11) input').val());
                   let porcentaje_mes_once_ventas = parseFloat(path_cliclo_negocio.find('td:eq(12) input').val());
                   let porcentaje_mes_doce_ventas = parseFloat(path_cliclo_negocio.find('td:eq(13) input').val());

                   let mes_cero_ventas = parseFloat($('#mes_cero_ventas').val())

                   let mes_uno_ventas = mes_cero_ventas * (porcentaje_mes_uno_ventas/100);
                   $('#mes_uno_ventas').val(mes_uno_ventas.toFixed(2))
                   let mes_dos_ventas = mes_cero_ventas * (porcentaje_mes_dos_ventas/100);
                   $('#mes_dos_ventas').val(mes_dos_ventas.toFixed(2))
                   let mes_tres_ventas = mes_cero_ventas * (porcentaje_mes_tres_ventas/100);
                   $('#mes_tres_ventas').val(mes_tres_ventas.toFixed(2))
                   let mes_cuatro_ventas = mes_cero_ventas * (porcentaje_mes_cuatro_ventas/100);
                   $('#mes_cuatro_ventas').val(mes_cuatro_ventas.toFixed(2))
                   let mes_cinco_ventas = mes_cero_ventas * (porcentaje_mes_cinco_ventas/100);
                   $('#mes_cinco_ventas').val(mes_cinco_ventas.toFixed(2))
                   let mes_seis_ventas = mes_cero_ventas * (porcentaje_mes_seis_ventas/100);
                   $('#mes_seis_ventas').val(mes_seis_ventas.toFixed(2))
                   let mes_siete_ventas = mes_cero_ventas * (porcentaje_mes_siete_ventas/100);
                   $('#mes_siete_ventas').val(mes_siete_ventas.toFixed(2))
                   let mes_ocho_ventas = mes_cero_ventas * (porcentaje_mes_ocho_ventas/100);
                   $('#mes_ocho_ventas').val(mes_ocho_ventas.toFixed(2))
                   let mes_nueve_ventas = mes_cero_ventas * (porcentaje_mes_nueve_ventas/100);
                   $('#mes_nueve_ventas').val(mes_nueve_ventas.toFixed(2))
                   let mes_diez_ventas = mes_cero_ventas * (porcentaje_mes_diez_ventas/100);
                   $('#mes_diez_ventas').val(mes_diez_ventas.toFixed(2))
                   let mes_once_ventas = mes_cero_ventas * (porcentaje_mes_once_ventas/100);
                   $('#mes_once_ventas').val(mes_once_ventas.toFixed(2))
                   let mes_doce_ventas = mes_cero_ventas * (porcentaje_mes_cero_ventas/100);
                   $('#mes_doce_ventas').val(mes_doce_ventas.toFixed(2))
                    
                   let porcentaje_costo_venta = parseFloat($('#porcentaje_costo_venta').val());

                   let mes_uno_compras = mes_uno_ventas * (porcentaje_costo_venta/100);
                   $('#mes_uno_compras').val(mes_uno_compras.toFixed(2));
                  
                   let mes_dos_compras = mes_dos_ventas * (porcentaje_costo_venta/100);
                   $('#mes_dos_compras').val(mes_dos_compras.toFixed(2));
                   let mes_tres_compras = mes_tres_ventas * (porcentaje_costo_venta/100);
                   $('#mes_tres_compras').val(mes_tres_compras.toFixed(2));
                   let mes_cuatro_compras = mes_cuatro_ventas * (porcentaje_costo_venta/100);
                   $('#mes_cuatro_compras').val(mes_cuatro_compras.toFixed(2));
                   let mes_cinco_compras = mes_cinco_ventas * (porcentaje_costo_venta/100);
                   $('#mes_cinco_compras').val(mes_cinco_compras.toFixed(2));
                   let mes_seis_compras = mes_seis_ventas * (porcentaje_costo_venta/100);
                   $('#mes_seis_compras').val(mes_seis_compras.toFixed(2));
                   let mes_siete_compras = mes_siete_ventas * (porcentaje_costo_venta/100);
                   $('#mes_siete_compras').val(mes_siete_compras.toFixed(2));
                   let mes_ocho_compras = mes_ocho_ventas * (porcentaje_costo_venta/100);
                   $('#mes_ocho_compras').val(mes_ocho_compras.toFixed(2));
                   let mes_nueve_compras = mes_nueve_ventas * (porcentaje_costo_venta/100);
                   $('#mes_nueve_compras').val(mes_nueve_compras.toFixed(2));
                   let mes_diez_compras = mes_diez_ventas * (porcentaje_costo_venta/100);
                   $('#mes_diez_compras').val(mes_diez_compras.toFixed(2));
                   let mes_once_compras = mes_once_ventas * (porcentaje_costo_venta/100);
                   $('#mes_once_compras').val(mes_once_compras.toFixed(2));
                   let mes_doce_compras = mes_doce_ventas * (porcentaje_costo_venta/100);
                   $('#mes_doce_compras').val(mes_doce_compras.toFixed(2));

                 
                   calcula_total_ingresos();
                   calcula_total_egresos();
                   
                }
                
                $('tr[fila="ingresos"][tipo] input').on('input', function() {
                  let valor = $(this).val();
                  let indice = $(this).parent().index();
                  let tipo = $(this).closest('tr').attr('tipo');
                  let inputs;
                  if (tipo) {
                    inputs = $('tr[fila="ingresos"][tipo="' + tipo + '"] input');
                  }
                  inputs.slice(indice).val(valor);
                });
                
                function copiar_valor_fila(filaTipo,input = 'cero') {
                  if(filaTipo == 'entidad_regulada' || filaTipo == 'linea_credito' || filaTipo == 'entidad_noregulada' ){
                     $('#table-flujo-caja > tbody > tr[tipo="' + filaTipo + '"]').each(function() {
                      let valorInicial = parseFloat($(this).find('input[mes_'+ input +'_' + filaTipo+']').val());
                      $(this).find('input').val(valorInicial.toFixed(2));
                    });
                  }else{
                    $('tr[tipo="' + filaTipo + '"] input').each(function() {
                      let valorInicial = parseFloat($('#mes_'+ input +'_' + filaTipo).val());
                      if ($(this).attr('type') !== 'hidden') {
                          $(this).val(valorInicial.toFixed(2));
                      }
                    });
                  }
                  
                }
                $('tr[fila="egreso"][tipo] > td > input').on('input', function() {
                  let valor = $(this).val();
                  let indice = $(this).parent().index();
                  let tipo = $(this).closest('tr').attr('tipo');
                  let key = $(this).closest('tr').attr('key');
                  let inputs;
                  if (key) {
                    inputs = $('tr[fila="egreso"][tipo="' + tipo + '"][key="' + key + '"] > td > input');
                    inputs.slice(indice+1).val(valor);
                    
                  }else{
                    inputs = $('tr[fila="egreso"][tipo="' + tipo + '"] > td > input');
                    inputs.slice(indice).val(valor);
                  }
                });
                
                //copiar_valor_fila('negocio_adicional');
//                 copiar_valor_fila('ingreso_fijo');
                //copiar_valor_fila('gasto_admin');
                //copiar_valor_fila('gasto_ventas');
                //copiar_valor_fila('servicios');
                //copiar_valor_fila('alquiler');
                //copiar_valor_fila('autovaluo');
                //copiar_valor_fila('transporte');
                //copiar_valor_fila('entidad_regulada');
                //copiar_valor_fila('linea_credito');
                //copiar_valor_fila('entidad_noregulada');
                //copiar_valor_fila('pago_prestamo','uno');
                //copiar_valor_fila('sunat');
                //copiar_valor_fila('otros_gastos');
                //copiar_valor_fila('canasta_familiar');
                

                
                function calcula_total_ingresos(){
                  let mes_cero_total_ingreso = 0;
                  let mes_uno_total_ingreso = 0;
                  let mes_dos_total_ingreso = 0;
                  let mes_tres_total_ingreso = 0;
                  let mes_cuatro_total_ingreso = 0;
                  let mes_cinco_total_ingreso = 0;
                  let mes_seis_total_ingreso = 0;
                  let mes_siete_total_ingreso = 0;
                  let mes_ocho_total_ingreso = 0;
                  let mes_nueve_total_ingreso = 0;
                  let mes_diez_total_ingreso = 0;
                  let mes_once_total_ingreso = 0;
                  let mes_doce_total_ingreso = 0;
                  $('#table-flujo-caja > tbody > tr[fila="ingresos"]').each(function () {

                    let mes_cero = parseFloat($(this).find('td:eq(1) input').val());
                    let mes_uno = parseFloat($(this).find('td:eq(2) input').val());
                    let mes_dos = parseFloat($(this).find('td:eq(3) input').val());
                    let mes_tres = parseFloat($(this).find('td:eq(4) input').val());
                    let mes_cuatro = parseFloat($(this).find('td:eq(5) input').val());
                    let mes_cinco = parseFloat($(this).find('td:eq(6) input').val());
                    let mes_seis = parseFloat($(this).find('td:eq(7) input').val());
                    let mes_siete = parseFloat($(this).find('td:eq(8) input').val());
                    let mes_ocho = parseFloat($(this).find('td:eq(9) input').val());
                    let mes_nueve = parseFloat($(this).find('td:eq(10) input').val());
                    let mes_diez = parseFloat($(this).find('td:eq(11) input').val());
                    let mes_once = parseFloat($(this).find('td:eq(12) input').val());
                    let mes_doce = parseFloat($(this).find('td:eq(13) input').val());
                    mes_cero_total_ingreso += mes_cero;
                    mes_uno_total_ingreso += mes_uno;
                    mes_dos_total_ingreso += mes_dos;
                    mes_tres_total_ingreso += mes_tres;
                    mes_cuatro_total_ingreso += mes_cuatro;
                    mes_cinco_total_ingreso += mes_cinco;
                    mes_seis_total_ingreso += mes_seis;
                    mes_siete_total_ingreso += mes_siete;
                    mes_ocho_total_ingreso += mes_ocho;
                    mes_nueve_total_ingreso += mes_nueve;
                    mes_diez_total_ingreso += mes_diez;
                    mes_once_total_ingreso += mes_once;
                    mes_doce_total_ingreso += mes_doce;

                  });
                  $('#mes_cero_total_ingreso').val(mes_cero_total_ingreso.toFixed(2))
                  
                  $('#mes_uno_total_ingreso').val(mes_uno_total_ingreso.toFixed(2))
                  $('#mes_dos_total_ingreso').val(mes_dos_total_ingreso.toFixed(2))
                  $('#mes_tres_total_ingreso').val(mes_tres_total_ingreso.toFixed(2))
                  $('#mes_cuatro_total_ingreso').val(mes_cuatro_total_ingreso.toFixed(2))
                  $('#mes_cinco_total_ingreso').val(mes_cinco_total_ingreso.toFixed(2))
                  $('#mes_seis_total_ingreso').val(mes_seis_total_ingreso.toFixed(2))
                  $('#mes_siete_total_ingreso').val(mes_siete_total_ingreso.toFixed(2))
                  $('#mes_ocho_total_ingreso').val(mes_ocho_total_ingreso.toFixed(2))
                  $('#mes_nueve_total_ingreso').val(mes_nueve_total_ingreso.toFixed(2))
                  $('#mes_diez_total_ingreso').val(mes_diez_total_ingreso.toFixed(2))
                  $('#mes_once_total_ingreso').val(mes_once_total_ingreso.toFixed(2))
                  $('#mes_doce_total_ingreso').val(mes_doce_total_ingreso.toFixed(2))
                  calcular_saldo_mensual();
                }
                function calcula_total_egresos(){
                  let mes_cero_total_egreso = 0;
                  let mes_uno_total_egreso = 0;
                  let mes_dos_total_egreso = 0;
                  let mes_tres_total_egreso = 0;
                  let mes_cuatro_total_egreso = 0;
                  let mes_cinco_total_egreso = 0;
                  let mes_seis_total_egreso = 0;
                  let mes_siete_total_egreso = 0;
                  let mes_ocho_total_egreso = 0;
                  let mes_nueve_total_egreso = 0;
                  let mes_diez_total_egreso = 0;
                  let mes_once_total_egreso = 0;
                  let mes_doce_total_egreso = 0;
                  $('#table-flujo-caja > tbody > tr[fila="egreso"]').each(function () {

                    let mes_cero = parseFloat($(this).find('td:eq(1) input').val());
                    let mes_uno = parseFloat($(this).find('td:eq(2) input').val());
                    let mes_dos = parseFloat($(this).find('td:eq(3) input').val());
                    let mes_tres = parseFloat($(this).find('td:eq(4) input').val());
                    let mes_cuatro = parseFloat($(this).find('td:eq(5) input').val());
                    let mes_cinco = parseFloat($(this).find('td:eq(6) input').val());
                    let mes_seis = parseFloat($(this).find('td:eq(7) input').val());
                    let mes_siete = parseFloat($(this).find('td:eq(8) input').val());
                    let mes_ocho = parseFloat($(this).find('td:eq(9) input').val());
                    let mes_nueve = parseFloat($(this).find('td:eq(10) input').val());
                    let mes_diez = parseFloat($(this).find('td:eq(11) input').val());
                    let mes_once = parseFloat($(this).find('td:eq(12) input').val());
                    let mes_doce = parseFloat($(this).find('td:eq(13) input').val());
                    mes_cero_total_egreso += mes_cero;
                    mes_uno_total_egreso += mes_uno;
                    mes_dos_total_egreso += mes_dos;
                    mes_tres_total_egreso += mes_tres;
                    mes_cuatro_total_egreso += mes_cuatro;
                    mes_cinco_total_egreso += mes_cinco;
                    mes_seis_total_egreso += mes_seis;
                    mes_siete_total_egreso += mes_siete;
                    mes_ocho_total_egreso += mes_ocho;
                    mes_nueve_total_egreso += mes_nueve;
                    mes_diez_total_egreso += mes_diez;
                    mes_once_total_egreso += mes_once;
                    mes_doce_total_egreso += mes_doce;

                  });
                  $('#mes_cero_total_egreso').val(mes_cero_total_egreso.toFixed(2))
                  
                  $('#mes_uno_total_egreso').val(mes_uno_total_egreso.toFixed(2))
                  $('#mes_dos_total_egreso').val(mes_dos_total_egreso.toFixed(2))
                  $('#mes_tres_total_egreso').val(mes_tres_total_egreso.toFixed(2))
                  $('#mes_cuatro_total_egreso').val(mes_cuatro_total_egreso.toFixed(2))
                  $('#mes_cinco_total_egreso').val(mes_cinco_total_egreso.toFixed(2))
                  $('#mes_seis_total_egreso').val(mes_seis_total_egreso.toFixed(2))
                  $('#mes_siete_total_egreso').val(mes_siete_total_egreso.toFixed(2))
                  $('#mes_ocho_total_egreso').val(mes_ocho_total_egreso.toFixed(2))
                  $('#mes_nueve_total_egreso').val(mes_nueve_total_egreso.toFixed(2))
                  $('#mes_diez_total_egreso').val(mes_diez_total_egreso.toFixed(2))
                  $('#mes_once_total_egreso').val(mes_once_total_egreso.toFixed(2))
                  $('#mes_doce_total_egreso').val(mes_doce_total_egreso.toFixed(2))
                  calcular_saldo_mensual();
                }
                $("#table-flujo-caja > tbody > tr > td > input").on("keyup", function() {
                    calc_flujo_caja();
                });
                $("#table-flujo-caja > tbody > tr > td > input[valida_input_vacio]").on('blur', function() {
                    calc_flujo_caja();
                });
                function calcular_saldo_mensual(){
                  
                  let mes_cero_total_ingreso = parseFloat($('#mes_cero_total_ingreso').val());
                  let mes_uno_total_ingreso = parseFloat($('#mes_uno_total_ingreso').val());
                  let mes_dos_total_ingreso = parseFloat($('#mes_dos_total_ingreso').val());
                  let mes_tres_total_ingreso = parseFloat($('#mes_tres_total_ingreso').val());
                  let mes_cuatro_total_ingreso = parseFloat($('#mes_cuatro_total_ingreso').val());
                  let mes_cinco_total_ingreso = parseFloat($('#mes_cinco_total_ingreso').val());
                  let mes_seis_total_ingreso = parseFloat($('#mes_seis_total_ingreso').val());
                  let mes_siete_total_ingreso = parseFloat($('#mes_siete_total_ingreso').val());
                  let mes_ocho_total_ingreso = parseFloat($('#mes_ocho_total_ingreso').val());
                  let mes_nueve_total_ingreso = parseFloat($('#mes_nueve_total_ingreso').val());
                  let mes_diez_total_ingreso = parseFloat($('#mes_diez_total_ingreso').val());
                  let mes_once_total_ingreso = parseFloat($('#mes_once_total_ingreso').val());
                  let mes_doce_total_ingreso = parseFloat($('#mes_doce_total_ingreso').val());

                  let mes_cero_total_egreso = parseFloat($('#mes_cero_total_egreso').val());
                  let mes_uno_total_egreso = parseFloat($('#mes_uno_total_egreso').val());
                  let mes_dos_total_egreso = parseFloat($('#mes_dos_total_egreso').val());
                  let mes_tres_total_egreso = parseFloat($('#mes_tres_total_egreso').val());
                  let mes_cuatro_total_egreso = parseFloat($('#mes_cuatro_total_egreso').val());
                  let mes_cinco_total_egreso = parseFloat($('#mes_cinco_total_egreso').val());
                  let mes_seis_total_egreso = parseFloat($('#mes_seis_total_egreso').val());
                  let mes_siete_total_egreso = parseFloat($('#mes_siete_total_egreso').val());
                  let mes_ocho_total_egreso = parseFloat($('#mes_ocho_total_egreso').val());
                  let mes_nueve_total_egreso = parseFloat($('#mes_nueve_total_egreso').val());
                  let mes_diez_total_egreso = parseFloat($('#mes_diez_total_egreso').val());
                  let mes_once_total_egreso = parseFloat($('#mes_once_total_egreso').val());
                  let mes_doce_total_egreso = parseFloat($('#mes_doce_total_egreso').val());
                  
                  let mes_cero_saldo_caja = mes_cero_total_ingreso - mes_cero_total_egreso;
                  let mes_uno_saldo_caja = mes_uno_total_ingreso - mes_uno_total_egreso;
                  let mes_dos_saldo_caja = mes_dos_total_ingreso - mes_dos_total_egreso;
                  let mes_tres_saldo_caja = mes_tres_total_ingreso - mes_tres_total_egreso;
                  let mes_cuatro_saldo_caja = mes_cuatro_total_ingreso - mes_cuatro_total_egreso;
                  let mes_cinco_saldo_caja = mes_cinco_total_ingreso - mes_cinco_total_egreso;
                  let mes_seis_saldo_caja = mes_seis_total_ingreso - mes_seis_total_egreso;
                  let mes_siete_saldo_caja = mes_siete_total_ingreso - mes_siete_total_egreso;
                  let mes_ocho_saldo_caja = mes_ocho_total_ingreso - mes_ocho_total_egreso;
                  let mes_nueve_saldo_caja = mes_nueve_total_ingreso - mes_nueve_total_egreso;
                  let mes_diez_saldo_caja = mes_diez_total_ingreso - mes_diez_total_egreso;
                  let mes_once_saldo_caja = mes_once_total_ingreso - mes_once_total_egreso;
                  let mes_doce_saldo_caja = mes_doce_total_ingreso - mes_doce_total_egreso;
                  
                  $('#mes_cero_saldo_caja').val(mes_cero_saldo_caja.toFixed(2));
                  $('#mes_uno_saldo_caja').val(mes_uno_saldo_caja.toFixed(2));
                  $('#mes_dos_saldo_caja').val(mes_dos_saldo_caja.toFixed(2));
                  $('#mes_tres_saldo_caja').val(mes_tres_saldo_caja.toFixed(2));
                  $('#mes_cuatro_saldo_caja').val(mes_cuatro_saldo_caja.toFixed(2));
                  $('#mes_cinco_saldo_caja').val(mes_cinco_saldo_caja.toFixed(2));
                  $('#mes_seis_saldo_caja').val(mes_seis_saldo_caja.toFixed(2));
                  $('#mes_siete_saldo_caja').val(mes_siete_saldo_caja.toFixed(2));
                  $('#mes_ocho_saldo_caja').val(mes_ocho_saldo_caja.toFixed(2));
                  $('#mes_nueve_saldo_caja').val(mes_nueve_saldo_caja.toFixed(2));
                  $('#mes_diez_saldo_caja').val(mes_diez_saldo_caja.toFixed(2));
                  $('#mes_once_saldo_caja').val(mes_once_saldo_caja.toFixed(2));
                  $('#mes_doce_saldo_caja').val(mes_doce_saldo_caja.toFixed(2));
                  
                  let saldo_inicial = parseFloat($('#saldo_inicial').val());
                  
                  let mes_cero_saldo_acumulado = mes_cero_saldo_caja + saldo_inicial;
                  $('#mes_cero_saldo_acumulado').val(mes_cero_saldo_acumulado.toFixed(2));
                  let mes_uno_saldo_acumulado = mes_uno_saldo_caja + mes_cero_saldo_acumulado ;
                  $('#mes_uno_saldo_acumulado').val(mes_uno_saldo_acumulado.toFixed(2));
                  let mes_dos_saldo_acumulado = mes_dos_saldo_caja + mes_uno_saldo_acumulado;
                  $('#mes_dos_saldo_acumulado').val(mes_dos_saldo_acumulado.toFixed(2));
                  
                  let mes_tres_saldo_acumulado = mes_tres_saldo_caja + mes_dos_saldo_acumulado;
                  $('#mes_tres_saldo_acumulado').val(mes_tres_saldo_acumulado.toFixed(2));
                  let mes_cuatro_saldo_acumulado = mes_cuatro_saldo_caja + mes_tres_saldo_acumulado;
                  $('#mes_cuatro_saldo_acumulado').val(mes_cuatro_saldo_acumulado.toFixed(2));
                  let mes_cinco_saldo_acumulado = mes_cinco_saldo_caja + mes_cuatro_saldo_acumulado;
                  $('#mes_cinco_saldo_acumulado').val(mes_cinco_saldo_acumulado.toFixed(2));
                  let mes_seis_saldo_acumulado = mes_seis_saldo_caja + mes_cinco_saldo_acumulado;
                  $('#mes_seis_saldo_acumulado').val(mes_seis_saldo_acumulado.toFixed(2));
                  let mes_siete_saldo_acumulado = mes_siete_saldo_caja + mes_seis_saldo_acumulado;
                  $('#mes_siete_saldo_acumulado').val(mes_siete_saldo_acumulado.toFixed(2));
                  let mes_ocho_saldo_acumulado = mes_ocho_saldo_caja + mes_siete_saldo_acumulado;
                  $('#mes_ocho_saldo_acumulado').val(mes_ocho_saldo_acumulado.toFixed(2));
                  let mes_nueve_saldo_acumulado = mes_nueve_saldo_caja + mes_ocho_saldo_acumulado;
                  $('#mes_nueve_saldo_acumulado').val(mes_nueve_saldo_acumulado.toFixed(2));
                  let mes_diez_saldo_acumulado = mes_diez_saldo_caja + mes_nueve_saldo_acumulado;
                  $('#mes_diez_saldo_acumulado').val(mes_diez_saldo_acumulado.toFixed(2));
                  let mes_once_saldo_acumulado = mes_once_saldo_caja + mes_diez_saldo_acumulado;
                  $('#mes_once_saldo_acumulado').val(mes_once_saldo_acumulado.toFixed(2));
                  let mes_doce_saldo_acumulado = mes_doce_saldo_caja + mes_once_saldo_acumulado;
                  $('#mes_doce_saldo_acumulado').val(mes_doce_saldo_acumulado.toFixed(2));
                  
                  if(mes_cero_saldo_caja<=0){
                      $('#mes_cero_saldo_caja').attr('style', 'background-color: #f68792 !important;border-color: #f68792 !important;');
                  }
                  if(mes_uno_saldo_caja<=0){
                      $('#mes_uno_saldo_caja').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_dos_saldo_caja<=0){
                      $('#mes_dos_saldo_caja').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_tres_saldo_caja<=0){
                      $('#mes_tres_saldo_caja').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_cuatro_saldo_caja<=0){
                      $('#mes_cuatro_saldo_caja').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_cinco_saldo_caja<=0){
                      $('#mes_cinco_saldo_caja').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_seis_saldo_caja<=0){
                      $('#mes_seis_saldo_caja').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_siete_saldo_caja<=0){
                      $('#mes_siete_saldo_caja').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_ocho_saldo_caja<=0){
                      $('#mes_ocho_saldo_caja').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_nueve_saldo_caja<=0){
                      $('#mes_nueve_saldo_caja').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_diez_saldo_caja<=0){
                      $('#mes_diez_saldo_caja').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_once_saldo_caja<=0){
                      $('#mes_once_saldo_caja').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_doce_saldo_caja<=0){
                      $('#mes_doce_saldo_caja').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  
                  if(mes_cero_saldo_acumulado<=0){
                      $('#mes_cero_saldo_acumulado').attr('style', 'background-color: #f68792 !important;border-color: #f68792 !important;');
                  }
                  if(mes_uno_saldo_acumulado<=0){
                      $('#mes_uno_saldo_acumulado').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_dos_saldo_acumulado<=0){
                      $('#mes_dos_saldo_acumulado').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_tres_saldo_acumulado<=0){
                      $('#mes_tres_saldo_acumulado').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_cuatro_saldo_acumulado<=0){
                      $('#mes_cuatro_saldo_acumulado').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_cinco_saldo_acumulado<=0){
                      $('#mes_cinco_saldo_acumulado').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_seis_saldo_acumulado<=0){
                      $('#mes_seis_saldo_acumulado').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_siete_saldo_acumulado<=0){
                      $('#mes_siete_saldo_acumulado').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_ocho_saldo_acumulado<=0){
                      $('#mes_ocho_saldo_acumulado').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_nueve_saldo_acumulado<=0){
                      $('#mes_nueve_saldo_acumulado').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_diez_saldo_acumulado<=0){
                      $('#mes_diez_saldo_acumulado').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_once_saldo_acumulado<=0){
                      $('#mes_once_saldo_acumulado').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                  if(mes_doce_saldo_acumulado<=0){
                      $('#mes_doce_saldo_acumulado').attr('style', 'background-color: #dc3545 !important;border-color: #dc3545 !important;');
                  }
                }
                
              </script>
              <tr>
                <td colspan=14>EGRESOS (S/.)</td>
              </tr>
              <tr fila="egreso">
                <td>Compras/ Costos</td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cero_compras" value="{{ encontrar_valor('evaluacion_actual_ganancia_costo_venta', $ganancia_perdida) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_uno_compras" value="{{ encontrar_valor('mes_uno_compras', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_dos_compras" value="{{ encontrar_valor('mes_dos_compras', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_tres_compras" value="{{ encontrar_valor('mes_tres_compras', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cuatro_compras" value="{{ encontrar_valor('mes_cuatro_compras', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cinco_compras" value="{{ encontrar_valor('mes_cinco_compras', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_seis_compras" value="{{ encontrar_valor('mes_seis_compras', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_siete_compras" value="{{ encontrar_valor('mes_siete_compras', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_ocho_compras" value="{{ encontrar_valor('mes_ocho_compras', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_nueve_compras" value="{{ encontrar_valor('mes_nueve_compras', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_diez_compras" value="{{ encontrar_valor('mes_diez_compras', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_once_compras" value="{{ encontrar_valor('mes_once_compras', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_doce_compras" value="{{ encontrar_valor('mes_doce_compras', $flujo_caja) }}"></td>
              </tr>
              <tr fila="egreso">
                <td>Inversiones</td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cero_inversiones" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" id="mes_uno_inversiones" disabled value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->propuesta_monto: 0 }}"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_inversiones" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_inversiones" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_inversiones" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_inversiones" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_inversiones" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_inversiones" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_inversiones" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_inversiones" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_inversiones" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_inversiones" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_inversiones" value="0.00"></td>
              </tr>
              <tr fila="egreso" tipo="gasto_admin">
                <td>Gastos de personal administrativo</td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cero_gasto_admin" value="{{ encontrar_valor('evaluacion_actual_ganancia_gasto_admin', $ganancia_perdida) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_gasto_admin" value="{{ encontrar_valor('mes_uno_gasto_admin', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_gasto_admin" value="{{ encontrar_valor('mes_dos_gasto_admin', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_gasto_admin" value="{{ encontrar_valor('mes_tres_gasto_admin', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_gasto_admin" value="{{ encontrar_valor('mes_cuatro_gasto_admin', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_gasto_admin" value="{{ encontrar_valor('mes_cinco_gasto_admin', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_gasto_admin" value="{{ encontrar_valor('mes_seis_gasto_admin', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_gasto_admin" value="{{ encontrar_valor('mes_siete_gasto_admin', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_gasto_admin" value="{{ encontrar_valor('mes_ocho_gasto_admin', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_gasto_admin" value="{{ encontrar_valor('mes_nueve_gasto_admin', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_gasto_admin" value="{{ encontrar_valor('mes_diez_gasto_admin', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_gasto_admin" value="{{ encontrar_valor('mes_once_gasto_admin', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_gasto_admin" value="{{ encontrar_valor('mes_doce_gasto_admin', $flujo_caja) }}"></td>
              </tr>
              <tr fila="egreso" tipo="gasto_ventas">
                <td>Gastos de personal de ventas</td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cero_gasto_ventas" value="{{ encontrar_valor('evaluacion_actual_ganancia_gasto_personal', $ganancia_perdida) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_gasto_ventas" value="{{ encontrar_valor('mes_uno_gasto_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_gasto_ventas" value="{{ encontrar_valor('mes_dos_gasto_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_gasto_ventas" value="{{ encontrar_valor('mes_tres_gasto_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_gasto_ventas" value="{{ encontrar_valor('mes_cuatro_gasto_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_gasto_ventas" value="{{ encontrar_valor('mes_cinco_gasto_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_gasto_ventas" value="{{ encontrar_valor('mes_seis_gasto_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_gasto_ventas" value="{{ encontrar_valor('mes_siete_gasto_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_gasto_ventas" value="{{ encontrar_valor('mes_ocho_gasto_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_gasto_ventas" value="{{ encontrar_valor('mes_nueve_gasto_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_gasto_ventas" value="{{ encontrar_valor('mes_diez_gasto_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_gasto_ventas" value="{{ encontrar_valor('mes_once_gasto_ventas', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_gasto_ventas" value="{{ encontrar_valor('mes_doce_gasto_ventas', $flujo_caja) }}"></td>
              </tr>
              <tr fila="egreso" tipo="servicios">
                <td>Servicios(agua, luz, teléfono , otros)</td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cero_servicios" value="{{ encontrar_valor('evaluacion_actual_ganancia_servicios', $ganancia_perdida) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_servicios" value="{{ encontrar_valor('mes_uno_servicios', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_servicios" value="{{ encontrar_valor('mes_dos_servicios', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_servicios" value="{{ encontrar_valor('mes_tres_servicios', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_servicios" value="{{ encontrar_valor('mes_cuatro_servicios', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_servicios" value="{{ encontrar_valor('mes_cinco_servicios', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_servicios" value="{{ encontrar_valor('mes_seis_servicios', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_servicios" value="{{ encontrar_valor('mes_siete_servicios', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_servicios" value="{{ encontrar_valor('mes_ocho_servicios', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_servicios" value="{{ encontrar_valor('mes_nueve_servicios', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_servicios" value="{{ encontrar_valor('mes_diez_servicios', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_servicios" value="{{ encontrar_valor('mes_once_servicios', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_servicios" value="{{ encontrar_valor('mes_doce_servicios', $flujo_caja) }}"></td>
              </tr>
              <tr fila="egreso" tipo="alquiler">
                <td>Alquiler de local</td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cero_alquiler" value="{{ encontrar_valor('evaluacion_actual_ganancia_alquiler', $ganancia_perdida) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_alquiler" value="{{ encontrar_valor('mes_uno_alquiler', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_alquiler" value="{{ encontrar_valor('mes_dos_alquiler', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_alquiler" value="{{ encontrar_valor('mes_tres_alquiler', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_alquiler" value="{{ encontrar_valor('mes_cuatro_alquiler', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_alquiler" value="{{ encontrar_valor('mes_cinco_alquiler', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_alquiler" value="{{ encontrar_valor('mes_seis_alquiler', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_alquiler" value="{{ encontrar_valor('mes_siete_alquiler', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_alquiler" value="{{ encontrar_valor('mes_ocho_alquiler', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_alquiler" value="{{ encontrar_valor('mes_nueve_alquiler', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_alquiler" value="{{ encontrar_valor('mes_diez_alquiler', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_alquiler" value="{{ encontrar_valor('mes_once_alquiler', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_alquiler" value="{{ encontrar_valor('mes_doce_alquiler', $flujo_caja) }}"></td>
              </tr>
              <tr fila="egreso" tipo="autovaluo">
                <td>Autoavalúo, serenazgo, parques y J.</td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cero_autovaluo" value="{{ encontrar_valor('evaluacion_actual_ganancia_autovaluo', $ganancia_perdida) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_autovaluo" value="{{ encontrar_valor('mes_uno_autovaluo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_autovaluo" value="{{ encontrar_valor('mes_dos_autovaluo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_autovaluo" value="{{ encontrar_valor('mes_tres_autovaluo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_autovaluo" value="{{ encontrar_valor('mes_cuatro_autovaluo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_autovaluo" value="{{ encontrar_valor('mes_cinco_autovaluo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_autovaluo" value="{{ encontrar_valor('mes_seis_autovaluo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_autovaluo" value="{{ encontrar_valor('mes_siete_autovaluo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_autovaluo" value="{{ encontrar_valor('mes_ocho_autovaluo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_autovaluo" value="{{ encontrar_valor('mes_nueve_autovaluo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_autovaluo" value="{{ encontrar_valor('mes_diez_autovaluo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_autovaluo" value="{{ encontrar_valor('mes_once_autovaluo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_autovaluo" value="{{ encontrar_valor('mes_doce_autovaluo', $flujo_caja) }}"></td>
              </tr>
              <tr fila="egreso" tipo="transporte">
                <td>Transporte</td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cero_transporte" value="{{ encontrar_valor('evaluacion_actual_ganancia_transporte', $ganancia_perdida) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_transporte" value="{{ encontrar_valor('mes_uno_transporte', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_transporte" value="{{ encontrar_valor('mes_dos_transporte', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_transporte" value="{{ encontrar_valor('mes_tres_transporte', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_transporte" value="{{ encontrar_valor('mes_cuatro_transporte', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_transporte" value="{{ encontrar_valor('mes_cinco_transporte', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_transporte" value="{{ encontrar_valor('mes_seis_transporte', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_transporte" value="{{ encontrar_valor('mes_siete_transporte', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_transporte" value="{{ encontrar_valor('mes_ocho_transporte', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_transporte" value="{{ encontrar_valor('mes_nueve_transporte', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_transporte" value="{{ encontrar_valor('mes_diez_transporte', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_transporte" value="{{ encontrar_valor('mes_once_transporte', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_transporte" value="{{ encontrar_valor('mes_doce_transporte', $flujo_caja) }}"></td>
              </tr>
              <tr fila="egreso">
                <td class="fw-bold"><u>Cuotas de deudas S. F. (S/.)</u></td>
                <td><input type="hidden" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cero_cuota_deuda" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_cuota_deuda" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_cuota_deuda" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_cuota_deuda" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_cuota_deuda" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_cuota_deuda" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_cuota_deuda" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_cuota_deuda" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_cuota_deuda" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_cuota_deuda" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_cuota_deuda" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_cuota_deuda" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_cuota_deuda" value="0.00"></td>
              </tr>
              <tr fila="egreso">
                <td class="fw-bold"><u>Entidades Reguladas</u></td>
                <td><input type="hidden" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cero_entidad_regulada" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_entidad_regulada" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_entidad_regulada" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_entidad_regulada" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_entidad_regulada" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_entidad_regulada" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_entidad_regulada" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_entidad_regulada" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_entidad_regulada" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_entidad_regulada" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_entidad_regulada" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_entidad_regulada" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_entidad_regulada" value="0.00"></td>
              </tr>
              @foreach($entidad_regulada as $key => $value)
                @php
                  $nombre_entidad = $value->tipo_entidad ? $tienda->nombre : $value->nombre_entidad ;
                @endphp
                <tr fila="egreso" tipo="entidad_regulada" json_individual key="{{ $key }}">
                  <td><input type="text" class="form-control" disabled nombre_entidad value="{{ $nombre_entidad }}"></td>
                  <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled mes_cero_entidad_regulada value="{{ $value->cuota }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_uno_entidad_regulada value="{{ isset($entidad_reguladas_json[$key]) ? $entidad_reguladas_json[$key]->mes_uno : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_dos_entidad_regulada value="{{ isset($entidad_reguladas_json[$key]) ? $entidad_reguladas_json[$key]->mes_dos : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_tres_entidad_regulada value="{{ isset($entidad_reguladas_json[$key]) ? $entidad_reguladas_json[$key]->mes_tres : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_cuatro_entidad_regulada value="{{ isset($entidad_reguladas_json[$key]) ? $entidad_reguladas_json[$key]->mes_cuatro : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_cinco_entidad_regulada value="{{ isset($entidad_reguladas_json[$key]) ? $entidad_reguladas_json[$key]->mes_cinco : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_seis_entidad_regulada value="{{ isset($entidad_reguladas_json[$key]) ? $entidad_reguladas_json[$key]->mes_seis : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_siete_entidad_regulada value="{{ isset($entidad_reguladas_json[$key]) ? $entidad_reguladas_json[$key]->mes_siete : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_ocho_entidad_regulada value="{{ isset($entidad_reguladas_json[$key]) ? $entidad_reguladas_json[$key]->mes_ocho : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_nueve_entidad_regulada value="{{ isset($entidad_reguladas_json[$key]) ? $entidad_reguladas_json[$key]->mes_nueve : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_diez_entidad_regulada value="{{ isset($entidad_reguladas_json[$key]) ? $entidad_reguladas_json[$key]->mes_diez : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_once_entidad_regulada value="{{ isset($entidad_reguladas_json[$key]) ? $entidad_reguladas_json[$key]->mes_once : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_doce_entidad_regulada value="{{ isset($entidad_reguladas_json[$key]) ? $entidad_reguladas_json[$key]->mes_doce : '0.00' }}"></td>
                </tr>
              @endforeach
              <tr fila="egreso">
                <td class="fw-bold"><u>Líneas de crédito(tarjetas) No Utilizadas</u></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
              </tr>
              @foreach($linea_credito as $key => $value)

                <tr fila="egreso" tipo="linea_credito" json_individual key="{{ $key }}">
                  <td><input type="text" class="form-control" disabled nombre_entidad value="{{ $value->entidad }}"></td>
                  <td><input type="text" valida_input_vacio class="form-control campo_moneda" mes_cero_linea_credito disabled value="{{ $value->cuota }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_uno_linea_credito value="{{ isset($linea_credito_json[$key]) ? $linea_credito_json[$key]->mes_uno : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_dos_linea_credito value="{{ isset($linea_credito_json[$key]) ? $linea_credito_json[$key]->mes_dos : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_tres_linea_credito value="{{ isset($linea_credito_json[$key]) ? $linea_credito_json[$key]->mes_tres : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_cuatro_linea_credito value="{{ isset($linea_credito_json[$key]) ? $linea_credito_json[$key]->mes_cuatro : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_cinco_linea_credito value="{{ isset($linea_credito_json[$key]) ? $linea_credito_json[$key]->mes_cinco : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_seis_linea_credito value="{{ isset($linea_credito_json[$key]) ? $linea_credito_json[$key]->mes_seis : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_siete_linea_credito value="{{ isset($linea_credito_json[$key]) ? $linea_credito_json[$key]->mes_siete : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_ocho_linea_credito value="{{ isset($linea_credito_json[$key]) ? $linea_credito_json[$key]->mes_ocho : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_nueve_linea_credito value="{{ isset($linea_credito_json[$key]) ? $linea_credito_json[$key]->mes_nueve : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_diez_linea_credito value="{{ isset($linea_credito_json[$key]) ? $linea_credito_json[$key]->mes_diez : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_once_linea_credito value="{{ isset($linea_credito_json[$key]) ? $linea_credito_json[$key]->mes_once : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_doce_linea_credito value="{{ isset($linea_credito_json[$key]) ? $linea_credito_json[$key]->mes_doce : '0.00' }}"></td>
                </tr>
              @endforeach
              <tr fila="egreso">
                <td class="fw-bold"><u>Entidades NO Reguladas</u></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" value="0.00"></td>
              </tr>
              @foreach($entidad_noregulada as $key => $value)
                @php
                  
                  $nombre_entidad_noregulada = $value->tipo_entidad ? $tienda->nombre : $value->nombre_entidad ;
                @endphp
                <?php 
                  
                ?>
                <tr fila="egreso" tipo="entidad_noregulada" json_individual key="{{ $key }}">
                  <td><input type="text" class="form-control" disabled nombre_entidad value="{{ $nombre_entidad_noregulada }}"></td>
                  <td><input type="text" valida_input_vacio class="form-control campo_moneda" mes_cero_entidad_noregulada disabled value="{{ $value->cuota }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_uno_entidad_noregulada value="{{ isset($entidad_noregulada_json[$key]) ? $entidad_noregulada_json[$key]->mes_uno : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_dos_entidad_noregulada value="{{ isset($entidad_noregulada_json[$key]) ? $entidad_noregulada_json[$key]->mes_dos : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_tres_entidad_noregulada value="{{ isset($entidad_noregulada_json[$key]) ? $entidad_noregulada_json[$key]->mes_tres : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_cuatro_entidad_noregulada value="{{ isset($entidad_noregulada_json[$key]) ? $entidad_noregulada_json[$key]->mes_cuatro : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_cinco_entidad_noregulada value="{{ isset($entidad_noregulada_json[$key]) ? $entidad_noregulada_json[$key]->mes_cinco : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_seis_entidad_noregulada value="{{ isset($entidad_noregulada_json[$key]) ? $entidad_noregulada_json[$key]->mes_seis : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_siete_entidad_noregulada value="{{ isset($entidad_noregulada_json[$key]) ? $entidad_noregulada_json[$key]->mes_siete : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_ocho_entidad_noregulada value="{{ isset($entidad_noregulada_json[$key]) ? $entidad_noregulada_json[$key]->mes_ocho : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_nueve_entidad_noregulada value="{{ isset($entidad_noregulada_json[$key]) ? $entidad_noregulada_json[$key]->mes_nueve : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_diez_entidad_noregulada value="{{ isset($entidad_noregulada_json[$key]) ? $entidad_noregulada_json[$key]->mes_diez : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_once_entidad_noregulada value="{{ isset($entidad_noregulada_json[$key]) ? $entidad_noregulada_json[$key]->mes_once : '0.00' }}"></td>
                  <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" mes_doce_entidad_noregulada value="{{ isset($entidad_noregulada_json[$key]) ? $entidad_noregulada_json[$key]->mes_doce : '0.00' }}"></td>
                </tr>
              @endforeach
              
              <tr fila="egreso" tipo="pago_prestamo">
                <td>Pago del préstamo SOLICITADO</td>
                <td><input type="hidden" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cero_pago_prestamo" value="0.00"></td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_uno_pago_prestamo" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_propuesta: 0 }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_pago_prestamo" value="{{ encontrar_valor('mes_dos_pago_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_pago_prestamo" value="{{ encontrar_valor('mes_tres_pago_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_pago_prestamo" value="{{ encontrar_valor('mes_cuatro_pago_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_pago_prestamo" value="{{ encontrar_valor('mes_cinco_pago_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_pago_prestamo" value="{{ encontrar_valor('mes_seis_pago_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_pago_prestamo" value="{{ encontrar_valor('mes_siete_pago_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_pago_prestamo" value="{{ encontrar_valor('mes_ocho_pago_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_pago_prestamo" value="{{ encontrar_valor('mes_nueve_pago_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_pago_prestamo" value="{{ encontrar_valor('mes_diez_pago_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_pago_prestamo" value="{{ encontrar_valor('mes_once_pago_prestamo', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_pago_prestamo" value="{{ encontrar_valor('mes_doce_pago_prestamo', $flujo_caja) }}"></td>
              </tr>
              <tr>
                <td colspan=14>&nbsp;</td>
              </tr>
              <tr fila="egreso" tipo="sunat">
                <td>SUNAT</td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cero_sunat" value="{{ encontrar_valor('evaluacion_actual_ganancia_sunat', $ganancia_perdida) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_sunat" value="{{ encontrar_valor('mes_uno_sunat', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_sunat" value="{{ encontrar_valor('mes_dos_sunat', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_sunat" value="{{ encontrar_valor('mes_tres_sunat', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_sunat" value="{{ encontrar_valor('mes_cuatro_sunat', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_sunat" value="{{ encontrar_valor('mes_cinco_sunat', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_sunat" value="{{ encontrar_valor('mes_seis_sunat', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_sunat" value="{{ encontrar_valor('mes_siete_sunat', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_sunat" value="{{ encontrar_valor('mes_ocho_sunat', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_sunat" value="{{ encontrar_valor('mes_nueve_sunat', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_sunat" value="{{ encontrar_valor('mes_diez_sunat', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_sunat" value="{{ encontrar_valor('mes_once_sunat', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_sunat" value="{{ encontrar_valor('mes_doce_sunat', $flujo_caja) }}"></td>
              </tr>
              <tr fila="egreso" tipo="otros_gastos" id="otros_gastos">
                <td>Otros Gastos</td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cero_otros_gastos" value="{{ encontrar_valor('evaluacion_actual_ganancia_otros_gastos', $ganancia_perdida) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_otros_gastos" value="{{ encontrar_valor('mes_uno_otros_gastos', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_otros_gastos" value="{{ encontrar_valor('mes_dos_otros_gastos', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_otros_gastos" value="{{ encontrar_valor('mes_tres_otros_gastos', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_otros_gastos" value="{{ encontrar_valor('mes_cuatro_otros_gastos', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_otros_gastos" value="{{ encontrar_valor('mes_cinco_otros_gastos', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_otros_gastos" value="{{ encontrar_valor('mes_seis_otros_gastos', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_otros_gastos" value="{{ encontrar_valor('mes_siete_otros_gastos', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_otros_gastos" value="{{ encontrar_valor('mes_ocho_otros_gastos', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_otros_gastos" value="{{ encontrar_valor('mes_nueve_otros_gastos', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_otros_gastos" value="{{ encontrar_valor('mes_diez_otros_gastos', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_otros_gastos" value="{{ encontrar_valor('mes_once_otros_gastos', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_otros_gastos" value="{{ encontrar_valor('mes_doce_otros_gastos', $flujo_caja) }}"></td>
              </tr>
              <tr fila="egreso" tipo="canasta_familiar">
                <td>Canasta Familiar y Otros</td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" disabled id="mes_cero_canasta_familiar" value="{{ encontrar_valor('evaluacion_actual_ganancia_gasto_familiar', $ganancia_perdida) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_uno_canasta_familiar" value="{{ encontrar_valor('mes_uno_canasta_familiar', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_dos_canasta_familiar" value="{{ encontrar_valor('mes_dos_canasta_familiar', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_tres_canasta_familiar" value="{{ encontrar_valor('mes_tres_canasta_familiar', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cuatro_canasta_familiar" value="{{ encontrar_valor('mes_cuatro_canasta_familiar', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_cinco_canasta_familiar" value="{{ encontrar_valor('mes_cinco_canasta_familiar', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_seis_canasta_familiar" value="{{ encontrar_valor('mes_seis_canasta_familiar', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_siete_canasta_familiar" value="{{ encontrar_valor('mes_siete_canasta_familiar', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_ocho_canasta_familiar" value="{{ encontrar_valor('mes_ocho_canasta_familiar', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_nueve_canasta_familiar" value="{{ encontrar_valor('mes_nueve_canasta_familiar', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_diez_canasta_familiar" value="{{ encontrar_valor('mes_diez_canasta_familiar', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_once_canasta_familiar" value="{{ encontrar_valor('mes_once_canasta_familiar', $flujo_caja) }}"></td>
                <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" id="mes_doce_canasta_familiar" value="{{ encontrar_valor('mes_doce_canasta_familiar', $flujo_caja) }}"></td>
              </tr>
              <tr>
                <td colspan=14>&nbsp;</td>
              </tr>
              <tr>
                <td class="fw-bold"><u>Total Egresos (B) (S/.)</u></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_cero_total_egreso" value="{{ encontrar_valor('mes_cero_total_egreso', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_uno_total_egreso" value="{{ encontrar_valor('mes_uno_total_egreso', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_dos_total_egreso" value="{{ encontrar_valor('mes_dos_total_egreso', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_tres_total_egreso" value="{{ encontrar_valor('mes_tres_total_egreso', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_cuatro_total_egreso" value="{{ encontrar_valor('mes_cuatro_total_egreso', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_cinco_total_egreso" value="{{ encontrar_valor('mes_cinco_total_egreso', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_seis_total_egreso" value="{{ encontrar_valor('mes_seis_total_egreso', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_siete_total_egreso" value="{{ encontrar_valor('mes_siete_total_egreso', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_ocho_total_egreso" value="{{ encontrar_valor('mes_ocho_total_egreso', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_nueve_total_egreso" value="{{ encontrar_valor('mes_nueve_total_egreso', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_diez_total_egreso" value="{{ encontrar_valor('mes_diez_total_egreso', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_once_total_egreso" value="{{ encontrar_valor('mes_once_total_egreso', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_doce_total_egreso" value="{{ encontrar_valor('mes_doce_total_egreso', $flujo_caja) }}"></td>
              </tr>
              <tr>
                <td colspan=14>&nbsp;</td>
              </tr>
              <tr saldo_caja_mensual>
                <td class="fw-bold"><u>SALDO CAJA MENSUAL (A-B) (S/.)</u></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_cero_saldo_caja" value="{{ encontrar_valor('mes_cero_saldo_caja', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_uno_saldo_caja" value="{{ encontrar_valor('mes_uno_saldo_caja', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_dos_saldo_caja" value="{{ encontrar_valor('mes_dos_saldo_caja', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_tres_saldo_caja" value="{{ encontrar_valor('mes_tres_saldo_caja', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_cuatro_saldo_caja" value="{{ encontrar_valor('mes_cuatro_saldo_caja', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_cinco_saldo_caja" value="{{ encontrar_valor('mes_cinco_saldo_caja', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_seis_saldo_caja" value="{{ encontrar_valor('mes_seis_saldo_caja', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_siete_saldo_caja" value="{{ encontrar_valor('mes_siete_saldo_caja', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_ocho_saldo_caja" value="{{ encontrar_valor('mes_ocho_saldo_caja', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_nueve_saldo_caja" value="{{ encontrar_valor('mes_nueve_saldo_caja', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_diez_saldo_caja" value="{{ encontrar_valor('mes_diez_saldo_caja', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_once_saldo_caja" value="{{ encontrar_valor('mes_once_saldo_caja', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_doce_saldo_caja" value="{{ encontrar_valor('mes_doce_saldo_caja', $flujo_caja) }}"></td>
              </tr>
              <tr saldo_acumulado_mensual>
                <td class="fw-bold"><u>SALDO ACUMULADO MENSUAL (S/.)</u></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_cero_saldo_acumulado" value="{{ encontrar_valor('mes_cero_saldo_acumulado', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_uno_saldo_acumulado" value="{{ encontrar_valor('mes_uno_saldo_acumulado', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_dos_saldo_acumulado" value="{{ encontrar_valor('mes_dos_saldo_acumulado', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_tres_saldo_acumulado" value="{{ encontrar_valor('mes_tres_saldo_acumulado', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_cuatro_saldo_acumulado" value="{{ encontrar_valor('mes_cuatro_saldo_acumulado', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_cinco_saldo_acumulado" value="{{ encontrar_valor('mes_cinco_saldo_acumulado', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_seis_saldo_acumulado" value="{{ encontrar_valor('mes_seis_saldo_acumulado', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_siete_saldo_acumulado" value="{{ encontrar_valor('mes_siete_saldo_acumulado', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_ocho_saldo_acumulado" value="{{ encontrar_valor('mes_ocho_saldo_acumulado', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_nueve_saldo_acumulado" value="{{ encontrar_valor('mes_nueve_saldo_acumulado', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_diez_saldo_acumulado" value="{{ encontrar_valor('mes_diez_saldo_acumulado', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_once_saldo_acumulado" value="{{ encontrar_valor('mes_once_saldo_acumulado', $flujo_caja) }}"></td>
                <td><input type="text" class="form-control campo_moneda" disabled id="mes_doce_saldo_acumulado" value="{{ encontrar_valor('mes_doce_saldo_acumulado', $flujo_caja) }}"></td>
              </tr>
              <tr>
                <td>Costo de Venta</td>
                <td><input type="text" valida_input_vacio class="form-control campo_moneda" style="background-color:#ccc;" id="porcentaje_costo_venta" value="{{ $credito_evaluacion_cuantitativa ? (100 - $credito_evaluacion_cuantitativa->margen_venta_calculado) : 0 }} " disabled></td>
              </tr>
            </tbody>
          </table>
        </div>  
      </div>
      <div class="row">
        <div class="col-sm-12">
        <table class="table table-bordered" id="table-comentario">
            <thead>
              <tr>
                <th>Supuestos</th>
                @if($view_detalle!='false')
                <th width="10px"><button type="button" class="btn btn-success" onclick="agregar_comentario()"><i class="fa fa-plus"></i></button></th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach($comentarios as $value)
                <tr>
                  <td><input type="text" descripcion {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" value="{{ $value->descripcion }}"></td>
                  
                @if($view_detalle!='false')
                <td><button type="button" onclick="eliminar_comentario(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                @endif
                 
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <script>
         function agregar_comentario(){
          let btn_eliminar = `<button type="button" onclick="eliminar_comentario(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button>` ;
          let tabla = `<tr>
                        <td><input type="text" descripcion class="form-control color_cajatexto"></td>
                        <td>${btn_eliminar}</td>
                      </tr>`;

          $(`#table-comentario > tbody`).append(tabla);
          
        }
        function eliminar_comentario(e){
          let path = $(e).closest('tr');
          path.remove();
        }
        function json_comentarios(){
          let data = [];
          $(`#table-comentario > tbody > tr`).each(function() {
              let descripcion        = $(this).find('td input[descripcion]').val();
              data.push({ 
                  descripcion: descripcion,
              });
          });
          return JSON.stringify(data);
        }
     </script>
      
      

      <div class="row mt-1">
        
        
        @if($view_detalle!='false')
        <div class="col" style="flex: 0 0 0%;">
          <button type="submit" class="btn btn-success" id="btn-save-cuantitativa"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
        </div>
        @endif
        <div class="col" style="flex: 0 0 0%;">
          <button type="button" 
                  class="btn btn-dark" 
                  onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=solicitud_flujocaja')}}', size: 'modal-fullscreen' })"
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
  tr[saldo_caja_mensual] > td > input{
    background-color:#bcd983 !important;
    border-color: #bcd983 !important;
  }
  tr[saldo_acumulado_mensual] > td > input{
    background-color:#bcd983 !important;
    border-color: #bcd983 !important;
  }
</style>
<script>
  valida_input_vacio();
  $('input').on('blur', function() {
      $('#boton_imprimir').attr('disabled',true);
  });
  generarTabla(new Date("{{ date_format(date_create($credito->fecha),'Y-m-d') }}") );
  function generarTabla(fechaInicio) {
    let tabla = $('#table-flujo-caja');
    let filaEncabezado = tabla.find('thead tr:first');
    let primer_mes = '';
    for (let i = 0; i < 12; i++) {
      
      let fechaActual = new Date(fechaInicio);
      fechaActual.setMonth(fechaInicio.getMonth() + i);
      let mes = fechaActual.toLocaleString('default', { month: 'short' }).toUpperCase();
      let mesNumero = fechaActual.getMonth() + 1;
      filaEncabezado.append('<th class="text-center">' + i + '</th>');
      filaEncabezado.next().append('<th class="text-center">' + mes + '</th>');
      if(i == 0){
         primer_mes = mes;
      }
    }
    filaEncabezado.append('<th class="text-center">12</th>');
    filaEncabezado.next().append('<th class="text-center">' + primer_mes + '</th>');
    
  }
  function json_evaluacion_encabezado(){
    let jsonData = [];
    $("#table-flujo-caja > thead > tr").each(function() {
        
        let thData = [];
        $(this).find("th").each(function() {
            let th = $(this).text()
            thData.push({ 
                th: th,
            });
        });
      jsonData.push({ 
          encabezado: thData,
      });
    });
    return JSON.stringify(jsonData);
  }
  function json_evaluacion_meses(){
    let jsonData = [];
    $("#table-flujo-caja > tbody > tr[encabezado]").each(function() {
        let rowData = {};
        rowData["Mes"] = $(this).find("td:first").text();
        $(this).find("td input").each(function(index) {
            let header = $("#table-flujo-caja thead th:eq(" + index + ")").text();
            rowData[header] = {
                value: $(this).val(),
                disabled: $(this).prop("disabled")
            };
        });
        jsonData.push(rowData);
    });
    return JSON.stringify(jsonData);
  }
  
  
  $('#table-flujo-caja input[onkeyup*="calcula_monto_meses(this)"]').each(function() {
    if ($(this).closest('td').index() !== 0) {
    }
  });
  function calcula_monto_meses(e){
    //let valorBase = parseFloat($("#table-flujo-caja tbody tr:eq(0) td:eq(1) input").val());
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
        $(`#table-flujo-caja tbody tr:eq(0) td:eq(${cellIndex}) input`).val(parseFloat(monto_venta).toFixed(2));
    }
  }
  function json_flujo_caja(){
    let jsonData = [];
    $("#table-flujo-caja > tbody > tr:not([json_individual]) > td > input:not([type='hidden']").each(function () {
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
  function json_individual(tipo){
    let jsonData = [];
    $("#table-flujo-caja > tbody > tr[tipo='"+tipo+"']").each(function () {
        
      
      let nombre_entidad = $(this).find('input[nombre_entidad]').val();
      let mes_cero = $(this).find('input[mes_cero_'+tipo+']').val();
      let mes_uno = $(this).find('input[mes_uno_'+tipo+']').val();
      let mes_dos = $(this).find('input[mes_dos_'+tipo+']').val();
      let mes_tres = $(this).find('input[mes_tres_'+tipo+']').val();
      let mes_cuatro = $(this).find('input[mes_cuatro_'+tipo+']').val();
      let mes_cinco = $(this).find('input[mes_cinco_'+tipo+']').val();
      let mes_seis = $(this).find('input[mes_seis_'+tipo+']').val();
      let mes_siete = $(this).find('input[mes_siete_'+tipo+']').val();
      let mes_ocho = $(this).find('input[mes_ocho_'+tipo+']').val();
      let mes_nueve = $(this).find('input[mes_nueve_'+tipo+']').val();
      let mes_diez = $(this).find('input[mes_diez_'+tipo+']').val();
      let mes_once = $(this).find('input[mes_once_'+tipo+']').val();
      let mes_doce = $(this).find('input[mes_doce_'+tipo+']').val();
      jsonData.push({ 
          nombre_entidad: nombre_entidad,
          mes_cero: mes_cero,
          mes_uno: mes_uno,
          mes_dos: mes_dos,
          mes_tres: mes_tres,
          mes_cuatro: mes_cuatro,
          mes_cinco: mes_cinco,
          mes_seis: mes_seis,
          mes_siete: mes_siete,
          mes_ocho: mes_ocho,
          mes_nueve: mes_nueve,
          mes_diez: mes_diez,
          mes_once: mes_once,
          mes_doce: mes_doce,
      });

    });
    return JSON.stringify(jsonData);
  }
</script>    