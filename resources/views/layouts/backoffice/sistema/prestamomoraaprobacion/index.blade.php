@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Aprobaciones de Descuento de Moras</span>
  </div>
</div>
<div class="table-responsive">
  <table class="table" id="tabla-contenido">
      <thead class="thead-dark">
        <tr>
          <th>Cod. Mora</th>
          <th>Cod. Crédito</th>
          <th>DNI</th>
          <th>Cliente</th>
          <th>Solicitado</th>
          <th>Aprobado</th>
          <th>Pendiente</th>
          <th width = '10px'>Crédito</th>
          <th width = '10px'></th>
        </tr>
      </thead>
              @include('app.tablesearch',[
                  'searchs'=>[
                      'codigo',
                      'codigocredito',
                      'dni',
                      'cliente',
                  ],
                  'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomoraaprobacion')
              ])
      <tbody>
        @foreach ($moras as $value)
          <?php
        
          $totalsolicitado_pagar = DB::table('s_prestamo_moradetalle')
              ->where('s_prestamo_moradetalle.idtienda', $tienda->id)
              ->where('s_prestamo_moradetalle.idestado', 1)
              ->where('s_prestamo_moradetalle.idprestamo_mora', $value->id)
              ->sum('morapagar');
        
          $totalsolicitado_descontar = DB::table('s_prestamo_moradetalle')
              ->where('s_prestamo_moradetalle.idtienda', $tienda->id)
              ->where('s_prestamo_moradetalle.idestado', 1)
              ->where('s_prestamo_moradetalle.idprestamo_mora', $value->id)
              ->sum('moradescontar');
        
          $totalsolicitado_pendiente = DB::table('s_prestamo_moradetalle')
              ->where('s_prestamo_moradetalle.idtienda', $tienda->id)
              ->where('s_prestamo_moradetalle.idestado', 1)
              ->where('s_prestamo_moradetalle.idprestamo_mora', $value->id)
              ->sum('moradescuento');
        ?>
          <tr>
            <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
            <td>{{ str_pad($value->creditocodigo, 8, "0", STR_PAD_LEFT) }}</td>
            <td>{{ $value->identificacion_cliente }}</td>
            <td>{{ $value->apellidos_cliente }}, {{ $value->nombre_cliente }}</td>
            <td>{{ number_format($totalsolicitado_pagar, 2, '.', '') }}</td>
            <td>{{ number_format($totalsolicitado_descontar, 2, '.', '') }}</td>
            <td>{{ number_format($totalsolicitado_pendiente, 2, '.', '') }}</td>
            <td>
              @if ($value->idestadocobranza == 1)
                  <span class="badge badge-pill badge-primary"><i class="fa fa-sync-alt"></i> Pendiente</span>
              @elseif ($value->idestadocobranza == 2)
                  <span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Cancelado</span>
              @endif
            </td>
            <td>
              <div class="header-user-menu menu-option" id="menu-opcion">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <ul>
                  @if ($value->idestadocobranza == 1)
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomoraaprobacion/'.$value->id.'/edit?view=aprobar') }}"><i class="fa fa-check"></i> Revisar</a></li>
                  @elseif ($value->idestadocobranza == 2)
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomoraaprobacion/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-list"></i> Detalle</a></li>
                  @endif
                  </ul>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
  </table>
</div>
{{ $moras->links('app.tablepagination', ['results' => $moras]) }}
@endsection