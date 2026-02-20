<div class="modal-header">
    <h5 class="modal-title">FORMATO DE EVALUACIÓN</h5>
    <button type="button" class="btn-close" id="modal-close-usuario-registrar" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <iframe src="{{ url('/backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=pdfsolicitud_formato_evaluacion') }}#zoom=100" frameborder="0" width="100%" style="height: calc(100vh - 54px)"></iframe>
</div>

