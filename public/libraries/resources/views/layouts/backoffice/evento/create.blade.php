@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<form class="js-validation-signin px-30" action="javascript:;" 
                                         onsubmit="callback({
                                                    route: 'backoffice/evento',
                                                    method: 'POST'
                                                },
                                                function(resultado){
                                                  if (resultado.resultado == 'CORRECTO') {
                                                    location.href = '{{ url('backoffice/evento') }}';                                                                            
                                                  }
                                                },this)">
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
  <input type="hidden" value="create" id="view"/>
  <div class="profile-edit-container">
    <div class="profile-edit-container">
        <div class="profile-edit-header fl-wrap">
            <h4>Registrar Nuevo Evento</h4>
        </div>
        <div class="custom-form">
            <div class="row">
              <div class="col-md-6">
                <div class="row">
                  <div id="div-ente-natural" style="display: block;">
                    <div class="col-md-12">
                      <label>Título * </label>
                      <input type="text" id="nombre"/>
                    </div>
                    <div class="col-md-12">
                      <label>Fecha * </label>
                      <input type="date" id="fecha"/>
                    </div>
                    <div class="col-md-12">
                      <label>Hora * </label>
                      <input type="time" id="hora"/>
                    </div>
                    <div class="col-md-12">
                      <label>Descripción * </label>
                      <textarea id="descripcion" cols="30" rows="10"></textarea>
                    </div>
                    <div class="col-md-12">
                      <br>
                      <label>PDF * </label>
                      <input type="file" id="pdf"/>
                    </div>
                  </div>                 
                </div>
              </div>
            </div>
          </div>
      </div>
    <div class="custom-form">
        <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios<i class="fa fa-angle-right"></i></button>
    </div>
  </div>
  
</form>
@endsection