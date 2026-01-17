<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/compraventa/'.$cvcompra->id) }}',
        method: 'DELETE',
        data:{
            view: 'eliminar_compra'
        }
    },
    function(resultado){
        $('#modal-close-compra-eliminar').click(); 
        search_compra();
    },this)">
    <div class="modal-header">
        <h5 class="modal-title">Eliminar Compra</h5>
        <button type="button" class="btn-close" id="modal-close-compra-eliminar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
            <i class="fa-solid fa-triangle-exclamation"></i> ¿Esta seguro de eliminar la compra?<br>
            <b>"CB{{$cvcompra->codigo}}"</b>
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
        <button type="submit" class="btn btn-danger">
            <i class="fa-solid fa-trash" style="color:#000 !important; font-weight: bold;"></i> Eliminar
        </button>
    </div>
</form>   
<script>
    sistema_select2({ input:'#idresponsable' });
    $('#idresponsable').change(function(){
        $('#idpermiso').val($(this).find('option:selected').attr('idpermiso'));
    });
</script>