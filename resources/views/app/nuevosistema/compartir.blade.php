<div class="modal-header">
    <h5 class="modal-title">Compartir</h5>
    <button type="button" class="btn-close" id="modal-close-compartir" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <label>{{ $tipo_compartir == 1 ? 'Correo Electrónico' : 'Nro. de WhatsApp' }}</label>
            <input type="text" class="form-control" id="campo_compartir" >
        </div>
        <div class="col-sm-12 mt-2">
            <button type="button" class="btn btn-success w-100" id="btn_compartir">COMPARTIR</button>
        </div>
    </div>
</div>
<script>
$('#btn_compartir').on('click', function() {
    let tipo        = {{ $tipo_compartir }};
    let campo       = $('#campo_compartir').val().trim();
    let url_voucher = '{!! $url_voucher !!}';

    if(campo == '') {
        let mensaje = 'Ingrese el ' + (tipo == 1 ? 'correo electrónico' : 'número de WhatsApp');
        modal({ route: "{{ url('backoffice/0/inicio/create?view=alerta') }}&mensaje=" + mensaje, size: 'modal-sm' });
        return;
    }

    if(tipo == 1) {
        let asunto = encodeURIComponent('Voucher de Pago');
        let cuerpo = encodeURIComponent('Estimado cliente, aquí está su voucher:\n' + url_voucher);
        window.open('mailto:' + campo + '?subject=' + asunto + '&body=' + cuerpo, '_blank');
    }
    if(tipo == 2) {
        let mensaje = encodeURIComponent('Aquí está su voucher de pago: ' + url_voucher);
        window.open('https://wa.me/' + campo + '?text=' + mensaje, '_blank');
    }
});
</script>