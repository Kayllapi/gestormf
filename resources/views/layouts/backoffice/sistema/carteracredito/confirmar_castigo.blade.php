<form action="javascript:;" onsubmit="confirmar_castigo(this)">
    <div class="modal-header">
        <h5 class="modal-title">AUTORIZAR</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
    </div>
    <div class="modal-body">
        <div class="alert mb-1 text-start py-0" style="background-color: #ffffff;border: 1px solid grey;">
            <b>CUENTAS SELECCIONADAS:</b> <span style="font-weight: normal;">{{ count($creditos) }}</span>
        </div>
        <div class="mt-2 bg-primary subtitulo">Aprobación</div>
        <div class="mb-1">
            <label for="idresponsable">Responsable *</label>
            <select class="form-select" id="idresponsable" required>
                <option value=""></option>
                @foreach($usuarios as $value)
                <option value="{{$value->id}}">{{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-1">
            <label for="responsableclave">Contraseña *</label>
            <input type="password" class="form-control" id="responsableclave" required autocomplete="current-password">
        </div>
        <div class="text-danger mt-2" role="alert" id="castigo-error"></div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Autorizar</button>
    </div>
</form>
<script>
    sistema_select2({ input:'#idresponsable' });
function confirmar_castigo(form) {
    let boton = $(form).find('[type=submit]');
    boton.prop('disabled', true);
    $('#castigo-error').text('');
    $.ajax({
        url: "{{url('backoffice/'.$tienda->id.'/carteracredito')}}", type: 'POST',
        data: {_token: '{{csrf_token()}}', view: @json($accionCastigo ?? 'confirmar_castigo'), creditos: @json($creditos), idresponsable: $('#idresponsable').val(), responsableclave: $('#responsableclave').val()},
        success: function(res) {
            $(form).find('[data-bs-dismiss=modal]').click();
            lista_credito();
            var mensaje = "Seleccione al menos una cuenta pendiente.";
            modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+res.mensaje, size: 'modal-sm' });
            return false;
        },
        error: function(xhr) {
            let errors = xhr.responseJSON?.errors;
            $('#castigo-error').text(errors ? Object.values(errors).flat().join(' ') : (xhr.responseJSON?.message || 'No se pudo confirmar el castigo.'));
            $('#responsableclave').val('');
        },
        complete: function(){ boton.prop('disabled', false); }
    });
}
</script>
