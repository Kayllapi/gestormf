<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHECK-LIST</title>
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
          margin-bottom: 0.7cm;
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
    <div style="float:left;font-size:15px;">{{ $tienda->nombre }}</div> {{ Auth::user()->usuario }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
  </footer>
  
  <main>
    <h4 align="center" style="font-size:13px;margin:0;padding:0;">CHECK-LIST</h4>
    <div class="row">
      <div class="col">
        <table>
          <tr>
            <td>NRO CRÉDITO:</td>
            <td class="border-td" width="200px">S{{ str_pad($credito->id, 8, '0', STR_PAD_LEFT)  }}</td>
          </tr>
          
          <tr>
            <td>CLIENTE/RAZON SOCIAL:</td>
            <td class="border-td">{{ $credito->nombreclientecredito }}</td>
          </tr>
          @if($users_prestamo->dni_pareja!='' or $users_prestamo->nombrecompleto_pareja!='')
          <tr>
            <td>PAREJA:</td>
            <td class="border-td">{{ $users_prestamo->nombrecompleto_pareja }}</td>
          </tr>
          @endif
          <tr>
            <td>AGENCIA/OFICINA:</td>
            <td class="border-td">{{ $tienda->nombreagencia }}</td>
          </tr>
        </table>
      </div>
      <div class="col">
        <table>
          <tr>
            <td>FECHA:</td>
            <td class="border-td" width="130px">{{ date_format(date_create($credito->fecha),'Y-m-d') }}</td>
          </tr>
          <tr>
            <td>DNI/RUC</td>
            <td class="border-td">{{ $credito->docuementocliente }}</td>
          </tr>
          @if($users_prestamo->dni_pareja!='' or $users_prestamo->nombrecompleto_pareja!='')
          <tr>
            <td>DNI:</td>
            <td class="border-td">{{ $users_prestamo->dni_pareja }}</td>
          </tr>
          @endif
          
        </table>
      </div>
      <div class="col">
        <table>
          <tr>
            <td>AVAL:</td>
            <td class="border-td" width="130px">{{ $credito->nombreavalcredito }}</td>
          </tr>
          <tr>
            <td>DNI</td>
            <td class="border-td">{{ $credito->documentoaval }}</td>
          </tr>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <table class="table">
          <thead>
            <tr>
              <th rowspan=3>ITEM</th>
              <th rowspan=3 width="500px">DOCUMENTOS DEL EXPEDIENTE DE CRÉDITO</th>
              <th>EN PROPUESTA</th>
              <th>ANTES DE DESEMBOLSO</th>
            </tr>
            <tr>
              <th width="100px">Por Asesor(a) de créditos</th>
              <th width="100px">Por Ejecutivo de  Operaciones</th>
            </tr>
            <tr>
              <th colspan=2>Marcar Check( <span style="font-family: DejaVu Sans, sans-serif;">✔</span> ) por lo adjuntado para propuesta, caso contrario línea horizontal( __ )</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan=4>&nbsp;</td>
            </tr>
            <tr>
              <td><b>I.</b></td>
              <td colspan=3><b>INSTRUMENTOS DE EVALUACIÓN</b></td>
            </tr>
            <tr>
              <td>1.1</td>
              <td>Acta de Aprobación del comité de créditos</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>1.2</td>
              <td>Propuesta de crédito</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>1.3</td>
              <td>Estado de cuenta  e Historial interno del solicitante/pareja(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) y del garante/pareja(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>1.4</td>
              <td>Formato de evaluación de Crédito </td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>1.5</td>
              <td>Reporte y calificación por vinculación de riesgo único: Cliente (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;), Aval (garante)/Fiador(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>1.6</td>
              <td>Solicitud de crédito</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>1.7</td>
              <td>Croquis de domicilio del cliente(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) y croquis de local de negocio(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>1.8</td>
              <td>Ficha de información del garante(aval)/fiador(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)Declaración jurada patrimonial del Aval(garante)/Fiador(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) </td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>1.9</td>
              <td>Croquis de domicilio de garante (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) y croquis de local de negocio(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>1.10</td>
              <td>Otros, Citar:</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>1.11</td>
              <td colspan=2>Verificación  del número minimo de 3 Firmas y sellos del Comité de créditos, conforme firmas electrónicas, o correo electrónico del responsable de créditos</td>
              <td></td>
            </tr>
            <tr>
              <td>1.12</td>
              <td colspan=2>Verificar el Visado de cada documento por el ejecutivo(a) de créditos y las firmas y huellas digitales respectivas requeridas  en el expediente en general</td>
              <td></td>
            </tr>
            <tr>
              <td><b>II.</b></td>
              <td colspan=3><b>DOCUEMENTOS DE DATOS PERSONALES</b></td>
            </tr>
            <tr>
              <td>2.1</td>
              <td>DNI del cliente(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) Reporte Central de Riesgos(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>2.2</td>
              <td>DNI de la pareja (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) Reporte Central de Riesgos(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>2.3</td>
              <td>Recibo de Luz(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;), Recibo de Agua(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;), o Recibo de Teléfono/Cable(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) del domicilio de "Solicitante"</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>2.4</td>
              <td>Contrato de alquiler de vivienda(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)  "Solitante"</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>2.5</td>
              <td>DNI Aval(garante)/Fiador1 (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) Reporte Central de Riesgos1 (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) </td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>2.6</td>
              <td>DNI Parega de Aval(garante)/Fiador1 (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) Reporte Central de riesgos1(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>2.7</td>
              <td>DNI Aval(garante)/Fiador2 (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) Reporte Central de Riesgos2 (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) </td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>2.8</td>
              <td>DNI Parega de Aval(garante)/Fiador2 (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) Reporte Central de riesgos2(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>2.9</td>
              <td>Otros, Citar:</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>2.10</td>
              <td colspan=2>Verificación de Firmas y Huellas digitales del Solicitante/Garante (aval)/Fiador y parejas respectivas en los DNIs en los documentos, como Solicitud de crédito, ficha de garante, declaraciones juradas y otras</td>
              <td></td>
            </tr>
            <tr>
              <td>2.11</td>
              <td colspan=2>Verificación el estado civil de los intervenientes conforme DNI y suparticipación en el préstamo</td>
              <td></td>
            </tr>
            <tr>
              <td>2.12</td>
              <td colspan=2>Aprobación de excepción por la instancia correspondiente, caso no se considere firma de pareja</td>
              <td></td>
            </tr>
            <tr>
              <td>2.13</td>
              <td colspan=2>Verificación de Vigencia de DNI de Cliente/Pareja, Garantes/Pareja</td>
              <td></td>
            </tr>
            <tr>
              <td>2.14</td>
              <td colspan=2>Verificar que los datos de los DNIs de los Intervenientes esten registrados correctamente en El SISTEMA</td>
              <td></td>
            </tr>
            <tr>
              <td><b>III.</b></td>
              <td colspan=3><b>DOCUMENTOS DE INGRESO Y EGRESO</b></td>
            </tr>
            <tr>
              <td>3.1</td>
              <td>Sustento de Remuneración del cliente (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) Sustento de Remuneración de Pereja (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>3.2</td>
              <td>Sustento de Ingresos por Negocios del cliente (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) Sustento de ingresos por negocios de Pereja (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>3.3</td>
              <td>Documentos de OTROS ingresos del cliente(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) Documentos de OTROS ingresos de pareja(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>3.4</td>
              <td>Sustento de pagos de deudas del cliente(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) Sustento de pagos de deudas de pareja(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>3.5</td>
              <td>Otros, Citar:</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td><b>IV.</b></td>
              <td colspan=3><b>DOCUMENTOS DE GARANTÍA Cliente:</b> (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) Aval/Garante(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
              
            </tr>
            <tr>
              <td>4.1</td>
              <td>Minuta de C/V(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;), Testimonio de C/V(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;), Titulo de Propiedad(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;), Copia L. SUNARP(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)Constan. de Posesión</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>4.2</td>
              <td>Testimonio de garantía Mobiliaria registrada en SUNARP(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)Reporte de Garantia de ahorros(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>4.3</td>
              <td>Minuta de C/V(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;), Testimonio de C/V(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;), Titulo de Prop.(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;), Copia L. SUNARP (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) del Garante1</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>4.4</td>
              <td>Minuta de C/V(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;), Testimonio de C/V(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;), Titulo de Prop.(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;), Copia L. SUNARP (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) del Garante2</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>4.5</td>
              <td>Tasación(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;), Copia L. Certificada(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)  del Solicitante</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>4.6</td>
              <td>Tasación(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;), Copia L. Certificada(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)  del Garante1</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>4.7</td>
              <td>Tasación(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;), Copia L. Certificada(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)  del Garante2</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>4.8</td>
              <td>Otras Garantías Preferidas Citar:</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>4.9</td>
              <td>Otras Garantías NO Preferidas Citar:</td>
              <td></td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <div class="row">
      <div class="col">
        <table class="">
          <thead>
            <tr>
              <th width=200px class="border-td">Asesor de créditos</th>
              <th width=200px class="border-td">Ejecutivo de operaciones</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="border-bottom border-right">&nbsp;</td>
              <td class="border-bottom">&nbsp;</td>
            </tr>
            <tr>
              <td class="border-bottom border-right">&nbsp;</td>
              <td class="border-bottom">&nbsp;</td>
            </tr>
            <tr>
              <td class="border-bottom border-right">&nbsp;</td>
              <td class="border-bottom">&nbsp;</td>
            </tr>
            <tr>
              <td class="border-bottom border-right">&nbsp;</td>
              <td class="border-bottom">&nbsp;</td>
            </tr>
            <tr>
              <td class="border-bottom border-right">&nbsp;</td>
              <td class="border-bottom">&nbsp;</td>
            </tr>
            <tr>
              <td class="border-bottom border-right">&nbsp;</td>
              <td class="border-bottom">&nbsp;</td>
            </tr>
            <tr>
              <td class="border-bottom border-right">&nbsp;</td>
              <td class="border-bottom">&nbsp;</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col">
        <br><br><br>
        <div style="width:300px;height:1px;border-bottom:1px solid #ccc;"></div>
        <p align="center">Asesor(a) de Créditos: {{ Auth::user()->codigo }} <br>Firma y Sello</p>
        <br><br>
        <div style="width:300px;height:1px;border-bottom:1px solid #ccc;"></div>
        <p align="center">Ejecutivo(a) de Operaciones <br>Firma y Sello</p>
      </div>
    </div>
    <div class="row">
      <div class="col border-td">
        En casos donde no se cumpla con lo dispuesto y lo revisado, el personal asignado de operaciones bajo responsabilidad, NO DEBERÁ DESEMBOLSAR EL CRÉDITO hasta su regularización para ello debera comunicar al responsable del Comité de Créditos o jefatura respectiva.
      </div>
    </div>
    <div class="row">
      <div class="col">Fecha y hora de emisión: {{ date('d/m/Y, h:i a') }}</div>
      <div class="col">Usuario: {{ Auth::user()->nombre.' '.Auth::user()->apellidopaterno }}</div>
    </div>
  </main>
</body>
</html>