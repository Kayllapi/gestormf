@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle de Comunicación de Baja</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacioncomunicacionbaja') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
        <div class="row">
            <div class="col-sm-6">
                <label>Empresa</label>
                <input type="text" value="{{ $facturacioncomunicacionbaja->emisor_ruc }} - {{ $facturacioncomunicacionbaja->emisor_razonsocial }}" disabled>
                <label>Correlativo</label>
                <input type="text" value="{{ $facturacioncomunicacionbaja->comunicacionbaja_correlativo }}" disabled>
            </div>
            <div class="col-sm-6">
                <label>Fecha de Generación</label>
                <input type="text" value="{{ $facturacioncomunicacionbaja->comunicacionbaja_fechageneracion }}" disabled>
                <label>Fecha de Comunicación</label>
                <input type="text" value="{{ $facturacioncomunicacionbaja->comunicacionbaja_fechacomunicacion }}" disabled>
            </div>
        </div>
        <div class="table-responsive">
                <table class="table" id="tabla-contenido">
                    <thead class="thead-dark">
                        <tr>
                              <th>Fecha de Emisión</th>
                              <th>Comprobante</th>
                              <th>Serie</th>
                              <th>Correlativo</th>
                              <th>Cliente</th>
                              <th>Moneda</th>
                              <th>Sub Total</th>
                              <th>IGV</th>
                              <th>Total</th>
                              <th>Motivo</th>
                            <th width="10px"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($facturacioncomunicacionbajadetalle as $value)
                         <tr style="background-color: #a53b93;color: #fff;height: 40px;">
                            <td>{{ $value->venta_fechaemision }}</td>
                            <td>
                              @if($value->venta_tipodocumento=='03')
                                  BOLETA
                              @elseif($value->venta_tipodocumento=='01')
                                  FACTURA
                              @endif
                           </td>
                            <td>{{ $value->serie }}</td>
                            <td>{{ $value->correlativo }}</td>
                            <td>{{ $value->cliente_razonsocial }}</td>
                            <td>{{ $value->venta_tipomoneda }}</td>
                            <td>{{ $value->venta_subtotal }}</td>
                            <td>{{ $value->venta_igv }}</td>
                            <td>{{ $value->venta_montoimpuestoventa }}</td>
                            <td>{{ $value->descripcionmotivobaja }}</td>
                            <td></td>
                         </tr>
                     @endforeach
                     </tbody>
                </table>
        </div>
       <div class="row">
          <div class="col-md-4"></div>
          <div class="col-md-4"> 
              <label>Total</label>
                 <input class="form-control" type="text" id="totalventa" placeholder="0.00" disabled>
              </div>
          <div class="col-md-4"></div>
       </div>        
@endsection
@section('subscripts')
@endsection