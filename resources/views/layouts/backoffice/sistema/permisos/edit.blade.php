<form action="javascript:;" 
        onsubmit="callback({
            route: '{{ url('backoffice/'.$tienda->id.'/permisos/'.$permiso->id) }}',
            method: 'PUT',
            data:{
                view: 'editar',
                idmodulos : seleccionar_modulos()
            }
        },
        function(resultado){
            $('#tabla-permisos').DataTable().ajax.reload();
            $('#modal-close-listanegra-registrar').click(); 
        },this)"> 
    <div class="modal-header">
        <h5 class="modal-title">Editar Permisos y Módulos</h5>
        <button type="button" class="btn-close" id="modal-close-listanegra-registrar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
          
             <div class="col-md-12">
                <label>Rango</label>
                <input type="number" value="{{ $permiso->rango }}" id="rango" class="form-control">
                <label>Nombre *</label>
                <input type="text" class="form-control" value="{{ $permiso->nombre }}" id="nombre"/>
  
            <div class="col-sm-12">
                <label>Permisos *</label>
            <div class="accordion" id="accordion_1">
                <?php
                $modulos = DB::table('modulo')
                    ->join('rolesmodulo','rolesmodulo.idmodulo','modulo.id')
                    ->join('roles','roles.id','rolesmodulo.idroles')
                    ->where('modulo.idmodulo',7)
                    ->where('modulo.idestado',1)
                    //->where('roles.idcategoria',4)
                    ->select('modulo.id as id', 'modulo.nombre as nombre')
                    ->orderBy('modulo.orden','asc')
                    ->distinct()
                    ->get();

                ?>
            
                @foreach($modulos as $subsubvalue)
                    <?php
                        $permiso_acceso = DB::table('permisoacceso')
                        ->where('permisoacceso.idmodulo',$subsubvalue->id)
                        ->where('permisoacceso.idpermiso',$permiso->id) 
                        ->first(); 
                        
                    ?>
                    <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{$subsubvalue->id}}">
                        <button class="accordion-button collapsed" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapse{{$subsubvalue->id}}" 
                                aria-expanded="false" 
                                aria-controls="collapse{{$subsubvalue->id}}">
                        {{$subsubvalue->nombre}}
                        <div style="display:none;">
                        <input class="form-check-input idpermiso MasterCheckbox{{$subsubvalue->id}}" 
                                type="checkbox" 
                                value="{{ $subsubvalue->id }}" 
                                id="idpermiso{{ $subsubvalue->id }}" 
                                {{ $permiso_acceso ? 'checked' : '' }}
                                >
                        </div>
                        </button>                  
                    </h2>
                    <div id="collapse{{$subsubvalue->id}}" class="accordion-collapse collapse" aria-labelledby="heading{{$subsubvalue->id}}" data-bs-parent="#accordion_1">
                        <div class="accordion-body">
                            <?php
                                $sistemamodulos = DB::table('modulo')
                                    //->join('rolesmodulo','rolesmodulo.idmodulo','modulo.id')
                                    //->join('roles','roles.id','rolesmodulo.idroles')
                                    ->where('modulo.idmodulo',$subsubvalue->id)
                                    ->where('modulo.idestado',1)
                                    //->where('roles.idcategoria',4)
                                    ->select('modulo.id as id', 'modulo.nombre as nombre', 'modulo.vista as vista')
                                    ->orderBy('modulo.orden','asc')
                                    ->distinct()
                                    ->get();
                            ?>
                            @foreach($sistemamodulos as $sistemavalue)
                                <?php
                                    
                                    $permiso_acceso_sub = DB::table('permisoacceso')
                                        ->where('permisoacceso.idmodulo',$sistemavalue->id)
                                        ->where('permisoacceso.idpermiso',$permiso->id) 
                                        ->first(); 
                          
                                  $sistemamodulos2 = DB::table('modulo')
                                            ->join('rolesmodulo','rolesmodulo.idmodulo','modulo.id')
                                            ->join('roles','roles.id','rolesmodulo.idroles')
                                            ->where('modulo.idmodulo',$sistemavalue->id)
                                            ->where('modulo.idestado',1)
                                            //->where('roles.idcategoria',4)
                                            ->select('modulo.id as id', 'modulo.nombre as nombre', 'modulo.vista as vista')
                                            ->orderBy('modulo.orden','asc')
                                            ->distinct()
                                            ->get();
                                ?>
                                <div class="table-responsive">
                                    <table class="table" id="tabla-usuarioacceso-permisos">
                                        <tr>
                                            <td width="10px"><i data-feather="check"></i></td>
                                            <td colspan='2'>
                                                <label class="chk" style="justify-content: space-between;width:100%;">
                                                    {{$sistemavalue->nombre}}
                                                    <input class="idpermiso MasterCheckboxSistema{{$sistemavalue->id}} checkboxlistitem{{$subsubvalue->id}}" 
                                                        type="checkbox" 
                                                        value="{{ $sistemavalue->id }}" 
                                                        style="margin-top: 10px;"
                                                        id="idpermiso{{ $sistemavalue->id }}" 
                                                        {{ $permiso_acceso_sub ? 'checked' : '' }}
                                                        >
                                                    <span class="checkmark"></span>
                                                </label>
                                                @foreach($sistemamodulos2 as $sistemavalue2)
                                                    <?php

                                                        $permiso_acceso_sub = DB::table('permisoacceso')
                                                            ->where('permisoacceso.idmodulo',$sistemavalue2->id)
                                                            ->where('permisoacceso.idpermiso',$permiso->id) 
                                                            ->first(); 
                                                    ?>
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <tr>
                                                                <td width="10px" style="background-color: #e1e1e1 !important;"><i data-feather="check"></i></td>
                                                                <td colspan='2' style="background-color: #e1e1e1 !important;">
                                                                    
                                                                    <label class="chk" style="justify-content: space-between;width:100%;">
                                                                        {{$sistemavalue->nombre}}
                                                                        <input class="idpermiso MasterCheckboxSistema{{$sistemavalue2->id}} checkboxlistitem{{$subsubvalue->id}}" 
                                                                            type="checkbox" 
                                                                            value="{{ $sistemavalue2->id }}" 
                                                                            style="margin-top: 10px;"
                                                                            id="idpermiso{{ $sistemavalue2->id }}" 
                                                                            {{ $permiso_acceso_sub ? 'checked' : '' }}
                                                                            >
                                                                        <span class="checkmark"></span>
                                                                    </label>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <script>
                                                        $(".MasterCheckboxSistema{{$sistemavalue2->id}}").change(function() {
                                                            $(".checkboxlistitemsistema{{$sistemavalue2->id}}").prop("checked", this.checked);
                                                        });
                                                        $(".checkboxlistitemsistema{{$sistemavalue2->id}}").change(function() {
                                                            if($(".checkboxlistitemsistema{{$sistemavalue2->id}}:checked").length>0){
                                                                $(".MasterCheckboxSistema{{$sistemavalue2->id}}").prop("checked", true);
                                                            }else{
                                                                $(".MasterCheckboxSistema{{$sistemavalue2->id}}").prop('checked', false); 
                                                            }
                                                            if($(".checkboxlistitem{{$subsubvalue->id}}:checked").length>0){
                                                                $(".MasterCheckbox{{$subsubvalue->id}}").prop("checked", true);
                                                            }else{
                                                                $(".MasterCheckbox{{$subsubvalue->id}}").prop('checked', false); 
                                                            }
                                                        });
                                                    </script>
                                                @endforeach
                                              
                                            </td>
                                          
                                        </tr>
                                    </table>
                                </div>
                                <script>
                                    $(".MasterCheckboxSistema{{$sistemavalue->id}}").change(function() {
                                        $(".checkboxlistitemsistema{{$sistemavalue->id}}").prop("checked", this.checked);
                                    });
                                    $(".checkboxlistitemsistema{{$sistemavalue->id}}").change(function() {
                                        if($(".checkboxlistitemsistema{{$sistemavalue->id}}:checked").length>0){
                                            $(".MasterCheckboxSistema{{$sistemavalue->id}}").prop("checked", true);
                                        }else{
                                            $(".MasterCheckboxSistema{{$sistemavalue->id}}").prop('checked', false); 
                                        }
                                        if($(".checkboxlistitem{{$subsubvalue->id}}:checked").length>0){
                                            $(".MasterCheckbox{{$subsubvalue->id}}").prop("checked", true);
                                        }else{
                                            $(".MasterCheckbox{{$subsubvalue->id}}").prop('checked', false); 
                                        }
                                    });
                                </script>
                            @endforeach
                        </div>
                    </div>
                    </div>

                    <!-- CARGAR TIENDAS -->
                    <script>
                        $(".checkboxlistitem{{$subsubvalue->id}}").change(function() {
                            if($(".checkboxlistitem{{$subsubvalue->id}}:checked").length>0){
                                $(".MasterCheckbox{{$subsubvalue->id}}").prop("checked", true);
                            }else{
                                $(".MasterCheckbox{{$subsubvalue->id}}").prop('checked', false); 
                            }
                        });
                    </script>
                @endforeach
            </div> 
            </div>
        </div>


    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form>  
<style>
    .accordion-button:not(.collapsed) {
    background-color: #212529;
}
    
</style>
<script>
    function seleccionar_modulos(){
        var idmodulos = '';
        $('.idpermiso[type=checkbox]:checked').each(function() {
            idmodulos = idmodulos+','+$(this).val();
        });
        return idmodulos;
    }
</script>
