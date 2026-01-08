<div class="list-single-main-wrapper fl-wrap">
                        <div class="breadcrumbs gradient-bg fl-wrap">
                          <span>Editar Bien</span>
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
                                    <label>Tipo de Bien</label>
                                    <select id="bien_editar_idprestamo_tipobien">
                                        <option></option>
                                        @foreach ($tipobien as $value)
                                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <label>Valor Estimado</label>
                                    <input type="number" value="{{$prestamobien->valorestimado}}" id="bien_editar_valorestimado" min="0" step="0.01">
                                </div>
                                <div class="col-sm-6">
                                    <label>Descripción</label>
                                    <textarea id="bien_editar_descripcion" cols="30" rows="10">{{$prestamobien->descripcion}}</textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn mx-btn-post">Actualizar Bien</button>
                        </form>

<script>
$('#bien_editar_idprestamo_tipobien').select2({
                    placeholder: '-- Seleccionar Tipo de Bien--',
                    minimumResultsForSearch: -1
                }).val({{$prestamobien->idprestamo_tipobien}}).trigger('change');
</script>