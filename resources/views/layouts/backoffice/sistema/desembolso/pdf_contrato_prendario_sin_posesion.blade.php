<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CONTRATO DE CRÉDITO 
CON GARANTÍA MOBILIARIA SIN POSESIÓN</title>
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
      .linea {
          width: 100%;
          border-top: 1px solid #000;
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
      <h4 align="center" style="font-size:13px;margin-top:10px;margin-bottom:10px;">CONTRATO DE CRÉDITO <br>CON GARANTÍA MOBILIARIA SIN POSESIÓN</h4>
      <!--div style="position:absolute; left:700px;top: 60px;">{{ $credito->cuenta!=''?$credito->cuenta:'00000000' }}</div-->
  <div style="text-align: justify;">
    Los que suscriben, por una parte, <b>{{ $tienda->nombre }}</b>, con RUC N° <b>{{ $tienda->ruc }}</b> y domicilio legal para 
    estos efectos en <b>{{ $tienda->direccion }}</b> Distrito de <b>{{ $ubigeo_tienda->distrito }}</b>, 
    Provincia de <b>{{ $ubigeo_tienda->provincia }}</b> y Departamento de <b>{{ $ubigeo_tienda->departamento }}</b> a 
    quién en lo sucesivo se le denominará como “EL ACREEDOR” y de otra parte “EL/LOS PRESTATARIO(S)”, cuyos datos se consignan al final del presente contrato.<br>
    
    Ambas partes y de forma separada, declaran tener la capacidad necesaria y la facultad suficiente, para la celebración 
    del presente. Hechas las declaraciones respectivas, ambas partes están de acuerdo en celebrar el presente contrato de 
    CRÉDITO, de producto denominado <b>{{ $credito->nombreproductocredito }}</b>, de conformidad con las siguientes cláusulas:
<br><br>
<b>I. CLÁUSULAS GENERALES</b>
<br>
    <b>1.1.</b> EL/LOS PRESTATARIO(S) declaran bajo juramento que los datos suministrados en ficha de información, solicitud, contrato 
    y demás documentos de crédito son verídicos y autoriza(n) a EL ACREEDOR el uso, para el seguimiento, control, evaluación y 
    clasificación del Préstamo otorgados a EL/LOS PRESTATARIO(S), conforme lo dispuesto en la Circular SBS N° 133-2010.<br>
    <b>1.2.</b> Los Canales de contratación que pone a disposición El ACREEDOR para atender a EL/LOS PRESTATARIO(S) son las oficinas y agencias, 
    página web, llamadas telefónicas, aplicaciones móviles, entre otros. EL ACREEDOR verificará la identidad de EL/LOS PRESTATARIO(S), 
    dejando constancia de su aceptación a través de las herramientas que tenga implementadas a tales efectos, conforme al marco legal aplicable 
    de ser el caso. Igualmente, durante la ejecución del presente contrato, EL ACREEDOR podrá usar medios directos de comunicación como: cartas a domicilio, 
    (simple o notarial) mensajes de texto SMS, llamadas telefónicas y otros canales digitales; también podrá utilizar los medios indirectos, 
    la escrita, audio visual, página web y otras determinadas por EL ACREEDOR.
    
<br><br>
<b>II.  SOBRE EL CRÉDITO</b>
<br>
    <b>2.1.</b> EL ACREEDOR otorga a EL/LOS PRESTATARIO(S), la suma de S/. <b>{{ $credito->monto_solicitado }}</b>, en condición de préstamo, 
    con pago <b>{{strtoupper($credito->forma_pago_credito_nombre)}}</b> en <b>{{$credito->cuotas}}</b> cuota(s), debiéndose pagar un total de S/. <b>{{ $credito->total_pagar }}</b> 
    al terminar el plazo pactado, que incluye capital más los intereses, comisión de servicio y gastos (cargos), a la tasa de interés, tasa de comisión de servicio y cargos establecidos en Hoja de Resumen del crédito; conforme lo informado y 
    aceptación de estos por parte de EL/LOS PRESTATARIO(S). <br>
    <b>2.2.</b> EL/LOS PRESTATARIO(S) se obliga a pagar a EL ACREEDOR el préstamo otorgado por este último más los intereses, comisión de servicio y gastos (cargos), 
    en la forma y plazo de pagos convenido de ambas partes conforme numeral 2.1; debiendo pagar en la misma moneda del crédito, 
    en las oficinas o agencias de EL ACREEDOR en forma de efectivo, transferencia bancaria, depósitos en cuenta, entre otras; 
    para lo cual se le otorga <b>{{ $credito->config_dias_tolerancia_garantia }}</b> días de tolerancia para el pago de las cuotas respectivas. Vencido los días de tolerancia 
    EL/LOS PRESTATARIO(S) pagará(n) el interés compensatorio y moratorio, custodia de garantía y cargos adicionales de ser el caso, 
    desde el primer día de su vencimiento, conforme tasas y penalidades establecidas en la hoja de resumen del crédito.<br>
    <b>2.3.</b> EL/LOS PRESTATARIO(S) podrá(n) solicitar ampliar su crédito vigente conforme amerite, disponiendo el monto parcial o total de 
    lo pagado a cuenta del crédito, conforme políticas establecidas. Para lo cual EL ACREEDOR procederá al recalculo del nuevo capital adeudado 
    por EL/LOS PRESTATARIO(S). El nuevo saldo de capital comprenderá los intereses, las comisiones de servicio, gastos(cargos) en caso correspondan. 
    La Ampliación generará la novación del Contrato de Crédito, por lo que, previamente se requerirá aceptación expresa de EL/LOS PRESTATARIO(S) dejándose 
    constancia de ello. De procederse con esta operación, EL ACREEDOR alcanzará los documentos correspondientes que detallen las nuevas condiciones 
    pactadas con EL/LOS PRESTATARIO(S), para lo cual será de aplicación el numeral 1.2 del Contrato.<br>
    <b>2.4.</b> EL ACREEDOR queda facultada a refinanciar la deuda de EL/LOS PRESTATARIO(S) en caso de incumplimiento de pago para lo cual 
    se incluirá el monto total de la deuda vencida, en una liquidación de saldo deudor conforme a sus reglas internas, como el 
    importe del capital saldo por pagar más intereses, servicios, intereses compensatorios y moratorios, custodia de garantía y 
    cargos establecidos en la Hoja Resumen del crédito. Bastando la intervención en dicha operación de cualquiera de los obligados.<br>
    <b>2.5.</b> Derechos y exigencias de Pagos Anticipados, serán a solicitud expresa de EL/LOS PRESTATARIO(S) como una opción y se 
    considerará las cuotas programadas a más de 30 días; de la misma forma, optará realizar de manera expresa pago de adelanto de cuotas. 
    Donde EL/LOS PRESTATARIO(S) declara haber sido informado con anterioridad al otorgamiento del crédito, considerándose para ello: (a) 
    El Pago Anticipado conlleva a la aplicación del monto al capital del crédito, con reducción de los intereses, comisión de servicio y 
    gastos al día del pago. (b) El Adelanto de Cuotas supone la aplicación del monto pagado a las cuotas inmediatamente posteriores a la 
    exigible en el periodo, sin que se produzca reducción de los intereses, comisión de servicio y gastos al día del pago.
    
<br><br>
<b>III. RESOLUCIÓN DEL CONTRATO POR INCUMPLIMIENTO DE PAGO Y OTROS</b>
<br>
    EL ACREEDOR podrá dar por vencidos todos los plazos del Préstamo resolviendo el Contrato y solicitando la ejecución de la Garantía Mobiliaria 
    constituida por EL/LOS PRESTATARIO(S) conforme este contrato, bastará que esta sea comunicada a EL/LOS PRESTATARIO(S) por los medios directos 
    previstos en el Contrato, por lo menos con tres (3) días de anticipación. Las causales que permitirán a EL ACREEDOR resolver de pleno derecho 
    el presente Contrato son las siguientes:<br>
    <b>3.1.</b> Al incumplimiento de pago de EL/LOS PRESTATARIO(S) sea el saldo o total del préstamo incluido intereses, comisión de servicio 
    y gastos. En créditos pagaderos hasta 2 meses de plazo y vencido, o en créditos pagaderos mayor a 2 meses de plazo, con 90 días a más de vencido la cuota de pago; 
    EL/LOS PRESTATARIO(S) tendrá(n) <b>{{ $credito->config_dias_tolerancia }}</b> días para su cancelación total o regularización, pagando custodia de ser el caso del bien(s) en garantía, 
    interés compensatorio y moratorio más cargos. Caso contrario EL ACREEDOR ejecutará la(s) garantías(s) entregada(s) en numeral 4.1. <br>
    <b>3.2.</b> EL/LOS PRESTATARIO(S) incurre en los supuestos previstos en el artículo 175 de Ley General del Sistema Financiero y del Sistema 
    de Seguros y Orgánica de la Superintendencia de Banca y Seguros, Ley N° 26702 <br>
    <b>3.3.</b> Otras permitidas por Ley, y que EL ACREEDOR implemente, comunicando oportunamente a EL/LOS PRESTATARIO(S).<br>
    <b>3.4.</b> EL/LOS PRESTATARIO(S) podrá resolver el presente Contrato, debiendo comunicar a EL ACREEDOR su decisión por los canales de 
    comunicación de numeral 1.2 y efectivizar dentro de los 30 días una vez comunicado a EL ACREEDOR. Para ello EL/LOS PRESTATARIO(S) deberá 
    previamente cancelar la totalidad de la obligación que mantenga pendiente de pago con EL ACREEDOR.<br>
    
<br>
<b>IV.  DE LA GARANTÍA MOBILIARIA</b>
<br>
    <b>4.1.</b> La garantía(s) mobiliaria(s) sin posesión que constituye(n) EL/LOS PRESTATARIO(S) a favor de EL ACREEDOR será de primera y preferente, siendo el (los) 
    siguiente(s) bien(es) que respaldará(n) el cumplimiento del pago del préstamo y de los futuros créditos que EL/LOS PRESTATARIO(S) pueda(n) obtener.<br>
    
    <br>
          <table style="width:100%;">
            <tr>
              <th style="border-bottom: 1px solid #000;">Código</th>
              <th style="border-bottom: 1px solid #000;">Bien</th>
              <th style="border-bottom: 1px solid #000;">Accesorios</th>
              <th style="border-bottom: 1px solid #000;">Año F.</th>
              <th style="border-bottom: 1px solid #000;">Color</th>
              <th style="border-bottom: 1px solid #000;">Serie/Motor/N°Partida</th>
              <th style="border-bottom: 1px solid #000;">Chasis</th>
              <th style="border-bottom: 1px solid #000;">Modelo</th>
              <th style="border-bottom: 1px solid #000;">Placa</th>
              <th style="border-bottom: 1px solid #000;">Estado</th>
            </tr>
            @foreach($garantias as $value)
              <tr>
                  <td>GP{{ str_pad($value->id, 8, '0', STR_PAD_LEFT)  }}</td>
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
            @endforeach
          </table>
    <br>
    
    <b>4.2.</b> Para la valorización de la Garantía Mobiliaria, EL ACREEDOR podrá requerir a EL/LOS PRESTATARIO(S) constancia de valorización 
    expedida por un tercero apto, que estará sujeto a la aprobación de EL ACREEDOR como condición previa para el otorgamiento del Préstamo. 
    Dicha valorización deberá respetar los parámetros estandarizados de tasación y precios de mercado. Los valores de cobertura (gravamen) y 
    comercial de la garantía(s), se detallan en la Hoja de resumen del crédito.<br>
    
    <b>4.3.</b> En caso de pérdida total o parcial, por causas no imputables a EL ACREEDOR, del (de los) bien(es) dado(s) en Garantía Mobiliaria, 
    o en caso de reducción de su valor en el transcurso del tiempo, EL/LOS PRESTATARIO(S) se compromete a otorgar en Garantía Mobiliaria otro(s) 
    bien(es) en el plazo máximo de seis (6) días útiles desde que este último tomó conocimiento de dicha situación o desde que ello le fue requerido 
    por EL ACREEDOR por alguno de los medios directos previstos en el numeral 1.2. EL/LOS PRESTATARIO(S) se compromete a que la valorización de la 
    Garantía Mobiliaria siempre ascienda a la detallada en la Hoja Resumen. El incumplimiento de esta obligación dará derecho a EL ACREEDOR a ejecutar 
    de inmediato la Garantía Mobiliaria.<br>
<b>4.4.</b><b> Del depositario. </b> La garantía(s) mobiliaria(s) que se constituye en respaldo del préstamo se realiza sin entrega de posesión del 
    (de los) bien(es) mueble(s) a EL ACREEDOR, modalidad de garantía mobiliaria conforme Ley de Garantía Mobiliaria N° 28677, Decreto Legislativo N° 1400 
    (o norma que la modifique o reemplace), en virtud de la cual se nombra DEPOSITARIO del (de los) bien(es) detallados en numeral 4.1. a EL/LOS PRESTATARIO(S)  
    y se detallan en Hoja de resumen del crédito entregado; siendo lugar de depósito en el domicilio del DEPOSITARIO.<br>
EL/LOS PRESTATARIO(S) podrá(n) designar un DEPOSITARIO tercero idóneo para que custodie el (los) bien(es) dado(s) en garantía. En ese caso, comunicará dicha 
    decisión a EL ACREEDOR, y esta última tendrá derecho a Adherirse al Contrato de Custodia que celebre EL/LOS PRESTATARIO(S) con el DEPOSITARIO, 
    asumiendo este último todas las obligaciones contempladas en la Ley de Garantía Mobiliaria, incluso la de entregar el bien gravado al Representante 
    Común cuando ello sea requerido conforme lo pactado en el Contrato del cual forma parte la presente cláusula adicional. El DEPOSITARIO designado 
    deberá contratar un seguro contra todo riesgo a satisfacción de EL ACREEDOR y endosar la póliza a nombre de esta última incluyendo sus renovaciones. 
    El seguro se mantendrá vigente hasta la cancelación total de la(s) obligación(es) garantizada(s). En caso de incumplimiento de esta obligación, 
    EL ACREEDOR contratará el seguro y trasladará dicho costo a EL/LOS PRESTATARIO(S). <br>
En caso EL/LOS PRESTATARIO(S) mantenga la calidad de DEPOSITARIO deberá contratar una póliza de seguros contra todo riesgo que cubra el valor del gravamen. 
    Dicha póliza deberá ser endosada a nombre de EL ACREEDOR y renovada hasta la cancelación total de la(s) obligación(es) garantizada(s). En caso incumpla 
    con esta obligación, EL ACREEDOR contratará el seguro y trasladará dicho costo a EL/LOS PRESTATARIO(S).<br>
<b>4.5.</b> EL/LOS PRESTATARIO(S) manifiesta(n) bajo juramento, que es el único y legítimo propietario del (de los) bien(es) otorgado(s) en Garantía Mobiliaria, 
    cláusula 4.1, realmente le corresponden y no contienen adulteraciones. De determinarse lo contrario a lo declarado por EL/LOS PRESTATARIO(S), este asumirá 
    todas las responsabilidades civiles y penales, frente a EL ACREEDOR y terceros. En tal caso EL ACREEDOR, procederá a resolver el presente Contrato en 
    virtud a Resolución SBS N° 3274-2017 artículos 40 y 41, quedando EL/LOS PRESTATARIO(S) obligado(s) a pagar a EL ACREEDOR un monto igual al saldo del Préstamo, 
    intereses y comisiones de servicio más los intereses compensatorios y moratorios de obligaciones impagas y gastos.<br>
EL/LOS PRESTATARIO(S) reconoce(n) que, como parte de la evaluación para el otorgamiento del préstamo, EL ACREEDOR le solicitó la presentación del comprobante de pago 
    de la adquisición del (de los) bien(es) materia de la garantía mobiliaria. Al no contar con dicho comprobante, EL/LOS PRESTATARIO(S) suscribe(n) la presente declaración 
    jurada, la cual forma parte integral del contrato de préstamo.<br>
<br>
<b>V. EJECUCIÓN DE LA GARANTÍA</b>
<br>    
    Las partes acuerdan que El ACREEDOR ejecutará la(s) garantía(s) mobiliaria de numeral 4.1. ante el incumplimiento de la Obligación Garantizada, 
    así como, luego de vencido los plazos otorgados en numeral 3.1 a través de la Venta Extrajudicial con venta privada o subasta pública y a elección 
    indistinta de EL ACREEDOR, conforme Ley N° 28677 Artículo 10 y 12, Decreto Legislativo N° 1400 (o norma que la modifique o reemplace); para lo cual 
    el EL/LOS PRESTATARIO(S) autoriza(n) a EL ACREEDOR con pleno conocimiento al suscribir el presente contrato. EL ACREEDOR en Venta Privada no requerirá 
    realizar publicación en medios de comunicación de mayor circulación; tampoco requiere de comunicación previa por cualquier medio directo a EL/LOS PRESTATARIO(S), 
    siendo una facultad que podrá ejercer EL ACREEDOR discrecionalmente, en venta por Subasta Pública deberá notificar a EL/LOS PRESTATARIO(S) mediante la publicación 
    de un aviso en medios de mayor circulación o en la página web de EL ACREEDOR, y el producto de la venta mencionada será aplicada al pago de la deuda. 
    El precio base de venta en Subasta Pública no será menor de los 2/3 de la valorización pactado de la garantía mobiliaria descrita en Hoja Resumen y de existir 
    remanente, este se pondrá a disposición de EL/LOS PRESTATARIO(S) en plazo máximo de 15 días calendarios que se comunicará conforme numeral 1.2. De no presentarse 
    postores a la Subasta, o no ser posible la Venta Privada, el (los) bien(es) se adjudicará(n) directamente a EL ACREEDOR para la cancelación del saldo de la deuda.
<br>
<br>
<b>VI. EMISIÓN DE TITULO VALOR INCOMPLETO</b>
<br>
    
    <b>6.1.</b> EL/LOS PRESTATARIO(S), en respaldo de su obligación, emiten y aceptan un pagaré incompleto o de ser el caso una Letra de cambio incompleta 
    de acuerdo con lo establecido en el Artículo 10° de la Ley 27287 de Títulos Valores, que podrá ser prorrogado o renovado por EL ACREEDOR con su simple 
    indicación en el título valor y sin que tales prórrogas o renovaciones puedan considerarse como una novación. EL/LOS PRESTATARIO(S) renuncia(n) la facultad 
    de incorporar al presente cualquier cláusula que limite o impida la libre negociación y/o cesión de los títulos valores, aceptando y autorizando la negociación 
    y/o cesión del título valor sin necesidad de comunicación futura y hacen constancia de haber sido informados e instruidos sobre los mecanismos y amparos de ley 
    sobre la emisión del título valor incompleto.<br>
    <b>6.2.</b> EL/LOS PRESTATARIO(S) declaran haber recibido copia del pagaré o Letra de cambio incompleta y autorizan de manera expresa a EL ACREEDOR a completar 
    dicho título valor respectivo cuando incumpla el pago de las cuotas según su cronograma de pagos o se produjeran las causales de resolución contractual 
    establecido en los numerales 3.1, 3.2 y 3.3.<br>
<br>
<b>VII.REPRESENTANTE COMÚN</b>
<br>
    Las Partes designan como Representante Común conforme Ley, para ejecutar la Garantía Mobiliaria en caso de incumplimiento en el pago de la Obligación Garantizada 
    a la(s) persona(s) indicada(s) en la Hoja de Resumen del crédito. Para que en forma individual e indistinta puedan realizar y formalizar el Remate Público o la 
    Venta Privada o la Adjudicación a EL ACREEDOR, del (de los) bien(es) otorgado(s) en Garantía Mobiliaria; otorgándole ambas partes Poder Específico, suficiente e 
    irrevocable, para tal fin.

<br><br>
<b>VIII. CESIÓN</b>
<br>
    EL/LOS PRESTATARIO(S) a través del presente autoriza a EL ACREEDOR para que este última pueda ceder o transmitir total o parcialmente todos los derechos y obligaciones 
    derivados del Contrato en favor de un tercero. Asimismo, EL ACREEDOR podrá afectar o dar en garantía, cualquiera que sea la forma que esté prevista, 
    los derechos que el Contrato le confiere en aplicación de los artículos 1206° y 1211° del Código Civil. El ACREEDOR comunicará a EL/LOS PRESTATARIO(S) 
    mediante aviso escrito al domicilio, correo electrónico, o comunicaciones telefónicas la cesión de derechos con carácter informativo. EL/LOS PRESTATARIO(S) por 
    su parte no podrá ceder su posición contractual a terceros, salvo autorice EL ACREEDOR.
<br><br>
    <?php
    $meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
    $mes = $meses[(date_format(date_create($credito->fecha_desembolso),'n')) - 1];
    $fecha_texto = date_format(date_create($credito->fecha_desembolso),'d') . ' de ' . $mes . ' de ' . date_format(date_create($credito->fecha_desembolso),'Y');
    ?>
<b>IX. VIGENCIA.</b> <br>
    Indeterminada hasta la cancelación total de la Obligación Garantizada.
<br><br>
    <b>X. JURISDICCIÓN Y COMPETENCIA.</b><br>
    Las partes renuncian al fuero de sus domicilios y se someten a la jurisdicción del lugar de celebración del Contrato. Todo cambio de domicilio de EL/LOS 
    PRESTATARIO(S) solo surtirá efecto desde su puesta en conocimiento a EL ACREEDOR, a través de comunicación física o electrónica, con una anticipación no 
    menor de cinco (5) días útiles.<br><br>
    <b>XI. ACEPTACIÓN DEL CONTRATO</b><br>
    En mi condición de EL/LOS PRESTATARIO(S), declaro haber recibido la hoja de resumen del préstamo, cronograma de pagos y el dinero respectivo; asimismo la información sobre las políticas y condiciones del préstamo, aceptando las mismas. <br><br>
    En fe a la verdad, suscribimos el presente contrato en la ciudad de <b>{{ $ubigeo_tienda->distrito }}</b>, a <b>{{ $fecha_texto }}</b>.<br>
</div>

<br><br>
<br><br>
      <div style="width:175px;margin-top: 30px;float:left;margin-right:5px;">
            <div class="linea"></div>
            <span style="padding-top:10px;"><b>{{ $usuario->nombrecompleto }}</b></span>
            <br>
            <span><b>RUC/DNI/CE: </b>{{ $usuario->identificacion }}</span>
            <br>
            <span><b>Domicilio: </b>{{ $usuario->direccion }}, {{ $distrito }} - {{ $provincia }} - {{ $departamento }}</span>
            <br>
            <span><b>EL/LOS PRESTATARIO(S)</b></span>
      </div>
      <div style="width:100px;margin-top: 10px;height:100px;float:left;margin-right:10px;border: 1px solid #000;">
      </div>
      <div style="width:175px;margin-top: 30px;float:left;margin-right:5px;">
            <div class="linea"></div>
            <span style="padding-top:10px;"><b><?php echo $aval!=''?$aval->nombrecompleto:'' ?></b></span>
            <br>
            <span><b>RUC/DNI/CE: </b><?php echo $aval!=''?$aval->identificacion:'' ?></span>
            <br>
            <span><b>Domicilio: </b> {{ $aval!=''?$aval->direccion:'' }}, {{ $distritoaval }} - {{ $provinciaaval }} - {{ $departamentoaval }}</span>
            <br>
            <span><b>EL/LOS PRESTATARIO(S)</b></span>
      </div>
      <div style="width:100px;margin-top: 10px;height:100px;float:left;margin-right:10px;border: 1px solid #000;">
      </div>
      <div style="width:157px;margin-top: -35px;float:left;">
            @if($tienda->firma!='')
            <div style="text-align:center">
            <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$tienda->firma) }}" width="100px"></div>
            @endif
            <div class="linea"></div>
            <span style="padding-top:10px;"><b>{{ $tienda->nombre }}</b></span>
            <br>
            <span>{{ $tienda->representante }}</span>
            <br>
            <span><b>Representante Legal</b></span>
            <br>
            <span><b>EL ACREEDOR</b></span>
      </div>
    </div>
  </main>
</body>
</html>