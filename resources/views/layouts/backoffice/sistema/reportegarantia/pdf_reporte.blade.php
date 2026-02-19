<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GARANTÍAS (PRENDARIA Y REGULAR) CON CRÉDITOS VIGENTES Y PRENDARIAS CANCELADOS POR ENTREGAR</title>
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
          font-size:16px !important;
          font-weight: bold;
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
    <div style="float:left;font-size:18px;">{{ $tienda->nombre }} | {{ $agencia->nombreagencia }}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
  </footer>
  {{-- @include('app/nuevosistema/cabecerapdf_a4') --}}
  <main>
    <div class="container">
      <h4 align="center">GARANTÍAS (PRENDARIA Y REGULAR) CON CRÉDITOS VIGENTES Y PRENDARIAS CANCELADOS POR ENTREGAR</h4>
          <div style="height:35px;">
          <div style="width:400px;float:left;"> 
            <div ><b>AGENCIA: </b>{{ $agencia->nombreagencia }}  </div>
            <div ><b>FECHA Y HORA: </b>{{ now()->format('d-m-Y h:i:s A') }}  </div>
          </div>
          <div style="width:400px;float:left;"> 
            <div><b>MODALIDAD: </b>
                @if($idmodalidad==1)
                    GARANTÍA PRENDARIA
                @elseif($idmodalidad==2)
                    GARANTÍA REGULAR
                @else
                    TODO
                @endif
            </div>
            <div><b>ASESOR/EJEC.: </b>{{ $asesor?$asesor->nombrecompleto:'TODO' }}</div>
          </div>
          </div>
          <?php  
              $html = '<table style="width:100%;border-bottom: 2px solid #000;">
              <thead class="table-dark">
                <tr>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">N°</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Cuenta</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Saldo de Deuda (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">T. Cred.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Apellidos y Nombres</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">RUC/DNI/CE</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Cnta. Propio/Aval</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Cod. Garantía</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Garantía</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Serie</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Placa</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Estado</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Acc./Doc.Original</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Detalle G.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">V. Cobertura (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">V. Comercial (S/.)</td>
                </tr>
              </thead>
              <tbody>';
          
          $i = 1;
          foreach($credito_garantias as $key => $value){
        
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
            
              $estado = '';
              if($value->garantias_idestado_garantia==1){
                  $estado = 'Usada';
              }
              elseif($value->garantias_idestado_garantia==2){
                  $estado = 'Seminueva';
              }
              elseif($value->garantias_idestado_garantia==3){
                  $estado = 'Nueva';
              }
              
              $propioavalado = '';
              $clientenombrecompleto = '';
              $clienteidentificacion = '';
              if($value->tipo=='CLIENTE'){
                  $propioavalado = 'Propio';
                  $clientenombrecompleto = $value->clientenombrecompleto;
                  $clienteidentificacion = $value->clienteidentificacion;
              }
              elseif($value->tipo=='AVAL'){
                  $propioavalado = 'Avalado';
                  $clientenombrecompleto = $value->avalnombrecompleto;
                  $clienteidentificacion = $value->avalidentificacion;
              }
            
              $cronograma = select_cronograma(
                  $value->idtienda,
                  $value->idcredito,
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
            
              $saldodeuda = '';
              $saldodeuda_valid = 0;
            
              if($cronograma['cuota_pendiente']>0){
                  $saldodeuda = $cronograma['cuota_pendiente'];
                  $saldodeuda_valid = 1;
              }else{
                  if($value->idestadoentrega==1 && $value->idforma_credito==1){
                      $saldodeuda = 'X Entregar';
                      $saldodeuda_valid = 1;
                  }
                  elseif($value->idestadoentrega==2 && $value->idforma_credito==1){
                      $saldodeuda = 'Entregado';
                  }
              }
              // descuento cuota
              /*$credito_descuentocuotas = DB::table('credito_descuentocuota')
                    ->where('credito_descuentocuota.idcredito',$value->idcredito)
                    ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                    ->first();
              $total_descuento_capital = 0; 
              $total_descuento_interes = 0; 
              $total_descuento_comision = 0; 
              $total_descuento_cargo = 0;  
              $total_descuento_penalidad = 0; 
              $total_descuento_tenencia = 0; 
              $total_descuento_compensatorio = 0; 
              $total_descuento_total = 0; 
              if($credito_descuentocuotas){
                  if($request->numerocuota>=$credito_descuentocuotas->numerocuota_fin){
                      $total_descuento_capital = $credito_descuentocuotas->capital;
                      $total_descuento_interes = $credito_descuentocuotas->interes;
                      $total_descuento_comision = $credito_descuentocuotas->comision;
                      $total_descuento_cargo = $credito_descuentocuotas->cargo;
                      $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                      $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                      $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
                      $total_descuento_total = $credito_descuentocuotas->total;
                  }
              }
            
              $cronograma = select_cronograma(
                  $value->idtienda,
                  $value->idcredito,
                  $value->idforma_credito,
                  $value->modalidadproductocredito,
                  $value->cuotas,
                  $total_descuento_capital,
                  $total_descuento_interes,
                  $total_descuento_comision,
                  $total_descuento_cargo,
                  $total_descuento_penalidad,
                  $total_descuento_tenencia,
                  $total_descuento_compensatorio,
                  0,
                  1,
                  'detalle_cobranza'
              );*/
            
              if($saldodeuda_valid==1){
            
                  $html .= "<tr id='show_data_select' idcredito_cobranzacuota='{$value->id}'>
                            <td>".($i)."</td>
                            <td>C{$value->cuentacredito}</td>
                            <td style='text-align:right'>{$saldodeuda}</td>
                            <td>{$cp}</td>
                            <td>{$clientenombrecompleto}</td>
                            <td>{$clienteidentificacion}</td>
                            <td>{$propioavalado}</td>
                            <td>{$value->garantias_codigo}</td>
                            <td>{$value->descripcion}</td>
                            <td>{$value->garantias_serie_motor_partida}</td>
                            <td>{$value->garantias_placa}</td>
                            <td>{$estado}</td>
                            <td>{$value->garantias_accesorio_doc}</td>
                            <td>{$value->garantias_detalle_garantia}</td>
                            <td style='text-align:right'>{$value->garantias_cobertura}</td>
                            <td style='text-align:right'>{$value->garantias_valorcomercial}</td>
                        </tr>";
                $i++;
              }
          }
          if(count($credito_garantias)==0){
              $html.= '<tr><td colspan="16" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }
              $html .= '</tbody>
            </table>';
            echo $html;
              ?>
                
    </div>
  </main>
</body>
</html>