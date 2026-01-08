<div class="list-single-main-wrapper fl-wrap">
                        <div class="breadcrumbs gradient-bg fl-wrap">
                          <span>Editar Garantia</span>
                          <a class="btn btn-success" href="javascript:;" onclick="bien_index()"><i class="fa fa-angle-left"></i> Atras</a></a>
                        </div>
                    </div>
                        <form action="javascript:;" 
                              onsubmit="callback({
                                    route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}}',
                                    method: 'PUT',
                                    data:   {
                                        view: 'editar-bien',
                                        idprestamo_creditobien: {{$prestamobien->id}}
                                    }
                                },
                                function(resultado){
                                    bien_index();
                                },this)">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label>Nombre del Producto *</label>
                                    <input type="text" value="{{$prestamobien->producto}}" id="bien_editar_producto">
                                    <label>Descripción</label>
                                    <textarea id="bien_editar_descripcion" cols="30" rows="10" onkeyup="texto_mayucula(this)">{{$prestamobien->descripcion}}</textarea>
                                </div>
                                <div class="col-sm-6">
                                    <label>Valor Estimado</label>
                                    <input type="number" value="{{$prestamobien->valorestimado}}" id="bien_editar_valorestimado" min="0" step="0.01">
                                    <label>Documento *</label>
                                    <select id="bien_editar_idprestamo_documento">
                                        <option></option>
                                        <option value="1">SIN DOCUMENTOS</option>
                                        <option value="2">COPIA/LEGALIZADO</option>
                                        <option value="3">ORIGINAL</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn mx-btn-post">Actualizar Garantia</button>
                        </form>

<script>
$('#bien_editar_idprestamo_documento').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
}).val({{$prestamobien->idprestamo_documento}}).trigger('change');
</script>