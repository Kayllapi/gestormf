<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EVALUACIÓN RESUMIDA</title>
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
          /*background-color: #144081;*/
          color: #000;
          text-align: center;
          line-height: 0.4cm;
          font-size:12px;
      }
      /** Definir las reglas de numeracion de página **/
      footer .page:after { content: counter(page, decimal-leading-zero); }

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
  <header>
    <div style="float:left;font-size:18px;">{{ $tienda->nombre }}</div> {{ Auth::user()->usuario }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
  </footer>
  @php
    $referencia_cliente = $credito_evaluacion_resumida ? ( is_null($credito_evaluacion_resumida->referencia) ? [] : json_decode($credito_evaluacion_resumida->referencia) ) : [];
    $dias = $credito_evaluacion_resumida ? ( $credito_evaluacion_resumida->venta_diaria == "" ? [] : json_decode($credito_evaluacion_resumida->venta_diaria) ) : [];
    $semanas = $credito_evaluacion_resumida ? ( $credito_evaluacion_resumida->venta_semanal == "" ? [] : json_decode($credito_evaluacion_resumida->venta_semanal) ) : [];

    $ingresos_gastos = $credito_evaluacion_resumida ? ( $credito_evaluacion_resumida->ingresos_gastos == "" ? [] : json_decode($credito_evaluacion_resumida->ingresos_gastos) ) : [];
  @endphp
  <main>
      <h4 align="center" style="font-size:13px;margin:0;padding:0;">EVALUACIÓN DE CRÉDITO - INGRESO INDEPENDIENTE <br> CRÉDITO: MyPE y CONSUMO N. R.</h4>
      <br>
    <span class="badge">I. INFORMACIÓN DEL CLIENTE</span>
    <div class="row">
      <div class="col" style="width:360px;">
        <table style="width:100%;">
          <tr>
            <td>AGENCIA/OFICINA:</td>
            <td class="border-td">{{ $tienda->nombreagencia }}</td>
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
            <td>TIPO GIRO ECONÓMICO</td>
            <td class="border-td">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->nombretipo_giro_economico : '' }}</td>
          </tr>
          <tr>
            <td>GIRO ECONÓMICO:</td>
            <td class="border-td">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->nombregiro_economico_evaluacion : '' }}</td>
          </tr>
          <tr>
            <td>DESCRIPCIÓN DE ACTIVIDAD:</td>
            <td class="border-td">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->descripcion_actividad : '' }}</td>
          </tr>
        </table>
      </div>
      <div class="col">
        <table>
          <tr>
            <td>FECHA:</td>
            <td class="border-td" width="100px">{{ date_format(date_create($credito->fecha),'Y-m-d') }}</td>
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
          <tr>
            <td>EJERCICIO:</td>
            <td class="border-td">{{ $users_prestamo->db_idforma_ac_economica }}</td>
          </tr>
          
        </table>
      </div>
      <div class="col">
        <table>
          <tr>
            <td>NRO SOLICITUD:</td>
            <td class="border-td" width="100px">S{{ str_pad($credito->id, 8, '0', STR_PAD_LEFT)  }}</td>
          </tr>
          <tr>
            <td>PRODUCTO:</td>
            <td class="border-td">{{ $credito->nombreproductocredito }}</td>
          </tr>
          <tr>
            <td>TIPO DE CAMBIO:</td>
            <td class="border-td">{{ configuracion($tienda->id,'tipo_cambio_dolar')['valor'] }}</td>
          </tr>
          <tr>
            <td>TIPO DE CLIENTE:</td>
            <td class="border-td">{{ $credito->tipo_operacion_credito_nombre }}</td>
          </tr>
          <tr>  
            <td>MODALIDAD:</td>
            <td class="border-td">{{ $credito->modalidad_credito_nombre }}</td>
          </tr>
        </table>
      </div>
    </div>
    
    <span class="badge">II. EVALUACIÓN CUALITATIVA</span>
    <span class="badge subtitle">2.1 N° DE ENTIDADES FINANCIERAS (Se considera deuda interna y Líneas de creditos sin uso)</span>
    <div class="row">
      <div class="col">
        <table class="table">
            <thead>
              <tr>
                <th style="color: #000 !important;" width="100px">Deudores</th>
                <th style="color: #000 !important;" width="100px">Como</th>
                <th style="color: #000 !important;text-align: center;" width="50px">N°</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="" rowspan="2">CLIENTE</td>
                <td style="">P.Natural</td>
                <td class="campo_moneda">{{ $credito_evaluacion_resumida ? number_format($credito_evaluacion_resumida->cantidad_cliente_natural, 2, '.', '') : '0.00' }}</td>
              </tr>
              <tr>
                <td style="">P.Jurídica</td>
                <td class="campo_moneda">{{ $credito_evaluacion_resumida ? number_format($credito_evaluacion_resumida->cantidad_cliente_juridico, 2, '.', '') : '0.00' }}</td>
              </tr>
              <tr>
                <td style="" rowspan="2">PAREJA</td>
                <td style="">P.Natural</td>
                <td class="campo_moneda">{{ $credito_evaluacion_resumida ? number_format($credito_evaluacion_resumida->cantidad_pareja_natural, 2, '.', '') : '0.00' }}</td>
              </tr>
              <tr>
                <td style="">P.Jurídica</td>
                <td class="campo_moneda">{{ $credito_evaluacion_resumida ? number_format($credito_evaluacion_resumida->cantidad_pareja_juridico, 2, '.', '') : '0.00' }}</td>
              </tr>
              <tr>
                <td style="text-align: right;" colspan=2>TOTAL</td>
                <td class="campo_moneda">{{ $credito_evaluacion_resumida ? number_format($credito_evaluacion_resumida->total_deuda, 2, '.', '') : '0.00' }}</td>
              </tr>
            </tbody>
          </table>
      </div>
      <div class="col">
      
        @if($users_prestamo_aval!='')
        <div class="col-md-5">
              <div class="row">
                <div class="col-md-7">
                  <div class="row">
                    <label class="col-sm-3 col-form-label" style="text-align: right;">AVAL: {{ $credito->nombreavalcredito }}</label>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="row">
                    <label class="col-sm-3 col-form-label" style="text-align: right;">DNI: {{ $credito->documentoaval }}</label>
                  </div>
                </div>
              </div>
              @if($users_prestamo_aval->dni_pareja!='' or $users_prestamo_aval->nombrecompleto_pareja!='')
              <div class="row">
                <div class="col-md-7">
                  <div class="row">
                    <label class="col-sm-3 col-form-label" style="text-align: right;">PAREJA: {{ $users_prestamo_aval->nombrecompleto_pareja }}</label>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="row">
                    <label class="col-sm-3 col-form-label" style="text-align: right;">DNI: {{ $users_prestamo_aval->dni_pareja }}</label>
                  </div>
                </div>
              </div>
              @endif
        </div>
        @endif
      </div>
      <div class="col">
        <table class="table">
          <thead>
            <tr>
              <th style="color: #000 !important;" width="100px">Codeudores</th>
              <th style="color: #000 !important;" width="100px">Como</th>
              <th style="color: #000 !important;text-align: center;" width="50px">N°</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td style="" rowspan="2">Garante (Aval)/Fiador</td>
              <td style="">P.Natural</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cantidad_garante_natural : '0.00' }}</td>
            </tr>
            <tr>
              <td style="">P.Jurídica</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cantidad_garante_juridico : '0.00' }}</td>
            </tr>
            <tr>
              <td style="" rowspan="2">Pareja de Garante/ fiador</td>
              <td style="">P.Natural</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cantidad_pareja_natural : '0.00' }}</td>
            </tr>
            <tr>
              <td style="">P.Jurídica</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cantidad_pareja_juridico : '0.00' }}</td>
            </tr>
            <tr>
              <td style="text-align: right;" colspan=2>TOTAL</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->total_deuda : '0.00' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
   
    <span class="badge subtitle">2.2 GESTIÓN DEL GIRO ECONÓMICO</span>
    <div class="row">
      <div class="col">
        <table>
          <tbody>
            <tr>
              <td width="250px">a) Experiencia como Microempresario(a) (meses)</td>
              <td class="border-td campo_moneda" width="50px">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->experiencia_microempresa : 0 }}</td>
            </tr>
            <tr>
              <td>b) Tiempo en el mismo local (meses)</td>
              <td class="border-td campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->tiempo_mismo_local : 0 }}</td>
            </tr>
            <tr>
              <td>c) Instalaciones o local</td>
              <td class="border-td campo_moneda">
                @if($users_prestamo->db_idlocalnegocio_ac_economica!='')
                  {{ $users_prestamo->db_idlocalnegocio_ac_economica }}
                @endif
              </td>
            </tr>
            <tr>
              <td>d) N° de trabajadores a tiempo completo</td>
              <td class="border-td campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->nro_trabajador_completo : 0 }}</td>
            </tr>
            <tr>
              <td>e) N° de trabajdores a tiempo parcial</td>
              <td class="border-td campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->nro_trabajador_parcal : 0 }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <span class="badge subtitle">2.3 REFERENCIAS</span>
    <div class="row">
      <div class="col">
        <table class="table">
          <thead>
            <tr>
              <th style="color: #000 !important;">N°</th>
              <th style="color: #000 !important;" width="100px">Fuente</th>
              <th style="color: #000 !important;" width="200px">Apellidos y Nombres</th>
              <th style="color: #000 !important;" width="200px">Vinculo: Familiar/Personas/Otros</th>
              <th style="color: #000 !important;">Telf./Celular</th>
            </tr>
          </thead>
          <tbody>
            @foreach($referencia_cliente as $key => $value)
              @php
                $fuente = DB::table('f_tiporeferencia')->whereId($value->fuente)->first();
              @endphp
              <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $fuente->nombre }}</td>
                <td>{{ $value->nombre }}</td>
                <td>{{ $value->vinculo }}</td>
                <td>{{ $value->celular }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    
    <span class="badge">III. INGRESOS Y GASTO FAMILIAR RESUMIDO (Mensual)</span>
    <div class="row">
      <div class="col">

        <table class="table">
          <thead>
            <tr>
              <th style="color: #000 !important;" width="10px">N°</th>
              <th style="color: #000 !important;" width="140px">Dias</th>
              <th style="color: #000 !important;">Ventas</th>
            </tr>
          </thead>
          <tbody>
            @if(count($dias) > 0)
              @foreach($dias as $value)
                <tr>
                  <td numero>{{ $value->numero }}</td>
                  <td dia>{{ $value->dia }}</td>
                  <td valor class="campo_moneda">{{ number_format($value->valor, 2, '.', '') }}</td>
                </tr>
              @endforeach
            @endif
            <tr total>
              <th colspan="2" style="color: #000 !important;">Venta Semanal</th>
              <td style="color: #000 !important;" class="campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->venta_total_dias : '0.00' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col">
         <table class="table">
          <thead>
            <tr>
              <th style="color: #000 !important;" width="140px">Semanas</th>
              <th style="color: #000 !important;">Ventas</th>
            </tr>
          </thead>
          <tbody>
            @if(count($semanas) > 0)
                @foreach($semanas as $value)
                  <tr>
                    <td semana>{{ $value->semana }}</td>
                    <td valor class="campo_moneda">{{ number_format($value->valor, 2, '.', '') }}</td>
                  </tr>
                @endforeach
              @endif

            <tr total>
              <th style="color: #000 !important;">Venta Mensual</th>
              <td style="color: #000 !important;" class="campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->venta_total_mensual : '0.00' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!--div class="row">
      <div class="col">
        <table>
          <tr>
            <td width="200px">% de margen por tipo de giro(%)  | Bodega tienda</td>
            <td class="border-td" align="center">22</td>
          </tr>
        </table>
      </div>
    </div-->
    <br>
    <div class="row">
      <div class="col">
        <table class="table table-bordered" id="table-ingresos-gastos">
          <thead>
            <tr>
              <th style="color: #000 !important;">Ingresos y gastos operativos del negocio</th>
              <th style="color: #000 !important;" width="80px">Monto</th>
              <th style="color: #000 !important;" width="80px">Ampliación / Compra deuda</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>+Ingreso por ventas</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_ventas', $ingresos_gastos) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>-Costo de venta(C. de producción)</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_costo_produccion', $ingresos_gastos) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>Utilidad Bruta</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_utilidad_bruta', $ingresos_gastos) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>-Gasto de Personal</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_gasto_personal', $ingresos_gastos) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>-Servicio de luz</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_luz', $ingresos_gastos) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>-Servico de agua</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_agua', $ingresos_gastos) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>-Teléfono/internet</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_telefono', $ingresos_gastos) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>-Cable</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_cable', $ingresos_gastos) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>-Alquiler de local</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_alquiler', $ingresos_gastos) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>-Autoavalúo, serenazgo, parques y J.</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_autovaluo', $ingresos_gastos) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>-Transporte</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_transporte', $ingresos_gastos) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>-Cuota de préstamo E. Reguladas</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_cuota_prestamo_regulada', $ingresos_gastos) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_ampliacion_regulada', $ingresos_gastos) }}</td>
            </tr>
            <tr>
              <td>-Cuota de préstamo E. No Reguladas (Incl. Deuda interna)</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_cuota_prestamo_noregulada', $ingresos_gastos) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_ampliacion_noregulada', $ingresos_gastos) }}</td>
            </tr>
            <tr>
              <td>-Sunat</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_sunat', $ingresos_gastos) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>-Otros gastos</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_otros_gastos', $ingresos_gastos) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>+Otros Negocios</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_otros_negocios', $ingresos_gastos) }}</td>
              <td></td>
            </tr>
            <tr>
              <td>+Ingreso Fijo (Sueldo, Pensión Seguro y otras)</td>
              <td class="campo_moneda">{{ encontrar_valor('ingresos_op_ingreso_fijo', $ingresos_gastos) }}</td>
              <td></td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td> Total Ingreso (S/.)</td>
              <td class="campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->ingresos_op_total : '0.00' }}</td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="col">
        <table class="table table-bordered" id="table-gastos-familiares">
          <thead>
            <tr>
              <th style="color: #000 !important;">Gastos Familiares</th>
              <th style="color: #000 !important;" width="110px">Monto</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Alimentación</td>
              <td class="campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_alimentacion : 0 }}</td>
            </tr>
            <tr>
              <td>Educación</td>
              <td class="campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_educacion : 0 }}</td>
            </tr>
            <tr>
              <td>Vestimenta</td>
              <td class="campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_vestimenta : 0 }}</td>
            </tr>
            <tr>
              <td>Transporte</td>
              <td class="campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_transporte : 0 }}</td>
            </tr>
            <tr>
              <td>Salud</td>
              <td class="campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_salud : 0 }}</td>
            </tr>
            <tr>
              <td>Alquiler de vivienda</td>
              <td class="campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_vivienda : 0 }}</td>
            </tr>
            <tr>
              <th style="">Servicios</th>
              <td class="campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->total_servicios : 0 }}</td>
            </tr>
            <tr>
              <td>Agua</td>
              <td class="campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_agua : 0 }}</td>
            </tr>
            <tr>
              <td>Luz</td>
              <td class="campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_luz : 0 }}</td>
            </tr>
            <tr>
              <td>Teléfono fijo e internet</td>
              <td class="campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_telefono_internet : 0 }}</td>
            </tr>
            <tr>
              <td>T. Celular</td>
              <td class="campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_celular : 0 }}</td>
            </tr>
            <tr>
              <td>Cable</td>
              <td class="campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_cable : 0 }}</td>
            </tr>
            <tr>
              <td>Otros gastos personales ({{ configuracion($tienda->id,'provision_gastos_familiares')['valor'] }}%)</td>
              <td class="campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_otros : 0 }}</td>
            </tr>
            <tr>
              <th style="">Total Gasto Familiar (S/.)</th>
              <td class="campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->gasto_total : 0 }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <br>
    <div class="row"> 
      <div class="col">
        @php
          $idformapagocredito = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->idforma_pago_credito : $credito->idforma_pago_credito;
          $nombre_forma_pago = '';
        @endphp
        @foreach($forma_pago_credito as $value)
          @if( $idformapagocredito == $value->id)
            @php $nombre_forma_pago = $value->nombre; @endphp
          @endif
        @endforeach
        <table class="table">
          <thead>
            <tr>
              <th style="color: #000 !important;" colspan=10>PROPUESTA</th>
            </tr>
            <tr>
              <th style="color: #000 !important;" rowspan=2>DESTINO DE CRÉDITO</th>
              <th style="color: #000 !important;" rowspan=2>Producto</th>
              <th style="color: #000 !important;" colspan=2>Plazo</th>
              <th style="color: #000 !important;" rowspan=2>FORMA DE PAGO</th>
              <th style="color: #000 !important;" rowspan=2>Monto Préstamo</th>
              <th style="color: #000 !important;" rowspan=2>TEM</th>

              <th style="color: #000 !important;" rowspan=2>Servicios/Otros (S/.)</th>
              <th style="color: #000 !important;" rowspan=2>Cargos (S/.)</th>
              <th style="color: #000 !important;" rowspan=2 >Cuota de Pago {{$nombre_forma_pago}} (S/.)</th>
            </tr>
            <tr>
              <th style="color: #000 !important;">Pago</th>
              <th style="color: #000 !important;">Cuotas</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>{{ $credito->tipo_destino_credito_nombre}}</td>
              <td>{{ $credito->nombreproductocredito }}</td>
              <td>
                @php
                  $idformapagocredito = $credito->idforma_pago_credito;
                @endphp
                @foreach($forma_pago_credito as $value)
                  @if( $idformapagocredito == $value->id)
                    {{ $value->nombre }}
                  @endif
                @endforeach
              </td> 
              <td class="campo_moneda">{{ $credito->cuotas }}</td>
              <td>Cuota Fija</td>
              <td class="campo_moneda">{{ $credito->monto_solicitado }}</td>
              <td class="campo_moneda">{{ $credito->tasa_tem }} %</td>
              <td class="campo_moneda">{{ $credito->cuota_comision }}</td>
              <td class="campo_moneda">{{ $credito->cuota_cargo }}</td>
              <td class="campo_moneda">{{ $credito->cuota_pago }}</td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td class="color_totales" colspan=9 align="right">PAGO MES (S/.)</td>
              <td class="color_totales campo_moneda">{{ $credito->total_propuesta }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
    
    <div class="row">
      <div class="col">
        
        
        <table>
            <tr>
              <td colspan="2"> (1): Indicador de Solvencia</td>
              <td class="border-td">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->estado_indicador_solvencia : '' }}</td>
            </tr>
            <tr>
              <td colspan="2"> (2): Indicador de Relación Cuota /Ingreso</td>
              <td class="border-td">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->estado_indicador_cuota_ingreso : '' }}</td>
            </tr>
            <tr>
              <td> (3): Indicador de Relación Cuota/Venta</td>
              <td width="50px"><b>a. Diaria</b></td>
              <td class="border-td">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->estado_indicador_cuota_venta_diario : '' }}</td>
            </tr>
            <tr>
              <td></td>
              <td><b>b. Semana</b></td>
              <td class="border-td">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->estado_indicador_cuota_venta_semanal : '' }}</td>
            </tr>
            <tr>
              <td></td>
              <td><b>c. Quicena</b></td>
              <td class="border-td">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->estado_indicador_cuota_venta_quincenal : '' }}</td>
            </tr>
            <tr>
              <td></td>
              <td><b>d. Mes</b></td>
              <td class="border-td">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->estado_indicador_cuota_venta_mensual : '' }}</td>
            </tr>
            <tr>
              <td colspan=3 align="center" class="border-td" style="background-color: #e5e5e5 !important;
              color: #000 !important;"><b>{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->estado_credito_general : '' }}</b></td>
            </tr>
          </table>
      </div>
      <div class="col">
        <table>
          <thead>
            <tr>
              <th colspan=2 style="background-color: #efefef !important;color: #000 !important;">INDICADOR DE SOLVENCIA (1)</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Excedente (S/.)</td>
              <td class="border-td campo_moneda" width="40px">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->indicador_solvencia_excedente : '0.00' }}</td>
            </tr>
            <tr>
              <td>Relación Cuota/ excedente (%)</td>
              <td class="border-td campo_moneda" width="40px">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->indicador_solvencia_cuotas : '0.00' }}</td>
            </tr>
            <tr>
              <td colspan=2>&nbsp;</td>
            </tr>
          </tbody>
        </table>
        
        
      </div>
      <div class="col">
        <table class="">
          <thead>
            <tr>
              <th colspan=2 style="background-color: #efefef !important;color: #000 !important;">RELACION CUOTA /INGRESO (2)</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>R. Cuota Mensual/Ingreso Mensual (%)</td>
              <td class="border-td campo_moneda" width="40px">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_mensual : '0.00' }}</td>
            </tr>
            <tr>
              <th colspan=2 style="background-color: #efefef !important;color: #000 !important;">RELACIÓN CUOTA/ VENTA (3)</th>
            </tr>
            <tr>
              <td>R. Cuota diaria/ Venta diaria (%)</td>
              <td class="border-td campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_venta_diaria : '0.00' }}</td>
            </tr>
            <tr>
              <td>R. Cuota Semanal/ Venta semanal (%)</td>
              <td class="border-td campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_venta_semanal : '0.00' }}</td>
            </tr>
            <tr>
              <td>R. Cuota quincenal/ Venta quincenal (%)</td>
              <td class="border-td campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_venta_quincenal : '0.00' }}</td>
            </tr>
            <tr>
              <td>R. Cuota Mensual/Venta Mensual (%)</td>
              <td class="border-td campo_moneda">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_venta_mensual : '0.00' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    
    
    <span class="badge subtitle">IV. DETALLE DEL DESTINO DEL PRÉSTAMO</span>
    <div class="row">
      <textarea id="detalle_destino_prestamo" class="form-control">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->detalle_destino_prestamo : '' }}</textarea>
    </div>
    <span class="badge subtitle">V. COMENTARIOS Y ESPECIFICACIONES DE FORTALEZAS DEL NEGOCIO</span>
    <div class="row">
      <textarea id="fortalezas_negocio" class="form-control">{{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->fortalezas_negocio : '' }}</textarea>
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

