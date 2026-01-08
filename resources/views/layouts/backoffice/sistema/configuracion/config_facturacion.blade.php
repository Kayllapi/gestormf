@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Configuración de Facturación',
    'botones'=>[
        'atras:/'.$tienda->id.'/configuracion/:Ir Atras'
    ]
])

<form action="javascript:;" 
      onsubmit="callback({
        route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
        method: 'PUT',
        data:   {
            view: 'config_facturacion'
        }
    },
    function(resultado){
        location.reload();                                                                        
    },this)">
  <div class="tabs-container" id="tab-modulo">
      <ul class="tabs-menu">
          <li class="current"><a href="#tab-modulo-1">Factura y Boleta</a></li>
          <li><a href="#tab-modulo-2">Nota de Crédito</a></li>
          <li><a href="#tab-modulo-3">Nota de Débito</a></li>
          <li><a href="#tab-modulo-4">Resúmen Diario</a></li>
          <li><a href="#tab-modulo-5">Comunicación de Baja</a></li>
          <li><a href="#tab-modulo-6">Guia de Remisión</a></li>
      </ul>
      <div class="tab">
          <div id="tab-modulo-1" class="tab-content" style="display: block;">
              <div class="row">
                <div class="col-sm-6">
                      <label>Cliente por Defecto</label>
                      <select id="facturacion_clientepordefecto">
                          @if(configuracion($tienda->id,'facturacion_clientepordefecto')['resultado']=='CORRECTO')
                          <?php $users = DB::table('users')->whereId(configuracion($tienda->id,'facturacion_clientepordefecto')['valor'])->first(); ?>
                          @if($users!='')
                          <option value="{{ configuracion($tienda->id,'facturacion_clientepordefecto')['valor'] }}">
                            @if($users->idtipopersona==1 or $users->idtipopersona==3)
                            {{ $users->identificacion }} - {{ $users->apellidos }}, {{ $users->nombre }}
                            @elseif($users->idtipopersona==2)
                            {{ $users->identificacion }} - {{ $users->nombre }}
                            @endif
                          </option>
                          @else
                          <option></option>
                          @endif
                          @else
                          <option></option>
                          @endif
                      </select>
                      <label>Empresa por Defecto</label>
                      <select id="facturacion_empresapordefecto">
                          <option></option>
                          @foreach($agencias as $value)
                          <option value="{{ $value->id }}">{{ $value->ruc }} - {{ $value->nombrecomercial }}</option>
                          @endforeach
                      </select>
                      <label>Comprobante por Defecto</label>
                      <select id="facturacion_comprobantepordefecto">
                          <option></option>
                          @foreach($comprobantes as $value)
                          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                          @endforeach
                      </select>
                      <label>Moneda por defecto</label>
                      <select id="facturacion_monedapordefecto">
                          <option value=""></option>
                          @foreach($monedas as $value)
                          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                          @endforeach
                      </select>
                </div>
                <div class="col-sm-6">
                      <label>IGV (%)</label>
                      <input type="number" value="{{ configuracion($tienda->id,'facturacion_igv')['valor'] }}" id="facturacion_igv" step="0.01" min="0">
                      <label>Ancho de Ticket (centimetro)</label>
                      <input type="number" value="{{ configuracion($tienda->id,'facturacion_anchoticket')['valor'] }}" id="facturacion_anchoticket" step="0.01" min="0">
                      <label>Tipo de Letra de Ticket</label>
                      <select id="facturacion_tipoletra">
                         <option value=""></option>
                         <option value="Courier">Courier</option>
                         <option value="Arial">Arial</option>
                         <option value="serif">Serif</option>
                         <option value="sans-serif">Sans Serif</option>
                         <option value="monospace">Monospace</option>
                         <option value="Times New Roman">Times New Roman (serif)</option>
                      </select>
                      <label>Estado de Facturación</label>
                      <select id="facturacion_estadofacturacion">
                          <option></option>
                          <option value="1">Habilitado</option>
                          <option value="2">Bloqueado</option>
                      </select>
                </div>
              </div>
              <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
          </div>
          <div id="tab-modulo-2" class="tab-content" style="display: none;">
          </div>
          <div id="tab-modulo-3" class="tab-content" style="display: none;">
          </div>
          <div id="tab-modulo-4" class="tab-content" style="display: none;">
          </div>
          <div id="tab-modulo-5" class="tab-content" style="display: none;">
          </div>
          <div id="tab-modulo-6" class="tab-content" style="display: none;">
          </div>
      </div>
  </div>
</form>
@endsection
@section('subscripts')
<script>
  tab({click:'#tab-modulo'});
</script>
<script>
    $("#facturacion_clientepordefecto").select2({
        @include('app.select2_cliente')
    });
  
    @if(configuracion($tienda->id,'facturacion_monedapordefecto')['resultado']=='CORRECTO')
        $("#facturacion_monedapordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val({{ configuracion($tienda->id,'facturacion_monedapordefecto')['valor'] }}).trigger("change");    
    @else
        $("#facturacion_monedapordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif
    @if(configuracion($tienda->id,'facturacion_tipoletra')['resultado']=='CORRECTO')
        $("#facturacion_tipoletra").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val('{{ configuracion($tienda->id,'facturacion_tipoletra')['valor'] }}').trigger("change");    
    @else
        $("#facturacion_tipoletra").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif
  
    @if(configuracion($tienda->id,'facturacion_empresapordefecto')['resultado']=='CORRECTO')
        $("#facturacion_empresapordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
            allowClear: true
        }).val({{ configuracion($tienda->id,'facturacion_empresapordefecto')['valor'] }}).trigger("change");    
    @else
        $("#facturacion_empresapordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
            allowClear: true
        });
    @endif

    @if(configuracion($tienda->id,'facturacion_comprobantepordefecto')['resultado']=='CORRECTO')
        $("#facturacion_comprobantepordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
            allowClear: true
        }).val({{ configuracion($tienda->id,'facturacion_comprobantepordefecto')['valor'] }}).trigger("change");   
    @else
        $("#facturacion_comprobantepordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
            allowClear: true
        });
    @endif
  
  @if(configuracion($tienda->id,'facturacion_estadofacturacion')['resultado']=='CORRECTO')
      $("#facturacion_estadofacturacion").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1,
          allowClear: true
      }).val({{ configuracion($tienda->id,'facturacion_estadofacturacion')['valor'] }}).trigger("change");    
  @else
      $("#facturacion_estadofacturacion").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1,
          allowClear: true
      });
  @endif
</script>
@endsection