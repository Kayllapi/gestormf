@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Activar Entretenimiento</span>
      <a class="btn btn-success" href="{{ url('backoffice/cineusuario') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div id="carga-activarentretenimiento">
    <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
            <div class="col-md-12">
                      <img src="{{url('/public/backoffice/consumidor/vouchercine/'.$userscine->vaucher)}}" style="
                          border-radius: 5px;
                          max-height: 500px;
                          max-width: 500px;
                          margin-bottom: 5px;
                          background-color: #c7c9cb;  
                          margin-bottom:5px;                                                                                          
                      ">
            </div>
            </div>
                      <div class="mensaje-warning">
                        <i class="fa fa-warning"></i> Verifica su voucher, una vez activada no podra anular.
                      </div>
            <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width: 100%;margin-bottom:5px;" onclick="activarentretenimiento()">Activar</button>
            <button type="button" class="btn  big-btn  color-bg flat-btn" style="width: 100%;background-color: #dd154d;" onclick="anularentretenimiento()">Anular</button>
        </div>
    </div>
</div>
@endsection
@section('scriptsbackoffice')
<script>
function activarentretenimiento(){
      callback({
          route: 'backoffice/cineusuario/{{ $userscine->id }}',
          method: 'PUT',
          carga: '#carga-activarentretenimiento',
          data: {
              view : 'activarentretenimiento',
              fechainicio : $('#fechainicio').val()
          }
      },
      function(resultado){
          location.href = '{{ url('backoffice/cineusuario') }}';                                                   
      })
}
function anularentretenimiento(){
      callback({
          route: 'backoffice/cineusuario/{{ $userscine->id }}',
          method: 'PUT',
          carga: '#carga-activarentretenimiento',
          data: {
              view : 'anularentretenimiento'
          }
      },
      function(resultado){
          location.href = '{{ url('backoffice/cineusuario') }}';                                                   
      })
}
</script>
@endsection