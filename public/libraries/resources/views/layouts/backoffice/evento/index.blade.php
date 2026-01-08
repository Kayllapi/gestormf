@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>EVENTOS</span>
      <a class="btn btn-warning" href="{{ url('backoffice/evento/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>

<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th width="100px">Fecha</th>
            <th width="80px">Hora</th>
            <th width="72%" >Tema</th>
            <th>Cantidad</th>
            <th width="10px">Estado</th>
            <th width="10px"></th>
          </tr>
        </thead>
        @include('app.tablesearch',[
            'searchs'=>['fecharegistro','hora','tema'],
            'search_url'=> url('backoffice/evento')
        ])
        <tbody>
          @php 
            $cont=0;
          @endphp
          @foreach($eventos as $value)
            <tr>
              <td>{{date_format(date_create($value->fecha),'d/m/Y')}}</td>
              <td>{{date_format(date_create($value->hora),'h:i A')}}</td>
              <td>{{ $value->nombre }}</td>
              <td> 
                <?=$countEvent = DB::table('eventoregistro')->where('idevento',$value->id)->count(); ?>
              </td>
              <td>
                @if($value->idestado==1) 
                <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-check"></i> No Visible</span></div>
                @else 
                <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Visible</span></div>
                @endif
              </td>
              
              <td>
                <div class="dropdown">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <div class="dropdown-content">
                    @php 
                      $eventCount = DB::table('eventoregistro')->where('idevento',$value->id)->count();
                    @endphp 
                    
                    @if($eventCount>0)
                      <a href="{{ url('backoffice/evento/'.$value->id.'/edit?view=editar') }}"><i class="fas fa-edit"></i> Editar</a>
                      <a href="{{ url('backoffice/evento/'.$value->id.'/edit?view=registrar') }}"><i class="fas fa-user"></i> Registrar</a>
                      <a href="{{ url('backoffice/evento/'.$value->id.'/edit?view=registrado') }}"><i class="fas fa-users"></i> Registrados</a>
                    @elseif($eventCount==0)
                     <a href="{{ url('backoffice/evento/'.$value->id.'/edit?view=editar') }}"><i class="fas fa-edit"></i> Editar</a>
                     <a href="{{ url('backoffice/evento/'.$value->id.'/edit?view=eliminarevento') }}"><i class="fas fa-trash"></i> Eliminar</a>
                    @endif
                  </div>
                </div> 
              </td>
            </tr>
            @php  $cont++; @endphp
          @endforeach
        </tbody>
    </table>
    {{ $eventos->links('app.tablepagination', ['results' => $eventos]) }}
</div>  
@endsection
    @section('scriptsbackoffice')
    <style>
    .new-dashboard-item {
        background-color: transparent;
        border: 1px solid #1877b7;
        color: #1877b7;
    }
    </style>
    @endsection
