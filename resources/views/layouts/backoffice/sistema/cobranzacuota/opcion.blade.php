<div class="modal-header">
    <h5 class="modal-title">TICKETS</h5>
    <button type="button"
        class="btn-close"
        id="modal-close-cambiar-estado"
        data-bs-dismiss="modal"
        aria-label="Close">
    </button>
</div>
<div class="modal-body">
    <div class="col-sm-12 mt-2 text-center">
        <button type="button" class="btn btn-primary"
            onclick="verpdf('pdf_pago')">
            VOUCHER DE PAGO
        </button>
        @if($idestadocredito==2 && $credito->idforma_credito==1 && $entregargarantia=='on')
            <button type="button" class="btn btn-warning"
            onclick="verpdf('pdf_garantia',{{$credito->id}})">
                V. ENTREGA DE GARANTIA
            </button>
        @endif
        <button type="button" class="btn btn-info" onclick="compartirVoucher()"
            style="padding: 2.5px 8px;">
            <i class="fa-solid fa-share-nodes" style="width: 30px; font-size: 20px;"></i>
        </button>
        <div class="col-sm-12 mt-2">
            <iframe id="iframe_acta_aprobacion" 
            src="{{ url('/backoffice/'.$tienda->id.'/cobranzacuota/'.$credito->id.'/edit?view=pdf_pago&idcobranzacuota='.$idcobranzacuota) }}#zoom=100" 
            frameborder="0" width="100%" height="600px"></iframe>
        </div>
    </div>
</div>
<script>
function verpdf(valor,idgarantia=0,num=0){
    $('#iframe_acta_aprobacion').attr('src',"{{ url('/backoffice/'.$tienda->id.'/cobranzacuota/'.$credito->id.'/edit?view=') }}"+valor+'&idcobranzacuota={{$idcobranzacuota}}&idgarantia='+idgarantia+'&num='+num+'#zoom=100');
}
imprimirTicket('pdf_pago');
function imprimirTicket(tipo,idgarantia=0,num=0){
    let iframe = document.getElementById('iframe_acta_aprobacion');
    iframe.onload = function () {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    };
}
function compartirVoucher() {
    let idcobranzacuota = {{$idcobranzacuota}};
    modal({ route:"{{url('backoffice/'.$tienda->id.'/cobranzacuota/'.$credito->id.'/edit?view=compartir_opcion')}}&idcobranzacuota="+idcobranzacuota, size: 'modal-sm' });
}
</script>