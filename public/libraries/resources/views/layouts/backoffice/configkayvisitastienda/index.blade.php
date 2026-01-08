@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>CONFIGURACIONES KAY</span>
    </div>
</div>
<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th>Titulo</th>
            <th>Cantidad</th>
            <th>Monedas KAY</th>
            <th>Estado</th>
            <th width="10px"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($configkayvisitastiendas as $value)
            <tr>
              <td>{{$value->titulo}}</td>
              <td>{{floatval($value->cantidad)}} visitas</td>
              <td>{{floatval($value->puntoskay)}} Monedas KAY</td>
              <td>
                @if($value->idestado==1)
                Activado
                @else
                Desactivado
                @endif
              </td>
              <td>
                <div class="dropdown">
                  <a href="javascript:;" id="btneliminar1" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <div class="dropdown-content">
                    <a href="{{ url('backoffice/configkayvisitastienda/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a>
                  </div>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
    </table>
    {{ $configkayvisitastiendas->links('app.tablepagination', ['results' => $configkayvisitastiendas]) }}
</div>
@endsection