<div class="modal-header">
    <h5 class="modal-title">
        Créditos Garantizados
        <button type="button"
            class="btn btn-success mb-1"
            id="idbuscarcliente"
            data-bs-toggle="modal"
            data-bs-target="#exampleModal"
            onclick="buscarcliente()">
            <i class="fa fa-search"></i> Buscar Cliente
        </button>
        {{-- <div style="display:none;float: right;margin-left: 5px;" id="cont_irainicio">
            <button type="button" class="btn btn-primary" onclick="credito_garantizado()">
                <i class="fa fa-refresh"></i> Actualizar
            </button>
        </div> --}}
        <!-- Modal -->
        <div class="modal fade"
            id="exampleModal"
            tabindex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Buscar Cliente</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <select class="form-control" id="idclientesearch">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body p-2">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12">
                                        <div class="row">
                                            <label for="fecha_inicio" class="col-sm-2 col-form-label">CLIENTE:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control" type="text" name="cliente_garantizado" id="cliente_garantizado" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            {{-- <h5 class="modal-title" style="text-align: center;">LISTA DE CRÉDITOS</h5> --}}
            <div class="card">
                <div class="card-body" style="height: calc(-317px + 100vh);">
                    <table class="table table-striped table-hover" id="table-credito-garantizado">
                        <thead class="table-dark">
                            <tr>
                                <th>N°</th>
                                <th>CLIENTE AVALADO</th>
                                <th>RUC/DNI/CE</th>
                                <th>CUENTA</th>
                                <th>F.C</th>
                                <th class="campo_moneda">DESEMBOLSO (S/.)</th>
                                <th class="campo_moneda">SALDO (S/.)</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="campo_moneda"><b>TOTAL:</b></th>
                                <th class="campo_moneda"><b id="total_credito_garantizado"></b></th>
                                <th class="campo_moneda"><b id="total_credito_garantizado_saldo"></b></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // abrir el modal al cargar la página
    $('#idbuscarcliente').click();
    function buscarcliente(){
        setTimeout(function () { 
        $('#idclientesearch').select2('open');
        }, 500);
    }

    $('#idclientesearch').select2({
        ajax: {
            url:"{{url('backoffice/'.$tienda->id.'/creditogarantizado/show_credito')}}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    buscar: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        placeholder: '-- Seleccionar --',
        minimumInputLength: 2,
        theme: 'bootstrap-5',
        dropdownParent: $('#idclientesearch').parent().parent()
    });

    $("#idclientesearch").on("change", function(e) {
        credito_garantizado();
    });

    function credito_garantizado(){
        $.ajax({
            url:"{{url('backoffice/'.$tienda->id.'/creditogarantizado/showlistacreditos')}}",
            type:'GET',
            data: {
                idcliente : $('#idclientesearch :selected').val()
            },
            success: function (res){
                $('#cliente_garantizado').val(res.cliente.identificacion + " - " + res.cliente.nombrecompleto)
                $('#table-credito-garantizado > tbody').html(res.html);
                $('#total_credito_garantizado').text(res.total_credito_garantizado);
                $('#total_credito_garantizado_saldo').text(res.total_credito_garantizado_saldo);

                $("#exampleModal").modal('hide');
                // $('#cont_irainicio').css('display','block');
            }
        })
    }
</script>  

