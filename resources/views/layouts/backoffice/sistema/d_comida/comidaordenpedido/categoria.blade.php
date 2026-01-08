<?php
$background = 'background-color:#31353d;';
if(configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'){
    $background = 'background-color:'.configuracion($tienda->id,'sistema_color')['valor'].';';
}
?>        
<div class="row">
    <div class="col-md-12">
        <div onclick="cargar_mesa()" class="cont_cabecera" style="float: left;width: 100%;<?php echo $background ?>">
            <div id="numeromesa_texto" class="pedido_cabecera"><i class="fa fa-arrow-left"></i> Atras | Mesa {{$numeromesa}}</div>
        </div>
    </div>
</div>
<div class="row">
@foreach($categorias as $value)
    <div class="col-xs-6 col-sm-6 col-md-6">
        <div class="cont_categoria" onclick="cargar_producto('{{$value->id}}')" style="<?php echo $background ?>">
            <div class="categoria_nombre">{{$value->nombre}}</div>
        </div>
    </div>
@endforeach
<script>
function cargar_producto(idcategoria){
    load('#cont-pedido-1');
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidaordenpedido/create')}}",
        type:'GET',
        data: {
            view : 'producto',
            idcategoria : idcategoria,
            numeromesa : '{{$numeromesa}}'
        },
        success: function (respuesta){
            $("#cont-pedido-1").html(respuesta);
        }
    })
} 
</script>