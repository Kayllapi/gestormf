<div class="modal-header">
  <h5 class="modal-title">Cartera de Crédito</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-credito-result">
             
            <div class="modal-body pb-0">
                <div class="row">
                    <div class="col-sm-12 col-md-7">
                        <div class="row">
                          <div class="col-sm-12 col-md-7">
                            <div class="row">
                                <label for="fecha_inicio" class="col-sm-3 col-form-label">AGENCIA</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="idagencia" disabled>
                                      <option></option>
                                          <option value="0" selected>TODA LAS AGENCIAS</option>
                                      @foreach($agencias as $value)
                                          <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                                      @endforeach
                                    </select>
                                </div>
                            </div>
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
                            <div class="row">
                                <label for="fecha_fin" class="col-sm-3 col-form-label">EJECUTIVO</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="idasesor" disabled>
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
                          <div class="col-sm-12 col-md-5" style="text-align: right;">
                              <div class="row">
                                <label for="fecha_inicio" class="col-sm-3 col-form-label">CORTE</label>
                                <div class="col-sm-9">
                                  <input type="date" class="form-control" id="fecha_inicio" value="{{ date('Y-m-d') }}">
                                </div>
                              </div>
                            <button type="button" class="btn btn-success" onclick="lista_credito()"><i class="fa-solid fa-search"></i> FILTRAR</button>
                          </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-5">
                      <table class="table table-bordered" style="margin-bottom: 3px;">
                        <thead class="table-dark"> 
                          <tr>
                            <th colspan="5" style='text-align:center;'>CATEGORIA DE CLASIFICACIÓN</th>
                          </tr>
                          <tr>
                            <th style='text-align:center;'>NORMAL</th>
                            <th style='text-align:center;'>CPP</th>
                            <th style='text-align:center;'>DEFICIENTE</th>
                            <th style='text-align:center;'>DUDOSO</th>
                            <th style='text-align:center;'>PÉRDIDA</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <th style='text-align:center;background-color: #19e35c !important;'><=8 DÍAS</th>
                            <th style='text-align:center;background-color: #E8E585 !important;'>>8<=30 DÍAS</th>
                            <th style='text-align:center;background-color: #FFC5C5 !important;'>>30<=60 DÍAS</th>
                            <th style='text-align:center;background-color: #959595 !important;'>>60<=120 DÍAS</th>
                            <th style='text-align:center;background-color: #959595 !important;'>>120 DÍAS</th>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                </div>
            </div> 
          </div>
        </div>
      </div>
  </div>
      <div class="col-sm-12 mt-1 mb-1">
        <div class="card">
          <div class="card-body p-2">
            <div class="modal-body p-0" style="overflow-y: scroll;height: calc(-305px + 100vh);">
            <table class="table table-striped table-hover" id="table-lista-credito">
              <thead class="table-dark" style="position: sticky;top: 0;"> 
                <tr>
                  <th>N°</th>
                  <th>CUENTA</th>
                  <th>RUC/DNI/CE</th>
                  <th>Apellidos y Nombres</th>
                  <th>RUC/DNI/CE (Aval)</th>
                  <th>Ape. Nom. Aval</th>
                  <th>Fecha Desemb.</th>
                  <th>Monto Cred. (S/.)</th>
                  <th>Saldo Cap. (S/.)</th>
                  <th>Saldo Deuda T. (S/.)</th>
                  <th>F. Pago</th>
                  <th>Cuotas</th>
                  <th>F.C.</th>
                  <th>Días de atraso</th>
                  <th>Calificación</th>
                  <th>Producto</th>
                  <th>Modalidad</th>
                  <th>Tele./Celu.</th>
                  <th>Direc/Domicilio</th>
                </tr>
              </thead>
              <tbody>
              
              </tbody>
            </table>
            </div>
          </div>
        </div>
      </div>
                              <div style="text-align: right;">
                                <button type="button" class="btn btn-info" onclick="exportar_pdf()">
                                  <i class="fa-solid fa-file-pdf" style="color:#000 !important;font-weight: bold;"></i> REPORTE PDF</button>
                                <button type="button" class="btn btn-success" onclick="exportar_excel()">
                                  <i class="fa-solid fa-file-excel" style="color:#000 !important;font-weight: bold;"></i> REPORTE EXCEL</button>
                              </div>
</div>
<script>
  /*var d= new Date();
  var fechatotal = `${d.getFullYear()}-${(d.getMonth() + 1)}-${d.getDate()}`;
  $("#fecha_fin").val(fechatotal);*/

  sistema_select2({ input:'#idagencia' });
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
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/propuestacredito/"+id+"/edit?view=opciones" });  
  }

   function exportar_pdf(){
      let url = "{{ url('backoffice/'.$tienda->id) }}/carteracredito/0/edit?view=exportar&fecha_inicio="+$('#fecha_inicio').val()+
          "&fecha_fin="+$('#fecha_fin').val()+
          "&idagencia="+$('#idagencia').val()+
          "&idformacredito="+$('#idformacredito').val()+
          "&idasesor="+$('#idasesor').val()+
          "&tipo=admin";
      modal({ route: url,size:'modal-fullscreen' })
   }
  
   function exportar_excel(){
        window.location.href = '{{url('backoffice/'.$tienda->id.'/carteracredito/0/edit')}}?view=exportar_excel&fecha_inicio='+$('#fecha_inicio').val()+
              '&fecha_fin='+$('#fecha_fin').val()+
              '&idagencia='+$('#idagencia').val()+
              '&idformacredito='+$('#idformacredito').val()+
              '&idasesor='+$('#idasesor').val()+
              '&tipo=admin';
    }
</script>  

