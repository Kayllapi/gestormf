<table class="tabla-detalle">
      <tr>
        <th colspan="3" style="background-color: #afaeae;">GENERAL</th>
      </tr>
      <tr>
        <td width="10px">FECHA DE REGISTRO</td>
        <td width="1px">:</td>
        <td>{{ date_format(date_create($s_marca->fecharegistro), 'd/m/Y - h:i:s A' ) }}</td>
      </tr>
      <tr>
        <td>NOMBRE</td>
        <td>:</td>
        <td>{{ $s_marca->nombre }}</td>
      </tr>
      <tr>
        <td>IMAGEN</td>
        <td>:</td>
        <?php 
        $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/sistema/'.$s_marca->imagen; 
        if(file_exists($rutaimagen) AND $s_marca->imagen!=''){
            $urlimagen = url('/public/backoffice/tienda/'.$tienda->id.'/sistema/'.$s_marca->imagen);
        }else{
            $urlimagen = url('public/backoffice/sistema/sin_imagen_cuadrado.png');
        }
        ?>
        <td><img src="{{ $urlimagen }}" height="100px"></td>
      </tr>
</table>