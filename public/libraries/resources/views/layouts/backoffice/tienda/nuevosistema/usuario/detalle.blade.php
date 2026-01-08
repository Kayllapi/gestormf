<div class="table-responsive">
  <table class="tabla-detalle">
      <tr>
        <th colspan="3" style="background-color: #afaeae;">GENERAL</th>
      </tr>
      <tr>
        <td width="10px">FECHA DE REGISTRO</td>
        <td width="1px">:</td>
        <td>{{ date_format(date_create($usuario->created_at), 'd/m/Y - h:i:s A' ) }}</td>
      </tr>
      <tr>
        <td width="10px">TIPO PERSONA</td>
        <td width="1px">:</td>
        <td>{{ $usuario->tipopersonanombre }}</td>
      </tr>
      <tr>
        @if($usuario->idtipopersona==1) 
        <td>DNI</td>
        <td>:</td>
        <td>{{ $usuario->identificacion }}</td>
        @elseif($usuario->idtipopersona==2) 
        <td>RUC</td>
        <td>:</td>
        <td>{{ $usuario->identificacion }}</td>
        @elseif($usuario->idtipopersona==3) 
        <td>CARNET EXTRAJERÍA</td>
        <td>:</td>
        <td>{{ $usuario->identificacion }}</td>
        @endif
      </tr>
      <tr>
        @if($usuario->idtipopersona==1) 
        <td>NOMBRE</td>
        <td>:</td>
        <td>{{ $usuario->nombre }}</td>
        @elseif($usuario->idtipopersona==2) 
        <td>NOMBRE COMERCIAL</td>
        <td>:</td>
        <td>{{ $usuario->nombre }}</td>
        @elseif($usuario->idtipopersona==3) 
        <td>NOMBRE</td>
        <td>:</td>
        <td>{{ $usuario->nombre }}</td>
        @endif
      </tr>
      <tr>
        @if($usuario->idtipopersona==1) 
        <td>APELLIDOS</td>
        <td>:</td>
        <td>{{ $usuario->apellidos }}</td>
        @elseif($usuario->idtipopersona==2) 
        <td>RAZÓN SOCIAL</td>
        <td>:</td>
        <td>{{ $usuario->apellidos }}</td>
        @elseif($usuario->idtipopersona==3) 
        <td>APELLIDOS</td>
        <td>:</td>
        <td>{{ $usuario->apellidos }}</td>
        @endif
      </tr>
      <tr>
        <td>IMAGEN DE PERFIL</td>
        <td>:</td>
        <?php 
        $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/sistema/'.$usuario->imagen; 
        if(file_exists($rutaimagen) AND $usuario->imagen!=''){
            $urlimagen = url('/public/backoffice/tienda/'.$tienda->id.'/sistema/'.$usuario->imagen);
        }else{
            $urlimagen = url('public/backoffice/sistema/sin_imagen_cuadrado.png');
        }
        ?>
        <td><img src="{{ $urlimagen }}" height="100px"></td>
      </tr>
      <tr>
        <td>FECHA DE NACIMIENTO</td>
        <td>:</td>
        <td>{{ $usuario->fechanacimiento }}</td>
      </tr>
      <tr>
        <td>LUGAR DE NACIMIENTO</td>
        <td>:</td>
        <td>{{ $usuario->ubigeonacimientonombre }}</td>
      </tr>
      <tr>
        @if($usuario->idgenero==1) 
        <td>GEMERO</td>
        <td>:</td>
        <td>MASCULINO</td>
        @elseif($usuario->idgenero==2) 
        <td>GEMERO</td>
        <td>:</td>
        <td>FEMENINO</td>
        @endif
      </tr>
      <tr>
        <td>ESTADO CIVIL</td>
        <td>:</td>
        <td>{{ $usuario->estadocivilnombre }}</td>
      </tr>
      <tr>
        <td>NIVEL DE ESTUDIO</td>
        <td>:</td>
        <td>{{ $usuario->nivelestudionombre }}</td>
      </tr>
      <tr>
        <td>OCUPACIÓN</td>
        <td>:</td>
        <td>{{ $usuario->ocupacion }}</td>
      </tr>
      <tr>
        <th colspan="3" style="background-color: #afaeae;">CONTACTO</th>
      </tr>
      <tr>
        <td>NÚMERO DE TELÉFONO</td>
        <td>:</td>
        <td>{{ $usuario->numerotelefono }}</td>
      </tr>
      <tr>
        <td>CORREO ELECTRÓNICO</td>
        <td>:</td>
        <td>{{ $usuario->email }}</td>
      </tr>
      <tr>
        <td>REFERENCIA</td>
        <td>:</td>
        <td>{{ $usuario->referencia }}</td>
      </tr>
      <tr>
        <td>UBIGEO</td>
        <td>:</td>
        <td>{{ $usuario->ubigeonombre }}</td>
      </tr>
      <tr>
        <td>DIRECCIÓN</td>
        <td>:</td>
        <td>{{ $usuario->direccion }}</td>
      </tr>
      <tr>
        <td>UBICACIÓN</td>
        <td>:</td>
        <td><div id="domicilio_mapa{{ $usuario->id }}" style="height: 100px;max-width:150px;width: 100%;margin-bottom: 5px;border-radius: 5px;border: 1px solid #aaa;"></div></td>
      </tr>
      <tr>
        <td>ESTADO</td>
        <td>:</td>
        @if ($usuario->idestado == 1)
        <td>HABILITADO</td>
        @elseif ($usuario->idestado == 2)
        <td>DESACTIVADO</td>
        @endif
      </tr>
</table>
</div>
<script>
    singleMap({
        'map' : '#domicilio_mapa{{ $usuario->id }}',
        'lat' : parseFloat( {{$usuario->mapa_latitud ?? '-12.071871667822409'}} ),
        'lng' : parseFloat( {{$usuario->mapa_longitud ?? '-75.21026847919165'}} ),
    });
</script>