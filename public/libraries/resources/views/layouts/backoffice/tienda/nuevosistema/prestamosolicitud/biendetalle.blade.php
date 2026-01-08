<div class="list-single-main-wrapper fl-wrap">
                        <div class="breadcrumbs gradient-bg fl-wrap">
                          <span>Detalle de Bien</span>
                          <a class="btn btn-success" href="javascript:;" onclick="bien_index()"><i class="fa fa-angle-left"></i> Atras</a></a>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Tipo de Bien</label>
                                <input type="text" value="{{$prestamobien->nombre_tipobien}}" id="bien_detalle_idprestamo_tipobien" disabled>
                                <label>Valor Estimado</label>
                                <input type="number" value="{{$prestamobien->valorestimado}}" id="bien_detalle_valorestimado" min="0" step="0.01" disabled>
                            </div>
                            <div class="col-sm-6">
                                <label>Descripción</label>
                                <textarea id="bien_detalle_descripcion" cols="30" rows="10" disabled>{{$prestamobien->descripcion}}</textarea>
                            </div>
                        </div>

