<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/cargo') }}',
        method: 'POST',
        data:{
            view: 'registrar',
            idcredito: '{{$idcredito}}'
        }
    },
    function(resultado){
        show_data_cargo();
        $('#close_opcionescredito').click();
    },this)"> 
    <div class="modal-header">
        <h5 class="modal-title">Registrar Cargo</h5>
        <button type="button" class="btn-close" id="close_opcionescredito" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
      <div class="row">
          <div class="col-sm-12 col-md-12">
            <div class="row">
                <div class="col-md-8">
                    <label>Tipo Cargos</label>
                    <select class="form-control" id="idtipocargo">
                      <option></option>
                      @foreach($tipocargos as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                      @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Importe S/.</label>
                    <input type="text" class="form-control" id="importe" valida_input_vacio>
                </div>
            </div>
          </div>
          <div class="col-sm-12 col-md-12">
            <label>Descripción</label>
            <textarea class="form-control" id="descripcion"></textarea>
            
              
          </div>
      </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
    </div>
</form>  
<script>
  valida_input_vacio();
  @include('app.nuevosistema.select2',['input'=>'#idtipocargo'])
  
</script>    