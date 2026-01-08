@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
        <span>Movimientos de Saldos</span>
        <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimientosaldo/create') }}">
            <i class="fa fa-angle-right"></i> Registrar</a>
        </a>
    </div>
</div>
<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
            <tr>
                <th>Código</th>
                <th>Caja</th>
                <th>Responsable</th>
                <th>Monto</th>
                <th>Motivo</th>
                <th>Fecha Registro</th>
                <th>Estado</th>
                <th width="10px"></th>
            </tr>
        </thead>

        <tbody>
            @foreach($movimientosaldos as $value)
                <tr>
                    <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                    <td>{{ $value->cajanombre }} </td>
                    <td>{{ $value->responsablenombre }}</td>
                    <td>{{ $value->monto }}</td>
                    <td>{{ $value->motivo }}</td>
                    <td>{{ date_format(date_create($value->fecharegistro), 'd/m/Y - h:i A' ) }}</td>
                    <td>
                        @if($value->idestadomovimientosaldo == 1)
                            <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span></div>
                        @elseif($value->idestadomovimientosaldo == 2)
                            <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
                        @endif
                    </td>
                    <td>
                        <div class="header-user-menu menu-option" id="menu-opcion">
                            <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                            <ul>
                                @if($value->idestadomovimientosaldo == 1)
                                    <li>
                                        <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimientosaldo/'.$value->id.'/edit?view=confirmar') }}">
                                            <i class="fa fa-check"></i> Confirmar
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimientosaldo/'.$value->id.'/edit?view=editar') }}">
                                            <i class="fa fa-edit"></i> Editar
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimientosaldo/'.$value->id.'/edit?view=eliminar') }}">
                                            <i class="fa fa-trash"></i> Eliminar
                                        </a>
                                    </li>
                                @elseif($value->idestadomovimientosaldo == 2)
                                    <li>
                                        <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/movimientosaldo/'.$value->id.'/edit?view=detalle') }}">
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
    {{ $movimientosaldos->links('app.tablepagination', ['results' => $movimientosaldos]) }}
</div>
@endsection
