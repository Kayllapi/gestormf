<div id="mx-carga-ordenpedido">
    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div id="cont-pedido-1"></div> 
        </div>
        <div class="col-sm-6 col-md-6">
            <div id="cont-pedido-2"></div> 
        </div>
    </div>
</div>
<script>
cargar_categoria('{{$numeromesa}}');
cargar_ordenpedido('{{$numeromesa}}','{{$idpedido}}');
function cargar_categoria(numeromesa){
    load('#cont-pedido-1');
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidaordenpedido/create')}}",
        type:'GET',
        data: {
            view : 'categoria',
            numeromesa : '{{$numeromesa}}'
        },
        success: function (respuesta){
            $("#cont-pedido-1").html(respuesta);
        }
    })
} 
function cargar_ordenpedido(numeromesa,idordenpedido){
    load('#cont-pedido-2');
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidaordenpedido/create')}}",
        type:'GET',
        data: {
            view : 'ordenpedido',
            numeromesa : '{{$numeromesa}}',
            idordenpedido : idordenpedido
        },
        success: function (respuesta){
            $("#cont-pedido-2").html(respuesta);
        }
    })
} 
</script>