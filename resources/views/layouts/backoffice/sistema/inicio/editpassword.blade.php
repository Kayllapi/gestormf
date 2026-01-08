<form class="js-validation-signin px-30" 
        action="javascript:;" 
        onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/inicio/'.$usuario->id) }}',
        method: 'PUT',
        data:{
            view: 'editarpassword'
        }
    },
    function(resultado){
          $('#modal-close-inicio-editarpassword').click(); 
    },this)"> 
    <style>
        .pass-input-wrap {
            position: relative;
            margin-bottom: 20px;
        }

        .pass-input-wrap .pass-input-icon {
            position: absolute;
            right: 10px;
            top: 28px;
            cursor: pointer;
        }



        .pass-input-wrap .pass-input {
            padding-right: 40px;
        }

    </style>
    <div class="modal-header">
        <h5 class="modal-title">Cambiar Contraseña</h5>
        <button type="button" class="btn-close" id="modal-close-inicio-editarpassword" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <input type="hidden" value="editpassword" id="view"/>
            <div class="pass-input-wrap fl-wrap" style="margin-bottom: 5px;">
                <label>Contraseña Actual: *</label>
                <div class="pass-input-icon"><i class="fa fa-eye" aria-hidden="true"></i></div>
                <input type="password" class="form-control pass-input" value="" id="antpassword" />
            </div>
            <div class="pass-input-wrap fl-wrap" style="margin-bottom: 5px;">
                <label>Nueva Contraseña: *</label>
                <div class="pass-input-icon"><i class="fa fa-eye" aria-hidden="true"></i></div>
                <input type="password" class="form-control pass-input" value="" id="password" />
            </div>
            <div class="pass-input-wrap fl-wrap" style="margin-bottom: 5px;">
                <label>Confirmar Nueva Contraseña: *</label>
                <div class="pass-input-icon"><i class="fa fa-eye" aria-hidden="true"></i></div>
                <input type="password" class="form-control pass-input" value="" id="password_confirmation" />
            </div>
    </div>
    <div class="modal-footer">
    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
  </div>
</form>
<script>

// Hacer que los iconos de ojo sean interactuables
$('.pass-input-wrap .pass-input-icon').on('click', function() {
  var passwordField = $(this).closest('.pass-input-wrap').find('.pass-input');
  if (passwordField.attr('type') === 'password') {
    passwordField.attr('type', 'text');
    $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
  } else {
    passwordField.attr('type', 'password');
    $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
  }
});

</script>