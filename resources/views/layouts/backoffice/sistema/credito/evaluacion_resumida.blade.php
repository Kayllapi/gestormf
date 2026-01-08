<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/credito/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'evaluacion_resumida',
              referencia: json_referencia(),
              dias: json_dias(),
              semanas: json_semanas(),
              ingresos_gastos: json_ingresos_gastos(),
              estado_credito_general: $('#estado_credito_general').text()
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
    
  
    <div class="modal-header" style="border-bottom: 0;">
        <h5 class="modal-title">EVALUACION RESUMIDA </h5>
        <button type="button" class="btn-close text-white" id="modal-close-garantia-cliente" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    @php
      $referencia_cliente = $credito_evaluacion_resumida ? ( is_null($credito_evaluacion_resumida->referencia) ? [] : json_decode($credito_evaluacion_resumida->referencia) ) : [];
      $dias = $credito_evaluacion_resumida ? ( $credito_evaluacion_resumida->venta_diaria == "" ? [] : json_decode($credito_evaluacion_resumida->venta_diaria) ) : [];
      $semanas = $credito_evaluacion_resumida ? ( $credito_evaluacion_resumida->venta_semanal == "" ? [] : json_decode($credito_evaluacion_resumida->venta_semanal) ) : [];
  
      $ingresos_gastos = $credito_evaluacion_resumida ? ( $credito_evaluacion_resumida->ingresos_gastos == "" ? [] : json_decode($credito_evaluacion_resumida->ingresos_gastos) ) : [];
    @endphp
    <div class="modal-body modal-body-cualitativa">
      <div class="row">
        <div class="col-sm-12 col-md-6">
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
            <label class="col-sm-4 col-form-label" style="text-align: right;">TIPO GIRO ECONÓMICO:</label>
            <div class="col-sm-8">
              <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" id="idtipo_giro_economico">
               <option value=""></option>
                @foreach($tipo_giro_economico as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
              </select>
              
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">GIRO ECONÓMICO:</label>
            <div class="col-sm-8">
              <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" id="idgiro_economico_evaluacion" disabled>
               <option value=""></option>
              </select>
              
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">DESCRIPCIÓN DE ACTIVIDAD:</label>
            <div class="col-sm-8">
              <input type="text" step="any" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" id="descripcion_actividad" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->descripcion_actividad : '' }}">
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-3">
          <div class="row">
            <label class="col-sm-3 col-form-label" style="text-align: right;">FECHA:</label>
            <div class="col-sm-7">
              <input type="date" step="any" class="form-control" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->fecha : date('Y-m-d') }}" disabled>
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
        <div class="col-sm-12 col-md-3">
          <div class="row">
            <label class="col-sm-5 col-form-label" style="text-align: right;">NRO SOLICITUD:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="S{{ str_pad($credito->id, 8, '0', STR_PAD_LEFT)  }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-5 col-form-label" style="text-align: right;">PRODUCTO:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->nombreproductocredito }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-5 col-form-label" style="text-align: right;">TIPO DE CAMBIO:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ configuracion($tienda->id,'tipo_cambio_dolar')['valor'] }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-5 col-form-label" style="text-align: right;">TIPO DE CLIENTE:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->tipo_operacion_credito_nombre }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-5 col-form-label" style="text-align: right;">MODALIDAD:</label>
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
        <span class="badge d-block">II. EVALUACION CUALITATIVA</span>
      </div>
      
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">2.1 N° DE ENTIDADES FINANCIERAS (Se considera deuda interna y Líneas de creditos sin uso)</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-4">
          <table class="table table-bordered" id="tabla-entidadesfinancieras">
            <thead>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;">Deudores</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;">Como</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;text-align: center;" width="100px">N°</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="background-color: #efefef !important;" rowspan="2">CLIENTE</td>
                <td style="background-color: #efefef !important;">P.Natural</td>
                <td style="background-color: #efefef !important;">
                  <input type="text" valida_input_vacio style="padding: 4px;" onkeyup="total_deudas()" 
                         onkeydown="total_deudas()" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->cantidad_cliente_natural : '0.00' }}" id="cantidad_cliente_natural"></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">P.Jurídica</td>
                <td style="background-color: #efefef !important;"><input type="text" valida_input_vacio style="padding: 4px;" onkeyup="total_deudas()" 
                                                                         onkeydown="total_deudas()" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" 
                                                                         value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->cantidad_cliente_juridico : '0.00' }}" id="cantidad_cliente_juridico"></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;" rowspan="2">PAREJA</td>
                <td style="background-color: #efefef !important;">P.Natural</td>
                <td style="background-color: #efefef !important;"><input type="text" valida_input_vacio style="padding: 4px;" onkeyup="total_deudas()" onkeydown="total_deudas()" 
                                                                         {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->cantidad_pareja_natural : '0.00' }}" id="cantidad_pareja_natural"></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">P.Jurídica</td>
                <td style="background-color: #efefef !important;"><input type="text" valida_input_vacio style="padding: 4px;" onkeyup="total_deudas()" onkeydown="total_deudas()" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" 
                                                                         value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->cantidad_pareja_juridico : '0.00' }}" id="cantidad_pareja_juridico"></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;text-align: right;" colspan=2>TOTAL</td>
                <td style="background-color: #c8c8c8 !important;"><input type="text" style="padding: 4px;" disabled class="form-control campo_moneda" 
                                                                         value="{{ $credito_evaluacion_resumida ? number_format($credito_evaluacion_resumida->total_deuda, 2, '.', '') : '0.00' }}" id="total_deuda"></td>
              </tr>
            </tbody>
          </table>
        </div>
        @if($users_prestamo_aval!='')
        <div class="col-md-5">
              <div class="row">
                <div class="col-md-7">
                  <div class="row">
                    <label class="col-sm-3 col-form-label" style="text-align: right;">AVAL:</label>
                    <div class="col-sm-9">
                        <input type="text" step="any" class="form-control" value="{{ $credito->nombreavalcredito }}" disabled>
                    </div>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="row">
                    <label class="col-sm-3 col-form-label" style="text-align: right;">DNI:</label>
                    <div class="col-sm-9">
                        <input type="text" step="any" class="form-control" value="{{ $credito->documentoaval }}" disabled>
                    </div>
                  </div>
                </div>
              </div>
              @if($users_prestamo_aval->dni_pareja!='' or $users_prestamo_aval->nombrecompleto_pareja!='')
              <div class="row">
                <div class="col-md-7">
                  <div class="row">
                    <label class="col-sm-3 col-form-label" style="text-align: right;">PAREJA:</label>
                    <div class="col-sm-9">
                      <input type="text" step="any" class="form-control" value="{{ $users_prestamo_aval->nombrecompleto_pareja }}" disabled>
                    </div>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="row">
                    <label class="col-sm-3 col-form-label" style="text-align: right;">DNI:</label>
                    <div class="col-sm-9">
                      <input type="text" step="any" class="form-control" value="{{ $users_prestamo_aval->dni_pareja }}" disabled>
                    </div>
                  </div>
                </div>
              </div>
              @endif
        </div>
        @endif
        <div class="col-sm-12 col-md-3">
          <table class="table table-bordered" id="tabla-entidadesfinancieras">
            <thead>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;">Codeudores</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;">Como</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;text-align: center;" width="100px">N°</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="background-color: #efefef !important;" rowspan="2">Garante (Aval)/Fiador</td>
                <td style="background-color: #efefef !important;">P.Natural</td>
                <td style="background-color: #efefef !important;">
                  <input type="text" valida_input_vacio style="padding: 4px;" onkeyup="total_deudas_garante()" 
                         onkeydown="total_deudas_garante()" class="form-control campo_moneda " 
                         value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cantidad_garante_natural : '0.00' }}" 
                         id="cantidad_garante_natural" disabled></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">P.Jurídica</td>
                <td style="background-color: #efefef !important;">
                  <input type="text" valida_input_vacio style="padding: 4px;" onkeyup="total_deudas_garante()" 
                         onkeydown="total_deudas_garante()" class="form-control campo_moneda" 
                         value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cantidad_garante_juridico : '0.00' }}" 
                         id="cantidad_garante_juridico" disabled></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;" rowspan="2">Pareja de Garante/ fiador</td>
                <td style="background-color: #efefef !important;">P.Natural</td>
                <td style="background-color: #efefef !important;">
                  <input type="text" valida_input_vacio style="padding: 4px;" 
                         onkeyup="total_deudas_garante()" onkeydown="total_deudas_garante()" 
                         class="form-control campo_moneda" 
                         value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cantidad_pareja_natural : '0.00' }}" 
                         id="cantidad_garante_pareja_natural" disabled></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">P.Jurídica</td>
                <td style="background-color: #efefef !important;">
                  <input type="text" valida_input_vacio style="padding: 4px;" 
                         onkeyup="total_deudas_garante()" onkeydown="total_deudas_garante()" class="form-control campo_moneda" 
                         value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cantidad_pareja_juridico : '0.00' }}" 
                         id="cantidad_garante_pareja_juridico" disabled></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;text-align: right;" colspan=2>TOTAL</td>
                <td style="background-color: #c8c8c8 !important;">
                  <input type="text" style="padding: 4px;" disabled 
                         class="form-control campo_moneda" 
                         value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->total_deuda : '0.00' }}" id="total_deuda_garante"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">2.2 GESTIÓN DEL GIRO ECONÓMICO</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-6">
          <table>
            <tr>
              <td width="300px">a) Experiencia como Microempresario(a) (meses)</td>
              <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->experiencia_microempresa : 0 }}" id="experiencia_microempresa" style="width:100px;"></td>
            </tr>
            <tr>
              <td>b) Tiempo en el mismo local (meses)</td>
              <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->tiempo_mismo_local : 0 }}" id="tiempo_mismo_local" style="width:100px;"></td>
            </tr>
            <tr>
              <td>c) Instalaciones o local</td>
              <td>
                @if($users_prestamo->db_idlocalnegocio_ac_economica!='')
                  
                  <input type="text" class="form-control campo_moneda" value="{{ $users_prestamo->db_idlocalnegocio_ac_economica }}" disabled id="instalacion_local" style="width:100px;">
                @endif
              </td>
            </tr>
            <tr>
              <td>d) N° de trabajadores a tiempo completo</td>
              <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->nro_trabajador_completo : 0 }}" id="nro_trabajador_completo" style="width:100px;"></td>
            </tr>
            <tr>
              <td>e) N° de trabajdores a tiempo parcial</td>
              <td><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->nro_trabajador_parcal : 0 }}" id="nro_trabajador_parcal" style="width:100px;"></td>
            </tr>
            
          </table>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">2.3 REFERENCIAS</span>
      </div>
      <div class="row">
        
        <div class="col-sm-12">
              
              
              <div class="mb-1">
                  <table class="table table-bordered" id="tabla-referencia">
                      <thead>
                          <th style="background-color: #c8c8c8 !important;color: #000 !important;">Fuente</th>
                          <th style="background-color: #c8c8c8 !important;color: #000 !important;">Apellidos y Nombres</th>
                          <th style="background-color: #c8c8c8 !important;color: #000 !important;">Vinculo: Familiar/Personas/Otros</th>
                          <th style="background-color: #c8c8c8 !important;color: #000 !important;">Telf./Celular</th>
                     
                        @if($view_detalle!='false')
                        <th width="10px" style="background-color: #c8c8c8 !important;color: #000 !important;">
                            <a href="javascript:;" class="btn btn-success" onclick="agregar_referencia()">
                              <i class="fa-solid fa-plus"></i>
                            </a>
                          </th>
                        @endif
                          
                      </thead>
                      <tbody num="0">
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block">III. INGRESOS Y GASTO FAMILIAR RESUMIDO (Mensual)</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-4">
        
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
                <th colspan="2" style="background-color: #c8c8c8 !important;color: #000 !important;">Venta Semanal</th>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;"><input type="text" id="venta_total_dias" step="any" class="form-control campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->venta_total_dias : '0.00' }}" disabled></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-sm-12 col-md-4">
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
                <th style="background-color: #c8c8c8 !important;color: #000 !important;">Venta Mensual</th>
                <td style="background-color: #c8c8c8 !important;color: #000 !important;"><input type="text" id="venta_total_mensual" step="any" class="form-control campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->venta_total_mensual : '0.00' }}" disabled></td>
              </tr>
            </tbody>
          </table>
          
          <div class="row mt-3 d-none">
            <label for="inputEmail3" class="col-sm-9 col-form-label">Margen de venta Máxima</label>
            <div class="col-sm-3">
              <input type="text" disabled class="form-control" id="margen_tipo_giro" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->margen_venta : '0.00' }}">
            </div>
          </div>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-sm-12 col-md-6">
          <table class="table table-bordered" id="table-ingresos-gastos">
            <thead>
              <tr>
                <th>Ingresos y gastos operativos del negocio</th>
                <th width="100px">Monto</th>
                <th width="100px">Ampliación/Compra deuda</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="background-color: #c8c8c8 !important;"><b>+Ingreso por ventas</b></td>
                <td style="background-color: #c8c8c8 !important;"><input type="text" class="form-control" valida_input_vacio id="ingresos_op_ventas" value="{{ encontrar_valor('ingresos_op_ventas', $ingresos_gastos) }}" disabled></td>
                <td></td>
              </tr>
              <tr>
                <td>-Costo de venta(C. de producción)</td>
                <td><input type="text" class="form-control" valida_input_vacio id="ingresos_op_costo_produccion" value="{{ encontrar_valor('ingresos_op_costo_produccion', $ingresos_gastos) }}" disabled></td>
                <td></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;"><b>Utilidad Bruta</b></td>
                <td style="background-color: #c8c8c8 !important;"><input type="text" class="form-control" valida_input_vacio id="ingresos_op_utilidad_bruta" value="{{ encontrar_valor('ingresos_op_utilidad_bruta', $ingresos_gastos) }}" disabled></td>
                <td></td>
              </tr>
              <tr>
                <td>-Gasto de Personal</td>
                <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" valida_input_vacio id="ingresos_op_gasto_personal" value="{{ encontrar_valor('ingresos_op_gasto_personal', $ingresos_gastos) }}"></td>
                <td></td>
              </tr>
              <tr>
                <td>-Servicio de luz</td>
                <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" valida_input_vacio id="ingresos_op_luz" value="{{ encontrar_valor('ingresos_op_luz', $ingresos_gastos) }}"></td>
                <td></td>
              </tr>
              <tr>
                <td>-Servico de agua</td>
                <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" valida_input_vacio id="ingresos_op_agua" value="{{ encontrar_valor('ingresos_op_agua', $ingresos_gastos) }}"></td>
                <td></td>
              </tr>
              <tr>
                <td>-Teléfono/internet</td>
                <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" valida_input_vacio id="ingresos_op_telefono" value="{{ encontrar_valor('ingresos_op_telefono', $ingresos_gastos) }}"></td>
                <td></td>
              </tr>
              <tr>
                <td>-Cable</td>
                <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" valida_input_vacio id="ingresos_op_cable" value="{{ encontrar_valor('ingresos_op_cable', $ingresos_gastos) }}"></td>
                <td></td>
              </tr>
              <tr>
                <td>-Alquiler de local</td>
                <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" valida_input_vacio id="ingresos_op_alquiler" value="{{ encontrar_valor('ingresos_op_alquiler', $ingresos_gastos) }}"></td>
                <td></td>
              </tr>
              <tr>
                <td>-Autoavalúo, serenazgo, parques y J.</td>
                <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" valida_input_vacio id="ingresos_op_autovaluo" value="{{ encontrar_valor('ingresos_op_autovaluo', $ingresos_gastos) }}"></td>
                <td></td>
              </tr>
              <tr>
                <td>-Transporte</td>
                <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" valida_input_vacio id="ingresos_op_transporte" value="{{ encontrar_valor('ingresos_op_transporte', $ingresos_gastos) }}"></td>
                <td></td>
              </tr>
              <tr>
                <td>-Cuota de préstamo E. Reguladas</td>
                <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" valida_input_vacio id="ingresos_op_cuota_prestamo_regulada" 
                           value="{{ encontrar_valor('ingresos_op_cuota_prestamo_regulada', $ingresos_gastos) }}"></td>
                <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" valida_input_vacio id="ingresos_op_ampliacion_regulada" 
                           value="{{ encontrar_valor('ingresos_op_ampliacion_regulada', $ingresos_gastos) }}"></td>
              </tr>
              <tr>
                <td>-Cuota de préstamo E. No Reguladas (Incl. Deuda interna)</td>
                <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" valida_input_vacio id="ingresos_op_cuota_prestamo_noregulada" value="{{ encontrar_valor('ingresos_op_cuota_prestamo_noregulada', $ingresos_gastos) }}"></td>
                <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" valida_input_vacio id="ingresos_op_ampliacion_noregulada" 
                           value="{{ encontrar_valor('ingresos_op_ampliacion_noregulada', $ingresos_gastos) }}"></td>
              </tr>
              <tr>
                <td>-Sunat</td>
                <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" valida_input_vacio id="ingresos_op_sunat" value="{{ encontrar_valor('ingresos_op_sunat', $ingresos_gastos) }}"></td>
                <td></td>
              </tr>
              <tr>
                <td>-Otros gastos</td>
                <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" valida_input_vacio id="ingresos_op_otros_gastos" value="{{ encontrar_valor('ingresos_op_otros_gastos', $ingresos_gastos) }}"></td>
                <td></td>
              </tr>
              <tr>
                <td>+Otros Negocios</td>
                <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" valida_input_vacio id="ingresos_op_otros_negocios" value="{{ encontrar_valor('ingresos_op_otros_negocios', $ingresos_gastos) }}"></td>
                <td></td>
              </tr>
              <tr>
                <td>+Ingreso Fijo (Sueldo, Pensión Seguro y otras)</td>
                <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" valida_input_vacio id="ingresos_op_ingreso_fijo" value="{{ encontrar_valor('ingresos_op_ingreso_fijo', $ingresos_gastos) }}"></td>
                <td></td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td style="background-color: #c8c8c8 !important;"><b> Total Ingreso (S/.)</b></td>
                <td style="background-color: #c8c8c8 !important;"><input type="text" class="form-control" valida_input_vacio id="ingresos_op_total" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->ingresos_op_total : '0.00' }}" disabled></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
          <div class="alert bg-danger text-white mt-2 fw-bold" id="resultado_modalidaddecredito" style="display:none">Registrar datos de Ampliación/Compra deuda!!</div>
        </div>
        <div class="col-sm-12 col-md-6">
          
          <table class="table table-bordered" id="table-gastos-familiares">
            <thead>
              <tr>
                <th>Gastos Familiares</th>
                <th  width="110px">Monto</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="background-color: #efefef !important;">Alimentación</td>
                <td style="background-color: #efefef !important;"><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_alimentacion : 0 }}" suma id="gasto_alimentacion"></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Educación</td>
                <td style="background-color: #efefef !important;"><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_educacion : 0 }}" suma id="gasto_educacion"></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Vestimenta</td>
                <td style="background-color: #efefef !important;"><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_vestimenta : 0 }}" suma id="gasto_vestimenta"></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Transporte</td>
                <td style="background-color: #efefef !important;"><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_transporte : 0 }}" suma id="gasto_transporte"></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Salud</td>
                <td style="background-color: #efefef !important;"><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_salud : 0 }}" suma id="gasto_salud"></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Alquiler de vivienda</td>
                <td style="background-color: #efefef !important;"><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_vivienda : 0 }}" suma id="gasto_vivienda"></td>
              </tr>
              <tr>
                <th style="background-color: #c8c8c8 !important;">Servicios</th>
                <td style="background-color: #c8c8c8 !important;"><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->total_servicios : 0 }}" suma id="total_servicios" disabled></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Agua</td>
                <td style="background-color: #efefef !important;"><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcular_servicios()" onkeydown="calcular_servicios()" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_agua : 0 }}" id="gasto_agua"></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Luz</td>
                <td style="background-color: #efefef !important;"><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcular_servicios()" onkeydown="calcular_servicios()" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_luz : 0 }}" id="gasto_luz"></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Teléfono fijo e internet</td>
                <td style="background-color: #efefef !important;"><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcular_servicios()" onkeydown="calcular_servicios()" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_telefono_internet : 0 }}" id="gasto_telefono_internet"></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">T. Celular</td>
                <td style="background-color: #efefef !important;"><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcular_servicios()" onkeydown="calcular_servicios()" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_celular : 0 }}" id="gasto_celular"></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Cable</td>
                <td style="background-color: #efefef !important;"><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcular_servicios()" onkeydown="calcular_servicios()" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_cable : 0 }}" id="gasto_cable"></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Otros gastos personales ({{ configuracion($tienda->id,'provision_gastos_familiares')['valor'] }}%)</td>
                <td style="background-color: #efefef !important;"><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_otros : 0 }}" 
                                                                         suma id="gasto_otros" disabled></td>
              </tr>
              <tr>
                <th style="background-color: #c8c8c8 !important;">Total Gasto Familiar (S/.)</th>
                <td style="background-color: #c8c8c8 !important;"><input type="text" disabled class="form-control campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_total : 0 }}" id="gasto_total"></td>
              </tr>
            </tbody>
          </table>
        
        </div>
      </div>
      <br>
      <div class="row d-none">
        <div class="col-sm-12 col-md-6">
          <table class="table">
            <thead>
              <tr>
                <th width="200px">SOLICITADO</th>
                <th>MONTO</th>
                <th>FRECUENCIA</th>
                <th>CUOTAS</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td></td>
                <td><input type="text" class="form-control campo_moneda" id="monto_solicitado" value="{{ $credito->monto_solicitado }}" disabled></td>
                <td><input type="text" class="form-control" id="frecuencia_pago" value="{{ $credito->forma_pago_credito_nombre }}" disabled></td>
                <td><input type="text" class="form-control text-center" id="coutas_credito" value="{{ $credito->cuotas }}" disabled></td>
                
                
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <div class="row mt-2">
        <div class="col-sm-12">
          <table class="table table-bordered" id="table-propuesta">
            <thead>
              <tr>
                <th colspan=10 class="text-center">PROPUESTA</th>
              </tr>
              <tr>
                <th rowspan=2>DESTINO DE CRÉDITO</th>
                <th rowspan=2>Producto</th>
                <th colspan=2>Plazo</th>
                <th rowspan=2>FORMA DE PAGO</th>
                <th rowspan=2>Monto Préstamo</th>
                <th rowspan=2>TEM</th>
                
                <th rowspan=2>Servicios/Otros (S/.)</th>
                <th rowspan=2>Cargos (S/.)</th>
                <th rowspan=2 >Cuota de Pago <span id="nombre_frecuencia_pago">Diario</span> (S/.)</th>
              </tr>
              <tr>
                <th>Pago</th>
                <th>Cuotas</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><input type="text" disabled class="form-control" id="propuesta_objetivo" value="{{ $credito->tipo_destino_credito_nombre}}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $credito->nombreproductocredito }}"></td>
                <td>
                  <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control" id="idforma_pago_credito" disabled>
                    @foreach($forma_pago_credito as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                  </select>
                </td> 
                <td>
                  <input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda" valida_input_vacio 
                         value="{{ $credito->cuotas }}" 
                         id="propuesta_cuotas" disabled>
                </td>
                <td>
                  <select class="form-control" id="propuesta_forma_pago" disabled>
                    <option value="1">Cuota Fija</option>
                  </select>
                </td>
                <td>
                  <input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda" valida_input_vacio 
                         value="{{ $credito->monto_solicitado }}" 
                         id="propuesta_monto" disabled>
                </td>
                <td>
                  <div class="input-group">
                    <input type="text" step="any" id="propuesta_tem" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda" 
                           value="{{ $credito->tasa_tem }}" minimo="{{ $credito->tasa_tem }}" disabled>
                    <span class="input-group-text">%</span>
                  </div>
                </td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito->cuota_comision }}" disabled id="propuesta_servicio_otros"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito->cuota_cargo }}" disabled id="propuesta_cargos"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito->cuota_pago }}" disabled id="propuesta_total_pagar"></td>
              </tr>
            </tbody>
            
            <tfoot>
              <tr>
                <td class="color_totales" colspan=9 align="right">PAGO MES (S/.)</td>
                <td class="color_totales"><input type="text" class="form-control campo_moneda" value="{{ $credito->total_propuesta }}" id="total_propuesta" disabled></td>
              </tr>
              <tr>
                <td colspan=10 class="text-center" id="mensaje_error_cronograma"></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      
      <div class="row mt-2">
        <div class="col-sm-12 col-md-5">
          <table class="table">
            <tr>
              <td colspan="2"> (1): Indicador de Solvencia</td>
              <td><input type="text" class="form-control" disabled id="estado_indicador_solvencia" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->estado_indicador_solvencia : '' }}"></td>
            </tr>
            <tr>
              <td colspan="2"> (2): Indicador de Relación Cuota /Ingreso</td>
              <td><input type="text" class="form-control" disabled id="estado_indicador_cuota_ingreso" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->estado_indicador_cuota_ingreso : '' }}"></td>
            </tr>
            <tr>
              <td> (3): Indicador de Relación Cuota/Venta</td>
              <td><b>a. Diaria</b></td>
              <td><input type="text" class="form-control" disabled id="estado_indicador_cuota_venta_diario" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->estado_indicador_cuota_venta_diario : '' }}"></td>
            </tr>
            <tr>
              <td></td>
              <td><b>b. Semana</b></td>
              <td><input type="text" class="form-control" disabled id="estado_indicador_cuota_venta_semanal" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->estado_indicador_cuota_venta_semanal : '' }}"></td>
            </tr>
            <tr>
              <td></td>
              <td><b>c. Quicena</b></td>
              <td><input type="text" class="form-control" disabled id="estado_indicador_cuota_venta_quincenal" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->estado_indicador_cuota_venta_quincenal : '' }}"></td>
            </tr>
            <tr>
              <td></td>
              <td><b>d. Mes</b></td>
              <td><input type="text" class="form-control" disabled id="estado_indicador_cuota_venta_mensual" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->estado_indicador_cuota_venta_mensual : '' }}"></td>
            </tr>
          </table>
            <div id="estado_credito_general" class="alert bg-success text-white mt-2 fw-bold">CRÉDITO NO VIABLE</div>
        </div>
        <div class="col-sm-12 col-md-3">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th colspan=2>INDICADOR DE SOLVENCIA (1)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Excedente (S/.)</td>
                <td style="width:100px;"><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->indicador_solvencia_excedente : '0.00' }}" id="indicador_solvencia_excedente" disabled></td>
              </tr>
              <tr>
                <td>Relación Cuota/ excedente (%)</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->indicador_solvencia_cuotas : '0.00' }}" id="indicador_solvencia_cuotas" disabled></td>
              </tr>
            </tbody>
          </table>
          
        </div>
        <div class="col-sm-12 col-md-4">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th colspan=2>RELACION CUOTA / INGRESO (2)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>R. Cuota Mensual/Ingreso Mensual (%)</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_mensual : '0.00' }}" disabled id="relacion_cuota_mensual"></td>
              </tr>
            </tbody>
            <thead>
              <tr>
                <th colspan=2>RELACIÓN CUOTA / VENTA (3)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><b>a.</b> Cuota diaria/ Venta diaria (%)</td>
                <td><input type="text" class="form-control campo_moneda" value="0.00" disabled id="relacion_cuota_venta_diaria"></td>
              </tr>
              <tr>
                <td><b>b.</b> Cuota Semanal/ Venta semanal (%)</td>
                <td><input type="text" class="form-control campo_moneda" value="0.00" disabled id="relacion_cuota_venta_semanal"></td>
              </tr>
              <tr>
                <td><b>c.</b> Cuota quincenal/ Venta quincenal (%)</td>
                <td><input type="text" class="form-control campo_moneda" value="0.00" disabled id="relacion_cuota_venta_quincenal"></td>
              </tr>
              <tr>
                <td><b>d.</b> Cuota Mensual/Venta Mensual (%)</td>
                <td><input type="text" class="form-control campo_moneda" value="0.00" disabled id="relacion_cuota_venta_mensual"></td>
              </tr>
            </tbody>
          </table>
        </div>
        
      </div>
      <script>
        calcular_inidicador_excedente();
        function calcular_inidicador_excedente(){
          let ingresos_op_total = parseFloat($('#ingresos_op_total').val())
          let gasto_total = parseFloat($('#gasto_total').val())
          let ingresos_op_ampliacion_regulada = parseFloat($('#ingresos_op_ampliacion_regulada').val())
          let ingresos_op_ampliacion_noregulada = parseFloat($('#ingresos_op_ampliacion_noregulada').val())
          
          @if($credito->idmodalidad_credito==2 || $credito->idmodalidad_credito==3)
              //$('#boton_guardar').attr('disabled',false);
              $('#resultado_modalidaddecredito').css('display','none');
              if(ingresos_op_ampliacion_regulada<=0 && ingresos_op_ampliacion_noregulada<=0){
                  //$('#boton_guardar').attr('disabled',true);
                  $('#resultado_modalidaddecredito').css('display','block');
              }
          @endif
              
          let indicador_solvencia_excedente = (ingresos_op_total - gasto_total) + ingresos_op_ampliacion_regulada + ingresos_op_ampliacion_noregulada ;
          $('#indicador_solvencia_excedente').val(indicador_solvencia_excedente.toFixed(2));
          
          
          let indicador_solvencia_cuotas = parseFloat($('#indicador_solvencia_cuotas').val())
          let rango_tope = parseFloat("{{ configuracion($tienda->id,'rango_tope')['valor'] }}")
          let estado_indicador_solvencia = '';
          
          if (indicador_solvencia_cuotas < 0) {
            estado_indicador_solvencia = "NO VIABLE";
          } else if (indicador_solvencia_cuotas <= rango_tope) {
            estado_indicador_solvencia = "VIABLE";
          } else if (indicador_solvencia_cuotas > rango_tope){
            estado_indicador_solvencia = "NO VIABLE";
          }
          $('#estado_indicador_solvencia').val(estado_indicador_solvencia);
          
          let relacion_cuota_mensual = parseFloat($('#relacion_cuota_mensual').val())
          let relacion_couta_ingreso = parseFloat("{{ configuracion($tienda->id,'relacion_couta_ingreso')['valor'] }}");
          
          let estado_indicador_cuota_ingreso = '';
          if (relacion_cuota_mensual < 0) {
            estado_indicador_cuota_ingreso = "NO VIABLE";
          } else if (relacion_cuota_mensual <= relacion_couta_ingreso) {
            estado_indicador_cuota_ingreso = "VIABLE";
          } else if (relacion_cuota_mensual > relacion_couta_ingreso){
            estado_indicador_cuota_ingreso = "NO VIABLE";
          }
          $('#estado_indicador_cuota_ingreso').val(estado_indicador_cuota_ingreso);
          
          let relacion_cuota_venta_diaria = parseFloat($('#relacion_cuota_venta_diaria').val())
          let relacion_cuota_venta_semanal = parseFloat($('#relacion_cuota_venta_semanal').val())
          let relacion_cuota_venta_quincenal = parseFloat($('#relacion_cuota_venta_quincenal').val())
          let relacion_cuota_venta_mensual = parseFloat($('#relacion_cuota_venta_mensual').val())
          
          let relacion_cuota_venta = parseFloat("{{ configuracion($tienda->id,'relacion_cuota_venta')['valor'] }}");
          
          let estado_indicador_cuota_venta_diario = '';
          if (relacion_cuota_venta_diaria < 0) {
            estado_indicador_cuota_venta_diario = "NO VIABLE";
          } else if (relacion_cuota_venta_diaria === 0) {
            estado_indicador_cuota_venta_diario = "----";
          } else if (relacion_cuota_venta_diaria <= relacion_cuota_venta) {
            estado_indicador_cuota_venta_diario = "VIABLE";
          } else if (relacion_cuota_venta_diaria > relacion_cuota_venta) {
            estado_indicador_cuota_venta_diario = "NO VIABLE";
          }
          $('#estado_indicador_cuota_venta_diario').val(estado_indicador_cuota_venta_diario);
          
          let estado_indicador_cuota_venta_semanal = '';
          if (relacion_cuota_venta_semanal < 0) {
            estado_indicador_cuota_venta_semanal = "NO VIABLE";
          } else if (relacion_cuota_venta_semanal === 0) {
            estado_indicador_cuota_venta_semanal = "----";
          } else if (relacion_cuota_venta_semanal <= relacion_cuota_venta) {
            estado_indicador_cuota_venta_semanal = "VIABLE";
          } else if (relacion_cuota_venta_semanal > relacion_cuota_venta) {
            estado_indicador_cuota_venta_semanal = "NO VIABLE";
          }
          $('#estado_indicador_cuota_venta_semanal').val(estado_indicador_cuota_venta_semanal);
          
          let estado_indicador_cuota_venta_quincenal = '';
          if (relacion_cuota_venta_quincenal < 0) {
            estado_indicador_cuota_venta_quincenal = "NO VIABLE";
          } else if (relacion_cuota_venta_quincenal === 0) {
            estado_indicador_cuota_venta_quincenal = "----";
          } else if (relacion_cuota_venta_quincenal <= relacion_cuota_venta) {
            estado_indicador_cuota_venta_quincenal = "VIABLE";
          } else if (relacion_cuota_venta_quincenal > relacion_cuota_venta) {
            estado_indicador_cuota_venta_quincenal = "NO VIABLE";
          }
          $('#estado_indicador_cuota_venta_quincenal').val(estado_indicador_cuota_venta_quincenal);
          
          let estado_indicador_cuota_venta_mensual = '';
          if (relacion_cuota_venta_mensual < 0) {
            estado_indicador_cuota_venta_mensual = "NO VIABLE";
          } else if (relacion_cuota_venta_mensual === 0) {
            estado_indicador_cuota_venta_mensual = "----";
          } else if (relacion_cuota_venta_mensual <= relacion_cuota_venta) {
            estado_indicador_cuota_venta_mensual = "VIABLE";
            
          } else if (relacion_cuota_venta_mensual > relacion_cuota_venta) {
            estado_indicador_cuota_venta_mensual = "NO VIABLE";
            
          }
          $('#estado_indicador_cuota_venta_mensual').val(estado_indicador_cuota_venta_mensual);
          
          if(estado_indicador_solvencia == "VIABLE" ||  estado_indicador_cuota_ingreso == "VIABLE" || estado_indicador_cuota_venta_diario == "VIABLE" || estado_indicador_cuota_venta_semanal == "VIABLE" || estado_indicador_cuota_venta_quincenal == "VIABLE" || estado_indicador_cuota_venta_mensual == "VIABLE" ){
            $('#estado_credito_general').removeClass('bg-danger');
            $('#estado_credito_general').addClass('bg-success'); 
            $('#estado_credito_general').html('CRÉDITO VIABLE');
            //$('#boton_guardar').attr('disabled',false);
            //validar boton

            @if($credito->idmodalidad_credito==2 || $credito->idmodalidad_credito==3)
                let ingresos_op_ampliacion_regulada = parseFloat($('#ingresos_op_ampliacion_regulada').val())
                let ingresos_op_ampliacion_noregulada = parseFloat($('#ingresos_op_ampliacion_noregulada').val())
                //$('#boton_guardar').attr('disabled',false);
                if(ingresos_op_ampliacion_regulada<=0 && ingresos_op_ampliacion_noregulada<=0){
                   // $('#boton_guardar').attr('disabled',true);
                }
            @endif
          }else{
            $('#estado_credito_general').removeClass('bg-success');
            $('#estado_credito_general').addClass('bg-danger');
            $('#estado_credito_general').html('CRÉDITO NO VIABLE');
            //$('#boton_guardar').attr('disabled',true);
          }
          /*
          $('#estado_indicador_cuota_venta_mensual').removeClass('bg-danger');
            $('#estado_indicador_cuota_venta_mensual').addClass('bg-success text-white');
          $('#estado_indicador_cuota_venta_mensual').removeClass('bg-success');
            $('#estado_indicador_cuota_venta_mensual').addClass('bg-danger text-white');
            */
        }
        calcular_relacion_cuota();
        function calcular_relacion_cuota(){
          let total_propuesta = parseFloat($('#total_propuesta').val())
          let ingresos_op_ampliacion_regulada = parseFloat($('#ingresos_op_ampliacion_regulada').val())
          let ingresos_op_ampliacion_noregulada = parseFloat($('#ingresos_op_ampliacion_noregulada').val())
          let indicador_solvencia_excedente = parseFloat($('#indicador_solvencia_excedente').val())
          let indicador_solvencia_cuotas = (total_propuesta / indicador_solvencia_excedente) * 100;
          $('#indicador_solvencia_cuotas').val(indicador_solvencia_cuotas.toFixed(2));
          
          let ingresos_op_total = parseFloat($('#ingresos_op_total').val())
          let relacion_cuota_mensual = (total_propuesta / (ingresos_op_total+ingresos_op_ampliacion_regulada+ingresos_op_ampliacion_noregulada)) * 100;
          $('#relacion_cuota_mensual').val(relacion_cuota_mensual.toFixed(2));
          
          let idforma_pago_credito = $('#idforma_pago_credito').val();
          let propuesta_total_pagar = parseFloat($('#propuesta_total_pagar').val());
          let ingresos_op_ventas = parseFloat($('#ingresos_op_ventas').val());
          
          
          let relacion_cuota_venta_diaria = 0;
          let relacion_cuota_venta_semanal = 0;
          let relacion_cuota_venta_quincenal = 0;
          let relacion_cuota_venta_mensual = 0;
          
          if(idforma_pago_credito == 1){
            relacion_cuota_venta_diaria = (propuesta_total_pagar)/(ingresos_op_ventas/26);
            relacion_cuota_venta_diaria = relacion_cuota_venta_diaria * 100;
          }
          else if(idforma_pago_credito == 2){
            relacion_cuota_venta_semanal = (propuesta_total_pagar)/(ingresos_op_ventas/4);
            relacion_cuota_venta_semanal = relacion_cuota_venta_semanal * 100;
          }
          else if(idforma_pago_credito == 3){
            relacion_cuota_venta_quincenal = (propuesta_total_pagar)/(ingresos_op_ventas/2);
            relacion_cuota_venta_quincenal = relacion_cuota_venta_quincenal * 100;
          }
          else if(idforma_pago_credito == 4){
            relacion_cuota_venta_mensual = (propuesta_total_pagar)/(ingresos_op_ventas);
            relacion_cuota_venta_mensual = relacion_cuota_venta_mensual * 100;
          }
          
          $('#relacion_cuota_venta_diaria').val(relacion_cuota_venta_diaria.toFixed(2));
          $('#relacion_cuota_venta_semanal').val(relacion_cuota_venta_semanal.toFixed(2));    
          $('#relacion_cuota_venta_quincenal').val(relacion_cuota_venta_quincenal.toFixed(2));   
          $('#relacion_cuota_venta_mensual').val(relacion_cuota_venta_mensual.toFixed(2));   
          
          calcular_inidicador_excedente();
          
        }
      </script>
      <div class="mb-1 mt-2">
        <span class="badge d-block">IV. DETALLE DEL DESTINO DEL PRÉSTAMO</span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <textarea id="detalle_destino_prestamo" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" cols="30" rows="3">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->detalle_destino_prestamo : '' }}</textarea>
        </div>
      </div>
      
      <div class="mb-1 mt-2">
        <span class="badge d-block">V. COMENTARIOS Y ESPECIFICACIONES DE FORTALEZAS DEL NEGOCIO</span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <textarea id="fortalezas_negocio"  {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" cols="30" rows="3">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->fortalezas_negocio : '' }}</textarea>
        </div>
      </div>
      <div class="row mt-1">
        
        @if($view_detalle!='false')
        <div class="col" style="flex: 0 0 0%;">
          <button type="submit" class="btn btn-success" id="boton_guardar"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS <b>({{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->cantidad_update : 0 }})</b></button>
        </div>
        @endif
        <div class="col" style="flex: 0 0 0%;">
          <button type="button" 
                  class="btn btn-dark" 
                  onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=solicitud_resumida')}}', size: 'modal-fullscreen' })"
                  id="boton_imprimir">
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
  .color_cajatexto + .select2-container--bootstrap-5 .select2-selection{
      background-color: #dfdf79 !important;
      border-color: #dfdf79 !important;
  }
  .form-check-input:checked {
      background-color: #585858 !important;
      border-color: #585858 !important;
  }
</style>
<script>
  valida_input_vacio();
  $('input[valida_input_vacio]').on('blur', function() {
      calcula_ingreso_gasto();
      actualizarTotalGastos();
  });
  $('input').on('blur', function() {
      $('#boton_imprimir').attr('disabled',true);
  });
  sistema_select2({ input:'#idtipo_giro_economico' });

  @if($credito_evaluacion_resumida)
  $("#idtipo_giro_economico").on("change", function(e) {
    
    <?php echo $view_detalle=='false' ? '' : "$('#idgiro_economico_evaluacion').removeAttr('disabled',false)" ?>
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
            option_select += `<option value="${value.id}" margen="${value.porcentaje}">${value.nombre}</option>`;
          });
          $('#idgiro_economico_evaluacion').html(option_select);
          $('#idgiro_economico_evaluacion').select2({
            placeholder:'-- Seleccionar --',
            theme: 'bootstrap-5',
            dropdownParent:'.modal'
          }).val('{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->idgiro_economico_evaluacion : 0 }}').trigger('change');
          
          
          let valor_giro_economico = $("#idgiro_economico_evaluacion").val();
          let option_giro_economigo = $('#idgiro_economico_evaluacion option[value="' + valor_giro_economico + '"]');
          let margen_venta = option_giro_economigo.attr('margen');
          $('#margen_tipo_giro').val(margen_venta);

        }
      })
  }).val('{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->idtipo_giro_economico : 0 }}').trigger('change');
  @else
  $("#idtipo_giro_economico").on("change", function(e) {
    
    <?php echo $view_detalle=='false' ? '' : "$('#idgiro_economico_evaluacion').removeAttr('disabled',false)" ?>
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
            option_select += `<option value="${value.id}" margen="${value.porcentaje}">${value.nombre}</option>`;
          });
          $('#idgiro_economico_evaluacion').html(option_select);
          $('#idgiro_economico_evaluacion').select2({
            placeholder:'-- Seleccionar --',
            theme: 'bootstrap-5',
            dropdownParent:'.modal'
          }).val('{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->idgiro_economico_evaluacion : 0 }}').trigger('change');
          
          
          /*let valor_giro_economico = $("#idgiro_economico_evaluacion").val();
          let option_giro_economigo = $('#idgiro_economico_evaluacion option[value="' + valor_giro_economico + '"]');
          let margen_venta = option_giro_economigo.attr('margen');
          $('#margen_tipo_giro').val(margen_venta);*/

        }
      })
  });
  @endif
  
  
  $("#idgiro_economico_evaluacion").on("change", function(e) {
    
    $.ajax({
        url:"{{url('backoffice/0/credito/showgiroeconomico_giro')}}",
        type:'GET',
        data: {
            tipogiro : $('#idgiro_economico_evaluacion :selected').val(),
            giro : e.currentTarget.value
        },
        success: function (res){

          $('#margen_tipo_giro').val(res);

        }
      })
  });
  /*function show_giro_economico(id){
    $.ajax({
        url:"{{url('backoffice/0/credito/showgiroeconomico')}}",
        type:'GET',
        data: {
            tipogiro : id
        },
        success: function (res){

          let option_select = `<option></option>`;
          $.each(res, function( key, value ) {
            option_select += `<option value="${value.id}" margen="${value.porcentaje}">${value.nombre}</option>`;
          });
          $('#idgiro_economico_evaluacion').html(option_select);
          $('#idgiro_economico_evaluacion').select2({
            placeholder:'-- Seleccionar --',
            theme: 'bootstrap-5',
            dropdownParent:'.modal'
          }).val('{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->idgiro_economico_evaluacion : 0 }}').trigger('change');
          
          
          let valor_giro_economico = $("#idgiro_economico_evaluacion").val();
          let option_giro_economigo = $('#idgiro_economico_evaluacion option[value="' + valor_giro_economico + '"]');
          let margen_venta = option_giro_economigo.attr('margen');
          console.log(margen_venta)
          $('#margen_tipo_giro').val(margen_venta);

        }
      })
  }*/
  function total_deudas() {
    let cantidad_cliente_natural = parseFloat($('#cantidad_cliente_natural').val());
    let cantidad_cliente_juridico = parseFloat($('#cantidad_cliente_juridico').val());
    let cantidad_pareja_natural = parseFloat($('#cantidad_pareja_natural').val());
    let cantidad_pareja_juridico = parseFloat($('#cantidad_pareja_juridico').val());
    $('#total_deuda').val(cantidad_cliente_natural+cantidad_cliente_juridico+cantidad_pareja_natural+cantidad_pareja_juridico);
  }
  function total_deudas_garante(){
    let cantidad_garante_natural = parseFloat($('#cantidad_garante_natural').val());
    let cantidad_garante_juridico = parseFloat($('#cantidad_garante_juridico').val());
    let cantidad_garante_pareja_natural = parseFloat($('#cantidad_garante_pareja_natural').val());
    let cantidad_garante_pareja_juridico = parseFloat($('#cantidad_garante_pareja_juridico').val());
    $('#total_deuda_garante').val(cantidad_garante_natural+cantidad_garante_juridico+cantidad_garante_pareja_natural+cantidad_garante_pareja_juridico);
  }
  @foreach($referencia_cliente as $value)
    agregar_referencia('{{ $value->fuente }}', '{{ $value->nombre }}', '{{ $value->vinculo }}', '{{ $value->celular }}');
  @endforeach
  function agregar_referencia(fuente='', nombre='', vinculo='', celular=''){

      var num   = $("#tabla-referencia > tbody").attr('num');
      var cant  = $("#tabla-referencia > tbody > tr").length;

      var tdeliminar = '<td><a href="javascript:;" onclick="eliminar_cliente_financiera('+num+',`referencia`)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></td>';

      let option = ``;
      @foreach($f_tiporeferencia as $value)
        var selectedOption = fuente == "{{ $value->id }}" ? 'selected' : '';
        option += `<option value="{{ $value->id }}" ${selectedOption}>{{$value->nombre}}</option>`;
      @endforeach
      var tabla= '<tr id="'+num+'">'+
                  '<td><select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" id="fuente'+num+'"><option></option>'+option+'</select></td>'+
                  '<td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" id="nombre'+num+'" value="'+nombre+'"></td>'+
                  '<td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" id="vinculo'+num+'" value="'+vinculo+'"></td>'+
                  '<td><input type="number" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" id="celular'+num+'" value="'+celular+'"></td>'+
                  
                    {{ $view_detalle=='false' ? '' : 'tdeliminar+' }}
                 '</tr>';

      $("#tabla-referencia > tbody").append(tabla);
      $("#tabla-referencia > tbody").attr('num',parseInt(num)+1);  

  }
  function eliminar_cliente_financiera(num,tabla){
      $("#tabla-"+tabla+" > tbody > tr#"+num).remove();
  }
  function calcula_total_dia() {
    let total = 0;
    let numero_dias = 0;
    $("#tabla-dias tbody tr:not([total])").each(function() {
      let dia = parseFloat($(this).find('td input').val());
      numero_dias += dia <= 0 ? 0 : 1;
      total += dia;
    });
    
    $('#venta_total_dias').val(total.toFixed(2));
    calcula_ingreso_gasto()
  }
  function calcula_total_mes() {
    let total = 0;
    $("#tabla-semanas tbody tr:not([total])").each(function() {
      let mes = parseFloat($(this).find('td input').val());
      total += mes;
    });
    $('#venta_total_mensual').val(total.toFixed(2));
    calcula_ingreso_gasto()
  }
  function json_referencia(){
      var data = [];
      $("#tabla-referencia > tbody > tr").each(function() {
          var num = $(this).attr('id');    
          data.push({ 
              fuente: $('#fuente'+num).val(),
              nombre: $('#nombre'+num).val(),
              vinculo: $('#vinculo'+num).val(),
              celular: $('#celular'+num).val(),
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
  function json_ingresos_gastos(){
    let jsonData = [];
    $("#table-ingresos-gastos input").each(function () {
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
    setTimeout(function() {
      calcula_ingreso_gasto();
    }, 1000);
  $("#table-ingresos-gastos tbody tr td input").on("keyup", function() {
    calcula_ingreso_gasto();
  });
  function calcula_ingreso_gasto(){
    let venta_total_dias = parseFloat($('#venta_total_dias').val())
    let venta_total_mensual = parseFloat($('#venta_total_mensual').val())
    let cantidad_dias  = 0;
    
    $('#tabla-dias tbody input[type="text"][valida_input_vacio]').each(function() {
      var valor = $(this).val();
      // Comprueba si el valor es mayor a cero o no está vacío.
      if (valor > 0) {
        cantidad_dias++;
      }
    });
    let total_operacion_dia = 0;
    if(cantidad_dias < 5){
       total_operacion_dia = venta_total_dias * 4;
    }
    else if(cantidad_dias == 5){
      total_operacion_dia = (venta_total_dias/5)*22 ;
    }
    else if(cantidad_dias == 6){
      total_operacion_dia = (venta_total_dias/6)*26 ;
    }
    else if(cantidad_dias == 7){
      total_operacion_dia = (venta_total_dias/7)*26 ;
    }
    let ingresos_op_ventas = total_operacion_dia + venta_total_mensual;
//     let ingresos_op_ventas = (venta_total_dias / cantidad_dias * 26 ) + venta_total_mensual;
    $('#ingresos_op_ventas').val(ingresos_op_ventas.toFixed(2));


    let margen_tipo_giro = parseFloat($('#margen_tipo_giro').val())
    let ingresos_op_costo_produccion = ingresos_op_ventas * (1 - (margen_tipo_giro / 100));
    $('#ingresos_op_costo_produccion').val(ingresos_op_costo_produccion.toFixed(2));

    let ingresos_op_utilidad_bruta = ingresos_op_ventas - ingresos_op_costo_produccion;
    $('#ingresos_op_utilidad_bruta').val(ingresos_op_utilidad_bruta.toFixed(2));



    let ingresos_op_gasto_personal = parseFloat($('#ingresos_op_gasto_personal').val())
    let ingresos_op_luz = parseFloat($('#ingresos_op_luz').val())
    let ingresos_op_agua = parseFloat($('#ingresos_op_agua').val())
    let ingresos_op_telefono = parseFloat($('#ingresos_op_telefono').val())
    let ingresos_op_cable = parseFloat($('#ingresos_op_cable').val())
    let ingresos_op_alquiler = parseFloat($('#ingresos_op_alquiler').val())
    let ingresos_op_autovaluo = parseFloat($('#ingresos_op_autovaluo').val())
    let ingresos_op_transporte = parseFloat($('#ingresos_op_transporte').val())
    let ingresos_op_cuota_prestamo_regulada = parseFloat($('#ingresos_op_cuota_prestamo_regulada').val())
    let ingresos_op_cuota_prestamo_noregulada = parseFloat($('#ingresos_op_cuota_prestamo_noregulada').val())
    let ingresos_op_sunat = parseFloat($('#ingresos_op_sunat').val())
    let ingresos_op_otros_gastos = parseFloat($('#ingresos_op_otros_gastos').val())
    let ingresos_op_otros_negocios = parseFloat($('#ingresos_op_otros_negocios').val())
    let ingresos_op_ingreso_fijo = parseFloat($('#ingresos_op_ingreso_fijo').val());

    let ingresos_op_total = ingresos_op_utilidad_bruta - 
                (ingresos_op_gasto_personal +
                ingresos_op_luz +
                ingresos_op_agua +
                ingresos_op_telefono +
                ingresos_op_cable +
                ingresos_op_alquiler +
                ingresos_op_autovaluo +
                ingresos_op_transporte +
                ingresos_op_cuota_prestamo_regulada +
                ingresos_op_cuota_prestamo_noregulada +
                ingresos_op_sunat +
                ingresos_op_otros_gastos) +
                ingresos_op_otros_negocios +
                ingresos_op_ingreso_fijo;
    $('#ingresos_op_total').val(ingresos_op_total.toFixed(2));
    calcular_inidicador_excedente();
    calcular_relacion_cuota();
   }
  
  function calcular_servicios(){
    let gasto_agua = parseFloat($('#gasto_agua').val());
    let gasto_luz = parseFloat($('#gasto_luz').val());
    let gasto_telefono_internet = parseFloat($('#gasto_telefono_internet').val());
    let gasto_celular = parseFloat($('#gasto_celular').val());
    let gasto_cable = parseFloat($('#gasto_cable').val());
    let total_servicios = gasto_agua+gasto_luz+gasto_telefono_internet+gasto_celular+gasto_cable;
    $('#total_servicios').val(total_servicios.toFixed(2));
    calcular_inidicador_excedente();
  }  
  function actualizarTotalGastos() {
    // servicios
    let gasto_agua = parseFloat($('#gasto_agua').val());
    let gasto_luz = parseFloat($('#gasto_luz').val());
    let gasto_telefono_internet = parseFloat($('#gasto_telefono_internet').val());
    let gasto_celular = parseFloat($('#gasto_celular').val());
    let gasto_cable = parseFloat($('#gasto_cable').val());
    let total_servicios = gasto_agua+gasto_luz+gasto_telefono_internet+gasto_celular+gasto_cable;

    // gastos familiares
    let gasto_alimentacion = parseFloat($('#gasto_alimentacion').val());
    let gasto_educacion = parseFloat($('#gasto_educacion').val());
    let gasto_vestimenta = parseFloat($('#gasto_vestimenta').val());
    let gasto_transporte = parseFloat($('#gasto_transporte').val());
    let gasto_salud = parseFloat($('#gasto_salud').val());
    let gasto_vivienda = parseFloat($('#gasto_vivienda').val());
    let gastosfamiliares = gasto_alimentacion+gasto_educacion+gasto_vestimenta+gasto_transporte+gasto_salud+gasto_vivienda;
    let porcenaje_gastos = "{{ configuracion($tienda->id,'provision_gastos_familiares')['valor'] }}";
    porcenaje_gastos = (parseFloat(porcenaje_gastos)/100);
    let gastos_otros = (gastosfamiliares+total_servicios) * porcenaje_gastos; // 12%
    let gastos_total = (gastosfamiliares+total_servicios)+gastos_otros;
    // Actualiza el campo "gasto_total" con la suma
    $('#gasto_total').val(gastos_total.toFixed(2));
    $('#gasto_otros').val(gastos_otros.toFixed(2));
    calcular_inidicador_excedente();
  }
  actualizarTotalGastos();
  $('#table-gastos-familiares input[id^="gasto_"]').not('#gasto_total').on('input', actualizarTotalGastos);
  
  sistema_select2({ input:'#idforma_pago_credito' });
  $("#idforma_pago_credito").on("change", function(e) {
    var selectedOption = $(this).select2('data')[0];
    var selectedText = selectedOption.text;
    $('#nombre_frecuencia_pago').text(selectedText);
  }).val('{{ $credito->idforma_pago_credito }}').trigger('change');
  
  /*$("#idforma_pago_credito").on("select2:select", function(e) {
    showtasa(1)
  });*/
  function cronograma(){
          
    let monto       = parseFloat($('#propuesta_monto').val());
    let numerocuota = parseFloat($('#propuesta_cuotas').val());
    let fechainicio = $('#fecha_desembolso').val();
    let frecuencia  = $('#idforma_pago_credito').val();
    let dia_gracia  = {{ $credito->dia_gracia }};

    let cargo       = $('#propuesta_cargos').val();

    //let tasa        = {{ $credito->tasa_tem }};
    let tasa        = $('#propuesta_tem').val();
    let tipotasa    = "{{$credito->modalidad_calculo}}" == 'Interes Simple' ? 1 : 2;
    let error_cronograma = '';
    $('#mensaje_error_cronograma').text(error_cronograma);
    $('#mensaje_error_cronograma').removeClass('bg-danger');
    if( monto <= 0 ){
        //alert("Monto de Prestamo debe ser mayor a 0.00.");
        error_cronograma = "Monto de Prestamo debe ser mayor a 0.00.";
        return false;
    }
    if(numerocuota<=0){
        //alert("El Número de Cuotas debe ser mayor a 0.");
        error_cronograma = "El Número de Cuotas debe ser mayor a 0.";
        return false;
    }

    if(dia_gracia<0){
        
        //alert("El día de gracia debe ser mayor o igual a 0!!.");
        error_cronograma = "El día de gracia debe ser mayor o igual a 0!!.";
        return false;
    }
    if(dia_gracia > {{$diasdegracia}}){
        //alert("Máximo puede poner {{$diasdegracia}} días de gracia!!.");
        error_cronograma = "Máximo puede poner {{$diasdegracia}} días de gracia!!.";
        return false;
    }

    if(monto=='' || numerocuota=='' || fechainicio=='' || frecuencia==''){
        error_cronograma = "Complete los campos";
        
        return false;
    }
    $('#mensaje_error_cronograma').text(error_cronograma);
    $('#mensaje_error_cronograma').addClass('bg-danger');

    $.ajax({
        url:"{{url('backoffice/0/credito/cronograma')}}",
        type: 'GET',
        data: {
            monto: monto,
            numerocuota: numerocuota,
            fechainicio: fechainicio,
            frecuencia: frecuencia,
            tasa: tasa,
            tipotasa: tipotasa,
            dia_gracia: dia_gracia,
            cargo: cargo,
            idcredito: '{{ $credito->id }}'
        },
        success: function (res) {
          $('#mensaje_error_cronograma').text('');
          $('#mensaje_error_cronograma').removeClass('bg-danger');
          if(res.resultado=='ERROR'){
            $('#mensaje_error_cronograma').addClass('bg-danger');
            $('#mensaje_error_cronograma').text(res.mensaje);
          }
          else{
            $('#mensaje_error_cronograma').text('');
            $('#mensaje_error_cronograma').removeClass('bg-danger');
            calcular_pago_mensual(res,numerocuota);
            let servicios_otros =  ( parseFloat(monto) * parseFloat(res.cargootros)) / 100;
            $('#propuesta_servicio_otros').val(servicios_otros.toFixed(2));
            
          }
        }
    });
  }
  function calcular_pago_mensual(res,numerocuota){

    let frecuencia_credito = parseFloat($('#idforma_pago_credito').val());
    
    let propuesta_total_pagar = parseFloat(res.total_pagar)/parseFloat(numerocuota);
    propuesta_total_pagar = parseFloat(propuesta_total_pagar.toFixed(1))
    $('#propuesta_total_pagar').val(propuesta_total_pagar.toFixed(2));
    let total_propuesta = 0;
    if (frecuencia_credito === 1) {
      total_propuesta = propuesta_total_pagar * 26;
    }
    else if ( frecuencia_credito === 2) {
      total_propuesta = propuesta_total_pagar * 4.29;
    }
    else if ( frecuencia_credito === 3) {
      total_propuesta = propuesta_total_pagar * 2;
    }
    else if ( frecuencia_credito === 4) {
      total_propuesta = propuesta_total_pagar;
    }
    $('#total_propuesta').val(total_propuesta.toFixed(2))
    calcular_relacion_cuota();
  }
  /*showtasa();
  function showtasa(valid=0){

    let monto       = $('#propuesta_monto').val();
    let numerocuota = $('#propuesta_cuotas').val();
    let frecuencia  = $('#idforma_pago_credito').val();

    if(monto==''){
        return false;
    }
    if(numerocuota==''){
        return false;
    }
    $.ajax({
      url:"{{url('backoffice/0/credito/showtasa')}}",
      type:'GET',
      data: {
          monto: monto,
          numerocuota: numerocuota,
          tasa: '',
          frecuencia: frecuencia,
          idcredito: '{{ $credito->id }}'
      },
      success: function (res){
        $('#propuesta_tem').attr('minimo',res.tasa_tem_minima);

        cronograma();
        if(valid==1){
            $('#propuesta_tem').val(res.tasa_tem_minima);
        }


      },
      complete: function(res){
        calcular_relacion_cuota();
      }
    })
  }*/

</script>    