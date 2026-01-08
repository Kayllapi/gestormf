<div class="modal-header">
    <h5 class="modal-title">
      Niveles de aprobación
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()" style="font-size: 20px;"></button>
</div>

<div class="modal-body">
  <form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/responsableaprobacion') }}',
        method: 'POST',
        data:{
            view: 'registrar',
            permiso_credito_prendario : getJsonPermiso('table-creditosprendarios'),
            permiso_credito_noprendario : getJsonPermiso('table-creditosnoprendarios'),
            select_creditosprendarios : select_creditosprendarios(),
            select_creditosnoprendarios : select_creditosnoprendarios()
        }
    },
    function(resultado){
   
    },this)"> 
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            
            <table class="table table-striped table-hover table-bordered" id="table-creditosprendarios">
              <thead class="table-dark">
                <tr>
                  <td colspan="7">CRÉDITOS PRENDARIOS</td>
                </tr>
                <tr>
                  <td style="width:200px;">NIVELES DE APROBACIÓN *</td>
                  <td colspan="2" style="width:200px;">RIESGO CREDITICIO (S/.) *</td>
                  <td>COMITÉ DE APROBACIÓN</td>
                  <td>AUTONOMÍA DE ADMINITRACIÓN</td>
                  <td>AUTONOMÍA DE GERENCIA GENERAL</td>
                  <td><a href="javascript:;" class="btn btn-success" onclick="agregar_nivelaprobacion_prendario()">
                      <i class="fa-solid fa-plus"></i>
                    </a></td>
                </tr>
              </thead>
              <tbody num="{{ count($nivelaprobacions_prendario) }}">
                @foreach($nivelaprobacions_prendario as $key => $value)
                  <tr id="{{ $key }}">
                    <td><input type="text" class="form-control" nombre_aprobacion value="{{ $value->nivelaprobacionnombre }}" id="nivelaprobacionnombre{{ $key }}"></td>
                    <td style="width:120px;">
                        <div class="input-group">
                          <span class="input-group-text">></span>
                          <input type="number" step="any" riesgocredito_one value="{{ $value->riesgocredito1 }}" id="riesgocredito1{{ $key }}" class="form-control campo_moneda">
                        </div>
                    </td>
                    <td style="width:120px;">
                        <div class="input-group">
                          <span class="input-group-text"><=</span>
                          <input type="number" step="any" riesgocredito_two value="{{ $value->riesgocredito2 }}" id="riesgocredito2{{ $key }}" class="form-control campo_moneda">
                        </div>
                    </td>
                    <td class="align-top" >
                      <div class="row">
                        <div class="col-12 col-md-9">
                          <label>Selección de Responsables:</label>
                          <select class="form-select" id="nivelaprobacion_cprendario{{ $key }}" onchange="addPermisoTable(this,'#container_permiso_nivelaprobacion{{ $key }}', $('input[name=nivelaprobacion{{ $key }}]:checked').val() )">
                            <option></option>
                            @foreach($permisos as $value_per)
                              <option value="{{ $value_per->id }}">{{ $value_per->nombre }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-12 col-md-3">
                          <label>Opción:</label>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="nivelaprobacion{{ $key }}" value="1" checked>
                            <label class="form-check-label">1</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="nivelaprobacion{{ $key }}" value="2">
                            <label class="form-check-label" >2</label>
                          </div>
                        </div>
                      </div>
                      <div id="container_permiso_nivelaprobacion{{ $key }}" data_nivelaprobacion >
                        <?php 
                          $data_nivelaprobacion = json_decode($value->nivelaprobacion);
                          $data_nivelaprobacion_uno = $data_nivelaprobacion[0]->tipo_uno;
                          $data_nivelaprobacion_dos = $data_nivelaprobacion[0]->tipo_dos;
                        ?>
                        <span class="tipo_uno">
                          @foreach($data_nivelaprobacion_uno as $permiso_val)
                            <button type="button" class="btn btn-warning m-1" valor_option="{{ $permiso_val->valor }}" text_option="{{ $permiso_val->texto }}">
                              {{ $permiso_val->texto }} <span class="badge text-bg-danger" onclick="removePermiso(this)"><i class="fa-solid fa-xmark"></i></span>
                            </button>
                          @endforeach
                        </span>
                        <span class="tipo_dos">
                          @foreach($data_nivelaprobacion_dos as $permiso_val)
                            <button type="button" class="btn btn-info m-1" valor_option="{{ $permiso_val->valor }}" text_option="{{ $permiso_val->texto }}">
                              {{ $permiso_val->texto }} <span class="badge text-bg-danger" onclick="removePermiso(this)"><i class="fa-solid fa-xmark"></i></span>
                            </button>
                          @endforeach
                        </span>
                      </div>     
                      
                    </td>
                    <td class="align-top">
                      <div class="row">
                        <div class="col-12 col-md-9">
                          <label>Selección de Responsables:</label>
                          <select class="form-select" id="autonomiaadministracion_cprendario{{ $key }}" onchange="addPermisoTable(this,'#container_permiso_autonomiaadministracion{{ $key }}', $('input[name=autonomiaadministracion{{ $key }}]:checked').val() )">
                            <option></option>
                            @foreach($permisos as $value_per)
                              <option value="{{ $value_per->id }}">{{ $value_per->nombre }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-12 col-md-3">
                          <label>Opción:</label>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="autonomiaadministracion{{ $key }}" value="1" checked>
                            <label class="form-check-label">1</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="autonomiaadministracion{{ $key }}" value="2">
                            <label class="form-check-label" >2</label>
                          </div>
                        </div>
                      </div>
                      <div id="container_permiso_autonomiaadministracion{{ $key }}" data_autonomiaadministracion >
                        <?php 
                          $data_autonomiaadministracion = json_decode($value->autonomiaadministracion);
                          $data_autonomiaadministracion_uno = $data_autonomiaadministracion[0]->tipo_uno;
                          $data_autonomiaadministracion_dos = $data_autonomiaadministracion[0]->tipo_dos;
                        ?>
                        <span class="tipo_uno">
                          @foreach($data_autonomiaadministracion_uno as $permiso_val)
                            <button type="button" class="btn btn-warning m-1" valor_option="{{ $permiso_val->valor }}" text_option="{{ $permiso_val->texto }}">
                              {{ $permiso_val->texto }} <span class="badge text-bg-danger" onclick="removePermiso(this)"><i class="fa-solid fa-xmark"></i></span>
                            </button>
                          @endforeach
                        </span>
                        <span class="tipo_dos">
                          @foreach($data_autonomiaadministracion_dos as $permiso_val)
                            <button type="button" class="btn btn-info m-1" valor_option="{{ $permiso_val->valor }}" text_option="{{ $permiso_val->texto }}">
                              {{ $permiso_val->texto }} <span class="badge text-bg-danger" onclick="removePermiso(this)"><i class="fa-solid fa-xmark"></i></span>
                            </button>
                          @endforeach
                        </span>
                      </div>  

                    </td>
                    <td class="align-top">
                      <div class="row">
                        <div class="col-12 col-md-9">
                          <label>Selección de Responsables:</label>
                          <select class="form-select" id="autonomiagerencia_cprendario{{ $key }}" onchange="addPermisoTable(this,'#container_permiso_autonomiagerencia{{ $key }}', $('input[name=autonomiagerencia{{ $key }}]:checked').val() )">
                            <option></option>
                            @foreach($permisos as $value_per)
                              <option value="{{ $value_per->id }}">{{ $value_per->nombre }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-12 col-md-3">
                          <label>Opción:</label>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="autonomiagerencia{{ $key }}" value="1" checked>
                            <label class="form-check-label">1</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="autonomiagerencia{{ $key }}" value="2">
                            <label class="form-check-label" >2</label>
                          </div>
                        </div>
                      </div>
                      <div id="container_permiso_autonomiagerencia{{ $key }}" data_autonomiagerencia >
                        <?php 
                          $data_autonomiagerencia = json_decode($value->autonomiagerencia);
                          $data_autonomiagerencia_uno = $data_autonomiagerencia[0]->tipo_uno;
                          $data_autonomiagerencia_dos = $data_autonomiagerencia[0]->tipo_dos;
                        ?>
                        <span class="tipo_uno">
                          @foreach($data_autonomiagerencia_uno as $permiso_val)
                            <button type="button" class="btn btn-warning m-1" valor_option="{{ $permiso_val->valor }}" text_option="{{ $permiso_val->texto }}">
                              {{ $permiso_val->texto }} <span class="badge text-bg-danger" onclick="removePermiso(this)"><i class="fa-solid fa-xmark"></i></span>
                            </button>
                          @endforeach
                        </span>
                        <span class="tipo_dos">
                          @foreach($data_autonomiagerencia_dos as $permiso_val)
                            <button type="button" class="btn btn-info m-1" valor_option="{{ $permiso_val->valor }}" text_option="{{ $permiso_val->texto }}">
                              {{ $permiso_val->texto }} <span class="badge text-bg-danger" onclick="removePermiso(this)"><i class="fa-solid fa-xmark"></i></span>
                            </button>
                          @endforeach
                        </span>
                      </div> 
                    </td>
                    <td><a id="del{{ $key }}" href="javascript:;" onclick="eliminar_creditoprendario({{ $key }})" class="btn btn-danger btn-sm" style="padding: 4px 11px;"><i class="fa fa-close"></i></a></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            <br>
            <br>
            <table class="table table-striped table-hover table-bordered" id="table-creditosnoprendarios">
              <thead class="table-dark">
                <tr>
                  <td colspan="7">CRÉDITOS NO PRENDARIOS</td>
                </tr>
                <tr>
                  <td style="width:200px;">NIVELES DE APROBACIÓN *</td>
                  <td colspan="2" style="width:200px;">RIESGO CREDITICIO (S/.) *</td>
                  <td>COMITÉ DE APROBACIÓN</td>
                  <td>AUTONOMÍA DE ADMINITRACIÓN</td>
                  <td>AUTONOMÍA DE GERENCIA GENERAL</td>
                  <td><a href="javascript:;" class="btn btn-success" onclick="agregar_nivelaprobacion_noprendario()">
                      <i class="fa-solid fa-plus"></i>
                    </a></td>
                </tr>
              </thead>
              <tbody num="{{ count($nivelaprobacions_noprendario) }}">
                @foreach($nivelaprobacions_noprendario as $key => $value)
                  <tr id="{{ $key }}">
                    <td><input type="text" class="form-control" nombre_aprobacion value="{{ $value->nivelaprobacionnombre }}" id="nivelaprobacionnombre_noprendario{{ $key }}"></td>
                    <td style="width:120px;">
                        <div class="input-group">
                          <span class="input-group-text">></span>
                          <input type="number" step="any" riesgocredito_one value="{{ $value->riesgocredito1 }}" id="riesgocredito1_noprendario{{ $key }}" class="form-control campo_moneda">
                        </div>
                    </td>
                    <td style="width:120px;">
                        <div class="input-group">
                          <span class="input-group-text"><=</span>
                          <input type="number" step="any" riesgocredito_two value="{{ $value->riesgocredito2 }}" id="riesgocredito2_noprendario{{ $key }}" class="form-control campo_moneda">
                        </div>
                    </td>
                    <td class="align-top" >
                      <div class="row">
                        <div class="col-12 col-md-9">
                          <label>Selección de Responsables:</label>
                          <select class="form-select" id="nivelaprobacion_cprendario_noprendario{{ $key }}" onchange="addPermisoTable(this,'#container_permiso_nivelaprobacion_noprendario{{ $key }}', $('input[name=nivelaprobacion_noprendario{{ $key }}]:checked').val() )">
                            <option></option>
                            @foreach($permisos as $value_per)
                              <option value="{{ $value_per->id }}">{{ $value_per->nombre }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-12 col-md-3">
                          <label>Opción:</label>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="nivelaprobacion_noprendario{{ $key }}" value="1" checked>
                            <label class="form-check-label">1</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="nivelaprobacion_noprendario{{ $key }}" value="2">
                            <label class="form-check-label" >2</label>
                          </div>
                        </div>
                      </div>
                      <div id="container_permiso_nivelaprobacion_noprendario{{ $key }}" data_nivelaprobacion >
                        <?php 
                          $data_nivelaprobacion_noprendario = json_decode($value->nivelaprobacion);
                          $data_nivelaprobacion_noprendario_uno = $data_nivelaprobacion_noprendario[0]->tipo_uno;
                          $data_nivelaprobacion_noprendario_dos = $data_nivelaprobacion_noprendario[0]->tipo_dos;
                        ?>
                        <span class="tipo_uno">
                          @foreach($data_nivelaprobacion_noprendario_uno as $permiso_val)
                            <button type="button" class="btn btn-warning m-1" valor_option="{{ $permiso_val->valor }}" text_option="{{ $permiso_val->texto }}">
                              {{ $permiso_val->texto }} <span class="badge text-bg-danger" onclick="removePermiso(this)"><i class="fa-solid fa-xmark"></i></span>
                            </button>
                          @endforeach
                        </span>
                        <span class="tipo_dos">
                          @foreach($data_nivelaprobacion_noprendario_dos as $permiso_val)
                            <button type="button" class="btn btn-info m-1" valor_option="{{ $permiso_val->valor }}" text_option="{{ $permiso_val->texto }}">
                              {{ $permiso_val->texto }} <span class="badge text-bg-danger" onclick="removePermiso(this)"><i class="fa-solid fa-xmark"></i></span>
                            </button>
                          @endforeach
                        </span>
                      </div>                    
                    </td>
                    <td class="align-top">
                      <div class="row">
                        <div class="col-12 col-md-9">
                          <label>Selección de Responsables:</label>
                          <select class="form-select" id="autonomiaadministracion_cprendario_noprendario{{ $key }}" onchange="addPermisoTable(this,'#container_permiso_autonomiaadministracion_noprendario{{ $key }}', $('input[name=autonomiaadministracion_noprendario{{ $key }}]:checked').val() )">
                            <option></option>
                            @foreach($permisos as $value_per)
                              <option value="{{ $value_per->id }}">{{ $value_per->nombre }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-12 col-md-3">
                          <label>Opción:</label>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="autonomiaadministracion_noprendario{{ $key }}" value="1" checked>
                            <label class="form-check-label">1</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="autonomiaadministracion_noprendario{{ $key }}" value="2">
                            <label class="form-check-label" >2</label>
                          </div>
                        </div>
                      </div>
                      <div id="container_permiso_autonomiaadministracion_noprendario{{ $key }}" data_autonomiaadministracion >
                        <?php 
                          $data_autonomiaadministracion_noprendario = json_decode($value->autonomiaadministracion);
                          $data_autonomiaadministracion_noprendario_uno = $data_autonomiaadministracion_noprendario[0]->tipo_uno;
                          $data_autonomiaadministracion_noprendario_dos = $data_autonomiaadministracion_noprendario[0]->tipo_dos;
                        ?>
                        <span class="tipo_uno">
                          @foreach($data_autonomiaadministracion_noprendario_uno as $permiso_val)
                            <button type="button" class="btn btn-warning m-1" valor_option="{{ $permiso_val->valor }}" text_option="{{ $permiso_val->texto }}">
                              {{ $permiso_val->texto }} <span class="badge text-bg-danger" onclick="removePermiso(this)"><i class="fa-solid fa-xmark"></i></span>
                            </button>
                          @endforeach
                        </span>
                        <span class="tipo_dos">
                          @foreach($data_autonomiaadministracion_noprendario_dos as $permiso_val)
                            <button type="button" class="btn btn-info m-1" valor_option="{{ $permiso_val->valor }}" text_option="{{ $permiso_val->texto }}">
                              {{ $permiso_val->texto }} <span class="badge text-bg-danger" onclick="removePermiso(this)"><i class="fa-solid fa-xmark"></i></span>
                            </button>
                          @endforeach
                        </span>
                      </div>  
                    </td>
                    <td class="align-top">
                      <div class="row">
                        <div class="col-12 col-md-9">
                          <label>Selección de Responsables:</label>
                          <select class="form-select" id="autonomiagerencia_cprendario_noprendario{{ $key }}" onchange="addPermisoTable(this,'#container_permiso_autonomiagerencia_noprendario{{ $key }}', $('input[name=autonomiagerencia_noprendario{{ $key }}]:checked').val() )">
                            <option></option>
                            @foreach($permisos as $value_per)
                              <option value="{{ $value_per->id }}">{{ $value_per->nombre }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-12 col-md-3">
                          <label>Opción:</label>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="autonomiagerencia_noprendario{{ $key }}" value="1" checked>
                            <label class="form-check-label">1</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="autonomiagerencia_noprendario{{ $key }}" value="2">
                            <label class="form-check-label" >2</label>
                          </div>
                        </div>
                      </div>
                      <div id="container_permiso_autonomiagerencia_noprendario{{ $key }}" data_autonomiagerencia >
                        <?php 
                          $data_autonomiagerencia_noprendario = json_decode($value->autonomiagerencia);
                          $data_autonomiagerencia_noprendario_uno = $data_autonomiagerencia_noprendario[0]->tipo_uno;
                          $data_autonomiagerencia_noprendario_dos = $data_autonomiagerencia_noprendario[0]->tipo_dos;
                        ?>
                        <span class="tipo_uno">
                          @foreach($data_autonomiagerencia_noprendario_uno as $permiso_val)
                            <button type="button" class="btn btn-warning m-1" valor_option="{{ $permiso_val->valor }}" text_option="{{ $permiso_val->texto }}">
                              {{ $permiso_val->texto }} <span class="badge text-bg-danger" onclick="removePermiso(this)"><i class="fa-solid fa-xmark"></i></span>
                            </button>
                          @endforeach
                        </span>
                        <span class="tipo_dos">
                          @foreach($data_autonomiagerencia_noprendario_dos as $permiso_val)
                            <button type="button" class="btn btn-info m-1" valor_option="{{ $permiso_val->valor }}" text_option="{{ $permiso_val->texto }}">
                              {{ $permiso_val->texto }} <span class="badge text-bg-danger" onclick="removePermiso(this)"><i class="fa-solid fa-xmark"></i></span>
                            </button>
                          @endforeach
                        </span>
                      </div> 
                    </td>
                    <td><a id="del{{ $key }}" href="javascript:;" onclick="eliminar_creditonoprendario({{ $key }})" class="btn btn-danger btn-sm" style="padding: 4px 11px;"><i class="fa fa-close"></i></a></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
     <div class="col-sm-12 mt-2">
        
<!--         <button type="button" class="btn btn-primary" onclick="getJsonPermiso('table-creditosprendarios')"><i class="fa-solid fa-floppy-disk"></i> Demo</button> -->
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
     </div>
  </div>
  </form>
</div>
<style>
  .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
      font-size: 12px;
      padding: 0px;
      padding-left: 5px;
      padding-right: 5px;
      background-color: #eee;
  }
  .select2-container--bootstrap-5 .select2-selection--multiple .select2-search .select2-search__field{
    font-size: 14px;
  }
  .select2-container--bootstrap-5 .select2-selection--multiple .select2-search {
    display: none;
  }
  .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove {
    width: 8px;
  }
</style>
<script>

@foreach($nivelaprobacions_prendario as $value)
//     agregar_nivelaprobacion_prendario('{{$value->nivelaprobacionnombre}}','{{$value->riesgocredito1}}',
//                             '{{$value->riesgocredito2}}','{{$value->nivelaprobacion}}','{{$value->autonomiaadministracion}}',
//                             '{{$value->autonomiagerencia}}');
@endforeach
@foreach($nivelaprobacions_noprendario as $value)
//     agregar_nivelaprobacion_noprendario('{{$value->nivelaprobacionnombre}}','{{$value->riesgocredito1}}',
//                             '{{$value->riesgocredito2}}','{{$value->nivelaprobacion}}','{{$value->autonomiaadministracion}}',
//                             '{{$value->autonomiagerencia}}');
@endforeach
  
function agregar_nivelaprobacion_prendario(nivelaprobacionnombre='',riesgocredito1='',riesgocredito2='',nivelaprobacion='',autonomiaadministracion='',autonomiagerencia=''){
  
  var option_nivelaprobacion = '';
  @foreach($permisos as $value)
      var selected = '';
      var nivelaprobaciones =  nivelaprobacion.split(',');
      for(var i = 0;i <  nivelaprobaciones.length;i++){
          if({{$value->id}} == nivelaprobaciones[i]){
              selected = 'selected';
              break;
          }
      }
      option_nivelaprobacion = option_nivelaprobacion+'<option value="{{ $value->id }}" '+selected+'>{{ $value->nombre }}</option>';
  @endforeach
  
  var option_autonomiaadministracion = '';
  @foreach($permisos as $value)
      var selected = '';
      var autonomiaadministraciones =  autonomiaadministracion.split(',');
      for(var i = 0;i <  autonomiaadministraciones.length;i++){
          if({{$value->id}} == autonomiaadministraciones[i]){
              selected = 'selected';
              break;
          }
      }
      option_autonomiaadministracion = option_autonomiaadministracion+'<option value="{{ $value->id }}" '+selected+'>{{ $value->nombre }}</option>';
  @endforeach
  
  var option_autonomiagerencia = '';
  @foreach($permisos as $value)
      var selected = '';
      var autonomiagerenciaes =  autonomiagerencia.split(',');
      for(var i = 0;i <  autonomiagerenciaes.length;i++){
          if({{$value->id}} == autonomiagerenciaes[i]){
              selected = 'selected';
              break;
          }
      }
      option_autonomiagerencia = option_autonomiagerencia+'<option value="{{ $value->id }}" '+selected+'>{{ $value->nombre }}</option>';
  @endforeach
     
  var num = $("#table-creditosprendarios > tbody").attr('num');
  let btn_eliminar = `<button type="button" onclick="eliminar_producto(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button>` ;

  let tabla = ` <tr id="${num}">
                  <td><input type="text" class="form-control" nombre_aprobacion value="${nivelaprobacionnombre}" id="nivelaprobacionnombre${num}"></td>
                  <td style="width:120px;">
                      <div class="input-group">
                        <span class="input-group-text">></span>
                        <input type="number" step="any" riesgocredito_one value="${riesgocredito1}" id="riesgocredito1${num}" class="form-control campo_moneda">
                      </div>
                  </td>
                  <td style="width:120px;">
                      <div class="input-group">
                        <span class="input-group-text"><=</span>
                        <input type="number" step="any" riesgocredito_two value="${riesgocredito2}" id="riesgocredito2${num}" class="form-control campo_moneda">
                      </div>
                  </td>
                  <td class="align-top" >
                    <div class="row">
                      <div class="col-12 col-md-9">
                        <label>Selección de Responsables:</label>
                        <select class="form-select" id="nivelaprobacion_cprendario${num}" onchange="addPermisoTable(this,'#container_permiso_nivelaprobacion${num}', $('input[name=nivelaprobacion${num}]:checked').val() )">
                          ${option_nivelaprobacion}
                        </select>
                      </div>
                      <div class="col-12 col-md-3">
                          <label>Opción:</label>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="nivelaprobacion${num}" value="1" checked>
                          <label class="form-check-label">1</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="nivelaprobacion${num}" value="2">
                          <label class="form-check-label" >2</label>
                        </div>
                      </div>
                    </div>
                    <div id="container_permiso_nivelaprobacion${num}" data_nivelaprobacion >
                      <span class="tipo_uno"></span>
                      <span class="tipo_dos"></span>
                    </div>                    

                    <select class="form-select d-none" id="nivelaprobacion${num}" multiple="multiple">
                      ${option_nivelaprobacion}
                    </select>
                  </td>
                  <td class="align-top">
                    <div class="row">
                      <div class="col-12 col-md-9">
                        <label>Selección de Responsables:</label>
                        <select class="form-select" id="autonomiaadministracion_cprendario${num}" onchange="addPermisoTable(this,'#container_permiso_autonomiaadministracion${num}', $('input[name=autonomiaadministracion${num}]:checked').val() )">
                          ${option_autonomiaadministracion}
                        </select>
                      </div>
                      <div class="col-12 col-md-3">
                          <label>Opción:</label>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="autonomiaadministracion${num}" value="1" checked>
                          <label class="form-check-label">1</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="autonomiaadministracion${num}" value="2">
                          <label class="form-check-label" >2</label>
                        </div>
                      </div>
                    </div>
                    <div id="container_permiso_autonomiaadministracion${num}" data_autonomiaadministracion >
                      <span class="tipo_uno"></span>
                      <span class="tipo_dos"></span>
                    </div>  

                    <select class="form-select d-none" id="autonomiaadministracion${num}" multiple="multiple">
                      ${option_autonomiaadministracion}
                    </select>
                  </td>
                  <td class="align-top">
                    <div class="row">
                      <div class="col-12 col-md-9">
                        <label>Selección de Responsables:</label>
                        <select class="form-select" id="autonomiagerencia_cprendario${num}" onchange="addPermisoTable(this,'#container_permiso_autonomiagerencia${num}', $('input[name=autonomiagerencia${num}]:checked').val() )">
                          ${option_autonomiagerencia}
                        </select>
                      </div>
                      <div class="col-12 col-md-3">
                          <label>Opción:</label>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="autonomiagerencia${num}" value="1" checked>
                          <label class="form-check-label">1</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="autonomiagerencia${num}" value="2">
                          <label class="form-check-label" >2</label>
                        </div>
                      </div>
                    </div>
                    <div id="container_permiso_autonomiagerencia${num}" data_autonomiagerencia >
                      <span class="tipo_uno"></span>
                      <span class="tipo_dos"></span>
                    </div> 
                    <select class="form-select d-none" id="autonomiagerencia${num}" multiple="multiple">
                      ${option_autonomiagerencia}
                    </select>
                  </td>
                  <td><a id="del${num}" href="javascript:;" onclick="eliminar_creditoprendario(${num})" class="btn btn-danger btn-sm" style="padding: 4px 11px;"><i class="fa fa-close"></i></a></td>
                </tr>`;
  
    $("#table-creditosprendarios > tbody").append(tabla);
    $("#table-creditosprendarios > tbody").attr('num',parseInt(num)+1);  
  
  
//   sistema_select2({ input:'#nivelaprobacion'+num });
  
//   sistema_select2({ input:'#autonomiaadministracion'+num });
//   sistema_select2({ input:'#autonomiagerencia'+num });
}
function addPermisoTable(e,container,tipo){
  
  let target_container = tipo == 1 ? 'tipo_uno' : 'tipo_dos';
  let color_badge = tipo == 2 ? 'info' : 'warning';
  let valOption = e.value;
  let textOption = e.options[e.selectedIndex].text;
  //console.log(textOption)
  if(textOption!=''){
  let badge = `<button type="button" class="btn btn-${color_badge} m-1" valor_option="${valOption}" text_option="${textOption}">
                  ${textOption} <span class="badge text-bg-danger" onclick="removePermiso(this)"><i class="fa-solid fa-xmark"></i></span>
                </button>`;
  
  $(container).find('.'+target_container).append(badge);
  }
  
  
  var idd = $('#'+e.id).val();
  if(idd!=''){
  $('#'+e.id).val(null).trigger("change");
  }
  
}
function removePermiso(e){
    var opcion = confirm("¿Esta seguro de eliminar?");
    if (opcion == true) {
          $(e).closest('button[type="button"]').remove();
    }
  
}
  
function getJsonPermiso(table){
  let data = [];
  $(`#${table} > tbody > tr`).each(function() {
    let nombre_aprobacion = $(this).find('input[nombre_aprobacion]').val();
    let riesgocredito_one = $(this).find('input[riesgocredito_one]').val();
    let riesgocredito_two = $(this).find('input[riesgocredito_two]').val();
    // ONE TD
    let data_nivelaprobacion = [];
    
    let data_nivelaprobacion_one = [];
    $(this).find('div[data_nivelaprobacion] > span.tipo_uno > button').each(function() {
      let valor = $(this).attr('valor_option');
      let texto = $(this).attr('text_option');
      data_nivelaprobacion_one.push({ 
            valor: valor,
            texto: texto,
        });
    });
    let data_nivelaprobacion_two = [];
    $(this).find('div[data_nivelaprobacion] > span.tipo_dos > button').each(function() {
      let valor = $(this).attr('valor_option');
      let texto = $(this).attr('text_option');
      data_nivelaprobacion_two.push({ 
            valor: valor,
            texto: texto,
        });
    });
    
    data_nivelaprobacion.push({
      tipo_uno: data_nivelaprobacion_one,
      tipo_dos: data_nivelaprobacion_two,
    });
    // TWO TD
    
    let data_autonomiaadministracion = [];
    
    let data_autonomiaadministracion_one = [];
    $(this).find('div[data_autonomiaadministracion] > span.tipo_uno > button').each(function() {
      let valor = $(this).attr('valor_option');
      let texto = $(this).attr('text_option');
      data_autonomiaadministracion_one.push({ 
            valor: valor,
            texto: texto,
        });
    });
    let data_autonomiaadministracion_two = [];
    $(this).find('div[data_autonomiaadministracion] > span.tipo_dos > button').each(function() {
      let valor = $(this).attr('valor_option');
      let texto = $(this).attr('text_option');
      data_autonomiaadministracion_two.push({ 
            valor: valor,
            texto: texto,
        });
    });
    
    data_autonomiaadministracion.push({
      tipo_uno: data_autonomiaadministracion_one,
      tipo_dos: data_autonomiaadministracion_two,
    });
    // THREE TD
    let data_autonomiagerencia = [];
    
    let data_autonomiagerencia_one = [];
    $(this).find('div[data_autonomiagerencia] > span.tipo_uno > button').each(function() {
      let valor = $(this).attr('valor_option');
      let texto = $(this).attr('text_option');
      data_autonomiagerencia_one.push({ 
            valor: valor,
            texto: texto,
        });
    });
    let data_autonomiagerencia_two = [];
    $(this).find('div[data_autonomiagerencia] > span.tipo_dos > button').each(function() {
      let valor = $(this).attr('valor_option');
      let texto = $(this).attr('text_option');
      data_autonomiagerencia_two.push({ 
            valor: valor,
            texto: texto,
        });
    });
    
    data_autonomiagerencia.push({
      tipo_uno: data_autonomiagerencia_one,
      tipo_dos: data_autonomiagerencia_two,
    });
    
    

    // Agregar data_nivelaprobacion a data
    data.push({
      nombre_aprobacion: nombre_aprobacion,
      riesgocredito_one: riesgocredito_one,
      riesgocredito_two: riesgocredito_two,
      data_nivelaprobacion: data_nivelaprobacion,
      data_autonomiaadministracion: data_autonomiaadministracion,
      data_autonomiagerencia: data_autonomiagerencia,
    });
    
  });
  return JSON.stringify(data);
}
  
function agregar_nivelaprobacion_noprendario(nivelaprobacionnombre='',riesgocredito1='',riesgocredito2='',nivelaprobacion='',autonomiaadministracion='',autonomiagerencia=''){
  
  var option_nivelaprobacion = '<option></option>';
  @foreach($permisos as $value)
      var selected = '';
      var nivelaprobaciones =  nivelaprobacion.split(',');
      for(var i = 0;i <  nivelaprobaciones.length;i++){
          if({{$value->id}} == nivelaprobaciones[i]){
              selected = 'selected';
              break;
          }
      }
      option_nivelaprobacion = option_nivelaprobacion+'<option value="{{ $value->id }}" '+selected+'>{{ $value->nombre }}</option>';
  @endforeach
  
  var option_autonomiaadministracion = '<option></option>';
  @foreach($permisos as $value)
      var selected = '';
      var autonomiaadministraciones =  autonomiaadministracion.split(',');
      for(var i = 0;i <  autonomiaadministraciones.length;i++){
          if({{$value->id}} == autonomiaadministraciones[i]){
              selected = 'selected';
              break;
          }
      }
      option_autonomiaadministracion = option_autonomiaadministracion+'<option value="{{ $value->id }}" '+selected+'>{{ $value->nombre }}</option>';
  @endforeach
  
  var option_autonomiagerencia = '<option></option>';
  @foreach($permisos as $value)
      var selected = '';
      var autonomiagerenciaes =  autonomiagerencia.split(',');
      for(var i = 0;i <  autonomiagerenciaes.length;i++){
          if({{$value->id}} == autonomiagerenciaes[i]){
              selected = 'selected';
              break;
          }
      }
      option_autonomiagerencia = option_autonomiagerencia+'<option value="{{ $value->id }}" '+selected+'>{{ $value->nombre }}</option>';
  @endforeach
     
  var num = $("#table-creditosnoprendarios > tbody").attr('num');
  let btn_eliminar = `<button type="button" onclick="eliminar_producto(this)" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button>` ;

  let tabla = ` <tr id="${num}">
                  <td><input type="text" class="form-control" nombre_aprobacion value="${nivelaprobacionnombre}" id="nivelaprobacionnombre_noprendario${num}"></td>
                  <td style="width:120px;">
                      <div class="input-group">
                        <span class="input-group-text">></span>
                        <input type="number" step="any" riesgocredito_one value="${riesgocredito1}" id="riesgocredito1_noprendario${num}" class="form-control campo_moneda">
                      </div>
                  </td>
                  <td style="width:120px;">
                      <div class="input-group">
                        <span class="input-group-text"><=</span>
                        <input type="number" step="any" riesgocredito_two value="${riesgocredito2}" id="riesgocredito2_noprendario${num}" class="form-control campo_moneda">
                      </div>
                  </td>
                  <td class="align-top" >
                    <div class="row">
                      <div class="col-12 col-md-9">
                        <label>Selección de Responsables:</label>
                        <select class="form-select" id="nivelaprobacion_cprendario_noprendario${num}" onchange="addPermisoTable(this,'#container_permiso_nivelaprobacion_noprendario${num}', $('input[name=nivelaprobacion_noprendario${num}]:checked').val() )">
                          ${option_nivelaprobacion}
                        </select>
                      </div>
                      <div class="col-12 col-md-3">
                          <label>Opción:</label>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="nivelaprobacion_noprendario${num}" value="1" checked>
                          <label class="form-check-label">1</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="nivelaprobacion_noprendario${num}" value="2">
                          <label class="form-check-label" >2</label>
                        </div>
                      </div>
                    </div>
                    <div id="container_permiso_nivelaprobacion_noprendario${num}" data_nivelaprobacion >
                      <span class="tipo_uno"></span>
                      <span class="tipo_dos"></span>
                    </div>                    

                    <select class="form-select d-none" id="nivelaprobacion_noprendario${num}" multiple="multiple">
                      ${option_nivelaprobacion}
                    </select>
                  </td>
                  <td class="align-top">
                    <div class="row">
                      <div class="col-12 col-md-9">
                        <label>Selección de Responsables:</label>
                        <select class="form-select" id="autonomiaadministracion_cprendario_noprendario${num}" onchange="addPermisoTable(this,'#container_permiso_autonomiaadministracion_noprendario${num}', $('input[name=autonomiaadministracion_noprendario${num}]:checked').val() )">
                          ${option_autonomiaadministracion}
                        </select>
                      </div>
                      <div class="col-12 col-md-3">
                          <label>Opción:</label>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="autonomiaadministracion_noprendario${num}" value="1" checked>
                          <label class="form-check-label">1</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="autonomiaadministracion_noprendario${num}" value="2">
                          <label class="form-check-label" >2</label>
                        </div>
                      </div>
                    </div>
                    <div id="container_permiso_autonomiaadministracion_noprendario${num}" data_autonomiaadministracion >
                      <span class="tipo_uno"></span>
                      <span class="tipo_dos"></span>
                    </div>  

                    <select class="form-select d-none" id="autonomiaadministracion_noprendario${num}" multiple="multiple">
                      ${option_autonomiaadministracion}
                    </select>
                  </td>
                  <td class="align-top">
                    <div class="row">
                      <div class="col-12 col-md-9">
                        <label>Selección de Responsables:</label>
                        <select class="form-select" id="autonomiagerencia_cprendario_noprendario${num}" onchange="addPermisoTable(this,'#container_permiso_autonomiagerencia_noprendario${num}', $('input[name=autonomiagerencia_noprendario${num}]:checked').val() )">
                          ${option_autonomiagerencia}
                        </select>
                      </div>
                      <div class="col-12 col-md-3">
                          <label>Opción:</label>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="autonomiagerencia_noprendario${num}" value="1" checked>
                          <label class="form-check-label">1</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="autonomiagerencia_noprendario${num}" value="2">
                          <label class="form-check-label" >2</label>
                        </div>
                      </div>
                    </div>
                    <div id="container_permiso_autonomiagerencia_noprendario${num}" data_autonomiagerencia >
                      <span class="tipo_uno"></span>
                      <span class="tipo_dos"></span>
                    </div> 
                    <select class="form-select d-none" id="autonomiagerencia_noprendario${num}" multiple="multiple">
                      ${option_autonomiagerencia}
                    </select>
                  </td>
                  <td><a id="del${num}" href="javascript:;" onclick="eliminar_creditonoprendario(${num})" class="btn btn-danger btn-sm" style="padding: 4px 11px;"><i class="fa fa-close"></i></a></td>
                </tr>`;
    /*
    let tabla = ` <tr id="${num}">
                  <td><input type="text" class="form-control" value="${nivelaprobacionnombre}" id="nivelaprobacionnombre_noprendario${num}"></td>
                  <td style="width:120px;">
                      <div class="input-group">
                        <span class="input-group-text">></span>
                        <input type="number" step="any" value="${riesgocredito1}" id="riesgocredito1_noprendario${num}" class="form-control campo_moneda">
                      </div>
                  </td>
                  <td style="width:120px;">
                      <div class="input-group">
                        <span class="input-group-text"><=</span>
                        <input type="number" step="any" value="${riesgocredito2}" id="riesgocredito2_noprendario${num}" class="form-control campo_moneda">
                      </div>
                  </td>
                  <td>
                    <select class="form-select" id="nivelaprobacion_noprendario${num}" multiple="multiple">
                      ${option_nivelaprobacion}
                    </select>
                  </td>
                  <td>
                    <select class="form-select" id="autonomiaadministracion_noprendario${num}" multiple="multiple">
                      ${option_autonomiaadministracion}
                    </select>
                  </td>
                  <td>
                    <select class="form-select" id="autonomiagerencia_noprendario${num}" multiple="multiple">
                      ${option_autonomiagerencia}
                    </select>
                  </td>
                  <td><a id="del${num}" href="javascript:;" onclick="eliminar_creditonoprendario(${num})" class="btn btn-danger btn-sm" style="padding: 4px 11px;"><i class="fa fa-close"></i></a></td>
                </tr>`;
    */
    $("#table-creditosnoprendarios > tbody").append(tabla);
    $("#table-creditosnoprendarios > tbody").attr('num',parseInt(num)+1);  

//   sistema_select2({ input:'#nivelaprobacion_noprendario'+num });
//   sistema_select2({ input:'#autonomiaadministracion_noprendario'+num });
//   sistema_select2({ input:'#autonomiagerencia_noprendario'+num });
}

function select_creditosprendarios(){
    var data = '';
    $("#table-creditosprendarios > tbody > tr").each(function() {
        var num = $(this).attr('id');        
        var nivelaprobacionnombre = $("#nivelaprobacionnombre"+num).val();
        var riesgocredito1 = $("#riesgocredito1"+num).val();
        var riesgocredito2 = $("#riesgocredito2"+num).val();
        var nivelaprobacion = $("#nivelaprobacion"+num).val();
        var autonomiaadministracion = $("#autonomiaadministracion"+num).val();
        var autonomiagerencia = $("#autonomiagerencia"+num).val();
        data = data+'/&/'+nivelaprobacionnombre+'/,/'+riesgocredito1+'/,/'+riesgocredito2+'/,/'+nivelaprobacion+'/,/'+autonomiaadministracion+'/,/'+autonomiagerencia;
    });
    return data;
}  
function select_creditosnoprendarios(){
    var data = '';
    $("#table-creditosnoprendarios > tbody > tr").each(function() {
        var num = $(this).attr('id');        
        var nivelaprobacionnombre = $("#nivelaprobacionnombre_noprendario"+num).val();
        var riesgocredito1 = $("#riesgocredito1_noprendario"+num).val();
        var riesgocredito2 = $("#riesgocredito2_noprendario"+num).val();
        var nivelaprobacion = $("#nivelaprobacion_noprendario"+num).val();
        var autonomiaadministracion = $("#autonomiaadministracion_noprendario"+num).val();
        var autonomiagerencia = $("#autonomiagerencia_noprendario"+num).val();
        data = data+'/&/'+nivelaprobacionnombre+'/,/'+riesgocredito1+'/,/'+riesgocredito2+'/,/'+nivelaprobacion+'/,/'+autonomiaadministracion+'/,/'+autonomiagerencia;
    });
    return data;
}  
  
function eliminar_creditoprendario(num){
    var opcion = confirm("¿Esta seguro de eliminar?");
    if (opcion == true) {
          $("#table-creditosprendarios tbody tr#"+num).remove();
    }
    
}
function eliminar_creditonoprendario(num){
    var opcion = confirm("¿Esta seguro de eliminar?");
    if (opcion == true) {
          $("#table-creditosnoprendarios tbody tr#"+num).remove();
    }
    
}
</script>
