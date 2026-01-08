@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Anular Movimiento</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimiento') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
@if(Auth::user()->idtienda==0)
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¡Con el usuario Master no puede realizar movimientos, ingrese con un usuario de esta tienda!
    </div>
@else
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/movimiento/{{ $s_movimiento->id }}',
        method: 'PUT',
        data:{
            view: 'anular'
        }
    },
    function(resultado){
            location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimiento') }}';                                                            
    },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
             <div class="col-md-6">
                <label>Tipo</label>
                <select id="idconceptomovimiento" disabled>
                    <option></option>
                    @foreach($s_conceptomovimientos as $value)
                    <option value="{{ $value->id }}">{{ $value->tipo }} - {{ $value->nombre }}</option>
                    @endforeach
                </select>
                <div class="row">
                    <div class="col-md-6">
                        <label>Moneda</label>
                        <select id="idmoneda" disabled>
                            <option></option>
                            @foreach($s_monedas as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Monto</label>
                        <input type="number" value="{{ $s_movimiento->monto }}" id="monto" placeholder="0.00" disabled/>
                    </div>
                </div>
             </div>
             <div class="col-md-6">
                <label>Concepto (opcional)</label>
                <textarea id="concepto" disabled>{{ $s_movimiento->concepto }}</textarea>
             </div>
           </div>
        </div>
    </div>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¡Esta seguro de Anular el Movimiento!
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;">Anular</button>
        </div>
    </div> 
</form>       
@endif
@endsection
@section('subscripts')
<script>
$("#idconceptomovimiento").select2({
    placeholder: "--  Seleccionar --"
}).val({{ $s_movimiento->s_idconceptomovimiento }}).trigger("change");
$("#idmoneda").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{ $s_movimiento->s_idmoneda }}).trigger("change");
</script>
@endsection