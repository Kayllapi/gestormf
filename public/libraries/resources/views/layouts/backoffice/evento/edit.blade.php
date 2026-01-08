@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<style>
  .custom-form textarea, .custom-form input[type="text"], .custom-form input[type=email], .custom-form input[type=password], .custom-form input[type=button], .custom-form input[type=date], .custom-form input[type=time] {
      float: left;
      border: 1px solid #eee;
      background: #f9f9f9;
      width: 100%;
      padding: 21px 20px 15px 21px;
      border-radius: 6px;
      color: #666;
      font-size: 13px;
      -webkit-appearance: none;
  }
</style>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/evento/{{ $evento->id }}',
        method: 'PUT'
    },
    function(resultado){
      location.href = '{{ url('backoffice/evento') }}';                                                                            
    },this)" enctype="multipart/form-data">
    <input type="hidden" value="editar" id="view"/>
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="profile-edit-header fl-wrap">
            <h4>Editar de Asistencia</h4>
        </div>
        <div class="custom-form">
          <div class="row">
              <div class="col-md-6">
                <div class="row">
                  <div id="div-ente-natural" style="display: block;">
                    <div class="col-md-12">
                      <label>Título * </label>
                      <input type="text" id="nombre" value="{{$evento->nombre}}"/>
                    </div>
                    <div class="col-md-12">
                      <label>Fecha * </label>
                      <input type="date" id="fecha" value="{{$evento->fecha}}"/>
                    </div>
                    <div class="col-md-12">
                      <label>Hora * </label>
                      <input type="time" id="hora" value="{{$evento->hora}}"/>
                    </div>
                    <div class="col-md-12">
                      <label>Descripción * </label>
                      <textarea id="descripcion" cols="30" rows="10">{{$evento->descripcion}}</textarea>
                    </div>
                    <div class="col-md-12">
                      <br>
                      <label>PDF * </label>
                      <input type="file" id="pdf"/><br>
                      <a target="_blank" href="{{ url('public/backoffice/evento/'.$evento->pdf) }}"><u>Ver PDF anterior</u></a>
                    </div>
                  </div>                 
                </div>
              </div>
            </div>
        </div>
    </div>
    <!-- profile-edit-container end-->  										
    <!-- profile-edit-container--> 
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Confirmar<i class="fa fa-angle-right"></i></button>
        </div>
    </div>
    <!-- profile-edit-container end-->  
</form>                             
@endsection
