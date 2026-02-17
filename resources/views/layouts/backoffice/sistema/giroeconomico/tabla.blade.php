<div class="modal-header">
    <h5 class="modal-title">
      Giro Económico
      <a href="javascript:;" 
         class="btn btn-primary" 
         onclick="load_nuevo_giro()">
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
          <div class="card-body" style="
            overflow-y: scroll;
            height: calc(100vh - 295px);
            padding-top: 0px;
            padding-bottom: 0px;">
            
            <table class="table table-striped table-hover" id="table-lista-giro">
              <thead class="table-dark" style="position: sticky;top: 0;">
                <tr>
                  <td width="10px"></td>
                  <td>Tipo de Giro</td>
                  <td>Giro Económico</td>
                  <td>Margen de Vta. Máximo (%)</td>
                  <td>Estado</td>
                  <td></td>
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
  lista_giro();
  function lista_giro(id){
    let idtipo_giro_economico = $('#idtipo_giro_economico').val();
    let estado = $('#estado').val();
    $.ajax({
      url:"{{url('backoffice/0/giroeconomico/show_table')}}",
      type:'GET',
      data: {
          idtipo_giro_economico : idtipo_giro_economico,
          estado : estado,
      },
      success: function (res){
        $('#table-lista-giro > tbody').html(res.html);
      }
    })
  }
  load_nuevo_giro();
  function load_nuevo_giro(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/giroeconomico/create?view=registrar')}}", result:'#form-result-giro'});
  }
  
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
        
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/giroeconomico/"+id+"/edit?view=editar", result:'#form-result-giro'});
    
  }
</script>

