<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FICHA DE LIQUIDACIÓN</title>
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
          margin-right: 0.5cm;
          margin-bottom: 2cm;
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
      .page {
          position: absolute;
          left:50%;
          margin-left: -5px;
          bottom:-5px;
      }
      .datafooter {
        position: absolute;
        bottom: -5px;
        text-align: right;
        right: 0px;
      }

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
      .linea_firma {
          width: 100%;
          border-bottom: 2px solid #000,
      }
     </style>
</head>
<body>
    <header>
      <div style="float:left;font-size:15px;">{{ $tienda->nombre }} | {{$tienda->nombreagencia}}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
    </header>
    <footer style="text-align:right;">
        <p class="page">Página </p>
    </footer>
    <main>
        <div class="container">
            <h4 align="center" style="margin-bottom: 0px;">FICHA DE LIQUIDACIÓN</h4>
            <b>CLIENTE: </b>{{ $credito->clientenombrecompleto }}<br>
            <b>CUENTA: </b>C{{ $credito->cuenta }}<br>
            <b>FECHA: </b>{{ $credito->fechaliquidaciongarantia }}<br>
            <table style="width:100%;" style="border-bottom:2px solid #000">
                <thead class="table-dark">
                    <tr>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >CODIGO DE GARANTIA</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >CLIENTE</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >RUC/DNI/CE</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >TIPO DE GARANTIA</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >DESCRIPCIÓN</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >Serie/Motor/N°Partida</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >MODELO</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >VALOR COMERCIAL</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >V.C. DESCT.</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >COBERTURA</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >P. LIQUID.</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >ACCESORIOS</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >COLOR</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >AÑO DE FABRICACIÓN</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >PLACA DEL VEHÍCULO</th>
                    </tr>
                </thead>
                <tbody>
                  <?php
                  $porcentaje_descuento_liquidacion = configuracion($tienda->id,'porcentaje_descuento_liquidacion')['valor'];
                  $total_valorcomercial = 0;
                  $total_descuento = 0;
                  $total_cobertura = 0;
                  $total_precio = 0;
                  ?>
                    @foreach ($credito_garantias as $value)
                  <?php
                  $valor_comercial_descuento = $value->valor_comercial - ($value->valor_comercial * $porcentaje_descuento_liquidacion / 100);
                  ?>
                        <tr>
                                <td>{{$value->garantias_codigo}}</td>
                                <td>{{$value->clientenombrecompleto}}</td>
                                <td>{{$value->dni}}</td>
                                <td>{{$value->garantias_tipogarantia}}</td>
                                <td>{{$value->descripcion}}</td>
                                <td>{{$value->garantias_serie_motor_partida}}</td>
                                <td>{{$value->garantias_modelo_tipo}}</td>
                                <td style="text-align:right;">{{$value->valor_comercial}}</td>
                                <td style="text-align:right;">{{number_format($valor_comercial_descuento, 2, '.', '')}}</td>
                                <td style="text-align:right;">{{$value->valor_realizacion}}</td>
                                <td style="text-align:right;">{{$value->precioliquidacion}}</td>
                                <td>{{$value->garantias_accesorio_doc}}</td>
                                <td>{{$value->garantias_color}}</td>
                                <td>{{$value->garantias_fabricacion}}</td>
                                <td>{{$value->garantias_placa}}</td>
                        </tr>
                      <?php
                      $total_valorcomercial += $value->valor_comercial;
                      $total_descuento += $valor_comercial_descuento;
                      $total_cobertura += $value->valor_realizacion;
                      $total_precio += $value->precioliquidacion;
                      ?>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th style="border-top: 2px solid #000;text-align:right;" colspan="7">TOTAL</th>
                        <th style="border-top: 2px solid #000;text-align:right;">{{number_format($total_valorcomercial, 2, '.', '')}}</th>
                        <th style="border-top: 2px solid #000;text-align:right;">{{number_format($total_descuento, 2, '.', '')}}</th>
                        <th style="border-top: 2px solid #000;text-align:right;">{{number_format($total_cobertura, 2, '.', '')}}</th>
                        <th style="border-top: 2px solid #000;text-align:right;">{{number_format($total_precio, 2, '.', '')}}</th>
                        <th style="border-top: 2px solid #000;" colspan="4"></th>
                    </tr>
                </tfoot>
            </table>
          </br>
            
      <?php
      $porcentaje_descuento_liquidacion = configuracion($tienda->id,'porcentaje_descuento_liquidacion')['valor'];
      ?>
      <table style="margin-top:60px;width:100%;">
          <tr>
              <td style="width:50%;" align="center">
                  <div style="border-top:solid 1px #000;margin-left:20px;margin-right:20px;width:260px;margin:auto;"></div>
                  <div style="padding-top:5px;">{{ $liquidaciongarantiaresponsable->nombrecompleto }}</div>
                  <div>Firma del Resposanble</div>
              </td>
              <td style="width:50%;" align="center">
                  <div style="border-top:solid 1px #000;margin-left:20px;margin-right:20px;width:260px;margin:auto;"></div>
                  <div style="padding-top:5px;">Firma de Administrador/Gerente</div><br>
              </td>
          </tr>
      </table>
        </div>
    </main>
</body>
</html>