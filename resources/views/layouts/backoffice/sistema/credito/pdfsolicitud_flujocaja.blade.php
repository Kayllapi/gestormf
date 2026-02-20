<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FLUJO DE CAJA</title>
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
        border:solid 1px #000000;    
      }
      .table, .table th, .table td {
        border: 1px solid #000000;
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
    <div style="float:left;font-size:15px;">{{ $tienda->nombre }} | {{ $tienda->nombreagencia }}</div> {{ Auth::user()->usuario }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
  </footer>
  @php
    $evaluacion_meses = $credito_evaluacion_cuantitativa ? ( $credito_evaluacion_cuantitativa->evaluacion_meses == "" ? [] : json_decode($credito_evaluacion_cuantitativa->evaluacion_meses) ) : [];
    $balance_general = $credito_evaluacion_cuantitativa ? ( $credito_evaluacion_cuantitativa->balance_general == "" ? [] : json_decode($credito_evaluacion_cuantitativa->balance_general) ) : [];
    $saldo_inicial = encontrar_valor('evaluacion_actual_caja', $balance_general) + encontrar_valor('evaluacion_actual_bancos', $balance_general);
    $ganancia_perdida = $credito_evaluacion_cuantitativa ? ( $credito_evaluacion_cuantitativa->ganancia_perdida == "" ? [] : json_decode($credito_evaluacion_cuantitativa->ganancia_perdida) ) : [];

    $ganancia_perdida_ing_adicional = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->ganancias_perdidas == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->ganancias_perdidas) ) : [];
    $entidad_regulada = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->entidad_regulada == "" ? [] : json_decode($credito_cuantitativa_deudas->entidad_regulada) ) : [];
    $linea_credito = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->linea_credito == "" ? [] : json_decode($credito_cuantitativa_deudas->linea_credito) ) : [];
    $entidad_noregulada = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->entidad_noregulada == "" ? [] : json_decode($credito_cuantitativa_deudas->entidad_noregulada) ) : [];
    $venta_mensual = 0;
    
    $flujo_caja = $credito_flujo_caja ? ( $credito_flujo_caja->flujo_caja == "" ? [] : json_decode($credito_flujo_caja->flujo_caja) ) : [];
    $encabezado = $credito_flujo_caja ? ( $credito_flujo_caja->encabezado == "" ? [] : json_decode($credito_flujo_caja->encabezado) ) : [];
  
    $entidad_reguladas = $credito_flujo_caja ? ( $credito_flujo_caja->entidad_reguladas == "" ? [] : json_decode($credito_flujo_caja->entidad_reguladas) ) : [];
    $linea_credito = $credito_flujo_caja ? ( $credito_flujo_caja->linea_credito == "" ? [] : json_decode($credito_flujo_caja->linea_credito) ) : [];
    $entidad_noregulada = $credito_flujo_caja ? ( $credito_flujo_caja->entidad_noregulada == "" ? [] : json_decode($credito_flujo_caja->entidad_noregulada) ) : [];
    $comentarios = $credito_flujo_caja ? ( $credito_flujo_caja->comentarios == "" ? [] : json_decode($credito_flujo_caja->comentarios) ) : [];
  @endphp
  <main>
    <span class="badge">X. FLUJO DE CAJA</span>
    
    <div class="row">
      <div class="col" style="width:300px;">
        <table style="width:100%;">
          <tr>
            <td>CLIENTE/RAZON SOCIAL:</td>
            <td class="border-td">{{ $credito->nombreclientecredito }}</td>
          </tr>
          {{-- <tr>
            <td>PRODUCTO:</td>
            <td class="border-td">{{ $credito->nombreproductocredito }}</td>
          </tr> --}}
        </table>
      </div>
      <div class="col">
        <table>
          <tr>
            <td>MODALIDAD:</td>
            <td class="border-td" width="100px">{{ $credito->modalidad_credito_nombre }}</td>
          </tr>
          
        </table>
      </div>
      <div class="col">
        <table>
          <tr>
            <td>NRO SOLICITUD:</td>
            <td class="border-td" width="100px">S{{ str_pad($credito->id, 8, '0', STR_PAD_LEFT)  }}</td>
          </tr>
          {{-- <tr>  
            <td>FECHA:</td>
            <td class="border-td">{{ date_format(date_create($credito->fecha),'Y-m-d') }}</td>
          </tr> --}}
        </table>
      </div>
      <div class="col">
        <table>
          <tr>
            <td>PRODUCTO:</td>
            <td class="border-td" width="130px">{{ $credito->nombreproductocredito }}</td>
          </tr>
        </table>
      </div>
      <div class="col">
        <table>
          <tr>  
            <td>FECHA:</td>
            <td class="border-td" width="100px">{{ date_format(date_create($credito->fecha),'Y-m-d') }}</td>
          </tr>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col">
        
        <table class="table" width="1060px">
          <thead>
            @if( count($encabezado) > 0)
              @foreach($encabezado as $value)
                <tr>
                  @foreach($value->encabezado as $thval)
                    <td style="text-align: center;">{{ $thval->th }}</td>
                  @endforeach
                  
                </tr>
              @endforeach
            @endif
            
          </thead>
          <tbody>
            @if(count($evaluacion_meses) > 0)

              @foreach ($evaluacion_meses as $key => $row)
                @if( $key == 1)
                <tr porcentaje_cliclo_negocio encabezado>
                  <td>CICLO DEL NEGOCIO</td>
                  @php $contador_meses = 1 @endphp
                  @php $primer_mes = 0 @endphp
                  @foreach ($row as $header => $cellData)
                      @if ($header !== 'Mes')
                        @php $contador_meses++ @endphp
                        @php
                          $value = $cellData->value;
                          if($contador_meses == 2){
                            $primer_mes = $value;
                          }
                        @endphp
                        <td>{{ $value }}</td>
                      @endif
                  @endforeach
                  <td>{{$primer_mes}}</td>
                </tr>
                @endif
              @endforeach
            @endif
            <tr>
              <td style="width: 200px">Saldo Inicial Caja</td>
              <td class="campo_moneda">{{ encontrar_valor('saldo_inicial', $flujo_caja) }}</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td colspan=14>INGRESOS (S/.)</td>
            </tr>
            <tr>
              <td>Ventas</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_ventas', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td>Otras Ventas</td>

              <td class="campo_moneda">{{ encontrar_valor('mes_cero_otras_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_otras_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_otras_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_otras_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_otras_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_otras_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_otras_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_otras_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_otras_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_otras_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_otras_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_otras_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_otras_ventas', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td>Cobranza de cuentas por cobrar</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_cobranza', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_cobranza', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_cobranza', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_cobranza', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_cobranza', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_cobranza', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_cobranza', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_cobranza', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_cobranza', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_cobranza', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_cobranza', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_cobranza', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_cobranza', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td>Venta de Activo Fijo</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_activo_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_activo_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_activo_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_activo_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_activo_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_activo_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_activo_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_activo_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_activo_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_activo_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_activo_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_activo_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_activo_fijo', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td>Ingreso por Negocio Adicional</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_negocio_adicional', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_negocio_adicional', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_negocio_adicional', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_negocio_adicional', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_negocio_adicional', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_negocio_adicional', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_negocio_adicional', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_negocio_adicional', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_negocio_adicional', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_negocio_adicional', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_negocio_adicional', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_negocio_adicional', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_negocio_adicional', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td>Ingresos Fijos</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_ingreso_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_ingreso_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_ingreso_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_ingreso_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_ingreso_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_ingreso_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_ingreso_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_ingreso_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_ingreso_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_ingreso_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_ingreso_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_ingreso_fijo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_ingreso_fijo', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td>Ingresos Financieros</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_ingreso_financiero', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_ingreso_financiero', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_ingreso_financiero', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_ingreso_financiero', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_ingreso_financiero', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_ingreso_financiero', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_ingreso_financiero', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_ingreso_financiero', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_ingreso_financiero', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_ingreso_financiero', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_ingreso_financiero', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_ingreso_financiero', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_ingreso_financiero', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td>Ingresos Extraordinarios</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_ingreso_extra', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_ingreso_extra', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_ingreso_extra', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_ingreso_extra', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_ingreso_extra', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_ingreso_extra', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_ingreso_extra', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_ingreso_extra', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_ingreso_extra', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_ingreso_extra', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_ingreso_extra', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_ingreso_extra', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_ingreso_extra', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td>Préstamo SOLICITADO</td>
              <td></td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_prestamo_solicitado', $flujo_caja) }}</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>Otros Préstamos</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_otros_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_otros_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_otros_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_otros_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_otros_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_otros_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_otros_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_otros_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_otros_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_otros_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_otros_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_otros_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_otros_prestamo', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td><b>Total Ingresos (A) (S/.)</b></td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_total_ingreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_total_ingreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_total_ingreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_total_ingreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_total_ingreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_total_ingreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_total_ingreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_total_ingreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_total_ingreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_total_ingreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_total_ingreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_total_ingreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_total_ingreso', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td colspan=14>EGRESOS (S/.)</td>
            </tr>
            <tr>
              <td>Compras/ Costos</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_compras', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_compras', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_compras', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_compras', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_compras', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_compras', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_compras', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_compras', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_compras', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_compras', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_compras', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_compras', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_compras', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td>Inversiones</td>
              <td></td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_inversiones', $flujo_caja) }}</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>Gastos de personal administrativo</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_gasto_admin', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_gasto_admin', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_gasto_admin', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_gasto_admin', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_gasto_admin', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_gasto_admin', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_gasto_admin', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_gasto_admin', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_gasto_admin', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_gasto_admin', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_gasto_admin', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_gasto_admin', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_gasto_admin', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td>Gastos de personal de ventas</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_gasto_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_gasto_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_gasto_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_gasto_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_gasto_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_gasto_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_gasto_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_gasto_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_gasto_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_gasto_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_gasto_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_gasto_ventas', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_gasto_ventas', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td>Servicios(agua, luz, teléfono , otros)</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_servicios', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_servicios', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_servicios', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_servicios', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_servicios', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_servicios', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_servicios', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_servicios', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_servicios', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_servicios', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_servicios', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_servicios', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_servicios', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td>Alquiler de local</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_alquiler', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_alquiler', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_alquiler', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_alquiler', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_alquiler', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_alquiler', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_alquiler', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_alquiler', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_alquiler', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_alquiler', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_alquiler', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_alquiler', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_alquiler', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td>Autoavalúo, serenazgo, parques y J.</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_autovaluo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_autovaluo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_autovaluo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_autovaluo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_autovaluo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_autovaluo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_autovaluo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_autovaluo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_autovaluo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_autovaluo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_autovaluo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_autovaluo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_autovaluo', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td>Transporte</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_transporte', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_transporte', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_transporte', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_transporte', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_transporte', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_transporte', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_transporte', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_transporte', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_transporte', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_transporte', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_transporte', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_transporte', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_transporte', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td class="fw-bold"><u>Cuotas de deudas S. F. (S/.)</u></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td class="fw-bold"><u>Entidades Reguladas</u></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            @foreach($entidad_reguladas as $value)
              
              <tr>
                <td class="campo_moneda">{{ $value->nombre_entidad }}</td>
                <td class="campo_moneda">{{ $value->mes_cero }}</td>
                <td class="campo_moneda">{{ $value->mes_uno }}</td>
                <td class="campo_moneda">{{ $value->mes_dos }}</td>
                <td class="campo_moneda">{{ $value->mes_tres }}</td>
                <td class="campo_moneda">{{ $value->mes_cuatro }}</td>
                <td class="campo_moneda">{{ $value->mes_cinco }}</td>
                <td class="campo_moneda">{{ $value->mes_seis }}</td>
                <td class="campo_moneda">{{ $value->mes_siete }}</td>
                <td class="campo_moneda">{{ $value->mes_ocho }}</td>
                <td class="campo_moneda">{{ $value->mes_nueve }}</td>
                <td class="campo_moneda">{{ $value->mes_diez }}</td>
                <td class="campo_moneda">{{ $value->mes_once }}</td>
                <td class="campo_moneda">{{ $value->mes_doce }}</td>
              </tr>
            @endforeach
            <tr>
              <td><u>Líneas de crédito(tarjetas) No Utilizadas</u></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            @foreach($linea_credito as $value)

              <tr>
                <td class="campo_moneda">{{ $value->nombre_entidad }}</td>
                <td class="campo_moneda">{{ $value->mes_cero }}</td>
                <td class="campo_moneda">{{ $value->mes_uno }}</td>
                <td class="campo_moneda">{{ $value->mes_dos }}</td>
                <td class="campo_moneda">{{ $value->mes_tres }}</td>
                <td class="campo_moneda">{{ $value->mes_cuatro }}</td>
                <td class="campo_moneda">{{ $value->mes_cinco }}</td>
                <td class="campo_moneda">{{ $value->mes_seis }}</td>
                <td class="campo_moneda">{{ $value->mes_siete }}</td>
                <td class="campo_moneda">{{ $value->mes_ocho }}</td>
                <td class="campo_moneda">{{ $value->mes_nueve }}</td>
                <td class="campo_moneda">{{ $value->mes_diez }}</td>
                <td class="campo_moneda">{{ $value->mes_once }}</td>
                <td class="campo_moneda">{{ $value->mes_doce }}</td>
              </tr>
            @endforeach
            <tr>
              <td class="fw-bold"><u>Entidades NO Reguladas</u></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            @foreach($entidad_noregulada as $value)
              <tr>
                <td class="campo_moneda">{{ $value->nombre_entidad }}</td>
                <td class="campo_moneda">{{ $value->mes_cero }}</td>
                <td class="campo_moneda">{{ $value->mes_uno }}</td>
                <td class="campo_moneda">{{ $value->mes_dos }}</td>
                <td class="campo_moneda">{{ $value->mes_tres }}</td>
                <td class="campo_moneda">{{ $value->mes_cuatro }}</td>
                <td class="campo_moneda">{{ $value->mes_cinco }}</td>
                <td class="campo_moneda">{{ $value->mes_seis }}</td>
                <td class="campo_moneda">{{ $value->mes_siete }}</td>
                <td class="campo_moneda">{{ $value->mes_ocho }}</td>
                <td class="campo_moneda">{{ $value->mes_nueve }}</td>
                <td class="campo_moneda">{{ $value->mes_diez }}</td>
                <td class="campo_moneda">{{ $value->mes_once }}</td>
                <td class="campo_moneda">{{ $value->mes_doce }}</td>
              </tr>
            @endforeach

            <tr>
              <td>Pago del préstamo SOLICITADO</td>
              <td></td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_pago_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_pago_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_pago_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_pago_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_pago_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_pago_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_pago_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_pago_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_pago_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_pago_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_pago_prestamo', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_pago_prestamo', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td colspan=14>&nbsp;</td>
            </tr>
            <tr>
              <td>SUNAT</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_sunat', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_sunat', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_sunat', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_sunat', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_sunat', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_sunat', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_sunat', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_sunat', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_sunat', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_sunat', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_sunat', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_sunat', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_sunat', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td>Otros Gastos</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_otros_gastos', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_otros_gastos', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_otros_gastos', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_otros_gastos', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_otros_gastos', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_otros_gastos', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_otros_gastos', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_otros_gastos', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_otros_gastos', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_otros_gastos', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_otros_gastos', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_otros_gastos', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_otros_gastos', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td>Canasta Familiar y Otros</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_canasta_familiar', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_canasta_familiar', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_canasta_familiar', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_canasta_familiar', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_canasta_familiar', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_canasta_familiar', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_canasta_familiar', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_canasta_familiar', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_canasta_familiar', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_canasta_familiar', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_canasta_familiar', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_canasta_familiar', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_canasta_familiar', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td colspan=14>&nbsp;</td>
            </tr>
            <tr>
              <td class="fw-bold"><u>Total Egresos (B) (S/.)</u></td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_total_egreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_total_egreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_total_egreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_total_egreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_total_egreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_total_egreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_total_egreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_total_egreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_total_egreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_total_egreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_total_egreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_total_egreso', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_total_egreso', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td colspan=14>&nbsp;</td>
            </tr>
            <tr>
              <td style="background-color: #e5e5e5 !important;
                color: #000 !important;"><u>SALDO CAJA MENSUAL (A-B) (S/.)</u></td>
              <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda">{{ encontrar_valor('mes_cero_saldo_caja', $flujo_caja) }}</td>
              <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda">{{ encontrar_valor('mes_uno_saldo_caja', $flujo_caja) }}</td>
              <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda">{{ encontrar_valor('mes_dos_saldo_caja', $flujo_caja) }}</td>
              <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda">{{ encontrar_valor('mes_tres_saldo_caja', $flujo_caja) }}</td>
              <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda">{{ encontrar_valor('mes_cuatro_saldo_caja', $flujo_caja) }}</td>
              <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda">{{ encontrar_valor('mes_cinco_saldo_caja', $flujo_caja) }}</td>
              <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda">{{ encontrar_valor('mes_seis_saldo_caja', $flujo_caja) }}</td>
              <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda">{{ encontrar_valor('mes_siete_saldo_caja', $flujo_caja) }}</td>
              <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda">{{ encontrar_valor('mes_ocho_saldo_caja', $flujo_caja) }}</td>
              <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda">{{ encontrar_valor('mes_nueve_saldo_caja', $flujo_caja) }}</td>
              <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda">{{ encontrar_valor('mes_diez_saldo_caja', $flujo_caja) }}</td>
              <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda">{{ encontrar_valor('mes_once_saldo_caja', $flujo_caja) }}</td>
              <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda">{{ encontrar_valor('mes_doce_saldo_caja', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td><u>SALDO ACUMULADO MENSUAL (S/.)</u></td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cero_saldo_acumulado', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_uno_saldo_acumulado', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_dos_saldo_acumulado', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_tres_saldo_acumulado', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cuatro_saldo_acumulado', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_cinco_saldo_acumulado', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_seis_saldo_acumulado', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_siete_saldo_acumulado', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_ocho_saldo_acumulado', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_nueve_saldo_acumulado', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_diez_saldo_acumulado', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_once_saldo_acumulado', $flujo_caja) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_doce_saldo_acumulado', $flujo_caja) }}</td>
            </tr>
            <tr>
              <td>Costo de Venta</td>
              <td class="campo_moneda">{{ encontrar_valor('porcentaje_costo_venta', $flujo_caja) }}</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="row"> 
      <div class="col">
        <table class="table"  width="705px">
          <thead>
            <tr>
              <th>Supuestos</th>
            </tr>
          </thead>
          <tbody>
            @foreach($comentarios as $value)
              <tr>
                <td>{{ $value->descripcion }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="col" style="margin-left:750px;margin-top:5px;">
        <div style="width:300px;height:1px;border-bottom:1px solid #000;"></div>
        <p align="center">Asesor(a) de Créditos: {{ Auth::user()->codigo }} <br>Firma y Sello</p>		
      </div>
    </div>

    <div class="row" >
    </div>
  </main>
</body>
</html>