<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/creditoordinario') }}',
        method: 'POST',
        data:{
            view: 'registrar'
        }
    },
    function(resultado){
        lista_garantias_cliente();
        load_nuevo_credito();
    },this)"> 
    <div class="modal-body">
      <div class="row justify-content-center">
        <div class="col-sm-12 col-md-6">
          
          <div class="row">
            <label class="col-sm-3 col-form-label">Nombre de Producto</label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="nombre">
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Tipo de Crédito</label>
            <div class="col-sm-6">
              <select class="form-control" id="idtipo_credito">
                <option></option>
                @foreach($tipo_credito as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Modalidad de Calculo</label>
            <div class="col-sm-6">
              <select class="form-control" id="modalidad">
                <option></option>
                <option value="Interes Simple">Interes Simple</option>
                <option value="Interes Compuesto">Interes Compuesto</option>
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Garantia Prendaria</label>
            <div class="col-sm-6">
              <select class="form-control" id="garantiaprendatario">
                <option></option>
                <option value="SI">SI</option>
                <option value="NO">NO</option>
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Con Evaluación</label>
            <div class="col-sm-6">
              <select class="form-control" id="conevaluacion">
                <option></option>
                <option value="SI">SI</option>
                <option value="NO">NO</option>
              </select>
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-3"></label>
            <div class="col-sm-6">
              <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> GENERAR PRODUCTO</button>
            </div>
          </div>
          
         
        </div>
      </div>
    </div>
</form>  
<script>
  @include('app.nuevosistema.select2',['input'=>'#idtipo_credito'])
  @include('app.nuevosistema.select2',['input'=>'#modalidad'])
  @include('app.nuevosistema.select2',['input'=>'#garantiaprendatario'])
  @include('app.nuevosistema.select2',['input'=>'#conevaluacion'])
</script>    