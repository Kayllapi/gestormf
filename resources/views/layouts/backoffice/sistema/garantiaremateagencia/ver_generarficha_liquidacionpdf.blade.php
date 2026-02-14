<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FICHA DE REMATE</title>
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
      footer > .page:after {
        content: counter(page, decimal-leading-zero);
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
        <div style="float:left;font-size:18px;">{{ $tienda->nombre }} | {{ $tienda->nombreagencia }}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
    </header>
    <footer style="text-align:right;">
        <p class="page">Página </p>
    </footer>
    <main>
        <div class="container">
            <h4 align="center" style="margin-bottom: 0px;">FICHA DE REMATE</h4>
            <b>CLIENTE: </b>{{ $credito->clientenombrecompleto }}<br>
            <table style="width:100%;">
                <thead class="table-dark">
                    <tr>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >CODIGO DE GARANTIA</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >CLIENTE</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >RUC/DNI/CE</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >TIPO DE GARANTIA</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >DESCRIPCIÓN</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >Serie/Motor/N°Partida</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >MODELO</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >OTROS</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >VALOR COMERCIAL</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >COBERTURA</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >ACCESORIOS</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >COLOR</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >AÑO DE FABRICACIÓN</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >AÑO DE COMPRA</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >PLACA DEL VEHÍCULO</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center" >DETALLE</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($credito_garantias as $value)
                        <tr>
                                <td>{{$value->garantias_codigo}}</td>
                                <td>{{$value->clientenombrecompleto}}</td>
                                <td>{{$value->dni}}</td>
                                <td>{{$value->garantias_tipogarantia}}</td>
                                <td>{{$value->descripcion}}</td>
                                <td>{{$value->garantias_serie_motor_partida}}</td>
                                <td>{{$value->garantias_modelo_tipo}}</td>
                                <td>{{$value->garantias_otros}}</td>
                                <td>{{$value->valor_comercial}}</td>
                                <td>{{$value->valor_realizacion}}</td>
                                <td>{{$value->garantias_accesorio_doc}}</td>
                                <td>{{$value->garantias_color}}</td>
                                <td>{{$value->garantias_fabricacion}}</td>
                                <td>{{$value->garantias_compra}}</td>
                                <td>{{$value->garantias_placa}}</td>
                                <td>{{$value->garantias_detalle_garantia}}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                </tfoot>
            </table>
        </div>
    </main>
</body>
</html>