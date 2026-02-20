<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FORMATO DE EVALUACIÓN</title>
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
        
        
        
      .campo_moneda {
          text-align: right;
      } 
  
  

     </style>
</head>
<body>
  @php
      $adicional_ingreso_mensual = $credito_formato_evaluacion ? ( $credito_formato_evaluacion->adicional_ingreso_mensual == "" ? [] : json_decode($credito_formato_evaluacion->adicional_ingreso_mensual) ) : [];
      $adicional_egresos_mensual = $credito_formato_evaluacion ? ( $credito_formato_evaluacion->adicional_egresos_mensual == "" ? [] : json_decode($credito_formato_evaluacion->adicional_egresos_mensual) ) : [];
      $deudas_financieras = $credito_formato_evaluacion ? ( $credito_formato_evaluacion->deudas_financieras == "" ? [] : json_decode($credito_formato_evaluacion->deudas_financieras) ) : [];
      $referencia = $credito_formato_evaluacion ? ( $credito_formato_evaluacion->referencia == "" ? [] : json_decode($credito_formato_evaluacion->referencia) ) : [];
    @endphp
  <header>
    <div style="float:left;font-size:15px;">{{ $tienda->nombre }}</div> {{ Auth::user()->usuario }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
  </footer>
  
  <main>
    <h4 align="center" style="font-size:13px;margin:0;padding:0;">EVALUACIÓN DE CRÉDITOS <br> EVALUACIÓN DE CRÉDITO - INGRESOS DEPENDIENTE <br>CRÉDITO: CONSUMO NO REVOLVENTE</h4>
    <br><div class="row">
      <div class="col" style="width:290px;">
        <table style="width:100%;">
          <tr>
            <td width="100px">AGENCIA/OFICINA:</td>
            <td class="border-td">{{ $tienda->nombreagencia }}</td>
          </tr>
          <tr>
            <td>Cliente (A. y N.):</td>
            <td class="border-td">{{ $credito->nombreclientecredito }}</td>
          </tr>
          <tr>
            <td>DNI/RUC:</td>
            <td class="border-td">{{ $credito->docuementocliente }}</td>
          </tr>
          @if($users_prestamo->dni_pareja!='' or $users_prestamo->nombrecompleto_pareja!='')
          <tr>
            <td>PAREJA:</td>
            <td class="border-td">{{ $users_prestamo->nombrecompleto_pareja }}</td>
          </tr>
          <tr>
            <td>DNI:</td>
            <td class="border-td">{{ $users_prestamo->dni_pareja }}</td>
          </tr>
          @endif
        </table>
      </div>
      <div class="col" style="width:185px;">
        <table style="width:100%;">
          <tr>
            <td width="100px">F. Evaluación:</td>
            <td class="border-td">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->fecha : date_format(date_create($credito->fecha),'d/m/Y') }}</td>
          </tr>
          <tr>
            <td>Nro de Solicitud:</td>
            <td class="border-td">S{{ str_pad($credito->id, 8, '0', STR_PAD_LEFT) }}</td>
          </tr>
          <tr>
            <td>Monto solicitado:</td>
            <td class="border-td">{{ $credito->monto_solicitado }}</td>
          </tr>
          <tr>
            <td>Nro de cuotas:</td>
            <td class="border-td">{{ $credito->cuotas }}</td>
          </tr>
          <tr>
            <td>Forma de Pago:</td>
            <td class="border-td">{{ $credito->forma_pago_credito_nombre }}</td>
          </tr>
        </table>
      </div>
      <div class="col" style="width:238px;">
        <table style="width:100%;">
          <tr>
            <td width="100px">Producto:</td>
            <td class="border-td">{{ $credito->nombreproductocredito }}</td>
          </tr>
          <tr>  
            <td>Modalidad:</td>
            <td class="border-td">{{ $credito->modalidad_credito_nombre }}</td>
          </tr>
          <tr>  
            <td>Tipo de cliente:</td>
            <td class="border-td">{{ $credito->tipo_operacion_credito_nombre }}</td>
          </tr>
          <tr>  
            <td>Destino de Crédito:</td>
            <td class="border-td">{{ $credito->tipo_destino_credito_nombre}}</td>
          </tr>
        </table>
      </div>
    </div>
    <span class="badge">I.  INGRESOS Y GASTOS</span>
    <div class="row">
      <div class="col" style="width:159px;">
        <table class="table">
          <thead>
            <tr>
              <th colspan=2>Ingreso Mensuales (Cliente y Pareja)</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Remuneración Total  Neta Cliente</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->remuneracion_total_cliente : '0.00' }}</td>
            </tr>
            <tr>
              <td>Remuneración variable</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->remuneracion_variable : '0.00' }}</td>
            </tr>
            <tr>
              <td>Remuneración de la Pareja</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->remuneracion_pareja : '0.00' }}</td>
            </tr>
            <tr>
              <td colspan=2>OTROS: (Remesas, pensión, Etc.)</td>
            </tr>
            @foreach($adicional_ingreso_mensual as $value)
              <tr>
                <td>{{ $value->descripcion }}</td>
                <td class="campo_moneda">{{ $value->monto }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td class="campo_moneda">TOTAL (S/.)</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->total_ingresos_mensuales : '0.00' }}</td>
            </tr>
          </tfoot>
        </table>
        <br>
        <table class="table" width="100%">
          <tbody>
            <tr>
              <td>Número total de Hijos</td>
              <td>{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->numero_total_hijos : '0.00' }}</td>
            </tr>
            <tr>
              <td>Total de hijos dependientes</td>
              <td>{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->total_hijos_dependientes : '0.00' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col" style="width:210px;">
        <table class="table">
          <thead>
            <tr>
              <th colspan=2>Egresos Mensuales (Cliente y Pareja)</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Pago de cuotas de deuda (Reporte RCC Reg. y no Reg.)</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->pago_cuotas_deuda : '0.00' }}</td>
            </tr>
            <tr>
              <td>Alimentación</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_alimentacion : '0.00' }}</td>
            </tr>
            <tr>
              <td>Salud</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_salud : '0.00' }}</td>
            </tr>
            <tr>
              <td>Educación</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_educacion : '0.00' }}</td>
            </tr>
            <tr>
              <td>Alquiler de vivienda</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_alquiler_vivienda : '0.00' }}</td>
            </tr>
            <tr>
              <td>Mobilidad</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_mobilidad : '0.00' }}</td>
            </tr>
            <tr>
              <td>S. de Luz</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_luz : '0.00' }}</td>
            </tr>
            <tr>
              <td>S. de Agua y Acantarillado</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_agua : '0.00' }}</td>
            </tr>
            <tr>
              <td>Teléfono</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_telefono : '0.00' }}</td>
            </tr>
            <tr>
              <td>Cable</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_cable : '0.00' }}</td>
            </tr>
            <tr>
              <td>Otros gastos personales ({{ configuracion($tienda->id,'provision_gastos_familiares')['valor'] }}%):</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->otros_gastos_personales : '0.00' }}</td>
            </tr>
            <tr>
              <td>Pensión de Alimentos</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_pension_alimentos : '0.00' }}</td>
            </tr>
            @foreach($adicional_egresos_mensual as $value)
              <tr>
                <td>{{ $value->descripcion }}</td>
                <td class="campo_moneda">{{ $value->monto }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td class="campo_moneda">TOTAL (S/.)</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->total_egresos_mensuales : '0.00' }}</td>
            </tr>
          </tfoot>
        </table>
        <br>
        <table class="table" width="100%">
          <tbody>
            <tr>
              <td style="text-align:center;">EXCEDENTE MENSUAL DISPONIBLE (S/.)</td>
            </tr>
            <tr>
              <td  class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->excedente_mensual_disponible : '0.00' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col" style="width:350px;">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th rowspan=2 width="50px">Deudas</th>
              <th>Inst. Finan.</th>
              <th rowspan=2>Saldo Capital</th>
              <th rowspan=2>Cuota Mensual</th>
              <th rowspan=2>CUOTA Ampliación/Compra de deuda</th>
            </tr>
            <tr>
              <th>PROVISIONAR LÍNEAS DE CRÉDITO NO USADAS: Consumo a 24 meses Cptal. de trabajo a 36 meses</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="5">INSTITUCIONES FINANCIERAS (Reguladas y no Reguladas)</td>
            </tr>
            @foreach($deudas_financieras as $value)
              <tr>
                <td>{{ $value->tipo }}</td>
                <td>{{ $value->banco }}</td>
                <td class="campo_moneda">{{ $value->saldo }}</td>
                <td class="campo_moneda">{{ $value->cuota }}</td>
                <td class="campo_moneda">{{ $value->ampliacion }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="5">{{ $tienda->nombre }}</td>
            </tr>
            <tr>
              <td>CLIENTE</td>
              <td></td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->saldo_capita_cliente : '0.00' }}</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->couta_mensual_cliente : '0.00' }}</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->cuota_ampliacion_cliente : '0.00' }}</td>
            </tr>
            <tr>
              <td>PAREJA</td>
              <td></td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->saldo_capita_pareja : '0.00' }}</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->couta_mensual_pareja : '0.00' }}</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->cuota_ampliacion_pareja : '0.00' }}</td>
            </tr>
            <tr>
              <td class="campo_moneda" colspan=2>TOTAL (S/.)</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->total_saldo_capital : '0.00' }}</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->total_couta_mensual : '0.00' }}</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->total_couta_ampliacion : '0.00' }}</td>
            </tr>
          </tfoot>
        </table>
        <br>
        <table class="table table-bordered mt-2" width="100%" id="table-entidad-financiera">
          <tbody>
            <tr>
              <td colspan=2>N° DE ENTIDADES FINANCIERAS</td>
            </tr>
            <tr>
              <td width="200px">CLIENTE</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->entidad_financiera_cliente : '0.00' }}</td>
            </tr>
            <tr>
              <td>PAREJA</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->entidad_financiera_pareja : '0.00' }}</td>
            </tr>
            <tr>
              <td class="fw-bold campo_moneda">TOTAL</td>
              <td class="campo_moneda">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->entidad_financiera_total : '0.00' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <span class="badge">II.  PROPUESTA DE CRÉDITO Y REFERENCIAS</span>
    <div class="row">
      <div class="col" style="width:610px;">

        @foreach($forma_pago_credito as $value)
          @if( $credito->idforma_pago_credito == $value->id)
            @php $nombre_forma_pago = $value->nombre; @endphp
          @endif
        @endforeach
        <table class="table" width="100%">
          <thead>
            <tr>
              <th colspan=10>PROPUESTA</th>
            </tr>
            <tr>
              <th rowspan=2>DESTINO DE CRÉDITO</th>
              <th rowspan=2>Producto</th>
              <th colspan=2>Plazo</th>
              <th rowspan=2>FORMA DE PAGO</th>
              <th rowspan=2>Monto Préstamo</th>
              <th rowspan=2>TEM</th>

              <th rowspan=2>Servicios / Otros (S/.)</th>
              <th rowspan=2>Cargos (S/.)</th>
              <th rowspan=2 >Cuota de Pago {{$nombre_forma_pago}} (S/.)</th>
            </tr>
            <tr>
              <th>Pago</th>
              <th>Cuotas</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>{{ $credito->tipo_destino_credito_nombre}}</td>
              <td>{{ $credito->nombreproductocredito }}</td>
              <td width="50px">
                @foreach($forma_pago_credito as $value)
                  @if( $credito->idforma_pago_credito == $value->id)
                    {{ $value->nombre }}
                  @endif
                @endforeach
              </td> 
              <td width="40px" class="campo_moneda">{{ $credito->cuotas }}</td>
              <td width="50px">Cuota Fija</td>
              <td width="40px" class="campo_moneda">{{ $credito->monto_solicitado }}</td>
              <td width="40px" class="campo_moneda">{{ $credito->tasa_tem }} %</td>
              <td width="40px" class="campo_moneda">{{ $credito->cuota_comision }}</td>
              <td width="40px" class="campo_moneda">{{ $credito->cuota_cargo }}</td>
              <td width="40px" class="campo_moneda">{{ $credito->cuota_pago }}</td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td class="color_totales" colspan=9 align="right">PAGO MES (S/.)</td>
              <td class="color_totales  campo_moneda">{{ $credito->total_propuesta }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="col" style="width:110px;">
        <table class="table" width="100%">
          <tr>
            <td style="text-align:center;"><b>CUOTA / EXCEDENTE</b></td>
          </tr>
          <tr>
            <td style="text-align:center;" class="campo_moneda">
              {{ $credito_formato_evaluacion ? $credito_formato_evaluacion->resultado_cuota_excedente : '0.00' }}%</td>
          </tr>
          <tr>
            <td style="text-align:center;"><b>RESULTADO</b></td>
          </tr>
          <tr>
            <td style="text-align:center;background-color: #e5e5e5 !important;
              color: #000 !important;"><b>{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->estado_evaluacion : '' }}</b></td>
          </tr>
        </table>
      </div>
    </div>
    <span class="badge subtitle">REFERENCIAS SOBRE MORAL DE PAGO Y LABORAL</span>
    <div class="row"> 
      <div class="col">
        <table class="table" width="400px">
          <thead>
            <tr>
              <th>N°</th>
              <th>Fuente</th>
              <th>Apellidos y Nombres</th>
              <th>Telf./Celular</th>
            </tr>
          </thead>
          <tbody>
            @foreach($referencia as $key => $value)
              @php
                $fuente = DB::table('f_tiporeferencia')->whereId($value->fuente)->first();
              @endphp
              <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $fuente->nombre }}</td>
                <td>{{ $value->nombre }}</td>
                <td>{{ $value->celular }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <span class="badge">III. COMENTARIOS SOBRE CENTRO LABORAL TIPO DE CONTRATO ANTIGÜEDAD, CONTINUIDAD Y FORTALEZAS IDENTIFICADAS</span>
    <div class="row">
      <textarea >{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->comentario_centro_laboral : '' }}</textarea>
    </div>
    <span class="badge">IV. COMENTARIOS SOBRE CAPACIDAD DE PAGO, INGRESOS ADICIONALES, DESTINO DE LOS CRÉDITOS VIGENTES,  ACUMULACIÓN PATRIMONIAL</span>
    <div class="row">
      <textarea >{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->comentario_capacidad_pago : '' }}</textarea>
    </div>
    <span class="badge">V. SUSTENTO DEL HISTORIAL DE PAGO INTERNO Y EXTERNO, REFERENCIAS PERSONALES Y BANCARIAS, ENDEUDAMIENTO</span>
    <div class="row">
      <textarea >{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->sustento_historial_pago : '' }}</textarea>
    </div>
    <span class="badge">VI. SUSTENTO DEL DESTINO  DEL CRÉDITO</span>
    <div class="row">
      <textarea >{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->sustento_destino_credito : '' }}</textarea>
    </div>
    
    <div class="row" >
      <div class="col" style="margin-left:215px;margin-top:60px;">
        <div style="width:300px;height:1px;border-bottom:1px solid #ccc;"></div>
        <p align="center">Asesor(a) de Créditos: {{ Auth::user()->codigo }} <br>Firma y Sello</p>		
      </div>
    </div>
  </main>
</body>
</html>