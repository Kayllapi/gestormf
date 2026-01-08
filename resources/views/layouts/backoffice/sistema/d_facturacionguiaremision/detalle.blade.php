@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
    <style>
        .title-container{
            font-weight: bold !important;
            font-size: 16px;
            padding-bottom: 10px;
        }
    </style>
    <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
            <span>Detalle Guia de Remisión</span>
            <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionguiaremision') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="col-sm-12">
                            <label class="title-container">GENERAL</label>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>Remitente *</label>
                                <input type="text" id="agencia" value="{{ strtoupper($facturacionguiaremision->agencia) }}"  disabled>
                            </div>
                            <div class="col-sm-6">
                                <label>Destinatario *</label>
                                <input type="text" value="{{ $facturacionguiaremision->destinatario }}"  disabled> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>Punto de Partida *</label>
                                <input type="text" value="{{ $ubigeo_partida->nombre }}"  disabled>
                            </div>
                            <div class="col-sm-6">
                                <label>Punto de Llegada *</label>
                                <input  type="text" value="{{ $ubigeo_llegada->nombre }}"  disabled/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>Dirección de Partida *</label>
                                <input  type="text" value="{{ $facturacionguiaremision->envio_direccionpartida }}"  disabled/>
                            </div>
                            <div class="col-sm-6">
                                <label>Dirección de Llegada *</label>
                                <input  type="text" value="{{ $facturacionguiaremision->envio_direccionllegada }}" disabled/>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-sm-12">
                            <label class="title-container">DETALLE DE TRASLADO</label>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>Motivo *</label>
                                <input type="text" value="{{ $facturacionguiaremision->envio_descripciontraslado }}" disabled>
                            </div>
                            <div class="col-sm-4">
                                <label>Fecha de Emisión*</label>
                                <input type="text" value="{{ date_format(date_create($facturacionguiaremision->despacho_fechaemision), 'Y-m-d') }}"  disabled>
                            </div>
                            <div class="col-sm-4">
                                <label>Fecha de Translado *</label>
                                <input type="text"value="{{ date_format(date_create($facturacionguiaremision->envio_fechatraslado), 'Y-m-d') }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label>Nombre del Transportista *</label>
                                <input type="text" value="{{ $transportista->transportista }}" disabled> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label>Observación *</label>
                                <input type="text" value="{{ $facturacionguiaremision->despacho_observacion }}" disabled> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          
            <!-- Seccion para mostrar los productos -->
            <div class="table-responsive">
                <table class="table" id="tabla-contenido">
                    <thead class="thead-dark">
                        <tr>
                            <th width="15%">Código</th>
                            <th>Producto</th>
                            <th width="60px">Cantidad</th>
                            <th width="110px">P. Unitario</th>
                            <th width="110px">P. Total</th> 
                            <th width="10px"></th>
                        </tr>
                    </thead>
                    <tbody num="0">
                        <?php $total = 0; ?>
                        @foreach($facturacionguiaremisiondetalles as $value)
                            <tr style="background-color: #a53b93;color: #fff;height: 40px;">
                                <td>{{ $value->codigo }}</td>
                                <td>{{ $value->descripcion }}</td>
                                <td>{{ $value->cantidad }}</td>
                                <td>{{ number_format($value->unidad, 2, ".", ",") }}</td>
                                <td>{{ number_format($value->cantidad * $value->unidad, 2, ".", ",") }}</td>
                                <td></td>
                            </tr>
                            <?php $total += $value->cantidad * $value->unidad; ?>
                        @endforeach
                    </tbody>
                </table>
            </div>
          
            <!-- Seccion mostrando el total, subtotal, igv -->
            <div class="row">
                <div class="col-md-4">
                </div>
                <div class="col-md-4"> 
                    <label>Sub Total</label>
                    <input type="text" id="subtotal" value="{{ number_format($total / 1.18, 2, '.', ',') }}" placeholder="0.00" disabled>

                    <label>IGV(18%)</label>
                    <input type="text" id="igv" value="{{ number_format($total - ($total / 1.18), 2, '.', ',') }}" placeholder="0.00" disabled>

                    <label>Total</label>
                    <input type="text" id="total" value="{{ number_format($total, 2, '.', ',') }}" placeholder="0.00" disabled>
                </div>
                <div class="col-md-4">
                </div>
            </div>
        </div>
    </div>
@endsection
@section('subscripts')
@endsection