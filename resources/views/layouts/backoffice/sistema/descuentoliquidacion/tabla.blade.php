<div class="modal-header">
    <h5 class="modal-title">
      Descuento y Liquidación
      <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="buscarcliente()">
        <i class="fa fa-search"></i> Buscar Cliente
      </button>
      
      

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
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-sm-12 col-md-3">
      <div class="row d-none data-cliente">
        <div class="col-sm-12">
          <label>Apellidos y Nombres: </label>
          <input type="text" disabled value="" class="form-control" id="data-cliente-nombre" style="background-color: white;">
          <input type="hidden" value="" class="form-control" id="data-cliente-id">
        </div>
        <div class="col-sm-12">
          <label>Documento de Identidad(RUC/DNI/CE): </label>
          <input type="text" disabled value="" class="form-control" id="data-cliente-documento" style="background-color: white;">
        </div>
        
      </div>
      <label>Préstamos: </label>
      <table class="table table-striped table-hover" id="table-detalle-prestamo">
        <thead class="table-dark">
          <tr>
            <th>MONTO</th>
            <th>F.C.</th>
            <th>N° CUENTA</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="2">SIN RESULTADOS</td>
          </tr>
        </tbody>
      </table>
      <div id="resumen-pagos-saldos" aria-live="polite">
        @include('layouts.backoffice.sistema.partials.resumen_pagos_saldos')
      </div>
      <div id="resumen-pagos-error" class="text-danger" role="alert"></div>
    </div>
    <div class="col-sm-12 col-md-9">
      <div class="card">
        <div class="card-body p-2" id="form-garantias-result">
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  var solicitudResumenPagos = null;

  function limpiar_resumen_pagos() {
    if (solicitudResumenPagos) {
      solicitudResumenPagos.abort();
      solicitudResumenPagos = null;
    }
    $('#resumen-pagos-error').text('');
    $('#resumen-pagos-saldos span[id]').not('#cont_listanegra').text('');
    $('#resumen-pagos-saldos td[id^="numero_cuota_"]').text('0');
    $('#cuota_pagada, #cuota_pendiente, #saldo_vencido, #saldo_capital').text('0.00');
  }

  function cargar_resumen_pagos(idcredito) {
    limpiar_resumen_pagos();
    solicitudResumenPagos = $.ajax({
      url: "{{url('backoffice/'.$tienda->id.'/cobranzacuota/show_cobranzacuota_cronograma')}}",
      type: 'GET',
      data: { idcredito: idcredito, numerocuota: 0, tipo: 'pagocuota', acuenta: 0 },
      success: function (res) {
        ['estadocuotas', 'numerodecuenta', 'asesor', 'numero_cuota_cancelada',
         'numero_cuota_pendiente', 'numero_cuota_vencida', 'cuota_pagada',
         'cuota_pendiente', 'saldo_vencido', 'saldo_capital'].forEach(function (campo) {
          $('#resumen-pagos-saldos #' + campo).text(res[campo]);
        });
        $('#resumen-pagos-saldos #clasificacion').text(res.clasificacion).css('background-color', '#ffc107');
        $('#resumen-pagos-saldos #resumen_cobranza').text(res.nombre_cobranza || '');
      },
      error: function (xhr, estado) {
        if (estado !== 'abort') {
          $('#resumen-pagos-error').text('No se pudo cargar el resumen de pagos y saldos. Seleccione el crédito nuevamente.');
        }
      }
    });
  }

  $('#idclientesearch').select2({
      ajax: {
          url:"{{url('backoffice/'.$tienda->id.'/descuentoliquidacion/show_credito')}}",
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
    lista_credito_cliente(e.currentTarget.value);
  });
  
  function buscarcliente(){
      setTimeout(function () { 
        $('#idclientesearch').select2('open');
      }, 500);
  }
  
  function lista_credito_cliente(id){
    limpiar_resumen_pagos();
    $('#cont_listanegra').empty();
    $('#form-garantias-result').empty();
    $.ajax({
      url:"{{url('backoffice/0/descuentoliquidacion/showlistacreditos')}}",
      type:'GET',
      data: {
          idcliente : id
      },
      success: function (res){
        
        $('.data-cliente').removeClass('d-none')
        $('#data-cliente-id').val(res.cliente.id);
        $('#data-cliente-nombre').val(res.cliente.nombrecompleto);
        $('#data-cliente-documento').val(res.cliente.identificacion);
        $('#table-detalle-prestamo > tbody').html(res.html);
        if (res.estado_listanegra == 2) {
          $('#cont_listanegra').html('<span style="background-color: #ffc9ca;padding-left: 5px;padding-right: 5px;border-radius: 5px;color: #93222c;">Cliente Observado</span>');
        }
        $("#exampleModal").modal('hide');
        $('#btn-create-cliente').removeClass('d-none');
        
      }
    })
  }
  
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
    cargar_resumen_pagos(id);
    $('#table-detalle-prestamo tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/descuentoliquidacion/"+id+"/edit?view=editar", result:'#form-garantias-result'});  
  }
</script>  

