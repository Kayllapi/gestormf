<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EVALUACIÓN CUANTITATIVA</title>
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
        
      .doble-subrayado {
            text-decoration: underline double;
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
      $evaluacion_meses = $credito_evaluacion_cuantitativa ? ( $credito_evaluacion_cuantitativa->evaluacion_meses == "" ? [] : json_decode($credito_evaluacion_cuantitativa->evaluacion_meses) ) : [];
      $venta_mensual = $credito_cuantitativa_margen_venta ? ($credito_cuantitativa_margen_venta->venta_mensual + $credito_cuantitativa_margen_venta->venta_total_mensual) : '0.00';
  
      $balance_general = $credito_evaluacion_cuantitativa ? ( $credito_evaluacion_cuantitativa->balance_general == "" ? [] : json_decode($credito_evaluacion_cuantitativa->balance_general) ) : [];
      $ganancia_perdida = $credito_evaluacion_cuantitativa ? ( $credito_evaluacion_cuantitativa->ganancia_perdida == "" ? [] : json_decode($credito_evaluacion_cuantitativa->ganancia_perdida) ) : [];
      
      $resumen_deuda = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->resumen == "" ? [] : json_decode($credito_cuantitativa_deudas->resumen) ) : [];
      $ganancia_adicional = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->ganancias_perdidas == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->ganancias_perdidas) ) : [];
    @endphp
    @php
      $balance_general_anterior = $credito_evaluacion_cuantitativa_anterior ? ( $credito_evaluacion_cuantitativa_anterior->balance_general == "" ? [] : json_decode($credito_evaluacion_cuantitativa_anterior->balance_general) ) : [];
      $ganancia_perdida_anterior = $credito_evaluacion_cuantitativa_anterior ? ( $credito_evaluacion_cuantitativa_anterior->ganancia_perdida == "" ? [] : json_decode($credito_evaluacion_cuantitativa_anterior->ganancia_perdida) ) : [];
      $resumen_deuda_anterior = $credito_cuantitativa_deudas_anterior ? ( $credito_cuantitativa_deudas_anterior->resumen == "" ? [] : json_decode($credito_cuantitativa_deudas_anterior->resumen) ) : [];
      $ganancia_adicional_anterior = $credito_cuantitativa_ingreso_adicional_anterior ? ( $credito_cuantitativa_ingreso_adicional_anterior->ganancias_perdidas == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional_anterior->ganancias_perdidas) ) : [];
     
    @endphp
  <main>
      <h4 align="center" style="font-size:13px;margin:0;padding:0;">EVALUACIÓN DE CRÉDITO - INGRESO INDEPENDIENTE <br> CRÉDITO: MyPE y CONSUMO N. R.</h4>
      
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
            <td>GIRO ECONÓMICO:</td>
            <td class="border-td">
              @if($credito->idevaluacion == 1)
              {{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->nombregiro_economico_evaluacion : '' }}
              @else
              {{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->nombregiro_economico_evaluacion : '' }}
              @endif
            </td>
          </tr>
          <tr>
            <td>DESCRIPCIÓN DE ACTIVIDAD:</td>
            <td class="border-td">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->descripcion_actividad : '' }}</td>
          </tr>
        </table>
      </div>
      <div class="col">
        <table>
          <tr>
            <td>FECHA:</td>
            <td class="border-td" width="100px">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->fecha : date_format(date_create($credito->fecha),'Y-m-d') }}</td>
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
    
    <span class="badge">III. EVALUACIÓN ECONÓMICA FINANCIERA (CUANTITATIVA)</span>
    <span class="badge subtitle">3.1 CICLO DEL NEGOCIO (Actual =100%, Alta > 100%, Baja &lt; 100%)</span>
    @if( $credito_evaluacion_cuantitativa!='')
    <div class="row">
      <div class="col">
        <table class="table" width="735px">
          <?php generarTabla($credito_evaluacion_cuantitativa->fecha); ?>
          <tbody>
          @if(count($evaluacion_meses) > 0)
            @foreach ($evaluacion_meses as $row)
              <tr>
                  <td>{{ $row->Mes }}</td>
                  @foreach ($row as $header => $cellData)
                      @if ($header !== 'Mes')
                          @php
                              $value = $cellData->value;
                              $disabled = $cellData->disabled ? 'disabled' : '';
                              $color_cajatexto = $cellData->disabled ?  : 'color_cajatexto';
                          @endphp
                          <td class="campo_moneda">{{ $value }}</td>
                      @endif
                  @endforeach
              </tr>
            @endforeach
          @endif
          </tbody>
        </table>
      </div>
    </div>
      @endif
    <span class="badge subtitle">3.2 ESTADOS FINANCIEROS</span>
    <div class="row">
      <div class="col">
        <table class="table">
          <thead>
            <tr>
              <th colspan=2></th>
              <th>Evaluación Anterior</th>
              <th>Evaluación Actual</th>
              <th></th>
              <th></th>
            </tr>
            <tr>
              <th colspan=2>BALANCE GENERAL</th>
              <th width="50px">Soles (S/. )</th>
              <th width="50px">Soles (S/. )</th>
              <th width="50px">Análisis Vertical(%)</th>
              <th width="50px">Análisis Horizontal (%)</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan=2>Caja</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_caja', $balance_general_anterior) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_caja', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_caja', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_caja', $balance_general) }}</td>
            </tr>
            <tr>
              <td colspan=2>Bancos</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_bancos', $balance_general_anterior) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_bancos', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_bancos', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_bancos', $balance_general) }}</td>
            </tr>
            <tr>
              <td colspan=2>Cuentas por cobrar a clientes</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_cuentas_cobrar', $balance_general_anterior) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_cuentas_cobrar', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_cuentas_cobrar', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_cuentas_cobrar', $balance_general) }}</td>
            </tr>
            <tr>
              <td colspan=2>Adelanto a proveedores</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_adelanto_prove', $balance_general_anterior) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_adelanto_prove', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_adelanto_prove', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_adelanto_prove', $balance_general) }}</td>
            </tr>
            <tr>
              <td colspan=2>Inventarios</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_inventario_anterior ? $credito_cuantitativa_inventario_anterior->total_inventario : '0.00' }}</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_inventario ? $credito_cuantitativa_inventario->total_inventario : '0.00' }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_inventario', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_inventario', $balance_general) }}</td>
            </tr>
            <tr>
              <td colspan=2><b>ACTIVO CORRIENTE</b></td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_activo_corriente', $balance_general_anterior) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_activo_corriente', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_activo_corriente', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_activo_corriente', $balance_general) }}</td>
            </tr>
            <tr>
              <td colspan=2>Activo inmueble</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_inventario_anterior ? $credito_cuantitativa_inventario_anterior->total_inmuebles : '0.00' }}</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_inventario ? $credito_cuantitativa_inventario->total_inmuebles : '0.00' }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_activo_inmueble', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_activo_inmueble', $balance_general) }}</td>
            </tr>
            <tr>
              <td colspan=2>Activo mueble</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_inventario_anterior ? $credito_cuantitativa_inventario_anterior->total_muebles : '0.00' }}</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_inventario ? $credito_cuantitativa_inventario->total_muebles : '0.00' }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_activo_mueble', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_activo_mueble', $balance_general) }}</td>
            </tr>
            <tr>
              <td colspan=2><b>ACTIVO NO CORRIENTE</b></td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_activo_nocorriente', $balance_general_anterior) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_activo_nocorriente', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_activo_nocorriente', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_activo_nocorriente', $balance_general) }}</td>
            </tr>
            <tr>
              <td colspan=2 style="background-color: #e5e5e5 !important;
              color: #000 !important;"><b class="doble-subrayado">TOTAL ACTIVO</b></td>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" class="campo_moneda"><b class="doble-subrayado">{{ encontrar_valor('evaluacion_actual_total_activo', $balance_general_anterior) }}</b></td>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" evaluacion_actual class="campo_moneda"><b class="doble-subrayado">{{ encontrar_valor('evaluacion_actual_total_activo', $balance_general) }}</b></td>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" analisis_vertical class="campo_moneda"><b class="doble-subrayado">{{ encontrar_valor('analisis_vertical_total_activo', $balance_general) }}</b></td>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" analisis_horizontal class="campo_moneda"><b class="doble-subrayado">{{ encontrar_valor('analisis_horizontal_total_activo', $balance_general) }}</b></td>
            </tr>
            <tr>
              <td colspan=2>Cuentas por pagar a proveedores</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_pagos_proveedor', $balance_general_anterior) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_pagos_proveedor', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_pagos_proveedor', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_pagos_proveedor', $balance_general) }}</td>
            </tr>
            <tr>
              <td rowspan=2 width="100px">Pasivos financieros a corto plazo</td>
              <td >E. Reguladas</td>
              <td class="campo_moneda">{{ encontrar_valor('comercial_er_cplazo', $resumen_deuda_anterior) + encontrar_valor('mes_er_cplazo', $resumen_deuda_anterior) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('comercial_er_cplazo', $resumen_deuda) + encontrar_valor('mes_er_cplazo', $resumen_deuda) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_pasivo_corto_regulada', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_pasivo_corto_regulada', $balance_general) }}</td>
            </tr>
            <tr>
              <td class="campo_moneda">E. No Reguladas</td>
              <td class="campo_moneda">{{ encontrar_valor('comercial_enr_cplazo', $resumen_deuda_anterior) + encontrar_valor('mes_enr_cplazo', $resumen_deuda_anterior) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('comercial_enr_cplazo', $resumen_deuda) + encontrar_valor('mes_enr_cplazo', $resumen_deuda) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_pasivo_corto_noregulada', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_pasivo_corto_noregulada', $balance_general) }}</td>
            </tr>
            <tr>
              <td colspan=2>Impuestos por pagar</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_impuestos', $balance_general_anterior) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_impuestos', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_impuestos', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_impuestos', $balance_general) }}</td>
            </tr>
            <tr>
              <td colspan=2>Otras cuentas por pagar</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_otras_cuentas', $balance_general_anterior) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_otras_cuentas', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_otras_cuentas', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_otras_cuentas', $balance_general) }}</td>
            </tr>
            <tr>
              <td colspan=2><b>PASIVO CORRIENTE</b></td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_pasivo_corriente', $balance_general_anterior) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_pasivo_corriente', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_pasivo_corriente', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_pasivo_corriente', $balance_general) }}</td>
            </tr>
            <tr>
              <td rowspan=2>Pasivo Fin. a Largo.Plazo</td>
              <td class="campo_moneda">E. Reguladas</td>
              <td class="campo_moneda">{{ encontrar_valor('comercial_er_couta', $resumen_deuda_anterior) + encontrar_valor('mes_er_lplazo', $resumen_deuda_anterior) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('comercial_er_couta', $resumen_deuda) + encontrar_valor('mes_er_lplazo', $resumen_deuda) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_pasivo_largo_regulada', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_pasivo_largo_regulada', $balance_general) }}</td>
            </tr>
            <tr>
              <td class="campo_moneda">E. No Reguladas</td>
              <td class="campo_moneda">{{ encontrar_valor('comercial_enr_lplazo', $resumen_deuda_anterior) + encontrar_valor('mes_enr_lplazo', $resumen_deuda_anterior) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('comercial_enr_lplazo', $resumen_deuda) + encontrar_valor('mes_enr_lplazo', $resumen_deuda) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_pasivo_largo_noregulada', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_pasivo_largo_noregulada', $balance_general) }}</td>
            </tr>
            <tr>
              <td colspan=2><b>PASIVO NO CORRIENTE</b></td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_pasivo_nocorriente', $balance_general_anterior) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_pasivo_nocorriente', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_pasivo_nocorriente', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_pasivo_nocorriente', $balance_general) }}</td>
            </tr>
            <tr>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" colspan=2><b>TOTAL PASIVO</b></td>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" class="campo_moneda"><b>{{ encontrar_valor('evaluacion_actual_total_pasivo', $balance_general_anterior) }}</b></td>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" class="campo_moneda"><b>{{ encontrar_valor('evaluacion_actual_total_pasivo', $balance_general) }}</b></td>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" class="campo_moneda"><b>{{ encontrar_valor('analisis_vertical_total_pasivo', $balance_general) }}</b></td>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" class="campo_moneda"><b>{{ encontrar_valor('analisis_horizontal_total_pasivo', $balance_general) }}</b></td>
            </tr>
            <tr>
              <td colspan=2>Capital social</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_capital_social', $balance_general_anterior) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_capital_social', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_capital_social', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_capital_social', $balance_general) }}</td>
            </tr>
            <tr>
              <td colspan=2>Utilidades acumuladas</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_utilidad_acumulada', $balance_general_anterior) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_utilidad_acumulada', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_utilidad_acumulada', $balance_general) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_utilidad_acumulada', $balance_general) }}</td>
            </tr>
            <tr>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" colspan=2><b>TOTAL PATRIMONIO</b></td>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" class="campo_moneda"><b>{{ encontrar_valor('evaluacion_actual_patrimonio', $balance_general_anterior) }}</b></td>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" class="campo_moneda"><b>{{ encontrar_valor('evaluacion_actual_patrimonio', $balance_general) }}</b></td>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" class="campo_moneda"><b>{{ encontrar_valor('analisis_vertical_patrimonio', $balance_general) }}</b></td>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" class="campo_moneda"><b>{{ encontrar_valor('analisis_horizontal_patrimonio', $balance_general) }}</b></td>
            </tr>
            <tr>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" class="campo_moneda"  colspan=2><b>TOTAL PASIVO + PATRIMONIO</b></td>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" class="campo_moneda"><b>{{ encontrar_valor('evaluacion_actual_pasivo_patrimonio', $balance_general_anterior) }}</b></td>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" class="campo_moneda"><b>{{ encontrar_valor('evaluacion_actual_pasivo_patrimonio', $balance_general) }}</b></td>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" class="campo_moneda"><b>{{ encontrar_valor('analisis_vertical_pasivo_patrimonio', $balance_general) }}</b></td>
              <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" class="campo_moneda"><b>{{ encontrar_valor('analisis_horizontal_pasivo_patrimonio', $balance_general) }}</b></td>
            </tr>
          </tbody>
        </table>
        <br>
        
    <span class="badge subtitle">3.3 MOVIMIENTO COMERCIAL</span>
      <div class="row">
        <div class="col">
          <table class="table">
            <thead>
              <tr>
                <th colspan=3>Ventas Mensuales (S/.)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td width="90px">Al crédito para cobro a {{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->dias_ventas_mensual : '0' }} días total al mes</td>
                <td class="campo_moneda" width="43px">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->credito_cobrando_venta_mensual : '0.00' }}</td>
                <td class="campo_moneda" width="43px">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->credito_porcentaje_venta_mensual : '0.00' }} %</td>
              </tr>
              <tr>
                <td>Al Contado</td>
                <td class="campo_moneda">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->contado_cobrando_venta_mensual : '0.00' }}</td>
                <td class="campo_moneda">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->contado_porcentaje_venta_mensual : '0.00' }} %</td>
              </tr>
            </tbody>
          </table> 
        </div>
        <div class="col">
          <table class="table">
            <thead>
              <tr>
                <th colspan=3>Compras Mensuales (S/.)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td width="90px">Al crédito para pago a {{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->dias_compras_mensual : '0' }} días total al mes</td>
                <td class="campo_moneda" width="43px">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->credito_cobrando_compra_mensual : '0.00' }}</td>
                <td class="campo_moneda" width="43px">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->credito_porcentaje_compra_mensual : '0.00' }} %</td>
              </tr>
              <tr>
                <td>Al Contado</td>
                <td class="campo_moneda">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->contado_cobrando_compra_mensual : '0.00' }}</td>
                <td class="campo_moneda">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->contado_porcentaje_compra_mensual : '0.00' }} %</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      </div> 
      <div class="col">
        <table class="table">
            <thead>
              <tr>
                <th></th>
                <th>Evaluación Anterior</th>
                <th>Evaluación Actual</th>
                <th></th>
                <th></th>
              </tr>
              <tr>
                <th width="120px">ESTADO DE GANANCIAS Y PERDIDAS</th>
                <th width="50px">Soles (S/. )</th>
                <th width="50px">Soles (S/. )</th>
                <th width="50px">Análisis Vertical(%)</th>
                <th width="50px">Análisis Horizontal (%)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="background-color: #e5e5e5 !important;
              color: #000 !important;"><b>VENTAS MENSUALES</b></td>
                <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" class="campo_moneda"><b>
                  {{ ($credito_cuantitativa_margen_venta_anterior ? $credito_cuantitativa_margen_venta_anterior->venta_mensual : 0) + ($credito_cuantitativa_margen_venta_anterior ? $credito_cuantitativa_margen_venta_anterior->venta_total_mensual : 0) }}</b></td>
                <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" class="campo_moneda"><b>
                  {{ ($credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->venta_mensual : 0) + ($credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->venta_total_mensual : 0) }}</b></td>
                <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" class="campo_moneda"><b>{{ encontrar_valor('analisis_vertical_ventamensual', $ganancia_perdida) }}</b></td>
                <td style="background-color: #e5e5e5 !important;
              color: #000 !important;" class="campo_moneda"><b>{{ encontrar_valor('analisis_horizontal_ventamensual', $ganancia_perdida) }}</b></td>
              </tr>
              <tr>
                <td>Costo de venta (C. de producción)</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_costo_venta', $ganancia_perdida_anterior) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_costo_venta', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_costo_venta', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_costo_venta', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td style="background-color: #e5e5e5 !important;
                color: #000 !important;"><b>UTILIDAD BRUTA</b></td>
                <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda"><b>{{ ( $credito_cuantitativa_margen_venta_anterior ? $credito_cuantitativa_margen_venta_anterior->margen_ventas : 0 ) + ( $credito_cuantitativa_margen_venta_anterior ? $credito_cuantitativa_margen_venta_anterior->margen_ventas_mensual : 0 ) }}</b></td>
                <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda"><b>{{ ( $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->margen_ventas : 0 ) + ( $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->margen_ventas_mensual : 0 ) }}</b></td>
                <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda"><b>{{ encontrar_valor('analisis_vertical_utilidad_bruta', $ganancia_perdida) }}</b></td>
                <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda"><b>{{ encontrar_valor('analisis_horizontal_utilidad_bruta', $ganancia_perdida) }}</b></td>
              </tr>
              <tr>
                <td>Gastos de personal administrativo</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_gasto_admin', $ganancia_perdida_anterior) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_gasto_admin', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_gasto_admin', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_gasto_admin', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td>Gastos de personal de ventas</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_gasto_personal', $ganancia_perdida_anterior) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_gasto_personal', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_gasto_personal', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_gasto_personal', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td><b>Servicios:</b></td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_servicios', $ganancia_perdida_anterior) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_servicios', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_servicios', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_servicios', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Luz</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_servicio_luz', $ganancia_perdida_anterior) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_servicio_luz', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_servicio_luz', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_servicio_luz', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Agua</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_servicio_agua', $ganancia_perdida_anterior) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_servicio_agua', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_servicio_agua', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_servicio_agua', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Telefono/internet</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_servicio_internet', $ganancia_perdida_anterior) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_servicio_internet', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_servicio_internet', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_servicio_internet', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- T. celular</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_servicio_celular', $ganancia_perdida_anterior) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_servicio_celular', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_servicio_celular', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_servicio_celular', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Cable</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_servicio_cable', $ganancia_perdida_anterior) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_servicio_cable', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_servicio_cable', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_servicio_cable', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td>Alquiler de local</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_gasto_admin', $ganancia_perdida_anterior) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_gasto_admin', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_alquiler', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_alquiler', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td>Autoavalúo, serenazgo, parques y J.</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_gasto_admin', $ganancia_perdida_anterior) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_gasto_admin', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_autovaluo', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_autovaluo', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td>Transporte</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_gasto_admin', $ganancia_perdida_anterior) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_gasto_admin', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_transporte', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_transporte', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td>Cuota de préstamo E. Reguladas</td>
                <td class="campo_moneda">{{ encontrar_valor('comercial_er_couta', $resumen_deuda_anterior) + encontrar_valor('mes_er_couta', $resumen_deuda_anterior) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('comercial_er_couta', $resumen_deuda) + encontrar_valor('mes_er_couta', $resumen_deuda) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_cuota_regulada', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_cuota_regulada', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td>Cuota de préstamo E. No Reguladas</td>
                <td class="campo_moneda">{{ encontrar_valor('comercial_enr_couta', $resumen_deuda_anterior) + encontrar_valor('mes_enr_couta', $resumen_deuda_anterior) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('comercial_enr_couta', $resumen_deuda) + encontrar_valor('mes_enr_couta', $resumen_deuda) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_cuota_noregulada', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_cuota_noregulada', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td>Sunat</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_sunat', $ganancia_perdida_anterior) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_sunat', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_sunat', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_sunat', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td>Otros gastos</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_otros_gastos', $ganancia_perdida_anterior) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('evaluacion_actual_ganancia_otros_gastos', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_otros_gastos', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_otros_gastos', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td style="background-color: #e5e5e5 !important;
                color: #000 !important;"><b>TOTAL DE GASTOS OPERATIVOS</b></td>
                <td class="campo_moneda" style="background-color: #e5e5e5 !important;
                color: #000 !important;"><b>{{ encontrar_valor('evaluacion_actual_ganancia_gastos_op', $ganancia_perdida_anterior) }}</b></td>
                <td class="campo_moneda" style="background-color: #e5e5e5 !important;
                color: #000 !important;"><b>{{ encontrar_valor('evaluacion_actual_ganancia_gastos_op', $ganancia_perdida) }}</b></td>
                <td class="campo_moneda" style="background-color: #e5e5e5 !important;
                color: #000 !important;"><b>{{ encontrar_valor('analisis_vertical_gastos_op', $ganancia_perdida) }}</b></td>
                <td class="campo_moneda" style="background-color: #e5e5e5 !important;
                color: #000 !important;"><b>{{ encontrar_valor('analisis_horizontal_gastos_op', $ganancia_perdida) }}</b></td>
              </tr>
              <tr>
                <td style="background-color: #e5e5e5 !important;
                color: #000 !important;"><b>UTILIDAD NETA</b></td>
                <td class="campo_moneda" style="background-color: #e5e5e5 !important;
                color: #000 !important;"><b>{{ encontrar_valor('evaluacion_actual_ganancia_utilidad_neta', $ganancia_perdida_anterior) }}</b></td>
                <td class="campo_moneda" style="background-color: #e5e5e5 !important;
                color: #000 !important;"><b>{{ encontrar_valor('evaluacion_actual_ganancia_utilidad_neta', $ganancia_perdida) }}</b></td>
                <td class="campo_moneda" style="background-color: #e5e5e5 !important;
                color: #000 !important;"><b>{{ encontrar_valor('analisis_vertical_utilidad_neta', $ganancia_perdida) }}</b></td>
                <td class="campo_moneda" style="background-color: #e5e5e5 !important;
                color: #000 !important;"><b>{{ encontrar_valor('analisis_horizontal_utilidad_neta', $ganancia_perdida) }}</b></td>
              </tr>
              <tr>
                <td>NEGOCIO ADICIONAL</td>
                <td class="campo_moneda">{{ encontrar_valor('ganancias_excedente_mensual', $ganancia_adicional_anterior) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('ganancias_excedente_mensual', $ganancia_adicional) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_negocio_adicional', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_negocio_adicional', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td>INGRESOS FIJOS</td>
                <td class="campo_moneda">{{ $credito_cuantitativa_ingreso_adicional_anterior ? $credito_cuantitativa_ingreso_adicional_anterior->total_ingreso_adicional : 0 }}</td>
                <td class="campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_ingreso_adicional : 0 }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_ingreso_fijo', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_ingreso_fijo', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td>GASTOS FAMILIARES</td>
                <td class="campo_moneda">{{ $credito_evaluacion_cualitativa_anterior ? $credito_evaluacion_cualitativa_anterior->gasto_total : 0 }}</td>
                <td class="campo_moneda">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_total : 0 }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_gasto_familiar', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_gasto_familiar', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td>Cuota de Préstamos de Consumo e Hipotecarios para Vivienda (Reg. y no Reg.)</td>
                <?php
                  $consumo_total_couta_anterior = encontrar_valor('consumo_total_couta', $resumen_deuda_anterior);
                  $vivienda_total_couta_anterior = encontrar_valor('vivienda_total_couta', $resumen_deuda_anterior);
                  $total_resumen_cuotas_linea_credito2_anterior = $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_resumen_cuotas_linea_credito2 : 0;
                  $evaluacion_actual_ganancia_cuota_vivienda_anterior = $consumo_total_couta_anterior + $vivienda_total_couta_anterior + $total_resumen_cuotas_linea_credito2_anterior;
                ?>
                <td class="campo_moneda">{{ $evaluacion_actual_ganancia_cuota_vivienda_anterior }}</td>
                <?php
                  $consumo_total_couta = encontrar_valor('consumo_total_couta', $resumen_deuda);
                  $vivienda_total_couta = encontrar_valor('vivienda_total_couta', $resumen_deuda);
                  $total_resumen_cuotas_linea_credito2 = $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_resumen_cuotas_linea_credito2 : 0;
                  $evaluacion_actual_ganancia_cuota_vivienda = $consumo_total_couta + $vivienda_total_couta + $total_resumen_cuotas_linea_credito2;
                ?>
                <td class="campo_moneda">{{ $evaluacion_actual_ganancia_cuota_vivienda }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_vertical_cuota_vivienda', $ganancia_perdida) }}</td>
                <td class="campo_moneda">{{ encontrar_valor('analisis_horizontal_cuota_vivienda', $ganancia_perdida) }}</td>
              </tr>
              <tr>
                <td style="background-color: #e5e5e5 !important;
                color: #000 !important;"><b>EXCEDENTE MENSUAL</b></td>
                <td class="campo_moneda" style="background-color: #e5e5e5 !important;
                color: #000 !important;"><b>{{ encontrar_valor('evaluacion_actual_ganancia_excedente_mensual', $ganancia_perdida_anterior) }}</b></td>
                <td class="campo_moneda" style="background-color: #e5e5e5 !important;
                color: #000 !important;"><b>{{ encontrar_valor('evaluacion_actual_ganancia_excedente_mensual', $ganancia_perdida) }}</b></td>
                <td class="campo_moneda" style="background-color: #e5e5e5 !important;
                color: #000 !important;"><b>{{ encontrar_valor('analisis_vertical_excedente_mensual', $ganancia_perdida) }}</b></td>
                <td class="campo_moneda" style="background-color: #e5e5e5 !important;
                color: #000 !important;"><b>{{ encontrar_valor('analisis_horizontal_excedente_mensual', $ganancia_perdida) }}</b></td>
              </tr>
            </tbody>
          </table>
      </div> 
    </div>
    <span class="badge subtitle" style="margin-top:-20px;">3.4 PRINCIPALES RATIOS FINANCIEROS</span>
    <div class="row">
      <div class="col">
        <table class="table">
          <tbody>
            <tr>
              <td width="200px">Rentabilidad del negocio</td>
              <td width="50px">%</td>
              <td width="50px" class="campo_moneda">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_negocio : '0.00' }}</td>
            </tr>
            <tr>
              <td>Rentabilidad de la unidad familiar</td>
              <td>Veces</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_unidadfamiliar : '0.00' }}</td>
            </tr>
            <tr>
              <td>Rentabilidad patrimonial (ROE)</td>
              <td>%</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_patrimonial : '0.00' }}</td>
            </tr>
            <tr>
              <td>Rentabilidad de los activos (ROA)</td>
              <td>%</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_activos : '0.00' }}</td>
            </tr>
            <tr>
              <td>Rentabilidad de las ventas (ROS)</td>
              <td>%</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_ventas : '0.00' }}</td>
            </tr>
            <tr>
              <td>Préstamo / capital de trabajo Neto</td>
              <td>%</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_prestamo : '0.00' }}</td>
            </tr>
            <tr>
              <td>Capital de trabajo</td>
              <td>S/</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_capital : '0.00' }}</td>
            </tr>
            <tr>
              <td>Liquidez</td>
              <td>Veces</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_liquidez : '0.00' }}</td>
            </tr>
          </tbody>
        </table>
      </div>  
      <div class="col">
        <table class="table">
          <tbody>
            <tr>
              <td width="200px">Liquidez Ácida</td>
              <td width="50px">Veces</td>
              <td width="50px" class="campo_moneda">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_liquidez_acida : '0.00' }}</td>
            </tr>
            <tr>
              <td>Endeudamiento patrimonial actual</td>
              <td>Veces</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_endeudamiento_actual : '0.00' }}</td>
            </tr>
            <tr>
              <td>Endeudamiento Patrim. con propuesta</td>
              <td>Veces</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_endeudamiento_propuesta : '0.00' }}</td>
            </tr>

            <tr>
              <td>Plazo prom.rotación de invent.</td>
              <td>Días</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_rotacion_inventario : '0.00' }}</td>
            </tr>
            <tr>
              <td>Plazo promedio de cobranza</td>
              <td>Días</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_promedio_cobranza : '0.00' }}</td>
            </tr>
            <tr>
              <td>Plazo promedio de pago</td>
              <td>Días</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_primedio_pago : '0.00' }}</td>
            </tr>
            <tr>
              <td style="background-color: #e5e5e5 !important;
                color: #000 !important;"><b>Cuota total/excedente total</b></td>
              <td style="background-color: #e5e5e5 !important;
                color: #000 !important;"><b>%</b></td>
              <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda"><b>{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->excedente_propuesta_con_deduccion : '0.00' }}</b></td>
            </tr>
          </tbody>
        </table>
        <div style="margin-top:5px;background-color:#e5e5e5;color: #000;text-align:center;"><b>{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->estado_credito : '' }}</b></div>
      </div>
    </div>
      
    <div class="row" >
      <div class="col" style="width:100%;">
        <span class="badge subtitle">3.5 COMENTARIOS DE ASPECTOS RESALTANTES</span>
        <div class="row">
          <textarea id="fortalezas_negocio" class="form-control">{{ $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->comentario : '' }}</textarea>
        </div>
      </div>
      <div class="col" style="margin-left:250px;margin-top:40px;">
        <div style="width:200px;height:1px;border-bottom:1px solid #ccc;"></div>
        <p align="center">Asesor(a) de Créditos: {{ Auth::user()->codigo }} <br>Firma y Sello</p>		
      </div>
    </div>
  </main>
</body>
</html>