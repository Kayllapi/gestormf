<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GASTOS ADMINISTRATIVOS Y  OPERATIVOS</title>
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
    <div style="float:left;font-size:18px;">{{ $tienda->ticket_nombre }} | {{ $tienda->nombreagencia }}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
  </footer>
  <main>
    <div class="container">
      <h4 align="center" style="font-size:16px;">GASTOS ADMINISTRATIVOS Y  OPERATIVOS</h4>
           <b>DE: </b>{{ $fechainicio }}<br>
           <b>HASTA: </b>{{ $fechafin }}<br>
            
            <table style="width:100%;">
              <thead class="table-dark">
                <tr>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" rowspan="2" width="10px">N°</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" rowspan="2">Operación</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" rowspan="2">Monto (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" rowspan="2">Fecha de gasto</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" rowspan="2">Descripción</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" colspan="2">Sustento</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" rowspan="2">F. Pago</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" rowspan="2">Banco</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" rowspan="2">Usuario</td>
                </tr>
                <tr>
                  <td style="border-bottom: 2px solid #000;text-align:center">Comprobante</td>
                  <td style="border-bottom: 2px solid #000;text-align:center">N° y Detalle de Comp.</td>
                </tr>
              </thead>
              <tbody>
            
              <?php  
            $total = 0;
            $total_caja = 0;
            $total_banco = 0;
            $html = '';
            foreach($gastoadministrativooperativo as $key => $value){
                $fechapago = date_format(date_create($value->fechapago),"d-m-Y H:i:s A");
                $cuenta = $value->banco!=''?$value->banco.' - ***'.substr($value->cuenta, -5).' ('.$value->numerooperacion.')':'';
                $html .= "<tr>
                              <td>".($key+1)."</td>
                              <td>{$value->codigoprefijo}{$value->codigo}</td>
                              <td style='text-align: right;'>{$value->monto}</td>
                              <td>{$fechapago}</td>
                              <td>{$value->descripcion}</td>
                              <td>{$value->s_sustento_comprobantenombre}</td>
                              <td>{$value->sustento_descripcion}</td>
                              <td>{$value->credito_tipoformapagonombre}</td>
                              <td>{$cuenta}</td>
                              <td>{$value->codigo_responsable}</td>
                          </tr>";
                $total = $total+$value->monto;
                if($value->idformapago==1){
                    $total_caja = $total_caja+$value->monto;
                }elseif($value->idformapago==2){
                    $total_banco = $total_banco+$value->monto;
                }
            }
          
          if(count($gastoadministrativooperativo)==0){
              $html.= '<tr><td colspan="10" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }
              $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="2" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;">Total (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;">'.number_format($total, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;">Caja (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;">'.number_format($total_caja, 2, '.', '').'</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;">Banco (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;">'.number_format($total_banco, 2, '.', '').'</td>
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