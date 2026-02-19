<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CONTRATO PRENDARIO</title>
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
    <div style="float:left;font-size:15px;">{{ $tienda->nombre }}</div> C{{ str_pad($credito->cuenta, 8, "0", STR_PAD_LEFT) }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
    <p class="datafooter">{{ $tienda->nombreagencia }}<br>
    {{ Auth::user()->codigo }}</p>
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
      <h4 align="center" style="font-size:13px;margin-top:2px;margin-bottom:2px;">CONTRATO DE CRÉDITO</h4>
      <!--div style="position:absolute; left:700px;top: 60px;">{{ $credito->cuenta!=''?$credito->cuenta:'00000000' }}</div-->
  <div style="text-align: justify;">
Los que suscriben, por una parte, <b>{{ $tienda->nombre }}</b>, con RUC N° <b>{{ $tienda->ruc }}</b> y domicilio legal para 
    estos efectos en {{ $tienda->direccion }} Distrito de {{ $ubigeo_tienda->distrito }}, 
    Provincia de {{ $ubigeo_tienda->provincia }} y Departamento de {{ $ubigeo_tienda->departamento }} a 
    quién en los sucesivo se le denominará como “EL ACREEDOR” y de otra parte Sr(a) <b>{{ $usuario->nombrecompleto }}</b>, 
    peruano(a), mayor de edad, con DNI N° <b>{{ $usuario->identificacion }}</b>, con domicilio en {{ $usuario->direccion }}, 
    Distrito de {{ $distrito }}, Provincia de {{ $provincia }} y Departamento de {{ $departamento }} a quién en los sucesivo 
    se le denominará como “EL/LOS PRESTATARIO(S)”.
Ambas partes y de forma separada, declaran tener la capacidad necesaria y la facultad suficiente, para la celebración 
    del mismo. 
    Hechas las declaraciones respectivas, ambas partes están de acuerdo en celebrar el presente contrato de CRÉDITO, 
    producto <b>{{ $credito->nombreproductocredito }}</b>, de conformidad con las siguientes cláusulas: 
<br><br>
    <b>I.</b> EL ACREEDOR otorga a EL/LOS PRESTATARIO(S), la suma de S/. {{ $credito->monto_solicitado }}, en condición de préstamo, 
    con pago {{strtoupper($credito->forma_pago_credito_nombre)}} en {{$credito->cuotas}} cuota(s), debiéndose pagar un total de S/. {{ $credito->total_pagar }} al terminar el plazo, 
    que incluye capital más los intereses, servicios y otros, conforme lo informado y aceptación de estos por parte de 
    EL/LOS PRESTATARIO(S). La entrega del monto total del préstamo, hoja de resumen del mismo y el cronograma de pagos, 
    se realizan en el momento de firmado del presente contrato. 
<br><br>
        <b>II.</b> EL/LOS PRESTATARIO(S) se obliga a pagar a EL ACREEDOR el monto total del préstamo otorgado por este último más 
    los intereses, servicios y otros, en la forma y plazo de pagos convenido de ambas partes conforme cláusula I; 
    para lo cual se le otorga los {{ $credito->config_dias_tolerancia_garantia }} días de tolerancia para los pagos respectivos. 
    Vencida el plazo convenido y los 
    días de tolerancia EL/LOS PRESTATARIO(S) pagará(n) el interés adicional del saldo total de capital vencido desde 
    el primer día de su vencimiento a la tasa de interés moratoria establecida conforme hoja de resumen; asimismo 
    EL/LOS PRESTATARIO(S) deberá pagar por custodia de la garantía prendaria de la deuda vencida, conforme tarifario 
    establecida en hoja resumen respectivo. 
<br><br>
    
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
        <b>III.</b> Si el pago de la(s) cuota(s) del crédito no es cubierto a los {{ $credito->config_dias_tolerancia_garantia }} días de su vencimiento, 
    EL/LOS PRESTATARIO(S) 
    se obliga a pagar a EL ACREEDOR una PENALIDAD de {{ $penalidad }} % de la cuota impaga, más el interes moratorio de {{$credito->config_tasa_moratoria}}% mensual. 
<br><br>
        <b>IV.</b> EL/LOS PRESTATARIO(S) efectuará sus pagos en el establecimiento de pago respectivo de EL ACREEDOR y sin necesidad 
    de cobro previo. 
<br><br>
        <b>V.</b> los derechos y exigencias de Pagos Anticipados, serán a solicitud expresa de EL/LOS PRESTATARIO( S) como una opción 
    y se considerará sólo las cuotas programadas a más de 30 días conforme política interna de EL ACREEDOR; de la misma forma, podrá optar realizar  de
    manera expresa pago de adelanto de cuotas. Donde EL/LOS PRESTATARIO( S) declara haber sido informado con anterioridad al otorgamiento del crédito de 
    la diferencia de pago anticipado y adelanto de cuotas
<br><br>
        <b>VI.</b> La garantía que presenta EL/LOS PRESTATARIO(S) a favor de EL ACREEDOR constituye de un(os) bien(es) en PRENDA.
    
    <br>
    <br>
          <table style="width:100%;">
            <tr>
              <th style="border-bottom: 2px solid #000;">Código</th>
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
    <?php
    $meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
    $mes = $meses[(date_format(date_create($credito->fecha_desembolso),'n')) - 1];
    $fecha_texto = date_format(date_create($credito->fecha_desembolso),'d') . ' de ' . $mes . ' de ' . date_format(date_create($credito->fecha_desembolso),'Y');
    ?>
        <b>VII.</b> Al incumplimiento de pago de EL/LOS PRESTATARIO(S) a EL ACREEDOR sea el saldo o total del préstamo otorgado, al 
    vencimiento del plazo pactado conforme cláusula I. del presente, EL/LOS PRESTATARIO(S) tendrá {{$credito->config_dias_tolerancia}} días para su 
    cancelación total incluida la penalidad de tenencia de garantía prendaria conforme hoja resumen y más cargos, caso contrario la(s) prenda(s) en garantía otorgadas conforme cláusula Quinta serán Liquidadas o 
    Rematadas sin previa notificación; para lo cual el EL/LOS PRESTATARIO(S) autoriza a EL ACREEDOR con pleno conocimiento 
    al suscribir el presente contrato, o en su defecto para la Ejecución de la Garantía conforme Ley N° 28677. En mi 
    condición de EL/LOS PRESTATARIO(S), declaro haber recibido la hoja de resumen del préstamo, cronograma de pagos y 
    la información respectiva sobre las políticas y condiciones del préstamo, aceptando los mismos y haber recibido 
    el dinero a mi entera satisfacción siendo el monto de S/. {{ $credito->monto_solicitado }}. En fe a la verdad, se suscribe el presente 
    documento en la ciudad de {{ $ubigeo_tienda->distrito }}, a {{ $fecha_texto }}.
</div>

<br><br>
      <div style="width:175px;margin-top: 30px;float:left;margin-right:5px;">
            <hr style="border: 1px solid #000;">
            <span style="padding-top:10px;"><b>{{ $usuario->nombrecompleto }}</b></span>
            <br>
            <span><b>DNI: </b>{{ $usuario->identificacion }}</span>
            <br>
            <span><b>Domicilio: </b>{{ $usuario->direccion }}, {{ $distrito }} - {{ $provincia }} - {{ $departamento }}</span>
            <br>
            <span><b>EL/LOS PRESTATARIO(S)</b></span>
      </div>
      <div style="width:100px;margin-top: 10px;height:130px;float:left;margin-right:10px;border: 1px solid #000;">
      </div>
      <div style="width:175px;margin-top: 30px;float:left;margin-right:5px;">
            <hr style="border: 1px solid #000;">
            <span style="padding-top:10px;"><b><?php echo $aval!=''?$aval->nombrecompleto:'' ?></b></span>
            <br>
            <span><b>DNI: </b><?php echo $aval!=''?$aval->identificacion:'' ?></span>
            <br>
            <span><b>Domicilio: </b> {{ $aval!=''?$aval->direccion:'' }}, {{ $distritoaval }} - {{ $provinciaaval }} - {{ $departamentoaval }}</span>
            <br>
            <span><b>EL/LOS PRESTATARIO(S)</b></span>
      </div>
      <div style="width:100px;margin-top: 10px;height:130px;float:left;margin-right:10px;border: 1px solid #000;">
      </div>
      <div style="width:157px;margin-top: -45px;float:left;">
            @if($tienda->firma!='')
            <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$tienda->firma) }}" width="140px">
            @endif
            <hr style="border: 1px solid #000;">
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