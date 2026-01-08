<div class="modal-header">
  <h5 class="modal-title">Feriados <button type="button" class="btn btn-primary" id="btn-create-cliente" onclick="load_nuevo_feriado()"><i class="fa-solid fa-plus"></i> Nuevo</button></h5>
  
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
        <div class="card">
          <div class="card-body" style="overflow-y: scroll;height: 260px;padding-top: 0px;padding-bottom: 0px;" id="cont-table-datosprestamos_cronograma">
            
            <table class="table table-striped table-hover" id="table-lista-feriados">
              <thead class="table-dark" style="position: sticky;top: 0;">
                <tr>
                  <td>N°</td>
                  <td>Fecha</td>
                  <td>Motivo</td>
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
      url:"{{url('backoffice/0/feriados/showferiados')}}",
      type:'GET',
      data: {
          
      },
      success: function (res){
          $('#table-lista-feriados > tbody').html(res.html);
          
          $('#cont-table-datosprestamos_cronograma').scrollTop(10000);
      }
    })
  }
  
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
        
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/feriados/"+id+"/edit?view=editar", result:'#form-credito-result'});
    
  }
  load_nuevo_feriado();
  function load_nuevo_feriado(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/feriados/create?view=registrar')}}", result:'#form-credito-result'});
  }

</script>  

