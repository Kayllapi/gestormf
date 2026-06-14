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
            <h5 class="modal-title" style="text-align: center;">LISTA DE CRÉDITOS</h5>
            <div class="card">
                <div class="card-body" style="height: calc(-317px + 100vh);">
                    <table class="table table-striped table-hover" id="table-credito-garantizado">
                        <thead class="table-dark">
                            <tr>
                                <th>N°</th>
                                <th>CLIENTE</th>
                                <th>RUC/DNI/CE</th>
                                <th>CUENTA</th>
                                <th class="campo_moneda">SALDOS (S/.)</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="campo_moneda"><b>Total:</b></th>
                                <th class="campo_moneda"><b id="total_credito_garantizado"></b></th>
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
                $('#table-credito-garantizado > tbody').html(res.html);
                $('#total_credito_garantizado').text(res.total_credito_garantizado);
                $("#exampleModal").modal('hide');
            }
        })
    }
</script>  

