@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Desembolsar Crédito',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamodesembolso: Ir Atras'
    ]
])
@if($prestamo_cobranza!='')
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> Hay una cobranza pendiente de este Cliente!!.
    </div>
@else

<div id="carga-credito">
    <div id="resultado-credito"></div>  
    <div class="tabs-container" id="tab-desembolso">
         <ul class="tabs-menu">
             <li class="current"><a href="#tab-desembolso-1">Desembolso</a></li>
             <li><a href="#tab-desembolso-2">Detalle de Crédito</a></li>
         </ul>
         <div class="tab">
            <div id="tab-desembolso-1" class="tab-content" style="display: block;">
                <form action="javascript:;"
                      onsubmit="callback({
                          route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamodesembolso/{{ $prestamodesembolso->id }}',
                          method: 'PUT',
                          carga: '#carga-credito',
                          data:   {
                            view: 'desembolsar'
                          }
                        },
                        function(resultado){
                          location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamodesembolso') }}';
                        },this)">
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
                                  <label>Monto a Desembolsar</label>
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
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <?php $total_gastoadministrativo = '0.00'; ?>
                            @if(configuracion($tienda->id,'prestamo_estadogasto_administrativo')['valor']=='on')
                            <div class="list-single-main-wrapper fl-wrap">
                                <div class="breadcrumbs gradient-bg fl-wrap">
                                  <span>Gasto Administrativo</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                  <label>Monto a Pagar</label>
                                  <?php 
                                  if($prestamodesembolso->monto>=0 && $prestamodesembolso->monto<=500){
                                      $total_gastoadministrativo = configuracion($tienda->id,'prestamo_gasto_administrativo_uno')['valor'];
                                  }elseif($prestamodesembolso->monto>500 && $prestamodesembolso->monto<=1000){
                                      $total_gastoadministrativo = configuracion($tienda->id,'prestamo_gasto_administrativo_dos')['valor'];
                                  }elseif($prestamodesembolso->monto>=000 && $prestamodesembolso->monto<=2000){
                                      $total_gastoadministrativo = configuracion($tienda->id,'prestamo_gasto_administrativo_tres')['valor'];
                                  }elseif($prestamodesembolso->monto>2000 && $prestamodesembolso->monto<=5000){
                                      $total_gastoadministrativo = configuracion($tienda->id,'prestamo_gasto_administrativo_cuatro')['valor'];
                                  }elseif($prestamodesembolso->monto>5000){
                                      $total_gastoadministrativo = configuracion($tienda->id,'prestamo_gasto_administrativo_cinco')['valor'];
                                  }
                                  ?>
                                  <input type="text" id="total_gastoadministrativo" value="{{ number_format($total_gastoadministrativo, 2, '.', '')}}" disabled/>
                                </div>
                                <div class="col-sm-6">
                                  <label>Agregar a Crédito</label>
                                        <div class="onoffswitch">
                                            <input type="checkbox" class="onoffswitch-checkbox check_gastoadministrativo" id="check_gastoadministrativo">
                                            <label class="onoffswitch-label" for="check_gastoadministrativo">
                                                <span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label> 
                                        </div>
                                </div>
                            </div>
                            <div id="cont-gastoadministrativo">
                                <div class="row">
                                    <div class="col-sm-6">
                                    <label>Monto recibido *</label>
                                    <input type="number" id="facturacion_montorecibido" min="0" step="0.01" onkeyup="calcular_vuelto_cuota()"/>
                                    </div>
                                    <div class="col-sm-6">
                                    <label>Vuelto</label>
                                    <input type="number" id="facturacion_vuelto" min="0" step="0.01" disabled/>
                                    </div>
                                </div>
                            </div>
                            @endif
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
                                              <option value="{{ $prestamodesembolso->idcliente }}">{{ $prestamodesembolso->cliente_nombre }}</option>
                                          </select>
                                       </div>
                                    </div>
                                    <label>Dirección *</label>
                                    <input type="text" id="cliente_direccion" value="{{ $prestamodesembolso->cliente_direccion }}"/>
                                    <label>Ubicación (Ubigeo) *</label>
                                    <select id="idubigeo">
                                        <option value="{{ $prestamodesembolso->cliente_idubigeo }}">{{ $prestamodesembolso->cliente_ubigeonombre }}</option>
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
                        </div>
                    </div>
                            
                            
                    <button type="submit" class="btn mx-btn-post"><i class="fa fa-check"></i> Desembolsar Crédito</button>   
                </form>    
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
@endif
@endsection

@section('subscripts')
<script>
    tab({click:'#tab-resultado'});
    tab({click:'#tab-desembolso'});   

    $('#idcliente').select2({
      @include('app.select2_cliente')
    });
  
    $('#idubigeo').select2({
      @include('app.select2_ubigeo')
    });
  
    $("#check_gastoadministrativo").click(function() {
        $('#cont-gastoadministrativo').css('display','block');
        $('#cont-creditocalendario').css('display','block');
        $('#cont-load-creditocalendario').html('');
        var checked = $("#check_gastoadministrativo:checked").val();
        if(checked=='on'){
            $('#cont-gastoadministrativo').css('display','none');
            $('#cont-creditocalendario').css('display','none');
            creditoCalendario();
        }
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
  
   function calcular_vuelto_cuota(){
        var montorecibido = parseFloat($('#facturacion_montorecibido').val());
        var total_gastoadministrativo = parseFloat($('#total_gastoadministrativo').val());
        var vuelto = (montorecibido-total_gastoadministrativo).toFixed(2);
        $('#facturacion_vuelto').val(vuelto);
    }
</script>
@endsection   