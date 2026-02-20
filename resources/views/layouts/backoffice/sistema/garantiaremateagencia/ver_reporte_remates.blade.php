<div id="carga_generarficha_liquidacion">
<form action="javascript:;" id="form_generarficha_liquidacion">
    <div class="modal-header">
        <h5 class="modal-title">REPORTE DE REMATES</h5>
        <button type="button" class="btn-close" id="modal-close-exportar-venta" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="col-sm-12 mt-2 text-center">
            <div class="col-sm-12 mt-2">
                @php
                    $url = url('/backoffice/'.$tienda->id.'/garantiaremateagencia/0/edit?view=ver_reporte_rematespdf');
                @endphp
                <iframe 
                    id="iframe_generarficha_liquidacion" 
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