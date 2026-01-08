@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Movimiento</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimiento') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
@if($caja['resultado']=='ABIERTO')
<form action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/movimiento',
        method: 'POST',
        data:{
            view: 'registrar',
            idtienda: '{{ $tienda->id }}'
        }
    },
    function(resultado){
           location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimiento') }}'; 
    },this)"> 
          <div class="row">
             <div class="col-md-6">
                <label>Tipo *</label>
                <select id="idconceptomovimiento">
                    <option></option>
                    @foreach($s_conceptomovimientos as $value)
                    <option value="{{ $value->id }}">{{ $value->tipo }} - {{ $value->nombre }}</option>
                    @endforeach
                </select>
                @if($caja['apertura']->config_sistema_moneda_usar==1)
                <label>Monto en Soles *</label>
                <input type="number" id="monto" placeholder="0.00" step="0.01" min="0"/>
                @elseif($caja['apertura']->config_sistema_moneda_usar==2)
                <label>Monto en Dolares *</label>
                <input type="number" id="monto" placeholder="0.00" step="0.01" min="0"/>
                @elseif($caja['apertura']->config_sistema_moneda_usar==3)
                <div class="row">
                    <div class="col-md-6">
                        <label>Moneda</label>
                        <select id="idmoneda">
                            <option></option>
                            @foreach($s_monedas as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Monto *</label>
                        <input type="number" id="monto" placeholder="0.00" step="0.01" min="0"/>
                    </div>
                </div>
                @endif
             </div>
             <div class="col-md-6">
                <label>Concepto *</label>
                <textarea id="concepto" onkeyup="texto_mayucula(this)"></textarea>
             </div>

           </div>
          <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form> 
@else
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¡Para realizar Movimientos, debe aperturar la Caja!
    </div>
@endif                            
@endsection
@section('subscripts')
@if($caja['resultado']=='ABIERTO'){
<script>
$("#idconceptomovimiento").select2({
    placeholder: "--  Seleccionar --"
});
@if($caja['apertura']->config_sistema_moneda_usar==3)
@if($caja['apertura']->config_sistema_monedapordefecto!='')
    $("#idmoneda").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    }).val({{ $caja['apertura']->config_sistema_monedapordefecto }}).trigger("change");
@else
    $("#idmoneda").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    }).val(1).trigger("change");
@endif
@endif
</script>
@endif
@endsection