<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEUDAS</title>
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
    $entidad_regulada = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->entidad_regulada == "" ? [] : json_decode($credito_cuantitativa_deudas->entidad_regulada) ) : [];
    $linea_credito = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->linea_credito == "" ? [] : json_decode($credito_cuantitativa_deudas->linea_credito) ) : [];
    $entidad_noregulada = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->entidad_noregulada == "" ? [] : json_decode($credito_cuantitativa_deudas->entidad_noregulada) ) : [];
    $resumen = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->resumen == "" ? [] : json_decode($credito_cuantitativa_deudas->resumen) ) : [];
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
            <td>DESCRIPCIÓN DE ACTIVIDAD:</td>
            <td class="border-td">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->descripcion_actividad : '' }}</td>
          </tr>
        </table>
      </div>
      <div class="col">
        <table>
          <tr>
            <td>FECHA:</td>
            <td class="border-td" width="100px">{{ $credito_cuantitativa_deudas?date_format(date_create($credito_cuantitativa_deudas->fecha),'Y-m-d'):'' }}</td>
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
    
    <span class="badge">VI. DETALLE DE DEUDAS FINANCIERAS</span>
    <span class="badge subtitle">6.1 Entidades Reguladas</span>
    <div class="row">
      <div class="col">
        <table class="table">
          <thead>
            <tr>
              <th rowspan=3>TIPO DE CRÉDITO</th>
              <th rowspan=3>ENTIDAD FINANCIERA</th>
              <th rowspan=3>DEUDOR</th>
              <th colspan=4>En moneda de origen (S/., $)</th>
              <th colspan=4>En Soles (S/.)</th>
              <th rowspan=2 colspan=2>DEDUCCIONES / COMPRA DE DEUDA O AMPLIACION (S/.)</th>
            </tr>
            <tr>
              <th rowspan=2>Moneda Soles(1) Dólar(2)</th>
              <th rowspan=2>Saldo Capital</th>
              <th rowspan=2>Plazo Pendiente (meses)</th>
              <th rowspan=2>Cuota</th>
              <th rowspan=2>Saldo Capital</th>
              <th rowspan=2>Cuota </th>
              <th colspan=2>Saldo capital según cronograma</th>
            </tr>
            <tr>
              <th>Corto Plazo</th>
              <th>Largo Plazo</th>
              <th>SALDO CAPITAL</th>
              <th>CUOTA</th>
            </tr>
          </thead>
          <tbody>
            @foreach($entidad_regulada as $value)
              @php
                $nombre_entidad = $value->tipo_entidad ? $tienda->nombre : $value->nombre_entidad ;
              @endphp
              <tr>
                <td tipo_credito>
                  @foreach($tipo_credito_evaluacion as $tipo_credito)
                    @if($tipo_credito->id == $value->id_tipo_credito)
                      {{ $tipo_credito->nombre }}
                    @endif
                  @endforeach
                </td>
                <td>{{ $nombre_entidad }}</td>
                <td>{{ $value->deudor }}</td>
                <td>{{ $value->moneda_origen == "1" ? "SOLES" : "DOLARES" }}</td>
                <td class="campo_moneda">{{ $value->saldo_capital_origen }}</td>
                <td class="campo_moneda">{{ $value->plazo_pendiente_origen }}</td>
                <td class="campo_moneda">{{ $value->cuota_origen }}</td>

                <td class="campo_moneda">{{ $value->saldo_capital }}</td>
                <td class="campo_moneda">{{ $value->cuota }}</td>
                <td class="campo_moneda">{{ $value->corto_plazo }}</td>
                <td class="campo_moneda">{{ $value->largo_plazo }}</td>

                <td class="campo_moneda">{{ $value->saldo_capital_deducciones }}</td>
                <td class="campo_moneda" >{{ $value->cuota_deducciones }}</td>

               </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td class="color_totales" colspan=7 align="right">Sub Total Deuda</td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_saldo_capital : '0.00' }}</td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_cuota : '0.00' }}</td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_corto_plazo : '0.00' }}</td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_largo_plazo : '0.00' }}</td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_saldo_capital_deducciones : '0.00' }}</td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_cuota_deducciones : '0.00' }}</td>

            </tr>
          </tfoot>
        </table>
      </div>
    </div>
    <span class="badge subtitle">Deudas de Líneas de Crédito(tarjetas) No Utilizadas</span>
    <div class="row">
      <div class="col">
        <table class="table table-bordered" id="table-linea-credito">
          <thead>
            <tr>
              <th rowspan=2>TIPO DE CRÉDITO</th>
              <th rowspan=2>ENTIDAD FINANCIERA</th>
              <th rowspan=2>DEUDOR</th>
              <th colspan=3>En moneda de origen (S/., $)</th>
              <th colspan=2>En Soles (S/.)</th>
            </tr>
            <tr>
              <th>Moneda Soles(1) Dólar(2)</th>
              <th>Línea de Crédito</th>
              <th>Cuota (24 meses)</th>

              <th>Línea de Crédito</th>
              <th>Cuota (24 meses) </th>
            </tr>
          </thead>
          <tbody>
            @foreach($linea_credito as $value)
              <tr >
                <td tipo_credito>
                    @foreach($tipo_credito_evaluacion as $tipo_credito)
                      @if($tipo_credito->nombre == $value->tipo_credito)
                        {{ $tipo_credito->nombre }}
                      @endif
                    @endforeach
                </td>
                <td entidad>{{ $value->entidad }}</td>
                <td deudor>{{ $value->deudor }}</td>
                <td>{{ $value->moneda_origen == "1" ? "SOLES" : "DOLARES" }}</td></td>
                <td class="campo_moneda" linea_credito_origen>{{ $value->linea_credito_origen }}"</td>

                <td class="campo_moneda" coutas_origen>{{ $value->coutas_origen }} </td>

                <td class="campo_moneda" linea_credito>{{ $value->linea_credito }} </td>
                <td class="campo_moneda" cuota>{{ $value->cuota }} </td>

               </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td class="color_totales" colspan=6 align="right">Sub Total Deuda</td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_lc_linea_credito : '0.00' }}</td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_lc_cuotas : '0.00' }}</td>
              
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
    <span class="badge subtitle">6.2 Entidades No Reguladas</span>
    <div class="row">
      <div class="col">
        <table class="table">
          <thead>
            <tr>
              <th rowspan=3>TIPO DE CRÉDITO</th>
              <th rowspan=3>ENTIDAD FINANCIERA</th>
              <th rowspan=3>DEUDOR</th>
              <th colspan=4>En moneda de origen (S/., $)</th>
              <th colspan=4>En Soles (S/.)</th>
              <th rowspan=2 colspan=2>DEDUCCIONES / COMPRA DE DEUDA O AMPLIACION (S/.)</th>
            </tr>
            <tr>
              <th rowspan=2>Moneda Soles(1) Dólar(2)</th>
              <th rowspan=2>Saldo Capital</th>
              <th rowspan=2>Plazo Pendiente (meses)</th>
              <th rowspan=2>Cuota</th>
              <th rowspan=2>Saldo Capital</th>
              <th rowspan=2>Cuota </th>
              <th colspan=2>Saldo capital según cronograma</th>
            </tr>
            <tr>
              <th>Corto Plazo</th>
              <th>Largo Plazo</th>
              <th>SALDO CAPITAL</th>
              <th>CUOTA</th>
            </tr>
          </thead>
          <tbody>
            @foreach($entidad_noregulada as $value)
              @php
                $nombre_entidad_noregulada = $value->tipo_entidad ? $tienda->nombre : $value->nombre_entidad ;
              @endphp
              <tr>
                <td tipo_credito>
                  @foreach($tipo_credito_evaluacion as $tipo_credito)
                    @if($tipo_credito->id == $value->id_tipo_credito)
                      {{ $tipo_credito->nombre }}
                    @endif
                  @endforeach
                </td>
                <td>{{ $nombre_entidad_noregulada }}</td>
                <td>{{ $value->deudor }}</td>
                <td>{{ $value->moneda_origen == "1" ? "SOLES" : "DOLARES" }}</td>
                <td class="campo_moneda">{{ $value->saldo_capital_origen }}</td>
                <td class="campo_moneda">{{ $value->plazo_pendiente_origen }}</td>
                <td class="campo_moneda">{{ $value->cuota_origen }}</td>

                <td class="campo_moneda">{{ $value->saldo_capital }}</td>
                <td class="campo_moneda">{{ $value->cuota }}</td>
                <td class="campo_moneda">{{ $value->corto_plazo }}</td>
                <td class="campo_moneda">{{ $value->largo_plazo }}</td>

                <td class="campo_moneda">{{ $value->saldo_capital_deducciones }}</td>
                <td class="campo_moneda" >{{ $value->cuota_deducciones }}</td>

               </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td class="color_totales" colspan=7 align="right">Sub Total Deuda</td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_noregulada_saldo_capital : '0.00' }}</td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_noregulada_cuota : '0.00' }}</td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_noregulada_corto_plazo : '0.00' }}</td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_noregulada_largo_plazo : '0.00' }}</td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_noregulada_saldo_capital_deducciones : '0.00' }}</td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_noregulada_cuota_deducciones : '0.00' }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
    <span class="badge subtitle">RESUMEN:</span>
    <div class="row">
      <div class="col">
        <table class="table" width="735px">
          <thead>
            <tr>
              <th rowspan=3>TIPO DE CRÉDITO CONSOLIDADO</th>
              <th colspan=3>Entidades Reguladas</th>
              <th colspan=3>Entidades No Reguladas</th>

              <th colspan=4>TOTAL</th>
            </tr>
            <tr>
              <th colspan=2>Saldo Capital/Línea</th>

              <th rowspan=2>Cuota</th>
              <th colspan=2>Saldo Capital/Línea</th>

              <th rowspan=2>Cuota</th>
              <th colspan=3>Saldo Capital/Línea</th>
              <th rowspan=2>Cuota</th>
            </tr>
            <tr>
              <th>C. Plazo</th>
              <th>L. Plazo</th>
              <th>C. Plazo</th>
              <th>L. Plazo</th>
              <th>C. Plazo</th>
              <th>L. Plazo</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Créditos comerciales</td>
              <td class="campo_moneda">{{ encontrar_valor('comercial_er_cplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('comercial_er_lplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('comercial_er_couta', $resumen) }}</td>

              <td class="campo_moneda">{{ encontrar_valor('comercial_enr_cplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('comercial_enr_lplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('comercial_enr_couta', $resumen) }}</td>

              <td class="campo_moneda">{{ encontrar_valor('comercial_total_cplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('comercial_total_lplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('comercial_total', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('comercial_total_couta', $resumen) }}</td>
            </tr>
            <tr>
              <td>Créditos MES</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_er_cplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_er_lplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_er_couta', $resumen) }}</td>

              <td class="campo_moneda">{{ encontrar_valor('mes_enr_cplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_enr_lplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_enr_couta', $resumen) }}</td>

              <td class="campo_moneda">{{ encontrar_valor('mes_total_cplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_total_lplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_total', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('mes_total_couta', $resumen) }}</td>
            </tr>
            <tr>
              <td>Créditos de consumo</td>
              <td class="campo_moneda">{{ encontrar_valor('consumo_er_cplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('consumo_er_lplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('consumo_er_couta', $resumen) }}</td>

              <td class="campo_moneda">{{ encontrar_valor('consumo_enr_cplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('consumo_enr_lplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('consumo_enr_couta', $resumen) }}</td>

              <td class="campo_moneda">{{ encontrar_valor('consumo_total_cplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('consumo_total_lplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('consumo_total', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('consumo_total_couta', $resumen) }}</td>
            </tr>
            <tr>
              <td>Créditos hipotecarios para vivienda</td>
              <td class="campo_moneda">{{ encontrar_valor('vivienda_er_cplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('vivienda_er_lplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('vivienda_er_couta', $resumen) }}</td>

              <td class="campo_moneda">{{ encontrar_valor('vivienda_enr_cplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('vivienda_enr_lplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('vivienda_enr_couta', $resumen) }}</td>

              <td class="campo_moneda">{{ encontrar_valor('vivienda_total_cplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('vivienda_total_lplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('vivienda_total', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('vivienda_total_couta', $resumen) }}</td>
            </tr>

          </tbody>
          <tfoot>
            <tr totales>
              <td align="right"><b>TOTAL</b></td>
              <td class="campo_moneda">{{ encontrar_valor('total_er_cplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('total_er_lplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('total_er_couta', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('total_enr_cplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('total_enr_lplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('total_enr_couta', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('total_total_cplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('total_total_lplazo', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('total_resumen', $resumen) }}</td>
              <td class="campo_moneda">{{ encontrar_valor('total_total_couta', $resumen) }}</td>
            </tr>
            <tr>
               <td colspan=11></td>
            </tr>
            <tr>
              <td><b>Líneas de Crédito(tarjetas) No Utilizadas</b></td>
              <td class="campo_moneda" colspan="2">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_resumen_linea_credito : '0.00' }}</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_resumen_cuotas_linea_credito : '0.00' }}</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td class="campo_moneda">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_resumen_cuotas_linea_credito2 : '0.00' }}</td>
            </tr>
          </tfoot>

        </table>
      </div>
    </div>
    <span class="badge subtitle">6.3 Propuesta de Financiamiento</span>
    <div class="row">
      <!--div class="col">
        <table class="table">
          <thead>
            <tr>
              <th width="200px" >SOLICITADO</th>
              <th>MONTO</th>
              <th>FRECUENCIA</th>
              <th>CUOTAS</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td></td>
              <td>{{ $credito->monto_solicitado }}</td>
              <td>{{ $credito->forma_pago_credito_nombre }}</td>
              <td>{{ $credito->cuotas }}</td>


            </tr>
          </tbody>
        </table>
      </div-->
      <div class="col">
        @php
          $idformapagocredito = $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->idforma_pago_credito : $credito->idforma_pago_credito;
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
              <th colspan=10>PROPUESTA</th>
            </tr>
            <tr>
              <th rowspan=2>DESTINO DE CRÉDITO</th>
              <th rowspan=2>Producto</th>
              <th colspan=2>Plazo</th>
              <th rowspan=2>FORMA DE PAGO</th>
              <th rowspan=2>Monto Préstamo (S/.)</th>
              <th rowspan=2>TEM</th>

              <th rowspan=2>Servicios/Otros (S/.)</th>
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
              <td>{{ $credito->cuotas }}</td>
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
        <table class="table">
          <tbody>
            <tr>
              <td width="330px">RIESGOS TOTAL PROYECTADO EN: {{ $tienda->nombre }} S/.</td>
              <td width="70px" class="campo_moneda">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->riesgo_proyectado_empresa : '0.00' }}</td>
            </tr>
            <tr>
              <td width="330px">RIESGO TOTAL PROYECTADO EN: TODO SISTEMA FINANCIERO S/.</td>
              <td width="70px" class="campo_moneda">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->riesgo_proyectado_todos : '0.00' }}</td>
            </tr>
          </tbody>
        </table>
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