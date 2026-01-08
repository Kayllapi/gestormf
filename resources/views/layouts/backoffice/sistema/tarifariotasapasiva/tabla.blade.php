<div class="modal-header">
  <h5 class="modal-title">Tasas Pasivas <button type="button" class="btn btn-primary" id="btn-create-cliente" onclick="load_form_create()"><i class="fa-solid fa-plus"></i> Nuevo</button></h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()" style="font-size: 20px;"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-create-result">
          </div>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body" style="overflow-y: scroll;height: 260px;padding-top: 0px;padding-bottom: 0px;">
            
            <table class="table table-striped table-hover" id="table-list-data">
              <thead class="table-dark" style="position: sticky;top: 0;">
                <tr>
                  <td>N°</td>
                  <td>CODIGO</td>
                  <td>MONTO (<=)</td>
                  <td>TEA%</td>
                  <td>TIPO DE AHORRO</td>
                  <td>PRODUCTO</td>
                  <td>PLAZO</td>
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
  
  show_data();
  function show_data(id=0){
    $.ajax({
      url:"{{url('backoffice/0/tarifariotasapasiva/showtable')}}",
      type:'GET',
      data: {
          idtipo_ahorro : id
      },
      success: function (res){
        $('#table-list-data > tbody').html(res.html);
      }
    })
  }
  
  function load_form_edit(e) {
    let id = $(e).attr('data-valor-columna');
        
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
//     modal({route:"{{url('backoffice')}}/{{$tienda->id}}/tarifariotasapasiva/"+id+"/edit?view=editar"}); 
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/tarifariotasapasiva/"+id+"/edit?view=editar", result:'#form-create-result'});
    
  }
  load_form_create();
  function load_form_create(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/tarifariotasapasiva/create?view=registrar')}}", result:'#form-create-result'});
  }
//   load_form_create();

</script>  

