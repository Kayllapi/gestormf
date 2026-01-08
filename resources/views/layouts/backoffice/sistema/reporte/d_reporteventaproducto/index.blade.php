@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de Ventas por Producto</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reporteventaproducto') }}" method="GET"> 
    <div class="custom-form">
      <div class="row">
         <div class="col-md-6">
           <label>Empresa</label>
            <select id="idagencia" name="idagencia">
                <option></option>
                @foreach($agencia as $value)
                <option value="{{ $value->id }}"?>{{ $value->nombrecomercial }}</option>
                @endforeach
            </select>
            <label>Producto</label>
            <select id="idproducto" name="idproducto">
                @if(isset($_GET['idproducto']))
                @if($_GET['idproducto']!='')
                <?php $producto = DB::table('s_producto')->where('idtienda',$tienda->id)->where('id',$_GET['idproducto'])->first();?>
                <option value="{{$producto->id}}">{{$producto->codigo}} - {{$producto->nombre}}</option>
                @else
                <option></option>
                @endif
                @else
                <option></option>
                @endif
            </select>
           <label>Cliente</label>
           <select id="idcliente" name="idcliente">
                @if(isset($_GET['idcliente']))
                @if($_GET['idcliente']!='')
                <?php $users = DB::table('users')->where('idtienda',$tienda->id)->where('id',$_GET['idcliente'])->first();?>
                <option value="{{$users->id}}">{{$users->identificacion}} - {{$users->apellidos}}, {{$users->nombre}}</option>
                @else
                <option></option>
                @endif
                @else
                <option></option>
                @endif
            </select>
           <label>Responsable</label>
            <select id="idresponsable" name="idresponsable">
                @if(isset($_GET['idresponsable']))
                @if($_GET['idresponsable']!='')
                <?php $users = DB::table('users')->where('idtienda',$tienda->id)->where('id',$_GET['idresponsable'])->first();?>
                <option value="{{$users->id}}">{{$users->identificacion}} - {{$users->apellidos}}, {{$users->nombre}}</option>
                @else
                <option></option>
                @endif
                @else
                <option></option>
                @endif
            </select>
         </div>
         <div class="col-md-6">
           <label>Comprobante</label>
            <select id="idcomprobante" name="idcomprobante">
                <option></option>
                @foreach($comprobante as $value)
                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
            </select>
            <label>Tipo de entrega</label>
            <select id="idtipoentrega" name="idtipoentrega">
                <option></option>
                @foreach($tipoentregas as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
            </select>
           <label>Moneda</label>
            <select id="idmoneda" name="idmoneda">
                <option></option>
                @foreach($moneda as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
            </select>
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
         </div>
         <div class="col-md-6">
          <div class="row">
            <div class="col-md-6">
                <a href="javascript:;" onclick="reporte('reporte')" class="btn  big-btn  color-bg flat-btn" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
            </div>
            <div class="col-md-6">
                  <a href="javascript:;" onclick="reporte('excel')"class="btn  big-btn  color-bg flat-btn" style="margin-bottom:10px;"><i class="fa fa-file-excel"></i>  Exportar Excel</a>
            </div>
          </div>
        </div>
       </div>
    </div>
</form>
<div class="table-responsive">
  <table class="table" id="tabla-contenido">
      <thead class="thead-dark">
        <tr>
          <th>Código de Venta</th>
          <th>Producto</th>
          <th>Comprobante</th>
          <th>P. Unitario</th>
          <th>Cant.</th>
          <th>Total</th>
          <th>Und. Medida</th>
          <th>Cliente</th>
          <th>Tipo de Pago</th>
          <th>Moneda</th>
          <th>Fecha Vendida</th>
          <th>Responsable</th>
        </tr>
      </thead>
      <tbody>
         @foreach($s_ventadetalle as $value)
          <tr>
           <td>{{ str_pad($value->codigoventa, 8, "0", STR_PAD_LEFT) }}</td>
           <td>{{ str_pad($value->codigo, 13, "0", STR_PAD_LEFT) }} - {{ $value->nombreproducto}}</td>
           <td>{{ $value->nombreComprobante }}</td>
           <td>{{ $value->preciounitario }}</td>
           <td>{{ $value->cantidad }}</td>
           <td>{{ $value->total }}</td>
           <td>{{ $value->nombreunidadmedida}}</td>
           <td>{{ $value->apellidocliente}},{{ $value->nombrecliente}}</td>
           <td>{{ $value->tipoentreganombre }}</td>
           <td>{{ $value->monedanombre }}</td> 
           <td>{{ date_format(date_create($value->fechaventa),"d/m/Y h:i:s A")}}</td>
           <td>{{ $value->nombreresponsable}}</td>
          </tr>
         @endforeach
      </tbody>
  </table>
</div>
<style>
  td{
    padding: 7px !important;
  }
</style>
{{ $s_ventadetalle->links('app.tablepagination', ['results' => $s_ventadetalle]) }}
@endsection
@section('subscripts')
<script>
     function reporte(tipo){
        window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/reporteventaproducto')}}?'+
                                'tipo='+tipo+
                                '&idproducto='+($('#idproducto').val()!=null?$('#idproducto').val():'')+
                                '&idresponsable='+($('#idresponsable').val()!=null?$('#idresponsable').val():'')+
                                '&idcliente='+($('#idcliente').val()!=null?$('#idcliente').val():'')+
                                '&idagencia='+($('#idagencia').val()!=null?$('#idagencia').val():'')+
                                '&idcomprobante='+($('#idcomprobante').val()!=null?$('#idcomprobante').val():'')+
                                '&idtipoentrega='+($('#idtipoentrega').val()!=null?$('#idtipoentrega').val():'')+
                                '&idmoneda='+($('#idmoneda').val()!=null?$('#idmoneda').val():'')+
                                '&fechainicio='+$('#fechainicio').val()+
                                '&fechafin='+$('#fechafin').val();
    }
  $("#idproducto").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reporteventaproducto/showlistarproducto')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                  buscar: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    placeholder: "--  Seleccionar Producto --",
    allowClear: true,
    minimumInputLength: 2,
    templateResult: function (state) {
        if (!state.id) {
            return state.text;
        }
        var urlimagen = '{{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }}';
        if(state.imagen!=null){
            urlimagen = '{{ url('public/backoffice/tienda') }}/'+state.idtienda+'/producto/40/'+state.imagen;
        }
        return $('<div>'+
                 '<div style="background-image: url('+urlimagen+');'+
                            'background-repeat: no-repeat;'+
                            'background-size: contain;'+
                            'background-position: center;'+
                            'width: 40px;'+
                            'height: 40px;'+
                            'float: left;'+
                            'margin-right: 5px;'+
                            'margin-top: -5px;">'+
                          '</div><div>'+state.nombre+'</div><div>'+state.unidadmedida+' - '+state.precioalpublico+'</div>');
    }
});
$("#idresponsable").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reporteventaproducto/showlistarusuario')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                  buscar: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2,
    allowClear: true
});
$("#idcliente").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reporteventaproducto/showlistarusuario')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                  buscar: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2,
    allowClear: true
});

$("#idagencia").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
}).val({{isset($_GET['idagencia'])?($_GET['idagencia']!=''?$_GET['idagencia']:'0'):'0'}}).trigger("change");
$("#idcomprobante").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
}).val({{isset($_GET['idcomprobante'])?($_GET['idcomprobante']!=''?$_GET['idcomprobante']:'0'):'0'}}).trigger("change");
$("#idtipoentrega").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
}).val({{isset($_GET['idtipoentrega'])?($_GET['idtipoentrega']!=''?$_GET['idtipoentrega']:'0'):'0'}}).trigger("change");
$("#idmoneda").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
}).val({{isset($_GET['idmoneda'])?($_GET['idmoneda']!=''?$_GET['idmoneda']:'0'):'0'}}).trigger("change");
</script>
@endsection