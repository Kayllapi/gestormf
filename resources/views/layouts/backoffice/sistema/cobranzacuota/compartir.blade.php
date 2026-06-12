<div class="modal-header">
    <h5 class="modal-title">Compartir</h5>
    <button type="button"
        class="btn-close"
        id="modal-close-compartir"
        data-bs-dismiss="modal"
        aria-label="Close">
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <label for="campo_texto">{{ $tipo_compartir == 1 ? 'Correo Electrónico' : 'Nro. de Whatsapp' }}</label>
            <input type="text" class="form-control" id="campo_texto"
                value="{{ $tipo_compartir == 1 ? $correo_cliente : $whatsapp_cliente }}" >
        </div>
        <div class="col-sm-12 mt-2">
            <button type="button" class="btn btn-success" id="btn-save-compartir"> ENVIAR</button>
        </div>
    </div>
</div>
<script>
$('#btn-save-compartir').on('click', function(e){
    e.preventDefault();
    let tipo  = {{ $tipo_compartir }}; /* 1 correo, 2 whatsapp */
    let campo = $('#campo_texto').val().trim();

    if(campo == ''){
        mensaje = 'Ingrese el ' + (tipo==1 ? 'correo electrónico' : 'número de WhatsApp');
        modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });
        return;
    }

    if(tipo == 1){
        let asunto = encodeURIComponent('Voucher / Documento de Crédito');
        let cuerpo = encodeURIComponent(
            'Estimado cliente, puede ver su Voucher / Documento haciendo clic en el siguiente enlace:\n\n'
            + '{!! $url_voucher !!}'
            + '\n\nSi el enlace no abre, cópielo y péguelo en su navegador.'
        );
        window.open('mailto:' + campo + '?subject=' + asunto + '&body=' + cuerpo, '_blank');
    }
    if(tipo == 2){
        let mensaje = encodeURIComponent('Aquí está su voucher de pago: {!! $url_voucher !!}');
        window.open('https://wa.me/' + campo + '?text=' + mensaje, '_blank');
    }
});
</script>