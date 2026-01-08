@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<?php $page = (isset($_GET['page'])?'page='.$_GET['page']:'') ?>
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>ELIMINAR USUARIO</span>
      <a class="btn btn-success" href="{{ url('backoffice/usuario?'.$page) }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div class="profile-edit-container">
    <form class="js-validation-signin px-30" 
          action="javascript:;" 
          onsubmit="callback({
              route: 'backoffice/usuario/{{ $usuario->id }}',
              method: 'DELETE'
          },
          function(resultado){
              if (resultado.resultado == 'CORRECTO') {
                  location.href = '{{ url('backoffice/usuario?'.$page) }}';                                                                            
              }                                                                                                                    
          },this)" enctype="multipart/form-data">
      <input type="hidden" value="deleteusuario" id="view"/>
      <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
              <div class="col-md-6">
                  <label>DNI</label>
                  <input type="text" id="dni" value="{{ $usuario->identificacion }}" disabled/>
                  <label>Nombre</label>
                  <input type="text" id="nombre" value="{{ $usuario->nombre }}" disabled/>
                  <label>Apellidos</label>
                  <input type="text" id="apellidos" value="{{ $usuario->apellidos }}" disabled/>
              </div>
              <div class="col-md-6">
                  <label>Número de Teléfono</label>
                  <input type="text" id="numerotelefono" value="{{ $usuario->numerotelefono }}" disabled/>
                  <label>Correo Electrónico (Usuario)</label>
                  <input type="text" id="email" value="{{ $usuario->email }}" disabled/>
              </div>
          </div>
        </div>
    </div>
      <?php
      $count_tienda = DB::table('tienda')->where('idusers',$usuario->id)->count();
      $red = DB::table('consumidor_red')->where('idusershijo',$usuario->id)->first();
      $count_planadquirido = 0;
      if($red!=''){
          $count_planadquirido = DB::table('consumidor_planadquirido')->where('idred',$red->id)->count();
      }
      $count_red = DB::table('consumidor_red')->where('idusershijo',$usuario->id)->count();
      $count_reparticion =  DB::table('consumidor_reparticion')->where('idusersda',$usuario->id)->count();
      ?>
      @if($count_tienda>0 || $count_planadquirido>0 || $count_red>0 || $count_reparticion>0)
      <div class="mensaje-danger">
          <b>Para eliminar este Usuario, es necesario limpiar toda las relaciones:</b><br>
          @if($count_red>0)
          El Usuario aun esta el Red de Consumidores.<br>
          @endif
          @if($count_reparticion>0)
          El Usuario aun tiene Bono Repartidos<br>
          @endif
          @if($count_planadquirido>0)
          El Usuario aun tiene un Plan de Inversión<br>
          @endif
          @if($count_tienda>0)
          El Usuario aun tiene Tiendas.<br>
          @endif
      </div>
      @else
      <div class="mensaje-warning">
        <i class="fa fa-warning"></i> ¿Esta seguro de Eliminar el Usuario?
      </div>
      <div class="profile-edit-container">
          <div class="custom-form">
              <button type="submit" class="btn  big-btn  color-bg flat-btn btn-danger" style="width:100%;"><i class="fa fa-trash"></i> Eliminar</button>
          </div>
      </div>
      @endif
    </form> 
</div>
@endsection