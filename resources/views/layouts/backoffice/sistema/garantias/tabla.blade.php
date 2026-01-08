<div class="modal-header">
    <h5 class="modal-title">
      Garantia Prendaria
      
<!--       <a href="javascript:;" 
         class="btn btn-primary" 
         onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/garantias/create?view=registrar')}}'})">
        <i class="fa-solid fa-plus"></i> Registrar
      </a> -->
      <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="buscarcliente()">
        <i class="fa fa-user"></i> Buscar Cliente
      </button>
      
      <button type="button" class="btn btn-primary d-none" id="btn-create-cliente" onclick="load_create_garantia()"><i class="fa-solid fa-plus"></i> NUEVA GARANTIA</button>
      <button type="button" class="btn btn-primary d-none" id="btn-create-depositario" onclick="load_create_depositario()">
        <i class="fa-solid fa-list"></i> Gestión de Depósitario y Poliza de Seguros</button>

      <!-- Modal -->
      <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5 text-white" id="exampleModalLabel">Buscar Cliente</h1>
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
    <div class="col-sm-12 col-md-4">
      <div class="row d-none data-cliente">
        <div class="col-sm-12">
          <label>Apellidos y Nombres: </label>
          <input type="text" disabled value="" class="form-control" id="data-cliente-nombre">
          <input type="hidden" value="" class="form-control" id="data-cliente-id">
        </div>
        <div class="col-sm-12">
          <label>Documento de Identidad(DNI/CE): </label>
          <input type="text" disabled value="" class="form-control" id="data-cliente-documento">
        </div>
        
      </div>
      <label>Garantias: </label>
      <table class="table table-striped table-hover" id="table-detalle-garantia">
        <thead class="table-dark">
          <tr>
            <th>DESCRIPCIÓN</th>
            <th>COBERTURA</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="2">SIN RESULTADOS</td>
          </tr>
        </tbody>
      </table>
      <hr>
      <div class="row">
        <div class="col-md-6">
          <div class="alert alert-light mb-1 mt-1 py-0 text-start d-none" id="alert-garantia-1">
            Garantía sin Préstamo
          </div>
          <div class="alert mb-1 text-start py-0 d-none" style="background-color: #40a7e9;" id="alert-garantia-2">
            Garantía por Recoger
          </div>
          <div class="alert mb-1 text-start py-0 d-none" style="background-color: #0fb669;" id="alert-garantia-3">
            Garantía con Préstamo
          </div>
        </div>
      </div>
          <div class="alert mb-1 text-start py-0 d-none" id="cont-ultimamodificacion" 
               style="background-color: #ffffff;border: 1px solid grey;">
              <b>MODIFICADO POR:</b> <span id="alert-ultimamodificacion"></span>
          </div>
      <button type="button" class="btn btn-danger mt-2 d-none" id="btn-delete-garantia" onclick="eliminar_garantia()"><i class="fa-solid fa-trash"></i> Eliminar Garantia</button>
      <button type="button" class="btn btn-success mt-2 d-none" id="btn-autorizar-garantia" onclick="modificar_garantia(1)"><i class="fa-solid fa-pencil"></i> Editar Garantia</button>
      <button type="button" class="btn btn-success mt-2 d-none" id="btn-autorizar-depositario" onclick="modificar_garantia(2)"><i class="fa-solid fa-pencil"></i> Editar Gestión de Depósitario y Poliza de Seguros</button>
    </div>
    <div class="col-sm-12 col-md-8">
      <div class="card">
        <div class="card-body p-2" id="form-garantias-result">
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idclientesearch' });
  $("#idclientesearch").on("change", function(e) {
    lista_garantias_cliente(e.currentTarget.value);
  });
  
  function buscarcliente(){
      setTimeout(function () { 
        $('#idclientesearch').select2('open');
      }, 500);
  }
  
  function lista_garantias_cliente(id){
    $.ajax({
      url:"{{url('backoffice/0/garantias/showlistagarantias')}}",
      type:'GET',
      data: {
          idcliente : id
      },
      success: function (res){
        
        $('.data-cliente').removeClass('d-none')
        $('#data-cliente-id').val(res.cliente.id);
        $('#data-cliente-nombre').val(res.cliente.nombrecompleto);
        $('#data-cliente-documento').val(res.cliente.identificacion);
        $('#table-detalle-garantia > tbody').html(res.html);
        $("#exampleModal").modal('hide');
        load_create_garantia(res.cliente.id);
        $('#btn-create-cliente').removeClass('d-none');
        $('#btn-create-depositario').removeClass('d-none');
      }
    })
  }
  
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
        
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/garantias/"+id+"/edit?view=editar", result:'#form-garantias-result'});  
  }
  
  function load_create_garantia(idcliente = 0){
    if(idcliente == 0){
       idcliente = $('#data-cliente-id').val()
    }
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/garantias/create?view=registrar')}}&idcliente="+idcliente, result:'#form-garantias-result'});
  }
  
  function load_create_depositario(){
    var idcliente = $('#data-cliente-id').val()
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/garantias/create?view=depositario')}}&idcliente="+idcliente, result:'#form-garantias-result'});
  }
//   load_create_garantia();

</script>  

