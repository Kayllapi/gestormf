<div id="carga_generarficha_liquidacion">
<form action="javascript:;" id="form_generarficha_liquidacion">
    <style>
        .form-check-label {
            margin-top: 5px;
            margin-left: 5px;
        }
    </style>
    <div class="modal-header">
        <h5 class="modal-title">FICHA DE REMATE</h5>
        <button type="button" class="btn-close" id="modal-close-exportar-venta" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="col-sm-12 mt-2 text-center">
            <div class="col-sm-12 mt-2">
                @php
                    $url = url('/backoffice/'.$tienda->id.'/garantiaremateagencia/0/edit?view=ver_generarficha_liquidacionpdf&idcredito='.$credito->id);
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