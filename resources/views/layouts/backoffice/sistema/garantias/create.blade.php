<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/garantias') }}',
        method: 'POST',
        data:{
            view: 'registrar',
        }
    },
    function(resultado){
        $('#tabla-garantias').DataTable().ajax.reload();
        load_create_garantia();
        lista_garantias_cliente({{ $idcliente }});
    },this)"> 
    <div class="modal-header">
        <h5 class="modal-title">Registrar Garantía Prendaria</h5>
    </div>
    <div class="modal-body">
        <div class="row">
          <div class="col-sm-12 col-md-12 d-none">
            <label>Cliente</label>
            <select class="form-control" id="idcliente" disabled>
              <option></option>
            </select>
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Tipo de Garantía *</label>
            <select class="form-control" id="idtipogarantia">
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
                        <input type="text" class="form-control bg-warning" id="valor_mercado" onkeyup="calc_montos()" onkeydown="calc_montos()" onchange="calc_montos()" value="0.00" disabled>
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
                        <input type="number" step="any" id="peso_gramos" class="form-control text-center bg-warning" value="0.00" onkeyup="calc_tarifa_joya()" onkeydown="calc_tarifa_joya()" onchange="calc_tarifa_joya()" disabled >
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
                        <input type="number" step="any" class="form-control text-center bg-warning" id="peso_neto" value="0.00" readonly>
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
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="guardar()">Guardar</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <br>
            <button type="button" class="btn btn-primary" id="button-modal-tipo-garantia" disabled data-bs-toggle="modal" data-bs-target="#modalValorizacion">
              <i class="fa-solid fa-dice-three"></i>
            </button>
          </div>
          
          
          <div class="mb-1 mt-2">
            <span class="badge d-block">VALORIZACIÓN</span>
          </div>
       
          <div class="col-sm-12 col-md-6">
            <label>Cobertura</label>
            <input type="text" class="form-control" id="cobertura" readonly>
            <input type="hidden" class="form-control" id="porcentajecobertura" readonly>
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Valor Comercial</label>
            <input type="text" class="form-control" id="valorcomercial" readonly>
            <input type="hidden" class="form-control" id="porcentajevalorcomercial" readonly>
          </div>
          
          
          <div class="mb-1 mt-2">
          <span class="badge d-block">DETALLE GARANTÍA</span>
          </div>
          <div class="col-sm-12 col-md-12">
            <label>Descripción *</label>
            <input type="text" class="form-control" id="descripcion">
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Serie/Motor/N° Partida *</label>
            <input type="text" class="form-control" id="serie_motor_partida">
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Chasis</label>
            <input type="text" class="form-control" id="chasis">
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Modelo/Tipo *</label>
            <input type="text" class="form-control" id="modelo_tipo">
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Otros/Password</label>
            <input type="text" class="form-control" id="otros">
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Estado *</label>
            <select class="form-control" id="idestado_garantia">
               <option></option>
              @foreach($estado_garantia as $value)
               <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Color *</label>
            <input type="text" class="form-control" id="color">
          </div>
          
          <div class="col-sm-12 col-md-4">
            <label>Año Fabricación</label>
            <input type="text" class="form-control" id="fabricacion">
          </div>
          <div class="col-sm-12 col-md-4">
            <label>Año Compra</label>
            <input type="text" class="form-control" id="compra">
          </div>
          <div class="col-sm-12 col-md-4">
            <label>Placa (Vehículos)</label>
            <input type="text" class="form-control" id="placa">
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Accesorio/Doc.Original</label>
            <input type="text" class="form-control" id="accesorio_doc">
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Detalle Garantia</label>
            <input type="text" class="form-control" id="detalle_garantia">
          </div>
          <div class="col-sm-12 col-md-6">
            <label>Estado de Ref. *</label>
            <select class="form-control" id="idestado_garantia_ref">
               <option></option>
               @foreach($estado_garantia_ref as $value)
               <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
            </select>
          </div>
        </div>
     
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form>  
<script>
    $('#btn-autorizar-garantia').addClass("d-none");
    $('#btn-autorizar-depositario').addClass("d-none");
  
    $('#btn-delete-garantia').addClass("d-none");
    $('#alert-garantia-1').addClass("d-none");
    $('#alert-garantia-2').addClass("d-none");
    $('#alert-garantia-3').addClass("d-none");
  
  @include('app.nuevosistema.select2',['input'=>'#idtipogarantia'])
  @include('app.nuevosistema.select2',['input'=>'#idestado_garantia'])
  @include('app.nuevosistema.select2',['input'=>'#idestado_garantia_ref'])
  sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idcliente', val: '{{ $idcliente }}' });
  @include('app.nuevosistema.select2',['input'=>'#idtipo_joyas' ])
  @include('app.nuevosistema.select2',['input'=>'#iddescuento_joya' ])
  
  sistema_select2({ input:'#idmetodo_valorizacion' });
  sistema_select2({ input:'#idtipo_garantia_detalle' });
  
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
      $('#valor_mercado_inicial').val(valor_mercado.toFixed(2));    
      $('#cobertura').val(monto_cobertura.toFixed(2));
      $('#valorcomercial').val(monto_valorcomercial.toFixed(2));
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

  //-------
  function tarifario_joyas(idtarifario){
    $.ajax({
      url:"{{url('backoffice/0/garantias/showtarifario')}}",
      type:'GET',
      data: {
          idtarifario : idtarifario
      },
      success: function (res){
        let option_select = `<option></option>`;
        $.each(res, function( key, value ) {
          option_select += `<option value="${value.id}" cobertura="${value.cobertura}" preciogramo="${value.precio}" >${value.tipo} </option>`;
        });
        $('#idtarifario_joya').html(option_select);
        sistema_select2({ input:'#idtarifario_joya'});
      }
    })
  }
  
  function descuento_joya(){
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
          sistema_select2({ input:'#idvalorizacion_descuento'});
          
        }
      })
    }
  
  $("#idtipo_garantia_detalle").on("change", function(e) {
    $('#valor_mercado').removeAttr('disabled',false)
    calc_montos();
  });
//   $("#idtipo_joyas").on("change", function(e) {
//     $('#idvalorizacion_descuento').attr('disabled',true)
//     $("#iddescuento_joya").val(0).trigger('change');
//     $('#idvalorizacion_descuento').html('');
    
//   });
  $("#iddescuento_joya").on("change", function(e) {
    $('#idvalorizacion_descuento').removeAttr('disabled',false)
    descuento_joya();
  });
  
  $("#idvalorizacion_descuento").on("change", function(e) {
    
    calc_tarifa_joya();
  });
  
  $("#idtarifario_joya").on("change", function(e) {
    $('#peso_gramos').removeAttr('disabled', false);
    calc_tarifa_joya();
  });
  
  
  function calc_tarifa_joya(){
    calc_desc_peso();
    let optionTarifaJoya = $("#idtarifario_joya").find('option:selected'); 
    let cobertura = optionTarifaJoya.attr('cobertura');
    
    let preciogramo = optionTarifaJoya.attr('preciogramo');

    let peso = $('#peso_neto').val();
    $('#porcentajecobertura').val(cobertura);
    $('#porcentajevalorcomercial').val('0.00');
    
    let monto_valorcomercial = parseFloat(peso)*parseFloat(preciogramo);
    
    let monto_cobertura = (parseFloat(monto_valorcomercial)*parseFloat(cobertura))/100;   
        
    $('#cobertura').val(monto_cobertura.toFixed(2));
    $('#valorcomercial').val(monto_valorcomercial.toFixed(2));
    
    $('#val-view-cobertura').val(monto_cobertura.toFixed(2));
    $('#val-view-valorcomercial').val(monto_valorcomercial.toFixed(2));
    
  }
  
  function calc_desc_peso(){
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
    
  }

  
</script>    