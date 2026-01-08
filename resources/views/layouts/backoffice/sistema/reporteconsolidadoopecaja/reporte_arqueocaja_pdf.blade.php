<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REPORTE DE ARQUEO DE CAJA</title>
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
  <main>
    <div class="container">
      <h4 align="center">REPORTE DE ARQUEO DE CAJA</h4>
      <div style="width:100%; height: 20px;">
            <div style="margin-left:180px;float:left;font-size: 13px;"><b>AGENCIA: </b>{{ $agencia->nombreagencia }}  </div>     
            <div style="margin-left:50px;float:left;font-size: 13px;"><b>CORTE : </b>{{ date_format(date_create($corte),'d/m/Y') }}</div> 
      </div>
      @if(!$arqueocaja)
                      <p class="text-center" 
                           style="background-color: #dc3545;
                                  padding: 15px;
                                  border-radius: 5px;
                                  color: #fff;
                                  width: 90%;
                                  margin: auto;text-align:center;">No Hay arqueo de caja con esta fecha!!</p>
      @else
      <div style="width:100%; margin-top: 10px; height:150px;">
          
                <div style="float:left;width:47%;margin-left:9px;">
                  <table class="table table-bordered" style="width:100%;">
                    <thead class="table-dark">
                      <tr>
                        <td colspan="3" style="text-align: center;">MONEDAS</td>
                      </tr>
                      <tr>
                        <td style="text-align: center;">Denominación</td>
                        <td style="text-align: center;">Cantidad</td>
                        <td style="text-align: center;">Total</td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td style="text-align: right;">S/ {{$arqueocaja_denominacion_1->denominacion}}</td>
                        <td style="width:33%;text-align: right;">{{$arqueocaja_denominacion_1->cantidad}}</td>
                        <td style="width:33%;text-align:right;">S/. {{$arqueocaja_denominacion_1->total}}</td>
                      </tr>
                      <tr>
                        <td style="text-align: right;">S/ {{$arqueocaja_denominacion_2->denominacion}}</td>
                        <td style="text-align: right;">{{$arqueocaja_denominacion_2->cantidad}}</td>
                        <td style="text-align:right;">S/. {{$arqueocaja_denominacion_2->total}}</td>
                      </tr>
                      <tr>
                        <td style="text-align: right;">S/ {{$arqueocaja_denominacion_3->denominacion}}</td>
                        <td style="text-align: right;">{{$arqueocaja_denominacion_3->cantidad}}</td>
                        <td style="text-align:right;">S/. {{$arqueocaja_denominacion_3->total}}</td>
                      </tr>
                      <tr>
                        <td style="text-align: right;">S/ {{$arqueocaja_denominacion_4->denominacion}}</td>
                        <td style="text-align: right;">{{$arqueocaja_denominacion_4->cantidad}}</td>
                        <td style="text-align:right;">S/. {{$arqueocaja_denominacion_4->total}}</td>
                      </tr>
                      <tr>
                        <td style="text-align: right;">S/ {{$arqueocaja_denominacion_5->denominacion}}</td>
                        <td style="text-align: right;">{{$arqueocaja_denominacion_5->cantidad}}</td>
                        <td style="text-align:right;">S/. {{$arqueocaja_denominacion_5->total}}</td>
                      </tr>
                      <tr>
                        <td style="text-align: right;">S/ {{$arqueocaja_denominacion_6->denominacion}}</td>
                        <td style="text-align: right;">{{$arqueocaja_denominacion_6->cantidad}}</td>
                        <td style="text-align:right;">S/. {{$arqueocaja_denominacion_6->total}}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div style="float:left;width:4%;height:10px;">
                </div>
                <div style="float:left;width:47%;">
                  <table class="table table-bordered" style="width:100%;">
                    <thead class="table-dark">
                      <tr>
                        <td colspan="3" style="text-align: center;">BILLETES</td>
                      </tr>
                      <tr>
                        <td style="text-align: center;">Denominación</td>
                        <td style="text-align: center;">Cantidad</td>
                        <td style="text-align: center;">Total</td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td style="text-align: right;">S/ {{$arqueocaja_denominacion_7->denominacion}}</td>
                        <td style="width:33%;text-align: right;">{{$arqueocaja_denominacion_7->cantidad}}</td>
                        <td style="width:33%;text-align:right;">S/. {{$arqueocaja_denominacion_7->total}}</td>
                      </tr>
                      <tr>
                        <td style="text-align: right;">S/ {{$arqueocaja_denominacion_8->denominacion}}</td>
                        <td style="text-align: right;">{{$arqueocaja_denominacion_8->cantidad}}</td>
                        <td style="text-align:right;">S/. {{$arqueocaja_denominacion_8->total}}</td>
                      </tr>
                      <tr>
                        <td style="text-align: right;">S/ {{$arqueocaja_denominacion_9->denominacion}}</td>
                        <td style="text-align: right;">{{$arqueocaja_denominacion_9->cantidad}}</td>
                        <td style="text-align:right;">S/. {{$arqueocaja_denominacion_9->total}}</td>
                      </tr>
                      <tr>
                        <td style="text-align: right;">S/ {{$arqueocaja_denominacion_10->denominacion}}</td>
                        <td style="text-align: right;">{{$arqueocaja_denominacion_10->cantidad}}</td>
                        <td style="text-align:right;">S/. {{$arqueocaja_denominacion_10->total}}</td>
                      </tr>
                      <tr>
                        <td style="text-align: right;">S/ {{$arqueocaja_denominacion_11->denominacion}}</td>
                        <td style="text-align: right;">{{$arqueocaja_denominacion_11->cantidad}}</td>
                        <td style="text-align:right;">S/. {{$arqueocaja_denominacion_11->total}}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
      </div>
          <div style="margin-top:10px;margin-left:150px;">
                  <table style="width:420px;">
                    <tbody>
                      <tr>
                        <td colspan="2"><b>FECHA Y HORA DE REGISTRO: </b>{{ date_format(date_create($arqueocaja->fecharegistro),'d/m/Y H:i:s A') }}</td>
                      </tr>
                      <tr>
                        <td>I. Validación de Operaciones por Cuenta Banco:  
                          <img src="{{url('public/backoffice/sistema/icono_check.png')}}" width="20px" style="margin-top:0px;"></td>
                        <td style="width:120px;">{{$arqueocaja->validacion_operaciones_cuenta_banco}}</td>
                      </tr>
                      <tr>
                        <td>II. Total de Efectivo en Caja al Arqueo: </td>
                        <td>S/. {{$arqueocaja->total}}</td>
                      </tr>
                    </tbody>
                  </table>
          </div> 
      
      <br><br><br><br>
          <table class="tabla_informativa" style="text-align:center;margin-left:260px;">
              <tr>
                  <td>______________________________</td>
              </tr>
              <tr>
                  <td><b>Responsable:</b> {{ strtoupper($arqueocaja->nombrecompleto_responsable) }}</td>
              </tr>
              <tr>
                  <td><b>Cargo:</b> {{  strtoupper($arqueocaja->nombre_permiso) }}</td>
              </tr>
              <tr>
                  <td><b>Usuario:</b> {{ strtoupper($arqueocaja->codigo_responsable) }}</td>
              </tr>
          </table> 
        @endif
    </div>
  </main>
</body>
</html>