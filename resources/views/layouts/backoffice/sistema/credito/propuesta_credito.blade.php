<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/credito/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'propuesta_credito',
              fenomenos: json_fenomenos(),
              monto_compra_deuda_det: monto_compra_deuda_det()
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
        <h5 class="modal-title">PROPUESTA DE CRÉDITO </h5>
        <button type="button" class="btn-close text-white" id="modal-close-garantia-cliente" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    @php
      $evaluacion_meses = $credito_evaluacion_cuantitativa ? ( $credito_evaluacion_cuantitativa->evaluacion_meses == "" ? [] : json_decode($credito_evaluacion_cuantitativa->evaluacion_meses) ) : [];
      $entidad_regulada = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->entidad_regulada == "" ? [] : json_decode($credito_cuantitativa_deudas->entidad_regulada) ) : [];
      $entidad_noregulada = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->entidad_noregulada == "" ? [] : json_decode($credito_cuantitativa_deudas->entidad_noregulada) ) : [];
      $vinculacion_deudor = $credito_cuantitativa_control_limites ? ( $credito_cuantitativa_control_limites->vinculacion_deudor == "" ? [] : json_decode($credito_cuantitativa_control_limites->vinculacion_deudor) ) : [];
  
      $lista_fenomenos = $credito_propuesta ? ( $credito_propuesta->fenomenos == "" ? [] : json_decode($credito_propuesta->fenomenos) ) : [];
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
            <label class="col-sm-4 col-form-label" style="text-align: right;">TIPO DE CRÉDITO:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $credito->forma_credito_nombre }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">TIPO DE CLIENTE:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $credito->tipo_operacion_credito_nombre }}" disabled>
            </div>
          </div>
          
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">PRODUCTO:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $credito->nombreproductocredito }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">MODALIDAD:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $credito->modalidad_credito_nombre }}" disabled>
            </div>
          </div>
        </div>

        <div class="col-sm-12 col-md-6">
          <div class="row">
            <label class="col-sm-5 col-form-label" style="text-align: right;">NRO SOLICITUD:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="S{{ str_pad($credito->id, 8, '0', STR_PAD_LEFT)  }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-5 col-form-label" style="text-align: right;">CÓD. CLIENTE:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->codigo_cliente }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-5 col-form-label" style="text-align: right;">FECHA:</label>
            <div class="col-sm-7">
              <input type="date" step="any" class="form-control" value="{{ $credito_propuesta ? $credito_propuesta->fecha : date('Y-m-d') }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-5 col-form-label" style="text-align: right;">ASESOR:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->usuario_asesor }}" disabled>
            </div>
          </div>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block">INFORMACIÓN DEL CLIENTE E INGRESO:</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-6">
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
        </div>
        <div class="col-sm-12 col-md-6">
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
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-6">
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">DIRECCIÓN:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $usuario->direccion }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">CONDICIÓN DE VIVIENDA/LOCAL:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ strtoupper($users_prestamo->db_idcondicionviviendalocal) }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">TIPO DE INGRESO PRINCIPAL:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $users_prestamo->idfuenteingreso == 1 ? 'INDEPENDIENTE' : 'DEPENDIENTE' }}" disabled>
            </div>
          </div>
          @if($users_prestamo->idfuenteingreso == 1)
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
          @endif
        </div>
        <div class="col-sm-12 col-md-6">
          <?php
            $suma_saldo = array_sum(array_column(array_filter($entidad_noregulada, function($dato) {
                return $dato->tipo_entidad === true;
            }), 'saldo_capital_origen'));
            $valor_serif = '';
            if( $suma_saldo > 0 ){
                $valor_serif = $credito->nombreclientecredito;
            }
          ?>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Vinculación con:</th>
                <th>{{ $tienda->nombre }} (Saldo Financiado)</th>
              </tr>
            </thead>
            <tbody>
              @if($credito->idevaluacion == 2)
              <?php
              $saldocapital_regulada = 0;
              foreach($entidad_regulada as $value){
                  if($value->tipo_entidad){
                      $saldocapital_regulada = $saldocapital_regulada+$value->saldo_capital;
                  }
              }
              $saldocapital_noregulada = 0;
              foreach($entidad_noregulada as $value){
                  if($value->tipo_entidad){
                      $saldocapital_noregulada = $saldocapital_noregulada+$value->saldo_capital;
                  }
              }
              $saldocapital = $saldocapital_regulada+$saldocapital_noregulada
              ?>
              <tr>
                <td><input type="text" class="form-control" disabled value="{{ $valor_serif }}"></td>
                <td style="width:100px;" ><input type="text" class="form-control" disabled value="{{ number_format($saldocapital, 2, '.', '') }}" style="text-align: right;"></td>
              </tr>
              @endif
              @foreach($vinculacion_deudor as $value)
              @if($value->cliente!='')
              <tr>
                <td><input type="text" class="form-control" disabled value="{{ $value->cliente }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $value->saldo }}" style="text-align: right;"></td>
              </tr>
              @endif
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block">PROPUESTA DE CRÉDITO:</span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <table class="table">
            <tbody>
              <tr>
                <td style="text-align: right;">Monto a Financiar:</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="monto_financiar" value="{{ $credito->monto_solicitado }}"></td>
                <td style="text-align: right;">Días de Gracia:</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="dias_de_gracia" value="{{ $credito->dia_gracia }}"></td>
                <td style="text-align: right;">Cuota de Pago</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="propuesta_total_pagar" value="{{ $credito->cuota_pago }}"></td>
              </tr>
              <tr>
                <td style="text-align: right;">TEM(%):</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="propuesta_tem" value="{{ $credito->tasa_tem }}"></td>
                <td style="text-align: right;">F. Pago:</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="nombre_forma_pago_credito" value="{{ $credito->forma_pago_credito_nombre }}"></td>
                <td style="text-align: right;">Plazo:</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="propuesta_cuotas" value="{{ $credito->cuotas }}"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-sm-12 col-md-6">
          <div class="mb-1 mt-2">
            <span class="badge d-block">CLIENTE:</span>
          </div>
          <table class="table table-bordered" id="table-garantia-cliente">
            <thead>
              <tr>
                <th>Garantías</th>
                <th>Descripción de garantía en Propuesta</th>
                <th>Valor de mercado (S/.)</th>
                <th>Valor comercial (Tasado) (S/.)</th>
                <th>Valor de realización(tasado) (S/.)</th>
              </tr>
            </thead>
            <tbody>
              @forelse($credito_garantias_cliente as $key => $value)
                <tr sumar_garantia>
                  <td>{{ $value->garantias_noprendarias_tipo_garantia_noprendaria }}</td>
                  <td>{{ $value->descripcion }}</td>
                  <td class="campo_moneda">{{ $value->valor_mercado }}</td>
                  <td class="campo_moneda">{{ $value->valor_comercial }}</td>
                  <td class="campo_moneda">{{ $value->valor_realizacion }}</td>
                </tr>
              @empty
              <tr sumar_garantia>
                <td>Sin Garantia</td>
                <td><input type="text" class="form-control color_cajatexto campo_moneda" disabled monto_garantia_cliente value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->saldo_noprendario_cliente : '0.00' }}" onkeyup="cal_total_garantia_cliente()" id="saldo_noprendario_cliente"></td>
                <td><input type="text" disabled class="form-control campo_moneda" id="propuesta_general" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->propuesta_monto : '0.00' }}"></td>
                <td></td>
                <td></td>
              </tr>
              @endforelse
              
            </tbody>
          </table>
          <br>
          <table>
            <tbody>
              <tr>
                <td>Clasificación NORMAL en S. Fin. últimos 6 meses:</td>
                <td>Cliente</td>
                <td>
                  <select class="form-control color_cajatexto " id="idclasificacion_cliente" {{ $view_detalle=='false' ? 'disabled' : '' }}>
                    @foreach($calificacion_cliente as $value)
                      <option value="{{ $value->id }}" <?php echo $value->id == ( $credito_propuesta ? $credito_propuesta->idclasificacion_cliente : 0 ) ? 'selected' : ''; ?> >{{ $value->nombre }}</option>
                    @endforeach
                  </select>
                </td>
                <td>meses</td>
              </tr>
              <tr>
                <td></td>
                <td>Prja./R. Leg.</td>
                <td>
                  <select class="form-control color_cajatexto " id="idclasificacion_cliente_pareja" {{ $view_detalle=='false' ? 'disabled' : '' }}>
                    @foreach($calificacion_cliente as $value)
                      <option value="{{ $value->id }}" <?php echo $value->id == ( $credito_propuesta ? $credito_propuesta->idclasificacion_cliente_pareja : 0 ) ? 'selected' : ''; ?>>{{ $value->nombre }}</option>
                    @endforeach
                  </select>
                </td>
                <td>meses</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-sm-12 col-md-6">
          <div class="mb-1 mt-2">
            <span class="badge d-block">AVAL:</span>
          </div>
          <table class="table table-bordered" id="table-garantia-aval">
            <thead>
              <tr>
                <th>Garantías</th>
                
                <th>Descripción de garantía en Propuesta</th>
                <th>Valor de mercado (S/.)</th>
                <th>Valor comercial (Tasado) (S/.)</th>
                <th>Valor de realización(tasado) (S/.)</th>
              </tr>
            </thead>
            <tbody>           
              @forelse($credito_garantias_aval as $key => $value)
                <tr >
                  <td>{{ $value->garantias_noprendarias_tipo_garantia_noprendaria }}</td>
                  <td>{{ $value->descripcion }}</td>
                  
                  <td class="campo_moneda">{{ $value->valor_mercado }}</td>
                  <td class="campo_moneda">{{ $value->valor_comercial }}</td>
                  <td class="campo_moneda">{{ $value->valor_realizacion }}</td>
                </tr>
              @empty
              <tr sumar_garantia>
                  <td>Sin Garantia</td>
                  <td><input type="text" class="form-control campo_moneda" monto_garantia_cliente disabled value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->saldo_noprendario_aval : '0.00' }}" onkeyup="cal_total_garantia_aval()" id="saldo_noprendario_aval"></td>
                <td class="color_totales"><input type="text" disabled class="form-control campo_moneda" monto_garantia_cliente id="propuesta_general_aval" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->propuesta_monto : '0.00' }}"></td>
                </tr>
              @endforelse
            </tbody>
          </table>
          <br>
          <table class="table table-bordered" id="table-garantia-aval">
            <tbody>           
                <tr>
                  <th style="width:200px;">A. y N. de Aval(Garante)/Fiador:</th>
                  <td>{{ $credito->nombreavalcredito }}</td>
                  <th style="width:50px;">DNI:</th>
                  <td style="width:90px;">{{ $credito->documentoaval }}</td>
                </tr>    
                @if($users_prestamo_aval!='')
                    @if($users_prestamo_aval->dni_pareja!='' or $users_prestamo_aval->nombrecompleto_pareja!='')
                      <tr>
                        <th>Pareja Aval(Garante)/Fiador:</th>
                        <td>{{ $users_prestamo_aval->nombrecompleto_pareja }}</td>
                        <th>DNI:</th>
                        <td>{{ $users_prestamo_aval->dni_pareja }}</td>
                      </tr>
                    @endif
                @endif
            </tbody>
          </table>
          <br>
          <table>
            <tbody>
              <tr>
                <td>Clasificación NORMAL en S. Fin. últimos 6 meses:</td>
                <td>Cliente</td>
                <td>
                  <select class="form-control color_cajatexto " id="idclasificacion_aval" {{ $view_detalle=='false' ? 'disabled' : '' }}>
                    @foreach($calificacion_cliente as $value)
                      <option value="{{ $value->id }}" <?php echo $value->id == ( $credito_propuesta ? $credito_propuesta->idclasificacion_aval : 0 ) ? 'selected' : ''; ?>>{{ $value->nombre }}</option>
                    @endforeach
                  </select>
                </td>
                <td>meses</td>
              </tr>
              <tr>
                <td></td>
                <td>Prja.</td>
                <td>
                  <select class="form-control color_cajatexto " id="idclasificacion_aval_pareja" {{ $view_detalle=='false' ? 'disabled' : '' }}>
                    @foreach($calificacion_cliente as $value)
                      <option value="{{ $value->id }}" <?php echo $value->id == ( $credito_propuesta ? $credito_propuesta->idclasificacion_aval_pareja : 0 ) ? 'selected' : ''; ?>>{{ $value->nombre }}</option>
                    @endforeach
                  </select>
                </td>
                <td>meses</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block">DESTINO DEL CRÉDITO: </span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-12">
          <table class="table">
            <tbody>
              <tr>
                <td style="width:80px">Destino:</td>
                <td colspan="2">
                  <input type="text" class="form-control" disabled id="tipo_destino_credito_nombre" 
                         value="{{ $credito->tipo_destino_credito_nombre}}"></td>
                <td style="width:100px">
                  <input type="text" class="form-control campo_moneda" disabled id="monto_destino_credito" 
                         value="{{ $credito->monto_solicitado }}"></td>
                <td style="width:100px">Detalle:</td>
                <td>
                    @if($credito->idevaluacion == 1)
                    <input type="text" class="form-control" disabled id="detalle_destino_credito" 
                           value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->detalle_destino_prestamo : '' }}">
                    @else
                    <input type="text" class="form-control" disabled id="detalle_destino_credito" 
                           value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->detalle_destino_prestamo : '' }}">
                    @endif
                    
                </td>
              </tr>
                  <?php
                  $saldo_prestamo_vigente_propio = DB::table('credito')
                      ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                      ->where('credito.idcliente',$credito->idcliente)
                      ->where('credito.estado','DESEMBOLSADO')
                      ->where('credito.idestadocredito',1)
                      ->select(
                          'credito.*',
                          'credito_prendatario.nombre as nombreproductocredito',
                          'credito_prendatario.modalidad as modalidadproductocredito',
                      )
                      ->distinct()
                      ->get();
                  $i=0;
                  ?>
              @if($credito->idmodalidad_credito==2 && count($saldo_prestamo_vigente_propio)>0)
                  @if($view_detalle=='false')
                      <tr>
                        <?php
                            $monto_compra_deuda_det = json_decode($credito_propuesta->monto_compra_deuda_det,true);
                        ?>
                        <td rowspan="{{count($monto_compra_deuda_det)}}"></td>
                        <td rowspan="{{count($monto_compra_deuda_det)}}">Ampliación de deuda</td>
                        @if($monto_compra_deuda_det!='')
                            @foreach($monto_compra_deuda_det as $value_det)
                                <?php
                                    $credito_det = DB::table('credito')
                                        ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                                        ->where('credito.id',$value_det['idcredito'])
                                        ->select(
                                            'credito.*',
                                            'credito_prendatario.nombre as nombreproductocredito',
                                            'credito_prendatario.modalidad as modalidadproductocredito',
                                        )
                                        ->first();
                                ?>
                                  <td style="width:250px" class="border-td">
                                  <input type="text" 
                                         class="form-control" 
                                         value="C{{ str_pad($credito_det->cuenta, 8, "0", STR_PAD_LEFT) }} - {{$credito_det->nombreproductocredito}}" 
                                         disabled>
                                  </td>
                                  <td style="width:100px" class="border-td">
                                  <input valida_input_vacio type="text" 
                                         class="form-control campo_moneda" 
                                         value="{{ $credito_propuesta ? $credito_propuesta->monto_compra_deuda : '0.00' }}" 
                                         disabled></td>  
                            @endforeach
                        @endif
                        <td rowspan="{{count($monto_compra_deuda_det)}}">Detalle:</td>
                        <td rowspan="{{count($monto_compra_deuda_det)}}" class="border-td">
                              <input type="text" class="form-control" 
                                     value="{{ $credito_propuesta ? $credito_propuesta->detalle_monto_compra_deuda : '' }}" disabled></td>
                    @else
                        <td rowspan="{{count($saldo_prestamo_vigente_propio)==0?1:count($saldo_prestamo_vigente_propio)}}"></td>
                        <td rowspan="{{count($saldo_prestamo_vigente_propio)==0?1:count($saldo_prestamo_vigente_propio)}}" style="width:300px">Ampliación de deuda</td>
                        @foreach($saldo_prestamo_vigente_propio as $value)
                          @if($i==0)
                            <?php

                            // descuento cuota
                            $credito_descuentocuotas = DB::table('credito_descuentocuota')
                                  ->where('credito_descuentocuota.idcredito',$value->id)
                                  ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                                  ->first();
                            $total_descuento_capital = 0; 
                            $total_descuento_interes = 0; 
                            $total_descuento_comision = 0; 
                            $total_descuento_cargo = 0;  
                            $total_descuento_penalidad = 0; 
                            $total_descuento_tenencia = 0; 
                            $total_descuento_compensatorio = 0; 
                            $total_descuento_total = 0; 
                            if($credito_descuentocuotas){
                                if(1000>=$credito_descuentocuotas->numerocuota_fin){
                                    $total_descuento_capital = $credito_descuentocuotas->capital;
                                    $total_descuento_interes = $credito_descuentocuotas->interes;
                                    $total_descuento_comision = $credito_descuentocuotas->comision;
                                    $total_descuento_cargo = $credito_descuentocuotas->cargo;
                                    $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                                    $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                                    $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
                                    $total_descuento_total = $credito_descuentocuotas->total;
                                }
                            }

                            $cronograma = select_cronograma(
                                $tienda->id,
                                $value->id,
                                $value->idforma_credito,
                                $value->modalidadproductocredito,
                                1000,
                                $total_descuento_capital,
                                $total_descuento_interes,
                                $total_descuento_comision,
                                $total_descuento_cargo,
                                $total_descuento_penalidad,
                                $total_descuento_tenencia,
                                $total_descuento_compensatorio
                            );
                            ?>
                            <td style="width:250px">
                              <input type="text" class="form-control" 
                                     value="C{{ str_pad($value->cuenta, 8, "0", STR_PAD_LEFT) }} - {{$value->nombreproductocredito}}" disabled>
                            </td>
                            <td style="width:100px">
                                <?php
                                $monto_compra_deuda_det_monto = '';
                                $monto_compra_deuda_det_check = '';
                                if($credito_propuesta){
                                    $monto_compra_deuda_det = json_decode($credito_propuesta->monto_compra_deuda_det,true);
                                    if($monto_compra_deuda_det!=''){
                                        foreach($monto_compra_deuda_det as $value_det){
                                            if($value_det['idcredito'] == $value->id){
                                                $monto_compra_deuda_det_monto = $value_det['monto_compra_deuda'];
                                                $monto_compra_deuda_det_check = 'checked';
                                            }
                                        }
                                    }
                                }
                                ?>
                                <div class="input-group">
                                  <input valida_input_vacio type="text" 
                                         class="form-control campo_moneda" 
                                         value="{{ $view_detalle=='false' ? $monto_compra_deuda_det_monto :$cronograma['cuota_pendiente'] }}" 
                                         id="monto_compra_deuda{{ $value->id }}" 
                                         disabled>
                                  <div class="input-group-text">
                                    <input class="form-check-input mt-0" 
                                           type="checkbox" 
                                           id="monto_compra_deuda_check"  
                                           value="{{ $view_detalle=='false' ? $monto_compra_deuda_det_monto :$cronograma['cuota_pendiente'] }}" 
                                           num="{{ $value->id }}" 
                                           onclick="calcula_neto_destino_credito()"
                                           <?php echo $monto_compra_deuda_det_check ?>>
                                  </div>
                                </div>
                            </td>    
                          @endif
                        <?php $i++ ?>
                        @endforeach 
                        @if(count($saldo_prestamo_vigente_propio)==0)
                            <td style="width:250px">
                            </td>
                            <td style="width:100px">
                            </td>
                        @endif
                        <td rowspan="{{count($saldo_prestamo_vigente_propio)==0?1:count($saldo_prestamo_vigente_propio)}}">Detalle:</td>
                        <td rowspan="{{count($saldo_prestamo_vigente_propio)==0?1:count($saldo_prestamo_vigente_propio)}}">
                          <input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" id="detalle_monto_compra_deuda" 
                                   value="{{ $credito_propuesta ? $credito_propuesta->detalle_monto_compra_deuda : '' }}"></td>
                      </tr>
                      <?php $ii=0 ?>
                      @if(count($saldo_prestamo_vigente_propio)>1)
                        @foreach($saldo_prestamo_vigente_propio as $value)
                          @if($ii>0)
                            <?php
                            // descuento cuota
                            $credito_descuentocuotas = DB::table('credito_descuentocuota')
                                  ->where('credito_descuentocuota.idcredito',$value->id)
                                  ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                                  ->first();
                            $total_descuento_capital = 0; 
                            $total_descuento_interes = 0; 
                            $total_descuento_comision = 0; 
                            $total_descuento_cargo = 0;  
                            $total_descuento_penalidad = 0; 
                            $total_descuento_tenencia = 0; 
                            $total_descuento_compensatorio = 0; 
                            $total_descuento_total = 0; 
                            if($credito_descuentocuotas){
                                if(1000>=$credito_descuentocuotas->numerocuota_fin){
                                    $total_descuento_capital = $credito_descuentocuotas->capital;
                                    $total_descuento_interes = $credito_descuentocuotas->interes;
                                    $total_descuento_comision = $credito_descuentocuotas->comision;
                                    $total_descuento_cargo = $credito_descuentocuotas->cargo;
                                    $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                                    $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                                    $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
                                    $total_descuento_total = $credito_descuentocuotas->total;
                                }
                            }

                            $cronograma = select_cronograma(
                                $tienda->id,
                                $value->id,
                                $value->idforma_credito,
                                $value->modalidadproductocredito,
                                1000,
                                $total_descuento_capital,
                                $total_descuento_interes,
                                $total_descuento_comision,
                                $total_descuento_cargo,
                                $total_descuento_penalidad,
                                $total_descuento_tenencia,
                                $total_descuento_compensatorio
                            );
                            ?>
                            <tr>
                              <td>
                                <input type="text" class="form-control" value="C{{ str_pad($value->cuenta, 8, "0", STR_PAD_LEFT) }} - {{$value->nombreproductocredito}}" disabled>
                              </td>
                              <td>
                                  <?php
                                  $monto_compra_deuda_det_monto = '';
                                  $monto_compra_deuda_det_check = '';
                                  if($credito_propuesta){
                                      $monto_compra_deuda_det = json_decode($credito_propuesta->monto_compra_deuda_det,true);
                                      if($monto_compra_deuda_det!=''){
                                          foreach($monto_compra_deuda_det as $value_det){
                                              if($value_det['idcredito'] == $value->id){
                                                  $monto_compra_deuda_det_monto = $value_det['monto_compra_deuda'];
                                                  $monto_compra_deuda_det_check = 'checked';
                                              }
                                          }
                                      }
                                  }
                                  ?>
                                <div class="input-group">
                                    <input valida_input_vacio 
                                           type="text" 
                                           class="form-control campo_moneda" 
                                           value="{{ $view_detalle=='false' ? $monto_compra_deuda_det_monto :$cronograma['cuota_pendiente'] }}" 
                                           id="monto_compra_deuda{{ $value->id }}" 
                                           disabled>
                                    <div class="input-group-text">
                                      <input class="form-check-input mt-0" 
                                             type="checkbox" 
                                             id="monto_compra_deuda_check" 
                                             value="{{ $view_detalle=='false' ? $monto_compra_deuda_det_monto :$cronograma['cuota_pendiente'] }}" 
                                             num="{{ $value->id }}" 
                                             onclick="calcula_neto_destino_credito()"
                                             <?php echo $monto_compra_deuda_det_check ?>>
                                    </div>
                                  </div>
                              </td>
                            </tr>
                          @endif
                        <?php $ii++ ?>
                        @endforeach 
                      @endif
                  @endif
              @endif 
              <tr>
                <td></td>
                <td colspan="2">Neto (S/)</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="neto_destino_credito" 
                           value="{{ $credito_propuesta ? (number_format($credito->monto_solicitado - $credito_propuesta->monto_compra_deuda, 2, '.', '')) : '0.00' }}"></td>
                
                <td colspan="2" rowspan="{{count($saldo_prestamo_vigente_propio)}}"></td>
              </tr>
            </tbody>
          </table>
          @if($credito->idmodalidad_credito==2 && count($saldo_prestamo_vigente_propio)>0 && $view_detalle!='false')
          <div id="result_ampliaciondeuda"></div>
          @endif
          <input type="hidden" class="form-control" value="0.00" id="monto_compra_deuda">
        </div>
      </div>
      @if($users_prestamo->idfuenteingreso == 1)
      <div class="mb-1 mt-2">
        <span class="badge d-block">SOBRE EL NEGOCIO:</span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <table class="table">
            <tbody>
              <tr>
                <td>SECTOR ECONÓMICO:</td>
                <td>
                
                @if($credito->idevaluacion == 1)
                <input type="text" class="form-control" 
                           value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->nombretipo_giro_economico : '' }}" disabled>
                @else
                 <input type="text" class="form-control" 
                           value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->nombretipo_giro_economico : '' }}" disabled>
                @endif
                 
                </td>
                <td width="100px"></td>
                <td>Forma de ejercicio:</td>
                <td><input type="text" class="form-control" disabled id="negocio_forma_ejercicio" value="{{ $users_prestamo->db_idforma_ac_economica }}"></td>
                <td>
                  @if($credito->idevaluacion == 2)
                  Otros Ingresos: 
                <?php
                  $negocio_otros_ingresos = '';
                  if($credito_evaluacion_cualitativa!=''){
                  if ($credito_evaluacion_cualitativa->saladario_fijo === "SI") {
                    $negocio_otros_ingresos = "SI";
                  } else if ($credito_evaluacion_cualitativa->alquiler_local === "SI") {
                    $negocio_otros_ingresos = "SI";
                  } else if ($credito_evaluacion_cualitativa->pensionista === "SI") {
                    $negocio_otros_ingresos = "Si";
                  } else if ($credito_evaluacion_cualitativa->otros_negocios === "SI") {
                    $negocio_otros_ingresos = "SI";
                  } else if ($credito_evaluacion_cualitativa->no_tiene === "SI") {
                    $negocio_otros_ingresos = "NO";
                  }
                  }
                  $negocio_cantidad_ventas_altas = 0;
                  $negocio_cantidad_ventas_bajas = 0;
                  if(count($evaluacion_meses)>0){
                  foreach ($evaluacion_meses[1] as $key => $value) {
                    if($key != 'Mes' ){
                      if(floatval($value->value) > 100) {
                        $negocio_cantidad_ventas_altas++;
                      }
                      if(floatval($value->value) < 100) {
                        $negocio_cantidad_ventas_bajas++;
                      }
                    }
                  }
                  }
                  
                ?>
                  @endif
                </td>
                <td>
                  @if($credito->idevaluacion == 2)
                  <input type="text" class="form-control" disabled id="negocio_otros_ingresos" value="{{ $negocio_otros_ingresos }}">
                  @endif
                </td>
              </tr>
              <tr>
                <td>Instalaciones:</td>
                <td>
                  @if($users_prestamo->casanegocio=='SI')
                  <input type="text" class="form-control" disabled id="negocio_instalaciones" 
                           value="Casa/Negocio"></td>
                  @else
                  <input type="text" class="form-control" disabled id="negocio_instalaciones" 
                           value="{{ $users_prestamo->db_idlocalnegocio_ac_economica }}"></td>
                  @endif
                <td></td>
                <td>Número de trabajadores:</td>
                <td>
                
                @if($credito->idevaluacion == 1)
                <input type="text" class="form-control" disabled id="negocio_nro_trabajadores" 
                           value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->nro_trabajador_completo + $credito_evaluacion_resumida->nro_trabajador_parcal : '0' }}">
                @else
                 <input type="text" class="form-control" disabled id="negocio_nro_trabajadores" 
                           value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->nro_trabajador_completo + $credito_evaluacion_cualitativa->nro_trabajador_parcal : '0' }}">
                @endif
                </td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Experiencia como empresario:</td>
                <td>
                
                @if($credito->idevaluacion == 1)
                <input type="text" class="form-control" disabled id="negocio_experiencia_empresario" 
                           value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->experiencia_microempresa : '0' }}">
                @else
                 <input type="text" class="form-control" disabled id="negocio_experiencia_empresario" 
                           value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->experiencia_microempresa : '0' }}">
                @endif
                  
                </td>
                <td>meses</td>
                <td>
                  @if($credito->idevaluacion == 2)
                  Meses de ventas altas:
                  @endif
                </td>
                <td>
                  @if($credito->idevaluacion == 2)
                  <input type="text" class="form-control" disabled id="negocio_cantidad_ventas_altas" 
                         value="{{ $negocio_cantidad_ventas_altas }}">
                  @endif
                </td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Tiempo en el mismo local:</td>
                <td>
                @if($credito->idevaluacion == 1)
                 <input type="text" class="form-control" disabled id="negocio_mismo_local" 
                           value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->tiempo_mismo_local : '0' }}">
                @else
                 <input type="text" class="form-control" disabled id="negocio_mismo_local" 
                           value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->tiempo_mismo_local : '0' }}">
                @endif
                </td>
                <td>meses</td>
                
                <td>
                  @if($credito->idevaluacion == 2)
                  Meses de venta bajas:
                  @endif</td>
                <td>
                  @if($credito->idevaluacion == 2)
                  <input type="text" class="form-control" disabled id="negocio_cantidad_ventas_bajas" 
                         value="{{ $negocio_cantidad_ventas_bajas }}">
                  @endif
                </td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Descripción de la actividad:</td>
                <td colspan=6>
                @if($credito->idevaluacion == 1)
                 <input type="text" class="form-control" disabled id="negocio_descripcion_actividad" 
                           value="{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->descripcion_actividad : '0' }}">
                @else
                 <input type="text" class="form-control" disabled id="negocio_descripcion_actividad" 
                           value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->descripcion_actividad : '0' }}">
                @endif
                </td>
              </tr>
            </tbody>
          </table>
          <table class="table table-bordered" id="table-fenomeno">
            <thead>
              <tr>
                <th width="200px">Afecto a Fenomeno coyuntural</th>
                <th>Descripción</th>
                @if($view_detalle!='false')
                <th width="10px"><button type="button" class="btn btn-success" onclick="agregar_fenomeno()"><i class="fa fa-plus"></i></button></th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach($lista_fenomenos as $value)
                <tr>
                  <td>
                    <select class="form-control" fenomeno {{ $view_detalle=='false' ? 'disabled' : '' }}>
                      @foreach($fenomenos as $fen_value)
                        <option value="{{ $fen_value->id }}" <?php echo $fen_value->id==$value->fenomeno ? 'selected' : ''; ?> >{{ $fen_value->nombre }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} descripcion class="form-control color_cajatexto" value="{{ $value->descripcion }}"></td>
                  @if($view_detalle!='false')
                  <td><button type="button" onclick="eliminar_fenomeno(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                  @endif
                </tr>
              @endforeach
            </tbody>
          </table>
          <script>
            function agregar_fenomeno(){
              let btn_eliminar = `<button type="button" onclick="eliminar_fenomeno(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button>` ;
              let option = ``;
              @foreach($fenomenos as $value)
                option += `<option value="{{ $value->id }}">{{ $value->nombre }}</option>`
              @endforeach
              let tabla = `<tr>
                            <td><select class="form-control" fenomeno>${option}</select></td>
                            <td><input type="text" descripcion class="form-control color_cajatexto"></td>
                            <td>${btn_eliminar}</td>
                          </tr>`;

              $(`#table-fenomeno > tbody`).append(tabla);

            }
            function eliminar_fenomeno(e){
              let path = $(e).closest('tr');
              path.remove();
            }
          </script>
        </div>
      </div>
      @endif
      <script>
      
            function json_fenomenos(){
              let data = [];
              $(`#table-fenomeno > tbody > tr`).each(function() {
                  let fenomeno = $(this).find('td select[fenomeno]').val();
                  let descripcion = $(this).find('td input[descripcion]').val();
                  data.push({ 
                      fenomeno: fenomeno,
                      descripcion: descripcion,
                  });
              });
              return JSON.stringify(data);
            }
      </script>
              <?php
                
                $tem_propuesta = $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->propuesta_tem : 0;
                $rango_menor = configuracion($tienda->id,'rango_menor')['valor'];
                $rango_tope = configuracion($tienda->id,'rango_tope')['valor'];
                $tope_capital_asignado = configuracion($tienda->id,'capital_asignado')['valor'];
                $relacion_couta_ingreso = configuracion($tienda->id,'relacion_couta_ingreso')['valor'];
                $relacion_cuota_venta = configuracion($tienda->id,'relacion_cuota_venta')['valor'];
                // Fila 01
                
                $rentabilidad_negocio = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_negocio : 0;  
                $rentabilidad_negocio_res = "Ver ROS";
                if( $rentabilidad_negocio > 0 && $rentabilidad_negocio > $tem_propuesta){
                  $rentabilidad_negocio_res = "Rentabilidad de Capital de Trabajo Adecuada";
                }
                $rentabilidad_negocio_res_coment = "--";
                if( $rentabilidad_negocio > 0){
                  $rentabilidad_negocio_res_coment = $rentabilidad_negocio;
                }
                // Fila 02
                $rentabilidad_ventas = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_ventas : 0;
                if ($rentabilidad_ventas > $tem_propuesta) {
                    $rentabilidad_ventas_res = "Rendimiento de Ventas Eficientes";
                }elseif($rentabilidad_ventas < $tem_propuesta) {
                    $rentabilidad_ventas_res = "Rendimiento de Ventas Moderados";
                }else{
                    $rentabilidad_ventas_res = 0;
                }
              
                $rentabilidad_ventas_res_coment = 0;
                if( $rentabilidad_ventas > 0 ){
                  $rentabilidad_ventas_res_coment = $rentabilidad_ventas;
                }
                // Fila 03
                $rentabilidad_unidad_familiar = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_unidadfamiliar : 0;
                $rentabilidad_unidad_familiar_res = '';
                if($rentabilidad_unidad_familiar > 1) {
                  $rentabilidad_unidad_familiar_res = "Riesgo Normal - Continuar Propuesta";
                }elseif($rentabilidad_unidad_familiar <= 1) {
                  $rentabilidad_unidad_familiar_res = "Alto Riesgo - Suspender Propuesta";
                }
                if($rentabilidad_unidad_familiar > 1) {
                  $rentabilidad_unidad_familiar_res_coment = "Sus ingresos totales del cliente PUEDE cubrir sus gastos familiares en más de 1 vez";
                }else{
                  $rentabilidad_unidad_familiar_res_coment = "Los ingresos totales del cliente NO son capaces de cubrir sus gastos familiares";
                }
                // Fila 04
                $rentabilidad_patrimonial = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_patrimonial : 0;
                $rentabilidad_activos = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_activos : 0;
                if($rentabilidad_patrimonial > $rentabilidad_activos && $rentabilidad_patrimonial > $credito->tasa_tem) {
                  $rentabilidad_patrimonial_res = "Adecuada Rentabilidad de los fondos propios invertidos en el negociopor INTERESA ENDEUDARSE";
                }else{
                  $rentabilidad_patrimonial_res = "Débil Rentabilidad de los fondos propios invertidos en el negocio NO INTERESA ENDEUDARSE";
                }
                // Fila 05
                if ($rentabilidad_activos < $rentabilidad_patrimonial && $rentabilidad_activos > $credito->tasa_tem) {
                  $rentabilidad_activos_res = "Rentabilidad Adeuado de las inversiones en el negocio, INTERESA ENDEUDARSE";
                } else {
                  $rentabilidad_activos_res = "Rentabilidad Débil de las inversiones en el negocio, NO INTERESA ENDEUDARSE";
                }
                // SOLVENCIA
                // Fila 06
                $solvencia_liquidez = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_liquidez : 0;
                if($solvencia_liquidez > 0 && $solvencia_liquidez >= 1) {
                  $solvencia_liquidez_res = "Solvente (Capacidad) puede asumir sus deudas a corto plazo - Continuar Propuesta";
                }else{
                  $solvencia_liquidez_res = "Insolvencia Técnica - Observar Propuesta";
                }
                // Fila 07
                $solvencia_liquidez_acida = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_liquidez_acida : 0;
                if ($solvencia_liquidez_acida >= 1) {
                    $solvencia_liquidez_acida_res = "CUENTA con Efectivo Inmediato para pagar deudas";
                } else {
                    $solvencia_liquidez_acida_res = "NO cuenta con Efectivo inmediato para Pagar deudas";
                }
                // Fila 08
                $solvencia_endeudamiento_propuesta = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_endeudamiento_propuesta : 0;
                if ($solvencia_endeudamiento_propuesta <= 0.85) {
                    $solvencia_endeudamiento_propuesta_res = "TIENE autonomía financiera - Proceder Propuesta";
                } else {
                    $solvencia_endeudamiento_propuesta_res = "NO tiene autonomía financiera - Observar Propuesta";
                }
                if ($solvencia_endeudamiento_propuesta <= 0.85) {
                    $solvencia_endeudamiento_propuesta_res_coment = "TIENE respaldo patrimonial para asumir la deuda propuesta";
                } else {
                    $solvencia_endeudamiento_propuesta_res_coment = "NO tiene respaldo patrimonial para asumir la deuda propuesta";
                }
                // Fila 09
                
                if($users_prestamo->idfuenteingreso == 2){
                    $solvencia_cuota_total = $credito_formato_evaluacion ? $credito_formato_evaluacion->resultado_cuota_excedente : 0;
                }else{
                    $solvencia_cuota_total = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->excedente_propuesta_con_deduccion : 0 ;
                }
                $stylebackground_solvencia_cuota_total = '';
                if ($solvencia_cuota_total <= $rango_menor && $solvencia_cuota_total>=0) {
                    $solvencia_cuota_total_res = "No evidencia Sobreendeudamiento - Existe Cobertura";
                } else {
                    $solvencia_cuota_total_res = "Evidencia Sobreendeudamiento - No Existe Cobertura";
                    $stylebackground_solvencia_cuota_total = 'color:red;';
                }
                // Fila 10
                $solvencia_capital_trabajo_neto = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_prestamo : 0;
                if ($solvencia_capital_trabajo_neto <= 85) {
                    $solvencia_capital_trabajo_neto_res = "Financiamiento dentro de lo Permisible";
                } else {
                    $solvencia_capital_trabajo_neto_res = "Financiamiento fuera de límite permisible";
                }
               if ($solvencia_capital_trabajo_neto <= 85) {
                    $solvencia_capital_trabajo_neto_res_coment = "Evidencia que se está financiando menos del capital de trabajo neto que tiene";
                } else {
                    $solvencia_capital_trabajo_neto_res_coment = "Muestra que se está financiando más que el capital de trabajo neto del cliente";
                }
                // Fila 11
                $solvencia_capital_trabajo = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_capital : 0 ; 
                // GESTON 
                // Fila 12
                $gestion_rotacion_inventario = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_rotacion_inventario : 0;
                if ($gestion_rotacion_inventario <= 7) {
                    $gestion_rotacion_inventario_res = "Negocio de Rotación MUY ALTA de ventas";
                } elseif ($gestion_rotacion_inventario <= 45) {
                    $gestion_rotacion_inventario_res = "Negocio de Rotación ALTA de ventas";
                } elseif ($gestion_rotacion_inventario <= 90) {
                    $gestion_rotacion_inventario_res = "Negocio de Rotación USUAL de ventas";
                } else {
                    $gestion_rotacion_inventario_res = "Negocio de Rotación LENTA de ventas";
                }
                if ($gestion_rotacion_inventario > 0) {
                    $gestion_rotacion_inventario_res_coment = "El inventario del cliente se renueva cada "; // Puedes reemplazar el punto con un espacio si es necesario
                } else {
                    $gestion_rotacion_inventario_res_coment = "-----";
                }
                // Fila 13
                $gestion_promedio_cobranza = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_promedio_cobranza : 0;
                if ($gestion_promedio_cobranza <= 45) {
                    $gestion_promedio_cobranza_res = "Gestión ADECUADA de sus ventas a crédito";
                } else {
                    $gestion_promedio_cobranza_res = "Gestión INADECUADA de sus ventas a crédito";
                }
                if ($gestion_promedio_cobranza > 0) {
                    $gestion_promedio_cobranza_res_coment = "El cliente realiza el cobro de sus ventas a crédito cada "; // Puedes reemplazar el punto con un espacio si es necesario
                } else {
                    $gestion_promedio_cobranza_res_coment = "-----";
                }
                // Fila 14
                $gestion_promedio_pago = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_primedio_pago : 0;
                if ($gestion_promedio_pago > $gestion_promedio_cobranza) {
                    $gestion_promedio_pago_res = "EXISTE calce a sus obligaciones con su proveedor";
                    $gestion_promedio_pago_res_coment = "Pago a proveedores usualmente puntual";
                } else {
                    $gestion_promedio_pago_res = "NO existe calce a sus obligaciones con su proveedor";
                  $gestion_promedio_pago_res_coment = "Pago a proveedores con retrasos";
                }
                // LIMITES
                // Fila 15
                $limites_financiamiento_vru = $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->porcentaje_resultado : 0;
                $limites_financiamiento_vru_res = ($limites_financiamiento_vru <= $tope_capital_asignado) ? "Dentro del límite permisible - Proceder propuesta" : "Fuera de límite permisible - Suspender propuesta";
                
                ############## RESULTADO EVALUACION RESUMIDA #############
                $validadar_resultado = 0;
                // SOLVENCIA 
                // Fila 01
                $res_solvencia_relacion_cuota_style = '';
                $res_solvencia_relacion_cuota = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->indicador_solvencia_cuotas : 0;
                if ($res_solvencia_relacion_cuota > 0 && $res_solvencia_relacion_cuota <= $rango_tope) {
                    $res_solvencia_relacion_cuota_res = "No evidencia Sobreendeudamiento EXISTE COBERTURA";
                } elseif ($res_solvencia_relacion_cuota > $rango_tope) {
                    $res_solvencia_relacion_cuota_res = "Evidencia Sobreendeudamiento NO EXISTE COBERTURA";
                    $res_solvencia_relacion_cuota_style = 'color:red;';
                    $validadar_resultado++;
                } elseif ($res_solvencia_relacion_cuota <= 0) {
                    $res_solvencia_relacion_cuota_res = "Sobreendeudamiento NO TIENE EXCEDENTE";
                    $res_solvencia_relacion_cuota_style = 'color:red;';
                    $validadar_resultado++;
                } else {
                    $res_solvencia_relacion_cuota_res = 0;
                }
                // OTROS RATIOS
                // Fila 02
                $res_ratios_cuota_ingreso_mensual_style = '';
                $res_ratios_cuota_ingreso_mensual = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_mensual : 0;
                if ($res_ratios_cuota_ingreso_mensual <= $relacion_couta_ingreso) {
                    $res_ratios_cuota_ingreso_mensual_res = "Dentro del rango establecido";
                } elseif ($res_ratios_cuota_ingreso_mensual > $relacion_couta_ingreso) {
                    $res_ratios_cuota_ingreso_mensual_res = "Fuera del rango establecido";
                    $res_ratios_cuota_ingreso_mensual_style = 'color:red;';
                    $validadar_resultado++;
                }
                if ($res_ratios_cuota_ingreso_mensual <= $relacion_couta_ingreso) {
                    $res_ratios_cuota_ingreso_mensual_res_coment = "VIABLE con segunda opción, para cumplir cuotas de pago a muy corto plazo";
                } elseif ($res_ratios_cuota_ingreso_mensual > $relacion_couta_ingreso) {
                    $res_ratios_cuota_ingreso_mensual_res_coment = "NO VIABLE con segunda opción, para cumplir cuotas de pago a muy corto plazo";
                } else {
                    $res_ratios_cuota_ingreso_mensual_res_coment = 0;
                }
                // Fila 03
                $res_ratios_venta_cuota_diaria_style = '';
                $res_ratios_venta_cuota_diaria = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_venta_diaria : 0;
                if ($res_ratios_venta_cuota_diaria <= $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_diaria_res = "Dentro del rango establecido";
                } elseif ($res_ratios_venta_cuota_diaria > $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_diaria_res = "Fuera del rango establecido";
                    $res_ratios_venta_cuota_diaria_style = 'color:red;';
                    $validadar_resultado++;
                } else {
                    $res_ratios_venta_cuota_diaria_res = 0;
                }
              
                if ($res_ratios_venta_cuota_diaria <= $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_diaria_res_coment = "VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
                } elseif ($res_ratios_venta_cuota_diaria > $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_diaria_res_coment = "NO VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
                }
                // Fila 04
                $res_ratios_venta_cuota_semanal_style = '';
                $res_ratios_venta_cuota_semanal = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_venta_semanal : 0;
                if ($res_ratios_venta_cuota_semanal <= $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_semanal_res = "Dentro del rango establecido";
                } elseif ($res_ratios_venta_cuota_semanal > $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_semanal_res = "Fuera del rango establecido";
                    $res_ratios_venta_cuota_semanal_style = 'color:red;';
                } else {
                    $res_ratios_venta_cuota_semanal_res = 0;
                }
              
                if ($res_ratios_venta_cuota_semanal <= $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_semanal_res_coment = "VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
                } elseif ($res_ratios_venta_cuota_semanal > $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_semanal_res_coment = "NO VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
                }
                // Fila 05
                $res_ratios_venta_cuota_quincenal_style = '';
                $res_ratios_venta_cuota_quincenal = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_venta_quincenal : 0;
                if ($res_ratios_venta_cuota_quincenal <= $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_quincenal_res = "Dentro del rango establecido";
                } elseif ($res_ratios_venta_cuota_quincenal > $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_quincenal_res = "Fuera del rango establecido";
                    $res_ratios_venta_cuota_quincenal_style = 'color:red;';
                } else {
                    $res_ratios_venta_cuota_quincenal_res = 0;
                }
                if ($res_ratios_venta_cuota_quincenal <= $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_quincenal_res_coment = "VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
                } elseif ($res_ratios_venta_cuota_quincenal > $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_quincenal_res_coment = "NO VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
                }
                // Fila 06
                $res_ratios_venta_cuota_mensual_style = '';
                $res_ratios_venta_cuota_mensual = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_venta_mensual : 0;
                if ($res_ratios_venta_cuota_mensual <= $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_mensual_res = "Dentro del rango establecido";
                } elseif ($res_ratios_venta_cuota_mensual > $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_mensual_res = "Fuera del rango establecido";
                    $res_ratios_venta_cuota_mensual_style = 'color:red;';
                } else {
                    $res_ratios_venta_cuota_mensual_res = 0;
                }
                if ($res_ratios_venta_cuota_mensual <= $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_mensual_res_coment = "VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
                } elseif ($res_ratios_venta_cuota_mensual > $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_mensual_res_coment = "NO VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
                }
                // Fila 07
              ?>
      @if($credito->idevaluacion == 2)
      <div class="mb-1 mt-2">
        <span class="badge d-block">RESULTADOS DE EVALUACIÓN :</span>
      </div>
      <div class="row"> 
        <div class="col-sm-12">
          <table class="table">
            <thead>
              <tr>
                <th width="200px">Indicadores</th>
                <th width="10px"></th>
                <th width="70px">Ratios</th>
                <th>Resultado</th>
                <th colspan=2>Comentarios</th>
                <th width="210px">Exigencias/Particularidades</th>
              </tr>
            </thead>
            <tbody>
              
            @if($users_prestamo->idfuenteingreso == 1)
              <tr>
                <th colspan=7><b>RENTABILIDAD</b></th>
              </tr>
              <tr>
                <td>Rentabilidad del negocio</td>
                <td>%</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="rentabilidad_negocio" value="{{ $rentabilidad_negocio }}"></td>
                <td><div class="cuadro-input">{{ $rentabilidad_negocio_res }}</div></td>
                <td  colspan="2"><div class="cuadro-input">Por cada sol invertido gana {{ $rentabilidad_negocio_res_coment }}%</div></td>
                <td><div class="cuadro-input">Giros de alta rotación o servicios puede ser (-), usar ROS</div></td>
              </tr>
              <tr>
                <td>Rentabilidad de las ventas (ROS)</td>
                <td>%</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="rentabilidad_ventas" value="{{ $rentabilidad_ventas }}"></td>
                <td><div class="cuadro-input">{{ $rentabilidad_ventas_res }}</div></td>
                <td colspan="2"><div class="cuadro-input">Su ganancia mensual por su venta es {{ $rentabilidad_ventas_res_coment }}%</div></td>
                <td><div class="cuadro-input">Se sugiere ROS>TEM</div></td>
              </tr>
              <tr>
                <td>Rentabilidad de la unidad familiar</td>
                <td>Veces</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="rentabilidad_unidad_familiar" value="{{ $rentabilidad_unidad_familiar }}"></td>
                <td><div class="cuadro-input">{{ $rentabilidad_unidad_familiar_res }}</div></td>
                <td colspan=2><div class="cuadro-input">{{ $rentabilidad_unidad_familiar_res_coment }}</div></td>
                <td><div class="cuadro-input">Se espera >1</div></td>
              </tr>
              <tr>
                <td>Rentabilidad patrimonial (ROE)</td>
                <td>%</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="rentabilidad_patrimonial" value="{{ $rentabilidad_patrimonial }}"></td>
                <td><div class="cuadro-input">{{ $rentabilidad_patrimonial_res }}</div></td>
                <td colspan=2><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" id="rentabilidad_patrimonial_res_coment" 
                                     value="{{ $credito_propuesta ? $credito_propuesta->rentabilidad_patrimonial_res_coment : '' }}"></td>
                <td><div class="cuadro-input">SI:  ROA>TEM → ROE>ROA. Endeudamiento tiene efecto apalancamiento (+) o amplificador</div></td>
              </tr>
              <tr>
                <td>Rentabilidad de los activos (ROA)</td>
                <td>%</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="rentabilidad_activos" value="{{ $rentabilidad_activos }}"></td>
                <td><div class="cuadro-input">{{ $rentabilidad_activos_res }}</div></td>
                <td colspan=2><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" id="rentabilidad_activos_res_coment" 
                                     value="{{ $credito_propuesta ? $credito_propuesta->rentabilidad_activos_res_coment : '' }}"></td>
                <td><div class="cuadro-input">Si: ROA&lt;TEM → ROE&lt;ROA. Endeudamiento tiene efecto apalancamiento (–) o reductor</div></td>
              </tr>
            @endif
              <tr>
                <th colspan=7><b>SOLVENCIA</b></th>
              </tr>
            
            @if($users_prestamo->idfuenteingreso == 1)
              <tr>
                <td>Liquidez</td>
                <td>Veces</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="solvencia_liquidez" value="{{ $solvencia_liquidez }}"></td>
                <td><div class="cuadro-input">{{ $solvencia_liquidez_res }}</div></td>
                <td colspan="2"><div class="cuadro-input">Por cada sol de obligaciones cuenta con S/ {{ $solvencia_liquidez }} para pagar en el corto plazo </div></td>
                <td><div class="cuadro-input">Se exije >1</div></td>
              </tr>
              <tr>
                <td>Liquidez Ácida</td>
                <td>Veces</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="solvencia_liquidez_acida" value="{{ $solvencia_liquidez_acida }}"></td>
                <td><div class="cuadro-input">{{ $solvencia_liquidez_acida_res }}</div></td>
                <td colspan="2"><div class="cuadro-input">Por cada sol de obligaciones cuenta de inmediato S/ {{ $solvencia_liquidez_acida }} para pagar en muy corto plazo</div></td>
                <td><div class="cuadro-input">Óptimo >1</div></td>
              </tr>
              <tr>
                <td>Endeudamiento Patrim. con propuesta</td>
                <td>Veces</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="solvencia_endeudamiento_propuesta" value="{{ $solvencia_endeudamiento_propuesta }}"></td>
                <td><div class="cuadro-input">{{ $solvencia_endeudamiento_propuesta_res }}</div></td>
                <td colspan=2><div class="cuadro-input">{{ $solvencia_endeudamiento_propuesta_res_coment }}</div></td>
                <td><div class="cuadro-input">Usualmente <1 (maximo considerado 0.85). Particularidad; en los giros de alta rotación ó servicios puede ser >1</div></td>
              </tr>
            @endif
              <tr>
                <td class="doble-subrayado">Cuota total/excedente total</td>
                <td class="doble-subrayado">%</td>
                <td><input type="text" class="form-control doble-subrayado campo_moneda" style="{{$stylebackground_solvencia_cuota_total}}" disabled id="solvencia_cuota_total" value="{{ $solvencia_cuota_total }}"></td>
                <td><div class="cuadro-input doble-subrayado" style="{{$stylebackground_solvencia_cuota_total}}">{{ $solvencia_cuota_total_res }}</div></td>
                <td colspan=2><input type="text" style="{{$stylebackground_solvencia_cuota_total}}" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto doble-subrayado" id="solvencia_cuota_total_res_coment" 
                                     value="{{ $credito_propuesta ? $credito_propuesta->solvencia_cuota_total_res_coment : '' }}"></td>
                <td><div class="cuadro-input doble-subrayado" > Se exije < 100% conforme política</div></td>
              </tr>
            
            @if($users_prestamo->idfuenteingreso == 1)
              <tr>
                <td>Préstamo / capital de trabajo Neto</td>
                <td>%</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="solvencia_capital_trabajo_neto" value="{{ $solvencia_capital_trabajo_neto }}"></td>
                <td><div class="cuadro-input">{{ $solvencia_capital_trabajo_neto_res }}</div></td>
                <td colspan=2><div class="cuadro-input">{{ $solvencia_capital_trabajo_neto_res_coment }}</div></td>
                <td><div class="cuadro-input">Usualmente:<100% (maximo considerado 85%). Particularidad: en giros de alta rotación ó servicios puede ser >100% o (-)</div></td>
              </tr>
              <tr>
                <td>Capital de trabajo</td>
                <td>S/</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="solvencia_capital_trabajo" value="{{ $solvencia_capital_trabajo }}"></td>
                <td></td>
                <td colspan=2><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" id="solvencia_capital_trabajo_res_coment" value="{{ $credito_propuesta ? $credito_propuesta->solvencia_capital_trabajo_res_coment : '' }}" ></td>
                <td><div class="cuadro-input">Giros de alta rotación ó servicios puede ser (-)</div></td>
              </tr>
              <tr>
                <th colspan=7><b>GESTIÓN</b></th>
              </tr>
              <tr>
                <td>Plazo prom.rotación de invent.</td>
                <td>Días</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="gestion_rotacion_inventario" value="{{ $gestion_rotacion_inventario }}"></td>
                <td><div class="cuadro-input">{{ $gestion_rotacion_inventario_res }}</div></td>
                <td colspan="2"><div class="cuadro-input">{{ $gestion_rotacion_inventario_res_coment }} {{ $gestion_rotacion_inventario }} días</div></td>
                <td></td>
              </tr>
              <tr>
                <td>Plazo promedio de cobranza</td>
                <td>Días</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="gestion_promedio_cobranza" value="{{ $gestion_promedio_cobranza }}"></td>
                <td><div class="cuadro-input">{{ $gestion_promedio_cobranza_res }}</div></td>
                <td colspan="2"><div class="cuadro-input">{{ $gestion_promedio_cobranza_res_coment }} {{ $gestion_promedio_cobranza }} días</div></td>
                <td><div class="cuadro-input">Plazo máximo adecuado de 30 a 45 días</td>
              </tr>
              <tr>
                <td>Plazo promedio de pago</td>
                <td>Días</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="gestion_promedio_pago" value="{{ $gestion_promedio_pago }}"></td>
                <td><div class="cuadro-input">{{ $gestion_promedio_pago_res }}</div></td>
                <td colspan=2><div class="cuadro-input">{{ $gestion_promedio_pago_res_coment }}</div></td>
                <td><div class="cuadro-input">CALCE: Plazo Prom.Cobranza < Plazo Prom. Pago</div></td>
              </tr>
              
            @endif
              <tr>
                <th colspan=7><b>LIMITES</b></th>
              </tr>
              <tr>
                <td>Financiamiento por VRU</td>
                <td>%</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="limites_financiamiento_vru" value="{{ $limites_financiamiento_vru }}"></td>
                <td><div class="cuadro-input">{{ $limites_financiamiento_vru_res }}</div></td>
                <td colspan=2><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" id="limites_financiamiento_vru_res_coment" 
                                     value="{{ $credito_propuesta ? $credito_propuesta->limites_financiamiento_vru_res_coment : '' }}"></td>
                <td></td>
              </tr>
              @if($credito->idevaluacion == 2 or $users_prestamo->idfuenteingreso == 2)
              <tr>
                <td class="doble-subrayado">N° de entidades (Cliente y Pareja)</td>
                <td class="doble-subrayado">N°</td>
                <?php
                    $limites_numero_entidades = 0;
                    if($credito->idevaluacion == 2){
                        $limites_numero_entidades = $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->total_deuda : 0;
                    }else{
                        if($users_prestamo->idfuenteingreso == 2){
                            $limites_numero_entidades = $credito_formato_evaluacion ? $credito_formato_evaluacion->entidad_financiera_total : 0;
                        }else{
                            $limites_numero_entidades = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->total_deuda : 0;
                        }
                    }
                    $entidad_maxima = configuracion($tienda->id,'entidades_maxima')['valor'];
                    $limites_numero_entidades_res = '';
                    $stylebackground_limites_numero_entidades_res ='';
                    $limites_numero_entidades_style = '';
                    if ($limites_numero_entidades > $entidad_maxima) {
                      $limites_numero_entidades_res = "Se sugiere no proceder o coverturar la propuesta";
                      $limites_numero_entidades_style = 'color:red;';
                      
                      $stylebackground_limites_numero_entidades_res = 'color:red;';
                    } else if ($limites_numero_entidades <= $entidad_maxima) {
                      $limites_numero_entidades_res = "Proceder con propuesta";
                    }
                ?>
                <td><input type="text" class="form-control doble-subrayado campo_moneda" 
                           value="{{ $limites_numero_entidades }}" 
                           id="limites_numero_entidades" style="{{$stylebackground_limites_numero_entidades_res}}" disabled></td>
                <td><div class="cuadro-input doble-subrayado" style="{{$stylebackground_limites_numero_entidades_res}}">{{ $limites_numero_entidades_res }}</div></td>
                <td colspan=2><input type="text" style="{{$stylebackground_limites_numero_entidades_res}}" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto doble-subrayado" id="limites_numero_entidades_res_coment" 
                            value="{{ $credito_propuesta ? $credito_propuesta->limites_numero_entidades_res_coment : '' }}"></td>
                <td></td>
              </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
      @endif
      @if($credito->idevaluacion == 1)
      <div class="mb-1 mt-2">
        <span class="badge d-block">RESULTADOS DE EVALUACIÓN RESUMIDA:</span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <table class="table">
            <thead>
              <tr>
                <th width="200px">Indicadores</th>
                <th width="10px"></th>
                <th width="70px">Ratios</th>
                <th>Resultado</th>
                <th colspan=2>Comentarios</th>
                <th width="210px">Exigencias/Particularidades</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th colspan=7><b>SOLVENCIA</b></th>
              </tr>
              <tr>
                <td class="doble-subrayado">Relación cuota/excedente</td>
                <td class="doble-subrayado">%</td>
                <td><input type="text" class="form-control doble-subrayado campo_moneda" disabled id="res_solvencia_relacion_cuota" value="{{ $res_solvencia_relacion_cuota }}"></td>
                <td><div class="cuadro-input doble-subrayado" style="{{$res_solvencia_relacion_cuota_style}}">{{ $res_solvencia_relacion_cuota_res }}</div></td>
                <td colspan=2><input type="text" class="form-control color_cajatexto doble-subrayado" {{ $view_detalle=='false' ? 'disabled' : '' }}  id="res_solvencia_relacion_cuota_coment" value="{{ $credito_propuesta ? $credito_propuesta->res_solvencia_relacion_cuota_coment : '' }}"></td>
                <td><div class="cuadro-input doble-subrayado">Se exije < 100% conforme política</div></td>
              </tr>
              <tr>
                <th colspan=7><b>OTROS RATIOS</b></th>
              </tr>
              <tr>
                <td>R. Cuota Mensual/Ingreso Mensual</td>
                <td>%</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="res_ratios_cuota_ingreso_mensual" value="{{ $res_ratios_cuota_ingreso_mensual }}"></td>
                <td><div class="cuadro-input" style="{{$res_ratios_cuota_ingreso_mensual_style}}">{{ $res_ratios_cuota_ingreso_mensual_res }}</div></td>
                <td colspan=2><div class="cuadro-input">{{ $res_ratios_cuota_ingreso_mensual_res_coment }}</div></td>
                <td><div class="cuadro-input">Debe ser <= que {{ configuracion($tienda->id,'relacion_couta_ingreso')['valor'] }}%. Considerar N° de entidades para determinar</div></td>
              </tr>
              @if($res_ratios_venta_cuota_diaria > 0)
              <tr>
                <td>R. Cuota diaria/ Venta diaria</td>
                <td>%</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="res_ratios_venta_cuota_diaria" value="{{ $res_ratios_venta_cuota_diaria }}"></td>
                <td><div class="cuadro-input" style="{{$res_ratios_venta_cuota_diaria_style}}">{{ $res_ratios_venta_cuota_diaria_res }}</div></td>
                <td colspan=2><div class="cuadro-input">{{ $res_ratios_venta_cuota_diaria_res_coment }}</div></td>
                <td><div class="cuadro-input">Debe ser <= que {{ configuracion($tienda->id,'relacion_cuota_venta')['valor'] }}%</div></td>
              </tr>
              @endif
              @if($res_ratios_venta_cuota_semanal > 0)
              <tr>
                <td>R. Cuota Semanal/ Venta semanal </td>
                <td>%</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="res_ratios_venta_cuota_semanal" value="{{ $res_ratios_venta_cuota_semanal }}"></td>
                <td><div class="cuadro-input" style="{{$res_ratios_venta_cuota_semanal_style}}">{{ $res_ratios_venta_cuota_semanal_res }}</div></td>
                <td colspan=2><div class="cuadro-input">{{ $res_ratios_venta_cuota_semanal_res_coment }}</div></td>
                <td><div class="cuadro-input">Debe ser <= que {{ configuracion($tienda->id,'relacion_cuota_venta')['valor'] }}%</div></td>
              </tr>
              @endif
              @if($res_ratios_venta_cuota_quincenal > 0)
              <tr>
                <td>R. Cuota Quincenal/ Vta. quincenal </td>
                <td>%</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="res_ratios_venta_cuota_quincenal" value="{{ $res_ratios_venta_cuota_quincenal }}"></td>
                <td><div class="cuadro-input" style="{{$res_ratios_venta_cuota_quincenal_style}}">{{ $res_ratios_venta_cuota_quincenal_res }}</div></td>
                <td colspan=2><div class="cuadro-input">{{ $res_ratios_venta_cuota_quincenal_res_coment }}</div></td>
                <td><div class="cuadro-input">Debe ser <= que {{ configuracion($tienda->id,'relacion_cuota_venta')['valor'] }}%</div></td>
              </tr>
              @endif
              @if($res_ratios_venta_cuota_mensual > 0)
              <tr>
                <td>R. Cuota Mensual/Venta Mensual ( Frec. Mensual)</td>
                <td>%</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="res_ratios_venta_cuota_mensual" value="{{ $res_ratios_venta_cuota_mensual }}"></td>
                <td><div class="cuadro-input" style="{{$res_ratios_venta_cuota_mensual_style}}">{{ $res_ratios_venta_cuota_mensual_res }}</div></td>
                <td colspan=2><div class="cuadro-input">{{ $res_ratios_venta_cuota_mensual_res_coment }}</div></td>
                <td><div class="cuadro-input">Debe ser <= que {{ configuracion($tienda->id,'relacion_cuota_venta')['valor'] }}%. Considerar determinante el N° de entidades</div></td>
              </tr>
              @endif
              @if($credito->idevaluacion == 1)
              <tr>
                <?php
                    $limites_numero_entidades = 0;
                    if($credito->idevaluacion == 2){
                        $limites_numero_entidades = $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->total_deuda : 0;
                    }else{
                        $limites_numero_entidades = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->total_deuda : 0;
                    }
                    $entidad_maxima = configuracion($tienda->id,'entidades_maxima')['valor'];
                    $limites_numero_entidades_res = '';
                    $limites_numero_entidades_style = '';
                    if ($limites_numero_entidades > $entidad_maxima) {
                      $limites_numero_entidades_res = "Se sugiere no proceder o coverturar la propuesta";
                      $limites_numero_entidades_style = 'color:red;';
                    } else if ($limites_numero_entidades <= $entidad_maxima) {
                      $limites_numero_entidades_res = "Proceder con propuesta";
                    }
                ?>
                <td class="doble-subrayado">N° de entidades (Cliente y Pareja)</td>
                <td class="doble-subrayado">N°</td>
                <td><input type="text" class="form-control doble-subrayado  campo_moneda" 
                           value="{{ $limites_numero_entidades }}" 
                           id="limites_numero_entidades" style="{{$limites_numero_entidades_style}}" disabled></td>
                <td><div class="cuadro-input doble-subrayado" style="{{$limites_numero_entidades_style}}">{{ $limites_numero_entidades_res }}</div></td>
                <td colspan=2><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto doble-subrayado" value="{{ $credito_propuesta ? $credito_propuesta->limites_numero_entidades_res_coment : '' }}"></td>
                <td></td>
              </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
      @endif
              <?php
              $excedente_propuesta_con_deduccion = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->excedente_propuesta_con_deduccion : 0;
              $rango_tope = configuracion($tienda->id,'rango_tope')['valor'];
              $estado_imprimir = 0;
              if ($excedente_propuesta_con_deduccion < 0) {
                  $estado_imprimir = 1;
              } else if ($excedente_propuesta_con_deduccion <= $rango_tope) {
                
              } else if ($excedente_propuesta_con_deduccion > $rango_tope){
                  $estado_imprimir = 1;
              }
      
              if($validadar_resultado==3){
                  $estado_imprimir = 1;
              }
              ?>
      
      <div class="row mt-1">
        <div class="col" style="flex: 0 0 0%;">
          @if($view_detalle!='false')
          <button type="submit" class="btn btn-success" id="boton_guardar">
            <i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
          @endif
        </div>
        <div class="col" style="flex: 0 0 0%;">
          @if($validadar_resultado==3)
          <div style="width: 300px;
    background-color: #dc3545;
    border-radius: 5px;
    padding: 5px;
    color: #fff;
    text-align: center;
    font-weight: bold;">NO ES VIABLE</div>
          @elseif($estado_imprimir==1)
          <div style="width: 300px;
    background-color: #dc3545;
    border-radius: 5px;
    padding: 5px;
    color: #fff;
    text-align: center;
    font-weight: bold;">Cuota total/excedente total "NO ES VIABLE"</div>
          @else
          <button type="button" class="btn btn-dark" 
                  onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=solicitudpropuesta_credito&tipo=1')}}', size: 'modal-fullscreen' })"
                  
                  id="boton_imprimir"
                  ><i class="fa-solid fa-file-pdf"></i> IMPRIMIR</button>
          @endif
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
  .form-check-input:checked {
      background-color: #585858 !important;
      border-color: #585858 !important;
  }
  .cuadro-input {
    background-color: #e9ecef;
    padding: 6px 5px;
    padding-bottom: 7px;
    border-radius: 3px;
    border: 1px solid #ced4da;
    font-weight: bold;
  }
</style>
<script>
  valida_input_vacio();
  $('input').on('blur', function() {
      $('#boton_imprimir').attr('disabled',true);
  });
  
          @if($credito->idmodalidad_credito==2 && count($saldo_prestamo_vigente_propio)>0 && $view_detalle!='false')
          calcula_neto_destino_credito();
          @endif
  function calcula_neto_destino_credito(){
    let monto_destino_credito = parseFloat($('#monto_destino_credito').val());
 
    let monto_compra_deuda = 0;
    $("input#monto_compra_deuda_check:checked").each(function (e) {
            let num = $(this).attr("num");
            monto_compra_deuda = monto_compra_deuda+parseFloat($('#monto_compra_deuda'+num).val());
    });
    
    $('#result_ampliaciondeuda').removeAttr('style').html('');
    if(monto_compra_deuda==0){
        $('#result_ampliaciondeuda').attr('style',`background-color: #dd1010;
    color: #fff;
    text-align: center;
    font-weight: bold;
    padding: 5px;
    border-radius: 5px;`).html('Es Obligatorio seleccionar una Amplación de deuda!!');
    }
    
    $('#monto_compra_deuda').val(monto_compra_deuda.toFixed(2))
    
    // let modalidad_credito = 2; PARA PRUEBAS
    let modalidad_credito = parseFloat("{{ $credito->idmodalidad_credito }}");
    if( ( modalidad_credito == 2 || modalidad_credito == 3 ) && (monto_compra_deuda == 0 || monto_compra_deuda == '') ){
      $('#error_monto_compra').removeClass('d-none');
      
      @if($credito->idmodalidad_credito==2 && count($saldo_prestamo_vigente_propio)>0)
      $('#boton_guardar').attr('disabled',true);
      @endif
    }else{
      $('#error_monto_compra').addClass('d-none');
      $('#boton_guardar').attr('disabled',false);
    }
    let neto_destino_credito = monto_destino_credito - monto_compra_deuda;
    $('#neto_destino_credito').val(neto_destino_credito.toFixed(2))
  }
  
  function monto_compra_deuda_det(){
    let data = [];
    $("input#monto_compra_deuda_check:checked").each(function (e) {
        let num = $(this).attr("num");
        data.push({ 
            idcredito: num,
            monto_compra_deuda: parseFloat($('#monto_compra_deuda'+num).val()),
        });
    });
    return JSON.stringify(data);
  }
  
</script>    