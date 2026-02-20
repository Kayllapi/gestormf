<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GIRO ECONOMICO</title>
    <style>
      *{
        font-family:helvetica;
        font-size:9px;
      }
      @page {
          margin: 0cm 0cm;
      }

      /** Defina ahora los márgenes reales de cada página en el PDF **/
      body {
          margin-top: 1.2cm;
          margin-left: 0.7cm;
          margin-right: 0.7cm;
          margin-bottom: 1cm;
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
        margin-bottom:0px;
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
        padding: 2px; /* Espaciado interno para el contenedor */
      }
      /* Estilo para las columnas */
      .col {
        display: inline-block; /* Hace que las columnas se muestren una al lado de la otra */
        padding: 2px; /* Espaciado interno para las columnas */
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
      .border-bottom{
        border-bottom:dashed 1px #888888;    
      }
      .border-right{
        border-right:solid 1px #888888;    
      }
     </style>
</head>
<body>
  <header>
    <div style="float:left;font-size:15px;">{{ $tienda->nombre }}</div> {{ Auth::user()->nombre.' '.Auth::user()->apellidopaterno }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
  </footer>
  
  <main>
    <h4 align="center" style="font-size:13px;margin:0;padding:0;">GIRO ECONOMICO</h4>
    <div class="row">
      <div class="col">
        <table class="table">
          <thead>
            <tr>
              <th style="font-weight: bold;" width="10px"></th>
              <th style="font-weight: bold;" width="100px">Tipo de Giro</th>
              <th style="font-weight: bold;" width="200px">Giro Económico</th>
              <th style="font-weight: bold;" width="200px">Margen de Vta. Máximo (%)</th>
              <th style="font-weight: bold;" width="200px">Estado</th>
            </tr>
          </thead>
          <tbody>
            @foreach($giros as $key => $value)
              <tr>
                  <td>{{ ($key+1) }}</td>
                  <td>{{$value->nombretipogiroeconomico}}</td>
                  <td>{{$value->nombre}}</td>
                  <td>{{$value->porcentaje}}</td>
                  <td>{{$value->estado}}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </main>
</body>
</html>