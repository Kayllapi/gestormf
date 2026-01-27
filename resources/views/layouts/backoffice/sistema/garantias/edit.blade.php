<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$idtienda.'/garantias/'.$garantias->id) }}',
          method: 'PUT',
          data:{
              view: 'editar',
              idtienda: {{$tienda->id}},
          }
      },
      function(resultado){
          $('#cont-ultimamodificacion').addClass('d-none');
          $('#alert-ultimamodificacion').html('');
                                                    
          $('#tabla-garantias').DataTable().ajax.reload();
          //load_edit_garantia();
          lista_garantias_cliente({{ $garantias->idcliente }});
      },this)" id="form-editar-garantia">
  
    <input type="hidden" id="idresponsable_modificado">
    <div class="modal-header">
        <h5 class="modal-title">Garantía Prendaria</h5>
    </div>
    <div class="modal-body">
        <div class="row">
          <div class="col-sm-6">
            <input type="text" style="font-size: 15px;" class="form-control" value="CÓDIGO: GP{{ str_pad($garantias->id, 8, '0', STR_PAD_LEFT)  }}" disabled>
          </div>
          <div class="col-sm-2">
            <div style="margin-top: 6px;
    background-color: #ffd100;
    float: left;
    padding-left: 3px;
    padding-right: 3px;
    border-radius: 3px;">{{ calcularDiasPasados($garantias->fecharegistro) }} DIA(S)</div>
          </div>
          <div class="col-sm-4">
            <label>Fecha de Registro</label>
            <input type="date" class="form-control" id="fecharegistro" value="{{ $garantias->fecharegistro }}" disabled>
          </div> 
          <div class="col-sm-12 col-md-12 d-none">
            <label>Cliente</label>
            <select class="form-control" id="idcliente" disabled>
              <option></option>
            </select>
          </div>
          <div class="col-sm-12 col-md-3">
            <label>Tipo de Garantía *</label>
            <select class="form-control" id="idtipogarantia" disabled>
              <option></option>
               @foreach($tipo_garantia as $value)
                  <option value="{{ $value->id }}" antiguedad="{{ $value->antiguedad}}" valor="{{ $value->valor}}" cobertura="{{ $value->cobertura}}">{{ $value->nombre }}</option>
                @endforeach
            </select>
            <div class="modal fade" id="modalValorizacion" tabindex="-1" aria-labelledby="modalValorizacionLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalValorizacionLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row">
                      <div class="col-sm-12 col-md-6 d-none option-tipo-general">
                        <label>Método Valorización</label>
                        <select class="form-control" id="idmetodo_valorizacion" >
                           <option ></option>
                          <?php $i=1 ?>
                          @foreach($metodo_valorizacion as $value)
                            <option value="{{ $value->id }}" num="{{ $i }}">{{ $i }}.- {{ $value->nombre }}</option>
                            <?php $i++ ?>
                          @endforeach
                        </select>
                      </div>
                      
                      <div class="col-sm-12 col-md-6 d-none option-tipo-general">
                        <label id="title-antiguedad">Antiguedad</label>
                        <select class="form-control" id="idtipo_garantia_detalle" disabled>
                           <option ></option>
                        </select>
                      </div>
                      <div class="col-sm-12 col-md-4 d-none option-tipo-general">
                        <label>Valor Mercado</label>
                        <input type="text" class="form-control bg-warning" id="valor_mercado" onkeyup="calc_montos()" onkeydown="calc_montos()" onchange="calc_montos()">
                        <input type="hidden" class="form-control bg-warning" id="valor_mercado_inicial" value="{{$garantias->valor_mercado}}">
                      </div>
                      
                      <div class="col-sm-12 col-md-6 d-none option-tipo-joya">
                        <label>Tipo de Joya</label>
                        <select class="form-control" id="idtipo_joyas">
                           <option ></option>
                           @foreach($tipo_joyas as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                           @endforeach
                        </select>
                      </div>
                      <div class="col-sm-12 col-md-6 d-none option-tipo-joya">
                        <label>Tipo de Oro</label>
                        <select class="form-control" id="idtarifario_joya" >
                           <option ></option>
                        </select>
                      </div>
                      <div class="col-sm-4 d-none option-tipo-joya">
                        <label>Peso en Gramos</label>
                        <input type="number" step="any" id="peso_gramos" class="form-control text-center bg-warning" value="{{ $garantias->peso_gramos }}" onkeyup="calc_tarifa_joya()" onkeydown="calc_tarifa_joya()" onchange="calc_tarifa_joya()">
                      </div>
                      <div class="mb-1 mt-2 d-none option-tipo-joya">
                        <span class="badge d-block">DESCUENTOS</span>
                      </div>
                      <div class="col-sm-12 col-md-6 d-none option-tipo-joya">
                        <label>Tipo de Descuento</label>
                        <select class="form-control" id="iddescuento_joya" >
                           <option></option>
                            @foreach($descuento_joya as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                           @endforeach
                        </select>
                      </div>
                      <div class="col-sm-12 col-md-6 d-none option-tipo-joya">
                        <label>Descuento</label>
                        <select class="form-control" id="idvalorizacion_descuento" disabled>
                           <option ></option>
                        </select>
                      </div>
                      <div class="col-sm-4 d-none option-tipo-joya">
                        <label>Peso Neto (g)</label>
                        <input type="number" step="any" class="form-control text-center bg-warning" id="peso_neto" value="{{ $garantias->peso_neto }}" disabled>
                      </div>
                      <div class="mb-1 mt-2">
                        <span class="badge d-block">TASACIÓN</span>
                      </div>
                      <div class="col-sm-4">
                        <label>Valor Cobertura S/</label>
                        <input type="text" class="form-control" id="val-view-cobertura" disabled>
                      </div>
                      <div class="col-sm-4">
                        <label>Valor Comercial S/</label>
                        <input type="text" class="form-control" id="val-view-valorcomercial" disabled>
                      </div>
                      <div class="col-sm-4">
                        <label>Valor de Mercado S/</label>
                        <input type="number" class="form-control" id="val-view-valormercado" onclick="calc_valormercado()" onkeyup="calc_valormercado()">
                        <span id="cont_mensaje_valormercado" style="color: #c52525;font-size: 12px;"></span>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="guardar()">Guardar</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-1">
            <br>
            <button type="button" class="btn btn-primary" disabled id="button-modal-tipo-garantia" data-bs-toggle="modal" data-bs-target="#modalValorizacion">
              <i class="fa-solid fa-dice-three"></i>
            </button>
          </div>
          
          <div class="col-sm-12 col-md-4">
            <label>Tipo de Joya</label>
            <?php $tipo_joyas = DB::table('tipo_joyas')->whereId($garantias->idtipo_joyas)->first(); ?>
            <input type="text" class="form-control" id="idtipo_joyas_nombre" value="{{$tipo_joyas?$tipo_joyas->nombre:''}}" disabled>
          </div>
          <div class="col-sm-12 col-md-4">
            <label>Tipo de Oro</label>
            <?php $tarifario_joyas = DB::table('tarifario_joyas')->whereId($garantias->idtarifario_joya)->first(); ?>
            <input type="text" class="form-control" id="idtarifario_joya_nombre" value="{{$tarifario_joyas?$tarifario_joyas->tipo:''}}" disabled>
          </div>   
          
          <div class="mb-1 mt-2">
            <span class="badge d-block">VALORIZACIÓN</span>
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Cobertura</label>
            <input type="text" class="form-control bg-light" id="cobertura" value="{{ $garantias->cobertura }}" disabled>
            <input type="hidden" class="form-control" id="porcentajecobertura" value="{{ $garantias->porcentajecobertura }}" disabled>
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Valor Comercial</label>
            <input type="text" class="form-control bg-light" id="valorcomercial" value="{{ $garantias->valorcomercial }}" disabled>
            <input type="hidden" class="form-control" id="porcentajevalorcomercial" value="{{ $garantias->porcentajevalorcomercial }}" disabled>
          </div>
          
          <div class="mb-1 mt-2">
            <span class="badge d-block">DETALLE GARANTÍA</span>
          </div>
          <div class="col-sm-12 col-md-12">
            <label>Descripción *</label>
            <input type="text" class="form-control" id="descripcion" value="{{ $garantias->descripcion }}" disabled>
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Serie/Motor/N° Partida *</label>
            <input type="text" class="form-control" id="serie_motor_partida" value="{{ $garantias->serie_motor_partida }}" disabled>
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Chasis</label>
            <input type="text" class="form-control" id="chasis" value="{{ $garantias->chasis }}" disabled>
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Modelo/Tipo *</label>
            <input type="text" class="form-control" id="modelo_tipo" value="{{ $garantias->modelo_tipo }}" disabled>
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Otros/Password</label>
            <input type="text" class="form-control" id="otros" value="{{ $garantias->otros }}" disabled>
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Estado *</label>
            <select class="form-control" id="idestado_garantia" disabled>
               <option></option>
              @foreach($estado_garantia as $value)
               <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Color *</label>
            <input type="text" class="form-control" id="color" value="{{ $garantias->color }}" disabled>
          </div>
          
          <div class="col-sm-12 col-md-4">
            <label>Año Fabricación</label>
            <input type="text" class="form-control" id="fabricacion" value="{{ $garantias->fabricacion }}" disabled>
          </div>
          <div class="col-sm-12 col-md-4">
            <label>Año Compra</label>
            <input type="text" class="form-control" id="compra" value="{{ $garantias->compra }}" disabled>
          </div>
          <div class="col-sm-12 col-md-4">
            <label>Placa (Vehículos)</label>
            <input type="text" class="form-control" id="placa" value="{{ $garantias->placa }}" disabled>
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Accesorio/Doc.Original</label>
            <input type="text" class="form-control" id="accesorio_doc" value="{{ $garantias->accesorio_doc }}" disabled>
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Detalle Garantia</label>
            <input type="text" class="form-control" id="detalle_garantia" value="{{ $garantias->detalle_garantia }}" disabled>
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Estado de Ref. *</label>
            <select class="form-control" id="idestado_garantia_ref" disabled>
               <option></option>
               @foreach($estado_garantia_ref as $value)
               <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
            </select>
          </div>
        </div>
          <div class="mb-1 mt-2">
            <span class="badge d-block">CRÉDITOS PROPIOS</span>
          </div>
              <table class="table">
                <thead>
                  <tr>
                    <th width="10px">Nro</th>
                    <th>RUC/DNI/CE - Apellidos y Nombres</th>
                    <th width="80px">Cuenta</th>
                    <th width="80px">Desembolso</th>
                    <th width="80px">Saldo</th>
                  </tr>
                </thead>
                <tbody>
            <?php $i=1 ?>
            @foreach($propios as $value)
                  <?php
                        $credito_descuentocuotas = DB::table('credito_descuentocuota')
                              ->where('credito_descuentocuota.idcredito',$value->idcredito)
                              ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                              ->first();
                        $total_descuento_capital = 0; 
                        $total_descuento_interes = 0; 
                        $total_descuento_comision = 0; 
                        $total_descuento_cargo = 0;  
                        $total_descuento_penalidad = 0; 
                        $total_descuento_tenencia = 0; 
                        $total_descuento_compensatorio = 0; 
                        $total_descuento_total = 0; 
                        if($credito_descuentocuotas){
                            if(1000>=$credito_descuentocuotas->numerocuota_fin){
                                $total_descuento_capital = $credito_descuentocuotas->capital;
                                $total_descuento_interes = $credito_descuentocuotas->interes;
                                $total_descuento_comision = $credito_descuentocuotas->comision;
                                $total_descuento_cargo = $credito_descuentocuotas->cargo;
                                $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                                $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                                $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
                                $total_descuento_total = $credito_descuentocuotas->total;
                            }
                        }
                
                        $cronograma = select_cronograma(
                            $tienda->id,
                            $value->idcredito,
                            $value->idforma_credito,
                            $value->modalidadproductocredito,
                            1000,
                            $total_descuento_capital,
                            $total_descuento_interes,
                            $total_descuento_comision,
                            $total_descuento_cargo,
                            $total_descuento_penalidad,
                            $total_descuento_tenencia,
                            $total_descuento_compensatorio
                        );
                        ?>
                  <tr>
                    <td style="text-align: center;">{{ $i }}</td>
                    <td>{{ $value->identificacion }} - {{ $value->nombrecompleto }}</td>
                    <td>
                      @if($value->cuenta!=0)
                      C{{ str_pad($value->cuenta, 8, '0', STR_PAD_LEFT)  }}
                      @else
                      EN PROCESO
                      @endif
                    </td>
                    <td style="text-align: right;">S/. {{ $value->monto_solicitado }}</td>
                    <td style="text-align: right;">S/. {{ $cronograma['saldo_capital'] }}</td>
                  </tr>
            <?php $i++ ?>
            @endforeach
                </tbody>
              </table>
    </div>
    <div class="modal-footer d-none" id="cont-btnguardar">
        <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form> 
<script>
  var html = '<ul class="text-danger" style="margin-top: 10px;">';
  @foreach($credito_polizaseguro as $value)
      @if($value->vigencia_hasta<now()->format('Y-m-d'))
      html += '<li>Póliza de Garantía "{{$value->asegurado}}", venció el {{Carbon\Carbon::parse($value->vigencia_hasta)->format('d/m/Y')}}</li>';
      @endif
  @endforeach
  $('#alert-garantia-poliza').html(html+'</ul>');
  
  $('#btn-delete-garantia').css("display","inline-block");
  $('#cont-btnguardar').addClass("d-none");
  $('#btn-autorizar-depositario').addClass("d-none");
  @if($garantia_credito)
    $('#form-editar-garantia').find('select').attr('disabled',true);
    $('#form-editar-garantia').find('input').attr('disabled',true);
    $('#btn-autorizar-garantia').addClass("d-none");
    $('#btn-delete-garantia').css("display","none");
  @else
    $('#btn-autorizar-garantia').removeClass("d-none")
  @endif
  function autorizar_edicion(val){
    $('#cont-btnguardar').removeClass("d-none");
    if(val==1){
    $('#idtipogarantia').removeAttr('disabled');
    var textTipoGarantia_id = $("#idtipogarantia :selected").val();
    if(textTipoGarantia_id!=0){
        $('#button-modal-tipo-garantia').removeAttr('disabled');
    }
    $('#otros').removeAttr('disabled');
    $('#accesorio_doc').removeAttr('disabled');
    $('#detalle_garantia').removeAttr('disabled');
    $('#descripcion').removeAttr('disabled');
    $('#serie_motor_partida').removeAttr('disabled');
    $('#chasis').removeAttr('disabled');
    $('#modelo_tipo').removeAttr('disabled');
    $('#idestado_garantia').removeAttr('disabled');
    $('#color').removeAttr('disabled');
    $('#fabricacion').removeAttr('disabled');
    $('#compra').removeAttr('disabled');
    $('#placa').removeAttr('disabled');
    $('#idestado_garantia_ref').removeAttr('disabled');
    }
    if(val==2){
        $('#polizaseguro_td_agregar').css('display','table-cell');

        $('#constituciongarantia_id').removeAttr('disabled');
        $('#custodiagarantia_id').removeAttr('disabled');

        $("#table-polizaseguro > tbody > tr").each(function() {
            var num = $(this).attr('id');  
            $('#polizaseguro_numero_poliza'+num).removeAttr('disabled');
            $('#polizaseguro_aseguradora'+num).removeAttr('disabled');
            $('#polizaseguro_prima_recio'+num).removeAttr('disabled');
            $('#polizaseguro_beneficiario'+num).removeAttr('disabled');
            $('#polizaseguro_asegurado'+num).removeAttr('disabled');
            $('#polizaseguro_tomador'+num).removeAttr('disabled');
            $('#polizaseguro_vigencia_desde'+num).removeAttr('disabled');
            $('#polizaseguro_vigencia_hasta'+num).removeAttr('disabled');

            $('#polizaseguro_td_eliminar'+num).css('display','block');
        });
    }
  }

  $('#btn-delete-garantia').removeClass("d-none");

  $('#alert-garantia-1').removeClass("d-none");
  $('#alert-garantia-2').removeClass("d-none");
  $('#alert-garantia-3').removeClass("d-none");
  
  $('#cont-ultimamodificacion').removeClass("d-none");
  $('#alert-ultimamodificacion').html('{{ $garantias->responsablenombrecliente }}');
  

  function eliminar_garantia(){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/garantias/'.$garantias->id.'/edit?view=eliminar')}}",  size: 'modal-sm' });  
  }
  function modificar_garantia(val){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/garantias/'.$garantias->id.'/edit?view=modificar')}}&val="+val,  size: 'modal-sm' });  
  }
  function load_edit_garantia(){
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/garantias/{{$garantias->id}}/edit?view=editar", result:'#form-garantias-result'});  
  }
  @include('app.nuevosistema.select2',['json'=>'tienda:usuario','input'=>'#idcliente','val'=>$garantias->idcliente])

  @include('app.nuevosistema.select2',['input'=>'#idtipogarantia'])
  @include('app.nuevosistema.select2',['input'=>'#idestado_garantia','val'=> $garantias->idestado_garantia ])
  @include('app.nuevosistema.select2',['input'=>'#idestado_garantia_ref','val'=> $garantias->idestado_garantia_ref ])
  @include('app.nuevosistema.select2',['input'=>'#idtipo_joyas','val'=> $garantias->idtipo_joyas ])
  @include('app.nuevosistema.select2',['input'=>'#iddescuento_joya','val'=> $garantias->iddescuento_joya ])

  sistema_select2({ input:'#idmetodo_valorizacion' });
  sistema_select2({ input:'#idtipo_garantia_detalle' });
  sistema_select2({ input:'#idtarifario_joya' });
  sistema_select2({ input:'#idvalorizacion_descuento' });

  @if($garantias->idtipogarantia!=0)
      $('#idtipogarantia').val({{$garantias->idtipogarantia}}).trigger('change');
  @endif  
  @if($garantias->idtipo_joyas!=0)
      tipo_joyas();
  @endif  
  @if($garantias->iddescuento_joya!=0)
      descuento_joya();
  @endif  
  
  $("#button-modal-tipo-garantia").on("click", function(e) {
      tipo_garantia();
  });

  $("#idtipogarantia").on("select2:select", function(e) {
      limpiar();
      tipo_garantia();
      $('#button-modal-tipo-garantia').removeAttr('disabled');
  });

  $("#idmetodo_valorizacion").on("select2:select", function(e) {
      metodo_valorizacion();
  })

  $("#idtipo_garantia_detalle").on("select2:select", function(e) {
      calc_montos();
  });

  //joya
  $("#idtipo_joyas").on("select2:select", function(e) {
      tipo_joyas();
  });
  
  $("#idtarifario_joya").on("select2:select", function(e) {
      calc_tarifa_joya();
  });
  
  $("#iddescuento_joya").on("select2:select", function(e) {
      descuento_joya();
  });

  $("#idvalorizacion_descuento").on("select2:select", function(e) {
      calc_tarifa_joya(); 
  });
  
  function tipo_garantia(){
      var textTipoGarantia = $("#idtipogarantia").find('option:selected').text();
      var idtipogarantia = $("#idtipogarantia :selected");
      let titleAntiguedad = idtipogarantia.attr('antiguedad');
      let titleValor = idtipogarantia.attr('valor');
      let titleCobertura = idtipogarantia.attr('cobertura');
      
      $('#title-antiguedad').text(titleAntiguedad);
      $('#title-valor').text(titleValor);
      $('#title-cobertura').text(titleCobertura);
      $('#modalValorizacionLabel').text(textTipoGarantia);
      $("#modalValorizacion").modal('show');
      if(idtipogarantia.val() == 6){
          $('.option-tipo-general').addClass('d-none');
          $('.option-tipo-joya').removeClass('d-none');
          //tarifario_joyas(e.currentTarget.value);  
      }else{
          $('.option-tipo-general').removeClass('d-none');
          $('.option-tipo-joya').addClass('d-none');
          //tipo_garantia(e.currentTarget.value);
      }

      if({{$garantias->idtipogarantia}}==idtipogarantia.val()){
          var valor_mercado_inicial = parseFloat($('#valor_mercado_inicial').val());
          $('#valor_mercado').val(valor_mercado_inicial.toFixed(2));
          @if($garantias->idmetodo_valorizacion!=0)
              $('#idmetodo_valorizacion').val({{$garantias->idmetodo_valorizacion}}).trigger('change');
              metodo_valorizacion();
          @endif
      } 
  }
  function metodo_valorizacion(){
    var num = $("#idmetodo_valorizacion").find('option:selected').attr('num');
    var idmetodo_valorizacion = $("#idmetodo_valorizacion :selected").val();
    let idtipogarantia = $("#idtipogarantia").find('option:selected').val();
    $('#idtipo_garantia_detalle').removeAttr('disabled',false)
    $.ajax({
      url:"{{url('backoffice/0/garantias/showtipogarantia')}}",
      type:'GET',
      data: {
          idtipogarantia      : idtipogarantia,
          idmetodovalorizacion : idmetodo_valorizacion
      },
      success: function (res){
          let option_select = `<option></option>`;
          var i = 1;
          $.each(res, function( key, value ) {
              option_select += `<option value="${value.id}" cobertura="${value.cobertura}" valor_comercial="${value.valor_comercial}" >${num}.${i}.- ${value.antiguedad}</option>`;
              i++;
          });
          $('#idtipo_garantia_detalle').html(option_select);
          sistema_select2({ input:'#idtipo_garantia_detalle', val:"{{ $garantias->idtipo_garantia_detalle }}"});
          calc_montos();
      }
    })
    
  }
  function calc_montos(){

      var idtipo_garantia_detalle = $("#idtipo_garantia_detalle").find('option:selected');
      let cobertura = idtipo_garantia_detalle.attr('cobertura');
      let valor_comercial = idtipo_garantia_detalle.attr('valor_comercial');
      let valor_mercado = $('#valor_mercado').val();    
      let monto_valorcomercial = (parseFloat(valor_mercado) * parseFloat(valor_comercial)) / 100;
      let monto_cobertura = (parseFloat(monto_valorcomercial) * parseFloat(cobertura)) / 100;
      
      $('#porcentajecobertura').val(cobertura);
      $('#porcentajevalorcomercial').val(valor_comercial);
      $('#val-view-cobertura').val(monto_cobertura.toFixed(2));
      $('#val-view-valorcomercial').val(monto_valorcomercial.toFixed(2));
    
  }
  function guardar(){
    
      let valor_mercado = parseFloat($('#valor_mercado').val());   
      let monto_cobertura = parseFloat($('#val-view-cobertura').val());    
      let monto_valorcomercial = parseFloat($('#val-view-valorcomercial').val());
      let valormercado =  parseFloat($("#val-view-valormercado").val());
    
      if(valormercado<monto_valorcomercial){
          return false;
      }
    
      $('#valor_mercado_inicial').val(valor_mercado.toFixed(2));    
      $('#cobertura').val(monto_cobertura.toFixed(2));
      $('#valorcomercial').val(monto_valorcomercial.toFixed(2));
      $('#idtipo_joyas_nombre').val($('#idtipo_joyas :selected').html());
      $('#idtarifario_joya_nombre').val($('#idtarifario_joya :selected').html());
    
      $('#modalValorizacion').modal('hide');
      
  }
  function limpiar(){
      $('#idmetodo_valorizacion').val(null).trigger('change');
      $('#idtipo_garantia_detalle').val(null).trigger('change');
      $('#valor_mercado').val('0.00');
      $('#val-view-cobertura').val('0.00');
      $('#val-view-valorcomercial').val('0.00');
      
      $('#cobertura').val('0.00');
      $('#valorcomercial').val('0.00');
      $('#porcentajevalorcomercial').val('0.00');
  }
  function tipo_joyas(){
      var idtipo_joyas = $("#idtipo_joyas :selected").val();
      $.ajax({
          url:"{{url('backoffice/0/garantias/showtarifario')}}",
          type:'GET',
          data: {
              idtipo_joyas : idtipo_joyas
          },
          success: function (res){
              let option_select = `<option></option>`;
              $.each(res, function( key, value ) {
                option_select += `<option value="${value.id}" cobertura="${value.cobertura}" preciogramo="${value.precio}" valormercado="${value.valormercado}">${value.tipo} </option>`;
              });
              $('#idtarifario_joya').html(option_select);
              sistema_select2({ input:'#idtarifario_joya', val:'{{ $garantias->idtarifario_joya }}'});
              calc_tarifa_joya();
          }
      })
  }
  function descuento_joya(){
    $('#idvalorizacion_descuento').removeAttr('disabled',false);
    var iddescuento_joya = $("#iddescuento_joya").find('option:selected').val();
    $.ajax({
        url:"{{url('backoffice/0/garantias/showdescuentojoya')}}",
        type:'GET',
        data: {
            iddescuento_joya : iddescuento_joya
        },
        success: function (res){
            let option_select = `<option></option>`;
            $.each(res, function( key, value ) {
              option_select += `<option value="${value.id}" descuento="${value.descuento}" >${value.descuento}(${value.val}) - ${value.detalle_descuento}</option>`;
            });
            $('#idvalorizacion_descuento').html(option_select);
            sistema_select2({ input:'#idvalorizacion_descuento', val:'{{ $garantias->idvalorizacion_descuento }}'});
            calc_tarifa_joya();
        }
    })
  }
  function calc_tarifa_joya(){
      let iddescuento_joya = $("#iddescuento_joya").find('option:selected').val();
      let selectDescuento = $("#idvalorizacion_descuento").find('option:selected'); 
      let descuento = selectDescuento.attr('descuento');
      let pesogramo = $('#peso_gramos').val();
    
      let peso_neto = 0;
      if(descuento === undefined){
          peso_neto = parseFloat(pesogramo);
      }else{
          let neto_descuento = 0;
          if(iddescuento_joya == 1 ){
              neto_descuento = (parseFloat(pesogramo) * parseFloat(descuento) ) / 100;
          }
          else{
              neto_descuento = parseFloat(descuento);
          }

          peso_neto = parseFloat(pesogramo) - parseFloat(neto_descuento);
      }

      $('#peso_neto').val(peso_neto.toFixed(2));
    
      let optionTarifaJoya = $("#idtarifario_joya").find('option:selected'); 
      let cobertura = parseFloat(optionTarifaJoya.attr('cobertura'));
      let preciogramo = parseFloat(optionTarifaJoya.attr('preciogramo'));
      let peso = parseFloat($('#peso_neto').val());
      let monto_valorcomercial = peso*preciogramo;
      let monto_cobertura = (monto_valorcomercial*cobertura)/100;  

      $('#porcentajecobertura').val(cobertura);
      $('#porcentajevalorcomercial').val('0.00');
      $('#val-view-cobertura').val(monto_cobertura.toFixed(2));
      $('#val-view-valorcomercial').val(monto_valorcomercial.toFixed(2));
      @if($garantias->valorcomercial>0)
      $('#val-view-valormercado').val({{$garantias->valorcomercial}});
      @else
      $('#val-view-valormercado').val(monto_valorcomercial.toFixed(2));
      @endif
    
    
      
      
      /*if(changeStatusCaldJoya){
        $('#cobertura').val(monto_cobertura.toFixed(2));
        $('#valorcomercial').val(monto_valorcomercial.toFixed(2));

        $('#val-view-cobertura').val(monto_cobertura.toFixed(2));
        $('#val-view-valorcomercial').val(monto_valorcomercial.toFixed(2));
      }else{
        $('#val-view-cobertura').val('{{ $garantias->cobertura }}');
        $('#val-view-valorcomercial').val('{{ $garantias->valorcomercial }}');
      }*/
  }
  function calc_valormercado(){
      let valormercado =  parseFloat($("#val-view-valormercado").val());
      let optionTarifaJoya = $("#idtarifario_joya").find('option:selected'); 
      let valormercado_porcentaje = parseFloat(optionTarifaJoya.attr('valormercado'));
      let cobertura = parseFloat(optionTarifaJoya.attr('cobertura'));
      let preciogramo = parseFloat(optionTarifaJoya.attr('preciogramo'));
      let peso = parseFloat($('#peso_neto').val());
      let monto_valorcomercial = peso*preciogramo;
      //let monto_cobertura = (monto_valorcomercial*cobertura)/100; 
    
      $('#cont_mensaje_valormercado').html('');
      if(valormercado<monto_valorcomercial){
          $('#cont_mensaje_valormercado').html('El Valor de Mercado debe ser mayor o igual a Valor Comercial');
      }
    
      var new_valorcomercial = (((valormercado-monto_valorcomercial)*(valormercado_porcentaje/100))+monto_valorcomercial);
      var new_cobertura = new_valorcomercial*(cobertura/100);

      $('#porcentajecobertura').val(cobertura);
      $('#porcentajevalorcomercial').val('0.00');
      $('#val-view-cobertura').val(new_cobertura.toFixed(2));
      $('#val-view-valorcomercial').val(new_valorcomercial.toFixed(2));
  }
  /*function calc_desc_peso(){
    let iddescuento_joya = $("#iddescuento_joya").find('option:selected').val();
    let selectDescuento = $("#idvalorizacion_descuento").find('option:selected'); 
    let pesogramo = $('#peso_gramos').val();
    let descuento = selectDescuento.attr('descuento');
    let peso_neto = 0;
    if(descuento === undefined){
        peso_neto = parseFloat(pesogramo);
    }else{
        let neto_descuento = 0;
        if(iddescuento_joya == 1 ){
            neto_descuento = (parseFloat(pesogramo) * parseFloat(descuento) ) / 100;
        }
        else{
            neto_descuento = parseFloat(descuento);
        }
      
        peso_neto = parseFloat(pesogramo) - parseFloat(neto_descuento);
    }
    
    $('#peso_neto').val(peso_neto.toFixed(2));
    
  }*/

</script>     