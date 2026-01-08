@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Apertura de Caja',
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
              view: 'registrar'
          }
      },
      function(resultado){
             location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura') }}';                                                             
      },this)"> 
          <div class="row">
             <div class="col-md-6">
                <label>Caja *</label>
                <select id="idcaja">
                    <option></option>
                    @foreach($s_cajas as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
                @if(configuracion($tienda->id,'sistema_moneda_usar')['valor']==1)
                @if(configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==2 or configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==3)
                        <label>Saldo anterior en Soles</label>
                        <input type="text" id="saldoanterior_soles" placeholder="0.00" disabled>
                @endif
                <label>Monto a asignar en Soles *</label>
                <input type="number" id="montoasignar" step="0.01" min="0"/>
                @elseif(configuracion($tienda->id,'sistema_moneda_usar')['valor']==2)
                @if(configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==2 or configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==3)
                        <label>Saldo anterior en Dolares</label>
                        <input type="text" id="saldoanterior_dolares" placeholder="0.00" disabled>
                @endif
                <label>Monto a asignar en Dolares *</label>
                <input type="number" id="montoasignar_dolares" step="0.01" min="0"/>
                @elseif(configuracion($tienda->id,'sistema_moneda_usar')['valor']==3)
                @if(configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==2 or configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==3)
                <div class="row">
                    <div class="col-md-6">
                        <label>Saldo anterior en Soles</label>
                        <input type="text" id="saldoanterior_soles" placeholder="0.00" disabled>
                    </div>
                    <div class="col-md-6">
                        <label>Saldo anterior en Dolares</label>
                        <input type="text" id="saldoanterior_dolares" placeholder="0.00" disabled>
                    </div>
                </div>
                @endif
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
                @if(configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==2 or configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==3)
                        <label>Saldo anterior en Soles</label>
                        <input type="text" id="saldoanterior_soles" placeholder="0.00" disabled>
                @endif
                <label>Monto a asignar en Soles *</label>
                <input type="number" id="montoasignar" step="0.01" min="0"/>
                @endif
             </div>
             <div class="col-md-6">
                <label>Persona responsable</label>
                <input type="text" value="{{ Auth::user()->apellidos }}, {{ Auth::user()->nombre }}" disabled>
                <label>Persona a asignar *</label>
                <select id="idusers">
                    <option value="{{Auth::user()->id}}">{{ Auth::user()->apellidos }}, {{ Auth::user()->nombre }}</option>
                </select>
             </div>
           </div>
          <button type="submit" class="btn mx-btn-post">Aperturar Caja</button>
</form>   
@endif                          
@endsection
@section('subscripts')
<script>
$("#idcaja").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/')}}/"+e.currentTarget.value,
        type:'GET',
        data: {
            view : 'saldoanterior'
       },
       success: function (respuesta){
          $("#saldoanterior_soles").val(respuesta.saldoactual_soles);
          $("#saldoanterior_dolares").val(respuesta.saldoactual_dolares);
       }
     })
});

$("#idusers").select2({
    @include('app.select2_acceso')
});
</script>
@endsection