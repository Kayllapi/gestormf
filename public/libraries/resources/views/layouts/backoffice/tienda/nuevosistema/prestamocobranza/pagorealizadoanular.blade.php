  <div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Anular Cobranza</span>
      <a class="btn btn-success" href="javascript:;" onclick="mostrar_pagorealizado()"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
  </div>
  <form action="javascript:;" 
        onsubmit="callback({
                            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamocobranza/{{ $cobranza->id }}',
                            method: 'PUT',
                            data:   {
                              view: 'anular_pagorealizado'
                            }
                          },
                          function(resultado){
                              pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamocobranza/{{ $cobranza->idprestamo_credito }}/edit?view=cobranza',result:'#cont-clientecredito'});
                              removecarga({input:'#carga-cobranza'});
                          },this)">
    
    <div class="row">
      <div class="col-sm-6">
        <label>Código</label>
        <input type="text" value="{{ $cobranza->codigo }}" disabled>
        <label>Fecha de Pago</label>
        <input type="text" value="{{ date_format(date_create($cobranza->fecharegistro), "d/m/Y h:i:s A") }}" disabled>
        <label>Agencia</label>
        <input type="text" value="{{ $agencia->nombrecomercial }}" disabled>
      </div>
      <div class="col-sm-6">
        <label>Cliente</label>
        <input type="text" value="{{ $cobranza->cliente_identificacion }} - {{ $cobranza->cliente }}" disabled>
        <label>Asesor</label>
        <input type="text" value="{{ $cobranza->asesor_apellidos }}, {{ $cobranza->asesor_nombre }} " disabled>
        <label>Ventanilla</label>
        <input type="text" value="{{ $cobranza->cajero_apellidos }}, {{ $cobranza->cajero_nombre }} " disabled>
      </div>
    </div>
    <table class="table">
      <thead style="background: #31353d; color: #fff;">
        <tr>
          <td style="padding: 8px; text-align: center;">Nº</td>
          <td style="padding: 8px; text-align: center;">Cuota</td>
          <td style="padding: 8px; text-align: center;">Mora</td>
          <td style="padding: 8px; text-align: center;">Acuenta</td>
          <td style="padding: 8px; text-align: center;">Total</td>
        </tr>
      </thead>
    <tbody>
        <?php 
        $total_cuota = 0;
        $total_moraapagar = 0;
        $total_acuenta = 0;
        $total_cuotaapagar = 0;
        ?>
        @foreach ($cobranzadetalle as $value)
          <?php
            $credito = DB::table('s_prestamo_creditodetalle')
              ->whereId($value->idprestamo_creditodetalle)
              ->first();
            $total_cuota = $total_cuota+$credito->cuota;
            $total_moraapagar = $total_moraapagar+$credito->moraapagar;
            $total_acuenta = $total_acuenta+$credito->acuenta;
            $total_cuotaapagar = $total_cuotaapagar+$credito->cuotaapagar;
          ?>
          <tr>
            <td style="padding: 8px; text-align: center;">{{ $credito->numero }}</td>
            <td style="padding: 8px; text-align: center;">{{ $credito->cuota }}</td>
            <td style="padding: 8px; text-align: center;">{{ $credito->moraapagar }}</td>
            <td style="padding: 8px; text-align: center;">{{ $credito->acuenta }}</td>
            <td style="padding: 8px; text-align: center;">{{ $credito->cuotaapagar }}</td>
          </tr>
        @endforeach
        <tr>
          <td colspan="4" style="text-align: right; font-weight: bold;">Cuota:</td>
          <td style="white-space: nowrap; padding: 8px; text-align: center;">{{ number_format($total_cuota, 2, '.', '') }}</td>
        </tr>
        <tr>
          <td colspan="4" style="text-align: right; font-weight: bold;">Mora (+):</td>
          <td style="white-space: nowrap; padding: 8px; text-align: center;">{{ number_format($total_moraapagar, 2, '.', '') }}</td>
        </tr>
        <tr>
          <td colspan="4" style="text-align: right; font-weight: bold;">Acuenta (-):</td>
          <td style="white-space: nowrap; padding: 8px; text-align: center;">{{ number_format($total_acuenta, 2, '.', '') }}</td>
        </tr>
        <tr>
          <td colspan="4" style="text-align: right; font-weight: bold;">Total:</td>
          <td style="white-space: nowrap; padding: 8px; text-align: center;">{{ number_format($total_cuotaapagar, 2, '.', '') }}</td>
        </tr>
      </tbody>
      </tbody>
    </table>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de Anular la Cobranza?</b>
    </div>
    <button type="submit" class="btn  mx-btn-post">Anular</button>
  </form>