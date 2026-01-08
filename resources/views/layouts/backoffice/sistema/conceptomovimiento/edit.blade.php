@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>EDITAR CONCEPTO MOVIMIENTO</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/conceptomovimiento') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/conceptomovimiento/{{ $movementConcept->id }}',
        method: 'PUT',
        data:{
            view: 'editar'
        }
    },
    function(resultado){
            location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/conceptomovimiento') }}';                                                            
    },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
             <div class="col-md-6">
               
                <label>Nombre Concepto Movimiento *<i class="fa fa-user"></i></label>
                <input type="text" id="nombre" value="{{$movementConcept->nombre}}" />
                <label>Tipo *</label>
                <select id="tipo">
                  @foreach($movementType as $value)
                    <option value="{{$value->id}}" <?=$value->id == $movementConcept->s_idtipomovimiento ? 'selected' : '' ?> > {{ $value->nombre }} </option>
                  @endforeach
                </select>
               
             </div>
           </div>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios <i class="fa fa-angle-right"></i></button>
        </div>
    </div> 
</form>                             
@endsection
@section('subscripts')
<style>
</style>
<script>
$('#tipo').niceSelect();
</script>
@endsection