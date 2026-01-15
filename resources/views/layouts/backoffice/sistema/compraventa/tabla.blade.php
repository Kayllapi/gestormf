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
                    <form action="javascript:;" id="form_movimientointernodinero_retiro1"> 
                        <div class="modal-body">
                            <div class="row ">
                                <div class="col-sm-2">
                                    <a href="javascript:;"
                                        class="btn btn-primary"
                                        style="font-size: 15px;"
                                        onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/compraventa/create?view=create_compra')}}'})">
                                        Registrar <br> Compra
                                    </a>
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
                                            <button type="button"
                                                class="btn btn-primary"
                                                onclick="search_compra()"
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
                            <table class="table table-striped table-hover table-bordered"
                                id="table-lista-compra"
                                style="table-layout: fixed; width: 100%;">
                                <thead class="table-dark" style="position: sticky;top: 0;">
                                    <tr style="font-weight: bold;">
                                        <td width="20px;">E</td>
                                        <td width="90px;">Código</td>
                                        <td width="180px;">Descripción</td>
                                        <td width="80px;">Serie</td>
                                        <td width="80px;">Tipo</td>
                                        <td width="180px;">Fecha Registro</td>
                                        <td width="80px;">Valor Compra</td>
                                        <td width="80px;">Valor Comercial</td>
                                        <td width="80px;">Chasis</td>
                                        <td width="80px;">Origen</td>
                                        <td width="80px;">Ficha</td>
                                        <td width="180px;">Vendedor</td>
                                        <td width="80px;">DNI</td>
                                        <td width="80px;">Lugar de Pago</td>
                                        <td width="90px;">Validación</td>
                                        <td width="80px;">Banco</td>
                                        <td width="110px;">Nro. Operación</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($cvcompras) == 0)
                                        <tr>
                                            <td colspan="17" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td>
                                        </tr>
                                    @else
                                        @foreach ($cvcompras as $value)
                                            <tr data-valor-columna="{{$value->id}}" onclick="show_data_compra(this)">
                                                <td>{{ $value->idestadocvcompra == 1 ? 'P' : 'V' }}</td>
                                                <td>{{ $value->idestadocvcompra == 1 ? 'CB' : 'VB' }}{{$value->codigo}}</td>
                                                <td>{{ Str::limit($value->descripcion, 25) }}</td>
                                                <td>{{ $value->serie_motor_partida }}</td>
                                                <td>{{ $value->modelo_tipo }}</td>
                                                <td>{{ date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A") }}</td>
                                                <td style="text-align: right;">{{ $value->valorcompra }}</td>
                                                <td style="text-align: right;">{{ $value->valorcomercial }}</td>
                                                <td>{{ $value->chasis }}</td>
                                                <td>{{ $value->idorigen == '1' ? 'SERFIP' : 'OTROS' }}</td>
                                                <td>{{ $value->numeroficha }}</td>
                                                <td>{{ Str::limit($value->vendedor_nombreapellidos, 25) }}</td>
                                                <td>{{ $value->vendedor_dni }}</td>
                                                <td>{{ $value->compra_idformapago == '1' ? 'CAJA' : 'BANCO' }}</td>
                                                <td>
                                                    @if ($value->compra_idformapago == '2')
                                                        <button type="button" class="btn btn-primary">
                                                            <i class="fa-solid fa-check"></i> Validar
                                                        </button>
                                                    @endif
                                                </td>
                                                <td>{{ $value->compra_banco }}</td>
                                                <td>{{ $value->compra_numerooperacion }}</td>
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
                                    <button type="button" class="btn btn-success" onclick="editar_compra()">
                                        <i class="fa-solid fa-pencil"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="eliminar_compra()">
                                        <i class="fa-solid fa-trash"></i> Eliminar
                                    </button>
                                    <button type="button" class="btn btn-warning" style="background-color: #F9F3B5 !important;">
                                        <i class="fa-solid fa-copy" style="color:#000 !important;"></i> Duplicar Váucher
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="exportar_pdf()" style="font-weight: bold;">
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
                                            <button type="button"
                                                class="btn btn-primary"
                                                onclick="search_venta()"
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
                            <table class="table table-striped table-hover table-bordered"
                                id="table-lista-venta"
                                style="table-layout: fixed; width: 100%;">
                                <thead class="table-dark" style="position: sticky;top: 0;">
                                    <tr style="font-weight: bold;">
                                        <td width="90px">Código</td>
                                        <td width="180px">Comprador</td>
                                        <td width="80px">DNI</td>
                                        <td width="180px">Fecha Registro</td>
                                        <td width="120px">Precio Venta Descuento</td>
                                        <td width="80px">Monto de Venta</td>
                                        <td width="80px;">Lugar de Pago</td>
                                        <td width="90px;">Validación</td>
                                        <td width="80px">Banco</td>
                                        <td width="110px">Nro. Operación</td>
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
                                    <button type="button" class="btn btn-warning" style="background-color: #F9F3B5 !important;">
                                        <i class="fa-solid fa-copy" style="color:#000 !important;"></i> Duplicar Váucher
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="exportar_pdf()" style="font-weight: bold;">
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

    function search_compra() {
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
        $row.addClass('selected');
    }
    function editar_compra() {
        const $selectedRow = $('#table-lista-compra tbody tr.selected');
        const id = $selectedRow.data('valor-columna');

        if (!id) {
            alert('Debe seleccionar un dato.');
            return;
        }

        const url = `{{ url('backoffice/'.$tienda->id) }}/compraventa/${id}/edit?view=editar_compra`;
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

    // Venta
    search_venta();
    function create_venta() {
        const $selectedRow = $('#table-lista-compra tbody tr.selected');
        const id = $selectedRow.data('valor-columna');

        if (!id) {
            alert('Debe seleccionar una compra.');
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
</script>