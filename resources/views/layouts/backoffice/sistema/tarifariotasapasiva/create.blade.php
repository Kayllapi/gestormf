<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/tarifariotasapasiva') }}',
        method: 'POST',
        data:{
            view: 'registrar'
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
              <input type="text" class="form-control" id="producto">
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Monto (<=):</label>
            <div class="col-sm-6">
              <input type="number" class="form-control" value="0.00" step="any" id="monto">
            </div>
          </div>
          <div class="row d-none container-plazo">
            <label class="col-sm-3 col-form-label">Plazo</label>
            <div class="col-sm-6">
              <input type="number" class="form-control" value="0.00" step="any" id="plazo">
            </div>
          </div>
           <div class="row">
            <label class="col-sm-3 col-form-label">TEA(%)</label>
            <div class="col-sm-6">
              <input type="number" class="form-control" value="0.00" step="any" id="tea">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-3"></label>
            <div class="col-sm-6">
              <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> GENERAR TARIFARIO</button>
            </div>
          </div>
          
         
        </div>
      </div>
    </div>
</form>  
<script>
  @include('app.nuevosistema.select2',['input'=>'#idtipo_ahorro'])
  
  $("#idtipo_ahorro").on("change", function(e) {
    show_data(e.currentTarget.value);
    if(e.currentTarget.value == 2){
       $('.container-plazo').removeClass('d-none');
    }
    else{
      $('.container-plazo').addClass('d-none');
    }
  });

</script>    