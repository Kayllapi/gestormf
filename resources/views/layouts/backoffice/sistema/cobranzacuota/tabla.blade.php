<div class="modal-header">
    <h5 class="modal-title">
      Pago de Préstamos Caja
      <button type="button" class="btn btn-success mb-1" id="idbuscarcliente" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="buscarcliente()">
        <i class="fa fa-search"></i> Buscar Cliente
      </button>
      <div style="display:none;float: right;margin-left: 5px;" id="cont_irainicio">
      <button type="button" class="btn btn-primary" onclick="lista_credito_cliente()">
        <i class="fa fa-refresh"></i> Actualizar
      </button>
      </div>
                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Buscar Cliente</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-sm-12">
                            <select class="form-control" id="idclientesearch">
                               <option></option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()" style="font-size: 20px;"></button>
    
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2 modal-body">
            <div class="row">
              <div class="col-sm-3">
                <div class="mb-1">
                  <span class="badge d-block">PRÉSTAMOS</span>
                </div>
                <div class="row d-none data-cliente">
                  <div class="col-sm-12">
                    <label>DNI/CE - Apellidos y Nombres: </label>
                    <input type="text" disabled value="" class="form-control mb-1" id="data-cliente-nombre" style="background-color: white;">
                    <input type="hidden" value="" class="form-control" id="data-cliente-id">
                  </div>
                </div>
                <table class="table table-striped table-hover" id="table-detalle-prestamo">
                  <thead class="table-dark">
                    <tr>
                      <th>MONTO</th>
                      <th></th>
                      <th>N° CUENTA</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td colspan="3">SIN RESULTADOS</td>
                    </tr>
                  </tbody>
                </table>
                <!--select class="form-control" id="idcliente">
                    <option></option>
                    @foreach($creditos as $value)
                      <option value="{{ $value->id }}">
                        {{ $value->identificacion }} - {{ $value->nombrecliente }} - S/. {{ $value->monto_solicitado }} - C{{ str_pad($value->cuenta, 8, "0", STR_PAD_LEFT) }}
                    @endforeach
                </select-->
                <div class="mb-1 mt-1">
                  <span class="badge d-block">RESUMEN DE PAGO Y SALDO DE PRÉSTAMO</span>
                </div>
                <input type="hidden" id="idcredito" value="0">
               <b> N° DE CUENTA: <span id="numerodecuenta"></span></b><br>
               <b> CLASIFICACIÓN: <span id="clasificacion"></span></b><br>
               <b> ASESOR/EJECUTIVO: <span id="asesor" style="color: #1162da;"></span></b>
                <table class="table table-bordered" id="table-prestamos">
                  <thead>
                      <tr>
                        <th style="text-align: center;background-color: #bcbcbc !important;color: #000 !important;">Estado de cuotas <span id="estadocuotas" style="background-color: #ffc107;"></span></th>
                        <th style="text-align: center;background-color: #bcbcbc !important;color: #000 !important;width: 50px;">N°</th>
                        <th style="text-align: center;background-color: #bcbcbc !important;color: #000 !important;width: 50px;">Saldo (S/.)</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr>
                        <td style="width: 150px;background-color: #efefef !important;color: #65bf00 !important;font-weight: bold;">Cancelados</td>
                        <td id="numero_cuota_cancelada" style="color: #65bf00 !important;text-align: right;font-weight:bold;">0</td>
                        <td id="cuota_pagada" style="color: #65bf00 !important;text-align: right;font-weight:bold;">0.00</td>
                      </tr>
                      <tr>
                        <td style="background-color: #efefef !important;font-weight: bold;">Pendientes</td>
                        <td id="numero_cuota_pendiente" style="text-align: right;font-weight:bold;">0</td>
                        <td id="cuota_pendiente"  style="text-align: right;font-weight:bold;">0.00</td>
                      </tr>
                      <tr>
                        <td style="background-color: #efefef !important;color: #dc3545 !important;font-weight: bold;">Cumplido y Vencidos</td>
                        <td id="numero_cuota_vencida" style="color: #dc3545 !important;text-align: right;font-weight:bold;">0</td>
                        <td id="saldo_vencido" style="color: #dc3545 !important;text-align: right;font-weight:bold;">0.00</td>
                      </tr>
                  </tbody>
                </table>
                <table class="table table-bordered mt-2" id="table-prestamos">
                  <tbody>
                      <tr>
                        <td style="text-align: center;
    background-color: #bcbcbc !important;color: #000 !important;
    font-weight: bold;" >Saldo capital de deuda (S/.)</td>
                        <td id="saldo_capital" style="text-align: right;font-weight:bold;width: 50px;" colspan="2">0.00</td>
                      </tr>
                  </tbody>
                </table>
                
              </div>
              <div class="col-sm-9">
                <div class="row">
                  <div class="col-sm-2">
                    <div style="background-color: #bcbcbc;font-weight: bold;" class="p-1">

                    <div class="mb-1">
                      <span class="badge d-block">DETALLES DE PAGO</span>
                    </div>
                    <div class="row">
                      <label class="col-sm-6 col-form-label">Cant. C.</label>
                      <div class="col-sm-6">
                        <input type="text" value="0" class="form-control" style="text-align: right;background-color: #fff;" id="detalle_cantidad_cuotas" disabled>
                      </div>
                    </div>
                    <div class="row">
                      <label class="col-sm-6 col-form-label">Monto a P.</label>
                      <div class="col-sm-6">
                        <input type="text" class="form-control" style="background-color: #fff;" placeholder="0" id="detalle_monto_apagar" valida_input_vacio disabled/>
                      </div>
                    </div>
                    Custo., Comp. y Morat.
                    <input type="text" class="form-control" style="background-color: #fff;" placeholder="0" id="tenencia_penalidad_mora" valida_input_vacio disabled/>

                    <div class="mb-1 mt-1">
                      <span class="badge d-block">PAGO A CUENTA - <a href="javascript:;" onclick="ver_pagoacuenta()" style="color: #ffc107;">Ver</a></span>
                    </div>
                    <input type="text" value="0.00" disabled style="background-color: #fff;" class="form-control" id="pagoacuenta_acuenta" valida_input_vacio>

                    <div class="mb-1 mt-1">
                      <span class="badge d-block">CTA X COBRAR - <a href="javascript:;" onclick="ver_cuentasporcobrar()" style="color: #ffc107;">Ver</a></span>
                    </div>

                      <input type="text" class="form-control" style="background-color: #fff;" placeholder="0" id="detalle_porcobrar" valida_input_vacio disabled/>

                    <div class="mb-1 mt-1">
                      <span class="badge d-block">DESC. - CUOTA <span id="detalle_descuento_numerocuota">(0)</span> - <a href="javascript:;" onclick="ver_descuentos()" style="color: #ffc107;">Ver</a></span>
                    </div>
                    <input type="text" class="form-control" style="background-color: #fff;" placeholder="0" id="totaldescuento" disabled valida_input_vacio>

                    <div class="mb-1 mt-1">
                      <span class="badge d-block">TOTAL A PAGAR</span>
                    </div>
                    <input type="text" class="form-control" style="background-color: #fff;" placeholder="0" id="totalapagar" disabled valida_input_vacio>

                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="opcion_pago" id="pagocuota" onclick="pagocuota()"> Pago de Cuotas
                    </div>

                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="opcion_pago" id="pagoacuenta" onclick="pagoacuenta()"> Pago a Cuenta
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="opcion_pago" id="pagototal" onclick="pagototal()"> Total
                    </div>
                    <button type="submit" 
                            class="btn btn-primary w-100 mt-1" 
                            onclick="cobrar()">
                      <i class="fa-solid fa-check"></i> Cobrar
                    </button>
                    </div>
                  </div>
                  <div class="col-sm-10">
                    <div class="mb-1">
                      <span class="badge d-block">DATOS DE PRÉSTAMO</span>
                    </div>
                    <div id="table-datosprestamos" class="modal-body"></div>
                    <div id="table-datosprestamos_cronograma" class="modal-body" style="overflow-y: scroll;height: 260px;padding-top: 0px;padding-bottom: 0px;"></div>
                    <div id="opciones_datosprestamos" class="modal-body"></div>
                    <!--a href="javascript:;" class="btn btn-primary" onclick="ver_opciones(35)">opcion</a-->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
</div>
<script>
    valida_input_vacio();
    sistema_select2({ input:'#idcliente' });
    sistema_select2({ input:'#numero_cuotas' });
    $('#idbuscarcliente').click();
    
    //pagocuota();
  
  $('#idclientesearch').select2({
      ajax: {
          url:"{{url('backoffice/'.$tienda->id.'/cobranzacuota/show_credito')}}",
          dataType: 'json',
          delay: 250,
          data: function (params) {
              return {
                    buscar: params.term
              };
          },
          processResults: function (data) {
              return {
                  results: data
              };
          },
          cache: true
      },
      placeholder: '-- Seleccionar --',
      minimumInputLength: 2,
      theme: 'bootstrap-5',
      dropdownParent: $('#idclientesearch').parent().parent()
  });
  
  $("#idclientesearch").on("change", function(e) {
    lista_credito_cliente();
  });
  
  function lista_credito_cliente(){
    $.ajax({
      url:"{{url('backoffice/0/cobranzacuota/showlistacreditos')}}",
      type:'GET',
      data: {
          idcliente : $('#idclientesearch :selected').val()
      },
      success: function (res){
        
        $('.data-cliente').removeClass('d-none')
        $('#data-cliente-id').val(res.cliente.id);
        $('#data-cliente-nombre').val(res.cliente.identificacion+' - '+res.cliente.nombrecompleto);
        $('#table-detalle-prestamo > tbody').html(res.html);
        $("#exampleModal").modal('hide');
        //load_create_prestamo(res.cliente.id);
        $('#btn-create-cliente').removeClass('d-none');
        
        // limpiar
        $('#cont_irainicio').css('display','block');
        
        
        $('#idcredito').val('0');
        $('#table-datosprestamos').html('');
        $('#numero_cuotas').html('0');
        $('#detalle_descuento_numerocuota').html('(0)');

        /*$('#detalle_descuento_capital').val(respuesta.descuento_capital);
        $('#detalle_descuento_interes').val(respuesta.descuento_interes);
        $('#detalle_descuento_comision').val(respuesta.descuento_comision);
        $('#detalle_descuento_cargo').val(respuesta.descuento_cargo);
        $('#detalle_descuento_tenencia').val(respuesta.descuento_tenencia);
        $('#detalle_descuento_penalidad').val(respuesta.descuento_penalidad);
        $('#detalle_descuento_moratoria').val(respuesta.descuento_moratoria);
        $('#detalle_descuento_total').val(respuesta.descuento_total);*/
        $('#totaldescuento').val('0.00');
        
        
        $('#table-datosprestamos_cronograma').html('');
        $('#opciones_datosprestamos').html('');

        $('#estadocuotas').html('');
        $('#numerodecuenta').html('');
        $('#clasificacion').html('');
        $('#asesor').html('');
        $('#numero_cuota_cancelada').html('0');
        $('#numero_cuota_pendiente').html('0');
        $('#numero_cuota_vencida').html('0');
        $('#cuota_pagada').html('0.00');
        $('#cuota_pendiente').html('0.00');
        $('#saldo_vencido').html('0.00');
        $('#saldo_capital').html('0.00');
        $('#numero_credito').html('0.00');

        $('#detalle_cantidad_cuotas').val('0');
        $('#detalle_monto_apagar').val('0.00');
        $('#pagoacuenta_acuenta').val('0.00');
        $('#pagoacuenta_capital').val('0.00');
        $('#pagoacuenta_interes').val('0.00');
        $('#pagoacuenta_interescuotamora').val('0.00');

        /*$('#detalle_capital').val(respuesta.descuento_capital);
        $('#detalle_interes').val(respuesta.descuento_interes);
        $('#detalle_comision').val(respuesta.descuento_comision);
        $('#detalle_cargo').val(respuesta.descuento_cargo);
        $('#detalle_tenencia').val(respuesta.descuento_tenencia);
        $('#detalle_penalidad').val(respuesta.descuento_penalidad);
        $('#detalle_moratoria').val(respuesta.descuento_moratoria);
        $('#detalle_total').val(respuesta.descuento_total);*/

        //$('#detalle_total_pagar').val(respuesta.penalidad_pagar);
        
        $('#tenencia_penalidad_mora').val('0.00');
        $('#detalle_porcobrar').val('0.00');
        $('#totalapagar').val('0.00');
        
      }
    })
  }
  
  function buscarcliente(){
      setTimeout(function () { 
        $('#idclientesearch').select2('open');
      }, 500);
  }
  
    function pagocuota(numerocuota=0){
        $('#cont_pagocuotas').css('display','none');
        var pagocuota = $('#pagocuota:checked').val();
        if(pagocuota=='on'){
            cronograma($('#idcredito').val(),numerocuota,'pagocuota');
            $('#cont_pagocuotas').css('display','block');
        }
    }
    function pagoacuenta(){
        var pagoacuenta = $('#pagoacuenta:checked').val();
        if(pagoacuenta=='on'){
            cronograma($('#idcredito').val(),0,'pagoacuenta');
        }
    }
    function pagototal(){
        var pagototal = $('#pagototal:checked').val();
        if(pagototal=='on'){
            cronograma($('#idcredito').val(),1000,'pagototal');
        }
    }
  
    function cobrar(){
        let numerocuota = $('#table-detalle-cronograma > tbody > tr.seleccionar').attr('data-numerocuota');
        
      
        var idcredito = $('#idcredito').val();
        var pagocuota = $('#pagocuota:checked').val();
        var pagoacuenta = $('#pagoacuenta:checked').val();
        var pagototal = $('#pagototal:checked').val();
   
        var opcion_pago = '';
        if(pagocuota=='on' && numerocuota != undefined){
            opcion_pago = 'PAGO_CUOTA';
           
            /*if(numerocuota == "" || numerocuota == undefined ){
              alert('Debe de seleccionar mínimo una cuota!!!.');   
              return false;
            }*/
        }
        else if(pagoacuenta=='on'){
           opcion_pago = 'PAGO_ACUENTA';
           numerocuota = 0;
        }
        else if(pagototal=='on'){
           opcion_pago = 'PAGO_TOTAL';
        }else{
           numerocuota = 0;
        }
        modal({ route:'{{url('backoffice/'.$tienda->id.'/cobranzacuota')}}/'+idcredito+'/edit?view=cobrar'+
        '&opcion='+opcion_pago+
        '&numerocuota='+numerocuota+
        '&opcion_pago='+opcion_pago, 
        size: 'modal-sm' })
    }
  
    
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
    $('#table-detalle-prestamo tr.selected').removeClass('selected');
    $(e).addClass('selected');
    
    $('#pagocuota').prop("checked", true);
    $('#pagoacuenta').prop("checked", false);
    $('#pagototal').prop("checked", false);
    
    show_data_credito(id);
    
  }
  
  function show_data_credito(idcredito) {
    
        $.ajax({
            url:"{{url('backoffice/'.$tienda->id.'/cobranzacuota/show_cobranzacuota')}}",
            type:'GET',
            data: {
                idcredito : idcredito,
            },
            success: function (respuesta){
                $('#idcredito').val(respuesta.idcredito);
                $('#table-datosprestamos').html(respuesta.datosprestamos);
              
                $('#numero_cuotas').html(respuesta.numero_cuotas);
      
                $('#detalle_descuento_numerocuota').html(respuesta.descuento_numerocuota);
                
                $('#detalle_descuento_capital').val(respuesta.descuento_capital);
                $('#detalle_descuento_interes').val(respuesta.descuento_interes);
                $('#detalle_descuento_comision').val(respuesta.descuento_comision);
                $('#detalle_descuento_cargo').val(respuesta.descuento_cargo);
                $('#detalle_descuento_tenencia').val(respuesta.descuento_tenencia);
                $('#detalle_descuento_penalidad').val(respuesta.descuento_penalidad);
                $('#detalle_descuento_moratoria').val(respuesta.descuento_moratoria);
                $('#detalle_descuento_total').val(respuesta.descuento_total);
                $('#totaldescuento').val(respuesta.descuento_total);
            }
        });
      
        cronograma(idcredito,0,'pagocuota');
  }
    $("#numero_cuotas").on("select2:select", function(e) {
        cronograma($('#idcredito').val(),e.params.data.id);
    });
  
    function cronograma(idcredito,numero_cuotas=0,tipo,acuenta=0){
        $.ajax({
            url:"{{url('backoffice/'.$tienda->id.'/cobranzacuota/show_cobranzacuota_cronograma')}}",
            type:'GET',
            data: {
                idcredito : idcredito,
                numerocuota : numero_cuotas,
                tipo : tipo,
                acuenta : acuenta,
            },
            success: function (respuesta){
              
                $('#table-datosprestamos_cronograma').html(respuesta.tabla_cronorgrama);
                $('#opciones_datosprestamos').html(respuesta.opciones_datosprestamos);
                
                $('#estadocuotas').html(respuesta.estadocuotas);
                $('#numerodecuenta').html(respuesta.numerodecuenta);
                $('#clasificacion').html('<span style="background-color: #ffc107;padding-left: 5px;padding-right: 5px;">'+respuesta.clasificacion+'</span>');
                $('#asesor').html(respuesta.asesor);
                $('#numero_cuota_cancelada').html(respuesta.numero_cuota_cancelada);
                $('#numero_cuota_pendiente').html(respuesta.numero_cuota_pendiente);
                $('#numero_cuota_vencida').html(respuesta.numero_cuota_vencida);
                $('#cuota_pagada').html(respuesta.cuota_pagada);
                $('#cuota_pendiente').html(respuesta.cuota_pendiente);
                $('#saldo_vencido').html(respuesta.saldo_vencido);
                $('#saldo_capital').html(respuesta.saldo_capital);
                $('#numero_credito').html(respuesta.numero_credito);
              
                $('#detalle_cantidad_cuotas').val(respuesta.cantidad_cuota);
                $('#detalle_monto_apagar').val(respuesta.monto_totalapagar);
                $('#pagoacuenta_acuenta').val(respuesta.pagoacuenta_acuenta);
                $('#pagoacuenta_capital').val(respuesta.pagoacuenta_capital);
                $('#pagoacuenta_interes').val(respuesta.pagoacuenta_interes);
                $('#pagoacuenta_interescuotamora').val(respuesta.pagoacuenta_interescuotamora);
              
                $('#detalle_capital').val(respuesta.descuento_capital);
                $('#detalle_interes').val(respuesta.descuento_interes);
                $('#detalle_comision').val(respuesta.descuento_comision);
                $('#detalle_cargo').val(respuesta.descuento_cargo);
                $('#detalle_tenencia').val(respuesta.descuento_tenencia);
                $('#detalle_penalidad').val(respuesta.descuento_penalidad);
                $('#detalle_moratoria').val(respuesta.descuento_moratoria);
                $('#detalle_total').val(respuesta.descuento_total);
                
                $('#detalle_total_pagar').val(respuesta.penalidad_pagar);
                $('#tenencia_penalidad_mora').val(respuesta.tenencia_penalidad_mora);
                
                $('#detalle_porcobrar').val(respuesta.descuento_porcobrar);
              
                $('#totalapagar').val(respuesta.totalapagar);
                
                setTimeout(function () { 
                    $('#table-datosprestamos_cronograma').scrollTop((respuesta.select_ultimacuotacancelada*32)-32);
                }, 500);
              
                $('td#cont-popover-cuota').popover({
                  trigger: 'focus'
                });
                /*$('[data-bs-toggle="popover"]').popover({
                  trigger: 'focus'
                })*/
                //const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
                //const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))
                
            }
        })
    }
    
    function ver_descuentos(){
        modal({ route:'{{url('backoffice/'.$tienda->id.'/cobranzacuota')}}/'+$('#idcredito').val()+'/edit?view=ver_descuentos' })
    }
    function ver_pagoacuenta(){
        modal({ route:'{{url('backoffice/'.$tienda->id.'/cobranzacuota')}}/'+$('#idcredito').val()+'/edit?view=ver_pagoacuenta' })
    }
    function ver_cuentasporcobrar(){
        modal({ route:'{{url('backoffice/'.$tienda->id.'/cobranzacuota')}}/'+$('#idcredito').val()+'/edit?view=ver_cuentasporcobrar' })
    }
    
    
    function ver_opciones(idcobranzacuota,idestadocredito,entregargarantia){
        modal({ route:'{{url('backoffice/'.$tienda->id.'/cobranzacuota')}}/'+$('#idcredito').val()+'/edit?view=opcion&idcobranzacuota='+idcobranzacuota+'&idestadocredito='+idestadocredito+'&entregargarantia='+entregargarantia, size: 'modal-sm' })
    }
    
   function vistapreliminar(){

      let url = "{{ url('backoffice/'.$tienda->id) }}/cobranzacuota/"+$('#idcredito').val()+"/edit?view=vistapreliminar";
      modal({ route: url, size: 'modal-fullscreen' })
   }
  
   function congelarcredito(){

      let url = "{{ url('backoffice/'.$tienda->id) }}/cobranzacuota/"+$('#idcredito').val()+"/edit?view=congelarcredito";
      modal({ route: url, size: 'modal-sm' })
   }
</script>  

