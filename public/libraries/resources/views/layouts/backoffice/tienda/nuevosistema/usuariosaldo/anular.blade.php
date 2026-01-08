@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Anular Saldo de Usuario',
    'botones'=>[
        'atras:/usuariosaldo: Ir Atras'
    ]
])
<form action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/usuariosaldo/{{$usuariosaldo->id}}',
        method: 'PUT',
        data:{
            view: 'anular'
        }
    },
    function(resultado){
           location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/usuariosaldo') }}';                                                            
    },this)">
          <div class="row">
             <div class="col-md-12">
                <div class="col-md-6">
                     <label>Cliente *</label>
                     <input type="text" value="{{$usuariosaldo->usuariosaldoruc}}-{{$usuariosaldo->usuariosaldoapellidos}},{{$usuariosaldo->usuariosaldonombre}}" disabled/>
                 </div>
                 <div class="col-md-3">
                      <label>Monto *</label>
                      <input type="text" value="{{$usuariosaldo->motivo}}" disabled/>
                 </div>
                 <div class="col-md-3">
                      <label>Motivo *</label>
                     <input type="text" value="{{$usuariosaldo->monto}}" disabled/> 
                 </div>
             </div>
           </div>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de Anular?
    </div>
    <button type="submit" class="btn mx-btn-post">Anular</button>
</form>  
@endsection