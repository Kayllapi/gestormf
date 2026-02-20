<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REPORTE DE CONTROL DE APERTURA Y CIERRE DE OPE. DE CAJA</title>
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
    <div style="float:left;font-size:15px;">{{ $tienda->nombre }} | {{ $tienda->nombreagencia }}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
  </footer>
  <main>
    <div class="container">
      <h4 align="center">REPORTE DE CONTROL DE APERTURA Y CIERRE DE OPE. DE CAJA</h4>
           <b>FECHA: {{date_format(date_create($fecha_corte),"d-m-Y")}}</b>
          
              
            
              <?php 
      
      
          $cierre_insitucionaldetalle = DB::table('cierre_insitucionaldetalle')
              ->join('cierre_insitucional','cierre_insitucional.id','cierre_insitucionaldetalle.idcierre_insitucional')
              ->join('users as responsable','responsable.id','cierre_insitucionaldetalle.idresponsable')
              ->join('tienda','tienda.id','cierre_insitucional.idtienda')
              ->where('cierre_insitucional.fechacorte',$fecha_corte)
              ->select(
                  'cierre_insitucionaldetalle.*',
                  'tienda.nombreagencia as nombreagencia',
                  'responsable.nombrecompleto as nombrecompleto_responsable',
                  'responsable.codigo as usuario_responsable',
              )
              ->orderBy('cierre_insitucionaldetalle.id','asc')
              ->get();
          
          $html = '<table style="width:100%;border-bottom: 2px solid #000;">
              <thead>
                <tr>
                  <td rowspan="2" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">N°</td>
                  <td rowspan="2" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Agencia</td>
                  <td colspan="3" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">APERTURA DE CAJA</td>
                  <td colspan="2" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">ARQUEO DE CAJA</td>
                  <td colspan="2" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">CIERRE DE CAJA</td>
                  <td rowspan="2" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Administrador de Agencia  (A. y N.)</td>
                  <td rowspan="2" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Usuario</td>
                </tr>
                <tr>
                  <td style="border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Estado</td>
                  <td style="border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Efectivo (S/.)</td>
                  <td style="border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Fecha y Hora</td>
                  <td style="border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Efectivo (S/.)</td>
                  <td style="border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Fecha y Hora</td>
                  <td style="border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Estado</td>
                  <td style="border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Fecha y Hora</td>
                </tr>
              </thead>
              <tbody>';
          $i = 1;
          foreach($cierre_insitucionaldetalle as $value){

              $html .= '<tr>
                            <td style="text-align:center;">'.$i.'</td>
                            <td>'.$value->nombreagencia.'</td>
                            <td>'.$value->apertura_estado.'</td>
                            <td style="text-align:right;">'.$value->apertura_efectivo.'</td>
                            <td style="text-align:center;">'.date_format(date_create($value->apertura_fecha),"d-m-Y H:i:s A").'</td>
                            <td style="text-align:right;">'.$value->arqueo_efectivo.'</td>
                            <td style="text-align:center;">'.date_format(date_create($value->arqueo_fecha),"d-m-Y H:i:s A").'</td>
                            <td>'.$value->cierre_estado.'</td>
                            <td style="text-align:center;">'.date_format(date_create($value->cierre_fecha),"d-m-Y H:i:s A").'</td>
                            <td>'.$value->nombrecompleto_responsable.'</td>
                            <td>'.$value->usuario_responsable.'</td>
                        </tr>';
             $i++;     
          }
          if(count($cierre_insitucionaldetalle)==0){
              $html.= '<tr><td colspan="11" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }
              $html .= '</tbody>
            </table><br>';
            echo $html;
          ?>
          @if($cierre_insitucional)
           <?php
              $html1 = '<table style="margin-left:350px;">
                <tr>
                  <td><b>REGISTRADO CIERRE DE CAJA - INSTITUCIONAL</b></td>
                </tr>
                <tr>
                  <td>Fecha y Hora: '.date_format(date_create($cierre_insitucional->fecharegistro),"d-m-Y H:i:s A").'</td>
                </tr>
            </table>';
            echo $html1;
              ?>
          <table class="tabla_informativa" style="text-align:left;margin-left:760px;">
              <tr>
                  <td>_____________________________________</td>
              </tr>
              <tr>
                  <td><b>Responsable:</b> {{ strtoupper($cierre_insitucional->nombrecompleto_responsable) }}</td>
              </tr>
              <tr>
                  <td><b>Cargo:</b> {{  strtoupper($cierre_insitucional->nombre_permiso) }}</td>
              </tr>
              <tr>
                  <td><b>Usuario:</b> {{ strtoupper($cierre_insitucional->codigo_responsable) }}</td>
              </tr>
          </table> 
        @endif
    </div>
  </main>
</body>
</html>