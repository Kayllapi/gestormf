<form action="javascript:;"
    id="form-registrar-compraventa"  
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/compraventa/') }}',
        method: 'POST',
        data:{
            view: 'registrar_compra',
        }
    },
    function(resultado){
        $('#modal-close-compraventa').click();
        search_compra();
        vaucher_compraCreate(resultado.idcvcompra);
    },this)">
    <div class="modal-header">
        <h1 class="modal-title">Registro de Compra de Bienes</h1>
        <button type="button" class="btn-close" id="modal-close-compraventa" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body p-1">
        <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">Agencia *</label>
            <div class="col-sm-4">
                <select class="form-control" id="idtienda">
                    <option></option>
                    @foreach($agencias as $value)
                        <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">Tipo de Bien *</label>
            <div class="col-sm-4">
                <select class="form-control" id="idtipogarantia">
                    <option></option>
                    @foreach($tipo_garantia as $value)
                        <option value="{{ $value->id }}" antiguedad="{{ $value->antiguedad}}" valor="{{ $value->valor}}" cobertura="{{ $value->cobertura}}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <label class="col-form-label" style="text-align: right;">Descripción *</label>
                <input type="text" class="form-control" id="descripcion">
            </div>
            <div class="col-sm-6">
                <label class="col-form-label" style="text-align: right;">Serie/Motor/N° Partida *</label>
                <input type="text" class="form-control" id="serie_motor_partida">
            </div>
            <div class="col-sm-6">
                <label class="col-form-label" style="text-align: right;">Chasis</label>
                <input type="text" class="form-control" id="chasis">
            </div>
            <div class="col-sm-6">
                <label class="col-form-label" style="text-align: right;">Modelo/Tipo *</label>
                <input type="text" class="form-control" id="modelo_tipo">
            </div>
            <div class="col-sm-6">
                <label class="col-form-label" style="text-align: right;">Otros/Contraseña</label>
                <input type="text" class="form-control" id="otros">
            </div>
            <div class="col-sm-6">
                <label class="col-form-label" style="text-align: right;">Estado *</label>
                <select class="form-control" id="idestado_garantia">
                    <option></option>
                    @foreach($estado_garantia as $value)
                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6">
                <label class="col-form-label" style="text-align: right;">Color *</label>
                <input type="text" class="form-control" id="color">
            </div>
            <div class="col-sm-3">
                <label class="col-form-label" style="text-align: right;">Año Fabricación</label>
                <input type="text" class="form-control" id="fabricacion">
            </div>
            <div class="col-sm-3">
                <label class="col-form-label" style="text-align: right;">Año Compra</label>
                <input type="text" class="form-control" id="compra">
            </div>
            <div class="col-sm-6">
                <label class="col-form-label" style="text-align: right;">Placa (Vehículos)</label>
                <input type="text" class="form-control" id="placa">
            </div>
            <div class="col-sm-4 mt-2">
                <div class="row">
                    <label class="col-sm-6 col-form-label" style="text-align: left;">Valor Compra (S/.) *</label>
                    <div class="col-sm-6">
                        <input type="number" step="0.10" class="form-control" id="valorcompra" onchange="this.value = (Math.round(this.value * 10) / 10).toFixed(2)">
                    </div>
                </div>
            </div>
            <div class="col-sm-8 mt-2">
                <div class="row">
                    <label class="col-sm-8 col-form-label" style="text-align: right;">
                        Valor Comercial (S/.) <span style="color: #c40000;">(Mínimo {{ configuracion($tienda->id,'margen_previsto')['valor'] }}% más del V.Compra)</span> *
                    </label>
                    <div class="col-sm-4">
                        <input type="number" step="any" class="form-control" id="valorcomercial">
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <label class="col-form-label" style="text-align: right;">Apellidos y Nombres (Vendedor) *</label>
                <input type="text" class="form-control" id="vendedor_nombreapellidos">
            </div>
            <div class="col-sm-6">
                <label class="col-form-label" style="text-align: right;">RUC/DNI/CE (Vendedor) *</label>
                <input type="text" class="form-control" id="vendedor_dni">
            </div>
            <div class="col-sm-3">
                <label class="col-form-label" style="text-align: right;">Origen *</label>
                <select class="form-control" id="idorigen">
                    <option></option>
                    <option value="{{ $tienda->id }}">{{ $tienda->nombre }}</option>
                    <option value="0">OTROS</option>
                </select>
            </div>
            <div class="col-sm-5">
                <label class="col-form-label" style="text-align: right;">N° de Ficha o Comprobante *</label>
                <input type="text" class="form-control" id="numeroficha">
            </div>
            <div class="col-sm-4 mt-2">
                <div class="row">
                    <div class="col-sm-12">
                        <label class="custom-radio">
                            <input type="radio" name="compra_idformapago" id="compra_idformapago" value="1" checked>
                            <span></span>
                            Caja
                        </label>
                        <label class="custom-radio">
                            <input type="radio" name="compra_idformapago" id="compra_idformapago" value="2">
                            <span></span>
                            Banco
                        </label>
                        {{-- <input type="radio" name="compra_idformapago" id="compra_idformapago" value="1" checked> Caja
                        <input type="radio" name="compra_idformapago" id="compra_idformapago" value="2"> Banco --}}
                    </div>
                    <div class="col-sm-12">
                        <label class="col-form-label" style="text-align: right;">Bancos</label>
                        <select class="form-control" id="compra_idbanco" disabled>
                            <option></option>
                            @foreach($bancos as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }} - ***{{ substr($value->cuenta, -5) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-12">
                        <label class="col-form-label" style="text-align: right;">N° Operación</label>
                        <input type="text" class="form-control" id="compra_numerooperacion" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-floppy-disk"></i> Guardar Compra
        </button>
    </div>
</form>

<script>
    sistema_select2({ input:'#idtienda', val:'{{$tienda->id}}' });
    sistema_select2({ input:'#idtipogarantia' });
    sistema_select2({ input:'#idestado_garantia' });
    sistema_select2({ input:'#idorigen' });
    sistema_select2({ input:'#compra_idbanco' });

    $('input[name="compra_idformapago"]').on('change', function() {
        if($(this).val() == 1){
            $('#compra_numerooperacion').prop('disabled', true);
            $('#compra_idbanco').prop('disabled', true);
        }else{
            $('#compra_numerooperacion').prop('disabled', false);
            $('#compra_idbanco').prop('disabled', false);
        }
    });

    $('#valorcompra').on('input', function() {
        var valorCompra = parseFloat($(this).val()) || 0;
        var margenPrevisto = parseFloat({{ configuracion($tienda->id,'margen_previsto')['valor'] }}) || 0;
        var valorComercial = valorCompra * ((margenPrevisto / 100) + 1);
        $('#valorcomercial').val(valorComercial.toFixed(2));
    });
</script>