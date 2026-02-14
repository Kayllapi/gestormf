<div class="modal-header">
    <h5 class="modal-title">Garantias</h5>
    <button type="button" class="btn-close" id="modal-close-garantias-modificar" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <label for="cliente" class="col-sm-2 col-form-label">N° DE CUENTA</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="cliente" value="C{{ $credito->cuenta }}" disabled>
                </div>
            </div>
            <div class="row">
                <label for="cliente" class="col-sm-2 col-form-label">CLIENTE</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="cliente" value="{{ $credito->clientenombrecompleto }}" disabled>
                </div>
            </div>
            <div class="row">
                <label for="valor_comercial" class="col-sm-2 col-form-label">Valor comercial</label>
                <div class="col-sm-2">
                    <input type="number" class="form-control" id="valor_comercial" value="">
                </div>
                <label for="saldo" class="col-sm-3 col-form-label">Saldo de Deuda Programada (C+I): S/.</label>
                <div class="col-sm-2">
                    <input type="number" class="form-control" id="saldo" value="">
                </div>
            </div>
            <div class="row">
                <label for="valor_realizacion" class="col-sm-2 col-form-label">Valor de cobertura</label>
                <div class="col-sm-2">
                    <input type="number" class="form-control" id="valor_realizacion" value="">
                </div>
                <label for="saldo" class="col-sm-3 col-form-label">Saldo de Deuda Total (C+I+Ic+M): S/.</label>
                <div class="col-sm-2">
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
                    <table class="table table-striped table-hover dataTable no-footer"
                        style="table-layout: fixed; width: 100%;"
                        id="table-liquidacion-garantias">
                        <thead class="table-dark" style="position: sticky;top: 0;">
                            <tr>
                                <th width="90px;">CODIGO DE GARANTIA</th>
                                <th width="70px;">CLIENTE</th>
                                <th width="90px;">RUC/DNI/CE</th>
                                <th width="90px;">TIPO DE GARANTIA</th>
                                <th width="100px;">DESCRIPCIÓN</th>
                                <th width="140px;">Serie/Motor/N°Partida</th>
                                <th width="70px;">MODELO</th>
                                <th width="70px;">OTROS</th>
                                <th width="90px;">VALOR COMERCIAL</th>
                                <th width="90px;">COBERTURA</th>
                                <th width="95px;">ACCESORIOS</th>
                                <th width="70px;">COLOR</th>
                                <th width="100px;">AÑO DE FABRICACIÓN</th>
                                <th width="100px;">AÑO DE COMPRA</th>
                                <th width="100px;">PLACA DEL VEHÍCULO</th>
                                <th width="100px;">DETALLE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($credito_garantias as $value)
                            <tr data-idcredito='{{ $value->idcredito }}'
                                data-valor-comercial='{{ $value->valor_comercial }}'
                                data-valor-realizacion='{{ $value->valor_realizacion }}'>
                                <td>{{$value->garantias_codigo}}</td>
                                <td>{{$value->clientenombrecompleto}}</td>
                                <td>{{$value->dni}}</td>
                                <td>{{$value->garantias_tipogarantia}}</td>
                                <td>{{$value->descripcion}}</td>
                                <td>{{$value->garantias_serie_motor_partida}}</td>
                                <td>{{$value->garantias_modelo_tipo}}</td>
                                <td>{{$value->garantias_otros}}</td>
                                <td>{{$value->valor_comercial}}</td>
                                <td>{{$value->valor_realizacion}}</td>
                                <td>{{$value->garantias_accesorio_doc}}</td>
                                <td>{{$value->garantias_color}}</td>
                                <td>{{$value->garantias_fabricacion}}</td>
                                <td>{{$value->garantias_compra}}</td>
                                <td>{{$value->garantias_placa}}</td>
                                <td>{{$value->garantias_detalle_garantia}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-12 mt-2">
            <div class="row">
                <div class="col-sm-8"></div>
                <label for="precio_liquidacion" class="col-sm-2 col-form-label">PRECIO DE LIQUIDACIÓN</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="precio_liquidacion" value="" disabled>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-6"></div>
                <button type="button" class="btn btn-primary col-sm-3" onclick="generarfichaLiquidacion()">GENERAR FICHA DE LIQUIDACIÓN</button>
                <button type="button" class="btn btn-primary col-sm-3" onclick="registrarprecioLiquidacion()">REGISTRAR PRECIO LIQUIDACIÓN</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#table-liquidacion-garantias').on('click', 'tbody tr', function () {
        $('#table-liquidacion-garantias tbody tr').removeClass('selected');
        $(this).addClass('selected');

        var valor_comercial = $(this).data('valor-comercial');
        var valor_realizacion = $(this).data('valor-realizacion');
        $('#valor_comercial').val(valor_comercial);
        $('#valor_realizacion').val(valor_realizacion);
        $('#precio_liquidacion').val(valor_comercial);
    });

    function generarfichaLiquidacion() {
        var selectedRow = $('#table-liquidacion-garantias tbody tr.selected');
        if (selectedRow.length === 0) {
            var mensaje = "Debe de seleccionar una garantía.";
            modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });
            return false;
        }
        var idcredito = selectedRow.data('idcredito');
        modal({ route:"{{url('backoffice/'.$tienda->id.'/garantiaremateagencia/0/edit?view=ver_generarficha_liquidacion')}}&idcredito="+idcredito,  size: 'modal-fullscreen' });
    }

    function registrarprecioLiquidacion() {
        var selectedRow = $('#table-liquidacion-garantias tbody tr.selected');
        if (selectedRow.length === 0) {
            var mensaje = "Debe de seleccionar una garantía.";
            modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });  
            return false;
        }
        var idcredito = selectedRow.data('idcredito');
        modal({ route:"{{url('backoffice/'.$tienda->id.'/garantiaremateagencia/0/edit?view=ver_registrarprecio_liquidacion')}}&idcredito="+idcredito,  size: 'modal-sm' });
    }
</script>