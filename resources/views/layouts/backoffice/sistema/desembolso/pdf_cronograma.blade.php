<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRONOGRAMA</title>
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
     </style>
</head>
<body>
  <header>
    <div style="float:left;font-size:15px;">{{ $tienda->nombre }} | {{$tienda->nombreagencia}}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
  </header>
  <main>
    <div class="container">
      <h4 align="center" style="margin: 0">CRONOGRAMA DE PAGOS (*)</h4>
    
          <table style="width:100%;">
            <tr>
              <td><b>PRODUCTO:</b> {{ $credito->nombreproductocredito }}</td>
              <td><b>ASESOR/EJECUTIVO:</b> {{ strtoupper($asesor->codigo) }}</td>
            </tr>
            <tr>
              <td><b>NOMBRE:</b> {{ $usuario->nombrecompleto }}</td>
              <td><b>DOC.IDENTIDAD:</b> {{ $usuario->identificacion }}</td>
            </tr>
            <tr>
              <td><b>CUENTA:</b> C{{ str_pad($credito->cuenta, 8, "0", STR_PAD_LEFT) }}</td>
              <td><b>COD.CLIENTE:</b> {{ $usuario->codigo }}</td>
            </tr>
          </table>
     
          <hr>
          <table style="width:100%;">
            <tr>
              <td width="100px"><b>MONTO DE PRESTAMO:</b> {{ $credito->monto_solicitado }}</td>
              <td width="100px"><b>MONEDA:</b> SOLES</td>
              <td width="100px"><b>FECHA DE DESEMBOLSO:</b> {{ date_format(date_create($credito->fecha_desembolso),'d-m-Y') }}</td>
            </tr>
            <tr>
              <td><b>FORMA DE PAGO:</b> {{ strtoupper($credito->forma_pago_credito_nombre) }}</td>
              <td><b>NUMERO CUOTAS:</b> {{ $credito->cuotas }}</td>
              <td><b>MODALIDAD CRÉDITO:</b> {{ strtoupper($credito->modalidad_credito_nombre) }}</td>
            </tr>
          </table>
     
          <table style="width:100%;">
            <tr>
              <th style="border-bottom: 2px solid #000;">CUOTA</th>
              <th style="border-bottom: 2px solid #000;">F. VENCIM.</th>
              <th style="border-bottom: 2px solid #000;">CAPITAL</th>
              <th style="border-bottom: 2px solid #000;">AMORTIZ.</th>
              <th style="border-bottom: 2px solid #000;">INTERES</th>
              <th style="border-bottom: 2px solid #000;">CARGO x <br>CUSTODIA. G.</th>
              <th style="border-bottom: 2px solid #000;">CUOTA DE PRESTAMO <br>(Int. + Cap. + Cust.)</th>
              <th style="border-bottom: 2px solid #000;">Ss. RECAUDO (**)</th>
              <th style="border-bottom: 2px solid #000;">TOTAL A PAGAR</th>
            </tr>
            <?php
            $total_amortizacion = 0;
            $total_interes = 0;
            $total_cuota = 0;
            $total_comision = 0;
            $total_cargo = 0;
            $total_total = 0;
            ?>
            @foreach($credito_cronograma as $value)
            <tr>
              <td>{{$value->numerocuota}}</td>
              <td>{{$value->fechapago}}</td>
              <td style="text-align: right;">{{$value->capital}}</td>
              <td style="text-align: right;">{{$value->amortizacion}}</td>
              <td style="text-align: right;">{{$value->interes}}</td>
              <td style="text-align: right;">{{$value->cargo}}</td>
              <td style="text-align: right;">{{$value->cuotapagar}}</td>
              <td style="text-align: right;">{{$value->comision}}</td>
              <td style="text-align: right;">{{$value->cuota_real}}</td>
            </tr>
            <?php
            $total_amortizacion = $total_amortizacion+$value->amortizacion;
            $total_interes = $total_interes+$value->interes;
            $total_cuota = $total_cuota+$value->cuotapagar;
            $total_comision = $total_comision+$value->comision;
            $total_cargo = $total_cargo+$value->cargo;
            $total_total = $total_total+$value->cuota_real;
            ?>
            @endforeach
            <tr>
              <td style="border-top: 2px solid #000;" colspan="3" align="center">*****TOTALES*****</td>
              <td style="border-top: 2px solid #000;text-align: right;">{{number_format($total_amortizacion, 2, '.', '')}}</td>
              <td style="border-top: 2px solid #000;text-align: right;">{{number_format($total_interes, 2, '.', '')}}</td>
              <td style="border-top: 2px solid #000;text-align: right;">{{number_format($total_cargo, 2, '.', '')}}</td>
              <td style="border-top: 2px solid #000;text-align: right;">{{number_format($total_cuota, 2, '.', '')}}</td>
              <td style="border-top: 2px solid #000;text-align: right;">{{number_format($total_comision, 2, '.', '')}}</td>
              <td style="border-top: 2px solid #000;text-align: right;">{{number_format($total_total, 2, '.', '')}}</td>
            </tr>
          </table>
      <div class="row" style="margin-top: -5px;">
        <div class="col" style="line-height: 1.3;">
          <p style="margin: 0;">- (*): Calculado con ajuste de céntimos en la última cuota para facilitar el pago en efectivo, manteniendo el costo total del crédito pactado</p>
          <p style="margin: 0;">- (**): Alternativo </p>
          <div style="width:100%; margin-top: 4px; border-bottom: 1px solid #5f5f5f;"></div>
          <p style="margin: 0;">- Estimado Cliente al pagar exija su comprobante respectivo.</p>
          <p style="margin: 0;">- Pague puntual sus cuotas y evite penalidades, gastos e interes adicional.</p>
        </div>
      </div>
      <p align="center" style="margin: 0; margin-bottom: 5px;"><b>CEL.: {{ $tienda->numerotelefono }}</b></p>
      <p align="center" style="margin: 0;"><b>{{ $tienda->paginaweb }}</b></p>
    </div>
  </main>
</body>
</html>