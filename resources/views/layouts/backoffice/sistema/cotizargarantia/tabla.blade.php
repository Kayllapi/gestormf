<div class="modal-header">
    <h5 class="modal-title">
      Cotizar Garantia  
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()" style="font-size: 20px;"></button>
</div>
<div class="modal-body">
  <div class="row justify-content-center">
    <div class="col-sm-12 col-md-8">
      <div class="card">
        <div class="card-body p-2" id="form-garantias-result">
           <div class="row">
            <div class="col-sm-12 col-md-12 d-none">
              <label>Cliente</label>
              <select class="form-control" id="idcliente" disabled>
                <option></option>
              </select>
            </div>
            <div class="col-sm-12 col-md-6">
              <label>Tipo de Garantia *</label>
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
                      <h1 class="modal-title fs-5 text-white" id="modalValorizacionLabel">Modal title</h1>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="row">
                        <div class="col-sm-12 col-md-6 d-none option-tipo-general">
                          <label>Método Valorizacion</label>
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
                    <!--div class="modal-footer">
                      <button type="button" class="btn btn-success" data-bs-dismiss="modal">Cambiar</button>
                    </div-->
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-12 col-md-6">
              <br>
              <button type="button" class="btn btn-primary"
                      id="button-modal-tipo-garantia" disabled data-bs-toggle="modal" data-bs-target="#modalValorizacion">
                <i class="fa-solid fa-dice-three"></i>
              </button>
            </div>


            <div class="mb-1 mt-2">
              <span class="badge d-block">VALORIZACIÓN</span>
            </div>

            <div class="col-sm-12 col-md-6">
              <label>Cobertura</label>
              <input type="text" class="form-control" id="cobertura" disabled>
              <input type="hidden" class="form-control" id="porcentajecobertura" disabled>
            </div>
            <div class="col-sm-12 col-md-6">
              <label>Valor Comercial</label>
              <input type="text" class="form-control" id="valorcomercial" disabled>
              <input type="hidden" class="form-control" id="porcentajevalorcomercial" disabled>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>  

<script>
  $('#btn-delete-garantia').addClass("d-none");
  @include('app.nuevosistema.select2',['input'=>'#idtipogarantia'])
  @include('app.nuevosistema.select2',['input'=>'#idmetodo_valorizacion'])
  @include('app.nuevosistema.select2',['input'=>'#idestado_garantia'])
  @include('app.nuevosistema.select2',['input'=>'#idestado_garantia_ref'])
  sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idcliente', val: '{{ $idcliente }}' });
  @include('app.nuevosistema.select2',['input'=>'#idtipo_joyas' ])
  @include('app.nuevosistema.select2',['input'=>'#iddescuento_joya' ])
  
  function limpiarcampos(){
      //$("#idtipogarantia").val(null).trigger('change');
      $("#cobertura").val('0.00');
      $("#valorcomercial").val('0.00');
      $("#idmetodo_valorizacion").val(null).trigger('change');
      $("#idtipo_garantia_detalle").val(null).trigger('change');
      $("#valor_mercado").val('0.00');
      $("#val-view-cobertura").val('0.00');
      $("#val-view-valorcomercial").val('0.00');
  }
  
  $("#idtipogarantia").on("change", function(e) {
    limpiarcampos();
    var textTipoGarantia = $("#idtipogarantia").find('option:selected').text();
    
    
    var optionSelected = $("#idtipogarantia").find('option:selected');
    let titleAntiguedad = optionSelected.attr('antiguedad');
    let titleValor = optionSelected.attr('valor');
    let titleCobertura = optionSelected.attr('cobertura');
    
    $('#title-antiguedad').text(titleAntiguedad);
    $('#title-valor').text(titleValor);
    $('#title-cobertura').text(titleCobertura);
    
    $('#modalValorizacionLabel').text(textTipoGarantia);
    $("#modalValorizacion").modal('show');
    $('#button-modal-tipo-garantia').removeAttr('disabled',false)
    
    
    if(e.currentTarget.value == 6){
      $('.option-tipo-general').addClass('d-none');
      $('.option-tipo-joya').removeClass('d-none');
      tarifario_joyas(e.currentTarget.value);  
    }else{
      $('.option-tipo-general').removeClass('d-none');
      $('.option-tipo-joya').addClass('d-none');
//       tipo_garantia(e.currentTarget.value);
      $("#idmetodo_valorizacion").val(0).trigger('change');
      $('#idtipo_garantia_detalle').html('');
      
    }
  });
  $("#idmetodo_valorizacion").on("change", function(e) {
    //limpiarcampos();
 
    var num = $("#idmetodo_valorizacion").find('option:selected').attr('num');
    $('#idtipo_garantia_detalle').removeAttr('disabled',false)
    tipo_garantia(e.currentTarget.value,num);
    
  });
  
  
  function tipo_garantia(idmetodovalorizacion,num){
    let idtipogarantia = $("#idtipogarantia").find('option:selected').val();
    $.ajax({
      url:"{{url('backoffice/0/garantias/showtipogarantia')}}",
      type:'GET',
      data: {
          idtipogarantia      : idtipogarantia,
          idmetodovalorizacion : idmetodovalorizacion
      },
      success: function (res){
        let option_select = `<option></option>`;
        var i = 1;
        $.each(res, function( key, value ) {
          option_select += `<option value="${value.id}" cobertura="${value.cobertura}" valor_comercial="${value.valor_comercial}" >${num}.${i}.- ${value.antiguedad}</option>`;
          i++;
        });
        $('#idtipo_garantia_detalle').html(option_select);
        sistema_select2({ input:'#idtipo_garantia_detalle'});

      }
    })
    
  }
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
            option_select += `<option value="${value.id}" descuento="${value.descuento}" >${value.descuento}% - ${value.detalle_descuento}</option>`;
          });
          $('#idvalorizacion_descuento').html(option_select);
          sistema_select2({ input:'#idvalorizacion_descuento'});
          
        }
      })
    }
  
  $("#idtipo_garantia_detalle").on("change", function(e) {
    console.log(e.currentTarget.value)
    if(e.currentTarget.value!=''){
    $('#valor_mercado').removeAttr('disabled',false)
    calc_montos();
    }
  });
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
  
  function calc_montos(){
    var opcionSeleccionada = $("#idtipo_garantia_detalle").find('option:selected');
    let cobertura = opcionSeleccionada.attr('cobertura');
    let valor_comercial = opcionSeleccionada.attr('valor_comercial');
    let valor_mercado = $('#valor_mercado').val();
    $('#porcentajecobertura').val(cobertura);
    $('#porcentajevalorcomercial').val(valor_comercial);
    let monto_valorcomercial = (parseFloat(valor_mercado) * parseFloat(valor_comercial)) / 100;
    let monto_cobertura = (parseFloat(monto_valorcomercial) * parseFloat(cobertura)) / 100;
    $('#cobertura').val(monto_cobertura.toFixed(2));
    $('#valorcomercial').val(monto_valorcomercial.toFixed(2));
    $('#val-view-cobertura').val(monto_cobertura.toFixed(2));
    $('#val-view-valorcomercial').val(monto_valorcomercial.toFixed(2));
  }
  
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

