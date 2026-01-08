@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Detalle de Saldo de Usuario',
    'botones'=>[
        'atras:/usuariosaldo: Ir Atras'
    ]
])
      <div class="row">
        <div class="col-md-12">
           <div class="col-md-6">
                <label>Cliente</label>
                <input type="text" value="{{$usuariosaldo->usuariosaldoruc}}-{{$usuariosaldo->usuariosaldoapellidos}},{{$usuariosaldo->usuariosaldonombre}}" disabled/>
            </div>
            <div class="col-md-3">
                 <label>Monto</label>
                 <input type="text" value="{{$usuariosaldo->motivo}}" disabled/>
            </div>
            <div class="col-md-3">
                 <label>Motivo</label>
                <input type="text" value="{{$usuariosaldo->monto}}" disabled/> 
            </div>
        </div>
       </div>
@endsection