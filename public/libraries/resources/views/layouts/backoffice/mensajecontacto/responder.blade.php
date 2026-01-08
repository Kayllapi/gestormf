@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 

<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>RESPONDER AL MENSAJE</span>
      <a class="btn btn-warning" href="{{ url('backoffice/usuario/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>

  <div class="container">
      <div class="row">
          <div class="col-md-4">
              <div id="message"></div>
              <form  class="custom-form" action="javascript:;">
                  <fieldset>
                      <label>De:</label>
                      <label><i class="fa fa-envelope"></i></label>
                      <input type="text" name="emailEmisor" id="nombre" value=" {{Auth::user()->email}} "/>
                      <label>Para:</label>
                      <div class="clearfix"></div>
                      <label><i class="fa fa-envelope"></i></label>
                      <input type="text" name="emailReceptor" id="nombre" value=" {{$mensajecontacto->email}} "/>
                      <textarea name="comments"  id="mensaje" cols="40" rows="3" placeholder="Tu mensaje:"></textarea>
                  </fieldset>
                  <button class="btn  big-btn  color-bg flat-btn" id="submit">Enviar<i class="fa fa-angle-right"></i></button>
              </form>
          </div>
      </div>
  </div>

@endsection