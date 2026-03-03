<div class="modal-header">
    <h5 class="modal-title">Garantias</h5>
    <button type="button" class="btn-close" id="modal-close-garantias-modificar" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <label for="cliente" class="col-sm-2 col-form-label">N° DE CUENTA</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="cliente" value="C{{ $credito->cuenta }}" disabled>
                </div>
                <label for="saldo" class="col-sm-5 col-form-label">Saldo de Deuda Programada (C+I+C.Ss/Otros+Cargo): S/.</label>
                <div class="col-sm-2">
                    <input type="number" class="form-control" id="saldo_deudaprogramada" value="{{ $cronograma['select_cuota'] }}" disabled>
                </div>
            </div>
            <div class="row">
                <label for="cliente" class="col-sm-2 col-form-label">CLIENTE</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="cliente" value="{{ $credito->clientenombrecompleto }}" disabled>
                </div>
                <label for="saldo" class="col-sm-5 col-form-label">Saldo de Deuda Total (C+I+C.Ss/Otros+Cargo+IC+IM+Custodia): S/.</label>
                <div class="col-sm-2">
                    <input type="number" class="form-control" id="saldo" value="{{ $cronograma['cuota_pendiente'] }}" disabled>
                </div>
            </div>
            <div class="row">
                <label for="valor_comercial" class="col-sm-2 col-form-label">Valor comercial</label>
                <div class="col-sm-2">
                    <input type="number" class="form-control" id="valor_comercial" value="" disabled>
                </div>
                <label for="valor_comercial_descuento" class="col-sm-2 col-form-label">V.Comercial con Descuento</label>
                <div class="col-sm-2">
                    <input type="number" class="form-control" id="valor_comercial_descuento" value="" disabled>
                </div>
            </div>
            <div class="row">
                <label for="valor_realizacion" class="col-sm-2 col-form-label">Valor de cobertura</label>
                <div class="col-sm-2">
                    <input type="number" class="form-control" id="valor_realizacion" value="" disabled>
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
                        height: 200px;
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
                                <th width="80px;">Serie/Motor/N°Partida</th>
                                <th width="70px;">MODELO</th>
                                <th width="90px;">VALOR COMERCIAL</th>
                                <th width="90px;">V.COMERCIAL CON DESCUENTO</th>
                                <th width="90px;">COBERTURA</th>
                                <th width="90px;"><span style="background-color: #E8E585;">PRECIO DE LIQUIDACIÓN</span></th>
                                <th width="70px;">OTROS</th>
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
                                    data-idcreditogarantia='{{ $value->id }}'
                                    data-valor-comercial='{{ $value->valor_comercial }}'
                                    data-valor-realizacion='{{ $value->valor_realizacion }}'>
                                    <td>{{$value->garantias_codigo}}</td>
                                    <td>{{$value->clientenombrecompleto}}</td>
                                    <td>{{$value->dni}}</td>
                                    <td>{{$value->garantias_tipogarantia}}</td>
                                    <td>{{$value->descripcion}}</td>
                                    <td>{{$value->garantias_serie_motor_partida}}</td>
                                    <td>{{$value->garantias_modelo_tipo}}</td>
                                    <td style="text-align: right">{{$value->valor_comercial}}</td>
                                    <td style="text-align: right">{{ number_format($value->valor_comercial - ($value->valor_comercial * configuracion($tienda->id,'porcentaje_descuento_liquidacion')['valor'] / 100), 2) }}</td>
                                    <td style="text-align: right">{{$value->valor_realizacion}}</td>
                                    <td style="background-color: #c1f5b5; text-align: right">{{$value->precioliquidacion}}</td>
                                    <td>{{$value->garantias_otros}}</td>
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
                <label for="precio_liquidacion_total" class="col-sm-2 col-form-label">P. DE LIQUIDACIÓN TOTAL</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="precio_liquidacion_total"
                        style="border-color: #969ca1;"
                        value="{{ number_format($credito_garantias->sum('precioliquidacion'), 2) }}" disabled>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-6"></div>
                <div class="col-sm-3">
                <button type="button" class="btn btn-primary" onclick="generarfichaLiquidacion()">GENERAR FICHA DE LIQUIDACIÓN</button>
                </div>
                @if($credito->idliquidaciongarantia==0)
                <div class="col-sm-3">
                <button type="button" class="btn btn-primary me-1" onclick="registrarprecioLiquidacion()" id="btn_precio_liquidacion">REGISTRAR PRECIO LIQUIDACIÓN</button>
                </div>
                @else
                <div class="col-sm-3">
                <div class="alert alert-danger me-1" style="padding-top: 4px;padding-bottom: 4px;">
                    <b>¡Ya tiene una Ficha de Liquidación!</b>
                </div>
                </div>
                @endif
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

        // Calcular el valor comercial con descuento
        var porcentaje_descuento_liquidacion = {{ configuracion($tienda->id,'porcentaje_descuento_liquidacion')['valor'] }};
        var valor_comercial_descuento = valor_comercial - (valor_comercial * porcentaje_descuento_liquidacion / 100);
        $('#valor_comercial_descuento').val(valor_comercial_descuento.toFixed(2));
    });

    function generarfichaLiquidacion() {
        var idcredito = {{ $credito->id }};
        if({{number_format($credito_garantias->sum('precioliquidacion'), 2)}}<{{ $cronograma['select_cuota'] }}){
            var mensaje = "El precio liquidación total (S/. {{number_format($credito_garantias->sum('precioliquidacion'), 2)}}) debe ser >= a la deuda programada (S/. {{$cronograma['select_cuota']}})";
            modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });  
            return false;
        }
        $('#btn_precio_liquidacion').addClass('d-none');
        modal({ route:"{{url('backoffice/'.$tienda->id.'/garantiaremateagencia/0/edit?view=ver_generarficha_liquidacion')}}&precio_liquidacion_total={{ number_format($credito_garantias->sum('precioliquidacion'), 2) }}&idcredito="+idcredito,  size: 'modal-fullscreen' });
    }

    function registrarprecioLiquidacion() {
        var selectedRow = $('#table-liquidacion-garantias tbody tr.selected');
        if (selectedRow.length === 0) {
            var mensaje = "Debe de seleccionar una garantía.";
            modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });  
            return false;
        }
        var idcreditogarantia = selectedRow.data('idcreditogarantia');
        modal({ route:"{{url('backoffice/'.$tienda->id.'/garantiaremateagencia/0/edit?view=ver_registrarprecio_liquidacion')}}&saldo_deudaprogramada={{ $cronograma['select_cuota'] }}&idcreditogarantia="+idcreditogarantia,  size: 'modal-sm' });
    }
</script>