<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/propuestacredito/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'comentario_visitas',
          }
      },
      function(res){
        removecarga({input:'#mx-carga'})
        $('#success-message').removeClass('d-none');
        $('#success-message').text(res.mensaje);
        setTimeout(function() {
          $('#success-message').addClass('d-none');
        }, 5000);
        document.getElementById('iframe_acta_aprobacion').contentWindow.location.reload();
      },this)"> 
    <div class="modal-header" style="border-bottom: 0;">
        <h5 class="modal-title">COMENTARIO DE VISITAS</h5>
        <button type="button" class="btn-close text-white" id="modal-close-garantia-cliente" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body modal-body-cualitativa" style="min-height: 400px;">
        <button type="button"
          class="btn btn-success mb-2"
          id="btn-autorizar-garantia"
          onclick="modificar_opciones('comentario')">
          <i class="fa-solid fa-pencil"></i> Registrar / Editar
        </button>
      <input type="hidden" name="idresponsable" id="idresponsable" value="">
      <div id="cont_editar" <?php echo $credito->idusuario_comentariovisita==0?'style="display:none;"':'' ?> >
          <div class="row">
            <div class="col-sm-12">
              <textarea class="form-control color_cajatexto" id="comentariovisita" cols="30" rows="10" disabled>{{ $credito->comentariovisita }}</textarea>
            </div>
          </div>

        <div class="row mt-1">
          <div class="col" style="flex: 0 0 0%;">
            <button type="submit"
              class="btn btn-success"
              id="btn-save-comentario"
              style="display: none;">
              <i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS
            </button>
          </div>
          <div class="col" style="flex: 1 0 0%;">
            <div id="success-message" class="alert alert-success d-none" style="text-align:left;"></div>
          </div>
          <div class="col" style="flex: 0 0 0%;">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i> SALIR</button>
          </div>
        </div>
      </div>
    </div>
</form>