@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Confirmar Movimiento de Productos</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productomovimiento') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div id="carga-productomovimiento">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
             <div class="col-md-6">
                <label>Tipo de Movimiento</label>
                <select id="idtipomovimiento" disabled>
                    <option></option>
                    @foreach($tipomovimientos as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
             </div>
             <div class="col-md-6">
                <label>Motivo</label>
                <input type="text" id="motivo"  value="{{$productomovimiento->motivo}}" disabled/>
             </div>
           </div>
          <div class="table-responsive">
            <table class="table" id="tabla-contenido">
                <thead class="thead-dark">
                  <tr>
                    <th width="15%">Código</th>
                    <th>Producto</th>
                    @if($configuracion!='')
                    @if($configuracion->venta_estadostock==1)
                    <th width="50px">Stock</th>
                    @endif
                    @endif
                    <th width="60px">Cantidad</th>
                  </tr>
                </thead>
                <tbody>
                <?php $i=0; ?>
                @foreach($productomovimientodetalles as $value)
                    <?php 
                      $tdstock = '';
                      $stock = stock_producto($tienda->id,$value->s_idproducto)['total'];
                      $style = 'background-color: #0ec529;color: #fff;';
                      if($configuracion!=''){
                          if($configuracion->venta_estadostock==1){
                            if($stock<$value->cantidad){
                                $style = 'background-color: #ce0e00;color: #fff;';
                            }
                            $tdstock = '<td style="text-align: center"> '.$stock.' </td>';
                          }
                      }
                    ?>
                    <tr style="<?php echo $style ?>">
                    <td style="height: 43px;">{{$value->productocodigo}}</td>
                    <td>{{$value->productonombre}}</td><?php echo $tdstock ?>
                    <td style="text-align: center;">{{$value->cantidad}}</td>      
                    </tr>
                <?php $i++; ?>
                @endforeach
                </tbody>

            </table>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="mensaje-warning">
              <i class="fa fa-warning"></i> Esta seguro de Confirmar</b>!.
              </div>
            </div>
           </div>
          <a href="javascript:;" onclick="confirmar_productomovimiento()" class="btn  big-btn  color-bg flat-btn">Confirmar</a>
        </div>
    </div>
</div>
@endsection
@section('subscripts')
<script>
$("#idtipomovimiento").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$productomovimiento->s_idtipomovimiento}}).trigger("change");
// confirmar venta
function confirmar_productomovimiento(){
    callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/productomovimiento/{{ $productomovimiento->id }}',
        method: 'PUT',
        carga: '#carga-productomovimiento',
        data:{
            view: 'confirmar',
        }
    },
    function(resultado){
         location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productomovimiento') }}';                                                  
    })
}

</script>
@endsection