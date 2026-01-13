<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/margendescuento/0') }}',
        method: 'PUT',
        data:{
            view: 'editar',
        }
    },
    function(resultado){},this)"> 
    <div class="modal-body">
        <div class="row justify-content-center">
            <div class="col-sm-12 col-md-6">
                <div class="mb-1">
                    <span class="badge d-block">Margen prevista / Descuento de Venta</span>
                </div>
                <div class="row mt-1">
                    <label class="col-sm-5 col-form-label" style="text-align: right;">Margen previsto MP(%):</label>
                    <div class="col-sm-7">
                        <input type="number" class="form-control" step="any" id="margen_previsto" value="{{ configuracion($tienda->id,'margen_previsto')['valor'] }}">
                    </div>
                </div>
                <div class="row mt-1">
                    <label class="col-sm-5 col-form-label" style="text-align: right;">Valor de Descuento VD(%):</label>
                    <div class="col-sm-7">
                        <input type="number" class="form-control" step="any" id="valor_descuento" value="{{ configuracion($tienda->id,'valor_descuento')['valor'] }}">
                    </div>
                </div>
                <div class="row mt-1">
                    <h5 class="col-sm-5" style="text-align: right;"><strong>MP > VD</strong></h5>
                </div>
            </div>
        </div>
        <div class="row mt-1 justify-content-center">
            <div class="col-sm-12 col-md-2">
                <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
            </div>
        </div>
    </div>
</form>    