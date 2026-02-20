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
    <div style="float:left;font-size:15px;">{{ $tienda->nombre }}</div> {{ Auth::user()->usuario }} | {{ date('d-m-Y H:iA') }}
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
          <!--tr>
            <td>DESCRIPCIÓN DE ACTIVIDAD:</td>
            <td class="border-td">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->descripcion_actividad : '' }}</td>
          </tr-->
        </table>
      </div>
      <div class="col">
        <table>
          <tr>
            <td>FECHA:</td>
            <td class="border-td" width="100px">{{ $credito_cuantitativa_control_limites ? date_format(date_create($credito_cuantitativa_control_limites->fecha),'Y-m-d') :'' }}</td>
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
    
    <span class="badge">
          @if($credito->idevaluacion == 1)
          VI. 
          @else
          @if($users_prestamo->idfuenteingreso == 2)
          VII.
          @else
          IX.
          @endif
          @endif
          GARANTIAS Y LIMITES</span>
    <span class="badge subtitle">{{ $credito->idevaluacion == 1 ? '6.1':($users_prestamo->idfuenteingreso == 2?'7.1':'9.1') }} GARANTÍAS DEL CLIENTE</span>
    <div class="row">
      <div class="col">
        <table class="table">
          <thead>
            <tr>
              <th rowspan="2">Garantías presentadas por el cliente</th>
              <th colspan="2">Saldo de Prést. vigente (S/)</th>
              <th rowspan="2">Propuesta</th>
              <th rowspan="2">Descripción de garantía en Propuesta</th>
              <th rowspan="2">Valor de mercado (S/.)</th>
              <th rowspan="2">Valor comercial (Tasado) (S/.)</th>
              <th rowspan="2">Valor de realización(tasado) (S/.)</th>
            </tr>
            <tr>
              <th style="width:40px;">Propio</th>
              <th style="width:40px;">Avalado</th>
            </tr>
          </thead>
          <tbody>
              <?php
                $garantia_cliente = 0;
                $key=0;
              ?>
            @foreach($credito_garantias_cliente as $value)
              <?php
                  $td_propio = '';
                  $td_propuesta = '';
                  if($key == 0){
                    $cliente_saldo_vigente_cliente = $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cliente_saldo_vigente_cliente : '0.00';
                    $monto_propuesta = $credito->monto_solicitado;
                    $cantidad_filas = count($credito_garantias_cliente);
                    $td_propio = '<td rowspan="'.$cantidad_filas.'" class="campo_moneda">'.$cliente_saldo_vigente_cliente.'</td>
                                    <td rowspan="'.$cantidad_filas.'"></td>';
                    $td_propuesta = '<td rowspan="'.$cantidad_filas.'" class="campo_moneda">'.$monto_propuesta.'</td>';
                  }
                  $key++;
                    
                  $garantia_cliente = $monto_propuesta+$cliente_saldo_vigente_cliente;
              ?>
              <tr>
                <td>{{ $value->nombretipogarantia }}</td>
                  <?php echo $td_propio; ?>
                  <?php echo $td_propuesta; ?>
                <td>{{ $value->descripcion_garantia }}</td>
                <td class="campo_moneda">{{ $value->valor_mercado_garantia }}</td>
                <td class="campo_moneda">{{ $value->valor_comercial_garantia }}</td>
                <td class="campo_moneda">{{ $value->valor_realizacion_garantia }}</td>
              </tr>
            @endforeach
            @foreach($credito_garantias_aval as $value)
              <?php
                  $td_propio = '';
                  $td_propuesta = '';
                  if($key == 0){
                    $cliente_saldo_vigente_aval = $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cliente_saldo_vigente_aval : '0.00';
                    $monto_propuesta = $credito->monto_solicitado;
                    $cantidad_filas = count($credito_garantias_aval);
                    $td_propio = '<td rowspan="'.$cantidad_filas.'" class="campo_moneda">'.$cliente_saldo_vigente_aval.'</td>
                                    <td rowspan="'.$cantidad_filas.'"></td>';
                    $td_propuesta = '<td rowspan="'.$cantidad_filas.'" class="campo_moneda">'.$monto_propuesta.'</td>';
                  }
                  $key++;
                    
                  $garantia_cliente = $monto_propuesta+$cliente_saldo_vigente_aval;
              ?>
              <tr>
                <td>{{ $value->nombretipogarantia }}</td>
                  <?php echo $td_propio; ?>
                  <?php echo $td_propuesta; ?>
                <td>{{ $value->descripcion_garantia }}</td>
                <td class="campo_moneda">{{ $value->valor_mercado_garantia }}</td>
                <td class="campo_moneda">{{ $value->valor_comercial_garantia }}</td>
                <td class="campo_moneda">{{ $value->valor_realizacion_garantia }}</td>
              </tr>
            @endforeach
            @if($key==0)
            <tr>
                <td>Sin Garantia</td>
                <td class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->saldo_noprendario_cliente : '0.00' }}</td>
                <td class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->propuesta_noprendario_cliente : '0.00' }}</td>
                <td class="color_totales" colspan=5></td>
            </tr>
            @endif
            <tr>
              <td class="campo_moneda">TOTAL S/.</td>
              <td colspan=3 class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->total_garantia_cliente : $garantia_cliente }}</td>
              <td colspan=4></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <br>
    <span class="badge subtitle">{{ $credito->idevaluacion == 1 ? '6.2':($users_prestamo->idfuenteingreso == 2?'7.2':'9.2') }} GARANTIAS DEL GARANTE(AVAL)/FIADOR</span>
    
    @if($users_prestamo_aval!='')
    <div class="row">
      <div class="col">
        <table class="">
          <tbody>
            <tr>
              <td width="55px">Ape. y Nom:</td>
              <td width="195px">{{ $credito->nombreavalcredito }}</td>
              <td>DNI:</td>
              <td width="50px">{{ $credito->documentoaval }}</td>
            </tr>
            @if($users_prestamo_aval->dni_pareja!='' or $users_prestamo_aval->nombrecompleto_pareja!='')
            <tr>
              <td>PAREJA:</td>
              <td>{{ $users_prestamo_aval->nombrecompleto_pareja }}</td>
              <td>DNI:</td>
              <td>{{ $users_prestamo_aval->dni_pareja }}</td>
            </tr>
            @endif
          </tbody>
        </table>
      </div>
      <div class="col">
        <span>N° DE ENTIDADES FINANCIERAS (Se considera deuda interna y Líneas de creditos sin uso)</span>
        <table class="table" width="390px">
          <thead>
            <tr>
              <th>Deudores</th>
              <th>Como</th>
              <th style="text-align: center;">N°</th>
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
              <td rowspan="2">Pareja de Garante/ fiador</td>
              <td>P.Natural</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cantidad_pareja_natural : '0.00' }}</td>
            </tr>
            <tr>
              <td>P.Jurídica</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->cantidad_pareja_juridico : '0.00' }}</td>
            </tr>
            <tr>
              <td class="color_totales campo_moneda" style="text-align: right;" colspan=2>TOTAL S/.</td>
              <td class="color_totales campo_moneda" >{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->total_deuda : '0.00' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th rowspan="2">Garantías presentadas por el Aval</th>
              <th colspan="2">Saldo de Prést. vigente (S/)</th>
              <th rowspan="2">Propuesta</th>
              <th rowspan="2">Descripción de garantía en Propuesta</th>
              <th rowspan="2">Valor de mercado (S/.)</th>
              <th rowspan="2">Valor comercial (Tasado) (S/.)</th>
              <th rowspan="2">Valor de realización(tasado) (S/.)</th>
            </tr>
            <tr>
              <th style="width:40px;">Propio</th>
              <th style="width:40px;">Avalado</th>
            </tr>
          </thead>
          <tbody>
            @php
              $garantia_cliente_aval = 0;
            @endphp             
            @forelse($credito_garantias_aval as $value)
              <?php
                $monto_anterior_aval = DB::table('credito_garantia')
                                            ->join('credito','credito.id','credito_garantia.idcredito')
                                            ->where('credito_garantia.idgarantias',0)
                                            ->where('credito_garantia.idcliente',$value->idcliente)
                                            ->where('credito_garantia.idgarantias_noprendarias',$value->idcliente)
                                            ->where('credito_garantia.idcredito','<>',$value->idcredito)
                                            ->orderby('credito_garantia.id','desc')
                                            ->sum('credito.monto_solicitado');
              ?>
              @php
                $propuesta_aval = $credito->monto_solicitado;
                $garantia_cliente_aval = $monto_anterior_aval + $propuesta_aval;
              @endphp
              <tr>
                <td>{{ $value->nombretipogarantia }}</td>
                <td class="campo_moneda">{{ $monto_anterior }}</td>
                <td class="campo_moneda">{{ $monto_anterior }}</td>
                <td class="campo_moneda">{{ $propuesta_aval }}</td>
                <td>{{ $value->descripcion_garantia }}</td>

                <td class="campo_moneda">{{ $value->valor_mercado_garantia }}</td>
                <td class="campo_moneda">{{ $value->valor_comercial_garantia }}</td>
                <td class="campo_moneda">{{ $value->valor_realizacion_garantia }}</td>
              </tr>
            @empty
            <tr>
                <td>Sin Garantia</td>
                <td class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->saldo_noprendario_aval : '0.00' }}</td>
                <td class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->propuesta_noprendario_aval : '0.00' }}</td>
                <td class="color_totales" colspan=5></td>
              </tr>
            @endforelse
            <tr>
              <td class="color_totales campo_moneda">TOTAL S/.</td>
              <td class="color_totales campo_moneda" colspan=3>{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->total_garantia_aval : $garantia_cliente_aval }}</td>
              <td class="color_totales" colspan=4></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <br>
    @endif
    <span class="badge subtitle">{{ $credito->idevaluacion == 1 ? '6.3':($users_prestamo->idfuenteingreso == 2?'7.3':'9.3') }} VINCULACIÓN POR RIESGO ÚNICO</span>

    <div class="row">
      <div class="col">
        <span>Deudores Vinculados con los que conforma Riesgo Único (Revisar Reporte, de existir Vinculación registrar) <br>
      <b>REGISTRAR SALDO DE PRÉSTAMO VIGENTE</b></span>
        <table class="table table-bordered" id="table-vinculo-deudor">
          <thead>
            <tr>
              <th rowspan=3>CODIGO DE CLIENTE</th>
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
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->total_vinculo_deudor : '0.00' }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
    <span class="badge subtitle">{{ $credito->idevaluacion == 1 ? '6.4':($users_prestamo->idfuenteingreso == 2?'7.4':'9.4') }} DETERMINACIÓN DE LIMITES</span>
    <div class="row">
      <div class="col">
        <table class="table" style="width:500px !important;">
          <tr>
            <td colspan="2">Total financiado al Deudor y Deudores vinculados (Incluido propuesta) ( S/.)</td>
            <td class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->total_financiado_deudor : '0.00' }}</td>
          </tr>
          <tr>
            <td style="background-color: #e5e5e5 !important;
                color: #000 !important;">Resultado (%)</td>
            <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->porcentaje_resultado : '0.00' }}</td>
            <td style="background-color: #e5e5e5 !important;
                color: #000 !important;" class="campo_moneda">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->estado_resultado : '0.00' }}</td>
          </tr>
        </table>
      </div>
    </div>
    
    <div class="row" >
      <div class="col" style="width:100%;">
        <span class="badge subtitle">{{ $credito->idevaluacion == 1 ? '6.5':($users_prestamo->idfuenteingreso == 2?'7.5':'9.5') }} COMENTARIOS SOBRE LA VINCULACIÓN</span>
        <div class="row">
          <textarea id="fortalezas_negocio" class="form-control">{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->comentarios : '' }}</textarea>
        </div>
      </div>
      <div class="col" style="margin-left:215px;margin-top:60px;">
        <div style="width:300px;height:1px;border-bottom:1px solid #ccc;"></div>
        <p align="center">Asesor(a) de Créditos: {{ Auth::user()->codigo }} <br>Firma y Sello</p>		
      </div>
    </div>

  </main>
</body>
</html>