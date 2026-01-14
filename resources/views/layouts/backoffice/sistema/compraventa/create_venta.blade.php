<form action="javascript:;"
    id="form-registrar-cvventa"  
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/compraventa/') }}',
        method: 'POST',
        data:{
            view: 'registrar_venta',
            idcvcompra: '{{ $cvcompra->id }}',
        }
    },
    function(resultado){
        $('#modal-close-cvventa').click();
    },this)">
    <div class="modal-header">
        <h1 class="modal-title">{{ $cvcompra->descripcion }}</h1>
        <button type="button" class="btn-close" id="modal-close-cvventa" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body p-1">
        <div class="row">
            <div class="col-sm-12">
                <label class="col-form-label" style="text-align: right;">Apellidos y Nombres (Comprador) *</label>
                <input type="text" class="form-control" id="comprador_nombreapellidos">
            </div>
            <div class="col-sm-12">
                <label class="col-form-label" style="text-align: right;">DNI (Comprador) *</label>
                <input type="text" class="form-control" id="comprador_dni">
            </div>
            <div class="col-sm-12">
                <label class="col-form-label" style="text-align: right;">Precio de Compra</label>
                <input type="number" class="form-control" id="venta_precio_compra" value="{{ $cvcompra->valorcompra }}" disabled>
            </div>
            <div class="col-sm-12">
                <label class="col-form-label" style="text-align: right;">Precio de Venta</label>
                <input type="number" class="form-control" id="venta_precio_venta">
            </div>
            <div class="col-sm-12">
                <label class="col-form-label" style="text-align: right;">Precio de Venta con Descuento *</label>
                <input type="number" class="form-control" id="venta_precio_venta_descuento">
            </div>
            <div class="col-sm-12">
                <label class="col-form-label" style="text-align: right;">Monto de Venta *</label>
                <input type="number" class="form-control" id="venta_montoventa">
            </div>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-12">
                        <input type="radio" name="venta_idformapago" id="venta_idformapago" value="1" checked> Caja
                        <input type="radio" name="venta_idformapago" id="venta_idformapago" value="2"> Banco
                    </div>
                    <div class="col-sm-12">
                        <label class="col-form-label" style="text-align: right;">N° Operación</label>
                        <input type="text" class="form-control" id="venta_numerooperacion" disabled>
                    </div>
                    <div class="col-sm-12">
                        <label class="col-form-label" style="text-align: right;">Bancos</label>
                        <select class="form-control" id="venta_idbanco" disabled>
                            <option></option>
                            @foreach($bancos as $value)
                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-floppy-disk"></i> Vender
        </button>
    </div>
</form>

<script>
    sistema_select2({ input:'#venta_idbanco' });

    $('input[name="venta_idformapago"]').on('change', function() {
        if($(this).val() == 1){
            $('#venta_numerooperacion').prop('disabled', true);
            $('#venta_idbanco').prop('disabled', true);
        }else{
            $('#venta_numerooperacion').prop('disabled', false);
            $('#venta_idbanco').prop('disabled', false);
        }
    });
</script>