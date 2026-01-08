@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle Compra</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compra') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
             <div class="col-md-6">
                <label>Proveedor</label>
                <select id="idproveedor" disabled>
                    <option></option>
                    @foreach($usuarios as $value)
                      <option value="{{ $value->id }}">{{$value->apellidos}}, {{ $value->nombre }}</option>
                    @endforeach
                </select>
                <div class="row">
                  <div class="col-md-7">
                    <label>Comprobante</label>
                    <select id="idcomprobante" disabled>
                        <option></option>
                        @foreach($comprobante as $value)
                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                        @endforeach
                    </select>
                  </div>
                  <div class="col-md-5">
                    <label>Serie - Correlativo</label>
                    <input type="text" value="{{ $compra->seriecorrelativo }}" id="seriecorrelativo" disabled/>
                  </div>
                </div>
             </div>
             <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <label>Fecha de Emisión</label>
                        <input type="date" value="{{ $compra->fechaemision }}" id="fechaemision" disabled/>
                    </div>
                    <div class="col-md-6">
                        <label>Moneda</label>
                        <select id="idmoneda" disabled>
                            <option></option>
                            @foreach($s_monedas as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <label>Estado</label>
                <select id="idestado" disabled>
                    <option value="1">Pedido (Orden de compra)</option>
                    <option value="2">Compra</option>
                    <option></option>
                </select>
             </div>
           </div>
        </div>
    </div>
    <div class="custom-form">
    <div class="table-responsive">
      <table class="table" id="tabla-contenido">
          <thead class="thead-dark">
            <tr>
              <th width="15%">Código</th>
              <th>Producto</th>
              <th width="60px">Cantidad</th>
              <th width="110px">P. Unitario</th>
              <th width="110px">P. Total</th>
              <th width="110px">Fecha de Vencimiento</th>
            </tr>
          </thead>
          <tbody>
          <?php $total = 0 ?>
          @foreach($s_compradetalles as $value)
          <?php $total = $total+number_format($value->cantidad*$value->preciounitario, 2, '.', '') ?>
            <tr style="background-color: #008cea;color: #fff;height: 40px;">
            <td>{{ $value->productocodigo }}</td>
            <td>{{ $value->productonombre }}</td>
            <td>{{ $value->cantidad }}</td>
            <td>{{ $value->preciounitario }}</td> 
            <td>{{ number_format($value->cantidad*$value->preciounitario, 2, '.', '') }}</td>    
            <td>{{ $value->fechavencimiento }}</td>
            </tr>
          @endforeach
          </tbody>

      </table>
    </div>
  </div>
  <div class="profile-edit-container">
    <div class="custom-form">
      <div class="row align-items-center">   
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
          <label for="">Total</label>
          <input type="text" id="totalsinredondear" value="{{ $compra->total==0?number_format($total, 2, '.', ''):$compra->total }}" disabled>
          <label for="">Total Redondeado</label>
          <input type="text" id="total" value="{{ $compra->totalredondeado==0?number_format(round($total, 2), 2, '.', ''):$compra->totalredondeado }}" disabled>
        </div>          
      </div> 
    </div>
  </div>
</div>
@endsection
@section('subscripts')
<script>
$("#idproveedor").select2({
    placeholder: "--  Seleccionar --"
}).val({{$compra->s_idusuarioproveedor}}).trigger("change");

$("#idmoneda").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{ $compra->s_idmoneda }}).trigger("change");
  
$("#idcomprobante").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$compra->s_idcomprobante}}).trigger("change");

$("#idestado").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$compra->s_idestado}}).trigger("change");
</script>
@endsection