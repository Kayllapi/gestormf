@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Configuración de Venta (Comida)',
    'botones'=>[
        'atras:/'.$tienda->id.'/configuracion/:Ir Atras'
    ]
])
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/{{ $configuracion!=''?$configuracion['id']:'0' }}',
        method: 'PUT',
        data:{
            view: 'config_ventacomida'
        }
    },
    function(resultado){
        location.reload();                                                                           
    },this)">
        <div class="row">
          <div class="col-sm-6">
                <label>Estado *</label>
                <select id="ventacomida_estado">
                    <option value="1">Habilitado</option>
                    <option value="2">Desabilitado</option>
                </select>
                <div id="cont-ventacomida_estado">
                <label>Número de Mesas</label>
                <input type="number" value="{{ $configuracion!=''?$configuracion['mesacomida_cantidadmesa']:'0' }}" id="ventacomida_numeromesa">
                </div>
          </div>
          <div class="col-sm-6">
          </div>
        </div>
    <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>  
@endsection
@section('subscripts')
<script>
$("#ventacomida_estado").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-ventacomida_estado').css('display','block');
    if(e.currentTarget.value == 2) {
        $('#cont-ventacomida_estado').css('display','none');
    }
}).val({{ $configuracion!=''?($configuracion['mesacomida_idambiente']!=0?$configuracion['mesacomida_idambiente']:2):'0' }}).trigger("change");
</script>
@endsection