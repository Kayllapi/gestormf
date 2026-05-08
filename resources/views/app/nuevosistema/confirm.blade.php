<div class="modal-header">
    <h5 class="modal-title">Confirmar</h5>
    <button type="button" class="btn-close" id="modal-close-usuario-autorizacion" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="alert alert-danger">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <b>{{$mensaje}}</b>
    </div>
    <div class="mt-2" style="text-align: right">
        <button type="submit" class="btn btn-success" id="btn-save-confirm">Aceptar</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
    </div>
</div>
