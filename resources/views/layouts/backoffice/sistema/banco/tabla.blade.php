<div class="modal-header">
  <h5 class="modal-title">Bancos <button type="button" class="btn btn-primary" id="btn-create-cliente" onclick="load_nuevo_feriado()"><i class="fa-solid fa-plus"></i> Nuevo</button></h5>
  
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
        <div class="card">
          <div class="card-body">
            
            <table class="table table-striped table-hover" id="table-lista-banco">
              <thead class="table-dark">
                <tr>
                  <td>N°</td>
                  <td>Banco</td>
                  <td>Cuenta</td>
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

  lista_feriado();
  function lista_feriado(){
    $.ajax({
      url:"{{url('backoffice/0/banco/showbanco')}}",
      type:'GET',
      data: {
          
      },
      success: function (res){
        $('#table-lista-banco > tbody').html(res.html);
      }
    })
  }
  
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
        
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/banco/"+id+"/edit?view=editar", result:'#form-credito-result'});
    
  }
  load_nuevo_feriado();
  function load_nuevo_feriado(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/banco/create?view=registrar')}}", result:'#form-credito-result'});
  }

</script>  

