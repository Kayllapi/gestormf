<form @include('app.nuevosistema.submit',['method'=>'DELETE','view'=>'eliminar','id'=>$s_categoria->id])>
    <table class="tabla-detalle">
      <tr>
        <th colspan="3" style="background-color: #afaeae;">GENERAL</th>
      </tr>
      <tr>
        <td width="10px">FECHA DE REGISTRO</td>
        <td width="1px">:</td>
        <td>{{ date_format(date_create($s_categoria->fecharegistro), 'd/m/Y - h:i:s A' ) }}</td>
      </tr>
      <tr>
        <td>CATEGORIA</td>
        <td>:</td>
        <td>{{ $s_categoria->categorianombre }}</td>
      </tr>
      <tr>
        <td>Sub Categoria</td>
        <td>:</td>
        <td>{{ $s_categoria->nombre }}</td>
      </tr>
      <tr>
        <td>IMAGEN</td>
        <td>:</td>
        <?php 
        $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/nuevosistema/'.$s_categoria->imagen; 
        if(file_exists($rutaimagen) AND $s_categoria->imagen!=''){
            $urlimagen = url('/public/backoffice/tienda/'.$tienda->id.'/nuevosistema/'.$s_categoria->imagen);
        }else{
            $urlimagen = url('public/backoffice/sistema/sin_imagen_cuadrado.png');
        }
        ?>
        <td><img src="{{ $urlimagen }}" height="100px"></td>
      </tr>
</table>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de eliminar?</b>
    </div>
    <button type="submit" class="btn mx-btn-post">Eliminar</button>
</form>                  
<script>
uploadfile({
  input:"#imagen",
  cont:"#cont-fileupload",
  result:"#resultado-fileupload",
  ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/nuevosistema/') }}",
  image: "{{ $s_categoria->imagen }}"
});
</script>