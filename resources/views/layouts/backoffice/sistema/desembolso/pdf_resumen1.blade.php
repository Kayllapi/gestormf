<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>H. RESUMEN</title>
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
    <div style="float:left;font-size:18px;">{{ $tienda->nombre }}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
  </header>
  <main>
    <div class="container">
      <h4 align="center">HOJA DE RESUMEN DE PRÉSTAMO</h4>
      <table style="width:100%; border: 2px solid #000;">
        <tr>
          <td><b>Cuenta:</b> C{{ str_pad($credito->cuenta, 8, "0", STR_PAD_LEFT) }}</td>
          <td><b>Monto de préstamo S/.:</b> {{ $credito->monto_solicitado }}</td>
        </tr>
        <tr>
          <td><b>Producto:</b> {{ $credito->nombreproductocredito }}</td>
          <td><b>Número de cuotas:</b> {{ $credito->cuotas }}</td>
        </tr>
        <tr>
          <td><b>Cliente:</b> {{ $credito->nombreclientecredito }}</td>
          <td><b>Interes Compensatorio de Periodo a Pagar S/.:</b> {{ $credito->interes_total }}</td>
        </tr>
        <tr>
          <td><b>DNI:</b> {{ $credito->docuementocliente }}</td>
          <td><b>Comisión de Servicio de Periodo S/.:</b> {{ $credito->total_comision }}</td>
        </tr>
        <tr>
          <td><b>Tipo de Cliente:</b> {{ $credito->tipo_operacion_credito_nombre }}</td>
          <td><b>Cargo de Periodo S/.:</b> {{ $credito->total_cargo }}</td>
        </tr>
        <tr>
          <td><b>Frecuencia de pago:</b> {{ $credito->forma_pago_credito_nombre }}</td>
          <td><b>Total a Pagar S/.:</b> {{ $credito->total_pagar }}</td>
        </tr>
        <tr>
          <td><b>Destino de Crédito:</b> {{ $credito->tipo_destino_credito_nombre }}</td>
          <td><b>Modalidad de Crédito:</b> {{ $credito->modalidad_credito_nombre }}</td>
        </tr>
        <tr>
          <td></td>
          @if($credito->cuotas==1)
          <td><b>Tasa de Interes Compensatorio Efectivo Mensual (TEM):</b> {{ $credito->tasa_tip }} %</td>
          @else
          <td><b>Tasa de Interes Compensatorio Efectivo Mensual (TEM):</b> {{ $credito->tasa_tem }} %</td>
          @endif
        </tr>
      </table>
      <table style="width:100%; border: 2px solid #000;border-top:0px solid #000;">
        <tr>
          <td style="text-align: right">
            <span style="float: left"><b>Fecha de Desembolso:</b> {{ date_format(date_create($credito->fecha_desembolso),'d-m-Y h:s:i A') }}</span>
            <span><b>Días de gracia:</b> {{ $credito->dia_gracia }}</span>
          </td>
          <td style="text-align: right"><b>Fecha de Cancelación:</b> {{ date_format(date_create($credito->fecha_ultimopago),'d-m-Y') }}</td>
        </tr>
      </table>
      
      <table style="width:100%;">
        <tr>
          <td colspan="2">
              <b>Garantia del Cliente</b>
          </td>
          <td width="90px">
              <b>Cobertura Real</b>
          </td>
        </tr>
        @if(count($garantias)>0)
        <?php $i=1;  ?>
            @foreach($garantias as $value)
        <tr>
          <td width="5px">{{$i}}.-</td>
          <td>{{ $value->garantias_codigo }} <b>{{ $value->garantias_noprendarias_tipo_garantia_noprendaria  }}:</b> {{ $value->descripcion }} 
          </td>
          <td>
              {{$value->valor_realizacion}}
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
      @if($credito->nombreavalcredito!='')
      <br>
      <table style="width:100%; border: 2px solid #000;">
        <tr>
          <td colspan="2"><b>Aval:</b> {{ $credito->nombreavalcredito }}</td>
        </tr>
      </table>
      @endif
      
      <table style="width:100%;">
        <tr>
          <td colspan="2">
              <b>Garantia del Aval</b>
          </td>
          <td width="90px">
              <b>Cobertura Real</b>
          </td>
        </tr>
        @if(count($garantiasaval)>0)
        <?php $i=1 ?>
            @foreach($garantiasaval as $value)
        <tr>
          <td width="5px">{{$i}}.-</td>
          <td>{{ $value->garantias_codigo }} <b>{{ $value->garantias_noprendarias_tipo_garantia_noprendaria  }}:</b> {{ $value->descripcion }} 
          <td>
              {{$value->valor_realizacion}}
          </td>
        </tr>
        <?php $i++ ?>
            @endforeach
        @else
        <tr>
          <td colspan="2">Aval No tiene ninguna garantia.</td>
        </tr>
        @endif
      </table>
      <br>
      <table style="width:100%; border: 2px solid #000;">
        <tr>
          <td colspan="3">PENALIDADES POR INCUMPLIMIENTO DE PAGO DE CUOTA (se considera {{ $credito->config_dias_tolerancia_garantia }} días de tolerancia ): </td>
        </tr>
        @if($credito->idforma_credito==1)
   
        <tr style="border-top: 2px solid #000;">
          <td colspan="3">Por custodia de garantía prendaria al vencimiento del plazo x dia (hasta {{ $credito->config_dias_maximo_penalidad }} días):</td>
        </tr>
        <br>
        <tr>
        @foreach($tipo_garantia1 as $value)
          <td><b>{{ $value->nombre }}:</b> S/. {{ $value->penalidad }}</td>
        @endforeach
        </tr>
        <tr>
        @foreach($tipo_garantia2 as $value)
          <td><b>{{ $value->nombre }}:</b> S/. {{ $value->penalidad }}</td>
        @endforeach
        </tr>
        <tr>
        @foreach($tipo_garantia3 as $value)
          <td><b>{{ $value->nombre }}:</b> S/. {{ $value->penalidad }}</td>
        @endforeach
        </tr>
        <br>
        @endif
    <?php
    /*$penalidad = 0;
    if($credito->idforma_credito==1){
        if($credito->modalidad_calculo=='Interes Simple'){
            $penalidad =  $credito->config_penalidad_couta_simple;
        }
        elseif($credito->modalidad_calculo=='Interes Compuesto'){
            $penalidad =  $credito->config_penalidad_couta_compuesto;
        }
    }else{
        if($credito->modalidad_calculo=='Interes Simple'){
            $penalidad =  $credito->config_penalidad_couta_simple_noprendaria;
        }
        elseif($credito->modalidad_calculo=='Interes Compuesto'){
            $penalidad =  $credito->config_penalidad_couta_compuesto_noprendaria;
        }
    }*/
      
    ?>
        <!--tr>
          <td colspan="3"  style="border-top: 2px solid #000;">
            Por Incumplimiento de las cuotas: % de la cuota<br>
          </td>
        </tr-->
        <tr>
          <td colspan="3"  style="border-top: 2px solid #000;">
            Tasa Moratoria Mensual: {{ $credito->config_tasa_moratoria }}%
          </td>
        </tr>
      </table>
      
          <?php
          $ubi = explode('-',$usuario->db_idubigeo);
          $departamento = '';
          $provincia = '';
          $distrito = '';
          if(count($ubi)>1){
              $departamento = $ubi[2];
              $provincia = $ubi[1];
              $distrito = $ubi[0];
          }
      
      
          $departamentoaval = '';
          $provinciaaval = '';
          $distritoaval = '';
      
          if($aval!=''){
          $ubiaval = explode('-',$aval->db_idubigeo);
          if(count($ubiaval)>1){
              $departamentoaval = $ubiaval[2];
              $provinciaaval = $ubiaval[1];
              $distritoaval = $ubiaval[0];
          }
          }
          ?>
      
<br>
<br>
      <table class="table" style="width:60%;margin-left:148px;">
        <tr>
          <td style="padding:5px;border: 1px solid #000;width:50%;">
            <span><b>{{ $tienda->nombre }}</b></span>
            <br>
            <span>{{ $tienda->representante }}</span>
            <br>
            <span><b>Representante Legal</b></span>
            <br>
            <span><b>EL ACREEDOR</b></span>
          </td>
          <td style="padding:5px;border: 1px solid #000;text-align:center;" colspan="2">
            <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$tienda->firma) }}" height="60px">
          </td>
        </tr>
      </table>
    <div style="width:100%; height:5px;"></div>
      <table class="table" style="width:100%;">
        <tr>
          <td style="padding:5px;border: 1px solid #000;width:50%;">
            <span><b>{{ $usuario->nombrecompleto }}</b></span>
            <br>
            <span><b>DNI: </b>{{ $usuario->identificacion }}</span>
            <br>
            <span><b>Domicilio: </b>{{ $usuario->direccion }}, {{ $distrito }} - {{ $provincia }} - {{ $departamento }}</span>
            <br>
            <span><b>EL/LOS PRESTATARIO(S)/Representante Legal</b></span>
          </td>
          <td style="padding:5px;border: 1px solid #000;">
          </td>
          <td style="padding:5px;border: 1px solid #000;width:15%;">
          </td>
        </tr>
        @if($credito->participarconyugue_titular=='on')
        <tr>
          <td style="padding:5px;border: 1px solid #000;width:50%;">
            <span><b>{{ $users_prestamo->nombrecompleto_pareja }}</b></span>
            <br>
            <span><b>DNI: </b>{{ $users_prestamo->dni_pareja }}</span>
            <br>
            <span><b>Domicilio: </b>{{ $usuario->direccion }}, {{ $distrito }} - {{ $provincia }} - {{ $departamento }}</span>
            <br>
            <span><b>EL/LOS PRESTATARIO(S)</b></span>
          </td>
          <td style="padding:5px;border: 1px solid #000;">
          </td>
          <td style="padding:5px;border: 1px solid #000;">
          </td>
        </tr>
        @endif
        @if($aval!='')
        <tr>
          <td style="padding:5px;border: 1px solid #000;width:50%;">
            <span><b>{{ $aval!=''?$aval->nombrecompleto:'' }}</b></span>
            <br>
            <span><b>DNI: </b>{{ $aval!=''?$aval->identificacion:'' }}</span>
            <br>
            <span><b>Domicilio: </b> {{ $aval!=''?$aval->direccion:'' }}, {{ $distritoaval }} - {{ $provinciaaval }} - {{ $departamentoaval }}</span>
            <br>
            <span><b>SU(S) AVAL(ES) SOLIDARIO(S)</b></span>
          </td>
          <td style="padding:5px;border: 1px solid #000;">
          </td>
          <td style="padding:5px;border: 1px solid #000;">
          </td>
        </tr>
        @endif
        @if($users_prestamo_aval!='' && $credito->participarconyugue_aval=='on')
        @if($users_prestamo_aval->dni_pareja!='' or $users_prestamo_aval->nombrecompleto_pareja!='')
        <tr>
          <td style="padding:5px;border: 1px solid #000;width:50%;">
            <span><b>{{ $users_prestamo_aval->nombrecompleto_pareja }}</b></span>
            <br>
            <span><b>DNI: </b>{{ $users_prestamo_aval->dni_pareja }}</span>
            <br>
            <span><b>Domicilio: </b> {{ $aval!=''?$aval->direccion:'' }}, {{ $distritoaval }} - {{ $provinciaaval }} - {{ $departamentoaval }}</span>
            <br>
            <span><b>SU(S) AVAL(ES) SOLIDARIO(S)</b></span>
          </td>
          <td style="padding:5px;border: 1px solid #000;">
          </td>
          <td style="padding:5px;border: 1px solid #000;">
          </td>
        </tr>
        @endif
        @endif
        
      </table>
      
      
    </div>
  </main>
</body>
</html>