<div class="modal-header">
  <h5 class="modal-title">
    Créditos Prendarias 
    <button type="button" class="btn btn-primary" id="btn-create-cliente" onclick="load_nuevo_credito()"><i class="fa-solid fa-plus"></i> Nuevo</button>
    <button type="button" class="btn btn-primary" id="btn-create-cliente" onclick="diasdegracia()"> Límites de los días de gracia</button>
  </h5>
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
          <div class="card-body" style="
            overflow-y: scroll;
            height: calc(100vh - 330px);
            padding-top: 0px;
            padding-bottom: 0px;">
            
            <table class="table table-striped table-hover" id="table-lista-credito">
              <thead class="table-dark" style="position: sticky;top: 0;">
                <tr>
                  <td>N°</td>
                  <td>CODIGO</td>
                  <td>PRODUCTO</td>
                  <td>MODALIDAD DE CALCULO</td>
                  <td>GARANTIA PRENDARIA</td>
                  <td>ESTADO</td>
                  <td>CON EVALUACION</td>
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
  sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idclientesearch' });
//   $("#idclientesearch").on("change", function(e) {
//     lista_garantias_cliente(e.currentTarget.value);
//   });
  lista_garantias_cliente();
  function lista_garantias_cliente(id){
    $.ajax({
      url:"{{url('backoffice/0/creditoprendatario/showcreditos')}}",
      type:'GET',
      data: {
          idcliente : id
      },
      success: function (res){
        $('#table-lista-credito > tbody').html(res.html);
      }
    })
  }
  function diasdegracia(){
    modal({route:"{{url('backoffice')}}/{{$tienda->id}}/creditoprendatario/0/edit?view=diasdegracia"}); 
  }
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
        
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
//     modal({route:"{{url('backoffice')}}/{{$tienda->id}}/creditoprendatario/"+id+"/edit?view=editar"}); 
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/creditoprendatario/"+id+"/edit?view=editar", result:'#form-credito-result'});
    
  }
  load_nuevo_credito();
  function load_nuevo_credito(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/creditoprendatario/create?view=registrar')}}", result:'#form-credito-result'});
  }
//   load_nuevo_credito();

</script>  

