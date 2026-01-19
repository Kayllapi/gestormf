<div id="carga_exportar_compra">
<form action="javascript:;" id="form_exportar_compra">
    <style>
        .form-check-label {
            margin-top: 5px;
            margin-left: 5px;
        }
    </style>
    <div class="modal-header">
        <h5 class="modal-title">REPORTE DE COMPRA</h5>
        <button type="button" class="btn-close" id="modal-close-exportar-compra" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="col-sm-12 mt-2 text-center">
            <div class="col-sm-12 mt-2">
                @php
                    $url = url('/backoffice/'.$tienda->id.'/compraventa/0/edit?view=exportar_comprapdf&id_agencia_compra='.$id_agencia_compra.'&fecha_inicio_compra='.$fecha_inicio_compra.'&fecha_fin_compra='.$fecha_fin_compra.'&check_compra='.$check_compra);
                @endphp
                <iframe 
                    id="iframe_exportar_compra" 
                    src="{{ $url }}#zoom=100"
                    frameborder="0"
                    width="100%"
                    style="height: calc(100vh - 62px);">
                </iframe>
            </div>
        </div>
    </div>
</form>   
</div>