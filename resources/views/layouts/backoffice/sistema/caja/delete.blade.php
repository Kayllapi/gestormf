@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Eliminar Caja',
    'botones'=>[
        'atras:/'.$tienda->id.'/caja: Ir Atras'
    ]
])
<form action="javascript:;" 
      onsubmit="callback({
          route: 'backoffice/tienda/sistema/{{ $tienda->id }}/caja/{{ $box->id }}',
          method: 'DELETE',
          data:{
              view: 'eliminar'
          }
      },
      function(resultado){
          location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/caja') }}';                                                                            
      },this)">
      <div class="row">
         <div class="col-md-6">
            <label>Nombre</label>
            <input type="text" id="nombre" value="{{$box->nombre}}" disabled/>
         </div>
      </div>
      <div class="mensaje-danger">
        <i class="fa fa-warning"></i> ¿Esta seguro Eliminar?
      </div>
      <button type="submit" class="btn mx-btn-post"><i class="fa fa-trash"></i> Eliminar</button>
</form>                             
@endsection