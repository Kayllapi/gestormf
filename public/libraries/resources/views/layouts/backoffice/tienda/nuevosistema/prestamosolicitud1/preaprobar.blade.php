<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Preaprobar Crédito</span>
    <a class="btn btn-success" href="javascript:;" onclick="index()"><i class="fa fa-angle-left"></i> Atras</a></a>
  </div>
</div>
<form action="javascript:;"
onsubmit="callback({
                    route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud/{{$prestamocredito->id}}',
                    method: 'PUT',
                    data:   {
                        view: 'preaprobar'
                    }
                },
                function(resultado){
                 index();
                },this)">
  <div class="row">
    <div class="col-sm-6">
        <label>Cliente</label>
        <input type="text" value="{{$prestamocredito->cliente_nombre}}" disabled>
        @if($prestamocredito->idconyuge!=0)
        <label>Participar con Cónyuge</label>
        <input type="text" value="{{$prestamocredito->conyuge_nombre}}" disabled>
        @endif
        @if($prestamocredito->idgarante!=0)
        <label>Participar con Aval o Garante</label>
        <input type="text" value="{{$prestamocredito->garante_nombre}}" disabled>
        @endif
        <div class="list-single-main-wrapper fl-wrap">
            <div class="breadcrumbs gradient-bg fl-wrap">
              <span>Crédito</span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <label>Monto</label>
                <input type="number" value="{{$prestamocredito->monto}}" min="0" step="0.01" disabled>
                <label>Número de Cuotas</label>
                <input type="number" value="{{$prestamocredito->numerocuota}}" min="1" step="1" disabled>
                <label>Fecha de Inicio</label>
                <input type="date" value="{{$prestamocredito->fechainicio}}" disabled>
                <label>Frecuencia</label>
                <input type="text" value="{{$prestamocredito->frecuencia_nombre}}" disabled>
                <div id="cont-numerodias" <?php echo $prestamocredito->idprestamo_frecuencia==5?'': 'style="display: none"' ?>>
                    <label>Número de Días</label>
                    <input type="number" value="{{$prestamocredito->numerodias}}" id="numerodias" value="0" min="0" step="1" disabled>
                </div>
            </div>
            <div class="col-md-4">
                <label>Tasa</label>
                <select id="idtasa" disabled>
                    <option></option>
                    <option value="1">Interes Fija</option>
                    <option value="2">Interes Efectiva</option>
                </select>
                <label>Interes %</label>
                <input type="number" min="0" step="0.01" id="tasa" value="{{ $prestamocredito->tasa }}" disabled>
                <label>Interes Total</label>
                <input type="text" id="total_interes" value="{{ $prestamocredito->total_interes }}" disabled/>
                @if($prestamocredito->total_segurodesgravamen>0)
                <label>Seguro Desgravamen</label>
                <input type="text" id="total_segurodesgravamen" value="{{ $prestamocredito->total_segurodesgravamen }}" disabled/>
                @endif
                <label>Total a Pagar</label>
                <input type="text" id="total_cuotafinal" value="{{ $prestamocredito->total_cuotafinal }}" disabled/>
            </div>
            <div class="col-md-4">
                <label>Excluir Días:</label>
          
                <table style="width: 100%;">
                  <tr>
                    <td style="text-align: right;padding: 10px;font-weight: bold;">Sábados</td>
                    <td>
                      <div class="onoffswitch">
                          <input type="checkbox" class="onoffswitch-checkbox excluirsabado" id="excluirsabado" onclick="creditoCalendario()" <?php echo $prestamocredito->excluirsabado=='on'?'checked="true"':'' ?> disabled>
                          <label class="onoffswitch-label" for="excluirsabado">
                              <span class="onoffswitch-inner"></span>
                              <span class="onoffswitch-switch"></span>
                          </label> 
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align: right;padding: 10px;font-weight: bold;">Domingos</td>
                    <td>
                      <div class="onoffswitch">
                          <input type="checkbox" class="onoffswitch-checkbox excluirdomingo" id="excluirdomingo" onclick="creditoCalendario()" <?php echo $prestamocredito->excluirdomingo=='on'?'checked="true"':'' ?> disabled>
                          <label class="onoffswitch-label" for="excluirdomingo">
                              <span class="onoffswitch-inner"></span>
                              <span class="onoffswitch-switch"></span>
                          </label> 
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align: right;padding: 10px;font-weight: bold;">Feriados</td>
                    <td>
                      <div class="onoffswitch">
                          <input type="checkbox" class="onoffswitch-checkbox excluirferiado" id="excluirferiado" onclick="creditoCalendario()" <?php echo $prestamocredito->excluirferiado=='on'?'checked="true"':'' ?> disabled>
                          <label class="onoffswitch-label" for="excluirferiado">
                              <span class="onoffswitch-inner"></span>
                              <span class="onoffswitch-switch"></span>
                          </label> 
                      </div>
                    </td>
                  </tr>
                </table>
            </div>
        </div> 
    </div>
    <div class="col-sm-6">
      <div class="table-responsive">
      <table class="table" id="tabla-preaprobado-creditocalendario">
        <thead style="background: #31353d; color: #fff;">
          <tr>
            <td style="padding: 8px;text-align: right;">Nº</td>
            <td style="padding: 8px;text-align: right;">Fecha de Pago</td>
            <td style="padding: 8px;text-align: right;">Saldo Capital</td>
            <td style="padding: 8px;text-align: right;">Amortización</td>
            <td style="padding: 8px;text-align: right;">Interes</td>
            @if ($prestamocredito->total_segurodesgravamen>0)
            <td style="padding: 8px;text-align: right;">Seguro</td>
            @endif
            <td style="padding: 8px;text-align: right;">Cuota</td>
            <td style="padding: 8px;text-align: right;">Total</td>
          </tr>
        </thead>
        <tbody>
          @foreach ($prestamocreditodetalle as $value)
            <tr>
              <td style="padding: 8px;text-align: right;width: 50px;">{{ $value->numero }}</td>
              <td style="padding: 8px;text-align: right;width: 120px;">{{ $value->fechavencimiento }}</td>
              <td style="padding: 8px;text-align: right;">{{ $value->saldocapital }}</td>
              <td style="padding: 8px;text-align: right;">{{ $value->amortizacion }}</td>
              <td style="padding: 8px;text-align: right;">{{ $value->interes }}</td>
              @if ($prestamocredito->total_segurodesgravamen>0)
              <td style="padding: 8px;text-align: right;">{{ $value->seguro }}</td>
              @endif
              <td style="padding: 8px;text-align: right;">{{ $value->cuota }}</td>
              <td style="padding: 8px;text-align: right;">{{ $value->total }}</td>
            </tr>
          @endforeach
          <tr style="background-color: #31353c;color: white;">
            <td style="padding: 8px;text-align: right;width: 50px;" colspan="3">TOTAL</td>
            <td style="padding: 8px;text-align: right;">{{ $prestamocredito->total_amortizacion }}</td>
            <td style="padding: 8px;text-align: right;">{{ $prestamocredito->total_interes }}</td>
            @if ($prestamocredito->total_segurodesgravamen>0)
            <td style="padding: 8px;text-align: right;">{{ $prestamocredito->total_segurodesgravamen }}</td>
            @endif
            <td style="padding: 8px;text-align: right;">{{ $prestamocredito->total_cuota }}</td>
            <td style="padding: 8px;text-align: right;">{{ $prestamocredito->total_cuotafinal }}</td>
          </tr>
        </tbody>
      </table>
      </div>
    </div>
  </div>
  <button type="submit" class="btn mx-btn-post">PreAprobar</button>
</form>
<script>
    $('#idtasa').select2({
        placeholder: '-- Seleccionar Tasa --',
        minimumResultsForSearch: -1,
    }).val({{ $prestamocredito->idprestamo_tipotasa }}).trigger('change');
</script>