<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VINCULACIÓN POR RIESGO UNICO</title>
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
    <div style="float:left;font-size:15px;">{{ $tienda->nombre }}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
  </footer>
  <main>
    <div class="container">
      <h4 align="center">VINCULACIÓN POR RIESGO UNICO</h4>
      
           <b>CLIENTE/AVAL: </b>{{ $cliente }}<br>
           <b>DIRECCIÓN DE DOMICILIO: </b>{{ $direcciondomicilio }}<br>
           <b>DIRECCIÓN DE NEGOCIO: </b>{{ $direccionnegocio }}<br><br>
            
            <table style="width:100%;">
              <thead class="table-dark">
                <tr>
                  <td style="border-top: 2px solid #000;text-align: center;font-weight: bold;" rowspan="2" colspan="3">VINCULADOS</td>
                  <td style="border-top: 2px solid #000;text-align: center;font-weight: bold;" colspan="8">RIESGO Saldo de Créd.(S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;" rowspan="3">TOTAL</td>
                </tr> 
                <tr>
                  <td style="border-top: 2px solid #000;text-align: center;font-weight: bold;" colspan="4">POR PROPIEDAD Y AVAL</td>
                  <td style="border-top: 2px solid #000;text-align: center;font-weight: bold;" colspan="2">POR NEGOCIO</td>
                  <td style="border-top: 2px solid #000;text-align: center;font-weight: bold;" colspan="2">FAMILIARES EN LA EMPRESA</td>
                </tr>
                <tr>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">N°</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">DOI/RUC</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">Nombres y Apellidos</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">Cnta</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">Avalados por Cliente al Vinculado con  MISMO DOMICILIO</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">Cnta</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">Avalados por Vinculado al Cliente  con MISMO DOMICILIO </td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">Cnta</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">Misma dirección de negocio del vinculado</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">Cnta</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">Usuario Vinculado con mismo domicilio del Cliente</td>
                </tr>
              </thead>
              <tbody>
            
              <?php  
              $html = '';
              $total = 0;
              foreach($credito_garantias as $key => $value){

                   $total_valor = 0;

                   $credito_garantias_valado = DB::table('credito_garantia')
                      ->join('credito','credito.id','credito_garantia.idcredito')
                      ->join('users as cliente','cliente.id','credito.idcliente')
                      ->where('credito.idestadocredito',1)
                      ->whereIn('credito.estado',['APROBADO','DESEMBOLSADO'])
                      ->where('credito_garantia.idcliente',$value->idcliente)
                      ->where('cliente.direccion',$value->direccioncliente)
                      ->where('credito_garantia.tipo','AVAL')
                      ->select(
                          'credito_garantia.*',
                          'credito.cuenta as cuenta',
                      )
                      ->get();
                  $cuenta_avalado = '';
                  $valor_avalado = '';
                  foreach($credito_garantias_valado as $valueavalado){
                      $cuenta_avalado = $cuenta_avalado.' C'.$valueavalado->cuenta;
                      $valor_avalado = $valor_avalado.' '.$valueavalado->valor_mercado;
                      $total_valor += $valueavalado->valor_mercado;
                  }

                   $credito_garantias_mismadireccion = DB::table('credito_garantia')
                      ->join('credito','credito.id','credito_garantia.idcredito')
                      //->join('users as cliente','cliente.id','credito.idcliente')
                      ->join('s_users_prestamo','s_users_prestamo.id_s_users','credito.idcliente')
                      ->where('credito.idestadocredito',1)
                      ->whereIn('credito.estado',['APROBADO','DESEMBOLSADO'])
                      ->where('s_users_prestamo.direccion_ac_economica','LIKE','%'.$s_users_prestamo->direccionnegocio.'%')
                      ->where('credito_garantia.idcredito',$value->idcredito)
                      //->where('credito_garantia.tipo','AVAL')
                      ->select(
                          'credito_garantia.*',
                          'credito.cuenta as cuenta',
                      )
                      ->get();
            
                  $cuenta_mismadireccion = '';
                  $valor_mismadireccion = '';
                  foreach($credito_garantias_mismadireccion as $valuemismadireccion){
                      $cuenta_mismadireccion = $cuenta_mismadireccion.'C'.$valuemismadireccion->cuenta.'<br>';
                      $valor_mismadireccion = $valor_mismadireccion.($valuemismadireccion->valor_mercado!=''?$valuemismadireccion->valor_mercado:'0.00').'<br>';
                      $total_valor += intval($valuemismadireccion->valor_mercado);
                  }

                   $credito_garantias_direccionusuario = DB::table('users')
                      ->where('users.idtipousuario',1)
                      ->where('users.direccion','LIKE','%'.$value->direccioncliente.'%')
                      ->first();

                  $cuenta_direccionusuario = '';
                  $valor_direccionusuario = '';

                  if($credito_garantias_direccionusuario){
                      $cuenta_direccionusuario = 'C'.$value->cuenta;
                      $valor_direccionusuario = $value->valor_mercado;
                  }
                  $total_valor = number_format($total_valor, 2, '.', '');
                  $html .= "<tr id='show_data_select' idcredito='{$value->id}'>
                                <td>".($key+1)."</td>
                                <td>{$value->identificacioncliente}</td>
                                <td style='width: 200px;'>{$value->nombrecliente}</td>
                                <td style='text-align: center;width: 80px;'>C{$value->cuenta}</td>
                                <td style='text-align: right;'>{$value->valor_mercado}</td>
                                <td style='text-align: center;width: 80px;'>{$cuenta_avalado}</td>
                                <td style='text-align: right;'>{$valor_avalado}</td>
                                <td style='text-align: center;width: 80px;'>{$cuenta_mismadireccion}</td>
                                <td style='text-align: right;'>{$valor_mismadireccion}</td>
                                <td style='text-align: center;width: 80px;'>{$cuenta_direccionusuario}</td>
                                <td style='text-align: right;'>{$valor_direccionusuario}</td>
                                <td style='text-align: right;width: 80px;'>{$total_valor}</td>
                            </tr>";
                  $total += $total_valor;
              }
            
             $credito_garantias_mismadireccions = DB::table('credito_garantia')
                ->join('credito','credito.id','credito_garantia.idcredito')
                ->join('users as cliente','cliente.id','credito.idcliente')
                ->join('s_users_prestamo','s_users_prestamo.id_s_users','credito.idcliente')
                ->where('credito.idestadocredito',1)
                ->whereIn('credito.estado',['APROBADO','DESEMBOLSADO'])
                ->where('s_users_prestamo.direccion_ac_economica','LIKE','%'.$s_users_prestamo->direccionnegocio.'%')
                ->where('cliente.id','<>',$idcliente)
                ->select(
                      'cliente.id as idcliente',
                      'cliente.identificacion as identificacioncliente',
                      'cliente.nombrecompleto as nombrecliente',
                )
                ->distinct()
                ->get();

              foreach($credito_garantias_mismadireccions as $key => $value){

                   $total_valor = 0;


                   $credito_garantias_mismadireccion = DB::table('credito_garantia')
                      ->join('credito','credito.id','credito_garantia.idcredito')
                      ->join('users as cliente','cliente.id','credito.idcliente')
                      ->join('s_users_prestamo','s_users_prestamo.id_s_users','credito.idcliente')
                      ->where('credito.idestadocredito',1)
                      ->whereIn('credito.estado',['APROBADO','DESEMBOLSADO'])
                      ->where('s_users_prestamo.direccion_ac_economica','LIKE','%'.$s_users_prestamo->direccionnegocio.'%')
                      ->where('cliente.id',$value->idcliente)
                      ->select(
                          'credito_garantia.*',
                          'credito.cuenta as cuenta',
                      )
                      ->get();

                  $cuenta_mismadireccion = '';
                  $valor_mismadireccion = '';
                  foreach($credito_garantias_mismadireccion as $valuemismadireccion){
                      $cuenta_mismadireccion = $cuenta_mismadireccion.'C'.$valuemismadireccion->cuenta.'<br>';
                      $valor_mismadireccion = $valor_mismadireccion.($valuemismadireccion->valor_mercado!=''?$valuemismadireccion->valor_mercado:'0.00').'<br>';
                      $total_valor += intval($valuemismadireccion->valor_mercado);
                  }

                  $total_valor = number_format($total_valor, 2, '.', '');
                  $html .= "<tr id='show_data_select'>
                                <td>".($key+1)."</td>
                                <td>{$value->identificacioncliente}</td>
                                <td>{$value->nombrecliente}</td>
                                <td style='text-align: center;width: 80px;'></td>
                                <td style='text-align: right;'></td>
                                <td style='text-align: center;width: 80px;'></td>
                                <td style='text-align: right;'></td>
                                <td style='text-align: center;width: 80px;'>{$cuenta_mismadireccion}</td>
                                <td style='text-align: right;'>{$valor_mismadireccion}</td>
                                <td style='text-align: center;width: 80px;'></td>
                                <td style='text-align: right;'></td>
                                <td style='text-align: right;width: 80px;'>{$total_valor}</td>
                            </tr>";
                  $total += $total_valor;
              }
              $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="11" style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">TOTAL S/.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:right;font-weight: bold;">'.number_format($total, 2, '.', '').'</td>
                </tr>';
                echo $html;
              ?>
              
              </tbody>
            </table>  
                
    </div>
  </main>
</body>
</html>