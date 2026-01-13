<div class="modal-header">
    <h5 class="modal-title">
        Compra y Venta de Bienes
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-1">
                <h5 class="modal-title text-center">
                    COMPRAS
                </h5>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body p-2">
                            <form action="javascript:;" id="form_movimientointernodinero_retiro1"> 
                                <div class="modal-body">
                                    <div class="row ">
                                        <div class="col-sm-2">
                                            <button type="button"
                                                class="btn btn-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#createCompraModal"
                                                onclick="create_compra()"
                                                style="font-size: 15px;">
                                                Registrar <br> Compra
                                            </button>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="row">
                                                <label class="col-sm-3 col-form-label" style="text-align: right;">Agencia</label>
                                                <div class="col-sm-7">
                                                    <select class="form-control" id="id_agencia_compra">
                                                        <option></option>
                                                        @foreach($agencias as $value)
                                                            <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-2">
                                                    <button type="button" class="btn btn-primary" onclick="search_compra()">
                                                        Buscar
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="col-sm-3 col-form-label" style="text-align: right;">Periodo</label>
                                                <div class="col-sm-3">
                                                    <input type="date" class="form-control" id="fecha_inicio_compra" value="{{ date('Y-m-d') }}"> 
                                                </div>
                                                <label class="col-sm-1 col-form-label" style="text-align: center;">al</label>
                                                <div class="col-sm-3">
                                                    <input type="date" class="form-control" id="fecha_fin_compra" value="{{ date('Y-m-d') }}">
                                                </div>
                                                <div class="col-sm-2">
                                                    <label class="chk" style="margin-top: 6px;">
                                                        <input type="checkbox" name="check_compra" id="check_compra">
                                                        <span class="checkmark"></span>
                                                        <span style="color: #c40000;">Todas</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body" style="overflow-y: scroll;height: 150px;padding: 0;margin-top: 5px;overflow-x: scroll;">
                            <table class="table table-striped table-hover table-bordered" id="table-lista-movimientointernodinero_retiro1">
                                <thead class="table-dark" style="position: sticky;top: 0;">
                                    <tr style="font-weight: bold;">
                                        <td>Estado(P-V)</td>
                                        <td>Descripción</td>
                                        <td>Serie</td>
                                        <td>Tipo</td>
                                        <td>Fecha Registro</td>
                                        <td>Valor Compra</td>
                                        <td>Chasis</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="6" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-2">
                                    <span>TOTAL: 0</span>
                                </div>
                                <div class="col-10 text-end">
                                    <button type="button" class="btn btn-info" style="background-color: #CFEBC5 !important;">
                                        <i class="fa-solid fa-pencil" style="color:#000 !important; font-weight: bold;"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-info" style="background-color: #FFC5C5 !important;">
                                        <i class="fa-solid fa-trash" style="color:#000 !important; font-weight: bold;"></i> Eliminar
                                    </button>
                                    <button type="button" class="btn btn-info" style="background-color: #F9F3B5 !important;">
                                        <i class="fa-solid fa-copy" style="color:#000 !important; font-weight: bold;"></i> Duplicar Váucher
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="exportar_pdf()" style="font-weight: bold;">
                                        <i class="fa-solid fa-file-pdf" style="color:#000 !important;font-weight: bold;"></i> Reporte
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-1">
                <h5 class="modal-title text-center">
                    VENTAS
                </h5>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body p-2">
                            <form action="javascript:;" id="form_movimientointernodinero_retiro1"> 
                                <div class="modal-body">
                                    <div class="row ">
                                        <div class="col-sm-2">
                                            <button type="button"
                                                class="btn btn-primary"
                                                onclick="create_venta()"
                                                style="font-size: 15px;">
                                                Registrar <br> Venta
                                            </button>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="row">
                                                <label class="col-sm-3 col-form-label" style="text-align: right;">Agencia</label>
                                                <div class="col-sm-7">
                                                    <select class="form-select" id="id_agencia_venta">
                                                        <option></option>
                                                        @foreach($agencias as $value)
                                                            <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-2">
                                                    <button type="button" class="btn btn-primary" onclick="search_venta()">
                                                        Buscar
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="col-sm-3 col-form-label" style="text-align: right;">Periodo</label>
                                                <div class="col-sm-3">
                                                    <input type="date" class="form-control" id="fecha_inicio_venta" value="{{ date('Y-m-d') }}"> 
                                                </div>
                                                <label class="col-sm-1 col-form-label" style="text-align: center;">al</label>
                                                <div class="col-sm-3">
                                                    <input type="date" class="form-control" id="fecha_fin_venta" value="{{ date('Y-m-d') }}">
                                                </div>
                                                <div class="col-sm-2">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body" style="overflow-y: scroll;height: 150px;padding: 0;margin-top: 5px;overflow-x: scroll;">
                            <table class="table table-striped table-hover table-bordered" id="table-lista-movimientointernodinero_retiro1">
                                <thead class="table-dark" style="position: sticky;top: 0;">
                                    <tr style="font-weight: bold;">
                                        <td>Descripción</td>
                                        <td>Serie</td>
                                        <td>Tipo</td>
                                        <td>Fecha Registro</td>
                                        <td>Valor Compra</td>
                                        <td>Chasis</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="6" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-2">
                                    <span>TOTAL: 0</span>
                                </div>
                                <div class="col-10 text-end">
                                    <button type="button" class="btn btn-info" style="background-color: #FFC5C5 !important;">
                                        <i class="fa-solid fa-trash" style="color:#000 !important; font-weight: bold;"></i> Eliminar
                                    </button>
                                    <button type="button" class="btn btn-info" style="background-color: #F9F3B5 !important;">
                                        <i class="fa-solid fa-copy" style="color:#000 !important; font-weight: bold;"></i> Duplicar Váucher
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="exportar_pdf()" style="font-weight: bold;">
                                        <i class="fa-solid fa-file-pdf" style="color:#000 !important;font-weight: bold;"></i> Reporte
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="createCompraModal" tabindex="-1" aria-labelledby="createCompraModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="createCompraModalLabel">Registro de Compra de Bienes</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-1">
                <div class="row">
                    <label class="col-sm-4 col-form-label" style="text-align: right;">Agencia *</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="id_agencia_compra_modal">
                            <option></option>
                            @foreach($agencias as $value)
                                <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-4 col-form-label" style="text-align: right;">Tipo de Bien *</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="idtipogarantia_modal">
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
                        <input type="text" class="form-control" id="descripcion_compra_modal">
                    </div>
                    <div class="col-sm-6">
                        <label class="col-form-label" style="text-align: right;">Serie/Motor/N° Partida *</label>
                        <input type="text" class="form-control" id="serie_motor_partida_compra_modal">
                    </div>
                    <div class="col-sm-6">
                        <label class="col-form-label" style="text-align: right;">Chasis</label>
                        <input type="text" class="form-control" id="chasis_compra_modal">
                    </div>
                    <div class="col-sm-6">
                        <label class="col-form-label" style="text-align: right;">Modelo/Tipo *</label>
                        <input type="text" class="form-control" id="modelo_tipo_compra_modal">
                    </div>
                    <div class="col-sm-6">
                        <label class="col-form-label" style="text-align: right;">Otros/Contraseña</label>
                        <input type="text" class="form-control" id="otros_contrasena_compra_modal">
                    </div>
                    <div class="col-sm-6">
                        <label class="col-form-label" style="text-align: right;">Estado *</label>
                        <select class="form-control" id="idestado_garantia_modal">
                            <option></option>
                            @foreach($estado_garantia as $value)
                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="col-form-label" style="text-align: right;">Color *</label>
                        <input type="text" class="form-control" id="color_compra_modal">
                    </div>
                    <div class="col-sm-3">
                        <label class="col-form-label" style="text-align: right;">Año Fabricación</label>
                        <input type="text" class="form-control" id="anio_fabricacion_compra_modal">
                    </div>
                    <div class="col-sm-3">
                        <label class="col-form-label" style="text-align: right;">Año Compra</label>
                        <input type="text" class="form-control" id="anio_compra_compra_modal">
                    </div>
                    <div class="col-sm-6">
                        <label class="col-form-label" style="text-align: right;">Placa (Vehículos)</label>
                        <input type="text" class="form-control" id="placa_vehiculos_compra_modal">
                    </div>
                    <div class="col-sm-6">
                        <label class="col-form-label" style="text-align: right;">Valor Compra (soles) *</label>
                        <input type="number" step="any" class="form-control" id="valor_compra_compra_modal">
                    </div>
                    <div class="col-sm-6">
                        <label class="col-form-label" style="text-align: right;">Valor Comercial ({{ configuracion($tienda->id,'margen_previsto')['valor'] }}%) *</label>
                        <input type="number" step="any" class="form-control" id="valor_comercial_compra_modal">
                    </div>
                    <div class="col-sm-6">
                        <label class="col-form-label" style="text-align: right;">Apellidos y Nombres (Vendedor) *</label>
                        <input type="text" class="form-control" id="vendedor_compra_modal">
                    </div>
                    <div class="col-sm-6">
                        <label class="col-form-label" style="text-align: right;">DNI (Vendedor) *</label>
                        <input type="text" class="form-control" id="dni_vendedor_compra_modal">
                    </div>
                    <div class="col-sm-3">
                        <label class="col-form-label" style="text-align: right;">Origen *</label>
                        <select class="form-control" id="origen_compra_modal">
                            <option></option>
                            <option value="1">SERFIP</option>
                            <option value="2">OTROS</option>
                        </select>
                    </div>
                    <div class="col-sm-5">
                        <label class="col-form-label" style="text-align: right;">N° de Ficha o Comprobante *</label>
                        <input type="text" class="form-control" id="numero_ficha_comprobante_compra_modal">
                    </div>
                    <div class="col-sm-4">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="chk" style="margin-top: 6px;">
                                    <input type="checkbox" name="tipo_pago" id="tipo_pago_caja" checked>
                                    <span class="checkmark"></span>
                                    <span>Caja</span>
                                </label>
                                <label class="chk" style="margin-top: 6px;">
                                    <input type="checkbox" name="tipo_pago" id="tipo_pago_banco">
                                    <span class="checkmark"></span>
                                    <span>Banco</span>
                                </label>
                                {{-- <input type="radio" name="tipo_pago" id="tipo_pago_caja" checked>
                                <label for="tipo_pago_caja">Caja</label>
                                <input type="radio" name="tipo_pago" id="tipo_pago_banco">
                                <label for="tipo_pago_banco">Banco</label> --}}
                            </div>
                            <div class="col-sm-12">
                                <label class="col-form-label" style="text-align: right;">N° Operación</label>
                                <input type="text" class="form-control" id="numero_operacion_compra_modal">
                            </div>
                            <div class="col-sm-12">
                                <label class="col-form-label" style="text-align: right;">Bancos</label>
                                <select class="form-control" id="banco_compra_modal">
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
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    sistema_select2({ input:'#id_agencia_compra', val:'{{$tienda->id}}' });
    sistema_select2({ input:'#id_agencia_venta', val:'{{$tienda->id}}' });
    sistema_select2({ input:'#id_agencia_compra_modal', val:'{{$tienda->id}}' });
    sistema_select2({ input:'#idtipogarantia_modal' });
    sistema_select2({ input:'#idestado_garantia_modal' });
    sistema_select2({ input:'#origen_compra_modal' });
    sistema_select2({ input:'#banco_compra_modal' });

    function create_compra(){
        console.log("Compra")
        $("#createCompraModal").modal('hide');
    }
    function search_compra(){
        console.log("Buscar Compra")
    }
    function create_venta(){
        
    }
    function search_venta(){
        
    }

    $('#valor_compra_compra_modal').on('input', function() {
        var valorCompra = parseFloat($(this).val()) || 0;
        var margenPrevisto = parseFloat({{ configuracion($tienda->id,'margen_previsto')['valor'] }}) || 0;
        var valorComercial = valorCompra * ((margenPrevisto / 100) + 1);
        $('#valor_comercial_compra_modal').val(valorComercial.toFixed(2));
    });
</script>