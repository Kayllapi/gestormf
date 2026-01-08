<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REQUERIMIENTO DE PAGO</title>
    <style>
      
      *{
          font-family:helvetica;
          font-size:14px;
      }
      @page {
          margin: 0cm 0cm;
      }

      /** Defina ahora los márgenes reales de cada página en el PDF **/
      body {
          margin-top: 1.2cm;
          margin-left: 1.8cm;
          margin-right: 1.5cm;
          margin-bottom: 2cm;
      }

      /** Definir las reglas del encabezado **/
      header {
          position: fixed;
          top: 0cm;
          left: 1.6cm;
          right: 1.3cm;
          height: 0.6cm;
          /** Estilos extra personales **/
          color: #000;
          text-align: center;
          line-height: 0.6cm;
          font-size:18px !important;
          font-weight: bold;
          border-bottom: 2px solid #144081; 
          margin:5px;
          text-align:right;
          padding:5px;
      }

      /** Definir las reglas del pie de página **/
      footer {
          position: fixed; 
          bottom: 0.7cm; 
          left: 1.6cm; 
          right: 1.3cm;
          height: 20px;

          /** Estilos extra personales **/
          color: #000;
          text-align: center;
          line-height: 0.4cm;
          font-size:11px;
      }
      /** Definir las reglas de numeracion de página **/
      footer .page:after { content: counter(page, decimal-leading-zero); }

      .saltopagina{
        display:block;
        page-break-before:always;
      }
      /** Definir las reglas para titulo principal **/
      .badge{
        background-color: #fff;
        text-align: left;
        font-size: 11px;
        color:#000;
        padding:3px;
        display:block;
        border-radius:5px;
        margin-bottom:2px;
        border: 1px solid #000;
      }
      /** Definir las reglas para subtitulo **/
      .subtitle{
        background-color: #fff; 
        color: #000;
        font-size:11px;
        border-width:0px;
      }
      .row {
        position:relative;
        padding: 2px;
      }
      .col {
        display: inline-block;
        padding: 2px;
        vertical-align: top;
      }
      .border-td{
        border:solid 1px #888888;    
      }
      
      .table, .table th, .table td {
        border: 1px solid #888888;
        border-collapse: collapse;
      }
      
      .table > thead > tr > th{
        background-color: #fff !important;color: #000 !important;text-align: center;
      }
      .table > tbody > tr > td{
        background-color: #fff !important;
      }
      .subtable{
        padding-left:10px;
      }
      .datafooter {
        position: absolute;
        bottom: 10px;
        text-align: right;
        right: 0.7cm;
      }
     </style>
</head>
<body>
  <header>
    <div style="float:left;font-size:18px;">{{ $tienda->nombre }}</div> C{{ str_pad($credito->cuenta, 8, "0", STR_PAD_LEFT) }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <div style="border-top: 1px solid #000"></div>
    <div style="position:absolute;left:0px;bottom:0px;">Contacto: {{$tienda->numerotelefono}}</div>
    <div style="position:absolute;left:200px;top:3px;">{{$tienda->paginaweb}}</div>
    <div style="position:absolute;right:0px;top:3px;">{{$tienda->direccion}}, {{$ubigeo_tienda->distrito}}, {{$ubigeo_tienda->provincia}}, {{$ubigeo_tienda->departamento}}</div>
  </footer>
  <main>
    <div class="container">
      <br>
      <h4 align="center" style="font-size:13px;margin-top:10px;margin-bottom:10px;">REQUERIMIENTO DE PAGO</h4>
      <!--div style="position:absolute; left:700px;top: 60px;">{{ $credito->cuenta!=''?$credito->cuenta:'00000000' }}</div-->
      <div style="text-align: justify;">
        
    <?php
    $meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
    $mes = $meses[(date_format(date_create(now()),'n')) - 1];
    $fecha_texto = date_format(date_create(now()),'d') . ' de ' . $mes . ' de ' . date_format(date_create(now()),'Y');
    ?>
    {{ $ubigeo_tienda->distrito }}, {{ $fecha_texto }}<br><br>

<b>PRESTATARIO</b><br>
<div style="width:100%;height:45px;">
    <div style="font-weight:bold;width:40px;float:left;">Sr(a):</div>
    <div style="float:left;">
        {{$credito->nombreclientecredito}}<br>
        {{$credito->direccioncliente}}<br>
        Distrito de {{$credito->distritoubigeocliente}}, Provincia de {{$credito->provinciaubigeocliente}} y Departamento de {{$credito->departamentoubigeocliente}}
    </div>    
</div>
<br>
@if($credito->nombreavalcredito!='')
<b>AVAL</b><br>
<div style="width:100%;height:45px;">
    <div style="font-weight:bold;width:40px;float:left;">Sr(a):</div>
    <div style="float:left;">
        {{$credito->nombreavalcredito}}<br>
        {{$credito->direccionaval}}<br>
        Distrito de {{$credito->distritoubigeoaval}}, Provincia de {{$credito->provinciaubigeoaval}} y Departamento de {{$credito->departamentoubigeoaval}}
    </div>     
</div>
<br>
@endif
<b>PRESENTE. - </b>
<br><br>
        
    <?php
    $meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
    $mes = $meses[(date_format(date_create($credito->fecha_desembolso),'n')) - 1];
    $fecha_texto = date_format(date_create($credito->fecha_desembolso),'d') . ' de ' . $mes . ' de ' . date_format(date_create($credito->fecha_desembolso),'Y');
    ?>
        
<b>REFERENCIA:</b> Contrato de Crédito N° C{{ $credito->cuenta }} de fecha {{ $fecha_texto }}
<br><br>
<div style="border-top: 1px solid #000"></div><br>
De nuestra consideración.
<br><br>
<?php
$cronograma = select_cronograma(
    $tienda->id,
    $credito->id,
    $credito->idforma_credito,
    $credito->modalidad_calculo,
    $credito->cuotas,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    1,
    'detalle_cobranza'
);
?>
Por medio de la presente nos dirigirnos a usted(es) a fin de requerirle(s) el pago de la(s) cuota(s) 
vencida(s) por el monto de S/. <b>{{$cronograma['cuota_vencida']}}</b> de la deuda que mantiene(n) con <b>{{ $tienda->nombre }}</b> conforme contrato 
de referencia; siendo <b>{{$cronograma['numero_cuota_vencida']}}</b> cuota(s) con <b>{{$cronograma['ultimo_atraso']}}</b> 
días de atraso. El monto incluye gastos de penalidad, interés 
compensatorio, interés moratorio y cargos respectivos. 
<br><br>
Al respecto se da un plazo de 24 horas para el pago y regularización respectivo o en el mejor de 
los casos buscar una alternativa de solución, caso contrario se procederá conforme Contrato, el mismo 
que firmó al momento de otorgarle el préstamo.
<br><br>

En caso realizó el pago respectivo dejar sin efecto la presente.
<br><br>
Atentamente,
      </div>

<br><br>
<br><br>
      <div style="width:200px;margin-top: -35px;margin: auto;text-align:center;">
            @if($tienda->firma!='')
            <div style="text-align:center">
            <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$tienda->firma) }}" width="100px"></div>
            @endif
            <div style="border-top: 1px solid #000;"></div>
            <span style="padding-top:10px;"><b>{{ $tienda->nombre }}</b></span>
            <br>
            <span>RUC: {{ $tienda->ruc }}</span>
            <br>
            <span><b>Representante Legal</b></span>
      </div>
      <br>
<br><br>
<br><br>
<br><br>
<br><br>
      <div style="width:100%;height:20px;">
        <div style="float:left;">Firma del receptor &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</div> 
        <div style="border-top: 1px dashed #000;float:left;width:180px;margin-top:12px;margin-left:5px;"></div>
      </div>
      <div style="width:100%;height:20px;">
        <div style="float:left;">Fecha de entrega &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</div> 
        <div style="border-top: 1px dashed #000;float:left;width:30px;margin-top:12px;margin-left:5px;"></div>
        <div style="float:left;">/</div>
        <div style="border-top: 1px dashed #000;float:left;width:30px;margin-top:12px;"></div>
        <div style="float:left;">/</div>
        <div style="border-top: 1px dashed #000;float:left;width:50px;margin-top:12px;"></div>
      </div>
      <div style="width:100%;height:20px;">
        <div style="float:left;">Nombres y Apellidos del receptor:</div> 
        <div style="border-top: 1px dashed #000;float:left;width:300px;margin-top:12px;margin-left:5px;"></div>
      </div>
      
      <br><br>
      
    </div>
  </main>
</body>
</html>