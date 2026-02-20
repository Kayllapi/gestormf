<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REPORTE DE REMATES</title>
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
      <div style="float:left;font-size:15px;">{{ $tienda->nombre }} | {{$tienda->nombreagencia}}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
    </header>
    <footer style="text-align:right;">
        <p class="page">Página </p>
    </footer>
    <main>
        <div class="container">
            <h4 align="center" style="margin-bottom: 0px;">REPORTE DE REMATES</h4>
            <b>AGENCIA: </b>{{ $tienda->nombreagencia }}<br>
            <table style="width:100%;" style="border-bottom: 2px solid #000;border-bottom: 2px solid #000;">
                <thead class="table-dark">
                    <tr>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;" >N°</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;" >CUENTA</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;" >CLIENTE</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;" >RUC/DNI/CE</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;" >TIPO DE GARANTÍA</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;" >DESCRIPCIÓN</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;" >MODELO</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;" >VALOR COMERCIAL</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;" >ACCESORIOS</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;" >COBERTURA</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;" >COLOR</th>
                        <th style="border-top: 2px solid #000;border-bottom: 2px solid #000;" >CÓDIGO GARANTÍA</th>
                    </tr>
                </thead>
                <tbody>                  
                    @foreach ($credito_garantias as $value)
                        <tr>
                                <td style="text-align:center;">{{$value['numero']}}</td>
                                <td>{{$value['cuenta']}}</td>
                                <td>{{$value['cliente']}}</td>
                                <td>{{$value['dni']}}</td>
                                <td>{{$value['tipo_garantia']}}</td>
                                <td>{{$value['descripcion']}}</td>
                                <td>{{$value['modelo']}}</td>
                                <td style="text-align:right;">{{$value['valorcomercial']}}</td>
                                <td>{{$value['accesorios']}}</td>
                                <td style="text-align:right;">{{$value['cobertura']}}</td>
                                <td>{{$value['color']}}</td>
                                <td>{{$value['codigo_garantia']}}</td>
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