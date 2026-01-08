@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>REGISTRAR MÓDULO</span>
      <a class="btn btn-success" href="{{ url('backoffice/configaulavirtual/'.$curso->id.'/edit?view=indexmodulo') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" action="javascript:;" 
                                         onsubmit="callback({
                                                    route: 'backoffice/configaulavirtual',
                                                    method: 'POST',
                                                    data:{
                                                      'view' : 'createmodulo',
                                                      'idcurso' : '{{ $curso->id }}'
                                                    }
                                                },
                                                function(resultado){
                                                  if (resultado.resultado == 'CORRECTO') {
                                                    location.href = '{{ url('backoffice/configaulavirtual/'.$curso->id.'/edit?view=indexmodulo') }}';                                                                            
                                                  }
                                                },this)">
  <div class="profile-edit-container">
    <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
              <div class="col-md-6">
                <label>Nombre *</label>
                <input type="text" id="nombre">
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