<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/tarifariotasapasiva/'.$tarifario->id) }}',
        method: 'PUT',
          data:{
              view: 'editar',
          }
      },
      function(resultado){
          show_data();
          load_form_create();
      },this)">
    <div class="modal-body">
      <div class="row justify-content-center">
        <div class="col-sm-12 col-md-6">
          <div class="row">
            <label class="col-sm-3 col-form-label">Tipo Ahorro:</label>
            <div class="col-sm-6">
              <select class="form-control" id="idtipo_ahorro">
                <option></option>
                @foreach($tipo_ahorro as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Producto:</label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="producto" value="{{ $tarifario->producto }}">
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Monto (<=):</label>
            <div class="col-sm-6">
              <input type="number" class="form-control" value="{{ $tarifario->monto }}" step="any" id="monto">
            </div>
          </div>
          <div class="row d-none container-plazo">
            <label class="col-sm-3 col-form-label">Plazo</label>
            <div class="col-sm-6">
              <input type="number" class="form-control" value="{{ $tarifario->plazo }}" step="any" id="plazo">
            </div>
          </div>
           <div class="row">
            <label class="col-sm-3 col-form-label">TEA(%)</label>
            <div class="col-sm-6">
              <input type="number" class="form-control" value="{{ $tarifario->tea }}" step="any" id="tea">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-3"></label>
            <div class="col-sm-6">
              <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
              <button type="button" onclick="load_form_delete()" class="btn btn-danger"><i class="fa-solid fa-trash"></i> ELIMINAR</button>
            </div>
          </div>
          
         
        </div>
      </div>
    </div>
</form>  
<script>
  @include('app.nuevosistema.select2',['input'=>'#idtipo_ahorro', 'val' => $tarifario->idtipo_ahorro ])
  
  $("#idtipo_ahorro").on("change", function(e) {
    if(e.currentTarget.value == 2){
       $('.container-plazo').removeClass('d-none');
    }
    else{
      $('.container-plazo').addClass('d-none');
    }
  }).val('{{ $tarifario->idtipo_ahorro }}').trigger('change')

  function load_form_delete(){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/tarifariotasapasiva/'.$tarifario->id.'/edit?view=eliminar')}}" });  
  }
</script>    