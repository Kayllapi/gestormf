<div class="modal-header">
    <h5 class="modal-title">
      Fenomenos
      <a href="javascript:;" 
         class="btn btn-primary" 
         onclick="load_nuevo_fenomenos()">
        <i class="fa-solid fa-plus"></i> Registrar
      </a>
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()" style="font-size: 20px;"></button>
</div>

<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-result-giro">
          </div>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            
            <table class="table table-striped table-hover" id="table-lista-fenomenos">
              <thead class="table-dark">
                <tr>
                  <td width="10px"></td>
                  <td>Nombre</td>
                  <td>Estado</td>
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
<script>
  lista_fenomenos();
  function lista_fenomenos(id){
    let estado = $('#estado').val();
    $.ajax({
      url:"{{url('backoffice/0/fenomenos/show_table')}}",
      type:'GET',
      data: {
          estado : estado,
      },
      success: function (res){
        $('#table-lista-fenomenos > tbody').html(res.html);
      }
    })
  }
  load_nuevo_fenomenos();
  function load_nuevo_fenomenos(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/fenomenos/create?view=registrar')}}", result:'#form-result-giro'});
  }
  
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
        
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/fenomenos/"+id+"/edit?view=editar", result:'#form-result-giro'});
    
  }
</script>

