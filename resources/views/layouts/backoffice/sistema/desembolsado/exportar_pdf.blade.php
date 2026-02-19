<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRÉDITOS DESEMBOLSADOS</title>
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
    <div style="float:left;font-size:15px;">{{ $tienda->nombre }} | {{ $agencia->nombreagencia }}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
  </footer>
  <main>
    <div class="container">
      <h4 align="center">CRÉDITOS DESEMBOLSADOS</h4>
          @if($fecha_inicio!='' && $fecha_fin!='')
           <b>De: {{$fecha_inicio}} Al: {{$fecha_fin}}</b>
          @endif
            
            <table style="width:100%;">
              <thead class="table-dark">
                <tr>
                  <td  style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">N°</td>
                  <td  style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">CLIENTE</td>
                  <td  style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">AVAL</td>
                  <td  style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:10px">DESEMBOLSO</td>
                  <td  style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">CUOTAS</td>
                  <td  style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">F. PAGO</td>
                  <td  style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">F. DESEMBOLSO</td>
                  <td  style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">CAJERO</td>
                  <td  style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">OPERACIÓN EN</td>
                  <td  style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">MODA. CRÉDITO</td>
                  <td  style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">ASESOR</td>
                </tr>
              </thead>
              <tbody>
            
              <?php  
          $html = '';
          $total_desembolsado = 0;
          foreach($creditos as $key => $value){
            
              $credito_formapago = DB::table('credito_formapago')->where('credito_formapago.idcredito',$value->id)->first();
              $operacionen = '';
              if($credito_formapago){
                  if($credito_formapago->idformapago==1){
                      $operacionen = 'CAJA';
                  }elseif($credito_formapago->idformapago==2){
                      $operacionen = 'BANCO';
                  }
              }
            
              $html .= "<tr id='show_data_select' idcredito='{$value->id}'>
                            <td>".($key+1)."</td>
                            <td>{$value->nombrecliente}</td>
                            <td>{$value->nombreaval}</td>
                            <td style='text-align:right;'>{$value->monto_solicitado}</td>
                            <td style='text-align:right;'>{$value->cuotas}</td>
                            <td>{$value->frecuencianombre}</td>
                            <td>{$value->fecha_desembolso}</td>
                            <td>{$value->codigocajero}</td>
                            <td>{$operacionen}</td>
                            <td>{$value->nombremodalidadcredito}</td>
                            <td>{$value->codigoasesor}</td>
                        </tr>";
              $total_desembolsado += $value->monto_solicitado;
          }
          if(count($creditos)==0){
              $html.= '<tr><td colspan="11" style="border-bottom: 2px solid #000;text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }else{
              $html.= '<tr><td colspan="11" style="border-top: 2px solid #000;"></td></tr>';
          }
              $html .= '
                <tr>
                  <td colspan="3" style="border-bottom: 2px solid #000;text-align:right;">TOTAL S/.</td>
                  <td style="border-bottom: 2px solid #000;text-align:right;">'.number_format($total_desembolsado, 2, '.', '').'</td>
                  <td colspan="7" style="border-bottom: 2px solid #000;"></td>
                </tr>';
            echo $html;
              ?>
              
              </tbody>
            </table>  
                
    </div>
  </main>
</body>
</html>