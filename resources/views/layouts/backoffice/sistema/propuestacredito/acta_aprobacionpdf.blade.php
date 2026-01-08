<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACTA APROBACIÓN</title>
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
      footer .page:after { content: counter(page, decimal-leading-zero); }

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
    <div style="float:left;font-size:18px;">{{ $tienda->nombre }}</div> {{ Auth::user()->usuario }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
  </footer>
  <main>
    <div class="row">
      <div class="col" style="width:500px;">
        <table style="width:100%;">
          <tr>
            <td width="130px">AGENCIA/OFICINA:</td>
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
            <td>FECHA DE APROBACIÓN:</td>
            <td class="border-td">{{ date_format(date_create($credito->fecha_aprobacion),'d-m-Y H:i:s A') }}</td>
          </tr>
        </table>
      </div>
      <div class="col">
        <table>
          <tr>
            <td width="100px">NRO SOLICITUD:</td>
            <td class="border-td" width="100px">S{{ str_pad($credito->id, 8, '0', STR_PAD_LEFT)  }}</td>
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
    </div>
          <?php
          //$monto_financiar = $credito_cuantitativa_deudas->propuesta_monto;
          //$propuesta_tem = $credito_cuantitativa_deudas->propuesta_tem;
          //$nombre_forma_pago_credito = $credito_cuantitativa_deudas->nombre_forma_pago_credito;
          //$propuesta_total_pagar = $credito_cuantitativa_deudas->propuesta_total_pagar;
          //$propuesta_cuotas = $credito_cuantitativa_deudas->propuesta_cuotas;

          ?>
      <div class="mb-1 mt-2">
        <span class="badge d-block">SOLICITUD: {{ $credito->estado }} - En comité de créditos con las condiciones siguientes</span>
      </div>
    <div class="row">
      <div class="col">
        <table style="width:100%;">
          <tr>
            <td>Segmento de fuente ingreso:</td>
            <td class="border-td">{{ $users_prestamo->idfuenteingreso == 1 ? 'INDEPENDIENTE' : 'DEPENDIENTE' }}</td>
          </tr>
          <tr>
            <td>Tipo de crédito:</td>
            <td class="border-td">{{ $credito->tipo_creditonombre }}</td>
          </tr>
          <tr>
            <td>Tipo de cliente:</td>
            <td class="border-td">{{ $credito->tipo_operacion_credito_nombre }}</td>
          </tr>
          <tr>
            <td>Producto:</td>
            <td class="border-td">{{ $credito->nombreproductocredito }}</td>
          </tr>
          <tr>
            <td>Destino del crédito:</td>
            <td class="border-td">{{ $credito->tipo_destino_credito_nombre}}</td>
          </tr>
        </table>
      </div>
      <div class="col">
        <table>
          <tr>
            <td>Frecuencia de Pago:</td>
            <td class="border-td" width="100px">{{ strtoupper($credito->forma_pago_credito_nombre) }}</td>
          </tr>
          <tr>
            <td>Modalidad:</td>
            <td class="border-td">{{ $credito->modalidad_credito_nombre }}</td>
          </tr>
          <tr>
            <td>N° de cuotas:</td>
            <td class="border-td">{{ $credito->cuotas }}</td>
          </tr>
          <tr>
            <td>Dias de gracia:</td>
            <td class="border-td">{{ $credito->dia_gracia }}</td>
          </tr>
          <tr>
            <td>Fecha primer pago:</td>
            <td class="border-td">{{ $credito->fecha_primerpago }}</td>
          </tr>
        </table>
      </div>
      <div class="col">
        <table>
          <tr>
            <td>Monto Aprobado S/.:</td>
            <td class="border-td campo_moneda" style="background-color:yellow;">{{ $credito->monto_solicitado }}</td>
          </tr>
          <tr>
            <td>Tasa efectiva mensual %:</td>
            <td class="border-td campo_moneda">{{ $credito->tasa_tem }}</td>
          </tr>
          <tr>
            <td>Cuota S/.:</td>
            <td class="border-td campo_moneda">{{ $credito->cuota_pago }}</td>
          </tr>
          <tr>  
            <td>Tipo de cambio %:</td>
            <td class="border-td campo_moneda">{{ configuracion($tienda->id,'tipo_cambio_dolar')['valor'] }}</td>
          </tr>
        </table>
      </div>
    </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block">GARANTIAS:</span>
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
                <td>{{ $value->garantias_noprendarias_tipo_garantia_noprendaria }}</td>
                <td>{{ $value->descripcion }}</td>
                <td class="campo_numero campo_moneda">{{ $value->valor_mercado }}</td>
                <td class="campo_numero campo_moneda">{{ $value->valor_comercial }}</td>
                <td class="campo_numero campo_moneda">{{ $value->valor_realizacion }}</td>
              </tr>
            @empty
            <tr sumar_garantia>
              <td colspan="5">Sin Garantia</td>
            </tr>
            @endforelse

          </tbody>
        </table>
          <?php
          
        $garantias = DB::table('credito_garantia')
          ->where('credito_garantia.tipo', 'CLIENTE')
          ->where('credito_garantia.idcredito', $credito->id)
          ->where('credito_garantia.idtipo_garantia_noprendaria', 0)
          ->select(
            'credito_garantia.*',
          )
          ->get();
          ?>
        
            @if(count($garantias)>0)
              <b>GARANTIA PRENDARIA</b>
              <table style="width:100%;">
                <?php $i=1 ?>
                @foreach($garantias as $value)
                <tr>
                  <td width="5px">{{$i}}.-</td>
                  <td>{{$value->garantias_codigo }} <b>{{ $value->garantias_noprendarias_tipo_garantia_noprendaria  }}:</b> {{ $value->descripcion }} 
                  </td>
                </tr>
                <?php $i++ ?>
                @endforeach
              </table>
            @else
              <table style="width:100%;">
                <tr>
                  <td colspan="2">No tiene ninguna garantia.</td>
                </tr>
              </table>
            @endif
      </div>
      <div class="col" style="width:361px;">
        
        <span class="badge" style="margin-bottom:5px;">AVAL: <span style="font-size:10px;">{{ $credito->nombreavalcredito }}</span> 
        <div style="font-size:10px;text-align: right;margin-top:-12px;"><span>DNI: <span style="font-size:10px;">{{ $credito->documentoaval }}</span> </span></div>
        </span>
  
        <table class="table table-bordered" id="table-garantia-aval">
          <thead>
            <tr>
              <th>Garantías</th>

              <th>Descripción de garantía en Propuesta</th>
              <th>Valor de mercado (S/.)</th>
              <th>Valor comercial (Tasado) (S/.)</th>
              <th>Valor de realización (Tasado) (S/.)</th>
            </tr>
          </thead>
          <tbody>           
            @forelse($credito_garantias_aval as $key => $value)
              <tr >
                <td>{{ $value->garantias_noprendarias_tipo_garantia_noprendaria }}</td>
                <td>{{ $value->descripcion }}</td>
                <td class="campo_numero campo_moneda">{{ $value->valor_mercado }}</td>
                <td class="campo_numero campo_moneda">{{ $value->valor_comercial }}</td>
                <td class="campo_numero campo_moneda">{{ $value->valor_realizacion }}</td>
              </tr>
            @empty
            <tr sumar_garantia>
              <td colspan="5">Sin Garantia</td>
            </tr>
            @endforelse
          </tbody>
        </table>
        
                @if($users_prestamo_aval!='')
          <table class="table table-bordered" id="table-garantia-aval" style="width:100%;margin-top:5px;">
            <tbody>            
                    @if($users_prestamo_aval->dni_pareja!='' or $users_prestamo_aval->nombrecompleto_pareja!='')
                      <tr>
                        <th style="width:130px;">Pareja Aval(Garante)/Fiador:</th>
                        <td>{{ $users_prestamo_aval->nombrecompleto_pareja }}</td>
                        <th style="width:30px;">DNI:</th>
                        <td style="width:40px;">{{ $users_prestamo_aval->dni_pareja }}</td>
                      </tr>
                    @endif
            </tbody>
          </table>
                @endif
      </div>
    </div>

    <div class="row">
      <div class="col">
        <table style="width:100%;">
          <tr>
            <td>Asesor(a):</td>
            <td class="border-td">{{ substr($asesor->nombre, 0, 1) }}{{ $asesor->apellidopaterno }}</td>
          </tr>
        </table>
      </div>
      <div class="col">
        <table>
          <tr>
            <td>Funcionario que aprueba:</td>
            <td class="border-td" width="100px">
            <?php $i=1 ?>
            <?php $funcionario='' ?>
            @foreach($credito_aprobacion as $value)
            <?php 
                if($i==1){
                    echo $funcionario = substr($value->nombre, 0, 1).' '.$value->apellidopaterno;
                }
                ?>
            <?php $i++ ?>
            @endforeach
            </td>
          </tr>
        </table>
      </div>
    </div>
    <span class="badge subtitle">Excepciones y autorizaciones:
    @if($usuario_excepcionyautorizacion!='')
    {{$usuario_excepcionyautorizacion->nombrecompleto}}
    @endif
    </span>
    <pre style="border: 1px solid #515151;padding:3px;margin-top:0px;white-space: pre-wrap;">{{ $credito->excepcionesautorizaciones }}</pre>
    <span class="badge subtitle">Opinión de área de Riesgos:
    @if($usuario_areariesgos!='')
    {{$usuario_areariesgos->nombrecompleto}}
    @endif</span>
   
    <pre style="border: 1px solid #515151;padding:3px;margin-top:0px;white-space: pre-wrap;">{{ $credito->areariesgos }}</pre>
    <span class="badge subtitle">Comentarios de visita y/o de verificación:
    @if($usuario_comentariovisita!='')
    {{$usuario_comentariovisita->nombrecompleto}}
    @endif</span>
    <pre style="border: 1px solid #515151;padding:3px;margin-top:0px;white-space: pre-wrap;">{{ $credito->comentariovisita }}</pre>
    <span class="badge subtitle">Funcionarios que aprueban en cómite de créditos la presente solicitud:</span>
     
    <div class="row"> 
        @foreach($credito_aprobacion as $key => $value)
          <div class="col">
              <table class="table table-bordered">
              <tbody>
                <tr>
                  <td>Funcionario</td>
                  <td>{{ substr($value->nombre, 0, 1) }}{{ $value->apellidopaterno }}</td>
                </tr>
                <tr>
                  <td>Cargo</td>
                  <td>{{ $value->nombre_permiso }}</td>
                </tr>
                <tr>
                  <td>Fecha</td>
                  <td>{{ date_format(date_create($value->fecha),"d/m/Y") }}</td>
                </tr>
                <tr>
                  <td>Hora</td>
                  <td>{{ date_format(date_create($value->fecha),"h:i A") }}</td>
                </tr>
              </tbody>
              </table>
          </div>
        @endforeach
    </div> 
  
              <table class="table table-bordered" style="width:100%;">
              <thead>
                <tr>
                    <th style="width:150px;">Funcionario</th>
                    <th>Comentario</th>
                    <th>Firma</th>
                </tr>
              </thead>
              <tbody>
        @foreach($credito_aprobacion as $key => $value)
                <tr>
                  <td style="height:70px;"> 
                    {{ substr($value->nombre, 0, 1) }}{{ $value->apellidopaterno }}</td>
                  <td style="vertical-align:top;"><pre style="padding:3px;margin-top:0px;white-space: pre-wrap;">{{ $value->comentario }}</pre></td>
                  <td width="200px"></td>
                </tr>
        @endforeach
              </tbody>
              </table>
      
        
  </main>
</body>
</html>