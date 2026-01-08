<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/credito/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'aprobar_propuesta',
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
        $('#close_confirmacionproceso').click();
        $('#close_opcionescredito').click();
                
      },this)"> 

  
    <div class="modal-header" style="border-bottom: 0;">
        <h5 class="modal-title">APROBAR CRÉDITO </h5>
        <button type="button" class="btn-close text-white" id="modal-close-garantia-cliente" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body modal-body-cualitativa">
      <div class="row">
        <div class="col-sm-12 col-md-6">
          <div class="row">
            <label class="col-sm-12 col-form-label">AGENCIA/OFICINA:</label>
            <div class="col-sm-12">
              <input type="text" step="any" class="form-control" value="{{ $tienda->nombreagencia }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-12 col-form-label">CLIENTE/RAZON SOCIAL:</label>
            <div class="col-sm-12">
              <input type="text" step="any" class="form-control" value="{{ $credito->nombreclientecredito }}" disabled>
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-6">
          <div class="row">
            <label class="col-sm-12 col-form-label">FECHA:</label>
            <div class="col-sm-12">
              <input type="date" step="any" class="form-control" value="{{ date_format(date_create($credito->fecha),'Y-m-d') }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-12 col-form-label">DNI/RUC:</label>
            <div class="col-sm-12">
              <input type="text" step="any" class="form-control" value="{{ $credito->docuementocliente }}" disabled>
            </div>
          </div>
        </div>
      </div>
      @if($credito->monto_solicitado<=0)
      <div class="row m-3">
        <div class="col-sm-12">
          <h5 class="text-center text-danger">No puedes aprobar un Crédito en 0.00</h5>
        </div>
      </div>
      @else
      <div class="row m-3">
        <div class="col-sm-12">
          <h5 class="text-center text-danger">¿Seguro que desea aprobar el crédito?</h5>
        </div>
      </div>
      <?php
          $estado_imprimir = 0;
          $validadar_resultado = 0;
          $validar_evaluacion = 0;
          if($users_prestamo->idfuenteingreso == 1){
                $rango_tope = configuracion($tienda->id,'rango_tope')['valor'];
                $relacion_couta_ingreso = configuracion($tienda->id,'relacion_couta_ingreso')['valor'];
                $relacion_cuota_venta = configuracion($tienda->id,'relacion_cuota_venta')['valor'];
                
              
  
                $res_solvencia_relacion_cuota = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->indicador_solvencia_cuotas : 0;
                if ($res_solvencia_relacion_cuota > $rango_tope) {
                    $validadar_resultado++;
                } elseif ($res_solvencia_relacion_cuota <= 0) {
                    $validadar_resultado++;
                }
  
                $res_ratios_cuota_ingreso_mensual = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_mensual : 0;
                if ($res_ratios_cuota_ingreso_mensual > $relacion_couta_ingreso) {
                    $validadar_resultado++;
                }
  
                $res_ratios_venta_cuota_diaria = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_venta_diaria : 0;
                if ($res_ratios_venta_cuota_diaria > $relacion_cuota_venta) {
                    $validadar_resultado++;
                }
  
              $excedente_propuesta_con_deduccion = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->excedente_propuesta_con_deduccion : 0;
              $rango_tope = configuracion($tienda->id,'rango_tope')['valor'];
              if ($excedente_propuesta_con_deduccion < 0) {
                  $estado_imprimir = 1;
              } else if ($excedente_propuesta_con_deduccion <= $rango_tope) {
                
              } else if ($excedente_propuesta_con_deduccion > $rango_tope){
                  $estado_imprimir = 1;
              }
              if($validadar_resultado==3){
                  $estado_imprimir = 1;
              }
            
          }elseif($users_prestamo->idfuenteingreso == 2){
              if($credito_formato_evaluacion){
                  if($credito_formato_evaluacion->estado_evaluacion=='CRÉDITO NO VIABLE'){
                      $validar_evaluacion = 1;
                      $estado_imprimir = 2;
                  }
              }
          }
  
          //validar ampliado

        $credito_propuesta = DB::table('credito_propuesta')->where('credito_propuesta.idcredito',$credito->id)->first();
          $validadar_ampliacion = 0;
          if($credito->idmodalidad_credito == 2){
            if($credito_propuesta){
              if($credito_propuesta->monto_compra_deuda_det==''){
                  $validadar_ampliacion = 1;
              }
            }else{
                $validadar_ampliacion = 1;
            }
          }
          $validadar_custodia = 0;
          if($credito->idforma_credito==1){
              $cliente = DB::table('users')->whereId($credito->idcliente)->first();
              if($cliente->custodiagarantia_id!=0){
                  $validadar_custodia = 1;
              }
          }
  $validad_eva_resumida  = 0;
              ?>
      <div class="row mt-1">
        <div class="col" style="flex: 0 0 0%;">
          @if($credito->idevaluacion==1)
              @if($credito_evaluacion_resumida && $credito->idforma_credito!=1)
                  @if($credito_evaluacion_resumida->estado_credito_general=='CRÉDITO VIABLE')
                  
                  <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-check"></i> SI, PASAR A PROCESO</button>
                
                  @else
                  <?php
                  $validad_eva_resumida = 1;
                  ?>
                  @endif
              @else
                  @if($credito->idforma_credito==1 && $validadar_custodia==1)
                  <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-check"></i> SI, PASAR A PROCESO</button>
                  @endif
              @endif
          @else
          @if($estado_imprimir==0 && $validadar_ampliacion==0)
          <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-check"></i> SI, PASAR A PROCESO</button>
          @endif
          @endif
        </div>
        <div class="col" style="flex: 1 0 0%;">
          @if($validad_eva_resumida==1)
          <div style="width: 300px;
    background-color: #dc3545;
    border-radius: 5px;
    padding: 5px;
    color: #fff;
    text-align: center;
    font-weight: bold;">CRÉDITO NO VIABLE</div>
          @elseif($validadar_resultado==3)
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
          @elseif($validar_evaluacion==1)
          <div style="width: 300px;
    background-color: #dc3545;
    border-radius: 5px;
    padding: 5px;
    color: #fff;
    text-align: center;
    font-weight: bold;">Cuota/excedente "NO ES VIABLE"</div>
          @elseif($validadar_ampliacion==1)
          <div style="width: 300px;
    background-color: #dc3545;
    border-radius: 5px;
    padding: 5px;
    color: #fff;
    text-align: center;
    font-weight: bold;">No Ha seleccionado Ningun Crédito a ampliar.</div>
          @elseif($credito->idforma_credito==1 && $validadar_custodia==0)
          <div style="width: 300px;
    background-color: #dc3545;
    border-radius: 5px;
    padding: 5px;
    color: #fff;
    text-align: center;
    font-weight: bold;">No Ha seleccionado Ningun Depósitario.</div>
          @else
          <div id="success-message" class="alert alert-success d-none" style="text-align:left;"></div>
          @endif
        </div>
        <div class="col" style="flex: 0 0 0%;">
          <button type="button" class="btn btn-danger" id="close_confirmacionproceso" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i> SALIR</button>
        </div>
      </div>
      @endif
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