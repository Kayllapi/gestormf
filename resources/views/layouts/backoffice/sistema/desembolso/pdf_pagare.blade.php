<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAGARÉ</title>
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
          margin-top: 0.8cm;
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
        border:solid 2px #000000;    
      }
      
      .table, .table th, .table td {
        border: 1px solid #000000;
        border-collapse: collapse;
        font-size:10px;
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
  @if($tienda->imagen!='')
      <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$tienda->imagen) }}" height="50px" 
           style="position:absolute;margin-top:-10px;margin-right:5px;">
  @endif
  <main>
    <div class="container">
      <h4 align="center">PAGARÉ</h4>
          <table class="table" style="width:100%;">
            <tr>
              <th>CONTRATO DE CRÉDITO N°</th>
              <th>LUGAR DE EMISIÓN</th>
              <th colspan="3">FECHA DE EMISIÓN<br>Día / Mes / Año</th>
              <th colspan="3">FECHA DE VENCIMIENTO<br>Día / Mes / Año</th>
              <th>MONEDA E IMPORTE</th>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
          </table>
          <p style="text-align: justify;margin-top:2px;">(Emitido de conformidad con el artículo 10 de la Ley de Títulos y Valores N° 27287)
Por este PAGARÉ prometo(emos), pagar incondicionalmente a la orden de la <b>{{ $tienda->nombre }}</b> con RUC. <b>{{ $tienda->ruc }}</b> o a quien éste hubiere endosado 
            el presente título, en sus oficinas de esta ciudad o donde se presente este título para su cobro la suma de: </p>
          <div style="border-top:1px solid #000000;width:70%;"></div>
          <p style="text-align: justify;margin-top:2px;margin-bottom:2px;">Importe a debitar en la siguiente cuenta de la entidad financiera que se indica: </p>
          <table class="table" style="width:100%;">
            <tr>
              <th>Entidad</th>
              <th>Oficina</th>
              <th>Número de Cuenta</th>
              <th>DC</th>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
          </table>
        
          <p style="text-align: justify;margin-top:2px;margin-bottom:2px;">
          <b>1.</b> Este pagaré debe ser pagado en la misma moneda que expresa este título valor. <br>
          <b>2.</b> El plazo de vencimiento el presente pagaré podrá ser prorrogado por su tenedor por el importe total o parcial, por el plazo que este señale en
            éste mismo documento, sin necesidad de intervención del obligado principal (emitente) ni de su/s avalista/s o fiador/es solidario/s, para cuyo 
            efecto el/los emitente/s y su/s avalista/s o fiador/es solidario/s prestan su consentimiento expreso al firmar este pagaré. La prórroga 
            no constituirá novación de la obligación contenida en el contrato de referencia. <br>
          <b>3.</b> El presente pagaré no requiere ser protestado por falta de pago, procediendo su ejecución por el sólo mérito de haber vencido su plazo 
            y no haber sido prorrogados. Sin embargo, el tenedor podrá protestarlo, en cuyo caso el obligado principal (emitente) y/o su/s avalista/s 
            o fiador/es solidario/s asumirán los gastos y comisiones de dicha diligencia. <br>
          <b>4.</b> El importe de este pagaré será pagado en cuotas según los montos que se señalan en el cronograma de pagos entregado al obligado principal 
            (emitente) a la firma del presente título valor. <br>
          <b>5.</b> Desde su último vencimiento hasta la cancelación, su importe total y/o cuotas, generará los intereses compensatorios y moratorios. 
            La constitución en mora será automática por el solo hecho de vencimiento de este pagaré. Además, abonaré(mos) las comisiones, gastos y penalidades que se generen.<br>
          <b>6.</b> Las tasas de interés compensatorios, moratorios, así como las comisiones, penalidades y gastos administrativos que me(nos) obligo(amos) 
            a pagar según párrafos 4 y 5   figuran en la Hoja de Resumen del crédito que es anexo al presente pagaré. <br>
          <b>7.</b> Para efectos de la ejecución de este título valor el obligado principal (emitente) y/o su/s avalista/s o fiador/es solidario/s señalan 
            como sus domicilios el consignado en el presente documento. El tenedor podrá entablar acción judicial donde lo tuviere por conveniente. 
            Asimismo, las partes renuncian al fuero que les pudiera corresponder y se someten a la competencia y jurisdicción de los jueces y tribunales de esta ciudad.  <br>             
          <b>8.</b> El/los Aval/fiador/es que suscribe/n este pagaré se constituye/n en Aval/fiador/es solidario/s del emitente (y solidariamente entre sí), 
            sin beneficio de excusión, comprometiéndose a pagar las obligaciones asumidas por el emitente a favor de <b>{{ $tienda->nombre }}</b>; incluyendo los intereses 
            compensatorios, moratorios, comisiones y gastos de toda clase que se deriven de este pagaré, sin reserva ni limitación alguna. <br>
          <b>9.</b> Declaro/declaramos haber recibido copia del presente Pagaré Incompleto sobre el cual renuncio/renunciamos expresamente a la inclusión en el 
            mismo, cualquier cláusula que prohíba o limite su libre negociación del presente Título Valor. Asimismo, Autorizo/Autorizamos a <b>{{ $tienda->nombre }}</b> o 
            a su tenedor respectivo a completar este pagaré en cualquier momento, con arreglo a los términos y condiciones del crédito. <br>
          <b>10.</b> En caso de ejecución de la prenda, se hará en forma directa, sin intervención de autoridad judicial al mejor postor.<br>
          <b>11.</b> Autorizo(amos) de manera expresa a <b>{{ $tienda->nombre }}</b>, para que al vencimiento de este pagaré o de sus prórrogas cargue en cualquiera de mis(nuestras) 
            cuentas o depósitos en <b>{{ $tienda->nombre }}</b>, debitando el importe adeudado, sin necesidad de aviso previo ni confirmación posterior.<br>
          <b>12.</b> AUTORIZACIÓN DE REFINANCIACIONES: Autorizo/autorizamos a efectuar las operaciones de refinanciación de las obligaciones derivadas del 
            presente título que fueran necesarias, bastando la intervención en dicha operación de cualquiera de los obligados con el presente 
            título valor, sin necesidad de suscribir nuevos pagarés; siendo facultad de <b>{{ $tienda->nombre }}</b> tener por suficiente esta autorización y/o modalidad. <br>
          <b>13.</b> AUTORIZACIÓN DE DESTRUCCIÓN: Autorizo/autorizamos la destrucción del presente título a su cancelación de conformidad con lo establecido 
            en la Ley de Títulos y Valores.<br>
                </p>
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
      
      
          $departamentoaval = '';
          $provinciaaval = '';
          $distritoaval = '';
      
          if($aval!=''){
          $ubiaval = explode('-',$aval->db_idubigeo);
          if(count($ubiaval)>1){
              $departamentoaval = $ubiaval[2];
              $provinciaaval = $ubiaval[1];
              $distritoaval = $ubiaval[0];
          }
          }
          ?>
      

      <table class="table" style="width:100%;">
        <tr>
          <td style="padding:5px;border: 1px solid #000;width:50%;">
            <span><b>EMITENTE 1:</b></span>
            <br>
            <span><b>RUC/DNI/CE:</b></span>
            <br>
            <span><b>DOMICILIO:</b></span>
            <br>
            <span><b>REP. LEGAL:</b></span>
          </td>
          <td style="padding:5px;padding-bottom:0;border: 1px solid #000;">
            <div style="font-size:7px;text-align:center;margin-top:60px;">(Sollo, Firma)</div>
          </td>
          <td style="padding:5px;padding-bottom:0;border: 1px solid #000;width:15%;">
            <div style="font-size:7px;text-align:center;margin-top:60px;">(Huella D.)</div>
          </td>
        </tr>
        <tr>
          <td style="padding:5px;border: 1px solid #000;width:50%;">
            <span><b>EMITENTE 2:</b></span>
            <br>
            <span><b>RUC/DNI/CE:</b></span>
            <br>
            <span><b>DOMICILIO:</b></span>
            <br>
            <br>
          </td>
          <td style="padding:5px;padding-bottom:0;border: 1px solid #000;">
            <div style="font-size:7px;text-align:center;margin-top:60px;">(Firma)</div>
          </td>
          <td style="padding:5px;padding-bottom:0;border: 1px solid #000;width:15%;">
            <div style="font-size:7px;text-align:center;margin-top:60px;">(Huella D.)</div>
          </td>
        </tr>
        <tr>
          <td style="padding:5px;border: 1px solid #000;width:50%;">
            <span><b>AVAL/FIADOR 1:</b></span>
            <br>
            <span><b>RUC/DNI/CE:</b></span>
            <br>
            <span><b>DOMICILIO:</b></span>
            <br>
            <span><b>REP. LEGAL:</b></span>
          </td>
          <td style="padding:5px;padding-bottom:0;border: 1px solid #000;">
            <div style="font-size:7px;text-align:center;margin-top:60px;">(Sollo, Firma)</div>
          </td>
          <td style="padding:5px;padding-bottom:0;border: 1px solid #000;width:15%;">
            <div style="font-size:7px;text-align:center;margin-top:60px;">(Huella D.)</div>
          </td>
        </tr>
        <tr>
          <td style="padding:5px;border: 1px solid #000;width:50%;">
            <span><b>AVAL/FIADOR 2:</b></span>
            <br>
            <span><b>RUC/DNI/CE:</b></span>
            <br>
            <span><b>DOMICILIO:</b></span>
            <br>
            <br>
          </td>
          <td style="padding:5px;padding-bottom:0;border: 1px solid #000;">
            <div style="font-size:7px;text-align:center;margin-top:60px;">(Firma)</div>
          </td>
          <td style="padding:5px;padding-bottom:0;border: 1px solid #000;width:15%;">
            <div style="font-size:7px;text-align:center;margin-top:60px;">(Huella D.)</div>
          </td>
        </tr>
      </table>
      
      <div>{{$usuario->codigo}}</div>
    </div>
  </main>
</body>
</html>