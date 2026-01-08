<?php 
$aperturacierre = DB::table('s_aperturacierre')
            ->whereId($idaperturacierre)
            ->first();
//$caja = caja($tienda->id,$aperturacierre->idusersrecepcion);
$fectivo_soles = efectivo($tienda->id,$aperturacierre->id,1); 
$fectivo_dolares = efectivo($tienda->id,$aperturacierre->id,2);
?>
    <div class="row">
        <div class="col-md-5">
            <table class="table table-cierrecaja" style="margin-bottom:5px;">
              <thead class="thead-dark">
                <tr>
                  <th colspan="3" style="text-align: center;">SUMATORIA TOTAL</th>
                </tr>
                <tr>
                  <th></th>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <th style="text-align: center;">Soles</th>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <th style="text-align: center;">Dolares</th>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <th style="text-align: center;">Soles</th>
                  <th style="text-align: center;">Dolares</th>
                  @endif
                </tr>
              </thead>
              <tbody>
                <tr class="table-warning">
                  <td class="td-ingreso">Apertura</td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-moneda">{{ $fectivo_soles['total_apertura'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-moneda">{{ $fectivo_dolares['total_apertura'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-moneda">{{ $fectivo_soles['total_apertura'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_apertura'] }}</td>
                  @endif
                </tr>
                <tr class="table-warning">
                  <td class="td-ingreso">Cierres de cajas Auxiliares</td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-moneda">{{ $fectivo_soles['total_ingresoapertura_auxiliar'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-moneda">{{ $fectivo_dolares['total_ingresoapertura_auxiliar'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-moneda">{{ $fectivo_soles['total_ingresoapertura_auxiliar'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_ingresoapertura_auxiliar'] }}</td>
                  @endif
                </tr>
                <tr>
                  <td class="td-ingreso">Ingreso</td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-moneda">{{ $fectivo_soles['total_ingresosdiversos'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-moneda">{{ $fectivo_dolares['total_ingresosdiversos'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-moneda">{{ $fectivo_soles['total_ingresosdiversos'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_ingresosdiversos'] }}</td>
                  @endif
                </tr>
                @if($fectivo_soles['total_ventas']>0 or $fectivo_dolares['total_ventas']>0)
                <tr>
                  <td class="td-ingreso">Ventas</td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-moneda">{{ $fectivo_soles['total_ventas'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-moneda">{{ $fectivo_dolares['total_ventas'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-moneda">{{ $fectivo_soles['total_ventas'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_ventas'] }}</td>
                  @endif
                </tr>
                @endif
                @if($fectivo_soles['total_compradevoluciones']>0 or $fectivo_dolares['total_compradevoluciones']>0)
                <tr>
                  <td class="td-ingreso">Devolución de compras</td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-moneda">{{ $fectivo_soles['total_compradevoluciones'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-moneda">{{ $fectivo_dolares['total_compradevoluciones'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-moneda">{{ $fectivo_soles['total_compradevoluciones'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_compradevoluciones'] }}</td>
                  @endif
                </tr>
                @endif
                @if($fectivo_soles['total_prestamo_gastosadministrativos']>0 or $fectivo_dolares['total_prestamo_gastosadministrativos']>0)
                <tr>
                  <td class="td-ingreso">Gastos Administrativos</td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-moneda">{{ $fectivo_soles['total_prestamo_gastosadministrativos'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-moneda">{{ $fectivo_dolares['total_prestamo_gastosadministrativos'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-moneda">{{ $fectivo_soles['total_prestamo_gastosadministrativos'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_prestamo_gastosadministrativos'] }}</td>
                  @endif
                </tr>
                @endif
                @if($fectivo_soles['total_prestamo_cobranzas']>0 or $fectivo_dolares['total_prestamo_cobranzas']>0)
                <tr>
                  <td class="td-ingreso">Cobranzas</td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-moneda">{{ $fectivo_soles['total_prestamo_cobranzas'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-moneda">{{ $fectivo_dolares['total_prestamo_cobranzas'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-moneda">{{ $fectivo_soles['total_prestamo_cobranzas'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_prestamo_cobranzas'] }}</td>
                  @endif
                </tr>
                @endif
                @if($fectivo_soles['total_prestamo_desembolsos_anulado']>0 or $fectivo_dolares['total_prestamo_desembolsos_anulado']>0)
                <tr>
                  <td class="td-ingreso">Desembolsos Anulados</td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-moneda">{{ $fectivo_soles['total_prestamo_desembolsos_anulado'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-moneda">{{ $fectivo_dolares['total_prestamo_desembolsos_anulado'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-moneda">{{ $fectivo_soles['total_prestamo_desembolsos_anulado'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_prestamo_desembolsos_anulado'] }}</td>
                  @endif
                </tr>
                @endif
                <tr class="table-info">
                  <td class="td-ingreso-total"><b>TOTAL</b></td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-ingreso-total"><b>{{ $fectivo_soles['total_ingresos'] }}</b></td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-ingreso-total"><b>{{ $fectivo_dolares['total_ingresos'] }}</b></td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-ingreso-total"><b>{{ $fectivo_soles['total_ingresos'] }}</b></td>
                  <td class="td-ingreso-total"><b>{{ $fectivo_dolares['total_ingresos'] }}</b></td>
                  @endif
                </tr>
                <tr class="table-warning">
                  <td class="td-egreso">Aperturas de Cajas Auxiliares</td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-moneda">{{ $fectivo_soles['total_egresoapertura_auxiliar'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-moneda">{{ $fectivo_dolares['total_egresoapertura_auxiliar'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-moneda">{{ $fectivo_soles['total_egresoapertura_auxiliar'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_egresoapertura_auxiliar'] }}</td>
                  @endif
                </tr>
                <tr>
                  <td class="td-egreso">Egresos</td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-moneda">{{ $fectivo_soles['total_egresosdiversos'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-moneda">{{ $fectivo_dolares['total_egresosdiversos'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-moneda">{{ $fectivo_soles['total_egresosdiversos'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_egresosdiversos'] }}</td>
                  @endif
                </tr>
                @if($fectivo_soles['total_compras']>0 or $fectivo_dolares['total_compras']>0)
                <tr>
                  <td class="td-egreso">Compras</td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-moneda">{{ $fectivo_soles['total_compras'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-moneda">{{ $fectivo_dolares['total_compras'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-moneda">{{ $fectivo_soles['total_compras'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_compras'] }}</td>
                  @endif
                </tr>
                @endif
                @if($fectivo_soles['total_ventadevoluciones']>0 or $fectivo_dolares['total_ventadevoluciones']>0)
                <tr>
                  <td class="td-egreso">Devolución de ventas</td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-moneda">{{ $fectivo_soles['total_ventadevoluciones'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-moneda">{{ $fectivo_dolares['total_ventadevoluciones'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-moneda">{{ $fectivo_soles['total_ventadevoluciones'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_ventadevoluciones'] }}</td>
                  @endif
                </tr>
                @endif
                @if($fectivo_soles['total_prestamo_desembolsos']>0 or $fectivo_dolares['total_prestamo_desembolsos']>0)
                <tr>
                  <td class="td-egreso">Desembolsos</td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-moneda">{{ $fectivo_soles['total_prestamo_desembolsos'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-moneda">{{ $fectivo_dolares['total_prestamo_desembolsos'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-moneda">{{ $fectivo_soles['total_prestamo_desembolsos'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_prestamo_desembolsos'] }}</td>
                  @endif
                </tr>
                @endif
                @if($fectivo_soles['total_prestamo_gastosadministrativos_anulado']>0 or $fectivo_dolares['total_prestamo_gastosadministrativos_anulado']>0)
                <tr>
                  <td class="td-egreso">Gastos Administrativos Anulados</td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-moneda">{{ $fectivo_soles['total_prestamo_gastosadministrativos_anulado'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-moneda">{{ $fectivo_dolares['total_prestamo_gastosadministrativos_anulado'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-moneda">{{ $fectivo_soles['total_prestamo_gastosadministrativos_anulado'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_prestamo_gastosadministrativos_anulado'] }}</td>
                  @endif
                </tr>
                @endif
                <tr>
                  <td class="td-egreso-total"><b>TOTAL</b></td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-egreso-total"><b>{{ $fectivo_soles['total_egresos'] }}</b></td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-egreso-total"><b>{{ $fectivo_dolares['total_egresos'] }}</b></td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-egreso-total"><b>{{ $fectivo_soles['total_egresos'] }}</b></td>
                  <td class="td-egreso-total"><b>{{ $fectivo_dolares['total_egresos'] }}</b></td>
                  @endif
                </tr>
                <tr>
                  <td class="td-total"><b>TOTAL EFECTIVO</b></td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-total"><b>{{ $fectivo_soles['total'] }}</b></td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-total"><b>{{ $fectivo_dolares['total'] }}</b></td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-total"><b>{{ $fectivo_soles['total'] }}</b></td>
                  <td class="td-total"><b>{{ $fectivo_dolares['total'] }}</b></td>
                  @endif
                </tr>
                @if($fectivo_soles['total_prestamo_cobranzacuentabancarias']>0 or $fectivo_dolares['total_prestamo_cobranzacuentabancarias']>0)
                <tr>
                  <td class="td-ingresodeposito">Cobranzas</td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-moneda">{{ $fectivo_soles['total_prestamo_cobranzacuentabancarias'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-moneda">{{ $fectivo_dolares['total_prestamo_cobranzacuentabancarias'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-moneda">{{ $fectivo_soles['total_prestamo_cobranzacuentabancarias'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_prestamo_cobranzacuentabancarias'] }}</td>
                  @endif
                </tr>
                @endif
                <tr>
                  <td class="td-totaldeposito"><b>TOTAL DEPÓSITO</b></td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-totaldeposito"><b>{{ $fectivo_soles['totaldeposito'] }}</b></td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-totaldeposito"><b>{{ $fectivo_dolares['totaldeposito'] }}</b></td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==3)
                  <td class="td-totaldeposito"><b>{{ $fectivo_soles['totaldeposito'] }}</b></td>
                  <td class="td-totaldeposito"><b>{{ $fectivo_dolares['totaldeposito'] }}</b></td>
                  @endif
                </tr>
              </tbody>
            </table>
        </div>
        <div class="col-md-7">
            <div class="tabs-container" id="tab-detalledeldia">
                @if($aperturacierre->config_sistema_moneda_usar==1)
                @elseif($aperturacierre->config_sistema_moneda_usar==2)
                @elseif($aperturacierre->config_sistema_moneda_usar==3)
                <ul class="tabs-menu">
                    <li class="current"><a href="#tab-detalledeldia-1">Soles</a></li>
                    <li><a href="#tab-detalledeldia-2">Dolares</a></li>
                </ul>
                @endif
                <div class="tab">
                    @if($aperturacierre->config_sistema_moneda_usar==1)
                    <div id="tab-detalledeldia-1" class="tab-content" style="display: block;">
                        <div class="list-single-main-wrapper fl-wrap">
                            <div class="breadcrumbs gradient-bg fl-wrap">
                              <span>Efectivo</span>
                            </div>
                        </div>
                        <div class="accordion">
                            <a class="toggle" href="#"> Cierres de Cajas Auxiliares ({{ $fectivo_soles['total_ingresoapertura_auxiliar'] }})<i class="fa fa-angle-down"></i></a>
                            <div class="accordion-inner">
                                <div class="table-responsive">
                                  <table class="table" id="tabla-contenido">
                                      <thead class="thead-dark">
                                        <tr>
                                          <th>Persona Asignado</th>
                                          <th>Monto</th>
                                          <th>Fecha</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                          @foreach($fectivo_soles['ingresoaperturaauxiliares'] as $value)
                                          <tr>
                                            <td>{{$value->usersrecepcionapellidos}}, {{$value->usersrecepcionnombre}}</td>
                                            <td>{{$value->montocierre}}</td>
                                            <td>{{$value->fechaconfirmacion}}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            <a class="toggle" href="#"> Aperturas de Cajas Auxiliares ({{ $fectivo_soles['total_egresoapertura_auxiliar'] }})<i class="fa fa-angle-down"></i></a>
                            <div class="accordion-inner">
                                <div class="table-responsive">
                                  <table class="table" id="tabla-contenido">
                                      <thead class="thead-dark">
                                        <tr>
                                          <th>Persona Asignado</th>
                                          <th>Monto</th>
                                          <th>Fecha</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                          @foreach($fectivo_soles['egresoaperturaauxiliares'] as $value)
                                          <tr>
                                            <td>{{$value->usersrecepcionapellidos}}, {{$value->usersrecepcionnombre}}</td>
                                            <td>{{$value->montoasignar}}</td>
                                            <td>{{$value->fechaconfirmacion}}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            <a class="toggle" href="#"> Ingresos ({{ $fectivo_soles['total_ingresosdiversos'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['ingresosdiversos'] as $value)
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
                            <a class="toggle" href="#"> Egresos ({{ $fectivo_soles['total_egresosdiversos'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['egresosdiversos'] as $value)
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
                            @if($fectivo_soles['total_compras']>0)
                            <a class="toggle" href="#"> Compras ({{ $fectivo_soles['total_compras'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['compras'] as $value)
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
                            @endif
                            @if($fectivo_soles['total_compradevoluciones']>0)
                            <a class="toggle" href="#"> Devolución de Compras ({{ $fectivo_soles['total_compradevoluciones'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['compradevoluciones'] as $value)
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
                            @endif
                            @if($fectivo_soles['total_ventas']>0)
                            <a class="toggle" href="#"> Ventas ({{ $fectivo_soles['total_ventas'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['ventas'] as $value)
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
                            @endif
                            @if($fectivo_soles['total_ventadevoluciones']>0)
                            <a class="toggle" href="#"> Devolución de Ventas ({{ $fectivo_soles['total_ventadevoluciones'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['ventadevoluciones'] as $value)
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
                            @endif
                            @if($fectivo_soles['total_prestamo_desembolsos']>0)
                            <a class="toggle" href="#"> Desembolsos ({{ $fectivo_soles['total_prestamo_desembolsos'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['prestamo_desembolsos'] as $value)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->monto }}</td>
                                            <td>{{ $value->fechadesembolsado }}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                            @if($fectivo_soles['total_prestamo_desembolsos_anulado']>0)
                            <a class="toggle" href="#"> Desembolsos Anulados ({{ $fectivo_soles['total_prestamo_desembolsos_anulado'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['prestamo_desembolsos_anulado'] as $value)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->monto }}</td>
                                            <td>{{ $value->fechadesembolsado }}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                            @if($fectivo_soles['total_prestamo_gastosadministrativos']>0)
                            <a class="toggle" href="#"> Gastos Administrativos ({{ $fectivo_soles['total_prestamo_gastosadministrativos'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['prestamo_desembolsos'] as $value)
                                          @if($value->facturacion_montorecibido>0)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->facturacion_montorecibido }}</td>
                                            <td>{{ $value->fecharegistro }}</td>
                                          </tr>
                                          @endif
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                            @if($fectivo_soles['total_prestamo_gastosadministrativos_anulado']>0)
                            <a class="toggle" href="#"> Gastos Administrativos Anulados ({{ $fectivo_soles['total_prestamo_gastosadministrativos_anulado'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['prestamo_desembolsos_anulado'] as $value)
                                          @if($value->facturacion_montorecibido>0)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->facturacion_montorecibido }}</td>
                                            <td>{{ $value->fecharegistro }}</td>
                                          </tr>
                                          @endif
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                            @if($fectivo_soles['total_prestamo_cobranzas']>0)
                            <a class="toggle" href="#"> Cobranzas ({{ $fectivo_soles['total_prestamo_cobranzas'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['prestamo_cobranzas'] as $value)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->cronograma_totalredondeado }}</td>
                                            <td>{{ $value->fecharegistro }}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                        </div>
                        <div class="list-single-main-wrapper fl-wrap">
                            <div class="breadcrumbs gradient-bg fl-wrap">
                              <span>Depósito</span>
                            </div>
                        </div>
                        <div class="accordion">
                            @if($fectivo_soles['total_prestamo_cobranzacuentabancarias']>0)
                            <a class="toggle" href="#"> Cobranzas ({{ $fectivo_soles['total_prestamo_cobranzacuentabancarias'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['prestamo_cobranzacuentabancarias'] as $value)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->monto }}</td>
                                            <td>{{ $value->fecharegistro }}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @elseif($aperturacierre->config_sistema_moneda_usar==2)
                    <div id="tab-detalledeldia-2" class="tab-content" style="display: block;">
                        <div class="list-single-main-wrapper fl-wrap">
                            <div class="breadcrumbs gradient-bg fl-wrap">
                              <span>Efectivo</span>
                            </div>
                        </div>
                        <div class="accordion">
                            <a class="toggle" href="#"> Cierres de Cajas Auxiliares ({{ $fectivo_dolares['total_ingresoapertura_auxiliar'] }})<i class="fa fa-angle-down"></i></a>
                            <div class="accordion-inner">
                                <div class="table-responsive">
                                  <table class="table" id="tabla-contenido">
                                      <thead class="thead-dark">
                                        <tr>
                                          <th>Persona Asignado</th>
                                          <th>Monto</th>
                                          <th>Fecha</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                          @foreach($fectivo_dolares['ingresoaperturaauxiliares'] as $value)
                                          <tr>
                                            <td>{{$value->usersrecepcionapellidos}}, {{$value->usersrecepcionnombre}}</td>
                                            <td>{{$value->montocierre_dolares}}</td>
                                            <td>{{$value->fechaconfirmacion}}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            <a class="toggle" href="#"> Aperturas de Cajas Auxiliares ({{ $fectivo_dolares['total_egresoapertura_auxiliar'] }})<i class="fa fa-angle-down"></i></a>
                            <div class="accordion-inner">
                                <div class="table-responsive">
                                  <table class="table" id="tabla-contenido">
                                      <thead class="thead-dark">
                                        <tr>
                                          <th>Persona Asignado</th>
                                          <th>Monto</th>
                                          <th>Fecha</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                          @foreach($fectivo_dolares['egresoaperturaauxiliares'] as $value)
                                          <tr>
                                            <td>{{$value->usersrecepcionapellidos}}, {{$value->usersrecepcionnombre}}</td>
                                            <td>{{$value->montoasignar_dolares}}</td>
                                            <td>{{$value->fechaconfirmacion}}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            <a class="toggle" href="#"> Ingresos ({{ $fectivo_dolares['total_ingresosdiversos'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['ingresosdiversos'] as $value)
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
                            <a class="toggle" href="#"> Egresos ({{ $fectivo_dolares['total_egresosdiversos'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['egresosdiversos'] as $value)
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
                            @if($fectivo_dolares['total_compras']>0)
                            <a class="toggle" href="#"> Compras ({{ $fectivo_dolares['total_compras'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['compras'] as $value)
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
                            @endif
                            @if($fectivo_dolares['total_compradevoluciones']>0)
                            <a class="toggle" href="#"> Devolución de Compras ({{ $fectivo_dolares['total_compradevoluciones'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['compradevoluciones'] as $value)
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
                            @endif
                            @if($fectivo_dolares['total_ventas']>0)
                            <a class="toggle" href="#"> Ventas ({{ $fectivo_dolares['total_ventas'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['ventas'] as $value)
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
                            @endif
                            @if($fectivo_dolares['total_ventadevoluciones']>0)
                            <a class="toggle" href="#"> Devolución de Ventas ({{ $fectivo_dolares['total_ventadevoluciones'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['ventadevoluciones'] as $value)
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
                            @endif
                            @if($fectivo_dolares['total_prestamo_desembolsos']>0)
                            <a class="toggle" href="#"> Desembolsos ({{ $fectivo_dolares['total_prestamo_desembolsos'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['prestamo_desembolsos'] as $value)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->monto }}</td>
                                            <td>{{ $value->fechadesembolsado }}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                            @if($fectivo_dolares['total_prestamo_desembolsos_anulado']>0)
                            <a class="toggle" href="#"> Desembolsos Anulados ({{ $fectivo_dolares['total_prestamo_desembolsos_anulado'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['prestamo_desembolsos_anulado'] as $value)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->monto }}</td>
                                            <td>{{ $value->fechadesembolsado }}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                            @if($fectivo_dolares['total_prestamo_gastosadministrativos']>0)
                            <a class="toggle" href="#"> Gastos Administrativos ({{ $fectivo_dolares['total_prestamo_gastosadministrativos'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['prestamo_desembolsos'] as $value)
                                          @if($value->facturacion_montorecibido>0)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->facturacion_montorecibido }}</td>
                                            <td>{{ $value->fecharegistro }}</td>
                                          </tr>
                                          @endif
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                            @if($fectivo_dolares['total_prestamo_gastosadministrativos_anulado']>0)
                            <a class="toggle" href="#"> Gastos Administrativos Anulados ({{ $fectivo_dolares['total_prestamo_gastosadministrativos_anulado'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['prestamo_desembolsos_anulado'] as $value)
                                          @if($value->facturacion_montorecibido>0)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->facturacion_montorecibido }}</td>
                                            <td>{{ $value->fecharegistro }}</td>
                                          </tr>
                                          @endif
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                            @if($fectivo_dolares['total_prestamo_cobranzas']>0)
                            <a class="toggle" href="#"> Cobranzas ({{ $fectivo_dolares['total_prestamo_cobranzas'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['prestamo_cobranzas'] as $value)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->cronograma_totalredondeado }}</td>
                                            <td>{{ $value->fecharegistro }}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                        </div>
                        <div class="list-single-main-wrapper fl-wrap">
                            <div class="breadcrumbs gradient-bg fl-wrap">
                              <span>Depósito</span>
                            </div>
                        </div>
                        <div class="accordion">
                            @if($fectivo_dolares['total_prestamo_cobranzacuentabancarias']>0)
                            <a class="toggle" href="#"> Cobranzas ({{ $fectivo_dolares['total_prestamo_cobranzacuentabancarias'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['prestamo_cobranzacuentabancarias'] as $value)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->monto }}</td>
                                            <td>{{ $value->fecharegistro }}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @elseif($aperturacierre->config_sistema_moneda_usar==3)
                    <div id="tab-detalledeldia-1" class="tab-content" style="display: block;">
                        <div class="list-single-main-wrapper fl-wrap">
                            <div class="breadcrumbs gradient-bg fl-wrap">
                              <span>Efectivo</span>
                            </div>
                        </div>
                        <div class="accordion">
                            <a class="toggle" href="#"> Cierres de Cajas Auxiliares ({{ $fectivo_soles['total_ingresoapertura_auxiliar'] }})<i class="fa fa-angle-down"></i></a>
                            <div class="accordion-inner">
                                <div class="table-responsive">
                                  <table class="table" id="tabla-contenido">
                                      <thead class="thead-dark">
                                        <tr>
                                          <th>Persona Asignado</th>
                                          <th>Monto</th>
                                          <th>Fecha</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                          @foreach($fectivo_soles['ingresoaperturaauxiliares'] as $value)
                                          <tr>
                                            <td>{{$value->usersrecepcionapellidos}}, {{$value->usersrecepcionnombre}}</td>
                                            <td>{{$value->montocierre}}</td>
                                            <td>{{$value->fechaconfirmacion}}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            <a class="toggle" href="#"> Aperturas de Cajas Auxiliares ({{ $fectivo_soles['total_egresoapertura_auxiliar'] }})<i class="fa fa-angle-down"></i></a>
                            <div class="accordion-inner">
                                <div class="table-responsive">
                                  <table class="table" id="tabla-contenido">
                                      <thead class="thead-dark">
                                        <tr>
                                          <th>Persona Asignado</th>
                                          <th>Monto</th>
                                          <th>Fecha</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                          @foreach($fectivo_soles['aperturaauxiliares'] as $value)
                                          <tr>
                                            <td>{{$value->usersrecepcionapellidos}}, {{$value->usersrecepcionnombre}}</td>
                                            <td>{{$value->montoasignar}}</td>
                                            <td>{{$value->fechaconfirmacion}}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            <a class="toggle" href="#"> Ingresos ({{ $fectivo_soles['total_ingresosdiversos'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['ingresosdiversos'] as $value)
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
                            <a class="toggle" href="#"> Egresos ({{ $fectivo_soles['total_egresosdiversos'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['egresosdiversos'] as $value)
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
                            @if($fectivo_soles['total_compras']>0)
                            <a class="toggle" href="#"> Compras ({{ $fectivo_soles['total_compras'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['compras'] as $value)
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
                            @endif
                            @if($fectivo_soles['total_compradevoluciones']>0)
                            <a class="toggle" href="#"> Devolución de Compras ({{ $fectivo_soles['total_compradevoluciones'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['compradevoluciones'] as $value)
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
                            @endif
                            @if($fectivo_soles['total_ventas']>0)
                            <a class="toggle" href="#"> Ventas ({{ $fectivo_soles['total_ventas'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['ventas'] as $value)
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
                            @endif
                            @if($fectivo_soles['total_ventadevoluciones']>0)
                            <a class="toggle" href="#"> Devolución de Ventas ({{ $fectivo_soles['total_ventadevoluciones'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['ventadevoluciones'] as $value)
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
                            @endif
                            @if($fectivo_soles['total_prestamo_desembolsos']>0)
                            <a class="toggle" href="#"> Desembolsos ({{ $fectivo_soles['total_prestamo_desembolsos'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['prestamo_desembolsos'] as $value)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->monto }}</td>
                                            <td>{{ $value->fechadesembolsado }}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                            @if($fectivo_soles['total_prestamo_gastosadministrativos']>0)
                            <a class="toggle" href="#"> Gastos Administrativos ({{ $fectivo_soles['total_prestamo_gastosadministrativos'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['prestamo_desembolsos'] as $value)
                                          @if($value->facturacion_montorecibido>0)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->facturacion_montorecibido }}</td>
                                            <td>{{ $value->fecharegistro }}</td>
                                          </tr>
                                          @endif
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                            @if($fectivo_soles['total_prestamo_gastosadministrativos_anulado']>0)
                            <a class="toggle" href="#"> Gastos Administrativos Anulados ({{ $fectivo_soles['total_prestamo_gastosadministrativos_anulado'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['prestamo_desembolsos_anulado'] as $value)
                                          @if($value->facturacion_montorecibido>0)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->facturacion_montorecibido }}</td>
                                            <td>{{ $value->fecharegistro }}</td>
                                          </tr>
                                          @endif
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                            @if($fectivo_soles['total_prestamo_cobranzas']>0)
                            <a class="toggle" href="#"> Cobranzas ({{ $fectivo_soles['total_prestamo_cobranzas'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['prestamo_cobranzas'] as $value)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->cronograma_totalredondeado }}</td>
                                            <td>{{ $value->fecharegistro }}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                        </div>
                        <div class="list-single-main-wrapper fl-wrap">
                            <div class="breadcrumbs gradient-bg fl-wrap">
                              <span>Depósito</span>
                            </div>
                        </div>
                        <div class="accordion">
                            @if($fectivo_soles['total_prestamo_cobranzacuentabancarias']>0)
                            <a class="toggle" href="#"> Cobranzas ({{ $fectivo_soles['total_prestamo_cobranzacuentabancarias'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_soles['prestamo_cobranzacuentabancarias'] as $value)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->monto }}</td>
                                            <td>{{ $value->fecharegistro }}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div id="tab-detalledeldia-2" class="tab-content" style="display: none;">
                        <div class="accordion">
                            <a class="toggle" href="#"> Cierres de Cajas Auxiliares ({{ $fectivo_dolares['total_ingresoapertura_auxiliar'] }})<i class="fa fa-angle-down"></i></a>
                            <div class="accordion-inner">
                                <div class="table-responsive">
                                  <table class="table" id="tabla-contenido">
                                      <thead class="thead-dark">
                                        <tr>
                                          <th>Persona Asignado</th>
                                          <th>Monto</th>
                                          <th>Fecha</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                          @foreach($fectivo_dolares['ingresoaperturaauxiliares'] as $value)
                                          <tr>
                                            <td>{{$value->usersrecepcionapellidos}}, {{$value->usersrecepcionnombre}}</td>
                                            <td>{{$value->montocierre_dolares}}</td>
                                            <td>{{$value->fechaconfirmacion}}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            <a class="toggle" href="#"> Aperturas Auxiliares ({{ $fectivo_dolares['total_egresoapertura_auxiliar'] }})<i class="fa fa-angle-down"></i></a>
                            <div class="accordion-inner">
                                <div class="table-responsive">
                                  <table class="table" id="tabla-contenido">
                                      <thead class="thead-dark">
                                        <tr>
                                          <th>Persona Asignado</th>
                                          <th>Monto</th>
                                          <th>Fecha</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                          @foreach($fectivo_dolares['aperturaauxiliares'] as $value)
                                          <tr>
                                            <td>{{$value->usersrecepcionapellidos}}, {{$value->usersrecepcionnombre}}</td>
                                            <td>{{$value->montoasignar_dolares}}</td>
                                            <td>{{$value->fechaconfirmacion}}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            <a class="toggle" href="#"> Ingresos ({{ $fectivo_dolares['total_ingresosdiversos'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['ingresosdiversos'] as $value)
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
                            <a class="toggle" href="#"> Egresos ({{ $fectivo_dolares['total_egresosdiversos'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['egresosdiversos'] as $value)
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
                            @if($fectivo_dolares['total_compras']>0)
                            <a class="toggle" href="#"> Compras ({{ $fectivo_dolares['total_compras'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['compras'] as $value)
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
                            @endif
                            @if($fectivo_dolares['total_compradevoluciones']>0)
                            <a class="toggle" href="#"> Devolución de Compras ({{ $fectivo_dolares['total_compradevoluciones'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['compradevoluciones'] as $value)
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
                            @endif
                            @if($fectivo_dolares['total_ventas']>0)
                            <a class="toggle" href="#"> Ventas ({{ $fectivo_dolares['total_ventas'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['ventas'] as $value)
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
                            @endif
                            @if($fectivo_dolares['total_ventadevoluciones']>0)
                            <a class="toggle" href="#"> Devolución de Ventas ({{ $fectivo_dolares['total_ventadevoluciones'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['ventadevoluciones'] as $value)
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
                            @endif
                            @if($fectivo_dolares['total_prestamo_desembolsos']>0)
                            <a class="toggle" href="#"> Desembolsos ({{ $fectivo_dolares['total_prestamo_desembolsos'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['prestamo_desembolsos'] as $value)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->monto }}</td>
                                            <td>{{ $value->fechadesembolsado }}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                            @if($fectivo_dolares['total_prestamo_desembolsos_anulado']>0)
                            <a class="toggle" href="#"> Desembolsos Anulados ({{ $fectivo_dolares['total_prestamo_desembolsos_anulado'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['prestamo_desembolsos_anulado'] as $value)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->monto }}</td>
                                            <td>{{ $value->fechadesembolsado }}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                            @if($fectivo_dolares['total_prestamo_gastosadministrativos']>0)
                            <a class="toggle" href="#"> Gastos Administrativos ({{ $fectivo_dolares['total_prestamo_gastosadministrativos'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['prestamo_desembolsos'] as $value)
                                          @if($value->facturacion_montorecibido>0)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->facturacion_montorecibido }}</td>
                                            <td>{{ $value->fecharegistro }}</td>
                                          </tr>
                                          @endif
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                            @if($fectivo_dolares['total_prestamo_gastosadministrativos_anulado']>0)
                            <a class="toggle" href="#"> Gastos Administrativos Anulados ({{ $fectivo_dolares['total_prestamo_gastosadministrativos_anulado'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['prestamo_desembolsos_anulado'] as $value)
                                          @if($value->facturacion_montorecibido>0)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->facturacion_montorecibido }}</td>
                                            <td>{{ $value->fecharegistro }}</td>
                                          </tr>
                                          @endif
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                            @if($fectivo_dolares['total_prestamo_cobranzas']>0)
                            <a class="toggle" href="#"> Cobranzas ({{ $fectivo_dolares['total_prestamo_cobranzas'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['prestamo_cobranzas'] as $value)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->cronograma_totalredondeado }}</td>
                                            <td>{{ $value->fecharegistro }}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                        </div>
                        <div class="list-single-main-wrapper fl-wrap">
                            <div class="breadcrumbs gradient-bg fl-wrap">
                              <span>Depósito</span>
                            </div>
                        </div>
                        <div class="accordion">
                            @if($fectivo_dolares['total_prestamo_cobranzacuentabancarias']>0)
                            <a class="toggle" href="#"> Cobranzas ({{ $fectivo_dolares['total_prestamo_cobranzacuentabancarias'] }})<i class="fa fa-angle-down"></i></a>
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
                                          @foreach($fectivo_dolares['prestamo_cobranzacuentabancarias'] as $value)
                                          <tr>
                                            <td style="height: 40px;">{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
                                            <td>{{ $value->cliente }}</td>
                                            <td>{{ $value->monto }}</td>
                                            <td>{{ $value->fecharegistro }}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
                        
        </div>
    </div>
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
.td-ingresodeposito {
    background-color: #516590;
    color: #fff;
}
.td-totaldeposito {
    font-size: 20px;
    background-color: #2c3b5a;
    color: #fff;
}
.td-moneda {
    background-color: #f9f9f9;
}
</style> 