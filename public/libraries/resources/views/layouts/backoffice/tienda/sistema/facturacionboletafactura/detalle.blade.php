@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
        <span>Detalle Comprobante</span>
        <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionboletafactura') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
        <div class="row">
            <div class="col-md-6">
                        <label>Cliente</label>
                        <input type="text" id="cliente" value="{{ strtoupper($facturacionboletafactura->cliente) }}" disabled>
                        <label>Direccion</label>
                        <input type="text" value="{{ strtoupper($facturacionboletafactura->cliente_direccion) }}" disabled/>
                        <label>Ubigeo</label>
                        <input type="text" value="{{ strtoupper($facturacionboletafactura->ubigeo) }}" disabled/>  
                        <label>Agencia</label>
                        <input type="text" value="{{ strtoupper($facturacionboletafactura->agencia) }}" disabled/>  
            </div>
            <div class="col-md-6">
                        <label>Moneda</label>
                        @if($facturacionboletafactura->venta_tipomoneda=='PEN')
                            <input class="form-control" type="text" value="SOLES" disabled>
                        @elseif($facturacionboletafactura->venta_tipomoneda=='USD')
                            <input class="form-control" type="text" value="DOLARES" disabled>
                        @endif  
                        <label>Fecha de Emisión</label>
                        <input type="text" value="{{ $facturacionboletafactura->venta_fechaemision }}" disabled/>  
                        <label >Comprobante</label>
                        @if($facturacionboletafactura->venta_tipodocumento=='03')
                            <input class="form-control" type="text" value="BOLETA" disabled>
                        @elseif($facturacionboletafactura->venta_tipodocumento=='01')
                            <input class="form-control" type="text" value="FACTURA" disabled>
                        @elseif($facturacionboletafactura->venta_tipodocumento=='00')
                            <input class="form-control" type="text" value="TICKET" disabled>
                        @endif   
                        <div class="row">
                            <div class="col-md-6">
                              <label>Serie</label>
                              <input type="text" value="{{ $facturacionboletafactura->venta_serie }}" disabled/>  
                            </div>
                            <div class="col-md-6">
                              <label>Correlativo</label>
                              <input type="text" value="{{ $facturacionboletafactura->venta_correlativo }}" disabled/>  
                            </div>
                        </div> 
            </div>
        </div>
        <div class="table-responsive">
            <table class="table" id="tabla-contenido">
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
                    <?php $total = 0 ?>
                    @foreach($boletafacturadetalle as $value)
                        <?php $total = $total+number_format($value->cantidad*$value->montopreciounitario, 2, '.', '') ?>
                        <tr style="background-color: #a53b93;color: #fff;height: 40px;">
                            <td>{{$value->productocodigo}}</td>
                            <td>{{$value->productonombre}}</td>
                            <td>{{$value->cantidad}}</td>
                            <td>{{$value->montopreciounitario}}</td>
                            <td>{{number_format($value->cantidad*$value->montopreciounitario, 2, '.', '') }}</td>  
                        </tr>
                    @endforeach 
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4"> 
                <label>Sub Total</label>
                <input type="text" value="{{$facturacionboletafactura->venta_valorventa}}"  disabled>
              
                <label>IGV</label>
                <input type="text" value="{{$facturacionboletafactura->venta_totalimpuestos}}"  disabled>
              
                <label>Total</label>
                <input type="text" value="{{$facturacionboletafactura->venta_montoimpuestoventa}}"  disabled>
            </div>
            <div class="col-md-4"></div>
        </div>                   
@endsection
@section('subscripts')
@endsection