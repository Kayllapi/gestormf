    <div class="modal-header" style="border-bottom: 0;">
        <h5 class="modal-title">ESTADO DE CUENTA </h5>
        <button type="button" class="btn-close text-white" id="modal-close-garantia-cliente" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="col-sm-12 mt-2">
        <iframe id="iframe_acta_aprobacion" 
                onload="ifrhgh()"
                src="{{ url('/backoffice/'.$tienda->id.'/estadocuenta/'.$credito->id.'/edit?view=pdf_credito') }}#zoom=100" 
                frameborder="0" width="100%"></iframe>
        </div>
    </div>
<script>
function ifrhgh(){
    var iframehght =  $(".modal-content").height();
    document.getElementById("iframe_acta_aprobacion").style.height = (iframehght-60)+"px";
}
</script>
