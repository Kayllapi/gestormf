@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Solicitudes de Crédito</span>
    <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/create') }}"><i class="fa fa-angle-right"></i> Registrar</a>
  </div>
</div>
<div class="table-responsive">
          <table class="table" id="tabla-contenido">
              <thead class="thead-dark">
                <tr>
                  <th>Código</th>
                  <th>Tipo</th>
                  <th>DNI/RUC</th>
                  <th>Cliente</th>
                  <th>Desembolso</th>
                  <th>Frecuencia</th>
                  <th>Nro de Cuotas</th>
                  <th>Fecha de Registro</th>
                  <th>Estado</th>
                  <th width="10px"></th>
                </tr>
              </thead>
              @include('app.tablesearch',[
                  'searchs'=>[
                      'codigocredito',
                      'select:acceso/1=NORMAL,2=REFINANCIADO,3=REPROGRAMADO,4=AMPLIADO',
                      'identificacion',
                      'cliente',
                      '',
                      'select:frecuencia/1=DIARIO,2=SEMANAL,3=QUINCENAL,4=MENSUAL,5=REFINANCIADO'
                  ],
                  'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud')
              ])
              <tbody>
                @foreach($prestamocreditos as $value)
                  <tr>
                    <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                    <td>
                      @if($value->idprestamo_estadocredito==1)
                      {{$value->idprestamo_tipocredito==5?$value->tipocredito:$value->tipocreditonombre}}
                      @elseif($value->idprestamo_estadocredito==2)
                      GRUPAL
                      @endif
                    </td>
                    <td>{{$value->clienteidentificacion}}</td>
                    <td>{{$value->cliente}}</td>
                    <td>{{$value->monedasimbolo.' '.$value->monto}}</td>
                    <td>{{$value->frecuencianombre}}</td>
                    <td>{{$value->numerocuota>0?$value->numerocuota.' Cuotas':''}}</td>
                    <td>{{date_format(date_create($value->fecharegistro), "d/m/Y h:i:s A")}}</td>
                    <td>
                        @if($value->idestadocredito==1)
                            @if($value->idestadoaprobacion==2)
                                <span class="badge badge-pill badge-danger"><i class="fa fa-ban"></i> Rechazado</span>
                            @else
                                <span class="badge badge-pill badge-info"><i class="fa fa-sync"></i> Solicitud</span>
                            @endif
                        @elseif($value->idestadocredito==2)
                            @if($value->idestadoaprobacion==3)
                                <span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Denegado</span>
                            @else
                                <span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Preaprobado</span>
                            @endif
                        @elseif($value->idestadocredito==3)
                            @if($value->idestadodesembolso==2)
                                <span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Aprobación Anulada</span>
                            @else
                                <span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Aprobado</span>
                            @endif
                        @elseif($value->idestadocredito==4)
                            @if($value->idestadodesembolso==3)
                                <span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Desembolso Anulado</span>
                            @else
                                <span class="badge badge-pill badge-primary"><i class="fa fa-check"></i> Desembolsado</span>
                            @endif
                        @endif
                    </td>
                    <td>
                      <div class="header-user-menu menu-option" id="menu-opcion">
                          <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                          <ul>
                          @if($value->idestadocredito==1)
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{$value->id}}/edit?view=editar"><i class="fa fa-edit"></i> Editar</a></li>
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{$value->id}}/edit?view=preaprobar"><i class="fa fa-check"></i> Pre Aprobar</a></li>
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{$value->id}}/edit?view=eliminar"><i class="fa fa-trash"></i> Eliminar</a></li>
                          @elseif($value->idestadocredito==2)
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{$value->id}}/edit?view=detalle"><i class="fa fa-list"></i> Detalle</a></li>
                          @elseif($value->idestadocredito==3)
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{$value->id}}/edit?view=detalle"><i class="fa fa-list"></i> Detalle</a></li>
                          @elseif($value->idestadocredito==4)
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{$value->id}}/edit?view=detalle"><i class="fa fa-list"></i> Detalle</a></li>
                          @elseif($value->idestadocredito==5)
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamosolicitud/{{$value->id}}/edit?view=detalle"><i class="fa fa-list"></i> Detalle</a></li>
                          @endif
                          </ul>
                      </div>
                    </td>
                  </tr>
                @endforeach 
              </tbody>
          </table>
</div>
{{ $prestamocreditos->links('app.tablepagination', ['results' => $prestamocreditos]) }}
@endsection