@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Anular Venta</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/ventadevolucion') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/ventadevolucion/{{$ventadevolucion->id}}',
        method: 'PUT',
        data:{
            view: 'anular'
        }
      },
      function(resultado){
           location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/ventadevolucion') }}';                                                            
      },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
             <div class="col-sm-3"> </div>
             <div class="col-sm-6">
                  <input type="text" value="{{str_pad($ventadevolucion->codigo, 8, "0", STR_PAD_LEFT)}}" style="text-align: center;"  disabled>
             </div>
          </div> 
          <div class="row">
             <input type="hidden" id="idventa" value="0" disabled>
             <div class="col-sm-6">
               <label>Cliente</label>
                  <input type="text" value="{{$ventadevolucion->apellidoscliente}},{{$ventadevolucion->nombrecliente}}" disabled>
               <label>Tipo de entrega</label>
                  <input type="text" value="{{$ventadevolucion->entreganombre}}" disabled>
             </div>
             <div class="col-sm-6">
               <label>Fecha de Registro</label>
                   <input type="text" value="{{$ventadevolucion->fecharegistro}}"  disabled>
               <label>Motivo de Devolución</label> 
                   <input type="text" value="{{$ventadevolucion->motivo}}" disabled>
             </div>
          </div>  
        </div>
    </div>
    <div class="custom-form">
      <div class="table-responsive">
         <table class="table" id="tabla-contenido-ventadevolucion">
             <thead class="thead-dark">
                 <tr>
                     <th width="60px">Código</th>
                     <th>Descripción de Producto</th>
                     <th width="60px">Cantidad</th>
                     <th width="110px">P. Unitario</th>
                     <th width="110px">P. Total</th>
                 </tr>
             </thead>
             <tbody>
                 @foreach($ventadevoluciondetalles as $value)
                 <tr style="background-color: #008cea;color: #fff;height: 40px;">
                     <td>{{ str_pad($value->codigoproducto, 8, "0", STR_PAD_LEFT) }}</td>
                     <td>{{$value->concepto}}</td>
                     <td>{{$value->cantidad}}</td>
                     <td>{{$value->preciounitario}}</td>
                     <td>{{$value->total}}</td>       
                 </tr>
                 @endforeach 
            </tbody>
       </table>
    </div>
    <?php $total = 0  ?>
    <?php
          $subtotal = $total+number_format($ventadevolucion->total/1.18, 2, '.', '');
          $igv      = $ventadevolucion->total-$subtotal
    ?>
      <div class="row">
        <div class="col-md-4"></div> 
        <div class="col-md-4">
            <label>Sub Total:</label>
             <input type="number"  style="text-align: center;font-size: 16px;"  value="{{$subtotal}}"step="0.01" disabled>
           <label>IGV:</label>
              <input type="number"  style="text-align: center;font-size: 16px;" value="{{$igv}}" step="0.01" disabled>
            <label style="font-weight: bold">Total:</label>
              <input type="number"  style="text-align: center;font-size: 16px;" value="{{$ventadevolucion->total}}"  disabled>
        </div>    
      </div> 
    </div>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¡Esta seguro de Anular la Venta de Devolución!
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;">Anular</button>
        </div>
    </div> 
</form>  
@endsection