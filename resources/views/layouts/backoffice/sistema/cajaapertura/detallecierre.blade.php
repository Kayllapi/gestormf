@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Detalle de Cierre de Caja',
    'botones'=>[
        'atras:/'.$tienda->id.'/cajaapertura: Ir Atras'
    ]
])
 
        <div class="row">
          <div class="col-md-6">
             <label>Responsable de Asiganción</label>
             <input type="text" value="{{ $s_aperturacierre->usersresponsableapellidos }}, {{ $s_aperturacierre->usersresponsablenombre }}" disabled/>
             <label>Responsable de Cierre</label>
             <input type="text" value="{{ $s_aperturacierre->usersrecepcionapellidos }}, {{ $s_aperturacierre->usersrecepcionnombre }}" disabled/>
          </div>
          <div class="col-md-6">
            @if($s_aperturacierre->config_sistema_moneda_usar==1)
                     <label>Total en Soles</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre }}" disabled/>
                     <label>Total cerrado en Soles</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_recibido }}" disabled/>
                @if($s_aperturacierre->montocierre<$s_aperturacierre->montocierre_recibido)
                     <label>Total Sobrante Soles</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre_recibido-$s_aperturacierre->montocierre, 2, '.', '') }}" id="totalsobrante_soles" disabled/>
                @elseif($s_aperturacierre->montocierre>$s_aperturacierre->montocierre_recibido)
                     <label>Total Faltante Soles</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre-$s_aperturacierre->montocierre_recibido, 2, '.', '') }}" id="totalsobrante_soles" disabled/>
                @endif
            @elseif($s_aperturacierre->config_sistema_moneda_usar==2)
                     <label>Total en Dolares</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_dolares }}" disabled/>
                     <label>Total cerrado en Dolares</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_recibido_dolares }}" disabled/>
                @if($s_aperturacierre->montocierre_dolares<$s_aperturacierre->montocierre_recibido_dolares)
                     <label>Total Sobrante Dolares</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre_recibido_dolares-$s_aperturacierre->montocierre_dolares, 2, '.', '') }}" id="totalsobrante_dolares" disabled/>
                @elseif($s_aperturacierre->montocierre_dolares>$s_aperturacierre->montocierre_recibido_dolares)
                     <label>Total Faltante Dolares</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre_dolares-$s_aperturacierre->montocierre_recibido_dolares, 2, '.', '') }}" id="totalsobrante_dolares" disabled/>
                @endif
            @elseif($s_aperturacierre->config_sistema_moneda_usar==3)
             <div class="row">
                 <div class="col-md-6">
                     <label>Total en Soles</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre }}" disabled/>
                 </div>
                 <div class="col-md-6">
                     <label>Total en Dolares</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_dolares }}" disabled/>
                 </div>
             </div>
             <div class="row">
                 <div class="col-md-6">
                     <label>Total cerrado en Soles</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_recibido }}" disabled/>
                 </div>
                 <div class="col-md-6">
                     <label>Total cerrado en Dolares</label>
                     <input type="number" value="{{ $s_aperturacierre->montocierre_recibido_dolares }}" disabled/>
                 </div>
             </div>
             <div class="row">
                 <div class="col-md-6">
                @if($s_aperturacierre->montocierre<$s_aperturacierre->montocierre_recibido)
                     <label>Total Sobrante Soles</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre_recibido-$s_aperturacierre->montocierre, 2, '.', '') }}" id="totalsobrante_soles" disabled/>
                @elseif($s_aperturacierre->montocierre>$s_aperturacierre->montocierre_recibido)
                     <label>Total Faltante Soles</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre-$s_aperturacierre->montocierre_recibido, 2, '.', '') }}" id="totalsobrante_soles" disabled/>
                @endif
                 </div>
                 <div class="col-md-6">
                @if($s_aperturacierre->montocierre_dolares<$s_aperturacierre->montocierre_recibido_dolares)
                     <label>Total Sobrante Dolares</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre_recibido_dolares-$s_aperturacierre->montocierre_dolares, 2, '.', '') }}" id="totalsobrante_dolares" disabled/>
                @elseif($s_aperturacierre->montocierre_dolares>$s_aperturacierre->montocierre_recibido_dolares)
                     <label>Total Faltante Dolares</label>
                     <input type="number" value="{{ number_format($s_aperturacierre->montocierre_dolares-$s_aperturacierre->montocierre_recibido_dolares, 2, '.', '') }}" id="totalsobrante_dolares" disabled/>
                @endif
                 </div>
             </div>
            @endif
          </div>
        </div>
        <div class="list-single-main-wrapper fl-wrap">
            <div class="breadcrumbs gradient-bg fl-wrap">
              <span>Detalle</span>
            </div>
        </div>
        @include('app.sistema_efectivo',['tienda'=>$tienda,'idaperturacierre'=>$s_aperturacierre->id])
@endsection