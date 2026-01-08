@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Registrar Caja',
    'botones'=>[
        'atras:/'.$tienda->id.'/caja: Ir Atras'
    ]
])
<form action="javascript:;" 
      onsubmit="callback({
          route: 'backoffice/tienda/sistema/{{ $tienda->id }}/caja',
          method: 'POST',
          data:{
              view: 'registrar',
              idtienda: '{{ $tienda->id }}'
          }
      },
      function(resultado){
             location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/caja') }}';                                                             
      },this)"> 
      <div class="row">
         <div class="col-md-6">
            <label>Nombre *</label>
            <input type="text" id="nombre" onkeyup="texto_mayucula(this)"/>
         </div>
       </div>
       <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>                             
@endsection