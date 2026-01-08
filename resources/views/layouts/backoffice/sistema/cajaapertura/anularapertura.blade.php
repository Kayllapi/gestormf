@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Cancelar la Apertura de Caja',
    'botones'=>[
        'atras:/'.$tienda->id.'/cajaapertura: Ir Atras'
    ]
])
<form action="javascript:;" 
      onsubmit="callback({
          route: 'backoffice/tienda/sistema/{{ $tienda->id }}/cajaapertura/{{ $s_aperturacierre->id }}',
          method: 'PUT',
          data:{
              view: 'anularapertura'
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
                @if($s_aperturacierre->config_sistema_moneda_usar==1)
                    <label>Monto asignado en Soles</label>
                    <input type="number" value="{{ $s_aperturacierre->montoasignar }}" id="montoasignar" disabled/>
                @elseif($s_aperturacierre->config_sistema_moneda_usar==2)
                    <label>Monto asignado en Dolares</label>
                    <input type="number" value="{{ $s_aperturacierre->montoasignar_dolares }}" id="montoasignar_dolares" disabled/>
                @elseif($s_aperturacierre->config_sistema_moneda_usar==3)
                    <div class="row">
                        <div class="col-md-6">
                            <label>Monto asignado en Soles</label>
                            <input type="number" value="{{ $s_aperturacierre->montoasignar }}" id="montoasignar" disabled/>
                        </div>
                        <div class="col-md-6">
                            <label>Monto asignado en Dolares</label>
                            <input type="number" value="{{ $s_aperturacierre->montoasignar_dolares }}" id="montoasignar_dolares" disabled/>
                        </div>
                    </div>
                @else
                    <label>Monto asignado en Soles</label>
                    <input type="number" value="{{ $s_aperturacierre->montoasignar }}" id="montoasignar" disabled/>
                @endif
             </div>
             <div class="col-md-6">
                <label>Persona responsable</label>
                <input type="text" value="{{ $s_aperturacierre->usersresponsableapellidos }}, {{ $s_aperturacierre->usersresponsablenombre }}" disabled>
                <label>Persona asignado</label>
                <input type="text" value="{{ $s_aperturacierre->usersrecepcionapellidos }}, {{ $s_aperturacierre->usersrecepcionnombre }}" disabled>
             </div>
          </div>
          <div class="mensaje-warning">
            <i class="fa fa-warning"></i> ¿Esta seguro Cancelar?
          </div>
          <button type="submit" class="btn mx-btn-post"><i class="fa fa-ban"></i> Cancelar</button>
</form>                             
@endsection