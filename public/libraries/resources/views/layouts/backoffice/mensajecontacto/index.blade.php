@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 

<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>MENSAJES DE CONTACTO</span>
      <a class="btn btn-warning" href="{{ url('backoffice/usuario/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>


<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th>NOMBRES</th>
            <th>CORREO</th>
            <th>MENSAJE</th>
            <th width="10px"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($mensajecontacto as $value)
            <tr>
              <td>{{ $value->nombre }} - <span> {{ date_format(date_create($value->fecharegistro), 'd-m-Y h:i A') }}</span></td>
              <td>{{ $value->email }}</td>
              <td>{{ $value->mensaje }}</td>
              <td>
                <div class="dropdown">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <div class="dropdown-content">
                     <a href="{{ url('backoffice/mensajecontacto/'.$value->id.'/edit?view=responder') }}" class="del-btn"><i class="fa fa-send"></i> Responder</a>
                     <a href="{{ url('backoffice/mensajecontacto/'.$value->id.'/edit?view=eliminar') }}" class="del-btn"><i class="fa fa-trash"></i> Eliminar</a>
                  </div>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
    </table>

</div>
@endsection