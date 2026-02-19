<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CARTERA DE CRÉDITO</title>
    <style>
      *{
        font-family:helvetica;
        font-size:11px;
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
          font-size:11px;
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
    <div style="float:left;font-size:18px;">{{ $tienda->nombre }} @if($agencia)| {{ $agencia->nombreagencia }} @else | TODA LAS AGENCIAS @endif</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
  </footer>
  <main>
    <div class="container">
      <h4 align="center">CARTERA DE CRÉDITO</h4>
          @if($fecha_inicio!='' && $fecha_fin!='')
           <b>AGENCIA: </b>{{ $agencia?$agencia->nombreagencia:'TODA LAS AGENCIAS' }}<br>
           <b>FORMA DE CRÉDITO: </b>{{ $idformacredito!=0?$idformacredito:'TODO' }}<br>
           <b>EJECUTIVO: </b>{{ $asesor?$asesor->usuario:'TODO' }}<br>
           <b>FECHA DE CORTE: {{$fecha_inicio}}</b>
          @endif
            
            <table style="width:100%;">
              <thead class="table-dark">
                <tr>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">N°</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">CUENTA</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">RUC/DNI/CE</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Apellidos y Nombres</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">RUC/DNI/CE (Aval)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Ape. Nom. Aval</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Fecha Desemb.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">MONTO (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Saldo C. (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Saldo Deuda T. (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">F. Pago</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Cuotas</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">F.C.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Días de atraso</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Calificación</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Producto</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Modalidad/Tel.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Direc/Domicilio</td>
                </tr>
              </thead>
              <tbody>
            
              <?php  
          $html = '';
          $total_desembolsado = 0;
          $total_saldo = 0;
          $total_deuda = 0;
            
          foreach($creditos as $key => $value){

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
            
              $cronograma = select_cronograma(
                  $tienda->id,
                  $value->id,
                  $value->idforma_credito,
                  $value->modalidadproductocredito,
                  $value->cuotas,
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
            
              $clasificacion = '';
            
              if($cronograma['ultimo_atraso']<=8){
                  $clasificacion = 'NORMAL';
              }
              elseif($cronograma['ultimo_atraso']>8 && $cronograma['ultimo_atraso']<=30){
                  $clasificacion = 'CPP';
              }
              elseif($cronograma['ultimo_atraso']>30 && $cronograma['ultimo_atraso']<=60){
                  $clasificacion = 'DIFICIENTE';
              }
              elseif($cronograma['ultimo_atraso']>60 && $cronograma['ultimo_atraso']<=120){
                  $clasificacion = 'DUDOSO';
              }
              elseif($cronograma['ultimo_atraso']>120){
                  $clasificacion = 'PÉRDIDA';
              }
              
              $html .= "<tr id='show_data_select' idcredito='{$value->id}'>
                            <td>".($key+1)."</td>
                            <td>C{$value->cuenta}</td>
                            <td>{$value->identificacioncliente}</td>
                            <td>{$value->nombrecliente}</td>
                            <td>{$value->avalidentificacion}</td>
                            <td>{$value->avalnombrecompleto}</td>
                            <td>{$value->fecha_desembolso}</td>
                            <td style='text-align:right;'>{$value->monto_solicitado}</td>
                            <td style='text-align:right;'>{$value->saldo_pendientepago}</td>
                            <td style='text-align:right;'>{$cronograma['cuota_pendiente']}</td>
                            <td>{$value->frecuencianombre}</td>
                            <td style='text-align:right;'>{$value->cuotas}</td>
                            <td>$cp</td>
                            <td>{$cronograma['ultimo_atraso']}</td>
                            <td>{$clasificacion}</td>
                            <td>{$value->nombreproductocredito}</td>
                            <td>{$value->nombremodalidadcredito}<br>{$value->telefonocliente}</td>
                            <td>{$value->direccioncliente}, {$value->ubigeonombre}</td>
                        </tr>";
              $total_desembolsado += $value->monto_solicitado;
              $total_saldo += $value->saldo_pendientepago;
              $total_deuda += $value->total_pendientepago;
          }
          if(count($creditos)==0){
              $html.= '<tr><td colspan="19" style="border-bottom: 2px solid #000;text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }else{
              $html.= '<tr><td colspan="19" style="border-top: 2px solid #000;"></td></tr>';
          }
              $html .= '
                <tr>
                  <td colspan="7" style="border-bottom: 2px solid #000;text-align:right;">TOTAL S/.</td>
                  <td style="border-bottom: 2px solid #000;text-align:right;">'.number_format($total_desembolsado, 2, '.', '').'</td>
                  <td style="border-bottom: 2px solid #000;text-align:right;">'.number_format($total_saldo, 2, '.', '').'</td>
                  <td style="border-bottom: 2px solid #000;text-align:right;">'.number_format($total_deuda, 2, '.', '').'</td>
                  <td colspan="8" style="border-bottom: 2px solid #000;"></td>
                </tr>';
            echo $html;
              ?>
              
              </tbody>
            </table>  
                
    </div>
  </main>
</body>
</html>