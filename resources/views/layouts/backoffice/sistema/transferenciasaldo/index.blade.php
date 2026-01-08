@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
        <span>Transferencia de Saldos</span>
        <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/transferenciasaldo/create') }}">
            <i class="fa fa-angle-right"></i> Registrar</a>
        </a>
    </div>
</div>
<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
            <tr>
                <th>Código</th>
                <th>Caja Origen</th>
                <th>Caja Destino</th>
                <th>Monto</th>
                <th>Motivo</th>
                <th>Fecha Solicitud</th>
                <th>Fecha Recepción</th>
                <th>Estado</th>
                <th width="10px"></th>
            </tr>
        </thead>

        <tbody>
            @foreach($transferenciasaldos as $value)
                <tr>
                    <td>{{ str_pad($value->codigo, 6, "0", STR_PAD_LEFT) }}</td>
                    <td>{{ $value->cajaorigen_nombre }} {{ $value->idresponsableorigen != 0 ? '('.$value->responsableorigen_nombre.')' : '' }}</td>
                    <td>{{ $value->cajadestino_nombre }} {{ $value->idresponsabledestino != 0 ? '('.$value->responsabledestino_nombre.')' : '' }}</td>
                    <td>{{ $value->monto }}</td>
                    <td>{{ $value->motivo }}</td>
                    <td>{{ date_format(date_create($value->fechasolicitud), 'd/m/Y - h:i A' ) }}</td>
                    <td>{{ $value->fecharecepcion != null ? date_format(date_create($value->fecharecepcion), 'd/m/Y - h:i A') : '---' }}</td>
                    <td>
                        @if ($value->idestado == 1)
                            <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span></div>
                        @elseif ($value->idestado == 2)
                            <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
                        @endif
                    </td>
                    <td>
                        <div class="header-user-menu menu-option" id="menu-opcion">
                            <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                            <ul>
                                @if ($value->idestado == 1)
                                    <li>
                                        <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/transferenciasaldo/'.$value->id.'/edit?view=confirmar') }}">
                                            <i class="fa fa-check"></i> Confirmar
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/transferenciasaldo/'.$value->id.'/edit?view=editar') }}">
                                            <i class="fa fa-edit"></i> Editar
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/transferenciasaldo/'.$value->id.'/edit?view=anular') }}">
                                            <i class="fa fa-ban"></i> Anular
                                        </a>
                                    </li>
                                @elseif ($value->idestado == 2)
                                    <li>
                                        <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/transferenciasaldo/'.$value->id.'/edit?view=detalle') }}">
                                            <i class="fa fa-list-alt"></i> Detalle
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $transferenciasaldos->links('app.tablepagination', ['results' => $transferenciasaldos]) }}
</div>
@endsection
