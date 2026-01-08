<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/parametrosevaluacion/0') }}',
        method: 'PUT',
          data:{
              view: 'editar',
              capital: json_agencia_capital()
          }
      },
      function(resultado){
          
      },this)"> 

    <div class="modal-body">
      <div class="row justify-content-center">
        <div class="col-sm-12 col-md-6">
          
          
          <div class="mb-1">
            <span class="badge d-block">Parametro de Evaluación</span>
          </div>
          <div class="row mt-1">
            <label class="col-sm-5 col-form-label" style="text-align: right;">PROVISION DE GASTOS FAMILIARES (%):</label>
            <div class="col-sm-7">
              <input type="number" class="form-control" step="any" id="provision_gastos_familiares" value="{{ configuracion($tienda->id,'provision_gastos_familiares')['valor'] }}">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-5 col-form-label" style="text-align: right;">PORCENTAJE MÍNIMO DE MUESTRA DE VENTAS (%):</label>
            <div class="col-sm-7">
              <input type="number" class="form-control" step="any" id="porcentaje_min_muestra" value="{{ configuracion($tienda->id,'porcentaje_min_muestra')['valor'] }}">
            </div>
          </div>
          <hr>
          <div class="row mt-1">
            <label class="col-sm-5 col-form-label" style="text-align: right;">ASIGNACION DE CAPITAL A AGENCIA:</label>
            <div class="col-sm-7">
              
              <table class="table table-bordered" id="table-capital-agencia">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Agencia</th>
                    <th>Capital</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($agencias as $key => $value)
                    <tr id="{{ $value->id }}">
                      <td>{{ $key+1 }}</td>
                      <td>{{ $value->nombreagencia }}</td>
                      <td><input type="number" step="any" capital_agencia value="{{ $value->capital_agencia }}" class="form-control"></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
              <script>
                function json_agencia_capital(){
                  let data = [];
                  $(`#table-capital-agencia > tbody > tr`).each(function() {
                    let id      = $(this).attr('id');
                    let capital = $(this).find('td input[capital_agencia]').val();
                    data.push({ 
                      id: id,
                      capital: capital,
                    });
                  });
                  return JSON.stringify(data);
                }
              </script>
            </div>
          </div>
          <!--div class="row mt-1">
            <label class="col-sm-5 col-form-label" style="text-align: right;">TOPE POR VINCULACION POR RIESGO UNICO(%):</label>
            <div class="col-sm-7">
              <input type="number" class="form-control" step="any" id="tope_vinculacion_riesgo" value="{{ configuracion($tienda->id,'tope_vinculacion_riesgo')['valor'] }}">
            </div>
          </div-->
          <div class="row mt-1">
            <label class="col-sm-5 col-form-label" style="text-align: right;">TOPE POR VINCULACION POR RIESGO UNICO(%):</label>
            <div class="col-sm-7">
              <input type="number" class="form-control" step="any" id="capital_asignado" value="{{ configuracion($tienda->id,'capital_asignado')['valor'] }}">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-5 col-form-label" style="text-align: right;">Número de entidades de endeudamiento máximo:</label>
            <div class="col-sm-7">
              <input type="number" class="form-control" step="any" id="entidades_maxima" value="{{ configuracion($tienda->id,'entidades_maxima')['valor'] }}">
            </div>
          </div>
          <hr>
          <div class="row mt-1">
            <label class="col-sm-5 col-form-label" style="text-align: right;">TOPE MÁXIMO DEL CICLO DE VENTA MENSUAL (%):</label>
            <div class="col-sm-7">
              <input type="number" class="form-control" step="any" id="ciclo_negocio_maximo" value="{{ configuracion($tienda->id,'ciclo_negocio_maximo')['valor'] }}">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-5 col-form-label" style="text-align: right;">(PARA EVALUACIÓN RESUMIDA)RELACIÓN CUOTA INGRESO(%):</label>
            <div class="col-sm-7">
              <input type="number" class="form-control" step="any" id="relacion_couta_ingreso" value="{{ configuracion($tienda->id,'relacion_couta_ingreso')['valor'] }}">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-5 col-form-label" style="text-align: right;">(PARA EVALUACIÓN RESUMIDA)RELACIÓN CUOTA VENTA(%):</label>
            <div class="col-sm-7">
              <input type="number" class="form-control" step="any" id="relacion_cuota_venta" value="{{ configuracion($tienda->id,'relacion_cuota_venta')['valor'] }}">
            </div>
          </div>
          <div class="mb-1">
            <span class="badge d-block" style="background-color: #878a8d;">Tope de Ratio Cuota Total/Excedente Total - Independiente</span>
          </div>
          <div class="row mt-1">
            <label class="col-sm-2 col-form-label" style="text-align: right;">RANGO MENOR (%):</label>
            <div class="col-sm-2">
              <input type="number" class="form-control" step="any" id="rango_menor" disabled value="{{ configuracion($tienda->id,'rango_menor')['valor'] }}">
            </div>
            <label class="col-sm-2 col-form-label" style="text-align: right;">DIFERENCIA (%):</label>
            <div class="col-sm-2">
              <input type="number" class="form-control" step="any" id="rango_diferencia" onkeyup="calc_rango_menor()" onkeydown="calc_rango_menor()" value="{{ configuracion($tienda->id,'rango_diferencia')['valor'] }}">
            </div>
            <label class="col-sm-2 col-form-label" style="text-align: right;">RANGO TOPE (%):</label>
            <div class="col-sm-2">
              <input type="number" class="form-control" step="any" id="rango_tope" onkeyup="calc_rango_menor()" onkeydown="calc_rango_menor()" value="{{ configuracion($tienda->id,'rango_tope')['valor'] }}">
            </div>
          </div>
          <div class="mb-1">
            <span class="badge d-block" style="background-color: #878a8d;">Tope de Ratio Cuota Total/Excedente Total - Dependiente</span>
          </div>
           <div class="row mt-1">
            <label class="col-sm-2 col-form-label" style="text-align: right;">RANGO TOPE (%):</label>
            <div class="col-sm-2">
              <input type="number" class="form-control" step="any" id="rango_tope_dependiente" value="{{ configuracion($tienda->id,'rango_tope_dependiente')['valor'] }}">
            </div>
          </div>
        </div>
        <script>
          function calc_rango_menor(){
            let rango_tope = parseFloat($('#rango_tope').val());
            let rango_diferencia = parseFloat($('#rango_diferencia').val());
            
            let rango_menor = rango_tope - rango_diferencia;
            $('#rango_menor').val(rango_menor.toFixed(2))
          }
        </script>
      </div>
      <div class="row mt-1 justify-content-center">
        <div class="col-sm-12 col-md-2">
          <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
        </div>
      </div>
      
    </div>
</form>    