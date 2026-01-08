<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CUENTAS CON CUOTAS PENDIENTES DE PAGO PARA GESTIÓN COBRANZA</title>
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
          width:70%;
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
      <h4 align="center" style="font-size:16px;">CUENTAS CON CUOTAS PENDIENTES DE PAGO PARA GESTIÓN COBRANZA</h4>
           
           
      
          <div style="height:35px;">
          <div style="width:400px;float:left;"> 
            <b>AGENCIA: </b>{{ $agencia?$agencia->nombreagencia:'TODA LAS AGENCIAS' }}<br>
           <b>ASESOR: </b>{{ $asesor?$asesor->nombrecompleto:'TODO' }}<br>
          </div>
          <div style="width:400px;float:left;"> 
            <b>DÍAS VENCIDOS &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DE: </b>{{ $dias_retencion_desde }}<br>
            <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HASTA: </b>{{ $dias_retencion_hasta }}<br>
          </div>
          </div>
      
            <table style="width:100%;">
              <thead class="table-dark">
                <tr>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">N°</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">GP</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">CUENTA</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">DOI/RUC</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Apellidos y Nombres</td>
                  <!--td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Fecha Desemb.</td-->
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Monto Crédito (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">F. Pago</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center"><span style="text-decoration: underline;font-weight: bold;">Saldo Cuotas Venc. (S/.)</span></td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center"><span style="text-decoration: underline;font-weight: bold;">Días Atraso</span></td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Nro. de Cuotas Cumplido y Venc.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Form. C.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Tele./Celu.</td>
                  <!--td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">F. Compromiso</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Anotación</td-->
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Direc/Domicilio</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Calificación</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Producto</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Modalidad</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">DOI/RUC (Aval)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Ape. Nom. Aval</td>
                </tr>
              </thead>
              <tbody>
            
              <?php  
          $html = '';
          $total_monto_solicitado = 0;
          $total_saldo_pendientepago = 0;
            
          foreach($creditos_ordenado as $key => $value){

              $html .= "<tr>
                  <td >".($key+1)."</td>
                  <td >--</td>
                  <td >C{$value['cuenta']}</td>
                  <td >{$value['identificacioncliente']}</td>
                  <td >{$value['nombrecliente']}</td>
                  <!--td >{$value['fecha_desembolso']}</td-->
                  <td style='text-align: right;'>{$value['monto_solicitado']}</td>
                  <td >{$value['frecuencianombre']}</td>
                  <td style='text-align: right;'>{$value['cuota_vencida']}</td>
                  <td style='text-align: right;'>".$value['ultimo_atraso']."</td>
                  <td style='text-align: right;'>".$value['cuotas']."</td>
                  <td >".$value['cp']."</td>
                  <td >{$value['telefonocliente']}</td>
                  <td >{$value['direccioncliente']}</td>
                  <td >{$value['clasificacion']}</td>
                  <td >{$value['nombreproductocredito']}</td>
                  <td >{$value['nombremodalidadcredito']}</td>
                  <td >{$value['identificacionaval']}</td>
                  <td >{$value['nombreaval']}</td>

              </tr>"; 
            
              $total_monto_solicitado = $total_monto_solicitado+$value['monto_solicitado'];
              $total_saldo_pendientepago = $total_saldo_pendientepago+$value['cuota_vencida'];
          }
          if(count($creditos_ordenado)==0){
              $html.= '<tr><td colspan="23" style="border-bottom: 2px solid #000;text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }else{
              $html.= '<tr><td colspan="23" style="border-top: 2px solid #000;"></td></tr>';
          }
              
              $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="5" style="border-bottom: 2px solid #000;text-align:right;">TOTAL S/.</td>
                  <td style="border-bottom: 2px solid #000;text-align:right;">'.number_format($total_monto_solicitado, 2, '.', '').'</td>
                  <td style="border-bottom: 2px solid #000;text-align:right;"></td>
                  <td style="border-bottom: 2px solid #000;text-align:right;">'.number_format($total_saldo_pendientepago, 2, '.', '').'</td>
                  <td colspan="14" style="border-bottom: 2px solid #000;text-align:right;"></td>
                </tr>';
                
            echo $html;
              ?>
              
              </tbody>
            </table>  
                
    </div>
  </main>
</body>
</html>