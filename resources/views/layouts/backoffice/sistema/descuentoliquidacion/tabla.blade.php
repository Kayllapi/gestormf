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
          <label>Documento de Identidad(DNI/CE): </label>
          <input type="text" disabled value="" class="form-control" id="data-cliente-documento" style="background-color: white;">
        </div>
        
      </div>
      <label>Préstamos: </label>
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
            <td colspan="2">SIN RESULTADOS</td>
          </tr>
        </tbody>
      </table>
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
        $("#exampleModal").modal('hide');
        $('#btn-create-cliente').removeClass('d-none');
        
      }
    })
  }
  
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
    $('#table-detalle-prestamo tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/descuentoliquidacion/"+id+"/edit?view=editar", result:'#form-garantias-result'});  
  }
</script>  

