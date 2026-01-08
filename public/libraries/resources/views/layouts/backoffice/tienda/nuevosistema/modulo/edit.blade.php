@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>EDITAR MÓDULO</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/modulo') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/modulo/{{ $s_modulo->id }}',
        method: 'PUT',
        data:{
            view: 'editar'
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
                  <label>Orden *</label>
                  <input type="text" id="orden" value="{{$s_modulo->orden}}"/>
               </div>
                <div class="col-md-12">
                  <label>Nombre *</label>
                  <input type="text" id="nombre" value="{{$s_modulo->nombre}}"/>
               </div>
               <div class="col-md-12">
                  <label>Icono *</label>
                  <input type="text" id="icono" value="{{$s_modulo->icono}}"/>
               </div>
               <div class="col-md-12">
                  <label>Ruta *</label>
                  <input type="text" id="ruta" value="{{$s_modulo->ruta}}"/>
               </div>
                <div class="col-md-12">
                  <label>Estado *</label>
                  <select class="form-control" id="idestado">
                    <option value="1" <?php echo $s_modulo->idestado==1 ? 'selected' : '' ?>>Activado</option>
                    <option value="2" <?php echo $s_modulo->idestado==2 ? 'selected' : '' ?>>Desactivado</option>
                  </select>
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
$('#idestado').niceSelect();
</script>
@endsection