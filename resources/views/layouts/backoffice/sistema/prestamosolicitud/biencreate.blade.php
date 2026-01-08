<div class="list-single-main-wrapper fl-wrap">
                        <div class="breadcrumbs gradient-bg fl-wrap">
                          <span>Registrar Garantia</span>
                          <a class="btn btn-success" href="javascript:;" onclick="bien_index()"><i class="fa fa-angle-left"></i> Atras</a>
                        </div>
                    </div>
                    <form action="javascript:;" 
                          onsubmit="callback({
                                route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud',
                                method: 'POST',
                                data:   {
                                    view: 'registrar-bien',
                                    idprestamo_credito: {{ $prestamocredito->id }}
                                }
                            },
                            function(resultado){
                                bien_index();
                            },this)">
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Nombre del Producto *</label>
                                <input type="text" id="bien_producto" onkeyup="texto_mayucula(this)">
                                <label>Descripción *</label>
                                <textarea id="bien_descripcion" cols="30" rows="10" onkeyup="texto_mayucula(this)"></textarea>
                            </div>
                            <div class="col-sm-6">
                                <label>Valor Estimado *</label>
                                <input type="number" id="bien_valorestimado" min="0" step="0.01">
                                <label>Documento *</label>
                                <select id="bien_idprestamo_documento">
                                    <option></option>
                                    <option value="1">SIN DOCUMENTOS</option>
                                    <option value="2">COPIA/LEGALIZADO</option>
                                    <option value="3">ORIGINAL</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn mx-btn-post">Guardar Garantia</button>
                    </form>

<script>
$('#bien_idprestamo_documento').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
});
</script>