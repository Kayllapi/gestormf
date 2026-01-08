@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Eliminar Tienda</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div class="profile-edit-container">
    <div class="custom-form">
        <form action="javascript:;" 
              onsubmit="callback({
                  route: 'backoffice/tienda/{{ $tienda->id }}',
                  method: 'DELETE',
                  data: {
                      view : 'deletetienda'      
                  }
              },
              function(resultado){
                  location.href = '{{ url('backoffice/tienda') }}';                                                                            
              },this)">
              <div class="row">
                  <div class="col-md-4">
                  </div>
                  <div class="col-md-4">
                    <label style="text-align: center;">Escribe "ELIMINAR" *</label>
                    <input type="text" id="validardato" style="text-align: center;"/>
                  </div>
              </div>
              <div class="mensaje-danger">
                ¿Esta Seguro de Eliminar la Tienda <b>"{{ $tienda->nombre }}"</b>?
              </div>
              <button type="submit" class="btn color-bg flat-btn" style="width:100%;">Eliminar</button>
        </form> 
    </div>
</div>
@endsection