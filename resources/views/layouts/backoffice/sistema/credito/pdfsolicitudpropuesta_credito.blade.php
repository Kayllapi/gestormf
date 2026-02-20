<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROPUESTA DE CRÉDITO</title>
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
     .doble-subrayado {
          text-decoration: underline;
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
        
      .campo_numero {
        text-align:right;
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
    $entidad_noregulada = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->entidad_noregulada == "" ? [] : json_decode($credito_cuantitativa_deudas->entidad_noregulada) ) : [];
    $vinculacion_deudor = $credito_cuantitativa_control_limites ? ( $credito_cuantitativa_control_limites->vinculacion_deudor == "" ? [] : json_decode($credito_cuantitativa_control_limites->vinculacion_deudor) ) : [];

    $lista_fenomenos = $credito_propuesta ? ( $credito_propuesta->fenomenos == "" ? [] : json_decode($credito_propuesta->fenomenos) ) : [];
  @endphp
  <main>
    <h4 align="center" style="font-size:13px;margin:0;padding:0;">PROPUESTA DE CRÉDITO</h4>
    <div class="row">
      <div class="col" style="width:366px;">
        <table style="width:100%;">
          <tr>
            <td>AGENCIA/OFICINA:</td>
            <td class="border-td">{{ $tienda->nombreagencia }}</td>
          </tr>
          <tr>
            <td>TIPO DE CRÉDITO:</td>
            <td class="border-td">{{ $credito->forma_credito_nombre }}</td>
          </tr>
          <tr>
            <td>TIPO DE CLIENTE:</td>
            <td class="border-td">{{ $credito->tipo_operacion_credito_nombre }}</td>
          </tr>
          <tr>
            <td>PRODUCTO:</td>
            <td class="border-td">{{ $credito->nombreproductocredito }}</td>
          </tr>
          <tr>
            <td>MODALIDAD:</td>
            <td class="border-td">{{ $credito->modalidad_credito_nombre }}</td>
          </tr>
        </table>
      </div>
      <div class="col" style="width:360px;">
        <table style="width:100%;">
          <tr>
            <td>NRO SOLICITUD:</td>
            <td class="border-td">S{{ str_pad($credito->id, 8, '0', STR_PAD_LEFT)  }}</td>
          </tr>
          <tr>
            <td>CÓD. CLIENTE:</td>
            <td class="border-td">{{ $credito->codigo_cliente }}</td>
          </tr>
          <tr>
            <td>FECHA:</td>
            <td class="border-td">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->fecha : date('Y-m-d') }}</td>
          </tr>
          <tr>
            <td>ASESOR:</td>
            <td class="border-td">{{ $credito->usuario_asesor }}</td>
          </tr>
        </table>
      </div>
    </div>
    <span class="badge">INFORMACIÓN DEL CLIENTE E INGRESO:</span>
    <div class="row">
      <div class="col" style="width:365px;">
        <table style="width:100%;">
          <tr>
            <td>CLIENTE/RAZON SOCIAL:</td>
            <td class="border-td">{{ $credito->nombreclientecredito }}</td>
          </tr>
          <tr>
            <td>DNI/RUC::</td>
            <td class="border-td">{{ $credito->docuementocliente }}</td>
          </tr>
          @if($users_prestamo->dni_pareja!='' or $users_prestamo->nombrecompleto_pareja!='')
          <tr>
            <td>PAREJA:</td>
            <td class="border-td">{{ $users_prestamo->nombrecompleto_pareja  }}</td>
          </tr>
          @endif
          @if($users_prestamo->dni_pareja!='' or $users_prestamo->nombrecompleto_pareja!='')
          <tr>
            <td>DNI:</td>
            <td class="border-td">{{ $users_prestamo->dni_pareja }}</td>
          </tr>
          @endif
          <tr>
            <td>DIRECCIÓN:</td>
            <td class="border-td">{{ $usuario->direccion }}</td>
          </tr>
          <tr>
            <td>CONDICIÓN DE VIVIENDA/LOCAL:</td>
            <td class="border-td">{{ strtoupper($users_prestamo->db_idcondicionviviendalocal) }}</td>
          </tr>
          <tr>
            <td>TIPO DE INGRESO PRINCIPAL:</td>
            <td class="border-td">{{ $users_prestamo->idfuenteingreso == 1 ? 'INDEPENDIENTE' : 'DEPENDIENTE' }}</td>
          </tr>
          @if($users_prestamo->idfuenteingreso == 1)
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
          @endif
        </table>
      </div>
      <div class="col" style="width:361px;">
        <?php
            $suma_saldo = array_sum(array_column(array_filter($entidad_noregulada, function($dato) {
                return $dato->tipo_entidad === true;
            }), 'saldo_capital_origen'));
            $valor_serif = '';
            $saldo_financiado = '0.00';
            if( $suma_saldo > 0 ){
                $valor_serif = $credito->nombreclientecredito;
                $saldo_financiado = $credito->monto_solicitado;
            }
          ?>
          <table class="table" style="width:100%;">
            <thead>
              <tr>
                <th>Vinculación con:</th>
                <th>{{ $tienda->nombre }} (Saldo Financiado)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>{{ $valor_serif }}</td>
                <td style="width:80px;" class="campo_numero">{{ $saldo_financiado }}</td>
              </tr>
              @foreach($vinculacion_deudor as $value)
              <tr>
                <td>{{ $value->cliente }}</td>
                <td style="width:80px;" class="campo_numero">{{ $value->saldo }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
      </div>
    </div>
    <span class="badge">PROPUESTA DE CRÉDITO:</span>
    <div class="row">
      <div class="col"  style="width:726px;">
        <table width="80%">
          <tbody>
            <tr>
              <td>Monto a Financiar:</td>
              <td class="border-td campo_numero">{{ $credito->monto_solicitado }}</td>
              <td>Días de Gracia:</td>
              <td class="border-td campo_numero">{{ $credito->dia_gracia }}</td>
              <td>Cuota de Pago</td>
              <td class="border-td campo_numero">{{ $credito->cuota_pago }}</td>
            </tr>
            <tr>
              <td>TEM(%):</td>
              <td class="border-td campo_numero">{{ $credito->tasa_tem }}</td>
              <td>F. Pago:</td>
              <td class="border-td">{{ $credito->forma_pago_credito_nombre }}</td>
              <td>Plazo:</td>
              <td class="border-td campo_numero">{{ $credito->cuotas }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col" style="width:365px;">
        <span class="badge" style="margin-bottom:5px;">CLIENTE:</span>
        <table class="table table-bordered" id="table-garantia-cliente">
          <thead>
            <tr>
              <th>Garantías</th>
              <th>Descripción de garantía en Propuesta</th>
              <th>Valor de mercado (S/.)</th>
              <th>Valor comercial (Tasado) (S/.)</th>
              <th>Valor de realización(tasado) (S/.)</th>
            </tr>
          </thead>
          <tbody>
            @forelse($credito_garantias_cliente as $key => $value)
              <tr sumar_garantia>
                <td>{{ $value->nombretipogarantia }}</td>
                <td>{{ $value->descripcion_garantia }}</td>
                <td class="campo_numero">{{ $value->valor_mercado_garantia }}</td>
                <td class="campo_numero">{{ $value->valor_comercial_garantia }}</td>
                <td class="campo_numero">{{ $value->valor_realizacion_garantia }}</td>
              </tr>
            @empty
            <tr sumar_garantia>
              <td>Sin Garantia</td>
              <td class="campo_numero">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->saldo_noprendario_cliente : '0.00' }}</td>
              <td class="campo_numero">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->propuesta_monto : '0.00' }}</td>
              <td></td>
              <td></td>
            </tr>
            @endforelse

          </tbody>
        </table>
        <br>
        <table>
          <tbody>
            <tr>
              <td>Clasificación NORMAL en S. Fin. últimos 6 meses:</td>
              <td>Cliente</td>
              <td class="border-td campo_numero" style="width:20px">
                  @foreach($calificacion_cliente as $value)
                    @if( $value->id == ( $credito_propuesta ? $credito_propuesta->idclasificacion_cliente : 0 ) )
                      {{ $value->nombre }}
                    @endif
                  @endforeach
              </td>
              <td>meses</td>
            </tr>
            <tr>
              <td></td>
              <td>Prja./R. Leg.</td>
              <td class="border-td campo_numero" style="width:20px">
                  @foreach($calificacion_cliente as $value)
                    @if( $value->id == ( $credito_propuesta ? $credito_propuesta->idclasificacion_cliente_pareja : 0 ) )
                      {{ $value->nombre }}
                    @endif
                  @endforeach
              </td>
              <td>meses</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col" style="width:361px;">
        <span class="badge" style="margin-bottom:5px;">AVAL:</span>
        <table class="table table-bordered" id="table-garantia-aval">
          <thead>
            <tr>
              <th>Garantías</th>

              <th>Descripción de garantía en Propuesta</th>
              <th>Valor de mercado (S/.)</th>
              <th>Valor comercial (Tasado) (S/.)</th>
              <th>Valor de realización(tasado) (S/.)</th>
            </tr>
          </thead>
          <tbody>           
            @forelse($credito_garantias_aval as $key => $value)
              <tr >
                <td>{{ $value->nombretipogarantia }}</td>
                <td>{{ $value->descripcion_garantia }}</td>

                <td class="campo_numero">{{ $value->valor_mercado_garantia }}</td>
                <td class="campo_numero">{{ $value->valor_comercial_garantia }}</td>
                <td class="campo_numero">{{ $value->valor_realizacion_garantia }}</td>
              </tr>
            @empty
            <tr sumar_garantia>
              <td>Sin Garantia</td>
              <td class="campo_numero">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->saldo_noprendario_aval : '0.00' }}</td>
              <td class="campo_numero">{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->propuesta_monto : '0.00' }}</td>
              <td></td>
              <td></td>
            </tr>
            @endforelse
          </tbody>
        </table>
          <br>
          <table class="table table-bordered" style="width:100%;">
            <tbody>           
                <tr>
                  <th style="width:140px;">A. y N. de Aval(Garante)/Fiador:</th>
                  <td>{{ $credito->nombreavalcredito }}</td>
                  <th style="width:30px;">DNI:</th>
                  <td style="width:50px;">{{ $credito->documentoaval }}</td>
                </tr>    
                @if($users_prestamo_aval!='')
                    @if($users_prestamo_aval->dni_pareja!='' or $users_prestamo_aval->nombrecompleto_pareja!='')
                      <tr>
                        <th>Pareja Aval(Garante)/Fiador:</th>
                        <td>{{ $users_prestamo_aval->nombrecompleto_pareja }}</td>
                        <th>DNI:</th>
                        <td>{{ $users_prestamo_aval->dni_pareja }}</td>
                      </tr>
                    @endif
                @endif
            </tbody>
          </table>
        <table>
          <tbody>
            <tr>
              <td>Clasificación NORMAL en S. Fin. últimos 6 meses:</td>
              <td>Cliente</td>
              <td class="border-td campo_numero" style="width:20px">
                @foreach($calificacion_cliente as $value)
                  @if($value->id == ( $credito_propuesta ? $credito_propuesta->idclasificacion_aval : 0 ))
                    {{ $value->nombre }}
                  @endif
                @endforeach
              </td>
              <td>meses</td>
            </tr>
            <tr>
              <td></td>
              <td>Prja.</td>
              <td class="border-td campo_numero" style="width:20px">
                  @foreach($calificacion_cliente as $value)
                    @if($value->id == ( $credito_propuesta ? $credito_propuesta->idclasificacion_aval_pareja : 0 ))
                      {{ $value->nombre }}
                    @endif
                  @endforeach
              </td>
              <td>meses</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    @if($credito->idmodalidad_credito==2)
    <span class="badge">DESTINO DEL CRÉDITO:</span>
    <div class="row">
      <div class="col">
              <?php
                  $monto_compra_deuda_det = json_decode($credito_propuesta->monto_compra_deuda_det,true);
              ?>
        <table>
          <tbody>
            <tr>
              <td>Destino:</td>
              <td style="width:200px" class="border-td" colspan="2">{{ $credito->tipo_destino_credito_nombre}}</td>
              <td style="width:80px" class="border-td campo_numero">{{ $credito->monto_solicitado }}</td>
              <td style="width:50px">Detalle:</td>
              <td class="border-td">
                @if($credito->idevaluacion == 1)
                {{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->detalle_destino_prestamo : '' }}
                @else
                {{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->detalle_destino_prestamo : '' }}
                @endif
              </td>
            </tr>
            <tr>
              <td rowspan="{{count($monto_compra_deuda_det)}}"></td>
              <td rowspan="{{count($monto_compra_deuda_det)}}">Ampliación de deuda</td>
              @if($monto_compra_deuda_det!='')
                  @foreach($monto_compra_deuda_det as $value_det)
                      <?php
                          $credito_det = DB::table('credito')
                              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                              ->where('credito.id',$value_det['idcredito'])
                              ->select(
                                  'credito.*',
                                  'credito_prendatario.nombre as nombreproductocredito',
                                  'credito_prendatario.modalidad as modalidadproductocredito',
                              )
                              ->first();
                      ?>
                        <td style="width:250px" class="border-td">
                          C{{ str_pad($credito_det->cuenta, 8, "0", STR_PAD_LEFT) }} - {{$credito_det->nombreproductocredito}}
                        </td>
                        <td style="width:100px" class="border-td">{{ $credito_propuesta ? $credito_propuesta->monto_compra_deuda : '0.00' }}</td>  
                  @endforeach
              @endif
              <td rowspan="{{count($monto_compra_deuda_det)}}">Detalle:</td>
              <td rowspan="{{count($monto_compra_deuda_det)}}" class="border-td">{{ $credito_propuesta ? $credito_propuesta->detalle_monto_compra_deuda : '' }}</td>
            </tr>
            <tr>
              <td></td>
              <td colspan="2">Neto (S/)</td>
              <td class="border-td campo_numero">{{ $credito_propuesta ? (number_format($credito->monto_solicitado - $credito_propuesta->monto_compra_deuda, 2, '.', '')) : '0.00' }}</td>
              <td colspan="2" rowspan="{{count($monto_compra_deuda_det)}}"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    @endif
     @if($users_prestamo->idfuenteingreso == 1)
    <span class="badge">SOBRE EL NEGOCIO:</span>
    <div class="row">
      <div class="col">
        <table>
          <tbody>
            <tr>
              <td>SECTOR ECONÓMICO:</td>
              <td class="border-td">
                
                @if($credito->idevaluacion == 1)
                {{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->nombretipo_giro_economico : '' }}
                @else
                {{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->nombretipo_giro_economico : '' }}
                @endif
              </td>
              <td width="100px"></td>
              <td>Forma de ejercicio:</td>
              <td class="border-td">{{ $users_prestamo->db_idforma_ac_economica }}</td>
              <td>
                  @if($credito->idevaluacion == 2)
                Otros Ingresos: 
              <?php
                $negocio_otros_ingresos = '';
                if($credito_evaluacion_cualitativa!=''){
                if ($credito_evaluacion_cualitativa->saladario_fijo === "SI") {
                  $negocio_otros_ingresos = "SI";
                } else if ($credito_evaluacion_cualitativa->alquiler_local === "SI") {
                  $negocio_otros_ingresos = "SI";
                } else if ($credito_evaluacion_cualitativa->normas_municipales === "SI") {
                  $negocio_otros_ingresos = "Si";
                } else if ($credito_evaluacion_cualitativa->pago_impuestos_dia === "SI") {
                  $negocio_otros_ingresos = "SI";
                } else {
                  $negocio_otros_ingresos = "NO";
                }
                }

                $negocio_cantidad_ventas_altas = 0;
                $negocio_cantidad_ventas_bajas = 0;

                if(count($evaluacion_meses)>0){
                foreach ($evaluacion_meses[1] as $key => $value) {
                  if($key != 'Mes' ){
                    if(floatval($value->value) > 100) {
                      $negocio_cantidad_ventas_altas++;
                    }
                    if(floatval($value->value) < 100) {
                      $negocio_cantidad_ventas_bajas++;
                    }
                  }
                }
                }

              ?>
                @endif
              </td>
                @if($credito->idevaluacion == 2)
              <td class="border-td">
                {{ $negocio_otros_ingresos }}
              </td>
                @else
                <td></td>
                @endif
            </tr>
            <tr>
              <td>Instalaciones:</td>
              <td class="border-td">
                  @if($users_prestamo->casanegocio=='SI')
                  Casa/Negocio
                  @else
                  {{ $users_prestamo->db_idlocalnegocio_ac_economica }}
                  @endif
              </td>
              <td></td>
              <td>Número de trabajadores:</td>
              <td class="border-td campo_numero">
                @if($credito->idevaluacion == 1)
                {{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->nro_trabajador_completo + $credito_evaluacion_resumida->nro_trabajador_parcal : '0' }}
                @else
                {{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->nro_trabajador_completo + $credito_evaluacion_cualitativa->nro_trabajador_parcal : '0' }}
                @endif
              </td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>Experiencia como empresario:</td>
              <td class="border-td campo_numero">
                @if($credito->idevaluacion == 1)
                {{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->experiencia_microempresa : '0' }}
                @else
                {{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->experiencia_microempresa : '0' }}
                @endif
              </td>
              <td>meses</td>
              <td>
                @if($credito->idevaluacion == 2)
                Meses de ventas altas:
                @endif
              </td>
                @if($credito->idevaluacion == 2)
              <td class="border-td campo_numero">
                {{ $negocio_cantidad_ventas_altas }}
              </td>
                @else
              <td></td>
                @endif
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>Tiempo en el mismo local:</td>
              <td class="border-td campo_numero">
                @if($credito->idevaluacion == 1)
                {{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->tiempo_mismo_local : '0' }}
                @else
                {{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->tiempo_mismo_local : '0' }}
                @endif
              </td>
              <td>meses</td>

              <td>
                @if($credito->idevaluacion == 2)
                Meses de venta bajas:
                @endif
              </td>
                @if($credito->idevaluacion == 2)
                <td class="border-td campo_numero">
                {{ $negocio_cantidad_ventas_bajas }}</td>
                @else
                <td></td>
                @endif
              </td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>Descripción de la actividad:</td>
              <td colspan=6 class="border-td">
                @if($credito->idevaluacion == 1)
                {{ $credito_evaluacion_resumida ? $credito_evaluacion_resumida->descripcion_actividad : '0' }}
                @else
                {{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->descripcion_actividad : '0' }}
                @endif
              </td>
            </tr>
          </tbody>
        </table>
        <table class="table" width="600px" id="table-fenomeno">
          <thead>
            <tr>
              <th width="200px">Afecto a Fenomeno coyuntural</th>
              <th>Descripción</th>
            </tr>
          </thead>
          <tbody>
            @foreach($lista_fenomenos as $value)
              <tr>
                <td>
                    @foreach($fenomenos as $fen_value)
                      @if($fen_value->id==$value->fenomeno)
                        {{ $fen_value->nombre }}
                      @endif
                    @endforeach
                </td>
                <td>{{ $value->descripcion }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif
            <?php

              $tem_propuesta = $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->propuesta_tem : 0;
              $rango_menor = configuracion($tienda->id,'rango_menor')['valor'];
              $rango_tope = configuracion($tienda->id,'rango_tope')['valor'];
              $tope_capital_asignado = configuracion($tienda->id,'capital_asignado')['valor'];
              $relacion_couta_ingreso = configuracion($tienda->id,'relacion_couta_ingreso')['valor'];
              $relacion_cuota_venta = configuracion($tienda->id,'relacion_cuota_venta')['valor'];
              // Fila 01
              $rentabilidad_negocio = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_negocio : 0;  
              $rentabilidad_negocio_res = "Ver ROS";
              if( $rentabilidad_negocio > 0 && $rentabilidad_negocio > $tem_propuesta){
                $rentabilidad_negocio_res = "Rentabilidad de Capital de Trabajo Adecuada";
              }
              $rentabilidad_negocio_res_coment = "--";
              if( $rentabilidad_negocio > 0){
                $rentabilidad_negocio_res_coment = $rentabilidad_negocio;
              }
              // Fila 02
              $rentabilidad_ventas = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_ventas : 0;
              if ($rentabilidad_ventas > $tem_propuesta) {
                  $rentabilidad_ventas_res = "Rendimiento de Ventas Eficientes";
              }elseif($rentabilidad_ventas < $tem_propuesta) {
                  $rentabilidad_ventas_res = "Rendimiento de Ventas Moderados";
              }else{
                  $rentabilidad_ventas_res = 0;
              }

              $rentabilidad_ventas_res_coment = 0;
              if( $rentabilidad_ventas > 0 ){
                $rentabilidad_ventas_res_coment = $rentabilidad_ventas;
              }
              // Fila 03
              $rentabilidad_unidad_familiar = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_unidadfamiliar : 0;
              $rentabilidad_unidad_familiar_res = '';
              if($rentabilidad_unidad_familiar > 1) {
                $rentabilidad_unidad_familiar_res = "Riesgo Normal - Continuar Propuesta";
              }elseif($rentabilidad_unidad_familiar <= 1) {
                $rentabilidad_unidad_familiar_res = "Alto Riesgo - Suspender Propuesta";
              }
              if($rentabilidad_unidad_familiar > 1) {
                $rentabilidad_unidad_familiar_res_coment = "Sus ingresos totales del cliente PUEDE cubrir sus gastos familiares en más de 1 vez";
              }else{
                $rentabilidad_unidad_familiar_res_coment = "Los ingresos totales del cliente NO son capaces de cubrir sus gastos familiares";
              }
              // Fila 04
              $rentabilidad_patrimonial = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_patrimonial : 0;
              $rentabilidad_activos = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_activos : 0;
              if($rentabilidad_patrimonial > $rentabilidad_activos && $rentabilidad_patrimonial > $credito->tasa_tem) {
                $rentabilidad_patrimonial_res = "Adecuada Rentabilidad de los fondos propios invertidos en el negociopor INTERESA ENDEUDARSE";
              }else{
                $rentabilidad_patrimonial_res = "Débil Rentabilidad de los fondos propios invertidos en el negocio NO INTERESA ENDEUDARSE";
              }
              // Fila 05
              if ($rentabilidad_activos < $rentabilidad_patrimonial && $rentabilidad_activos > $credito->tasa_tem) {
                $rentabilidad_activos_res = "Rentabilidad Adeuado de las inversiones en el negocio, INTERESA ENDEUDARSE";
              } else {
                $rentabilidad_activos_res = "Rentabilidad Débil de las inversiones en el negocio, NO INTERESA ENDEUDARSE";
              }
              // SOLVENCIA
              // Fila 06
              $solvencia_liquidez = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_liquidez : 0;
              if($solvencia_liquidez > 0 && $solvencia_liquidez >= 1) {
                $solvencia_liquidez_res = "Solvente (Capacidad) puede asumir sus deudas a corto plazo - Continuar Propuesta";
              }else{
                $solvencia_liquidez_res = "Insolvencia Técnica - Observar Propuesta";
              }
              // Fila 07
              $solvencia_liquidez_acida = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_liquidez_acida : 0;
              if ($solvencia_liquidez_acida >= 1) {
                  $solvencia_liquidez_acida_res = "CUENTA con Efectivo Inmediato para pagar deudas";
              } else {
                  $solvencia_liquidez_acida_res = "NO cuenta con Efectivo inmediato para Pagar deudas";
              }
              // Fila 08
              $solvencia_endeudamiento_propuesta = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_endeudamiento_propuesta : 0;
              if ($solvencia_endeudamiento_propuesta <= 0.85) {
                  $solvencia_endeudamiento_propuesta_res = "TIENE autonomía financiera - Proceder Propuesta";
              } else {
                  $solvencia_endeudamiento_propuesta_res = "NO tiene autonomía financiera - Observar Propuesta";
              }
              if ($solvencia_endeudamiento_propuesta <= 0.85) {
                  $solvencia_endeudamiento_propuesta_res_coment = "TIENE respaldo patrimonial para asumir la deuda propuesta";
              } else {
                  $solvencia_endeudamiento_propuesta_res_coment = "NO tiene respaldo patrimonial para asumir la deuda propuesta";
              }
              // Fila 09
                if($users_prestamo->idfuenteingreso == 2){
                    $solvencia_cuota_total = $credito_formato_evaluacion ? $credito_formato_evaluacion->resultado_cuota_excedente : 0;
                }else{
                    $solvencia_cuota_total = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->excedente_propuesta_con_deduccion : 0 ;
                }
                
              if ($solvencia_cuota_total <= $rango_menor && $solvencia_cuota_total>=0) {
                  $solvencia_cuota_total_res = "No evidencia Sobreendeudamiento - Existe Cobertura";
              } else {
                  $solvencia_cuota_total_res = "Evidencia Sobreendeudamiento - No Existe Cobertura";
              }
              // Fila 10
              $solvencia_capital_trabajo_neto = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_prestamo : 0;
              if ($solvencia_capital_trabajo_neto <= 85) {
                  $solvencia_capital_trabajo_neto_res = "Financiamiento dentro de lo Permisible";
              } else {
                  $solvencia_capital_trabajo_neto_res = "Financiamiento fuera de límite permisible";
              }
             if ($solvencia_capital_trabajo_neto <= 85) {
                  $solvencia_capital_trabajo_neto_res_coment = "Evidencia que se está financiando menos del capital de trabajo neto que tiene";
              } else {
                  $solvencia_capital_trabajo_neto_res_coment = "Muestra que se está financiando más que el capital de trabajo neto del cliente";
              }
              // Fila 11
              $solvencia_capital_trabajo = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_capital : 0 ; 
              // GESTON 
              // Fila 12
              $gestion_rotacion_inventario = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_rotacion_inventario : 0;
              if ($gestion_rotacion_inventario <= 7) {
                  $gestion_rotacion_inventario_res = "Negocio de Rotación MUY ALTA de ventas";
              } elseif ($gestion_rotacion_inventario <= 45) {
                  $gestion_rotacion_inventario_res = "Negocio de Rotación ALTA de ventas";
              } elseif ($gestion_rotacion_inventario <= 90) {
                  $gestion_rotacion_inventario_res = "Negocio de Rotación USUAL de ventas";
              } else {
                  $gestion_rotacion_inventario_res = "Negocio de Rotación LENTA de ventas";
              }
              if ($gestion_rotacion_inventario > 0) {
                  $gestion_rotacion_inventario_res_coment = "El inventario del cliente se renueva cada "; // Puedes reemplazar el punto con un espacio si es necesario
              } else {
                  $gestion_rotacion_inventario_res_coment = "";
              }
              // Fila 13
              $gestion_promedio_cobranza = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_promedio_cobranza : 0;
              if ($gestion_promedio_cobranza <= 45) {
                  $gestion_promedio_cobranza_res = "Gestión ADECUADA de sus ventas a crédito";
              } else {
                  $gestion_promedio_cobranza_res = "Gestión INADECUADA de sus ventas a crédito";
              }
              if ($gestion_promedio_cobranza > 0) {
                  $gestion_promedio_cobranza_res_coment = "El cliente realiza el cobro de sus ventas a crédito cada "; // Puedes reemplazar el punto con un espacio si es necesario
              } else {
                  $gestion_promedio_cobranza_res_coment = "";
              }
              // Fila 14
              $gestion_promedio_pago = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_primedio_pago : 0;
              if ($gestion_promedio_pago > $gestion_promedio_cobranza) {
                  $gestion_promedio_pago_res = "EXISTE calce a sus obligaciones con su proveedor";
                  $gestion_promedio_pago_res_coment = "Pago a proveedores usualmente puntual";
              } else {
                  $gestion_promedio_pago_res = "NO existe calce a sus obligaciones con su proveedor";
                  $gestion_promedio_pago_res_coment = "Pago a proveedores con retrasos";
              }
              // LIMITES
              // Fila 15
              $limites_financiamiento_vru = $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->porcentaje_resultado : 0;
              $limites_financiamiento_vru_res = ($limites_financiamiento_vru <= $tope_capital_asignado) ? "Dentro del límite permisible - Proceder propuesta" : "Fuera de límite permisible - Suspender propuesta";

              ############## RESULTADO EVALUACION RESUMIDA #############
              // SOLVENCIA 
              // Fila 01
              $res_solvencia_relacion_cuota = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->indicador_solvencia_cuotas : 0;
              if ($res_solvencia_relacion_cuota > 0 && $res_solvencia_relacion_cuota <= $rango_tope) {
                  $res_solvencia_relacion_cuota_res = "No evidencia Sobreendeudamiento EXISTE COBERTURA";
              } elseif ($res_solvencia_relacion_cuota > $rango_tope) {
                  $res_solvencia_relacion_cuota_res = "Evidencia Sobreendeudamiento NO EXISTE COBERTURA";
              } elseif ($res_solvencia_relacion_cuota <= 0) {
                  $res_solvencia_relacion_cuota_res = "Sobreendeudamiento NO TIENE EXCEDENTE";
              } else {
                  $res_solvencia_relacion_cuota_res = 0;
              }
              // OTROS RATIOS
              // Fila 02
              $res_ratios_cuota_ingreso_mensual = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_mensual : 0;
              if ($res_ratios_cuota_ingreso_mensual <= $relacion_couta_ingreso) {
                  $res_ratios_cuota_ingreso_mensual_res = "Dentro del rango establecido";
              } elseif ($res_ratios_cuota_ingreso_mensual > $relacion_couta_ingreso) {
                  $res_ratios_cuota_ingreso_mensual_res = "Fuera del rango establecido";
              }
              if ($res_ratios_cuota_ingreso_mensual <= $relacion_couta_ingreso) {
                  $res_ratios_cuota_ingreso_mensual_res_coment = "VIABLE con segunda opción, para cumplir cuotas de pago a muy corto plazo";
              } elseif ($res_ratios_cuota_ingreso_mensual > $relacion_couta_ingreso) {
                  $res_ratios_cuota_ingreso_mensual_res_coment = "NO VIABLE con segunda opción, para cumplir cuotas de pago a muy corto plazo";
              } else {
                  $res_ratios_cuota_ingreso_mensual_res_coment = 0;
              }
              // Fila 03
              $res_ratios_venta_cuota_diaria = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_venta_diaria : 0;
              if ($res_ratios_venta_cuota_diaria <= $relacion_cuota_venta) {
                $res_ratios_venta_cuota_diaria_res = "Dentro del rango establecido";
              } elseif ($res_ratios_venta_cuota_diaria > $relacion_cuota_venta) {
                $res_ratios_venta_cuota_diaria_res = "Fuera del rango establecido";
              } else {
                $res_ratios_venta_cuota_diaria_res = 0;
              }

              if ($res_ratios_venta_cuota_diaria <= $relacion_cuota_venta) {
                  $res_ratios_venta_cuota_diaria_res_coment = "VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
              } elseif ($res_ratios_venta_cuota_diaria > $relacion_cuota_venta) {
                  $res_ratios_venta_cuota_diaria_res_coment = "NO VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
              }
              // Fila 04
              $res_ratios_venta_cuota_semanal = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_venta_semanal : 0;
              if ($res_ratios_venta_cuota_semanal <= $relacion_cuota_venta) {
                  $res_ratios_venta_cuota_semanal_res = "Dentro del rango establecido";
              } elseif ($res_ratios_venta_cuota_semanal > $relacion_cuota_venta) {
                  $res_ratios_venta_cuota_semanal_res = "Fuera del rango establecido";
              } else {
                  $res_ratios_venta_cuota_semanal_res = 0;
              }

              if ($res_ratios_venta_cuota_semanal <= $relacion_cuota_venta) {
                  $res_ratios_venta_cuota_semanal_res_coment = "VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
              } elseif ($res_ratios_venta_cuota_semanal > $relacion_cuota_venta) {
                  $res_ratios_venta_cuota_semanal_res_coment = "NO VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
              }
              // Fila 05
              $res_ratios_venta_cuota_quincenal = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_venta_quincenal : 0;
              if ($res_ratios_venta_cuota_quincenal <= $relacion_cuota_venta) {
                  $res_ratios_venta_cuota_quincenal_res = "Dentro del rango establecido";
              } elseif ($res_ratios_venta_cuota_quincenal > $relacion_cuota_venta) {
                  $res_ratios_venta_cuota_quincenal_res = "Fuera del rango establecido";
              } else {
                  $res_ratios_venta_cuota_quincenal_res = 0;
              }
              if ($res_ratios_venta_cuota_quincenal <= $relacion_cuota_venta) {
                  $res_ratios_venta_cuota_quincenal_res_coment = "VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
              } elseif ($res_ratios_venta_cuota_quincenal > $relacion_cuota_venta) {
                  $res_ratios_venta_cuota_quincenal_res_coment = "NO VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
              }
              // Fila 06
              $res_ratios_venta_cuota_mensual = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_venta_mensual : 0;
              if ($res_ratios_venta_cuota_mensual <= $relacion_cuota_venta) {
                  $res_ratios_venta_cuota_mensual_res = "Dentro del rango establecido";
              } elseif ($res_ratios_venta_cuota_mensual > $relacion_cuota_venta) {
                  $res_ratios_venta_cuota_mensual_res = "Fuera del rango establecido";
              } else {
                  $res_ratios_venta_cuota_mensual_res = 0;
              }
              if ($res_ratios_venta_cuota_mensual <= $relacion_cuota_venta) {
                  $res_ratios_venta_cuota_mensual_res_coment = "VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
              } elseif ($res_ratios_venta_cuota_mensual > $relacion_cuota_venta) {
                  $res_ratios_venta_cuota_mensual_res_coment = "NO VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
              }
              // Fila 07
            ?>
    @if( $credito->idevaluacion == 2 or $users_prestamo->idfuenteingreso == 2)
    
    <span class="badge">RESULTADOS DE EVALUACIÓN:</span>
    <div class="row">
      <div class="col" style="width:100%;">
        <table class="table" style="width:100%;">
          <thead>
            <tr>
              <th width="150px">Indicadores</th>
              <th width="10px"></th>
              <th width="30px">Ratios</th>
              <th>Resultado</th>
              <th colspan=2>Comentarios</th>
              <th width="140px">Exigencias/Particularidades</th>
            </tr>
          </thead>
          <tbody>
            @if($users_prestamo->idfuenteingreso == 1)
            <tr>
              <th colspan=7><b>RENTABILIDAD</b></th>
            </tr>
            <tr>
              <td>Rentabilidad del negocio</td>
              <td>%</td>
              <td class="campo_numero">{{ $rentabilidad_negocio }}</td>
              <td><div class="cuadro-input">{{ $rentabilidad_negocio_res }}</div></td>
              <td colspan="2"><div class="cuadro-input">Por cada sol invertido gana {{ $rentabilidad_negocio_res_coment }}%</div></td>
              <td><div class="cuadro-input">Giros de alta rotación o servicios puede ser (-), usar ROS</div></td>
            </tr>
            <tr>
              <td>Rentabilidad de las ventas (ROS)</td>
              <td>%</td>
              <td class="campo_numero">{{ $rentabilidad_ventas }}</td>
              <td><div class="cuadro-input">{{ $rentabilidad_ventas_res }}</div></td>
              <td colspan="2"><div class="cuadro-input">Su ganancia mensual por su venta es {{ $rentabilidad_ventas_res_coment }}%</div></td>
              <td><div class="cuadro-input">Se sugiere ROS>TEM</div></td>
            </tr>
            <tr>
              <td>Rentabilidad de la unidad familiar</td>
              <td>Veces</td>
              <td class="campo_numero">{{ $rentabilidad_unidad_familiar }}</td>
              <td><div class="cuadro-input">{{ $rentabilidad_unidad_familiar_res }}</div></td>
              <td colspan=2><div class="cuadro-input">{{ $rentabilidad_unidad_familiar_res_coment }}</div></td>
              <td<div class="cuadro-input">Se espera >1</div></td>
            </tr>
            <tr>
              <td>Rentabilidad patrimonial (ROE)</td>
              <td>%</td>
              <td class="campo_numero">{{ $rentabilidad_patrimonial }}</td>
              <td><div class="cuadro-input">{{ $rentabilidad_patrimonial_res }}</div></td>
              <td colspan=2>{{ $credito_propuesta ? $credito_propuesta->rentabilidad_patrimonial_res_coment : '' }}</td>
              <td>SI:  ROA>TEM <span style="font-family: DejaVu Sans, sans-serif;">&rarr;</span> ROE>ROA. Endeudamiento tiene efecto apalancamiento (+) o amplificador</td>
            </tr>
            <tr>
              <td>Rentabilidad de los activos (ROA)</td>
              <td>%</td>
              <td class="campo_numero">{{ $rentabilidad_activos }}</td>
              <td>{{ $rentabilidad_activos_res }}</td>
              <td colspan=2>{{ $credito_propuesta ? $credito_propuesta->rentabilidad_activos_res_coment : '' }}</td>
              <td>Si: ROA&lt;TEM <span style="font-family: DejaVu Sans, sans-serif;">&rarr;</span> ROE&lt;ROA. Endeudamiento tiene efecto apalancamiento (–) o reductor</td>
            </tr>
            @endif
            <tr>
              <th colspan=7><b>SOLVENCIA</b></th>
            </tr>
            @if($users_prestamo->idfuenteingreso == 1)
            <tr>
              <td>Liquidez</td>
              <td>Veces</td>
              <td class="campo_numero">{{ $solvencia_liquidez }}</td>
              <td><div class="cuadro-input">{{ $solvencia_liquidez_res }}</div></td>
              <td colspan="2"><div class="cuadro-input">Por cada sol de obligaciones cuenta con S/ {{ $solvencia_liquidez }} para pagar en el corto plazo </div></td>
              <td><div class="cuadro-input">Se exije >1</div></td>
            </tr>
            <tr>
              <td>Liquidez Ácida</td>
              <td>Veces</td>
              <td class="campo_numero">{{ $solvencia_liquidez_acida }}</td>
              <td><div class="cuadro-input">{{ $solvencia_liquidez_acida_res }}</div></td>
              <td colspan="2"><div class="cuadro-input">Por cada sol de obligaciones cuenta de inmediato S/ {{ $solvencia_liquidez_acida }} para pagar en muy corto plazo</div></td>
              <td><div class="cuadro-input">Óptimo >1</div></td>
            </tr>
            <tr>
              <td>Endeudamiento Patrim. con propuesta</td>
              <td>Veces</td>
              <td class="campo_numero">{{ $solvencia_endeudamiento_propuesta }}</td>
              <td><div class="cuadro-input">{{ $solvencia_endeudamiento_propuesta_res }}</div></td>
              <td colspan=2><div class="cuadro-input">{{ $solvencia_endeudamiento_propuesta_res_coment }}</div></td>
              <td><div class="cuadro-input">Usualmente &lt;1 (maximo considerado 0.85). Particularidad; en los giros de alta rotación ó servicios puede ser >1</div></td>
            </tr>
            @endif
            <tr>
              <td class="doble-subrayado">Cuota total/excedente total</td>
              <td class="doble-subrayado">%</td>
              <td class="doble-subrayado campo_numero">{{ $solvencia_cuota_total }}</td>
              <td class="doble-subrayado">{{ $solvencia_cuota_total_res }}</td>
              <td class="doble-subrayado" colspan=2>{{ $credito_propuesta ? $credito_propuesta->solvencia_cuota_total_res_coment : '' }}</td>
              <td class="doble-subrayado">Se exije &lt; 100% conforme política</td>
            </tr>
            @if($users_prestamo->idfuenteingreso == 1)
            <tr>
              <td>Préstamo / capital de trabajo Neto</td>
              <td>%</td>
              <td class="campo_numero">{{ $solvencia_capital_trabajo_neto }}</td>
              <td><div class="cuadro-input">{{ $solvencia_capital_trabajo_neto_res }}</div></td>
              <td colspan=2><div class="cuadro-input">{{ $solvencia_capital_trabajo_neto_res_coment }}</div></td>
              <td><div class="cuadro-input">Usualmente:&lt;100% (maximo considerado 85%). Particularidad: en giros de alta rotación ó servicios puede ser >100% o (-)</div></td>
            </tr>
            <tr>
              <td>Capital de trabajo</td>
              <td>S/</td>
              <td class="campo_numero">{{ $solvencia_capital_trabajo }}</td>
              <td></td>
              <td colspan=2>{{ $credito_propuesta ? $credito_propuesta->solvencia_capital_trabajo_res_coment : '' }}</td>
              <td><div class="cuadro-input">Giros de alta rotación ó servicios puede ser (-)</div></td>
            </tr>
            <tr>
              <th colspan=7><b>GESTIÓN</b></th>
            </tr>
            <tr>
              <td>Plazo prom.rotación de invent.</td>
              <td>Días</td>
              <td class="campo_numero">{{ $gestion_rotacion_inventario }}</td>
              <td><div class="cuadro-input">{{ $gestion_rotacion_inventario_res }}</div></td>
              <td colspan="2"><div class="cuadro-input">{{ $gestion_rotacion_inventario_res_coment }} {{ $gestion_rotacion_inventario }} días</div></td>
              <td><div class="cuadro-input"></td>
            </tr>
            <tr>
              <td>Plazo promedio de cobranza</td>
              <td>Días</td>
              <td class="campo_numero">{{ $gestion_promedio_cobranza }}</td>
              <td><div class="cuadro-input">{{ $gestion_promedio_cobranza_res }}</div></td>
              <td colspan="2"><div class="cuadro-input">{{ $gestion_promedio_cobranza_res_coment }} {{ $gestion_promedio_cobranza }} días</div></td>
              <td><div class="cuadro-input">Plazo máximo adecuado de 30 a 45 días </td>
            </tr>
            <tr>
              <td>Plazo promedio de pago</td>
              <td>Días</td>
              <td class="campo_numero">{{ $gestion_promedio_pago }}</td>
              <td><div class="cuadro-input">{{ $gestion_promedio_pago_res }}</div></td>
              <td colspan=2><div class="cuadro-input">{{ $gestion_promedio_pago_res_coment }}</div></td>
              <td><div class="cuadro-input">CALCE: Plazo Prom.Cobranza &lt; Plazo Prom. Pago</div></td>
            </tr>
            @endif
            <tr>
              <th colspan=7><b>LIMITES</b></th>
            </tr>
            <tr>
              <td>Financiamiento por VRU</td>
              <td>%</td>
              <td class="campo_numero">{{ $limites_financiamiento_vru }}</td>
              <td><div class="cuadro-input">{{ $limites_financiamiento_vru_res }}</div></td>
              <td colspan=2>{{ $credito_propuesta ? $credito_propuesta->limites_financiamiento_vru_res_coment : '' }}</td>
              <td></td>
            </tr>
            <tr>
              <td class="doble-subrayado">N° de entidades (Cliente y Pareja)</td>
              <td class="doble-subrayado">N°</td>
              <?php
                  $limites_numero_entidades = 0;
                  if($_GET['tipo'] == 'evaluacion_completa'){
                      $limites_numero_entidades = $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->total_deuda : 0;
                  }else{
                        if($users_prestamo->idfuenteingreso == 2){
                            $limites_numero_entidades = $credito_formato_evaluacion ? $credito_formato_evaluacion->entidad_financiera_total : 0;
                        }else{
                            $limites_numero_entidades = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->total_deuda : 0;
                        }
                  }
                  $entidad_maxima = configuracion($tienda->id,'entidades_maxima')['valor'];
                  $limites_numero_entidades_res = '';
                  if ($limites_numero_entidades > $entidad_maxima) {
                    $limites_numero_entidades_res = "Se sugiere no proceder o coverturar la propuesta";
                  } else if ($limites_numero_entidades <= $entidad_maxima) {
                    $limites_numero_entidades_res = "Proceder con propuesta";
                  }
              ?>
              <td class="doble-subrayado campo_numero">{{ $limites_numero_entidades }}</td>
              <td class="doble-subrayado">{{ $limites_numero_entidades_res }}</td>
              <td class="doble-subrayado"colspan=2>{{ $credito_propuesta ? $credito_propuesta->limites_numero_entidades_res_coment : '' }}</td>
              <td class="doble-subrayado"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    @endif 
  
    @if( $credito->idevaluacion == 1)
      
    <span class="badge">RESULTADOS DE EVALUACIÓN RESUMIDA:</span>
    <div class="row">
      <div class="col">
        <table class="table">
          <thead>
            <tr>
              <th width="150px">Indicadores</th>
              <th width="10px"></th>
              <th width="30px">Ratios</th>
              <th>Resultado</th>
              <th colspan=2>Comentarios</th>
              <th width="140px">Exigencias/Particularidades</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th colspan=7 style="text-align:left;"><b>SOLVENCIA</b></th>
            </tr>
            <tr>
              <td class="doble-subrayado">Relación cuota/excedente</td>
              <td class="doble-subrayado">%</td>
              <td class="doble-subrayado campo_numero">{{ $res_solvencia_relacion_cuota }}</td>
              <td class="doble-subrayado">{{ $res_solvencia_relacion_cuota_res }}</td>
              <td class="doble-subrayado" colspan=2>{{ $credito_propuesta ? $credito_propuesta->res_solvencia_relacion_cuota_coment : '' }}</td>
              <td class="doble-subrayado">Se exije &lt; 100% conforme política</td>
            </tr>
            <tr>
              <th colspan=7 style="text-align:left;"><b>OTROS RATIOS</b></th>
            </tr>
            <tr>
              <td>R. Cuota Mensual/Ingreso Mensual</td>
              <td>%</td>
              <td class="campo_numero">{{ $res_ratios_cuota_ingreso_mensual }}</td>
              <td><div class="cuadro-input">{{ $res_ratios_cuota_ingreso_mensual_res }}</div></td>
              <td colspan=2><div class="cuadro-input">{{ $res_ratios_cuota_ingreso_mensual_res_coment }}</div></td>
              <td><div class="cuadro-input">Debe ser &lt;= que {{ configuracion($tienda->id,'relacion_couta_ingreso')['valor'] }}%. Considerar N° de entidades para determinar</div></td>
            </tr>
            @if($res_ratios_venta_cuota_diaria > 0)
            <tr>
              <td>R. Cuota diaria/ Venta diaria</td>
              <td>%</td>
              <td class="campo_numero">{{ $res_ratios_venta_cuota_diaria }}</td>
              <td><div class="cuadro-input">{{ $res_ratios_venta_cuota_diaria_res }}</div></td>
              <td colspan=2><div class="cuadro-input">{{ $res_ratios_venta_cuota_diaria_res_coment }}</div></td>
              <td><div class="cuadro-input">Debe ser &lt;= que {{ configuracion($tienda->id,'relacion_cuota_venta')['valor'] }}%</div></td>
            </tr>
            @endif
            @if($res_ratios_venta_cuota_semanal > 0)
            <tr>
              <td>R. Cuota Semanal/ Venta semanal </td>
              <td>%</td>
              <td class="campo_numero">{{ $res_ratios_venta_cuota_semanal }}</td>
              <td><div class="cuadro-input">{{ $res_ratios_venta_cuota_semanal_res }}</div></td>
              <td colspan=2><div class="cuadro-input">{{ $res_ratios_venta_cuota_semanal_res_coment }}</div></td>
              <td><div class="cuadro-input">Debe ser &lt;= que {{ configuracion($tienda->id,'relacion_cuota_venta')['valor'] }}%</div></td>
            </tr>
            @endif
            @if($res_ratios_venta_cuota_quincenal > 0)
            <tr>
              <td>R. Cuota Quincenal/ Vta. quincenal </td>
              <td>%</td>
              <td class="campo_numero">{{ $res_ratios_venta_cuota_quincenal }}</td>
              <td><div class="cuadro-input">{{ $res_ratios_venta_cuota_quincenal_res }}</div></td>
              <td colspan=2><div class="cuadro-input">{{ $res_ratios_venta_cuota_quincenal_res_coment }}</div></td>
              <td><div class="cuadro-input">Debe ser &lt;= que {{ configuracion($tienda->id,'relacion_cuota_venta')['valor'] }}%</div></td>
            </tr>
            @endif
            @if($res_ratios_venta_cuota_mensual > 0)
            <tr>
              <td>R. Cuota Mensual/Venta Mensual ( Frec. Mensual)</td>
              <td>%</td>
              <td class="campo_numero">{{ $res_ratios_venta_cuota_mensual }}</td>
              <td><div class="cuadro-input">{{ $res_ratios_venta_cuota_mensual_res }}</div></td>
              <td colspan=2><div class="cuadro-input">{{ $res_ratios_venta_cuota_mensual_res_coment }}</div></td>
              <td><div class="cuadro-input">Debe ser &lt;= que {{ configuracion($tienda->id,'relacion_cuota_venta')['valor'] }}%. Considerar determinante el N° de entidades</div></td>
            </tr>
            @endif
            <tr>
              <?php
                  $limites_numero_entidades = 0;
                  if($_GET['tipo'] == 'evaluacion_completa'){
                      $limites_numero_entidades = $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->total_deuda : 0;
                  }else{
                      $limites_numero_entidades = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->total_deuda : 0;
                  }
                  $entidad_maxima = configuracion($tienda->id,'entidades_maxima')['valor'];
                  $limites_numero_entidades_res = '';
                  if ($limites_numero_entidades > $entidad_maxima) {
                    $limites_numero_entidades_res = "Se sugiere no proceder o coverturar la propuesta";
                  } else if ($limites_numero_entidades <= $entidad_maxima) {
                    $limites_numero_entidades_res = "Proceder con propuesta";
                  }
              ?>
              <td class="doble-subrayado">N° de entidades (Cliente y Pareja)</td>
              <td class="doble-subrayado">N°</td>
              <td class="doble-subrayado campo_numero">{{ $limites_numero_entidades }}</td>
              <td class="doble-subrayado">{{ $limites_numero_entidades_res }}</td>
              <td class="doble-subrayado" colspan=2>{{ $credito_propuesta ? $credito_propuesta->limites_numero_entidades_res_coment : '' }}</td>
              <td class="doble-subrayado"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    @endif 
    <div class="row" >
      <div class="col" style="margin-left:215px;margin-top:60px;">
        <div style="width:300px;height:1px;border-bottom:1px solid #ccc;"></div>
        <p align="center">Asesor(a) de Créditos: {{ Auth::user()->codigo }} <br>Firma y Sello</p>		
      </div>
    </div> 
    
  </main>
</body>
</html>