<table style="width:100%">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <th style="font-weight: 900; background-color:#31353d; color: #ffffff; text-align: center; font-size: 12px; " colspan="7">
              {{ $titulo }}
            </th>
        </tr>
        <tr></tr>
        <tr>
            <th></th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Código</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Nombre</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Categoría</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Marca</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">U.Medida</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Precio</th>
            <th style="border: 1px solid black; font-weight: 900; background-color: #31353d; color: #ffffff;">Estado</th>
        </tr>
    </thead>
    <tbody>
       @foreach($producto as $value)
          <tr>
            <td></td>
            <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
            <td>{{ $value->nombre}}</td>
            <td>{{ $value->nombrecategoria}}</td>
            <td>{{ $value->nombremarca}}</td>
            <td>{{ $value->nombreummedida}}</td>
            <td>{{ $value->precioalpublico}}</td>
            <td>
            @if($value->s_idestado==1)
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Activado</span></div>
            @else
              <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fas fa-sync-alt"></i> Desactivado</span></div> 
            @endif
          </td>
          </tr>
          @endforeach
    </tbody>
</table>