<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/credito/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'propuesta_credito',
              fenomenos: json_fenomenos(),
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
      },this)"> 
    
  
    <div class="modal-header" style="border-bottom: 0;">
        <h5 class="modal-title">PROPUESTA DE CRÉDITO </h5>
        <button type="button" class="btn-close text-white" id="modal-close-garantia-cliente" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    @php
      $evaluacion_meses = $credito_evaluacion_cuantitativa ? ( $credito_evaluacion_cuantitativa->evaluacion_meses == "" ? [] : json_decode($credito_evaluacion_cuantitativa->evaluacion_meses) ) : [];
      $entidad_noregulada = $credito_cuantitativa_deudas ? ( $credito_cuantitativa_deudas->entidad_noregulada == "" ? [] : json_decode($credito_cuantitativa_deudas->entidad_noregulada) ) : [];
      $vinculacion_deudor = $credito_cuantitativa_control_limites ? ( $credito_cuantitativa_control_limites->vinculacion_deudor == "" ? [] : json_decode($credito_cuantitativa_control_limites->vinculacion_deudor) ) : [];
  
      $lista_fenomenos = $credito_propuesta ? ( $credito_propuesta->fenomenos == "" ? [] : json_decode($credito_propuesta->fenomenos) ) : [];
    @endphp
    <div class="modal-body modal-body-cualitativa">
      <div class="row">
        <div class="col-sm-12 col-md-6">
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">AGENCIA/OFICINA:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $tienda->nombreagencia }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">TIPO DE CRÉDITO:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $credito->forma_credito_nombre }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">TIPO DE CLIENTE:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $credito->tipo_operacion_credito_nombre }}" disabled>
            </div>
          </div>
          
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">PRODUCTO:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $credito->nombreproductocredito }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">MODALIDAD:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $credito->modalidad_credito_nombre }}" disabled>
            </div>
          </div>
        </div>

        <div class="col-sm-12 col-md-6">
          <div class="row">
            <label class="col-sm-5 col-form-label" style="text-align: right;">NRO SOLICITUD:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="S{{ str_pad($credito->id, 8, '0', STR_PAD_LEFT)  }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-5 col-form-label" style="text-align: right;">CÓD. CLIENTE:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->codigo_cliente }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-5 col-form-label" style="text-align: right;">FECHA:</label>
            <div class="col-sm-7">
              <input type="date" step="any" class="form-control" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->fecha : date('Y-m-d') }}" disabled>
            </div>
          </div>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block">INFORMACIÓN DEL CLIENTE E INGRESO:</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-6">
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">CLIENTE/RAZON SOCIAL:</label>
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
        <div class="col-sm-12 col-md-6">
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
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-6">
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">TIPO DE INGRESO PRINCIPAL:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $users_prestamo->idfuenteingreso == 1 ? 'INDEPENDIENTE' : 'DEPENDIENTE' }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">GIRO ECONÓMICO:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control" value="{{ $credito_evaluacion_cualitativa->nombregiro_economico_evaluacion }}" disabled>
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-6">
          <?php
            $suma_saldo = array_sum(array_column(array_filter($entidad_noregulada, function($dato) {
                return $dato->tipo_entidad === true;
            }), 'saldo_capital_origen'));
            $valor_serif = '0';
            if( $suma_saldo > 0 ){
              $valor_serif = $credito->nombreclientecredito;
            }
          ?>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Vinculación con:</th>
                <th>{{ $tienda->nombre }}</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan=2><input type="text" class="form-control" disabled value="{{ $valor_serif }}"></td>
              </tr>
              @foreach($vinculacion_deudor as $value)
              <tr>
                <td colspan=2><input type="text" class="form-control" disabled value="{{ $value->cliente }}"></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block">PROPUESTA DE CRÉDITO:</span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <table class="table">
            <tbody>
              <tr>
                <td>Monto a Financiar:</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="monto_financiar" value="{{ $credito_cuantitativa_deudas->propuesta_monto }}"></td>
                <td>Monto a Solicitado:</td>
                <td><input type="text" class="form-control" disabled id="monto_solicitado" value="{{ $credito->monto_solicitado }}"></td>
                <td>Periodo de Gracia:</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="dias_de_gracia" value="{{ $diasdegracia }}"></td>
                <td>TEM(%):</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="propuesta_tem" value="{{ $credito_cuantitativa_deudas->propuesta_tem }}"></td>
              </tr>
              <tr>
                
                <td>F. Pago:</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="nombre_forma_pago_credito" value="{{ $credito_cuantitativa_deudas->nombre_forma_pago_credito }}"></td>
                <td></td>
                <td></td>
                <td>Cuota de Pago</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="propuesta_total_pagar" value="{{ $credito_cuantitativa_deudas->propuesta_total_pagar }}"></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Plazo:</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="propuesta_cuotas" value="{{ $credito_cuantitativa_deudas->propuesta_cuotas }}"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-sm-12 col-md-6">
          <table class="table table-bordered" id="table-garantia-cliente">
            <thead>
              <tr>
                <th>Garantías presentadas por el cliente</th>
                <th>Descripción de garantía en Propuesta</th>
                <th>Valor de mercado (S/.)</th>
                <th>Valor comercial (Tasado) (S/.)</th>
                <th>Valor de realización(tasado) (S/.)</th>
              </tr>
            </thead>
            <tbody>
              @forelse($credito_garantias_cliente as $key => $value)
                <tr sumar_garantia>
                  <td>{{ $value->nombretipogarantia }}</td>
                  <td>{{ $value->descripcion_garantia }}</td>
                  <td class="campo_moneda">{{ $value->valor_mercado_garantia }}</td>
                  <td class="campo_moneda">{{ $value->valor_comercial_garantia }}</td>
                  <td class="campo_moneda">{{ $value->valor_realizacion_garantia }}</td>
                </tr>
              @empty
              <tr sumar_garantia>
                <td>Sin Garantia</td>
                <td><input type="text" class="form-control color_cajatexto campo_moneda" disabled monto_garantia_cliente value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->saldo_noprendario_cliente : '0.00' }}" onkeyup="cal_total_garantia_cliente()" id="saldo_noprendario_cliente"></td>
                <td><input type="text" disabled class="form-control campo_moneda" id="propuesta_general" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->propuesta_monto : '0.00' }}"></td>
              </tr>
              @endforelse
              
            </tbody>
          </table>
          <br>
          <table>
            <tbody>
              <tr>
                <td>Clasificación NORMAL en S. Fin. últimos 6 meses:</td>
                <td>Cliente</td>
                <td>
                  <select class="form-control" id="idclasificacion_cliente">
                    @foreach($calificacion_cliente as $value)
                      <option value="{{ $value->id }}" <?php echo $value->id == ( $credito_propuesta ? $credito_propuesta->idclasificacion_cliente : 0 ) ? 'selected' : ''; ?> >{{ $value->nombre }}</option>
                    @endforeach
                  </select>
                </td>
                <td>meses</td>
              </tr>
              <tr>
                <td></td>
                <td>Prja./R. Leg.</td>
                <td>
                  <select class="form-control" id="idclasificacion_cliente_pareja">
                    @foreach($calificacion_cliente as $value)
                      <option value="{{ $value->id }}" <?php echo $value->id == ( $credito_propuesta ? $credito_propuesta->idclasificacion_cliente_pareja : 0 ) ? 'selected' : ''; ?>>{{ $value->nombre }}</option>
                    @endforeach
                  </select>
                </td>
                <td>meses</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-sm-12 col-md-6">
          <table class="table table-bordered" id="table-garantia-aval">
            <thead>
              <tr>
                <th>Garantías presentadas por el cliente</th>
                
                <th>Descripción de garantía en Propuesta</th>
                <th>Valor de mercado (S/.)</th>
                <th>Valor comercial (Tasado) (S/.)</th>
                <th>Valor de realización(tasado) (S/.)</th>
              </tr>
            </thead>
            <tbody>           
              @forelse($credito_garantias_aval as $key => $value)
                <tr >
                  <td>{{ $value->nombretipogarantia }}</td>
                  <td>{{ $value->descripcion_garantia }}</td>
                  
                  <td>{{ $value->valor_mercado_garantia }}</td>
                  <td>{{ $value->valor_comercial_garantia }}</td>
                  <td>{{ $value->valor_realizacion_garantia }}</td>
                </tr>
              @empty
              <tr sumar_garantia>
                  <td>Sin Garantia</td>
                  <td><input type="text" class="form-control campo_moneda" monto_garantia_cliente disabled value="{{ $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->saldo_noprendario_aval : '0.00' }}" onkeyup="cal_total_garantia_aval()" id="saldo_noprendario_aval"></td>
                <td class="color_totales"><input type="text" disabled class="form-control campo_moneda" monto_garantia_cliente id="propuesta_general_aval" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->propuesta_monto : '0.00' }}"></td>
                </tr>
              @endforelse
            </tbody>
          </table>
          <br>
          <table>
            <tbody>
              <tr>
                <td>Clasificación NORMAL en S. Fin. últimos 6 meses:</td>
                <td>Cliente</td>
                <td>
                  <select class="form-control" id="idclasificacion_aval">
                    @foreach($calificacion_cliente as $value)
                      <option value="{{ $value->id }}" <?php echo $value->id == ( $credito_propuesta ? $credito_propuesta->idclasificacion_aval : 0 ) ? 'selected' : ''; ?>>{{ $value->nombre }}</option>
                    @endforeach
                  </select>
                </td>
                <td>meses</td>
              </tr>
              <tr>
                <td></td>
                <td>Prja.</td>
                <td>
                  <select class="form-control" id="idclasificacion_aval_pareja">
                    @foreach($calificacion_cliente as $value)
                      <option value="{{ $value->id }}" <?php echo $value->id == ( $credito_propuesta ? $credito_propuesta->idclasificacion_aval_pareja : 0 ) ? 'selected' : ''; ?>>{{ $value->nombre }}</option>
                    @endforeach
                  </select>
                </td>
                <td>meses</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block">DESTINO DEL CRÉDITO:</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-12">
          <table class="table">
            <tbody>
              <tr>
                <td>Destino:</td>
                <td style="width:300px"><input type="text" class="form-control" disabled id="tipo_destino_credito_nombre" value="{{ $credito->tipo_destino_credito_nombre}}"></td>
                <td style="width:100px"><input type="text" class="form-control campo_moneda" disabled id="monto_destino_credito" value="{{ $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->propuesta_monto : '0.00' }}"></td>
                <td style="width:100px">Detalle:</td>
                <td><input type="text" class="form-control" disabled id="detalle_destino_credito" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->detalle_destino_prestamo : '' }}"></td>
              </tr>
              <tr>
                <td></td>
                <td>Monto para compra / Ampliación de deuda</td>
                <td>
                  <input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" id="monto_compra_deuda" value="{{ $credito_propuesta ? $credito_propuesta->monto_compra_deuda : '0.00' }}" onkeyup="calcula_neto_destino_credito()" onkeydown="calcula_neto_destino_credito()">
                  <span class="text-danger d-none" id="error_monto_compra">Debe ingresar un monto mayor a 0.00</span>
                </td>
                <td>Detalle:</td>
                <td><input type="text" class="form-control color_cajatexto" id="detalle_monto_compra_deuda" value="{{ $credito_propuesta ? $credito_propuesta->detalle_monto_compra_deuda : '' }}"></td>
              </tr>
              <tr>
                <td></td>
                <td>Neto (S/)</td>
                <td><input type="text" class="form-control campo_moneda" disabled id="neto_destino_credito" value="{{ $credito_propuesta ? $credito_propuesta->neto_destino_credito : '0.00' }}"></td>
              </tr>
            </tbody>
          </table>
          
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block">SOBRE EL NEGOCIO:</span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <table class="table">
            <tbody>
              <tr>
                <td>SECTOR ECONÓMICO:</td>
                <td><input type="text" class="form-control" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->nombretipo_giro_economico : '' }}" disabled></td>
                <td width="100px"></td>
                <td>Forma de ejercicio:</td>
                <td><input type="text" class="form-control" disabled id="negocio_forma_ejercicio" value="{{ $users_prestamo->db_idforma_ac_economica }}"></td>
                <td>Otros Ingresos: 
                <?php
                  $negocio_otros_ingresos = '';
                  if ($credito_evaluacion_cualitativa->saladario_fijo === "SI") {
                    $negocio_otros_ingresos = "SI";
                  } else if ($credito_evaluacion_cualitativa->alquiler_local === "SI") {
                    $negocio_otros_ingresos = "SI";
                  } else if ($credito_evaluacion_cualitativa->normas_municipales === "SI") {
                    $negocio_otros_ingresos = "Si";
                  } else if ($credito_evaluacion_cualitativa->pago_impuestos_dia === "SI") {
                    $negocio_otros_ingresos = "SI";
                  } else {
                    $negocio_otros_ingresos = "NO";
                  }
                  
                  $negocio_cantidad_ventas_altas = 0;
                  $negocio_cantidad_ventas_bajas = 0;
                  
                  foreach ($evaluacion_meses[1] as $key => $value) {
                    if($key != 'Mes' ){
                      if(floatval($value->value) > 100) {
                        $negocio_cantidad_ventas_altas++;
                      }
                      if(floatval($value->value) < 100) {
                        $negocio_cantidad_ventas_bajas++;
                      }
                    }
                  }
                  
                ?>
                </td>
                <td><input type="text" class="form-control" disabled id="negocio_otros_ingresos" value="{{ $negocio_otros_ingresos }}"></td>
              </tr>
              <tr>
                <td>Instalaciones:</td>
                <td><input type="text" class="form-control" disabled id="negocio_instalaciones" value="{{ $users_prestamo->db_idlocalnegocio_ac_economica }}"></td>
                <td></td>
                <td>Número de trabajadores:</td>
                <td><input type="text" class="form-control" disabled id="negocio_nro_trabajadores" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->nro_trabajador_completo + $credito_evaluacion_cualitativa->nro_trabajador_parcal : '0' }}"></td>
              </tr>
              <tr>
                <td>Experiencia como empresario:</td>
                <td><input type="text" class="form-control" disabled id="negocio_experiencia_empresario" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->experiencia_microempresa : '0' }}"></td>
                <td>meses</td>
                <td>Meses de ventas altas:</td>
                <td><input type="text" class="form-control" disabled id="negocio_cantidad_ventas_altas" value="{{ $negocio_cantidad_ventas_altas }}"></td>
              </tr>
              <tr>
                <td>Tiempo en el mismo local:</td>
                <td><input type="text" class="form-control" disabled id="negocio_mismo_local" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->tiempo_mismo_local : '0' }}"></td>
                <td>meses</td>
                
                <td>Meses de venta bajas:</td>
                <td><input type="text" class="form-control" disabled id="negocio_cantidad_ventas_bajas" value="{{ $negocio_cantidad_ventas_bajas }}"></td>
              </tr>
              <tr>
                <td>Descripción de la actividad:</td>
                <td colspan=6><input type="text" class="form-control" disabled id="negocio_descripcion_actividad" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->descripcion_actividad : '' }}"></td>
              </tr>
            </tbody>
          </table>
          <table class="table table-bordered" id="table-fenomeno">
            <thead>
              <tr>
                <th width="200px">Afecto a Fenomeno coyuntural</th>
                <th>Descripción</th>
                <th width="10px"><button type="button" class="btn btn-success" onclick="agregar_fenomeno()"><i class="fa fa-plus"></i></button></th>
              </tr>
            </thead>
            <tbody>
              @foreach($lista_fenomenos as $value)
                <tr>
                  <td>
                    <select class="form-control" fenomeno>
                      @foreach($fenomenos as $fen_value)
                        <option value="{{ $fen_value->id }}" <?php echo $fen_value->id==$value->fenomeno ? 'selected' : ''; ?> >{{ $fen_value->nombre }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td><input type="text" descripcion class="form-control color_cajatexto" value="{{ $value->descripcion }}"></td>
                  <td><button type="button" onclick="eliminar_fenomeno(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                </tr>
              @endforeach
            </tbody>
          </table>
          <script>
            function agregar_fenomeno(){
              let btn_eliminar = `<button type="button" onclick="eliminar_fenomeno(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button>` ;
              let option = ``;
              @foreach($fenomenos as $value)
                option += `<option value="{{ $value->id }}">{{ $value->nombre }}</option>`
              @endforeach
              let tabla = `<tr>
                            <td><select class="form-control" fenomeno>${option}</select></td>
                            <td><input type="text" descripcion class="form-control color_cajatexto"></td>
                            <td>${btn_eliminar}</td>
                          </tr>`;

              $(`#table-fenomeno > tbody`).append(tabla);

            }
            function eliminar_fenomeno(e){
              let path = $(e).closest('tr');
              path.remove();
            }
            function json_fenomenos(){
              let data = [];
              $(`#table-fenomeno > tbody > tr`).each(function() {
                  let fenomeno = $(this).find('td select[fenomeno]').val();
                  let descripcion = $(this).find('td input[descripcion]').val();
                  data.push({ 
                      fenomeno: fenomeno,
                      descripcion: descripcion,
                  });
              });
              return JSON.stringify(data);
            }
          </script>
        </div>
      </div>
      
      <div class="mb-1 mt-2 {{ $_GET['tipo'] == 'evaluacion_resumida' ? 'd-none' : '' }} ">
        <span class="badge d-block">RESULTADOS DE EVALUACIÓN :</span>
      </div>
      <div class="row {{ $_GET['tipo'] == 'evaluacion_resumida' ? 'd-none' : '' }}"> 
        <div class="col-sm-12">
          <table class="table">
            <thead>
              <tr>
                <th width="200px">Indicadores</th>
                <th width="10px"></th>
                <th width="70px">Ratios</th>
                <th>Resultado</th>
                <th colspan=2>Comentarios</th>
                <th width="210px">Exigencias/Particularidades</th>
              </tr>
            </thead>
            <tbody>
              <?php
                
                $tem_propuesta = $credito_cuantitativa_deudas ? $credito_cuantitativa_deudas->propuesta_tem : 0;
                $rango_menor = configuracion($tienda->id,'rango_menor')['valor'];
                $rango_tope = configuracion($tienda->id,'rango_tope')['valor'];
                $tope_capital_asignado = configuracion($tienda->id,'capital_asignado')['valor'];
                $relacion_couta_ingreso = configuracion($tienda->id,'relacion_couta_ingreso')['valor'];
                $relacion_cuota_venta = configuracion($tienda->id,'relacion_cuota_venta')['valor'];
                // Fila 01
                $rentabilidad_negocio = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_negocio : 0;  
                $rentabilidad_negocio_res = "Ver ROS";
                if( $rentabilidad_negocio > 0 && $rentabilidad_negocio > $tem_propuesta){
                  $rentabilidad_negocio_res = "Rentabilidad de Capital de Trabajo Adecuada";
                }
                $rentabilidad_negocio_res_coment = "--";
                if( $rentabilidad_negocio > 0){
                  $rentabilidad_negocio_res_coment = $rentabilidad_negocio;
                }
                // Fila 02
                $rentabilidad_ventas = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_ventas : 0;
                if ($rentabilidad_ventas > $tem_propuesta) {
                    $rentabilidad_ventas_res = "Rendimiento de Ventas Eficientes";
                }elseif($rentabilidad_ventas < $tem_propuesta) {
                    $rentabilidad_ventas_res = "Rendimiento de Ventas Moderados";
                }else{
                    $rentabilidad_ventas_res = 0;
                }
              
                $rentabilidad_ventas_res_coment = 0;
                if( $rentabilidad_ventas > 0 ){
                  $rentabilidad_ventas_res_coment = $rentabilidad_ventas;
                }
                // Fila 03
                $rentabilidad_unidad_familiar = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_unidadfamiliar : 0;
                $rentabilidad_unidad_familiar_res = '';
                if($rentabilidad_unidad_familiar > 1) {
                  $rentabilidad_unidad_familiar_res = "Riesgo Normal - Continuar Propuesta";
                }elseif($rentabilidad_unidad_familiar <= 1) {
                  $rentabilidad_unidad_familiar_res = "Alto Riesgo - Suspender Propuesta";
                }
                if($rentabilidad_unidad_familiar > 1) {
                  $rentabilidad_unidad_familiar_res_coment = "Sus ingresos totales del cliente PUEDE cubrir sus gastos familiares en más de 1 vez";
                }else{
                  $rentabilidad_unidad_familiar_res_coment = "Los ingresos totales del cliente NO son capaces de cubrir sus gastos familiares";
                }
                // Fila 04
                $rentabilidad_patrimonial = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_patrimonial : 0;
                $rentabilidad_activos = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_activos : 0;
                if($rentabilidad_patrimonial > $rentabilidad_activos && $rentabilidad_patrimonial > 0.05) {
                  $rentabilidad_patrimonial_res = "Adecuada Rentabilidad de los fondos propios invertidos en el negocio";
                }else{
                  $rentabilidad_patrimonial_res = "Débil Rentabilidad de los fondos propios invertidos en el negocio";
                }
                // Fila 05
                if ($rentabilidad_activos > 0.05) {
                  $rentabilidad_activos_res = "Rentabilidad Adecuada de las inversiones en el negocio";
                } else {
                  $rentabilidad_activos_res = "Rentabilidad Débil de las inversiones en el negocio";
                }
                // SOLVENCIA
                // Fila 06
                $solvencia_liquidez = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_liquidez : 0;
                if($solvencia_liquidez > 0 && $solvencia_liquidez >= 1) {
                  $solvencia_liquidez_res = "Solvente (Capacidad) puede asumir sus deudas a corto plazo - Continuar Propuesta";
                }else{
                  $solvencia_liquidez_res = "Insolvencia Técnica - Observar Propuesta";
                }
                // Fila 07
                $solvencia_liquidez_acida = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_liquidez_acida : 0;
                if ($solvencia_liquidez_acida >= 1) {
                    $solvencia_liquidez_acida_res = "CUENTA con Efectivo Inmediato para pagar deudas";
                } else {
                    $solvencia_liquidez_acida_res = "NO cuenta con Efectivo inmediato para Pagar deudas";
                }
                // Fila 08
                $solvencia_endeudamiento_propuesta = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_endeudamiento_propuesta : 0;
                if ($solvencia_endeudamiento_propuesta < 0.8) {
                    $solvencia_endeudamiento_propuesta_res = "TIENE autonomía financiera - Proceder Propuesta";
                } else {
                    $solvencia_endeudamiento_propuesta_res = "NO tiene autonomía financiera - Observar Propuesta";
                }
                if ($solvencia_endeudamiento_propuesta < 1) {
                    $solvencia_endeudamiento_propuesta_res_coment = "TIENE respaldo patrimonial para asumir la deuda propuesta";
                } else {
                    $solvencia_endeudamiento_propuesta_res_coment = "NO tiene respaldo patrimonial para asumir la deuda propuesta";
                }
                // Fila 09
                $solvencia_cuota_total = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_cuota_total : 0 ;
                if ($solvencia_cuota_total <= $rango_menor) {
                    $solvencia_cuota_total_res = "No evidencia Sobreendeudamiento - Existe Cobertura";
                } else {
                    $solvencia_cuota_total_res = "Evidencia Cobertura con signo de Sobreendeudamiento";
                }
                // Fila 10
                $solvencia_capital_trabajo_neto = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_prestamo : 0;
                if ($solvencia_capital_trabajo_neto < 0.8) {
                    $solvencia_capital_trabajo_neto_res = "Financiamiento dentro de lo Permisible";
                } else {
                    $solvencia_capital_trabajo_neto_res = "Financiamiento fuera de límite permisible";
                }
               if ($solvencia_capital_trabajo_neto < 0.8) {
                    $solvencia_capital_trabajo_neto_res_coment = "Evidencia que se está financiando menos del capital de trabajo neto que tiene";
                } else {
                    $solvencia_capital_trabajo_neto_res_coment = "Muestra que se está financiando más que el capital de trabajo neto del cliente";
                }
                // Fila 11
                $solvencia_capital_trabajo = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_capital : 0 ; 
                // GESTON 
                // Fila 12
                $gestion_rotacion_inventario = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_rotacion_inventario : 0;
                if ($gestion_rotacion_inventario < 7) {
                    $gestion_rotacion_inventario_res = "Negocio de Rotación MUY ALTA de ventas";
                } elseif ($gestion_rotacion_inventario <= 45) {
                    $gestion_rotacion_inventario_res = "Negocio de Rotación ALTA de ventas";
                } elseif ($gestion_rotacion_inventario <= 90) {
                    $gestion_rotacion_inventario_res = "Negocio de Rotación USUAL de ventas";
                } else {
                    $gestion_rotacion_inventario_res = "Negocio de Rotación LENTA de ventas";
                }
                if ($gestion_rotacion_inventario > 0) {
                    $gestion_rotacion_inventario_res_coment = "El inventario del cliente se renueva cada "; // Puedes reemplazar el punto con un espacio si es necesario
                } else {
                    $gestion_rotacion_inventario_res_coment = "-----";
                }
                // Fila 13
                $gestion_promedio_cobranza = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_promedio_cobranza : 0;
                if ($gestion_promedio_cobranza <= 45) {
                    $gestion_promedio_cobranza_res = "Gestión ADECUADA de sus ventas a crédito";
                } else {
                    $gestion_promedio_cobranza_res = "Gestión INADECUADA de sus ventas a crédito";
                }
                if ($gestion_promedio_cobranza > 0) {
                    $gestion_promedio_cobranza_res_coment = "El cliente realiza el cobro de sus ventas a crédito cada "; // Puedes reemplazar el punto con un espacio si es necesario
                } else {
                    $gestion_promedio_cobranza_res_coment = "-----";
                }
                // Fila 14
                $gestion_promedio_pago = $credito_evaluacion_cuantitativa ? $credito_evaluacion_cuantitativa->ratio_re_primedio_pago : 0;
                if ($gestion_promedio_pago > $gestion_promedio_cobranza) {
                    $gestion_promedio_pago_res = "EXISTE calce a sus obligaciones con su proveedor";
                } else {
                    $gestion_promedio_pago_res = "NO existe calce a sus obligaciones con su proveedor";
                }
                if ($gestion_promedio_pago < $gestion_promedio_cobranza) {
                  $gestion_promedio_pago_res_coment = "Pago a proveedores usualmente puntual";
                } else {
                  $gestion_promedio_pago_res_coment = "Pago a proveedores con retrasos";
                }
                // LIMITES
                // Fila 15
                $limites_financiamiento_vru = $credito_cuantitativa_control_limites ? $credito_cuantitativa_control_limites->porcentaje_resultado : 0;
                $limites_financiamiento_vru_res = ($limites_financiamiento_vru <= $tope_capital_asignado) ? "Dentro del límite permisible" : "Fuera de límite permisible";
                
                ############## RESULTADO EVALUACION RESUMIDA #############
                // SOLVENCIA 
                // Fila 01
                $res_solvencia_relacion_cuota = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->indicador_solvencia_cuotas : 0;
                if ($res_solvencia_relacion_cuota <= $rango_tope) {
                    $res_solvencia_relacion_cuota_res = "No evidencia Sobreendeudamiento EXISTE COBERTURA";
                } elseif ($res_solvencia_relacion_cuota > $rango_tope) {
                    $res_solvencia_relacion_cuota_res = "Evidencia Sobreendeudamiento NO EXISTE COBERTURA";
                } else {
                    $res_solvencia_relacion_cuota_res = 0;
                }
                // OTROS RATIOS
                // Fila 02
                $res_ratios_cuota_ingreso_mensual = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_mensual : 0;
                if ($res_ratios_cuota_ingreso_mensual <= $relacion_couta_ingreso) {
                    $res_ratios_cuota_ingreso_mensual_res = "Dentro del rango establecido";
                } elseif ($res_ratios_cuota_ingreso_mensual > $relacion_couta_ingreso) {
                    $res_ratios_cuota_ingreso_mensual_res = "Fuera del rango establecido";
                }
                if ($res_ratios_cuota_ingreso_mensual <= $relacion_couta_ingreso) {
                    $resultado = "VIABLE con segunda opción, para cumplir cuotas de pago a muy corto plazo";
                } elseif ($res_ratios_cuota_ingreso_mensual > $relacion_couta_ingreso) {
                    $resultado = "NO VIABLE con segunda opción, para cumplir cuotas de pago a muy corto plazo";
                } else {
                    $resultado = 0;
                }
                // Fila 03
                $res_ratios_venta_cuota_diaria = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_venta_diaria : 0;
                if ($res_ratios_venta_cuota_diaria <= $relacion_cuota_venta) {
                  $res_ratios_venta_cuota_diaria_res = "Dentro del rango establecido";
                } elseif ($res_ratios_venta_cuota_diaria > $relacion_cuota_venta) {
                  $res_ratios_venta_cuota_diaria_res = "Fuera del rango establecido";
                } else {
                  $res_ratios_venta_cuota_diaria_res = 0;
                }
              
                if ($res_ratios_venta_cuota_diaria <= $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_diaria_res_coment = "VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
                } elseif ($res_ratios_venta_cuota_diaria > $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_diaria_res_coment = "NO VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
                }
                // Fila 04
                $res_ratios_venta_cuota_semanal = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_venta_semanal : 0;
                if ($res_ratios_venta_cuota_semanal <= $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_semanal_res = "Dentro del rango establecido";
                } elseif ($res_ratios_venta_cuota_semanal > $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_semanal_res = "Fuera del rango establecido";
                } else {
                    $res_ratios_venta_cuota_semanal_res = 0;
                }
              
                if ($res_ratios_venta_cuota_semanal <= $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_semanal_res_coment = "VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
                } elseif ($res_ratios_venta_cuota_semanal > $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_semanal_res_coment = "NO VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
                }
                // Fila 05
                $res_ratios_venta_cuota_quincenal = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_venta_quincenal : 0;
                if ($res_ratios_venta_cuota_quincenal <= $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_quincenal_res = "Dentro del rango establecido";
                } elseif ($res_ratios_venta_cuota_quincenal > $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_quincenal_res = "Fuera del rango establecido";
                } else {
                    $res_ratios_venta_cuota_quincenal_res = 0;
                }
                if ($res_ratios_venta_cuota_quincenal <= $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_quincenal_res_coment = "VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
                } elseif ($res_ratios_venta_cuota_quincenal > $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_quincenal_res_coment = "NO VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
                }
                // Fila 06
                $res_ratios_venta_cuota_mensual = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->relacion_cuota_venta_mensual : 0;
                if ($res_ratios_venta_cuota_mensual <= $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_mensual_res = "Dentro del rango establecido";
                } elseif ($res_ratios_venta_cuota_mensual > $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_mensual_res = "Fuera del rango establecido";
                } else {
                    $res_ratios_venta_cuota_mensual_res = 0;
                }
                if ($res_ratios_venta_cuota_mensual <= $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_mensual_res_coment = "VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
                } elseif ($res_ratios_venta_cuota_mensual > $relacion_cuota_venta) {
                    $res_ratios_venta_cuota_mensual_res_coment = "NO VIABLE con ÚLTIMA opción, para cumplir cuotas de pago a muy corto plazo";
                }
                // Fila 07
              ?>
              <tr>
                <th colspan=7><b>RENTABILIDAD</b></th>
              </tr>
              <tr>
                <td>Rentabilidad del negocio</td>
                <td>%</td>
                <td><input type="text" class="form-control" disabled id="rentabilidad_negocio" value="{{ $rentabilidad_negocio }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $rentabilidad_negocio_res }}"></td>
                <td><input type="text" class="form-control" disabled value="Por cada sol invertido gana"></td>
                <td width="100px"><input type="text" class="form-control" disabled value="{{ $rentabilidad_negocio_res_coment }}"></td>
                <td><input type="text" class="form-control" disabled value="Giros de alta rotación o servicios puede ser (-), usar ROS"></td>
              </tr>
              <tr>
                <td>Rentabilidad de las ventas (ROS)</td>
                <td>%</td>
                <td><input type="text" class="form-control" disabled id="rentabilidad_ventas" value="{{ $rentabilidad_ventas }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $rentabilidad_ventas_res }}"></td>
                <td><input type="text" class="form-control" disabled value="Su ganancia mensual por su venta es "></td>
                <td><input type="text" class="form-control" disabled value="{{ $rentabilidad_ventas_res_coment }}"></td>
                <td><input type="text" class="form-control" disabled value="Se sugiere ROS>TEM"></td>
              </tr>
              <tr>
                <td>Rentabilidad de la unidad familiar</td>
                <td>Veces</td>
                <td><input type="text" class="form-control single-subrayado" disabled id="rentabilidad_unidad_familiar" value="{{ $rentabilidad_unidad_familiar }}"></td>
                <td><input type="text" class="form-control single-subrayado" disabled value="{{ $rentabilidad_unidad_familiar_res }}"></td>
                <td colspan=2><input type="text" class="form-control single-subrayado" disabled value="{{ $rentabilidad_unidad_familiar_res_coment }}"></td>
                <td><input type="text" class="form-control" disabled value="Se espera >1"></td>
              </tr>
              <tr>
                <td>Rentabilidad patrimonial (ROE)</td>
                <td>%</td>
                <td><input type="text" class="form-control" disabled id="rentabilidad_patrimonial" value="{{ $rentabilidad_patrimonial }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $rentabilidad_patrimonial_res }}"></td>
                <td colspan=2><input type="text" class="form-control color_cajatexto" id="rentabilidad_patrimonial_res_coment" value="{{ $credito_propuesta ? $credito_propuesta->rentabilidad_patrimonial_res_coment : '' }}"></td>
                <td><input type="text" class="form-control" disabled value="Óptimo ROE>ROA>TEM"></td>
              </tr>
              <tr>
                <td>Rentabilidad de los activos (ROA)</td>
                <td>%</td>
                <td><input type="text" class="form-control" disabled id="rentabilidad_activos" value="{{ $rentabilidad_activos }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $rentabilidad_activos_res }}"></td>
                <td colspan=2><input type="text" class="form-control color_cajatexto" id="rentabilidad_activos_res_coment" value="{{ $credito_propuesta ? $credito_propuesta->rentabilidad_activos_res_coment : '' }}"></td>
                <td><input type="text" class="form-control" disabled value="Si ROA>ROE, endeudamiento fue de impacto  negativo"></td>
              </tr>
              <tr>
                <th colspan=7><b>SOLVENCIA</b></th>
              </tr>
              <tr>
                <td>Liquidez</td>
                <td>Veces</td>
                <td><input type="text" class="form-control" disabled id="solvencia_liquidez" value="{{ $solvencia_liquidez }}"></td>
                <td><input type="text" class="form-control single-subrayado" disabled value="{{ $solvencia_liquidez_res }}"></td>
                <td><input type="text" class="form-control single-subrayado" disabled value="Por cada sol de obligaciones cuenta con  "></td>
                <td><input type="text" class="form-control single-subrayado" disabled value="S/ {{ $solvencia_liquidez }} para pagar en el corto plazo "></td>
                <td><input type="text" class="form-control" disabled value="Se exije >1"></td>
              </tr>
              <tr>
                <td>Liquidez Ácida</td>
                <td>Veces</td>
                <td><input type="text" class="form-control" disabled id="solvencia_liquidez_acida" value="{{ $solvencia_liquidez_acida }}"></td>
                <td><input type="text" class="form-control single-subrayado" disabled value="{{ $solvencia_liquidez_acida_res }}"></td>
                <td><input type="text" class="form-control single-subrayado" disabled value="Por cada sol de obligaciones cuenta de inmediato "></td>
                <td><input type="text" class="form-control single-subrayado" disabled value="S/ {{ $solvencia_liquidez_acida }} para pagar en muy corto plazo"></td>
                <td><input type="text" class="form-control" disabled value="Óptimo >1"></td>
              </tr>
              <tr>
                <td>Endeudamiento Patrim. con propuesta</td>
                <td>Veces</td>
                <td><input type="text" class="form-control" disabled id="solvencia_endeudamiento_propuesta" value="{{ $solvencia_endeudamiento_propuesta }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $solvencia_endeudamiento_propuesta_res }}"></td>
                <td colspan=2><input type="text" class="form-control single-subrayado" disabled value="{{ $solvencia_endeudamiento_propuesta_res_coment }}"></td>
                <td><input type="text" class="form-control" disabled value="<1,/giros de alta rotación o SS puede ser >1"></td>
              </tr>
              <tr>
                <td class="doble-subrayado">Cuota total/excedente total</td>
                <td class="doble-subrayado">%</td>
                <td><input type="text" class="form-control doble-subrayado" disabled id="solvencia_cuota_total" value="{{ $solvencia_cuota_total }}"></td>
                <td><input type="text" class="form-control doble-subrayado" disabled value="{{ $solvencia_cuota_total_res }}"></td>
                <td colspan=2><input type="text" class="form-control color_cajatexto" id="solvencia_cuota_total_res_coment" value="{{ $credito_propuesta ? $credito_propuesta->solvencia_cuota_total_res_coment : '' }}"></td>
                <td><input type="text" class="form-control" disabled value="Se exije < 100% conforme política"></td>
              </tr>
              <tr>
                <td>Préstamo / capital de trabajo Neto</td>
                <td>%</td>
                <td><input type="text" class="form-control" disabled id="solvencia_capital_trabajo_neto" value="{{ $solvencia_capital_trabajo_neto }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $solvencia_capital_trabajo_neto_res }}"></td>
                <td colspan=2><input type="text" class="form-control" disabled value="{{ $solvencia_capital_trabajo_neto_res_coment }}"></td>
                <td><input type="text" class="form-control" disabled value="<1,/giros de alta rotación o SS puede ser >1y (-)"></td>
              </tr>
              <tr>
                <td>Capital de trabajo</td>
                <td>S/</td>
                <td><input type="text" class="form-control" disabled id="solvencia_capital_trabajo" value="{{ $solvencia_capital_trabajo }}"></td>
                <td></td>
                <td colspan=2><input type="text" class="form-control color_cajatexto" id="solvencia_capital_trabajo_res_coment" value="{{ $credito_propuesta ? $credito_propuesta->solvencia_capital_trabajo_res_coment : '' }}" ></td>
                <td><input type="text" disabled class="form-control" value="'Giros de alta rotación y SS puede ser (-)"></td>
              </tr>
              <tr>
                <th colspan=7><b>GESTIÓN</b></th>
              </tr>
              <tr>
                <td>Plazo prom.rotación de invent.</td>
                <td>Días</td>
                <td><input type="text" class="form-control" disabled id="gestion_rotacion_inventario" value="{{ $gestion_rotacion_inventario }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $gestion_rotacion_inventario_res }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $gestion_rotacion_inventario_res_coment }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $gestion_rotacion_inventario }} días"></td>
                <td><input type="text" class="form-control" disabled value=" ---- "></td>
              </tr>
              <tr>
                <td>Plazo promedio de cobranza</td>
                <td>Días</td>
                <td><input type="text" class="form-control" disabled id="gestion_promedio_cobranza" value="{{ $gestion_promedio_cobranza }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $gestion_promedio_cobranza_res }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $gestion_promedio_cobranza_res_coment }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $gestion_promedio_cobranza }} días"></td>
                <td><input type="text" class="form-control" disabled value="Rango de 30 a 45 días"></td>
              </tr>
              <tr>
                <td>Plazo promedio de pago</td>
                <td>Días</td>
                <td><input type="text" class="form-control" disabled id="gestion_promedio_pago" value="{{ $gestion_promedio_pago }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $gestion_promedio_pago_res }}"></td>
                <td colspan=2><input type="text" class="form-control" disabled value="{{ $gestion_promedio_pago_res_coment }}"></td>
                <td><input type="text" class="form-control" disabled value="Lo óptimo es tener calce con cobranza"></td>
              </tr>
              <tr>
                <th colspan=7><b>LIMITES</b></th>
              </tr>
              <tr>
                <td>Financiamiento por VRU</td>
                <td>%</td>
                <td><input type="text" class="form-control" disabled id="limites_financiamiento_vru" value="{{ $limites_financiamiento_vru }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $limites_financiamiento_vru_res }}"></td>
                <td colspan=2><input type="text" class="form-control color_cajatexto" id="limites_financiamiento_vru_res_coment" 
                                     value="{{ $credito_propuesta ? $credito_propuesta->limites_financiamiento_vru_res_coment : '' }}"></td>
                <td><input type="text" class="form-control" disabled value=" --- "></td>
              </tr>
              <tr>
                <td class="doble-subrayado">N° de entidades (Cliente y Pareja)</td>
                <td class="doble-subrayado">N°</td>
                <?php
                    $limites_numero_entidades = 0;
                    if($_GET['tipo'] == 'evaluacion_completa'){
                        $limites_numero_entidades = $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->total_deuda : 0;
                    }else{
                        $limites_numero_entidades = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->total_deuda : 0;
                    }
                    $entidad_maxima = configuracion($tienda->id,'entidades_maxima')['valor'];
                    $limites_numero_entidades_res = '';
                    if ($limites_numero_entidades > $entidad_maxima) {
                      $limites_numero_entidades_res = "Se sugiere no proceder o coverturar la propuesta";
                    } else if ($limites_numero_entidades <= $entidad_maxima) {
                      $limites_numero_entidades_res = "Proceder con propuesta";
                    }
                ?>
                <td><input type="text" class="form-control doble-subrayado" 
                           value="{{ $limites_numero_entidades }}" 
                           id="limites_numero_entidades" disabled></td>
                <td><input type="text" class="form-control doble-subrayado" disabled id="limites_numero_entidades_res" 
                           value="{{ $limites_numero_entidades_res }}"></td>
                <td colspan=2><input type="text" class="form-control color_cajatexto" id="limites_numero_entidades_res_coment" 
                            value="{{ $credito_propuesta ? $credito_propuesta->limites_numero_entidades_res_coment : '' }}"></td>
                <td><input type="text" class="form-control" disabled value=" --- "></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="mb-1 mt-2 {{ $_GET['tipo'] == 'evaluacion_completa' ? 'd-none' : '' }}">
        <span class="badge d-block">RESULTADOS DE EVALUACIÓN RESUMIDA:</span>
      </div>
      <div class="row {{ $_GET['tipo'] == 'evaluacion_completa' ? 'd-none' : '' }}">
        <div class="col-sm-12">
          <table class="table">
            <thead>
              <tr>
                <th width="200px">Indicadores</th>
                <th width="10px"></th>
                <th width="70px">Ratios</th>
                <th>Resultado</th>
                <th colspan=2>Comentarios</th>
                <th width="210px">Exigencias/Particularidades</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th colspan=7><b>SOLVENCIA</b></th>
              </tr>
              <tr>
                <td class="doble-subrayado">Relación cuota/excedente</td>
                <td class="doble-subrayado">%</td>
                <td><input type="text" class="form-control doble-subrayado" disabled id="res_solvencia_relacion_cuota" value="{{ $res_solvencia_relacion_cuota }}"></td>
                <td><input type="text" class="form-control doble-subrayado" disabled value="{{ $res_solvencia_relacion_cuota_res }}"></td>
                <td colspan=2><input type="text" class="form-control color_cajatexto" id="res_solvencia_relacion_cuota_coment" value="{{ $credito_propuesta ? $credito_propuesta->res_solvencia_relacion_cuota_coment : '' }}"></td>
                <td><input type="text" class="form-control" disabled value="Se exije < 100% conforme política"></td>
              </tr>
              <tr>
                <th colspan=7><b>OTROS RATIOS</b></th>
              </tr>
              <tr>
                <td>R. Cuota Mensual/Ingreso Mensual</td>
                <td>%</td>
                <td><input type="text" class="form-control " disabled id="res_ratios_cuota_ingreso_mensual" value="{{ $res_ratios_cuota_ingreso_mensual }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $res_ratios_cuota_ingreso_mensual_res }}"></td>
                <td colspan=2><input type="text" class="form-control" disabled value=""></td>
                <td><input type="text" class="form-control" disabled value="Debe ser <= que {{ configuracion($tienda->id,'relacion_couta_ingreso')['valor'] }}%"></td>
              </tr>
              @if($res_ratios_venta_cuota_diaria > 0)
              <tr>
                <td>R. Cuota diaria/ Venta diaria</td>
                <td>%</td>
                <td><input type="text" class="form-control " disabled id="res_ratios_venta_cuota_diaria" value="{{ $res_ratios_venta_cuota_diaria }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $res_ratios_venta_cuota_diaria_res }}"></td>
                <td colspan=2><input type="text" class="form-control" disabled value="{{ $res_ratios_venta_cuota_diaria_res_coment }}"></td>
                <td><input type="text" class="form-control" disabled value="Debe ser <= que {{ configuracion($tienda->id,'relacion_cuota_venta')['valor'] }}%"></td>
              </tr>
              @endif
              @if($res_ratios_venta_cuota_semanal > 0)
              <tr>
                <td>R. Cuota Semanal/ Venta semanal </td>
                <td>%</td>
                <td><input type="text" class="form-control" disabled id="res_ratios_venta_cuota_semanal" value="{{ $res_ratios_venta_cuota_semanal }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $res_ratios_venta_cuota_semanal_res }}"></td>
                <td colspan=2><input type="text" class="form-control" disabled value="{{ $res_ratios_venta_cuota_semanal_res_coment }}"></td>
                <td><input type="text" class="form-control" disabled value="Debe ser <= que {{ configuracion($tienda->id,'relacion_cuota_venta')['valor'] }}%"></td>
              </tr>
              @endif
              @if($res_ratios_venta_cuota_quincenal > 0)
              <tr>
                <td>R. Cuota Quincenal/ Vta. quincenal </td>
                <td>%</td>
                <td><input type="text" class="form-control " disabled id="res_ratios_venta_cuota_quincenal" value="{{ $res_ratios_venta_cuota_quincenal }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $res_ratios_venta_cuota_quincenal_res }}"></td>
                <td colspan=2><input type="text" class="form-control" disabled value="{{ $res_ratios_venta_cuota_quincenal_res_coment }}"></td>
                <td><input type="text" class="form-control" disabled value="Debe ser <= que {{ configuracion($tienda->id,'relacion_cuota_venta')['valor'] }}%"></td>
              </tr>
              @endif
              @if($res_ratios_venta_cuota_mensual > 0)
              <tr>
                <td>R. Cuota Mensual/Venta Mensual ( Frec. Mensual)</td>
                <td>%</td>
                <td><input type="text" class="form-control" disabled id="res_ratios_venta_cuota_mensual" value="{{ $res_ratios_venta_cuota_mensual }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $res_ratios_venta_cuota_mensual_res }}"></td>
                <td colspan=2><input type="text" class="form-control" disabled value="{{ $res_ratios_venta_cuota_mensual_res_coment }}"></td>
                <td><input type="text" class="form-control" disabled value="Debe ser <= que {{ configuracion($tienda->id,'relacion_cuota_venta')['valor'] }}%"></td>
              </tr>
              @endif
              <tr>
                <?php
                    $limites_numero_entidades = 0;
                    if($_GET['tipo'] == 'evaluacion_completa'){
                        $limites_numero_entidades = $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->total_deuda : 0;
                    }else{
                        $limites_numero_entidades = $credito_evaluacion_resumida ? $credito_evaluacion_resumida->total_deuda : 0;
                    }
                    $entidad_maxima = configuracion($tienda->id,'entidades_maxima')['valor'];
                    $limites_numero_entidades_res = '';
                    if ($limites_numero_entidades > $entidad_maxima) {
                      $limites_numero_entidades_res = "Se sugiere no proceder o coverturar la propuesta";
                    } else if ($limites_numero_entidades <= $entidad_maxima) {
                      $limites_numero_entidades_res = "Proceder con propuesta";
                    }
                ?>
                <td class="doble-subrayado">N° de entidades (Cliente y Pareja)</td>
                <td class="doble-subrayado">N°</td>
                <td><input type="text" class="form-control doble-subrayado" 
                           value="{{ $limites_numero_entidades }}" 
                           id="limites_numero_entidades" disabled></td>
                <td><input type="text" class="form-control doble-subrayado" disabled  value="{{ $limites_numero_entidades_res }}"></td>
                <td colspan=2><input type="text" class="form-control color_cajatexto" value="{{ $credito_propuesta ? $credito_propuesta->limites_numero_entidades_res_coment : '' }}"></td>
                <td><input type="text" class="form-control" disabled value=" --- "></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="row mt-1">
        <div class="col" style="flex: 0 0 0%;">
          <button type="submit" class="btn btn-success" id="boton_guardar"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
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
  valida_input_vacio();
  function calcula_neto_destino_credito(){
    let monto_destino_credito = parseFloat($('#monto_destino_credito').val());
    let monto_compra_deuda = parseFloat($('#monto_compra_deuda').val());
    // let modalidad_credito = 2; PARA PRUEBAS
    let modalidad_credito = parseFloat("{{ $credito->idmodalidad_credito }}");
    if( ( modalidad_credito == 2 || modalidad_credito == 3 ) && (monto_compra_deuda == 0 || monto_compra_deuda == '') ){
      $('#error_monto_compra').removeClass('d-none');
      $('#boton_guardar').attr('disabled',true);
    }else{
      $('#error_monto_compra').addClass('d-none');
      $('#boton_guardar').attr('disabled',false);
    }
    let neto_destino_credito = monto_destino_credito - monto_compra_deuda;
    $('#neto_destino_credito').val(neto_destino_credito.toFixed(2))
  }
  
</script>    