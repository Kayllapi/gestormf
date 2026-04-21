<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GARANTIAS Y LIMITES</title>
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
    $vinculacion_deudor = $credito_cuantitativa_control_limites ? ( $credito_cuantitativa_control_limites->vinculacion_deudor == "" ? [] : json_decode($credito_cuantitativa_control_limites->vinculacion_deudor) ) : [];
    $entidad_noregulada = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->entidad_noregulada == "" ? [] : json_decode($credito_cuantitativa_deudas->entidad_noregulada) ) : [];
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
            <td class="border-td">{{ $users_prestamo->dni_pareja }}</td>
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
            <td>ASESOR (A):</td>
            <td class="border-td">{{ Auth::user()->nombre }}</td>
          </tr>
        </table>
      </div>
      <div class="col">
        <table>
          <tr>
            <td>FECHA:</td>
            <td class="border-td" width="100px">{{ $credito_cuantitativa_control_limites ? date_format(date_create($credito_cuantitativa_control_limites->fecha),'Y-m-d') :'' }}</td>
          </tr>
          <tr>
            <td>RUC/DNI/CE</td>
            <td class="border-td">{{ $credito->docuementocliente }}</td>
          </tr>
          @if($users_prestamo->dni_pareja!='' or $users_prestamo->nombrecompleto_pareja!='')
          <tr>
            <td>DNI/CE:</td>
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
    <span class="badge">
      @if ($users_prestamo->idfuenteingreso == 1)
          @if($credito->idevaluacion == 1)
            VI. 
          @else
            IX.
          @endif
      @elseif ($users_prestamo->idfuenteingreso == 2)
        VII.
      @endif
      GARANTÍAS Y LÍMITES
    </span>
    <span class="subtitle">
      {{ $users_prestamo->idfuenteingreso == 1 ? ($credito->idevaluacion == 1 ? '6.1' : '9.1') : ($users_prestamo->idfuenteingreso == 2 ? '7.1' : '') }}  GARANTÍAS Y DEUDAS DEL CLIENTE
    </span>
    <div class="row">
        <div class="col">
          <input type="hidden" id="cliente_saldo_vigente_cliente_det" value="{{json_encode($credito_garantias_cliente)}}">
          <input type="hidden" id="cliente_saldo_vigente_aval_det" value="{{json_encode($credito_garantias_aval)}}">
          <table class="table" id="table-garantia-cliente">
            <thead>
              <tr>
                <th>Garantías presentadas por el cliente</th>
                <th>Descripción de garantía en Propuesta</th>
                <th style="width:150px;">Valor de mercado (S/.)</th>
                <th style="width:150px;">Valor comercial (Tasado) (S/.)</th>
                <th style="width:150px;">Valor de realización (tasado) (S/.)</th>
              </tr>
            </thead>
            <tbody>
              @if($view_detalle=='false')
                  @if($credito_cuantitativa_control_limites)
                  <?php
                    $saldo_vigente = json_decode($credito_cuantitativa_control_limites->cliente_saldo_vigente_cliente_det);
                  ?>
                  @foreach($saldo_vigente as $value)
                    <tr>
                        <td>{{ $value->garantias_noprendarias_tipo_garantia_noprendaria }}</td>
                        <td>{{ $value->descripcion }}</td>
                        <td class="campo_moneda">{{ $value->valor_mercado==0?'':$value->valor_mercado }}</td>
                        <td class="campo_moneda">{{ $value->valor_comercial==0?'':$value->valor_comercial }}</td>
                        <td class="campo_moneda">{{ $value->valor_realizacion==0?'':$value->valor_realizacion }}</td>
                    </tr>
                  @endforeach
                  @if(count($saldo_vigente)==0)
                  <tr>
                    <td colspan="8">Sin Garantia</td>
                  </tr>
                  @endif
                @endif
              @else
                  @foreach($credito_garantias_cliente as $value)
                    <tr>
                        <td>{{ $value->garantias_noprendarias_tipo_garantia_noprendaria }}</td>
                        <td>{{ $value->descripcion }}</td>
                        <td class="campo_moneda">{{ $value->valor_mercado==0?'':$value->valor_mercado }}</td>
                        <td class="campo_moneda">{{ $value->valor_comercial==0?'':$value->valor_comercial }}</td>
                        <td class="campo_moneda">{{ $value->valor_realizacion==0?'':$value->valor_realizacion }}</td>
                    </tr>
                  @endforeach
                  @if(count($credito_garantias_cliente)==0)
                  <tr>
                    <td colspan="5">Sin Garantia</td>
                  </tr>
                  @else
                  @endif
              @endif
            </tbody>
          </table>
        </div>
      </div>
    <span class="subtitle">
      {{ $users_prestamo->idfuenteingreso == 1 ? ($credito->idevaluacion == 1 ? '6.1.1' : '9.1.1') : ($users_prestamo->idfuenteingreso == 2 ? '7.1.1' : '') }} SALDO DE DEUDA VIGENTE DEL CLIENTE
    </span>
    <input type="hidden" id="cliente_saldo_vigente_cliente_det" value="{{json_encode($credito_garantias_cliente)}}">
    <input type="hidden" id="cliente_saldo_vigente_aval_det" value="{{json_encode($credito_garantias_aval)}}">
    <div class="row">
        <div class="col">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th colspan="2" style="text-align: center;">PROPIOS</th>
                </tr>
                <tr>
                  <th width="80px">CUENTA</th>
                  <th width="80px">SALDOS (S/.)</th>
                </tr>
              </thead>
              <tbody>
                  <?php
                    $total_saldo_vigente_propio = 0;
                    $total_financiado_deudor = 0;
                  ?>
                  @if($view_detalle=='false')
                      @if($credito_cuantitativa_control_limites)
                      <?php
                        $saldo_vigente = json_decode($credito_cuantitativa_control_limites->credito_saldodeduda_cliente_propio_det);
                      ?>
                      @foreach($saldo_vigente as $value)
                        <tr>
                            <td>{{ $value->cuenta }}</td>
                            <td class="campo_moneda">{{ $value->saldo_vigente }}</td>
                        </tr>
                        <?php $total_saldo_vigente_propio = $total_saldo_vigente_propio+$value->saldo_vigente; ?>
                      @endforeach
                    @endif
                  @else
                      @foreach($credito_saldodeduda_cliente_propio as $value)
                        <tr>
                            <td>{{ $value['cuenta'] }}</td>
                            <td class="campo_moneda">{{ $value['saldo_vigente'] }}</td>
                        </tr>
                        <?php $total_saldo_vigente_propio = $total_saldo_vigente_propio+$value['saldo_vigente']; ?>
                      @endforeach
                  @endif
              </tbody>
              <tfoot>
                <tr>
                  <th>TOTAL</th>
                  <th class="campo_moneda">{{number_format($total_saldo_vigente_propio, 2, '.', '')}}</th>
                </tr>
              </tfoot>
            </table>
        </div>
        <input type="hidden" id="total_saldodeuda_cliente_propio" value="{{number_format($total_saldo_vigente_propio, 2, '.', '')}}">
        @php
            $total_financiado_deudor = $total_financiado_deudor + $total_saldo_vigente_propio;
        @endphp
        <div class="col">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th colspan="2" style="text-align: center;">AVALADO</th>
                </tr>
                <tr>
                  <th width="80px">CUENTA</th>
                  <th width="80px">SALDOS (S/.)</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  $total_saldo_vigente_aval = 0; 
                  $cuentas_aval = [];
                ?>
                @if($view_detalle=='false')
                    @if($credito_cuantitativa_control_limites)
                    <?php
                      $saldo_vigente = json_decode($credito_cuantitativa_control_limites->credito_saldodeduda_cliente_aval_det);
                    ?>
                    @foreach($saldo_vigente as $value)
                      <tr>
                          <td>{{ $value->cuenta }}</td>
                          <td class="campo_moneda">{{ $value->saldo_vigente }}</td>
                      </tr>
                      <?php $cuentas_aval[] = $value->cuenta; ?>
                      <?php $total_saldo_vigente_aval += $value->saldo_vigente; ?>
                    @endforeach
                  @endif
                @else
                    @foreach($credito_saldodeduda_cliente_aval as $value)
                      <tr>
                          <td>{{ $value['cuenta'] }}</td>
                          <td class="campo_moneda">{{ $value['saldo_vigente'] }}</td>
                      </tr>
                      <?php $cuentas_aval[] = $value['cuenta']; ?>
                      <?php $total_saldo_vigente_aval += $value['saldo_vigente']; ?>
                    @endforeach
                @endif
              </tbody>
              <tfoot>
                <tr>
                  <th>TOTAL</th>
                  <th class="campo_moneda">{{number_format($total_saldo_vigente_aval, 2, '.', '')}}</th>
                </tr>
              </tfoot>
            </table>
        </div>
        <input type="hidden" id="total_saldodeuda_cliente_aval" value="{{number_format($total_saldo_vigente_aval, 2, '.', '')}}">
        @php
            $total_financiado_deudor = $total_financiado_deudor + $total_saldo_vigente_aval;
        @endphp
        <input type="hidden" id="total_garantia_cliente" value="{{number_format($total_saldo_vigente_propio+$total_saldo_vigente_aval, 2, '.', '')}}">
    </div>
    <span class="subtitle">
      {{ $users_prestamo->idfuenteingreso == 1 ? ($credito->idevaluacion == 1 ? '6.2' : '9.2') : ($users_prestamo->idfuenteingreso == 2 ? '7.2' : '') }} GARANTÍAS Y DEUDAS DEL GARANTE(AVAL)/FIADOR
    </span>
    @if($users_prestamo_aval!='')
      <div class="row" container-garantias-aval>
        <div class="col">
          <table>
            <tr>
              <td width="90px">Apellidos y Nombres:</td>
              <td class="border-td" width="110px">{{ $credito->nombreavalcredito }}</td>
            </tr>
            @if($users_prestamo_aval->dni_pareja!='' or $users_prestamo_aval->nombrecompleto_pareja!='')
            <tr>
              <td>PAREJA:</td>
              <td class="border-td">{{ $users_prestamo_aval->nombrecompleto_pareja }}</td>
            </tr>
            @endif
          </table>
        </div>
        <div class="col">
          <table>
            <tr>
              <td>RUC/DNI/CE:</td>
              <td class="border-td" width="50px">{{ $credito->documentoaval }}</td>
            </tr>
            @if($users_prestamo_aval->dni_pareja!='' or $users_prestamo_aval->nombrecompleto_pareja!='')
            <tr>
              <td>DNI/CE:</td>
              <td class="border-td">{{ $users_prestamo_aval->dni_pareja }}</td>
            </tr>
            @endif
          </table>
        </div>
        <div class="col">
          <table class="table table-bordered" id="table-vinculo-deudor">
            <thead>
              <tr>
                <th colspan="3" width="350px">N° DE ENTIDADES FINANCIERAS (Se considera deuda interna y Líneas de creditos sin uso)</th>
              </tr>
              <tr>
                <th>Deudores</th>
                <th>Como</th>
                <th>N°</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td rowspan="2">Garante (Aval)/Fiador</td>
                <td>P.Natural</td>
                <td class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cantidad_garante_natural : '0.00' }}</td>
              </tr>
              <tr>
                <td>P.Jurídica</td>
                <td class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cantidad_garante_juridico : '0.00' }}</td>
              </tr>
              <tr>
                <td rowspan="2">Pareja de Garante (Aval)/ fiador</td>
                <td>P.Natural</td>
                <td class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cantidad_pareja_natural : '0.00' }}</td>
              </tr>
              <tr>
                <td>P.Jurídica</td>
                <td class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cantidad_pareja_juridico : '0.00' }}</td>
              </tr>
              <tr>
                <td style="text-align: right;" colspan=2>TOTAL S/.</td>
                <td class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->total_deuda : '0.00' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      @endif
      <div class="row" container-garantias-aval>
        <div class="col-sm-12">
          <table class="table table-bordered" id="table-garantia-aval">
            <thead>
              <tr>
                <th>Garantías presentadas por el aval</th>
                <th>Descripción de garantía en Propuesta</th>
                <th style="width:150px;">Valor de mercado (S/.)</th>
                <th style="width:150px;">Valor comercial (Tasado) (S/.)</th>
                <th style="width:150px;">Valor de realización (tasado) (S/.)</th>
              </tr>
            </thead>
            <tbody>
              @if($view_detalle=='false')
                  @if($credito_cuantitativa_control_limites)
                  <?php
                    $saldo_vigente = json_decode($credito_cuantitativa_control_limites->cliente_saldo_vigente_aval_det);
                  ?>
                  @foreach($saldo_vigente as $value)
                    <tr>
                        <td>{{ $value->garantias_noprendarias_tipo_garantia_noprendaria }}</td>
                        <td>{{ $value->descripcion }}</td>
                        <td class="campo_moneda">{{ $value->valor_mercado==0?'':$value->valor_mercado }}</td>
                        <td class="campo_moneda">{{ $value->valor_comercial==0?'':$value->valor_comercial }}</td>
                        <td class="campo_moneda">{{ $value->valor_realizacion==0?'':$value->valor_realizacion }}</td>
                    </tr>
                  @endforeach
                  @if(count($saldo_vigente)==0)
                  <tr>
                    <td colspan="8">Sin Garantia</td>
                  </tr>
                  @endif
                @endif
              @else
                  @foreach($credito_garantias_aval as $value)
                    <tr>
                        <td>{{ $value->garantias_noprendarias_tipo_garantia_noprendaria }}</td>
                        <td>{{ $value->descripcion }}</td>
                        <td class="campo_moneda">{{ $value->valor_mercado }}</td>
                        <td class="campo_moneda">{{ $value->valor_comercial }}</td>
                        <td class="campo_moneda">{{ $value->valor_realizacion }}</td>
                    </tr>
                  @endforeach
                  @if(count($credito_garantias_aval)==0)
                  <tr>
                    <td colspan="5">Sin Garantia</td>
                  </tr>
                  @else
                  @endif
                @endif
            </tbody>
          </table>
        </div>
      </div>
    <span class="subtitle">
      {{ $users_prestamo->idfuenteingreso == 1 ? ($credito->idevaluacion == 1 ? '6.2.1' : '9.2.1') : ($users_prestamo->idfuenteingreso == 2 ? '7.2.1' : '') }} SALDO DE DEUDA VIGENTE DEL AVAL
    </span>
      <input type="hidden" id="credito_saldodeduda_aval_propio_det" value="{{json_encode($credito_saldodeduda_aval_propio)}}">
      <input type="hidden" id="credito_saldodeduda_aval_aval_det" value="{{json_encode($credito_saldodeduda_aval_aval)}}">
      <div class="row">
        <div class="col">
          <table class="table table-bordered" id="table-garantia-aval-propio">
            <thead>
              <tr>
                <th colspan="2" style="text-align: center;">PROPIOS</th>
              </tr>
              <tr>
                <th width="80px">CUENTA</th>
                <th width="80px">SALDOS (S/.)</th>
              </tr>
            </thead>
            <tbody>
                  <?php $total_saldo_vigente_propio = 0; ?>
                  <?php $total_saldo_vigente_propio_input = 0; ?>
              @if($view_detalle=='false')
                  @if($credito_cuantitativa_control_limites)
                  <?php
                    $saldo_vigente = json_decode($credito_cuantitativa_control_limites->credito_saldodeduda_aval_propio_det);
                  ?>
                  @foreach($saldo_vigente as $value)
                    <tr>
                        <td>{{ $value->cuenta }}</td>
                        <td class="campo_moneda">{{ $value->saldo_vigente }}</td>
                    </tr>
                    <?php $total_saldo_vigente_propio += $value->saldo_vigente; ?>
                    @if(!in_array($value->cuenta, $cuentas_aval))
                        <?php $total_saldo_vigente_propio_input += $value->saldo_vigente; ?>
                    @endif
                  @endforeach
                @endif
              @else
                  @foreach($credito_saldodeduda_aval_propio as $value)
                    <tr>
                        <td>{{ $value['cuenta'] }}</td>
                        <td class="campo_moneda">{{ $value['saldo_vigente'] }}</td>
                    </tr>
                    <?php $total_saldo_vigente_propio += $value['saldo_vigente']; ?>
                    @if(!in_array($value['cuenta'], $cuentas_aval))
                        <?php $total_saldo_vigente_propio_input += $value['saldo_vigente']; ?>
                    @endif
                  @endforeach
              @endif
            </tbody>
            <tfoot>
              <tr>
                <th>TOTAL</th>
                <th class="campo_moneda">{{number_format($total_saldo_vigente_propio, 2, '.', '')}}</th>
              </tr>
            </tfoot>
          </table>
        </div>
          <input type="hidden" id="total_saldodeuda_aval_propio" value="{{number_format($total_saldo_vigente_propio, 2, '.', '')}}">
          <input type="hidden" id="total_saldodeuda_aval_propio_input" value="{{number_format($total_saldo_vigente_propio_input, 2, '.', '')}}">
          @php
            $total_financiado_deudor = $total_financiado_deudor + $total_saldo_vigente_propio_input;
        @endphp
        <div class="col">
          <table class="table table-bordered" id="table-garantia-aval-aval">
            <thead>
              <tr>
                <th colspan="2" style="text-align: center;">AVALADO</th>
              </tr>
              <tr>
                <th width="80px">CUENTA</th>
                <th width="80px">SALDOS (S/.)</th>
              </tr>
            </thead>
            <tbody>
                  <?php $total_saldo_vigente_aval = 0; ?>
              @if($view_detalle=='false')
                  @if($credito_cuantitativa_control_limites)
                  <?php
                    $saldo_vigente = json_decode($credito_cuantitativa_control_limites->credito_saldodeduda_aval_aval_det);
                  ?>
                  @foreach($saldo_vigente as $value)
                    <tr>
                        <td>{{ $value->cuenta }}</td>
                        <td class="campo_moneda">{{ $value->saldo_vigente }}</td>
                    </tr>
                    <?php $total_saldo_vigente_aval = $total_saldo_vigente_aval+$value->saldo_vigente; ?>
                  @endforeach
                @endif
              @else
                  @foreach($credito_saldodeduda_aval_aval as $value)
                    <tr>
                        <td>{{ $value['cuenta'] }}</td>
                        <td class="campo_moneda">{{ $value['saldo_vigente'] }}</td>
                    </tr>
                    <?php $total_saldo_vigente_aval = $total_saldo_vigente_aval+$value['saldo_vigente']; ?>
                  @endforeach
              @endif
            </tbody>
            <tfoot>
              <tr>
                <th>TOTAL</th>
                <th class="campo_moneda">{{number_format($total_saldo_vigente_aval, 2, '.', '')}}</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <input type="hidden" id="total_saldodeuda_aval_aval" value="{{number_format($total_saldo_vigente_aval, 2, '.', '')}}">
      <input type="hidden" id="total_garantia_aval" value="{{number_format($total_saldo_vigente_propio+$total_saldo_vigente_aval, 2, '.', '')}}">        
    <span class="subtitle" style="margin: 0 !important;">
      {{ $users_prestamo->idfuenteingreso == 1 ? ($credito->idevaluacion == 1 ? '6.3' : '9.3') : ($users_prestamo->idfuenteingreso == 2 ? '7.3' : '') }} VINCULACIÓN POR RIESGO ÚNICO
    </span>
    <div class="row">
      <div class="col">
        <span>Deudores Vinculados con los que conforma Riesgo Único (Revisar Reporte, de existir Vinculación registrar) <br>
        <b>REGISTRAR SALDO DE PRÉSTAMO VIGENTE</b></span>
        <table class="table table-bordered" id="table-vinculo-deudor">
          <thead>
            <tr>
              <th rowspan=3>DNI/CE CLIENTE</th>
              <th rowspan=3>APELLIDOS Y NOMBRES</th>
              <th colspan=5>FORMA DE VINCULACIÓN</th>
              <th rowspan=3>{{ $tienda->nombre }} </th>
            </tr>
            <tr>
              <th colspan=2>Por propiedad Directa</th>
              <th colspan=2>Por Propiedad Indirecta</th>
              <th>Gestión</th>
            </tr>
            <tr>
              <th>Pertenece al Cliente (%)</th>
              <th>Pertenece al Vinculado (%)</th>
              <th>Pertenece al Cliente (%)</th>
              <th>Pertenece al Vinculado (%)</th>
              <th>Textual</th>
            </tr>
          </thead>
          <tbody>
            @foreach($vinculacion_deudor as $value)
              <tr >
                <td>{{ $value->codigo }}</td>
                <td>{{ $value->cliente }}</td>
                <td>{{ $value->cliente_propiedad_directa }}</td>
                <td>{{ $value->vinculado_propiedad_directa }}</td>
                <td>{{ $value->cliente_propiedad_indirecta }}</td>
                <td>{{ $value->vinculado_propiedad_indirecta }}</td>
                <td>{{ $value->gestion }}</td>
                <td class="campo_moneda">{{ $value->saldo }}</td>
               </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td class="color_totales campo_moneda" colspan=7>TOTAL S/.</td>
              <td class="color_totales campo_moneda">
                {{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->total_vinculo_deudor : '0.00' }}
                <input type="hidden" value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->total_vinculo_deudor : '0.00' }}" id="total_vinculo_deudor">
                @php
                    $total_financiado_deudor = $total_financiado_deudor + ($credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->total_vinculo_deudor : 0);
                @endphp
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
    <span class="subtitle">
      {{ $users_prestamo->idfuenteingreso == 1 ? ($credito->idevaluacion == 1 ? '6.4' : '9.4') : ($users_prestamo->idfuenteingreso == 2 ? '7.4' : '') }} DETERMINACIÓN DE LíMITES
    </span>
    <div class="row">
      <div class="col">
          @php
              $total_financiado_deudor = $total_financiado_deudor + $credito->monto_solicitado;
              $total_financiado_deudor = $total_financiado_deudor + ($credito_formato_evaluacion ? $credito_formato_evaluacion->saldo_capita_pareja : 0);

              $reporte_institucional = $tienda->capital_agencia;
              $porcentaje_resultado = round(($total_financiado_deudor/$reporte_institucional)*100, 2);
          @endphp
          <table style="width:450px !important;">
            <tr style="display: none;">
              <td>Capital Asignado</td>
              <td width="30px" class="border-td">{{ $tienda->capital_agencia }}</td>
              <td width="130px" class="border-td">{{ configuracion($tienda->id,'capital_asignado')['valor'] }}</td>
            </tr>
            <tr>
              <td colspan="2">Total financiado al Deudor y Deudores vinculados (Incluido propuesta) ( S/.)</td>
              <td class="border-td" width="130px" >
                {{-- {{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->total_financiado_deudor : '0.00' }} --}}
                {{ $total_financiado_deudor }}
                <input type="hidden" id="total_financiado_deudor" value="{{ $total_financiado_deudor }}">
              </td>
            </tr>
          </table>
          <table>
            <tr>
              <td>Resultado (%)</td>
              <td class="border-td" width="30px">
                {{-- <input type="text" class="form-control campo_moneda" style="width:60px;"
                      value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->porcentaje_resultado : '0.00' }}" disabled id="porcentaje_resultado"> --}}
                <input type="hidden" id="reporte_institucional" value="{{ $reporte_institucional }}">
                {{ $porcentaje_resultado }}
              </td>
              <td width="211px">
              <td class="border-td" width="130px" style="text-align:center;background-color: #e5e5e5 !important; color: #000 !important;">
                <b>{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->estado_resultado : '0.00' }}</b>
              </td>
            </tr>
          </table>
          <script>
            determina_resultado();
            function determina_resultado(){
              let credito_solicitado = parseFloat("{{$credito->monto_solicitado}}");
              let total_saldodeuda_cliente_propio = parseFloat($('#total_saldodeuda_cliente_propio').val());
              let total_saldodeuda_cliente_aval = parseFloat($('#total_saldodeuda_cliente_aval').val());
              let total_saldodeuda_aval_propio_input = parseFloat($('#total_saldodeuda_aval_propio_input').val());
              let total_vinculo_deudor = parseFloat($('#total_vinculo_deudor').val());
              let entidad_financiera_pareja = parseFloat("{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->saldo_capita_pareja : '0.00' }}");
              // let total_financiado_deudor = ( credito_solicitado + total_saldodeuda_cliente_propio + total_saldodeuda_cliente_aval + total_saldodeuda_aval_propio_input + total_vinculo_deudor + entidad_financiera_pareja );
              // $('#total_financiado_deudor').html(total_financiado_deudor.toFixed(2))

              let total_financiado_deudor = parseFloat($('#total_financiado_deudor').val());
              
              let reporte_institucional = parseFloat($('#reporte_institucional').val());
              let porcentaje_resultado = (total_financiado_deudor/reporte_institucional) * 100;
              $('#porcentaje_resultado').val(porcentaje_resultado.toFixed(2))
              
              let capital_asignado = parseFloat($('#capital_asignado').val());
              $('#estado_resultado ').val('Suspender Propuesta');
              $('#estado_resultado ').removeClass('bg-success');
              $('#estado_resultado ').addClass('bg-danger');
              
              if(porcentaje_resultado <= capital_asignado ){
                $('#estado_resultado ').val('Continuar Propuesta');
                $('#estado_resultado ').removeClass('bg-danger');
                $('#estado_resultado ').addClass('bg-success');
              }else{
              }
            }
          </script>
      </div>
    </div>
    <div class="row" >
      <div class="col" style="width:100%;">
        <span class="subtitle">
          {{ $users_prestamo->idfuenteingreso == 1 ? ($credito->idevaluacion == 1 ? '6.5' : '9.5') : ($users_prestamo->idfuenteingreso == 2 ? '7.5' : '') }} COMENTARIOS SOBRE LA VINCULACIÓN
        </span>
        <div class="row">
          <textarea style="border:solid 1px #000000;" id="fortalezas_negocio" class="form-control">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->comentarios : '' }}</textarea>
        </div>
      </div>
      <div class="col" style="margin-left:215px;margin-top:30px;">
        <div style="width:300px;height:1px;border-bottom:1px solid #000;"></div>
        <p align="center">Asesor(a) de Créditos: {{ Auth::user()->codigo }} <br>Firma y Sello</p>		
      </div>
    </div>
  </main>
</body>
</html>