<div class="modal-header">
    <h5 class="modal-title">Compartir</h5>
    <button type="button"
        class="btn-close"
        id="modal-close-compartir-opcion"
        data-bs-dismiss="modal"
        aria-label="Close">
    </button>
</div>
<div class="modal-body">
    <div style="text-align: center">
        <button type="button"
            class="btn btn-warning"
            onclick="compartirCampoText(1)">
            <i class="fa-solid fa-envelope"></i> Correo
        </button>
        <button type="button"
            class="btn btn-success"
            onclick="compartirCampoText(2)">
            <i class="fa-brands fa-square-whatsapp"></i> Whatsapp
        </button>
    </div>
</div>
<script>
    /* 1 correo, 2 whatsapp */
    function compartirCampoText(tipo_compartir) {
        let idcobranzacuota = {{$idcobranzacuota}};
        modal({ route:"{{url('backoffice/'.$tienda->id.'/cobranzacuota/'.$credito->id.'/edit?view=compartir')}}&idcobranzacuota="+idcobranzacuota+"&tipo_compartir="+tipo_compartir, size: 'modal-sm' });
    }
</script>