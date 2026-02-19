<div class="modal-header">
  <h5 class="modal-title">Consolidado de Cartera de Crédito Administrador</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-credito-result">
             
            <div class="modal-body pb-0">
              
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
                                <label class="col-sm-3 col-form-label">F. CRÉDITO</label>
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
                                    <div class="col-sm-12 col-md-6">
                                      
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
  <div class="row mt-1"> 
      <div class="col-sm-7">
        <div class="card">
          <div class="card-body">
            <table class="table table-bordered"  style="margin-bottom: 10px;">
              <tbody>
                <tr>
                  <th style='text-align:center;background-color: #E8E585 !important;font-weight: bold;'>(Días de Mora > {{configuracion($tienda->id,'dias_tolerancia_garantia')['valor']}} días)</th>
                </tr>
              </tbody>
            </table>
            <div style="height: calc(-314px + 100vh);">
            <table class="table table-striped" id="table-lista-credito">
              <thead class="table-dark" style="position: sticky;top: 0;"> 
                <tr>
                  <th>Asesor/ejecutivo</th>
                  <th style='text-align:right;'>Cartera (S/.)</th>
                  <th style='text-align:right;'>N° de Créditos</th>
                  <th style='text-align:right;'>En Mora (S/.)</th>
                  <th style='text-align:right;'>% de Mora</th>
                  <th style='text-align:right;'>N° de Cred. En Mora</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="card">
          <div class="card-body">
            <table class="table table-striped" id="table-lista-credito1">
              <thead class="table-dark"> 
                <tr>
                  <th>CLASIFICACIÓN</th>
                  <th style='text-align:center;'>SALDO</th>
                  <th style='text-align:center;'>N° DE CRÉDITOS</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-sm-2">
        <div class="card">
          <div class="card-body">

            <table class="table table-striped" id="table-lista-credito2">
              <thead class="table-dark"> 
                <tr>
                  <th colspan="2" style='text-align:center; background-color: #a7a7a7 !important;'>INDICE DE MORA REGULAR</th>
                </tr>
                <tr>
                  <th style='text-align:center;'>% de Mora</th>
                  <th style='text-align:center;'>Clasificación Consid.</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
                              <div style="text-align: right;margin-top:-30px">
                                <button type="button" class="btn btn-info" onclick="exportar_pdf()" style="font-weight: bold;">
                                  <i class="fa-solid fa-file-pdf" style="color:#000 !important;font-weight: bold;"></i> REPORTE PDF</button>
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
    $.ajax({
      url:"{{url('backoffice/0/consolidadocarteracredito/showtable')}}",
      type:'GET',
      data: {
          idagencia : $('#idagencia').val(),
          idformacredito : $('#idformacredito').val(),
          idasesor : $('#idasesor').val(),
          inicio : $('#fecha_inicio').val(),
          tipo : 'admin',
      },
      success: function (res){
        $('#table-lista-credito > tbody').html(res.html);
        $('#table-lista-credito1 > tbody').html(res.html1);
        $('#table-lista-credito2 > tbody').html(res.html2);
        $("tr#show_data_select").on("click", function() {
            $('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        });
      }
    })
  }
 
   function exportar_pdf(){
      let url = "{{ url('backoffice/'.$tienda->id) }}/consolidadocarteracredito/0/edit?view=exportar&fecha_inicio="+$('#fecha_inicio').val()+
          "&idagencia="+$('#idagencia').val()+
          "&idformacredito="+$('#idformacredito').val()+
          "&idasesor="+$('#idasesor').val()+
          "&tipo=admin";
      modal({ route: url,size:'modal-fullscreen' })
   }
</script>  

