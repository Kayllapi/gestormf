@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Configuración de Comida',
    'botones'=>[
        'atras:/'.$tienda->id.'/configuracion/:Ir Atras'
    ]
])
<form action="javascript:;" 
      onsubmit="callback({
        route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
        method: 'PUT',
        data:   {
            view: 'config_comida'
        }
    },
    function(resultado){
        location.reload();                                                                        
    },this)">
    <div class="tabs-container" id="tab-menuconfiguracioncomida">
        <ul class="tabs-menu">
            <li class="current"><a href="#tab-menuconfiguracioncomida-0">Pedidos</a></li>
        </ul>
        <div class="tab">
            <div id="tab-menuconfiguracioncomida-0" class="tab-content" style="display: block;">
                  <div class="row">
                      <div class="col-sm-6">
                          <label>Cantidad de Mesas</label>
                          <input type="number" value="{{configuracion($tienda->id,'comida_cantidadmesa')['valor']}}" min="1" id="comida_cantidadmesa">
                      </div>
                  </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>
@endsection
@section('subscripts')
<script>
  tab({click:'#tab-menuconfiguracioncomida'});
</script>
@endsection