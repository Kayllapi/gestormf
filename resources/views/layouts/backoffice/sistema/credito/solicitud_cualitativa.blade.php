<div class="modal-header">
    <h5 class="modal-title">SOLICITUD DE EVALUACION CUALITATIVA</h5>
    <button type="button" class="btn-close" id="modal-close-usuario-registrar" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <iframe src="{{ url('/backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=pdfsolicitud_cualitativa') }}#zoom=100" frameborder="0" width="100%" height="600px"></iframe>
</div>

