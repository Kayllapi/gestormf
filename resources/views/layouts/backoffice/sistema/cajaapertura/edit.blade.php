@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Confirmar Apertura de Caja',
    'botones'=>[
        'atras:/'.$tienda->id.'/cajaapertura: Ir Atras'
    ]
])
<form action="javascript:;" 
      onsubmit="callback({
          route: 'backoffice/tienda/sistema/{{ $tienda->id }}/cajaapertura/{{ $s_aperturacierre->id }}',
          method: 'PUT',
          data:{
              view: 'editar'
          }
      },
      function(resultado){
              location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura') }}';                                                            
      },this)">
          <div class="row">
             <div class="col-md-6">
                <label>Caja</label>
                <input type="text" value="{{ $s_aperturacierre->cajanombre }}" disabled>
                <?php 
                $efectivocajasoles = efectivocaja($tienda->id,$s_aperturacierre->s_idcaja,1);
                $efectivocajadolares = efectivocaja($tienda->id,$s_aperturacierre->s_idcaja,2);
                ?>
                @if(configuracion($tienda->id,'sistema_moneda_usar')['valor']==1)
                @if(configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==2 or configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==3)
                        <label>Saldo anterior en Soles</label>
                        <input type="text" value="{{$efectivocajasoles['total']}}" disabled>
                @endif
                        <label>Monto a asignar en Soles</label>
                        <input type="number" value="{{ $s_aperturacierre->montoasignar }}" id="montoasignar" step="0.01" min="0"/>
                @elseif(configuracion($tienda->id,'sistema_moneda_usar')['valor']==2)
                @if(configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==2 or configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==3)
                        <label>Saldo anterior en Dolares</label>
                        <input type="text" value="{{$efectivocajadolares['total']}}" disabled>
                @endif
                        <label>Monto a asignar en Dolares</label>
                        <input type="number" value="{{ $s_aperturacierre->montoasignar_dolares }}" id="montoasignar_dolares" step="0.01" min="0"/>
                @elseif(configuracion($tienda->id,'sistema_moneda_usar')['valor']==3)
                @if(configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==2 or configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==3)
                <div class="row">
                    <div class="col-md-6">
                        <label>Saldo anterior en Soles</label>
                        <input type="text" value="{{$efectivocajasoles['total']}}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label>Saldo anterior en Dolares</label>
                        <input type="text" value="{{$efectivocajadolares['total']}}" disabled>
                    </div>
                </div>
                @endif
                <div class="row">
                    <div class="col-md-6">
                        <label>Monto a asignar en Soles</label>
                        <input type="number" value="{{ $s_aperturacierre->montoasignar }}" id="montoasignar" step="0.01" min="0"/>
                    </div>
                    <div class="col-md-6">
                        <label>Monto a asignar en Dolares</label>
                        <input type="number" value="{{ $s_aperturacierre->montoasignar_dolares }}" id="montoasignar_dolares" step="0.01" min="0"/>
                    </div>
                </div>
                @else
                @if(configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==2 or configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==3)
                        <label>Saldo anterior en Soles</label>
                        <input type="text" value="{{$efectivocajasoles['total']}}" disabled>
                @endif
                        <label>Monto a asignar en Soles</label>
                        <input type="number" value="{{ $s_aperturacierre->montoasignar }}" id="montoasignar" step="0.01" min="0"/>
                @endif
             </div>
             <div class="col-md-6">
                <label>Persona responsable</label>
                <input type="text" value="{{ $s_aperturacierre->usersresponsableapellidos }}, {{ $s_aperturacierre->usersresponsablenombre }}" disabled>
                <label>Persona a asignar</label>
                <input type="text" value="{{ $s_aperturacierre->usersrecepcionapellidos }}, {{ $s_aperturacierre->usersrecepcionnombre }}" disabled>
             </div>
           </div>
          <button type="submit" class="btn mx-btn-post"><i class="fa fa-check"></i> Confirmar</button>
</form>                             
@endsection