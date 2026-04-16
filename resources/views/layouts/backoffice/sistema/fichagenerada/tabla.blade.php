<div class="modal-header">
  <h5 class="modal-title">Historial de Ficha de Liquidaciones</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-credito-result">
            <div class="modal-body pb-0">
                <div class="row">
                    <div class="col-md-4">
                      <div class="row">
                            <label class="col-md-3 col-form-label text-end">AGENCIA</label>
                            <div class="col-md-9">
                                <select class="form-control" id="idagencia">
                                  <option></option>
                                  @foreach($agencias as $value)
                                      <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                                  @endforeach
                                </select>
                            </div>
                      </div>
                    </div>
                    <div class="col-md-8">
                      <div class="row">
                            <label for="fecha_inicio" class="col-sm-2 col-form-label text-end">PERIODO</label>
                            <div class="col-sm-3">
                              <input type="date" class="form-control" id="fecha_inicio" value="{{ date('Y-m-d') }}">
                            </div>
                            <label for="fecha_fin" class="col-sm-1 col-form-label text-end">AL</label>
                            <div class="col-sm-3">
                              <input type="date" class="form-control" id="fecha_fin" value="{{ date('Y-m-d') }}">
                            </div>
                          <div class="col-sm-12 col-md-1" style="text-align: right;">
                              <button type="button" class="btn btn-success" onclick="lista_credito()"><i class="fa-solid fa-search"></i> FILTRAR</button>
                          </div>
                          <div class="col-sm-12 col-md-2" style="text-align: right;">
                              <button type="button" class="btn btn-danger" onclick="eliminar()"><i class="fa-solid fa-trash"></i> ELIMINAR</button>
                          </div>
                      </div>
                    </div>
                </div>
              
            </div> 
          </div>
        </div>
      </div>
  </div>
      <div class="col-sm-12 mt-1">
        <div class="card">
          <div class="card-body">
          <div style="
            overflow-y: scroll;
            height: calc(100vh - 238px);
            padding: 0;">
            <table class="table table-striped table-hover" id="table-lista-credito">
              <thead class="table-dark" style="position: sticky;top: 0;"> 
                <tr>
                  <th>CóDIGO DE GARANTÍA</th>
                  <th>CLIENTE</th>
                  <th>RUC/DNI/CE</th>
                  <th>TIPO DE GARANTÍA</th>
                  <th>DESCRIPCIÓN</th>
                  <th>Serie/Motor/N°Partida</th>
                  <th>MODELO</th>
                  <th>VALOR COMERCIAL</th>
                  <th>V.C. DESCT.</th>
                  <th>COBERTURA</th>
                  <th>P. LIQUID.</th>
                  <th>ACCESORIOS</th>
                  <th>COLOR</th>
                  <th>AÑO DE FABRICACIÓN</th>
                  <th>PLACA DEL VEHÍCULO</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          </div>
        </div>
      </div>
      <div class="text-end mt-1">
        <button type="button" class="btn btn-info" onclick="exportar_pdf()">
          <i class="fa-solid fa-file-pdf"></i> REPORTE</button>
      </div>
</div>
<script>
  /*var d= new Date();
  var fechatotal = `${d.getFullYear()}-${(d.getMonth() + 1)}-${d.getDate()}`;
  $("#fecha_fin").val(fechatotal);*/

  sistema_select2({ input:'#idagencia',val:'{{$tienda->id}}' });
  
  lista_credito();
  function lista_credito(){
    //let estado_credito = $('input[name="estado_credito"]:checked').val();
    
    $.ajax({
      url:"{{url('backoffice/'.$tienda->id.'/fichagenerada/showtable')}}",
      type:'GET',
      data: {
          //estado : estado_credito,
          idagencia : $('#idagencia').val(),
          fecha_inicio : $('#fecha_inicio').val(),
          fecha_fin : $('#fecha_fin').val(),
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
 
   function exportar_pdf(){
      let url = "{{ url('backoffice/'.$tienda->id) }}/fichagenerada/0/edit?view=exportar&fecha_inicio="+$('#fecha_inicio').val()+
          "&fecha_fin="+$('#fecha_fin').val()+
          "&idagencia="+$('#idagencia').val();
      modal({ route: url,size:'modal-fullscreen' })
   }

    function eliminar(){
      let idcredito_garantia = $('#table-lista-credito > tbody > tr.selected').attr('idcredito');
                        
      if(idcredito_garantia == "" || idcredito_garantia == undefined ){
        mensaje = 'Debe de seleccionar una ficha.';
        modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' }); 
        return false;
      }
        modal({ route:"{{ url('backoffice/'.$tienda->id) }}/fichagenerada/"+idcredito_garantia+"/edit?view=eliminar",  size: 'modal-sm' });
    }
</script>  

