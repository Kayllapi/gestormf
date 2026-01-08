<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/credito/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'control_limites',
              vinculacion_deudor: json_vinculacion_deudor(),
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
      $vinculacion_deudor = $credito_cuantitativa_control_limites ? ( $credito_cuantitativa_control_limites->vinculacion_deudor == "" ? [] : json_decode($credito_cuantitativa_control_limites->vinculacion_deudor) ) : [];
      $entidad_regulada = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->entidad_regulada == "" ? [] : json_decode($credito_cuantitativa_deudas->entidad_regulada) ) : [];
      $entidad_noregulada = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->entidad_noregulada == "" ? [] : json_decode($credito_cuantitativa_deudas->entidad_noregulada) ) : [];
    @endphp
    
    <?php
      $saldo_capital_empresa = 0;
      $saldo_deducciones_empresa = 0;
      foreach($entidad_regulada as $value){
        if($value->tipo_entidad){
          $saldo_capital_empresa += $value->saldo_capital;
          $saldo_deducciones_empresa += $value->saldo_capital_deducciones;
        }
      }
      foreach($entidad_noregulada as $value){
        if($value->tipo_entidad){
          $saldo_capital_empresa += $value->saldo_capital;
          $saldo_deducciones_empresa += $value->saldo_capital_deducciones;
        }
      }
  
    ?>
    <div class="modal-header" style="border-bottom: 0;">
        <h5 class="modal-title">GARANTIAS Y LIMITES </h5>
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
              @if($credito->idevaluacion == 1)
              <input type="text" step="any" class="form-control" value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->nombregiro_economico_evaluacion : '' }}" disabled>
              @else
              <input type="text" step="any" class="form-control" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->nombregiro_economico_evaluacion : '' }}" disabled>
              @endif
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-3">
          <div class="row">
            <label class="col-sm-3 col-form-label" style="text-align: right;">FECHA:</label>
            <div class="col-sm-7">
              <input type="date" step="any" class="form-control" value="{{ $credito_cuantitativa_control_limites ? date_format(date_create($credito_cuantitativa_control_limites->fecha),'Y-m-d') :'' }}" disabled>
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
        <span class="badge d-block">
          @if($credito->idevaluacion == 1)
          VI. 
          @else
          @if($users_prestamo->idfuenteingreso == 2)
          VII.
          @else
          IX.
          @endif
          @endif
          GARANTIAS Y LIMITES</span>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">
          {{ $credito->idevaluacion == 1 ? '6.1':($users_prestamo->idfuenteingreso == 2?'7.1':'9.1') }} GARANTÍAS DEL CLIENTE</span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <table class="table table-bordered" id="table-garantia-cliente">
            <thead>
              <tr>
                <th rowspan="2">Garantías presentadas por el cliente</th>
                <th colspan="2">Saldo de Prést. vigente (S/)</th>
                <th rowspan="2">Propuesta</th>
                <th rowspan="2">Descripción de garantía en Propuesta</th>
                <th rowspan="2">Valor de mercado (S/.)</th>
                <th rowspan="2">Valor comercial (Tasado) (S/.)</th>
                <th rowspan="2">Valor de realización(tasado) (S/.)</th>
              </tr>
              <tr>
                <th style="width:90px;">Propio</th>
                <th style="width:90px;">Avalado</th>
              </tr>
            </thead>
            <tbody>
              @php
                $garantia_cliente = 0;
              @endphp
              @forelse($credito_garantias_cliente as $key => $value)
                <?php
                  // monto anterior relacionado con el producto no prendario
                  $monto_anterior = DB::table('credito_garantia')
                      ->join('credito','credito.id','credito_garantia.idcredito')
                      ->where('credito_garantia.idgarantias',0)
                      ->where('credito_garantia.idcliente',$value->idcliente)
                      ->where('credito_garantia.idgarantias_noprendarias',$value->idcliente)
                      ->where('credito_garantia.idcredito','<>',$value->idcredito)
                      ->orderby('credito_garantia.id','desc')
                      ->sum('credito.monto_solicitado');
                $td_propuesta = '';
                if($key == 0){
                  $monto_propuesta = $credito->monto_solicitado;
                  $cantidad_filas = count($credito_garantias_cliente);
                  $td_propuesta = '<td rowspan="'.$cantidad_filas.'" class="text-center">
                                    <input type="hidden" disabled class="form-control campo_moneda" id="propuesta_general" value="'.$monto_propuesta.'">
                                    <b>'.$monto_propuesta.'</b>
                                  </td>';
                }
                ?>
                @php
                  $propuesta = $value->tipo_garantia_no_prendaria == 1 ? $value->valor_realizacion_garantia : $value->valor_mercado_garantia;
                  $garantia_cliente = $monto_anterior + $propuesta;
                @endphp
                
                <tr sumar_garantia>
                  <td>{{ $value->nombretipogarantia }}</td>
                  <td><input type="text" class="form-control campo_moneda" monto_garantia_cliente disabled value="{{ $monto_anterior }}"></td>
                  <td><input type="text" class="form-control campo_moneda" monto_garantia_cliente disabled value="{{ $monto_anterior }}"></td>
                  <?php echo $td_propuesta; ?>
                  <td>{{ $value->descripcion_garantia }}</td>
                  
                  <td class="campo_moneda">{{ $value->valor_mercado_garantia }}</td>
                  <td class="campo_moneda">{{ $value->valor_comercial_garantia }}</td>
                  <td class="campo_moneda">{{ $value->valor_realizacion_garantia }}</td>
                </tr>
              @empty
              <tr sumar_garantia>
                <td>Sin Garantia</td>
                <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" disabled monto_garantia_cliente value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->saldo_noprendario_cliente : '0.00' }}" onkeyup="cal_total_garantia_cliente()" id="saldo_noprendario_cliente"></td>
                <td><input type="text" disabled class="form-control campo_moneda" id="propuesta_general" value="{{ $credito->monto_solicitado }}"></td>
              </tr>
              @endforelse
              <tr>
                <td class="color_totales campo_moneda">TOTAL S/.</td>
                <td class="color_totales" colspan=3>
                  <input type="text" disabled class="form-control campo_moneda text-center" id="total_garantia_cliente" value="{{ $garantia_cliente }}">
                </td>
                <td class="color_totales" colspan=5></td>
              </tr>
            </tbody>
          </table>
          
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">
          {{ $credito->idevaluacion == 1 ? '6.2':($users_prestamo->idfuenteingreso == 2?'7.2':'9.2') }} GARANTIAS DEL GARANTE(AVAL)/FIADOR</span>
      </div>
      @if($users_prestamo_aval!='')
      <div class="row" container-garantias-aval>
        <div class="col-sm-12 col-md-6">
          <div class="row">
            <div class="col-sm-12 col-md-8">
              <div class="row">
                <label class="col-sm-6 col-form-label" style="text-align: right;">Apellidos y Nombres:</label>
                <div class="col-sm-6">
                  <input type="text" step="any" class="form-control" value="{{ $credito->nombreavalcredito }}" disabled>
                </div>
              </div>
              
              @if($users_prestamo_aval->dni_pareja!='' or $users_prestamo_aval->nombrecompleto_pareja!='')
              <div class="row">
                <label class="col-sm-6 col-form-label" style="text-align: right;">PAREJA:</label>
                <div class="col-sm-6">
                  <input type="text" step="any" class="form-control" value="{{ $users_prestamo_aval->nombrecompleto_pareja }}" disabled>
                </div>
              </div>
              @endif
            </div>
            <div class="col-sm-12 col-md-4">
              <div class="row">
                <label class="col-sm-3 col-form-label" style="text-align: right;">DNI:</label>
                <div class="col-sm-7">
                  <input type="text" step="any" class="form-control" value="{{ $credito->documentoaval }}" disabled>
                </div>
              </div>
              @if($users_prestamo_aval->dni_pareja!='' or $users_prestamo_aval->nombrecompleto_pareja!='')
              
              <div class="row">
                <label class="col-sm-3 col-form-label" style="text-align: right;">DNI:</label>
                <div class="col-sm-7">
                  <input type="text" step="any" class="form-control" value="{{ $users_prestamo_aval->dni_pareja }}" disabled>
                </div>
              </div>
              @endif
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-6">
          <p>N° DE ENTIDADES FINANCIERAS (Se considera deuda interna y Líneas de creditos sin uso)</p>
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
                <td style="background-color: #efefef !important;" rowspan="2">Garante (Aval)/Fiador</td>
                <td style="background-color: #efefef !important;">P.Natural</td>
                <td style="background-color: #efefef !important;">
                  <input 
                         type="text" valida_input_vacio 
                         style="padding: 4px;" 
                         onkeyup="total_deudas()" 
                         onkeydown="total_deudas()" 
                         {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda " 
                         value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cantidad_garante_natural : '0.00' }}" 
                         id="cantidad_garante_natural">
                </td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">P.Jurídica</td>
                <td style="background-color: #efefef !important;">
                  <input 
                         type="text" valida_input_vacio 
                         style="padding: 4px;" 
                         onkeyup="total_deudas()" 
                         onkeydown="total_deudas()" 
                         {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" 
                         value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cantidad_garante_juridico : '0.00' }}" 
                         id="cantidad_garante_juridico">
                </td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;" rowspan="2">Pareja de Garante/ fiador</td>
                <td style="background-color: #efefef !important;">P.Natural</td>
                <td style="background-color: #efefef !important;">
                  <input 
                         type="text" valida_input_vacio 
                         style="padding: 4px;" 
                         onkeyup="total_deudas()" 
                         onkeydown="total_deudas()" 
                         {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" 
                         value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cantidad_pareja_natural : '0.00' }}" 
                         id="cantidad_pareja_natural">
                </td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">P.Jurídica</td>
                <td style="background-color: #efefef !important;">
                  <input 
                         type="text" valida_input_vacio 
                         style="padding: 4px;" 
                         onkeyup="total_deudas()" 
                         onkeydown="total_deudas()"
                         {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" 
                         value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cantidad_pareja_juridico : '0.00' }}" 
                         id="cantidad_pareja_juridico">
                </td>
              </tr>
              <tr>
                <td class="color_totales" style="background-color: #c8c8c8 !important;text-align: right;" colspan=2>TOTAL S/.</td>
                <td class="color_totales" style="background-color: #c8c8c8 !important;">
                  <input 
                         type="text" valida_input_vacio
                         style="padding: 4px;" 
                         disabled class="form-control campo_moneda" 
                         value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->total_deuda : '0.00' }}" 
                         id="total_deuda">
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <br>
      @endif
      <div class="row" container-garantias-aval>
        <div class="col-sm-12">
          <table class="table table-bordered" id="table-garantia-aval">
            <thead>
              <tr>
                <th rowspan="2">Garantías presentadas por el Aval</th>
                <th colspan="2">Saldo de Prést. vigente (S/)</th>
                <th rowspan="2">Propuesta</th>
                <th rowspan="2">Descripción de garantía en Propuesta</th>
                <th rowspan="2">Valor de mercado (S/.)</th>
                <th rowspan="2">Valor comercial (Tasado) (S/.)</th>
                <th rowspan="2">Valor de realización(tasado) (S/.)</th>
              </tr>
              <tr>
                <th style="width:90px;">Propio</th>
                <th style="width:90px;">Avalado</th>
              </tr>
            </thead>
            <tbody>
              @php
                $garantia_cliente_aval = 0;
              @endphp             
              @forelse($credito_garantias_aval as $key => $value)
                <?php
                  
                  $monto_anterior_aval = DB::table('credito_garantia')
                                              ->join('credito','credito.id','credito_garantia.idcredito')
                                              ->where('credito_garantia.idgarantias',0)
                                              ->where('credito_garantia.idcliente',$value->idcliente)
                                              ->where('credito_garantia.idgarantias_noprendarias',$value->idcliente)
                                              ->where('credito_garantia.idcredito','<>',$value->idcredito)
                                              ->orderby('credito_garantia.id','desc')
                                              ->sum('credito.monto_solicitado');
                  $td_propuesta = '';
                  if($key == 0){
                    $monto_propuesta = $credito->monto_solicitado;
                    $cantidad_filas = count($credito_garantias_aval);
                    $td_propuesta = '<td rowspan="'.$cantidad_filas.'" class="text-center">
                                      <input type="hidden" disabled class="form-control campo_moneda" id="propuesta_general_aval" value="'.$monto_propuesta.'">
                                      <b>'.$monto_propuesta.'</b>
                                    </td>';
                  }
              
                ?>
                @php
                  $propuesta_aval = $value->tipo_garantia_no_prendaria == 1 ? $value->valor_realizacion_garantia : $value->valor_mercado_garantia;
                  $garantia_cliente_aval = $monto_anterior_aval + $propuesta_aval;
                @endphp
                <tr sumar_garantia>
                  <td>{{ $value->nombretipogarantia }}</td>
                  <td><input type="text" class="form-control campo_moneda" monto_garantia_cliente disabled value="0.00"></td>
                  <td><input type="text" class="form-control campo_moneda" monto_garantia_cliente disabled value="0.00"></td>
                  <?php echo $td_propuesta; ?>
                  <td>{{ $value->descripcion_garantia }}</td>
                  
                  <td>{{ $value->valor_mercado_garantia }}</td>
                  <td>{{ $value->valor_comercial_garantia }}</td>
                  <td>{{ $value->valor_realizacion_garantia }}</td>
                </tr>
              @empty
              <tr sumar_garantia>
                  <td>Sin Garantia</td>
                  <td><input type="text" class="form-control campo_moneda" monto_garantia_cliente disabled value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->saldo_noprendario_aval : '0.00' }}" onkeyup="cal_total_garantia_aval()" id="saldo_noprendario_aval"></td>
                <td class="color_totales"><input type="text" disabled class="form-control campo_moneda" monto_garantia_cliente id="propuesta_general_aval" value="{{ $credito->monto_solicitado }}"></td>
                </tr>
              @endforelse
              
              <tr>
                <td class="color_totales campo_moneda">TOTAL S/.</td>
                <td class="color_totales" colspan=3><input type="text" disabled class="form-control campo_moneda text-center" id="total_garantia_aval" value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->total_garantia_aval : $garantia_cliente_aval }}"></td>
                <td class="color_totales" colspan=5></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">
          {{ $credito->idevaluacion == 1 ? '6.3':($users_prestamo->idfuenteingreso == 2?'7.3':'9.3') }} VINCULACIÓN POR RIESGO ÚNICO</span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <p style="margin-bottom: 5px;">Deudores Vinculados con los que conforma Riesgo Único (Revisar Reporte, de existir Vinculación registrar)<br>
            <b style="background-color:yellow">REGISTRAR SALDO DE PRÉSTAMO VIGENTE</b>
          </p>
          <table class="table table-bordered" id="table-vinculo-deudor">
            <thead>
              <tr>
                <th rowspan=3 style="width:100px;">DNI CLIENTE</th>
                <th rowspan=3>APELLIDOS Y NOMBRES</th>
                <th colspan=5 style="text-align:center;">FORMA DE VINCULACIÓN</th>
                
                <th rowspan=3 style="width:100px;">{{ $tienda->nombre }} </th>
                  @if($view_detalle!='false')
                  <th rowspan=3 style="width:10px;"><button type="button" class="btn btn-success" onclick="agrega_deudor_vinculado()"><i class="fa fa-plus"></i></button></th>
                  @endif
              </tr>
              <tr>
                
                <th colspan=2 style="text-align:center;">Por propiedad Directa</th>
                <th colspan=2 style="text-align:center;">Por Propiedad Indirecta</th>
                <th>Gestión</th>
              </tr>
              <tr>
                
                <th style="width:100px;">Pertenece al Cliente (%)</th>
                <th style="width:100px;">Pertenece al Vinculado (%)</th>
                <th style="width:100px;">Pertenece al Cliente (%)</th>
                <th style="width:100px;">Pertenece al Vinculado (%)</th>
                <th>Textual</th>
              </tr>
            </thead>
            <tbody>
              @foreach($vinculacion_deudor as $value)
                <tr >
                  <td codigo><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" value="{{ $value->codigo }}" onkeyup="persona_riesgo(this)"></td>
                  <td cliente><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" value="{{ $value->cliente }}"></td>
                  <td cliente_propiedad_directa><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" value="{{ $value->cliente_propiedad_directa }}"></td>
                  <td vinculado_propiedad_directa><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" value="{{ $value->vinculado_propiedad_directa }}"></td>
                  <td cliente_propiedad_indirecta><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" value="{{ $value->cliente_propiedad_indirecta }}"></td>
                  <td vinculado_propiedad_indirecta><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" value="{{ $value->vinculado_propiedad_indirecta }}"></td>
                  <td gestion>
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      <option value=""></option>
                      <option value="Gerente"       {{ $value->gestion == "Gerente"       ? "selected" : "" }}>Gerente</option>
                      <option value="Administrador" {{ $value->gestion == "Administrador" ? "selected" : "" }}>Administrador</option>
                      <option value="Directivo"     {{ $value->gestion == "Directivo"     ? "selected" : "" }}>Directivo</option>
                      <option value="Trabajador"    {{ $value->gestion == "Trabajador"    ? "selected" : "" }}>Trabajador</option>
                      <option value="Otros"         {{ $value->gestion == "Otros"         ? "selected" : "" }}>Otros</option>
                    </select>
                  </td>
                  <td saldo><input type="text" valida_input_vacio {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" onkeyup="calcular_total()" value="{{ $value->saldo }}"></td>
           
                  @if($view_detalle!='false')
                  <td><button type="button" onclick="eliminar_deudor(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                  @endif
                 </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td class="color_totales campo_moneda" colspan=7>TOTAL S/.</td>
                <td class="color_totales"><input type="text" disabled class="form-control campo_moneda" value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->total_vinculo_deudor : '0.00' }}" id="total_vinculo_deudor"></td>
              </tr>
            </tfoot>
          </table>
          <script>
            function persona_riesgo(e){
    
              let tr = $(e).closest('tr');
              let dni = $(e).val();
              
              if( dni.length === 8 ){
                $.ajax({
                  url:"{{url('backoffice/0/credito/showpersona')}}",
                  type:'GET',
                  data: {
                      dni: dni,
                  },
                  success: function (res){
                    $(tr).find('td[cliente] input').val(res.nombrecompleto);
                  }
                })
              }
              
             }
          </script>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">
          {{ $credito->idevaluacion == 1 ? '6.4':($users_prestamo->idfuenteingreso == 2?'7.4':'9.4') }} DETERMINACIÓN DE LIMITES</span>
      </div>
      <div class="row">
        <div class="col-sm-42">
          <table class="table" style="width:500px !important;">
            <tr class="d-none">
              <td>Capital Asignado</td>
              <td width="100px"><input type="text" disabled class="form-control campo_moneda" value="{{ $tienda->capital_agencia }}" id="reporte_institucional"></td>
              <td width="300px"><input type="text" disabled class="form-control campo_moneda" value="{{ configuracion($tienda->id,'capital_asignado')['valor'] }}" id="capital_asignado"></td>
              

            </tr>
            <tr>
              <td colspan="2">Total financiado al Deudor y Deudores vinculados (Incluido propuesta) ( S/.)</td>
              <td><input type="text" class="form-control campo_moneda" value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->total_financiado_deudor : '0.00' }}" disabled id="total_financiado_deudor"></td>
            </tr>
            <tr>
              <td>Resultado (%)</td>
              <td><input type="text" class="form-control campo_moneda" style="width:60px;"
                         value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->porcentaje_resultado : '0.00' }}" disabled id="porcentaje_resultado"></td>
              <td><input type="text" class="form-control text-center" value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->estado_resultado : '0.00' }}" disabled id="estado_resultado"></td>
            </tr>
          </table>
          <script>
            determina_resultado();
            function determina_resultado(){
              let saldo_capital_empresa = parseFloat("{{$saldo_capital_empresa}}");
              let saldo_deducciones_empresa = parseFloat("{{$saldo_deducciones_empresa}}");
              let credito_solicitado = parseFloat("{{$credito->monto_solicitado}}");
              
              let total_vinculo_deudor = parseFloat($('#total_vinculo_deudor').val());
              let total_financiado_deudor = ( total_vinculo_deudor + credito_solicitado + saldo_capital_empresa ) - saldo_deducciones_empresa;
              
              //console.log(total_vinculo_deudor,credito_solicitado,saldo_capital_empresa,saldo_deducciones_empresa)
              
              $('#total_financiado_deudor').val(total_financiado_deudor.toFixed(2))
              
              
              let reporte_institucional = parseFloat($('#reporte_institucional').val());
              let porcentaje_resultado = (total_financiado_deudor/reporte_institucional) * 100;
              $('#porcentaje_resultado').val(porcentaje_resultado.toFixed(2))
              
              let capital_asignado = parseFloat($('#capital_asignado').val());
              //capital_asignado = capital_asignado*100;
              //console.log(porcentaje_resultado,capital_asignado)
              $('#estado_resultado ').val('Suspender Propuesta');
              $('#estado_resultado ').removeClass('bg-success');
              $('#estado_resultado ').addClass('bg-danger');
              
              if(porcentaje_resultado <= capital_asignado ){
                $('#estado_resultado ').val('Continuar Propuesta');
                $('#estado_resultado ').removeClass('bg-danger');
                $('#estado_resultado ').addClass('bg-success');
                //$('#btn-guardar-cambios').attr('disabled',false)
              }else{
                //$('#btn-guardar-cambios').attr('disabled',true)
              }
              
            }
            // $saldo_capital_empresa
            // $saldo_deducciones_empresa
          </script>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">
          {{ $credito->idevaluacion == 1 ? '6.5':($users_prestamo->idfuenteingreso == 2?'7.5':'9.5') }} COMENTARIOS SOBRE LA VINCULACIÓN</span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <textarea id="comentarios"  {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" cols="30" rows="3">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->comentarios : '' }}</textarea>
        </div>
      </div>
      <div class="row mt-1">
        @if($view_detalle!='false')
        <div class="col" style="flex: 0 0 0%;">
          <button type="submit" class="btn btn-success" id="btn-guardar-cambios"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
        </div>
        @endif
        <div class="col" style="flex: 0 0 0%;">
          <button type="button" 
                  class="btn btn-dark" 
                  onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=solicitud_control_limites')}}', size: 'modal-fullscreen' })"
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
  @if($credito->idaval == '0')
    $('div[container-garantias-aval]').find('input').attr('disabled',true)
    $('div[container-garantias-aval]').addClass('d-none');
  @endif
  $('input').on('blur', function() {
      $('#boton_imprimir').attr('disabled',true);
  });
  
  function cal_total_garantia_cliente(){
    let total_garantia = 0;
    let propuesta = parseFloat($('#propuesta_general').val());
    
    $(`#table-garantia-cliente > tbody > tr[sumar_garantia]`).each(function() {
      let monto = $(this).find('td input[monto_garantia_cliente]').val();
      total_garantia += parseFloat(monto);
    });
    total_garantia = total_garantia + propuesta;
    $('#total_garantia_cliente').val(total_garantia.toFixed(2));
  }
  cal_total_garantia_cliente()
  
  cal_total_garantia_aval()
  function cal_total_garantia_aval(){
    let total_garantia = 0;
    let propuesta = parseFloat($('#propuesta_general_aval').val());
    $(`#table-garantia-aval > tbody > tr[sumar_garantia]`).each(function() {
      let monto = $(this).find('td input[monto_garantia_cliente]').val();
      total_garantia += parseFloat(monto);
    });
    total_garantia = total_garantia + propuesta;
    $('#total_garantia_aval').val(total_garantia.toFixed(2));
  }
  function total_deudas() {
    var cantidad_garante_natural = parseFloat($('#cantidad_garante_natural').val());
    var cantidad_garante_juridico = parseFloat($('#cantidad_garante_juridico').val());
    var cantidad_pareja_natural = parseFloat($('#cantidad_pareja_natural').val());
    var cantidad_pareja_juridico = parseFloat($('#cantidad_pareja_juridico').val());
    $('#total_deuda').val(cantidad_garante_natural+cantidad_garante_juridico+cantidad_pareja_natural+cantidad_pareja_juridico);
  }  
  function agrega_deudor_vinculado(){
    let btn_eliminar = `<button type="button" onclick="eliminar_deudor(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button>` ;
    let tabla = `<tr >
                  <td codigo><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" onkeyup="persona_riesgo(this)"></td>
                  <td cliente><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" readonly></td>
                  <td cliente_propiedad_directa><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto"></td>
                  <td vinculado_propiedad_directa><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto"></td>
                  <td cliente_propiedad_indirecta><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto"></td>
                  <td vinculado_propiedad_indirecta><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto"></td>
                  <td gestion>
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      <option></option>
                      <option value="Gerente">Gerente</option>
                      <option value="Administrador">Administrador</option>
                      <option value="Directivo">Directivo</option>
                      <option value="Trabajador">Trabajador</option>
                      <option value="Otros">Otros</option>
                    </select>
                  </td>
                  <td saldo><input type="text" valida_input_vacio value="0.00" onkeyup="calcular_total()" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto"></td>
                  {{ $view_detalle=='false' ? '' : '<td>${btn_eliminar}</td>' }}
                  
                 </tr>`;

      $("#table-vinculo-deudor > tbody").append(tabla);
      calcular_total();
      valida_input_vacio();
    }
  function calcular_total(){
    
    let total = 0;

    $("#table-vinculo-deudor > tbody > tr").each(function() {
      total += parseFloat($(this).find('td[saldo] input').val());
    });
    $('#total_vinculo_deudor').val(total.toFixed(2))
    determina_resultado();
  }
  function eliminar_deudor(e){
    let path = $(e).closest('tr');
    path.remove();
    calcular_total();
  }
  function json_vinculacion_deudor(){
    let data = [];
    $(`#table-vinculo-deudor > tbody > tr`).each(function() {
      let codigo                        = $(this).find('td[codigo] input').val();
      let cliente                       = $(this).find('td[cliente] input').val();
      let cliente_propiedad_directa     = $(this).find('td[cliente_propiedad_directa] input').val();
      let vinculado_propiedad_directa   = $(this).find('td[vinculado_propiedad_directa] input').val();
      let cliente_propiedad_indirecta   = $(this).find('td[cliente_propiedad_indirecta] input').val();
      let vinculado_propiedad_indirecta = $(this).find('td[vinculado_propiedad_indirecta] input').val();
      let gestion                       = $(this).find('td[gestion] select').val();
      let saldo                         = $(this).find('td[saldo] input').val();

      data.push({ 
        codigo: codigo,
        cliente: cliente,
        cliente_propiedad_directa: cliente_propiedad_directa,
        vinculado_propiedad_directa: vinculado_propiedad_directa,
        cliente_propiedad_indirecta: cliente_propiedad_indirecta,
        vinculado_propiedad_indirecta: vinculado_propiedad_indirecta,
        gestion: gestion,
        saldo: saldo,
      });
    });
    return JSON.stringify(data);
  }
</script>