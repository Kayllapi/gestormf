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
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body" style="overflow-y: scroll;height: 270px;padding: 0;margin-top: 5px;overflow-x: scroll;">
                            <table class="table table-striped table-hover table-bordered" id="table-lista-compra">
                                <thead class="table-dark" style="position: sticky;top: 0;">
                                    <tr style="font-weight: bold;">
                                        <td>E</td>
                                        <td>Código</td>
                                        <td>Descripción</td>
                                        <td>Serie</td>
                                        <td>Tipo</td>
                                        <td>Fecha Registro</td>
                                        <td>Valor Compra</td>
                                        <td>Valor Comercial</td>
                                        <td>Chasis</td>
                                        <td>Origen</td>
                                        <td>Ficha</td>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-2">
                                    TOTAL: <span id="total_compra">0.00</span>
                                </div>
                                <div class="col-10 text-end">
                                    <button type="button" class="btn btn-info" style="background-color: #CFEBC5 !important;" onclick="editar_compra()">
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
                        <div class="card-body" style="overflow-y: scroll;height: 270px;padding: 0;margin-top: 5px;overflow-x: scroll;">
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
                                    TOTAL: <span id="total_venta">0.00</span>
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
<script>
    sistema_select2({ input:'#id_agencia_compra', val:'{{$tienda->id}}' });
    sistema_select2({ input:'#id_agencia_venta', val:'{{$tienda->id}}' });

    function search_compra(){
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
        let id = $(e).attr('data-valor-columna');
        $('tr.selected').removeClass('selected');
        $(e).addClass('selected');

        let url = "{{ url('backoffice/'.$tienda->id) }}/compraventa/"+id+"/edit?view=editar_compra";
        modal({ route: url }); // , size: 'modal-sm'
    }
//     function editar_compra(){
//         let id = $('#table-lista-compra > tbody > tr.selected').attr('data-valor-columna');
//         console.log(id);
//         $('tr.selected').removeClass('selected');
//         $('#table-lista-compra > tbody > tr.selected').addClass('selected');

//         if(id == "" || id == undefined ){
//             alert('Debe de seleccionar un dato.');
//             return false;
//         }

//         let url = "{{ url('backoffice/'.$tienda->id) }}/compraventa/"+id+"/edit?view=editar_compra";
//         modal({ route: url });
//    }
</script>