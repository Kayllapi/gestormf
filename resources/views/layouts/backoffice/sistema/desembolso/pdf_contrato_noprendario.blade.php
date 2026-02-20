<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CONTRATO NO PRENDARIO</title>
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
     </style>
</head>
<body>
  <header>
    <div style="float:left;font-size:15px;">{{ $tienda->nombre }}</div> C{{ str_pad($credito->cuenta, 8, "0", STR_PAD_LEFT) }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
    <p class="datafooter">{{ $tienda->nombreagencia }} / {{ Auth::user()->codigo }}</p>
  </footer>
  <main>
    <div class="container">
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
      <h4 align="center" style="font-size:13px;margin-top:3px;margin-bottom:5px;">CONTRATO DE CRÉDITO</h4>
  <div style="text-align: justify;">
      Los que suscriben, por una parte, <b>{{ $tienda->nombre }}</b>, con RUC/DNI N° <b>{{ $tienda->ruc }}</b> y domicilio legal para estos efectos 
    en <b>{{ $tienda->direccion }}</b> Distrito de <b>{{ $ubigeo_tienda->distrito }}</b>, Provincia de <b>{{ $ubigeo_tienda->provincia }}</b> y Departamento de <b>{{ $ubigeo_tienda->departamento }}</b> a 
    quién en lo sucesivo se le denominará como <b>“EL ACREEDOR”</b>, y de la otra parte <b>“EL/LOS PRESTATARIO(S)”</b> y <b>“SU(S) AVAL(ES) SOLIDARIO(S)”</b>, cuyos datos se 
    consignan al final del presente contrato y ficha de información.
    Hechas las declaraciones respectivas, ambas partes están de acuerdo en celebrar el presente contrato de <b>CRÉDITO</b>, producto <b>{{ $credito->nombreproductocredito }}</b>, de conformidad con 
    las siguientes clausulas: 
    <div style="width:100%; height:5px;"></div>
    <b>I.	CLÁUSULAS GENERALES</b>
    <br>
    <b>1.1.</b>	EL/LOS PRESTATARIO(S) y/o SU(S) AVAL/FIADOR(ES) SOLIDARIO(S) declaran bajo juramento que los datos suministrados en ficha de información, 
    solicitud, contrato y demás documentos de crédito, son verídicos y autoriza(n) a EL ACREEDOR a verificarlos.
    <br>
    <b>1.2.</b>	El ACREEDOR podrá modificar unilateralmente, las condiciones contractuales, incluyendo las tasas de interés compensatorio y moratorio, 
    comisiones de servicios y gastos; en situaciones de refinanciamiento de la obligación de EL/LOS PRESTATARIO(S). Asimismo, las modificaciones 
    contractuales están también en función a los informes favorables de la Superintendencia de Banca, Seguros y AFP’s y del Banco Central de Reserva del Perú, 
    ante situaciones extraordinarias imprevisibles que pongan en riesgo el sistema financiero.
    <div style="width:100%; height:5px;"></div>
    <b>II.	SOBRE EL PRÉSTAMO </b>
    <br>
    <b>2.1.</b>	EL ACREEDOR otorga a EL/LOS PRESTATARIO(S), la suma de S/. <b>{{ $credito->monto_solicitado }}</b>, en condición de préstamo, con pago <b>{{strtoupper($credito->forma_pago_credito_nombre)}}</b> en <b>{{$credito->cuotas}}</b> cuota(s), 
    debiéndose pagar un total de S/. <b>{{ $credito->total_pagar }}</b> al terminar el plazo, que incluye capital más los intereses, comisión de servicio y gastos, 
    conforme lo informado y aceptación de estos por parte de EL/LOS PRESTATARIO(S) y/o SU(S) AVAL/FIADOR(ES) SOLIDARIO(S). 
    La entrega del monto total del préstamo, hoja de resumen y el cronograma de pagos, se realizan en el momento de firmado del presente contrato y del título valor. 
    <br>
    <b>2.2.</b>	EL/LOS PRESTATARIO(S) se obliga(n) a pagar a EL ACREEDOR el monto total de préstamo otorgado por este último más los intereses, comisión de servicio y gastos, 
    en la forma y plazo de pagos convenido de ambas partes conforme numeral 2.1., para lo cual se otorga <b>{{ $credito->config_dias_tolerancia_garantia }}</b> días de tolerancia para los pagos respectivos. 
    Vencida el plazo convenido y los días de tolerancia EL/LOS PRESTATARIO(S) pagará(n) el interés compensatorio y moratorio más gastos, desde el primer día 
    de su vencimiento conforme las tasas de hoja de resumen de crédito.
    <br>
    <?php
    $penalidad = 0;
    if($credito->idforma_credito==1){
        if($credito->modalidad_calculo=='Interes Simple'){
            $penalidad =  $credito->config_penalidad_couta_simple;
        }
        elseif($credito->modalidad_calculo=='Interes Compuesto'){
            $penalidad =  $credito->config_penalidad_couta_compuesto;
        }
    }else{
        if($credito->modalidad_calculo=='Interes Simple'){
            $penalidad =  $credito->config_penalidad_couta_simple_noprendaria;
        }
        elseif($credito->modalidad_calculo=='Interes Compuesto'){
            $penalidad =  $credito->config_penalidad_couta_compuesto_noprendaria;
        }
    }
      
    ?>

    <b>2.3.</b>	EL/LOS PRESTATARIO(S) efectuará sus pagos en el establecimiento de pago respectivo de EL ACREEDOR; asimismo EL/LOS PRESTATARIO(S) autoriza(n) expresamente a EL ACREEDOR para cobro del préstamo en domicilio y/o local de negocio de ser el caso.
    <br>
    <b>2.4.</b>	EL/LOS PRESTATARIO(S) y/o SU(S) AVAL/FIADOR(ES) SOLIDARIO(S) autorizan expresamente a EL ACREEDOR, a efectuar las gestiones de cobranza en su domicilio, centro laboral y/o negocio, a través de mecanismos idóneos y permitidos por el Código de Protección y Defensa del Consumidor y sus normas modificatorias.
    <div style="width:100%; height:5px;"></div>
    <b>III.	 RESOLUCIÓN DE CONTRATO SIN PREVIO AVISO</b>
    <br>
    EL ACREEDOR deja expresa constancia que, en cualquiera de los siguientes supuestos tendrá la facultad de dar por vencidos todos los plazos pactados y proceder a la cobranza del saldo no pagado del crédito, intereses, comisión de servicio y gastos; asimismo los intereses compensatorios, moratorios pactados y cargos adeudados.
    <br>
    <b>3.1.</b>	Si EL/LOS PRESTATARIO(S) incumplen el pago total o parcial de una o más de las cuotas pactadas o cualquiera de sus obligaciones, EL ACREEDOR tendrá la facultad de dar por vencidos los plazos del crédito otorgados bajo las condiciones del presente contrato.
    <br>
    <b>3.2.</b>	En el caso de garantía real para el crédito. Si EL BIEN se pierde o deteriora, o pierda valor contable, o estuviese en peligro de ello y EL/LOS PRESTATARIO(S) y/o SU(S) AVAL(ES) SOLIDARIO(S) no cumplen con mejorar o reemplazar y proteger la garantía, a exigencias de EL ACREEDOR; asimismo en caso no cumplan en contratar, renovar y/o endosar la póliza de seguro, mientras esté vigente el crédito hasta su cancelación.
    <div style="width:100%; height:5px;"></div>
    <b>IV.	EMISIÓN DE TITULO VALOR INCOMPLETO</b>
    <br>
    <b>4.1.</b>	EL/LOS PRESTATARIO(S) y/o SU(S) AVAL/FIADOR(ES) SOLIDARIO(S), en respaldo de su obligación, emiten y aceptan un pagaré incompleto o de ser el caso una Letra de cambio incompleta de acuerdo con lo establecido en el Artículo 10° de la Ley 27287 de Títulos Valores, que podrá ser prorrogado o renovado por EL ACREEDOR con su simple indicación en el título valor y sin que tales prórrogas o renovaciones puedan considerarse como una novación. EL/LOS PRESTATARIO(S) y/o SU(S) AVAL/FIADOR(ES) SOLIDARIO(S) renuncia(n) la facultad de incorporar al presente cualquier cláusula que limite o impida la libre negociación y/o cesión de los títulos valores, aceptando y autorizando la negociación y/o cesión del título valor sin necesidad de comunicación futura y hacen constancia de haber sido informados e instruidos sobre los mecanismos y amparos de ley sobre la emisión del título valor incompleto.
    <br>
    <b>4.2.</b>	EL/LOS PRESTATARIO(S) y/o SU(S) AVAL/FIADOR(ES) SOLIDARIO(S) declaran haber recibido copia del pagaré o Letra de cambio incompleta y autorizan de manera expresa a EL ACREEDOR a completar dicho título valor respectivo cuando incumpla el pago de las cuotas según su cronograma de pagos o se produjeran las causales de resolución contractual establecido en los numerales 3.1 y 3.2. 
    <br>
    <b>4.3.</b>	EL ACREEDOR queda facultada a refinanciar incluyendo el monto total de la deuda vencida, en una liquidación de saldo deudor conforme a sus reglas internas, como el importe del capital saldo por pagar más intereses, servicios, intereses compensatorios y moratorios más gastos y cargos establecidos en la Hoja Resumen de EL ACREEDOR. Bastando la intervención en dicha operación de cualquiera de los obligados y se tendrá como fecha de emisión el día de suscripción y de vencimiento el día en que se complete el pagaré, a partir del cual EL ACREEDOR queda facultada a exigir su pago en vía judicial, sin que sea obligatorio el protesto del mencionado título valor.   
    <br>
    <b>4.4.</b>	Conforme el artículo 17, de Ley De Títulos Valores, Ley Nº 27287, EL/LOS PRESTATARIO(S) y/o SU(S) AVAL/FIADOR(ES) SOLIDARIO(S) autorizan a EL ACREEDOR destruir en totalidad el pagaré o letra de cambio, cuando el crédito garantizado con dicho título valor se encuentre totalmente cancelado. 
    <div style="width:100%; height:5px;"></div>
    <b>V.	PAGO ANTICIPADO Y ADELANTO DE CUOTAS</b>
    <br>
    <b>5.1.</b>	EL/LOS PRESTATARIO(S) podrá(n) efectuar pago anticipado o adelanto de cuotas en cualquier momento, y declaran que con anterioridad al otorgamiento del crédito EL ACREEDOR les ha informado sobre; las diferencias entre el pago adelantado y el pago anticipado, y los derechos que tienen de requerir y forma de proceder en su aplicación.
    <br>
    <b>5.2.</b>	Pago anticipado. - Al realizar el pago anticipado total o parcial se reducirán los intereses, comisión de servicio y gastos a la fecha de pago. Cuando se realice un pago anticipado parcial (mayores a dos cuotas), es decir sin cancelar la deuda total, EL/LOS PRESTATARIO(S) podrán optar por reducir el monto de las cuotas por mantener el plazo de crédito restante u optar reducir el número de cuotas como efecto de reducción del plazo del crédito, debiendo realizar la elección antes de realizar el pago anticipado, para ello EL ACREEDOR deberá entregar el nuevo cronograma respectivo.
    <br>
    <b>5.3.</b>	Adelanto de cuotas. - En caso de que EL/LOS PRESTATARIO(S) manifieste(n) expresamente su voluntad de adelantar el pago de cuotas, EL ACREEDOR procederá a aplicar el monto pagado sobre la cuota del periodo a las cuotas inmediatas siguientes no vencidas, sin la reducción de intereses, comisión de servicio ni gastos. 
    <div style="width:100%; height:5px;"></div>
    <b>VI.	CONSENTIMIENTO PARA TRATAMIENTO DE DATOS PERSONALES </b>
<br>
    <b>6.1.</b>	De conformidad con la Ley N° 29733 - Ley de Protección de Datos Personales y su Reglamento aprobado mediante D.S. 003-2013-JUS, EL/LOS PRESTATARIO(S) y/o SU(S) AVAL/FIADOR(ES) SOLIDARIO(S) otorga(n) su consentimiento expreso, para que los datos personales facilitadas conforme numeral 1.1. sean almacenadas en data de EL ACREEDOR, siendo tratados los mismos interna, y externamente para LA COMUNICACIÓN en aspectos de acceso a productos y servicios financieros, gestión de los servicios, gestiones de cobranza, gestiones legales y aspectos históricos de información. EL/LOS PRESTATARIO(S) y/o SU(S) AVAL/FIADOR(ES) SOLIDARIO(S) podrá(n) ejercer su derecho de acceso, actualización, rectificación, e inclusión de datos personales
<br>
    <b>6.2.</b>	EL/LOS PRESTATARIO(S) y/o SU(S) AVAL/FIADOR(ES) SOLIDARIO(S) autorizan a EL ACREEDOR mantener sus datos personales declarados con finalidad y uso antes mencionados a posterior de culminado el presente contrato.
<br>
    <b>6.3.</b>	EL ACREEDOR queda autorizado a proporcionar información conforme a la Ley aplicable, relacionado a todo incumplimiento de obligaciones que EL/LOS PRESTATARIO(S) y/o SU(S) AVAL/FIADOR(ES) asumen según el presente contrato, en las centrales de riesgo, terceros o agentes de información crediticia, pudiendo difundirse y/o comercializarse, libre de responsabilidad para EL ACREEDOR.

    <div style="width:100%; height:5px;"></div>
    <b>VII.	DEL AVAL SOLIDARIO Y GARANTÍAS</b><br>
    <b>7.1.</b>	SU(S) AVAL/FIADOR(ES) SOLIDARIO(S) deja(n) expresa constancia que su fianza es por plazo indeterminado; además de solidaria, indivisible e ilimitada: responder por el pago del capital, más intereses, comisión de servicio, interés compensatorios y moratorios más gastos y cargos de la presente obligación y cualquier otra obligación de cargo de EL/LOS PRESTATARIO(S). 
    <br>
    <b>7.2.</b>	El procedimiento de ejecución de las Garantías que presenta(n) EL/LOS PRESTATARIO(S) y/o SU(S) AVAL/FIADOR(ES) SOLIDARIO(S) será conforme ley N° 28677 de garantías y Ley N° 27287 de títulos y valores.
    <div style="width:100%; height:5px;"></div>
    <b>VIII.	DOMICILIO Y JURISDICCIÓN TERRITORIAL </b>
    <br>
Las partes fijan como su(s) domicilio(s) el/los que aparece(n) consignado(s) en el presente contrato y conforme declaración en numeral 1.1. del presente, donde se dirigirán todas las comunicaciones. EL/LOS PRESTATARIO(S) y/o SU(S) AVAL/FIADOR(ES) SOLIDARIO(S) se obliga(n) a comunicar a EL ACREEDOR cualquier cambio de domicilio, y deberá estar ubicado dentro del área urbana donde se firmó el presente. Caso contrario se mantiene lo fijado al inicio.
De conformidad a los Artículos 25 y 26 del Código Procesal Civil y al Artículo 34° del Código Civil, ambas partes se someten a la jurisdicción y competencia territorial de los jueces de la provincia del lugar de suscripción del presente contrato y señalan como sus domicilios los indicados en el presente contrato, donde se les hará llegar las notificaciones a que hubiere lugar.

    <div style="width:100%; height:5px;"></div>
    <?php
    $meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
    $mes = $meses[(date_format(date_create($credito->fecha_desembolso),'n')) - 1];
    $fecha_texto = date_format(date_create($credito->fecha_desembolso),'d') . ' de ' . $mes . ' de ' . date_format(date_create($credito->fecha_desembolso),'Y');
    ?>
EL/LOS PRESTATARIO(S) y/o SU(S) AVAL(ES) SOLIDARIO(S), declaramos haber recibido una copia del presente contrato, de la Hoja Resumen, Cronograma de pagos, y 
    los aceptamos en su integridad y suscribimos en fe a la verdad del presente contrato en la ciudad de <b>{{ $ubigeo_tienda->distrito }}</b>, a <b>{{ $fecha_texto }}</b>.

</div>
<br>
<br>
      <table class="table" style="width:60%;margin-left:148px;">
        <tr>
          <td style="padding:5px;border: 1px solid #000;width:50%;">
            <span><b>{{ $tienda->nombre }}</b></span>
            <br>
            <span>{{ $tienda->representante }}</span>
            <br>
            <span><b>Representante Legal</b></span>
            <br>
            <span><b>EL ACREEDOR</b></span>
          </td>
          <td style="padding:5px;border: 1px solid #000;text-align:center;" colspan="2">
            @if($tienda->firma!='')
            <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$tienda->firma) }}" height="60px">
            @endif
          </td>
        </tr>
      </table>
    <div style="width:100%; height:5px;"></div>
      <table class="table" style="width:100%;">
        <tr>
          <td style="padding:5px;border: 1px solid #000;width:60%;height:70px;">
            <span><b>PRESTATARIO:</b> {{ $usuario->nombrecompleto }}</span>
            <br>
            <span><b>RUC/DNI/CE: </b>{{ $usuario->identificacion }}</span>
            <br>
            <span><b>DOMICILIO: </b>{{ $usuario->direccion }}, {{ $distrito }} - {{ $provincia }} - {{ $departamento }}</span>
            <br>
            <span><b>REP. LEGAL:</b></span>
          </td>
          <td style="padding:5px;padding-bottom:0;border: 1px solid #000;">
            <div style="font-size:7px;text-align:center;margin-top:65px;">(Sello, Firma)</div>
          </td>
          <td style="padding:5px;padding-bottom:0;border: 1px solid #000;width:15%;">
            <div style="font-size:7px;text-align:center;margin-top:65px;">(Huella D.)</div>
          </td>
        </tr>
        @if($credito->participarconyugue_titular=='on')
        <tr>
          <td style="padding:5px;border: 1px solid #000;width:60%;height:70px;">
            <span><b>PRESTATARIO:</b> {{ $users_prestamo->nombrecompleto_pareja }}</span>
            <br>
            <span><b>RUC/DNI/CE: </b>{{ $users_prestamo->dni_pareja }}</span>
            <br>
            <span><b>DOMICILIO: </b>{{ $usuario->direccion }}, {{ $distrito }} - {{ $provincia }} - {{ $departamento }}</span>
          </td>
          <td style="padding:5px;padding-bottom:0;border: 1px solid #000;">
            <div style="font-size:7px;text-align:center;margin-top:65px;">(Firma)</div>
          </td>
          <td style="padding:5px;padding-bottom:0;border: 1px solid #000;">
            <div style="font-size:7px;text-align:center;margin-top:65px;">(Huella D.)</div>
          </td>
        </tr>
        @endif
        @if($aval!='')
        <tr>
          <td style="padding:5px;border: 1px solid #000;width:60%;height:70px;">
            <span><b>AVAL/FIADOR SOLIDARIO:</b> {{ $aval!=''?$aval->nombrecompleto:'' }}</span>
            <br>
            <span><b>RUC/DNI/CE: </b>{{ $aval!=''?$aval->identificacion:'' }}</span>
            <br>
            <span><b>DOMICILIO: </b> {{ $aval!=''?$aval->direccion:'' }}, {{ $distritoaval }} - {{ $provinciaaval }} - {{ $departamentoaval }}</span>
            <br>
            <span><b>REP. LEGAL:</b></span>
          </td>
          <td style="padding:5px;padding-bottom:0;border: 1px solid #000;">
            <div style="font-size:7px;text-align:center;margin-top:65px;">(Sello, Firma)</div>
          </td>
          <td style="padding:5px;padding-bottom:0;border: 1px solid #000;">
            <div style="font-size:7px;text-align:center;margin-top:65px;">(Huella D.)</div>
          </td>
        </tr>
        @endif
        @if($users_prestamo_aval!='' && $credito->participarconyugue_aval=='on')
        @if($users_prestamo_aval->dni_pareja!='' or $users_prestamo_aval->nombrecompleto_pareja!='')
        <tr>
          <td style="padding:5px;border: 1px solid #000;width:60%;height:70px;">
            <span><b>AVAL/FIADOR SOLIDARIO:</b> {{ $users_prestamo_aval->nombrecompleto_pareja }}</span>
            <br>
            <span><b>RUC/DNI/CE: </b>{{ $users_prestamo_aval->dni_pareja }}</span>
            <br>
            <span><b>DOMICILIO: </b> {{ $aval!=''?$aval->direccion:'' }}, {{ $distritoaval }} - {{ $provinciaaval }} - {{ $departamentoaval }}</span> 
          </td>
          <td style="padding:5px;padding-bottom:0;border: 1px solid #000;">
            <div style="font-size:7px;text-align:center;margin-top:65px;">(Firma)</div>
          </td>
          <td style="padding:5px;padding-bottom:0;border: 1px solid #000;">
            <div style="font-size:7px;text-align:center;margin-top:65px;">(Huella D.)</div>
          </td>
        </tr>
        @endif
        @endif
        
      </table>
    </div>
  </main>
</body>
</html>