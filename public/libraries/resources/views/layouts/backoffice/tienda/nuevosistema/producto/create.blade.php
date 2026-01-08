<form @include('app.nuevosistema.submit',['method'=>'POST','view'=>'registrar'])> 
    <div class="row">
       <div class="col-md-6">
          <label>Código de Producto</label>
          <input type="text" id="codigo"/>
          <label>Nombre de Producto *</label>
          <input type="text" id="nombre"/>
          <label>Precio Público *</label>
          <input type="number" id="precioalpublico" step="0.01" min="0"/>
       </div>
       <div class="col-md-6">
          <label>Categoría *</label>
          <select id="idcategoria">
              <option></option>
              @foreach($categorias as $value)
              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                  <?php
                  $subcategorias = DB::table('s_categoria')
                      ->where('s_categoria.s_idcategoria',$value->id)
                      ->orderBy('s_categoria.nombre','asc')
                      ->get();
                  ?>
                  @foreach($subcategorias as $subvalue)
                  <option value="{{$subvalue->id}}">{{ $value->nombre }} / {{ $subvalue->nombre }}</option>
                  @endforeach
              @endforeach
          </select>
          <label>Marca</label>
          <select id="idmarca">
              <option></option>
              @foreach($marcas as $value)
              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
          </select>
       </div>
     </div>
     <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>                 
<script>
$("#idcategoria").select2({
    placeholder: "---  Seleccionar ---"
});
  
$("#idunidadmedida").select2({
    placeholder: "---  Seleccionar ---"
}).val(1).trigger("change");
  
$("#idmarca").select2({
    placeholder: "---  Seleccionar ---",
    allowClear: true
});
</script>