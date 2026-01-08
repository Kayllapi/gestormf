@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Accesos',
    'botones'=>[
        'registrar:/'.$tienda->id.'/usuarioacceso/create?view=registrar: Registrar'
    ]
])
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th width="100px">RUC/DNI </th>
        <th>Cliente</th>
        <th>Usuario</th>
        <th>Cargo</th>
        <th width="100px">Estado</th>
        <th width="10px"></th>
      </tr>
    </thead>
    @include('app.tablesearch',[
        'searchs'=>['identificacion','cliente','usuario','permiso','select:acceso/1=Activado,2=Desactivado'],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/usuarioacceso')
    ])
    <tbody>
        @foreach($usuarios as $value)
        <tr clave="{{$value->clave}}">
          <td>{{ $value->identificacion }}</td>
          <td>
            @if($value->idtipopersona==1)
              {{ $value->apellidos }}, {{ $value->nombre }}
            @else
              {{ $value->nombre }}
            @endif  
          </td>
          <td>
            <?php 
                $lusuario = explode('@',$value->usuario); 
                $valusuario = $value->usuario;
                if($lusuario>1){
                    $valusuario = $lusuario[0];
                }
            ?>
            {{ $valusuario }}
            @if(Auth::user()->idtienda==0)
            ({{$value->clave}})
            @endif
          </td>
          <td>{{ $value->cargo }}</td>
          <td>
            @if($value->idestadousuario==1)
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Activado</span></div>
            @else
              <div class="td-badge"><span class="badge badge-pill badge-dark">Desactivado</span></div> 
            @endif
          </td>
          <td>
                <div class="header-user-menu menu-option" id="menu-opcion">
                    <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                    <ul>
                        <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/usuarioacceso/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a></li>
                        <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/usuarioacceso/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a></li>
                    </ul>
                </div>
          </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $usuarios->links('app.tablepagination', ['results' => $usuarios]) }}
</div>

@endsection
