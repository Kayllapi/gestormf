@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Recepcionar Cierre de Caja</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/aperturaycierre') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/aperturaycierre/{{ $s_aperturacierre->id }}',
        method: 'PUT',
        data:{
            view: 'confirmarrecepcioncierre'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/aperturaycierre') }}';                                                                            
    },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
             <div class="col-md-6">
                <label>Caja</label>
                <select id="idcaja" disabled>
                    <option></option>
                    @foreach($s_cajas as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
                <div class="row">
                    <div class="col-md-6">
                        <label>Monto cerrado en Soles</label>
                        <input type="number" value="{{ $s_aperturacierre->montocierre }}" id="montocierre" disabled/>
                    </div>
                    <div class="col-md-6">
                        <label>Monto cerrado en Dolares</label>
                        <input type="number" value="{{ $s_aperturacierre->montocierre_dolares }}" id="montocierre_dolares" disabled/>
                    </div>
                </div>
             </div>
             <div class="col-md-6">
                <label>Persona responsable</label>
                <select id="idusersresponsable" disabled>
                    <option></option>
                    @foreach($users as $value)
                    <option value="{{ $value->id }}">{{ $value->apellidos }}, {{ $value->nombre }}</option>
                    @endforeach
                </select>
                <label>Persona asignado</label>
                <select id="idusers" disabled>
                    <option></option>
                    @foreach($users as $value)
                    <option value="{{ $value->id }}">{{ $value->apellidos }}, {{ $value->nombre }}</option>
                    @endforeach
                </select>
             </div>
           </div>
        </div>
    </div>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¡Esta seguro de Recepcionar!
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn mx-btn-post">Confirmar Cierre</button>
        </div>
    </div> 
</form>                             
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