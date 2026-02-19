<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESTADO DE CUENTA</title>
    <style>
      *{
        font-family:helvetica;
        font-size:12px;
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
      .datafooter {
        position: absolute;
        bottom: 10px;
        text-align: right;
        right: 0.7cm;
      }
     </style>
</head>
<body>
  <header>
    <div style="float:left;font-size:18px;">{{ $tienda->nombre }} | {{ $tienda->nombreagencia }}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
  </header>
  <main>
    <div class="container">
      <h4 align="center">ESTADO DE CUENTA</h4>
      <b>CLIENTE:</b>
      <table style="width:100%; border-top: 1px solid #000;border-bottom: 2px solid #000;border-left:0px solid #000;border-right:0px solid #000;">
        <tr>
          <td><b>APELLIDOS Y NOMBRES</b> </td>
          <td><b>:</b> {{ $credito->nombreclientecredito }}</td>
          <td><b>RUC/DNI/CE</b> </td>
          <td><b>:</b> {{ $credito->docuementocliente }}</td>
        </tr>
      </table>
      <table style="width:100%;">
        <tr>
          <td style="width:140px;"><b>CUENTA</b></td>
          <td><b>:</b> C{{ str_pad($credito->cuenta, 8, "0", STR_PAD_LEFT) }}</td>
          <td style="width:130px;"><b>TEM</b></td>
          <td><b>:</b> {{ $credito->tasa_tem }}%</td>
        </tr>
        <tr>
          <td><b>PRÉSTAMO S/.</b> </td>
          <td><b>:</b> {{ $credito->monto_solicitado }}</td>
          <td><b>TCEM</b></td>
          <td><b>:</b> {{ $credito->tasa_tem }}%</td>
        </tr>
        <tr>
          <td><b>F. DE PAGO/CUOTAS</b> </td>
          <td><b>:</b> {{ $credito->forma_pago_credito_nombre }} ({{ $credito->cuotas }} Cuotas)</td>
          <td><b>TIP</b> </td>
          <td><b>:</b> {{ $credito->tasa_tip }}%</td>
        </tr>
        <tr>
          <td><b>PRODUCTO</b> </td>
          <td><b>:</b> {{ $credito->nombreproductocredito }}</td>
          <td><b>ASESOR/EJEC.</b> </td>
          <td><b>:</b> {{ $asesor->nombre }}</td>
        </tr>
        <tr>
          <td><b>MODALIDAD DE CRED.</b> </td>
          <td><b>:</b> {{ $credito->modalidad_credito_nombre }}</td>
          <td><b>F. DE DESEMBOLSO</b> </td>
          <td><b>:</b> {{ date_format(date_create($credito->fecha_desembolso),'d-m-Y h:i:s A') }}</td>
        </tr>
        <?php
        $cronograma = select_cronograma(
              $tienda->id,
              $credito->id,
              $credito->idforma_credito,
              $credito->modalidad_calculo,
              $credito->cuotas,
          );
            
              $clasificacion = '';
            
              if($cronograma['ultimo_atraso']<=8){
                  $clasificacion = 'NORMAL';
              }
              elseif($cronograma['ultimo_atraso']>8 && $cronograma['ultimo_atraso']<=30){
                  $clasificacion = 'CPP';
              }
              elseif($cronograma['ultimo_atraso']>30 && $cronograma['ultimo_atraso']<=60){
                  $clasificacion = 'DIFICIENTE';
              }
              elseif($cronograma['ultimo_atraso']>60 && $cronograma['ultimo_atraso']<=120){
                  $clasificacion = 'DUDOSO';
              }
              elseif($cronograma['ultimo_atraso']>120){
                  $clasificacion = 'PÉRDIDA';
              }
        ?>
        <tr>
          <td><b>CLASIFICACIÓN</b> </td>
          <td><b>:</b> {{ $clasificacion }}</td>
          <td></td>
          <td></td>
        </tr>
      </table>
      <br>
      <table style="width:100%;">
        <tr>
          <td style="width:80px;"><b>DIRECCIÓN</b></td>
          <td colspan="2"><b>:</b> {{ $credito->direccionclientecredito }}, {{ $credito->clienteubigeonombre }}</td>
          <td><b>TELÉFONOS:</b> 
            
    @php 
      $telefono_cliente = $users_prestamo ? ( is_null($users_prestamo->telefono_cliente) ? [] : json_decode($users_prestamo->telefono_cliente) ) : [];
      $telefono_pareja = $users_prestamo ? ( is_null($users_prestamo->telefono_pareja) ? [] : json_decode($users_prestamo->telefono_pareja) ) : [];
      $referencia_cliente = $users_prestamo ? ( is_null($users_prestamo->referencia_cliente) ? [] : json_decode($users_prestamo->referencia_cliente) ) : [];
    @endphp
  
    @foreach($telefono_cliente as $value)
       {{$value->valor}} &nbsp;
    @endforeach
          </td>
        </tr>
    @foreach($referencia_cliente as $value)
        <tr>
          <td><b>REF. TELEF.</b></td>
          <td><b>:</b> {{ $value->vinculo }}</td>
          <td>{{ $value->referencia }}</td>
          <td><b>TELÉFONOS:</b> {{ $value->celular }}</td>
        </tr>
    @endforeach
      </table>
      <br>
      <table style="width:100%; border: 1px solid #000;border-left:0px solid #000;border-right:0px solid #000;border-top:0px solid #000;">
        <tr>
          <td><b>PAREJA:</b> {{ $users_prestamo ? $users_prestamo->ap_paterno_pareja : '' }} {{ $users_prestamo ? $users_prestamo->ap_materno_pareja : '' }}, {{ $users_prestamo ? $users_prestamo->nombres_pareja : '' }}</td>
          <td><b>DNI/CE:</b> {{ $users_prestamo ? $users_prestamo->dni_pareja : '' }}</td>
          <td><b>TELÉFONOS:</b> 
            
    @foreach($telefono_pareja as $value)
        {{$value->valor}}  &nbsp;
    @endforeach
            
          </td>
        </tr>
      </table>
      
        @if($aval!='')
      <br>
      <b>GARANTE(AVAL)/FIADOR:</b>
      <table style="width:100%; border-top: 1px solid #000;border-bottom: 2px solid #000;border-left:0px solid #000;border-right:0px solid #000;">
        <tr>
          <td><b>APELLIDOS Y NOMBRES:</b> {{ $aval->nombrecompleto }}</td>
          <td><b>DNI/CE:</b> {{ $aval->identificacion }}</td>
        </tr>
      </table>
      <br>
      <table style="width:100%;">
        <tr >
          <td colspan="2"><b>DIRECCIÓN:</b> {{ $aval->direccion }}, {{ $aval->clienteubigeonombre }}</td>
          <td><b>TELÉFONOS:</b> 
            
    @php 
      $telefono_cliente_aval = $users_prestamo_aval ? ( is_null($users_prestamo_aval->telefono_cliente) ? [] : json_decode($users_prestamo_aval->telefono_cliente) ) : [];
      $telefono_pareja_aval = $users_prestamo_aval ? ( is_null($users_prestamo_aval->telefono_pareja) ? [] : json_decode($users_prestamo_aval->telefono_pareja) ) : [];
    @endphp
  
    @foreach($telefono_cliente_aval as $value)
       {{$value->valor}} &nbsp;
    @endforeach
          </td>
        </tr>
        <tr>
          <td><b>PAREJA:</b> {{ $users_prestamo_aval ? $users_prestamo_aval->ap_paterno_pareja : '' }} {{ $users_prestamo_aval ? $users_prestamo_aval->ap_materno_pareja : '' }}, {{ $users_prestamo_aval ? $users_prestamo_aval->nombres_pareja : '' }}</td>
          <td><b>DNI/CE:</b> {{ $users_prestamo_aval ? $users_prestamo_aval->dni_pareja : '' }}</td>
          <td><b>TELÉFONOS:</b>
    @foreach($telefono_pareja_aval as $value) 
            {{ $value->valor }} &nbsp;
          </td>
    @endforeach
        </tr>
      </table>
        @endif
      
      <h4 align="center">HISTORIAL DE PAGO</h4>
      <?php echo $html_historial_pago ?>
      <br>
      <b>RESUMEN DE PAGOS Y SALDOS:</b>
      <div style="height:100px;">
      <div style="width:290px;float:left;margin-right:10px;">
      <table style="width:100%; border: 1px solid #000;border-left:0px solid #000;border-right:0px solid #000;">
        <tr>
          <td style="border-bottom:1px solid #000;text-align:center;"><b>Est. de Cuotas - {{ $credito->forma_pago_credito_nombre }}</b></td>
          <td style="border-bottom:1px solid #000;text-align:center;"><b>N°</b></td>
          <td style="border-bottom:1px solid #000;text-align:center;"><b>Saldo(S/.)</b></td>
        </tr>
        <tr>
          <td>Cancelados</td>
          <td style="text-align:right;">{{$numero_cuota_cancelada}}</td>
          <td style="text-align:right;">{{$cuota_pagada}}</td>
        </tr>
        <tr>
          <td>Pendientes</td>
          <td style="text-align:right;">{{$numero_cuota_pendiente}}</td>
          <td style="text-align:right;">{{$cuota_pendiente}}</td>
        </tr>
        <tr>
          <td>Cumplido y Vencidos</td>
          <td style="text-align:right;">{{$numero_cuota_vencida}}</td>
          <td style="text-align:right;">{{$saldo_vencido}}</td>
        </tr>
      </table>
      </div>
      <div style="width:160px;float:left;margin-right:10px;">
      <table style="width:100%; border: 1px solid #000;border-left:0px solid #000;border-right:0px solid #000;">
        <tr>
          <td style="border-bottom:1px solid #000;text-align:center;"><b>Forma de Pago de Cuo.</b></td>
          <td style="border-bottom:1px solid #000;text-align:center;"><b>N°</b></td>
        </tr>
        <tr>
          <td>Adelantado</td>
          <td style="text-align:right;">{{$pagocuota_adelantado}}</td>
        </tr>
        <tr>
          <td>Puntual</td>
          <td style="text-align:right;">{{$pagocuota_puntual}}</td>
        </tr>
        <tr>
          <td>Vencido</td>
          <td style="text-align:right;">{{$pagocuota_vencido}}</td>
        </tr>
      </table>
      </div>
      <div style="width:130px;float:left;margin-right:10px;">
      <table style="width:100%; border: 1px solid #000;border-left:0px solid #000;border-right:0px solid #000;">
        <tr>
          <td style="border-bottom:1px solid #000;text-align:center;"><b>Saldo D. Capital (S/.)</b></td>
        </tr>
        <tr>
          <td style="text-align:right;">{{$saldo_capital}}</td>
        </tr>
      </table>
      </div>
      <div style="width:130px;float:left;">
      <table style="width:100%; border: 1px solid #000;border-left:0px solid #000;border-right:0px solid #000;">
        <tr>
          <td style="border-bottom:1px solid #000;text-align:center;"><b>Saldo CxC (S/.)</b></td>
        </tr>
        <tr>
          <td style="text-align:right;">0.00</td>
        </tr>
      </table>
      </div>
      </div>
      
      
      <br>
      <b>GARANTÍA DE CLIENTE:</b>
      <table style="width:100%;border-top: 2px solid #000;border-bottom: 1px solid #000;">
        <tr>
          <td colspan="2" style="border-bottom: 1px solid #000;">
              <b>Descripción</b>
          </td>
          <td width="120px" style="border-bottom: 1px solid #000;">
              <b>Cobertura Real (S/.)</b>
          </td>
          <td width="120px" style="border-bottom: 1px solid #000;">
              <b>Valor Comercial (S/.)</b>
          </td>
        </tr>
        @if(count($garantias)>0)
        <?php $i=1;  ?>
            @foreach($garantias as $value)
        <tr>
          <td width="5px">{{$i}}.-</td>
          <td>{{ $value->garantias_codigo }} <b>{{ $value->garantias_noprendarias_tipo_garantia_noprendaria  }}:</b> {{ $value->descripcion }} 
          </td>
          <td style="text-align:right;">
              {{$value->valor_realizacion}}
          </td>
          <td style="text-align:right;">
              {{$value->valor_comercial}}
          </td>
        </tr>
        <?php $i++ ?>
            @endforeach
        @else
        <tr>
          <td colspan="2">Cliente No tiene ninguna garantia.</td>
        </tr>
        @endif
      </table>
      
      <br>
      <b>GARANTÍA DE AVAL:</b>
      <table style="width:100%;border-top: 2px solid #000;border-bottom: 1px solid #000;">
        <tr>
          <td colspan="2" style="border-bottom: 1px solid #000;">
              <b>Descripción</b>
          </td>
          <td width="120px" style="border-bottom: 1px solid #000;">
              <b>Cobertura Real (S/.)</b>
          </td>
          <td width="120px" style="border-bottom: 1px solid #000;">
              <b>Valor Comercial (S/.)</b>
          </td>
        </tr>
        @if(count($garantiasaval)>0)
        <?php $i=1 ?>
            @foreach($garantiasaval as $value)
        <tr>
          <td width="5px">{{$i}}.-</td>
          <td>{{ $value->garantias_codigo }} <b>{{ $value->garantias_noprendarias_tipo_garantia_noprendaria  }}:</b> {{ $value->descripcion }}
          </td>
          <td style="text-align:right;">
              {{$value->valor_realizacion}}
          </td>
          <td style="text-align:right;">
              {{$value->valor_comercial}}
          </td>
        </tr>
        <?php $i++ ?>
            @endforeach
        @else
        <tr>
          <td colspan="2">{{$aval==''?'No tiene Aval/Garante/Fiador.':''}} {{ $aval!='' && count($garantiasaval)==0?'El Aval/Garante/Fiador no tiene ninguna garantia.':''}}</td>
        </tr>
        @endif
      </table>
      
    </div>
  </main>
</body>
</html>