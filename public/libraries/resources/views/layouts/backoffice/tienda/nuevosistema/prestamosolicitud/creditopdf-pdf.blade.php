<!DOCTYPE html>
<html>
<head>
  <title>PDF Solicitud</title>
  <style>
    html, body {
      margin: 0px;
      padding: 15px;
      font-size: 11px;
      font-weight: bold;
      font-family: Courier;
    }
    
    .table {
      width: 100%;
      margin:0px;
      padding:0px;
      border-collapse: collapse;
      border-spacing: 0;
/*       font-size: 11px; */
/*         border: none; */
    }
    .table td {
      border:1px solid #ccc;
      padding: 5px;
    }
    .b-primary {
      background-color: #008cea;
      color:white;
    }
    .b-titulo {
      height:20px;
    }
    .b-subtitulo {
      background-color: #eae7e7;
    }
    .text-left {
      text-align: left;
    }
  </style>
</head>
<body>
  <table class="table">
    <tbody>
      <tr class="b-primary">
        <td class="b-titulo" colspan="8">Solicitud de Crédito</td>
      </tr>
      <tr>
        <td colspan="2"></td>
        <td class="b-subtitulo">Microempresa Individual</td>
        <td>(&nbsp;)</td>
        <td class="b-subtitulo">Consumo Independiente</td>
        <td>(&nbsp;)</td>
        <td class="b-subtitulo">Consumo Dependiente</td>
        <td>(&nbsp;)</td>
      </tr>
      <tr>
        <td colspan="2"></td>
        <td class="b-subtitulo">Refinanciación</td>
        <td>(&nbsp;)</td>
        <td class="b-subtitulo">Reprogramación</td>
        <td>(&nbsp;)</td>
        <td class="b-subtitulo">Ampliación</td>
        <td>(&nbsp;)</td>
      </tr>
      
      <tr class="b-primary">
        <td class="b-titulo" colspan="6">1. Solicitud del Cliente</td>
        <td>Fecha:</td>
        <td>{{date_format(date_create($prestamocredito->fechainicio),"d/m/Y")}}</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="2">Agencia Oficina:</td>
        <td colspan="6">{{$prestamocredito->tiendanombre}}</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="3" style="width:40%;">Apellidos y Nombres del Solicitante</td>
        <td class="b-subtitulo" style="width:10%;">N° DNI</td>
        <td class="b-subtitulo" colspan="3" style="width:40%;">Apellidos y Nombres del Conyuge</td>
        <td class="b-subtitulo" style="width:10%;">N° DNI</td>
      </tr>
      <tr>
        <td colspan="3">{{$prestamocredito->clienteapellidos}}, {{$prestamocredito->clientenombre}}</td>
        <td>{{$prestamocredito->clienteidentificacion}}</td>
        <td colspan="3">
          @if($prestamocredito->idconyuge!=0)
          {{$prestamocredito->conyugeapellidos}}, {{$prestamocredito->conyugenombre}}
          @endif
        </td>
        <td>{{$prestamocredito->conyugeidentificacion}}</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="2">Tipo Cliente:</td>
        <td colspan="2">
          @if($cantidadrecurrente>1)
            Recurrente ({{$cantidadrecurrente}})
          @else
            Nuevo
          @endif
        </td>
        <td class="b-subtitulo" colspan="2">Producto:</td>
        <td colspan="2">
        <?php $numpr=1 ?>
        @foreach($productos as $value)
        <?php 
        if($numpr==1){ 
          echo $value->nombre_giro; 
        }else{
          echo ', '.$value->nombre_giro; 
        }
        $numpr++;
        ?>
        @endforeach
        </td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="2">Monto Solicitado:</td>
        <td colspan="2">{{$prestamocredito->total_cuotafinal}}</td>
        <td class="b-subtitulo" colspan="2">N° de Cuotas:</td>
        <td>{{$prestamocredito->numerocuota}}</td>
        <td>{{$prestamocredito->frecuencia_nombre}}</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="2">Destino del Crédito</td>
        <td class="b-subtitulo" colspan="2">Descripción</td>
        <td class="b-subtitulo" colspan="3" rowspan="2">N° DE ENTIDADES FINANCIERAS CON QUIENES TIENE DEUDA A LA FECHA DE LA PROPUESTA:</td>
        <td rowspan="2">---</td>
      </tr>
      <tr>
        <td colspan="2" rowspan="2">{{$prestamocualitativo!=''?$prestamocualitativo->destino:''}}</td>
        <td colspan="2" rowspan="2">{{$prestamocualitativo!=''?$prestamocualitativo->descripcion:''}}</td>
      </tr>
      <tr>
        <td colspan="4">
          <br>
          <br>
          <br>
          _____________________________ <br>
          FIRMA DEL SOLICITANTE
        </td>
      </tr>
      <tr class="b-primary">
        <td class="b-titulo" colspan="8">2. Propuesta del Asesor de Negocios</td>
      </tr>
      <tr>
        <td class="b-subtitulo">Monto</td>
        <td class="b-subtitulo">Tasa (TEM)</td>
        <td class="b-subtitulo">Monto de Cuota</td>
        <td class="b-subtitulo">N° de Cuotas</td>
        <td class="b-subtitulo">Frecuencia de Pagos</td>
        <td class="b-subtitulo">Fecha de Pago de la 1° Cuota</td>
        <td class="b-subtitulo" colspan="2">Calificación Central de Riesgo</td>
      </tr>
      <tr>
        <td>{{$prestamocredito->monto}}</td>
        <td>{{$prestamocredito->tasa}}</td>
        <td>{{$prestamocredito->cuota}}</td>
        <td>{{$prestamocredito->numerocuota}}</td>
        <td>{{$prestamocredito->frecuencia_nombre}}</td>
        <td>{{date_format(date_create($prestamocredito->fechainicio),"d/m/Y")}}</td>
        <td colspan="2">{{$prestamocualitativo!=''?$prestamocualitativo->calificacion:''}}</td>
      </tr>
      <tr>
        <td colspan="8">
          {{$prestamocualitativo!=''?$prestamocualitativo->comentario:''}}
        </td>
      </tr>
      @if(count($avales)>0)
      <tr>
        <td class="b-subtitulo" colspan="6">Avales</td>
        <td class="b-subtitulo" colspan="2">Firma</td>
      </tr>
      <?php $numaval=1 ?>
      @foreach($avales as $value)
      <tr>
        <td class="b-subtitulo" rowspan="3">Aval {{$numaval}}:</td>
        <td class="b-subtitulo">Nombres:</td>
        <td colspan="4">{{$value->avalnombre}}</td>
        <td rowspan="3" colspan="2"></td>
      </tr>
      <tr>
        <td class="b-subtitulo">Apellidos:</td>
        <td colspan="4" colspan="2">{{$value->avalapellidos}}</td>
      </tr>
      <tr>
        <td class="b-subtitulo">DNI:</td>
        <td colspan="4" colspan="2">{{$value->avalidentificacion}}</td>
      </tr>
      <?php $numaval++ ?>
      @endforeach
      @endif
      @if($prestamocredito->idestadocredito==3)
      <tr class="b-primary">
        <td class="b-titulo" colspan="5">3. Resolución del Comité de Crédito</td>
        <td colspan="2">N° de Pagaré</td>
        <td></td>
      </tr>
      <tr>
        <td class="b-subtitulo">Aprobado:</td>
        <td>---</td>
        <td class="b-subtitulo">Denegado:</td>
        <td>---</td>
        <td class="b-subtitulo">Denegado Difinitivo:</td>
        <td>---</td>
        <td class="b-subtitulo">Fecha: </td>
        <td>---</td>
      </tr>
      <tr>
        <td class="b-subtitulo">Monto</td>
        <td class="b-subtitulo">Tasa (TEM)</td>
        <td class="b-subtitulo">Monto de Cuota</td>
        <td class="b-subtitulo">N° de Cuotas</td>
        <td class="b-subtitulo" colspan="2">Fecha de Pago 1° Cuota</td>
        <td class="b-subtitulo" colspan="2">Frecuencia de Pago</td>
      </tr>
      <tr>
        <td>---</td>
        <td>---</td>
        <td>---</td>
        <td>---</td>
        <td colspan="2">---</td>
        <td colspan="2">---</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="2">Comentarios/Observaciones:</td>
        <td colspan="6">----</td>
      </tr>
      @endif
    </tbody>
  </table>
</body>
</html>