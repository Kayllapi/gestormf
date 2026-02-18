<div class="modal-header">
    <h5 class="modal-title">
      Estado de Cuenta / Historial
      <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="buscarcliente()">
        <i class="fa fa-search"></i> Buscar Cliente
      </button>
      <div style="display:none;float: right;margin-left: 5px;" id="cont_irainicio">
      <button type="button" class="btn btn-primary" onclick="verpdf()">
        <i class="fa fa-refresh"></i> Actualizar
      </button>
      </div>
      <input type="hidden" id="idcliente_credito">

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
          <label>Documento de Identidad(DNI/CE): </label>
          <input type="text" disabled value="" class="form-control" id="data-cliente-documento" style="background-color: white;">
        </div>
        <input type="hidden" id="idultimocredito_resumida" value="0">
        <input type="hidden" id="idultimocredito_completa" value="0">
      </div>
      <label>Préstamos: </label>
      <span id="cont_listanegra"></span>
      <div style="overflow-y: scroll;height: calc(-246px + 100vh);">
      <table class="table table-striped table-hover" id="table-detalle-prestamo">
        <thead class="table-dark" style="position: sticky;top: 0;">
          <tr>
            <th>MONTO</th>
            <th></th>
            <th>ESTADO</th>
            <th>DESEMBOLSO</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="2">SIN RESULTADOS</td>
          </tr>
        </tbody>
      </table>
        </div>
    </div>
    <div class="col-sm-12 col-md-9">
      <div class="card">
        <div style="display:none;text-align: center;padding: 5px;" id="cont_opcioncredito">
            <button type="button" class="btn btn-primary" onclick="ver_ultimo_evaluacion(1)">
              <i class="fa fa-check"></i> Ver Última Evaluación (Resumida)
            </button>
            <button type="button" class="btn btn-primary" onclick="ver_ultimo_evaluacion(2)">
              <i class="fa fa-check"></i> Ver Última Evaluación (Completa)
            </button>
        </div>
        <iframe id="iframe_acta_aprobacion" frameborder="0" width="100%" style="height: calc(-170px + 100vh);"></iframe>
      </div>
    </div>
  </div>
</div>
<script>

  $('#idclientesearch').select2({
      ajax: {
          url:"{{url('backoffice/'.$tienda->id.'/estadocuenta/show_credito')}}",
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
      $('#idcliente_credito').val(e.currentTarget.value);
      verpdf();
      lista_credito_cliente(e.currentTarget.value);
      $("#exampleModal").modal('hide');
      
  });
  function verpdf(){
      //$('#cont_opcioncredito').css('display','none');
      $('#cont_irainicio').css('display','block');
      var idcliente = $('#idcliente_credito').val();
      $('#iframe_acta_aprobacion').attr('src',"{{ url('/backoffice/'.$tienda->id.'/estadocuenta') }}/"+idcliente+"/edit?view=pdf_estado#zoom=90");
  }
  
  
  
  function ver_ultimo_evaluacion(idselectevaluacion){
      var idultimocredito_resumida = $('#idultimocredito_resumida').val();
      var idultimocredito_completa = $('#idultimocredito_completa').val();
    
      if(idselectevaluacion== 1 && idultimocredito_resumida==0){
          alert('No tiene un crédito con evaluación resumida');
          return false;
      }
      else if(idselectevaluacion== 2 && idultimocredito_completa==0){
          alert('No tiene un crédito con evaluación completa');
          return false;
      }
    
     // var idevaluacion = $('#idevaluacion').val();
      if(idselectevaluacion==1){
          modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/propuestacredito/"+idultimocredito_resumida+"/edit?view=opciones" });
          //modal({ route:'{{url('backoffice/'.$tienda->id.'/credito')}}/'+idultimocredito_resumida+'/edit?view=evaluacion_resumida&detalle=false', size: 'modal-fullscreen' });
      }else if(idselectevaluacion==2 && idultimocredito_completa!=0){
          modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/propuestacredito/"+idultimocredito_completa+"/edit?view=opciones" });
          //modal({ route:'{{url('backoffice/'.$tienda->id.'/credito')}}/'+idultimocredito_completa+'/edit?view=evaluacion_cuantitativa&detalle=false', size: 'modal-fullscreen' });
      }
  }
  
  function buscarcliente(){
      setTimeout(function () { 
        $('#idclientesearch').select2('open');
      }, 500);
  }
  
  
  function lista_credito_cliente(id){
    $.ajax({
      url:"{{url('backoffice/0/estadocuenta/showlistacreditos')}}",
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
        $("#exampleModal").modal('hide');
        $('#btn-create-cliente').removeClass('d-none');
        $('#cont_listanegra').html('');
        if(res.estado_listanegra==2){
            $('#cont_listanegra').html('<span style="background-color: #d21212;padding-left: 5px;padding-right: 5px;border-radius: 5px;color: white;">Cliente en Lista Negra</div>');
        }
        
        $('#idultimocredito_resumida').val(res.idultimocredito_resumida);
        $('#idultimocredito_completa').val(res.idultimocredito_completa);
        //$('#idevaluacion').val(res.idevaluacion);
        
        $('#cont_opcioncredito').css('display','block');
   
        
      }
    })
  }
  
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
    $('#table-detalle-prestamo tr.selected').removeClass('selected');
    $(e).addClass('selected');
      $('#iframe_acta_aprobacion').attr('src',"{{ url('/backoffice/'.$tienda->id.'/estadocuenta') }}/"+id+"/edit?view=pdf_credito#zoom=90"); 
  }
</script>  

