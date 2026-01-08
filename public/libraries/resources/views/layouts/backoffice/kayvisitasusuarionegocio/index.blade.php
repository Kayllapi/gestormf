@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>GENERAR VISITAS</span>
      <a class="btn btn-warning" href="{{ url('backoffice/kaygenerarvisitasusuario/create') }}"><i class="fa fa-angle-right"></i> Obtener Monedas KAY</a></a>
    </div>
</div>
<div class="table-responsive">
  <table class="table" id="tabla-contenido">
      <thead class="thead-dark">
        <tr>
          <th width="140px">Fecha registro</th>
          <th width="120px">IP Address</th>
          <th>Tienda - Producto</th>
          <th>Referencia</th>
          <th width="120px">Monedas KAY</th>
        </tr>
      </thead>
      <tbody>
        @foreach($kaygenerarvisitas as $value)
          <tr>
            <td>{{$value->fecharegistro}}</td>
            <td>{{$value->ipaddress}}</td>
            <td>
                  <?php 
                  $link = url($value->tiendalink);
                  $tiendaproducto = $value->tiendanombre; 
                  ?>
                  <a href="{{ $link }}" target="_blank" style="color: #1e7dbd;border-bottom: 1px dashed;line-height: 2.8;"><i class="fa fa-link"></i> {{ $tiendaproducto }}</a>
            </td>
            <td>
              @if($value->referencia=='')
                  NINGUNO
              @else
                  {{$value->referencia}}
              @endif
            </td>
            <td>{{ 1/$value->cantidad }}</td>
          </tr>   
        @endforeach
      </tbody>
  </table>
  {{ $kaygenerarvisitas->links('app.tablepagination', ['results' => $kaygenerarvisitas]) }}
</div>
@endsection