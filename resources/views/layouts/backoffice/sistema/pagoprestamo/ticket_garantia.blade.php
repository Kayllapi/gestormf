<div id="carga_cambiar_estado">
<form action="javascript:;" id="form_cambiar_estado">
    <style>
      .form-check-label {
          margin-top: 5px;
          margin-left: 5px;
      }
    </style>
    <div class="modal-header">
        <h5 class="modal-title">V. ENTREGA DE GARANTÍA</h5>
        <button type="button" class="btn-close" id="modal-close-cambiar-estado" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
          @if($count_creditopendiente>0)
              <p class="text-center" 
                 style="background-color: #dc3545;
                        padding: 10px;
                        border-radius: 5px;
                        color: #fff;
                        width: 100%;
                        margin: auto;">Está pendiente a recojo de garantia!!.</p>
          @else
            <div class="col-sm-12 mt-2 text-center">
                <iframe id="iframe_acta_aprobacion" 
                src="{{ url('/backoffice/'.$tienda->id.'/pagoprestamo/'.$credito_cobranzacuota->id.'/edit?view=pdf_garantia') }}#zoom=100" 
                frameborder="0" width="100%" height="600px"></iframe>
            </div>
          @endif
        
    </div>
</form>   
</div>