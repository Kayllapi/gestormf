<form action="javascript:;" onsubmit="">
  <div class="modal-header">
    <h5 class="modal-title">OPCIONES</h5>
    <button type="button" class="btn-close" id="close_opcionescredito" data-bs-dismiss="modal" aria-label="Close"></button>
  </div>
  <div class="modal-body">
    <div class="row" style="font-size: 14px;padding: 7px;">
      <div class="col-md-6"><b>CLIENTE:</b> {{ $usuario->nombrecompleto }}</div>
      <div class="col-md-6" style="text-align: right;"><b>PRODUCTO:</b> {{ $credito->nombreproductocredito }}</div>
    </div>

    <div class="col-sm-12 mt-2">
      <button type="button" class="btn btn-success me-1" onclick="modal_credito('solicitud')">SOLICITUD CRÉDITO</button>
    </div>
        <div class="col-sm-12 mt-2">
          <button type="button" class="btn btn-primary me-1" onclick="modal_credito('garantia_cliente')">GARANTIAS</button>

          <button type="button" class="btn btn-primary me-1" 
                  onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=cronograma')}}&detalle=false', size: 'modal-fullscreen' })">CRONOGRAMA</button>
        </div>

    <hr style="margin-top: 8px;margin-bottom: 8px;">
    @if($credito->conevaluacion == 'SI')
            @if($users_prestamo->idfuenteingreso == 1)
            <div class="col-sm-12 mt-2">
                <div class="row">
                    <div class="col-md-9">
                        <button type="button" class="btn btn-secondary" style="color: #000;background-color: #00bf3e;border-color: #00bf3e;height: 50px;margin-bottom: 5px;"
                                onclick="form_propuesta_credito()">
                          HOJA DE PROPUESTA DE CRÉDITO</button>
                    </div>
                </div>
              </div>
            <div class="col-sm-12">
                <div class="btn-group mb-1 evaluacion-completa" id="cont_cualitativa">
                  <button type="button" class="btn btn-warning evaluacion" style="background-color: #80e52e;border-color: #212529;"
                          onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=evaluacion_cualitativa')}}&detalle=false', size: 'modal-fullscreen' })">
                    1.- EVALUACION CUALITATIVA</button>
                </div>
                <div class="btn-group mb-1 evaluacion-completa" id="cont_cuantitativa">
                  <button type="button" class="btn btn-warning evaluacion" style="background-color: #80e52e;border-color: #212529;"
                          onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=evaluacion_cuantitativa')}}&detalle=false', size: 'modal-fullscreen' })">
                    2.- EVALUACION CUANTITATIVA</button>
                </div>
                <div class="btn-group mb-1 evaluacion-completa" id="cont_margen_ventas">
                  <button type="button" class="btn btn-warning evaluacion" style="background-color: #c0f297;border-color: #212529;"
                          onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=margen_ventas')}}&detalle=false', size: 'modal-fullscreen' })">
                    3.- MARGEN DE VENTAS</button>
                </div>
                <div class="btn-group mb-1 evaluacion-completa" id="cont_inventario_activos">
                  <button type="button" class="btn btn-warning evaluacion" style="background-color: #c0f297;border-color: #212529;"
                          onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=inventario_activos')}}&detalle=false', size: 'modal-fullscreen' })">
                    4.- INVENTARIO Y ACTIVOS</button>
                </div>
                <div class="btn-group mb-1 evaluacion-completa" id="cont_deudas">
                  <button type="button" class="btn btn-warning evaluacion" style="background-color: #c0f297;border-color: #212529;"
                          onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=deudas')}}&detalle=false', size: 'modal-fullscreen' })">
                    5.- DEUDAS</button>
                </div>
                <div class="btn-group mb-1 evaluacion-completa" id="cont_ingresoadicional">
                  <button type="button" class="btn btn-warning evaluacion" style="background-color: #c0f297;border-color: #212529;"
                          onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=ingresos_adicionales')}}&detalle=false', size: 'modal-fullscreen' })">
                    6.- INGRESO ADICIONAL - MES Y FIJOS</button>
                </div>
                <div class="btn-group mb-1 evaluacion-completa" id="cont_control_limites">
                  <button type="button" class="btn btn-warning evaluacion" style="background-color: #c0f297;border-color: #212529;"
                          onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=control_limites')}}&detalle=false', size: 'modal-fullscreen' })">
                    7.- GARANTIAS Y LIMITES</button>
                </div>
                <div class="btn-group mb-1 evaluacion-completa" id="cont_flujocaja">
                  <button type="button" class="btn btn-warning evaluacion" style="background-color: #c0f297;border-color: #212529;"
                          onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=flujo_caja')}}&detalle=false', size: 'modal-fullscreen' })">
                    8.- FLUJO DE CAJA</button>
                </div>
                <div class="btn-group mb-1 evaluacion-resumida" id="cont_evaluacion_resumida">
                  <button type="button" class="btn btn-warning evaluacion" style="background-color: #c0f297;border-color: #212529;"
                          onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=evaluacion_resumida')}}&detalle=false', size: 'modal-fullscreen' })">
                    1.- EVALUACION RESUMIDA</button>
                </div>
                <div class="btn-group mb-1 evaluacion-resumida">
                  <button type="button" class="btn btn-warning evaluacion" style="background-color: #c0f297;border-color: #212529;"
                          onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=control_limites')}}&detalle=false', size: 'modal-fullscreen' })">
                    2.- GARANTIAS Y LIMITES</button>
                </div>
                <button type="button" class="btn btn-secondary mb-1" style="background-color: #6e726b;border-color: #212529;"  
                        onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=solicitud_checklist')}}&detalle=false', size: 'modal-fullscreen' })">
                  CHECK LIST</button>
              </div>
            @elseif($users_prestamo->idfuenteingreso == 2)
            <div class="col-md-9 mb-1">
                <button type="button" class="btn btn-secondary" style="color: #000;background-color: #00bf3e;border-color: #00bf3e;height: 50px;margin-bottom: 5px;"
                        onclick="form_propuesta_credito()">
                  HOJA DE PROPUESTA DE CRÉDITO</button>
            </div>
            <div class="col-sm-12">
              <div class="btn-group mb-1" id="formato_evaluacion">
                <button type="button" class="btn btn-warning evaluacion" style="background-color: #c0f297;border-color: #212529;"
                        onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=formato_evaluacion')}}&detalle=false', size: 'modal-fullscreen' })">
                  1.- FORMATO DE EVALUACIÓN</button>
              </div>
                <div class="btn-group mb-1 evaluacion-resumida">
                  <button type="button" class="btn btn-warning evaluacion" style="background-color: #c0f297;border-color: #212529;"
                          onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=control_limites')}}&detalle=false', size: 'modal-fullscreen' })">
                    2.- GARANTIAS Y LIMITES</button>
                </div>

              <button type="button" class="btn btn-secondary mb-1" style="background-color: #6e726b;border-color: #212529;"  
                      onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=solicitud_checklist')}}&detalle=false', size: 'modal-fullscreen' })">
                CHECK LIST</button>
            </div>
            @endif
        @else
        @endif

  </div>
  </div>

</form>
                        <script>
                          function form_propuesta_credito(){
                            let tipo = $("input[name='evaluacion']:checked").attr('id');
                            modal({ route:"{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=propuesta_credito')}}&detalle=false&tipo="+tipo, size: 'modal-fullscreen' })
                          }  
                        </script>
<style>
  #success-message {
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
  

  function modal_credito(vista) {
    let size_modal = (vista == 'cronograma' ? 'modal-fullscreen' : 'modal-lg');
    modal({
      route: "{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=')}}" + vista +'&detalle=false',
      size: size_modal
    });
  }
</script>