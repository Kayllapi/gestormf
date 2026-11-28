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
    <div style="float:left;font-size:15px;">{{ $tienda->nombre }}</div> {{ Auth::user()->codigo }} | {{ date('d/m/Y H:iA') }}
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
                  <td style="border-top: 2px solid #000;text-align: center;font-weight: bold;" colspan="6">RIESGO Saldo de Créd.(S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;" rowspan="3">TOTAL</td>
                </tr> 
                <tr>
                  <td style="border-top: 2px solid #000;text-align: center;font-weight: bold;" colspan="2">VINCULACIÓN POR GESTIÓN</td>
                  <td style="border-top: 2px solid #000;text-align: center;font-weight: bold;" colspan="2">VINCULACIÓN POR DEPENDENCIA ECONÓMICA O FAMILIAR</td>
                  <td style="border-top: 2px solid #000;text-align: center;font-weight: bold;" colspan="2">VINCULACIÓN POR PROPIEDAD Y GESTIÓN</td>
                </tr>
                <tr>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">N°</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">DOI/RUC</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">Nombres y Apellidos</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">Cnta</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">Misma dirección de negocio (Mismo Negocio)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">Cnta</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">Unión Económica Familiar (Parejas)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">Cnta</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align: center;font-weight: bold;">Control Directo</td>
                </tr>
              </thead>
              <tbody>
            
              @include(sistema_view().'/vinculacionriesgounico/filas', ['filas' => $filas])
              </tbody>
            </table>  
                
    </div>
  </main>
</body>
</html>
