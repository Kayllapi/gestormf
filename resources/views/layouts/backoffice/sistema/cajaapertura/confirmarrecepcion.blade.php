@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Recepcionar Apertura de Caja',
    'botones'=>[
        'atras:/'.$tienda->id.'/cajaapertura: Ir Atras'
    ]
])
<div id="carga-aperturacaja">
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
                @if($s_aperturacierre->config_prestamo_tipocierrecaja==2 or $s_aperturacierre->config_prestamo_tipocierrecaja==3)
                @if($s_aperturacierre->idtipocaja==2)
                @if($s_aperturacierre->config_sistema_moneda_usar==1)
                    <label>Monto total cobrado en Soles *</label>
                    <input type="text" id="montocobradoauxiliar">
                @elseif($s_aperturacierre->config_sistema_moneda_usar==2)
                    <label>Monto total cobrado en Dolares *</label>
                    <input type="text" id="montocobradoauxiliar_dolares">
                @elseif($s_aperturacierre->config_sistema_moneda_usar==3)
                    <div class="row">
                        <div class="col-md-6">
                            <label>Monto total cobrado en Soles *</label>
                            <input type="text" id="montocobradoauxiliar">
                        </div>
                        <div class="col-md-6">
                            <label>Monto total cobrado en Dolares *</label>
                            <input type="text" id="montocobradoauxiliar_dolares">
                        </div>
                    </div>
                @else
                    <label>Monto total cobrado en Soles *</label>
                    <input type="text" id="montocobradoauxiliar">
                @endif
                @endif
                @endif
             </div>
             <div class="col-md-6">
                <label>Persona responsable</label>
                <input type="text" value="{{ $s_aperturacierre->usersresponsableapellidos }}, {{ $s_aperturacierre->usersresponsablenombre }}" disabled>
                <label>Persona asignado</label>
                <input type="text" value="{{ $s_aperturacierre->usersrecepcionapellidos }}, {{ $s_aperturacierre->usersrecepcionnombre }}" disabled>
             </div>
          </div>
          <div class="row">
              <div class="col-md-6">
                  <form action="javascript:;" 
                        onsubmit="callback({
                            route: 'backoffice/tienda/sistema/{{ $tienda->id }}/cajaapertura/{{ $s_aperturacierre->id }}',
                            method: 'PUT',
                            carga: '#carga-aperturacaja',
                            data:{
                                view: 'confirmarrecepcion',
                                montocobradoauxiliar: $('#montocobradoauxiliar').val(),
                                montocobradoauxiliar_dolares: $('#montocobradoauxiliar_dolares').val()
                            }
                        },
                        function(resultado){
                            location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura') }}';                                                                            
                        },this)">
                        <button type="submit" class="btn mx-btn-post"><i class="fa fa-check"></i> Recepcionar</button>
                  </form>   
              </div>
              <div class="col-md-6">
                  <form action="javascript:;" 
                        onsubmit="callback({
                            route: 'backoffice/tienda/sistema/{{ $tienda->id }}/cajaapertura/{{ $s_aperturacierre->id }}',
                            method: 'PUT',
                            carga: '#carga-aperturacaja',
                            data:{
                                view: 'anularenvio'
                            }
                        },
                        function(resultado){
                            location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura') }}';                                                                            
                        },this)">
                        <button type="submit" class="btn mx-btn-post"><i class="fa fa-ban"></i> Rechazar</button>
                  </form>  
              </div>
          </div>    
</div>                      
@endsection

@section('subscripts')
<script>
$("#idcaja").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$s_aperturacierre->s_idcaja}}).trigger("change");

$("#idusers").select2({
    placeholder: "---  Seleccionar ---"
}).val({{$s_aperturacierre->idusersrecepcion}}).trigger("change");

$("#idusersresponsable").select2({
    placeholder: "-- Seleccionar --"
}).val({{$s_aperturacierre->idusersresponsable}}).trigger("change");

</script>
@endsection