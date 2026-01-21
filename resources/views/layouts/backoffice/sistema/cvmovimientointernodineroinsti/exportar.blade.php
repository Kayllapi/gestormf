<div id="carga_cambiar_estado">
<form action="javascript:;" id="form_cambiar_estado">
    <div class="modal-header">
        <h5 class="modal-title">REPORTE</h5>
        <button type="button" class="btn-close" id="modal-close-cambiar-estado" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
       <div class="col-sm-12 mt-2 text-center">
       <div class="col-sm-12 mt-2">
        <iframe id="iframe_acta_aprobacion" 
        src="{{ url('/backoffice/'.$tienda->id.'/cvmovimientointernodineroinsti/0/edit?view=exportar_pdf&fechainicio='.$fechainicio.'&fechafin='.$fechafin) }}#zoom=100" 
        frameborder="0" width="100%" height="500px"></iframe>
      </div>
      </div>
  </div>
</form>   
</div>