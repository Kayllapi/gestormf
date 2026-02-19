<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HISTORIAL DE PAGO DETALLADO DE CRÉDITOS</title>
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
          margin-bottom: 0.7cm;
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
      <h4 align="center">HISTORIAL DE PAGO DETALLADO DE CRÉDITOS</h4>
            <div ><b>AGENCIA: </b>{{ $agencia->nombreagencia }}  </div>
            <div ><b>PERIODO: </b>{{ date_format(date_create($fechainicio),"d-m-Y") }} -  
              <b>AL: </b>{{date_format(date_create($fechafin),"d-m-Y")}}</div>
          <?php  
              $html = '<table style="width:100%;">
              <thead class="table-dark" style="position: sticky;top: 0;">
                <tr>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">N°</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Cuenta</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">T. Cred.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Apellidos y Nombres</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">DOI/RUC</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Fecha/Hora</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Cuotas</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Capital</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Interés</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">C. SS /Desgrav.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Cargo</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">CxC</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Cust.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">I. Comp.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">I. Morat.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Total (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">F/L. PAGO</td>
                </tr>
              </thead>
              <tbody>';
              
          $total_amortizacion = 0;
          $total_interes = 0;
          $total_cargo = 0;
          $total_comision = 0;
          $cobrar_cargo = 0;
          $total_tenencia = 0;
          $total_penalidad = 0;
          $total_compensatorio = 0;
          $total_totalcuota = 0;
      
          $total_caja = 0;
          $total_banco = 0;
          
          
          foreach($credito_cobranzacuotas as $key => $value){
            
              /*$credito_adelanto = DB::table('credito_adelanto')->where('credito_adelanto.idcredito_cobranzacuota',$value->id)->get();
              
              $t_cuotapagado = 0;
              $t_acuenta = 0;
              $t_penalidad = 0;
              $t_tenencia = 0;
              $t_compensatorio = 0;
              $t_cuentaxcobrar = 0;
              $t_capital = 0;
              $t_interes = 0;
              $t_cargo = 0;
              $t_cxc = 0;
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
                  $t_capital = $t_capital+$valueadelanto->capital;
                  $t_interes = $t_interes+$valueadelanto->interes;
                  $t_comision = $t_comision+$valueadelanto->comision;
                  //$t_total = $t_total+$valueadelanto->total;
              }
            
              $t_cuotapagado = number_format($t_cuotapagado, 2, '.', '');
              $t_acuenta = number_format($t_acuenta, 2, '.', '');
              $t_penalidad = number_format($t_penalidad, 2, '.', '');
              $t_tenencia = number_format($t_tenencia, 2, '.', '');
              $t_compensatorio = number_format($t_compensatorio, 2, '.', '');
              $t_cuentaxcobrar = number_format($value->cobrar_cargo, 2, '.', '');
              $t_total = number_format($value->total_pagar+$t_cuentaxcobrar, 2, '.', '');
              $t_capital = number_format($t_capital, 2, '.', '');
              $t_interes = number_format($t_interes, 2, '.', '');
            
              $t_cargo = number_format($value->total_cargo, 2, '.', '');
              $t_cxc = number_format($value->total_comision, 2, '.', '');*/
            
            
              $operacionen1 = '';
       
              if($value->idformapago==0){ $operacionen1 = 'TRANSITORIO'; }
              if($value->idformapago==1){ $operacionen1 = 'CAJA'; }
              if($value->idformapago==2){ $operacionen1 = 'BANCO'; }
            
              $cuotas = str_replace(',',', ',$value->pago_cuota);
              //$num_operacion =  'OP'.str_pad($value->codigo, 10, "0", STR_PAD_LEFT);
            
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
              $fecharegistro = date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A");
              $html .= "<tr id='show_data_select' idcredito_cobranzacuota='{$value->id}'>
                            <td style='height: 20px;'>".($key+1)."</td>
                            <td style='height: 20px;'>C{$value->cuentacredito}</td>
                            <td style='height: 20px;'>{$cp}</td>
                            <td style='height: 20px;'>{$value->nombrecliente}</td>
                            <td style='height: 20px;'>{$value->identificacion}</td>
                            <td style='text-align:center;height: 20px;width: 135px;'>{$fecharegistro}</td>
                            <td style='height: 20px;'>{$cuotas}</td>
                            
                            <td style='text-align:right;height: 20px;'>{$value->total_amortizacion}</td>
                            <td style='text-align:right;height: 20px;'>{$value->total_interes}</td>
                            <td style='text-align:right;height: 20px;'>{$value->total_comision}</td>
                            <td style='text-align:right;height: 20px;'>{$value->total_cargo}</td>
                            <td style='text-align:right;height: 20px;'>{$value->cobrar_cargo}</td>
                            <td style='text-align:right;height: 20px;'>{$value->total_tenencia}</td>
                            <td style='text-align:right;height: 20px;'>{$value->total_penalidad}</td>
                            <td style='text-align:right;height: 20px;'>{$value->total_compensatorio}</td>
                            <td style='text-align:right;height: 20px;'>{$value->total_totalcuota}</td>
                            <td style='height: 20px;'>{$operacionen1}</td>
                        </tr>";
                        
                  
              $total_amortizacion += $value->total_amortizacion;
              $total_interes += $value->total_interes;
              $total_cargo += $value->total_comision;
              $total_comision += $value->total_cargo;
              $cobrar_cargo += $value->cobrar_cargo;
              $total_tenencia += $value->total_tenencia;
              $total_penalidad += $value->total_penalidad;
              $total_compensatorio += $value->total_compensatorio;
              $total_totalcuota += $value->total_totalcuota;  
            
              if($value->idformapago==1){
                  $total_caja = $total_caja+$total_totalcuota;
              }
              if($value->idformapago==2){
                  $total_banco = $total_banco+$total_totalcuota;
              }
          }
          if(count($credito_cobranzacuotas)==0){
              $html.= '<tr><td colspan="16" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }
              $html .= '</tbody><tfoot class="table-dark" style="position: sticky;bottom: 0;">
                <tr>
                  <td colspan="7" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">TOTAL S/.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">'.number_format($total_amortizacion, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">'.number_format($total_interes, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">'.number_format($total_cargo, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">'.number_format($total_comision, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">'.number_format($cobrar_cargo, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">'.number_format($total_tenencia, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">'.number_format($total_penalidad, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">'.number_format($total_compensatorio, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right">'.number_format($total_totalcuota, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;"></td>
                </tr>
              </tfoot>
            </table><br>
            <table style="width:100%;">
                <tfoot class="table-dark" style="position: sticky;bottom: 0;">
                <tr>
                  <td style="width:400px;"></td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;width:70px;">RESUMEN:</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;width:70px;">CAJA</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;width:70px;">'.number_format($total_caja, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;width:70px;">BANCO</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;width:70px;">'.number_format($total_banco, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;width:70px;">TRANSIT.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;width:70px;">0.00</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;width:70px;">T. EFE. (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;width:70px;">'.number_format($total_caja+$total_banco, 2, '.', '').'</td>
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