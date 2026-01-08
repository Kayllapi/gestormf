@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Producto</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/producto') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/producto',
        method: 'POST',
        data:{
            view: 'registrar',
            idtienda: '{{ $tienda->id }}'
        }
    },
    function(resultado){
           location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/producto') }}';                                                             
    },this)"> 
          <div class="row">
             <div class="col-md-6">
                @if(configuracion($tienda->id,'sistema_tipocodigoproducto')['valor']==2)
                      <label>Código de Producto</label>
                      <table class="table">
                              <tr>
                                  <td style="background-color: #eae7e7;padding: 10px;text-align:center; width: 40px;font-weight: bold;">01</td>
                                  <td style="background-color: #eae7e7;"><input type="text" id="codigo1"></td>
                                  <td style="background-color: #eae7e7;padding: 10px;text-align:center; width: 40px;font-weight: bold;">06</td>
                                  <td style="background-color: #eae7e7;"><input type="text" id="codigo6"></td>
                              </tr>
                              <tr>
                                  <td style="background-color: #eae7e7;padding: 10px;text-align:center;font-weight: bold;">02</td>
                                  <td style="background-color: #eae7e7;"><input type="text" id="codigo2"></td>
                                  <td style="background-color: #eae7e7;padding: 10px;text-align:center; width: 40px;font-weight: bold;">07</td>
                                  <td style="background-color: #eae7e7;"><input type="text" id="codigo7"></td>
                              </tr>
                              <tr>
                                  <td style="background-color: #eae7e7;padding: 10px;text-align:center;font-weight: bold;">03</td>
                                  <td style="background-color: #eae7e7;"><input type="text" id="codigo3"></td>
                                  <td style="background-color: #eae7e7;padding: 10px;text-align:center; width: 40px;font-weight: bold;">08</td>
                                  <td style="background-color: #eae7e7;"><input type="text" id="codigo8"></td>
                              </tr>
                              <tr>
                                  <td style="background-color: #eae7e7;padding: 10px;text-align:center;font-weight: bold;">04</td>
                                  <td style="background-color: #eae7e7;"><input type="text" id="codigo4"></td>
                                  <td style="background-color: #eae7e7;padding: 10px;text-align:center; width: 40px;font-weight: bold;">09</td>
                                  <td style="background-color: #eae7e7;"><input type="text" id="codigo9"></td>
                              </tr>
                              <tr>
                                  <td style="background-color: #eae7e7;padding: 10px;text-align:center;font-weight: bold;">05</td>
                                  <td style="background-color: #eae7e7;"><input type="text" id="codigo5"></td>
                                  <td style="background-color: #eae7e7;padding: 10px;text-align:center; width: 40px;font-weight: bold;">10</td>
                                  <td style="background-color: #eae7e7;"><input type="text" id="codigo10"></td>
                              </tr>
                      </table>
                @else
                <label>Código de Producto</label>
                <input type="text" id="codigo"/>
                @endif
                <label>Nombre de Producto *</label>
                <input type="text" id="nombre" onkeyup="texto_mayucula(this)"/>
                <label>Precio Público *</label>
                <input type="number" id="precioalpublico" step="0.01" min="0"/>
                @if(configuracion($tienda->id,'sistema_estadounidadmedida')['valor']==1)
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
                @endif
             </div>
             <div class="col-md-6">
                @if(configuracion($tienda->id,'sistema_estadounidadmedida')['valor']!=1)
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
                @endif
                <label>Marca</label>
                <select id="idmarca">
                    <option></option>
                    @foreach($marcas as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
                @if(configuracion($tienda->id,'sistema_estadounidadmedida')['valor']==1)
                            <div class="list-single-main-wrapper fl-wrap">
                                <div class="breadcrumbs gradient-bg fl-wrap">
                                  <span>Presentación</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-7">
                                    <label>Unidad de Medida *</label>
                                    <select id="idunidadmedida">
                                        <option></option>
                                        @foreach($unidadmedidas as $value)
                                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label>Por (Cantidad de Unidades) *</label>
                                    <input type="number" value="1" id="por" onkeyup="seleccionar_cantidadunidadmedida()"/>
                                </div>
                            </div>
                              <label>Producto a agrupar *</label>
                              <select id="idproducto" disabled>
                                  <option></option>
                              </select>
                @endif
             </div>
           </div>
          <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>                             
@endsection
@section('subscripts')
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
  
$("#idproducto").select2({
    @include('app.select2_producto')
});
  
function seleccionar_cantidadunidadmedida(){
    var por = $('#por').val();
    $('#idproducto').attr('disabled','true');
    if(por>1){
        $('#idproducto').removeAttr('disabled');
    }
}
</script>
@endsection