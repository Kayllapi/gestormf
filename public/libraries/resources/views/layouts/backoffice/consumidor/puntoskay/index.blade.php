@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Monedas KAY</span>
      <a class="btn btn-warning" href="{{ url('backoffice/consumidor/puntoskay/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>

<div class="table-responsive">
<table class="table" id="tabla-contenido">
                <thead class="thead-dark">
                  <tr>
                    <th>Fecha Registrado</th>
                    <th>Fecha Aprobado</th>
                    <th>Usuario</th>
                    <th>Correo Electrónico</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Total</th>
                    <th width="10px">Estado</th>
                    <th width="10px">Opción</th>
                  </tr>
                </thead>
                @include('app.tablesearch',[
                    'searchs'=>['fecharegistro','fechaconfirmacion','usuario','correo','','',''],
                    'search_url'=> url('backoffice/consumidor/puntoskay')
                ])
                <tbody>
                    @foreach($puntoskays as $value)
                    <tr>
                      <td>
                          {{ date_format(date_create($value->fecharegistro),"d/m/Y h:i:s A") }}
                      </td>
                      <td>
                          @if($value->idestado==3)
                          {{ $value->fechaconfirmacion!=''?date_format(date_create($value->fechaconfirmacion),"d/m/Y h:i:s A"):'---' }}
                          @elseif($value->idestado==4)
                          {{ $value->fechaanulado!=''?date_format(date_create($value->fechaanulado),"d/m/Y h:i:s A"):'---' }}
                          @else
                          ---
                          @endif
                      </td>
                      <td>{{ $value->usersapellidos }}, {{ $value->usersnombre }}</td>
                      <td>{{ $value->usersemail }}</td>
                      <td>{{ $value->cantidad }} Kays</td>
                      <td>{{ $value->precio }}</td>
                      <td>{{ $value->total }}</td>
                      <td>
                        @if($value->idestadopuntoskay==1)
                            @if($value->idestadosolicitud==1)
                                <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-refresh"></i> Solicitando</span></div>
                            @elseif($value->idestadosolicitud==2)
                                <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Rechazado</span></div>
                            @endif
                        @elseif($value->idestadopuntoskay==2)
                            <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Aprobado</span></div>
                        @endif
                      </td>
                      <td>
                        <div class="dropdown">
                          <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                          <div class="dropdown-content">
                            @if($value->idestadopuntoskay==1)
                            <a href="{{ url('backoffice/consumidor/puntoskay/'.$value->id.'/edit?view=aprobar') }}"><i class="fa fa-check"></i> Revisar</a>
                            <a href="{{ url('backoffice/consumidor/puntoskay/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a>
                            @elseif($value->idestadopuntoskay==2)
                            <a href="{{ url('backoffice/consumidor/puntoskay/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-th-list"></i> Detalle</a>
                            @endif
                          </div>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
</div>
{{ $puntoskays->links('app.tablepagination', ['results' => $puntoskays]) }}
@endsection