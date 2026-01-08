@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Confirmar Ahorro',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamo/ahorroconfirmacion: Ir Atras'
    ]
])
<div id="carga-ahorro">
    <div id="resultado-credito"></div>  
    <div class="tabs-container" id="tab-desembolso">
         <ul class="tabs-menu">
             <li class="current"><a href="#tab-desembolso-1">Confirmación</a></li>
             <li><a href="#tab-desembolso-2">Detalle de Ahorro</a></li>
         </ul>
         <div class="tab">
            <div id="tab-desembolso-1" class="tab-content" style="display: block;">
                <form action="javascript:;"
                      onsubmit="callback({
                          route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamo/ahorroconfirmacion/{{ $prestamoahorro->id }}',
                          method: 'PUT',
                          carga: '#carga-ahorro',
                          data:   {
                            view: 'confirmar',
                            idprestamo_tipoahorro: '{{$prestamoahorro->idprestamo_tipoahorro}}'
                          }
                        },
                        function(resultado){
                          location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorroconfirmacion') }}';
                        },this)">
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
                                  <label>Monto a Confirmar</label>
                                  <input type="text" value="{{$prestamoahorro->monedasimbolo}} {{$prestamoahorro->monto}}" disabled/>
                                  @elseif($prestamoahorro->idprestamo_tipoahorro==3)
                                  <label>Monto a Confirmar</label>
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
                                <label>Cliente *</label>
                                    <div class="row">
                                       <div class="col-md-12">
                                          <select id="idcliente" disabled>
                                              <option value="{{ $prestamoahorro->idcliente }}">{{ $prestamoahorro->cliente_nombre }}</option>
                                          </select>
                                       </div>
                                    </div>
                                    <label>Dirección *</label>
                                    <input type="text" id="cliente_direccion" value="{{ $prestamoahorro->cliente_direccion }}"/>
                                    <label>Ubicación (Ubigeo) *</label>
                                    <select id="idubigeo">
                                        <option value="{{ $prestamoahorro->cliente_idubigeo }}">{{ $prestamoahorro->cliente_ubigeonombre }}</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label>Agencia *</label>
                                    <select id="idagencia">
                                      <option></option>
                                      @foreach ($agencias as $value)
                                      <option value="{{ $value->id }}">{{ $value->ruc }} - {{ $value->nombrecomercial }}</option>
                                      @endforeach
                                    </select>
                                    <label>Moneda *</label>
                                    <select id="idmoneda">
                                      <option></option>
                                      @foreach ($monedas as $value)
                                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                      @endforeach
                                    </select>
                                    <label>Tipo de Comprobante *</label>
                                    <select id="idtipocomprobante">
                                      <option></option>
                                      @foreach ($tipocomprobante as $value)
                                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                      @endforeach
                                    </select>
                                </div>
                            </div>
                                  @if($prestamoahorro->idprestamo_tipoahorro==1)
                                  <label>Cobrar Ganancia *</label>
                                    <select id="idcobrarganancia">
                                      <option></option>
                                      <option value="1">AL INICIO DEL AHORRO</option>
                                      <option value="2">CADA MES</option>
                                      <option value="3">AL FINALIZAR EL AHORRO</option>
                                    </select>
                                  @endif
                        </div>
                    </div>
                            
                            
                    <button type="submit" class="btn mx-btn-post"><i class="fa fa-check"></i> Confirmar Ahorro</button>   
                </form>    
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

    $('#idcliente').select2({
      @include('app.select2_cliente')
    });
  
    $('#idubigeo').select2({
      @include('app.select2_ubigeo')
    });
  
        $("#idcobrarganancia").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @if(configuracion($tienda->id,'facturacion_empresapordefecto')['resultado']=='CORRECTO')
        $("#idagencia").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val({{ configuracion($tienda->id,'facturacion_empresapordefecto')['valor'] }}).trigger("change");    
    @else
        $("#idagencia").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif

    @if(configuracion($tienda->id,'facturacion_monedapordefecto')['resultado']=='CORRECTO')
        $("#idmoneda").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val({{ configuracion($tienda->id,'facturacion_monedapordefecto')['valor'] }}).trigger("change");
    @else
        $("#idmoneda").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif

    @if(configuracion($tienda->id,'facturacion_comprobantepordefecto')['resultado']=='CORRECTO')
        $("#idtipocomprobante").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val({{ configuracion($tienda->id,'facturacion_comprobantepordefecto')['valor'] }}).trigger("change");   
    @else
        $("#idtipocomprobante").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
  @endif
  

    expedientedetalle_index({{$prestamoahorro->id}});
    function expedientedetalle_index(idcredito){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorrosolicitud/'+idcredito+'/edit?view=expedientedetalle',result:'#cont-expedientedetalle'});
    }

</script>
@endsection   