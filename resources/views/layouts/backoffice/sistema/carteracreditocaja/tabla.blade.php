<div class="modal-header">
  <h5 class="modal-title">Cartera de Crédito Administrador</h5>
  
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
                                          <option value="0" selected>TODO</option>
                                      @foreach($agencias as $value)
                                          <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                                      @endforeach
                                    </select>
                                </div>
                              </div>
                            </div>
                        </div>
                        <div class="row">
                           <div class="col-sm-12 col-md-6">
                              <div class="row">
                                <label for="fecha_fin" class="col-sm-3 col-form-label">F. CRÉDITO</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="idformacredito">
                                      <option></option>
                                      <option value="0" selected>TODO</option>
                                      <option value="CP">CP</option>
                                      <option value="CNP">CNP</option>
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
                                <label for="fecha_fin" class="col-sm-3 col-form-label">EJECUTIVO</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="idasesor">
                                      <option></option>
                                      <option value="0" selected>TODO</option>
                                      <?php
                                      $usuarios = DB::table('users')
                                          ->join('users_permiso','users_permiso.idusers','users.id')
                                          ->join('permiso','permiso.id','users_permiso.idpermiso')
                                          ->whereIn('users_permiso.idpermiso',[3,4,7])
                                          ->where('users_permiso.idtienda',$tienda->id)
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
                            <div class="col-sm-12 col-md-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                      <div class="row">
                                        <label for="fecha_inicio" class="col-sm-3 col-form-label">CORTE</label>
                                        <div class="col-sm-9">
                                          <input type="date" class="form-control" id="fecha_inicio" value="{{ date('Y-m-d') }}">
                                        </div>
                                      </div>
                                    </div>
                                </div>
                            </div>
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

            <table class="table table-bordered" style="width:600px;margin-bottom: 3px;">
              <thead class="table-dark"> 
                <tr>
                  <td colspan="5" style='text-align:center;'>CATEGORIA DE CLASIFICACIÓN</td>
                </tr>
                <tr>
                  <td style='text-align:center;'>NORMAL</td>
                  <td style='text-align:center;'>CPP</td>
                  <td style='text-align:center;'>DEFICIENTE</td>
                  <td style='text-align:center;'>DUDOSO</td>
                  <td style='text-align:center;'>PÉRDIDA</td>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td style='text-align:center;background-color: #78d7ab !important;'><=8 DÍAS</td>
                  <td style='text-align:center;background-color: #78d7ab !important;'>>8<=30 DÍAS</td>
                  <td style='text-align:center;background-color: #78d7ab !important;'>>30<=60 DÍAS</td>
                  <td style='text-align:center;background-color: #78d7ab !important;'>>60<=120 DÍAS</td>
                  <td style='text-align:center;background-color: #78d7ab !important;'>>120 DÍAS</td>
                </tr>
              </tbody>
            </table>
          <div class="card-body" style="overflow-y: scroll;height: 260px;padding: 0;margin-top: 5px;">

            <table class="table table-striped table-hover" id="table-lista-credito">
              <thead class="table-dark" style="position: sticky;top: 0;"> 
                <tr>
                  <td>N°</td>
                  <td>CUENTA</td>
                  <td>DOI/RUC</td>
                  <td>Apellidos y Nombres</td>
                  <td>DOI/RUC (Aval)</td>
                  <td>Ape. Nom. Aval</td>
                  <td>Fecha Desemb.</td>
                  <td>Monto Cred. (S/.)</td>
                  <td>Saldo Cap. (S/.)</td>
                  <td>Saldo Deuda T. (S/.)</td>
                  <td>F. Pago</td>
                  <td>Cuotas</td>
                  <td>Form. C.</td>
                  <td>Días de atraso</td>
                  <td>Calificación</td>
                  <td>Producto</td>
                  <td>Modalidad</td>
                  <td>Tele./Celu.</td>
                  <td>Direc/Domicilio</td>
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
  sistema_select2({ input:'#idformacredito' });
  sistema_select2({ input:'#idasesor' });
  
  lista_credito();
  function lista_credito(){
    //let estado_credito = $('input[name="estado_credito"]:checked').val();
    
    $.ajax({
      url:"{{url('backoffice/0/carteracredito/showtable')}}",
      type:'GET',
      data: {
          //estado : estado_credito,
          idagencia : $('#idagencia').val(),
          idformacredito : $('#idformacredito').val(),
          idasesor : $('#idasesor').val(),
          inicio : $('#fecha_inicio').val(),
          tipo : 'admin',
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
 
   /*function vistapreliminar(){
      let idcredito = $('#table-lista-credito > tbody > tr.selected').attr('idcredito');
                        
      if(idcredito == "" || idcredito == undefined ){
        alert('Debe de seleccionar un crédito.');   
        return false;
      }
      let url = "{{ url('backoffice/'.$tienda->id) }}/carteracredito/"+idcredito+"/edit?view=desembolsar";
      modal({ route: url, size: 'modal-fullscreen' })
   }*/

 
   function exportar_pdf(){
      let url = "{{ url('backoffice/'.$tienda->id) }}/carteracredito/0/edit?view=exportar&fecha_inicio="+$('#fecha_inicio').val()+
          "&fecha_fin="+$('#fecha_fin').val()+
          "&idagencia="+$('#idagencia').val()+
          "&idformacredito="+$('#idformacredito').val()+
          "&idasesor="+$('#idasesor').val()+
          "&tipo=admin";
      modal({ route: url,size:'modal-fullscreen' })
   }
</script>  

