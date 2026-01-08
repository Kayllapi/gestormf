@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>MÓDULOS</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/modulo/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th >Nombre</th>
        <th >Ruta</th>
        <th >Estado</th>
        <th width="10px"></th>
      </tr>
    </thead>
<!--     @include('app.tablesearch',[
        'searchs'=>['tipo','nombre','',''],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/marca')
    ]) -->
    <tbody>
      @foreach($s_modulo as $value)
        <tr>
          <td>{{$value->orden}} <?php echo $value->icono;?> {{$value->nombre}}</td>
          <td>{{$value->ruta}}</td>
          <td>@if($value->idestado==1) Activado @else Desactivado @endif </td>
          <td>
            <div class="dropdown">
              <a href="javascript:;" class="btn btn-success">Opción <i class="fa fa-angle-down"></i></a>
              <div class="dropdown-content">
                <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/modulo/'.$value->id.'/edit?view=submodulo') }}"><i class="fa fa-plus"></i> Sub Módulo</a>
                <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/modulo/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a>
                <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/modulo/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a>
              </div>
            </div>
          </td>
        </tr>
        <?php $submodulo = DB::table('s_modulo')->where('s_modulo.s_idmodulo',$value->id)->get(); ?>
        @foreach($submodulo as $subvalue)
          <tr>
          <td>&nbsp;&nbsp;&nbsp;{{$value->orden}}.{{$subvalue->orden}} <?php echo $subvalue->icono;?> {{$subvalue->nombre}}</td>
          <td>{{$subvalue->ruta}}</td>
          <td>@if($subvalue->idestado==1) Activado @else Desactivado @endif </td>
          <td>
            <div class="dropdown">
              <a href="javascript:;" class="btn btn-warning">Opción <i class="fa fa-angle-down"></i></a>
              <div class="dropdown-content">
                <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/modulo/'.$subvalue->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a>
                <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/modulo/'.$subvalue->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a>
              </div>
            </div>
          </td>
        </tr>
        @endforeach 
      @endforeach 
    </tbody>
</table>
{{ $s_modulo->links('app.tablepagination', ['results' => $s_modulo]) }}
</div>
@endsection
