<div class="modal-header">
    <h5 class="modal-title">
      Garantía Regular
      <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="buscarcliente()">
        <i class="fa fa-user"></i> Buscar Cliente
      </button>
      
      <button type="button" class="btn btn-primary d-none" id="btn-create-cliente" onclick="load_create_garantianoprendaria()"><i class="fa-solid fa-plus"></i> NUEVA GARANTÍA</button>

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
    <div class="col-sm-12 col-md-4">
      <div class="row d-none data-cliente">
        <div class="col-sm-12">
          <label>Apellidos y Nombres: </label>
          <input type="text" disabled value="" class="form-control" id="data-cliente-nombre">
          <input type="hidden" value="" class="form-control" id="data-cliente-id">
        </div>
        <div class="col-sm-12">
          <label>Documento de Identidad (RUC/DNI/CE): </label>
          <input type="text" disabled value="" class="form-control" id="data-cliente-documento">
        </div>
        
      </div>
      <label>Garantías: </label>
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
          <div class="alert alert-light mb-1 mt-1 py-0 text-start d-none" id="alert-garantia-1" style="color: #000;">
            Garantía sin Préstamo
          </div>
          <!--div class="alert mb-1 text-start py-0 d-none" style="background-color: #6bc5ff;" id="alert-garantia-2">
            Garantía por Recoger
          </div-->
          <div class="alert mb-1 text-start py-0 d-none" style="background-color: #3cd48d;" id="alert-garantia-3">
            Garantía con Préstamo
          </div>
        </div>
      </div>
          <div class="alert mb-1 text-start py-0 d-none" id="cont-ultimamodificacion" 
               style="background-color: #ffffff;border: 1px solid grey;">
              <b>MODIFICADO POR:</b> <span id="alert-ultimamodificacion" style="font-weight: normal;"></span>
          </div>
      <button type="button" class="btn btn-danger mt-2 d-none" id="btn-delete-garantia" onclick="eliminar_garantianoprendaria()"><i class="fa-solid fa-trash"></i> Eliminar Garantia</button>
      <button type="button" class="btn btn-success mt-2 d-none" id="btn-autorizar-garantia" onclick="modificar_garantianoprendaria()"><i class="fa-solid fa-pencil"></i> Editar Garantia</button>
    
    </div>
    <div class="col-sm-12 col-md-8">
      <div class="card">
        <div class="card-body p-2" id="form-garantias-noprendario-result">
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
      url:"{{url('backoffice/0/garantiasnoprendario/showlistagarantiasnopredanrio')}}",
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
        load_create_garantianoprendaria(res.cliente.id);
        $('#btn-create-cliente').removeClass('d-none');
      }
    })
  }
  
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/garantiasnoprendario/"+id+"/edit?view=editar", result:'#form-garantias-noprendario-result'});  
  }
  
  function load_create_garantianoprendaria(idcliente = 0){
    if(idcliente == 0){
       idcliente = $('#data-cliente-id').val()
    }
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/garantiasnoprendario/create?view=registrar')}}&idcliente="+idcliente, result:'#form-garantias-noprendario-result'});
  }
//   load_create_garantianoprendaria();

</script>  

