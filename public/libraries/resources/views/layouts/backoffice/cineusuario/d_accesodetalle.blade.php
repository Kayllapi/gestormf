@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle de Acceso / {{ $usuario->nombre }}</span>
      <a class="btn btn-success" href="{{ url('backoffice/cineusuario/'.$usuario->id.'/edit?view=acceso') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
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
@endsection