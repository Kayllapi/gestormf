@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Pedidos',
    'botones'=>[
        'registrar:/'.$tienda->id.'/comidaordenpedido/create: Registrar'
    ]
])
<div id="cont-index-ordenpedido">
  @include('app.sistema.tabla',[
    'tabla' => 'tabla-preaprobados',
    'script' => 'scriptsapp2',
    'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/comidaordenpedido/show-indexordenpedido'),
    'thead' => [
        ['data' => 'Codigo'],
        ['data' => 'Mesa'],
        ['data' => 'Total'],
        ['data' => 'Responsable'],
        ['data' => 'Fecha Pedido'],
        ['data' => 'Fecha Comandado'],
        ['data' => 'Fecha Vendido'],
        ['data' => 'Estado', 'width' => '10px'],
        ['data' => '', 'width' => '10px']
    ],
    'tbody' => [
        ['data' => 'codigo'],
        ['data' => 'datapedido'],
        ['data' => 'totalpedido'],
        ['data' => 'responsable'],
        ['data' => 'fechapedido'],
        ['data' => 'fechacomandado'],
        ['data' => 'fechavendido'],
        ['data' => 'estado'],
        ['render' => 'opcion'],
    ],
    'tfoot' => [
        ['input' => ''],
        ['input' => ''],
        ['input' => ''],
        ['input' => ''],
        ['input' => ''],
        ['input' => ''],
        ['input' => ''],
        ['input' => ''],
        ['input' => ''],
    ]
  ])
</div>
<div id="cont-resultado-ordenpedido"></div>
@endsection
@section('subscripts')
<script>
    function detalle_ordenpedido(idordenpedido) {
      $('#cont-index-ordenpedido').css('display', 'none');
      pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id) }}/comidaordenpedido/'+idordenpedido+'/edit?view=comida_detalleordenpedido',result:'#cont-resultado-ordenpedido'});
    }
    function anular_ordenpedido(idordenpedido) {
      $('#cont-index-ordenpedido').css('display', 'none');
      pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id) }}/comidaordenpedido/'+idordenpedido+'/edit?view=comida_anularordenpedido',result:'#cont-resultado-ordenpedido'});
    }
    function eliminar_ordenpedido(idordenpedido) {
      $('#cont-index-ordenpedido').css('display', 'none');
      pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id) }}/comidaordenpedido/'+idordenpedido+'/edit?view=comida_eliminarordenpedido',result:'#cont-resultado-ordenpedido'});
    }
</script>
@endsection