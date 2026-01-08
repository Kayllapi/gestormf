
<div class="table-responsive">
  <table class="table" id="tabla-contenido">
      <thead class="thead-dark">
        <tr>
          <th>Código de Venta</th>
          <th>Cliente</th>
          <th>Producto</th>
          <th>Cantidad</th>
        </tr>
      </thead>
      <tbody>
         @foreach($s_ventadetalle as $value)
          <tr>
           <td>{{ str_pad($value->codigoventa, 8, "0", STR_PAD_LEFT) }}</td>
            <td>{{$value->cliente}}</td>
           <td>{{ $value->nombreproducto}}</td>
           <td>{{ $value->cantidad }}</td>
          </tr>
         @endforeach
      </tbody>
  </table>
</div>