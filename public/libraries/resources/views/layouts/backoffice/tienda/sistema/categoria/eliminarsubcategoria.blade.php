@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>{{$s_categoria_1->nombre}} / Eliminar</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/categoria/'.$s_categoria_1->id.'/edit?view=indexsubcategoria') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/categoria/{{ $s_categoria->id }}',
        method: 'DELETE',
        data:{
            view: 'eliminarsubcategoria'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/categoria/'.$s_categoria_1->id.'/edit?view=indexsubcategoria') }}';                                                                           
    },this)">
    <div class="profile-edit-container">
      <div class="custom-form">
        <div class="row">
          <div class="col-sm-6">
                <label>Nombre *</label>
                <input type="text" id="nombre" value="{{$s_categoria->nombre}}" disabled/>
                <label>Imagen</label>              
                <div class="fuzone" id="cont-fileupload" style="height:120px">
                    <div id="resultado-fileupload"></div>
                </div>
          </div>
        </div>
      </div>
    </div>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de eliminar?</b>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn btn-danger">Eliminar</button>
        </div>
    </div> 
</form>                             
@endsection