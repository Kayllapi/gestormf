<form @include('app.nuevosistema.submit',['method'=>'PUT','view'=>'editpermiso','id'=>$usuario->id])>
    <div id="cont-mensaje" class="mensaje-info" <?php echo $usuario->idestado==1? 'style="display:none;"':'' ?>>
        <i class="fa fa-exclamation-circle" style="font-size: 30px;margin-bottom: 5px;"></i><br>
        Al activar el acceso, el usuario podra acceder al Sistema de Gestión.<br>
        <b>¿Desea activar el acceso de Usuario?</b><br>          
    </div>
    @if($usuario->idestado==1)
    <div style="width: 100%;text-align: center;float: left;margin-bottom: 5px;">
        <div class="onoffswitch" style="margin: auto;">
            <input type="checkbox" class="onoffswitch-checkbox estadoacceso" id="estadoacceso" checked="checked">
            <label class="onoffswitch-label" for="estadoacceso">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
            </label>
        </div>
    </div>
    @else
    <div style="width: 100%;text-align: center;float: left;margin-bottom: 5px;">
        <div class="onoffswitch" style="margin: auto;">
            <input type="checkbox" class="onoffswitch-checkbox estadoacceso" id="estadoacceso">
            <label class="onoffswitch-label" for="estadoacceso">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
            </label>
        </div>
    </div>
    @endif
    <div id="cont-accesousuario" <?php echo $usuario->idestado==2? 'style="display:none;"':'' ?>>
        <div class="row">
            <div class="col-md-6">
                <label>Usuario *</label>
                <?php 
                    $lusuario = explode('@',$usuario->usuario); 
                    $valusuario = $usuario->usuario;
                    if($lusuario>1){
                        $valusuario = $lusuario[0];
                    }
                ?>
                <input type="text" value="{{ $valusuario }}" id="usuario">
                <style>
                    .left-input-group>input {
                        border-left: 1px solid #aaa !important;
                        border-radius: 0px !important;
                        border-top-left-radius: 5px !important;
                        border-bottom-left-radius: 5px !important;
                    }
                    .left-input-group>.input-group-prepend>.input-group-text {
                        border-radius: 5px;
                        border-top-left-radius: 0;
                        border-bottom-left-radius: 0;
                    }
                </style>
                <label>Cambiar Contraseña</label>
                <input type="text" id="password"> 
                <label>Permiso *</label>
                <select id="idrol" onchange="mostrarPermisos()">
                    <option></option>
                    @foreach($roles as $value)
                    <option value="{{ $value->id }}" >{{ $value->description }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">      
                <div class='table-responsive' id="cont-table-permiso">
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>

<script>
    @if($usuario->idroles!='')
        $("#idrol").select2({
            placeholder: "---  Seleccionar ---",
            minimumResultsForSearch: -1
        }).val({{$usuario->idroles}}).trigger("change");
    @else
        $("#idrol").select2({
            placeholder: "---  Seleccionar ---",
            minimumResultsForSearch: -1
        });
    @endif
    $("#idestado").select2({
        placeholder: "---  Seleccionar ---",
        minimumResultsForSearch: -1
    }).val({{$usuario->idestado}}).trigger("change");

    $("#estadoacceso").click(function(){
        $('#cont-accesousuario').css('display','none');
        $('#cont-mensaje').css('display','block');
        var checked = $("#estadoacceso:checked").val();
        if(checked=='on'){
            $('#cont-accesousuario').css('display','block');
            $('#cont-mensaje').css('display','none');
        }
    });
  
    // funcion para mostrar los permisos
    function mostrarPermisos() {
        $('#cont-table-permiso').html('');
        $.ajax({
            url:  '{{$_GET['url_sistema']}}/{{ $tienda->id }}/usuario/show-detalle-permiso',
            type: 'GET',
            data: {
                idrol: $('#idrol').val(),
            },
            beforeSend: function (data) {
                load('#cont-table-permiso');
            },
            success: function (res) {
                $('#cont-table-permiso').html(res['html']);
            }
        });
    }
    // fin
</script>
