<div class="modal-header">
  <h5 class="modal-title">Generación de Crédito <button type="button" class="btn btn-primary" id="btn-create-cliente" onclick="load_nuevo_credito()"><i class="fa-solid fa-plus"></i> Nuevo</button></h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()"></button>
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
            <h5 class="modal-title" style="margin-top: 10px;text-align: center;">Préstamos en Procesos</h5>
        <div class="card">
          <div class="card-body" style="height: calc(-380px + 100vh);">

            <table class="table table-striped table-hover" id="table-lista-credito">
              <thead class="table-dark">
                <tr>
                  <th>N°</th>
                  <th>CLIENTE</th>
                  <th>AVAL</th>
                  <th>PRODUCTO</th>
                  <th>PRÉSTAMO</th>
                  <th>ESTADO</th>
                  <th>FECHA</th>
                  <th width="10px"></th>
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

