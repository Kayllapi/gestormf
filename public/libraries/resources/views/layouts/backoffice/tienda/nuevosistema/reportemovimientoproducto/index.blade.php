@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de Movimientos por Producto</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reportemovimientoproducto') }}" 
      method="GET"> 
    <div class="custom-form">
      <div class="row">
         <div class="col-md-6">
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
                <label>Tipo Movimiento</label>
             <select id="idtipomovimiento" name="idtipomovimiento">
                <option></option>
                @foreach($tipomovimiento as $value)
                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
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
         </div>
         <div class="col-md-6">
            <label>Motivo</label>
            <input type="text" name="motivo" id="motivo" value="{{isset($_GET['motivo'])?($_GET['motivo']!=''?$_GET['motivo']:''):''}}"/>
            <label>Fecha inicio</label>
            <input type="date" name="fechainicio" id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}"/>
            <label>Fecha fin</label>
            <input type="date" name="fechafin" id="fechafin" value="{{isset($_GET['fechafin'])?($_GET['fechafin']!=''?$_GET['fechafin']:''):''}}"/>
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
        <th>Tipo</th>
        <th>Motivo</th>
        <th>Responsable</th>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Fecha de registro</th>
        <th width="10px">Estado</th>
      </tr>
    </thead>
    <tbody>
        @foreach($s_productomovimiento as $value)
        <tr>
          <td>{{$value->nombretipomovimiento}}</td>
          <td>{{$value->motivo}}</td>
          <td>{{$value->responsablenombre}}</td>
          <td>{{$value->productonombre}}</td>
          <td>{{$value->cantidad}}</td>
          <td>{{ date_format(date_create($value->fecharegistro), 'd/m/Y h:i:s A') }}</td>
          <td>
             @if($value->s_idestado==2)
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
            @else
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Pendiente</span></div> 
            @endif
          </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
{{ $s_productomovimiento->links('app.tablepagination', ['results' => $s_productomovimiento]) }}
@endsection
@section('subscripts')
<script>
  function reporte(tipo){
        window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportemovimientoproducto')}}?'+
                                'tipo='+tipo+
                                '&idresponsable='+($('#idresponsable').val()!=null?$('#idresponsable').val():'')+
                                '&idtipomovimiento='+($('#idtipomovimiento').val()!=null?$('#idtipomovimiento').val():'')+
                                '&idproducto='+($('#idproducto').val()!=null?$('#idproducto').val():'')+
                                '&motivo='+$('#motivo').val()+
                                '&fechainicio='+$('#fechainicio').val()+
                                '&fechafin='+$('#fechafin').val();
    }
$("#idresponsable").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportemovimientoproducto/showlistarusuario')}}",
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
    $("#idproducto").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportemovimientoproducto/showlistarproducto')}}",
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
  $("#idtipomovimiento").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
}).val({{isset($_GET['idtipomovimiento'])?($_GET['idtipomovimiento']!=''?$_GET['idtipomovimiento']:'0'):'0'}}).trigger("change");

</script>
@endsection