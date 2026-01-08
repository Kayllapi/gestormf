@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de Compra de Producto</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reportecompraproducto') }}" method="GET"> 
    <div class="custom-form">
      <div class="row">
        <div class="col-md-6">
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
            <label>Proveedor</label>
             <select id="idproveedor" name="idproveedor">
                  @if(isset($_GET['idproveedor']))
                  @if($_GET['idproveedor']!='')
                  <?php $users = DB::table('users')->where('idtienda',$tienda->id)->where('id',$_GET['idproveedor'])->first();?>
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
                <label>Fecha inicio</label>
                 <input type="date" name="fechainicio" id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
                  <label>Fecha fin</label>
            <input type="date" name="fechafin" id="fechafin" value="{{isset($_GET['fechafin'])?($_GET['fechafin']!=''?$_GET['fechafin']:''):''}}">
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
</form>
<div class="table-responsive">
  <table class="table" id="tabla-contenido">
      <thead class="thead-dark">
        <tr>
          <th width="10px">Código</th>
          <th>Producto</th>
          <th>Comprobante</th>
          <th>Serie Correlativo</th>
          <th>P. Unitario</th>
          <th>Cant.</th>
          <th>Total</th>
          <th>Proveedor</th>
          <th>Fecha Emisión</th>
          <th width="30px">Responsable</th>
        </tr>
      </thead>
      <tbody>
         @foreach($s_compradetalle as $value)
        <tr>
          <td>{{ str_pad($value->codigocompra, 8, "0", STR_PAD_LEFT) }}</td>
          <td>{{ str_pad($value->codigo, 13, "0", STR_PAD_LEFT) }} - {{ $value->nombreproducto}}</td>
           <td>{{ $value->nombreComprobante }}</td>
          <td>{{ str_pad($value->seriecorrelativo, 8, "0", STR_PAD_LEFT) }}</td>
           <td>{{ $value->preciounitario }}</td>
           <td>{{ $value->cantidad }}</td>
           <td>{{ $value->preciototal }}</td>
           <td>{{ $value->nombreproveedor}}</td>
           <td>{{ date_format(date_create($value->fechacompra), 'd/m/Y h:i:s A') }}</td>
           <td>{{ $value->apellidosresponsable}},{{ $value->nombreresponsable}}</td>
          </tr>
         @endforeach
      </tbody>
  </table>
</div>
{{ $s_compradetalle->links('app.tablepagination', ['results' => $s_compradetalle]) }}
@endsection
@section('subscripts')
<script>
   function reporte(tipo){
        window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportecompraproducto')}}?'+
                                'tipo='+tipo+
                                '&idresponsable='+($('#idresponsable').val()!=null?$('#idresponsable').val():'')+
                                '&idproveedor='+($('#idproveedor').val()!=null?$('#idproveedor').val():'')+
                                '&idproducto='+($('#idproducto').val()!=null?$('#idproducto').val():'')+
//                                 '&idagencia='+($('#idagencia').val()!=null?$('#idagencia').val():'')+
                                '&idcomprobante='+($('#idcomprobante').val()!=null?$('#idcomprobante').val():'')+
//                                 '&motivo='+$('#motivo').val()+
                                '&fechainicio='+$('#fechainicio').val()+
                                '&fechafin='+$('#fechafin').val();
    }
  $("#idproducto").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportecompraproducto/showlistarproducto')}}",
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
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportecompraproducto/showlistarusuario')}}",
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
$("#idproveedor").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportecompraproducto/showlistarusuario')}}",
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
</script>
@endsection