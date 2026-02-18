<div class="modal-header">
    <h5 class="modal-title">
     Recepcionar la Asignación, Incremento y Reducción de Capital
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>

<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2">
            <div class="modal-body" style="text-align: center;">
     
                    <button type="button" class="btn btn-success" onclick="recepcionar()" id="cont_recepcionar" 
                            style="font-weight: bold;">
                      <i class="fa-solid fa-check" style="font-weight: bold;"></i> RECEPCIONAR</button>
       
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body" style="
            overflow-y: scroll;
            height: calc(100vh - 266px);
            padding: 0;
            margin-top: 5px;
            overflow-x: scroll;">
            <table class="table table-striped table-hover table-bordered" id="table-lista-asignacioncapital">
              <thead class="table-dark" style="position: sticky;top: 0;">
                <tr>
                  <td>Agencia</td>
                  <td>Fecha</td>
                  <td>N° Operación</td>
                  <td>Tipo de operación</td>
                  <td>Destino de Depósito / Fuente de Retiro</td>
                  <td>Monto (S/.)</td>
                  <td>Banco</td>
                  <td>N° operación (banco)</td>
                  <td>Descripción</td>
                  <td style="width:80px;">Usuario Emisor</td>
                  <td style="width:90px;">Usuario  Rec. Final Efectivo</td>
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
  lista_asignacioncapital();
  function lista_asignacioncapital(id){
    var fechainicio = $('#fechainicio').val();
    var fechafin = $('#fechafin').val();
    $.ajax({
      url:"{{url('backoffice/0/recepcionarasignacioncapital/show_table')}}",
      type:'GET',
      data:{
          fechainicio: $('#fechainicio').val(),
          fechafin: $('#fechafin').val(),
      },
      success: function (res){
          $('#table-lista-asignacioncapital > tbody').html(res.html);
          lista_saldocapitalasignado();
      }
    })
  }
  
  function show_data(e) {
      let id = $(e).attr('data-valor-columna');

      $('tr.selected').removeClass('selected');
      $(e).addClass('selected');
      pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/recepcionarasignacioncapital/"+id+"/edit?view=editar", result:'#form-result-giro'});

            
      let idresponsable_recfinal = $(e).attr('idresponsable_recfinal');
      //console.log(idresponsable_recfinal)
      $('#cont_recepcionar').css('display','none'); 
      $('#cont_voucher').css('display','none');         
      if(idresponsable_recfinal == 0){
          $('#cont_recepcionar').css('display','inline-block');
      }else{
          $('#cont_voucher').css('display','inline-block');
          
      }
  }
   function recepcionar(){
      let idasignacioncapital = $('#table-lista-asignacioncapital > tbody > tr.selected').attr('data-valor-columna');
                        
      if(idasignacioncapital == "" || idasignacioncapital == undefined ){
        alert('Debe de seleccionar una Asignación, Incremento ó Reducción de Capital.');   
        return false;
      }
      let url = "{{ url('backoffice/'.$tienda->id) }}/recepcionarasignacioncapital/"+idasignacioncapital+"/edit?view=recepcionar";
      modal({ route: url })
   }
   function voucher(){
      let idasignacioncapital = $('#table-lista-asignacioncapital > tbody > tr.selected').attr('data-valor-columna');
                        
      if(idasignacioncapital == "" || idasignacioncapital == undefined ){
        alert('Debe de seleccionar una Asignación, Incremento ó Reducción de Capital.');   
        return false;
      }
      let url = "{{ url('backoffice/'.$tienda->id) }}/recepcionarasignacioncapital/"+idasignacioncapital+"/edit?view=voucher";
      modal({ route: url })
   }
</script>

