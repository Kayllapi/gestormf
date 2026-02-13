<div class="modal-header">
  <h5 class="modal-title">Agregar / Quitar de: Lista de Remates - Administrador</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2">
             <div id="cont-filtro"></div>
            <div class="modal-body">
              
                <div class="row">
                    <div class="col-sm-12 col-md-9">
                        <div class="row mb-1">
                           <div class="col-sm-12 col-md-6">
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
                            </div>
                          <div class="col-sm-12 col-md-6" style="text-align: right;">
                              <button type="button" class="btn btn-success" onclick="actualizar_tabla_origen()"><i class="fa-solid fa-search"></i> FILTRAR</button>
                          </div>
                        </div>
                        <div class="row mb-1">
                           <div class="col-sm-12 col-md-6">
                              <div class="row">
                                <label for="fecha_fin" class="col-sm-3 col-form-label">F. CRÉDITO</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="idformacredito" disabled>
                                      <option></option>
                                      <option value="0" selected>TODO</option>
                                      <option value="CP">CP</option>
                                      <option value="CNP">CNP</option>
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
                        </div>
                                
                    </div>
                </div>
                <div class="row">
                <div style="width:46%;float: left;">
                    
                    @include('app.nuevosistema.tabla',[
                        'tabla' => '#tabla-origendes',
                        'route' => url('backoffice/'.$tienda->id.'/garantiaremateagencia/showcliente_asignar?idagencia='.$tienda->id),
                        'check_id' => 'check_origen',
                        'scrollY' => 'calc(-321px  + 100vh)',
                        'dom' => 'rt',
                        'thead' => [
                            ['data' => '' ],
                            ['data' => 'N°' ],
                            ['data' => 'CUENTA' ],
                            ['data' => 'RUC/DNI/CE' ],
                            ['data' => 'Apellidos y Nombres' ],
                            ['data' => 'Monto Crédito (S/.)' ],
                            ['data' => 'F. Pago' ],
                            ['data' => 'Saldo Cuotas Venc. (S/.)' ],
                            ['data' => 'Días Vencido', 'class' => 'dia_vencido' ],
                            ['data' => 'Form. C.' ],
                            ['data' => 'Nro. de Cuotas Cumplido y Venc.' ],
                            ['data' => 'Tele./Celu.' ],
                            ['data' => 'F. Compromiso' ],
                            ['data' => 'Anotación' ],
                            ['data' => 'Direc/Domicilio' ],
                            ['data' => 'Calificación' ],
                            ['data' => 'Producto' ],
                            ['data' => 'Modalidad' ],
                            ['data' => 'RUC/DNI/CE (Aval)' ],
                            ['data' => 'Ape. Nom. Aval' ],
                            ['data' => 'Ejecutivo' ],
                        ],
                        'tbody' => [
                            ['data' => 'id','type'=>'check'],
                            ['data' => 'key','type'=>'text'],
                            ['data' => 'cuenta','type'=>'text'],
                            ['data' => 'identificacioncliente','type'=>'text'],
                            ['data' => 'nombrecliente','type'=>'text'],
                            ['data' => 'monto_solicitado','type'=>'money'],
                            ['data' => 'frecuencianombre','type'=>'text'],
                            ['data' => 'cuota_vencida','type'=>'money'],
                            ['data' => 'ultimo_atraso','type'=>'money'],
                            ['data' => 'cp','type'=>'text'],
                            ['data' => 'cuotas','type'=>'text'],
                            ['data' => 'telefonocliente','type'=>'text'],
                            ['data' => 'fechacompromiso','type'=>'text'],
                            ['data' => 'comentario','type'=>'text'],
                            ['data' => 'direccioncliente','type'=>'text'],
                            ['data' => 'clasificacion','type'=>'text'],
                            ['data' => 'nombreproductocredito','type'=>'text'],
                            ['data' => 'nombremodalidadcredito','type'=>'text'],
                            ['data' => 'identificacionaval','type'=>'text'],
                            ['data' => 'nombreaval','type'=>'text'],
                            ['data' => 'codigoasesor','type'=>'text'],
                        ],
                    ])
                <input type="hidden" id="check_origen">
                  <button type="button" class="btn btn-primary mt-1" id="btn-autorizar-garantia" onclick="ver_garantia()">
                      Garantías </button>
                </div>
                <div class="row  text-center align-items-center" 
                     style="width:8%;float: left;height: 350px;margin-left:0px;margin-right:0px;">
                    <div class="col-md-12">
                    <button type="button" class="btn btn-warning mb-1 mt-1" id="btn-autorizar-garantia" onclick="autorizar_garantia()">
                      Agregar <i class="fa fa-angle-right"></i></button>
                    <button type="button" class="btn btn-danger" id="btn-quitar-garantia" onclick="quitar_garantia()">
                      <i class="fa fa-angle-left"></i> Quitar</button>
                    </div>
                </div>
                <div style="width:46%;float: left;">
                    <div style="text-align: center;background-color: #a7a7a7;padding: 2px;">LISTA DE REMATES</div>
                    @include('app.nuevosistema.tabla',[
                        'tabla' => '#tabla-destinodes',
                        'route' => url('backoffice/'.$tienda->id.'/garantiaremateagencia/showcliente_destino'),
                        'check_id' => 'check_destino',
                        'scrollY' => 'calc(-321px  + 100vh)',
                        'dom' => 'rt',
                        'thead' => [
                            ['data' => '' ],
                            ['data' => 'CLIENTE' ],
                            ['data' => 'RUC/DNI/CE' ],
                            ['data' => 'TIPO DE GARANTÍA' ],
                            ['data' => 'DESCRIPCIÓN' ],
                            ['data' => 'MODELO' ],
                            ['data' => 'VALOR COMERCIAL' ],
                            ['data' => 'ACCESORIOS' ],
                            ['data' => 'COBERTURA' ],
                            ['data' => 'COLOR' ],
                            ['data' => 'CÓDIGO GARANTÍA' ],
                        ],
                        'tbody' => [
                            ['data' => 'id','type'=>'check'],
                            ['data' => 'cliente','type'=>'text'],
                            ['data' => 'dni','type'=>'text'],
                            ['data' => 'tipo_garantia','type'=>'text'],
                            ['data' => 'descripcion','type'=>'text'],
                            ['data' => 'modelo','type'=>'text'],
                            ['data' => 'valorcomercial','type'=>'text'],
                            ['data' => 'accesorios','type'=>'text'],
                            ['data' => 'cobertura','type'=>'text'],
                            ['data' => 'color','type'=>'text'],
                            ['data' => 'codigo_garantia','type'=>'text'],
                        ],
                    ])
                <input type="hidden" id="check_destino">
                  <button type="button" class="btn btn-warning1 mt-1">
                     LIQUIDACIÓN DE GARANTÍAS </button>
                  <button type="button" class="btn btn-info mt-1">
                     <i class="fa-solid fa-file-pdf"></i> REMATES </button>
                </div>
                </div>
            </div> 
          </div>
        </div>
      </div>
  </div>
</div>
              <style>
                #tabla-destinodes td,
                #tabla-origendes td {
                        white-space: nowrap;
                }
                .dia_vencido >span {
                  background-color: #ffb2b2 !important;
                }
              </style>
<style>
.form-check-input {
    width: 2em;
    height: 2em;
}
</style>
<script>

  sistema_select2({ input:'#idagencia',val:'{{$tienda->id}}' });
  sistema_select2({ input:'#idformacredito',val:'CP' });
  sistema_select2({ input:'#idasesor',val:'{{Auth::user()->id}}' });
  
    $(`#tabla-origendes`).on("click", "tr", function(e) {
        $('#tabla-destinodes > tbody > tr').removeClass('selected');
    });
    $(`#tabla-destinodes`).on("click", "tr", function(e) {
        $('#tabla-origendes > tbody > tr').removeClass('selected');
    });

  $('#tabla-origendes').on('change', 'input[type="checkbox"]', function () {
      if ($(this).is(':checked')) {
          // Desmarcar todos los checks de la tabla DESTINO
          $('#tabla-destinodes input[type="checkbox"]').prop('checked', false);
          // Opcional: limpiar valores hidden si usas
          $('#check_destino').val('');
      }
  });

  // Cuando hacen check en la tabla DESTINO
  $('#tabla-destinodes').on('change', 'input[type="checkbox"]', function () {
      if ($(this).is(':checked')) {
          // Desmarcar todos los checks de la tabla ORIGEN
          $('#tabla-origendes input[type="checkbox"]').prop('checked', false);
          // Opcional: limpiar valores hidden si usas
          $('#check_origen').val('');
      }
  });
  
  function ver_garantia(){
      let idcredito = $('#tabla-origendes > tbody > tr.selected').attr('data-valor-columna');    
      if(idcredito == "" || idcredito == undefined ){
        var mensaje = "Debe de seleccionar un crédito.";
        modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });  
        return false;
      }
      modal({ route:"{{url('backoffice/'.$tienda->id.'/garantiaremateagencia/0/edit?view=ver_garantia')}}&idcredito="+idcredito,  size: 'modal-fullscreen' }); 
  }
  
  function autorizar_garantia(){
      var check_origen = $('#check_origen').val();
      if(check_origen==''){
        var mensaje = "Debe de seleccionar un crédito.";
        modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });  
          return false;
      }
      modal({ route:"{{url('backoffice/'.$tienda->id.'/garantiaremateagencia/0/edit?view=autorizar')}}",  size: 'modal-sm' }); 
  }
  
  function quitar_garantia(){
      var check_destino = $('#check_destino').val();
      if(check_destino==''){
        var mensaje = "Debe de seleccionar un crédito.";
        modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });  
          return false;
      }
      modal({ route:"{{url('backoffice/'.$tienda->id.'/garantiaremateagencia/0/edit?view=quitar')}}",  size: 'modal-sm' }); 
  }
  
  actualizar_tabla_origen();
  
  function actualizar_tabla_origen(){
        var root = '{{url('backoffice/'.$tienda->id.'/garantiaremateagencia/showcliente_asignar')}}?idagencia='+$('#idagencia').val()+'&idformacredito='+$('#idformacredito').val()+'&idasesor='+$('#idasesor').val();
        $('#tabla-origendes').DataTable().ajax.url(root).load();
  }
  
  function actualizar_tabla_destino(){
        var root = '{{url('backoffice/'.$tienda->id.'/garantiaremateagencia/showcliente_destino')}}?idagencia='+$('#idagencia').val();
        $('#tabla-destinodes').DataTable().ajax.url(root).load();
  }
</script>  

