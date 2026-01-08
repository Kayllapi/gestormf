@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Ordenes de Pedido',
])
<div id="cont-pedido"></div> 
<style>
  .cont_mesa {
      padding: 5px;
      border-radius: 5px;
      margin-bottom: 5px;
      height: 82px;
      cursor: pointer;
  }
  .mesa_numero {
      line-height: 2;
      font-size: 16px;
      color: #ffffff;
      font-weight: bold;
      cursor: pointer;
  }
  .mesa_tiempo {
      font-size: 12px;
      color: #ffffff;
  }
  .mesa_mesero {
      font-size: 14px;
      color: #ffffff;
      padding-bottom: 8px;
  }
  
  .cont_cabecera {
      padding: 5px;
      border-radius: 5px;
      margin-bottom: 5px;
  }
  .pedido_cabecera {
      line-height: 2;
      font-size: 16px;
      color: #ffffff;
      font-weight: bold;
      cursor: pointer;
  }
  
  .cont_categoria {
      padding: 5px;
      border-radius: 5px;
      margin-bottom: 5px;
  }
  .categoria_nombre {
      line-height: 1.2;
      font-size: 14px;
      color: #ffffff;
      font-weight: bold;
      cursor: pointer;    
      padding-top: 8px;
      padding-bottom: 8px;
  }
  .cont_producto {
      padding: 5px;
      border-radius: 5px;
      margin-bottom: 5px;
      cursor: pointer;
  }
  .producto_nombre {
      line-height: 1.2;
      font-size: 14px;
      color: #ffffff;
      font-weight: bold;
      padding-top: 8px;
      padding-bottom: 5px;
  }
  .producto_precio {
      /*line-height: 2;*/
      font-size: 14px;
      color: #ffffff;
      font-weight: bold;
      padding-bottom: 8px;
  }
</style>
@endsection
@section('htmls')
<!--  modal ordenarpedido --> 
<div class="main-register-wrap modal-ordenarpedido" id="modal-ordenarpedido">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <h3 style="text-align: center;">Orden de Pedido</h3>
            <div class="mx-modal-cuerpo">
            <div id="contenido-producto-ordenarpedido"></div>
            </div>
        </div>
    </div>
</div>
<!--  fin modal ordenarpedido --> 
@endsection
@section('subscripts')
<script>
modal({click:'#modal-ordenarpedido'});
cargar_mesa(); 
function cargar_mesa(){
    load('#cont-pedido');
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidaordenpedido/create')}}",
        type:'GET',
        data: {
            view : 'mesa'
        },
        success: function (respuesta){
            $("#cont-pedido").html(respuesta);
        }
    })
}   
function cargar_pedido(numeromesa,idordenpedido){
    load('#cont-pedido');
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidaordenpedido/create')}}",
        type:'GET',
        data: {
            view : 'pedido',
            numeromesa : numeromesa,
            idordenpedido : idordenpedido
        },
        success: function (respuesta){
            $("#cont-pedido").html(respuesta);
        }
    })
}

function enviar_cocina(idordenpedido){
    callback({
        route:  'backoffice/tienda/sistema/{{ $tienda->id }}/comida/comidaordenpedido/'+idordenpedido,
        method: 'PUT',
        carga:  '#mx-carga-ordenpedido',
        data:{
            view: 'enviar_cocina',
        }
    },
    function(resultado){
        removecarga({input:'#mx-carga-ordenpedido'});
    },this)
    var myIframe = document.getElementById("imprimir-ordenpedido").contentWindow;
    myIframe.focus();
    myIframe.print();
    return false;
}
function agregar_pedido(){
    $('.modal-ordenarpedido').css('display','none');
}
function cambiar_mesa(){
    //removecarga({input:'#mx-carga-ordenpedido'});
    $('.modal-ordenarpedido').css('display','none');
    cargar_mesa();
}
</script>
@endsection