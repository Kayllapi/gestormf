<div class="modal-header">
    <h5 class="modal-title">Compartir</h5>
    <button type="button" class="btn-close" id="modal-close-compartir-opcion" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body text-center">
    <button type="button" class="btn btn-warning" onclick="compartirTipo(1)">
        <i class="fa-solid fa-envelope"></i> Correo
    </button>
    <button type="button" class="btn btn-success" onclick="compartirTipo(2)">
        <i class="fa-brands fa-square-whatsapp"></i> WhatsApp
    </button>
</div>
<script>
function compartirTipo(tipo) {
    let url_base = '{{ $url_base }}';
    let idcliente = '{{ $idcliente }}';
    let url_voucher = decodeURIComponent(new URLSearchParams(url_base.split('?')[1]).get('url_voucher'));
    modal({ 
        route: "{{ url('backoffice/0/inicio/create?view=compartir') }}"
             + "&tipo_compartir=" + tipo 
             + "&clt=" + idcliente 
             + "&url_voucher=" + encodeURIComponent(url_voucher), 
        size: 'modal-sm' 
    });
}
</script>