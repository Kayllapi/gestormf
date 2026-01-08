  <div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Rematar Producto</span>
      <a class="btn btn-success" href="javascript:;" onclick="mostrar_garantias()"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
  </div>
  <form action="javascript:;" 
        onsubmit="callback({
                            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamocobranza/{{ $prestamobien->id }}',
                            method: 'PUT',
                            data:   {
                              view: 'rematar_garantias'
                            }
                          },
                          function(resultado){
                              mostrar_garantias();
                          },this)">
          <div class="row">
             <div class="col-md-12">
                <label>Nombre de Producto *</label>
                <input type="text" value="{{$prestamobien->producto}}" id="nombre" onkeyup="texto_mayucula(this)"/>
                <label>Descripción (Descripción del Producto)</label>
                <textarea id="descripcion">{{$prestamobien->descripcion}}</textarea>
                <label>Precio Público *</label>
                <input type="number" value="{{$prestamobien->valorestimado}}" id="precioalpublico" step="0.01" min="0"/>
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
             </div>
             <div class="col-md-12">
             </div>
           </div>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de rematar la garantia?</b>
    </div>
    <button type="submit" class="btn  mx-btn-post">Rematar Producto</button>
  </form>
<script>
$("#idcategoria").select2({
    placeholder: "---  Seleccionar ---"
});
</script>