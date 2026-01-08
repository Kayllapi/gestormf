@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<?php
$get_idtiendaorigen = isset($_GET['idtiendaorigen']) ? $_GET['idtiendaorigen'] : 1;
$get_idusersorigen = isset($_GET['idusersorigen']) ? $_GET['idusersorigen'] : '';

?>
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de Transferencia por Productos</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reportetransferenciaproducto') }}" method="GET"> 
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
           <label>Código de Transferencia</label>
            <input class="form-control" type="number" id="codigo" name="codigo" value="{{isset($_GET['codigo'])?($_GET['codigo']!=''?$_GET['codigo']:''):''}}">
            <label>Unidad de Medida</label>
            <select id="unidadmedida" name="unidadmedida">
                <option></option>
                @foreach($unidadmedida as $value)
                <option value="{{ $value->id }}"> {{ $value->nombre }}</option>
                @endforeach
            </select>
         </div>
         <div class="col-md-6">
           <label>Motivo</label>
                  <input class="form-control" type="text" id="motivo" name="motivo" value="{{isset($_GET['motivo'])?($_GET['motivo']!=''?$_GET['motivo']:''):''}}">
                  <label>Fecha de Inicio</label>
                  <input class="form-control" type="date" name="fechainicio" id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
                  <label>Fecha de Fin</label>
                  <input class="form-control" type="date" name="fechafin" id="fechafin" value="{{isset($_GET['fechafin'])?($_GET['fechafin']!=''?$_GET['fechafin']:''):''}}">
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
        <th width="110px">Fecha Registro</th>
        <th>Tienda Origen (Responsable)</th>
        <th>Tienda Destino (Responsable)</th>
        <th>Productos</th>
        <th width="10px">Unidad de Medida</th>
        <th width="10px">Cantidad</th>
        <th width="10px">Cantidad Envío</th>
        <th width="10px">Cantidad Recepcionado</th>
        <th width="10px">Motivo</th>
      </tr>
    </thead>
    <tbody>
       @foreach($detalletransferencia as $value)
    <tr>
      <td>{{ str_pad($value->codigotransferencia, 6, "0", STR_PAD_LEFT) }}</td>
      <td>{{ date_format(date_create($value->fecharegistro), 'd/m/Y - h:i A')  }}</td>
       <td>{{ $value->tienda_origen_nombre}} {{ $value->idusersorigen!=0?'('.$value->nombreorigen.')':'' }}</td>
      <td>{{ $value->tienda_destino_nombre}} {{ $value->idusersdestino!=0?'('.$value->nombredestino.')':'' }}</td>
      <td>{{ $value->nombreproducto }}</td>
      <td>{{ $value->nombremedida }}</td>
      <td>{{ $value->cantidad }}</td>
      <td>{{ $value->cantidadenviado }}</td>
      <td>{{ $value->cantidadrecepcion }}</td>
      <td>{{ $value->motivo}}</td>
    </tr>
    @endforeach
    </tbody>
</table>
</div>
@endsection
@section('subscripts')
<script>
   function reporte(tipo){
        window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportetransferenciaproducto')}}?'+
                                'tipo='+tipo+
                                '&idproducto='+($('#idproducto').val()!=null?$('#idproducto').val():'')+
                                '&unidadmedida='+($('#unidadmedida').val()!=null?$('#unidadmedida').val():'')+
                                '&codigo='+$('#codigo').val()+
                                '&motivo='+$('#motivo').val()+
                                '&fechainicio='+$('#fechainicio').val()+
                                '&fechafin='+$('#fechafin').val();
    }
  $("#idproducto").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportetransferenciaproducto/showlistarproducto')}}",
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
  $("#unidadmedida").select2({
      placeholder: "--  Seleccionar --",
      allowClear: true
  }).val({{isset($_GET['unidadmedida'])?($_GET['unidadmedida']!=''?$_GET['unidadmedida']:'0'):'0'}}).trigger("change");
</script>
@endsection