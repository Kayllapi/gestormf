<div class="modal-header">
  <h5 class="modal-title">Historial de Pagos de  Préstamos Asesor/Cobranza</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-credito-result">
             
            <div class="modal-body">
              
                <div class="row">
                    <div class="col-sm-12 col-md-9">
                        <div class="row">
                           <div class="col-sm-12 col-md-6">
                              <div class="row">
                                <label for="fecha_inicio" class="col-sm-3 col-form-label">AGENCIA</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="idagencia" disabled>
                                      <option></option>
                                      @foreach($agencias as $value)
                                          <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                                      @endforeach
                                    </select>
                                </div>
                              </div>
                            </div>
                          <div class="col-sm-12 col-md-6" style="text-align: right;">
                              <button type="button" class="btn btn-success" onclick="lista_credito()"><i class="fa-solid fa-search"></i> FILTRAR</button>
                          </div>
                        </div>
                        <div class="row">
                           <div class="col-sm-12 col-md-6">
                              <div class="row">
                                <label for="fecha_fin" class="col-sm-3 col-form-label">CLIENTE</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="idcliente">
                                      <option></option>
                                    </select>
                                </div>
                              </div>
                           </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                      <div class="row">
                                        <label for="fecha_inicio" class="col-sm-3 col-form-label">DEL</label>
                                        <div class="col-sm-9">
                                          <input type="date" class="form-control" id="fecha_inicio" value="{{ date('Y-m-d') }}">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                      <div class="row">
                                        <label for="fecha_fin" class="col-sm-3 col-form-label">AL</label>
                                        <div class="col-sm-9">
                                          <input type="date" class="form-control" id="fecha_fin" value="{{ date('Y-m-d') }}">
                                        </div>
                                      </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                                
                    </div>
                      <div class="col-sm-12 col-md-3" style="text-align: right;">
                          <div>
                          <button type="button" class="btn btn-secondary mb-1" onclick="ticketpago()" style="font-weight: bold;width: 190px;">
                            <i class="fa-solid fa-check" style="font-weight: bold;"></i> VOUCHER DE PAGO</button>
                          <button type="button" class="btn btn-warning mb-1" onclick="ticketgarantia()" style="font-weight: bold;width: 190px;">
                            <i class="fa-solid fa-check" style="font-weight: bold;"></i> V. ENTREGA DE GARANTÍA</button>
                          </div>
                          <div>
                      </div>
                </div>
              
            </div> 
          </div>
        </div>
      </div>
  </div>
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body" id="tabla-pagoprestamo" style="
            overflow-y: scroll;
            height: calc(100vh - 266px);
            padding-top: 0px;
            padding-bottom: 0px;"> 
          </div>
        </div>
      </div>
      <div style="text-align: right;">
        <button type="button" class="btn btn-info" onclick="exportar_pdf()" style="font-weight: bold;">
          <i class="fa-solid fa-file-pdf" style="color:#000 !important;font-weight: bold;"></i> REPORTE</button>
      </div>
</div>
<script>

  sistema_select2({ input:'#idagencia',val:'{{$tienda->id}}' });
  sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idcliente' });
  
  lista_credito();
  function lista_credito(){
    
    $.ajax({
      url:"{{url('backoffice/0/pagoprestamo/showtable')}}",
      type:'GET',
      data: {
          //estado : estado_credito,
          idagencia : $('#idagencia').val(),
          idcliente : $('#idcliente').val(),
          inicio : $('#fecha_inicio').val(),
          fin : $('#fecha_fin').val(),
      },
      success: function (res){
        $('#tabla-pagoprestamo').html(res.html);
        $("tr#show_data_select").on("click", function() {
            $('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        });
      }
    })
  }
 
    function validar(idcredito_cobranzacuota){
        modal({ route:"{{ url('backoffice/'.$tienda->id) }}/pagoprestamo/"+idcredito_cobranzacuota+"/edit?view=validar",  size: 'modal-sm' });
    }
  
   function exportar_pdf(){
      let url = "{{ url('backoffice/'.$tienda->id) }}/pagoprestamo/0/edit?view=exportar&fecha_inicio="+$('#fecha_inicio').val()+
          "&fecha_fin="+$('#fecha_fin').val()+
          "&idagencia="+$('#idagencia').val()+
          "&idcliente="+$('#idcliente').val();
      modal({ route: url,size:'modal-fullscreen' })
   }
  
 
   function vistapreliminar(){
      let idcredito = $('#tabla-pagoprestamo > tbody > tr.selected').attr('idcredito');
                        
      if(idcredito == "" || idcredito == undefined ){
        alert('Debe de seleccionar un crédito.');   
        return false;
      }
      let url = "{{ url('backoffice/'.$tienda->id) }}/pagoprestamo/"+idcredito+"/edit?view=eliminar";
      modal({ route: url })
   }
 
   function ticketpago(){
      let idcredito_cobranzacuota = $('#table-lista-credito > tbody > tr.selected').attr('idcredito_cobranzacuota');
                        
      if(idcredito_cobranzacuota == "" || idcredito_cobranzacuota == undefined ){
        alert('Debe de seleccionar un crédito.');   
        return false;
      }
      let url = "{{ url('backoffice/'.$tienda->id) }}/pagoprestamo/"+idcredito_cobranzacuota+"/edit?view=ticket";
      modal({ route: url,  size: 'modal-sm' })
   }
   function ticketgarantia(){
      let idcredito_cobranzacuota = $('#table-lista-credito > tbody > tr.selected').attr('idcredito_cobranzacuota');
                        
      if(idcredito_cobranzacuota == "" || idcredito_cobranzacuota == undefined ){
        alert('Debe de seleccionar un crédito.');   
        return false;
      }
      let url = "{{ url('backoffice/'.$tienda->id) }}/pagoprestamo/"+idcredito_cobranzacuota+"/edit?view=ticket_garantia";
      modal({ route: url })
   }
</script>  

