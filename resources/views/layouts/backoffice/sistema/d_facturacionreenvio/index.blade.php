@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Consulta de Validez de Comprobantes</span>
    </div>
</div>

<form action="{{ url('backoffice/reportefacturacionboletafactura') }}" method="GET" autocomplete="off">
<div class="row">
    <div class="col-md-6">
        <label>Fecha inicio</label>
        <input type="date" name="fechainicio" id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
    </div>
    <div class="col-md-6">
        <label>Fecha fin</label>
        <input type="date" name="fechafin" id="fechafin" value="{{isset($_GET['fechafin'])?($_GET['fechafin']!=''?$_GET['fechafin']:''):''}}">
    </div>
</div>
</form>
<div class="row">
    <div class="col-md-6">
        <a href="javascript:;" onclick="reporte('reporte')" class="btn  btn-warning" style="margin-bottom: 5px;
    padding-bottom: 10px;
    padding-top: 10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
    </div>
    <div class="col-md-6">
        <button style="width: 100%;margin-bottom: 5px;" id="reenviarComprobante" class="btn btn-primary">Consulta Validez</button>
    </div>
</div>

<div class="table-responsive">
  <table class="table" id="tabla-contenido">
      <thead class="thead-dark">
          <tr>
              <th width="40px">Serie</th>
              <th width="85px">Correlativo</th>
              <th width="80px">Total</th>
              <th width="85px">Moneda</th>
              <th width="170px">Fecha de Emisión</th>
              <th>Cliente</th>
              <th>Emisor</th>
              <th>R. Mensaje</th>
              <th width="10px">Reenvio</th>
          </tr>
      </thead>
      <tbody>       
          @foreach($facturas as $value)
            <tr idfactura='{{ $value->id }}'>
              <td>{{ $value->venta_serie }}</td>
              <td>{{ str_pad($value->venta_correlativo, 8, "0", STR_PAD_LEFT) }}</td>
              <td>{{ $value->venta_montoimpuestoventa }}</td>
              <td>
                  @if($value->venta_tipomoneda=='PEN')
                      SOLES
                  @elseif($value->venta_tipomoneda=='USD')
                      DOLARES
                  @endif
              </td>
              <td>{{ date_format(date_create($value->venta_fechaemision), 'd/m/Y h:i:s A') }}</td>
              <td>
                {{ $value->cliente_numerodocumento }}</br>
                {{ $value->cliente_razonsocial }}
              </td>
              <td>
                {{ $value->emisor_ruc }}</br>
                {{ $value->emisor_razonsocial }}
              </td>
              <td></td>
              <td></td>
            </tr>
          @endforeach
      </tbody>
  </table>
</div>
@endsection

@section('subscripts')
<script>
  $('#reenviarComprobante').click(function (e) {
    reenviar();
  });
  
  let tabla = document.querySelectorAll('#tabla-contenido tbody tr');
  
  function ajaxEnviar(key, idfactura) {
    $.ajax({
      url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionreenvio/show-reenviar')}}",
      type: 'GET',
      dataType: 'json',
      data: { idfactura },
      success: function (data) {
        console.log(data)
        tabla[key].cells[tabla[1].cells.length - 2].innerHTML = data['mensaje'];
        
        if (data['resultado'] == 'ERROR') {
            tabla[key].style.background = '#F08080';
            tabla[key].cells[tabla[1].cells.length - 1].innerHTML = '<a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionboletafactura') }}/'+idfactura+'/edit?view=ticket&estado='+data['estado']+'&codigo='+data['codigo']+'&mensaje='+data['mensaje']+'" target="_blank" class="btn btn-primary">'+
                          '<i class="fa fa-check"></i> Reenviar'+
                        '</a>';
        }else {
            tabla[key].style.background = '#0DD9B3';
        }
        
      }
    });
  }
  
  function reenviar() {
    tabla.forEach( (value, key) => {
      setTimeout(() => {
          ajaxEnviar(key, value.getAttribute('idfactura'));
      }, key * 1000);
    });
  }
  
  function reporte(tipo){
    window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionreenvio')}}?'+
      'tipo='+tipo+
      '&fechainicio='+$('#fechainicio').val()+
      '&fechafin='+$('#fechafin').val();
  }
  
</script>
@endsection