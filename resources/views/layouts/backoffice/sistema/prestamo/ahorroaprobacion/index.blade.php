@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Aprobaciones de Ahorro'
])
<div class="table-responsive">
          <table class="table" id="tabla-contenido">
              <thead class="thead-dark">
                <tr>
                  <th>Código</th>
                  <th>Tipo</th>
                  <th>DNI/RUC</th>
                  <th>Cliente</th>
                  <th>Fecha de Aprobado</th>
                  <th>Estado</th>
                  <th width="10px"></th>
                </tr>
              </thead>
              @include('app.tablesearch',[
                  'searchs'=>[
                      'codigocredito',
                      'select:acceso/1=NORMAL,2=REFINANCIADO,3=REPROGRAMADO,4=AMPLIADO',
                      'identificacion',
                      'cliente'
                  ],
                  'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrosolicitud')
              ])
              <tbody>
                @foreach($prestamoahorros as $value)
                  <tr>
                    <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                    <td>{{$value->tipocreditonombre}} {{$value->ahorrolibre_tiponombre!=''?'('.$value->ahorrolibre_tiponombre.')':''}}</td>
                    <td>{{$value->clienteidentificacion}}</td>
                    <td>{{$value->cliente}}</td>
                    <td>{{$value->fechaaprobado!=''?date_format(date_create($value->fechaaprobado), "d/m/Y h:i:s A"):'---'}}</td>
                    <td>
                        @if($value->idestadoahorro==2)
                            @if($value->idestadoaprobacion==3)
                                <span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Denegado</span>
                            @else
                                <span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Preaprobado</span>
                            @endif
                        @elseif($value->idestadoahorro==3)
                            @if($value->idestadoconfirmacion==2)
                                <span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Aprobación Anulado</span>
                            @else
                                <span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Aprobado</span>
                            @endif
                        @elseif($value->idestadoahorro==4)
                            @if($value->idestadoconfirmacion==3)
                                <span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Confirmación Anulado</span>
                            @else
                                <span class="badge badge-pill badge-primary"><i class="fa fa-check"></i> Confirmado</span>
                            @endif
                        @endif
                    </td>
                    <td>
                      <div class="header-user-menu menu-option" id="menu-opcion">
                          <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                          <ul>
                          @if($value->idestadoahorro==2)
                            @if($value->idestado==1)
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorroaprobacion/{{$value->id}}/edit?view=aprobar"><i class="fa fa-check"></i> Revisar Ahorro</a></li>
                            @elseif($value->idestado==3)
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorroaprobacion/{{$value->id}}/edit?view=detalle"><i class="fa fa-list"></i> Detalle</a></li>
                            @endif
                          @elseif($value->idestadoahorro==3)
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorroaprobacion/{{$value->id}}/edit?view=detalle"><i class="fa fa-list"></i> Detalle</a></li>
                          @elseif($value->idestadoahorro==4)
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorroaprobacion/{{$value->id}}/edit?view=detalle"><i class="fa fa-list"></i> Detalle</a></li>
                          @elseif($value->idestadoahorro==5)
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorroaprobacion/{{$value->id}}/edit?view=detalle"><i class="fa fa-list"></i> Detalle</a></li>
                          @endif
                          </ul>
                      </div>
                    </td>
                  </tr>
                @endforeach 
              </tbody>
          </table>
          {{ $prestamoahorros->links('app.tablepagination', ['results' => $prestamoahorros]) }}
</div>
@endsection