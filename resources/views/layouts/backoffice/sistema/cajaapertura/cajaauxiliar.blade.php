@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Apertura de Caja Auxiliar',
    'botones'=>[
        'atras:/'.$tienda->id.'/cajaapertura: Ir Atras'
    ]
])
@if(Auth::user()->idtienda==0)
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¡Con el usuario Master no puede aperturar caja, ingrese con un usuario de esta tienda!
    </div>
@else
<form action="javascript:;" 
      onsubmit="callback({
          route: 'backoffice/tienda/sistema/{{ $tienda->id }}/cajaapertura',
          method: 'POST',
          data:{
              view: 'registrar_auxiliar'
          }
      },
      function(resultado){
             location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura') }}';                                                             
      },this)"> 
          <div class="row">
             <div class="col-md-6">
                <label>Caja</label>
                <input type="text" value="{{$s_aperturacierre->cajanombre}}" disabled/>
                @if($s_aperturacierre->config_sistema_moneda_usar==1)
                    <label>Monto a asignar en Soles *</label>
                    <input type="number" id="montoasignar" step="0.01" min="0"/>
                @elseif($s_aperturacierre->config_sistema_moneda_usar==2)
                    <label>Monto a asignar en Dolares *</label>
                    <input type="number" id="montoasignar_dolares" step="0.01" min="0"/>
                @elseif($s_aperturacierre->config_sistema_moneda_usar==3)
                    <div class="row">
                        <div class="col-md-6">
                            <label>Monto a asignar en Soles *</label>
                            <input type="number" id="montoasignar" step="0.01" min="0"/>
                        </div>
                        <div class="col-md-6">
                            <label>Monto a asignar en Dolares *</label>
                            <input type="number" id="montoasignar_dolares" step="0.01" min="0"/>
                        </div>
                    </div>
                @else
                    <label>Monto a asignar en Soles *</label>
                    <input type="number" id="montoasignar" step="0.01" min="0"/>
                @endif
             </div>
             <div class="col-md-6">
                <label>Persona responsable</label>
                <input type="text" value="{{ Auth::user()->apellidos }}, {{ Auth::user()->nombre }}" disabled>
                <label>Persona a asignar *</label>
                <select id="idusers">
                    <option></option>
                </select>
             </div>
           </div>
          <button type="submit" class="btn mx-btn-post">Aperturar Caja</button>
</form>   
@endif                          
@endsection
@section('subscripts')
<script>
$("#idusers").select2({
    @include('app.select2_acceso')
});
</script>
@endsection