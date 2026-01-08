<div class="mb-1">
<label>Módulos</label>
<div class="accordion" id="accordion_1">
    <?php
    $modulos = DB::table('modulo')
        ->join('rolesmodulo','rolesmodulo.idmodulo','modulo.id')
        ->join('roles','roles.id','rolesmodulo.idroles')
        ->where('modulo.idmodulo',7)
        ->where('modulo.idestado',1)
        ->where('roles.idcategoria',$tienda->idcategoria)
        ->select('modulo.id as id', 'modulo.nombre as nombre')
        ->orderBy('modulo.orden','asc')
        ->distinct()
        ->get();
      
        
    ?>
  
    @foreach($modulos as $subsubvalue)
        <?php 
        
        if($idsucursal != 0 || $idsucursal != '0'){
          
          $rolesmodulo = DB::table('usersrolesmodulo')
              // ->where('idusers',$usuario->id)
              ->when($usuario !== null, function ($query) use ($usuario) {
                return $query->where('idusers', $usuario->id);
              })
              ->where('idmodulo',$subsubvalue->id)
              ->where('idsucursal',$idsucursal)
              ->where($where)
              ->limit(1)
              ->first(); 
        }else{
          
          
          
          $rolesmodulo = DB::table('usersrolesmodulo')
        
              // ->where('usersrolesmodulo.idusers',$usuario->id)
              ->when($usuario !== null, function ($query) use ($usuario) {
                return $query->where('usersrolesmodulo.idusers', $usuario->id);
              })
              ->where('usersrolesmodulo.idsucursal',0)
              ->where('usersrolesmodulo.idmodulo',$subsubvalue->id)
              ->orWhere('usersrolesmodulo.idmodulo',$subsubvalue->id)
              ->whereNull('usersrolesmodulo.idsucursal')
              // ->where('usersrolesmodulo.idusers',$usuario->id)
              ->when($usuario !== null, function ($query) use ($usuario) {
                return $query->where('usersrolesmodulo.idusers', $usuario->id);
              })
              ->limit(1)
              ->first(); 
        }
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
                     {{ $rolesmodulo!='' ? 'checked':''  }}
                     >
              </div>
            </button>                  
          </h2>
          <div id="collapse{{$subsubvalue->id}}" class="accordion-collapse collapse" aria-labelledby="heading{{$subsubvalue->id}}" data-bs-parent="#accordion_1">
            <div class="accordion-body">
          <?php
          $sistemamodulos = DB::table('modulo')
              ->join('rolesmodulo','rolesmodulo.idmodulo','modulo.id')
              ->join('roles','roles.id','rolesmodulo.idroles')
              ->where('modulo.idmodulo',$subsubvalue->id)
              ->where('modulo.idestado',1)
              ->where('roles.idcategoria',$tienda->idcategoria)
              ->select('modulo.id as id', 'modulo.nombre as nombre', 'modulo.vista as vista')
              ->orderBy('modulo.orden','asc')
              ->distinct()
              ->get();
          ?>
          @foreach($sistemamodulos as $sistemavalue)
            <div class="table-responsive">
            <table class="table table-hover" id="tabla-usuarioacceso-permisos">
              <tr>
                  <td width="10px"><i data-feather="check"></i></td>
                  <td colspan='2'>
                      <?php 
                      if($idsucursal>0){
                        
                        $rolesmodulo = DB::table('usersrolesmodulo')
                          ->where('idmodulo',$sistemavalue->id)
                          ->where('idsucursal',$idsucursal)
                          // ->where('idusers',$usuario->id)
                          ->when($usuario !== null, function ($query) use ($usuario) {
                            return $query->where('idusers', $usuario->id);
                          })
                          ->limit(1)
                          ->first(); 
                      }else{
                        
                        $rolesmodulo = DB::table('usersrolesmodulo')
                            // ->where($where_sistemavalue_null)

                            // ->where('usersrolesmodulo.idusers',$usuario->id)
                            ->when($usuario !== null, function ($query) use ($usuario) {
                              return $query->where('usersrolesmodulo.idusers', $usuario->id);
                            })
                            ->where('usersrolesmodulo.idsucursal',0)
                            ->where('usersrolesmodulo.idmodulo',$sistemavalue->id)
                            ->orWhere('usersrolesmodulo.idmodulo',$sistemavalue->id)
                            ->whereNull('usersrolesmodulo.idsucursal')
                            // ->where('usersrolesmodulo.idusers',$usuario->id)
                            ->when($usuario !== null, function ($query) use ($usuario) {
                              return $query->where('usersrolesmodulo.idusers', $usuario->id);
                            })
                            ->limit(1)
                            ->first(); 
                      }
                      ?>
                      <div class="form-check form-check-reverse">
                        <input class="form-check-input idpermiso MasterCheckboxSistema{{$sistemavalue->id}} checkboxlistitem{{$subsubvalue->id}}" 
                               type="checkbox" 
                               value="{{ $sistemavalue->id }}" 
                               style="margin-top: 10px;"
                               id="idpermiso{{ $sistemavalue->id }}" {{ $usuario ? ($rolesmodulo!='' ? 'checked':'') : ''  }}>
                        <label class="form-check-label" style="width: 100%;text-align: left;padding-bottom: 10px;padding-top: 8px;margin-top: 0px;" for="idpermiso{{ $sistemavalue->id }}">
                          {{$sistemavalue->nombre}}
                        </label>
                      </div>
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