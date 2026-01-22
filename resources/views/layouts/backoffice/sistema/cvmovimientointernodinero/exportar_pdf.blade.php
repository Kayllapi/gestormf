<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPERACIONES DE MOVIMIENTO INTERNO DE EFECTIVO</title>
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
          margin-bottom: 0.7cm;
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
    <div style="float:left;font-size:18px;">{{ $tienda->ticket_nombre }} | {{ $agencia->nombreagencia }}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
  </footer>
  <main>
    <div class="container">
      <h4 align="center" style="font-size:16px;margin-bottom:10px;">OPERACIONES DE MOVIMIENTO INTERNO DE EFECTIVO</h4>
      <div style="width:100%; height: 20px;">
            <div style="margin-left:265px;float:left;font-size: 13px;"><b>AGENCIA: </b>{{ $agencia->nombreagencia }}  </div>     
            <div style="margin-left:50px;float:left;font-size: 13px;"><b>FECHA INICIO: </b>{{ date_format(date_create($fechainicio),'d/m/Y') }}</div>   
            <div style="margin-left:50px;float:left;font-size: 13px;"><b>FECHA FIN: </b>{{ date_format(date_create($fechafin),'d/m/Y') }}</div> 
      </div>
      <h4 align="center" style="font-size:16px;margin-top:30px;">HABILITACIÓN Y GESTIÓN DE LIQUIDEZ ( I )</h4>
      <h4 align="center" style="font-size:16px;margin-bottom:0px;">RETIRO</h4>
            <table style="width:100%;">
              <thead class="table-dark">
                <tr>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:85px;">Código</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:200px;">Fuente de Retiro</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:50px;">Monto</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:130px;">Banco</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">N° operación (banco)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Descripción</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:130px;">Fecha</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:60px;">Usuario</td>
                </tr>
              </thead>
              <tbody>
            
              <?php  
            $total = 0;
            $html = '';
            foreach($movimientointernodinero_retiro1 as $key => $value){
                $fecharegistro = date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A");
                $cuenta = $value->banco!=''?$value->banco.' - ***'.substr($value->cuenta, -5):'';
                $numerooperacion = $value->banco!=''?$value->numerooperacion:'';
                $html .= "<tr>
                              <td style='white-space: nowrap;'>{$value->codigoprefijo}{$value->codigo}</td>
                              <td style='white-space: nowrap;'>{$value->credito_fuenteretironombre}</td>
                              <td style='white-space: nowrap;text-align:right;'>{$value->monto}</td>
                              <td style='white-space: nowrap;'>{$cuenta}</td>
                              <td style='white-space: nowrap;'>{$numerooperacion}</td>
                              <td style='white-space: nowrap;'>{$value->descripcion}</td>
                              <td style='white-space: nowrap;'>{$fecharegistro}</td>
                              <td style='white-space: nowrap;'>{$value->codigo_responsable}</td>
                          </tr>";
                $total = $total+$value->monto;
            }
          
            if(count($movimientointernodinero_retiro1)==0){
                $html.= '<tr><td colspan="8" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
            }
               
            $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="2" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;">Total Retiros (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;">'.number_format($total, 2, '.', '').'</td>
                  <td colspan="5" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;"></td>
                </tr>'; 

            echo $html;
              ?>
              
              </tbody>
            </table>  
      <h4 align="center" style="font-size:16px;margin-bottom:0px;">DEPÓSITO</h4>
            <table style="width:100%;">
              <thead class="table-dark">
                <tr>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:85px;">Código</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:200px;">Destino de Depósito</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:50px;">Monto</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:130px;">Banco</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">N° operación (banco)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Descripción</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:130px;">Fecha</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:60px;">Usuario</td>
                </tr>
              </thead>
              <tbody>
            
              <?php  
            $total = 0;
            $html = '';
            foreach($movimientointernodinero_deposito1 as $key => $value){
                $fecharegistro = date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A");
                $cuenta = $value->banco!=''?$value->banco.' - ***'.substr($value->cuenta, -5):'';
                $numerooperacion = $value->banco!=''?$value->numerooperacion:'';
                $html .= "<tr>
                              <td style='white-space: nowrap;'>{$value->codigoprefijo}{$value->codigo}</td>
                              <td style='white-space: nowrap;'>{$value->credito_fuenteretironombre}</td>
                              <td style='white-space: nowrap;text-align:right;'>{$value->monto}</td>
                              <td style='white-space: nowrap;'>{$cuenta}</td>
                              <td style='white-space: nowrap;'>{$numerooperacion}</td>
                              <td style='white-space: nowrap;'>{$value->descripcion}</td>
                              <td style='white-space: nowrap;'>{$fecharegistro}</td>
                              <td style='white-space: nowrap;'>{$value->codigo_responsable}</td>
                          </tr>";
                $total = $total+$value->monto;
            }
          
            if(count($movimientointernodinero_deposito1)==0){
                $html.= '<tr><td colspan="8" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
            }
               
            $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="2" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;">Total Depósitos (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;">'.number_format($total, 2, '.', '').'</td>
                  <td colspan="5" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;"></td>
                </tr>'; 

            echo $html;
              ?>
              
              </tbody>
            </table>  
      <h4 align="center" style="font-size:16px;">CIERRE Y APERTURA DE CAJA</h4>
      <h4 align="center" style="font-size:16px;margin-bottom:0px;">RETIRO</h4>
              
            <table style="width:100%;">
              <thead class="table-dark">
                <tr>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:85px;">Código</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:200px;">Fuente de Retiro</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:50px;">Monto</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Descripción</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:130px;">Fecha</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:60px;">Usuario</td>
                </tr>
              </thead>
              <tbody>
            
              <?php  
                
            $total = 0;
            $html = '';
            foreach($movimientointernodinero_retiro3 as $key => $value){
                $fecharegistro = date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A");
                $html .= "<tr>
                              <td style='white-space: nowrap;'>{$value->codigoprefijo}{$value->codigo}</td>
                              <td style='white-space: nowrap;'>{$value->credito_fuenteretironombre}</td>
                              <td style='white-space: nowrap;text-align:right;'>{$value->monto}</td>
                              <td style='white-space: nowrap;'>{$value->descripcion}</td>
                              <td style='white-space: nowrap;'>{$fecharegistro}</td>
                              <td style='white-space: nowrap;'>{$value->codigo_responsable}</td>
                          </tr>";
                $total = $total+$value->monto;
            }
          
            if(count($movimientointernodinero_retiro3)==0){
                $html.= '<tr><td colspan="8" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
            }
               
            $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="2" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;">Total Retiros (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;">'.number_format($total, 2, '.', '').'</td>
                  <td colspan="3" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;"></td>
                </tr>'; 

            echo $html;
              ?>
              
              </tbody>
            </table>   
      <h4 align="center" style="font-size:16px;margin-bottom:0px;">DEPÓSITO</h4> 
            <table style="width:100%;">
              <thead class="table-dark">
                <tr>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:85px;">Código</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:200px;">Destino de Depósito</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:50px;">Monto</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Descripción</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:130px;">Fecha</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;width:60px;">Usuario</td>
                </tr>
              </thead>
              <tbody>
            
              <?php  
                
            $total = 0;
            $html = '';
            foreach($movimientointernodinero_deposito3 as $key => $value){
                $fecharegistro = date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A");
                $html .= "<tr>
                              <td style='white-space: nowrap;'>{$value->codigoprefijo}{$value->codigo}</td>
                              <td style='white-space: nowrap;'>{$value->credito_fuenteretironombre}</td>
                              <td style='white-space: nowrap;text-align:right;'>{$value->monto}</td>
                              <td style='white-space: nowrap;'>{$value->descripcion}</td>
                              <td style='white-space: nowrap;'>{$fecharegistro}</td>
                              <td style='white-space: nowrap;'>{$value->codigo_responsable}</td>
                          </tr>";
                $total = $total+$value->monto;
            }
          
            if(count($movimientointernodinero_deposito3)==0){
                $html.= '<tr><td colspan="8" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
            }
               
            $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="2" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;">Total Depósitos (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;">'.number_format($total, 2, '.', '').'</td>
                  <td colspan="3" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;"></td>
                </tr>'; 

            echo $html;
              ?>
              
              </tbody>
            </table>   
    </div>
  </main>
</body>
</html>