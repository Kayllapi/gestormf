<div class="modal-header">
  <h5 class="modal-title">Tasas Activas <button type="button" class="btn btn-primary" id="btn-create-cliente" onclick="load_nuevo_tarifario()"><i class="fa-solid fa-plus"></i> Nuevo</button></h5>
  
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
          <div class="card-body" style="overflow-y: scroll;height: 260px;padding-top: 0px;padding-bottom: 0px;">
            
            <table class="table table-striped table-hover" id="table-lista-credito">
              <thead class="table-dark" style="position: sticky;top: 0;">
                <tr>
                  <td>N°</td>
                  <td>CÓDIGO</td>
                  <td>MONTO (<=)</td>
                  <td>CUOTA (<=)</td>
                  <td>TEM %</td>
                  <td>SERVICIOS/OTROS %</td>
                  <td>FORMA DE PAGO</td>
                  <td>TIPO DE CRÉDITO</td>
                  <td>PRODUCTO</td>
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
//     lista_tarifario(e.currentTarget.value);
//   });
  lista_tarifario();
  function lista_tarifario(id){
    let tipo_producto_credito = $('#tipo_producto_credito').val();
    let idcredito_prendatario = $('#idcredito_prendatario').val();
    let idforma_pago_credito = $('#idforma_pago_credito').val();
    
    let tipo = $("#idforma_credito").find('option:selected').val();
    
    $.ajax({
      url:"{{url('backoffice/0/tarifario/showtarifario')}}",
      type:'GET',
      data: {
          idcliente : id,
          tipo_producto_credito : tipo_producto_credito,
          idcredito_prendatario : idcredito_prendatario,
          idforma_pago_credito : idforma_pago_credito,
          tipo : tipo,
          
      },
      success: function (res){
        $('#table-lista-credito > tbody').html(res.html);
      }
    })
  }
  
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
        
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
//     modal({route:"{{url('backoffice')}}/{{$tienda->id}}/tarifario/"+id+"/edit?view=editar"}); 
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/tarifario/"+id+"/edit?view=editar", result:'#form-credito-result'});
    
  }
  load_nuevo_tarifario();
  function load_nuevo_tarifario(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/tarifario/create?view=registrar')}}", result:'#form-credito-result'});
  }
//   load_nuevo_tarifario();

</script>  

