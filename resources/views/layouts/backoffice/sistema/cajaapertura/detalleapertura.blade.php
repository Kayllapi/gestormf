@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle de Apertura de Caja</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
          <div class="row">
             <div class="col-md-6">
                <label>Caja</label>
                <input type="text" value="{{ $s_aperturacierre->cajanombre }}" disabled/>
                @if($s_aperturacierre->config_sistema_moneda_usar==1)
                        <label>Monto asignado en Soles</label>
                        <input type="number" value="{{ $s_aperturacierre->montoasignar }}" disabled/>
                @elseif($s_aperturacierre->config_sistema_moneda_usar==2)
                        <label>Monto asignado en Dolares</label>
                        <input type="number" value="{{ $s_aperturacierre->montoasignar_dolares }}" disabled/>
                @elseif($s_aperturacierre->config_sistema_moneda_usar==3)
                <div class="row">
                    <div class="col-md-6">
                        <label>Monto asignado en Soles</label>
                        <input type="number" value="{{ $s_aperturacierre->montoasignar }}" disabled/>
                    </div>
                    <div class="col-md-6">
                        <label>Monto asignado en Dolares</label>
                        <input type="number" value="{{ $s_aperturacierre->montoasignar_dolares }}" disabled/>
                    </div>
                </div>
                @endif
                <label>Persona asignado</label>
                <input type="text" value="{{ $s_aperturacierre->usersrecepcionapellidos }}, {{ $s_aperturacierre->usersrecepcionnombre }}" disabled/>
             </div>
             <div class="col-md-6">
                <label>Persona responsable</label>
                <input type="text" value="{{ $s_aperturacierre->usersresponsableapellidos }}, {{ $s_aperturacierre->usersresponsablenombre }}" disabled/>
                <label>Fecha de Apertura</label>
                <input type="text" value="{{ date_format(date_create($s_aperturacierre->fecharegistro),"d/m/Y h:i:s A") }}" disabled/>
                <label>Fecha de Confirmación de Apertura</label>
                <input type="text" value="{{ $s_aperturacierre->fechaconfirmacion!=''?date_format(date_create($s_aperturacierre->fechaconfirmacion),"d/m/Y h:i:s A"):'' }}" disabled/>
             </div>
           </div>                         
@endsection