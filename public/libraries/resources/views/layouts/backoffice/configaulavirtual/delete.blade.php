@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>ELIMINAR CURSO</span>
      <a class="btn btn-success" href="{{ url('backoffice/configaulavirtual/'.$curso->id.'/edit?view=indexcurso') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div class="profile-edit-container">
    <form class="js-validation-signin px-30" 
                                          action="javascript:;" 
                                          onsubmit="callback({
                                            route: 'backoffice/configaulavirtual/{{ $curso->id }}',
                                            method: 'DELETE',
                                            data:{
                                                'view' : 'cursoeliminar',
                                                'idcurso' : '{{ $curso->id }}'
                                            }
                                        },
                                        function(resultado){
                                            if (resultado.resultado == 'CORRECTO') {
                                                location.href = '{{ url('backoffice/configaulavirtual') }}';                                                                            
                                            }                                                                                                                    
                                        },this)" enctype="multipart/form-data">
      <div class="custom-form">
        <p>¿Esta Seguro de Eliminar el Curso <b>"{{ $curso->nombre }}'</b>?</p>
      </div>
      <div class="profile-edit-container">
          <div class="custom-form">
              <button type="submit" class="btn  big-btn  color-bg flat-btn">Eliminar</button>
          </div>
      </div>
    </form> 
</div>

@endsection