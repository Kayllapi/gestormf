<form action="javascript:;"
    id="form-editar-compraventa"  
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/compraventa/'.$cvcompra->id) }}',
        method: 'PUT',
        data:{
            view: 'update_compra',
        }
    },
    function(resultado){
        $('#modal-close-edit-compraventa').click();
        search_compra();
    },this)">
    <div class="modal-header">
        <h1 class="modal-title">Editar Compra de Bienes</h1>
        <button type="button" class="btn-close" id="modal-close-edit-compraventa" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">Agencia *</label>
            <div class="col-sm-4">
                <select class="form-control" id="idtienda" disabled>
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
                <select class="form-control" id="idtipogarantia" disabled>
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
                <input type="text" class="form-control" id="descripcion" value="{{$cvcompra->descripcion}}" disabled>
            </div>
            <div class="col-sm-6">
                <label class="col-form-label" style="text-align: right;">Serie/Motor/N° Partida *</label>
                <input type="text" class="form-control" id="serie_motor_partida" value="{{$cvcompra->serie_motor_partida}}" disabled>
            </div>
            <div class="col-sm-6">
                <label class="col-form-label" style="text-align: right;">Chasis</label>
                <input type="text" class="form-control" id="chasis" value="{{$cvcompra->chasis}}" disabled>
            </div>
            <div class="col-sm-6">
                <label class="col-form-label" style="text-align: right;">Modelo/Tipo *</label>
                <input type="text" class="form-control" id="modelo_tipo" value="{{$cvcompra->modelo_tipo}}" disabled>
            </div>
            <div class="col-sm-6">
                <label class="col-form-label" style="text-align: right;">Otros/Contraseña</label>
                <input type="text" class="form-control" id="otros" value="{{$cvcompra->otros}}" disabled>
            </div>
            <div class="col-sm-6">
                <label class="col-form-label" style="text-align: right;">Estado *</label>
                <select class="form-control" id="idestado_garantia" disabled>
                    <option></option>
                    @foreach($estado_garantia as $value)
                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6">
                <label class="col-form-label" style="text-align: right;">Color *</label>
                <input type="text" class="form-control" id="color" value="{{$cvcompra->color}}" disabled>
            </div>
            <div class="col-sm-3">
                <label class="col-form-label" style="text-align: right;">Año Fabricación</label>
                <input type="text" class="form-control" id="fabricacion" value="{{$cvcompra->fabricacion}}" disabled>
            </div>
            <div class="col-sm-3">
                <label class="col-form-label" style="text-align: right;">Año Compra</label>
                <input type="text" class="form-control" id="compra" value="{{$cvcompra->compra}}" disabled>
            </div>
            <div class="col-sm-6">
                <label class="col-form-label" style="text-align: right;">Placa (Vehículos)</label>
                <input type="text" class="form-control" id="placa" value="{{$cvcompra->placa}}" disabled>
            </div>
            <div class="col-sm-4 mt-2">
                <div class="row">
                    <label class="col-sm-6 col-form-label" style="text-align: left;">Valor Compra (S/.) *</label>
                    <div class="col-sm-6">
                        <input type="number" step="any" class="form-control" id="valorcompra" value="{{$cvcompra->valorcompra}}" disabled>
                    </div>
                </div>
            </div>
            <div class="col-sm-8 mt-2">
                <div class="row">
                    <label class="col-sm-6 col-form-label" style="text-align: right;">
                        Valor Comercial <span style="color: #c40000;">(Mínimo {{ configuracion($tienda->id,'margen_previsto')['valor'] }}% más del V.Compra)</span> *
                    </label>
                    <div class="col-sm-6">
                        <input type="number" step="any" class="form-control" id="valorcomercial" value="{{$cvcompra->valorcomercial}}">
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <label class="col-form-label" style="text-align: right;">Apellidos y Nombres (Vendedor) *</label>
                <input type="text" class="form-control" id="vendedor_nombreapellidos" value="{{$cvcompra->vendedor_nombreapellidos}}" disabled>
            </div>
            <div class="col-sm-6">
                <label class="col-form-label" style="text-align: right;">RUC/DNI/CE (Vendedor) *</label>
                <input type="text" class="form-control" id="vendedor_dni" value="{{$cvcompra->vendedor_dni}}" disabled>
            </div>
            <div class="col-sm-3">
                <label class="col-form-label" style="text-align: right;">Origen *</label>
                <select class="form-control" id="idorigen" disabled>
                    <option></option>
                    <option value="1">SERFIP</option>
                    <option value="2">OTROS</option>
                </select>
            </div>
            <div class="col-sm-5">
                <label class="col-form-label" style="text-align: right;">N° de Ficha o Comprobante *</label>
                <input type="text" class="form-control" id="numeroficha" value="{{$cvcompra->numeroficha}}" disabled>
            </div>
            <div class="col-sm-4 mt-2">
                <div class="row">
                    <div class="col-sm-12">
                        <label class="custom-radio">
                            <input type="radio" name="compra_idformapago" id="compra_idformapago" value="1" @if($cvcompra->compra_idformapago == 1) checked @endif disabled>
                            <span></span>
                            Caja
                        </label>
                        <label class="custom-radio">
                            <input type="radio" name="compra_idformapago" id="compra_idformapago" value="2" @if($cvcompra->compra_idformapago == 2) checked @endif disabled>
                            <span></span>
                            Banco
                        </label>
                        {{-- <input type="radio" name="compra_idformapago" id="compra_idformapago" value="1" @if($cvcompra->compra_idformapago == 1) checked @endif disabled> Caja
                        <input type="radio" name="compra_idformapago" id="compra_idformapago" value="2" @if($cvcompra->compra_idformapago == 2) checked @endif disabled> Banco --}}
                    </div>
                    <div class="col-sm-12">
                        <label class="col-form-label" style="text-align: right;">Bancos</label>
                        <select class="form-control"
                            id="compra_idbanco"
                            disabled>
                            <option></option>
                            @foreach($bancos as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }} - ***{{ substr($value->cuenta, -5) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-12">
                        <label class="col-form-label" style="text-align: right;">N° Operación</label>
                        <input type="text"
                            class="form-control"
                            id="compra_numerooperacion"
                            value="{{$cvcompra->compra_numerooperacion}}"
                            disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-floppy-disk"></i> Guardar R.Compra
        </button>
    </div>
</form>

<script>
    sistema_select2({ input:'#idtienda', val:'{{$cvcompra->idtienda}}' });
    sistema_select2({ input:'#idtipogarantia', val:'{{$cvcompra->idtipogarantia}}' });
    sistema_select2({ input:'#idestado_garantia', val:'{{$cvcompra->idestado_garantia}}' });
    sistema_select2({ input:'#idorigen', val:'{{$cvcompra->idorigen}}' });
    sistema_select2({ input:'#compra_idbanco', val:'{{$cvcompra->compra_idbanco}}' });

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