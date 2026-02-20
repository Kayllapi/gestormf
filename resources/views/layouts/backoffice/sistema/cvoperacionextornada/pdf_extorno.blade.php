<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPERACIONES EXTORNADAS</title>
    <style>
      *{
        font-family:helvetica;
        font-size:12px;
      }
      @page {
          margin: 0cm 0cm;
      }

      /** Defina ahora los márgenes reales de cada página en el PDF **/
      body {
          margin-top: 1.2cm;
          margin-left: 0.7cm;
          margin-right: 0.7cm;
          margin-bottom: 0cm;
      }

      header {
          position: fixed;
          top: 0cm;
          left: 0.7cm;
          right: 0.7cm;
          height: 0.6cm;
          color: #0f0f0f;
          text-align: center;
          line-height: 0.6cm;
          font-size:15px !important;
          font-weight: bold;
          margin:5px;
          text-align:right;
          padding:5px;
      }
      footer {
          position: fixed; 
          bottom: 0cm; 
          left: 0.7cm; 
          right: 0.7cm;
          height: 1cm;
          color: #000;
          text-align: center;
          line-height: 0.4cm;
          font-size:12px;
      }
      footer > .page:after { content: counter(page, decimal-leading-zero); }

      .saltopagina{
        display:block;
        page-break-before:always;
      }
      /** Definir las reglas para titulo principal **/
      .badge{
        background-color: #fff;
        text-align: left;
        font-size: 12px;
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
    <div style="float:left;font-size:15px;" >{{ $tienda->ticket_nombre }} | {{ $agencia->nombreagencia }}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page" style="text-align:right;">Página </p>
  </footer>
  <main>
    <div class="container">
      <h4 align="center" style="margin:0px;">OPERACIONES EXTORNADAS</h4>
           <b>De: {{$fecha_inicio}} Al: {{$fecha_fin}}</b>
          <table style="width:100%;">
            <tr>
              <th style="border-bottom: 2px solid #000;border-top: 2px solid #000;">FECHA Y HORA</th>
              <th style="border-bottom: 2px solid #000;border-top: 2px solid #000;">COD/OPER.</th>
              <th style="border-bottom: 2px solid #000;border-top: 2px solid #000;">TIPO/OPER.</th>
              <th style="border-bottom: 2px solid #000;border-top: 2px solid #000;">N° DETALLE COMPRO.</th>
              <th style="border-bottom: 2px solid #000;border-top: 2px solid #000;">DESCRIPCIÓN/CLIENTES</th>
              <th style="border-bottom: 2px solid #000;border-top: 2px solid #000;">BANCO(OPER.)</th>
              <th style="border-bottom: 2px solid #000;border-top: 2px solid #000;">MONTO</th>
              <th style="border-bottom: 2px solid #000;border-top: 2px solid #000;">US.(EMISOR)</th>
              <th style="border-bottom: 2px solid #000;border-top: 2px solid #000;">US.(EXTORNO)</th>
              <th style="border-bottom: 2px solid #000;border-top: 2px solid #000;">SUCURSAL</th>
            </tr>
            <?php
            $totalpago = 0;
            ?>
            @foreach($gastoadministrativooperativos as $value)
            <?php
              $coutas = str_replace(',',', ',$value->pago_cuota);
              $totalpago += $value->total_pagar;
              $cuenta = $value->banco!=''?$value->banco.' - ***'.substr($value->cuenta, -5).' ('.$value->numerooperacion.')':'';
            ?>
            <tr>
              <td>{{$value->fechaextorno}}</td>
              <td>{{$value->codigo}}</td>
              <td>{{$value->operacion}}</td>
              <td>{{$value->detalleoperacion}}</td>
              <td>{{$value->descripcion}}</td>
              <td>{{$cuenta}}</td>
              <td style="text-align: right;">S/. {{$value->total_pagar}}</td>
              <td>{{$value->codigoresponsable}}</td>
              <td>{{$value->codigoresponsableeliminado}}</td>
              <td>{{$value->tiendanombre}}</td>
            </tr>
            @endforeach
            <tr>
              <th style="border-top: 2px solid #000;" colspan="10"></th>
            </tr>
          </table>

    </div>
  </main>
</body>
</html>