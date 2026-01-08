@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle de Reprogramación</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoreprogramacion') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div id="carga-refinanciacion">
  <div class="row">
      <div class="col-sm-12">
          <label>Crédito del Cliente</label>
          <select id="idcliente" disabled>
              <option value="{{$s_prestamo_credito->id}}">
                {{$s_prestamo_credito->tipocredito}} / {{$s_prestamo_credito->estado}} / {{$s_prestamo_credito->codigo}} / {{$s_prestamo_credito->cliente_identificacion}} - {{$s_prestamo_credito->cliente_apellidos}}, {{$s_prestamo_credito->cliente_nombre}} / {{date_format(date_create($s_prestamo_credito->fechadesembolsado),"d-m-Y")}} / {{$s_prestamo_credito->monedasimbolo}} {{$s_prestamo_credito->monto}}</option>
          </select>
      </div>
      <div id="cont-clientecredito"></div>
  </div>
</div>
    <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
          <span>Reprogramar Crédito</span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label>Frecuencia</label>
            <select id="idfrecuencia" disabled>
                <option></option>
                @foreach($frecuencias as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
            </select>
            <label>Fecha de Inicio *</label>
            <input type="date" value="{{$s_prestamo_credito->fechainicio}}" id="fechainicio" onchange="mostrar_credito_reprogramado()" disabled/>
              <label>Motivo de reprogramación *</label>
              <textarea id="reprogramar_motivo" style="height:85px;" disabled>{{$s_prestamo_credito->motivo}}</textarea>
        </div>
        <div class="col-md-6">
              <label>Documento *</label>
                <div class="mensaje-success">
                  <i class="fa fa-file-pdf"></i> <a href="{{ url('/public/backoffice/tienda/'.$tienda->id.'/prestamoreprogramacion/'.$s_prestamo_credito->documento) }}" target="_blank">Ver Documento</a>
                </div>
        </div>
    </div> 
@endsection
@section('subscripts')
<script>
    $('#idcliente').select2({
        placeholder: "-- Seleccionar Cliente --",
        allowClear: true,
        minimumInputLength: 2,
    });
  
    tab({click:'#tab-credito'});
  
    $('#idfrecuencia').select2({
        placeholder: '-- Seleccionar Frecuencia --',
        minimumResultsForSearch: -1,
    }).val({{ $s_prestamo_credito->idprestamo_frecuencia }}).trigger('change');

</script>
@endsection