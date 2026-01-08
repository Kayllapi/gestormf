@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Detalle de Desembolso',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamodesembolso: Ir Atras'
    ]
])
<div id="carga-credito">
    <div class="tabs-container" id="tab-desembolso">
         <ul class="tabs-menu">
             <li class="current"><a href="#tab-desembolso-1">Desembolso</a></li>
             <li><a href="#tab-desembolso-2">Detalle de Crédito</a></li>
         </ul>
         <div class="tab">
            <div id="tab-desembolso-1" class="tab-content" style="display: block;">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="list-single-main-wrapper fl-wrap">
                                <div class="breadcrumbs gradient-bg fl-wrap">
                                  <span>Crédito Aprobado</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                  <label>Tipo de Crédito</label>
                                  <input type="text" value="{{$prestamodesembolso->tipocreditonombre}}" disabled/>
                                  <label>Código de Crédito</label>
                                  <input type="text" value="{{ str_pad($prestamodesembolso->codigo, 8, "0", STR_PAD_LEFT) }}" disabled/>
                                  <label>Monto Desembolsado</label>
                                  <input type="text" value="{{$prestamodesembolso->monedasimbolo}} {{$prestamodesembolso->monto}}" disabled/>
                                  @if($prestamodesembolso->total_abono>0)
                                  <label>Monto a Abonar</label>
                                  <input type="text" value="{{$prestamodesembolso->monedasimbolo}} {{$prestamodesembolso->total_abono}}" disabled/>
                                  @endif
                                  <label>Frecuencia</label>
                                  <input type="text" value="{{$prestamodesembolso->frecuencia_nombre}}" disabled/>
                                </div>
                                <div class="col-sm-6">
                                  <label>Fecha de Aprobación</label>
                                  <input type="text" value="{{date_format(date_create($prestamodesembolso->fechapreaprobado), "d/m/Y h:i:s A")}}" disabled/>
                                  <label>Asesor</label>
                                  <input type="text" value="{{$prestamodesembolso->asesor_nombre}}" disabled/>
                                  <label>Supervisor</label>
                                  <input type="text" value="{{$prestamodesembolso->supervisor_nombre}}" disabled/>
                                  <label>Cliente</label>
                                  <input type="text" value="{{$prestamodesembolso->cliente_nombre}}" disabled/>
                                  <label>Cajero</label>
                                  <input type="text" value="{{$prestamodesembolso->cajero_nombre}}" disabled/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                          @if( $prestamodesembolso->total_gastoadministrativo>0)
                            <div class="list-single-main-wrapper fl-wrap">
                                <div class="breadcrumbs gradient-bg fl-wrap">
                                  <span>Gasto Administrativo</span>
                                </div>
                            </div>
                            <label>Gasto Administrativo</label>
                            <input type="text" id="total_gastoadministrativo" value="{{ $prestamodesembolso->total_gastoadministrativo }}" disabled/>
                            @if($prestamodesembolso->idestadogastoadministrativo==1)
                            <div id="cont-gastoadministrativo">
                                <label>Monto recibido</label>
                                <input type="number" id="facturacion_montorecibido" value="{{ $prestamodesembolso->facturacion_montorecibido }}" min="0" step="0.01" disabled/>
                                <label>Vuelto</label>
                                <input type="number" id="facturacion_vuelto" value="{{ $prestamodesembolso->facturacion_vuelto }}" min="0" step="0.01" disabled/>
                            </div>
                            @endif
                            @endif
                            <div class="list-single-main-wrapper fl-wrap">
                                <div class="breadcrumbs gradient-bg fl-wrap">
                                  <span>Facturación</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label>Cliente</label>
                                    <input type="text" value="{{ $prestamodesembolso->facturacion_cliente_apellidos }}, {{ $prestamodesembolso->facturacion_cliente_nombre }}" disabled>

                                    <label>Dirección</label>
                                    <input type="text" id="cliente_direccion" value="{{ $prestamodesembolso->facturacion_cliente_direccion }}" disabled>

                                    <label>Ubigeo</label>
                                    <input type="text" id="cliente_ubigeonombre" value="{{ $prestamodesembolso->facturacion_cliente_ubigeonombre }}" disabled>
                                </div>
                                <div class="col-sm-6">
                                    <label>Agencia</label>
                                    <input type="text" id="agencia_nombre" value="{{ $prestamodesembolso->facturacion_agenciaruc }} - {{ $prestamodesembolso->facturacion_agenciarazonsocial }}" disabled>

                                    <label>Moneda</label>
                                    <input type="text" value="{{ $prestamodesembolso->monedanombre }}" disabled>

                                    <label>Tipo de Comprobante</label>
                                    <input type="text" id="tipocomprobante_nombre" value="{{ $prestamodesembolso->facturacion_tipocomprobantenombre }}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div id="tab-desembolso-2" class="tab-content" style="display: none;">
                @include('app.prestamo_creditodetalle',[
                  'idtienda'=>$tienda->id,
                  'idprestamocredito'=>$prestamodesembolso->id
                ])  
            </div>
        </div>
    </div>
</div>
@endsection

@section('subscripts')
<script>
    tab({click:'#tab-resultado'});
    tab({click:'#tab-desembolso'});   
</script>
@endsection