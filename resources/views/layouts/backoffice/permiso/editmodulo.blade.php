@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>{{ strtoupper($permiso->description) }} / MÓDULOS</span>
      <a class="btn btn-success" href="{{ url('backoffice/permiso') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="selectestadomodulo(this)" enctype="multipart/form-data">
    <div class="table-responsive">
        <table class="table" id="tabla-contenido">
            <thead class="thead-dark">
              <tr>
                <th width="10px"></th>
                <th colspan="6">Nombre</th>
                <th width="10px"></th>
              </tr>
            </thead>  
            <tbody>
              @foreach($modulos as $value)
              <?php $countrolesmodulos = DB::table('rolesmodulo')->where('idmodulo',$value->id)->count();?>
              <?php $countmodulos = DB::table('modulo')->where('idmodulo',$value->id)->count();?>
                <tr>
                  <td>{{ $value->orden }}</td>
                  <td width="10px"><i class="{{ $value->icono }}"></i></td>
                  <td colspan="5">{{ $value->nombre }}</td>
                  <td>
                    <div class="onoffswitch">
                        <?php $rolesmodulo = DB::table('rolesmodulo')->where('idroles',$permiso->id)->where('idmodulo',$value->id)->limit(1)->first(); ?>
                        <input type="checkbox" class="onoffswitch-checkbox idpermiso" id="idpermiso{{ $value->id }}" value="{{ $value->id }}" <?php echo $rolesmodulo!='' ? 'checked':'' ?>>
                        <label class="onoffswitch-label" for="idpermiso{{ $value->id }}">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                  </td>
                </tr>
                <?php
                $submodulos = DB::table('modulo')
                  ->where('idmodulo',$value->id)
                  ->where('idestado',1)
                  ->orderBy('orden','asc')
                  ->get();
                ?>
                @foreach($submodulos as $subvalue)
                <?php $countrolesmodulos = DB::table('rolesmodulo')->where('idmodulo',$subvalue->id)->count();?>
                <?php $countmodulos = DB::table('modulo')->where('idmodulo',$subvalue->id)->count();?>
                <tr>
                  <td></td>
                  <td></td>
                  <td width="10px">{{ $value->orden }}.{{ $subvalue->orden }}</td>
                  <td colspan="4">{{ $subvalue->nombre }}</td>
                  <td>
                    <div class="onoffswitch">
                        <?php $rolesmodulo = DB::table('rolesmodulo')->where('idroles',$permiso->id)->where('idmodulo',$subvalue->id)->limit(1)->first(); ?>
                        <input type="checkbox" class="onoffswitch-checkbox idpermiso" id="idpermiso{{ $value->id }}{{ $subvalue->id }}" value="{{ $subvalue->id }}" <?php echo $rolesmodulo!='' ? 'checked':'' ?>>
                        <label class="onoffswitch-label" for="idpermiso{{ $value->id }}{{ $subvalue->id }}">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                  </td>
                </tr>
                <?php
                $subsubmodulos = DB::table('modulo')
                  ->where('idmodulo',$subvalue->id)
                  ->orderBy('orden','asc')
                  ->where('idestado',1)
                  ->get();
                ?>
                @foreach($subsubmodulos as $subsubvalue)
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td width="10px">{{ $value->orden }}.{{ $subvalue->orden }}.{{ $subsubvalue->orden }}</td>
                  <td colspan="3">{{ $subsubvalue->nombre }}</td>
                  <td>
                    <div class="onoffswitch">
                        <?php $rolesmodulo = DB::table('rolesmodulo')->where('idroles',$permiso->id)->where('idmodulo',$subsubvalue->id)->limit(1)->first(); ?>
                        <input type="checkbox" class="onoffswitch-checkbox idpermiso" id="idpermiso{{ $value->id }}{{ $subvalue->id }}{{ $subsubvalue->id }}" value="{{ $subsubvalue->id }}" <?php echo $rolesmodulo!='' ? 'checked':'' ?>>
                        <label class="onoffswitch-label" for="idpermiso{{ $value->id }}{{ $subvalue->id }}{{ $subsubvalue->id }}">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                  </td>
                </tr>
                <?php
                $sistemamodulos = DB::table('modulo')
                  ->where('idmodulo',$subsubvalue->id)
                  ->orderBy('orden','asc')
                  ->where('idestado',1)
                  ->get();
                ?>
                @foreach($sistemamodulos as $sistemavalue)
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td width="10px">{{ $value->orden }}.{{ $subvalue->orden }}.{{ $subsubvalue->orden }}.{{ $sistemavalue->orden }}</td>
                  <td colspan="2">{{ $sistemavalue->nombre }}</td>
                  <td>
                    <div class="onoffswitch">
                        <?php $rolesmodulo = DB::table('rolesmodulo')->where('idroles',$permiso->id)->where('idmodulo',$sistemavalue->id)->limit(1)->first(); ?>
                        <input type="checkbox" class="onoffswitch-checkbox idpermiso" id="idpermiso{{ $value->id }}{{ $subvalue->id }}{{ $subsubvalue->id }}{{ $sistemavalue->id }}" value="{{ $sistemavalue->id }}" <?php echo $rolesmodulo!='' ? 'checked':'' ?>>
                        <label class="onoffswitch-label" for="idpermiso{{ $value->id }}{{ $subvalue->id }}{{ $subsubvalue->id }}{{ $sistemavalue->id }}">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                  </td>
                </tr>
                <?php
                $sistemamoduloopcions = DB::table('modulo')
                  ->where('idmodulo',$sistemavalue->id)
                  ->orderBy('orden','asc')
                  ->where('idestado',1)
                  ->get();
                ?>
                @foreach($sistemamoduloopcions as $sistemavalueopcion)
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td width="10px">{{ $value->orden }}.{{ $subvalue->orden }}.{{ $subsubvalue->orden }}.{{ $sistemavalue->orden }}.{{ $sistemavalueopcion->orden }}</td>
                  <td>{{ $sistemavalueopcion->nombre }}</td>
                  <td>
                    <div class="onoffswitch">
                        <?php $rolesmodulo = DB::table('rolesmodulo')->where('idroles',$permiso->id)->where('idmodulo',$sistemavalueopcion->id)->limit(1)->first(); ?>
                        <input type="checkbox" class="onoffswitch-checkbox idpermiso" id="idpermiso{{ $value->id }}{{ $subvalue->id }}{{ $subsubvalue->id }}{{ $sistemavalue->id }}{{ $sistemavalueopcion->id }}" value="{{ $sistemavalueopcion->id }}" <?php echo $rolesmodulo!='' ? 'checked':'' ?>>
                        <label class="onoffswitch-label" for="idpermiso{{ $value->id }}{{ $subvalue->id }}{{ $subsubvalue->id }}{{ $sistemavalue->id }}{{ $sistemavalueopcion->id }}">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                        </label>
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
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</button><br><br><br><br><br><br> 
        </div>
    </div>  
</form>                             
@endsection
@section('scriptsbackoffice')
<script>
function selectestadomodulo(pthis){
    var idmodulos = '';
    $('.idpermiso[type=checkbox]:checked').each(function() {
        idmodulos = idmodulos+','+$(this).val();
    });
    callback({
        route: 'backoffice/permiso/{{ $permiso->id }}',
        method: 'PUT',
        data: {
            'view' : 'editarmodulo',
            'idmodulos' : idmodulos
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/permiso') }}';                                                                            
    },pthis)
}

</script>
@endsection