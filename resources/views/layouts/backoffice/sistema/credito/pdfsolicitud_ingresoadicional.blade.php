<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INGRESO ADICIONAL</title>
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
    $venta_mensual = $credito_cuantitativa_margen_venta ? ($credito_cuantitativa_margen_venta->venta_mensual + $credito_cuantitativa_margen_venta->venta_total_mensual) : '0.00';

    $evaluacion_meses = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->evaluacion_meses == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->evaluacion_meses) ) : [];
    $productos = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->productos == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->productos) ) : [];
    $productos_mensual = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->productos_mensual == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->productos_mensual) ) : [];
    $dias = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->dias == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->dias) ) : [];
    $semanas = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->semanas == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->semanas) ) : [];
    $subproducto = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->subproducto == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->subproducto) ) : [];
    $subproductomensual = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->subproductomensual == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->subproductomensual) ) : [];

    $inventario = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->inventario == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->inventario) ) : [];
    $inmuebles = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->inmuebles == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->inmuebles) ) : [];
    $muebles = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->muebles == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->muebles) ) : [];

    $resumen = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->balance_general == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->balance_general) ) : [];
    $ganancia_perdida = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->ganancias_perdidas == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->ganancias_perdidas) ) : [];

    $adicional_fijo = $credito_cuantitativa_ingreso_adicional ? ( $credito_cuantitativa_ingreso_adicional->adicional_fijo == "" ? [] : json_decode($credito_cuantitativa_ingreso_adicional->adicional_fijo) ) : [];
  @endphp
  <main>
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
            <td class="border-td">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->nombregiro_economico_evaluacion : '' }}</td>
          </tr>
          <tr>
            <td>GIRO ECONÓMICO ADICIONAL:</td>
            <td class="border-td">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->nombreingresoadicional : '' }}</td>
          </tr>
        </table>
      </div>
      <div class="col">
        <table>
          <tr>
            <td>FECHA:</td>
            <td class="border-td" width="100px">{{ $credito_cuantitativa_ingreso_adicional!=''?date_format(date_create($credito_cuantitativa_ingreso_adicional->fecha),'Y-m-d'):'' }}</td>
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
    <span class="badge">VII. EVALUACIÓN DE INGRESO ADICIONAL INDEPENDIENTE:</span>
    <span class="badge subtitle">7.1 EVALUACIÓN ECONÓMICA FINANCIERA:</span>
    <span class="badge subtitle">7.1.1 CICLO DEL NEGOCIO: (Actual =100%, Alta > 100%, Baja &lt; 100%)</span>
    @if($credito_cuantitativa_ingreso_adicional!='')
    <div class="row">
      <div class="col">
        <table class="table" width="735px">
          <?php generarTabla($credito_cuantitativa_ingreso_adicional->fecha); ?>
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
      <div class="col">
        <table>
          <tr>
            <td>MARGEN DE VENTAS TOTAL CALCULADO:</td>
            <td>{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->margen_venta_calculado : '0.00' }}</td>
          </tr>
        </table>
      </div>
    </div>
    @endif  
    <span class="badge subtitle">7.1.2 ESTADOS FINANCIEROS</span>
    <div class="row">
      <div class="col">
        <table class="table table-bordered" id="table-balance-general">
          <thead>
            <tr>
              <th colspan=2>BALANCE GENERAL</th>
              <th width="50px">Soles (S/. )</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan=2>Caja</td>
              <td class="campo_moneda">{{ encontrar_valor('balance_caja', $resumen) }}</td>
            </tr>
            <tr>
              <td colspan=2>Bancos</td>
              <td class="campo_moneda">{{ encontrar_valor('balance_bancos', $resumen) }}</td>
            </tr>
            <tr>
              <td colspan=2>Cuentas por cobrar a clientes</td>
              <td class="campo_moneda">{{ encontrar_valor('balance_cuentas_cobrar', $resumen) }}</td>
            </tr>
            <tr>
              <td colspan=2>Adelanto a proveedores</td>
              <td class="campo_moneda">{{ encontrar_valor('balance_adelanto_proveedor', $resumen) }}</td>
            </tr>
            <tr>
              <td colspan=2>Inventarios</td>
              <td class="campo_moneda">{{ encontrar_valor('balance_inventario', $resumen) }}</td>
            </tr>
            <tr>
              <td colspan=2><b>ACTIVO CORRIENTE</b></td>
              <td class="campo_moneda">{{ encontrar_valor('balance_activo_corriente', $resumen) }}</td>
            </tr>
            <tr>
              <td colspan=2>Activo inmueble</td>
              <td class="campo_moneda">{{ encontrar_valor('balance_activo_inmueble', $resumen) }}</td>
            </tr>
            <tr>
              <td colspan=2>Activo mueble</td>
              <td class="campo_moneda">{{ encontrar_valor('balance_activo_mueble', $resumen) }}</td>
            </tr>
            <tr>
              <td colspan=2><b>ACTIVO NO CORRIENTE</b></td>
              <td class="campo_moneda">{{ encontrar_valor('balance_activo_nocorriente', $resumen) }}</td>
            </tr>
            <tr>
              <td style="background-color: #c8c8c8 !important;
              color: #000 !important;" colspan=2><b>TOTAL ACTIVO</b></td>
              <td style="background-color: #c8c8c8 !important;
              color: #000 !important;" class="campo_moneda">{{ encontrar_valor('balance_total_activo', $resumen) }}</td>
            </tr>
            <tr>
              <td colspan=2>Cuentas por pagar a proveedores</td>
              <td class="campo_moneda">{{ encontrar_valor('balance_cuentas_pagar', $resumen) }}</td>
            </tr>
            <tr>
              <td rowspan=2>Pasivos financieros a corto plazo</td>
              <td >E. Reguladas</td>
              <td class="campo_moneda">{{ encontrar_valor('balance_corto_reguladas', $resumen) }}</td>
            </tr>
            <tr>
              <td > E. No Reguladas</td>
              <td class="campo_moneda">{{ encontrar_valor('balance_corto_noreguladas', $resumen) }}</td>
            </tr>
            <tr>
              <td colspan=2>Impuestos por pagar</td>
              <td class="campo_moneda">{{ encontrar_valor('balance_impuesto', $resumen) }}</td>
            </tr>
            <tr>
              <td colspan=2>Otras cuentas por pagar</td>
              <td class="campo_moneda">{{ encontrar_valor('balance_otras_cuentas', $resumen) }}</td>
            </tr>
             <tr>
              <td colspan=2><b>PASIVO CORRIENTE</b></td>
              <td class="campo_moneda">{{ encontrar_valor('balance_pasivo_corriente', $resumen) }}</td>
            </tr>
            <tr>
              <td rowspan=2>Pasivo Fin. a Largo.Plazo </td>
              <td>E. Reguladas</td>
              <td class="campo_moneda">{{ encontrar_valor('balance_largo_reguladas', $resumen) }}</td>
            </tr>
            <tr>
              <td>E. No Reguladas</td>
              <td class="campo_moneda">{{ encontrar_valor('balance_largo_noreguladas', $resumen) }}</td>
            </tr>
            <tr>
              <td colspan=2><b>PASIVO NO CORRIENTE</b></td>
              <td class="campo_moneda">{{ encontrar_valor('balance_pasivo_nocorriente', $resumen) }}</td>
            </tr>
            <tr>
              <td style="background-color: #c8c8c8 !important;
              color: #000 !important;" colspan=2><b>TOTAL PASIVO</b></td>
              <td style="background-color: #c8c8c8 !important;
              color: #000 !important;" class="campo_moneda">{{ encontrar_valor('balance_total_pasivo', $resumen) }}</td>
            </tr>
            <tr>
              <td colspan=2>Capital social</td>
              <td class="campo_moneda">{{ encontrar_valor('balance_capital_social', $resumen) }}</td>
            </tr>
            <tr>
              <td colspan=2>Utilidades acumuladas</td>
              <td class="campo_moneda">{{ encontrar_valor('balance_utilidad_acumulada', $resumen) }}</td>
            </tr>
            <tr>
              <td style="background-color: #c8c8c8 !important;
              color: #000 !important;" colspan=2><b>TOTAL PATRIMONIO</b></td>
              <td style="background-color: #c8c8c8 !important;
              color: #000 !important;" class="campo_moneda">{{ encontrar_valor('balance_total_patrimonio', $resumen) }}</td>
            </tr>
            <tr>
              <td style="background-color: #c8c8c8 !important;
              color: #000 !important;" colspan=2><b>TOTAL PASIVO + PATRIMONIO</b></td>
              <td style="background-color: #c8c8c8 !important;
              color: #000 !important;" class="campo_moneda">{{ encontrar_valor('balance_pasivo_patrimonio', $resumen) }}</td>
            </tr>




          </tbody>
        </table>
      </div>  
      <div class="col">
        <table class="table table-bordered" id="table-estado-ganancias-perdidas">
          <thead>
            <tr>
              <th>ESTADO DE GANANCIAS Y PERDIDAS</th>
              <th width="50px">Soles (S/. )</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><b>VENTAS MENSUALES</b></td>
              <td class="campo_moneda">{{ encontrar_valor('ganancias_venta_mensual', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td>Costo de venta (C. de producción)</td>
              <td class="campo_moneda">{{ encontrar_valor('ganancias_costo_venta', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td style="background-color: #c8c8c8 !important;
              color: #000 !important;"><b>UTILIDAD BRUTA</b></td>
              <td style="background-color: #c8c8c8 !important;
              color: #000 !important;" class="campo_moneda">{{ encontrar_valor('ganancias_utilidad_bruta', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td>Gastos de personal administrativo</td>
              <td class="campo_moneda">{{ encontrar_valor('ganancias_gasto_administrativo', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td>Gastos de personal de ventas</td>
              <td class="campo_moneda">{{ encontrar_valor('ganancias_gasto_ventas', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td><b>Servicios:</b></td>
              <td class="campo_moneda">{{ encontrar_valor('ganancias_total_servicios', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Luz</td>
              <td class="campo_moneda">{{ encontrar_valor('ganancias_servicio_luz', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Agua</td>
              <td class="campo_moneda">{{ encontrar_valor('ganancias_servicio_agua', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Telefono/internet</td>
              <td class="campo_moneda">{{ encontrar_valor('ganancias_servicio_internet', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- T. celular</td>
              <td class="campo_moneda">{{ encontrar_valor('ganancias_servicio_celular', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Cable</td>
              <td class="campo_moneda">{{ encontrar_valor('ganancias_servicio_cable', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td>Alquiler de local</td>
              <td class="campo_moneda">{{ encontrar_valor('ganancias_alquiler_local', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td>Autoavalúo, serenazgo, parques y J.</td>
              <td class="campo_moneda">{{ encontrar_valor('ganancias_autovaluo', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td>Transporte</td>
              <td class="campo_moneda">{{ encontrar_valor('ganancias_transporte', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td>Cuota de préstamo E. Reguladas</td>
              <td class="campo_moneda">{{ encontrar_valor('ganancias_cuota_prestamo_regulada', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td>Cuota de préstamo E. No Reguladas</td>
              <td class="campo_moneda">{{ encontrar_valor('ganancias_cuota_prestamo_noregulada', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td>Sunat</td>
              <td class="campo_moneda">{{ encontrar_valor('ganancias_sunat', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td>Otros gastos</td>
              <td class="campo_moneda">{{ encontrar_valor('ganancias_otros_gastos', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td style="background-color: #c8c8c8 !important;
              color: #000 !important;"><b>TOTAL DE GASTOS OPERATIVOS</b></td>
              <td style="background-color: #c8c8c8 !important;
              color: #000 !important;" class="campo_moneda">{{ encontrar_valor('ganancias_gastos_operativos', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td style="background-color: #c8c8c8 !important;
              color: #000 !important;"><b>UTILIDAD NETA</b></td>
              <td style="background-color: #c8c8c8 !important;
              color: #000 !important;" class="campo_moneda">{{ encontrar_valor('ganancias_utilidad_neta', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td>Cuota de Préstamos de Consumo e Hipotecarios para Vivienda (Reg. y no Reg.)</td>
              <td class="campo_moneda">{{ encontrar_valor('ganancias_consumo_hipotecario', $ganancia_perdida) }}</td>
            </tr>
            <tr>
              <td style="background-color: #c8c8c8 !important;
              color: #000 !important;"><b>EXCEDENTE MENSUAL</b></td>
              <td style="background-color: #c8c8c8 !important;
              color: #000 !important;" class="campo_moneda">{{ encontrar_valor('ganancias_excedente_mensual', $ganancia_perdida) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <span class="badge subtitle">7.1.3 MOVIMIENTO COMERCIAL</span>
    <div class="row">
      <div class="col" style="width:350px;">
        <table class="table" style="width:100%;">
          <thead>
            <tr>
              <th colspan=3>Ventas Mensuales (S/.)</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Al crédito para cobro a {{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->dias_ventas_mensual : '0' }} días total al mes</td>
              <td class="campo_moneda" width="70px">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->credito_cobrando_venta_mensual : '0.00' }}</td>
              <td class="campo_moneda" width="70px">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->credito_porcentaje_venta_mensual : '0.00' }}%</td>
            </tr>
            <tr>
              <td>Al Contado</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->contado_cobrando_venta_mensual : '0.00' }}</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->contado_porcentaje_venta_mensual : '0.00' }}%</td>
            </tr>
          </tbody>
        </table>  
      </div>  
      <div class="col" style="width:350px;">
        <table class="table" style="width:100%;">
          <thead>
            <tr>
              <th colspan=3>Compras Mensuales (S/.)</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Al crédito para pago a {{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->dias_compras_mensual : '0' }} días total al mes</td>
              <td class="campo_moneda" width="70px">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->credito_cobrando_compra_mensual : '0.00' }}</td>
              <td class="campo_moneda" width="70px">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->credito_porcentaje_compra_mensual : '0.00' }}%</td>
            </tr>
            <tr>
              <td>Al Contado</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->contado_cobrando_compra_mensual : '0.00' }}</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->contado_porcentaje_compra_mensual : '0.00' }}%</td>
            </tr>
          </tbody>
        </table> 
      </div>  
    </div>
    <span class="badge subtitle">7.1.4 COMENTARIOS</span>
    <div class="row">
      <div class="col">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->comentario : '' }}</div>  
    </div>
    <span class="badge subtitle">7.2. CALCULO DE MARGEN Y NIVEL VENTAS</span>
    <span class="badge subtitle">7.2.1 VENTAS DENTRO DE LA SEMANA (VENTAS CON FRECUENCIA DIARIA Y SEMANAL)</span>
    <div class="row"> 
      <div class="col">
        <table class="table">
          <thead>
            <tr>
              <th rowspan=2 width="150px">VENTA MUESTRA: De productos(de mayor rotación) que comercializa, produce o presta servicio</th>
              <th rowspan=2>U. de Med.</th>
              <th rowspan=2>Cantidad</th>
              <th rowspan=2>P. de venta</th>
              <th rowspan=2 width="50px">P. de Compra /Costo de Produc.</th>
              <th colspan=2>TOTAL (S/.)</th>
              <th rowspan=2 width="50px">Marg. x Producto</th>
              
            </tr>
            <tr>
              <th>VENTAS</th>
              <th>Costo: Vent./Prod.</th>
            </tr>
          </thead>
          <tbody num="0">
            @foreach($productos as $key => $value)
              <tr id="{{ $value->id }}">
                <td>{{ $value->producto }}</td>
                <td>{{ $value->unidadmedida }}</td>
                <td class="campo_moneda">{{ $value->cantidad }}</td>
                <td class="campo_moneda">{{ $value->precioventa }}</td>
                <td class="campo_moneda">{{ $value->preciocompra }}</td>
                <td class="campo_moneda">{{ $value->subtotalventa }}</td>
                <td class="campo_moneda">{{ $value->subtotalcompra }}</td>
                <td class="campo_moneda">{{ $value->margen }} %</td>
             </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan=5 align="right">TOTAL (S/.)</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_venta : '0.00' }}</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_compra : '0.00' }}</td>
              <td></td>
            </tr>
            <tr>
              <th colspan=5 align="right">Mg. de Venta</th>
              <th>
                <div class="input-group campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->porcentaje_margen : '0.00' }} %</th>
              <td></td>
              <td></td>
            </tr>
          </tfoot>
      </table>
      </div>
      <div class="col">
        <table class="table" width="220px">
          <thead>
            <tr>
              <th colspan=2>CÁLCULO DE VENTAS</th>
            </tr>
            <tr>
              <th>FRECUENCIA</th>
              <th >{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->frecuencia_ventas : 'DIARIO' }}</th>
            </tr>
          </thead>
        </table>
        <table class="table" width="220px">
          <thead>
            <tr>
              <th>N°</th>
              <th>Dias</th>
              <th>Ventas</th>
            </tr>
          </thead>
          <tbody>
            @if(count($dias) > 0)
              @foreach($dias as $value)
                <tr>
                  <td>{{ $value->numero }}</td>
                  <td>{{ $value->dia }}</td>
                  <td class="campo_moneda">{{ $value->valor }}</td>
                </tr>
              @endforeach
            @endif
            <tr total>
              <th colspan="2">Venta Semanal (S/.)</th>
              <td class="campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->venta_total_dias : '0.00' }}</td>
            </tr>
          </tbody>
        </table>
        <table class="table" width="220px">
          <thead>
            <tr>
              <th>N° de Días</th>
              <th >{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->numero_dias : '0' }}</th>
            </tr>
          </thead>
        </table>
        <table class="table" width="220px">
          <thead>
            <tr>
              <th>Venta mensual (S/.)</th>
              <th class="campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->venta_mensual : '0' }}</th>
            </tr>
          </thead>
        </table>
        <table class="table" width="220px">
          <thead>
            <tr>
              <th>N°</th>
              <th>Día/Recabo Datos</th>
              <th >Ventas</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->recabo_dato_numero : '1' }}</td>
              <td>{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->recabo_dato_dia : '' }}</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->recabo_dato_monto : '0.00' }}</td>
            </tr>
          </tbody>
        </table>
        <table class="table" width="220px">
          <thead>
            <tr>
              <th colspan="2">Estado de muestra de DATOS</th>
            </tr>
            <tr>
              <th colspan="2" class="campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->estado_muestra : '0.00' }}</th>
            </tr>
          </thead>
        </table>
        <table class="table" width="220px">
          <thead>
            <tr>
              <th>Mg. De venta al mes (1) (S/.)</th>
              <th class="campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->margen_ventas : '0.00' }}</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
    <div class="row">
      @foreach($subproducto as $value)
        <div class="col">
          <table class="table" width="220px">
            <thead>
              <tr>  
                <th width="55px">Materia prima (en U., Doc. Etc) M. Obra y otros</th>
                <th>Cantidad</th>
                <th>Costo x U., Doc. Etc.</th>
                <th>Total (S/.)</th>
              </tr>
            </thead>
            <tbody>
              @foreach($value->producto as $key => $items)
              <tr>
                <td>{{ $items->producto }}</td>
                <td class="campo_moneda">{{ $items->cantidad }}</td>
                <td class="campo_moneda">{{ $items->costo }}</td>
                <td class="campo_moneda">{{ $items->total }}</td>
              </tr>
              @endforeach

            </tbody>
            <tfoot>
              <tr>
                <td colspan=3>Costo de Materia Prima</td>
                <td class="campo_moneda">{{ $value->costo_materia_prima }}</td>
              </tr>
              <tr>
                <td colspan=3>Costo de mano de obra</td>
                <td class="campo_moneda">{{ $value->costo_mano_obra }}</td>
              </tr>
              <tr>
                <td colspan=3>Otros costos</td>
                <td class="campo_moneda">{{ $value->costo_otros }}</td>
              </tr>
              <tr>
                <td colspan=3>Costo Total (S/.)</td>
                <td class="campo_moneda" costo_total>{{ isset($value->costo_total)?$value->costo_total:'0.00' }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      @endforeach
    </div>
    <span class="badge subtitle">7.2.2 VENTAS EN MAS DE UNA SEMANA (VENTAS CON FRECUENCIA MENSUAL)</span>
    <div class="row"> 
      <div class="col">
        <table class="table">
          <thead>
            <tr>
              <th rowspan=2 width="150px">VENTA MUESTRA: De productos(de mayor rotación) que comercializa, produce o presta servicio</th>
              <th rowspan=2>U. de Med.</th>
              <th rowspan=2>Cantidad</th>
              <th rowspan=2>P. de venta</th>
              <th rowspan=2 width="50px">P. de Compra /Costo de Produc.</th>
              <th colspan=2>TOTAL (S/.)</th>
              <th rowspan=2 width="50px">Marg. x Producto</th>
              
            </tr>
            <tr>
              <th>VENTAS</th>
              <th>Costo: Vent./Prod.</th>
            </tr>
          </thead>
          <tbody num="0">
            @foreach($productos_mensual as $key => $value)
              <tr>
                <td>{{ $value->producto }}</td>
                <td>{{$value->unidadmedida}}</td>
                <td class="campo_moneda">{{ $value->cantidad }}</td>
                <td class="campo_moneda">{{ $value->precioventa }}</td>
                <td class="campo_moneda">{{ $value->preciocompra }}</td>
                <td class="campo_moneda">{{ $value->subtotalventa }}</td>
                <td class="campo_moneda">{{ $value->subtotalcompra }}</td>
                <td class="campo_moneda">{{ $value->margen }}%</td>
             </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan=5 align="right">TOTAL (S/.)</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_venta_mensual : '0.00' }}</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_compra_mensual : '0.00' }}</td>
              <td></td>
            </tr>
            <tr>
              <th colspan=5 align="right">Mg. de Venta</th>
              <th>
                <div class="input-group campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->porcentaje_margen_mensual : '0.00' }} %</th>
              <td></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="col">
        <table class="table" width="220px">
          <thead>
              <tr>
                <th colspan=2>CÁLCULO DE VENTAS</th>
              </tr>
              <tr>
                <th>FRECUENCIA</th>
                <th >MENSUAL</th>
              </tr>
            </thead>
        </table>
        <table class="table" width="220px">
          <thead>
            <tr>
              <th>Semanas</th>
              <th>Ventas</th>
            </tr>
          </thead>
          <tbody>
            @if(count($semanas) > 0)
                @foreach($semanas as $value)
                  <tr>
                    <td>{{ $value->semana }}</td>
                    <td class="campo_moneda">{{ $value->valor }}</td>
                  </tr>
                @endforeach
              @endif

            <tr total>
              <th>Venta Mensual (S/.)</th>
              <td class="campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->venta_total_mensual : '0.00' }}</td>
            </tr>
          </tbody>
        </table>
        <table class="table" width="220px">
          <thead>
            <tr>
              <th colspan="2">Estado de muestra de DATOS</th>
            </tr>
            <tr>
              <th colspan="2">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->estado_muestra_mensual : '' }}</th>
            </tr>
          </thead>
        </table>
        <table class="table"  width="220px">
          <thead>
            <tr>
              <th>Mg. De venta al mes (2) (S/.)</th>
              <th class="campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->margen_ventas_mensual : '0.00' }}</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
    <div class="row">
      @foreach($subproductomensual as $value)
        <div class="col">
          <table class="table" width="220px">
            <thead>
              <tr>  
                <th width="55px">Materia prima (en U., Doc. Etc) M. Obra y otros</th>
                <th>Cantidad</th>
                <th>Costo x U., Doc. Etc.</th>
                <th>Total (S/.)</th>
              </tr>
            </thead>
            <tbody>
              @foreach($value->producto as $key => $items)
              <tr>
                <td>{{ $items->producto }}</td>
                <td class="campo_moneda">{{ $items->cantidad }}</td>
                <td class="campo_moneda">{{ $items->costo }}</td>
                <td class="campo_moneda">{{ $items->total }}</td>
              </tr>
              @endforeach

            </tbody>
            <tfoot>
              <tr>
                <td colspan=3>Costo de Materia Prima</td>
                <td class="campo_moneda" costo_materia_prima>{{ $value->costo_materia_prima }}</td>
              </tr>
              <tr>
                <td colspan=3>Costo de mano de obra</td>
                <td class="campo_moneda" costo_mano_obra>{{ $value->costo_mano_obra }}</td>
              </tr>
              <tr>
                <td colspan=3>Otros costos</td>
                <td class="campo_moneda" costo_otros>{{ $value->costo_otros }}</td>
              </tr>
              <tr>
                <th colspan=3>Costo Total (S/.)</th>
                <th class="campo_moneda" costo_total>{{ isset($value->costo_total)?$value->costo_total:'0.00' }}</th>
              </tr>
            </tfoot>
          </table>
        </div>
      @endforeach  
    </div>
    <span class="badge subtitle">7.3 INVENTARIO Y ACTIVOS FIJOS - NEGOCIO ADICIONAL</span>
    <div class="row">
      <div class="col">
        <table class="table">
          <thead>
            <tr>
              <th width="160px">Inventario de Productos</th>
              <th>Unid. Med.</th>
              <th>Cantidad</th>
              <th>Precio de compra</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            @foreach($inventario as $value)
              <tr>
                <td>{{ $value->nombre }}</td>
                <td>{{ $value->medida }}</td>
                <td class="campo_moneda">{{ $value->cantidad }}</td>
                <td class="campo_moneda">{{ $value->precio }}</td>
                <td class="campo_moneda" >{{ $value->subtotalventa }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td class="color_totales campo_moneda" colspan=4>Inventario total de productos  (S/.) </td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_inventario : '0.00' }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="col">
        <table class="table" width="350px">
          <thead>
            <tr>
              <th>Activos Inmuebles</th>
              <th>Unid. Med.</th>
              <th>Cantidad</th>
              <th>Valor estimado</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            @foreach($inmuebles as $value)
              <tr>
              <td>{{ $value->nombre }}</td>
              <td>{{$value->medida}}</td>
              <td class="campo_moneda">{{ $value->cantidad }}</td>
              <td class="campo_moneda">{{ $value->precio }}</td>
              <td class="campo_moneda" >{{ $value->subtotalventa }}</td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td class="color_totales campo_moneda" colspan=4>Total de activos inmuebles  (S/.) </td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_inmuebles : '0.00' }}</td>
            </tr>
          </tfoot>
        </table>
        <br>
        <table class="table" width="350px">
          <thead>
            <tr>
              <th>Activos Muebles</th>
              <th>Unid. Med.</th>
              <th>Cantidad</th>
              <th>Valor estimado (como usado)</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            @foreach($muebles as $value)
              <tr>
                <td>{{ $value->nombre }}</td>
                <td >{{ $value->medida }}</td>
                <td class="campo_moneda">{{ $value->cantidad }}</td>
                <td class="campo_moneda">{{ $value->precio }}</td>
                <td class="campo_moneda">{{ $value->subtotalventa }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td class="color_totales campo_moneda" colspan=4>Total de activos muebles (S/.) </td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_muebles : '0.00' }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
    <span class="badge">VIII. INGRESO ADICIONAL FIJO:</span>
    <div class="row">
      <div class="col">
        <table class="table">
          <thead>
            <tr>
              <th width="20px">N°</th>
              <th>Especificación (Con boletas de pago debidamente sustentado)</th>
              <th width="100px">Monto neto (S/.)</th>
            </tr>
          </thead>
          <tbody>
            @foreach($adicional_fijo as $value)
              <tr>
                <td>{{ $value->numeracion }}</td>
                <td>{{ $value->descripcion }}</td>
                <td class="campo_moneda">{{ $value->monto }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td class="color_totales campo_moneda" colspan="2"><b>TOTAL</b> (S/.) </td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_ingreso_adicional ? $credito_cuantitativa_ingreso_adicional->total_ingreso_adicional : '0.00' }}</td>
            </tr>
          </tfoot>
        </table>
      </div>  
    </div>
    <div class="row" >
      <div class="col" style="margin-left:215px;margin-top:60px;">
        <div style="width:300px;height:1px;border-bottom:1px solid #000;"></div>
        <p align="center">Asesor(a) de Créditos: {{ Auth::user()->codigo }} <br>Firma y Sello</p>		
      </div>
    </div>
  </main>
</body>
</html>