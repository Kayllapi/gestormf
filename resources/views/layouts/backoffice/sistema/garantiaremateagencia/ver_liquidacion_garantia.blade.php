<div class="modal-header">
    <h5 class="modal-title">Garantias</h5>
    <button type="button" class="btn-close" id="modal-close-garantias-modificar" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <label for="cliente" class="col-sm-3 col-form-label">CLIENTE</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="cliente" value="{{ $credito->clientenombrecompleto }}" disabled>
                </div>
            </div>
            <div class="row">
                <label for="valor_comercial" class="col-sm-3 col-form-label">Valor comercial</label>
                <div class="col-sm-3">
                    <input type="number" class="form-control" id="valor_comercial" value="{{ $credito_garantias->sum('valor_comercial') }}">
                </div>
                <label for="saldo" class="col-sm-3 col-form-label">SALDO: C+I S/.</label>
                <div class="col-sm-3">
                    <input type="number" class="form-control" id="saldo" value="">
                </div>
            </div>
            <div class="row">
                <label for="valor_comercial" class="col-sm-3 col-form-label">Venta con descuento</label>
                <div class="col-sm-3">
                    <input type="number" class="form-control" id="valor_comercial">
                </div>
                <label for="saldo" class="col-sm-3 col-form-label">SALDO: C+I+Ic+M S/.</label>
                <div class="col-sm-3">
                    <input type="number" class="form-control" id="saldo">
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body"
                    style="
                        overflow-y: scroll;
                        padding: 0;
                        margin-top: 5px;
                        overflow-x: scroll;">
                    <table class="table table-striped table-hover" style="table-layout: fixed; width: 100%;" id="table-lista-credito">
                        <thead class="table-dark" style="position: sticky;top: 0;">
                            <tr>
                                <th width="70px;">CLIENTE</th>
                                <th width="90px;">RUC/DNI/CE</th>
                                <th width="90px;">TIPO DE GARANTIA</th>
                                <th width="100px;">DESCRIPCIÓN</th>
                                <th width="70px;">MODELO</th>
                                <th width="90px;">VALOR COMERCIAL</th>
                                <th width="95px;">ACCESORIOS</th>
                                <th width="90px;">COBERTURA</th>
                                <th width="70px;">COLOR</th>
                                <th width="90px;">CODIGO DE GARANTIA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($credito_garantias as $value)
                            <tr>
                                <td>{{$value->clientenombrecompleto}}</td>
                                <td>{{$value->dni}}</td>
                                <td>{{$value->garantias_tipogarantia}}</td>
                                <td>{{$value->descripcion}}</td>
                                <td>{{$value->garantias_modelo_tipo}}</td>
                                <td>{{$value->valor_comercial}}</td>
                                <td>{{$value->garantias_accesorio_doc}}</td>
                                <td>{{$value->valor_realizacion}}</td>
                                <td>{{$value->garantias_color}}</td>
                                <td>{{$value->garantias_codigo}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>