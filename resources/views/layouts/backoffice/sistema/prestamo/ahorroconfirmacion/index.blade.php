@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Confirmaciones'
])
<div class="table-responsive">
          <table class="table" id="tabla-contenido">
              <thead class="thead-dark">
                <tr>
                  <th>Código de Ahorro</th>
                  <th>Tipo de Ahorro</th>
                  <th>DNI/RUC</th>
                  <th>Cliente</th>
                  <th>Fecha de Confirmación</th>
                  <th>Estado</th>
                  <th width="10px"></th>
                </tr>
              </thead>
              @include('app.tablesearch',[
                  'searchs'=>[
                      'codigoahorro',
                      'select:acceso/1=NORMAL,2=REFINANCIADO,3=REPROGRAMADO,4=AMPLIADO',
                      'identificacion',
                      'cliente'
                  ],
                  'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorroconfirmacion')
              ])
              <tbody>
                @foreach($prestamoahorros as $value)
                  <tr>
                    <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                    <td>{{$value->tipoahorronombre}} {{$value->ahorrolibre_tiponombre!=''?'('.$value->ahorrolibre_tiponombre.')':''}}</td>
                    <td>{{$value->clienteidentificacion}}</td>
                    <td>{{$value->cliente}}</td>
                    <td>{{$value->fechaconfirmado!=''?date_format(date_create($value->fechaconfirmado), "d/m/Y h:i:s A"):'---'}}</td>
                    <td>
                        @if($value->idestadoahorro==3)
                            @if($value->idestadoconfirmacion==2)
                                <span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Confirmación Anulado</span>
                            @else
                                <span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Aprobado</span>
                            @endif
                        @elseif($value->idestadoahorro==4)
                            @if($value->idestadoconfirmacion==3)
                                <span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Confirmación Anulado</span>
                            @else
                                <span class="badge badge-pill badge-primary"><i class="fa fa-check"></i> Confirmado</span>
                            @endif
                        @elseif($value->idestadoahorro==5)
                            <span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span>
                        @endif
                    </td>
                    <td>
                      <div class="header-user-menu menu-option" id="menu-opcion">
                          <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                          <ul>
                          @if($value->idestadoahorro==3)
                            @if($value->idestadoconfirmacion==2)
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorroconfirmacion/{{$value->id}}/edit?view=detalleconfirmacion"><i class="fa fa-list"></i> Detalle de Confirmación</a></li>
                            @else
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorroconfirmacion/{{$value->id}}/edit?view=confirmar"><i class="fa fa-check"></i> Confirmar</a></li>
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorroconfirmacion/{{$value->id}}/edit?view=anularaprobacion"><i class="fa fa-ban"></i> Anular Confirmación</a></li>
                            @endif
                          @elseif($value->idestadoahorro==4)
                            @if($value->idestadoconfirmacion==3)
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorroconfirmacion/{{$value->id}}/edit?view=detalleconfirmacion"><i class="fa fa-list"></i> Detalle de Confirmación</a></li>
                            @else
                              <?php
                              $prestamo_recaudacion = DB::table('s_prestamo_ahorrorecaudacion')
                                  ->where('s_prestamo_ahorrorecaudacion.idprestamo_ahorro', $value->id)
                                  ->where('s_prestamo_ahorrorecaudacion.idestadorecaudacion', 2)
                                  ->limit(1)
                                  ->first();
                              ?>
                              @if($prestamo_recaudacion=='')
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorroconfirmacion/{{$value->id}}/edit?view=anularconfirmacion"><i class="fa fa-ban"></i> Anular Confirmación</a></li>
                              @endif
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorroconfirmacion/{{$value->id}}/edit?view=ticket"><i class="fa fa-ticket-alt"></i> Ticket</a></li>
                              @if($value->idprestamo_tipoahorro==1 or $value->idprestamo_tipoahorro==2)
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorroconfirmacion/{{$value->id}}/edit?view=cronograma"><i class="fa fa-calendar-check"></i> Cronograma</a></li>
                              @if($value->idprestamo_tipoahorro==2)
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorroconfirmacion/{{$value->id}}/edit?view=tarjeta"><i class="fa fa-credit-card"></i> Tarjeta de Recaudación</a></li>
                              @endif
                              @endif
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorroconfirmacion/{{$value->id}}/edit?view=documento"><i class="fa fa-folder-open"></i> Documentos</a></li>
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorroconfirmacion/{{$value->id}}/edit?view=detalleconfirmacion"><i class="fa fa-list"></i> Detalle</a></li>
                            @endif
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