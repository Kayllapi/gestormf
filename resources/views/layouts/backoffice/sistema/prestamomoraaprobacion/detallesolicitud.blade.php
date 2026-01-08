@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle de Solicitud de Descuento de Mora</span>
      <a class="btn btn-success" href="{{ redirect()->getUrlGenerator()->previous() }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
            <div class="col-sm-12">
              <div class="row">
                <div class="col-sm-6">
                  <label>Código de Crédito</label>
                  <input type="text" value="{{ str_pad($s_prestamo_mora->creditocodigo, 8, "0", STR_PAD_LEFT) }}" disabled>
                  <label>Código de Mora</label>
                  <input type="text" value="{{ str_pad($s_prestamo_mora->codigo, 8, "0", STR_PAD_LEFT) }}" disabled>
                </div>
                <div class="col-sm-6">
                  <label>Asesor</label>
                  <input type="text" value="{{ $s_prestamo_mora->asesorapellidos }}, {{ $s_prestamo_mora->asesornombre }}" disabled>
                  <label>Cliente</label>
                  <input type="text" value="{{ $s_prestamo_mora->clienteidentificacion }} - {{ $s_prestamo_mora->clienteapellidos }}, {{ $s_prestamo_mora->clientenombre }}" disabled>
                </div>
              </div>
            </div>
            <div class="col-sm-12">
                <div class="table-responsive">
                  <table class="table" id="tabla-contenido">
                      <thead class="thead-dark">
                        <tr>
                          <th>Fecha registro</th>
                          <th width="100px">Solicitado</th>
                          <th width="100px">Aprobado</th>
                          <th width="100px">Pendiente</th>
                          <th>Motivo</th>
                          <th width = '10px'>Estado</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        $pend = 0;
                        $total = 0;
                        $totaldescontar = 0;
                        $totaldescuento = 0;
                        ?>
                        @foreach ($s_prestamo_moradetalles as $value)
                          <tr>
                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ date_format(date_create($value->fecharegistro),"d/m/Y h:i A") }}</td>
                            <td>{{ $value->morapagar }}</td>
                            <td>{{ $value->moradescontar }}</td>
                            <td>{{ $value->moradescuento }}</td>
                            <td>{{ $value->motivo }}</td>
                            <td>
                              @if ($value->idestadomoradetalle == 1)
                                  <span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span>
                              @elseif ($value->idestadomoradetalle == 2)
                                  <span class="badge badge-pill badge-primary"><i class="fa fa-sync-alt"></i> Solicitando</span>
                              @elseif ($value->idestadomoradetalle == 3)
                                  <span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Aprobado</span>
                              @elseif ($value->idestadomoradetalle == 4)
                                  <span class="badge badge-pill badge-dark"><i class="fa fa-check"></i> Anulado</span>
                              @endif
                            </td>
                          </tr>
                        <?php 
                        $total = $total+$value->morapagar;
                        $totaldescontar = $totaldescontar+$value->moradescontar;
                        $totaldescuento = $totaldescuento+$value->moradescuento;
                        ?>
                        @endforeach
                      </tbody>
                  </table>
                </div>
            </div>
                <div class="col-sm-4">
                </div>
                <div class="col-sm-4">
                  <label>Total Solicitado</label>
                  <input type="number" id="totalapagar" value="{{ number_format($total, 2, '.', '') }}" disabled>
                  <label>Total Aprobado</label>
                  <input type="number" value="{{ number_format($totaldescontar, 2, '.', '') }}" disabled>
                  <label>Total Pendiente</label>
                  <input type="number" id="totaldescuento"  value="{{ number_format($totaldescuento, 2, '.', '') }}" disabled>
                </div>
@endsection