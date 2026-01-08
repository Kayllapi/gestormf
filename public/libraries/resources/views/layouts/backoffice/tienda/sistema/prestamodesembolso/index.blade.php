@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Desembolsos'
])
<div class="table-responsive">
          <table class="table" id="tabla-contenido">
              <thead class="thead-dark">
                <tr>
                  <th>Tipo de Crédito</th>
                  <th>Código de Crédito</th>
                  <th>DNI/RUC</th>
                  <th>Cliente</th>
                  <th>Frecuencia</th>
                  <th>Desembolso</th>
                  <th>Nro de Cuotas</th>
                  <th>Fecha de Desembolso</th>
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
                      'select:frecuencia/1=DIARIO,2=SEMANAL,3=QUINCENAL,4=MENSUAL,5=REFINANCIADO'
                  ],
                  'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/prestamodesembolso')
              ])
              <tbody>
                @foreach($prestamocreditos as $value)
                  <tr>
                    <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                    <td>{{$value->tipocreditonombre}}</td>
                    <td>{{$value->clienteidentificacion}}</td>
                    <td>{{$value->cliente}}</td>
                    <td>{{$value->frecuencianombre}}</td>
                    <td>{{$value->monedasimbolo.' '.$value->monto}}</td>
                    <td>{{$value->numerocuota.' Cuotas'}}</td>
                    <td>{{$value->fechadesembolsado!=''?date_format(date_create($value->fechadesembolsado), "d/m/Y h:i:s A"):'---'}}</td>
                    <td>
                        @if($value->idestadocredito==3)
                            @if($value->idestadodesembolso==2)
                                <span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Aprobación Anulado</span>
                            @else
                                <span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Aprobado</span>
                            @endif
                        @elseif($value->idestadocredito==4)
                            @if($value->idestadodesembolso==3)
                                <span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Desembolso Anulado</span>
                            @else
                                <span class="badge badge-pill badge-primary"><i class="fa fa-check"></i> Desembolsado</span>
                            @endif
                        @elseif($value->idestadocredito==5)
                            <span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span>
                        @endif
                    </td>
                    <td>
                      <div class="header-user-menu menu-option" id="menu-opcion">
                          <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                          <ul>
                          @if($value->idestadocredito==3)
                            @if($value->idestadodesembolso==2)
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamodesembolso/{{$value->id}}/edit?view=detalleaprobacion"><i class="fa fa-list"></i> Detalle de Aprobación</a></li>
                            @else
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamodesembolso/{{$value->id}}/edit?view=desembolsar"><i class="fa fa-check"></i> Desembolsar</a></li>
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamodesembolso/{{$value->id}}/edit?view=anularaprobacion"><i class="fa fa-ban"></i> Anular Aprobación</a></li>
                            @endif
                          @elseif($value->idestadocredito==4)
                            @if($value->idestadodesembolso==3)
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamodesembolso/{{$value->id}}/edit?view=detalledesembolso"><i class="fa fa-list"></i> Detalle de Desembolso</a></li>
                            @else
                              <?php
                              $prestamo_cobranza = DB::table('s_prestamo_cobranza')
                                  ->where('s_prestamo_cobranza.idprestamo_credito', $value->id)
                                  ->where('s_prestamo_cobranza.idestadocobranza', 2)
                                  ->limit(1)
                                  ->first();
                              ?>
                              @if($prestamo_cobranza=='')
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamodesembolso/{{$value->id}}/edit?view=anulardesembolso"><i class="fa fa-ban"></i> Anular Desembolso</a></li>
                              @endif
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamodesembolso/{{$value->id}}/edit?view=ticket"><i class="fa fa-ticket-alt"></i> Ticket</a></li>
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamodesembolso/{{$value->id}}/edit?view=cronograma"><i class="fa fa-calendar-check"></i> Cronograma</a></li>
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamodesembolso/{{$value->id}}/edit?view=tarjeta"><i class="fa fa-credit-card"></i> Tarjeta de Pago</a></li>
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamodesembolso/{{$value->id}}/edit?view=documento"><i class="fa fa-folder-open"></i> Documentos</a></li>
                              <li><a href="{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamodesembolso/{{$value->id}}/edit?view=detalledesembolso"><i class="fa fa-list"></i> Detalle</a></li>
                            @endif
                          @endif
                          </ul>
                      </div>
                    </td>
                  </tr>
                @endforeach 
              </tbody>
          </table>
          {{ $prestamocreditos->links('app.tablepagination', ['results' => $prestamocreditos]) }}
</div>
@endsection