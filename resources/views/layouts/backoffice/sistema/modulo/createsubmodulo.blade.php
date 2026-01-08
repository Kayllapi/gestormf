@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>CREAR SUB MÓDULO</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/modulo') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/modulo',
        method: 'POST',
        data:{
            view: 'registrar',
            idtienda: '{{ $tienda->id }}'
        }
    },
    function(resultado){
       location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/modulo') }}';                                                                            
    },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-sm-6">
              <div class="row">
               <div class="col-md-12">
                  <label>Módulo *</label>
                  <input type="text" value="{{$s_modulo->nombre}}" disabled/>
                  <input type="hidden" id="idmodulo" value="{{$s_modulo->id}}" />
               </div>
               <div class="col-md-12">
                  <label>Orden *</label>
                  <input type="text" id="orden"/>
               </div>
                <div class="col-md-12">
                  <label>Nombre *</label>
                  <input type="text" id="nombre"/>
               </div>
               <div class="col-md-12">
                  <label>Icono *</label>
                  <input type="text" id="icono"/>
               </div>
               <div class="col-md-12">
                  <label>Ruta *</label>
                  <input type="text" id="ruta"/>
               </div>
             </div>
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
<script>

</script>
@endsection