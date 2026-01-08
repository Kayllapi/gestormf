@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle Tranferencia de Productos</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div id="carga-productotransferencia">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
             <div class="col-md-6">
               <label>Estado de Tranferencia</label>
                  <select id="idestado" disabled>
                      <option value="1">Solicitar Productos</option>
                      <option value="2">Enviar Productos</option>
                      <option value="3">Recepcionar Productos</option>
                  </select>
               <label>Motivo</label>
                <input type="text" id="motivo"  value="{{$productotransferencia->motivo}}" disabled/>
                
             </div>
                <div class="col-md-6">
                <label>Tienda Origen</label>
                  <select class="form-control" id="idtiendaorigen" disabled>
                      <option></option>
                    @foreach($tiendas as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                  </select>
                    <label>Tienda Destino</label>
                  <select class="form-control" id="idtiendadestino" disabled>
                      <option></option>
                    @foreach($tiendas as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                  </select>
             </div>
           </div>
          <div class="table-responsive">
            <table class="table" id="tabla-contenido">
                <thead class="thead-dark">
                  <tr>
                    <th width="15%">Código</th>
                    <th>Producto</th>
                    <th width="110px">Unidad de Medida</th>
                    <th width="110px">Cantidad</th>
                    <th width="50px">Enviado</th>
                    <th width="50px">Recepcionado</th>
                    <th width="110px">Motivo</th>
                    <th width="10px"></th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($detalletransferencia as $value)
                    <tr>
                      <td>{{str_pad($value->producodigoimpresion, 6, "0", STR_PAD_LEFT)}}</td>
                      <td>{{$value->productonombre}}</td>
                      <td>{{$value->unidadmedidanombre}}</td>
                      <td>{{$value->cantidad}}</td>
                      <td>{{$value->cantidadenviado}}</td>
                      <td>{{$value->cantidadrecepcion}}</td>
                      <td>{{$value->motivo}}</td> 
                    </tr>
                    @endforeach
                </tbody>

            </table>
          </div>
        </div>
    </div>
</div>
@endsection
@section('subscripts')
<script>
$('#idestado').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}).val({{$productotransferencia->idestado}}).trigger('change'); 
  
$('#idtiendaorigen').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}).val({{$productotransferencia->idtiendaorigen}}).trigger('change');  
  
$('#idtiendadestino').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}).val({{$productotransferencia->idtiendadestino}}).trigger('change'); 
</script>
@endsection