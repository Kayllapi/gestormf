@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Preaprobar Crédito Grupal',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamosolicitudgrupal: Ir Atras'
    ]
])
<div id="carga-credito">
    <div id="resultado-credito"></div>   
    <div id="cont-expedientedetalle"></div>
    @if($prestamocreditogrupal->fechainiciocero>=Carbon\Carbon::now()->format('Y-m-d'))
    <form action="javascript:;"
          onsubmit="callback({
              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitudgrupal/{{$prestamocreditogrupal->id}}',
              method: 'PUT',
              carga: '#carga-credito',
              data:   {
                  view: 'preaprobar'
              }
          },
          function(resultado){
              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitudgrupal') }}';
          },this)">
          <button type="submit" class="btn mx-btn-post"><i class="fa fa-check"></i> Preaprobar Crédito</button>
    </form>
    @else
    <a href="javascript:;" id="modal-preaprobarcredito" class="btn mx-btn-post"><i class="fa fa-check"></i> Preaprobar Crédito</a>
    @endif
</div>
@endsection
@section('htmls')
<!--  modal preaprobarcredito --> 
<div class="main-register-wrap modal-preaprobarcredito" id="modal-preaprobarcredito">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Preaprobar Crédito</h3>
            <div class="mx-modal-cuerpo">
                <div class="custom-form">
                <div class="profile-edit-container">
                    <div class="mensaje-warning">
                      La Fecha de Inicio del Crèdito es diferente a la de hoy.<br>
                      <b>Fecha de Inicio: {{date_format(date_create($prestamocreditogrupal->fechainiciocero),"d/m/Y")}}</b>
                    </div>
                    <form action="javascript:;"
                          onsubmit="callback({
                              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitudgrupal/{{$prestamocreditogrupal->id}}',
                              method: 'PUT',
                              carga: '#carga-credito',
                              data:   {
                                  view: 'preaprobar'
                              }
                          },
                          function(resultado){
                              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitudgrupal') }}';
                          },this)">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitudgrupal/{{$prestamocreditogrupal->id}}/edit?view=editar" class="btn mx-btn-post"><i class="fa fa-edit"></i> Modificar Crédito</a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn mx-btn-post"><i class="fa fa-check"></i> Preaprobar Crédito</button>
                            </div>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  fin modal preaprobarcredito -->   
@endsection
@section('subscripts')
<script>
    modal({click:'#modal-preaprobarcredito'});
    expedientedetalle_index({{$prestamocreditogrupal->id}});
    function expedientedetalle_index(idcredito){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitudgrupal/'+idcredito+'/edit?view=expedientedetalle',result:'#cont-expedientedetalle'});
    }
</script>     
@endsection