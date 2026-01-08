@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Eliminar Acceso',
    'botones'=>[
        'atras:/'.$tienda->id.'/usuarioacceso: Ir Atras'
    ]
])
<form action="javascript:;"
      onsubmit="callback({
                              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/usuarioacceso/{{ $usuario->id }}',
                              method: 'DELETE',
                              data: {
                                  view : 'eliminar'
                              }
                          },
                          function(resultado){
                              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/usuarioacceso') }}';                                        
                          },this)">
        <div class="row">
            <div class="col-md-6">
                <label>Persona</label>
                <input type="text" value="{{$usuario->idtipopersona==1?$usuario->apellidos.', '.$usuario->nombre:$usuario->nombre}}" disabled>
                <label>Usuario</label>
                <?php 
                    $lusuario = explode('@',$usuario->usuario); 
                    $valusuario = $usuario->usuario;
                    if($lusuario>1){
                        $valusuario = $lusuario[0];
                    }
                ?>
                <input type="text" value="{{ $valusuario }}" id="usuario" disabled>
            </div>
            <div class="col-md-6">
                <label>Cargo</label>
                <input type="text" id="cargo" value="{{ $usuario->cargo }}" disabled> 
                <label>Estado</label>
                <select id="idestadousuario" disabled>
                    <option></option>
                    <option value="1">Activado</option>
                    <option value="2">Desactivado</option>
                </select>
            </div>
        </div>
        <div class="mensaje-warning">
          <i class="fa fa-warning"></i> ¿Esta seguro de Eliminar?
        </div>
        <button type="submit" class="btn mx-btn-post">Eliminar</button>
</form>
@endsection
@section('subscripts')
<script>
  
    $("#idusuario").select2({
        placeholder: "---  Seleccionar ---",
        minimumResultsForSearch: -1
    });
  
    $("#idestadousuario").select2({
        placeholder: "---  Seleccionar ---",
        minimumResultsForSearch: -1
    }).val({{$usuario->idestadousuario}}).trigger("change");
  
</script>
@endsection