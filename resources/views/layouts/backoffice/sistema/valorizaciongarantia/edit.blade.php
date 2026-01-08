<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$idtienda.'/valorizaciongarantia/'.$tipogarantiadetalle->id) }}',
          method: 'PUT',
          data:{
              view: 'editar',
              idtienda: {{$tienda->id}}
          }
      },
      function(resultado){
          lista_valorizacion_garantia();
          load_create_tipogarantia()
      },this)">
    <div class="modal-header d-none">
        <h5 class="modal-title">Editar Valorizacion</h5>
        <button type="button" class="btn-close" id="modal-close-valorizaciongarantia-editar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
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
                @foreach($metodo_valorizacion as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
              </select>

          </div>
          <div class="col-sm-12 col-md-3 mb-1">
              <label>Antiguedad de Compra/Producción *</label>
              <input type="text" class="form-control" id="antiguedad_compra" value="{{ $tipogarantiadetalle->antiguedad }}">
          </div>
          <div class="col-sm-12 col-md-3 mb-1">
              <label>VALOR COMERCIAL (%) del precio de compra</label>
              <input type="text" class="form-control" id="valor_comercial" value="{{ $tipogarantiadetalle->valor_comercial }}">
          </div>
          <div class="col-sm-12 col-md-3 mb-1">
              <label>COBERTURA(%) de valor comercial</label>
              <input type="text" class="form-control" id="cobertura" value="{{ $tipogarantiadetalle->cobertura }}">
          </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" onclick="eliminar_garantia()"><i class="fa-solid fa-trash"></i> Eliminar Valorización</button>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form> 
<script>

    function eliminar_garantia(){
      modal({ route:"{{url('backoffice/'.$tienda->id.'/valorizaciongarantia/'.$tipogarantiadetalle->id.'/edit?view=eliminar')}}" });  
    }
    @include('app.nuevosistema.select2',['input'=>'#idmetodovalorizacion','val' => $tipogarantiadetalle->idmetodo_valorizacion ])
    @include('app.nuevosistema.select2',['input'=>'#idtipogarantia','val' => $tipogarantiadetalle->idtipo_garantia ])
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