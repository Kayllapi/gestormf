<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/garantiaremateagencia/'.$credito_garantia->id) }}',
        method: 'PUT',
        data:{
            view: 'registrar_precio_liquidacion',
        }
    },
    function(resultado){
        $('#modal-close-precioliquidacion').click();
        $('#modal-close-garantias-modificar').click();
        liquidacion_garantia();
    },this)">
    <div class="modal-header">
        <h5 class="modal-title">Precio de Liquidación</h5>
        <button type="button" class="btn-close" id="modal-close-precioliquidacion" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <label for="precioliquidacion" class="col-sm-5 col-form-label">Precio de Liquidación *</label>
            <div class="col-sm-7">
                <input type="number" class="form-control" id="precioliquidacion" value="{{ $credito_garantia->precioliquidacion }}">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"> Ingresar</button>
    </div>
</form>   