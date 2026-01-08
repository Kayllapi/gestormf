<div id="carga_cambiar_estado">
<form action="javascript:;" id="form_cambiar_estado">
    <style>
      .form-check-label {
          margin-top: 5px;
          margin-left: 5px;
      }
    </style>
    <div class="modal-header">
        <h5 class="modal-title">ESTADO DE CRÉDITO</h5>
        <button type="button" class="btn-close" id="modal-close-cambiar-estado" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
      <div class="row" style="font-size: 14px;padding: 7px;">
          <div class="col-md-6"><b>CLIENTE:</b> {{ $usuario->nombrecompleto }}</div>
          <div class="col-md-6" style="text-align: right;"><b>PRODUCTO:</b> {{ $credito->nombreproductocredito }}</div>
          <div class="col-md-3"><b>MONTO DE PRÉSTAMO:</b>S/. {{ $credito->monto_solicitado }}</div>
          <div class="col-md-2"><b>FORMA DE PAGO:</b> {{ $credito->forma_pago_credito_nombre }}</div>
          <div class="col-md-2"><b>N° DE CUOTAS:</b> {{ $credito->cuotas }}</div>
          <div class="col-md-3"><b>N° DE SOLICITUD:</b> S{{ str_pad($credito->id, 8, "0", STR_PAD_LEFT) }}</div>
      </div>
      @if( $credito->estado == 'APROBADO' && $estado == 'APROBADO')
        
        <div class="alert alert-success">
          <i class="fa-solid fa-check"></i>
          <b>¡CRÉDITO APROBADO!</b>
        </div>
      <br>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th colspan=3 class="text-center">Detalle de Aprobación</th>
            </tr>
            <tr>
              <th width="10px">#</th>
              <th>Cargo</th>
              <th>Personal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($credito_aprobacion as $key => $value)
            <tr>
              <td>{{ ($key+1) }}</td>
              <td>{{ $value->nombre_permiso }}</td>
              <td>{{ $value->nombre_usuario }}</td>
            </tr>
            @endforeach
            
          </tbody>
        </table>
      
      @else
        @if($estado == 'APROBADO')
          <div class="row">
              <div class="col-md-4">
                <div class="mb-1">
                    <label style="background-color: #636363;
    color: #fff;
    width: 100%;
    border-radius: 5px;
    padding: 0px 5px;
    margin-bottom: 5px;">Seleccionar Nivel de Aprobación</label>
                    <select class="form-control" id="tipo_validacion" onchange="mostrar_permisos(this.value)" {{ $credito->aprobacion_tipo_validacion!='' && count($credito_aprobacion)>0?'disabled':'' }}>
                      <option disabled selected> -- Seleccione una opcion -- </option>
                      <option value="nivelaprobacion" {{ $credito->aprobacion_tipo_validacion=='nivelaprobacion' && count($credito_aprobacion)>0?'selected':'' }}>1. COMITÉ DE APROBACIÓN</option>
                      <option value="autonomiaadministracion" {{ $credito->aprobacion_tipo_validacion=='autonomiaadministracion' && count($credito_aprobacion)>0?'selected':'' }}>2. AUTONOMÍA DE ADMINITRACIÓN</option>
                      <option value="autonomiagerencia" {{ $credito->aprobacion_tipo_validacion=='autonomiagerencia' && count($credito_aprobacion)>0?'selected':'' }}>3. AUTONOMÍA DE GERENCIA GENERAL</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label style="background-color: #636363;
    color: #fff;
    width: 100%;
    border-radius: 5px;
    padding: 0px 5px;
    margin-bottom: 5px;">Nivel</label>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="exampleRadios" id="check_uno_table" 
                                 value="table_uno" {{ $credito->aprobacion_nivel_validacion==1 && count($credito_aprobacion)>0?'checked':'' }} {{ $credito->aprobacion_nivel_validacion!=0 && count($credito_aprobacion)>0?'disabled':'' }}>
                          <label class="form-check-label" for="check_uno_table">
                            NIVEL 1
                          </label>
                        </div>
                  
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="exampleRadios" id="check_dos_table" 
                                 value="table_dos" {{ $credito->aprobacion_nivel_validacion==2 && count($credito_aprobacion)>0?'checked':'' }} {{ $credito->aprobacion_nivel_validacion!=0 && count($credito_aprobacion)>0?'disabled':'' }}>
                          <label class="form-check-label" for="check_dos_table">
                            NIVEL 2
                          </label>
                        </div>
                </div>
              
              </div>
              <div class="col-md-8">
                  <div class="mb-1">
                    <label style="background-color: #636363;
                      color: #fff;
                      width: 100%;
                      border-radius: 5px;
                      padding: 0px 5px;
                      margin-bottom: 5px;text-align: center;">ACTA DE APROBACIÓN</label>
                </div>
                <div class="row">
                  <div class="col-sm-4">
                    <div class="mb-1">
                        <label>EXEPCIONES Y AUTORIZACIONES</label>
                        <textarea class="form-control" disabled cols="30" rows="5">{{ $credito->excepcionesautorizaciones }}</textarea>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="mb-1">
                        <label>OPINIÓN DE AREA DE RIESGOS</label>
                        <textarea class="form-control" disabled cols="30" rows="5">{{ $credito->areariesgos }}</textarea>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="mb-1">
                        <label>COMENTARIO DE VISITAS Y/O VERIFICACIÓN</label>
                        <textarea class="form-control" disabled cols="30" rows="5">{{ $credito->comentariovisita }}</textarea>
                    </div>
                  </div>
                </div>
              </div>
              @if($credito->comentariovisita!='' or $credito->idforma_credito==1)
                  @if($credito->aprobacion_tipo_validacion!='' && $credito->aprobacion_nivel_validacion!=0 && count($credito_aprobacion)>0 )
                  <div class="col-sm-12 col-md-12">
                    <table class="table" id="table-permisos-nivel-uno">
                      <thead>
                        <tr>
                          <th width="220px">Permiso</th>
                          <th width="300px">Usuario</th>
                          <th width="150px">Clave</th>
                          <th width="220px"></th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($credito_aprobacion as $value)
                          <?php
                          $disabled = '';
                          $color_cajatexto = 'color_cajatexto';
                          if($value->idusers!=0){
                            $disabled = 'disabled';
                            $color_cajatexto = '';
                          }

                          $usuario_permiso = DB::table('users_permiso')
                                        ->join('users','users.id','users_permiso.idusers')
                                        ->join('permiso','permiso.id','users_permiso.idpermiso')
                                        ->where('users_permiso.idpermiso',$value->idpermiso)
                                        ->where('users_permiso.idtienda',$tienda->id)
                                        ->select(
                                          'users_permiso.*',
                                          DB::raw('CONCAT(users.nombrecompleto," (",permiso.nombre,")") as nombre_personal')
                                        )
                                        ->get();
                          ?>
                            <tr id="{{ $value->id }}" idpermiso="{{ $value->idpermiso }}" idestado="{{ $value->idestado }}">
                              <td><span class="badge bg-warning">{{ $value->nombre_permiso }}</span></td>
                              <td>
                              @if($value->idusers!=0)
                                <select class="form-control" id="per_usuario{{ $value->id }}" usuario {{$disabled}}>
                                    <option value="{{ $value->idusers }}" idpermiso="{{ $value->idpermiso }}" idestado="{{ $value->idestado }}">{{ $value->nombre_usuario }}</option>
                                </select>
                              @else
                                <select class="form-control" id="per_usuario{{ $value->id }}" usuario {{$disabled}}>
                                    <option disabled selected> -- Seleccionar Responsable -- </option>
                                    @foreach($usuario_permiso as $valueusers)
                                    <option value="{{ $valueusers->idusers }}" idpermiso="{{ $valueusers->idpermiso }}">{{ $valueusers->nombre_personal }}</option>
                                    @endforeach
                                </select>
                              @endif
                              </td>
                              <td><input type="password" value="{{ $value->clave_usuario }}" password_users id="per_clave{{ $value->id }}" class="form-control text-center" {{$disabled}}></td>
                              @if($value->idusers!=0)
                                  @if($value->idestado==1)
                                  <td id="resultado_cambiar_permiso{{ $value->id }}">
                                    <div style="background-color: #198754;padding: 7px;border-radius: 5px;color: #fff;text-align: center;font-weight: bold;">CORRECTO</div></td>
                                  @elseif($value->idestado==2)
                                  <td id="resultado_cambiar_permiso{{ $value->id }}">
                                    <div style="background-color: #dc3545;padding: 7px;border-radius: 5px;color: #fff;text-align: center;font-weight: bold;">ANULADO</div></td>
                                  @endif
                              @else
                              <td id="resultado_cambiar_permiso{{ $value->id }}" style="240px">
                                <button type="button" class="btn btn-warning" onclick="validarclave({{ $value->id }},1,'#table-permisos-nivel-uno')"><i class="fa-solid fa-check"></i> APROBAR</button>
                                <button type="button" class="btn btn-danger" onclick="validarclave({{ $value->id }},2,'#table-permisos-nivel-uno')"><i class="fa-solid fa-ban"></i> DESAPROBAR</button>
                              </td>
                              @endif
                              <td><input type="text" comentario_users id="per_comentario{{ $value->id }}" value="{{ $value->comentario }}" class="form-control {{$color_cajatexto}}" {{$disabled}}></td>
                            </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  @else
                  <div class="col-sm-12 col-md-12">
                    <div id="cont_permiso_nivel_uno" style="display:none;">
                    <table class="table" id="table-permisos-nivel-uno">
                      <thead>
                        <tr>
                          <th width="220px">Permiso</th>
                          <th width="300px">Usuario</th>
                          <th width="150px">Clave</th>
                          <th width="220px"></th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>

                      </tbody>
                    </table>
                    </div>
                    <div id="cont_permiso_nivel_dos" style="display:none;">
                    <table class="table" id="table-permisos-nivel-dos">
                      <thead>
                        <tr>
                          <th>Permiso</th>
                          <th>Usuario</th>
                          <th width="150px">Clave</th>
                          <th width="220px"></th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                    </div>
                  </div>
                  @endif
              @else
                        <p class="text-center" 
                           style="background-color: #dc3545;
                                  padding: 10px;
                                  border-radius: 5px;
                                  color: #fff;
                                  width: 80%;
                                  margin: auto;">El Comentario de Visitas es Obligatorio.</p>
                  
              @endif
          </div>
                
        @elseif($estado == 'APROBADO')
          <p class="text-center">¿Seguro que desea pasar el crédito a <b>{{ $estado }}</b>?</p>
          <div class="col-sm-12 mt-2 text-center">
            <button type="submit" class="btn btn-success"><i class="fa-solid fa-check"></i> APROBAR CRÉDITO</button>
          </div>
        @elseif($estado == 'ELIMINAR' )
          <?php
            $credito_cobranzacuota = DB::table('credito_cobranzacuota')
              ->where('credito_cobranzacuota.idcredito',$credito->id)
              ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
              ->count();
            $fecha = Carbon\Carbon::now()->format('Y-m-d');
            $ultimafecha = date_format(date_create($credito->fecha_desembolso),"Y-m-d");
          ?>
          @if($fecha!=$ultimafecha)
                        <p class="text-center" 
                           style="background-color: #dc3545;
                                  padding: 10px;
                                  border-radius: 5px;
                                  color: #fff;
                                  width: 100%;
                                  margin: auto;">El Crédito esta fuera de fecha, no es posible ELIMINAR.</p>
          @elseif($credito_cobranzacuota>0)
                        <p class="text-center" 
                           style="background-color: #dc3545;
                                  padding: 10px;
                                  border-radius: 5px;
                                  color: #fff;
                                  width: 100%;
                                  margin: auto;">El Crédito tiene una cobranza, no es posible ELIMINAR.</p>

          @else
      
              @if($permiso=='institucional')
              <div class="row">
                  <div class="col-sm-4"> </div>
                  <div class="col-sm-4 mt-2"> 
                  <label class="mt-1" style="background-color: #636363;
                    color: #fff;
                    width: 100%;
                    border-radius: 5px;
                    padding: 0px 5px;
                    margin-bottom: 5px;">Aprobación</label>
                        <div class="mb-1">
                            <label>Responsable (Gerencia General) *</label>
                            <select class="form-select" id="idresponsable">
                                <option value=""></option>
                                @foreach($usuarios as $value)
                                <option value="{{$value->id}}">{{$value->nombretienda}} - {{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-1">
                            <label>Contraseña *</label>
                            <input type="password" class="form-control" id="responsableclave">
                        </div>
                  </div>
               </div>
              <p class="text-center">¿Seguro que desea eliminar el crédito?</p>
              <div class="col-sm-12 mt-2 text-center">
                <button type="submit" class="btn btn-danger"  onclick="cambiarestado()"><i class="fa-solid fa-check"></i> ELIMINAR CRÉDITO</button>
              </div>
              @elseif($permiso=='administrador')
              @if($credito->estado=='DESEMBOLSADO')
                        <p class="text-center" 
                           style="background-color: #dc3545;
                                  padding: 10px;
                                  border-radius: 5px;
                                  color: #fff;
                                  width: 100%;
                                  margin: auto;">Usted no tiene permiso para ELIMINAR.</p>
              @else
              <p class="text-center">¿Seguro que desea eliminar el crédito?</p>
              <div class="col-sm-12 mt-2 text-center">
                <button type="submit" class="btn btn-danger"  onclick="cambiarestado()"><i class="fa-solid fa-check"></i> ELIMINAR CRÉDITO</button>
              </div>
              @endif
              @endif
          @endif
             
        @else
          
          @if($credito->estado=='DESEMBOLSADO')
                        <p class="text-center" 
                           style="background-color: #dc3545;
                                  padding: 10px;
                                  border-radius: 5px;
                                  color: #fff;
                                  width: 100%;
                                  margin: auto;">Un Crédito desembolsado no es posible pasar a GENERAR CRÉDITO.</p>
          @else
          <p class="text-center">¿Seguro que desea pasar a <b>"GENERAR CRÉDITO"</b>?</p>
          <div class="col-sm-12 mt-2 text-center">
            <button type="button" class="btn btn-success" onclick="cambiarestado()"><i class="fa-solid fa-check"></i> PASAR A GENERAR CRÉDITO</button>
          </div>
          @endif
        @endif
      @endif
      
      
    </div>
</form>   
</div>
<style>
  #success-message{
    background: #00a759;
    color: white;
    font-weight: bold;
  }
  .doble-subrayado {
    text-decoration: underline double;
  }
  .single-subrayado {
    text-decoration: underline;
  }
  .form-check .form-check-input {
    float: left;
    margin-left: -0.7em;
    font-size: 1rem;
  }

</style>
<script>
    sistema_select2({ input:'#idresponsable' });
  @if($credito->aprobacion_tipo_validacion!='' && $credito->aprobacion_nivel_validacion!=0)
  //setTimeout(function() {
      /*mostrar_permisos('{{$credito->aprobacion_tipo_validacion}}');
  
        let nivel_uno = $('#check_uno_table:checked').val();
        let nivel_dos = $('#check_dos_table:checked').val();
    
        let valCheck = '';
        if(nivel_uno=='table_uno'){
            valCheck = 'table_uno';
        }
        if(nivel_dos=='table_dos'){
            valCheck = 'table_dos';
        }*/
  
    //let valCheck = $(this).val();
    /*let tableDosInputs = $('#table-permisos-nivel-dos > tbody input');
    let tableDosSelects = $('#table-permisos-nivel-dos > tbody select');
    let tableUnoInputs = $('#table-permisos-nivel-uno > tbody input');
    let tableUnoSelects = $('#table-permisos-nivel-uno > tbody select');

    tableDosInputs.add(tableDosSelects).add(tableUnoInputs).add(tableUnoSelects).attr('disabled', false);

    $('#cont_permiso_nivel_uno').css('display','none');
    $('#cont_permiso_nivel_dos').css('display','none');
    if (valCheck === 'table_uno') {
        tableDosInputs.add(tableDosSelects).attr('disabled', true);
        $('#cont_permiso_nivel_uno').css('display','block');
        $('#cont_permiso_nivel_dos').css('display','none');
    } else {
        tableUnoInputs.add(tableUnoSelects).attr('disabled', true);
        $('#cont_permiso_nivel_uno').css('display','none');
        $('#cont_permiso_nivel_dos').css('display','block');
    }
    }, 500);*/
  
      
  @endif
  function mostrar_permisos(valor){
    
    $.ajax({
      url:"{{url('backoffice/'.$tienda->id.'/propuestacredito/showpermisos')}}",
      type:'GET',
      data: {
          idnivelaprobacion : '{{ $nivel_aprobacion!='' ? $nivel_aprobacion->id:0 }}',
          idcredito : '{{ $credito->id }}',
          campo : valor
      },
      success: function (res){
      //console.log(res)
        let tr_data_uno = ``;
        let tr_data_dos = ``;
        let data_uno = [];
        let data_dos = [];
        if( valor == 'nivelaprobacion'){
          data_uno = res.option_nivelaprobacion_user_uno;
          
          data_dos = res.option_nivelaprobacion_user_dos;
        }
        else if( valor == 'autonomiaadministracion'){
          data_uno = res.option_autonomiaadministracion_user_uno;
          data_dos = res.option_autonomiaadministracion_user_dos;
        }
        else if( valor == 'autonomiagerencia'){
          data_uno = res.option_autonomiagerencia_user_uno;
          data_dos = res.option_autonomiagerencia_user_dos;
        }
       
        creaTablaPermisos(data_uno,'#table-permisos-nivel-uno',res);
        creaTablaPermisos(data_dos,'#table-permisos-nivel-dos',res);
      }
    });
    
  }
  function creaTablaPermisos(data, target,res){
    //console.log(res.usuario)
    //$(target).removeClass('d-none');
    //$('#check_uno_table').prop('checked', true);
    /*let tr_body = `<tr id="0" idpermiso="${res.usuario.idpermiso}" idestado="0">
                          <td><span class="badge bg-warning">${res.usuario.permiso}</span></td>
                          <td><select class="form-control" id="per_usuario0" usuario disabled>
                            <option value="${res.usuario.id}" idpermiso="${res.usuario.idpermiso}" idestado="0" selected>${res.usuario.nombrecompleto}</option>
                          </select></td>
                          <td><input type="password" password_users id="per_clave0" class="form-control text-center"></td>
                          <td id="resultado_cambiar_permiso0">
                            <button type="button" class="btn btn-warning" onclick="validarclave(0,1,'${target}')">
                            <i class="fa-solid fa-check"></i> APROBAR</button>
                            <button type="button" class="btn btn-danger" onclick="validarclave(0,2,'${target}')">
                            <i class="fa-solid fa-ban"></i> DESAPROBAR</button></td>
                          <td><input type="text" comentario_users id="per_comentario0" class="form-control color_cajatexto"></td>
                        </tr>`;*/
    let tr_body = '';
    let num = 1;
    //console.log(data)
    data.forEach((valor, index) => {
 
        let array_usuarios = valor.usuarios;
        let option_usuario = `<option disabled selected> -- Seleccionar Responsable -- </option>`;
        let valid_estado = '';
        let aprobacion_idestado = 0;
        array_usuarios.forEach((va_user) => {
          option_usuario += `<option value="${va_user.idusers}" idpermiso="${va_user.idpermiso}" idestado="${va_user.idestado}" ${(va_user.estado_validar=='OK'?'selected':'')}>${va_user.nombre_personal}</option>`;
          if(va_user.estado_validar=='OK'){
              valid_estado = va_user.estado_validar
              aprobacion_idestado = va_user.idestado
          }
        });
      
        /*let disabled = '';
        let btn_valid = `<td id="resultado_cambiar_permiso${num}">
                            <button type="button" class="btn btn-warning" onclick="validarclave(${num},1)">
                            <i class="fa-solid fa-check"></i> APROBAR</button>
                            <button type="button" class="btn btn-danger" onclick="validarclave(${num},2)">
                            <i class="fa-solid fa-ban"></i> ANULAR</button></td>`;*/
        /*if(valid_estado=='OK'){
            disabled = 'disabled';
            
            if(aprobacion_idestado==1){
                btn_valid = `<td id="resultado_cambiar_permiso${num}"><div style="background-color: #198754;padding: 7px;border-radius: 5px;color: #fff;text-align: center;font-weight: bold;">Ok</div></td>`;
                btn_anular = '';
            }else if(aprobacion_idestado==2){
                btn_valid = '';
                btn_anular = `<td id="resultado_cambiar_permiso$${num}"><div style="background-color: #dc3545;padding: 7px;border-radius: 5px;color: #fff;text-align: center;font-weight: bold;">ANULADO</div></td>`;
            }
            
        }*/

        tr_body += `<tr id="${num}" idpermiso="${valor.idpermiso}" idestado="0">
                          <td><span class="badge bg-warning" style="color:#000">${valor.permiso}</span></td>
                          <td><select class="form-control" id="per_usuario${num}" usuario>${option_usuario}</select></td>
                          <td><input type="password" password_users id="per_clave${num}" class="form-control text-center"></td>
                          <td id="resultado_cambiar_permiso${num}" style="240px">
                            <button type="button" class="btn btn-warning" onclick="validarclave(${num},1,'${target}')">
                            <i class="fa-solid fa-check"></i> APROBAR</button>
                            <button type="button" class="btn btn-danger" onclick="validarclave(${num},2,'${target}')">
                            <i class="fa-solid fa-ban"></i> DESAPROBAR</button></td>
                          <td><input type="text" comentario_users id="per_comentario${num}" class="form-control color_cajatexto"></td>
                        </tr>`;
        num++;
    });
    $(target+' > tbody').html(tr_body);
    /*$('#table-permisos-nivel-dos > tbody input').attr('disabled',false);
    $('#table-permisos-nivel-dos > tbody select').attr('disabled',false);
    $('#table-permisos-nivel-uno > tbody input').attr('disabled',false);
    $('#table-permisos-nivel-uno > tbody select').attr('disabled',false);*/
    
    /*if(data.length == 0){
      $(target).addClass('d-none');
    }*/
  }
   
   function cambiarestado(){

        callback({
              route: '{{ url('backoffice/'.$tienda->id.'/propuestacredito/'.$credito->id) }}',
              method: 'PUT',
              id: '#form_cambiar_estado',
              carga: '#carga_cambiar_estado',
              data:{
                  view: 'cambiar_estado',
                  estado: '{{ $estado }}',
                  permiso: '{{ $permiso }}',
                  idresponsable: $('#idresponsable').val(),
                  responsableclave: $('#responsableclave').val(),
              }
          },
          function(res){
            lista_credito();
            $('#modal-close-cambiar-estado').click(); 
          })
  }
  
  function validarclave(num,estado,target){
      
        $(target+' > tbody > tr#'+num).attr('idestado',estado);
    
        let idpermiso = $('#per_usuario'+num+' option:selected').attr('idpermiso');  
          
        let idusers = $(target+' > tbody > tr #per_usuario'+num).val();
        let password = $(target+' > tbody > tr #per_clave'+num).val();
        let comentario = $(target+' > tbody > tr #per_comentario'+num).val();
        let nivel_uno = $('#check_uno_table:checked').val();
        let nivel_dos = $('#check_dos_table:checked').val();
    
        let nivel_a = '';
        if(nivel_uno=='table_uno'){
            nivel_a = '1';
        }
        if(nivel_dos=='table_dos'){
            nivel_a = '2';
        }

        callback({
              route: '{{ url('backoffice/'.$tienda->id.'/propuestacredito/'.$credito->id) }}',
              method: 'PUT',
              id: '#form_cambiar_estado',
              carga: '#carga_cambiar_estado',
              data:{
                  view: 'cambiar_estado',
                  estado: '{{ $estado }}',
                  tipo_validacion : $('#tipo_validacion option:selected').val(),
                  nivel_validacion : nivel_a,
                  idpermiso : idpermiso,
                  idusers : idusers,
                  password : password,
                  comentario : comentario,
                  idestado : estado,
                  credito_aprobacion : jsonAprobacion(),
              }
          },
          function(res){
            if(estado==1){
                $(target+' > tbody > tr #resultado_cambiar_permiso'+num).html('<div style="background-color: #198754;padding: 7px;border-radius: 5px;color: #fff;text-align: center;font-weight: bold;">CORRECTO</div>');
            }
            else if(estado==2){
                $(target+' > tbody > tr #resultado_cambiar_permiso'+num).html('<div style="background-color: #dc3545;padding: 7px;border-radius: 5px;color: #fff;text-align: center;font-weight: bold;">ANULADO</div>');
            }
            
            $(target+' > tbody > tr #per_usuario'+num).attr('disabled', true);
            $(target+' > tbody > tr #per_clave'+num).attr('disabled', true);
            $(target+' > tbody > tr #per_comentario'+num).attr('disabled', true);
            $(target+' > tbody > tr #per_comentario'+num).removeClass('color_cajatexto');
            $('#tipo_validacion').attr('disabled', true);
            $('#check_uno_table').attr('disabled', true);
            $('#check_dos_table').attr('disabled', true);
            removecarga({input:'#carga_cambiar_estado'});
          
            if(res['credito_aprobado']=='CORRECTO'){
              lista_credito();
              $('#modal-close-cambiar-estado').click(); 
            }
         
          })
  }
  
  
  $('input[name="exampleRadios"]').change(function() {
    let valCheck = $(this).val();
    let tableDosInputs = $('#table-permisos-nivel-dos > tbody input');
    let tableDosSelects = $('#table-permisos-nivel-dos > tbody select');
    let tableUnoInputs = $('#table-permisos-nivel-uno > tbody input');
    let tableUnoSelects = $('#table-permisos-nivel-uno > tbody select');

    tableDosInputs.add(tableDosSelects).add(tableUnoInputs).add(tableUnoSelects).attr('disabled', false);
    
    $('#cont_permiso_nivel_uno').css('display','none');
    $('#cont_permiso_nivel_dos').css('display','none');
    if (valCheck === 'table_uno') {
        tableDosInputs.add(tableDosSelects).attr('disabled', true);
        $('#cont_permiso_nivel_uno').css('display','block');
        $('#cont_permiso_nivel_dos').css('display','none');
    } else {
        tableUnoInputs.add(tableUnoSelects).attr('disabled', true);
        $('#cont_permiso_nivel_uno').css('display','none');
        $('#cont_permiso_nivel_dos').css('display','block');
    }
  });
  
  function jsonAprobacion(){
    let checkTable = $('input[name="exampleRadios"]:checked').val();
    let table = '#table-permisos-nivel-uno > tbody > tr';
    if(checkTable == 'table_dos'){
       table = '#table-permisos-nivel-dos > tbody > tr';
    }
    
    let data = [];
    $(table).each(function() {
        let idpermiso = $(this).attr('idpermiso'); 
        //let idpermiso = $(this).find('select[usuario] option:selected').attr('idpermiso'); 
        let idestado = $(this).attr('idestado');    
        let idusers = $(this).find('select[usuario]').val();
        let password = $(this).find('input[password_users]').val();
        let comentario = $(this).find('input[comentario_users]').val();
        data.push({ 
          idpermiso : idpermiso,
          idusers : idusers!=undefined?idusers:0,
          password : password!=undefined?password:'',
          comentario : comentario!=undefined?comentario:'',
          idestado : idestado!=undefined?idestado:0
        });
    });
    return JSON.stringify(data);
  }
  
  

</script>