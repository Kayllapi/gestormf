@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Monedas Kay</span>
      <a class="btn btn-warning" href="{{ url('backoffice/perfil/0/edit?view=monedaskay_registrar') }}"><i class="fa fa-plus"></i> Comprar</a></a>
    </div>
</div>
@include('app.consumidor.puntoskay')
<div class="table-responsive">
      <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th>Fecha Registrado</th>
            <th>Fecha Aprobado</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Total</th>
            <th width="10px">Estado</th>
          </tr>
        </thead>
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
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $puntoskays->links('app.tablepagination', ['results' => $puntoskays]) }}
@endsection
@section('scriptsbackoffice')
<script>

</script>
@endsection