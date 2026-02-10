<div class="modal-header">
  <h5 class="modal-title">Lista de Garantias Prend. Entregadas</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <div class="row mb-1">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-credito-result">
             
            <div class="modal-body">
              
                <div class="row">
                    <div class="col-sm-12 col-md-8">
                        <div class="row">
                            <div class="col-sm-12 col-md-8">
                              <div class="row mb-1">
                                <label for="fecha_fin" class="col-sm-2 col-form-label">CLIENTE</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="idcliente">
                                      <option></option>
                                    </select>
                                </div>
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-4">
                              <button type="button" class="btn btn-success" onclick="lista_credito()"><i class="fa-solid fa-search"></i> FILTRAR</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-4">
                              <div class="row">
                                <label for="fecha_inicio" class="col-sm-3 col-form-label">DEL</label>
                                <div class="col-sm-9">
                                  <input type="date" class="form-control" id="fecha_inicio" value="{{ date('Y-m-d') }}">
                                </div>
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                              <div class="row">
                                <label for="fecha_fin" class="col-sm-3 col-form-label">AL</label>
                                <div class="col-sm-9">
                                  <input type="date" class="form-control" id="fecha_fin" value="{{ date('Y-m-d') }}">
                                </div>
                              </div>
                            </div>
                        </div>
                    </div>
                      <div class="col-sm-12 col-md-4" style="text-align: right;">
                          <div>
                          <button type="button" class="btn btn-warning mb-1" onclick="ticketgarantia()">
                            <i class="fa-solid fa-check"></i> V. ENTREGA DE GARANTÍA</button>
                          </div>
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
            <div id="tabla-pagoprestamo" class="tabla_overflow" style="height: calc(-247px  + 100vh)">
            </div>
          </div>
        </div>
      </div>
</div>
<script>
  sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idcliente' });
  
  lista_credito();
  function lista_credito(){
    
    $.ajax({
      url:"{{url('backoffice/'.$tienda->id.'/garantiaentregada/showtable')}}",
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
 
   function ticketgarantia(){
      let idcredito_garantia = $('#table-lista-credito > tbody > tr.selected').attr('idcredito_garantia');
                        
      if(idcredito_garantia == "" || idcredito_garantia == undefined ){
        var mensaje = "Debe de seleccionar un registro.";
        modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });
        return false;
      }
      let url = "{{ url('backoffice/'.$tienda->id) }}/garantiarecoger/"+idcredito_garantia+"/edit?view=ticket_garantia";
      modal({ route: url, size: 'modal-sm' })
   }
</script>  

