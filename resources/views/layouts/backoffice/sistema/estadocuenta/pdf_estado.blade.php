<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRÉSTAMOS</title>
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
          margin-bottom: 2cm;
      }

      /** Definir las reglas del encabezado **/
      header {
          position: fixed;
          top: 0cm;
          left: 0.7cm;
          right: 0.7cm;
          height: 0.6cm;
          /** Estilos extra personales **/
          color: #676869;
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
          bottom: 0cm; 
          left: 0.7cm; 
          right: 0.7cm;
          height: 1cm;

          /** Estilos extra personales **/
          color: #000;
          text-align: center;
          line-height: 0.4cm;
          font-size:12px;
      }
      /** Definir las reglas de numeracion de página **/ 
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
    <div style="float:left;font-size:18px;">{{ $tienda->nombre }} | {{ $tienda->nombreagencia }}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
  </header>
  <main>
    <div class="container">
      <h4 align="center">PRÉSTAMOS</h4>
          <table style="width:100%;">
            <tr>
              <th style="border-bottom: 2px solid #000;">N°</th>
              <th style="border-bottom: 2px solid #000;">N° CUENTA</th>
              <th style="border-bottom: 2px solid #000;">FC</th>
              <th style="border-bottom: 2px solid #000;">PRODUCTO</th>
              <th style="border-bottom: 2px solid #000;">MONT. PRES.</th>
              <th style="border-bottom: 2px solid #000;">F. DESEMBOLSO</th>
              <th style="border-bottom: 2px solid #000;">F. PAGO</th>
              <th style="border-bottom: 2px solid #000;">N° CUOTA</th>
              <th style="border-bottom: 2px solid #000;">TEM</th>
              <th style="border-bottom: 2px solid #000;">ESTADO</th>
              <th style="border-bottom: 2px solid #000;">SUCURSAL</th>
            </tr>
            <?php
            $i = 1;
            $totalpago = 0;
            ?>
            @foreach($creditos as $value)
            <?php
            
              $cp = '';
              if($value->idforma_credito==1){
                  $cp = 'CP';
              }
              elseif($value->idforma_credito==2){
                  $cp = 'CNP';
              }
              elseif($value->idforma_credito==3){
                  $cp = 'CC';
              }
            ?>
            <tr>
              <td>{{$i}}</td>
              <td>C{{$value->cuenta}}</td>
              <td>{{$cp}}</td>
              <td>{{$value->nombreproductocredito}}</td>
              <td style="text-align: right;">S/. {{$value->monto_solicitado}}</td>
              <td>{{date_format(date_create($value->fecha_desembolso),'d-m-Y h:i:s A')}}</td>
              <td>{{ strtoupper($value->forma_pago_credito_nombre) }}</td>
              <td style="text-align: right;">{{$value->cuotas}}</td>
              <td style="text-align: right;">{{$value->tasa_tem}}</td>
              <td>
                @if($value->idestadocredito==2)
                CANCELADO
                @else
                PEND.
                @endif
              </td>
              <td>{{$value->tiendanombre}}</td>
            </tr>
            <?php
            $i++;
            ?>
            @endforeach
          </table>

    </div>
  </main>
</body>
</html>