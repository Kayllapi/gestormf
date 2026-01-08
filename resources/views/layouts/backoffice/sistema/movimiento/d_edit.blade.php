@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Movimiento</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimiento') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/movimiento/{{ $s_movimiento->id }}',
        method: 'PUT',
        data:{
            view: 'editar'
        }
    },
    function(resultado){
            location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimiento') }}';                                                            
    },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
             <div class="col-md-6">
                <label>Tipo *</label>
                <select id="idconceptomovimiento">
                    <option></option>
                    @foreach($s_conceptomovimientos as $value)
                    <option value="{{ $value->id }}">{{ $value->tipo }} - {{ $value->nombre }}</option>
                    @endforeach
                </select>
                <label>Monto *</label>
                <input type="number" value="{{ $s_movimiento->monto }}" id="monto" placeholder="0.00" step="0.01" min="0"/>
                <label>Responsable *</label>
                <select id="idusuarioresponsable">
                    <option></option>
                    @foreach($usuarios as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
             </div>
             <div class="col-md-6">
                <label>Concepto *</label>
                <textarea id="concepto">{{ $s_movimiento->concepto }}</textarea>
             </div>

           </div>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</button>
        </div>
    </div> 
</form>                             
@endsection
@section('subscripts')
<script>
$("#idusuarioresponsable").select2({
    placeholder: "--  Seleccionar --"
}).val({{ $s_movimiento->s_idusuarioresponsable }}).trigger("change");

$("#idconceptomovimiento").select2({
    placeholder: "--  Seleccionar --"
}).val({{ $s_movimiento->s_idconceptomovimiento }}).trigger("change");
</script>
@endsection