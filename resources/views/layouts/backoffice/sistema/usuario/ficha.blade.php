<div class="modal-header">
    <h5 class="modal-title">FICHA CLIENTE</h5>
    <button type="button" class="btn-close" id="modal-close-usuario-registrar" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <iframe src="{{ url('/backoffice/'.$tienda->id.'/usuario/'.$usuario->id.'/edit?view=fichapdf') }} " class="modal-1"></iframe>
</div>