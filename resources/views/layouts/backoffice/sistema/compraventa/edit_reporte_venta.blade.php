<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/compraventa/0') }}',
        method: 'PUT',
        data:{
            view: 'update_reporte_venta'
        }
    },
    function(resultado){
        $('#modal-close-venta-reporte').click(); 
    },this)">
    <div class="modal-header">
        <h5 class="modal-title">Reporte de Venta</h5>
        <button type="button" class="btn-close" id="modal-close-venta-reporte" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
            <i class="fa-solid fa-triangle-exclamation"></i> ¿Está seguro ver el reporte de la venta?<br>
        </div>
        <div class="mt-2 bg-primary subtitulo">Aprobación</div>
        <div class="mb-1">
            <label>Responsable *</label>
            <select class="form-select" id="idresponsable">
                <option value=""></option>
                @foreach($usuarios as $value)
                    <option value="{{$value->id}}" idpermiso="{{$value->idpermiso}}">{{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                @endforeach
            </select>
        </div>
        <input type="hidden" id="idpermiso">
        <div class="mb-1">
            <label>Contraseña *</label>
            <input type="password" class="form-control" id="responsableclave">
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-file-pdf" style="color:#000 !important; font-weight: bold;"></i> Ver Reporte
        </button>
    </div>
</form>   
<script>
    sistema_select2({ input:'#idresponsable' });
    $('#idresponsable').change(function(){
        $('#idpermiso').val($(this).find('option:selected').attr('idpermiso'));
    });
</script>