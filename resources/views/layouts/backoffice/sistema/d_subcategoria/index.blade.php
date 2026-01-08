@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Sub Categorías</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/subcategoria/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th>CATEGORIA</th>
        <th width="30px"></th>
        <th>NOMBRE</th>
        <th>ESTADO</th>
        <th width="10px"></th>
      </tr>
    </thead>
    @include('app.tablesearch',[
        'searchs'=>['nombre',''],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/subcategoria')
    ])
    <tbody>
      @foreach($s_categorias as $value)
        <tr>
          
          <td>{{$value->categoria}}</td>
          <td>
            @if($value->imagen!='')
            <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$value->imagen)  }}" height="40px">
            @else
                 <img src="{{ url('public/backoffice/sistema/sin_imagen_cuadrado.png')  }}" height="40px">
            @endif
          </td>
          <td>{{$value->nombre}}</td>
                <?php
                 $estado = '';
                     if($value->idestado==1){
                        $estado = 'Activado';
                    }elseif($value->idestado==2){
                         $estado = 'Desactivado';
                     }
                ?>
          <td>{{$estado}}</td>
          <td>
            
             <div class="header-user-menu menu-option" id="menu-opcion">
                 <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                 <ul>
                     <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/subcategoria/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a></li>
                     <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/subcategoria/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a></li>
                 </ul>
             </div>
          </td>
        </tr>
      @endforeach 
    </tbody>
</table>
</div>
{{ $s_categorias->links('app.tablepagination', ['results' => $s_categorias]) }}
@endsection
