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
                    <div class="col-sm-12 col-md-6">
                        <div class="row">
                          <div class="col-sm-12 col-md-10">
                              <div class="row">
                                <label for="fecha_inicio" class="col-sm-3 col-form-label">AGENCIA</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" value="{{$tienda->nombreagencia}}" disabled>
                                    {{-- <select class="form-control" id="idagencia" disabled>
                                      <option></option>
                                      @foreach($agencias as $value)
                                          <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                                      @endforeach
                                    </select> --}}
                                    <input type="hidden" id="idagencia" value="{{$tienda->id}}">
                                </div>
                              </div>
                            </div>
                        </div> 
                    </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <button type="button" class="btn btn-warning" onclick="cambiar_estado('PENDIENTE')" id="btn_pasargenerarcredito"> PASAR A GENERAR CRÉDITO</button>
                    <button type="button" class="btn btn-success" onclick="cambiar_estado('APROBADO')"> APROBAR CRÉDITO</button>
                    <!-- <button type="button" class="btn btn-danger" onclick="cambiar_estado('ELIMINAR')"> ELIMINAR CRÉDITO</button> -->
                    <button type="button" class="btn btn-info" onclick="acta_aprobacion()" style="float: right;"> 
                    <b>ACTA DE APROBACIÓN</b><br>
                    <div style="float: right;margin-right:5px;font-size:13px;">
                      <span class="badge bg-success">Excepciones</span> <span class="badge bg-warning" style="border: 1px solid #212529 !important;">Op. Riesgos</span> <span class="badge bg-primary">Verificaciones</span></div>
                    </button>
                    
                    <script>
                      function cambiar_estado(tipo){
                        let estado = $('#table-lista-credito > tbody > tr.selected').attr('estado');
                        let idcredito = $('#table-lista-credito > tbody > tr.selected').attr('idcredito');
                        let desaprobado_definitivo = $('#table-lista-credito > tbody > tr.selected').attr('desaprobado_definitivo');

                        if(idcredito == "" || idcredito == undefined ){
                          var mensaje = "Debe de seleccionar un crédito.";
                          modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });
                          return false;
                        }

                        if(estado == "DESAPROBADO" && desaprobado_definitivo == "1" && (tipo == "ELIMINAR" || tipo == "APROBADO" || tipo == "PENDIENTE")){
                          var mensaje = "Solicitud de crédito desaprobado definitivo, no puede realizar ninguna acción.";
                          modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });
                          return false;
                        }
                        if(estado == "CANCELADO"){
                          var mensaje = "Crédito Cancelado, no puede realizar ninguna acción.";
                          modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });    
                          return false;
                        }
                        if(tipo == "ELIMINAR" && (estado == "DESEMBOLSADO" || estado == "APROBADO") && idcredito_refinanciado != undefined && idcredito_refinanciado != "0"){
                          var mensaje = "Para eliminar este crédito Refinanciado hacerlo desde el Módulo Historial de Pagos";
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
                  <div class="col-sm-12 mt-2">
                    <div class="row">
                      <div class="col-sm-12 col-md-8">
                        <div class="form-check form-check-inline">
                          <label class="radio-custom">
                            <input type="radio" name="estado_credito" id="estado_enproceso" value="PROCESO" onclick="lista_credito();" checked>
                            <span class="radio"></span> EN PROCESO
                          </label>
                        </div>
                        <div class="form-check form-check-inline">
                          <label class="radio-custom">
                            <input type="radio" name="estado_credito" id="estado_aprobado" value="APROBADO" onclick="lista_credito();">
                            <span class="radio"></span> APROBADOS
                          </label>
                        </div>
                        <div class="form-check form-check-inline">
                          <label class="radio-custom" style="color: #dc3545;">
                            <input type="radio" name="estado_credito" id="estado_desaprobado" value="DESAPROBADO" onclick="lista_credito();">
                            <span class="radio"></span> DESAPROBADOS
                          </label>
                        </div>
                        <div class="form-check form-check-inline">
                          <label class="radio-custom">
                            <input type="radio" name="estado_credito" id="estado_desembolso" value="DESEMBOLSADO" onclick="lista_credito();">
                            <span class="radio"></span> DESEMBOLSADOS
                          </label>
                        </div>
                        <div class="form-check form-check-inline">
                          <label class="radio-custom">
                            <input type="radio" name="estado_credito" id="estado_cancelado" value="CANCELADO" onclick="lista_credito();">
                            <span class="radio"></span> CANCELADOS
                          </label>
                        </div>
                      </div>
                      <div class="col-sm-12 col-md-auto ms-md-auto">
                        <div class="row align-items-center">
                          <label for="fecha_inicio" class="col-auto col-form-label">DE</label>
                          <div class="col-auto">
                            <input type="date" class="form-control" id="fecha_inicio" style="width: 100px;" value="{{ date('Y-m-d') }}">
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-12 col-md-auto">
                        <div class="row align-items-center">
                          <label for="fecha_fin" class="col-auto col-form-label">AL</label>
                          <div class="col-auto">
                            <input type="date" class="form-control" id="fecha_fin" style="width: 100px;" value="{{ date('Y-m-d') }}">
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-12 col-md-1">
                          <button type="button" style="width: 100%;" class="btn btn-primary" onclick="lista_credito();"> BUSCAR</button>
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
          <div class="card-body" style="height: calc(-283px + 100vh);">

            <table class="table table-striped table-hover" id="table-lista-credito">
              <thead class="table-dark">
                <tr>
                  <th>N°</th>
                  <th>CLIENTE</th>
                  <th>AVAL</th>
                  <th>PRODUCTO</th>
                  <th>PRÉSTAMO</th>
                  <th>F.C.</th>
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
  // sistema_select2({ input:'#idagencia',val:'{{$tienda->id}}' });
  lista_credito();
  function lista_credito(){
    let estado_credito = $('input[name="estado_credito"]:checked').val();
    
    $.ajax({
      url:"{{url('backoffice/0/propuestacredito/showtable')}}",
      type:'GET',
      data: {
          estado : estado_credito,
          idagencia : $('#idagencia').val(),
          inicio : $('#fecha_inicio').val(),
          fin : $('#fecha_fin').val(),
      },
      success: function (res){
        $('#table-lista-credito > tbody').html(res.html);
        $("tr#show_data_select").on("click", function() {
            $('tr.selected').removeClass('selected');
            $(this).addClass('selected');
            let idcredito_refinanciado = $(this).attr('idcredito_refinanciado');
            if (idcredito_refinanciado!=0) {
              $('#btn_pasargenerarcredito').hide();
            } else {
              $('#btn_pasargenerarcredito').show();
            }
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

