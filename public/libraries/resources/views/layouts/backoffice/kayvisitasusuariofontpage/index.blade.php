@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>VISITAS PARA FONTPAGE</span>
      <a class="btn btn-warning" href="{{ url('backoffice/kayvisitasusuariofontpage/create') }}"><i class="fa fa-angle-right"></i> Generar Visitas</a></a>
    </div>
</div>
<div class="table-responsive">
  <table class="table" id="tabla-contenido">
      <thead class="thead-dark">
        <tr>
          <th width="180px">Fecha registro</th>
          <th width="120px">IP Address</th>
          <th>FontPage (Tienda)</th>
          <th>Referencia</th>
          <th>Responsable</th>
          <th width="120px">Monedas KAY</th>
        </tr>
      </thead>
      <tbody>
        @foreach($kayvisitasusuariofontpage as $value)
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
            <td>
              @if($value->idusersenvio==1)
                  KAYLLAPI
              @else
                  CLIENTE
              @endif
            </td>
            <td>{{$value->puntoskay}} KAY</td>
          </tr>   
        @endforeach
      </tbody>
  </table>
  {{ $kayvisitasusuariofontpage->links('app.tablepagination', ['results' => $kayvisitasusuariofontpage]) }}
</div>
@endsection