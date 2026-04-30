<div class="modal-header">
  <h5 class="modal-title">OPCIONES</h5>
  <button type="button" class="btn-close" id="close_opcionescredito" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
  <div class="row" style="font-size: 14px;padding: 7px;">
    <div class="col-md-5"><b>CLIENTE:</b> <span style="font-weight: normal;">{{ $usuario->nombrecompleto }}</span></div>
    <div class="col-md-3"><b>F. INGRESO:</b> <span style="font-weight: normal;">{{ $users_prestamo->db_idfuenteingreso }}</span></div>
    <div class="col-md-4" style="text-align: right;"><b>PRODUCTO:</b> <span style="font-weight: normal;">{{ $credito->nombreproductocredito }}</span></div>
  </div>
  @if($credito->idcredito_refinanciado!=0)
    <p class="text-center" 
      style="background-color: #dc3545;
        padding: 10px;
        border-radius: 5px;
        color: #fff;
        width: 80%;
        margin: auto;">
      El Crédito no fue refinanciado, elimine!!.
    </p>
  @else
    <div class="col-sm-12 mt-2">
      <button type="button" class="btn btn-success me-1" onclick="modal_credito_garantia('solicitud')">SOLICITUD CRÉDITO</button>
    </div>
    <div class="col-sm-12 mt-2">
      <button type="button" class="btn btn-primary me-1" onclick="modal_credito_garantia('garantia_cliente')">GARANTIAS</button>
      <button type="button"
        class="btn btn-primary me-1" 
        onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=cronograma')}}', size: 'modal-fullscreen' })">
        CRONOGRAMA
      </button>
    </div>
    <hr style="margin-top: 8px;margin-bottom: 8px;">
    @if($credito->conevaluacion == 'SI')
      @if($users_prestamo->idfuenteingreso == 1) {{-- independiente --}}
        <div class="col-sm-12 mt-2">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="evaluacion" id="evaluacion_completa" onclick="guardar_tipoevaluacion(2)" 
                            <?php echo $credito->idevaluacion==2?'checked':''?>>
                      <label class="form-check-label" for="evaluacion_completa">
                        Evaluación Completa
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="evaluacion" id="evaluacion_resumida" onclick="guardar_tipoevaluacion(1)"
                            <?php echo $credito->idevaluacion==1?'checked':($credito->idevaluacion==0?'checked':'')?>>
                      <label class="form-check-label" for="evaluacion_resumida">
                        Evaluación Resumida
                      </label>
                    </div>
                </div>
                <div class="col-md-9">
                    <button type="button" class="btn btn-secondary" style="color: #000;background-color: #00bf3e;border-color: #00bf3e;height: 50px;"
                            onclick="form_propuesta_credito()">
                      HOJA DE PROPUESTA DE CRÉDITO</button>
                    <script>
                      function pdf_propuesta_credito(){
                        let tipo = $("input[name='evaluacion']:checked").attr('id');
                        modal({ route:"{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=solicitudpropuesta_credito')}}&tipo="+tipo, size: 'modal-fullscreen' })
                      }
                      function form_propuesta_credito(){
                        let tipo = $("input[name='evaluacion']:checked").attr('id');
                        modal({ route:"{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=propuesta_credito')}}&tipo="+tipo, size: 'modal-fullscreen' })
                      }  
                    </script>
                    <button type="button" class="btn btn-warning"  style="height: 50px;"
                            onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=aprobar_propuesta')}}', size: 'modal-small' })">
                      <i class="fa fa-check"></i> PASAR A APROBACIÓN</button>

                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="btn-group mb-1 evaluacion-completa" id="cont_cualitativa">
              <button type="button"
                class="btn btn-primary evaluacion"
                style="background-color: #b8b1b1 !important;"
                onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=evaluacion_cualitativa')}}', size: 'modal-fullscreen' })">
                1.- EVALUACION CUALITATIVA
              </button>
            </div>
            <div class="btn-group mb-1 evaluacion-completa" id="cont_cuantitativa">
              <button type="button"
                class="btn btn-primary evaluacion"
                style="background-color: #b8b1b1 !important;"
                onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=evaluacion_cuantitativa')}}', size: 'modal-fullscreen' })">
                2.- EVALUACION CUANTITATIVA
              </button>
            </div>
            <div class="btn-group mb-1 evaluacion-completa" id="cont_margen_ventas">
              <button type="button" class="btn btn-primary evaluacion"
                      onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=margen_ventas')}}', size: 'modal-fullscreen' })">
                3.- MARGEN DE VENTAS</button>
            </div>
            <div class="btn-group mb-1 evaluacion-completa" id="cont_inventario_activos">
              <button type="button" class="btn btn-primary evaluacion"
                      onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=inventario_activos')}}', size: 'modal-fullscreen' })">
                4.- INVENTARIO Y ACTIVOS</button>
            </div>
            <div class="btn-group mb-1 evaluacion-completa" id="cont_deudas">
              <button type="button" class="btn btn-primary evaluacion"
                      onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=deudas')}}', size: 'modal-fullscreen' })">
                5.- DEUDAS</button>
            </div>
            <div class="btn-group mb-1 evaluacion-completa" id="cont_ingresoadicional">
              <button type="button" class="btn btn-primary evaluacion"
                      onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=ingresos_adicionales')}}', size: 'modal-fullscreen' })">
                6.- INGRESO ADICIONAL - MES Y FIJOS</button>
            </div>
            <div class="btn-group mb-1 evaluacion-completa" id="cont_control_limites">
              <button type="button" class="btn btn-primary evaluacion"
                      onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=control_limites')}}', size: 'modal-fullscreen' })">
                7.- GARANTIAS Y LIMITES</button>
            </div>
            <div class="btn-group mb-1 evaluacion-completa" id="cont_flujocaja">
              <button type="button" class="btn btn-primary evaluacion"
                      onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=flujo_caja')}}', size: 'modal-fullscreen' })">
                8.- FLUJO DE CAJA</button>
            </div>
            <div class="btn-group mb-1 evaluacion-resumida" id="cont_evaluacion_resumida">
              <button type="button" class="btn btn-primary evaluacion"
                      onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=evaluacion_resumida')}}', size: 'modal-fullscreen' })">
                1.- EVALUACION RESUMIDA</button>
            </div>
            <div class="btn-group mb-1 evaluacion-resumida">
              <button type="button" class="btn btn-primary evaluacion"
                      onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=control_limites')}}', size: 'modal-fullscreen' })">
                2.- GARANTIAS Y LIMITES</button>
            </div>
            <button type="button" class="btn btn-warning1 mb-1"
                    onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=solicitud_checklist')}}', size: 'modal-fullscreen' })">
              CHECK LIST</button>
        </div>
      @elseif($users_prestamo->idfuenteingreso == 2) {{-- dependiente --}}
        <div class="col-md-9 mb-1">
            <button type="button" class="btn btn-secondary" style="color: #000;background-color: #00bf3e;border-color: #00bf3e;height: 50px;"
                    onclick="form_propuesta_credito()">
              HOJA DE PROPUESTA DE CRÉDITO</button>
            <script>
              function pdf_propuesta_credito(){
                let tipo = $("input[name='evaluacion']:checked").attr('id');
                modal({ route:"{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=solicitudpropuesta_credito')}}&tipo="+tipo, size: 'modal-fullscreen' })
              }
              function form_propuesta_credito(){
                let tipo = $("input[name='evaluacion']:checked").attr('id');
                modal({ route:"{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=propuesta_credito')}}&tipo="+tipo, size: 'modal-fullscreen' })
              }  
            </script>
            <button type="button" class="btn btn-warning"  style="height: 50px;"
                    onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=aprobar_propuesta')}}', size: 'modal-small' })">
              <i class="fa fa-check"></i> PASAR A APROBACIÓN</button>
        </div>
        <div class="col-sm-12">
          <div class="btn-group mb-1" id="formato_evaluacion">
            <button type="button" class="btn btn-primary evaluacion"
                    onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=formato_evaluacion')}}', size: 'modal-fullscreen' })">
              1.- FORMATO DE EVALUACIÓN</button>
          </div>
            <div class="btn-group mb-1 evaluacion-resumida">
              <button type="button" class="btn btn-primary evaluacion"
                      onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=control_limites')}}', size: 'modal-fullscreen' })">
                2.- GARANTIAS Y LIMITES</button>
            </div>

            <!--div class="btn-group mb-1">
            <button type="button" class="btn btn-warning" 
                    onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=aprobar_propuesta')}}', size: 'modal-small' })">
              <i class="fa fa-check"></i> PASAR A APROBACIÓN</button>

            </div-->

          <button type="button" class="btn btn-warning1 mb-1"
                  onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=solicitud_checklist')}}', size: 'modal-fullscreen' })">
            CHECK LIST</button>
        </div>
      @endif
    @else
      <div class="btn-group mb-1">
        <button type="button"
          class="btn btn-warning" 
          onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=aprobar_propuesta')}}', size: 'modal-small' })">
          <i class="fa fa-check"></i>
          PASAR A APROBACIÓN
        </button>
      </div>
      @if ($credito->idforma_credito==2) {{-- no prendario --}}
        <button type="button"
          class="btn btn-warning1 mb-1"
          onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=solicitud_checklist')}}', size: 'modal-fullscreen' })">
          CHECK LIST
        </button>
      @endif
    @endif
  @endif

  {{-- Ampliación de crédito --}}
  @if($credito->idforma_credito==2 && $credito->conevaluacion == 'NO' && $credito->idmodalidad_credito == 2) {{-- no prendario && sin evaluación && ampliado --}}
    <form action="javascript:;" 
      onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/credito/'.$credito->id) }}',
        method: 'PUT',
        data:{
          view: 'propuesta_credito_ampliado',
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
      @php
        $view_detalle = '';
        $credito_propuesta = DB::table('credito_propuesta')->where('credito_propuesta.idcredito',$credito->id)->first();
      @endphp
      <div class="mb-1 mt-2">
        <span class="badge d-block">DESTINO, AMPLIACIÓN Y ENTREGA DE CRÉDITO: </span>
      </div>
      <div class="row modal-body-cualitativa">
        <div class="col-sm-12 col-md-12">
          <table class="table">
            <tbody>
              <tr>
                <td style="width:80px">Destino:</td>
                <td colspan="2">
                  <input type="text"
                    class="form-control"
                    disabled
                    id="tipo_destino_credito_nombre" 
                    value="{{ $credito->tipo_destino_credito_nombre}}">
                </td>
                <td style="width:100px">
                  <input type="text"
                    class="form-control campo_moneda"
                    disabled
                    id="monto_destino_credito" 
                    value="{{ $credito->monto_solicitado }}">
                </td>
              </tr>
              @php
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
              @endphp
              @if($credito->idmodalidad_credito==2 && count($saldo_prestamo_vigente_propio)>0)
                <tr>
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
                          value="C{{ str_pad($value->cuenta, 8, "0", STR_PAD_LEFT) }} - {{$value->nombreproductocredito}}"
                          disabled>
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
              <tr>
                <td></td>
                <td colspan="2">Neto a Entregar (S/)</td>
                <td>
                  <input type="text"
                    class="form-control campo_moneda"
                    disabled
                    id="neto_destino_credito" 
                    value="{{ $credito_propuesta ? (number_format($credito->monto_solicitado - $credito_propuesta->monto_compra_deuda, 2, '.', '')) : $credito->monto_solicitado }}">
                </td>
              </tr>
            </tbody>
          </table>
          @if($credito->idmodalidad_credito==2 && count($saldo_prestamo_vigente_propio)>0 && $view_detalle!='false')
            <div id="result_ampliaciondeuda"></div>
          @endif
          <input type="hidden" class="form-control" value="0.00" id="monto_compra_deuda">
        </div>
      </div>
      <div class="row mt-1">
        <div class="col" style="flex: 0 0 0%;">
          <button type="submit" class="btn btn-success" id="boton_guardar">
            <i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS
          </button>
        </div>
      </div>
    </form>
  @endif
</div>
<style>
  #success-message{
    background: #00a759;
    color: white;
    font-weight: bold;
  }
  .doble-subrayado {
    text-decoration: underline double;
  }
  .single-subrayado {
    text-decoration: underline;
  }
  .form-check .form-check-input {
    float: left;
    margin-left: -0.7em;
    font-size: 1rem;
  }

</style>

<script>

  $(".evaluacion-completa").hide();

  @if($credito->idevaluacion==2)
      $(".evaluacion-completa").show();
      $('.evaluacion-resumida').hide();
  @endif
  
  $("input[name='evaluacion']").change(function() {
    $(".evaluacion-completa").hide();

    if ($("#evaluacion_resumida").is(":checked")) {
      $('.evaluacion-resumida').show();
    } else if ($("#evaluacion_completa").is(":checked")) {
      $(".evaluacion-completa").show();
      $('.evaluacion-resumida').hide();
    }
  });

  function modal_credito_garantia(vista){
    //let size_modal = (vista == 'cronograma' ? 'modal-fullscreen' : 'modal-lg');
    modal({ route:"{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=')}}"+vista, size: 'modal-fullscreen' });  
  }
  
  function guardar_tipoevaluacion(idevaluacion){
      $.ajax({
        url:"{{url('backoffice/0/credito/show_tipoevaluacion')}}",
        type:'GET',
        data: {
          idcredito: '{{$credito->id}}',
          idevaluacion: idevaluacion
        },
        success: function (res){
          
            

        }
      })
  }

  calcula_neto_destino_credito();
  function calcula_neto_destino_credito(){
    let monto_destino_credito = parseFloat($('#monto_destino_credito').val());
 
    let monto_compra_deuda = 0;
    $("input#monto_compra_deuda_check:checked").each(function (e) {
            let num = $(this).attr("num");
            monto_compra_deuda = monto_compra_deuda+parseFloat($('#monto_compra_deuda'+num).val());
    });
    
    $('#result_ampliaciondeuda').removeAttr('style').html('');
    if(monto_compra_deuda==0){
        $('#result_ampliaciondeuda').attr('style',`background-color: #ffc9ca;
            border: 1px solid #ff6666 !important;
            color: #93222c !important;
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
    }else{
      $('#error_monto_compra').addClass('d-none');
      $('#boton_guardar').attr('disabled',false);
      $('#boton_imprimir').attr('disabled',false);
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