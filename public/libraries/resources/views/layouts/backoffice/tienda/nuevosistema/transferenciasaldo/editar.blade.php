<form @include('app.nuevosistema.submit',['method'=>'PUT',
  'view'=>'editar',
  'id' =>$transferenciasaldo->id])> 
    <div class="row">
       <div class="col-md-6">
          <label>De *</label>
          <select id="idcajaorigen">
              <option></option>
              @foreach($cajas as $value)
                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
          </select>
         
          <label for="idcajadestino">Para *</label>
          <select class="form-control" id="idcajadestino">
              <option></option>
              @foreach($cajas as $value)
                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
          </select>
       </div>
       <div class="col-md-6">
          <label for="monto">Monto *</label>
          <input type="number" value="{{ $transferenciasaldo->monto }}" min="0" step="0.01" id="monto">
          <label for="motivo">Motivo *</label>
          <input type="text" id="motivo" value="{{ $transferenciasaldo->motivo }}">
       </div>
     </div>
     <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>                 
<script>
$("#idcajaorigen").select2({
    placeholder: "---  Seleccionar ---"
}).val({{ $transferenciasaldo->idcajaorigen }}).trigger('change');
  
$("#idcajadestino").select2({
    placeholder: "---  Seleccionar ---"
}).val({{ $transferenciasaldo->idcajadestino }}).trigger('change'); 
</script>