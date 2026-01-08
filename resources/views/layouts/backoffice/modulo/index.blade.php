@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Módulos</span>
      <a class="btn btn-warning" href="{{ url('backoffice/modulo/create?view=registrar') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th width="10px"></th>
            <th colspan="6">Nombre</th>
            <th>Vista / Controlador</th>
            <th>Modulos</th>
            <th>Estado</th>
            <th width="10px"></th>
          </tr>
        </thead>  
        <tbody>
          @foreach($modulos as $value)
            <?php $countmodulos1 = DB::table('modulo')->where('idmodulo',$value->id)->count();?>
            <tr>
              <td>{{ $value->orden }}</td>
              <td><i class="{{ $value->icono }}"></i></td>
              <td colspan="5">{{ $value->nombre }}</td>
              <td>
                  {{ $value->vista }}<br>
                  {{ $value->controlador }}
              </td>
              <td>{{ $countmodulos1 }}</td>
              <td>
                @if($value->idestado==1)
                  Activado
                @else
                  Desactivado
                @endif
              </td>
              <td>
                <div class="dropdown">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <div class="dropdown-content">
                    <a href="{{ url('backoffice/modulo/'.$value->id.'/edit?view=registrarsubmodulo') }}"><i class="fa fa-plus"></i> Registrar</a>
                    <a href="{{ url('backoffice/modulo/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a>
                    @if($countmodulos1==0)
                    <a href="{{ url('backoffice/modulo/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a>
                    @endif
                  </div>
                </div>
              </td>
            </tr>
            <?php
            $submodulos = DB::table('modulo')
              ->where('idmodulo',$value->id)
              ->select(
                  'modulo.*'
              )
              ->orderBy('orden','asc')
              ->get();
            ?>
            @foreach($submodulos as $subvalue)
            <?php //$countrolesmodulos = DB::table('rolesmodulo')->where('idmodulo',$subvalue->id)->count();?>
            <?php $countmodulos2 = DB::table('modulo')->where('idmodulo',$subvalue->id)->count();?>
            <tr>
              <td></td>
              <td width="10px">{{ $value->orden }}.{{ $subvalue->orden }}</td>
              <td width="10px"><i class="{{ $subvalue->icono }}"></i></td>
              <td colspan="4">{{ $subvalue->nombre }}</td>
              <td>
                  {{ $subvalue->vista }}<br>
                  {{ $subvalue->controlador }}
              </td>
              <td>{{ $countmodulos2 }}</td>
              <td>
                @if($subvalue->idestado==1)
                  Activado
                @else
                  Desactivado
                @endif
              </td>
              <td>
                <div class="dropdown">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <div class="dropdown-content">
                    <a href="{{ url('backoffice/modulo/'.$subvalue->id.'/edit?view=registrarsubsubmodulo') }}"><i class="fa fa-plus"></i> Registrar</a>
                    <a href="{{ url('backoffice/modulo/'.$subvalue->id.'/edit?view=editarsubmodulo') }}"><i class="fa fa-edit"></i> Editar</a>
                    @if($countmodulos2==0)
                    <a href="{{ url('backoffice/modulo/'.$subvalue->id.'/edit?view=eliminarsubmodulo') }}"><i class="fa fa-trash"></i> Eliminar</a>
                    @endif
                  </div>
                </div>
              </td>
            </tr>
            <?php
            $subsubmodulos = DB::table('modulo')
              ->where('idmodulo',$subvalue->id)
              ->select(
                  'modulo.*'
              )
              ->orderBy('orden','asc')
              ->get();
            ?>
            @foreach($subsubmodulos as $subsubvalue)
            <?php $subcountmodulos = DB::table('modulo')->where('idmodulo',$subsubvalue->id)->count();?>
            <?php 
                $rutaimagen = getcwd().'/public/backoffice/sistema/modulo/'.$subsubvalue->imagen; 
                if(file_exists($rutaimagen) AND $subsubvalue->imagen!=''){
                    $urlimagen = url('public/backoffice/sistema/modulo/'.$subsubvalue->imagen);
                }else{
                    $urlimagen = url('public/backoffice/sistema/sin_imagen_redondo.png');
                }
            ?>
            <tr>
              <td></td>
              <td></td>
              <td width="10px">{{ $value->orden }}.{{ $subvalue->orden }}.{{ $subsubvalue->orden }}</td>
              <td width="10px"><img src="{{$urlimagen}}" height="30px"></td>
              <td colspan="3"><i class="{{ $subvalue->icono }}"></i> {{ $subsubvalue->nombre }}</td>
              <td>
                  {{ $subsubvalue->vista }}<br>
                  {{ $subsubvalue->controlador }}
              </td>
              <td>{{ $subcountmodulos }}</td>
              <td>
                @if($subsubvalue->idestado==1)
                  Activado
                @else
                  Desactivado
                @endif
              </td>
              <td>
                <div class="dropdown">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <div class="dropdown-content">
                    @if($subsubvalue->vista=='' && $subsubvalue->controlador=='')
                    <a href="{{ url('backoffice/modulo/'.$subsubvalue->id.'/edit?view=registrarsistemamodulo') }}"><i class="fa fa-plus"></i> Registrar</a>
                    @endif
                    <a href="{{ url('backoffice/modulo/'.$subsubvalue->id.'/edit?view=editarsubsubmodulo') }}"><i class="fa fa-edit"></i> Editar</a>
                    @if($subcountmodulos==0)
                    <a href="{{ url('backoffice/modulo/'.$subsubvalue->id.'/edit?view=eliminarsubsubmodulo') }}"><i class="fa fa-trash"></i> Eliminar</a>
                    @endif
                  </div>
                </div>
              </td>
            </tr>
            <?php
            $sistemamodulos = DB::table('modulo')
              ->where('idmodulo',$subsubvalue->id)
              ->select(
                  'modulo.*'
              )
              ->orderBy('orden','asc')
              ->get();
            ?>
            @foreach($sistemamodulos as $sistemavalue)
            <?php $subcountmodulossistema = DB::table('modulo')->where('idmodulo',$sistemavalue->id)->count();?>
            <?php 
                $rutaimagen = getcwd().'/public/backoffice/sistema/modulo/'.$sistemavalue->imagen; 
                if(file_exists($rutaimagen) AND $sistemavalue->imagen!=''){
                    $urlimagen = url('public/backoffice/sistema/modulo/'.$sistemavalue->imagen);
                }else{
                    $urlimagen = url('public/backoffice/sistema/sin_imagen_redondo.png');
                }
            ?>
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td width="10px">{{ $value->orden }}.{{ $subvalue->orden }}.{{ $subsubvalue->orden }}.{{ $sistemavalue->orden }}</td>
              <td width="10px"><img src="{{$urlimagen}}" height="30px"></td>
              <td colspan="2"><i class="{{ $sistemavalue->icono }}"></i> {{ $sistemavalue->nombre }}</td>
              <td>
                  {{ $sistemavalue->vista }}<br>
                  {{ $sistemavalue->controlador }}
              </td>
              <td>{{ $subcountmodulossistema }}</td>
              <td>
                @if($sistemavalue->idestado==1)
                  Activado
                @else
                  Desactivado
                @endif
              </td>
              <td>
                <div class="dropdown">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <div class="dropdown-content">
                    <a href="{{ url('backoffice/modulo/'.$sistemavalue->id.'/edit?view=registrarsistemamoduloopcion') }}"><i class="fa fa-plus"></i> Registrar</a>
                    <a href="{{ url('backoffice/modulo/'.$sistemavalue->id.'/edit?view=editarsistemamodulo') }}"><i class="fa fa-edit"></i> Editar</a>
                    <a href="{{ url('backoffice/modulo/'.$sistemavalue->id.'/edit?view=eliminarsistemamodulo') }}"><i class="fa fa-trash"></i> Eliminar</a>
                  </div>
                </div>
              </td>
            </tr>
            <?php
            $sistemamoduloopcions = DB::table('modulo')
              ->where('idmodulo',$sistemavalue->id)
              ->select(
                  'modulo.*'
              )
              ->orderBy('orden','asc')
              ->get();
            ?>
            @foreach($sistemamoduloopcions as $sistemavalueopcion)
            <?php $subcountmodulossistemaopcion = DB::table('modulo')->where('idmodulo',$sistemavalueopcion->id)->count();?>
            <?php 
                $rutaimagen = getcwd().'/public/backoffice/sistema/modulo/'.$sistemavalueopcion->imagen; 
                if(file_exists($rutaimagen) AND $sistemavalueopcion->imagen!=''){
                    $urlimagen = url('public/backoffice/sistema/modulo/'.$sistemavalueopcion->imagen);
                }else{
                    $urlimagen = url('public/backoffice/sistema/sin_imagen_redondo.png');
                }
            ?>
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td width="10px">{{ $value->orden }}.{{ $subvalue->orden }}.{{ $subsubvalue->orden }}.{{ $sistemavalue->orden }}.{{ $sistemavalueopcion->orden }}</td>
              <td width="10px"><img src="{{$urlimagen}}" height="30px"></td>
              <td><i class="{{ $sistemavalueopcion->icono }}"></i> {{ $sistemavalueopcion->nombre }}</td>
              <td>
                  {{ $sistemavalueopcion->vista }}<br>
                  {{ $sistemavalueopcion->opcion }}
              </td>
              <td>{{ $subcountmodulossistemaopcion }}</td>
              <td>
                @if($sistemavalueopcion->idestado==1)
                  Activado
                @else
                  Desactivado
                @endif
              </td>
              <td>
                <div class="dropdown">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <div class="dropdown-content">
                    <a href="{{ url('backoffice/modulo/'.$sistemavalueopcion->id.'/edit?view=editarsistemamoduloopcion') }}"><i class="fa fa-edit"></i> Editar</a>
                    <a href="{{ url('backoffice/modulo/'.$sistemavalueopcion->id.'/edit?view=eliminarsistemamoduloopcion') }}"><i class="fa fa-trash"></i> Eliminar</a>
                  </div>
                </div>
              </td>
            </tr>
            @endforeach
            @endforeach
            @endforeach
            @endforeach
          @endforeach
        </tbody>
    </table>
</div>  
<style>
.td-badge {
    width: 100%;
    text-align: left;
    padding-top: 1px;
    padding-bottom: 1px;
}
</style>
@endsection