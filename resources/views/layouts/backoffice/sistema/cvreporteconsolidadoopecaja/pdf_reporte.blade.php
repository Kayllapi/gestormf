<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REPORTE CONSOLIDADO DE OPERACIONES DE CAJA</title>
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
          margin-bottom: 0.7cm;
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
          color: #000;
          text-align: center;
          line-height: 0.4cm;
          font-size:12px;
      }
      /** Definir las reglas de numeracion de página **/ 
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
    <div style="float:left;font-size:18px;">{{ $co_actual['tienda']->ticket_nombre }} | {{ $co_actual['agencia']->nombreagencia }}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
  </footer>
  <main>
    <div class="container">
      <h4 align="center" style="margin:0px;">REPORTE CONSOLIDADO DE OPERACIONES DE COMPRA Y VENTAS DE BIENES - INSTITUCIONAL</h4>
            <div style="margin-top:-5px;"><b>AGENCIA: </b>{{ $co_actual['agencia']->nombreagencia }}</div>
          <table style="width:100%;">
            <tr>
              <th colspan="6" rowspan="2" style="border-bottom: 2px solid #000;border-top: 2px solid #000;">Saldos y Operaciones de Efectivo</th>
              <th colspan="2" rowspan="2" style="border-bottom: 2px solid #000;border-top: 2px solid #000;"></th>
              <th style="border-bottom: 2px solid #000;border-top: 2px solid #000;width:60px;"></th>
              <th style="border-bottom: 2px solid #000;border-top: 2px solid #000;width:80px;">Corte</th>
              <th style="border-bottom: 2px solid #000;border-top: 2px solid #000;width:60px;"></th>
              <th style="border-bottom: 2px solid #000;border-top: 2px solid #000;width:80px;">Cierre Ant.</th>
            </tr>
            <tr>
              <th style="border-bottom: 2px solid #000;">Arqueo</th>
              <th style="border-bottom: 2px solid #000;">{{$co_actual['corte']}}</th>
              <th style="border-bottom: 2px solid #000;">Arqueo</th>
              <th style="border-bottom: 2px solid #000;">{{$co_anterior?date("d-m-Y",strtotime(date($co_anterior->corte))):'0.00'}}</th>
            </tr>
            <tr>
              <td style="text-align:left;width:80px;"><b>Saldos</b></td>
              <td style="width:80px;"></td>
              <td colspan="4" style="border-bottom: 2px solid #000;text-align:left;">Caja</td>
              <td colspan="2" style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;text-align:right;"><b>{{$co_actual['arqueo_caja']}}</b></td>
              <td style="border-bottom: 2px solid #000;text-align:right;"><b>{{$co_actual['saldos_caja']}}</b></td>
              <td style="border-bottom: 2px solid #000;text-align:right;"><b>{{$co_anterior?$co_anterior->arqueo_caja:'0.00'}}</b></td>
              <td style="border-bottom: 2px solid #000;text-align:right;"><b>{{$co_anterior?$co_anterior->saldos_caja:'0.00'}}</b></td>
            </tr>
            <tr>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td colspan="4" style="border-bottom: 2px solid #000;"></td>
              <td colspan="2" style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
            </tr>
            <tr>
              <td rowspan="3" style="text-align:left;"><b>Ingreso y Egreso por 
                <span style="border-bottom: 1px solid #000;">Caja</span></b></td>
              <td style="text-align:left;"><b>Ingreso</b></td>
              <td colspan="4" style="text-align:left;">Venta de Bienes</td>
              <td></td>
              <td style="text-align:right;"></td>
              <td></td>
              <td style="text-align:right;"><b>{{$co_actual['ingresoyegresocaja_ingreso_cvventa']}}</b></td>
              <td></td>
              <td style="text-align:right;"><b>0.00</b></td>
            </tr>
            <tr>
              <td></td>
              <td colspan="4" style="text-align:left;">Incremento de Capital</td>
              <td colspan="2"></td>
              <td></td>
              <td style="text-align:right;"><b>{{$co_actual['ingresoyegresocaja_ingreso_incrementocapital']}}</b></td>
              <td></td>
              <td style="text-align:right;"><b>{{$co_anterior?$co_anterior->ingresoyegresocaja_ingreso_incrementocapital:'0.00'}}</b></td>
            </tr>
            <tr>
              <td></td>
              <td colspan="4" style="border-bottom: 2px solid #000;text-align:left;">Ingresos Extraordinarios</td>
              <td colspan="2" style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;text-align:right;"><b>{{$co_actual['ingresoyegresocaja_ingreso_ingresosextraordinarios']}}</b></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;text-align:right;"><b>{{$co_anterior?$co_anterior->ingresoyegresocaja_ingreso_ingresosextraordinarios:'0.00'}}</b></td>
            </tr>
            <tr>
              <td></td>
              <td style="text-align:left;"><b>Egreso</b></td>
              <td colspan="4" style="text-align:left;">Compra de Bienes</td>
              <td></td>
              <td style="text-align:right;"></td>
              <td></td>
              <td style="text-align:right;"><b>{{$co_actual['ingresoyegresocaja_egreso_cvcompra']}}</b></td>
              <td></td>
              <td style="text-align:right;"><b>0.00</b></td>
            </tr>
            <tr>
              <td></td>
              <td></td>
              <td colspan="4" style="text-align:left;">Reducción de Capital</td>
              <td colspan="2"></td>
              <td></td>
              <td style="text-align:right;"><b>{{$co_actual['ingresoyegresocaja_egreso_reduccioncapital']}}</b></td>
              <td></td>
              <td style="text-align:right;"><b>{{$co_anterior?$co_anterior->ingresoyegresocaja_egreso_reduccioncapital:'0.00'}}</b></td>
            </tr>
            <tr>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td colspan="4" style="border-bottom: 2px solid #000;text-align:left;">Gastos admistrativos y operativos</td>
              <td colspan="2" style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;text-align:right;"><b>{{$co_actual['ingresoyegresocaja_egreso_gastosadministrativosyoperativos']}}</b></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;text-align:right;"><b>{{$co_anterior?$co_anterior->ingresoyegresocaja_egreso_gastosadministrativosyoperativos:'0.00'}}</b></td>
            </tr>
            <tr>
              <td rowspan="5" style="text-align:left;"><b>Ingreso y Egreso por Cuenta 
                <span style="border-bottom: 1px solid #000;">Banco</span></b></td>
              <td style="text-align:left;"><b>Ingreso</b></td>
              <td colspan="4" style="text-align:left;">Venta de Bienes</td>
              <td></td>
              <td>
                @if($co_actual['ingresoyegresobanco_ingreso_cvventa_validacion_cantidad']>0)
                  @if($co_actual['ingresoyegresobanco_ingreso_cvventa_validacion']=='CHECK')
                    <img src="{{url('public/backoffice/sistema/icono_check.png')}}" width="20px">
                  @else
                    <img src="{{url('public/backoffice/sistema/icono_close.png')}}" width="20px">
                  @endif
                @endif
              </td>
              <td></td>
              <td style="text-align:right;"><b>{{$co_actual['ingresoyegresobanco_ingreso_cvventa']}}</b></td>
              <td></td>
              <td style="text-align:right;"><b>0.00</b></td>
            </tr>
            @foreach($co_actual['ingresoyegresobanco_ingreso_cvventas'] as $value)
            <tr>
              <td></td>
              <td></td>
              <td colspan="4" style="text-align:left;">{{ $value['banco_nombre'] }}: {{ $value['banco_cuenta'] }}</td>
              <td>
                @if($value['validacion']=='CHECK')
                <img src="{{url('public/backoffice/sistema/icono_check.png')}}" width="20px">
                @endif
              </td>
              <td></td>
              <td style="text-align:right;">{{ $value['banco'] }}</td>
              <td></td>
              <td style="text-align:right;">0.00</td>
            </tr>
            @endforeach
            <tr>
              <td></td>
              <td></td>
              <td colspan="4" style="text-align:left;">Incremento de Capital</td>
              <td></td>
              <td>
                @if($co_actual['ingresoyegresobanco_ingreso_incrementocapital_validacion_cantidad']>0)
                  @if($co_actual['ingresoyegresobanco_ingreso_incrementocapital_validacion']=='CHECK')
                    <img src="{{url('public/backoffice/sistema/icono_check.png')}}" width="20px">
                  @else
                    <img src="{{url('public/backoffice/sistema/icono_close.png')}}" width="20px">
                  @endif
                @endif
              </td>
              <td></td>
              <td style="text-align:right;"><b>{{$co_actual['ingresoyegresobanco_ingreso_incrementocapital']}}</b></td>
              <td></td>
              <td style="text-align:right;"><b>{{$co_anterior?$co_anterior->ingresoyegresobanco_ingreso_incrementocapital:'0.00'}}</b></td>
            </tr>
            @foreach($co_actual['ingresoyegresobanco_ingreso_incrementocapital_bancos'] as $value)
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td colspan="4" style="text-align:left;">{{ $value['banco_nombre'] }}: {{ $value['banco_cuenta'] }}</td>
              <td>
                @if($value['validacion']=='CHECK')
                <img src="{{url('public/backoffice/sistema/icono_check.png')}}" width="20px">
                @endif
              </td>
              <td></td>
              <td style="text-align:right;">{{ $value['banco'] }}</td>
              <td></td>
              <td style="text-align:right;">0.00</td>
            </tr>
            @endforeach
            <tr>
              <td></td>
              <td></td>
              <td colspan="4" style="text-align:left;">Ingresos Extraordinarios</td>
              <td></td>
              <td>
                @if($co_actual['ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion_cantidad']>0)
                @if($co_actual['ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion']=='CHECK')
                <img src="{{url('public/backoffice/sistema/icono_check.png')}}" width="20px">
                @else
                <img src="{{url('public/backoffice/sistema/icono_close.png')}}" width="20px">
                @endif
                @endif
              </td>
              <td></td>
              <td style="text-align:right;"><b>{{$co_actual['ingresoyegresobanco_ingreso_ingresosextraordinarios']}}</b></td>
              <td></td>
              <td style="text-align:right;"><b>{{$co_anterior?$co_anterior->ingresoyegresobanco_ingreso_ingresosextraordinarios:'0.00'}}</b></td>
            </tr>
            @foreach($co_actual['ingresoyegresobanco_ingreso_ingresosextraordinarios_bancos'] as $value)
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td colspan="4" style="text-align:left;">{{ $value['banco_nombre'] }}: {{ $value['banco_cuenta'] }}</td>
              <td>
                @if($value['validacion']=='CHECK')
                <img src="{{url('public/backoffice/sistema/icono_check.png')}}" width="20px">
                @endif
              </td>
              <td></td>
              <td style="text-align:right;">{{ $value['banco'] }}</td>
              <td></td>
              <td style="text-align:right;">0.00</td>
            </tr>
            @endforeach
            <tr>
              <td></td>
              <td style="border-top: 2px solid #000;text-align:left;"><b>Egreso</b></td>
              <td colspan="4" style="border-top: 2px solid #000;text-align:left;">Compra de Bienes</td>
              <td style="border-top: 2px solid #000;"></td>
              <td style="border-top: 2px solid #000;">
                @if($co_actual['ingresoyegresobanco_egreso_cvcompra_validacion_cantidad']>0)
                  @if($co_actual['ingresoyegresobanco_egreso_cvcompra_validacion']=='CHECK')
                    <img src="{{url('public/backoffice/sistema/icono_check.png')}}" width="20px">
                  @else
                    <img src="{{url('public/backoffice/sistema/icono_close.png')}}" width="20px">
                  @endif
                @endif
              </td>
              <td style="border-top: 2px solid #000;"></td>
              <td style="border-top: 2px solid #000;text-align:right;"><b>{{$co_actual['ingresoyegresobanco_egreso_cvcompra']}}</b></td>
              <td style="border-top: 2px solid #000;"></td>
              <td style="border-top: 2px solid #000;text-align:right;"><b>0.00</b></td>
            </tr>
            @foreach($co_actual['ingresoyegresobanco_egreso_cvcompras'] as $value)
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td colspan="4" style="text-align:left;">{{ $value['banco_nombre'] }}: {{ $value['banco_cuenta'] }}</td>
              <td>
                {{-- @if($value['validacion']=='CHECK')
                <img src="{{url('public/backoffice/sistema/icono_check.png')}}" width="20px">
                @endif --}}
              </td>
              <td></td>
              <td style="text-align:right;">{{ $value['banco'] }}</td>
              <td></td>
              <td style="text-align:right;"><b>0.00</b></td>
            </tr>
            @endforeach
            <tr>
              <td></td>
              <td></td>
              <td colspan="4" style="text-align:left;">Reducción de Capital</td>
              <td></td>
              <td>
                @if($co_actual['ingresoyegresobanco_egreso_reduccioncapital_validacion_cantidad']>0)
                @if($co_actual['ingresoyegresobanco_egreso_reduccioncapital_validacion']=='CHECK')
                <img src="{{url('public/backoffice/sistema/icono_check.png')}}" width="20px">
                @else
                <img src="{{url('public/backoffice/sistema/icono_close.png')}}" width="20px">
                @endif
                @endif
              </td>
              <td></td>
              <td style="text-align:right;"><b>{{$co_actual['ingresoyegresobanco_egreso_reduccioncapital']}}</b></td>
              <td></td>
              <td style="text-align:right;"><b>{{$co_anterior?$co_anterior->ingresoyegresobanco_egreso_reduccioncapital:'0.00'}}</b></td>
            </tr>
            @foreach($co_actual['ingresoyegresobanco_egreso_reduccioncapital_bancos'] as $value)
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td colspan="4" style="text-align:left;">{{ $value['banco_nombre'] }}: {{ $value['banco_cuenta'] }}</td>
              <td>
                @if($value['validacion']=='CHECK')
                <img src="{{url('public/backoffice/sistema/icono_check.png')}}" width="20px">
                @endif
              </td>
              <td></td>
              <td style="text-align:right;">{{ $value['banco'] }}</td>
              <td></td>
              <td style="text-align:right;">0.00</td>
            </tr>
            @endforeach
            <tr>
              <td></td>
              <td></td>
              <td colspan="4" style="text-align:left;">Gastos administrativos y operativos</td>
              <td></td>
              <td>
                @if($co_actual['ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion_cantidad']>0)
                @if($co_actual['ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion']=='CHECK')
                <img src="{{url('public/backoffice/sistema/icono_check.png')}}" width="20px">
                @else
                <img src="{{url('public/backoffice/sistema/icono_close.png')}}" width="20px">
                @endif
                @endif
              </td>
              <td></td>
              <td style="text-align:right;"><b>{{$co_actual['ingresoyegresobanco_egreso_gastosadministrativosyoperativos']}}</b></td>
              <td></td>
              <td style="text-align:right;"><b>{{$co_anterior?$co_anterior->ingresoyegresobanco_egreso_gastosadministrativosyoperativos:'0.00'}}</b></td>
            </tr>
            @foreach($co_actual['ingresoyegresobanco_egreso_gastosadministrativosyoperativos_bancos'] as $value)
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td colspan="4" style="text-align:left;">{{ $value['banco_nombre'] }}: {{ $value['banco_cuenta'] }}</td>
              <td>
                @if($value['validacion']=='CHECK')
                <img src="{{url('public/backoffice/sistema/icono_check.png')}}" width="20px">
                @endif
              </td>
              <td></td>
              <td style="text-align:right;">{{ $value['banco'] }}</td>
              <td></td>
              <td style="text-align:right;">0.00</td>
            </tr>
            @endforeach
            <tr>
              <td rowspan="3" style="text-align:left;border-top: 2px solid #000;"><b>Movimiento Interno de Efectivo</b></td>
              <td style="border-top: 2px solid #000;"></td>
              <td colspan="4" style="text-align:left;border-top: 2px solid #000;"><b>HABILITACIÓN Y GESTIÓN DE LIQUIDEZ ( I )</b></td>
              <td colspan="2" style="border-top: 2px solid #000;"></td>
              <td style="text-align:right;border-bottom: 1px solid #000;border-top: 2px solid #000;"><b>{{$co_actual['habilitacion_gestion_liquidez1']}}</b></td>
              <td style="border-top: 2px solid #000;"></td>
              <td style="border-top: 2px solid #000;"></td>
              <td style="border-top: 2px solid #000;"></td>
              <td style="border-top: 2px solid #000;"></td>
            </tr>
            <tr>
              <td></td>
              <td colspan="3" style="text-align:left;"><b>I.</b> Ret. de Reserva CF para Caja</td>
              <td style="text-align:right;padding-right:30px;"><b>{{$co_actual['ret_reservacf_caja_sum']}}</b></td>
              <td colspan="2" ><b>I.</b> Dep. a Caja desde Reserva CF </td>
              <td style="text-align:right;"><b>{{$co_actual['dep_caja_reservacf']}}</b></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td></td>
              <td colspan="3" style="text-align:left;"><b>II.</b> Ret. de Banco para Caja</td>
              <td style="text-align:right;padding-right:30px;"><b>{{$co_actual['ret_banco_caja_sum']}}</b></td>
              <td colspan="2"><b>II.</b> Dep.  a Caja desde Banco </td>
              <td style="text-align:right;"><b>{{$co_actual['dep_caja_banco']}}</b></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            @foreach($co_actual['ret_banco_caja_bancos'] as $value)
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td colspan="2" style="text-align:left;">{{ $value['banco_nombre'] }}: {{ $value['banco_cuenta'] }}</td>
              <td style="text-align:right;padding-right:30px;">{{ $value['banco'] }}</td>
              <td style="width:50px;"></td>
              <td style="text-align:left;">{{ $value['banco_nombre'] }}: {{ $value['banco_cuenta'] }}</td>
              <td style="text-align:right;">{{ $value['banco_dep'] }}</td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            @endforeach
            <tr>
              <td></td>
              <td></td>
              <td colspan="3" style="text-align:left;"><b>III.</b> Ret. de Caja para Reserva CF</td>
              <td style="text-align:right;padding-right:30px;"><b>{{$co_actual['ret_caja_reservacf_sum']}}</b></td>
              <td colspan="2"><b>III.</b> Dep.  a Reserva CF desde Caja</td>
              <td style="text-align:right;"><b>{{$co_actual['dep_reservacf_caja']}}</b></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td></td>
              <td></td>
              <td colspan="3" style="text-align:left;"><b>IV.</b> Ret. de Caja para Banco</td>
              <td style="text-align:right;padding-right:30px;"><b>{{$co_actual['ret_caja_banco_sum']}}</b></td>
              <td colspan="2"><b>IV.</b> Dep. a Banco desde Caja</td>
              <td style="text-align:right;"><b>{{$co_actual['dep_banco_caja']}}</b></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            @foreach($co_actual['ret_caja_banco_bancos'] as $value)
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td colspan="2" style="text-align:left;">{{ $value['banco_nombre'] }}: {{ $value['banco_cuenta'] }}</td>
              <td style="text-align:right;padding-right:30px;">{{ $value['banco'] }}</td>
              <td></td>
              <td style="text-align:left;">{{ $value['banco_nombre'] }}: {{ $value['banco_cuenta'] }}</td>
              <td style="text-align:right;">{{ $value['banco_dep'] }}</td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            @endforeach
            <tr>
              <td></td>
              <td></td>
              <td colspan="4" style="text-align:left;"><b>HABILITACIÓN Y GESTIÓN DE LIQUIDEZ ( II )</b></td>
              <td colspan="2"></td>
              <td style="text-align:right;border-bottom: 1px solid #000;"><b>{{$co_actual['habilitacion_gestion_liquidez2']}}</b></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td></td>
              <td></td>
              <td colspan="3" style="text-align:left;"><b>V.</b> Ret. de Banco para Reserva CF</td>
              <td style="text-align:right;padding-right:30px;"><b>{{$co_actual['ret_banco_reservacf']}}</b></td>
              <td colspan="2"><b>V.</b> Dep. a Reserva CF desde Banco</td>
              <td style="text-align:right;"><b>{{$co_actual['dep_reservacf_banco']}}</b></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            @foreach($co_actual['ret_banco_reservacf_bancos'] as $value)
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td colspan="2" style="text-align:left;">{{ $value['banco_nombre'] }}: {{ $value['banco_cuenta'] }}</td>
              <td style="text-align:right;padding-right:30px;">{{ $value['banco'] }}</td>
              <td></td>
              <td style="text-align:left;">{{ $value['banco_nombre'] }}: {{ $value['banco_cuenta'] }}</td>
              <td style="text-align:right;">{{ $value['banco_dep'] }}</td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            @endforeach
            <tr>
              <td></td>
              <td></td>
              <td colspan="4" style="text-align:left;"><b>CIERRE Y APERTURA DE CAJA</b></td>
              <td colspan="2"></td>
              <td style="text-align:right;border-bottom: 1px solid #000;"><b>{{$co_actual['cierre_caja_apertura']}}</b></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td></td>
              <td></td>
              <td colspan="3" style="text-align:left;"><b>I.</b> Ret. de Reserva CF para Caja</td>
              <td style="text-align:right;padding-right:30px;"><b>{{$co_actual['ret_reservacf_caja_total']}}</b></td>
              <td colspan="2"><b>I.</b> Dep. a Caja desde Reserva CF</td>
              <td style="text-align:right;"><b>{{$co_actual['dep_caja_reservacf_total']}}</b></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td></td>
              <td></td>
              <td colspan="3" style="text-align:left;"><b>III.</b> Ret. de Caja para Reserva CF</td>
              <td style="text-align:right;padding-right:30px;"><b>{{$co_actual['ret_caja_reservacf_total']}}</b></td>
              <td colspan="2"><b>III.</b> Dep. a Reserva CF desde Caja</td>
              <td style="text-align:right;"><b>{{$co_actual['dep_reservacf_caja_total']}}</b></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td colspan="4" style="border-bottom: 2px solid #000;"></td>
              <td colspan="2" style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
            </tr>
            <tr>
              <td colspan="11"></td>
            </tr>
            <tr>
              <td></td>
              <td></td>
              <td colspan="4" style="text-align:left;">Validación de Operaciones por Cuenta Banco: 
              <img src="{{url('public/backoffice/sistema/icono_check.png')}}" width="20px"></td>
              <td></td>
              <td></td>
              <td></td>
              <td style="text-align:center;"><b>
                @if($co_actual['validacion_operaciones_cuenta_banco']=='SIN OPERACIONES')
                SIN OPERAC.
                @else
                {{$co_actual['validacion_operaciones_cuenta_banco']}}
                @endif
                </b></td>
              <td></td>
              <td style="text-align:right;"><b></b></td>
            </tr>
            <tr>
              <td colspan="11"></td>
            </tr>
            <tr>
              <td></td>
              <td></td>
              <td colspan="4" style="border-bottom: 2px solid #000;"></td>
              <td colspan="2" style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
            </tr><tr>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td colspan="4" style="border-bottom: 2px solid #000;"></td>
              <td colspan="2" style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
            </tr>
            <tr>
              <td colspan="11"></td>
            </tr>
            <tr>
              <td></td>
              <td></td>
              <td colspan="4" style="text-align:left;">Efectivo en Caja al Corte (S/.):</td>
              <td></td>
              <td></td>
              <td></td>
              <td style="text-align:right;border-bottom: 1px solid #000;border-top: 1px solid #000;"><b>{{$co_actual['efectivo_caja_corte']}}</b></td>
              <td></td>
              <td style="text-align:right;border-bottom: 1px solid #000;border-top: 1px solid #000;"><b>{{$co_anterior?$co_anterior->efectivo_caja_corte:'0.00'}}</b></td>
            </tr>
            <tr>
              <td colspan="11"></td>
            </tr>
            <tr>
              <td></td>
              <td></td>
              <td colspan="4" style="text-align:left;">Efectivo en Caja al Arqueo (S/.):</td>
              <td></td>
              <td></td>
              <td></td>
              <td style="text-align:right;border-bottom: 1px solid #000;border-top: 1px solid #000;"><b>{{$co_actual['efectivo_caja_arqueo']}}</b></td>
              <td></td>
              <td style="text-align:right;border-bottom: 1px solid #000;border-top: 1px solid #000;"><b>{{$co_anterior?$co_anterior->efectivo_caja_arqueo:'0.00'}}</b></td>
            </tr>
            <tr>
              <td colspan="11"></td>
            </tr>
            <tr>
              <td></td>
              <td></td>
              <td colspan="4" style="text-align:left;">Resultado:</td>
              <td></td>
              <td></td>
              <td></td>
              <td style="text-align:right;border-bottom: 1px solid #000;border-top: 1px solid #000;"><b>{{$co_actual['resultado']}}</b></td>
              <td></td>
              <td style="text-align:right;border-bottom: 1px solid #000;border-top: 1px solid #000;"><b>{{$co_anterior?$co_anterior->resultado:'0.00'}}</b></td>
            </tr>
            <tr>
              <td colspan="11"></td>
            </tr>
            <tr>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td colspan="4" style="border-bottom: 2px solid #000;"></td>
              <td colspan="2" style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
              <td style="border-bottom: 2px solid #000;"></td>
            </tr>
          </table> 
          @if($data_actual)
          <br><br>
          <?php $resposanble = DB::table('users')->where('id',$data_actual->idresponsable_registro)->first(); ?>
          <?php $permiso = DB::table('permiso')->where('id',$data_actual->idresponsable_registro_idpermiso)->first(); ?>
          <table class="tabla_informativa" style="text-align:center;margin-left:560px;">
              <tr>
                  <td>______________________________<br>
                    {{ strtoupper($permiso->nombre) }}<br>
                    {{ strtoupper($resposanble->nombrecompleto) }}<br>
                    {{ strtoupper($resposanble->codigo) }}
                  </td>
              </tr>
          </table> 
          @endif
    </div>
  </main>
</body>
</html>