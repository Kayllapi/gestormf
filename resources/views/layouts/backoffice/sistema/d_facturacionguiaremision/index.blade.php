@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
        <span>Guía de Remisión</span>
        <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionguiaremision/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
            <tr>
                <th>Serie</th>
                <th>Correlativo</th>
                <th>Fecha de Emisión</th>
                <th>RUC/DNI</th>
                <th>Remitente</th>
              
                <th>RUC</th>
                <th>Destinatario</th>
              
                <th>Motivo</th>
                <th>Traslado</th>
                <th>RUC/DNI</th>
                <th>Transportista</th>
                <th>Responsable</th>
                <th rowspan="2" width="10px"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($facturacionguiaremision as $value)
                <tr>
                    <td>{{ $value->despacho_serie }}</td>
                    <td>{{ str_pad($value->despacho_correlativo, 8, "0", STR_PAD_LEFT) }}</td>
                    <td>{{ date_format(date_create($value->despacho_fechaemision),"d/m/Y h:i:s A") }}</td>
                  
                    <td>{{ $value->emisor_ruc }}</td>
                    <td>{{ $value->emisor_nombrecomercial }}</td>
                  
                    <td>{{ $value->despacho_destinatario_numerodocumento }}</td>
                    <td>{{ $value->despacho_destinatario_razonsocial }}</td>

                    <td>{{ $value->motivotrasladonombre }}</td>
                    <td>{{ date_format(date_create($value->envio_fechatraslado),"d/m/Y")}}</td>
                    <td>{{ $value->transporte_choferdocumento }}</td>
                    <td>{{ $value->transportista }}</td>
                  
                    <td>{{ $value->responsablenombre }}</td>
                    <td>
                        <div class="header-user-menu menu-option" id="menu-opcion">
                            <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                            <ul>
                                  <li>
                                    <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionguiaremision/'.$value->id.'/edit?view=ticket') }}">
                                          <i class="fa fa-receipt"></i> Comprobante
                                    </a>
                                  </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach 
        </tbody>
    </table>
   {{ $facturacionguiaremision->links('app.tablepagination', ['results' => $facturacionguiaremision]) }}
</div>
@endsection
