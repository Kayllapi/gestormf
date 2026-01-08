<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/credito/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'deudas',
              entidad_regulada: json_entidad('table-credito-entidad-regulada'),
              entidad_noregulada: json_entidad('table-credito-entidad-noregulada'),
              linea_credito: json_linea_credito(),
              resumen: json_resumen(),
          }
      },
      function(res){
        removecarga({input:'#mx-carga'})
        $('#success-message').removeClass('d-none');
        $('#success-message').text(res.mensaje);
        setTimeout(function() {
          $('#success-message').addClass('d-none');
        }, 5000);
        lista_credito();
        load_nuevo_credito();
                
        $('#boton_imprimir').attr('disabled',false);
      },this)"> 

    @php
      $entidad_regulada = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->entidad_regulada == "" ? [] : json_decode($credito_cuantitativa_deudas->entidad_regulada) ) : [];
      $linea_credito = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->linea_credito == "" ? [] : json_decode($credito_cuantitativa_deudas->linea_credito) ) : [];
      $entidad_noregulada = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->entidad_noregulada == "" ? [] : json_decode($credito_cuantitativa_deudas->entidad_noregulada) ) : [];
      $resumen = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->resumen == "" ? [] : json_decode($credito_cuantitativa_deudas->resumen) ) : [];
  
      $ganancia_perdida = $credito_evaluacion_cuantitativa ? ( $credito_evaluacion_cuantitativa->ganancia_perdida == "" ? [] : json_decode($credito_evaluacion_cuantitativa->ganancia_perdida) ) : [];
    @endphp
    <div class="modal-header" style="border-bottom: 0;">
        <h5 class="modal-title">DEUDAS</h5>
        <button type="button" class="btn-close text-white" id="modal-close-garantia-cliente" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body modal-body-cualitativa">
      <div class="row">
        <div class="col-sm-12 col-md-5">
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">AGENCIA/OFICINA:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $tienda->nombreagencia }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">CLIENTE/RAZON SOCIAL:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $credito->nombreclientecredito }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">GIRO ECONÓMICO:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->nombregiro_economico_evaluacion : '' }}" disabled>
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-3">
          <div class="row">
            <label class="col-sm-3 col-form-label" style="text-align: right;">FECHA:</label>
            <div class="col-sm-7">
              <input type="date" step="any" class="form-control" value="{{ $credito_cuantitativa_deudas?date_format(date_create($credito_cuantitativa_deudas->fecha),'Y-m-d'):'' }}" id="fecha_desembolso" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label" style="text-align: right;">DNI/RUC:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->docuementocliente }}" disabled>
            </div>
          </div>
          @if($users_prestamo->dni_pareja!='' or $users_prestamo->nombrecompleto_pareja!='')
          <div class="row">
            <label class="col-sm-3 col-form-label" style="text-align: right;">DNI:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $users_prestamo->dni_pareja }}" disabled>
            </div>
          </div>
          @endif
          
          <div class="row">
            <label class="col-sm-3 col-form-label" style="text-align: right;">EJERCICIO:</label>
            <div class="col-sm-7">
              @if($users_prestamo->db_idforma_ac_economica!='')
                <input type="text" step="any" class="form-control" id="ejercicio_giro_economico" value="{{ $users_prestamo->db_idforma_ac_economica }}" disabled>
              @else
                <input type="text" step="any" class="form-control" id="ejercicio_giro_economico" value="{{ $users_prestamo->db_idforma_ac_economica }}" disabled>
              @endif
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-4">
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">NRO SOLICITUD:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="S{{ str_pad($credito->id, 8, '0', STR_PAD_LEFT)  }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">PRODUCTO:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->nombreproductocredito }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">TIPO DE CAMBIO:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" id="tipo_cambio_moneda" value="{{ configuracion($tienda->id,'tipo_cambio_dolar')['valor'] }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">TIPO DE CLIENTE:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->tipo_operacion_credito_nombre }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">MODALIDAD:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->modalidad_credito_nombre }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">DESTINO DE CRÉDITO:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->tipo_destino_credito_nombre}}" disabled>
            </div>
          </div>
        </div>


      
    </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block"> VI. DETALLE DE DEUDAS FINANCIERAS</span>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">6.1 Entidades Reguladas</span>
      </div>
      MODALIDAD CREDITO: {{ $credito->idmodalidad_credito }}
      <div class="row">
        <div class="col-sm-12">
          <table class="table table-bordered" id="table-credito-entidad-regulada">
            <thead>
              <tr>
                <th rowspan=3>TIPO DE CRÉDITO</th>
                <th rowspan=3>ENTIDAD FINANCIERA</th>
                <th rowspan=3 width="80px">DEUDOR</th>
                <th colspan=4>En moneda de origen (S/., $)</th>
                <th colspan=4>En Soles (S/.)</th>
                <th rowspan=2 colspan=2>DEDUCCIONES / COMPRA DE DEUDA O AMPLIACION (S/.)</th>
                
                @if($view_detalle!='false')
                <th rowspan=3>
                  <button type="button" class="btn btn-success" onclick="agrega_credito_entidad_regulada()"><i class="fa fa-plus"></i></button>
                </th>
                @endif 
              </tr>
              <tr>
                <th rowspan=2 width="60px">Moneda Soles(1) Dólar(2)</th>
                <th rowspan=2 width="100px">Saldo Capital</th>
                <th rowspan=2 width="80px">Plazo Pendiente (meses)</th>
                <th rowspan=2 width="60px">Cuota</th>
                <th rowspan=2 width="100px">Saldo Capital</th>
                <th rowspan=2 width="100px">Cuota </th>
                <th colspan=2>Saldo capital según cronograma</th>
              </tr>
              <tr>
                <th width="80px">Corto Plazo</th>
                <th width="80px">Largo Plazo</th>
                <th width="100px">SALDO CAPITAL</th>
                <th width="100px">CUOTA</th>
              </tr>
            </thead>
            <tbody>
              @foreach($entidad_regulada as $value)
                @php
                  $nombre_entidad = $value->tipo_entidad ? $tienda->nombre : $value->nombre_entidad ;
                @endphp
                <tr>
                  <td tipo_credito>
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" onchange="calcular_soles_entidad_regulada(this)">
                      @foreach($tipo_credito_evaluacion as $tipo_credito)
                        <option value="{{ $tipo_credito->id }}" {{ $tipo_credito->id == $value->id_tipo_credito ? "selected" : "" }} >{{ $tipo_credito->nombre }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td entidad>
                    <div class="input-group">
                      <input nombre_entidad type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" {{ $value->tipo_entidad ? 'disabled' : '' }}  value="{{ $nombre_entidad }}">
                      <div class="input-group-text">
                        <input tipo_entidad onclick="mostrar_endidad(this)" class="form-check-input mt-0" type="checkbox" {{ $view_detalle=='false' ? 'disabled' : '' }} {{ $value->tipo_entidad ? 'checked' : '' }}>
                      </div>
                    </div>
                  </td>
                  <td deudor>
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      <option value="Cliente" {{ $value->deudor == "Cliente" ? "selected" : "" }}>Cliente</option>
                      <option value="Pareja"  {{ $value->deudor == "Pareja" ? "selected" : "" }}>Pareja</option>
                      <option value="Empresa" {{ $value->deudor == "Empresa" ? "selected" : "" }}>Empresa</option>
                    </select>
                  </td>
                  <td moneda_origen onchange="calcular_soles_entidad_regulada(this)">
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      <option value="1" {{ $value->moneda_origen == "1" ? "selected" : "" }}>Soles</option>
                      <option value="2" {{ $value->moneda_origen == "2" ? "selected" : "" }}>Dolares</option>
                    </select>
                  </td>
                  <td saldo_capital_origen><input type="text" valida_input_vacio value="{{ $value->saldo_capital_origen }}" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" onkeyup="calcular_soles_entidad_regulada(this)"></td>
                  <td plazo_pendiente_origen><input type="text" valida_input_vacio value="{{ $value->plazo_pendiente_origen }}" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" onkeyup="calcular_soles_entidad_regulada(this)"></td>
                  <td cuota_origen><input type="text" valida_input_vacio value="{{ $value->cuota_origen }}" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" onkeyup="calcular_soles_entidad_regulada(this)"></td>

                  <td saldo_capital><input type="number" value="{{ $value->saldo_capital }}" class="form-control campo_moneda" disabled></td>
                  <td cuota><input type="number" value="{{ $value->cuota }}" class="form-control campo_moneda" disabled></td>
                  <td corto_plazo><input type="number" value="{{ $value->corto_plazo }}" class="form-control campo_moneda" disabled></td>
                  <td largo_plazo><input type="number" value="{{ $value->largo_plazo }}" class="form-control campo_moneda" disabled></td>

                  <td saldo_capital_deducciones><input type="text" valida_input_vacio value="{{ $value->saldo_capital_deducciones }}" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" onkeyup="calcular_soles_entidad_regulada(this)"></td>
                  <td cuota_deducciones><input type="text" valida_input_vacio value="{{ $value->cuota_deducciones }}" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" onkeyup="calcular_soles_entidad_regulada(this)"></td>
              
                @if($view_detalle!='false')
                <td><button type="button" onclick="eliminar_producto(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                @endif 
                 </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td class="color_totales" colspan=7 align="right">Sub Total Deuda</td>
                <td class="color_totales"><input type="number" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_saldo_capital : '0.00' }}" class="form-control campo_moneda" id="total_saldo_capital" disabled></td>
                <td class="color_totales"><input type="number" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_cuota : '0.00' }}" class="form-control campo_moneda" id="total_cuota" disabled></td>
                <td class="color_totales"><input type="number" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_corto_plazo : '0.00' }}" class="form-control campo_moneda" id="total_corto_plazo" disabled></td>
                <td class="color_totales"><input type="number" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_largo_plazo : '0.00' }}" class="form-control campo_moneda" id="total_largo_plazo" disabled></td>
                <td class="color_totales"><input type="text" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_saldo_capital_deducciones : '0.00' }}" class="form-control campo_moneda" id="total_saldo_capital_deducciones" disabled></td>
                <td class="color_totales"><input type="text" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_cuota_deducciones : '0.00' }}" class="form-control campo_moneda" id="total_cuota_deducciones" disabled></td>
              
                @if($view_detalle!='false')
                <td class="color_totales"></td>
                @endif 
              </tr>
            </tfoot>
          </table>
          <div id="error_entidad_regulada" class="alert alert-danger mt-2 d-none" style="background-color: #ff6666;border-color: #ff6666;color: #000;font-weight: bold;">Registrar en Deducciones/Compra de deuda o ampliación, respectiva.</div>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">Deudas de Líneas de Crédito(tarjetas) No Utilizadas</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-10">
          <table class="table table-bordered" id="table-linea-credito">
            <thead>
              <tr>
                <th rowspan=3>TIPO DE CRÉDITO</th>
                <th rowspan=3>ENTIDAD FINANCIERA</th>
                <th rowspan=3>DEUDOR</th>
                <th colspan=3>En moneda de origen (S/., $)</th>
                <th colspan=2>En Soles (S/.)</th>
                @if($view_detalle!='false')
                <th rowspan=2>
                  <button type="button" class="btn btn-success" onclick="agrega_linea_credito()"><i class="fa fa-plus"></i></button>
                </th>
                @endif 
              </tr>
              <tr>
                <th>Moneda Soles(1) Dólar(2)</th>
                <th>Línea de Crédito</th>
                <th>Cuota (24 meses)</th>
                
                <th>Línea de Crédito</th>
                <th>Cuota (24 meses) </th>
              </tr>
            </thead>
            <tbody>
              @foreach($linea_credito as $value)
                <tr >
                  <td tipo_credito>
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      @foreach($tipo_credito_evaluacion as $tipo_credito)
                        <option value="{{ $tipo_credito->nombre }}" {{ $tipo_credito->nombre == $value->tipo_credito ? "selected" : "" }} >{{ $tipo_credito->nombre }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td entidad><input type="text" class="form-control campo_moneda color_cajatexto" value="{{ $value->entidad }}"></td>
                  <td deudor>
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      <option value="Cliente" {{ $value->deudor == "Cliente" ? "selected" : "" }}>Cliente</option>
                      <option value="Pareja"  {{ $value->deudor == "Pareja" ? "selected" : "" }}>Pareja</option>
                      <option value="Empresa" {{ $value->deudor == "Empresa" ? "selected" : "" }}>Empresa</option>
                    </select>
                  </td>
                  <td moneda_origen onchange="calcular_soles_linea_credito(this)">
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      <option value="1" {{ $value->moneda_origen == "1" ? "selected" : "" }}>Soles</option>
                      <option value="2" {{ $value->moneda_origen == "2" ? "selected" : "" }}>Dolares</option>
                    </select>
                  </td>
                  <td linea_credito_origen><input type="text" valida_input_vacio value="{{ $value->linea_credito_origen }}" class="form-control campo_moneda color_cajatexto" onkeyup="calcular_soles_linea_credito(this)"></td>

                  <td coutas_origen><input type="number" value="{{ $value->coutas_origen }}" class="form-control campo_moneda" disabled></td>

                  <td linea_credito><input type="number" value="{{ $value->linea_credito }}" class="form-control campo_moneda" disabled></td>
                  <td cuota><input type="number" value="{{ $value->cuota }}" class="form-control campo_moneda" disabled></td>
                 @if($view_detalle!='false')
                <td><button type="button" onclick="eliminar_producto(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                @endif 
                  
                 </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td class="color_totales" colspan=6 align="right">Sub Total Deuda</td>
                <td class="color_totales"><input type="number" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_lc_linea_credito : '0.00' }}" class="form-control campo_moneda" id="total_lc_linea_credito" disabled></td>
                <td class="color_totales"><input type="number" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_lc_cuotas : '0.00' }}" class="form-control campo_moneda" id="total_lc_cuotas" disabled></td>
              
                @if($view_detalle!='false')
                <td class="color_totales"></td>
                @endif 
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">6.2 Entidades No Reguladas</span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <table class="table table-bordered" id="table-credito-entidad-noregulada">
            <thead>
              <tr>
                <th rowspan=3>TIPO DE CRÉDITO</th>
                <th rowspan=3>ENTIDAD FINANCIERA</th>
                <th rowspan=3 width="80px">DEUDOR</th>
                <th colspan=4>En moneda de origen (S/., $)</th>
                <th colspan=4>En Soles (S/.)</th>
                <th rowspan=2 colspan=2>DEDUCCIONES / COMPRA DE DEUDA O AMPLIACION (S/.)</th>
                
          
                @if($view_detalle!='false')
                <th rowspan=3>
                  <button type="button" class="btn btn-success" onclick="agrega_credito_entidad_noregulada()"><i class="fa fa-plus"></i></button>
                </th>
                @endif 
              </tr>
              <tr>
                <th rowspan=2 width="60px">Moneda Soles(1) Dólar(2)</th>
                <th rowspan=2 width="100px">Saldo Capital</th>
                <th rowspan=2 width="80px">Plazo Pendiente (meses)</th>
                <th rowspan=2 width="60px">Cuota</th>
                <th rowspan=2 width="100px">Saldo Capital</th>
                <th rowspan=2 width="100px">Cuota </th>
                <th colspan=2>Saldo capital según cronograma</th>
              </tr>
              <tr>
                <th width="80px">Corto Plazo</th>
                <th width="80px">Largo Plazo</th>
                <th width="100px">SALDO CAPITAL</th>
                <th width="100px">CUOTA</th>
              </tr>
            </thead>
            <tbody>
              @foreach($entidad_noregulada as $value)
                @php
                  $nombre_entidad_noregulada = $value->tipo_entidad ? $tienda->nombre : $value->nombre_entidad ;
                @endphp
                <tr>
                  <td tipo_credito>
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" onchange="calcular_soles_entidad_regulada()">
                      @foreach($tipo_credito_evaluacion as $tipo_credito)
                        <option value="{{ $tipo_credito->id }}" {{ $tipo_credito->id == $value->id_tipo_credito ? "selected" : "" }} >{{ $tipo_credito->nombre }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td entidad>
                    <div class="input-group">
                      <input nombre_entidad type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" {{ $value->tipo_entidad ? 'disabled' : '' }}  value="{{ $nombre_entidad_noregulada }}">
                      <div class="input-group-text">
                        <input tipo_entidad onclick="mostrar_endidad(this)" class="form-check-input mt-0" type="checkbox" {{ $view_detalle=='false' ? 'disabled' : '' }} {{ $value->tipo_entidad ? 'checked' : '' }}>
                      </div>
                    </div>
                  </td>
                  <td deudor>
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      <option value="Cliente" {{ $value->deudor == "Cliente" ? "selected" : "" }}>Cliente</option>
                      <option value="Pareja"  {{ $value->deudor == "Pareja" ? "selected" : "" }}>Pareja</option>
                      <option value="Empresa" {{ $value->deudor == "Empresa" ? "selected" : "" }}>Empresa</option>
                    </select>
                  </td>
                  <td moneda_origen onchange="calcular_soles_entidad_regulada(this)">
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      <option value="1" {{ $value->moneda_origen == "1" ? "selected" : "" }}>Soles</option>
                      <option value="2" {{ $value->moneda_origen == "2" ? "selected" : "" }}>Dolares</option>
                    </select>
                  </td>
                  <td saldo_capital_origen><input type="text" valida_input_vacio value="{{ $value->saldo_capital_origen }}" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" onkeyup="calcular_soles_entidad_regulada(this)"></td>
                  <td plazo_pendiente_origen><input type="text" valida_input_vacio value="{{ $value->plazo_pendiente_origen }}" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" onkeyup="calcular_soles_entidad_regulada(this)"></td>
                  <td cuota_origen><input type="text" valida_input_vacio value="{{ $value->cuota_origen }}" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" onkeyup="calcular_soles_entidad_regulada(this)"></td>

                  <td saldo_capital><input type="number" value="{{ $value->saldo_capital }}" class="form-control campo_moneda" disabled></td>
                  <td cuota><input type="number" value="{{ $value->cuota }}" class="form-control campo_moneda" disabled></td>
                  <td corto_plazo><input type="number" value="{{ $value->corto_plazo }}" class="form-control campo_moneda" disabled></td>
                  <td largo_plazo><input type="number" value="{{ $value->largo_plazo }}" class="form-control campo_moneda" disabled></td>

                  <td saldo_capital_deducciones><input type="text" valida_input_vacio value="{{ $value->saldo_capital_deducciones }}" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" onkeyup="calcular_soles_entidad_regulada(this)"></td>
                  <td cuota_deducciones><input type="text" valida_input_vacio value="{{ $value->cuota_deducciones }}" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda color_cajatexto" onkeyup="calcular_soles_entidad_regulada(this)"></td>

                @if($view_detalle!='false')
                <td><button type="button" onclick="eliminar_producto(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                @endif 
                 </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td class="color_totales" colspan=7 align="right">Sub Total Deuda</td>
                <td class="color_totales"><input type="number" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_noregulada_saldo_capital : '0.00' }}" class="form-control campo_moneda" id="total_noregulada_saldo_capital" disabled></td>
                <td class="color_totales"><input type="number" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_noregulada_cuota : '0.00' }}" class="form-control campo_moneda" id="total_noregulada_cuota" disabled></td>
                <td class="color_totales"><input type="number" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_noregulada_corto_plazo : '0.00' }}" class="form-control campo_moneda" id="total_noregulada_corto_plazo" disabled></td>
                <td class="color_totales"><input type="number" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_noregulada_largo_plazo : '0.00' }}" class="form-control campo_moneda" id="total_noregulada_largo_plazo" disabled></td>
                <td class="color_totales"><input type="text" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_noregulada_saldo_capital_deducciones : '0.00' }}" class="form-control campo_moneda" id="total_noregulada_saldo_capital_deducciones" disabled></td>
                <td class="color_totales"><input type="text" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_noregulada_cuota_deducciones : '0.00' }}" class="form-control campo_moneda" id="total_noregulada_cuota_deducciones" disabled></td>
               
                @if($view_detalle!='false')
                <td class="color_totales"></td>
                @endif
              </tr>
            </tfoot>
          </table>
          <div id="error_entidad_noregulada" class="alert alert-danger mt-2 d-none" style="background-color: #ff6666;border-color: #ff6666;color: #000;font-weight: bold;">Registrar en Deducciones/Compra de deuda o ampliación, respectiva.</div>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">RESUMEN:</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-12">
          <table class="table table-bordered" id="table-resumen">
            <thead>
              <tr>
                <th rowspan=3>TIPO DE CRÉDITO CONSOLIDADO</th>
                <th colspan=3>Entidades Reguladas</th>
                <th colspan=3>Entidades No Reguladas</th>
                
                <th colspan=4>TOTAL</th>
              </tr>
              <tr>
                <th colspan=2>Saldo Capital/Línea</th>
                
                <th rowspan=2>Cuota</th>
                <th colspan=2>Saldo Capital/Línea</th>
                
                <th rowspan=2>Cuota</th>
                <th colspan=3>Saldo Capital/Línea</th>
                <th rowspan=2>Cuota</th>
              </tr>
              <tr>
                <th>C. Plazo</th>
                <th>L. Plazo</th>
                <th>C. Plazo</th>
                <th>L. Plazo</th>
                <th>C. Plazo</th>
                <th>L. Plazo</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Créditos comerciales</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('comercial_er_cplazo', $resumen) }}" disabled id="comercial_er_cplazo" ></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('comercial_er_lplazo', $resumen) }}" disabled id="comercial_er_lplazo" ></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('comercial_er_couta', $resumen) }}" disabled id="comercial_er_couta" ></td>
                
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('comercial_enr_cplazo', $resumen) }}" disabled id="comercial_enr_cplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('comercial_enr_lplazo', $resumen) }}" disabled id="comercial_enr_lplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('comercial_enr_couta', $resumen) }}" disabled id="comercial_enr_couta"></td>
                
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('comercial_total_cplazo', $resumen) }}" disabled id="comercial_total_cplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('comercial_total_lplazo', $resumen) }}" disabled id="comercial_total_lplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('comercial_total', $resumen) }}" disabled id="comercial_total"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('comercial_total_couta', $resumen) }}" disabled id="comercial_total_couta"></td>
              </tr>
              <tr>
                <td>Créditos MES</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('mes_er_cplazo', $resumen) }}" disabled id="mes_er_cplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('mes_er_lplazo', $resumen) }}" disabled id="mes_er_lplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('mes_er_couta', $resumen) }}" disabled id="mes_er_couta"></td>
                
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('mes_enr_cplazo', $resumen) }}" disabled id="mes_enr_cplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('mes_enr_lplazo', $resumen) }}" disabled id="mes_enr_lplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('mes_enr_couta', $resumen) }}" disabled id="mes_enr_couta"></td>
                
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('mes_total_cplazo', $resumen) }}" disabled id="mes_total_cplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('mes_total_lplazo', $resumen) }}" disabled id="mes_total_lplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('mes_total', $resumen) }}" disabled id="mes_total"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('mes_total_couta', $resumen) }}" disabled id="mes_total_couta"></td>
              </tr>
              <tr>
                <td>Créditos de consumo</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('consumo_er_cplazo', $resumen) }}" disabled id="consumo_er_cplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('consumo_er_lplazo', $resumen) }}" disabled id="consumo_er_lplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('consumo_er_couta', $resumen) }}" disabled id="consumo_er_couta"></td>
                
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('consumo_enr_cplazo', $resumen) }}" disabled id="consumo_enr_cplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('consumo_enr_lplazo', $resumen) }}" disabled id="consumo_enr_lplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('consumo_enr_couta', $resumen) }}" disabled id="consumo_enr_couta"></td>
                
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('consumo_total_cplazo', $resumen) }}" disabled id="consumo_total_cplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('consumo_total_lplazo', $resumen) }}" disabled id="consumo_total_lplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('consumo_total', $resumen) }}" disabled id="consumo_total"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('consumo_total_couta', $resumen) }}" disabled id="consumo_total_couta"></td>
              </tr>
              <tr>
                <td>Créditos hipotecarios para vivienda</td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('vivienda_er_cplazo', $resumen) }}" disabled id="vivienda_er_cplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('vivienda_er_lplazo', $resumen) }}" disabled id="vivienda_er_lplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('vivienda_er_couta', $resumen) }}" disabled id="vivienda_er_couta"></td>
                
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('vivienda_enr_cplazo', $resumen) }}" disabled id="vivienda_enr_cplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('vivienda_enr_lplazo', $resumen) }}" disabled id="vivienda_enr_lplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('vivienda_enr_couta', $resumen) }}" disabled id="vivienda_enr_couta"></td>
                
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('vivienda_total_cplazo', $resumen) }}" disabled id="vivienda_total_cplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('vivienda_total_lplazo', $resumen) }}" disabled id="vivienda_total_lplazo"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('vivienda_total', $resumen) }}" disabled id="vivienda_total"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('vivienda_total_couta', $resumen) }}" disabled id="vivienda_total_couta"></td>
              </tr>
              
            </tbody>
            <tfoot>
              <tr totales>
                <td class="color_totales" align="center"><b>TOTAL</b></td>
                <td class="color_totales"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('total_er_cplazo', $resumen) }}" disabled id="total_er_cplazo"></td>
                <td class="color_totales"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('total_er_lplazo', $resumen) }}" disabled id="total_er_lplazo"></td>
                <td class="color_totales"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('total_er_couta', $resumen) }}" disabled id="total_er_couta"></td>
                <td class="color_totales"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('total_enr_cplazo', $resumen) }}" disabled id="total_enr_cplazo"></td>
                <td class="color_totales"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('total_enr_lplazo', $resumen) }}" disabled id="total_enr_lplazo"></td>
                <td class="color_totales"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('total_enr_couta', $resumen) }}" disabled id="total_enr_couta"></td>
                <td class="color_totales"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('total_total_cplazo', $resumen) }}" disabled id="total_total_cplazo"></td>
                <td class="color_totales"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('total_total_lplazo', $resumen) }}" disabled id="total_total_lplazo"></td>
                <td class="color_totales"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('total_resumen', $resumen) }}" disabled id="total_resumen"></td>
                <td class="color_totales"><input type="text" class="form-control campo_moneda" value="{{ encontrar_valor('total_total_couta', $resumen) }}" disabled id="total_total_couta"></td>
              </tr>
              <tr>
                 <td colspan=11></td>
              </tr>
              <tr nosumar>
                <td class="color_totales" align="center"><b>Líneas de Crédito(tarjetas) No Utilizadas</b></td>
                <td class="color_totales" colspan="2"><input type="text" class="form-control campo_moneda" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_resumen_linea_credito : '0.00' }}" disabled id="total_resumen_linea_credito"></td>
                <td class="color_totales"><input type="text" class="form-control campo_moneda" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_resumen_cuotas_linea_credito : '0.00' }}" disabled id="total_resumen_cuotas_linea_credito"></td>
                <td class="color_totales"></td>
                <td class="color_totales"></td>
                <td class="color_totales"></td>
                <td class="color_totales"></td>
                <td class="color_totales"></td>
                <td class="color_totales"></td>
                <td class="color_totales"><input type="text" class="form-control campo_moneda" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->total_resumen_cuotas_linea_credito2 : '0.00' }}" disabled id="total_resumen_cuotas_linea_credito2"></td>
              </tr>
            </tfoot>
            
          </table>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;color: #000;">6.3 Propuesta de Financiamiento</span>
      </div>
      <div class="row d-none">
        <div class="col-sm-12 col-md-6">
          <table class="table">
            <thead>
              <tr>
                <th width="200px">SOLICITADO</th>
                <th>MONTO</th>
                <th>FRECUENCIA</th>
                <th>CUOTAS</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td></td>
                <td><input type="text" class="form-control campo_moneda" id="monto_solicitado" value="{{ $credito->monto_solicitado }}" disabled></td>
                <td><input type="text" class="form-control" id="frecuencia_pago" value="{{ $credito->forma_pago_credito_nombre }}" disabled></td>
                <td><input type="text" class="form-control text-center" id="coutas_credito" value="{{ $credito->cuotas }}" disabled></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-sm-12 col-md-6">
        <button type="button" class="btn btn-warning me-1" 
                onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=cronograma')}}', size: 'modal-fullscreen' })">EDITAR CRONOGRAMA</button>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-sm-12">
          <table class="table table-bordered" id="table-propuesta">
            <thead>
              <tr>
                <th colspan=10 class="text-center">PROPUESTA</th>
              </tr>
              <tr>
                <th rowspan=2>DESTINO DE CRÉDITO</th>
                <th rowspan=2>Producto</th>
                <th colspan=2>Plazo</th>
                <th rowspan=2>FORMA DE PAGO</th>
                <th rowspan=2>Monto Préstamo (S/.)</th>
                <th rowspan=2>TEM</th>
                
                <th rowspan=2>Servicios/Otros (S/.)</th>
                <th rowspan=2>Cargos (S/.)</th>
                <th rowspan=2 >Cuota de Pago <span id="nombre_frecuencia_pago">Diario</span> (S/.)</th>
              </tr>
              <tr>
                <th>Pago</th>
                <th>Cuotas</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><input type="text" disabled class="form-control" id="propuesta_objetivo" value="{{ $credito->tipo_destino_credito_nombre}}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $credito->nombreproductocredito }}"></td>
                <td>
                  <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control" id="idforma_pago_credito" disabled>
                    @foreach($forma_pago_credito as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                  </select>
                </td> 
                <td>
                  
                  <input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control text-center" valida_input_vacio 
                         value="{{ $credito->cuotas }}" 
                         id="propuesta_cuotas" disabled>
                </td>
                <td>
                  <select class="form-control" id="propuesta_forma_pago" disabled>
                    <option value="1">Cuota Fija</option>
                  </select>
                </td>
                <td>
                  <input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda" valida_input_vacio 
                         value="{{ $credito->monto_solicitado }}" 
                         id="propuesta_monto" disabled>
                </td>
                <td>
                  <div class="input-group">
                    <input type="text" step="any" id="propuesta_tem" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control campo_moneda" 
                           value="{{ $credito->tasa_tem }}" minimo="{{ $credito->tasa_tem }}" disabled>
                    <span class="input-group-text">%</span>
                  </div>
                </td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito->cuota_comision }}" disabled id="propuesta_servicio_otros"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito->cuota_cargo }}" disabled id="propuesta_cargos"></td>
                <td><input type="text" class="form-control campo_moneda" value="{{ $credito->cuota_pago }}" disabled id="propuesta_total_pagar"></td>
              </tr>
            </tbody>
            
            <tfoot>
              <tr>
                <td class="color_totales" colspan=9 align="right">PAGO MES (S/.)</td>
                <td class="color_totales"><input type="text" class="form-control campo_moneda" value="{{ $credito->total_propuesta }}" id="total_propuesta" disabled></td>
              </tr>
              <tr>
                <td colspan=10 class="text-center" id="mensaje_error_cronograma"></td>
              </tr>
            </tfoot>
          </table>
          <script>
            /*showtasa();
              function showtasa(valid=0){
    
                let monto       = $('#propuesta_monto').val();
                let numerocuota = $('#propuesta_cuotas').val();
                let frecuencia  = $('#idforma_pago_credito').val();
                
                if(monto==''){
                    return false;
                }
                if(numerocuota==''){
                    return false;
                }
                $.ajax({
                  url:"{{url('backoffice/0/credito/showtasa')}}",
                  type:'GET',
                  data: {
                      monto: monto,
                      numerocuota: numerocuota,
                      tasa: '',
                      frecuencia: frecuencia,
                      idcredito: '{{ $credito->id }}'
                  },
                  success: function (res){
                    $('#propuesta_tem').attr('minimo',res.tasa_tem_minima);
                    
                    cronograma();
                    if(valid==1){
                        $('#propuesta_tem').val(res.tasa_tem_minima);
                    }
                    calcular_riesgo_empresa();
                  },
                  complete: function(res){
                    
                  }
                })
             }*/
          </script>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-sm-12 col-md-10">
          <table class="table">
            <tbody>
              <tr>
                <td style="border: 1px solid #a6a9ab;" width="600px">RIESGOS TOTAL PROYECTADO EN: {{ $tienda->nombre }} (S/.)</td>
                <td style="border: 1px solid #a6a9ab;" width="100px"><input type="text" class="form-control campo_moneda" disabled value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->riesgo_proyectado_empresa : '0.00' }}" id="riesgo_proyectado_empresa"></td>
                <td style="background-color: #efefef;"></td>
              </tr>
              <tr>
                <td style="border: 1px solid #a6a9ab;">RIESGO TOTAL PROYECTADO EN: TODO SISTEMA FINANCIERO (S/.)</td>
                <td style="border: 1px solid #a6a9ab;"><input type="text" class="form-control campo_moneda" disabled value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->riesgo_proyectado_todos : '0.00' }}" id="riesgo_proyectado_todos"></td>
                <td style="background-color: #efefef;"></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-sm-12 col-md-6 d-none">
          <table class="table">
            <tbody>
              <tr class="d-none">
                <td>Rango Menor</td>
                <td>Diferencia</td>
                <td>Rango Tope</td>
              </tr>
              <tr class="d-none">
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan=3>
                  
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="row mt-1">
        
        @if($view_detalle!='false')
        <div class="col" style="flex: 0 0 0%;">
          <button type="submit" class="btn btn-success" ><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
        </div>
        @endif
        <div class="col" style="flex: 0 0 0%;">
          <button type="button" 
                  class="btn btn-dark" 
                  onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=solicitud_deudas')}}', size: 'modal-fullscreen' })"
                  id="boton_imprimir"
                  >
            <i class="fa-solid fa-file-pdf"></i> IMPRIMIR</button>
        </div>
        <div class="col" style="flex: 1 0 0%;">
          <div id="success-message" class="alert alert-success d-none" style="text-align:left;"></div>
        </div>
        <div class="col" style="flex: 0 0 0%;">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i> SALIR</button>
        </div>
      </div>
</form> 
  
  <input type="hidden" class="form-control" id="estado_regulada_noregulada">
<style>
  /*
  {{ $view_detalle=='false' ? '' : '.modal-body-cualitativa .form-check-input[type=checkbox],
  .modal-body-cualitativa .select2-container--bootstrap-5 .select2-selection {
      background-color: #ffffb5;
  }' }}*/
  
  .form-check-input:checked {
      background-color: #585858 !important;
      border-color: #585858 !important;
  }
</style>  
<script>
  $('input').on('blur', function() {
      $('#boton_imprimir').attr('disabled',true);
  });
  $('#propuesta_tem').inputmask("decimal", {
    digits  : 2,
    placeholder : "0.00",
    allowMinus : false,
    allowPlus : false,
    max : 9999999999999991,
    digitsOptional : false
  });
  $('#propuesta_tem').on('click', function() {
    $(this).select();
  });
  $('#propuesta_tem').on('blur', function() {
    valida_tem()
  });
  $('#propuesta_tem').on('keydown', function() {
    setTimeout(function() {
      valida_tem();
    }, 1000);
  });
  /*calcular_excedente();
  function calcular_excedente(){
    let total_cuota = parseFloat($('#total_cuota').val());
    let total_lc_cuotas = parseFloat($('#total_lc_cuotas').val());
    let total_noregulada_cuota = parseFloat($('#total_noregulada_cuota').val());
    let evaluacion_actual_ganancia_excedente_mensual = parseFloat("{{ encontrar_valor('evaluacion_actual_ganancia_excedente_mensual', $ganancia_perdida) }}");

    let suma_cuotas = ( total_cuota + total_lc_cuotas + total_noregulada_cuota );
    let excedente_antes_propuesta = (suma_cuotas / ( evaluacion_actual_ganancia_excedente_mensual + suma_cuotas )) * 100;
    $('#excedente_antes_propuesta').val(excedente_antes_propuesta.toFixed(2));


    let total_propuesta = parseFloat($('#total_propuesta').val());
    let excedente_propuesta_sin_deduccion = ( (total_propuesta + suma_cuotas ) / ( evaluacion_actual_ganancia_excedente_mensual + suma_cuotas )  ) * 100;
    $('#excedente_propuesta_sin_deduccion').val(excedente_propuesta_sin_deduccion.toFixed(2));

    let total_cuota_deducciones = parseFloat($('#total_cuota_deducciones').val());
    let total_noregulada_cuota_deducciones = parseFloat($('#total_noregulada_cuota_deducciones').val());
    let suma_deducciones = total_cuota_deducciones + total_noregulada_cuota_deducciones;

    let excedente_propuesta_con_deduccion = ( ( (total_propuesta + suma_cuotas ) - suma_deducciones ) / ( evaluacion_actual_ganancia_excedente_mensual + suma_cuotas - suma_deducciones ) ) * 100;
    $('#excedente_propuesta_con_deduccion').val(excedente_propuesta_con_deduccion.toFixed(2));


    evaluarCredito();
  }*/

  function valida_tem(){
    let minimo = parseFloat($('#propuesta_tem').attr('minimo'));
    if ($('#propuesta_tem').val() === "" || parseFloat($('#propuesta_tem').val()) < minimo) {
      $('#propuesta_tem').val(minimo.toFixed(2));
    }
  }
  valida_input_vacio();
  sistema_select2({ input:'#idforma_pago_credito' });
  $("#idforma_pago_credito").on("change", function(e) {
    var selectedOption = $(this).select2('data')[0];
    var selectedText = selectedOption.text;
    $('#nombre_frecuencia_pago').text(selectedText);
  }).val('{{ $credito->idforma_pago_credito }}').trigger('change');
  
  $("#idforma_pago_credito").on("select2:select", function(e) {
    showtasa(1)
  });

  function cronograma(){
          
    let monto       = parseFloat($('#propuesta_monto').val());
    let numerocuota = parseFloat($('#propuesta_cuotas').val());
    let fechainicio = $('#fecha_desembolso').val();
    let frecuencia  = $('#idforma_pago_credito').val();
    let dia_gracia  = {{ $credito->dia_gracia }};

    let cargo       = $('#propuesta_cargos').val();

    //let tasa        = {{ $credito->tasa_tem }};
    let tasa        = $('#propuesta_tem').val();
    let tipotasa    = "{{$credito->modalidad_calculo}}" == 'Interes Simple' ? 1 : 2;
    let error_cronograma = '';
    $('#mensaje_error_cronograma').text(error_cronograma);
    $('#mensaje_error_cronograma').removeClass('bg-danger');
    if( monto <= 0 ){
        //alert("Monto de Prestamo debe ser mayor a 0.00.");
        error_cronograma = "Monto de Prestamo debe ser mayor a 0.00.";
        return false;
    }
    if(numerocuota<=0){
        //alert("El Número de Cuotas debe ser mayor a 0.");
        error_cronograma = "El Número de Cuotas debe ser mayor a 0.";
        return false;
    }

    if(dia_gracia<0){
        
        //alert("El día de gracia debe ser mayor o igual a 0!!.");
        error_cronograma = "El día de gracia debe ser mayor o igual a 0!!.";
        return false;
    }
    if(dia_gracia > {{$diasdegracia}}){
        //alert("Máximo puede poner {{$diasdegracia}} días de gracia!!.");
        error_cronograma = "Máximo puede poner {{$diasdegracia}} días de gracia!!.";
        return false;
    }

    if(monto=='' || numerocuota=='' || fechainicio=='' || frecuencia==''){
        error_cronograma = "Complete los campos";
        
        return false;
    }
    $('#mensaje_error_cronograma').text(error_cronograma);
    $('#mensaje_error_cronograma').addClass('bg-danger');

    $.ajax({
        url:"{{url('backoffice/0/credito/cronograma')}}",
        type: 'GET',
        data: {
            monto: monto,
            numerocuota: numerocuota,
            fechainicio: fechainicio,
            frecuencia: frecuencia,
            tasa: tasa,
            tipotasa: tipotasa,
            dia_gracia: dia_gracia,
            cargo: cargo,
            idcredito: '{{ $credito->id }}'
        },
        success: function (res) {
          $('#mensaje_error_cronograma').text('');
          $('#mensaje_error_cronograma').removeClass('bg-danger');
          if(res.resultado=='ERROR'){
            $('#mensaje_error_cronograma').addClass('bg-danger');
            $('#mensaje_error_cronograma').text(res.mensaje);
          }
          else{
            $('#mensaje_error_cronograma').text('');
            $('#mensaje_error_cronograma').removeClass('bg-danger');
            calcular_pago_mensual(res,numerocuota);
            let servicios_otros =  ( parseFloat(monto) * parseFloat(res.cargootros)) / 100;
            $('#propuesta_servicio_otros').val(servicios_otros.toFixed(2));
            
          }
        }
    });
  }
  function calcular_pago_mensual(res,numerocuota){

    let frecuencia_credito = parseFloat($('#idforma_pago_credito').val());
    
    let propuesta_total_pagar = parseFloat(res.total_pagar)/parseFloat(numerocuota);
    propuesta_total_pagar = parseFloat(propuesta_total_pagar.toFixed(1))
    $('#propuesta_total_pagar').val(propuesta_total_pagar.toFixed(2));
    let total_propuesta = 0;
    if (frecuencia_credito === 1) {
      total_propuesta = propuesta_total_pagar * 26;
    }
    else if ( frecuencia_credito === 2) {
      total_propuesta = propuesta_total_pagar * 4.29;
    }
    else if ( frecuencia_credito === 3) {
      total_propuesta = propuesta_total_pagar * 2;
    }
    else if ( frecuencia_credito === 4) {
      total_propuesta = propuesta_total_pagar;
    }
    $('#total_propuesta').val(total_propuesta.toFixed(2))
    //calcular_excedente();
  }
  function mostrar_endidad(e) {
    let path = $(e).closest('td[entidad]');
    let estado_check = $(e).prop("checked");
    if(estado_check){
        $(path).find('input[nombre_entidad]').attr('disabled',true);
        $(path).find('input[nombre_entidad]').val('{{ $tienda->nombre }}');
    }
    else{
        $(path).find('input[nombre_entidad]').attr('disabled',false);
        $(path).find('input[nombre_entidad]').val('');
    }
  }
  function agrega_credito_entidad_regulada(){

    let btn_eliminar = `<button type="button" onclick="eliminar_producto(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button>` ;
    let option_select = ``;
    @foreach($tipo_credito_evaluacion as $value)
      option_select += `<option value="{{ $value->id }}">{{ $value->nombre }}</option>`
    @endforeach
    let tabla = `<tr >
                  <td tipo_credito>
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto" onchange="calcular_soles_entidad_regulada()">
                      <option></option>
                      ${option_select}
                    </select>
                  </td>
                  <td entidad>
                    <div class="input-group">
                      <input nombre_entidad type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      <div class="input-group-text">
                        <input tipo_entidad onclick="mostrar_endidad(this)" class="form-check-input mt-0" type="checkbox" value="" >
                      </div>
                    </div>
                  </td>
                  <td deudor>
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      <option></option>
                      <option value="Cliente">Cliente</option>
                      <option value="Pareja">Pareja</option>
                      <option value="Empresa">Empresa</option>
                    </select>
                  </td>
                  <td moneda_origen onchange="calcular_soles_entidad_regulada(this)">
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      <option></option>
                      <option value="1">Soles</option>
                      <option value="2">Dolares</option>
                    </select>
                  </td>
                  <td saldo_capital_origen><input type="text" valida_input_vacio value="0.00" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcular_soles_entidad_regulada(this)"></td>
                  <td plazo_pendiente_origen><input type="text" valida_input_vacio value="0.00" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcular_soles_entidad_regulada(this)"></td>
                  <td cuota_origen><input type="text" valida_input_vacio value="0.00" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcular_soles_entidad_regulada(this)"></td>

                  <td saldo_capital><input type="number" value="0.00" class="form-control campo_moneda" disabled></td>
                  <td cuota><input type="number" value="0.00" class="form-control campo_moneda" disabled></td>
                  <td corto_plazo><input type="number" value="0.00" class="form-control campo_moneda" disabled></td>
                  <td largo_plazo><input type="number" value="0.00" class="form-control campo_moneda" disabled></td>

                  <td saldo_capital_deducciones><input type="text" valida_input_vacio value="0.00" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcular_soles_entidad_regulada(this)"></td>
                  <td cuota_deducciones><input type="text" valida_input_vacio value="0.00" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcular_soles_entidad_regulada(this)"></td>

                  <td>${btn_eliminar}</td>
                 </tr>`;

      $("#table-credito-entidad-regulada > tbody").append(tabla);
      valida_input_vacio();


  }
  function eliminar_producto(e){
    let path = $(e).closest('tr');
    path.remove();
    calcula_total('#table-credito-entidad-regulada')
    calcula_total('#table-credito-entidad-noregulada')
    calcula_total_linea_credito();
  }
  function calcular_soles_entidad_regulada(e){
    let path = $(e).closest('tr');
    let moneda_origen = parseFloat($(path).find('td[moneda_origen] select').val());
    let saldo_capital_origen = parseFloat($(path).find('td[saldo_capital_origen] input').val());
    let plazo_pendiente_origen = parseFloat($(path).find('td[plazo_pendiente_origen] input').val());
    let cuota_origen = parseFloat($(path).find('td[cuota_origen] input').val());
    let tipo_cambio_moneda = parseFloat($('#tipo_cambio_moneda').val());
    let saldo_capital = moneda_origen == 1 ? saldo_capital_origen : saldo_capital_origen * tipo_cambio_moneda;
    let cuota = moneda_origen == 1 ? cuota_origen : cuota_origen * tipo_cambio_moneda;
    let corto_plazo = 0;
    if(plazo_pendiente_origen <= 12){
      corto_plazo = saldo_capital;
    }
    else if(plazo_pendiente_origen > 12){
      corto_plazo = saldo_capital/plazo_pendiente_origen * 12;
    }
    let largo_plazo = saldo_capital - corto_plazo;
    $(path).find('td[saldo_capital] input').val(saldo_capital.toFixed(2));
    $(path).find('td[cuota] input').val(cuota.toFixed(2));
    $(path).find('td[corto_plazo] input').val(corto_plazo.toFixed(2));
    $(path).find('td[largo_plazo] input').val(largo_plazo.toFixed(2));
    calcula_total('#table-credito-entidad-regulada')
    calcula_total('#table-credito-entidad-noregulada')
    //calcular_excedente();
  }
  var estado_btn_save = true;
  var estado_regulada = true; // NO BORRAR SE USAN EN calcula_total()
  var estado_noregulada = true; // NO BORRAR SE USAN EN calcula_total()
  function calcula_total(table){
    var estado_regulada_noregulada = '';
    let saldo_capital = 0;
    let cuota = 0;
    let corto_plazo = 0;
    let largo_plazo = 0;
    let saldo_capital_deducciones = 0;
    let cuota_deducciones = 0;
    let modalidad_credito = parseFloat("{{ $credito->idmodalidad_credito }}");
    
    $(`${table} > tbody > tr`).each(function() {
      let check_empresa = $(this).find('td[entidad] input[tipo_entidad]').prop("checked");
      saldo_capital += parseFloat($(this).find('td[saldo_capital] input').val());
      cuota += parseFloat($(this).find('td[cuota] input').val());
      corto_plazo += parseFloat($(this).find('td[corto_plazo] input').val());
      largo_plazo += parseFloat($(this).find('td[largo_plazo] input').val());
      saldo_capital_deducciones += parseFloat($(this).find('td[saldo_capital_deducciones] input').val());
      cuota_deducciones += parseFloat($(this).find('td[cuota_deducciones] input').val());
    });

    if(table == "#table-credito-entidad-regulada"){
      $('#total_saldo_capital').val(saldo_capital.toFixed(2))
      $('#total_cuota').val(cuota.toFixed(2))
      $('#total_corto_plazo').val(corto_plazo.toFixed(2))
      $('#total_largo_plazo').val(largo_plazo.toFixed(2))
      $('#total_saldo_capital_deducciones').val(saldo_capital_deducciones.toFixed(2))
      $('#total_cuota_deducciones').val(cuota_deducciones.toFixed(2))
      if( ( modalidad_credito == 2 || modalidad_credito == 3 ) && (saldo_capital_deducciones == 0 || cuota_deducciones == 0)){
        estado_regulada = false;
      }else{
        estado_regulada = true;
      }
      calcular_riesgo_empresa()
    }
    else if(table == "#table-credito-entidad-noregulada"){
      $('#total_noregulada_saldo_capital').val(saldo_capital.toFixed(2))
      $('#total_noregulada_cuota').val(cuota.toFixed(2))
      $('#total_noregulada_corto_plazo').val(corto_plazo.toFixed(2))
      $('#total_noregulada_largo_plazo').val(largo_plazo.toFixed(2))
      $('#total_noregulada_saldo_capital_deducciones').val(saldo_capital_deducciones.toFixed(2))
      $('#total_noregulada_cuota_deducciones').val(cuota_deducciones.toFixed(2))
      
      if( ( modalidad_credito == 2 || modalidad_credito == 3 ) && (saldo_capital_deducciones == 0 || cuota_deducciones == 0)){
        //$("#error_entidad_noregulada").removeClass('d-none')
        estado_noregulada = false;
      }else{
        estado_noregulada = true;
        //$("#error_entidad_noregulada").addClass('d-none')
      }
      
    }
    calcular_resumen();
    if(estado_regulada == false && estado_noregulada == false){
      $("#error_entidad_regulada").removeClass('d-none')
      $("#error_entidad_noregulada").removeClass('d-none')
      $('#btn-guardar-cambios-deudas').attr('disabled',false)
      estado_btn_save = true;
      // validar boton
      let estado_credito = $('#estado_credito').val();
      if(estado_credito == 'CREDITO VIABLE'){
         $('#btn-guardar-cambios-deudas').attr('disabled',false)
      }else{
        $('#btn-guardar-cambios-deudas').attr('disabled',true)
      }
      estado_regulada_noregulada = 'ERROR';
    }else{
      $("#error_entidad_regulada").addClass('d-none')
      $("#error_entidad_noregulada").addClass('d-none')
      $('#btn-guardar-cambios-deudas').attr('disabled',true)
      estado_btn_save = false;
      estado_regulada_noregulada = 'CORRECTO';
    }
    
    $('#estado_regulada_noregulada').val(estado_regulada_noregulada)
    
  }
  
  calcular_riesgo_empresa();
  function calcular_riesgo_empresa(){
    let total_empresa = 0;
    let total_deducciones = 0;
    let propuesta_monto = parseFloat($('#propuesta_monto').val());
    let total_saldo_capital = parseFloat($('#total_saldo_capital').val());
    let total_noregulada_saldo_capital = parseFloat($('#total_noregulada_saldo_capital').val());
    
    let total_saldo_capital_deducciones = parseFloat($('#total_saldo_capital_deducciones').val());
    let total_noregulada_saldo_capital_deducciones = parseFloat($('#total_noregulada_saldo_capital_deducciones').val());
    
    $(`#table-credito-entidad-noregulada > tbody > tr`).each(function() {
      let check_empresa = $(this).find('td[entidad] input[tipo_entidad]').prop("checked");
      if(check_empresa){
         total_empresa += parseFloat($(this).find('td[saldo_capital] input').val())
         total_deducciones += parseFloat($(this).find('td[saldo_capital_deducciones] input').val())
      }
    });
    
    let total_riesgo = (total_empresa + propuesta_monto) - total_deducciones;
    $('#riesgo_proyectado_empresa').val(total_riesgo.toFixed(2));
    
    let riesgo_proyectado_todos = ( total_saldo_capital + total_noregulada_saldo_capital + propuesta_monto ) - total_saldo_capital_deducciones - total_noregulada_saldo_capital_deducciones;
    $('#riesgo_proyectado_todos').val(riesgo_proyectado_todos.toFixed(2));
    
    calcular_excedente();
  }
  
  /*function evaluarCredito() {
    let excedente_propuesta_con_deduccion = parseFloat($('#excedente_propuesta_con_deduccion').val());
    
    let rango_menor = parseFloat($('#rango_menor').val());
    let rango_diferencia = parseFloat($('#rango_diferencia').val());
    let rango_tope = parseFloat($('#rango_tope').val());
    let estado_credito = '';
    if (excedente_propuesta_con_deduccion < 0) {
      estado_credito = "CREDITO NO VIABLE";
      $('#estado_credito').removeClass('bg-success');
      $('#estado_credito').addClass('bg-danger');
      
      $('#excedente_propuesta_con_deduccion').removeClass('bg-success');
      $('#excedente_propuesta_con_deduccion').addClass('bg-danger');
      $('#btn-guardar-cambios-deudas').attr('disabled',true)
    } else if (excedente_propuesta_con_deduccion <= rango_tope) {
      estado_credito = "CREDITO VIABLE";
      $('#estado_credito').removeClass('bg-danger');
      $('#estado_credito').addClass('bg-success');
      $('#excedente_propuesta_con_deduccion').removeClass('bg-danger');
      $('#excedente_propuesta_con_deduccion').addClass('bg-success');
      $('#btn-guardar-cambios-deudas').attr('disabled',false)
      // validar boton
      let estado_regulada_noregulada =$('#estado_regulada_noregulada').val();
      if(estado_regulada_noregulada == 'ERROR'){
        $('#btn-guardar-cambios-deudas').attr('disabled',true)
      }else{
        $('#btn-guardar-cambios-deudas').attr('disabled',false)
      }
    } else if (excedente_propuesta_con_deduccion > rango_tope){
      estado_credito = "CREDITO NO VIABLE";
      $('#estado_credito').removeClass('bg-success');
      $('#estado_credito').addClass('bg-danger');
      $('#excedente_propuesta_con_deduccion').removeClass('bg-success');
      $('#excedente_propuesta_con_deduccion').addClass('bg-danger');
      $('#btn-guardar-cambios-deudas').attr('disabled',true)
    }
    $('#estado_credito').val(estado_credito);
  }*/

  function agrega_linea_credito(){
    let btn_eliminar = `<button type="button" onclick="eliminar_producto(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button>` ;
    let option_select = ``;
    @foreach($tipo_credito_evaluacion as $value)
      option_select += `<option value="{{ $value->nombre }}">{{ $value->nombre }}</option>`
    @endforeach
    let tabla = `<tr >
                  <td tipo_credito>
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      <option></option>
                      ${option_select}
                    </select>
                  </td>
                  <td entidad><input type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto"></td>
                  <td deudor>
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      <option></option>
                      <option value="Cliente">Cliente</option>
                      <option value="Pareja">Pareja</option>
                      <option value="Empresa">Empresa</option>
                    </select>
                  </td>
                  <td moneda_origen onchange="calcular_soles_linea_credito(this)">
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      <option></option>
                      <option value="1">Soles</option>
                      <option value="2">Dolares</option>
                    </select>
                  </td>
                  <td linea_credito_origen><input type="text" valida_input_vacio value="0.00" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcular_soles_linea_credito(this)"></td>

                  <td coutas_origen><input type="number" value="0.00" class="form-control campo_moneda" disabled></td>

                  <td linea_credito><input type="number" value="0.00" class="form-control campo_moneda" disabled></td>
                  <td cuota><input type="number" value="0.00" class="form-control campo_moneda" disabled></td>
                 
                  <td>${btn_eliminar}</td>
                 </tr>`;

      $("#table-linea-credito > tbody").append(tabla);
    valida_input_vacio();
  }
  function calcular_soles_linea_credito(e){
    let path = $(e).closest('tr');
    let moneda_origen = parseFloat($(path).find('td[moneda_origen] select').val());
    let tipo_cambio_moneda = parseFloat($('#tipo_cambio_moneda').val());
    let linea_credito_origen = parseFloat($(path).find('td[linea_credito_origen] input').val());
    let coutas_origen = parseFloat(linea_credito_origen/24);
    
    let linea_credito = moneda_origen == 1 ? linea_credito_origen : linea_credito_origen * tipo_cambio_moneda;
    let cuota = moneda_origen == 1 ? coutas_origen : coutas_origen * tipo_cambio_moneda;
    
    $(path).find('td[coutas_origen] input').val(coutas_origen.toFixed(2));
    $(path).find('td[linea_credito] input').val(linea_credito.toFixed(2));
    $(path).find('td[cuota] input').val(cuota.toFixed(2));
    calcula_total_linea_credito();
  }
  function calcula_total_linea_credito(){
    let linea_credito = 0;
    let cuota = 0;

    $("#table-linea-credito > tbody > tr").each(function() {
      linea_credito += parseFloat($(this).find('td[linea_credito] input').val());
      cuota += parseFloat($(this).find('td[cuota] input').val());
    });
    $('#total_lc_linea_credito').val(linea_credito.toFixed(2))
    $('#total_lc_cuotas').val(cuota.toFixed(2))
    
    $('#total_resumen_linea_credito').val(linea_credito.toFixed(2))
    $('#total_resumen_cuotas_linea_credito').val(cuota.toFixed(2))
    $('#total_resumen_cuotas_linea_credito2').val(cuota.toFixed(2))
    
    
  }
  
  function agrega_credito_entidad_noregulada(){
    let btn_eliminar = `<button type="button" onclick="eliminar_producto(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button>` ;
    let option_select = ``;
    @foreach($tipo_credito_evaluacion as $value)
      option_select += `<option value="{{ $value->id }}">{{ $value->nombre }}</option>`
    @endforeach
    let tabla = `<tr >
                  <td tipo_credito>
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      <option></option>
                      ${option_select}
                    </select>
                  </td>
                  <td entidad>
                    <div class="input-group">
                      <input nombre_entidad type="text" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      <div class="input-group-text">
                        <input tipo_entidad onclick="mostrar_endidad(this)" class="form-check-input mt-0 color_cajatexto" type="checkbox" value="" >
                      </div>
                    </div>
                  </td>
                  <td deudor>
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      <option></option>
                      <option value="Cliente">Cliente</option>
                      <option value="Pareja">Pareja</option>
                      <option value="Empresa">Empresa</option>
                    </select>
                  </td>
                  <td moneda_origen onchange="calcular_soles_entidad_regulada(this)">
                    <select {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto">
                      <option></option>
                      <option value="1">Soles</option>
                      <option value="2">Dolares</option>
                    </select>
                  </td>
                  <td saldo_capital_origen><input type="text" valida_input_vacio value="0.00" class="form-control campo_moneda color_cajatexto" onkeyup="calcular_soles_entidad_regulada(this)"></td>
                  <td plazo_pendiente_origen><input type="text" valida_input_vacio value="0.00" class="form-control campo_moneda color_cajatexto" onkeyup="calcular_soles_entidad_regulada(this)"></td>
                  <td cuota_origen><input type="text" valida_input_vacio value="0.00" class="form-control campo_moneda color_cajatexto" onkeyup="calcular_soles_entidad_regulada(this)"></td>

                  <td saldo_capital><input type="number" value="0.00" class="form-control campo_moneda" disabled></td>
                  <td cuota><input type="number" value="0.00" class="form-control campo_moneda" disabled></td>
                  <td corto_plazo><input type="number" value="0.00" class="form-control campo_moneda" disabled></td>
                  <td largo_plazo><input type="number" value="0.00" class="form-control campo_moneda" disabled></td>

                  <td saldo_capital_deducciones><input type="text" valida_input_vacio value="0.00" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcular_soles_entidad_regulada(this)"></td>
                  <td cuota_deducciones><input type="text" valida_input_vacio value="0.00" {{ $view_detalle=='false' ? 'disabled' : '' }} class="form-control color_cajatexto campo_moneda" onkeyup="calcular_soles_entidad_regulada(this)"></td>

                  <td>${btn_eliminar}</td>
                 </tr>`;

      $("#table-credito-entidad-noregulada > tbody").append(tabla);
    valida_input_vacio();
  }
  
  function calcular_resumen(){
    // COMERCIAL
    let comercial_er_cplazo = 0;
    let comercial_er_lplazo = 0;
    let comercial_er_couta = 0;
    
    let comercial_enr_cplazo = 0;
    let comercial_enr_lplazo = 0;
    let comercial_enr_couta = 0;
    // MES
    let mes_er_cplazo = 0;
    let mes_er_lplazo = 0;
    let mes_er_couta = 0;
    
    let mes_enr_cplazo = 0;
    let mes_enr_lplazo = 0;
    let mes_enr_couta = 0;
    // VIVIENDA
    let consumo_er_cplazo = 0;
    let consumo_er_lplazo = 0;
    let consumo_er_couta = 0;
    
    let consumo_enr_cplazo = 0;
    let consumo_enr_lplazo = 0;
    let consumo_enr_couta = 0;
    // VIVIENDA
    let vivienda_er_cplazo = 0;
    let vivienda_er_lplazo = 0;
    let vivienda_er_couta = 0;
    
    let vivienda_enr_cplazo = 0;
    let vivienda_enr_lplazo = 0;
    let vivienda_enr_couta = 0;
    
    $(`#table-credito-entidad-regulada > tbody > tr`).each(function() {
      let tipo_credito = $(this).find('td[tipo_credito] select').val();
      if(tipo_credito == 1){
        comercial_er_cplazo += parseFloat($(this).find('td[corto_plazo] input').val());
        comercial_er_lplazo += parseFloat($(this).find('td[largo_plazo] input').val());
        comercial_er_couta  += parseFloat($(this).find('td[cuota] input').val());
      }
      else if(tipo_credito == 2){
        mes_er_cplazo += parseFloat($(this).find('td[corto_plazo] input').val());
        mes_er_lplazo += parseFloat($(this).find('td[largo_plazo] input').val());
        mes_er_couta += parseFloat($(this).find('td[cuota] input').val());
      }
      else if(tipo_credito == 3){
        consumo_er_cplazo += parseFloat($(this).find('td[corto_plazo] input').val());
        consumo_er_lplazo += parseFloat($(this).find('td[largo_plazo] input').val());
        consumo_er_couta += parseFloat($(this).find('td[cuota] input').val());
      }
      else if(tipo_credito == 4){
        vivienda_er_cplazo += parseFloat($(this).find('td[corto_plazo] input').val());
        vivienda_er_lplazo += parseFloat($(this).find('td[largo_plazo] input').val());
        vivienda_er_couta += parseFloat($(this).find('td[cuota] input').val());  
      }
      
    });
    $(`#table-credito-entidad-noregulada > tbody > tr`).each(function() {
      let tipo_credito = $(this).find('td[tipo_credito] select').val();
      if(tipo_credito == 1){
        comercial_enr_cplazo  += parseFloat($(this).find('td[corto_plazo] input').val());
        comercial_enr_lplazo  += parseFloat($(this).find('td[largo_plazo] input').val());
        comercial_enr_couta   += parseFloat($(this).find('td[cuota] input').val());
      }
      else if(tipo_credito == 2){
        mes_enr_cplazo += parseFloat($(this).find('td[corto_plazo] input').val());
        mes_enr_lplazo += parseFloat($(this).find('td[largo_plazo] input').val());
        mes_enr_couta += parseFloat($(this).find('td[cuota] input').val());    
      }
      else if(tipo_credito == 3){
        consumo_enr_cplazo += parseFloat($(this).find('td[corto_plazo] input').val());
        consumo_enr_lplazo += parseFloat($(this).find('td[largo_plazo] input').val());
        consumo_enr_couta += parseFloat($(this).find('td[cuota] input').val());    
      }
      else if(tipo_credito == 4){
        vivienda_enr_cplazo += parseFloat($(this).find('td[corto_plazo] input').val());
        vivienda_enr_lplazo += parseFloat($(this).find('td[largo_plazo] input').val());
        vivienda_enr_couta += parseFloat($(this).find('td[cuota] input').val());   
      }
      
    });
    
    $('#comercial_er_cplazo').val(comercial_er_cplazo.toFixed(2));
    $('#comercial_er_lplazo').val(comercial_er_lplazo.toFixed(2));
    $('#comercial_er_couta').val(comercial_er_couta.toFixed(2));
    
    $('#comercial_enr_cplazo').val(comercial_enr_cplazo.toFixed(2));
    $('#comercial_enr_lplazo').val(comercial_enr_lplazo.toFixed(2));
    $('#comercial_enr_couta').val(comercial_enr_couta.toFixed(2));
    // MES
    $('#mes_er_cplazo').val(mes_er_cplazo.toFixed(2));
    $('#mes_er_lplazo').val(mes_er_lplazo.toFixed(2));
    $('#mes_er_couta').val(mes_er_couta.toFixed(2));
    
    $('#mes_enr_cplazo').val(mes_enr_cplazo.toFixed(2));
    $('#mes_enr_lplazo').val(mes_enr_lplazo.toFixed(2));
    $('#mes_enr_couta').val(mes_enr_couta.toFixed(2));
    // VIVIENDA
    $('#consumo_er_cplazo').val(consumo_er_cplazo.toFixed(2));
    $('#consumo_er_lplazo').val(consumo_er_lplazo.toFixed(2));
    $('#consumo_er_couta').val(consumo_er_couta.toFixed(2));
    
    $('#consumo_enr_cplazo').val(consumo_enr_cplazo.toFixed(2));
    $('#consumo_enr_lplazo').val(consumo_enr_lplazo.toFixed(2));
    $('#consumo_enr_couta').val(consumo_enr_couta.toFixed(2));
    // VIVIENDA
    $('#vivienda_er_cplazo').val(vivienda_er_cplazo.toFixed(2));
    $('#vivienda_er_lplazo').val(vivienda_er_lplazo.toFixed(2));
    $('#vivienda_er_couta').val(vivienda_er_couta.toFixed(2));
    
    $('#vivienda_enr_cplazo').val(vivienda_enr_cplazo.toFixed(2));
    $('#vivienda_enr_lplazo').val(vivienda_enr_lplazo.toFixed(2));
    $('#vivienda_enr_couta').val(vivienda_enr_couta.toFixed(2));
    
    let comercial_total_cplazo = comercial_er_cplazo + comercial_enr_cplazo;
    let comercial_total_lplazo = comercial_er_lplazo + comercial_enr_lplazo;
    let comercial_total = comercial_total_cplazo + comercial_total_lplazo;
    let comercial_total_couta = comercial_er_couta + comercial_enr_couta;
    
    let mes_total_cplazo = mes_er_cplazo + mes_enr_cplazo;
    let mes_total_lplazo = mes_er_lplazo + mes_enr_lplazo;
    let mes_total = mes_total_cplazo + mes_total_lplazo;
    let mes_total_couta = mes_er_couta + mes_enr_couta;
    
    let consumo_total_cplazo = consumo_er_cplazo + consumo_enr_cplazo;
    let consumo_total_lplazo = consumo_er_lplazo + consumo_enr_lplazo;
    let consumo_total = consumo_total_cplazo + consumo_total_lplazo;
    let consumo_total_couta = consumo_er_couta + consumo_enr_couta;
    
    let vivienda_total_cplazo = vivienda_er_cplazo + vivienda_enr_cplazo;
    let vivienda_total_lplazo = vivienda_er_lplazo + vivienda_enr_lplazo;
    let vivienda_total = vivienda_total_cplazo + vivienda_total_lplazo;
    let vivienda_total_couta = vivienda_er_couta + vivienda_enr_couta;
    
    $('#comercial_total_cplazo').val(comercial_total_cplazo.toFixed(2));
    $('#comercial_total_lplazo').val(comercial_total_lplazo.toFixed(2));
    $('#comercial_total').val(comercial_total.toFixed(2));
    $('#comercial_total_couta').val(comercial_total_couta.toFixed(2));
    $('#mes_total_cplazo').val(mes_total_cplazo.toFixed(2));
    $('#mes_total_lplazo').val(mes_total_lplazo.toFixed(2));
    $('#mes_total').val(mes_total.toFixed(2));
    $('#mes_total_couta').val(mes_total_couta.toFixed(2));
    $('#consumo_total_cplazo').val(consumo_total_cplazo.toFixed(2));
    $('#consumo_total_lplazo').val(consumo_total_lplazo.toFixed(2));
    $('#consumo_total').val(consumo_total.toFixed(2));
    $('#consumo_total_couta').val(consumo_total_couta.toFixed(2));
    $('#vivienda_total_cplazo').val(vivienda_total_cplazo.toFixed(2));
    $('#vivienda_total_lplazo').val(vivienda_total_lplazo.toFixed(2));
    $('#vivienda_total').val(vivienda_total.toFixed(2));
    $('#vivienda_total_couta').val(vivienda_total_couta.toFixed(2));
    calcula_total_resumen();
  }
  
  function calcula_total_resumen(){
    let totales = Array(11).fill(0);
    $("#table-resumen tbody tr").each(function() {
      let celdas = $(this).find("td input");
      celdas.each(function(indice) {
        totales[indice] += parseFloat($(this).val());
      });
    });
    $("#table-resumen tfoot tr[totales] td input").each(function(indice) {
      $(this).val(totales[indice].toFixed(2));
    });
  }
  // JSON
  function json_entidad(table){
    let data = [];
    $(`#${table} > tbody > tr`).each(function() {
        let id                        = $(this).attr('id');
        let id_tipo_credito           = $(this).find('td[tipo_credito] select').val();
        let nombre_entidad            = $(this).find('td[entidad] input[nombre_entidad]').val();
        let tipo_entidad              = $(this).find('td[entidad] input[tipo_entidad]').prop("checked");
        let deudor                    = $(this).find('td[deudor] select').val();
        let moneda_origen             = $(this).find('td[moneda_origen] select').val();
        let saldo_capital_origen      = $(this).find('td[saldo_capital_origen] input').val();
        let plazo_pendiente_origen    = $(this).find('td[plazo_pendiente_origen] input').val();
        let cuota_origen              = $(this).find('td[cuota_origen] input').val();
        let saldo_capital             = $(this).find('td[saldo_capital] input').val();
        let cuota                     = $(this).find('td[cuota] input').val();
        let corto_plazo               = $(this).find('td[corto_plazo] input').val();
        let largo_plazo               = $(this).find('td[largo_plazo] input').val();
        let saldo_capital_deducciones = $(this).find('td[saldo_capital_deducciones] input').val();
        let cuota_deducciones         = $(this).find('td[cuota_deducciones] input').val();

        data.push({ 
            id: id,
            id_tipo_credito: id_tipo_credito,
            nombre_entidad: nombre_entidad,
            tipo_entidad: tipo_entidad,
            deudor: deudor,
            moneda_origen: moneda_origen,
            saldo_capital_origen: saldo_capital_origen,
            plazo_pendiente_origen: plazo_pendiente_origen,
            cuota_origen: cuota_origen,
            saldo_capital: saldo_capital,
            cuota: cuota,
            corto_plazo: corto_plazo,
            largo_plazo: largo_plazo,
            saldo_capital_deducciones: saldo_capital_deducciones,
            cuota_deducciones: cuota_deducciones,
        });
    });
    return JSON.stringify(data);
  }
  function json_linea_credito(){
    let data = [];
    $(`#table-linea-credito > tbody > tr`).each(function() {
      let id                        = $(this).attr('id');
      let tipo_credito              = $(this).find('td[tipo_credito] select').val();
      let entidad                   = $(this).find('td[entidad] input').val();
      let deudor                    = $(this).find('td[deudor] select').val();
      let moneda_origen             = $(this).find('td[moneda_origen] select').val();
      let linea_credito_origen      = $(this).find('td[linea_credito_origen] input').val();
      let coutas_origen             = $(this).find('td[coutas_origen] input').val();
      let linea_credito             = $(this).find('td[linea_credito] input').val();
      let cuota                     = $(this).find('td[cuota] input').val();

      data.push({ 
        id: id,
        tipo_credito: tipo_credito,
        entidad: entidad,
        deudor: deudor,
        moneda_origen: moneda_origen,
        linea_credito_origen: linea_credito_origen,
        coutas_origen: coutas_origen,
        linea_credito: linea_credito,
        cuota: cuota,
      });
    });
    return JSON.stringify(data);
  }
  
  function json_resumen(){
    let jsonData = [];
    $("#table-resumen input").each(function () {
        let input = $(this);
        let id = input.attr("id");
        let valor = input.val();
        jsonData.push({ 
          id: id,
          valor: valor,
        });
    });
    return JSON.stringify(jsonData);
  }
  
  
  
  
  
  
  
  
  
  
  
  
  
</script>