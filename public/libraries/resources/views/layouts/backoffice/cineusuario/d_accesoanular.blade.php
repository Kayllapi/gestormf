@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Anular Acceso / {{ $usuario->nombre }}</span>
      <a class="btn btn-success" href="{{ url('backoffice/cineusuario/'.$usuario->id.'/edit?view=acceso') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
                  route: 'backoffice/cineusuario/{{$userscine->id}}',
                  method: 'PUT',
                  data: {
                      view : 'accesoanular'
                  }
              },
              function(resultado){
                if (resultado.resultado == 'CORRECTO') {
                  location.href = '{{ url('backoffice/cineusuario/'.$usuario->id.'/edit?view=acceso') }}';                           
                }
              },this)">
      <div class="profile-edit-container">
        <div class="custom-form">
                  <div class="row">
                    <div class="col-md-4">
                    <label>Días de regalo</label>
                    <input type="number" value="{{$userscine->diasprueba}}" id="diasprueba" disabled/>
                    </div>
                    <div class="col-md-4">
                        <label>Fecha de inicio</label>
                        <input type="date" value="{{$userscine->fechainicio}}" id="fechainicio" disabled/>
                    </div>
                    <div class="col-md-4">
                        <label>Fecha fin</label>
                        <input type="date" value="{{$userscine->fechafin}}" id="fechafin" disabled/>
                    </div>
                  </div>
        </div>
    </div>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de anular el acceso?
    </div>
    <div class="custom-form">
        <button type="submit" class="btn  big-btn  color-bg flat-btn btn-danger" style="width:100%">Anular Acceso</button>
    </div>
</form>
@endsection