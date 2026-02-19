<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DECLARACIÓN JURADA</title>
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
    <div style="float:left;font-size:15px;"></div> {{ date('d-m-Y H:iA') }}
  </header>
  <main>
    <div class="container">
      <h4 align="center">DECLARACIÓN JURADA DE PROPIEDAD</h4>
      <?php
          $ubi = explode('-',$usuario->db_idubigeo);
          $departamento = '';
          $provincia = '';
          $distrito = '';
          if(count($ubi)>1){
              $departamento = $ubi[2];
              $provincia = $ubi[1];
              $distrito = $ubi[0];
          }
        ?>
      <div>
        <p  style="text-align: justify;">Yo, <b>{{ $usuario->nombrecompleto }}</b>, identificado (a) con DNI N° <b>{{ $usuario->identificacion }}</b> , con domicilio en <b>{{ $usuario->direccion }}</b>,
          Distrito de {{ $distrito }}, Provincia de {{ $provincia }}, Departamento de {{ $departamento }}.
          <br><br>
          <b>DECLARO BAJO JURAMENTO:</b>
        </p>
        <p>1.- Ser propietario del presente bien(es) que son: </p>
      </div>
          <table style="width:100%;">
            <tr>
              <th style="border-bottom: 2px solid #000;">N°</th>
              <th style="border-bottom: 2px solid #000;">Bien</th>
              <th style="border-bottom: 2px solid #000;">Accesorios</th>
              <th style="border-bottom: 2px solid #000;">Año F.</th>
              <th style="border-bottom: 2px solid #000;">Color</th>
              <th style="border-bottom: 2px solid #000;">Serie/Motor/N°Partida</th>
              <th style="border-bottom: 2px solid #000;">Chasis</th>
              <th style="border-bottom: 2px solid #000;">Modelo</th>
              <th style="border-bottom: 2px solid #000;">Placa</th>
              <th style="border-bottom: 2px solid #000;">Estado</th>
            </tr>
            <?php $i=1; ?>
            @foreach($garantias as $value)
              <tr>
                  <td><b>{{ $i  }}.-</b></td>
                  <td>{{ $value->descripcion }}</td>
                  <td>{{ $value->accesorio_doc }}</td>
                  <td>{{ $value->fabricacion }}</td>
                  <td>{{ $value->color }}</td>
                  <td>{{ $value->serie_motor_partida }}</td>
                  <td>{{ $value->chasis }}</td>
                  <td>{{ $value->modelo_tipo }}</td>
                  <td>{{ $value->placa }}</td>
                  <td>
                    @if($value->idestado_garantia==1)
                    Usada
                    @elseif($value->idestado_garantia==2)
                    Seminueva
                    @elseif($value->idestado_garantia==3)
                    Nueva
                    @endif
                  </td>
              </tr>
            <?php $i++; ?>
            @endforeach
          </table>
      
      <p style="text-align: justify;">2.- No contar con el comprobante respecvo de bién(es) citada(s) en el  ítem  1 del presente.</p>
      <p style="text-align: justify;">3.- Dejar en calidad de prenda dicho bién(es) citada(s) en el ítem 1 a <b>{{ $tienda->nombre }}</b> ante un préstamo dinerario, conforme al acuerdo mutuo y contrato celebrado.</p>
      <p style="text-align: justify;">Manifiesto que lo mencionado responde a la verdad de los hechos y tengo conocimiento; que si lo declarado es
        falso,estoy sujeto a los alcances de lo establecido en el Arculo 427º y el arculo 438º del Código Penal, para los
        que hacen una falsa declaración, violando el principio de veracidad, así como para aquellos que cometan falsedad,
        simulando o alterando la verdad  intencionalmente.</p>
      
    
    <?php
    $meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
    $mes = $meses[(date_format(date_create($credito->fecha_desembolso),'n')) - 1];
    $fecha_texto = date_format(date_create($credito->fecha_desembolso),'d') . ' de ' . $mes . ' de ' . date_format(date_create($credito->fecha_desembolso),'Y');
    ?>
      <br>
      <p>{{ $ubigeo_tienda->distrito }}, {{ $fecha_texto }}</p>
      
      <table style="width:50%; float: right;">
        <tr>
          <td style="padding:0px; width: 150px;" align="left">
            <hr style="border: 1px solid #000; margin-top: 50px;">
            <span style="padding-top:10px;"><b>{{ $usuario->nombrecompleto }}</b></span>
            <br>
            <span>DNI: {{ $usuario->identificacion }}</span>
            <br>
            <span>Domicilio: {{ $usuario->direccion }}</span>
            <br>
            <span>Distrito de {{ $distrito }}</span>
          </td>
          <td style="border: 1px solid #000; width: 50px; height: 30px;"></td>
        </tr>
      </table>
    </div>
  </main>
</body>
</html>