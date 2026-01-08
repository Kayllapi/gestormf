@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Reprogramación</span>
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
<form action="javascript:;" 
      onsubmit="callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamoreprogramacion/{{$s_prestamo_credito->idprestamo_reprogramacion}}',
            method: 'PUT',
            data:   {
                view: 'editar',
                idprestamo_credito: $('#idcliente').val()
            }
        },
        function(resultado){
          location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoreprogramacion') }}';
        }, this)">
<div class="col-sm-6">
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
            <input type="date" value="{{$s_prestamo_credito->fechainicio}}" id="fechainicio" onchange="mostrar_credito_reprogramado()"/>
              <label>Motivo de reprogramación *</label>
              <textarea id="reprogramar_motivo" style="height:85px;">{{$s_prestamo_credito->motivo}}</textarea>
        </div>
        <div class="col-md-6">
              <label>Documento *</label>
                <div class="mensaje-success">
                  <i class="fa fa-file-pdf"></i> <a href="{{ url('/public/backoffice/tienda/'.$tienda->id.'/prestamoreprogramacion/'.$s_prestamo_credito->documento) }}" target="_blank">Ver Documento</a>
                </div>
              <div class="mensaje-warning">
                <i class="fa fa-warning"></i> Subir un Documento.
              </div>
              <input type="file" class="upload" id="reprogramar_documento">
              <input type="hidden" class="upload" id="anterior_reprogramar_documento" value="{{ $s_prestamo_credito->documento }}">
        </div>
    </div> 
    <button type="submit" class="btn mx-btn-post" style="margin-bottom: 5px;">Guardar Reprogramación</button>
</div>
<div class="col-sm-6">
    <div id="cont-credito_reprogramado"></div>
</div>  
</form>
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
  
    mostrar_credito_reprogramado();
  
    function mostrar_credito_reprogramado(){
        $.ajax({
            url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoreprogramacion/'.$s_prestamo_credito->id.'/edit') }}",
            type: 'GET',
            data: {
                view: 'credito_reprogramado',
                fechainicio: $('#fechainicio').val()
            },
            beforeSend: function (data) {
                load('#cont-credito_reprogramado');
            },
            success: function (res) {
                $('#cont-credito_reprogramado').html(res);
            }
        });
    }
</script>
@endsection