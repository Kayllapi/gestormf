<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/penalidadcomision/0') }}',
        method: 'PUT',
          data:{
              view: 'editar',
              idagencia: '{{ $agencia->id }}'
          }
      },
      function(resultado){
          
      },this)"> 

    <div class="modal-body">
      <div class="row justify-content-center">
        <div class="col-sm-12 col-md-6">
          
          <div class="alert alert-warning p-1 mb-2">
            <b>AGENCIA:</b> {{ $agencia->nombreagencia }} &mdash; los cambios se guardan solo sobre esta agencia.
          </div>

          <div class="mb-1">
            <span class="badge d-block">Penalidad por custodia x día (al vencimiento del período crédito)</span>
          </div>
          {{-- tipo_garantia.penalidad es un valor unico para TODAS las agencias (esa tabla no tiene
               idtienda), por eso aqui solo se muestra y no se edita. --}}
          <div class="alert alert-info p-1 mb-1">
            <i class="fa fa-info-circle"></i> Estas penalidades son <b>comunes a todas las agencias</b>: se muestran solo como referencia.
          </div>
          <table class="table" id="table-penalidad">
            <tbody>
              @foreach($tipo_garantia as $value)
                <tr id="{{ $value->id }}">
                  <td>{{ $value->nombre }} S/.</td>
                  <td penalidad><input type="number" step="any" class="form-control" value="{{ $value->penalidad }}" disabled></td>
                </tr>
              @endforeach
            </tbody>
          </table>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Días Maximo de penalidad x custodia:</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="dias_maximo_penalidad" value="{{ configuracion($agencia->id,'dias_maximo_penalidad')['valor'] }}">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Cargo x Cust. Garantía (al vencimiento del período crédito):</label>
            <label class="col-sm-2 chk">
                <input type="checkbox" name="cargo_custodia_garantia" id="cargo_custodia_garantia" {{ configuracion($agencia->id,'cargo_custodia_garantia')['valor'] == '1' ? 'checked' : '' }}>
                <span class="checkmark"></span>
                <span>Activar</span>
            </label>
          </div>
          <!--div class="mb-1">
            <span class="badge d-block">Penalidad cuota vencida (Prendaria)</span>
          </div>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Penalidad por cuota de cálculo Simple (%):</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="penalidad_couta_simple" value="{{ configuracion($agencia->id,'penalidad_couta_simple')['valor'] }}">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Penalidad por cuota de cálculo Compuesto (%):</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="penalidad_couta_compuesto" value="{{ configuracion($agencia->id,'penalidad_couta_compuesto')['valor'] }}">
            </div>
          </div>
          <div class="mb-1">
            <span class="badge d-block">Penalidad cuota vencida (No Prendaria)</span>
          </div>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Penalidad por cuota de cálculo Simple (%):</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="penalidad_couta_simple_noprendaria" value="{{ configuracion($agencia->id,'penalidad_couta_simple_noprendaria')['valor'] }}">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Penalidad por cuota de cálculo Compuesto (%):</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="penalidad_couta_compuesto_noprendaria" value="{{ configuracion($agencia->id,'penalidad_couta_compuesto_noprendaria')['valor'] }}">
            </div>
          </div-->
          <div class="mb-1">
            <span class="badge d-block">Días de Tolerancia para liquidación de garantias prendarias, Descuento y Precio</span>
          </div>
          <div class="row mt-1">
            <label class="col-sm-1 col-form-label" style="text-align: right;">Días:</label>
            <div class="col-sm-1">
              <input type="number" class="form-control" step="any" id="dias_tolerancia" value="{{ configuracion($agencia->id,'dias_tolerancia')['valor'] }}">
            </div>
            <label class="col-sm-4 col-form-label" style="text-align: right;">% de Desc. Liquid. de VC</label>
            <div class="col-sm-1">
              <input type="number" class="form-control" step="any" id="porcentaje_descuento_liquidacion" value="{{ configuracion($agencia->id,'porcentaje_descuento_liquidacion')['valor'] }}">
            </div>
            <label class="col-sm-4 col-form-label" style="text-align: right;">% de Incre. de Precio Liquid.</label>
            <div class="col-sm-1">
              <input type="number" class="form-control" step="any" id="porcentaje_precio_liquidacion" value="{{ configuracion($agencia->id,'porcentaje_precio_liquidacion')['valor'] }}">
            </div>
          </div>
          <div class="mb-1">
            <span class="badge d-block">Otros</span>
          </div>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Días Tolerancia de cuota vencida (Prendaria y No Prendaria):</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="dias_tolerancia_garantia" value="{{ configuracion($agencia->id,'dias_tolerancia_garantia')['valor'] }}">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Tasa Moratoria Mensual (%):</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="tasa_moratoria" value="{{ configuracion($agencia->id,'tasa_moratoria')['valor'] }}">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Tipo de Cambio ($):</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="tipo_cambio_dolar" value="{{ configuracion($agencia->id,'tipo_cambio_dolar')['valor'] }}">
            </div>
          </div>
          <div class="mb-1">
            <span class="badge d-block">Costo por gestión de garantia (Custodia de garantia por ACREEDOR)</span>
          </div>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Costo Mensual (%):</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="comision_gestion_garantia_cargo" value="{{ configuracion($agencia->id,'comision_gestion_garantia_cargo')['valor'] }}">
            </div>
          </div>
          <div class="mb-1">
            <span class="badge d-block">Costo por gestión de garantia (Custodia de garantia Convenio con ACREEDOR)</span>
          </div>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Costo Mensual (%):</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="comision_gestion_garantia_convenio" value="{{ configuracion($agencia->id,'comision_gestion_garantia_convenio')['valor'] }}">
            </div>
          </div>
         
          
         
        </div>
        <div class="col-sm-12 col-md-4 d-none">
          <div class="mb-1">
            <span class="badge d-block">Penalidad por tenencia x día</span>
          </div>
          <table class="table" id="table-noprendatario">
            <tbody>
              @foreach($tipo_garantia_noprendaria as $value)
                <tr id="{{ $value->id }}">
                  <td>{{ $value->nombre }}</td>
                  <td penalidad><input type="number" step="any" class="form-control" value="{{ $value->penalidad }}"></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
      </div>
      <div class="row mt-1 justify-content-center">
        <div class="col-sm-12 col-md-2">
          <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
        </div>
      </div>
      
    </div>
</form>      