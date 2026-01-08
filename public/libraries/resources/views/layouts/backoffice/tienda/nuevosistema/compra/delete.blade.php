<form @include('app.nuevosistema.submit',['method'=>'DELETE','view'=>'eliminar','id'=>$compra->id])>

<table class="tabla-detalle">
      <tr>
        <th colspan="3" style="background-color: #afaeae;">GENERAL</th>
      </tr>
      <tr>
        <td width="10%">FECHA DE EMISION</td>
        <td width="1px">:</td>
        <td>{{ $compra->fechaemision }}</td>
      </tr>
      <tr>
        <td>PROVEEDOR</td>
        <td>:</td>
        <td>{{$usuarios->apellidos}}, {{ $usuarios->nombre }}</td>
      </tr>
      <tr>
        <td>COMPROBANTE</td>
        <td>:</td>
        <td>{{ $comprobante->nombre }} </td>
      </tr>
      <tr>
        <td>SERIE-CORRELATIVO</td>
        <td>:</td>
        <td>{{ $compra->seriecorrelativo }} </td>
      </tr>
      <tr>
        <td>ESTADO</td>
        <td>:</td>
        <td>{{ $compra->s_idestado }} </td>
      </tr>
      <tr>
        <td>MONEDA</td>
        <td>:</td>
        <td>{{ $s_monedas->nombre }} </td>
      </tr>
</table>


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
   <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de eliminar?</b>
    </div>
    <div class="profile-edit-container">
      <div class="custom-form">
        <button type="submit" class="btn mx-btn-post">Eliminar</button>
      </div>
    </div> 
</form>
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