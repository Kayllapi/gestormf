@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de Stock</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reportekardex') }}" method="GET"> 
    <div class="custom-form">
      <div class="row">
         <div class="col-md-12">
            <label>Producto</label>
            <select id="idproducto" name="idproducto">
                <?php
                $s_producto = DB::table('s_producto')
                  ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                  ->where('s_producto.id',isset($_GET['idproducto'])?$_GET['idproducto']:'0')
                  ->select(
                      's_producto.*',
                      'unidadmedida.nombre as unidadmedidanombre'
                  )
                  ->first();
                $value = '';
                $name = '';
                if($s_producto!=''){
                    $value = $s_producto->id;
                    $name = $s_producto->codigo.' / '.$s_producto->nombre.' / '.$s_producto->unidadmedidanombre.' x '.$s_producto->por.' / '.$s_producto->precioalpublico;
                }
                ?>
                <option value="{{$value}}">{{$name}}</option>
            </select>
         </div>
        <div class="col-md-6">
          <div class="row">
            <div class="col-md-6">
                <a href="javascript:;" onclick="reporte('reporte')" class="btn  big-btn  color-bg flat-btn" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
            </div>
            <div class="col-md-6">
                  <a href="javascript:;" onclick="reporte('excel')"class="btn  big-btn  color-bg flat-btn" style="margin-bottom:10px;"><i class="fa fa-file-excel"></i>  Exportar Excel</a>
            </div>
          </div>
        </div>
         
       </div>
    </div>
</form>
<?php

      /*$s_producto  = DB::table('s_producto')
            ->where('s_producto.s_idproducto','<>',0)
            ->where('s_producto.por',1)
            ->orderBy('s_producto.id','asc')
            ->get();
      foreach($s_producto as $value){
           productosaldo_actualizar(
                $tienda->id,
                $value->s_idproducto,
                'SALDO AGRUPADO',
                $value->idunidadmedida,
                $value->por,
            );
          //echo $value->nombre.'<br>';
      }*/
      /*$s_compra = DB::table('s_compradetalle')
            ->join('s_compra','s_compra.id','s_compradetalle.s_idcompra')
            ->where('s_compra.idtienda','<>',115)
            ->select(
                DB::raw('CONCAT("COMPRA") as tabla'),
                's_compradetalle.cantidad as cantidad',
                's_compradetalle.preciounitario as preciounitario',
                's_compradetalle.preciototal as preciototal',
                's_compradetalle.s_idproducto as idproducto',
                's_compradetalle.idunidadmedida as idunidadmedida',
                's_compradetalle.por as por',
                's_compra.idtienda as idtienda',
            )
            ->orderBy('s_compradetalle.id','asc');

      $s_venta = DB::table('s_ventadetalle')
            ->join('s_venta','s_venta.id','s_ventadetalle.s_idventa')
            ->where('s_venta.idtienda','<>',115)
            ->select(
                DB::raw('CONCAT("VENTA") as tabla'),
                's_ventadetalle.cantidad as cantidad',
                's_ventadetalle.preciounitario as preciounitario',
                's_ventadetalle.total as preciototal',
                's_ventadetalle.s_idproducto as idproducto',
                's_ventadetalle.idunidadmedida as idunidadmedida',
                's_ventadetalle.por as por',
                's_venta.idtienda as idtienda',
            )
            ->orderBy('s_ventadetalle.id','asc');

      $s_productomovimiento  = DB::table('s_productomovimiento')
            ->leftJoin('s_producto','s_producto.id','s_productomovimiento.s_idproducto')
            ->where('s_productomovimiento.idtienda','<>',115)
            ->select(
                DB::raw('IF(s_productomovimiento.s_idtipomovimiento=1,CONCAT("MOVIMIENTO INGRESO"),CONCAT("MOVIMIENTO SALIDA")) as tabla'),
                's_productomovimiento.cantidad as cantidad',
                's_productomovimiento.preciounitario as preciounitario',
                's_productomovimiento.total as preciototal',
                's_productomovimiento.s_idproducto as idproducto',
                's_productomovimiento.idunidadmedida as idunidadmedida',
                's_productomovimiento.por as por',
                's_productomovimiento.idtienda as idtienda',
            )
            ->orderBy('s_productomovimiento.id','asc')
            ->union($s_compra)
            ->union($s_venta)
            ->get();
      
        
        foreach($s_productomovimiento as $value){
                productosaldo_actualizar(
                    $value->idtienda,
                    $value->idproducto,
                    $value->tabla,
                    $value->idunidadmedida,
                    $value->por,
                    $value->cantidad,
                    $value->preciounitario,
                    $value->preciototal,
                );
        }*/

        
?>
@if($productosaldos->total()==0)
  @if(isset($_GET['idproducto']))
   <div class="mensaje-warning">
      <i class="fa fa-warning"></i> No hay ningun producto con ese filtro, ingrese nuevo filtro de reporte.
    </div>
  @endif
@else
<div class="table-responsive">
<table class="table" id="tabla-contenido" style="margin-bottom: 5px;">
    <thead class="thead-dark">
      <tr>
        <th>Fecha / Hora</th>
        <th>Código</th>
        <th>Producto</th>
        <th>Unidad de Medida</th>
        <th>Concepto</th>
        <th>Cantidad</th>
        <th>Stock</th>
      </tr>
    </thead>
    <tbody>
        @foreach($productosaldos as $value)
        <?php
        $class_dato = 'td_dato';
        $class_es = 'td_es';
        $class_saldo = 'td_saldo';
        if($value->concepto=='SALDO INICIAL'){
            $class_dato = 'td_reset_dato';
            $class_es = 'td_reset_es';
            $class_saldo = 'td_reset_saldo';
        }
        ?>
        <tr>
          <td class="{{$class_dato}}">{{ $value->concepto=='SALDO INICIAL'? '' : date_format(date_create($value->fecharegistro), 'd/m/Y h:i:s A') }}</td>
          <td class="{{$class_dato}}">{{ $value->concepto=='SALDO INICIAL'? '' : $value->producto_codigo }}</td>
          <td class="{{$class_dato}}">{{ $value->concepto=='SALDO INICIAL'? '' : $value->producto_nombre }}</td>
          <td class="{{$class_dato}}">{{ $value->concepto=='SALDO INICIAL'? '' : $value->unidadmedidanombre.' X '.$value->producto_por }}</td>
          <td class="{{$class_dato}}">{{ $value->concepto }}</td>
          <td class="{{$class_dato}}">{{ $value->cantidad }}</td>
          <td class="{{$class_saldo}}">{{ $value->saldo_cantidad }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
{{ $productosaldos->links('app.tablepagination', ['results' => $productosaldos]) }}
@endif
@endsection
@section('subscripts')
<style>
  .td_reset_dato{
    background-color: #838ea9;
    color: white;
    padding: 10px !important;
  }
  .td_reset_es{
    background-color: #838ea9;
    color: white;
    padding: 10px !important;
  }
  .td_reset_saldo{
    background-color: #838ea9;
    color: white;
    padding: 10px !important;
  }
  .td_dato{
    background-color: #bec2cc;
    padding: 10px !important;
  }
  .td_es{
    background-color: #99d1f7;
    padding: 10px !important;
  }
  .td_saldo {
    background-color: #9ee0a1;
    padding: 10px !important;
  }
</style>
<script>
      function reporte(tipo){
        window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportekardex')}}?'+
                                'tipo='+tipo+
                                '&idproducto='+($('#idproducto').val()!=null?$('#idproducto').val():'');
    }
$("#idproducto").select2({
    @include('app.select2_producto',[
        'idtienda'=>$tienda->id
    ])
});
</script>
@endsection