<form action="javascript:;"
    id="form_compartir">
    <div class="modal-header">
        <h5 class="modal-title">COMPARTIR</h5>
        <button type="button" class="btn-close" id="modal-close-compartir" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-12">
                <select class="form-control" id="tipo_compartir">
                    <option value="1">Correo Electrónico</option>
                    <option value="2">Whatsapp</option>
                </select>
            </div>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="campo_texto" placeholder="Ingrese el correo electrónico">
            </div>
            <div class="col-sm-12 mt-2 text-right">
              <button type="button" class="btn btn-success" id="btn-save-compartir"> COMPARTIR</button>
            </div>
        </div>
    </div>
</form>
{{-- <script>
    sistema_select2({ input:'#tipo_compartir' });
    $('#tipo_compartir').change(function(){
        if($(this).val() == 1){
            $('#campo_texto').attr('placeholder', 'Ingrese el correo electrónico');
        } else if($(this).val() == 2){
            $('#campo_texto').attr('placeholder', 'Ingrese el número de Whatsapp');
        } else {
            $('#campo_texto').attr('placeholder', '');
        }
    });
</script> --}}
<script>
sistema_select2({ input:'#tipo_compartir' });

$('#tipo_compartir').change(function(){
    $('#campo_texto').val('');
    if($(this).val() == 1){
        $('#campo_texto').attr('placeholder', 'Ingrese el correo electrónico');
    } else {
        $('#campo_texto').attr('placeholder', 'Ingrese el número de Whatsapp (ej: 51987654321)');
    }
});

$('#btn-save-compartir').on('click', function(e){
    e.preventDefault();
    let tipo  = $('#tipo_compartir').val();
    let campo = $('#campo_texto').val().trim();
    let idcobranzacuota = {{$idcobranzacuota}};

    if(campo == ''){
        mensaje = 'Ingrese el ' + (tipo==1 ? 'correo electrónico' : 'número de WhatsApp');
        modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });
        return;
    }

    if(tipo == 1){
        let url_pdf = "{{ url('backoffice/'.$tienda->id.'/cobranzacuota/'.$credito->id.'/edit') }}"
            + "?view=pdf_pago"
            + "&idcobranzacuota={{ $idcobranzacuota }}";
        let asunto  = encodeURIComponent('Voucher de Pago');
        let cuerpo  = encodeURIComponent('Estimado cliente, adjunto su voucher de pago:\n' + url_pdf);
        window.open('mailto:' + campo + '?subject=' + asunto + '&body=' + cuerpo, '_blank');
    }
    if(tipo == 2){
        let url_pdf = "{{ url('backoffice/'.$tienda->id.'/cobranzacuota/'.$credito->id.'/edit') }}"
            + "?view=pdf_pago"
            + "&idcobranzacuota={{ $idcobranzacuota }}";
        let mensaje = encodeURIComponent('Aquí está su voucher de pago: ' + url_pdf);
        window.open('https://wa.me/' + campo + '?text=' + mensaje, '_blank');
    } /*else {
        callback({
            route: '{{ url('backoffice/'.$tienda->id.'/cobranzacuota/'.$credito->id) }}',
            method: 'PUT',
            data:{
                view: 'compartir',
                tipo_compartir: tipo,
                campo_texto: campo,
                idcobranzacuota: idcobranzacuota
            }
        },
        function(res){
            if(res.resultado == 'CORRECTO'){
                alert('Correo enviado correctamente.');
                $('#modal_compartir').modal('hide');
            } else {
                alert(res.mensaje);
            }
        },this);
    }*/
});
</script>