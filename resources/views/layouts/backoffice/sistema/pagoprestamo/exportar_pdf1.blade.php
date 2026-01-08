<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HISTORIAL DE PAGOS DE PRÉSTAMOS</title>
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
    <div style="float:left;font-size:18px;">{{ $tienda->nombre }}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
  </header>
  <main>
    <div class="container">
      <h4 align="center">HISTORIAL DE PAGOS DE PRÉSTAMOS</h4>
           <b>De: {{$fecha_inicio}} Al: {{$fecha_fin}}</b>
          <table style="width:100%;">
            <tr>
              <th style="border-bottom: 2px solid #000;">N°</th>
              <th style="border-bottom: 2px solid #000;">CLIENTE</th>
              <th style="border-bottom: 2px solid #000;">CUOTAS</th>
              <th style="border-bottom: 2px solid #000;">ACUENTA</th>
              <th style="border-bottom: 2px solid #000;">CAPITAL</th>
              <th style="border-bottom: 2px solid #000;">INTERES</th>
              <th style="border-bottom: 2px solid #000;">COMISIÓN</th>
              <th style="border-bottom: 2px solid #000;">CARGO</th>
              <th style="border-bottom: 2px solid #000;">PENALIDAD</th>
              <th style="border-bottom: 2px solid #000;">TENENCIA</th>
              <th style="border-bottom: 2px solid #000;">MORA</th>
              <th style="border-bottom: 2px solid #000;">TOTAL</th>
              <th style="border-bottom: 2px solid #000;">FECHA</th>
              <th style="border-bottom: 2px solid #000;">F. PAGO</th>
              <th style="border-bottom: 2px solid #000;">BANCO</th>
              <th style="border-bottom: 2px solid #000;">N° OPERACIÓN</th>
            </tr>
          <?php  
          $total_pagoacuenta = 0;
          $total_amortizacion = 0;
          $total_interes = 0;
          $total_comision = 0;
          $total_cargo = 0;
          $total_penalidad = 0;
          $total_tenencia = 0;
          $total_compensatorio = 0;
          $total_pagar = 0;
          ?>
          
          @foreach($creditos as $key => $value)
            
              <?php  
              $operacionen = '';
       
              if($value->idformapago==1){
                  $operacionen = 'CAJA';
              }elseif($value->idformapago==2){
                  $operacionen = 'BANCO';
              }
            
              $users_prestamo = DB::table('s_users_prestamo')
                  ->join('ubigeo','ubigeo.id','s_users_prestamo.idubigeo_ac_economica')
                  ->where('id_s_users',$value->idcliente)
                  ->select(
                      's_users_prestamo.*',
                      'ubigeo.nombre as ubigeonombre',
                  )
                  ->first();
            
              $direccion = $value->clientedireccion.', '.$value->ubigeonombre;
              if($users_prestamo){
                  if($users_prestamo->casanegocio=='SI'){
                      $direccion = $users_prestamo->direccion_ac_economica.', '.$users_prestamo->ubigeonombre;
                  }
              }
            
              $coutas = str_replace(',',', ',$value->pago_cuota);
              ?>
                      <tr>
                            <td rowspan='2'>{{$key+1}}</td>
                        <td><u>{{$value->nombrecliente}}</u></td>
                            <td>{{$coutas}}</td>
                            <td style='text-align:right'>{{$value->total_pagoacuenta}}</td>
                            <td style='text-align:right'>{{$value->total_amortizacion}}</td>
                            <td style='text-align:right'>{{$value->total_interes}}</td>
                            <td style='text-align:right'>{{$value->total_comision}}</td>
                            <td style='text-align:right'>{{$value->total_cargo}}</td>
                            <td style='text-align:right'>{{$value->total_penalidad}}</td>
                            <td style='text-align:right'>{{$value->total_tenencia}}</td>
                            <td style='text-align:right'>{{$value->total_compensatorio}}</td>
                            <td style='text-align:right'>{{$value->total_pagar}}</td>
                            <td style='text-align:center'>{{$value->fecharegistro}}</td>
                            <td>{{$operacionen}}</td>
                            <td>{{$value->banco}}</td>
                            <td>{{$value->cuenta}}</td>
                        </tr>
                        <tr>
                            <td colspan='15'>{{$direccion}}</td>
                        </tr>
                  
              <?php        
                        
              $total_pagoacuenta = $total_pagoacuenta+$value->total_pagoacuenta;
              $total_amortizacion =$total_amortizacion+$value->total_amortizacion;
              $total_interes = $total_interes+$value->total_interes;
              $total_comision = $total_comision+$value->total_comision;
              $total_cargo = $total_cargo+$value->total_cargo;
              $total_penalidad = $total_penalidad+$value->total_penalidad;
              $total_tenencia = $total_tenencia+$value->total_tenencia;
              $total_compensatorio = $total_compensatorio+$value->total_compensatorio;
              $total_pagar = $total_pagar+$value->total_pagar;
              ?>
          
          @endforeach
          </tbody><tfoot class="table-dark" style="position: sticky;bottom: 0;">
                <tr>
                  <td style="border-top: 2px solid #000;text-align:right" colspan="3">TOTAL</td>
                  <td style="border-top: 2px solid #000;text-align:right">{{number_format($total_pagoacuenta, 2, '.', '')}}</td>
                  <td style="border-top: 2px solid #000;text-align:right">{{number_format($total_amortizacion, 2, '.', '')}}</td>
                  <td style="border-top: 2px solid #000;text-align:right">{{number_format($total_interes, 2, '.', '')}}</td>
                  <td style="border-top: 2px solid #000;text-align:right">{{number_format($total_comision, 2, '.', '')}}</td>
                  <td style="border-top: 2px solid #000;text-align:right">{{number_format($total_cargo, 2, '.', '')}}</td>
                  <td style="border-top: 2px solid #000;text-align:right">{{number_format($total_penalidad, 2, '.', '')}}</td>
                  <td style="border-top: 2px solid #000;text-align:right">{{number_format($total_tenencia, 2, '.', '')}}</td>
                  <td style="border-top: 2px solid #000;text-align:right">{{number_format($total_compensatorio, 2, '.', '')}}</td>
                  <td style="border-top: 2px solid #000;text-align:right">{{number_format($total_pagar, 2, '.', '')}}</td>
                  <td style="border-top: 2px solid #000;" colspan="5"></td>
                </tr>
              </tfoot>
            </table>
    </div>
  </main>
</body>
</html>