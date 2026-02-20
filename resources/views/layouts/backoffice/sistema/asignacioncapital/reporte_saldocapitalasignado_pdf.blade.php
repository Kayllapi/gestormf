<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOVIMIENTO Y SALDO DE  CAPITAL ASIGNADO</title>
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
    <div style="float:left;font-size:15px;">{{ $tienda->nombre }} | {{ $agencia->nombreagencia }}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
  </footer>
  <main>
    <div class="container">
      <h4 align="center" style="font-size:15px;">MOVIMIENTO Y SALDO DE  CAPITAL ASIGNADO</h4>
            <div style="float:left;"><b>AGENCIA: </b>{{ $agencia->nombreagencia }}  </div>     
            <div style="margin-left:50px;float:left;"><b>A FECHA: </b>{{ now()->format('d-m-Y H:i:s A') }}</div>    <br>
            
            <table style="width:100%;">
              <thead class="table-dark">
                <tr>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">N° Operación</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Fecha</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Tipo de operación</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Destino de Depósito / Fuente de Retiro</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Monto (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Banco</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">N° operación (banco)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Descripción</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;width:70px;">Usuario Emisor</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;width:70px;">Usuario Rec. Final Efectivo</td>
                </tr>
              </thead>
              <tbody>
            
              <?php  
            $total = 0;
            $total_suma = 0;
            $total_resta = 0;
            $html = '';
            foreach($asignacioncapital as $key => $value){
                
                $style = '';
                $subrayado = '';
                $subrayado_sincolor = '';
                $signo = '';
              
                if($value->idtipooperacion==2 or $value->idtipooperacion==3){
                    $style = 'color:#000;';
                    $subrayado = 'border-bottom: 3px double #000;font-weight: bold;';
                    $subrayado_sincolor = 'border-bottom: 3px double black';
                    $signo = '-';
                }
              
                $fecharegistro = date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A");
                $cuenta = $value->banco!=''?$value->banco.' - ***'.substr($value->cuenta, -5):'';
                $numerooperacion = $value->banco!=''?$value->numerooperacion:'';
                $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data(this)'>
                              <td>{$value->codigoprefijo}{$value->codigo}</td>
                              <td>{$fecharegistro}</td>
                              <td><span style='{$subrayado_sincolor}'>{$value->credito_tipooperacionnombre}</span></td>
                              <td>{$value->credito_tipodestinonombre}</td>
                              <td style='text-align: right;{$style}'><span style='{$subrayado}'>{$signo}{$value->monto}</span></td>
                              <td>{$cuenta}</td>
                              <td>{$numerooperacion}</td>
                              <td>{$value->descripcion}</td>
                              <td>{$value->codigo_responsable}</td>
                              <td>{$value->codigo_responsable_recfinal}</td>
                          </tr>";
                $total = $total+$value->monto;
                if($value->idtipooperacion==1 or $value->idtipooperacion==4){
                    $total_suma = $total_suma+$value->monto;
                }elseif($value->idtipooperacion==2 or $value->idtipooperacion==3){
                    $total_resta = $total_resta+$value->monto;
                }
            }
          
          if(count($asignacioncapital)==0){
              $html.= '<tr><td colspan="10" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }
               
              $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="4" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">Total (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total_suma-$total_resta, 2, '.', '').'</td>
                  <td colspan="5" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;"></td>
                </tr>'; 
            echo $html;
              ?>
              
              </tbody>
            </table>  
                
    </div>
  </main>
</body>
</html>