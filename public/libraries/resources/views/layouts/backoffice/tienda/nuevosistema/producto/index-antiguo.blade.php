@include('app.nuevosistema.cabecera',[
    'botones'=>[
        'atrassubmodulo',
        'create:registrar/Registrar',
        'edit:editar/Editar',
        'delete:eliminar/Eliminar'
    ]
])

@include('app.nuevosistema.tabla',[
    'tabla' => 'tabla-'.$_GET['name_modulo'],
    'thead' => [
        ['data' => 'id'],
        ['data' => 'Código'],
        ['data' => 'Nombre'],
        ['data' => 'Categoría'],
        ['data' => 'Marca'],
        ['data' => 'U. Medida'],
        ['data' => 'Precio'],
        ['data' => 'T. Virtual'],
        ['data' => 'Estado','width' => '10px'],
    ],
    'tbody' => [
        ['data' => 'id'],
        ['data' => 'codigo'],
        ['data' => 'nombre'],
        ['data' => 'categoria'],
        ['data' => 'marca'],
        ['data' => 'unidadmedida'],
        ['data' => 'precio'],
        ['data' => 'estadotiendavirtual'],
        ['data' => 'estado'],
    ]
])



<script>
$("#buscar_estadoProducto").select2({
    placeholder: "--- Estado de Sistema ---",
    minimumResultsForSearch: -1,
    allowClear: true
});
$("#buscar_estadotvProducto").select2({
    placeholder: "--- Estado de T. Virtual ---",
    minimumResultsForSearch: -1,
    allowClear: true
});
function mostrar_producto(idproducto){
    $('#stopscanear').click();
    $('#cont-camaraqr').css('display','none');
    $('#cont-producto-imagen').css('display','block');
    $.ajax({
        url:"{{url($_GET['url_sistema'].'/'.$tienda->id.'/producto/showstockimagenproducto')}}",
        type:'GET',
        data: {
            idproducto : idproducto
        },
        beforeSend: function (data) {
            load('#cont-producto-imagen');
            load('#cont-producto-stock');
        },
        success: function (respuesta){
            $('#cont-producto-imagen').html(respuesta['imagenes']);
            $('#cont-producto-stock').html(respuesta['stock']);
        }
    });
}
</script>