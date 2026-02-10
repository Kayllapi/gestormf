<div class="modal-header">
  <h5 class="modal-title">Aprobación de Créditos Administrador</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-credito-result">
             
            <div class="modal-body">
              
                <div class="row">
                  <div class="col-sm-12">
                    <button type="button" class="btn btn-warning" onclick="cambiar_estado('PENDIENTE')"> PASAR A GENERAR CRÉDITO</button>
                    <button type="button" class="btn btn-success" onclick="cambiar_estado('APROBADO')"> APROBAR CRÉDITO</button>
                    <button type="button" class="btn btn-danger" onclick="cambiar_estado('ELIMINAR')"> ELIMINAR CRÉDITO</button>
                    <button type="button" class="btn btn-info" onclick="acta_aprobacion()" style="float: right;"> 
                    <b>ACTA DE APROBACIÓN</b><br>
                    <div style="float: right;margin-right:5px;font-size:13px;">
                      (Excepciones, Op. Riesgos, Verificaciones)</div>
                    </button>
                    
                    <script>
                      function cambiar_estado(tipo){
                        let estado = $('#table-lista-credito > tbody > tr.selected').attr('estado');
                        let idcredito = $('#table-lista-credito > tbody > tr.selected').attr('idcredito');
                        
                        if(idcredito == "" || idcredito == undefined ){
                          var mensaje = "Debe de seleccionar un crédito.";
                          modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });  
                          return false;
                        }
                        
                        if(estado == "DESAPROBADO"){  
                          var mensaje = "o puede eliminar un Crédito Desaprobado.";
                          modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });  
                          return false;
                        }
                        if(estado == "CANCELADO"){
                          var mensaje = "No puede eliminar un Crédito Cancelado.";
                          modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });    
                          return false;
                        }
                        
                        let url = "{{ url('backoffice/'.$tienda->id) }}/propuestacredito/"+idcredito+"/edit?view=cambiar_estado&tipo="+tipo+'&permiso=institucional';
                        modal({ route: url, size: 'modal-fullscreen' })
                      }
                      function acta_aprobacion(){
                        let idcredito = $('#table-lista-credito > tbody > tr.selected').attr('idcredito');
                        
                        if(idcredito == "" || idcredito == undefined ){
                          var mensaje = "Debe de seleccionar un crédito.";
                          modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });  
                          return false;
                        }
                        let url = "{{ url('backoffice/'.$tienda->id) }}/propuestacredito/"+idcredito+"/edit?view=acta_aprobacion";
                        modal({ route: url, size: 'modal-fullscreen' })
                      }
                    </script>
                  </div>
                  <div class="col-sm-12 mt-3">
                    <div class="row">
                      <div class="col-sm-12 col-md-7">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="estado_credito" id="estado_enproceso" value="PROCESO" onclick="lista_credito();" checked>
                          <label class="form-check-label" for="estado_enproceso">EN PROCESO</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="estado_credito" id="estado_aprobado" value="APROBADO" onclick="lista_credito();">
                          <label class="form-check-label" for="estado_aprobado">APROBADOS</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="estado_credito" id="estado_desaprobado" value="DESAPROBADO" onclick="lista_credito();">
                          <label class="form-check-label" for="estado_desaprobado" style="color: #dc3545;">DESAPROBADOS</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="estado_credito" id="estado_desembolso" value="DESEMBOLSADO" onclick="lista_credito();">
                          <label class="form-check-label" for="estado_desembolso">DESEMBOLSADOS</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="estado_credito" id="estado_cancelado" value="CANCELADO" onclick="lista_credito();">
                          <label class="form-check-label" for="estado_cancelado">CANCELADOS</label>
                        </div>
                      </div>
                      <div class="col-sm-12 col-md-2">
                        <div class="row mb-3">
                          <label for="fecha_inicio" class="col-sm-2 col-form-label">DE</label>
                          <div class="col-sm-10">
                            <input type="date" class="form-control" id="fecha_inicio" value="{{ date('Y-m-d') }}">
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-12 col-md-2">
                        <div class="row mb-3">
                          <label for="fecha_fin" class="col-sm-2 col-form-label">AL</label>
                          <div class="col-sm-10">
                            <input type="date" class="form-control" id="fecha_fin" value="{{ date('Y-m-d') }}">
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-12 col-md-1">
                          <button type="button" class="btn btn-primary" onclick="lista_credito();"> BUSCAR</button>
                      </div>
                    </div>
                  </div>
                </div>
              
            </div> 
          </div>
        </div>
      </div>
      <div class="col-sm-12">
            <h5 class="modal-title" style="margin-top: 10px;text-align: center;">LISTA DE CRÉDITOS</h5>
        <div class="card">
          <div class="card-body">

            <table class="table table-striped table-hover" id="table-lista-credito">
              <thead class="table-dark">
                <tr>
                  <th>N°</th>
                  <th>CLIENTE</th>
                  <th>AVAL</th>
                  <th>PRODUCTO</th>
                  <th>PRÉSTAMO</th>
                  <th>ESTADO</th>
                  <th>ASESOR/EJECUTIVO</th>
                  <th>FECHA</th>
                  <th>MODALI. CRÉDITO</th>
                  <th>PROPUESTA</th>
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
<style>
  
</style>
<script>
  lista_credito();
  function lista_credito(){
    let estado_credito = $('input[name="estado_credito"]:checked').val();
    
    $.ajax({
      url:"{{url('backoffice/0/propuestacredito/showtable')}}",
      type:'GET',
      data: {
          estado : estado_credito,
          idagencia : '{{$tienda->id}}',
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
 

</script>  

