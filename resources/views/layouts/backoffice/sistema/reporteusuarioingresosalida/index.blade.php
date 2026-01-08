@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte Boletas y Facturas</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reporteusuarioingresosalida') }}" method="GET"> 
    <div class="custom-form">
      <div class="row">
          <div class="col-md-6">
           <div class="row">
             <div class="col-md-12">
                <label>Identificacion</label>
                  <input type="text" id="identificacion" name="identificacion" value="{{isset($_GET['identificacion'])?($_GET['identificacion']!=''?$_GET['identificacion']:''):''}}"/>
             </div>
             <div class="col-md-12">
                <label>Usuario</label>
                  <select id="idusario" name="idusario">
                      @if(isset($_GET['idusario']))
                      @if($_GET['idusario']!='')
                      <?php $users = DB::table('users')->where('id',$_GET['idusario'])->first();?>
                      <option value="{{$users->id}}">{{$users->identificacion}} - {{$users->apellidos}}, {{$users->nombre}}</option>
                      @else
                      <option></option>
                      @endif
                      @else
                      <option></option>
                      @endif
                  </select>
              </div>
            </div>
        </div>
        <div class="col-md-6">
           <div class="row">
             <div class="col-md-6">
                <label>Fecha inicio</label>
                  <input type="date" name="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
             </div>
             <div class="col-md-6">
                <label>Fecha fin</label>
                  <input type="date" name="fechafin" value="{{isset($_GET['fechafin'])?($_GET['fechafin']!=''?$_GET['fechafin']:''):''}}">
             </div>
             <div class="col-md-12">
              <label>Estado</label>
            <select id="idestado" name="idestado">
                <option></option>
                <option value="1">Correcto</option>
                <option value="2">Incorrecto</option>
            </select>
             </div>
           </div>
         </div>
        <div class="col-md-12">
            <button type="submit" class="btn  big-btn  color-bg flat-btn" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</button>
         </div>
      
       </div>
    </div>
</form>
<div class="table-responsive">
  <table class="table" id="tabla-contenido">
      <thead class="thead-dark">
        <tr>
          <th width="200">Fecha Registro</th>
          <th width="150">Identificación</th>
          <th>Usuario</th>
          <th width="150">Observación</th>
          <th width="150">Estado</th>
        </tr>
      </thead>
      <tbody>
       @foreach($usuarioingresosalida as $value)
        <tr>
            <td>{{ date_format(date_create($value->fecharegistro), 'd-m-Y h:i:s A') }}</td>
            <td>{{ $value->identificacioningresada}}</td>
            <td>{{ $value->usuarioapellido}},{{ $value->usuarionombre}}</td>
            <td>{{ $value->observacion}}</td>
          <td>
          @if($value->idestado==1)
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> CORRECTO</span></div>
            @elseif($value->idestado==2)
              <div class="td-badge"><span class="badge badge-pill badge-danger"><i class="fas fa-ban"></i> INCORRECTO</span></div> 
           
            @endif</td>
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
{{ $usuarioingresosalida->links('app.tablepagination', ['results' => $usuarioingresosalida]) }}
@endsection
@section('subscripts')
<script>
  $("#idusario").select2({
    @include('app.select2_cliente')
  });
  $("#idestado").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
}).val({{isset($_GET['idestado'])?($_GET['idestado']!=''?$_GET['idestado']:'0'):'0'}}).trigger("change");

</script>
@endsection