<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/credito/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'evaluacion_cualitativa',
              referencia: seleccionar_referencia()
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
    
  
    <div class="modal-header" style="border-bottom: 0;">
        <h5 class="modal-title">EVALUACIÓN CUALITATIVA </h5>
        <button type="button" class="btn-close text-white" id="modal-close-garantia-cliente" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
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
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">TIPO GIRO ECONÓMICO:</label>
            <div class="col-sm-8">
              <select class="form-control color_cajatexto" id="idtipo_giro_economico" {{ $view_detalle=='false' ? 'disabled' : '' }}>
                @foreach($tipo_giro_economico as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
              </select>
              
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">GIRO ECONÓMICO:</label>
            <div class="col-sm-8">
              <select class="form-control color_cajatexto" id="idgiro_economico_evaluacion" disabled>
               <option value=""></option>
              </select>
              
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">DESCRIPCIÓN DE ACTIVIDAD:</label>
            <div class="col-sm-8">
              <input type="text" step="any" class="form-control color_cajatexto" id="descripcion_actividad" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->descripcion_actividad : '' }}" {{ $view_detalle=='false' ? 'disabled' : ''  }}>
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-3">
          <div class="row">
            <label class="col-sm-3 col-form-label" style="text-align: right;">FECHA:</label>
            <div class="col-sm-7">
              <input type="date" step="any" class="form-control" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->fecha : date('Y-m-d') }}" disabled>
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
        <div class="col-sm-12 col-md-3">
          <div class="row">
            <label class="col-sm-5 col-form-label" style="text-align: right;">NRO SOLICITUD:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="S{{ str_pad($credito->id, 8, '0', STR_PAD_LEFT)  }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-5 col-form-label" style="text-align: right;">PRODUCTO:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->nombreproductocredito }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-5 col-form-label" style="text-align: right;">TIPO DE CAMBIO:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ configuracion($tienda->id,'tipo_cambio_dolar')['valor'] }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-5 col-form-label" style="text-align: right;">TIPO DE CLIENTE:</label>
            <div class="col-sm-7">
              <input type="text" step="any" class="form-control" value="{{ $credito->tipo_operacion_credito_nombre }}" disabled>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-5 col-form-label" style="text-align: right;">MODALIDAD:</label>
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
        <span class="badge d-block">II. EVALUACION CUALITATIVA</span>
      </div>
      
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;
    color: #000;">2.1 N° DE ENTIDADES FINANCIERAS (Se considera deuda interna y Líneas de creditos sin uso)</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-4">
          <table class="table table-bordered" id="tabla-entidadesfinancieras">
            <thead>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;">Deudores</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;">Como</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;text-align: center;" width="100px">N°</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="background-color: #efefef !important;" rowspan="2">CLIENTE</td>
                <td style="background-color: #efefef !important;">P.Natural</td>
                <td style="background-color: #efefef !important;">
                  <input type="text" valida_input_vacio style="padding: 4px;" onkeyup="total_deudas()" 
                         onkeydown="total_deudas()" class="form-control color_cajatexto text-center " 
                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->cantidad_cliente_natural : 0 }}" id="cantidad_cliente_natural" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">P.Jurídica</td>
                <td style="background-color: #efefef !important;"><input type="text" valida_input_vacio style="padding: 4px;" onkeyup="total_deudas()" 
                                                                         onkeydown="total_deudas()" class="form-control color_cajatexto text-center" 
                                                                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->cantidad_cliente_juridico : 0 }}" id="cantidad_cliente_juridico" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;" rowspan="2">PAREJA</td>
                <td style="background-color: #efefef !important;">P.Natural</td>
                <td style="background-color: #efefef !important;"><input type="text" valida_input_vacio style="padding: 4px;" onkeyup="total_deudas()" onkeydown="total_deudas()" 
                                                                         class="form-control color_cajatexto text-center" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->cantidad_pareja_natural : 0 }}" id="cantidad_pareja_natural" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">P.Jurídica</td>
                <td style="background-color: #efefef !important;"><input type="text" valida_input_vacio style="padding: 4px;" onkeyup="total_deudas()" onkeydown="total_deudas()" class="form-control color_cajatexto text-center" 
                                                                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->cantidad_pareja_juridico : 0 }}" id="cantidad_pareja_juridico" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
              </tr>
              <tr>
                <td style="background-color: #c8c8c8 !important;text-align: right;" colspan=2>TOTAL</td>
                <td style="background-color: #c8c8c8 !important;"><input type="number" style="padding: 4px;" disabled class="form-control text-center" 
                                                                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->total_deuda : 0 }}" id="total_deuda" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;
    color: #000;">2.2 GESTIÓN DEL GIRO ECONÓMICO</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-6">
          <table>
            <tr>
              <td width="300px">a) Experiencia como Microempresario(a) (meses)</td>
              <td><input type="text" valida_input_vacio class="form-control color_cajatexto text-center" 
                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->experiencia_microempresa : 0 }}" 
                         id="experiencia_microempresa" style="width:100px;" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
            </tr>
            <tr>
              <td>b) Tiempo en el mismo local (meses)</td>
              <td><input type="text" valida_input_vacio class="form-control color_cajatexto text-center" 
                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->tiempo_mismo_local : 0 }}" 
                         id="tiempo_mismo_local" style="width:100px;" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
            </tr>
            <tr>
              <td>c) Instalaciones o local</td>
              <td>
                @if($users_prestamo->db_idlocalnegocio_ac_economica!='')
                  
                  <input type="text" class="form-control text-center" value="{{ $users_prestamo->db_idlocalnegocio_ac_economica }}" disabled 
                         id="instalacion_local" style="width:100px;">
                @endif
              </td>
            </tr>
            <tr>
              <td>d) N° de trabajadores a tiempo completo</td>
              <td><input type="text" valida_input_vacio class="form-control color_cajatexto text-center" 
                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->nro_trabajador_completo : 0 }}" 
                         id="nro_trabajador_completo" style="width:100px;" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
            </tr>
            <tr>
              <td>e) N° de trabajdores a tiempo parcial</td>
              <td><input type="text" valida_input_vacio class="form-control color_cajatexto text-center" 
                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->nro_trabajador_parcal : 0 }}" 
                         id="nro_trabajador_parcal" style="width:100px;" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
            </tr>
            
          </table>
        </div>
        <div class="col-sm-12 col-md-6">
          <table>
            <tr>
              <td>f) Estabilidad de los otros ingresos (marcar √)
                <br>
                <table class="m-2">
                  <tr>
                    <td>Asalariado fijo</td>
                    <td class="pt-2">
                      <div class="form-check">
                        <input class="form-check-input" {{ $view_detalle=='false' ? 'disabled' : ''  }} type="checkbox" {{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->saladario_fijo == "SI" ? "checked" : "" ) : "" }} id="saladario_fijo" >
                      </div>
                    </td>
                    <td>Otros negocios</td>
                    <td class="pt-2">
                      <div class="form-check">
                        <input class="form-check-input" {{ $view_detalle=='false' ? 'disabled' : ''  }} type="checkbox" {{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->otros_negocios == "SI" ? "checked" : "" ) : "" }} id="otros_negocios">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>Alquiler de locales</td>
                    <td class="pt-2">
                      <div class="form-check">
                        <input class="form-check-input" {{ $view_detalle=='false' ? 'disabled' : ''  }} type="checkbox" {{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->alquiler_local == "SI" ? "checked" : "" ) : "" }} id="alquiler_local">
                      </div>
                    </td>
                    <td>No tiene</td>
                    <td class="pt-2">
                      <div class="form-check">
                        <input class="form-check-input" {{ $view_detalle=='false' ? 'disabled' : ''  }} type="checkbox" {{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->no_tiene == "SI" ? "checked" : "" ) : "" }} id="no_tiene">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>Pensionista</td>
                    <td class="pt-2">
                      <div class="form-check">
                        <input class="form-check-input" {{ $view_detalle=='false' ? 'disabled' : ''  }} type="checkbox" {{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->pensionista == "SI" ? "checked" : "" ) : "" }} id="pensionista">
                      </div>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td>g) Gestión en general (marcar √) <br>
                <table class="m-2">
                  <tr>
                    <td>Lleva registros de ventas, cuentas por cobrar y pagar</td>
                    <td class="pt-2">
                       <div class="form-check">
                        <input class="form-check-input" type="checkbox" {{ $view_detalle=='false' ? 'disabled' : ''  }} {{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->registro_ventas_cuentas == "SI" ? "checked" : "" ) : "" }} id="registro_ventas_cuentas">
                      </div>
                    </td>
                    <td>Pagos de impuestos al día</td>
                    <td class="pt-2">
                       <div class="form-check">
                        <input class="form-check-input" type="checkbox" {{ $view_detalle=='false' ? 'disabled' : ''  }} {{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->pago_impuestos_dia == "SI" ? "checked" : "" ) : "" }} id="pago_impuestos_dia">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>Pago de recibo de servicios báicos al día</td>
                    <td class="pt-2">
                       <div class="form-check">
                        <input class="form-check-input" type="checkbox" {{ $view_detalle=='false' ? 'disabled' : ''  }} {{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->pago_servicios_dia == "SI" ? "checked" : "" ) : "" }} id="pago_servicios_dia">
                      </div>
                    </td>
                    <td>Política de orden en su establecimiento </td>
                    <td class="pt-2">
                       <div class="form-check">
                        <input class="form-check-input" type="checkbox" {{ $view_detalle=='false' ? 'disabled' : ''  }} {{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->politica_orden == "SI" ? "checked" : "" ) : "" }} id="politica_orden">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>Local cumple con normas municipales y legales</td>
                    <td class="pt-2">
                       <div class="form-check">
                        <input class="form-check-input" type="checkbox" {{ $view_detalle=='false' ? 'disabled' : ''  }} {{ $credito_evaluacion_cualitativa ? ( $credito_evaluacion_cualitativa->normas_municipales == "SI" ? "checked" : "" ) : "" }} id="normas_municipales">
                      </div>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;
    color: #000;">2.3 REFERENCIAS</span>
      </div>
      <div class="row">
        
        <div class="col-sm-12">
              @php
                $referencia_cliente = $credito_evaluacion_cualitativa ? ( is_null($credito_evaluacion_cualitativa->referencia) ? [] : json_decode($credito_evaluacion_cualitativa->referencia) ) : [];
              @endphp
              
              <div class="mb-1">
                  <table class="table table-bordered" id="tabla-referencia">
                      <thead>
                          <th style="background-color: #c8c8c8 !important;color: #000 !important;">Fuente</th>
                          <th style="background-color: #c8c8c8 !important;color: #000 !important;">Apellidos y Nombres</th>
                          <th style="background-color: #c8c8c8 !important;color: #000 !important;">Vinculo: Familiar/Personas/Otros</th>
                          <th style="background-color: #c8c8c8 !important;color: #000 !important;">Telf./Celular</th>
                          @if($view_detalle!='false')
                          <th width="10px" style="background-color: #c8c8c8 !important;color: #000 !important;">
                            <a href="javascript:;" class="btn btn-success" onclick="agregar_referencia()">
                              <i class="fa-solid fa-plus"></i>
                            </a>
                          </th>
                          @endif
                      </thead>
                      <tbody num="0">
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
      
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;
    color: #000;">2.4 GASTOS FAMILIARES BÁSICOS (Mensual)</span>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-4">
          <table class="table table-bordered" id="table-gastos-familiares">
            <thead>
              <tr>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;">Concepto</th>
                <th style="background-color: #c8c8c8 !important;color: #000 !important;"  width="110px">Monto</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="background-color: #efefef !important;">Alimentación</td>
                <td style="background-color: #efefef !important;">
                  <input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" 
                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_alimentacion : 0 }}" suma id="gasto_alimentacion" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Educación</td>
                <td style="background-color: #efefef !important;">
                  <input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" 
                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_educacion : 0 }}" suma id="gasto_educacion" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Vestimenta</td>
                <td style="background-color: #efefef !important;">
                  <input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" 
                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_vestimenta : 0 }}" suma id="gasto_vestimenta" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Transporte</td>
                <td style="background-color: #efefef !important;">
                  <input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" 
                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_transporte : 0 }}" suma id="gasto_transporte" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Salud</td>
                <td style="background-color: #efefef !important;">
                  <input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" 
                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_salud : 0 }}" suma id="gasto_salud" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Alquiler de vivienda</td>
                <td style="background-color: #efefef !important;">
                  <input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" 
                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_vivienda : 0 }}" suma id="gasto_vivienda" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
              </tr>
              <tr>
                <th style="background-color: #c8c8c8 !important;">Servicios</th>
                <td style="background-color: #c8c8c8 !important;"><input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->total_servicios : 0 }}" suma id="total_servicios" disabled></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Agua</td>
                <td style="background-color: #efefef !important;">
                  <input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" onkeyup="calcular_servicios()" onkeydown="calcular_servicios()" 
                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_agua : 0 }}" id="gasto_agua" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Luz</td>
                <td style="background-color: #efefef !important;">
                  <input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" onkeyup="calcular_servicios()" onkeydown="calcular_servicios()" 
                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_luz : 0 }}" id="gasto_luz" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Teléfono fijo e internet</td>
                <td style="background-color: #efefef !important;">
                  <input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" onkeyup="calcular_servicios()" onkeydown="calcular_servicios()" 
                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_telefono_internet : 0 }}" id="gasto_telefono_internet" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">T. Celular</td>
                <td style="background-color: #efefef !important;">
                  <input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" onkeyup="calcular_servicios()" onkeydown="calcular_servicios()" 
                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_celular : 0 }}" id="gasto_celular" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Cable</td>
                <td style="background-color: #efefef !important;">
                  <input type="text" valida_input_vacio class="form-control color_cajatexto campo_moneda" onkeyup="calcular_servicios()" onkeydown="calcular_servicios()" 
                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_cable : 0 }}" id="gasto_cable" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
              </tr>
              <tr>
                <td style="background-color: #efefef !important;">Otros gastos personales ({{ configuracion($tienda->id,'provision_gastos_familiares')['valor'] }}%)</td>
                <td style="background-color: #efefef !important;">
                  <input type="text" class="form-control campo_moneda" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_otros : 0 }}" 
                                                                         suma id="gasto_otros" disabled></td>
              </tr>
              <tr>
                <th style="background-color: #c8c8c8 !important;">Total Gasto Familiar (S/.)</th>
                <td style="background-color: #c8c8c8 !important;"><input type="text" disabled class="form-control campo_moneda" value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->gasto_total : 0 }}" id="gasto_total"></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-sm-12 col-md-4">
          <table>
            <tr>
              <td>Número total de hijos</td>
              <td><input type="text" valida_input_vacio class="form-control color_cajatexto" 
                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->total_hijos : 0 }}" id="total_hijos" style="width:100px;" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
            </tr>
            <tr>
              <td>Número de hijos dependientes </td>
              <td><input type="text" valida_input_vacio class="form-control color_cajatexto" 
                         value="{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->total_hijos_dependientes : 0 }}" id="total_hijos_dependientes" style="width:100px;" {{ $view_detalle=='false' ? 'disabled' : ''  }}></td>
            </tr>
          </table>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;
    color: #000;">2.5 DETALLE DEL DESTINO DEL PRÉSTAMO</span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <textarea id="detalle_destino_prestamo" class="form-control color_cajatexto" cols="30" rows="3" {{ $view_detalle=='false' ? 'disabled' : ''  }}>{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->detalle_destino_prestamo : '' }}</textarea>
        </div>
      </div>
      <div class="mb-1 mt-2">
        <span class="badge d-block" style="background-color: #aaa;
    color: #000;">2.6 COMENTARIOS Y ESPECIFICACIONES DE FORTALEZAS DEL NEGOCIO</span>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <textarea id="fortalezas_negocio"  class="form-control color_cajatexto" cols="30" rows="3" {{ $view_detalle=='false' ? 'disabled' : ''  }}>{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->fortalezas_negocio : '' }}</textarea>
        </div>
      </div>

      <div class="row mt-1">
        @if($view_detalle!='false')
        <div class="col" style="flex: 0 0 0%;">
          <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS <b>({{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->cantidad_update : 0 }})</b></button>
        </div>
        @endif
        <div class="col" style="flex: 0 0 0%;">
          <button type="button" 
                  class="btn btn-dark" 
                  onclick="modal({ route:'{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=solicitud_cualitativa')}}', size: 'modal-fullscreen' })"
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
  .color_cajatexto + .select2-container--bootstrap-5 .select2-selection{
      background-color: #dfdf79 !important;
      border-color: #dfdf79 !important;
  }
  .form-check-input:checked {
      background-color: #585858 !important;
      border-color: #585858 !important;
  }
</style>
<script>
valida_input_vacio();
  $('input').on('blur', function() {
      $('#boton_imprimir').attr('disabled',true);
  });
sistema_select2({ input:'#idtipo_giro_economico' });
@foreach($referencia_cliente as $value)
  agregar_referencia('{{ $value->fuente }}', '{{ $value->nombre }}', '{{ $value->vinculo }}', '{{ $value->celular }}');
@endforeach
function agregar_referencia(fuente='', nombre='', vinculo='', celular=''){

    var num   = $("#tabla-referencia > tbody").attr('num');
    var cant  = $("#tabla-referencia > tbody > tr").length;

    var tdeliminar = '';
    @if($view_detalle!='false')
    tdeliminar = '<td><a href="javascript:;" onclick="eliminar_cliente_financiera('+num+',`referencia`)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></td>';
    @endif

    let option = ``;
    @foreach($f_tiporeferencia as $value)
      var selectedOption = fuente == "{{ $value->id }}" ? 'selected' : '';
      option += `<option value="{{ $value->id }}" ${selectedOption}>{{$value->nombre}}</option>`;
    @endforeach
    var tabla= '<tr id="'+num+'">'+
                '<td><select class="form-control color_cajatexto" {{ $view_detalle=='false' ? 'disabled' : ''  }} id="fuente'+num+'"><option></option>'+option+'</select></td>'+
                '<td><input type="text" class="form-control color_cajatexto" {{ $view_detalle=='false' ? 'disabled' : ''  }} id="nombre'+num+'" value="'+nombre+'"></td>'+
                '<td><input type="text" class="form-control color_cajatexto" {{ $view_detalle=='false' ? 'disabled' : ''  }} id="vinculo'+num+'" value="'+vinculo+'"></td>'+
                '<td><input type="number" class="form-control color_cajatexto" {{ $view_detalle=='false' ? 'disabled' : ''  }} id="celular'+num+'" value="'+celular+'"></td>'+
                tdeliminar+
               '</tr>';

    $("#tabla-referencia > tbody").append(tabla);
    $("#tabla-referencia > tbody").attr('num',parseInt(num)+1);  

}
function eliminar_cliente_financiera(num,tabla){
    $("#tabla-"+tabla+" > tbody > tr#"+num).remove();
}
function seleccionar_referencia(){
    var data = [];
    $("#tabla-referencia > tbody > tr").each(function() {
        var num = $(this).attr('id');    
        data.push({ 
            fuente: $('#fuente'+num).val(),
            nombre: $('#nombre'+num).val(),
            vinculo: $('#vinculo'+num).val(),
            celular: $('#celular'+num).val(),
        });
    });
    return JSON.stringify(data);
}
$("#idtipo_giro_economico").on("change", function(e) {
  <?php echo $view_detalle=='false' ? '' : "$('#idgiro_economico_evaluacion').removeAttr('disabled',false)" ?>
  
  
  show_giro_economico(e.currentTarget.value);
}).val('{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->idtipo_giro_economico : 0 }}').trigger('change');
function show_giro_economico(id){
  $.ajax({
      url:"{{url('backoffice/0/credito/showgiroeconomico')}}",
      type:'GET',
      data: {
          tipogiro : id
      },
      success: function (res){
        
        let option_select = `<option></option>`;
        $.each(res, function( key, value ) {
          option_select += `<option value="${value.id}" >${value.nombre}</option>`;
        });
        $('#idgiro_economico_evaluacion').html(option_select);
        sistema_select2({ input:'#idgiro_economico_evaluacion', val:'{{ $credito_evaluacion_cualitativa ? $credito_evaluacion_cualitativa->idgiro_economico_evaluacion : 0 }}'});
        
      }
    })
}
  
function total_deudas() {
  var cantidad_cliente_natural = parseFloat($('#cantidad_cliente_natural').val());
  var cantidad_cliente_juridico = parseFloat($('#cantidad_cliente_juridico').val());
  var cantidad_pareja_natural = parseFloat($('#cantidad_pareja_natural').val());
  var cantidad_pareja_juridico = parseFloat($('#cantidad_pareja_juridico').val());
  $('#total_deuda').val(cantidad_cliente_natural+cantidad_cliente_juridico+cantidad_pareja_natural+cantidad_pareja_juridico);
}
function calcular_servicios(){
  let gasto_agua = parseFloat($('#gasto_agua').val());
  let gasto_luz = parseFloat($('#gasto_luz').val());
  let gasto_telefono_internet = parseFloat($('#gasto_telefono_internet').val());
  let gasto_celular = parseFloat($('#gasto_celular').val());
  let gasto_cable = parseFloat($('#gasto_cable').val());
  let total_servicios = gasto_agua+gasto_luz+gasto_telefono_internet+gasto_celular+gasto_cable;
  $('#total_servicios').val(total_servicios.toFixed(2));
}  
function actualizarTotalCheckboxes($tabla, totalId) {
  var suma = 0;
  $tabla.find('input[type="checkbox"]').each(function() {
    if ($(this).prop('checked')) {
      suma++;
    }
  });
  $('#' + totalId).val(suma);
}

actualizarTotalCheckboxes($('#table-tendencia-con'), 'total_tendencia_con');
actualizarTotalCheckboxes($('#table-tendencia-des'), 'total_tendencia_des');
actualizarTotalCheckboxes($('#table-tendencia-est'), 'total_tendencia_est');

$('input[type="checkbox"]').on('change', function() {
  var $tabla = $(this).closest('table');
  var totalId = $tabla.find('input[type="text"]').attr('id');
  actualizarTotalCheckboxes($tabla, totalId);
});
function actualizarTotalGastos() {
  // servicios
  let gasto_agua = parseFloat($('#gasto_agua').val());
  let gasto_luz = parseFloat($('#gasto_luz').val());
  let gasto_telefono_internet = parseFloat($('#gasto_telefono_internet').val());
  let gasto_celular = parseFloat($('#gasto_celular').val());
  let gasto_cable = parseFloat($('#gasto_cable').val());
  let total_servicios = gasto_agua+gasto_luz+gasto_telefono_internet+gasto_celular+gasto_cable;
  
  // gastos familiares
  let gasto_alimentacion = parseFloat($('#gasto_alimentacion').val());
  let gasto_educacion = parseFloat($('#gasto_educacion').val());
  let gasto_vestimenta = parseFloat($('#gasto_vestimenta').val());
  let gasto_transporte = parseFloat($('#gasto_transporte').val());
  let gasto_salud = parseFloat($('#gasto_salud').val());
  let gasto_vivienda = parseFloat($('#gasto_vivienda').val());
  let gastosfamiliares = gasto_alimentacion+gasto_educacion+gasto_vestimenta+gasto_transporte+gasto_salud+gasto_vivienda;
  let porcenaje_gastos = "{{ configuracion($tienda->id,'provision_gastos_familiares')['valor'] }}";
  porcenaje_gastos = (parseFloat(porcenaje_gastos)/100);
  let gastos_otros = (gastosfamiliares+total_servicios) * porcenaje_gastos; // 12%
  let gastos_total = (gastosfamiliares+total_servicios)+gastos_otros;
  // Actualiza el campo "gasto_total" con la suma
  $('#gasto_total').val(gastos_total.toFixed(2));
  $('#gasto_otros').val(gastos_otros.toFixed(2));
}

// Llama a la función cuando se carga la página y cuando cambian los valores
actualizarTotalGastos();
$('#table-gastos-familiares input[id^="gasto_"]').not('#gasto_total').on('input', actualizarTotalGastos);
</script>    