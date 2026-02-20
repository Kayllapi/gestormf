<div class="modal-header">
  <h5 class="modal-title">Cartera de Cliente</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <form action="javascript:;" 
        onsubmit="callback({
            route: '{{ url('backoffice/'.$tienda->id.'/carteradecliente') }}',
            method: 'POST',
            data:{
                view: 'enviar',
            }
        },
        function(resultado){
        actualizar_tabla_origen();
        actualizar_tabla_destino();
        $('#check_origen').val('');
        $('#check_seleccionar_todocheck_origen').prop('checked',false);
        },this)"> 
          <div class="card">
            <div class="card-body p-2">
                <div class="modal-body pb-0">
                <div class="row">
                    <div class="col-sm-12 col-md-9">
                        <div class="row">
                           <div class="col-sm-12 col-md-8">
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
                        </div>
                        <div class="row">
                           <div class="col-sm-12 col-md-8">
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
                        </div>
                        <div class="row">
                           <div class="col-sm-12 col-md-8">
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
                          <div class="col-sm-12 col-md-2" style="text-align: right;">
                              <button type="button" class="btn btn-success" onclick="lista_credito()"><i class="fa-solid fa-search"></i> FILTRAR</button>
                          </div>
                        </div>
                                
                    </div>
                </div>
                </div>
            </div>
        </div>
  <div class="row mt-1 mb-1">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2">
             <div id="cont-filtro"></div>
            <div class="modal-body p-0">
                    @include('app.nuevosistema.tabla',[
                        'tabla' => '#tabla-origendes',
                        'route' => url('backoffice/'.$tienda->id.'/carteradecliente/showcliente'),
                        'scrollY' => 'calc(-398px + 100vh)',
                        'thead' => [
                            ['data' => '' ],
                            ['data' => 'Cod. Cliente' ],
                            ['data' => 'DOC' ],
                            ['data' => 'Nombre' ],
                            ['data' => 'Ejecutivo' ],
                            ['data' => 'Saldo C. Ult. Desemb. (S/.)' ],
                            ['data' => 'F. Pago' ],
                            ['data' => 'Cuotas' ],
                            ['data' => 'Form. C.' ],
                            ['data' => 'Producto' ],
                            ['data' => 'Fecha Cancelación' ],
                            ['data' => 'Telefóno' ],
                            ['data' => 'Direc/Domicilio' ],
                            ['data' => 'Direc/Negocio' ],
                        ],
                        'tbody' => [
                            ['data' => 'orden','type'=>'text'],
                            ['data' => 'codigo','type'=>'text'],
                            ['data' => 'doc','type'=>'text'],
                            ['data' => 'nombre','type'=>'text'],
                            ['data' => 'asesororigen','type'=>'text'],
                            ['data' => 'saldo','type'=>'money'],
                            ['data' => 'formapago','type'=>'text'],
                            ['data' => 'cuota','type'=>'text'],
                            ['data' => 'fomac','type'=>'text'],
                            ['data' => 'producto','type'=>'text'],
                            ['data' => 'fechacancelado','type'=>'text'],
                            ['data' => 'telefono','type'=>'text'],
                            ['data' => 'direcciondomicilio','type'=>'text'],
                            ['data' => 'direccionnegocio','type'=>'text'],
                        ],
                        'tfoot' => [
                            ['type' => ''],
                            ['type' => 'text'],
                            ['type' => 'text'],
                            ['type' => 'text'],
                            ['type' => 'text'],
                            ['type' => 'text'],
                            ['type' => 'text'],
                            ['type' => 'text'],
                            ['type' => 'text'],
                            ['type' => 'text'],
                            ['type' => 'text'],
                            ['type' => 'text'],
                            ['type' => 'text'],
                            ['type' => 'text'],
                        ]
                    ])
            </div> 
          </div>
        </div>
      </div>
  </div>
  </form>
                              <div style="text-align: right;">
                                <button type="button" class="btn btn-info" onclick="exportar_pdf()" style="font-weight: bold;">
                                  <i class="fa-solid fa-file-pdf" style="color:#000 !important;font-weight: bold;"></i> REPORTE PDF</button>
                                <button type="button" class="btn btn-success" onclick="exportar_excel()" style="font-weight: bold;">
                                  <i class="fa-solid fa-file-excel" style="color:#000 !important;font-weight: bold;"></i> REPORTE EXCEL</button>
                              </div>
</div>
<style>
.form-check-input {
    width: 2em;
    height: 2em;
}
</style>
<script>

  sistema_select2({ input:'#idagencia' });
  sistema_select2({ input:'#idasesor' });
  sistema_select2({ input:'#idformacredito' });


  function lista_credito(){
      actualizar_tabla();
  }
  
  function enviar(){
    callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/cliente',
        method: 'POST',
        data:{
            view: 'registrar',
            idtienda: '{{ $tienda->id }}'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cliente') }}';                                                                            
    })
    
    $.ajax({
      url:"{{url('backoffice/0/carteradecliente/enviar')}}",
      type:'GET',
      data: {
          idagencia : $('#idagencia').val(),
          idasesororigen : $('#idasesororigen').val(),
          idasesordestino : $('#idasesordestino').val(),
          check_origen : $('#check_origen').val(),
      },
      success: function (res){
        $('#cont-filtro').html(res.html);
      }
    })
  }
  
  function actualizar_tabla(){
        var root = '{{url('backoffice/'.$tienda->id.'/carteradecliente/showcliente')}}?idagencia='+$('#idagencia').val()+'&idasesor='+$('#idasesor').val()+'&idformacredito='+$('#idformacredito').val();
        $('#tabla-origendes').DataTable().ajax.url(root).load();
  }

   function exportar_pdf(){
      let url = "{{ url('backoffice/'.$tienda->id) }}/carteradecliente/0/edit?view=exportar&idagencia="+$('#idagencia').val()+
          "&idasesor="+$('#idasesor').val()+
          "&idformacredito="+$('#idformacredito').val()+
          "&tipo=admin";
      modal({ route: url,size:'modal-fullscreen' })
   }
  
   function exportar_excel(){
        window.location.href = '{{url('backoffice/'.$tienda->id.'/carteradecliente/0/edit')}}?view=exportar_excel&idagencia='+$('#idagencia').val()+
              '&idasesor='+$('#idasesor').val()+
              '&idformacredito='+$('#idformacredito').val()+
              '&tipo=admin';
    }
</script>  

