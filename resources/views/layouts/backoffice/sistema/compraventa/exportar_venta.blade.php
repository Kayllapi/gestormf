<div id="carga_exportar_venta">
<form action="javascript:;" id="form_exportar_venta">
    <style>
        .form-check-label {
            margin-top: 5px;
            margin-left: 5px;
        }
    </style>
    <div class="modal-header">
        <h5 class="modal-title">REPORTE DE VENTA</h5>
        <button type="button" class="btn-close" id="modal-close-exportar-venta" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="col-sm-12 mt-2 text-center">
            <div class="col-sm-12 mt-2">
                @php
                    $url = url('/backoffice/'.$tienda->id.'/compraventa/0/edit?view=exportar_ventapdf&id_agencia_venta='.$id_agencia_venta.'&fecha_inicio_venta='.$fecha_inicio_venta.'&fecha_fin_venta='.$fecha_fin_venta);
                @endphp
                <iframe 
                    id="iframe_exportar_venta" 
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