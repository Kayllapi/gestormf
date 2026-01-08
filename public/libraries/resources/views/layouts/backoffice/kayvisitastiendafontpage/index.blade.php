@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>VISITAS PARA FONTPAGE</span>
      <a class="btn btn-warning" href="{{ url('backoffice/kayvisitastiendafontpage/create') }}"><i class="fa fa-angle-right"></i> Invertir</a></a>
    </div>
</div>
@include('app.puntoskay')
<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th>Tienda a Promocionar</th>
            <th>Inversión</th>
            <th>Alcance</th>
            <th>Resultado</th>
            <th width="150px">Publicación</th>
            <th width="10px"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($kayvisitastiendafontpage as $value)
            <tr>
              <td>{{$value->tiendanombre}}</td>
              <td>{{floatval($value->totalpuntoskay)}} Monedas KAY</td>
              <td>{{floatval($value->totalpuntoskay)*floatval($value->cantidad)}} visitas</td>
              <td>
                <?php
                  $kayvisitasusuariofontpage =  DB::table('kayvisitasusuariofontpage')
                                       ->where('idtienda',$value->idtienda)
                                       ->where('idestado',1)
                                       ->count();
                ?>
                {{$kayvisitasusuariofontpage}} visitas</td>
              <td>{{$value->fechaconfirmacion}}</td>
              <td>
                @if($value->fechaconfirmacion!='')
                <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Correcto</span></div>
                @else
                <div class="dropdown">
                  <a href="javascript:;" id="btneliminar1" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <div class="dropdown-content">
                    <a href="{{ url('backoffice/kayvisitastiendafontpage/'.$value->id.'/edit?view=confirmar') }}"><i class="fas fa-check"></i> Confirmar</a>
                    <a href="{{ url('backoffice/kayvisitastiendafontpage/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a>
                    <a href="{{ url('backoffice/kayvisitastiendafontpage/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a>
                  </div>
                </div>
               @endif 
              </td>
            </tr>
          @endforeach
        </tbody>
    </table>
    {{ $kayvisitastiendafontpage->links('app.tablepagination', ['results' => $kayvisitastiendafontpage]) }}
</div>
@endsection