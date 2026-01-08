<div class="modal-header">
  <h5 class="modal-title">Generación de Crédito <button type="button" class="btn btn-primary" id="btn-create-cliente" onclick="load_nuevo_credito()"><i class="fa-solid fa-plus"></i> Nuevo</button></h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()" style="font-size: 20px;"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-credito-result">
          </div>
        </div>
      </div>
      <div class="col-sm-12">
            <h5 class="modal-title" style="margin-top: 10px;text-align: center;">Prestamos en Procesos</h5>
        <div class="card">
          <div class="card-body">

            <table class="table table-striped table-hover" id="table-lista-credito">
              <thead class="table-dark">
                <tr>
                  <td>N°</td>
                  <td>CLIENTE</td>
                  <td>AVAL</td>
                  <td>PRODUCTO</td>
                  <td>PRESTAMO</td>
                  <td>ESTADO</td>
                  <td>FECHA</td>
                  <td width="10px"></td>
                </tr>
              </thead>
              <tbody>
              
              </tbody>
            </table>
          </div>
        </div>
      </div>
  </div>
</div>
<style>
  #menu-opcion ul {
      background-color: #0a58ca;
  }
  #menu-opcion ul li a.dropdown-item {
      color: #fff;
  }
  #menu-opcion ul li a.dropdown-item:hover {
      color: #181818 !important;
      background-color: #dfdf79;
  }
</style>
<script>
  

  lista_credito();
  function lista_credito(id){
    $.ajax({
      url:"{{url('backoffice/0/credito/showtable')}}",
      type:'GET',
      data: {
//           idcliente : id
      },
      success: function (res){
        $('#table-lista-credito > tbody').html(res.html);
        $("tr#show_data_select").on("click", function() {
            $('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        });
        /*$("tr#show_data").on("dblclick", function(e) {
            show_data(this);
        });*/
      }
    })
  }
  function btnEditar(id){
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/credito/"+id+"/edit?view=editar", result:'#form-credito-result', carga:'false'});
  }
  function btnEliminar(id){
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/credito/"+id+"/edit?view=eliminar" });  
  }

  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
    //$('tr.selected').removeClass('selected');
    //$(e).addClass('selected');
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/credito/"+id+"/edit?view=opciones" });  
  }
  load_nuevo_credito();
  function load_nuevo_credito(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/credito/create?view=registrar')}}", result:'#form-credito-result'});
  }

</script>  

