<div class="modal-header">
  <h5 class="modal-title">Asiganción de Cartera</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()" style="font-size: 20px;"></button>
</div>
<div class="modal-body">
  <form action="javascript:;" 
        onsubmit="callback({
            route: '{{ url('backoffice/'.$tienda->id.'/asignaciondecartera') }}',
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
                <div class="row">
                    <div class="col-sm-12 col-md-9">
                        <div class="row">
                           <div class="col-sm-12 col-md-6">
                              <div class="row">
                                <label for="fecha_inicio" class="col-sm-3 col-form-label">AGENCIA</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="idagencia">
                                      <option></option>
                                      @foreach($agencias as $value)
                                          <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                                      @endforeach
                                    </select>
                                </div>
                              </div>
                            </div>
                        </div>
                                
                    </div>
                </div>
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2">
             <div id="cont-filtro"></div>
            <div class="modal-body">
                <div style="width:47%;float: left;">
                    <span class="badge d-block" style="margin-bottom: 5px;">Origen</span>
                        <div class="row">
                           <div class="col-sm-12 col-md-12">
                              <div class="row">
                                <label for="fecha_fin" class="col-sm-3 col-form-label">EJECUTIVO</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="idasesororigen">
                                      <option></option>
                                      <?php
                                      $usuarios = DB::table('users')
                                          ->join('users_permiso','users_permiso.idusers','users.id')
                                          ->join('permiso','permiso.id','users_permiso.idpermiso')
                                          ->whereIn('users_permiso.idpermiso',[3,4,7])
                                          ->select('users.*','users_permiso.id as idusers_permiso','permiso.nombre as nombrepermiso')
                                          ->get();
                                      ?>
                                      @foreach($usuarios as $value)
                                      <option value="{{$value->id}}" idusers_permiso="{{$value->idusers_permiso}}">{{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                                      @endforeach
                                    </select>
                                </div>
                              </div>
                            </div>
                        </div>
                    @include('app.nuevosistema.tabla',[
                        'tabla' => '#tabla-origendes',
                        'route' => url('backoffice/'.$tienda->id.'/asignaciondecartera/showcliente_origen'),
                        'check_id' => 'check_origen',
                        'thead' => [
                            ['data' => '' ],
                            ['data' => '' ],
                            ['data' => 'Nombre' ],
                            ['data' => 'DOC' ],
                            ['data' => 'Estado' ],
                        ],
                        'tbody' => [
                            ['data' => 'orden','type'=>'text'],
                            ['data' => 'id','type'=>'check'],
                            ['data' => 'nombre','type'=>'text'],
                            ['data' => 'doc','type'=>'text'],
                            ['data' => 'estado','type'=>'text'],
                        ],
                        'tfoot' => [
                            ['type' => ''],
                            ['type' => 'check'],
                            ['type' => 'text'],
                            ['type' => 'text'],
                            ['type' => 'text'],
                        ]
                    ])
                <input type="hidden" id="check_origen">
                </div>
                <div class="row  text-center align-items-center" style="width:6%;float: left;height: 400px;">
                    <div class="col-md-12">
                      <button type="submit" class="btn  big-btn  color-bg flat-btn" style="background-color: #144081;
    color: #fff;width: 50px;"><i class="fa fa-angle-right"></i></button>
                    </div>
                </div>
                <div style="width:47%;float: left;">
                    <span class="badge d-block" style="margin-bottom: 5px;">Destino</span>
                        <div class="row">
                           <div class="col-sm-12 col-md-12">
                              <div class="row">
                                <label for="fecha_fin" class="col-sm-3 col-form-label">EJECUTIVO</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="idasesordestino" disabled>
                                      <option></option>
                                    </select>
                                </div>
                              </div>
                            </div>
                        </div>
                    @include('app.nuevosistema.tabla',[
                        'tabla' => '#tabla-destinodes',
                        'route' => url('backoffice/'.$tienda->id.'/asignaciondecartera/showcliente_destino'),
                        'thead' => [
                            ['data' => '' ],
                            ['data' => 'Nombre' ],
                            ['data' => 'DOC' ],
                        ],
                        'tbody' => [
                            ['data' => 'orden','type'=>'text'],
                            ['data' => 'nombre','type'=>'text'],
                            ['data' => 'doc','type'=>'text'],
                        ],
                        'tfoot' => [
                            ['type' => ''],
                            ['type' => 'text'],
                            ['type' => 'text'],
                        ]
                    ])
                </div>
            </div> 
          </div>
        </div>
      </div>
  </div>
  </form>
</div>
<style>
.form-check-input {
    width: 2em;
    height: 2em;
}
</style>
<script>

  sistema_select2({ input:'#idagencia' });
  sistema_select2({ input:'#idasesororigen' });
  sistema_select2({ input:'#idasesordestino' });

    $(`#idagencia`).on("change", function(e) {
        actualizar_tabla_origen();
        actualizar_tabla_destino();
    });
    $(`#idasesororigen`).on("change", function(e) {
        $('#idasesordestino').removeAttr('disabled');
        $.ajax({
          url:"{{url('backoffice/'.$tienda->id.'/asignaciondecartera/show_destino')}}",
          type:'GET',
          data: {
              idasesororigen : $('#idasesororigen').val(),
              idusers_permiso : $('#idasesororigen :selected').attr('idusers_permiso'),
          },
          success: function (res){
              $('#idasesordestino').html(res);
              sistema_select2({ input:'#idasesordestino' });
              actualizar_tabla_destino();
          }
        })
        actualizar_tabla_origen();
    });
    $(`#idasesordestino`).on("change", function(e) {
        actualizar_tabla_destino();
    });
  
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
      url:"{{url('backoffice/0/asignaciondecartera/enviar')}}",
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
  
  function actualizar_tabla_origen(){
        var root = '{{url('backoffice/'.$tienda->id.'/asignaciondecartera/showcliente_origen')}}?idagencia='+$('#idagencia').val()+'&idasesororigen='+$('#idasesororigen').val();
        $('#tabla-origendes').DataTable().ajax.url(root).load();
  }
  
  function actualizar_tabla_destino(){
        var root = '{{url('backoffice/'.$tienda->id.'/asignaciondecartera/showcliente_destino')}}?idagencia='+$('#idagencia').val()+'&idasesordestino='+$('#idasesordestino').val();
        $('#tabla-destinodes').DataTable().ajax.url(root).load();
  }
</script>  

