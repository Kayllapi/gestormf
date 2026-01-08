<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CARTERA DE CLIENTE</title>
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
      <h4 align="center">CARTERA DE CLIENTE</h4>
           <b>AGENCIA: </b>{{ $agencia?$agencia->nombreagencia:'TODA LAS AGENCIAS' }}<br>
           <b>F. CRÉDITO: </b>{{ $idformacredito?$idformacredito:'TODO' }}<br>
           <b>EJECUTIVO: </b>{{ $asesor?$asesor->usuario:'TODO' }}<br>
            
            <table style="width:100%;">
              <thead class="table-dark">
                <tr>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">N°</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Cod. Cliente</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">DOC</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Nombre</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Ejec. Origen</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Saldo C. Ult. Desemb. (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">F. Pago</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Cuotas</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Form. C.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Producto</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Fecha Cancelación</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Telefóno</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Direc/Domicilio</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Direc/Negocio</td>
                </tr>
              </thead>
              <tbody>
            
              <?php  
          
          $where1 = [];
          if($idformacredito!='' && $idformacredito!=0){
              if($idformacredito=='CP'){
                  $where1[] = ['credito.idforma_credito',1];
              }
              elseif($idformacredito=='CNP'){
                  $where1[] = ['credito.idforma_credito',2];
              }
              elseif($idformacredito=='CC'){
                  $where1[] = ['credito.idforma_credito',3];
              }
          }
                
            $html = '';
            $orden = 1;
            foreach($users as $value){
              
               $credito = DB::table('credito')
                    ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                    ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
                    ->leftjoin('users','users.id','credito.idasesor')
                    ->where('credito.idcliente',$value->id)
                    ->where('credito.idestadocredito',1)
                    ->where('credito.estado','DESEMBOLSADO')
                    ->where($where1)
                    ->select(
                        'credito.*',
                        'credito_prendatario.nombre as nombreproductocredito' ,
                        'forma_pago_credito.nombre as frecuencianombre' ,
                        'users.usuario as codigoasesor',
                    )
                    ->orderBy('id','desc')
                    ->first();
              
              $saldo_pendientepago = '';
              $frecuencianombre = '';
              $cuota = '';
              $nombreproductocredito = '';
              $idforma_credito = '';
              $fecha_cancelado = '';
              if($credito!='' || $idformacredito==0){
                  if($credito){
                      $saldo_pendientepago = $credito->saldo_pendientepago;
                      $frecuencianombre = $credito->frecuencianombre;
                      $cuota = $credito->cuotas;
                      $nombreproductocredito = $credito->nombreproductocredito;
                      $idforma_credito = $credito->idforma_credito;
                      $fecha_cancelado = $credito->fecha_cancelado;
                  }
              
                  $users_prestamo = DB::table('s_users_prestamo')->where('s_users_prestamo.id_s_users',$value->id)->first();

                  $direccionnegocio = '';
                  if($users_prestamo){
                      $direccionnegocio = $users_prestamo->direccion_ac_economica;
                  }

                  $cp = '';
                  if($idforma_credito==1){
                      $cp = 'CP';
                  }
                  elseif($idforma_credito==2){
                      $cp = 'CNP';
                  }
                  elseif($idforma_credito==3){
                      $cp = 'CC';
                  }

                  $usersasesor = DB::table('users')->whereId($value->idasesor)->first();
                  $codigoasesor = '';
                  if($usersasesor){
                      $codigoasesor = $usersasesor->usuario;
                  }
                
                  $fecha_cancelado = $fecha_cancelado!=''?$fecha_cancelado:'';

                  $html .= "<tr id='show_data_select' idcredito='{$value->id}'>
                                <td>".$orden."</td>
                                <td>{$value->codigo}</td>
                                <td>{$value->identificacion}</td>
                                <td>{$value->nombrecompleto}</td>
                                <td>{$codigoasesor}</td>
                                <td>{$saldo_pendientepago}</td>
                                <td>{$frecuencianombre}</td>
                                <td>{$cuota}</td>
                                <td>{$cp}</td>
                                <td>{$nombreproductocredito}</td>
                                <td>{$fecha_cancelado}</td>
                                <td>{$value->numerotelefono}</td>
                                <td>{$value->direccion}</td>
                                <td>{$direccionnegocio}</td>
                            </tr>";
                  $orden++;
              }
            }

                  $html .= '<tr>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                            </tr>';
            echo $html;
              ?>
              
              </tbody>
            </table>  
                
    </div>
  </main>
</body>
</html>