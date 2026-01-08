@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Movimientos de Productos</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productomovimiento/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido table-hover">
    <thead class="thead-dark">
      <tr>
        <th>Tipo</th>
        <th>Motivo</th>
        <th>Responsable</th>
        <th>Producto</th>
        <th>Cantidad</th>
        <th width="180px">Fecha de registro</th>
        <th width="10px">Estado</th>
      </tr>
    </thead>
    @include('app.tablesearch',[
        'searchs'=>['tipo','motivo','responsable','producto'],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/productomovimiento')
    ])
    <tbody>
        @foreach($s_productomovimiento as $value)
        <?php
        /*productosaldo_actualizar(
                        $tienda->id,
                        'MOVIMIENTO INGRESO',
                        $value->productocodigo,
                        $value->productonombre,
                        $value->idunidadmedida,
                        $value->por,
                        $value->cantidad,
                        $value->preciounitario,
                        $value->total,
                        $value->s_idproducto,
                        $value->id
                    );*/
      ?>
        <tr>
          <td onclick="mostrar_producto({{$value->id}})">{{$value->nombretipomovimiento}}</td>
          <td onclick="mostrar_producto({{$value->id}})">{{$value->motivo}}</td>
          <td onclick="mostrar_producto({{$value->id}})">{{$value->responsablenombre}}</td>
          <td onclick="mostrar_producto({{$value->id}})">{{$value->productonombre}}</td>
          <td onclick="mostrar_producto({{$value->id}})">{{$value->cantidad}}</td>
          <td onclick="mostrar_producto({{$value->id}})">{{$value->fecharegistro}}</td>
          <td onclick="mostrar_producto({{$value->id}})">
            @if($value->idestadoproductomovimiento==2)
              <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Confirmado</span></div>
            @else
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fas fa-sync-alt"></i> Pendiente</span></div> 
            @endif
          </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
{{ $s_productomovimiento->links('app.tablepagination', ['results' => $s_productomovimiento]) }}
@endsection
@section('subscripts')
<script>
function mostrar_producto(idproducto){
    $('#stopscanear').click();
    $('#cont-camaraqr').css('display','none');
    $('#cont-producto-imagen').css('display','block');
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/productomovimiento/showstockimagenproducto')}}",
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
<script src="{{ url('public/libraries/webcodecamjs/js/filereader.js') }}"></script>
<script src="{{ url('public/libraries/webcodecamjs/js/qrcodelib.js') }}"></script>
<script src="{{ url('public/libraries/webcodecamjs/js/webcodecamjquery.js') }}"></script>
<script src="{{ url('public/libraries/webcodecamjs/js/mainjquerycallback.js') }}"></script>
<style>
  @media (max-width: 1064px){
    #cont-camaraqr {
        height: 303px !important;
    }
    #webcodecam-canvas {
        height: 300px !important;
    }
  }

</style>
<script>
webcodecamcallback({
      play : '#playscanear',
      stop : '#stopscanear',
      contenedor : '#cont-camaraqr',
      contenedorOcultar : '#cont-producto-imagen',
      canvas : '#webcodecam-canvas'
  },
  function(resultado){
      if(resultado!=''){
          $("#codigoProducto").val(resultado.code);
          $("#buscador_producto").submit();
      }  
});
</script>
@endsection

