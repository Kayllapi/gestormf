<!DOCTYPE html>
<html>
<head>
    <title>Tarjeta de Pago</title>
    <style>
      html, body {
          margin: 0px;
          padding: 0px;
          font-size: 9px;
          font-weight: bold;
          font-family: Courier;
      }
      .contenedor {
        text-align: center;
        padding: 15px;
          padding-top:25px;
			  width: <?php echo  configuracion($tienda->id,'tarjetapago_anchoimpresion')['valor']!=null?(configuracion($tienda->id,'tarjetapago_anchoimpresion')['valor']-1):'11' ?>cm;
      }
      .table {
          width: 100%;
          margin:0px;
          padding:0px;
      }
      .tablatarjeta {
          float:left;
      }
      .tablatarjeta table {
          width: 100%;
          margin:0px;
          padding:0px;
      }
      .tablatarjeta table, .tablatarjeta table th, .tablatarjeta  table td {
          border: 1px solid black;
          border-collapse: collapse;
          padding:5px;
          text-align:right;
      }
      .logo {
          width:100%;
      }
      .nombrecomercial {
          font-size: 15px;
      }
      .nombrecomercial_detalle {
          font-size: 9px;
      }
      .cabecera {
          width:100%;
      }
      .empresa {
          width:50%;
          float:left;
      }
      .info {
          width:50%;
          float:left;
      }
      span.clear { 
        clear: left; 
        display: block; 
      }
    </style>
</head>
<body>
    <?php

        $empresa = '<div class="logo">
            <img src="'.url('public/backoffice/tienda/'.$tienda->id.'/logo/'.$tienda->imagen).'" height="50px">
            </div><div class="nombrecomercial_detalle">
            <div class="nombrecomercial">'.strtoupper($tienda->nombre).'</div>
           '.strtoupper($tienda->direccion).'<br>
           '.strtoupper($tienda->ubigeonombre).'<br>
            </div>';
   
    $db_mora = 0;
    if($prestamodesembolso->idprestamo_frecuencia==1){
        $db_mora = configuracion($tienda->id,'prestamo_mora_diario')['valor'];
    }elseif($prestamodesembolso->idprestamo_frecuencia==2){
        $db_mora = configuracion($tienda->id,'prestamo_mora_semanal')['valor'];
    }elseif($prestamodesembolso->idprestamo_frecuencia==3){
        $db_mora = configuracion($tienda->id,'prestamo_mora_quincenal')['valor'];
    }elseif($prestamodesembolso->idprestamo_frecuencia==4){
        $db_mora = configuracion($tienda->id,'prestamo_mora_mensual')['valor'];
    }elseif($prestamodesembolso->idprestamo_frecuencia==5){
        $db_mora = configuracion($tienda->id,'prestamo_mora_programado')['valor'];
    }
    ?>
    <div class="contenedor">
        <div class="cabecera">
        @if(configuracion($tienda->id,'prestamo_tarjetapago_ubicacionlogo')['valor']==1)
        <div class="empresa">
        <?php echo $empresa ?>
        </div>
        <div class="info">
        <table>
            <thead>
                <tr>
                    <td>DESEMBOLSO</td>
                    <td>:</td>
                    <td>{{date_format(date_create($prestamodesembolso->fechadesembolsado),"d/m/Y")}}</td>
                </tr>
                <tr>
                    <td>COD. CRÉDITO</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->codigo}}</td>
                </tr>
                <tr>
                    <td>CLIENTE</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->cliente_nombre}}</td>
                </tr>
                <tr>
                    <td>DIRECCIÓN</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->cliente_direccion}}</td>
                </tr>
                <tr>
                    <td>TELF. CLIENTE</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->cliente_numerotelefono}}</td>
                </tr>
                <tr>
                    <td>ASESOR</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->asesor_nombre}}</td>
                </tr>
                <tr>
                    <td>DESEMBOLSADO</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->monedasimbolo}} {{$prestamodesembolso->monto}}</td>
                </tr>
                <tr>
                    <td>MORA X DÍA</td>
                    <td>:</td>
                    <td>{{$db_mora}}</td>
                </tr>
            </thead>
        </table>
        </div>
        @elseif(configuracion($tienda->id,'prestamo_tarjetapago_ubicacionlogo')['valor']==2)
        <div class="info">
        <table>
            <thead>
                <tr>
                    <td>DESEMBOLSO</td>
                    <td>:</td>
                    <td>{{date_format(date_create($prestamodesembolso->fechadesembolsado),"d/m/Y")}}</td>
                </tr>
                <tr>
                    <td>COD. CRÉDITO</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->codigo}}</td>
                </tr>
                <tr>
                    <td>CLIENTE</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->cliente_nombre}}</td>
                </tr>
                <tr>
                    <td>DIRECCIÓN</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->cliente_direccion}}</td>
                </tr>
                <tr>
                    <td>TELF. CLIENTE</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->cliente_numerotelefono}}</td>
                </tr>
                <tr>
                    <td>ASESOR</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->asesor_nombre}}</td>
                </tr>
                <tr>
                    <td>DESEMBOLSADO</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->monedasimbolo}} {{$prestamodesembolso->monto}}</td>
                </tr>
                <tr>
                    <td>MORA X DÍA</td>
                    <td>:</td>
                    <td>{{$db_mora}}</td>
                </tr>
            </thead>
        </table>
        </div>
        <div class="empresa">
        <?php echo $empresa ?>
        </div>
        @elseif(configuracion($tienda->id,'prestamo_tarjetapago_ubicacionlogo')['valor']==3)
        <?php echo $empresa ?><br>
        <table>
            <thead>
                <tr>
                    <td>DESEMBOLSO</td>
                    <td>:</td>
                    <td>{{date_format(date_create($prestamodesembolso->fechadesembolsado),"d/m/Y")}}</td>
                    <td>TELF. CLIENTE</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->cliente_numerotelefono}}</td>
                </tr>
                <tr>
                    <td>COD. CRÉDITO</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->codigo}}</td>
                    <td>ASESOR</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->asesor_nombre}}</td>
                </tr>
                <tr>
                    <td>CLIENTE</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->cliente_nombre}}</td>
                    <td>DESEMBOLSADO</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->monedasimbolo}} {{$prestamodesembolso->monto}}</td>
                </tr>
                <tr>
                    <td>DIRECCIÓN</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->cliente_direccion}}</td>
                    <td>MORA X DÍA</td>
                    <td>:</td>
                    <td>{{$db_mora}}</td>
                </tr>
            </thead>
        </table>
        @else
        <div class="empresa">
        <?php echo $empresa ?>
        </div>
        <div class="info">
        <table>
            <thead>
                <tr>
                    <td>DESEMBOLSO</td>
                    <td>:</td>
                    <td>{{date_format(date_create($prestamodesembolso->fechadesembolsado),"d/m/Y")}}</td>
                </tr>
                <tr>
                    <td>COD. CRÉDITO</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->codigo}}</td>
                </tr>
                <tr>
                    <td>CLIENTE</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->cliente_nombre}}</td>
                </tr>
                <tr>
                    <td>DIRECCIÓN</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->cliente_direccion}}</td>
                </tr>
                <tr>
                    <td>TELF. CLIENTE</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->cliente_numerotelefono}}</td>
                </tr>
                <tr>
                    <td>ASESOR</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->asesor_nombre}}</td>
                </tr>
                <tr>
                    <td>DESEMBOLSADO</td>
                    <td>:</td>
                    <td>{{$prestamodesembolso->monedasimbolo}} {{$prestamodesembolso->monto}}</td>
                </tr>
                <tr>
                    <td>MORA X DÍA</td>
                    <td>:</td>
                    <td>{{$db_mora}}</td>
                </tr>
            </thead>
        </table>
        </div>
        @endif
        <span class="clear"></span>
        </div>
        <div class="tablatarjeta">
        <table>
            <thead>
                <tr>
                    <td>Nº</td>
                    <td>FECHA</td>
                    <td>SALDO</td>
                    <td>CUOTA</td>
                    @if($prestamodesembolso->total_abono>0)
                    <td>ABONO</td>
                    @endif
                    <td>MORA</td>
                    <td>TOTAL</td>
                    <td>CANCELADO</td>
                    <td>FIRMA</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($prestamodesembolsodetalle as $value)
                <tr>
                    <td>{{ $value->numero }}</td>
                    <td>{{ date_format(date_create($value->fechavencimiento),"d/m/Y") }}</td>
                    <td>{{ $value->saldomontototal }}</td>
                    <td>{{ $value->total }}</td>
                    @if($prestamodesembolso->total_abono>0)
                    <td>{{ $value->abono }}</td>
                    @endif
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</body>
</html>