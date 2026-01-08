@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Configuración de Inventario',
    'botones'=>[
        'atras:/'.$tienda->id.'/configuracion/:Ir Atras'
    ]
])

<form action="javascript:;" 
      onsubmit="callback({
        route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
        method: 'PUT',
        data:   {
            view: 'config_almacen'
        }
    },
    function(resultado){
        location.reload();                                                                        
    },this)">
  <div class="tabs-container" id="tab-modulo">
      <ul class="tabs-menu">
          <li class="current"><a href="#tab-modulo-1">Categorias</a></li>
          <li><a href="#tab-modulo-2">Marcas</a></li>
          <li><a href="#tab-modulo-3">Productos</a></li>
          <li><a href="#tab-modulo-4">Movimiento de Productos</a></li>
          <li><a href="#tab-modulo-5">Transferencia de Productos</a></li>
      </ul>
      <div class="tab">
          <div id="tab-modulo-1" class="tab-content" style="display: block;">
          </div>
          <div id="tab-modulo-2" class="tab-content" style="display: none;">
          </div>
          <div id="tab-modulo-3" class="tab-content" style="display: none;">
              <div class="row">
                <div class="col-sm-6">
                      <label>Tipo de Código de Producto</label>
                      <select id="sistema_tipocodigoproducto">
                          <option></option>
                          <option value="1">Código Único</option>
                          <option value="2">Código Multiple</option>
                      </select>
                      <label>Estado de Stock</label>
                      <select id="sistema_estadostock">
                          <option></option>
                          <option value="1">Habilitado</option>
                          <option value="2">Desabilitado</option>
                      </select>
                </div>
                <div class="col-sm-6">
                      <label>Estado de Descuentos</label>
                      <select id="sistema_estadodescuento">
                          <option></option>
                          <option value="1">Habilitado</option>
                          <option value="2">Desabilitado</option>
                      </select>
                      <label>Estado de Unidad Medida</label>
                      <select id="sistema_estadounidadmedida">
                          <option></option>
                          <option value="1">Habilitado</option>
                          <option value="2">Desabilitado</option>
                      </select>
                </div>
              </div>
          </div>
          <div id="tab-modulo-4" class="tab-content" style="display: none;">
          </div>
          <div id="tab-modulo-5" class="tab-content" style="display: none;">
          </div>
      </div>
  </div>
  <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>

@endsection
@section('subscripts')
<!-- tab -->
<script>
  tab({click:'#tab-modulo'});
</script>
<!-- Categorias -->
<script>
</script>
<!-- Marcas -->
<script>
</script>
<!-- Productos -->
<script>
    @if(configuracion($tienda->id,'sistema_tipocodigoproducto')['resultado']=='CORRECTO')
        $("#sistema_tipocodigoproducto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        }).val({{ configuracion($tienda->id,'sistema_tipocodigoproducto')['valor'] }}).trigger("change");   
    @else
        $("#sistema_tipocodigoproducto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        });
    @endif
    @if(configuracion($tienda->id,'sistema_estadostock')['resultado']=='CORRECTO')
        $("#sistema_estadostock").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        }).val({{ configuracion($tienda->id,'sistema_estadostock')['valor'] }}).trigger("change");   
    @else
        $("#sistema_estadostock").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        });
    @endif
    @if(configuracion($tienda->id,'sistema_estadodescuento')['resultado']=='CORRECTO')
        $("#sistema_estadodescuento").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        }).val({{ configuracion($tienda->id,'sistema_estadodescuento')['valor'] }}).trigger("change");   
    @else
        $("#sistema_estadodescuento").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        });
    @endif
    @if(configuracion($tienda->id,'sistema_estadounidadmedida')['resultado']=='CORRECTO')
        $("#sistema_estadounidadmedida").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        }).val({{ configuracion($tienda->id,'sistema_estadounidadmedida')['valor'] }}).trigger("change");   
    @else
        $("#sistema_estadounidadmedida").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1,
        });
    @endif
</script>
<!-- Movimiento de Productos -->
<script>
</script>
<!-- Transferencia de Productos -->
<script>
</script>
@endsection