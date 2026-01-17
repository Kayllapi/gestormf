<div class="modal-header">
    <h5 class="modal-title">
        Compra y Venta de Bienes
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <h5 class="modal-title text-center">
                COMPRAS
            </h5>
            <div class="row">
                <div class="col-sm-12">
                    <form action="javascript:;" id="form_compra"> 
                        <div class="modal-body">
                            <div class="row ">
                                <div class="col-sm-2">
                                    <a href="javascript:;"
                                        class="btn btn-primary"
                                        style="font-size: 15px; background-color: #FFBD81 !important;"
                                        onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/compraventa/create?view=create_compra')}}'})">
                                        Registrar <br> Compra
                                    </a>
                                </div>
                                <div class="col-sm-1 mt-3">
                                    <a href="javascript:;" 
                                        class="sistema-font" 
                                        onclick="search_compra()">
                                        <i class="fa-solid fa-arrows-rotate" style="color: #000;"></i>
                                    </a>
                                </div> 
                                <div class="col-sm-9">
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
                                            <button type="button"
                                                class="btn btn-primary"
                                                onclick="search_compraFiltro()"
                                                style="font-weight: bold;">
                                                <i class="fa-solid fa-search"></i> Filtrar
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
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body"
                            style="
                                overflow-y: scroll;
                                height: calc(100vh - 266px);
                                padding: 0;
                                margin-top: 5px;
                                overflow-x: scroll;">
                            <table class="table table-striped table-hover"
                                id="table-lista-compra"
                                style="table-layout: fixed; width: 100%;">
                                <thead class="table-dark" style="position: sticky;top: 0;">
                                    <tr style="font-weight: bold;">
                                        <td width="20px;">E</td>
                                        <td width="90px;">Cod. Oper.</td>
                                        <td width="155px;">Fecha Registro</td>
                                        <td width="190px;">Descripción</td>
                                        <td width="120px;">Serie/Motor/N°.P.</td>
                                        <td width="80px;">Chasis</td>
                                        <td width="90px;">Modelo/T.</td>
                                        <td width="90px;">Otros</td>
                                        <td width="80px;">Valor Comercial</td>
                                        <td width="80px;">Estado</td>
                                        <td width="80px;">Color</td>
                                        <td width="80px;">Año de Fabricación</td>
                                        <td width="80px;">Año de Compra</td>
                                        <td width="80px;">Placa del Vehículo</td>
                                        <td width="80px;">Origen</td>
                                        <td width="150px;">N° Ficha/Comprobante</td>
                                        <td width="180px;">Vendedor</td>
                                        <td width="90px;">RUC/DNI/CE</td>
                                        <td width="80px;">Lugar de Pago</td>
                                        <td width="110px;">Validación</td>
                                        <td width="80px;">Banco</td>
                                        <td width="110px;">N° Operación</td>
                                        <td width="90px;">Responsable</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($cvcompras) == 0)
                                        <tr>
                                            <td colspan="23" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td>
                                        </tr>
                                    @else
                                        @foreach ($cvcompras as $value)
                                            <tr data-valor-columna="{{$value->id}}"
                                                data-valor-compra_idformapago="{{$value->compra_idformapago}}"
                                                data-valor-validar_estado="{{$value->validar_estado}}"
                                                onclick="show_data_compra(this)">
                                                <td>{{ $value->idestadocvcompra == 1 ? 'P' : 'V' }}</td>
                                                <td>{{ $value->idestadocvcompra == 1 ? 'CB' : 'VB' }}{{$value->codigo}}</td>
                                                <td>{{ date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A") }}</td>
                                                <td>{{ Str::limit($value->descripcion, 25) }}</td>
                                                <td>{{ $value->serie_motor_partida }}</td>
                                                <td>{{ $value->chasis }}</td>
                                                <td>{{ $value->modelo_tipo }}</td>
                                                <td>{{ $value->otros }}</td>
                                                <td style="text-align: right;">{{ $value->valorcomercial }}</td>
                                                <td>{{ $value->estado }}</td>
                                                <td>{{ $value->color }}</td>
                                                <td>{{ $value->fabricacion }}</td>
                                                <td>{{ $value->compra }}</td>
                                                <td>{{ $value->placa }}</td>
                                                <td>{{ $value->idorigen == '1' ? 'SERFIP' : 'OTROS' }}</td>
                                                <td>{{ $value->numeroficha }}</td>
                                                <td>{{ Str::limit($value->vendedor_nombreapellidos, 25) }}</td>
                                                <td>{{ $value->vendedor_dni }}</td>
                                                <td>{{ $value->compra_idformapago == '1' ? 'CAJA' : 'BANCO' }}</td>
                                                <td>
                                                    @if ($value->compra_idformapago == '2')
                                                        @if ($value->validar_estado == 1)
                                                            <i class="fa-solid fa-check"></i> ({{ $value->validar_responsablecodigo }})
                                                        @else
                                                            <button type="button" class="btn btn-success" onclick="validar_compra({{ $value->id }})">
                                                                <i class="fa-solid fa-check"></i> Validar
                                                            </button>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>{{ $value->compra_banco }}</td>
                                                <td>{{ $value->compra_numerooperacion }}</td>
                                                <td>{{ $value->responsablecodigo }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-3">
                                    TOTAL: <span id="total_compra" style="font-weight: normal;">{{ number_format($cvcompras->sum('valorcompra'), 2, '.', '') ?? 0 }}</span>
                                </div>
                                <div class="col-9 text-end">
                                    <button type="button" class="btn btn-success" onclick="validar_editcompra()">
                                        <i class="fa-solid fa-pencil"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="eliminar_compra()">
                                        <i class="fa-solid fa-trash"></i> Eliminar
                                    </button>
                                    <button type="button" class="btn btn-warning" style="background-color: #F9F3B5 !important;" onclick="vaucher_compra()">
                                        <i class="fa-solid fa-copy" style="color:#000 !important;"></i> Duplicar Voucher
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="reporte_compra()" style="font-weight: bold;">
                                        <i class="fa-solid fa-file-pdf"></i> Reporte
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h5 class="modal-title text-center">
                VENTAS
            </h5>
            <div class="row">
                <div class="col-sm-12">
                    <form action="javascript:;" id="form_venta"> 
                        <div class="modal-body">
                            <div class="row ">
                                <div class="col-sm-2">
                                    <button type="button"
                                        class="btn btn-primary"
                                        onclick="create_venta()"
                                        style="font-size: 15px; background-color: #FFBD81 !important;">
                                        Registrar <br> Venta
                                    </button>
                                </div>
                                <div class="col-sm-1 mt-3">
                                    <a href="javascript:;" 
                                        class="sistema-font" 
                                        onclick="search_venta()">
                                        <i class="fa-solid fa-arrows-rotate" style="color: #000;"></i>
                                    </a>
                                </div> 
                                <div class="col-sm-9">
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
                                            <button type="button"
                                                class="btn btn-primary"
                                                onclick="search_ventaFiltro()"
                                                style="font-weight: bold;">
                                                <i class="fa-solid fa-search"></i> Filtrar
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
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body" 
                            style="
                                overflow-y: scroll;
                                height: calc(100vh - 266px);
                                padding: 0;
                                margin-top: 5px;
                                overflow-x: scroll;">
                            <table class="table table-striped table-hover"
                                id="table-lista-venta"
                                style="table-layout: fixed; width: 100%;">
                                <thead class="table-dark" style="position: sticky;top: 0;">
                                    <tr style="font-weight: bold;">
                                        <td width="90px">Cod. Oper.</td>
                                        <td width="155px;">Fecha Registro</td>
                                        <td width="190px;">Descripción</td>
                                        <td width="120px;">Serie/Motor/N°.P.</td>
                                        <td width="80px;">Chasis</td>
                                        <td width="90px;">Modelo/T.</td>
                                        <td width="90px;">Otros</td>
                                        <td width="80px">Valor Comercial</td>
                                        <td width="120px">Precio Venta Descuento</td>
                                        <td width="80px">Precio Venta Final</td>
                                        <td width="80px;">Estado</td>
                                        <td width="80px;">Color</td>
                                        <td width="80px;">Año de Fabricación</td>
                                        <td width="80px;">Año de Compra</td>
                                        <td width="80px;">Placa del Vehículo</td>
                                        <td width="80px;">Origen</td>
                                        <td width="150px;">N° Ficha/Comprobante</td>
                                        <td width="180px">Comprador</td>
                                        <td width="90px">RUC/DNI/CE</td>
                                        <td width="80px;">Lugar de Pago</td>
                                        <td width="110px;">Validación</td>
                                        <td width="80px;">Banco</td>
                                        <td width="110px;">N° Operación</td>
                                        <td width="90px;">Responsable</td>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-3">
                                    TOTAL: <span id="total_venta" style="font-weight: normal;">{{ number_format($cvventas->sum('venta_precio_venta_descuento'), 2, '.', '') ?? 0 }}</span>
                                </div>
                                <div class="col-9 text-end">
                                    <button type="button" class="btn btn-danger" onclick="eliminar_venta()">
                                        <i class="fa-solid fa-trash"></i> Eliminar
                                    </button>
                                    <button type="button" class="btn btn-warning" style="background-color: #F9F3B5 !important;" onclick="vaucher_venta()">
                                        <i class="fa-solid fa-copy" style="color:#000 !important;"></i> Duplicar Voucher
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="reporte_venta()" style="font-weight: bold;">
                                        <i class="fa-solid fa-file-pdf"></i> Reporte
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
<script>
    sistema_select2({ input:'#id_agencia_compra', val:'{{$tienda->id}}' });
    sistema_select2({ input:'#id_agencia_venta', val:'{{$tienda->id}}' });

    // Compra
    function search_compra() {
        $.ajax({
            url:"{{url('backoffice/0/compraventa/show_table_compra')}}",
            type:'GET',
            data:{
                id_agencia_compra: $('#id_agencia_compra').val(),
                fecha_inicio_compra: null,
                fecha_fin_compra: null,
                check_compra: 0,
            },
            success: function (res){
                $('#table-lista-compra > tbody').html(res.html);
                $('#total_compra').html(res.total);
            }
        })
    }
    function search_compraFiltro() {
        $.ajax({
            url:"{{url('backoffice/0/compraventa/show_table_compra')}}",
            type:'GET',
            data:{
                id_agencia_compra: $('#id_agencia_compra').val(),
                fecha_inicio_compra: $('#fecha_inicio_compra').val(),
                fecha_fin_compra: $('#fecha_fin_compra').val(),
                check_compra: $('#check_compra').is(':checked') ? 1 : 0,
            },
            success: function (res){
                $('#table-lista-compra > tbody').html(res.html);
                $('#total_compra').html(res.total);
            }
        })
    }
    function show_data_compra(e) {
        const $row = $(e);
        $('#table-lista-compra tbody tr').removeClass('selected');
        $('#table-lista-venta tbody tr').removeClass('selected');
        $row.addClass('selected');
    }
    function validar_compra(id) {
        const url = `{{ url('backoffice/'.$tienda->id) }}/compraventa/${id}/edit?view=edit_validar_compra`;
        modal({ route: url, size: 'modal-sm' });
    }
    function validar_editcompra() {
        const $selectedRow = $('#table-lista-compra tbody tr.selected');
        const id = $selectedRow.data('valor-columna');

        if (!id) {
            alert('Debe seleccionar un dato.');
            return;
        }

        const url = `{{ url('backoffice/'.$tienda->id) }}/compraventa/${id}/edit?view=edit_validar_editcompra`;
        modal({ route: url, size: 'modal-sm' });
    }
    function editar_compra() {
        const $selectedRow = $('#table-lista-compra tbody tr.selected');
        const id = $selectedRow.data('valor-columna');

        if (!id) {
            alert('Debe seleccionar un dato.');
            return;
        }

        const url = `{{ url('backoffice/'.$tienda->id) }}/compraventa/${id}/edit?view=edit_compra`;
        modal({ route: url });
    }
    function eliminar_compra() {
        const $selectedRow = $('#table-lista-compra tbody tr.selected');
        const id = $selectedRow.data('valor-columna');

        if (!id) {
            alert('Debe seleccionar un dato.');
            return;
        }

        const url = `{{ url('backoffice/'.$tienda->id) }}/compraventa/${id}/edit?view=eliminar_compra`;
        modal({ route: url, size: 'modal-sm' });
    }
    function reporte_compra() {
        const url = `{{ url('backoffice/'.$tienda->id) }}/compraventa/0/edit?view=edit_reporte_compra`;
        modal({ route: url, size: 'modal-sm' });
    }
    function exportar_compra() {
        const url = `{{ url('backoffice/'.$tienda->id) }}/compraventa/0/edit?view=exportar_compra
                &id_agencia_compra=${$('#id_agencia_compra').val()}
                &fecha_inicio_compra=${$('#fecha_inicio_compra').val()}
                &fecha_fin_compra=${$('#fecha_fin_compra').val()}
                &check_compra=${$('#check_compra').is(':checked') ? 1 : 0}`;
        modal({ route: url, size: 'modal-fullscreen' });
    }
    function vaucher_compra() {
        const $selectedRow = $('#table-lista-compra tbody tr.selected');
        const id = $selectedRow.data('valor-columna');

        if (!id) {
            alert('Debe seleccionar un dato.');
            return;
        }

        const url = `{{ url('backoffice/'.$tienda->id) }}/compraventa/${id}/edit?view=vaucher_compra`;
        modal({ route: url, size: 'modal-sm' });
    }
    function vaucher_compraCreate(id) {
        const url = `{{ url('backoffice/'.$tienda->id) }}/compraventa/${id}/edit?view=vaucher_compra`;
        modal({ route: url, size: 'modal-sm' });
    }

    // Venta
    search_venta();
    function create_venta() {
        const $selectedRow = $('#table-lista-compra tbody tr.selected');
        const id = $selectedRow.data('valor-columna');
        const compra_idformapago = $selectedRow.data('valor-compra_idformapago');
        const validar_estado = $selectedRow.data('valor-validar_estado');

        if (!id) {
            alert('Debe seleccionar una compra.');
            return;
        }
        if (compra_idformapago == 2 && validar_estado != 1) {
            alert('La compra debe ser validada.');
            return;
        }

        const url = `{{ url('backoffice/'.$tienda->id) }}/compraventa/create?view=create_venta&idcvcompra=${id}`;
        modal({ route: url, size: 'modal-sm' });
    }
    function search_venta() {
        $.ajax({
            url:"{{url('backoffice/0/compraventa/show_table_venta')}}",
            type:'GET',
            data:{
                id_agencia_venta: $('#id_agencia_venta').val(),
                fecha_inicio_venta: '',
                fecha_fin_venta: '',
            },
            success: function (res){
                $('#table-lista-venta > tbody').html(res.html);
                $('#total_venta').html(res.total);
            }
        })
    }
    function search_ventaFiltro() {
        $.ajax({
            url:"{{url('backoffice/0/compraventa/show_table_venta')}}",
            type:'GET',
            data:{
                id_agencia_venta: $('#id_agencia_venta').val(),
                fecha_inicio_venta: $('#fecha_inicio_venta').val(),
                fecha_fin_venta: $('#fecha_fin_venta').val(),
            },
            success: function (res){
                $('#table-lista-venta > tbody').html(res.html);
                $('#total_venta').html(res.total);
            }
        })
    }
    function show_data_venta(e) {
        const $row = $(e);
        $('#table-lista-compra tbody tr').removeClass('selected');
        $('#table-lista-venta tbody tr').removeClass('selected');
        $row.addClass('selected');
    }
    function eliminar_venta() {
        const $selectedRow = $('#table-lista-venta tbody tr.selected');
        const id = $selectedRow.data('valor-columna');

        if (!id) {
            alert('Debe seleccionar un dato.');
            return;
        }

        const url = `{{ url('backoffice/'.$tienda->id) }}/compraventa/${id}/edit?view=eliminar_venta`;
        modal({ route: url, size: 'modal-sm' });
    }
    function reporte_venta() {
        const url = `{{ url('backoffice/'.$tienda->id) }}/compraventa/0/edit?view=edit_reporte_venta`;
        modal({ route: url, size: 'modal-sm' });
    }
    function exportar_venta() {
        const url = `{{ url('backoffice/'.$tienda->id) }}/compraventa/0/edit?view=exportar_venta
                &id_agencia_venta=${$('#id_agencia_venta').val()}
                &fecha_inicio_venta=${$('#fecha_inicio_venta').val()}
                &fecha_fin_venta=${$('#fecha_fin_venta').val()}`;
        modal({ route: url, size: 'modal-fullscreen' });
    }
    function vaucher_venta() {
        const $selectedRow = $('#table-lista-venta tbody tr.selected');
        const id = $selectedRow.data('valor-columna');

        if (!id) {
            alert('Debe seleccionar un dato.');
            return;
        }

        const url = `{{ url('backoffice/'.$tienda->id) }}/compraventa/${id}/edit?view=vaucher_venta`;
        modal({ route: url, size: 'modal-sm' });
    }
    function vaucher_ventaCreate(id) {
        const url = `{{ url('backoffice/'.$tienda->id) }}/compraventa/${id}/edit?view=vaucher_venta`;
        modal({ route: url, size: 'modal-sm' });
    }
    function validar_venta(id) {
        const url = `{{ url('backoffice/'.$tienda->id) }}/compraventa/${id}/edit?view=edit_validar_venta`;
        modal({ route: url, size: 'modal-sm' });
    }
</script>