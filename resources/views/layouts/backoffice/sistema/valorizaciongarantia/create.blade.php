<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/valorizaciongarantia') }}',
        method: 'POST',
        data:{
            view: 'registrar'
        }
    },
    function(resultado){  
        lista_valorizacion_garantia();
              load_create_tipogarantia()
    },this)"> 
    
    <div class="modal-body">
        <div class="row">
           <div class="col-sm-12 col-md-4 mb-1">
              <label>TIPO DE GARANTIA</label>
              <select class="form-control" id="idtipogarantia">
                  <option></option>
                @foreach($tipo_garantia as $value)
                  <option value="{{ $value->id }}" antiguedad="{{ $value->antiguedad}}" valor="{{ $value->valor}}" cobertura="{{ $value->cobertura}}">{{ $value->nombre }}</option>
                @endforeach
              </select>

          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-3 mb-1">
              <label>Metodo de Valorización *</label>
              <select class="form-control" id="idmetodovalorizacion">
                  <option></option>
                @foreach($metodo_valorizacion as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
              </select>
                
          </div>
          <div class="col-sm-12 col-md-3 mb-1">
                <label id="title-antiguedad">Antiguedad de Compra/Producción *</label>
                <input type="text" class="form-control" id="antiguedad_compra">
          </div>
          <div class="col-sm-12 col-md-3 mb-1">
                <label id="title-valor">VALOR COMERCIAL (%) del precio de compra</label>
                <input type="text" class="form-control" id="valor_comercial" value="0.00">
            </div>
          <div class="col-sm-12 col-md-3 mb-1">
                <label id="title-cobertura">COBERTURA (%) de valor comercial</label>
                <input type="text" class="form-control" id="cobertura" value="0.00">
           </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form>  
<script>
    $('#table-detalle-valorizacion-garantia > tbody').html('');
    
    @include('app.nuevosistema.select2',['input'=>'#idmetodovalorizacion'])
    @include('app.nuevosistema.select2',['input'=>'#idtipogarantia'])
    $("#idtipogarantia").on("change", function(e) {
      
      var optionSelected = $("#idtipogarantia").find('option:selected');
      let titleAntiguedad = optionSelected.attr('antiguedad');
      let titleValor = optionSelected.attr('valor');
      let titleCobertura = optionSelected.attr('cobertura');
      $('#title-antiguedad').text(titleAntiguedad);
      $('#title-valor').text(titleValor);
      $('#title-cobertura').text(titleCobertura);
      lista_valorizacion_garantia();
    });
    $("#idmetodovalorizacion").on("change", function(e) {
      lista_valorizacion_garantia();
    });
  
    
    
</script>    