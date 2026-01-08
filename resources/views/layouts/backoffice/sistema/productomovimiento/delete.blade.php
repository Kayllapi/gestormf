@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Eliminar Movimiento de Productos</span>
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
                    <th width="60px">Cantidad</th>
                    <th width="10px"></th>
                  </tr>
                </thead>
                <tbody>
                @foreach($productomovimientodetalles as $value)
                    <tr>
                    <td style="height: 43px;">{{$value->productocodigo}}</td>
                    <td>{{$value->productonombre}}</td>
                    <td style="text-align: center;">{{$value->cantidad}}</td>      
                    </tr>
                @endforeach
                </tbody>

            </table>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="mensaje-warning">
              <i class="fa fa-warning"></i> Esta seguro de Eliminar</b>!!.
              </div>
            </div>
           </div>
          <a href="javascript:;" onclick="eliminar_productomovimiento()" class="btn  big-btn  color-bg flat-btn">Eliminar</a>
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
// eliminar venta
function eliminar_productomovimiento(){
    callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/productomovimiento/{{ $productomovimiento->id }}',
        method: 'DELETE',
        carga: '#carga-productomovimiento',
        data:{
            view: 'eliminar',
        }
    },
    function(resultado){
         location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productomovimiento') }}';                                                  
    })
}

</script>
@endsection