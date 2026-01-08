
<a href="javascript:;" onclick="ir_submodulo({{$_GET['idmodulo']}})" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-modulo">
  <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/atras.png')}}" class="category-bubble-icon">
  <h2 data-v-b789b216="" class="category-bubble-title">Atras</h2>
</a> 
@if($tienda->idcategoria==13 or $tienda->idcategoria==24 or $tienda->idcategoria==30)
<a href="javascript:;" id="modal-master" onclick="load_modulo('{{$_GET['nombre_modulo']}}','{{$_GET['imagen_modulo']}}',{{$_GET['idmodulo']}},'{{$_GET['nombre_submodulo']}}','{{$_GET['imagen_submodulo']}}',{{$_GET['idsubmodulo']}},'{{$_GET['name_modulo']}}','Configuración General','config_general',)" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-submodulo" >
    <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/otro.png')}}" class="category-bubble-icon">
  <h2 data-v-b789b216="" class="category-bubble-title">Configuración General</h2>
</a>
@endif


@if($tienda->idcategoria==24 or $tienda->idcategoria==30)
<a href="javascript:;" id="modal-master" onclick="load_modulo('{{$_GET['nombre_modulo']}}','{{$_GET['imagen_modulo']}}',{{$_GET['idmodulo']}},'{{$_GET['nombre_submodulo']}}','{{$_GET['imagen_submodulo']}}',{{$_GET['idsubmodulo']}},'{{$_GET['name_modulo']}}','Configurar Venta','config_comercio')" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-submodulo" >
    <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/otro.png')}}" class="category-bubble-icon">
  <h2 data-v-b789b216="" class="category-bubble-title">Configurar Venta</h2>
</a>
@endif
@if($tienda->idcategoria==30)
<a href="javascript:;" id="modal-master" onclick="load_modulo('{{$_GET['nombre_modulo']}}','{{$_GET['imagen_modulo']}}',{{$_GET['idmodulo']}},'{{$_GET['nombre_submodulo']}}','{{$_GET['imagen_submodulo']}}',{{$_GET['idsubmodulo']}},'{{$_GET['name_modulo']}}','Configurar Mesas (Comida)','config_comida')" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-submodulo" >
    <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/otro.png')}}" class="category-bubble-icon">
  <h2 data-v-b789b216="" class="category-bubble-title">Configurar Mesas (Comida)</h2>
</a>
@endif
@if($tienda->idcategoria==13 or $tienda->idcategoria==24 or $tienda->idcategoria==30)
<a href="javascript:;" id="modal-master" onclick="load_modulo('{{$_GET['nombre_modulo']}}','{{$_GET['imagen_modulo']}}',{{$_GET['idmodulo']}},'{{$_GET['nombre_submodulo']}}','{{$_GET['imagen_submodulo']}}',{{$_GET['idsubmodulo']}},'{{$_GET['name_modulo']}}','Configurar Facturación','config_facturacion')" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-submodulo" >
    <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/otro.png')}}" class="category-bubble-icon">
  <h2 data-v-b789b216="" class="category-bubble-title">Configurar Facturación</h2>
</a>
@endif
@if($tienda->idcategoria==13)
<a href="javascript:;" id="modal-master" onclick="load_modulo('{{$_GET['nombre_modulo']}}','{{$_GET['imagen_modulo']}}',{{$_GET['idmodulo']}},'{{$_GET['nombre_submodulo']}}','{{$_GET['imagen_submodulo']}}',{{$_GET['idsubmodulo']}},'{{$_GET['name_modulo']}}','Configurar Prestamo','config_prestamo')" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-submodulo" >
    <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulosistema/otro.png')}}" class="category-bubble-icon">
  <h2 data-v-b789b216="" class="category-bubble-title">Configurar Prestamo</h2>
</a>
@endif