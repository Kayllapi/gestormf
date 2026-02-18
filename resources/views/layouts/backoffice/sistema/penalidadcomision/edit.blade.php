<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/penalidadcomision/0') }}',
        method: 'PUT',
          data:{
              view: 'editar',
              prendario: tipo_garantia(),
              noprendario: tipo_garantia_noprendario()
          }
      },
      function(resultado){
          
      },this)"> 

    <div class="modal-body">
      <div class="row justify-content-center">
        <div class="col-sm-12 col-md-6">
          
          
          <div class="mb-1">
            <span class="badge d-block">Penalidad por custodia x día (al vencimiento del período crédito)</span>
          </div>
          <table class="table" id="table-penalidad">
            <tbody>
              @foreach($tipo_garantia as $value)
                <tr id="{{ $value->id }}">
                  <td>{{ $value->nombre }} S/.</td>
                  <td penalidad><input type="number" step="any" class="form-control" value="{{ $value->penalidad }}"></td>
                </tr>
              @endforeach
            </tbody>
          </table>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Días Maximo de penalidad x custodia:</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="dias_maximo_penalidad" value="{{ configuracion($tienda->id,'dias_maximo_penalidad')['valor'] }}">
            </div>
          </div>
          <!--div class="mb-1">
            <span class="badge d-block">Penalidad cuota vencida (Prendaria)</span>
          </div>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Penalidad por cuota de cálculo Simple (%):</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="penalidad_couta_simple" value="{{ configuracion($tienda->id,'penalidad_couta_simple')['valor'] }}">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Penalidad por cuota de cálculo Compuesto (%):</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="penalidad_couta_compuesto" value="{{ configuracion($tienda->id,'penalidad_couta_compuesto')['valor'] }}">
            </div>
          </div>
          <div class="mb-1">
            <span class="badge d-block">Penalidad cuota vencida (No Prendaria)</span>
          </div>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Penalidad por cuota de cálculo Simple (%):</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="penalidad_couta_simple_noprendaria" value="{{ configuracion($tienda->id,'penalidad_couta_simple_noprendaria')['valor'] }}">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Penalidad por cuota de cálculo Compuesto (%):</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="penalidad_couta_compuesto_noprendaria" value="{{ configuracion($tienda->id,'penalidad_couta_compuesto_noprendaria')['valor'] }}">
            </div>
          </div-->
          <div class="mb-1">
            <span class="badge d-block">Días de Tolerancia para liquidación de garantias prendarias y porcentaje de Descuento</span>
          </div>
          <div class="row mt-1">
            <label class="col-sm-1 col-form-label" style="text-align: right;">Días:</label>
            <div class="col-sm-3">
              <input type="number" class="form-control" step="any" id="dias_tolerancia" value="{{ configuracion($tienda->id,'dias_tolerancia')['valor'] }}">
            </div>
            <label class="col-sm-4 col-form-label" style="text-align: right;">% de Descuento Liquidación:</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="porcentaje_descuento" value="">
            </div>
          </div>
          <div class="mb-1">
            <span class="badge d-block">Otros</span>
          </div>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Días Tolerancia de cuota vencida (Prendaria y No Prendaria):</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="dias_tolerancia_garantia" value="{{ configuracion($tienda->id,'dias_tolerancia_garantia')['valor'] }}">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Tasa Moratoria Mensual (%):</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="tasa_moratoria" value="{{ configuracion($tienda->id,'tasa_moratoria')['valor'] }}">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Tipo de Cambio ($):</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="tipo_cambio_dolar" value="{{ configuracion($tienda->id,'tipo_cambio_dolar')['valor'] }}">
            </div>
          </div>
          <div class="mb-1">
            <span class="badge d-block">Costo por gestión de garantia (Custodia de garantia del ACREEDOR)</span>
          </div>
          <div class="row mt-1">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Costo Mensual (%):</label>
            <div class="col-sm-4">
              <input type="number" class="form-control" step="any" id="comision_gestion_garantia_cargo" value="{{ configuracion($tienda->id,'comision_gestion_garantia_cargo')['valor'] }}">
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
<script>
  function tipo_garantia(){
    var data = [];
    $("#table-penalidad > tbody > tr").each(function() {
        var id = $(this).attr('id');  
        let penalidad = $(this).find('td[penalidad] > input').val();
        data.push({ 
            id: id,
            penalidad: penalidad,
        });
    });
    return JSON.stringify(data);
  }
  function tipo_garantia_noprendario(){
    var data = [];
    $("#table-noprendatario > tbody > tr").each(function() {
        var id = $(this).attr('id');  
        let penalidad = $(this).find('td[penalidad] > input').val();
        data.push({ 
            id: id,
            penalidad: penalidad,
        });
    });
    return JSON.stringify(data);
  }
</script>    