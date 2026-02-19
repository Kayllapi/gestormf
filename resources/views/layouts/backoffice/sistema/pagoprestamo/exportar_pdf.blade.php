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
      <h4 align="center">HISTORIAL DE PAGOS DE PRÉSTAMOS</h4>
           <b>De: {{$fecha_inicio}} Al: {{$fecha_fin}}</b>
          
              
            
              <?php  
              $html = '<table style="width:100%;">
              <thead class="table-dark" style="position: sticky;top: 0;">
                <tr>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">N°</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">CLIENTE</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">CUOTAS</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">C. PAGADO</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">ACUENTA</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">INT. COM.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">INT. MORA.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">CUSTODIA</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">CXC</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">TOTAL (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">FECHA</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">F/L. PAGO</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">L. BANCO</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">N° OPERACIÓN</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">RESPONSABLE</td>
                </tr>
              </thead>
              <tbody>';
              
          $total_cuotapagado = 0;
          $total_acuenta = 0;
          $total_interes_compensatorio = 0;
          $total_interes_moratorio = 0;
          $total_custodia = 0;
          $total_cuentaxcobrar = 0;
          $total_total = 0;
          $total_caja = 0;
          $total_banco = 0;
          
          
          foreach($credito_cobranzacuotas as $key => $value){
            
              $credito_adelanto = DB::table('credito_adelanto')->where('credito_adelanto.idcredito_cobranzacuota',$value->id)->get();
              
              $t_cuotapagado = 0;
              $t_acuenta = 0;
              $t_penalidad = 0;
              $t_tenencia = 0;
              $t_compensatorio = 0;
              $t_cuentaxcobrar = 0;
              //$t_total = 0;
            
              foreach($credito_adelanto as $valueadelanto){
                  $credito_cronograma = DB::table('credito_cronograma')->where('credito_cronograma.id',$valueadelanto->idcredito_cronograma)->first();
                  if($credito_cronograma){
                      if($credito_cronograma->idestadocredito_cronograma==2){
                          $t_cuotapagado = $t_cuotapagado+$valueadelanto->total;
                      }else{
                          if($t_cuotapagado>0){
                              $t_acuenta = $t_acuenta+$valueadelanto->total;
                          }else{
                              $t_acuenta = $t_acuenta+$valueadelanto->capital+$valueadelanto->comision+$valueadelanto->cargo+$valueadelanto->interes;
                          }
                      }
                  }
                  $t_penalidad = $t_penalidad+$valueadelanto->penalidad;
                  $t_tenencia = $t_tenencia+$valueadelanto->tenencia;
                  $t_compensatorio = $t_compensatorio+$valueadelanto->compensatorio;
                  //$t_total = $t_total+$valueadelanto->total;
              }
            
              $t_cuotapagado = number_format($t_cuotapagado, 2, '.', '');
              $t_acuenta = number_format($t_acuenta, 2, '.', '');
              $t_penalidad = number_format($t_penalidad, 2, '.', '');
              $t_tenencia = number_format($t_tenencia, 2, '.', '');
              $t_compensatorio = number_format($t_compensatorio, 2, '.', '');
              $t_cuentaxcobrar = number_format($value->cobrar_cargo, 2, '.', '');
              $t_total = number_format($value->total_pagar+$t_cuentaxcobrar, 2, '.', '');
            
              $operacionen1 = '';
              $operacionen2 = '';
              $bgcolor = '';
       
              if($value->idformapago==0){ $operacionen1 = 'TRANSITORIO'; }
              if($value->idformapago==1){ $operacionen1 = 'CAJA'; }
              if($value->idformapago==2){ $operacionen1 = 'BANCO'; }
            
              $coutas = str_replace(',',', ',$value->pago_cuota);
              $num_operacion =  'OP'.str_pad($value->codigo, 10, "0", STR_PAD_LEFT);
              $html .= "<tr id='show_data_select' idcredito_cobranzacuota='{$value->id}'>
                            <td style='height: 20px;'>".($key+1)."</td>
                            <td style='height: 20px;'>{$value->nombrecliente}</td>
                            <td style='height: 20px;'>{$coutas}</td>
                            <td style='text-align:right;height: 20px;'>{$t_cuotapagado}</td>
                            <td style='text-align:right;height: 20px;'>{$t_acuenta}</td>
                            <td style='text-align:right;height: 20px;'>{$t_penalidad}</td>
                            <td style='text-align:right;height: 20px;'>{$t_compensatorio}</td>
                            <td style='text-align:right;height: 20px;'>{$t_tenencia}</td>
                            <td style='text-align:right;height: 20px;'>{$t_cuentaxcobrar}</td>
                            <td style='text-align:right;height: 20px;'>{$t_total}</td>
                            <td style='text-align:center;height: 20px;width: 125px;'>{$value->fecharegistro}</td>
                            <td style='height: 20px;'>{$operacionen1}</td>
                            <td style='height: 20px;'>{$value->banco}</td>
                            <td style='height: 20px;'>{$num_operacion}</td>
                            <td style='height: 20px;'>{$value->usuariocajero}</td>
                        </tr>";
                        
                    
              $total_cuotapagado = $total_cuotapagado+$t_cuotapagado;
              $total_acuenta = $total_acuenta+$t_acuenta;
              $total_interes_compensatorio = $total_interes_compensatorio+$t_penalidad;
              $total_interes_moratorio = $total_interes_moratorio+$t_compensatorio;
              $total_custodia = $total_custodia+$t_tenencia;
              $total_cuentaxcobrar = $total_cuentaxcobrar+$t_cuentaxcobrar;
              $total_total = $total_total+$t_total;
            
              if($value->idformapago==1){
                  $total_caja = $total_caja+$t_total;
              }
              if($value->idformapago==2){
                  $total_banco = $total_banco+$t_total;
              }
          }
          if(count($credito_cobranzacuotas)==0){
              $html.= '<tr><td colspan="13" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }
              $html .= '</tbody><tfoot class="table-dark" style="position: sticky;bottom: 0;">
                <tr>
                  <td colspan="3" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">TOTAL S/.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_cuotapagado, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_acuenta, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_interes_compensatorio, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_interes_moratorio, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_custodia, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_cuentaxcobrar, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_total, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;" colspan="5"></td>
                </tr>
              </tfoot>
            </table><br>
            <table style="width:100%;">
                <tfoot class="table-dark" style="position: sticky;bottom: 0;">
                <tr>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;width:270px;">RESUMEN:</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;width:70px;">CAJA</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;width:70px;">'.number_format($total_caja, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;width:70px;">BANCO</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;width:70px;">'.number_format($total_banco, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;width:70px;">TRANSIT.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;width:70px;">0.00</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;width:70px;"><u>T. EFE. (S/.)</u></td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;width:70px;"><u>'.number_format($total_caja+$total_banco, 2, '.', '').'</u></td>
                  <td style="" colspan="4"></td>
                </tr>
              </tfoot>
            </table>';
            echo $html;
              ?>
                
    </div>
  </main>
</body>
</html>