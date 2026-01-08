<div class="modal-header">
  <h5 class="modal-title">Desembolso de Préstamos</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()" style="font-size: 20px;"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-credito-result">
             
            <div class="modal-body">
              
                <div class="row">
                  <div class="col-sm-12 col-md-12" style="text-align: center;">
                      <button type="button" class="btn btn-warning" onclick="vistapreliminar()"><i class="fa-solid fa-search"></i> VISTA PRELIMINAR</button>
                  </div>
                </div>
              
            </div> 
          </div>
        </div>
      </div>
  </div>
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">

            <table class="table table-striped table-hover" id="table-lista-credito">
              <thead class="table-dark">
                <tr>
                  <td>N°</td>
                  <td>CLIENTE</td>
                  <td>AVAL</td>
                  <td>DESEMBOLSO</td>
                  <td>CUOTAS</td>
                  <td>F. PAGO</td>
                  <td>F. APROBACIÓN</td>
                  <td>ASESOR/EJECUTIVO</td>
                  <td>MODA. CRÉDITO</td>
                </tr>
              </thead>
              <tbody>
              
              </tbody>
            </table>
          </div>
        </div>
      </div>
</div>
<script>
  var d= new Date();
  var fechatotal = `${d.getFullYear()}-${(d.getMonth() + 1)}-${d.getDate()}`;
  $("#fecha_fin").val(fechatotal);

  lista_credito();
  function lista_credito(){
    //let estado_credito = $('input[name="estado_credito"]:checked').val();
    
    $.ajax({
      url:"{{url('backoffice/0/desembolso/showtable')}}",
      type:'GET',
      data: {
          //estado : estado_credito,
          inicio : $('#fecha_inicio').val(),
          fin : $('#fecha_fin').val(),
      },
      success: function (res){
        $('#table-lista-credito > tbody').html(res.html);
        $("tr#show_data_select").on("click", function() {
            $('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        });
      }
    })
  }
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
    //$('tr.selected').removeClass('selected');
    //$(e).addClass('selected');
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/propuestacredito/"+id+"/edit?view=opciones" });  
  }
  function btnDetalleAprobacion(e) {
    let id = $(e).attr('data-valor-columna');
    //$('tr.selected').removeClass('selected');
    //$(e).addClass('selected');
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/propuestacredito/"+id+"/edit?view=detalle" });  
  }
 
   function vistapreliminar(){
      let idcredito = $('#table-lista-credito > tbody > tr.selected').attr('idcredito');
                        
      if(idcredito == "" || idcredito == undefined ){
        alert('Debe de seleccionar un crédito.');   
        return false;
      }
      let url = "{{ url('backoffice/'.$tienda->id) }}/desembolso/"+idcredito+"/edit?view=desembolsar";
      modal({ route: url, size: 'modal-fullscreen' })
   }

</script>  

