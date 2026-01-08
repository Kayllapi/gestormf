<form @include('app.nuevosistema.submit',['method' => 'PUT',
      'view' => 'confirmarcierre',
      'id'   =>$s_aperturacierre->id])>
    <div class="row">
        <div class="col-md-6">
            <div class="accordion">
                <a class="toggle" href="#"> Ingresos : ({{ $fectivo['total_ingresosdiversos'] }})<i class="fa fa-angle-down"></i></a>
                <div class="accordion-inner">
                    <div class="table-responsive">
                      <table class="table" id="tabla-contenido">
                          <thead class="thead-dark">
                            <tr>
                              <th>Código</th>
                              <th>Detalle</th>
                              <th>Monto</th>
                              <th>Fecha</th>
                            </tr>
                          </thead>
                          <tbody>
                              @foreach($fectivo['ingresosdiversos'] as $value)
                              <tr>
                                <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                <td>{{$value->conceptomovimientonombre}} - {{$value->concepto}}</td>
                                <td>{{$value->monto}}</td>
                                <td>{{$value->fechaconfirmacion}}</td>
                              </tr>
                              @endforeach
                          </tbody>
                      </table>
                   </div>
                </div>
                <a class="toggle" href="#"> Egresos : ({{ $fectivo['total_egresosdiversos'] }})<i class="fa fa-angle-down"></i></a>
                <div class="accordion-inner">
                   <div class="table-responsive">
                      <table class="table" id="tabla-contenido">
                          <thead class="thead-dark">
                            <tr>
                              <th>Código</th>
                              <th>Detalle</th>
                              <th>Monto</th>
                              <th>Fecha</th>
                            </tr>
                          </thead>
                          <tbody>
                              @foreach($fectivo['egresosdiversos'] as $value)
                              <tr>
                                <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                <td>{{$value->conceptomovimientonombre}} - {{$value->concepto}}</td>
                                <td>{{$value->monto}}</td>
                                <td>{{$value->fechaconfirmacion}}</td>
                              </tr>
                              @endforeach
                          </tbody>
                      </table>
                   </div>
                </div>
                <a class="toggle" href="#"> Compras : ({{ $fectivo['total_compras'] }})<i class="fa fa-angle-down"></i></a>
                <div class="accordion-inner">
                    <div class="table-responsive">
                      <table class="table" id="tabla-contenido">
                          <thead class="thead-dark">
                            <tr>
                              <th>Código</th>
                              <th>Proveedor</th>
                              <th>Monto</th>
                              <th>Fecha</th>
                            </tr>
                          </thead>
                          <tbody>
                              @foreach($fectivo['compras'] as $value)
                              <tr>
                                <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                <td>{{$value->proveedor}}</td>
                                <td>{{number_format($value->totalredondeado, 2, '.', '')}}</td>
                                <td>{{$value->fechaconfirmacion}}</td>
                              </tr>
                              @endforeach
                          </tbody>
                      </table>
                   </div>
                </div>
                <a class="toggle" href="#"> Devolución de Compras : ({{ $fectivo['total_compradevoluciones'] }})<i class="fa fa-angle-down"></i></a>
                <div class="accordion-inner">
                    <div class="table-responsive">
                      <table class="table" id="tabla-contenido">
                          <thead class="thead-dark">
                            <tr>
                              <th>Cod. Compra</th>
                              <th>Cod. Impresión</th>
                              <th>Monto</th>
                              <th>Fecha</th>
                            </tr>
                          </thead>
                          <tbody>
                              @foreach($fectivo['compradevoluciones'] as $value)
                              <tr>
                                <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                <td>{{ str_pad($value->codigoimpresion, 8, "0", STR_PAD_LEFT) }}</td>
                                <td>{{number_format($value->totalredondeado, 2, '.', '')}}</td>
                                <td>{{$value->fechaconfirmacion}}</td>
                              </tr>
                              @endforeach
                          </tbody>
                      </table>
                   </div>
                </div>
                <a class="toggle" href="#"> Ventas : ({{ $fectivo['total_ventas'] }})<i class="fa fa-angle-down"></i></a>
                <div class="accordion-inner">
                    <div class="table-responsive">
                      <table class="table" id="tabla-contenido">
                          <thead class="thead-dark">
                            <tr>
                              <th>Código</th>
                              <th>Cliente</th>
                              <th>Monto</th>
                              <th>Fecha</th>
                            </tr>
                          </thead>
                          <tbody>
                              @foreach($fectivo['ventas'] as $value)
                              <tr>
                                <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                <td>{{$value->cliente}}</td>
                                <td>{{number_format($value->totalredondeado, 2, '.', '')}}</td>
                                <td>{{$value->fechaconfirmacion}}</td>
                              </tr>
                              @endforeach
                          </tbody>
                      </table>
                   </div>
                </div>
                <a class="toggle" href="#"> Devolución de Ventas : ({{ $fectivo['total_ventadevoluciones'] }})<i class="fa fa-angle-down"></i></a>
                <div class="accordion-inner">
                    <div class="table-responsive">
                      <table class="table" id="tabla-contenido">
                          <thead class="thead-dark">
                            <tr>
                              <th>Cod. Venta</th>
                              <th>Cod. Impresión</th>
                              <th>Monto</th>
                              <th>Fecha</th>
                            </tr>
                          </thead>
                          <tbody>
                              @foreach($fectivo['ventadevoluciones'] as $value)
                              <tr>
                                <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                <td>{{ str_pad($value->codigoimpresion, 8, "0", STR_PAD_LEFT) }}</td>
                                <td>{{number_format($value->totalredondeado, 2, '.', '')}}</td>
                                <td>{{$value->fechaconfirmacion}}</td>
                              </tr>
                              @endforeach
                          </tbody>
                      </table>
                   </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <table class="table table-cierrecaja" style="margin-bottom:5px;">
              <thead class="thead-dark">
                <tr>
                  <th colspan="2" style="text-align: center;">Sumatoria Total </th>
                </tr>
              </thead>
              <tbody>
                <tr class="table-warning">
                  <td class="td-ingreso">Apertura</td>
                  <td class="td-moneda">{{ $fectivo['total_apertura'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso">Ingreso</td>
                  <td class="td-moneda">{{ $fectivo['total_ingresosdiversos'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso">Ventas</td>
                  <td class="td-moneda">{{ $fectivo['total_ventas'] }}</td>
                </tr>
                <tr>
                  <td class="td-ingreso">Devolución de compras</td>
                  <td class="td-moneda">{{ $fectivo['total_compradevoluciones'] }}</td>
                </tr>
                <tr class="table-info">
                  <td class="td-ingreso-total"><b>Sub Total</b></td>
                  <td class="td-ingreso-total"><b>{{ $fectivo['total_ingresos'] }}</b></td>
                </tr>
                <tr>
                  <td class="td-egreso">Egresos</td>
                  <td class="td-moneda">{{ $fectivo['total_egresosdiversos'] }}</td>
                </tr>
                <tr>
                  <td class="td-egreso">Compras</td>
                  <td class="td-moneda">{{ $fectivo['total_compras'] }}</td>
                </tr>
                <tr>
                  <td class="td-egreso">Devolución de ventas</td>
                  <td class="td-moneda">{{ $fectivo['total_ventadevoluciones'] }}</td>
                </tr>
                <tr>
                  <td class="td-egreso-total"><b>Sub Total</b></td>
                  <td class="td-egreso-total"><b>{{ $fectivo['total_egresos'] }}</b></td>
                </tr>
                <tr>
                  <td class="td-total"><b>Total</b></td>
                  <td class="td-total"><b>{{ $fectivo['total'] }}</b></td>
                </tr>
              </tbody>
            </table>
        </div>
    </div>
  <button type="submit" class="btn mx-btn-post">Confirmar Cierre</button>
</form>  
<style>
.table-cierrecaja > tbody > tr > td {
    padding: 10px !important;
}
.td-ingreso {
    background-color: #39a7ff;
    color: #fff;
    width: 50%;
}
.td-ingreso-total {
    background-color: #1176c7;
    color: #fff;
}
.td-egreso {
    background-color: #ff5939;
    color: #fff;
}
.td-egreso-total {
    background-color: #d42200;
    color: #fff;
}
.td-total {
    font-size: 20px;
    background-color: #09a50f;
    color: #fff;
}
.td-moneda {
    background-color: #f9f9f9;
}
</style>                        

<script>
$("#idcaja").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/aperturaycierre/')}}/"+e.currentTarget.value,
        type:'GET',
        data: {
            view : 'saldoanterior',
            idcaja : e.currentTarget.value,
       },
       success: function (respuesta){
          $("#saldoanterior").val(respuesta.saldoactual);
       }
     })
}).val({{$s_aperturacierre->s_idcaja}}).trigger("change");

$("#idusers").select2({
    placeholder: "---  Seleccionar ---"
}).val({{$s_aperturacierre->idusersrecepcion}}).trigger("change");
</script>
