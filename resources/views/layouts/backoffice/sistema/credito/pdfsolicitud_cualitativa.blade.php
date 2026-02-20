<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EVALUACIÓN CUALITATIVA</title>
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
  
  <main>
      <h4 align="center" style="font-size:13px;margin:0;padding:0;">EVALUACIÓN DE CRÉDITO - INGRESO INDEPENDIENTE <br> CRÉDITO: MyPE y CONSUMO N. R.</h4>
      <br>
    <span class="badge">I. INFORMACIÓN DEL CLIENTE</span>
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
            <td>TIPO GIRO ECONÓMICO</td>
            <td class="border-td">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->nombretipo_giro_economico : '' }}</td>
          </tr>
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
            <td class="border-td" width="100px">{{ date_format(date_create($credito->fecha),'Y-m-d') }}</td>
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
    <span class="badge">II. EVALUACIÓN CUALITATIVA</span>
    <span class="badge subtitle">2.1 N° DE ENTIDADES FINANCIERAS (Se considera deuda interna y Líneas de creditos sin uso)</span>
    <div class="row">
      <div class="col">
        <table class="table">
          <thead>
            <tr>
              <th width="100px">Deudores</th>
              <th width="100px">Como</th>
              <th width="50px">N°</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td rowspan="2">CLIENTE</td>
              <td>P.Natural</td>
              <td style="text-align: center;">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->cantidad_cliente_natural : 0 }}</td>
            </tr>
            <tr>
              <td>P.Jurídica</td>
              <td style="text-align: center;">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->cantidad_cliente_juridico : 0 }}</td>
            </tr>
            <tr>
              <td rowspan="2">PAREJA</td>
              <td>P.Natural</td>
              <td style="text-align: center;">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->cantidad_pareja_natural : 0 }}</td>
            </tr>
            <tr>
              <td>P.Jurídica</td>
              <td style="text-align: center;">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->cantidad_pareja_juridico : 0 }}</td>
            </tr>
            <tr>
              <td style="text-align: right;" colspan=2>TOTAL</td>
              <td style="text-align: center;">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->total_deuda : 0 }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <span class="badge subtitle">2.2 GESTIÓN DEL GIRO ECONÓMICO</span>
    <div class="row">
      <div class="col">
        <table>
          <tbody>
            <tr>
              <td width="250px">a) Experiencia como Microempresario(a) (meses)</td>
              <td class="border-td campo_moneda" width="50px">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->experiencia_microempresa : 0 }}</td>
            </tr>
            <tr>
              <td>b) Tiempo en el mismo local (meses)</td>
              <td class="border-td campo_moneda">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->tiempo_mismo_local : 0 }}</td>
            </tr>
            <tr>
              <td>c) Instalaciones o local</td>
              <td class="border-td">
                @if($users_prestamo->db_idlocalnegocio_ac_economica!='')
                  {{ $users_prestamo->db_idlocalnegocio_ac_economica }}
                @endif
              </td>
            </tr>
            <tr>
              <td>d) N° de trabajadores a tiempo completo</td>
              <td class="border-td campo_moneda">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->nro_trabajador_completo : 0 }}</td>
            </tr>
            <tr>
              <td>e) N° de trabajdores a tiempo parcial</td>
              <td class="border-td campo_moneda">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->nro_trabajador_parcal : 0 }}</td>
            </tr>
            <tr>
              <td>
                f) Estabilidad de los otros ingresos (marcar x):
                <table class="subtable">
                  <tbody>
                    <tr>
                      <td>Asalariado fijo</td>
                      <td>{{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->saladario_fijo == "SI" ? "(x)" : "( )" ) : "( )" }}</td>
                      <td>Otros negocios</td>
                      <td>{{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->otros_negocios == "SI" ? "(x)" : "( )" ) : "( )" }}</td>
                    </tr>
                    <tr>
                      <td>Alquiler de locales</td>
                      <td>{{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->alquiler_local == "SI" ? "(x)" : "( )" ) : "( )" }}</td>
                      <td>No tiene</td>
                      <td>{{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->no_tiene == "SI" ? "(x)" : "( )" ) : "( )" }}</td>
                    </tr>
                    <tr>
                      <td>Pensionista</td>
                      <td>{{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->pensionista == "SI" ? "(x)" : "( )" ) : "( )" }}</td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col">
        <table>
          <tbody>
            
            <tr>
              <td>g) Gestión en general (marcar x):
                <table class="subtable">
                  <tr>
                    <td>Lleva registros de ventas, cuentas por cobrar y pagar</td>
                    <td>{{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->registro_ventas_cuentas == "SI" ? "(x)" : "( )" ) : "( )" }}</td>
                  </tr>
                  <tr>
                    <td>Pagos de impuestos al día</td>
                    <td>{{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->pago_impuestos_dia == "SI" ? "(x)" : "( )" ) : "( )" }}</td>
                  </tr>
                  <tr>
                    <td>Pago de recibo de servicios báicos al día</td>
                    <td>{{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->pago_servicios_dia == "SI" ? "(x)" : "( )" ) : "( )" }}</td>
                  </tr>
                  <tr>  
                    <td>Política de orden en su establecimiento </td>
                    <td>{{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->politica_orden == "SI" ? "(x)" : "( )" ) : "( )" }}</td>
                  </tr>
                  <tr>
                    <td>Local cumple con normas municipales y legales</td>
                    <td>{{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->normas_municipales == "SI" ? "(x)" : "( )" ) : "( )" }}</td>
                  </tr>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <span class="badge subtitle">2.3 REFERENCIAS</span>
    <div class="row">
      <div class="col">
        @php
          $referencia_cliente = $credito_evaluacion_cualitativa ? ( is_null($credito_evaluacion_cualitativa->referencia) ? [] : json_decode($credito_evaluacion_cualitativa->referencia) ) : [];
        @endphp
        <table class="table">
          <thead>
            <tr>
              <th>N°</th>
              <th>Fuente</th>
              <th>Apellidos y Nombres</th>
              <th>Vinculo: Familiar/Personas/Otros</th>
              <th>Telf./Celular</th>
            </tr>
          </thead>
          <tbody>
            @foreach($referencia_cliente as $key => $value)
              @php
                $fuente = DB::table('f_tiporeferencia')->whereId($value->fuente)->first();
              @endphp
              <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $fuente->nombre }}</td>
                <td>{{ $value->nombre }}</td>
                <td>{{ $value->vinculo }}</td>
                <td>{{ $value->celular }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <span class="badge subtitle">2.4 GASTOS FAMILIARES BÁSICOS (Mensual)</span>
    <div class="row">
      <div class="col">
        <table class="table table-bordered" id="table-gastos-familiares">
          <thead>
            <tr>
              <th>Concepto</th>
              <th width="80px">Monto</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Alimentación</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_alimentacion : 0 }}</td>
            </tr>
            <tr>
              <td>Educación</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_educacion : 0 }}</td>
            </tr>
            <tr>
              <td>Vestimenta</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_vestimenta : 0 }}</td>
            </tr>
            <tr>
              <td>Transporte</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_transporte : 0 }}</td>
            </tr>
            <tr>
              <td>Salud</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_salud : 0 }}</td>
            </tr>
            <tr>
              <td>Alquiler de vivienda</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_vivienda : 0 }}</td>
            </tr>
            <tr>
              <th style="background-color: #fff !important;" align="left">Servicios</th>
              <td style="background-color: #fff !important;" class="campo_moneda">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->total_servicios : 0 }}</td>
            </tr>
            <tr>
              <td>Agua</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_agua : 0 }}</td>
            </tr>
            <tr>
              <td>Luz</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_luz : 0 }}</td>
            </tr>
            <tr>
              <td>Teléfono fijo e internet</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_telefono_internet : 0 }}</td>
            </tr>
            <tr>
              <td>T. Celular</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_celular : 0 }}</td>
            </tr>
            <tr>
              <td>Cable</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_cable : 0 }}</td>
            </tr>
            <tr>
              <td>Otros gastos personales (15%)</td>
              <td class="campo_moneda">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_otros : 0 }}</td>
            </tr>
            <tr>
              <th style="background-color: #fff !important;" align="left">Total Gasto Familiar (S/.)</th>
              <td style="background-color: #fff !important;" class="campo_moneda">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_total : 0 }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col">
        <table>
          <tr>
            <td>Número total de hijos</td>
            <td class="border-td" width="50px" align="center">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->total_hijos : 0 }}</td>
          </tr>
          <tr>
            <td>Número de hijos dependientes </td>
            <td class="border-td" width="50px" align="center">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->total_hijos_dependientes : 0 }}</td>
          </tr>
        </table>
      </div>
    </div>
    <span class="badge subtitle">2.5 DETALLE DEL DESTINO DEL PRÉSTAMO</span>
    <div class="row">
      <textarea id="detalle_destino_prestamo" class="form-control">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->detalle_destino_prestamo : '' }}</textarea>
    </div>
    <span class="badge subtitle">2.6 COMENTARIOS Y ESPECIFICACIONES DE FORTALEZAS DEL NEGOCIO</span>
    <div class="row">
      <textarea id="fortalezas_negocio" class="form-control">{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->fortalezas_negocio : '' }}</textarea>
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