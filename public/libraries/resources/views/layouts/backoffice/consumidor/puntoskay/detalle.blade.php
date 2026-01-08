@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>DETALLE DE PUNTOS</span>
      <a class="btn btn-success" href="{{ url('backoffice/puntoskay') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>

<div id="carga-puntoskay">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
              <label>Fecha de Registro</label>
              <input type="text" value="{{ date_format(date_create($puntoskay->fecharegistro),"d/m/Y h:i:s A") }}" id="correo" disabled>
              <label>Correo Electrónico</label>
              <input type="text" value="{{ $puntoskay->usersemail }}" id="correo" disabled>
              <label>Apellidos y Nombres</label>
              <input type="text" value="{{ $puntoskay->usersapellidos }}, {{ $puntoskay->usersnombre }}" id="nombre" disabled>
              <label>Cantidad de Puntos KAY</label>
              <input type="text" value="{{ $puntoskay->cantidad }}" id="cantidad" disabled>
              <label>Total a Pagar</label>
              <input type="text" value="{{ $puntoskay->monto }}" id="monto" disabled>
            </div>
            <div class="col-md-6">
              <img src="{{ url('public/backoffice/consumidor/voucher/'.$puntoskay->voucher) }}" style="margin-bottom: 5px;">
            </div>
          </div>
        </div>
    </div>
</div>                           
@endsection