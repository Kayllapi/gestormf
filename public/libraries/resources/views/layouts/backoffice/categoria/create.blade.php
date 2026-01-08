@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>REGISTRAR CATEGORIA</span>
      <a class="btn btn-success" href="{{ url('backoffice/categoria') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" action="javascript:;" 
                                         onsubmit="callback({
                                                    route: 'backoffice/categoria',
                                                    method: 'POST'
                                                },
                                                function(resultado){
                                                  if (resultado.resultado == 'CORRECTO') {
                                                    location.href = '{{ url('backoffice/categoria') }}';                                                                            
                                                  }
                                                },this)">
 
  <input type="hidden" value="create" id="view"/>
  <div class="profile-edit-container">
    <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
              <div class="col-md-6">
                <div class="row">
                  <div id="div-ente-natural" style="display: block;">
                    <div class="col-md-12">
                      <label>Nombre * <i class="fa fa-id-card"></i></label>
                      <input type="text" id="nombre"/>
                    </div>
                  </div>                 
                </div>
              </div>
            </div>
          </div>
      </div>
    <div class="custom-form">
        <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</button>
    </div>
  </div>
  
</form>
@endsection