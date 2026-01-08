@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>REGISTRADOS</span>
      <a class="btn btn-success" href="{{ url('backoffice/evento/'.$evento->id.'/edit?view=registrar') }}"><i class="fa fa-angle-right"></i>REGISTRAR</a></a>
      <a class="btn btn-warning" href="{{ url('backoffice/evento') }}"><i class="fa fa-angle-left"></i>ATRÁS</a></a>
    </div>
</div>

  <table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th>Nombres</th>
        <th>Correo</th>
        <th>Celular</th>
        <th>Fecha Registros</th>
        <th>Estado</th>
        <th width="10px"></th>
      </tr>
    </thead>
    @include('app.tablesearch',[
        'searchs'=>['name','email','phone'],
        'search_url'=> url('#')
    ])
    <tbody> 
      <?php $contar = 1;?>
      @foreach($registrados as $value)
        <tr>
          <td>{{$value->nombre}}</td>
          <td>{{$value->correo}}</td>
          <td>{{$value->telefono}}</td>
          <td>{{$value->fecharegistro}}</td>
          <td>
            @if($value->idestado==1) 
            <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Pendiente</span></div> 
            @else
            <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div> 
            @endif</td>
          <td>
             @if($value->idestado==1)
              <div class="dropdown">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <div class="dropdown-content">
                   <a href="{{ url('backoffice/evento/'.$value->id.'/edit?view=confirmar') }}"><i class="fas fa-check-square"></i> Confirmar</a>
                   <a href="{{ url('backoffice/evento/'.$value->id.'/edit?view=eliminar') }}"><i class="fas fa-trash"></i>Eliminar</a>
                </div>
              </div>
             @elseif($value->idestado==2)
              <div class="dropdown">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <div class="dropdown-content">
                   <a href="{{ url('backoffice/evento/'.$value->id.'/edit?view=editinscription') }}"><i class="fas fa-edit"></i> Editar</a>
                   <a href="{{ url('backoffice/evento/'.$value->id.'/edit?view=certify') }}"><i class="fas fa-certificate"></i> Certificar</a>
                </div>
              </div>
             @endif
          </td>
        </tr>
      <?php $contar++;?>
      @endforeach
    </tbody>
  </table>
 {{ $registrados->links('app.tablepagination', ['results' => $registrados]) }}
</div>
@endsection