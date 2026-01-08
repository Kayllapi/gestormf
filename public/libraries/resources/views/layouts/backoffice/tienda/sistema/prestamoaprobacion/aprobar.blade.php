@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Crédito',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamoaprobacion: Ir Atras'
    ]
])
<div id="carga-credito">
    <div id="resultado-credito"></div>  
  
    @include('app.prestamo_creditodetalle',[
      'idtienda'=>$tienda->id,
      'idprestamocredito'=>$prestamocredito->id
    ])    
  
    <div class="row">
        <div class="col-md-4">
            <a href="javascript:;" id="modal-aprobarcredito" class="btn mx-btn-post" style="margin-bottom: 5px;"><i class="fa fa-check"></i> Aprobar Crédito</a>
        </div>
        <div class="col-md-4">
            <a href="javascript:;" id="modal-rechazarcredito" class="btn mx-btn-post" style="margin-bottom: 5px;"><i class="fa fa-ban"></i> Rechazar Crédito</a>
        </div>
        <div class="col-md-4">
            <a href="javascript:;" id="modal-denegarcredito" class="btn mx-btn-post" style="margin-bottom: 5px;"><i class="fa fa-close"></i> Denegar Crédito</a>
        </div>
    </div>
</div>
@endsection
@section('htmls')
<!--  modal aprobarcredito --> 
<div class="main-register-wrap modal-aprobarcredito" id="modal-aprobarcredito">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Aprobar Crédito</h3>
            <div class="mx-modal-cuerpo">
                <div class="custom-form">
                <div class="profile-edit-container">
                    <form action="javascript:;"
                          onsubmit="callback({
                              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamoaprobacion/{{$prestamocredito->id}}',
                              method: 'PUT',
                              data:   {
                                  view: 'aprobar'
                              }
                          },
                          function(resultado){
                              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoaprobacion') }}';
                          },this)">
                    <label>Comentario/Observación</label>
                    <textarea name="observacionsupervisor" id="observacionsupervisor_aprobar" cols="30" rows="10" onkeyup="texto_mayucula(this)"></textarea>
                    <button type="submit" class="btn mx-btn-post"><i class="fa fa-check"></i> Aprobar Crédito</button>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  fin modal aprobarcredito --> 
<!--  modal rechazarcredito --> 
<div class="main-register-wrap modal-rechazarcredito" id="modal-rechazarcredito">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Rechazar Crédito</h3>
            <div class="mx-modal-cuerpo">
                <div class="custom-form">
                <div class="profile-edit-container">
                    <form action="javascript:;"
                          onsubmit="callback({
                              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamoaprobacion/{{$prestamocredito->id}}',
                              method: 'PUT',
                              data:   {
                                  view: 'rechazar'
                              }
                          },
                          function(resultado){
                              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoaprobacion') }}';
                          },this)">
                    <label>Comentario/Observación *</label>
                    <textarea name="observacionsupervisor" id="observacionsupervisor_rechazar" cols="30" rows="10" onkeyup="texto_mayucula(this)"></textarea>
                    <button type="submit" class="btn mx-btn-post"><i class="fa fa-ban"></i> Rechazar Crédito</button>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  fin modal rechazarcredito --> 
<!--  modal denegarcredito --> 
<div class="main-register-wrap modal-denegarcredito" id="modal-denegarcredito">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Denegar Crédito</h3>
            <div class="mx-modal-cuerpo">
                <div class="custom-form">
                <div class="profile-edit-container">
                    <form action="javascript:;"
                          onsubmit="callback({
                              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamoaprobacion/{{$prestamocredito->id}}',
                              method: 'PUT',
                              data:   {
                                  view: 'denegar'
                              }
                          },
                          function(resultado){
                              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoaprobacion') }}';
                          },this)">
                    <label>Comentario/Observación *</label>
                    <textarea name="observacionsupervisor" id="observacionsupervisor_denegar" cols="30" rows="10" onkeyup="texto_mayucula(this)"></textarea>
                    <button type="submit" class="btn mx-btn-post"><i class="fa fa-close"></i> Denegar Crédito</button>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  fin modal denegarcredito --> 
@endsection
@section('subscripts')
<script>
    tab({click:'#tab-resultado'});
    modal({click:'#modal-aprobarcredito'});
    modal({click:'#modal-rechazarcredito'});
    modal({click:'#modal-denegarcredito'});
</script>
@endsection