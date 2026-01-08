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
        <td class="b-titulo" colspan="5">1. Solicitud del Cliente</td>
        <td>Fecha:</td>
        <td>{{date_format(date_create($prestamocredito->fecharegistro),"d/m/Y h:i:s A")}}</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="2">Agencia Oficina:</td>
        <td colspan="5">{{$prestamocredito->tiendanombre}}</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="3">Apellidos y Nombres del Solicitante</td>
        <td class="b-subtitulo">N° DNI</td>
        <td class="b-subtitulo">N° RUC</td>
        <td class="b-subtitulo" colspan="2">Dirección del Negocio</td>
      </tr>
      <tr>
        <td colspan="3">{{$prestamocredito->clienteapellidos}}, {{$prestamocredito->clientenombre}}</td>
        <td>{{$prestamocredito->clienteidentificacion}}</td>
        <td>---</td>
        <td colspan="2">---</td>
      </tr>
      @if($prestamocredito->idconyuge!=0)
      <tr>
        <td class="b-subtitulo" colspan="3">Apellidos y Nombres del Conyuge</td>
        <td class="b-subtitulo">N° DNI</td>
        <td class="b-subtitulo">N° RUC</td>
        <td class="b-subtitulo" colspan="2">Dirección del Domicilio</td>
      </tr>
      <tr>
        <td colspan="3">{{$prestamocredito->conyugeapellidos}}, {{$prestamocredito->conyugenombre}}</td>
        <td>{{$prestamocredito->conyugeidentificacion}}</td>
        <td>---</td>
        <td colspan="2">---</td>
      </tr>
      @endif
      <tr>
        <td class="b-subtitulo" colspan="2">Tipo Cliente en Financiera Rey:</td>
        <td colspan="2">---</td>
        <td class="b-subtitulo">Producto:</td>
        <td colspan="2">---</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="2">Monto Solicitado:</td>
        <td colspan="2">{{$prestamocredito->total_cuotafinal}}</td>
        <td class="b-subtitulo">N° de Cuotas:</td>
        <td>{{$prestamocredito->numerocuota}}</td>
        <td>{{$prestamocredito->frecuencia_nombre}}</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="2">Actividad o Cargo:</td>
        <td colspan="2">---</td>
        <td class="b-subtitulo" colspan="2">Frecuencia de Pago:</td>
        <td>---</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="2">Destino del Crédito</td>
        <td class="b-subtitulo" colspan="2">Descripción</td>
        <td class="b-subtitulo" colspan="2" rowspan="2">N° DE ENTIDADES FINANCIERAS CON QUIENES TIENE DEUDA A LA FECHA DE LA PROPUESTA,INCLUYENDO FINANCIERA REY:</td>
        <td rowspan="2">---</td>
      </tr>
      <tr>
        <td colspan="2" rowspan="2">---</td>
        <td colspan="2" rowspan="2">---</td>
      </tr>
      <tr>
        <td colspan="3">
          <br>
          <br>
          _____________________________ <br>
          FIRMA DEL SOLICITANTE
        </td>
      </tr>
      <tr class="b-primary">
        <td class="b-titulo" colspan="5">2. Propuesta del Asesor de Negocios</td>
        <td>Fecha:</td>
        <td>{{date_format(date_create($prestamocredito->fechainicio),"d/m/Y")}}</td>
      </tr>
      <tr>
        <td class="b-subtitulo">Monto</td>
        <td class="b-subtitulo">Tasa (TEM)</td>
        <td class="b-subtitulo">Monto de Cuota</td>
        <td class="b-subtitulo">N° de Cuotas</td>
        <td class="b-subtitulo">Frecuencia de Pagos</td>
        <td class="b-subtitulo">Fecha ed Pago de la 1° Cuota</td>
        <td class="b-subtitulo">Calificación Central de Riesgo</td>
      </tr>
      <tr>
        <td>{{$prestamocredito->monto}}</td>
        <td>{{$prestamocredito->tasa}}</td>
        <td>{{$prestamocredito->total_cuotafinal}}</td>
        <td>{{$prestamocredito->numerocuota}}</td>
        <td>{{$prestamocredito->frecuencia_nombre}}</td>
        <td>{{date_format(date_create($prestamocredito->fechainicio),"d/m/Y")}}</td>
        <td>---</td>
      </tr>
      <tr>
        <td colspan="7">
          <br>
          <br>
          <br>
          <br>
          <br>
          ---
        </td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="2">Riesgo</td>
        <td colspan="5">---</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="4" rowspan="2">Garantias</td>
        <td class="b-subtitulo" colspan="3">Personas que firman el Pagaré</td>
      </tr>
      <tr>
        <td class="b-subtitulo">DNI</td>
        <td class="b-subtitulo" colspan="2">TITULARES</td>
      </tr>
      <tr>
        <td class="b-subtitulo">Titular:</td>
        <td colspan="3">---</td>
        <td>---</td>
        <td rowspan="4" colspan="2">---</td>
      </tr>
      @if($prestamocredito->idgarante!=0)
      <tr>
        <td class="b-subtitulo">Aval 1:</td>
        <td colspan="3">{{$prestamocredito->garanteapellidos}}, {{$prestamocredito->garantenombre}}</td>
        <td>{{$prestamocredito->garanteidentificacion}}</td>
      </tr>
      @endif
      <tr>
        <td class="b-subtitulo">Aval 2:</td>
        <td colspan="3">--</td>
        <td>---</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="5">Total en Soles</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="5">Exposición Patrimonial (Total Pasivo/Patrimonio)</td>
        <td colspan="2" rowspan="4">---</td>
      </tr>
      <tr>
        <td colspan="5">---</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="5">Monto Cuota/Excedente</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="5">Deuda Total/Valor de Garantía</td>
      </tr>
      <tr class="b-primary">
        <td class="b-titulo" colspan="5">3. Resolución del Comité de Crédito</td>
        <td colspan=2>N° de Pagaré</td>
      </tr>
      <tr>
        <td class="b-subtitulo">Aprobado:</td>
        <td>---</td>
        <td class="b-subtitulo">Denegado:</td>
        <td>---</td>
        <td class="b-subtitulo">Denegado Difinitivo:</td>
        <td>---</td>
        <td class="b-subtitulo">Fecha: </td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="2">Monto</td>
        <td class="b-subtitulo">Tasa (TEM)</td>
        <td class="b-subtitulo">Monto de Cuota</td>
        <td class="b-subtitulo">N° de Cuotas</td>
        <td class="b-subtitulo">Fecha de Pago 1° Cuota</td>
        <td class="b-subtitulo">Frecuencia de Pago</td>
      </tr>
      <tr>
        <td colspan="2">---</td>
        <td>---</td>
        <td>---</td>
        <td>---</td>
        <td>---</td>
        <td>---</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="3">Comentarios/Observaciones:</td>
        <td colspan="4">----</td>
      </tr>
      <tr>
        <td class="b-subtitulo" colspan="3">Nivel de Aprobación:</td>
        <td colspan="4">----</td>
      </tr>
    </tbody>
  </table>
</body>
</html>