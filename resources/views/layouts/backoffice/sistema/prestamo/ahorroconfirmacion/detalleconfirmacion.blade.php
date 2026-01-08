@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Detalle de Confirmación',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamo/ahorroconfirmacion: Ir Atras'
    ]
])
<div id="carga-ahorro">
    <div class="tabs-container" id="tab-desembolso">
         <ul class="tabs-menu">
             <li class="current"><a href="#tab-desembolso-1">Confirmación</a></li>
             <li><a href="#tab-desembolso-2">Detalle de Ahorro</a></li>
         </ul>
         <div class="tab">
            <div id="tab-desembolso-1" class="tab-content" style="display: block;">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="list-single-main-wrapper fl-wrap">
                                <div class="breadcrumbs gradient-bg fl-wrap">
                                  <span>Ahorro Aprobado</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                  <label>Tipo de Ahorro</label>
                                  <input type="text" value="{{$prestamoahorro->tipoahorronombre}} {{$prestamoahorro->ahorrolibre_tiponombre!=''?'('.$prestamoahorro->ahorrolibre_tiponombre.')':''}}" disabled/>
                                  <label>Código de Ahorro</label>
                                  <input type="text" value="{{ str_pad($prestamoahorro->codigo, 8, "0", STR_PAD_LEFT) }}" disabled/>
                                  @if($prestamoahorro->idprestamo_tipoahorro==1 or $prestamoahorro->idprestamo_tipoahorro==2)
                                  <label>Monto Confirmado</label>
                                  <input type="text" value="{{$prestamoahorro->monedasimbolo}} {{$prestamoahorro->monto}}" disabled/>
                                  @elseif($prestamoahorro->idprestamo_tipoahorro==3)
                                  <label>Monto Confirmado</label>
                                  <input type="text" value="{{$prestamoahorro->monedasimbolo}} {{$prestamoahorro->ahorrolibre_monto}}" disabled/>
                                  @endif
                                  @if($prestamoahorro->idprestamo_tipoahorro==2)
                                  <label>Frecuencia</label>
                                  <input type="text" value="{{$prestamoahorro->frecuencia_nombre}}" disabled/>
                                  @endif
                                  @if($prestamoahorro->idprestamo_tipoahorro==1)
                                  <label>Tiempo</label>
                                  <input type="text" value="{{ $prestamoahorro->tiempo }} MESES" disabled/>
                                  @endif
                                </div>
                                <div class="col-sm-6">
                                  <label>Fecha de Aprobación</label>
                                  <input type="text" value="{{date_format(date_create($prestamoahorro->fechapreaprobado), "d/m/Y h:i:s A")}}" disabled/>
                                  <label>Asesor</label>
                                  <input type="text" value="{{$prestamoahorro->asesor_nombre}}" disabled/>
                                  <label>Supervisor</label>
                                  <input type="text" value="{{$prestamoahorro->supervisor_nombre}}" disabled/>
                                  <label>Cliente</label>
                                  <input type="text" value="{{$prestamoahorro->cliente_nombre}}" disabled/>
                                  <label>Cajero</label>
                                  <input type="text" value="{{$prestamoahorro->cajero_nombre}}" disabled/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="list-single-main-wrapper fl-wrap">
                                <div class="breadcrumbs gradient-bg fl-wrap">
                                  <span>Facturación</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label>Cliente</label>
                                    <input type="text" value="{{ $prestamoahorro->facturacion_cliente_apellidos }}, {{ $prestamoahorro->facturacion_cliente_nombre }}" disabled>

                                    <label>Dirección</label>
                                    <input type="text" id="cliente_direccion" value="{{ $prestamoahorro->facturacion_cliente_direccion }}" disabled>

                                    <label>Ubigeo</label>
                                    <input type="text" id="cliente_ubigeonombre" value="{{ $prestamoahorro->facturacion_cliente_ubigeonombre }}" disabled>
                                </div>
                                <div class="col-sm-6">
                                    <label>Agencia</label>
                                    <input type="text" id="agencia_nombre" value="{{ $prestamoahorro->facturacion_agenciaruc }} - {{ $prestamoahorro->facturacion_agenciarazonsocial }}" disabled>

                                    <label>Moneda</label>
                                    <input type="text" value="{{ $prestamoahorro->monedanombre }}" disabled>

                                    <label>Tipo de Comprobante</label>
                                    <input type="text" id="tipocomprobante_nombre" value="{{ $prestamoahorro->facturacion_tipocomprobantenombre }}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div id="tab-desembolso-2" class="tab-content" style="display: none;">
                <div id="cont-expedientedetalle"></div> 
            </div>
        </div>
    </div>
</div>
@endsection

@section('subscripts')
<script>
    tab({click:'#tab-desembolso'});  
    expedientedetalle_index({{$prestamoahorro->id}});
    function expedientedetalle_index(idcredito){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorrosolicitud/'+idcredito+'/edit?view=expedientedetalle',result:'#cont-expedientedetalle'});
    }   
</script>
@endsection