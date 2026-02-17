<div class="modal-header">
  <h5 class="modal-title">Creditos Desembolsados</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()" style="font-size: 20px;"></button>
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
                                <label for="fecha_fin" class="col-sm-3 col-form-label">EJECUTIVO</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="idasesor">
                                      <option></option>
                                      <?php
                                      $usuarios = DB::table('users')
                                          ->join('users_permiso','users_permiso.idusers','users.id')
                                          ->join('permiso','permiso.id','users_permiso.idpermiso')
                                          ->whereIn('users_permiso.idpermiso',[3,4,7])
                                          ->select('users.*','permiso.nombre as nombrepermiso')
                                          ->get();
                                      ?>
                                      @foreach($usuarios as $value)
                                      <option value="{{$value->id}}">{{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                                      @endforeach
                                    </select>
                                </div>
                              </div>
                            </div>
                        </div>
                                
                    </div>
                      <div class="col-sm-12 col-md-3" style="text-align: right;">
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
          <div class="card-body" style="
            overflow-y: scroll;
            height: calc(100vh - 266px);
            padding: 0;
            margin-top: 5px;">
            <table class="table table-striped table-hover" id="table-lista-credito">
              <thead class="table-dark" style="position: sticky;top: 0;">
                <tr>
                  <td>N°</td>
                  <td>CLIENTE</td>
                  <td>AVAL</td>
                  <td>DESEMBOLSO</td>
                  <td>CUOTAS</td>
                  <td>F. PAGO</td>
                  <td>F. DESEMBOLSO</td>
                  <td>CAJERO</td>
                  <td>OPERACIÓN EN</td>
                  <td>VALIDACIÓN</td>
                  <td>MODA. CRÉDITO</td>
                  <td>ASESOR</td>
                  <td>OPCIÓN</td>
                </tr>
              </thead>
              <tbody>
              
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div style="text-align: right;">
        <button type="button" class="btn btn-info" onclick="exportar_pdf()" style="font-weight: bold;">
          <i class="fa-solid fa-file-pdf" style="color:#000 !important;font-weight: bold;"></i> REPORTE</button>
      </div>
</div>
<script>
  /*var d= new Date();
  var fechatotal = `${d.getFullYear()}-${(d.getMonth() + 1)}-${d.getDate()}`;
  $("#fecha_fin").val(fechatotal);*/

  sistema_select2({ input:'#idagencia',val:'{{$tienda->id}}' });
  sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idcliente' });
  sistema_select2({ input:'#idasesor' });
  
  lista_credito();
  function lista_credito(){
    //let estado_credito = $('input[name="estado_credito"]:checked').val();
    
    $.ajax({
      url:"{{url('backoffice/0/desembolsado/showtable')}}",
      type:'GET',
      data: {
          //estado : estado_credito,
          idagencia : $('#idagencia').val(),
          idcliente : $('#idcliente').val(),
          idasesor : $('#idasesor').val(),
          inicio : $('#fecha_inicio').val(),
          fin : $('#fecha_fin').val(),
          tipo : 'asesor',
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
 
   function vistapreliminar(){
      let idcredito = $('#table-lista-credito > tbody > tr.selected').attr('idcredito');
                        
      if(idcredito == "" || idcredito == undefined ){
        alert('Debe de seleccionar un crédito.');   
        return false;
      }
      let url = "{{ url('backoffice/'.$tienda->id) }}/desembolsado/"+idcredito+"/edit?view=desembolsar";
      modal({ route: url, size: 'modal-fullscreen' })
   }

    function validar(idcredito){
        modal({ route:"{{ url('backoffice/'.$tienda->id) }}/desembolsado/"+idcredito+"/edit?view=validar",  size: 'modal-sm' });
    }

   function exportar_pdf(){
     var fecha_inicio = $('#fecha_inicio').val()!=undefined?$('#fecha_inicio').val():'';
     var fecha_fin = $('#fecha_fin').val()!=undefined?$('#fecha_fin').val():'';
      let url = "{{ url('backoffice/'.$tienda->id) }}/desembolsado/0/edit?view=exportar&fecha_inicio="+fecha_inicio+
          "&fecha_fin="+fecha_fin+
          "&idagencia="+$('#idagencia').val()+
          "&idcliente="+$('#idcliente').val()+
          "&idasesor="+$('#idasesor').val()+
          "&tipo=asesor";
      modal({ route: url,size:'modal-fullscreen' })
   }
</script>  

