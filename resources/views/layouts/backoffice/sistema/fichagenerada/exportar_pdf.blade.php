<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HISTORIAL DE FICHA DE LIQUIDACIONES</title>
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
      <h4 align="center">HISTORIAL DE FICHA DE LIQUIDACIONES</h4>
           <div align="center">Periodo: {{$fecha_inicio}} Al: {{$fecha_fin}}</div> <br>
        @foreach($creditos as $valuecredito)
           <?php
            $liquidaciongarantiaresponsable = DB::table('users')->whereId($valuecredito->idliquidaciongarantiaresponsable)->first();
            ?>
           <b>Cuenta: C{{$valuecredito->cuenta}}</b> - <b>Responsable: {{$liquidaciongarantiaresponsable->nombrecompleto}}</b> 
            
            <table style="width:100%;" style="border-bottom:2px solid #000">
              <thead class="table-dark">
                <tr>
                  <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">CODIGO DE GARANTIA</th>
                  <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">CLIENTE</th>
                  <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">RUC/DNI/CE</th>
                  <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">TIPO DE GARANTIA</th>
                  <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">DESCRIPCIÓN</th>
                  <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">Serie/Motor/N°Partida</th>
                  <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">MODELO</th>
                  <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">VALOR COMERCIAL</th>
                  <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">V.C. DESCT.</th>
                  <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">COBERTURA</th>
                  <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">P. LIQUID.</th>
                  <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">ACCESORIOS</th>
                  <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">COLOR</th>
                  <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">AÑO DE FABRICACIÓN</th>
                  <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center;font-weight: bold;">PLACA DEL VEHÍCULO</th>
                </tr>
              </thead>
              <tbody>
            
              <?php  
                
           
          
          $credito_garantias = DB::table('credito_garantia')
              ->join('credito','credito.id','credito_garantia.idcredito')
              ->join('users as cliente','cliente.id','credito_garantia.idcliente')
              ->where('credito_garantia.idcredito',$valuecredito->id)
              ->select(
                'credito_garantia.*',
                'cliente.nombrecompleto as clientenombrecompleto',
                'cliente.identificacion as dni'
              )
              ->orderBy('credito_garantia.fecharegistro_listaremate','asc')
              ->get();
                
          $porcentaje_descuento_liquidacion = configuracion($tienda->id,'porcentaje_descuento_liquidacion')['valor'];
                  $total_valorcomercial = 0;
                  $total_descuento = 0;
                  $total_cobertura = 0;
                  $total_precio = 0;
          $html = '';
          foreach($credito_garantias as $key => $value){

              $valor_comercial_descuento = number_format($value->valor_comercial - ($value->valor_comercial * $porcentaje_descuento_liquidacion / 100), 2, '.', '');

              $html .= "<tr id='show_data_select' idcredito='{$value->id}'>
                                <td>".$value->garantias_codigo."</td>
                                <td>".$value->clientenombrecompleto."</td>
                                <td>".$value->dni."</td>
                                <td>".$value->garantias_tipogarantia."</td>
                                <td>".$value->descripcion."</td>
                                <td>".$value->garantias_serie_motor_partida."</td>
                                <td>".$value->garantias_modelo_tipo."</td>
                                <td style='text-align:right;'>".$value->valor_comercial."</td>
                                <td style='text-align:right;'>".number_format($valor_comercial_descuento, 2, '.', '')."</td>
                                <td style='text-align:right;'>".$value->valor_realizacion."</td>
                                <td style='text-align:right;'>".$value->precioliquidacion."</td>
                                <td>".$value->garantias_accesorio_doc."</td>
                                <td>".$value->garantias_color."</td>
                                <td>".$value->garantias_fabricacion."</td>
                                <td>".$value->garantias_placa."</td>
                        </tr>";
                      $total_valorcomercial += $value->valor_comercial;
                      $total_descuento += $valor_comercial_descuento;
                      $total_cobertura += $value->valor_realizacion;
                      $total_precio += $value->precioliquidacion;
          }
          if(count($credito_garantias)==0){
              $html.= '<tr><td colspan="15" style="border-bottom: 2px solid #000;text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }else{
              $html .= '
                
                    <tr>
                        <th style="border-top: 2px solid #000;text-align:right;" colspan="7">TOTAL</th>
                        <th style="border-top: 2px solid #000;text-align:right;">'.number_format($total_valorcomercial, 2, '.', '').'</th>
                        <th style="border-top: 2px solid #000;text-align:right;">'.number_format($total_descuento, 2, '.', '').'</th>
                        <th style="border-top: 2px solid #000;text-align:right;">'.number_format($total_cobertura, 2, '.', '').'</th>
                        <th style="border-top: 2px solid #000;text-align:right;">'.number_format($total_precio, 2, '.', '').'</th>
                        <th style="border-top: 2px solid #000;" colspan="4"></th>
                    </tr>';
          }
            echo $html;
              ?>
      @endforeach          
              </tbody>
            </table>  
                
    </div>
  </main>
</body>
</html>