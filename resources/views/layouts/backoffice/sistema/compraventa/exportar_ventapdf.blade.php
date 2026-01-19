<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REPORTE DE VENTA</title>
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
        <div style="float:left;font-size:18px;">{{ $tienda->ticket_nombre }} | {{ $tienda->nombreagencia }}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
    </header>
    <footer style="text-align:right;">
        <p class="page">Página </p>
    </footer>
    <main>
        <div class="container">
            <h4 align="center">REPORTE DE VENTA</h4>
            <b>AGENCIA: </b>{{ $tienda->nombreagencia }}<br>
            <b>PERIODO: </b>{{ $fecha_inicio_venta }} al {{ $fecha_fin_venta }}<br>
            <table style="width:100%;">
                <thead class="table-dark">
                    <tr>
                        <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">N°</td>
                        <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Cod. Operación</td>
                        <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Fecha Registro</td>
                        <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Descripción</td>
                        <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Serie/Motor/N° P.</td>
                        <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Modelo/T.</td>
                        <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Valor Compra</td>
                        <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Valor Comercial</td>
                        <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Precio Venta Descuento</td>
                        <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Precio Venta Final</td>
                        <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Estado</td>
                        <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Placa de Vehículo</td>
                        <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Origen</td>
                        <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Comprador</td>
                        <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Lugar de Pago</td>
                        <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Responsable</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $html = '';
                        foreach($cvventas as $key => $value){
                            $fecharegistro = date_format(date_create($value->venta_fecharegistro),"d-m-Y H:i:s A");
                            $origen = $value->idorigen == '1' ? 'SERFIP' : 'OTROS';
                            $lugar_pago = $value->venta_idformapago == '1' ? 'CAJA' : 'BANCO';
                            $descripcion = Str::limit($value->descripcion, 25);
                            $comprador = Str::limit($value->comprador_nombreapellidos, 25);

                            $html .= "<tr id='show_data_select' idcredito='{$value->id}'>
                                        <td>".($key+1)."</td>
                                        <td>VB{$value->codigoventa}</td>
                                        <td>{$fecharegistro}</td>
                                        <td>{$descripcion}</td>
                                        <td>{$value->serie_motor_partida}</td>
                                        <td>{$value->modelo_tipo}</td>
                                        <td style='text-align: right;'>{$value->valorcompra}</td>
                                        <td style='text-align: right;'>{$value->valorcomercial}</td>
                                        <td style='text-align: right;'>{$value->venta_precio_venta_descuento}</td>
                                        <td style='text-align: right;'>{$value->venta_montoventa}</td>
                                        <td>{$value->estado}</td>
                                        <td>{$value->placa}</td>
                                        <td>{$origen}</td>
                                        <td>{$comprador}</td>
                                        <td>{$lugar_pago}</td>
                                        <td>{$value->responsablecodigo}</td>
                                    </tr>";
                        }
                        if(count($cvventas)==0){
                            $html.= '<tr><td colspan="16" style="border-bottom: 2px solid #000;text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
                        }
                        echo $html;
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>