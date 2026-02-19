<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CONSOLIDADO DE CARTERA DE CRÉDITOS POR CLASIFICACIÓN</title>
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
    <div style="float:left;font-size:15px;">{{ $tienda->nombre }} @if($agencia)| {{ $agencia->nombreagencia }} @else | TODA LAS AGENCIAS @endif</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
  </footer>
  <main>
    <div class="container">
      <h4 align="center">CONSOLIDADO DE CARTERA DE CRÉDITOS</h4>
           <b>AGENCIA: </b>{{ $agencia?$agencia->nombreagencia:'TODA LAS AGENCIAS' }}<br>
           <b>FORMA DE CRÉDITO: </b>{{ $idformacredito!=0?$idformacredito:'TODO' }}<br>
           <b>EJECUTIVO: </b>{{ $asesor?$asesor->usuario:'TODO' }}<br>
           <b>FECHA DE CORTE: </b> {{$fecha_inicio}}
            
            
              <?php  
          $html = '';
          $total_cartera = 0;
          $total_num_creditos = 0;
          $total_mora_soles = 0;
          $total_mora_porcentaje = 0;
          $total_numero_moracredito = 0;
          
          $clasificacion_normal_cantidad = 0;
          $clasificacion_normal_saldo = 0;
          $clasificacion_normal_creditos = 0;
          
          $clasificacion_cpp_cantidad = 0;
          $clasificacion_cpp_saldo = 0;
          $clasificacion_cpp_creditos = 0;
          
          $clasificacion_deficiente_cantidad = 0;
          $clasificacion_deficiente_saldo = 0;
          $clasificacion_deficiente_creditos = 0;
          
          $clasificacion_dudoso_cantidad = 0;
          $clasificacion_dudoso_saldo = 0;
          $clasificacion_dudoso_creditos = 0;
          
          $clasificacion_perdida_cantidad = 0;
          $clasificacion_perdida_saldo = 0;
          $clasificacion_perdida_creditos = 0;
          
          $total_saldos = 0;
          $total_creditos = 0;
          
          foreach($asesores_credito as $valueasesor){
              $creditos = DB::table('credito')
                  ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                  ->where('credito.estado','DESEMBOLSADO')
                  ->where('credito.idestadocredito',1)
                  ->where('credito.saldo_pendientepago','>',0)
                  ->where($where)
                  ->select(
                      'credito.*',
                      'credito_prendatario.modalidad as modalidadproductocredito',
                  )
                  ->orderBy('credito.fecha_desembolso','asc')
                  ->get();
              $cartera = 0;
              $num_creditos = 0;
              $total_moracredito = 0;
              //$mora_porcentaje = 0;
              $numero_moracredito = 0;
              foreach($creditos as $key => $value){
                  $cronograma = select_cronograma(
                      $value->idtienda,
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
                
                
                  $cartera += $value->saldo_pendientepago;
                  $num_creditos++;
                  //$mora_porcentaje = number_format($cronograma['total_moracredito']/$cartera*100, 2, '.', '');
                  if($cronograma['numero_moracredito']>0){
                      $numero_moracredito ++;
                      $total_moracredito += $value->saldo_pendientepago;
                  }
                
                  if($cronograma['ultimo_atraso']<=8){
                      $clasificacion_normal_cantidad++;
                      $clasificacion_normal_saldo += $value->saldo_pendientepago;
                      $clasificacion_normal_creditos++;

                      $total_saldos += $value->saldo_pendientepago;
                      $total_creditos ++;
                  }
                  elseif($cronograma['ultimo_atraso']>8 && $cronograma['ultimo_atraso']<=30){
                      $clasificacion_cpp_cantidad++;
                      $clasificacion_cpp_saldo += $value->saldo_pendientepago;
                      $clasificacion_cpp_creditos++;

                      $total_saldos += $value->saldo_pendientepago;
                      $total_creditos ++;
                  }
                  elseif($cronograma['ultimo_atraso']>30 && $cronograma['ultimo_atraso']<=60){
                      $clasificacion_deficiente_cantidad++;
                      $clasificacion_deficiente_saldo += $value->saldo_pendientepago;
                      $clasificacion_deficiente_creditos++;

                      $total_saldos += $value->saldo_pendientepago;
                      $total_creditos ++;
                  }
                  elseif($cronograma['ultimo_atraso']>60 && $cronograma['ultimo_atraso']<=120){
                      $clasificacion_dudoso_cantidad++;
                      $clasificacion_dudoso_saldo += $value->saldo_pendientepago;
                      $clasificacion_dudoso_creditos++;

                      $total_saldos += $value->saldo_pendientepago;
                      $total_creditos ++;
                  }
                  elseif($cronograma['ultimo_atraso']>120){
                      $clasificacion_perdida_cantidad++;
                      $clasificacion_perdida_saldo += $value->saldo_pendientepago;
                      $clasificacion_perdida_creditos++;

                      $total_saldos += $value->saldo_pendientepago;
                      $total_creditos ++;
                  }
              }
            
              $total_moracredito = number_format($total_moracredito, 2, '.', '');
              $mora_porcentaje = number_format($total_moracredito/$cartera*100, 2, '.', '');
              
              $html .= "<tr>
                            <td>{$valueasesor->codigoasesor}</td>
                            <td style='text-align:right;'>{$cartera}</td>
                            <td style='text-align:right;'>{$num_creditos}</td>
                            <td style='text-align:right;'>{$total_moracredito}</td>
                            <td style='text-align:right;'>{$mora_porcentaje}</td>
                            <td style='text-align:right;'>{$numero_moracredito}</td>
                        </tr>";
              $total_cartera = $total_cartera+$cartera;
              $total_num_creditos = $total_num_creditos+$num_creditos;
              $total_mora_soles = $total_mora_soles+$total_moracredito;
              $total_mora_porcentaje = $total_mora_porcentaje+$mora_porcentaje;
              $total_numero_moracredito = $total_numero_moracredito+$numero_moracredito;
          }
              $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">TOTAL S/.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_cartera, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.$total_num_creditos.'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_mora_soles, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_mora_porcentaje, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.$total_numero_moracredito.'</td>
                </tr>';
          
              
              $html1 = "<tr>
                            <td><b>NORMAL (0)</b></td>
                            <td style='text-align:right;'>{$clasificacion_normal_saldo}</td>
                            <td style='text-align:right;width:100px'>{$clasificacion_normal_creditos}</td>
                        </tr><tr>
                            <td><b>CPP (1)</b></td>
                            <td style='text-align:right;'>{$clasificacion_cpp_saldo}</td>
                            <td style='text-align:right;'>{$clasificacion_cpp_creditos}</td>
                        </tr><tr>
                            <td><b>DEFICIENTE (2)</b></td>
                            <td style='text-align:right;'>{$clasificacion_deficiente_saldo}</td>
                            <td style='text-align:right;'>{$clasificacion_deficiente_creditos}</td>
                        </tr><tr>
                            <td><b>DUDOSO (3)</b></td>
                            <td style='text-align:right;'>{$clasificacion_dudoso_saldo}</td>
                            <td style='text-align:right;'>{$clasificacion_dudoso_creditos}</td>
                        </tr><tr>
                            <td><b>PÉRDIDA (4)</b></td>
                            <td style='text-align:right;'>{$clasificacion_perdida_saldo}</td>
                            <td style='text-align:right;'>{$clasificacion_perdida_creditos}</td>
                        </tr><tr>
                            <td style='border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;'><b>TOTAL</b></td>
                            <td style='border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;'>{$total_saldos}</td>
                            <td style='border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;'>{$total_creditos}</td>
                        </tr>";
          
              $demora1 = 0;
              $demora2 = 0;
              $demora3 = 0;
              if($total_saldos>0){
              $demora1 = number_format(($clasificacion_cpp_saldo+$clasificacion_deficiente_saldo+$clasificacion_dudoso_saldo+$clasificacion_perdida_saldo)/$total_saldos*100, 2, '.', '');
              $demora2 = number_format(($clasificacion_deficiente_saldo+$clasificacion_dudoso_saldo+$clasificacion_perdida_saldo)/$total_saldos*100, 2, '.', '');
              $demora3 = number_format(($clasificacion_dudoso_saldo+$clasificacion_perdida_saldo)/$total_saldos*100, 2, '.', '');
              }
          
              $html2 = "<tr>
                            <td style='text-align:right;'>{$demora1}</td>
                            <td style='text-align:right;width:100px'>(1,2,3,4)</td>
                        </tr><tr>
                            <td style='text-align:right;'>{$demora2}</td>
                            <td style='text-align:right;'>(2,3,4) </td>
                        </tr><tr>
                            <td style='text-align:right;border-bottom: 2px solid #000;'>{$demora3}</td>
                            <td style='text-align:right;border-bottom: 2px solid #000;'>(3,4)</td>
                        </tr>";
               
              ?>
              <br>
              <br>
            <table class="table table-bordered"  style="margin:auto;margin-bottom: 10px;">
              <tbody>
                <tr>
                  <td style='text-align:center;background-color: #E8E585 !important;font-weight: bold;'>(Días de Mora > {{configuracion($tienda->id,'dias_tolerancia_garantia')['valor']}} días)</td>
                </tr>
              </tbody>
            </table>
            <table style="width:100%;">
              <thead class="table-dark">
                <tr>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center"><b>Asesor/ejecutivo</b></td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center"><b>Cartera (S/.)</b></td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center"><b>N° de Créditos</b></td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center"><b>En Mora (S/.)</b></td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center"><b>% de Mora</b></td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center"><b>N° de Cred. En Mora</b></td>
                </tr>
              </thead>
              <tbody>
                <?php  echo $html; ?>
              </tbody>
            </table>  
              <br>
      <h4 align="center">CONSOLIDADO DE CARTERA DE CRÉDITOS POR CLASIFICACIÓN REGULAR</h4>
            <div>
                <div style="width:57%;float:left;">
                    <table style="width:100%;">
                      <thead class="table-dark">
                        <tr>
                          <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center"><b>CLASIFICACIÓN</b></td>
                          <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center"><b>SALDO</td>
                          <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center"><b>N° DE CRÉDITOS</b></td>
                        </tr>
                      </thead>
                      <tbody>
                        <?php  echo $html1; ?>
                      </tbody>
                    </table> 
                </div>
                <div style="width:40%;float:right;">
                    <table style="width:100%;">
                      <thead class="table-dark"> 
                        <tr>
                          <td colspan="2" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center"><b>INDICE DE MORA REGULAR</b></td>
                        </tr>
                        <tr>
                          <td style="border-bottom: 2px solid #000;text-align:center"><b>% de Mora</b></td>
                          <td style="border-bottom: 2px solid #000;text-align:center"><b>Clasificación Consid.</b></td>
                        </tr>
                      </thead>
                      <tbody>
                        <?php  echo $html2; ?>
                      </tbody>
                    </table>
                </div>
            </div>
    </div>
  </main>
</body>
</html>