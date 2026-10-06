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
                <div class="col-sm-12 col-md-6">
                  <label for="idagencia">AGENCIA</label>
                  <select class="form-control" id="idagencia">
                    <option value="0" selected>TODO</option>
                    @foreach($agencias as $value)
                        <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-sm-12 col-md-6">
                  <label for="idasesor">ASESOR</label>
                  <select class="form-control" id="idasesor">
                    <option value="0">TODO</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <label for="idclientesearch">CLIENTE</label>
                  <select class="form-control" id="idclientesearch">
                     <option></option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Motivo Lista Negra -->
      <div class="modal fade" id="modalMotivoListaNegra" tabindex="-1" aria-labelledby="modalMotivoListaNegraLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="modalMotivoListaNegraLabel">Motivo - Lista Negra</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="alert alert-danger" id="motivo_listanegra_texto"></div>
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
            <th>FC</th>
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

  sistema_select2({ input:'#idagencia' });
  sistema_select2({ input:'#idasesor' });

  cargar_asesores($('#idagencia').val());

  $('#idclientesearch').select2({
      ajax: {
          url:"{{url('backoffice/'.$tienda->id.'/estadocuenta/show_credito')}}",
          dataType: 'json',
          delay: 250,
          data: function (params) {
              return {
                    buscar: params.term,
                    idagencia: $('#idagencia').val(),
                    idasesor: $('#idasesor').val()
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

  // El asesor depende de la agencia: al cambiarla se recargan los asesores de esa
  // agencia y se limpia el cliente, porque el que estaba elegido puede no
  // pertenecer ya a la nueva agencia.
  function cargar_asesores(idagencia){
      limpiar_cliente_buscado();

      $.ajax({
          url:"{{url('backoffice/'.$tienda->id.'/inicio/show_asesor')}}",
          type:'GET',
          data: {
              idtienda : idagencia
          },
          success: function (respuesta){
              // Se reemplaza el <select> completo, hay que rehacer el select2.
              if($('#idasesor').data('select2')){
                  $('#idasesor').select2('destroy');
              }
              $('#idasesor').html(respuesta);
              sistema_select2({ input:'#idasesor' });
          }
      })
  }

  // "change.select2" solo dispara los handlers internos del select2, por lo que
  // no dispara el onchange de arriba (que recarga la ficha del cliente).
  function limpiar_cliente_buscado(){
      $('#idclientesearch').val(null).trigger('change.select2');
  }

  $('#idagencia').on("change", function(e) {
      cargar_asesores($('#idagencia').val());
  });

  $('#idasesor').on("change", function(e) {
      limpiar_cliente_buscado();
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
      $('#iframe_acta_aprobacion').attr('src',"{{ url('/backoffice/'.$tienda->id.'/estadocuenta') }}/"+idcliente+"/edit?view=pdf_estado#zoom=100");
  }
  
  
  
  function ver_ultimo_evaluacion(idselectevaluacion){
      var idultimocredito_resumida = $('#idultimocredito_resumida').val();
      var idultimocredito_completa = $('#idultimocredito_completa').val();
    
      if(idselectevaluacion== 1 && idultimocredito_resumida==0){
        mensaje = 'No tiene un crédito con evaluación resumida';
        modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });
          return false;
      }
      else if(idselectevaluacion== 2 && idultimocredito_completa==0){
        mensaje = 'No tiene un crédito con evaluación completa';
        modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });
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
  
  function ver_motivo_listanegra(){
      $('#modalMotivoListaNegra').modal('show');
  }

  function buscarcliente(){
      // Se conservan los filtros de agencia/asesor, solo se limpia el texto
      // de busqueda para que el usuario escriba con el filtro ya aplicado.
      limpiar_cliente_buscado();
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
          $('#cont_listanegra').html('<span style="background-color: #ffc9ca;padding-left: 5px;padding-right: 5px;border-radius: 5px;color: #93222c; float: right;cursor: pointer;" onclick="ver_motivo_listanegra()">Cliente en Lista Negra</span>');
          $('#motivo_listanegra_texto').text(res.motivo_listanegra);
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
      $('#iframe_acta_aprobacion').attr('src',"{{ url('/backoffice/'.$tienda->id.'/estadocuenta') }}/"+id+"/edit?view=pdf_credito#zoom=100"); 
  }
</script>  

