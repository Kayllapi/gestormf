<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/credito/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'formato_evaluacion',
              adicional_ingreso_mensual: json_adicional_ingreso_mensual(),
              adicional_egresos_mensual: json_adicional_egresos_mensual(),
              deudas_financieras: json_deudas_financieras(),
              referencia: json_referencia(),
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
      $adicional_ingreso_mensual = $credito_formato_evaluacion ? ( $credito_formato_evaluacion->adicional_ingreso_mensual == "" ? [] : json_decode($credito_formato_evaluacion->adicional_ingreso_mensual) ) : [];
      $adicional_egresos_mensual = $credito_formato_evaluacion ? ( $credito_formato_evaluacion->adicional_egresos_mensual == "" ? [] : json_decode($credito_formato_evaluacion->adicional_egresos_mensual) ) : [];
      $deudas_financieras = $credito_formato_evaluacion ? ( $credito_formato_evaluacion->deudas_financieras == "" ? [] : json_decode($credito_formato_evaluacion->deudas_financieras) ) : [];
      $referencia = $credito_formato_evaluacion ? ( $credito_formato_evaluacion->referencia == "" ? [] : json_decode($credito_formato_evaluacion->referencia) ) : [];
    @endphp
    <div class="modal-header" style="border-bottom: 0;">
        <h5 class="modal-title">FORMATO DE EVALUACIÓN </h5>
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
            <label class="col-sm-4 col-form-label" style="text-align: right;">Cliente (A. y N.):</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $credito->nombreclientecredito }}" disabled>
            </div>
          </div>
          @if($users_prestamo->dni_pareja!='' or $users_prestamo->nombrecompleto_pareja!='')
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">PAREJA:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $users_prestamo->nombrecompleto_pareja }}" disabled>
            </div>
          </div>
          @endif
          
        </div>
        <div class="col-sm-12 col-md-5">
          <div class="row">
            <label class="col-sm-3 col-form-label" style="text-align: right;">F. EVALUACIÓN:</label>
            <div class="col-sm-7">
              <input type="date" step="any" class="form-control" value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->fecha : date_format(date_create($credito->fecha),'d/m/Y') }}"  id="fecha_desembolso" disabled>
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
          
        </div>
        <div class="col-sm-12 col-md-2">
          <div class="row">
            <label class="col-sm-5 col-form-label" style="text-align: right;">NRO:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="S{{ str_pad($credito->id, 8, '0', STR_PAD_LEFT)  }}" disabled>
            </div>
          </div>
        </div>
      </div>
      <br>
      <table class="table table-bordered">
        <tbody>
          <tr>
            <td style="background-color: #c8c8c8 !important;color: #000 !important;">Monto solicitado:</td>
            <td>S/ {{ $credito->monto_solicitado }}</td>
            <td style="background-color: #c8c8c8 !important;color: #000 !important;">Nro de cuotas:</td>
            <td>{{ $credito->cuotas }}</td>
            <td style="background-color: #c8c8c8 !important;color: #000 !important;">Forma de Pago;</td>
            <td>{{ $credito->forma_pago_credito_nombre }}</td>
          </tr>
          <tr>
            <td style="background-color: #c8c8c8 !important;color: #000 !important;">Producto:</td>
            <td>{{ $credito->nombreproductocredito }}</td>
            <td style="background-color: #c8c8c8 !important;color: #000 !important;">Modalidad:</td>
            <td>{{ $credito->modalidad_credito_nombre }}</td>
            <td style="background-color: #c8c8c8 !important;color: #000 !important;">Tipo de cliente:</td>
            <td>{{ $credito->tipo_operacion_credito_nombre }}</td>
          </tr>
          <tr>
            <td colspan=4></td>
            <td style="background-color: #c8c8c8 !important;color: #000 !important;">Destino de Crédito:</td>
            <td>{{ $credito->tipo_destino_credito_nombre}}</td>
          </tr>
        </tbody> 
      </table>
      <div class="mb-1 mt-2">
        <span class="badge d-block">I. INGRESOS Y GASTOS</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-3">
          <table class="table table-bordered" id="table-ingresos-mensuales">
            <thead>
              <tr>
                <th colspan=3>Ingreso Mensuales (Cliente y Pareja)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Remuneración Total Neta Cliente</td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->remuneracion_total_cliente : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="remuneracion_total_cliente"></td>
                <td></td>
              </tr>
              <tr>
                <td>Remuneración variable</td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->remuneracion_variable : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="remuneracion_variable"></td>
                <td></td>
              </tr>
              <tr>
                <td>Remuneración de la Pareja</td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->remuneracion_pareja : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="remuneracion_pareja"></td>
                <td></td>
              </tr>
              <tr tr_encabezado>
                <td colspan=2>OTROS: (Remesas, pensión, Etc.)</td>
                <td><button type="button" class="btn btn-success" onclick="agrega_ingreso_mensual()"><i class="fa fa-plus"></i></button></td>
              </tr>
              @foreach($adicional_ingreso_mensual as $value)
                <tr adicional>
                  <td><input type="text" value="{{ $value->descripcion }}" class="form-control color_cajatexto" descripcion></td>
                  <td><input type="text" valida_input_vacio value="{{ $value->monto }}" class="form-control color_cajatexto campo_moneda" monto></td>
                  <td><button type="button" class="btn btn-danger" onclick="eliminar_fila(this)"><i class="fa fa-trash"></i></button></td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr tr_encabezado>
                <td >TOTAL (S/)</td>
                <td><input type="text" disabled class="form-control campo_moneda" id="total_ingresos_mensuales" value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->total_ingresos_mensuales : '0.00' }}"></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
          <br>
          <br>
          <table class="table table-bordered mt-2" width="100%">
            <thead>
              <tr>
                <th>Número total de Hijos</th>
                <th>Total de hijos dependientes</th>
                </tr>
            </thead>
            <tbody>
              <tr>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->numero_total_hijos : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="numero_total_hijos"></td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->total_hijos_dependientes : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="total_hijos_dependientes"></td>
              </tr>
            </tbody>
          </table>
          
        </div>
        <div class="col-sm-12 col-md-3">
          <table class="table table-bordered" id="table-egresos-mensuales">
            <thead>
              <tr>
                <th colspan=3>Egresos Mensuales (Cliente y Pareja)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Pago de cuotas de deuda (Reporte RCC Reg. y no Reg.)</td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->pago_cuotas_deuda : '0.00' }}" class="form-control campo_moneda" disabled id="pago_cuotas_deuda"></td>
                <td></td>
              </tr>
              <tr>
                <td>Alimentación</td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_alimentacion : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="monto_alimentacion"></td>
                <td></td>
              </tr>
              <tr>
                <td>Salud</td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_salud : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="monto_salud"></td>
                <td></td>
              </tr>
              <tr>
                <td>Educación</td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_educacion : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="monto_educacion"></td>
                <td></td>
              </tr>
              <tr>
                <td>Alquiler de vivienda</td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_alquiler_vivienda : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="monto_alquiler_vivienda"></td>
                <td></td>
              </tr>
              <tr>
                <td>Mobilidad</td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_mobilidad : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="monto_mobilidad"></td>
                <td></td>
              </tr>
              <tr>
                <td>S. de Luz</td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_luz : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="monto_luz"></td>
                <td></td>
              </tr>
              <tr>
                <td>S. de Agua y Acantarillado</td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_agua : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="monto_agua"></td>
                <td></td>
              </tr>
              <tr>
                <td>Teléfono</td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_telefono : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="monto_telefono"></td>
                <td></td>
              </tr>
              <tr>
                <td>Cable</td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_cable : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="monto_cable"></td>
                <td></td>
              </tr>
              <tr>
                <td>Otros gastos personales ({{ configuracion($tienda->id,'provision_gastos_familiares')['valor'] }}%):</td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->otros_gastos_personales : '0.00' }}" disabled class="form-control campo_moneda" id="otros_gastos_personales"></td>
                <td></td>
              </tr>
              <tr>
                <td>Pensión de Alimentos</td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->monto_pension_alimentos : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="monto_pension_alimentos"></td>
                <td></td>
              </tr>
              <tr tr_encabezado>
                <td colspan=2></td>
                <td><button type="button" class="btn btn-success" onclick="agrega_egreso_mensual()"><i class="fa fa-plus"></i></button></td>
              </tr>
              @foreach($adicional_egresos_mensual as $value)
                <tr adicional>
                  <td><input type="text" value="{{ $value->descripcion }}" class="form-control color_cajatexto" descripcion></td>
                  <td><input type="text" valida_input_vacio value="{{ $value->monto }}" class="form-control color_cajatexto campo_moneda" monto></td>
                  <td><button type="button" class="btn btn-danger" onclick="eliminar_fila(this)"><i class="fa fa-trash"></i></button></td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr tr_encabezado>
                <td >TOTAL (S/.)</td>
                <td><input type="text" disabled class="form-control campo_moneda" id="total_egresos_mensuales" value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->total_egresos_mensuales : '0.00' }}"></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
          <table class="table table-bordered mt-2" width="100%">
            <tbody>
              <tr>
                <td style="background-color: #144081 !important; color:white !important;text-align:center;">EXCEDENTE MENSUAL DISPONIBLE (S/.)</td>
              </tr>
              <tr>
                <td><input type="text" value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->excedente_mensual_disponible : '0.00' }}" disabled class="form-control campo_moneda" id="excedente_mensual_disponible"></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-sm-12 col-md-6">
          <table class="table table-bordered" id="table-deudas">
            <thead>
              <tr>
                <th rowspan=2 width="100px">Deudas</th>
                <th>Inst. Finan.</th>
                <th rowspan=2>Saldo Capital</th>
                <th rowspan=2>Cuota Mensual</th>
                <th rowspan=2 colspan=2>CUOTA Ampliación/Compra de deuda</th>
              </tr>
              <tr>
                <th>PROVISIONAR LÍNEAS DE CRÉDITO NO USADAS: Consumo a 24 meses Cptal. de trabajo a 36 meses</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="5">INSTITUCIONES FINANCIERAS (Reguladas y no Reguladas)</td>
                <td><button type="button" class="btn btn-success" onclick="agrega_deuda()"><i class="fa fa-plus"></i></button></td>
              </tr>
              @foreach($deudas_financieras as $value)
                <tr adicional>
                  <td>
                    <select class="form-control color_cajatexto" tipo>
                      <option></option>
                      <option value="CLIENTE" {{ $value->tipo == "CLIENTE" ? 'selected' : '' }} >CLIENTE</option>
                      <option value="PAREJA" {{ $value->tipo == "PAREJA" ? 'selected' : '' }} >PAREJA</option>
                    </select>
                  </td>
                  <td><input type="text" class="form-control color_cajatexto" value="{{ $value->banco }}" banco></td>
                  <td><input type="text" valida_input_vacio value="{{ $value->saldo }}" class="form-control color_cajatexto campo_moneda" saldo></td>
                  <td><input type="text" valida_input_vacio value="{{ $value->cuota }}" class="form-control color_cajatexto campo_moneda" cuota></td>
                  <td><input type="text" valida_input_vacio value="{{ $value->ampliacion }}" class="form-control color_cajatexto campo_moneda" ampliacion></td>
                  <td><button type="button" class="btn btn-danger" onclick="eliminar_fila(this)"><i class="fa fa-trash"></i></button></td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td colspan="6">{{ $tienda->nombre }}</td>
              </tr>
              <tr>
                <td>CLIENTE</td>
                <td></td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->saldo_capita_cliente : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="saldo_capita_cliente"></td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->couta_mensual_cliente : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="couta_mensual_cliente"></td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->cuota_ampliacion_cliente : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="cuota_ampliacion_cliente"></td>
                <td></td>
              </tr>
              <tr>
                <td>PAREJA</td>
                <td></td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->saldo_capita_pareja : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="saldo_capita_pareja"></td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->couta_mensual_pareja : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="couta_mensual_pareja"></td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->cuota_ampliacion_pareja : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="cuota_ampliacion_pareja"></td>
                <td></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;" colspan=2>TOTAL (S/.)</td>
                <td style="background-color: #c8c8c8 !important;"><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->total_saldo_capital : '0.00' }}" class="form-control campo_moneda" disabled id="total_saldo_capital"></td>
                <td style="background-color: #c8c8c8 !important;"><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->total_couta_mensual : '0.00' }}" class="form-control campo_moneda" disabled id="total_couta_mensual"></td>
                <td style="background-color: #c8c8c8 !important;"><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->total_couta_ampliacion : '0.00' }}" class="form-control campo_moneda" disabled id="total_couta_ampliacion"></td>
                <td style="background-color: #c8c8c8 !important;"></td>
              </tr>
            </tfoot>
          </table>
          <div id="error_deudas" class="alert alert-danger mt-2 d-none" style="background-color: #ff6666;border-color: #ff6666;color: #000;font-weight: bold;">Registrar en Deducciones/Compra de deuda o ampliación, respectiva.</div>
          <br><br>
          <table class="table table-bordered mt-2" style="width: 300px;" id="table-entidad-financiera">
            <thead>
              <tr>
                <th colspan="2">N° DE ENTIDADES FINANCIERAS</th>
                </tr>
            </thead>
            <tbody>
              <tr>
                <td style="width: 200px;">CLIENTE</td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->entidad_financiera_cliente : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="entidad_financiera_cliente"></td>
              </tr>
              <tr>
                <td>PAREJA</td>
                <td><input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->entidad_financiera_pareja : '0.00' }}" class="form-control color_cajatexto campo_moneda" id="entidad_financiera_pareja"></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;" class="fw-bold">TOTAL</td>
                <td style="background-color: #c8c8c8 !important;">
                  <input type="text" valida_input_vacio value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->entidad_financiera_total : '0.00' }}" 
                         class="form-control campo_moneda" disabled id="entidad_financiera_total"></td>
              </tr>
            </tbody>
          </table>
          
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block">II.  PROPUESTA DE CRÉDITO Y REFERENCIAS</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-10">
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
                <th rowspan=2 width="80px">Monto Préstamo</th>
                <th rowspan=2 width="80px">TEM</th>
                
                <th rowspan=2 width="80px">Servicios / Otros (S/.)</th>
                <th rowspan=2 width="80px">Cargos (S/.)</th>
                <th rowspan=2 width="80px">Cuota de Pago <span id="nombre_frecuencia_pago">Diario</span> (S/.)</th>
              </tr>
              <tr>
                <th>Pago</th>
                <th  width="80px">Cuotas</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><input type="text" disabled class="form-control" id="propuesta_objetivo" value="{{ $credito->tipo_destino_credito_nombre}}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $credito->nombreproductocredito }}"></td>
                <td>
                  <select class="form-control" id="idforma_pago_credito" disabled>
                    @foreach($forma_pago_credito as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                  </select>
                </td> 
                <td>
                  
                  <input type="text" class="form-control text-center" valida_input_vacio 
                         value="{{ $credito->cuotas }}" 
                         id="propuesta_cuotas" disabled>
                </td>
                <td>
                  <select class="form-control" id="propuesta_forma_pago" disabled>
                    <option value="1">Cuota Fija</option>
                  </select>
                </td>
                <td>
                  <input type="text" class="form-control campo_moneda" valida_input_vacio 
                         value="{{ $credito->monto_solicitado }}" 
                         id="propuesta_monto" disabled>
                </td>
                <td>
                  <div class="input-group">
                    <input type="text" step="any" id="propuesta_tem" class="form-control campo_moneda" disabled
                           value="{{ $credito->tasa_tem }}" minimo="{{ $credito->tasa_tem }}">
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
                <td class="color_totales"><input type="text" class="form-control campo_moneda" 
                                                 value="{{ $credito->total_propuesta }}" id="total_propuesta" disabled></td>
              </tr>
              <tr>
                <td colspan=10 class="text-center" id="mensaje_error_cronograma"></td>
              </tr>
            </tfoot>
          </table>
        </div>
        <div class="col-sm-12 col-md-2">
          <table class="table table-bordered">
            <tr>
              <td style="background-color: #144081 !important; color:white !important;">CUOTA / EXCEDENTE (%)</td>
            </tr>
            <tr>
              <td><input type="text" disabled value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->resultado_cuota_excedente : '0.00' }}" class="form-control campo_moneda" id="resultado_cuota_excedente"></td>
            </tr>
            <tr>
              <td style="background-color: #144081 !important; color:white !important;">RESULTADO</td>
            </tr>
            <tr>
              <td><input type="text" disabled value="{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->estado_evaluacion : '0.00' }}" 
                         class="form-control campo_moneda" id="estado_evaluacion" style="text-align: center;"></td>
            </tr>
          </table>
        </div>
      </div>
      
      
      <div class="mb-1 mt-2">
        <span class="badge d-block">REFERENCIAS SOBRE MORAL DE PAGO Y LABORAL</span>
      </div>
      <div class="row">
        
        <div class="col-sm-12">
              
              
              <div class="mb-1">
                  <table class="table table-bordered" id="tabla-referencia">
                      <thead>
                          <th>Fuente</th>
                          <th>Apellidos y Nombres</th>
                          <th>Telf./Celular</th>
                          <th width="10px">
                            <a href="javascript:;" class="btn btn-success" onclick="agregar_referencia()">
                              <i class="fa-solid fa-plus"></i>
                            </a>
                          </th>
                      </thead>
                      <tbody num="0">
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block">III. COMENTARIOS SOBRE CENTRO LABORAL TIPO DE CONTRATO ANTIGÜEDAD, CONTINUIDAD Y FORTALEZAS IDENTIFICADAS</span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <textarea id="comentario_centro_laboral"  class="form-control color_cajatexto" cols="30" rows="2">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->comentario_centro_laboral : '' }}</textarea>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block">IV. COMENTARIOS SOBRE CAPACIDAD DE PAGO, INGRESOS ADICIONALES, DESTINO DE LOS CRÉDITOS VIGENTES,  ACUMULACIÓN PATRIMONIAL</span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <textarea id="comentario_capacidad_pago"  class="form-control color_cajatexto" cols="30" rows="2">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->comentario_capacidad_pago : '' }}</textarea>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block">V. SUSTENTO DEL HISTORIAL DE PAGO INTERNO Y EXTERNO, REFERENCIAS PERSONALES Y BANCARIAS, ENDEUDAMIENTO</span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <textarea id="sustento_historial_pago"  class="form-control color_cajatexto" cols="30" rows="2">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->sustento_historial_pago : '' }}</textarea>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block">VI. SUSTENTO DEL DESTINO  DEL CRÉDITO</span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <textarea id="sustento_destino_credito"  class="form-control color_cajatexto" cols="30" rows="2">{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->sustento_destino_credito : '' }}</textarea>
        </div>
      </div>
      <div class="row mt-1">
        @if($view_detalle!='false')
        <div class="col" style="flex: 0 0 0%;">
          <button type="submit" class="btn btn-success" id="btn_guardar"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
        </div>
        @endif
        <div class="col" style="flex: 0 0 0%;">
          <button type="button" 
                  class="btn btn-dark" 
                  onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=solicitud_formato_evaluacion')}}', size: 'modal-fullscreen' })"
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
    </div>
</form> 
<style>
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
  function valida_tem(){
    let minimo = parseFloat($('#propuesta_tem').attr('minimo'));
    if ($('#propuesta_tem').val() === "" || parseFloat($('#propuesta_tem').val()) < minimo) {
      $('#propuesta_tem').val(minimo.toFixed(2));
    }
  }
  
  valida_input_vacio();
  $('input[valida_input_vacio]').on('blur', function() {
      suma_ingreso_mensual();
      suma_egreso_mensual();
      suma_deudas();
      suma_entidad_financiera();
  });
  @foreach($referencia as $value)
    agregar_referencia('{{ $value->fuente }}', '{{ $value->nombre }}', '{{ $value->celular }}');
  @endforeach
  function agrega_ingreso_mensual(){
    let fila = `<tr adicional>
                  <td><input type="text" class="form-control color_cajatexto" descripcion></td>
                  <td><input type="text" valida_input_vacio value="0.00" class="form-control color_cajatexto campo_moneda" monto></td>
                  <td><button type="button" class="btn btn-danger" onclick="eliminar_fila(this)"><i class="fa fa-trash"></i></button></td>
                </tr>`;
    $(`#table-ingresos-mensuales > tbody`).append(fila);
    valida_input_vacio();
    suma_ingreso_mensual();
  }
  function eliminar_fila(e){
    $(e).closest('tr').remove();
    suma_ingreso_mensual();
    suma_ingreso_mensual();
    suma_egreso_mensual();
  }
  function agrega_egreso_mensual(){
    let fila = `<tr adicional>
                  <td><input type="text" class="form-control color_cajatexto" descripcion></td>
                  <td><input type="text" valida_input_vacio value="0.00" class="form-control color_cajatexto campo_moneda" monto></td>
                  <td><button type="button" class="btn btn-danger" onclick="eliminar_fila(this)"><i class="fa fa-trash"></i></button></td>
                </tr>`;
    $(`#table-egresos-mensuales > tbody`).append(fila);
    valida_input_vacio();
  }
  function agrega_deuda(){
    let fila = `<tr adicional>
                  <td>
                    <select class="form-control color_cajatexto" tipo>
                      <option></option>
                      <option value="CLIENTE">CLIENTE</option>
                      <option value="PAREJA">PAREJA</option>
                    </select>
                  </td>
                  <td><input type="text" class="form-control color_cajatexto" banco></td>
                  <td><input type="text" valida_input_vacio value="0.00" class="form-control color_cajatexto campo_moneda" saldo></td>
                  <td><input type="text" valida_input_vacio value="0.00" class="form-control color_cajatexto campo_moneda" cuota></td>
                  <td><input type="text" valida_input_vacio value="0.00" class="form-control color_cajatexto campo_moneda" ampliacion></td>
                  <td><button type="button" class="btn btn-danger" onclick="eliminar_fila(this)"><i class="fa fa-trash"></i></button></td>
                </tr>`;
    $(`#table-deudas > tbody`).append(fila);
    valida_input_vacio();
  }
  function json_adicional_ingreso_mensual(){
    let data = [];
    $("#table-ingresos-mensuales > tbody > tr[adicional]").each(function() {
        let descripcion  = $(this).find('td input[descripcion]').val();
        let monto     = $(this).find('td input[monto]').val();
        data.push({ 
            descripcion: descripcion,
            monto: monto,
        });
    });
    return JSON.stringify(data);
  }
  function json_adicional_egresos_mensual(){
    let data = [];
    $("#table-egresos-mensuales > tbody > tr[adicional]").each(function() {
        let descripcion  = $(this).find('td input[descripcion]').val();
        let monto     = $(this).find('td input[monto]').val();
        data.push({ 
            descripcion: descripcion,
            monto: monto,
        });
    });
    return JSON.stringify(data);
  }
  function json_deudas_financieras(){
    let data = [];
    $("#table-deudas > tbody > tr[adicional]").each(function() {
        let tipo  = $(this).find('td select[tipo]').val();
        let banco     = $(this).find('td input[banco]').val();
        let saldo     = $(this).find('td input[saldo]').val();
        let cuota     = $(this).find('td input[cuota]').val();
        let ampliacion     = $(this).find('td input[ampliacion]').val();
        data.push({ 
            tipo: tipo,
            banco: banco,
            saldo: saldo,
            cuota: cuota,
            ampliacion: ampliacion,
        });
    });
    return JSON.stringify(data);
  }
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


      },
      complete: function(res){
        
      }
    })
  }*/
  
  sistema_select2({ input:'#idforma_pago_credito' });
  $("#idforma_pago_credito").on("change", function(e) {
    var selectedOption = $(this).select2('data')[0];
    var selectedText = selectedOption.text;
    $('#nombre_frecuencia_pago').text(selectedText);
  }).val('{{ $credito_formato_evaluacion ? $credito_formato_evaluacion->idforma_pago_credito : $credito->idforma_pago_credito }}').trigger('change');
  
  /*$("#idforma_pago_credito").on("select2:select", function(e) {
    showtasa(1)
  });*/
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
    
  }
  
  function agregar_referencia(fuente='', nombre='', celular=''){

      var num   = $("#tabla-referencia > tbody").attr('num');
      var cant  = $("#tabla-referencia > tbody > tr").length;

      var tdeliminar = '<td><a href="javascript:;" onclick="eliminar_referencia('+num+',`referencia`)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></td>';

      let option = ``;
      @foreach($f_tiporeferencia as $value)
        var selectedOption = fuente == "{{ $value->id }}" ? 'selected' : '';
        option += `<option value="{{ $value->id }}" ${selectedOption}>{{$value->nombre}}</option>`;
      @endforeach
      var tabla= '<tr id="'+num+'">'+
                  '<td><select class="form-control color_cajatexto" id="fuente'+num+'"><option></option>'+option+'</select></td>'+
                  '<td><input type="text" class="form-control color_cajatexto" id="nombre'+num+'" value="'+nombre+'"></td>'+
                  '<td><input type="number" class="form-control color_cajatexto" id="celular'+num+'" value="'+celular+'"></td>'+
                  tdeliminar+
                 '</tr>';

      $("#tabla-referencia > tbody").append(tabla);
      $("#tabla-referencia > tbody").attr('num',parseInt(num)+1);  

  }
  function eliminar_referencia(num,tabla){
      $("#tabla-referencia > tbody > tr#"+num).remove();
  }
  function json_referencia(){
      var data = [];
      $("#tabla-referencia > tbody > tr").each(function() {
          var num = $(this).attr('id');    
          data.push({ 
              fuente: $('#fuente'+num).val(),
              nombre: $('#nombre'+num).val(),
              celular: $('#celular'+num).val(),
          });
      });
      return JSON.stringify(data);
  }
  function suma_ingreso_mensual(){
    let remuneracion_total_cliente = parseFloat($("#remuneracion_total_cliente").val());
    let remuneracion_variable = parseFloat($("#remuneracion_variable").val());
    let remuneracion_pareja = parseFloat($("#remuneracion_pareja").val());
    let ingresos_adicionales = 0;
    $("#table-ingresos-mensuales > tbody > tr[adicional]").each(function() {
      let monto = $(this).find('td input[monto]').val();
      ingresos_adicionales += parseFloat(monto);  
    });
    let total_ingresos_mensuales = remuneracion_total_cliente + 
                                    remuneracion_variable + 
                                    remuneracion_pareja +
                                    ingresos_adicionales;
    $("#total_ingresos_mensuales").val(total_ingresos_mensuales.toFixed(2))
    calcula_excedente_mensual()
  }

  function suma_egreso_mensual(){

    let total_couta_mensual = parseFloat($("#total_couta_mensual").val());// FROM DEUDAS
    $("#pago_cuotas_deuda").val(total_couta_mensual.toFixed(2));
    let pago_cuotas_deuda = parseFloat($("#pago_cuotas_deuda").val());
    let monto_alimentacion = parseFloat($("#monto_alimentacion").val());
    let monto_salud = parseFloat($("#monto_salud").val());
    let monto_educacion = parseFloat($("#monto_educacion").val());
    let monto_alquiler_vivienda = parseFloat($("#monto_alquiler_vivienda").val());
    let monto_mobilidad = parseFloat($("#monto_mobilidad").val());
    let monto_luz = parseFloat($("#monto_luz").val());
    let monto_agua = parseFloat($("#monto_agua").val());
    let monto_telefono = parseFloat($("#monto_telefono").val());
    let monto_cable = parseFloat($("#monto_cable").val());

    let monto_pension_alimentos = parseFloat($("#monto_pension_alimentos").val());

    let porcentaje_gatos = parseFloat("{{ configuracion($tienda->id,'provision_gastos_familiares')['valor'] }}")/100;

    let otros_gastos_personales = (pago_cuotas_deuda +
                                  monto_alimentacion +
                                  monto_salud +
                                  monto_educacion + 
                                  monto_alquiler_vivienda +
                                  monto_mobilidad +
                                  monto_luz +
                                  monto_agua +
                                  monto_telefono +
                                  monto_cable) * porcentaje_gatos;
    $("#otros_gastos_personales").val(otros_gastos_personales.toFixed(2))

    let egreso_adicionales = 0;
    $("#table-egresos-mensuales > tbody > tr[adicional]").each(function() {
      let monto = $(this).find('td input[monto]').val();
      egreso_adicionales += parseFloat(monto);  
    });
    let total_egresos_mensuales = pago_cuotas_deuda +
                                  monto_alimentacion +
                                  monto_salud +
                                  monto_educacion + 
                                  monto_alquiler_vivienda +
                                  monto_mobilidad +
                                  monto_luz +
                                  monto_agua +
                                  monto_telefono +
                                  monto_cable +
                                  otros_gastos_personales +
                                  monto_pension_alimentos +
                                  egreso_adicionales;
    $("#total_egresos_mensuales").val(total_egresos_mensuales.toFixed(2))
    calcula_excedente_mensual()
  }
  suma_deudas();
  function suma_deudas(){
    let total_saldo = 0;
    let total_cuota = 0;
    let total_ampliacion = 0;
    $("#table-deudas > tbody > tr[adicional]").each(function() {

        let saldo     = $(this).find('td input[saldo]').val();
        let cuota     = $(this).find('td input[cuota]').val();
        let ampliacion = $(this).find('td input[ampliacion]').val();
        total_saldo += parseFloat(saldo);
        total_cuota += parseFloat(cuota);
        total_ampliacion += parseFloat(ampliacion);
    });
    let saldo_capita_cliente = parseFloat($('#saldo_capita_cliente').val());
    let couta_mensual_cliente = parseFloat($('#couta_mensual_cliente').val());
    let cuota_ampliacion_cliente = parseFloat($('#cuota_ampliacion_cliente').val());
    
    let saldo_capita_pareja = parseFloat($('#saldo_capita_pareja').val());
    let couta_mensual_pareja = parseFloat($('#couta_mensual_pareja').val());
    let cuota_ampliacion_pareja = parseFloat($('#cuota_ampliacion_pareja').val());

    let total_saldo_capital = total_saldo + saldo_capita_cliente + saldo_capita_pareja;
    let total_couta_mensual = total_cuota + couta_mensual_cliente + couta_mensual_pareja;
    let total_couta_ampliacion = total_ampliacion + cuota_ampliacion_cliente + cuota_ampliacion_pareja;
    
    let modalidad_credito = parseFloat("{{ $credito->idmodalidad_credito }}");
    if( ( modalidad_credito == 2 || modalidad_credito == 3 ) && (total_couta_ampliacion == 0)){
        $('#error_deudas').removeClass('d-none')
        //$('#btn_guardar').attr('disabled',true);
    }else{
        $('#error_deudas').addClass('d-none')
        //$('#btn_guardar').attr('disabled',false);
        // validar boton
        let resultado_cuota_excedente = parseFloat($('#resultado_cuota_excedente').val());
        let rango_tope = parseFloat("{{ configuracion($tienda->id,'rango_tope_dependiente')['valor'] }}")/100;
        if (resultado_cuota_excedente < 0) {
          //$('#btn_guardar').attr('disabled',true);
        } else if (resultado_cuota_excedente <= rango_tope) {
          //$('#btn_guardar').attr('disabled',false);
        } else {
          //$('#btn_guardar').attr('disabled',true);
        }
    }
  
    
    $('#total_saldo_capital').val(total_saldo_capital.toFixed(2));
    $('#total_couta_mensual').val(total_couta_mensual.toFixed(2));
    $('#total_couta_ampliacion').val(total_couta_ampliacion.toFixed(2));
    suma_egreso_mensual();
    
        
  }
  function suma_entidad_financiera(){
    let entidad_financiera_cliente = parseFloat($('#entidad_financiera_cliente').val())
    let entidad_financiera_pareja = parseFloat($('#entidad_financiera_pareja').val())
    let entidad_financiera_total = entidad_financiera_cliente + entidad_financiera_pareja
    $('#entidad_financiera_total').val(entidad_financiera_total.toFixed(0))
  }
  calcula_excedente_mensual();
  function calcula_excedente_mensual(){
    let total_ingresos_mensuales = parseFloat($('#total_ingresos_mensuales').val());
    let total_egresos_mensuales = parseFloat($('#total_egresos_mensuales').val());
    let total_couta_ampliacion = parseFloat($('#total_couta_ampliacion').val());

    let excedente_mensual_disponible = total_ingresos_mensuales - total_egresos_mensuales + total_couta_ampliacion
    $('#excedente_mensual_disponible').val(excedente_mensual_disponible.toFixed(2))

    // CALCULO CUOTA EXCEDENTE

    let total_propuesta = parseFloat($('#total_propuesta').val());
    let resultado_cuota_excedente = (total_propuesta / excedente_mensual_disponible) * 100;
    $('#resultado_cuota_excedente').val(resultado_cuota_excedente.toFixed(2))

    let rango_tope = parseFloat("{{ configuracion($tienda->id,'rango_tope_dependiente')['valor'] }}");
    //console.log(resultado_cuota_excedente)
    //console.log(rango_tope)
    let estado_evaluacion = '-----';
    if (resultado_cuota_excedente < 0) {
      $('#estado_evaluacion').removeClass('bg-success text-white');
      $('#estado_evaluacion').addClass('bg-danger text-white');
      //$('#btn_guardar').attr('disabled',true);
      estado_evaluacion = "CRÉDITO NO VIABLE";
    } else if (resultado_cuota_excedente <= rango_tope) {
      $('#estado_evaluacion').addClass('bg-success text-white');
      $('#estado_evaluacion').removeClass('bg-danger text-white');
      estado_evaluacion = "CRÉDITO VIABLE";
      //$('#btn_guardar').attr('disabled',false);
      // validar boton
      let modalidad_credito = parseFloat("{{ $credito->idmodalidad_credito }}");
      if( ( modalidad_credito == 2 || modalidad_credito == 3 ) && (total_couta_ampliacion == 0)){
          //$('#btn_guardar').attr('disabled',true);
      }else{
          //$('#btn_guardar').attr('disabled',false);
      }
    } else {
      $('#estado_evaluacion').removeClass('bg-success text-white');
      $('#estado_evaluacion').addClass('bg-danger text-white');
      estado_evaluacion = "CRÉDITO NO VIABLE";
      //$('#btn_guardar').attr('disabled',true);
    }
    $('#estado_evaluacion').val(estado_evaluacion);

    
  }
  
  $('#table-ingresos-mensuales ').on('input', suma_ingreso_mensual);
  $('#table-egresos-mensuales').on('input', suma_egreso_mensual);
  $('#table-deudas').on('input', suma_deudas);
  $('#table-entidad-financiera').on('input', suma_entidad_financiera);
  $('#table-propuesta').on('input', calcula_excedente_mensual);
</script>    