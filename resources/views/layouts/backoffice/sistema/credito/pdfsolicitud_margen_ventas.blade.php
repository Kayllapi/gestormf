<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MARGEN DE VENTAS</title>
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
    <div style="float:left;font-size:15px;">{{ $tienda->nombre }} | {{ $tienda->nombreagencia }}</div> {{ Auth::user()->usuario }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
  </footer>
  @php
    $productos = $credito_cuantitativa_margen_venta ? ( $credito_cuantitativa_margen_venta->productos == "" ? [] : json_decode($credito_cuantitativa_margen_venta->productos) ) : [];
    $productos_mensual = $credito_cuantitativa_margen_venta ? ( $credito_cuantitativa_margen_venta->productos_mensual == "" ? [] : json_decode($credito_cuantitativa_margen_venta->productos_mensual) ) : [];
    $dias = $credito_cuantitativa_margen_venta ? ( $credito_cuantitativa_margen_venta->dias == "" ? [] : json_decode($credito_cuantitativa_margen_venta->dias) ) : [];
    $semanas = $credito_cuantitativa_margen_venta ? ( $credito_cuantitativa_margen_venta->semanas == "" ? [] : json_decode($credito_cuantitativa_margen_venta->semanas) ) : [];
    $subproducto = $credito_cuantitativa_margen_venta ? ( $credito_cuantitativa_margen_venta->subproducto == "" ? [] : json_decode($credito_cuantitativa_margen_venta->subproducto) ) : [];
    $subproductomensual = $credito_cuantitativa_margen_venta ? ( $credito_cuantitativa_margen_venta->subproductomensual == "" ? [] : json_decode($credito_cuantitativa_margen_venta->subproductomensual) ) : [];
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
            <td class="border-td" width="100px">{{ date_format(date_create($credito_cuantitativa_margen_venta->fecha),'Y-m-d') }}</td>
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
    <span class="badge">IV. CALCULO DE MARGEN Y NIVEL VENTAS</span>
    <span class="badge subtitle">4.1 VENTAS DENTRO DE LA SEMANA (VENTAS CON FRECUENCIA DIARIA Y SEMANAL)</span>
            <div style="margin-left:140px;">MARGEN DE VENTAS TOTAL CALCULADO: {{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->margen_venta_calculado : '0.00' }}%</div>

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
          <tbody >
            @foreach($productos as $key => $value)
              <tr>
                <td>{{ $value->producto }}</td>
                <td >
                  {{$value->unidadmedida}}

                </td>
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
              <td class="campo_moneda">{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->total_venta : '0.00' }}</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->total_compra : '0.00' }}</td>
              <td></td>
            </tr>
            <tr>
              <td colspan=5 align="right">Mg. de Venta</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->porcentaje_margen : '0.00' }}%</td>
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
              <th width="100px">FRECUENCIA</th>
              <th>{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->frecuencia_ventas : 'DIARIO' }}</th>
            </tr>
          </thead>
        </table>
        <br>
        <table class="table" width="220px">
          <thead>
            <tr>
              <th width="10px">N°</th>
              <th>Dias</th>
              <th>Ventas</th>
            </tr>
          </thead>
          <tbody>
            @foreach($dias as $value)
              <tr>
                <td numero>{{ $value->numero }}</td>
                <td dia>{{ $value->dia }}</td>
                <td class="campo_moneda" valor>{{ number_format($value->valor, 2, '.', '') }}</td>
              </tr>
            @endforeach
            <tr total>
              <th colspan="2">Venta Semanal (S/.)</th>
              <td class="campo_moneda">{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->venta_total_dias : '0.00' }}</td>
            </tr>
          </tbody>
        </table>
        <br>
        <table class="table" width="220px">
          <thead>
            <tr>
              <th>N° de Días</th>
              <th >{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->numero_dias : '0' }}</th>
            </tr>
          </thead>
        </table>
        <br>
        <table class="table" width="220px">
          <thead>
            <tr>
              <th>Venta mensual (S/.)</th>
              <th class="campo_moneda" >{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->venta_mensual : '0' }}</th>
            </tr>
          </thead>
        </table>
        <br>
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
              <td>{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->recabo_dato_numero : '1' }}</td>
              <td>{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->recabo_dato_dia : '' }}</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->recabo_dato_monto : '0.00' }}</td>
            </tr>
          </tbody>
        </table>
        <br>
        <table class="table" width="220px">
          <thead>
            <tr>
              <th colspan="2">Estado de muestra de DATOS</th>
            </tr>
            <tr>
              <th colspan="2">{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->estado_muestra : '0.00' }}</th>
            </tr>
          </thead>
        </table>
        <table class="table" width="220px">
          <thead>
            <tr>
              <th>Mg. De venta al mes (1) (S/.)</th>
              <th class="campo_moneda">{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->margen_ventas : '0.00' }}</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
    <br>
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
    
    <span class="badge subtitle">4.2 VENTAS EN MAS DE UNA SEMANA (VENTAS CON FRECUENCIA MENSUAL)</span>
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
              <td class="campo_moneda">{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->total_venta_mensual : '0.00' }}</td>
              <td class="campo_moneda">{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->total_compra_mensual : '0.00' }}</td>
              <td></td>
            </tr>
            <tr>
              <th colspan=5 align="right">Mg. de Venta</th>
              <th class="campo_moneda">{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->porcentaje_margen_mensual : '0.00' }}%</th>
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
                <th width="100px">FRECUENCIA</th>
                <th >MENSUAL</th>
              </tr>
            </thead>
        </table>
        <br>
        <table class="table table-bordered" width="220px">
          <thead>
            <tr>
              <th width="140px">Semanas</th>
              <th>Ventas</th>
            </tr>
          </thead>
          <tbody>
            @foreach($semanas as $value)
              <tr>
                <td semana>{{ $value->semana }}</td>
                <td class="campo_moneda" valor>{{ number_format($value->valor, 2, '.', '') }}</td>
              </tr>
            @endforeach
            <tr total>
              <th>Venta Mensual (S/.)</th>
              <td class="campo_moneda">{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->venta_total_mensual : '0.00' }}</td>
            </tr>
          </tbody>
        </table>
        <br>
        <table class="table" width="220px">
          <thead>
            <tr>
              <th colspan="2">Estado de muestra de DATOS</th>
            </tr>
            <tr>
              <th class="campo_moneda" colspan="2">{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->estado_muestra_mensual : '' }}</th>
            </tr>
          </thead>
        </table>
        <br>
        <table class="table" width="220px">
          <thead>
            <tr>
              <th width="150px">Mg. De venta al mes (2) (S/.)</th>
              <th class="campo_moneda">{{ $credito_cuantitativa_margen_venta ? $credito_cuantitativa_margen_venta->margen_ventas_mensual : '0.00' }}</th>
            </tr>
          </thead>
        </table>
      </div>
      
    </div>
    <br>
    
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
    <div class="row" >
      <div class="col" style="margin-left:215px;margin-top:60px;">
        <div style="width:300px;height:1px;border-bottom:1px solid #ccc;"></div>
        <p align="center">Asesor(a) de Créditos: {{ Auth::user()->codigo }} <br>Firma y Sello</p>		
      </div>
    </div>
  </main>
</body>
</html>