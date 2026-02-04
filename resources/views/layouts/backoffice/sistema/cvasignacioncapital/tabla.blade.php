<div class="modal-header">
    <h5 class="modal-title">
     Asignación, Incremento y Reducción de Capital
      <a href="javascript:;" 
         class="btn btn-primary" 
         onclick="load_nuevo_asignacioncapital()">
        <i class="fa-solid fa-plus"></i> Nuevo
      </a>
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>

<div class="modal-body">
  <div class="row">
        @if(!$validacionDiaria['arqueocaja'])
            <div class="modal-body" style="position: absolute; z-index: 100;">
                <div class="alert bg-danger" style="height: 110px;">
                <br>
                <i class="fa fa-warning" style="font-size: 35px;"></i> <br>
                <b>Falta arquear caja {{ $validacionDiaria['fechacorte'] }}!!</b>
                </div>
            </div>
        @elseif(!$validacionDiaria['cierre_caja'])
            <div class="modal-body" style="position: absolute; z-index: 100;">
                <div class="alert bg-danger" style="height: 110px;">
                <br>
                <i class="fa fa-warning" style="font-size: 35px;"></i> <br>
                <b>Falta cerrar caja {{ $validacionDiaria['fechacorte'] }}!!</b>
                </div>
            </div>
        @elseif($arqueocaja)
          <div class="modal-body" style="position: absolute; z-index: 100;">
            <div class="alert bg-danger" style="height: 110px;">
            <i class="fa fa-warning" style="font-size: 35px;"></i> <br>
            <b>Ya esta arqueado la caja!!</b>
            </div>
          </div>
      @endif
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-result-giro">
          </div>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2">
            <div class="modal-body">
              <div class="row">
                <label class="col-sm-3 col-form-label" style="text-align: right;">Fecha inicio</label>
                <div class="col-sm-2">
                  <input type="date" class="form-control" id="fechainicio" value="{{now()->format('Y-m-d')}}">
                </div>
                <label class="col-sm-1 col-form-label" style="text-align: right;">Fecha fin:</label>
                <div class="col-sm-2">
                  <input type="date" class="form-control" id="fechafin" value="{{now()->format('Y-m-d')}}">
                </div>
                <div class="col-sm-1">
                  <button type="button" class="btn btn-primary" onclick="lista_asignacioncapital()" style="font-weight: bold;">
                                  <i class="fa-solid fa-search"></i> 
                    Filtrar</button>
                </div>
                <div class="col-md-3" style="text-align: right;">
                    <div>
                    <!--button type="button" class="btn btn-success mb-1" onclick="recepcionar()" id="cont_recepcionar" style="font-weight: bold; display:none;">
                      <i class="fa-solid fa-check" style="font-weight: bold;"></i> RECEPCIONAR</button-->
                      <button type="button" class="btn btn-primary mb-1" onclick="voucher()" id="cont_voucher" style="font-weight: bold; display:none">
                        <i class="fa-solid fa-list" style="font-weight: bold;"></i> VOUCHER
                      </button>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body" style="overflow-y: scroll;height: 200px;padding: 0;margin-top: 5px;overflow-x: scroll;">
            
            <table class="table table-striped table-hover" id="table-lista-asignacioncapital">
              <thead class="table-dark" style="position: sticky;top: 0; font-weight: bold;">
                <tr>
                  <td>Agencia</td>
                  <td>Fecha</td>
                  <td>N° Operación</td>
                  <td>Tipo de operación</td>
                  <td>Destino de Depósito / Fuente de Retiro</td>
                  <td>Monto (S/.)</td>
                  <td>Banco</td>
                  <td>Validación</td>
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
  <!--div style="text-align: right;">
    <button type="button" class="btn btn-info" onclick="exportar_pdf()" style="font-weight: bold;">
      <i class="fa-solid fa-file-pdf" style="color:#000 !important;font-weight: bold;"></i> REPORTE PDF</button>
  </div-->
  <div class="row">
      <div class="col-sm-4">
          <div class="mb-1">
            <span class="badge d-block" style="margin-top: 10px;">SALDO DE CAPITAL ASIGNADO</span>
          </div>
         <div class="card">
          <div class="card-body" style="overflow-y: scroll;height: 150px;padding: 0;margin-top: 5px;overflow-x: scroll;">
            
          <table class="table table-striped table-hover table-bordered" id="table-lista-saldocapitalasignado">
            <thead class="table-dark" style="position: sticky;top: 0;">
              <tr>
                <td>AGENCIA</td>
                <td>Monto (S/.)</td>
                <td width="10px"></td>
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
      url:"{{url('backoffice/0/cvasignacioncapital/show_table')}}",
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
  lista_saldocapitalasignado();
  function lista_saldocapitalasignado(id){
    $.ajax({
      url:"{{url('backoffice/0/cvasignacioncapital/show_saldocapitalasignado')}}",
      type:'GET',
      data:{
          fechainicio: $('#fechainicio').val(),
          fechafin: $('#fechafin').val(),
      },
      success: function (res){
        $('#table-lista-saldocapitalasignado > tbody').html(res.html);
      }
    })
  }
  load_nuevo_asignacioncapital();
  function load_nuevo_asignacioncapital(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/cvasignacioncapital/create?view=registrar')}}", result:'#form-result-giro'});
  }
  
    function validar(idasignacioncapital){
        modal({ route:"{{ url('backoffice/'.$tienda->id) }}/cvasignacioncapital/"+idasignacioncapital+"/edit?view=validar",  size: 'modal-sm' });
    }
  
   function reporte_saldocapitalasignado(idagencia){
      let url = "{{ url('backoffice/'.$tienda->id) }}/cvasignacioncapital/0/edit?view=reporte_saldocapitalasignado&idagencia="+idagencia;
      modal({ route: url,size:'modal-fullscreen' })
   }
  
  function show_data(e) {
      let id = $(e).attr('data-valor-columna');

      $('tr.selected').removeClass('selected');
      $(e).addClass('selected');
      pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/cvasignacioncapital/"+id+"/edit?view=editar", result:'#form-result-giro'});

            
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
  
   function exportar_pdf(){
      let url = "{{ url('backoffice/'.$tienda->id) }}/cvasignacioncapital/0/edit?view=exportar&fechainicio="+$('#fechainicio').val()+
          "&fechafin="+$('#fechafin').val();
      modal({ route: url,size:'modal-fullscreen' })
   }
  
   function recepcionar(){
      let idasignacioncapital = $('#table-lista-asignacioncapital > tbody > tr.selected').attr('data-valor-columna');
                        
      if(idasignacioncapital == "" || idasignacioncapital == undefined ){
        mensaje = 'Debe de seleccionar una operación.';
        modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });
        return false;
      }
      let url = "{{ url('backoffice/'.$tienda->id) }}/cvasignacioncapital/"+idasignacioncapital+"/edit?view=recepcionar";
      modal({ route: url })
   }
   function voucher(){
      let idasignacioncapital = $('#table-lista-asignacioncapital > tbody > tr.selected').attr('data-valor-columna');
                        
      if(idasignacioncapital == "" || idasignacioncapital == undefined ){
        mensaje = 'Debe de seleccionar una operación.';
        modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });
        return false;
      }
      let url = "{{ url('backoffice/'.$tienda->id) }}/cvasignacioncapital/"+idasignacioncapital+"/edit?view=voucher";
      modal({ route: url, size: 'modal-sm' })
   }
</script>

