<form @include('app.nuevosistema.submit',['method'=>'DELETE','view'=>'eliminar','id'=>$s_agencia->id])>
<div class="table-responsive">
  <table class="tabla-detalle">
      <tr>
        <th colspan="3" style="background-color: #afaeae;">GENERAL</th>
      </tr>
      <tr>
        <td width="10px">FECHA DE REGISTRO</td>
        <td width="1px">:</td>
        <td>{{ date_format(date_create($s_agencia->fecharegistro), 'd/m/Y - h:i:s A' ) }}</td>
      </tr>
      <tr>
        <td>LOGO</td>
        <td>:</td>
        <?php 
        $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/sistema/'.$s_agencia->logo; 
        if(file_exists($rutaimagen) AND $s_agencia->logo!=''){
            $urlimagen = url('/public/backoffice/tienda/'.$tienda->id.'/sistema/'.$s_agencia->logo);
        }else{
            $urlimagen = url('public/backoffice/sistema/sin_imagen_cuadrado.png');
        }
        ?>
        <td><img src="{{ $urlimagen }}" height="100px"></td>
      </tr>
      <tr>
        <td>RUC</td>
        <td>:</td>
        <td>{{ $s_agencia->ruc }}</td>
      </tr>
      <tr>
        <td>NOMBRE COMERCIAL</td>
        <td>:</td>
        <td>{{ $s_agencia->nombrecomercial }}</td>
      </tr>
      <tr>
        <td>RAZÓN SOCIAL</td>
        <td>:</td>
        <td>{{ $s_agencia->razonsocial }}</td>
      </tr>
      <tr>
        <td>UBICACIÓN (UBIGEO)</td>
        <td>:</td>
        <td>{{ $s_agencia->ubigeonombre }}</td>
      </tr>
      <tr>
        <td>DIRECCIÓN</td>
        <td>:</td>
        <td>{{ $s_agencia->direccion }}</td>
      </tr>
      <tr>
        <td>ESTADO</td>
        <td>:</td>
        @if ($s_agencia->idestado == 1)
        <td>ACTIVADO</td>
        @elseif ($s_agencia->idestado == 2)
        <td>DESACTIVADO</td>
        @endif
      </tr>
      <tr>
        <th colspan="3" style="background-color: #afaeae;">FACTURACIÓN</th>
      </tr>
      <tr>
        <td>SERIE</td>
        <td>:</td>
        <td>{{ $s_agencia->facturacion_serie }}</td>
      </tr>
      <tr>
        <td>CORRELATIVO INICIAL</td>
        <td>:</td>
        <td>{{ $s_agencia->facturacion_correlativoinicial }}</td>
      </tr>
      <tr>
        <td>USUARIO</td>
        <td>:</td>
        <td>{{ $s_agencia->facturacion_usuario }}</td>
      </tr>
      <tr>
        <td>CLAVE</td>
        <td>:</td>
        <td>{{ $s_agencia->facturacion_clave }}</td>
      </tr>
      <tr>
        <td>CERTIFICADO</td>
        <td>:</td>
        <td>{{ $s_agencia->facturacion_certificado }}</td>
      </tr>
      <tr>
        <td>ESTADO</td>
        <td>:</td>
        @if ($s_agencia->idestadofacturacion == 1)
        <td>ACTIVADO</td>
        @elseif ($s_agencia->idestadofacturacion == 2)
        <td>DESACTIVADO</td>
        @endif
      </tr>
</table>
</div>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro Eliminar?</b>
    </div>
    <button type="submit" class="btn mx-btn-post">Eliminar</button>
</form>   