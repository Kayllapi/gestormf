<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/usuario') }}',
          method: 'POST',
          data:{
              view: 'autorizacion'
          }
      },
      function(res){
        console.log(res);
        let estado = res.resultado;
        if(estado == 'CORRECTO'){
            $('#modal-close-usuario-autorizacion').click(); 
            autorizar_edicion()
        }
        else{
            console.log('volver a ingresar');
        }
        
      },this)"> 
    <div class="modal-header">
        <h5 class="modal-title"> | AUTORIZAR |</h5>
        <button type="button" class="btn-close" id="modal-close-usuario-autorizacion" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <label>Contraseña: <span class="text-danger">*</span></label>
                <input type="password"  id="password_autorizacion" class="form-control">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Autorizar</button>
    </div>
</form>                      
<script>

</script>
