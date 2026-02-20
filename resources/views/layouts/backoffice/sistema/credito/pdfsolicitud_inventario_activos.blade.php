<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVENTARIOS Y ACTIVOS</title>
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
    $inventario = $credito_cuantitativa_inventario ? ( $credito_cuantitativa_inventario->inventario == "" ? [] : json_decode($credito_cuantitativa_inventario->inventario) ) : [];
    $inmuebles = $credito_cuantitativa_inventario ? ( $credito_cuantitativa_inventario->inmuebles == "" ? [] : json_decode($credito_cuantitativa_inventario->inmuebles) ) : [];
    $muebles = $credito_cuantitativa_inventario ? ( $credito_cuantitativa_inventario->muebles == "" ? [] : json_decode($credito_cuantitativa_inventario->muebles) ) : [];
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
            <td class="border-td" width="100px">{{ $credito_cuantitativa_inventario!=''?date_format(date_create($credito_cuantitativa_inventario->fecha),'Y-m-d') : '' }}</td>
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
    
    <span class="badge">V. INVENTARIO Y  ACTIVOS FIJOS - NEGOCIO PRINCIPAL</span>
    <div class="row">
      
      <div class="col">
        <table class="table" >
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
              <td class="color_totales campo_moneda" colspan=4>Inventario total de productos  (S/.)</td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_inventario ? $credito_cuantitativa_inventario->total_inventario : '0.00' }}</td>
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
              <td class="color_totales campo_moneda" colspan=4>Total de activos inmuebles  (S/.)</td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_inventario ? $credito_cuantitativa_inventario->total_inmuebles : '0.00' }}</td>
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
              <td class="color_totales campo_moneda" colspan=4>Total de activos muebles (S/.)</td>
              <td class="color_totales campo_moneda">{{ $credito_cuantitativa_inventario ? $credito_cuantitativa_inventario->total_muebles : '0.00' }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
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