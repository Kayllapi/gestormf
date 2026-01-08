@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Eliminar Transferencia de Productos</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
      <form action="javascript:;" 
              id="formproductotransferencia"
              onsubmit="callback({
                    route: 'backoffice/tienda/sistema/{{ $tienda->id }}/productotransferencia/{{$productotransferencia->id}}',
                    method: 'DELETE',
                    carga: '#carga-formproductotransferencia',
                    idform: 'formproductotransferencia',
                    data: {
                        view: 'eliminar'

                    }
                },
                function(resultado){
                     location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia') }}';                                                                            
                },this)"> 
      <div class="profile-edit-container">
        <div class="custom-form">
           <div class="row">
            <div class="col-sm-6">
               <label>Estado</label>
                  <select class="form-control" id="idestadotransferencia" disabled>
                      <option value="1">Solicitar Productos</option>
                      <option value="2">Enviar Productos</option>
                      <option value="3">Recepcionar Productos</option>
                  </select>
                <label >Motivo</label>
                  <input type="text" class="form-control" value="{{$productotransferencia->motivo}}" id="motivo"  disabled>
            </div>
            <div class="col-sm-6">
               <label >De</label>
                  <select class="form-control" id="idtiendaorigen" disabled>
                      <option></option>
                    @foreach($tiendas as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                  </select>
                <label >Para</label>
                  <select class="form-control" id="idtiendadestino" disabled>
                      <option></option>
                    @foreach($tiendas as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                  </select>
              </div>
         </div>
      </div>
     </div>
   </form>
        
        <div class="table-responsive">
            <table class="table" id="tabla-productotransferencia" style="margin-bottom: 5px;">
                <thead class="thead-dark">
                  <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>U. Medida</th>
                    <th width="80px">Cantidad</th>
                    <th width="200px">Motivo (Opcional)</th>
                  </tr>
                </thead>
                <tbody num="{{count($detalletransferencia)}}">
                    <?php $i=0 ?>
                    @foreach($detalletransferencia as $value)
                    <tr id="{{$i}}" idproducto="{{$value->idproducto}}">
                      <td>{{str_pad($value->producodigoimpresion, 6, "0", STR_PAD_LEFT)}}</td>
                      <td>{{$value->productonombre}}</td>
                      <td>{{$value->unidadmedidanombre}}</td>
                      <td>{{$value->cantidad}}</td>
                      <td>{{$value->motivo}}</td> 
                    </tr>
                    <?php $i++ ?>
                    @endforeach
                </tbody>
            </table>
        </div>
<div class="alert alert-warning">
						<i class="fa fa-info-circle"></i> ¿Esta seguro de eliminar?
				</div>
    <div class="modal-footer">
        <a href="javascript:;" class="btn btn-success" onclick="$('#formproductotransferencia').submit();">
         Eliminar
        </a>
    </div>
  

@endsection
@section('subscripts')
<script>
$('#idestado').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}).val({{$productotransferencia->idestado}}).trigger('change'); 
  
$('#idtiendaorigen').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}).val({{$productotransferencia->idtiendaorigen}}).trigger('change');  
  
$('#idtiendadestino').select2({
    placeholder: '--Seleccionar--',
    minimumResultsForSearch: -1
}).val({{$productotransferencia->idtiendadestino}}).trigger('change'); 
</script>
@endsection