<?php 
$aperturacierre = DB::table('s_aperturacierre')
            ->whereId($idaperturacierre)
            ->first();
$fectivo_soles = efectivo($tienda->id,$aperturacierre->id,1); 
$fectivo_dolares = efectivo($tienda->id,$aperturacierre->id,2);

?>
        <nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-1" data-bs-toggle="tab" data-bs-target="#nav-target-1" type="button" role="tab" aria-controls="nav-1" aria-selected="true">Total</button>
            <button class="nav-link" id="nav-2" data-bs-toggle="tab" data-bs-target="#nav-target-2" type="button" role="tab" aria-controls="nav-2" aria-selected="false">Detalle</button>
          </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
          <div class="tab-pane fade show active" id="nav-target-1" role="tabpanel" aria-labelledby="nav-1" tabindex="0">
            <table class="table table-cierrecaja" style="margin-bottom:5px;">
              <thead class="thead-dark">
                <tr>
                  <th></th>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <th style="text-align: center;color: #fff;">Soles</th>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <th style="text-align: center;color: #fff;">Dolares</th>
                  @else
                  <th style="text-align: center;color: #fff;">Soles</th>
                  <th style="text-align: center;color: #fff;">Dolares</th>
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
                  @else
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
                  @else
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
                  @else
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
                  @else
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
                  @else
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
                  @else
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
                  @else
                  <td class="td-moneda">{{ $fectivo_soles['total_prestamo_cobranzas'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_prestamo_cobranzas'] }}</td>
                  @endif
                </tr>
                @endif
                @if($fectivo_soles['total_ahorro_recaudaciones']>0 or $fectivo_dolares['total_ahorro_recaudaciones']>0)
                <tr>
                  <td class="td-ingreso">Recaudaciones</td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-moneda">{{ $fectivo_soles['total_ahorro_recaudaciones'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-moneda">{{ $fectivo_dolares['total_ahorro_recaudaciones'] }}</td>
                  @else
                  <td class="td-moneda">{{ $fectivo_soles['total_ahorro_recaudaciones'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_ahorro_recaudaciones'] }}</td>
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
                  @else
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
                  @else
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
                  @else
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
                  @else
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
                  @else
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
                  @else
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
                  @else
                  <td class="td-moneda">{{ $fectivo_soles['total_prestamo_desembolsos'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_prestamo_desembolsos'] }}</td>
                  @endif
                </tr>
                @endif
                @if($fectivo_soles['total_ahorro_retiros']>0 or $fectivo_dolares['total_ahorro_retiros']>0)
                <tr>
                  <td class="td-egreso">Retiros</td>
                  @if($aperturacierre->config_sistema_moneda_usar==1)
                  <td class="td-moneda">{{ $fectivo_soles['total_ahorro_retiros'] }}</td>
                  @elseif($aperturacierre->config_sistema_moneda_usar==2)
                  <td class="td-moneda">{{ $fectivo_dolares['total_ahorro_retiros'] }}</td>
                  @else
                  <td class="td-moneda">{{ $fectivo_soles['total_ahorro_retiros'] }}</td>
                  <td class="td-moneda">{{ $fectivo_dolares['total_ahorro_retiros'] }}</td>
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
                  @else
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
                  @else
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
                  @else
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
                  @else
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
                  @else
                  <td class="td-totaldeposito"><b>{{ $fectivo_soles['totaldeposito'] }}</b></td>
                  <td class="td-totaldeposito"><b>{{ $fectivo_dolares['totaldeposito'] }}</b></td>
                  @endif
                </tr>
              </tbody>
            </table>
          </div>
          <div class="tab-pane fade" id="nav-target-2" role="tabpanel" aria-labelledby="nav-2" tabindex="0">
  
              <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                  <button class="nav-link active" id="nav-detalle-1" data-bs-toggle="tab" data-bs-target="#nav-target-detalle-1" type="button" role="tab" aria-controls="nav-detalle-1" aria-selected="true">Soles</button>
                  <button class="nav-link" id="nav-detalle-2" data-bs-toggle="tab" data-bs-target="#nav-target-detalle-2" type="button" role="tab" aria-controls="nav-detalle-2" aria-selected="false">Dolares</button>
                </div>
              </nav>
              <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-target-detalle-1" role="tabpanel" aria-labelledby="nav-detalle-1" tabindex="0">
                  <div class="accordion" id="accordion_1">
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading1">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
                          Cierres de Cajas Auxiliares ({{ $fectivo_soles['total_ingresoapertura_auxiliar'] }})
                        </button>
                      </h2>
                      <div id="collapse1" class="accordion-collapse collapse" aria-labelledby="heading1" data-bs-parent="#accordion_1">
                        <div class="accordion-body">
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
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                          Aperturas de Cajas Auxiliares ({{ $fectivo_soles['total_egresoapertura_auxiliar'] }})
                        </button>
                      </h2>
                      <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2" data-bs-parent="#accordion_1">
                        <div class="accordion-body">
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
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading3">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                          Ingresos ({{ $fectivo_soles['total_ingresosdiversos'] }})
                        </button>
                      </h2>
                      <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3" data-bs-parent="#accordion_1">
                        <div class="accordion-body">
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
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading4">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                          Egresos ({{ $fectivo_soles['total_egresosdiversos'] }})
                        </button>
                      </h2>
                      <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4" data-bs-parent="#accordion_1">
                        <div class="accordion-body">
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
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading5">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                          Compras ({{ $fectivo_soles['total_compras'] }})
                        </button>
                      </h2>
                      <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="heading5" data-bs-parent="#accordion_1">
                        <div class="accordion-body">
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
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading6">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
                          Devolución de Compras ({{ $fectivo_soles['total_compradevoluciones'] }})
                        </button>
                      </h2>
                      <div id="collapse6" class="accordion-collapse collapse" aria-labelledby="heading6" data-bs-parent="#accordion_1">
                        <div class="accordion-body">
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
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading7">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="false" aria-controls="collapse7">
                          Ventas ({{ $fectivo_soles['total_ventas'] }})
                        </button>
                      </h2>
                      <div id="collapse7" class="accordion-collapse collapse" aria-labelledby="heading7" data-bs-parent="#accordion_1">
                        <div class="accordion-body">
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
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading8">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse8" aria-expanded="false" aria-controls="collapse8">
                          Devolución de Ventas ({{ $fectivo_soles['total_ventadevoluciones'] }})
                        </button>
                      </h2>
                      <div id="collapse8" class="accordion-collapse collapse" aria-labelledby="heading8" data-bs-parent="#accordion_1">
                        <div class="accordion-body">
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
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="nav-target-detalle-2" role="tabpanel" aria-labelledby="nav-detalle-2" tabindex="0">
                  <div class="accordion" id="accordion_2">
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading1">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
                          Cierres de Cajas Auxiliares ({{ $fectivo_dolares['total_ingresoapertura_auxiliar'] }})
                        </button>
                      </h2>
                      <div id="collapse1" class="accordion-collapse collapse" aria-labelledby="heading1" data-bs-parent="#accordion_2">
                        <div class="accordion-body">
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
                                            <td>{{$value->montocierre}}</td>
                                            <td>{{$value->fechaconfirmacion}}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                          Aperturas de Cajas Auxiliares ({{ $fectivo_dolares['total_egresoapertura_auxiliar'] }})
                        </button>
                      </h2>
                      <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2" data-bs-parent="#accordion_1">
                        <div class="accordion-body">
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
                                            <td>{{$value->montoasignar}}</td>
                                            <td>{{$value->fechaconfirmacion}}</td>
                                          </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                               </div>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading3">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                          Ingresos ({{ $fectivo_dolares['total_ingresosdiversos'] }})
                        </button>
                      </h2>
                      <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3" data-bs-parent="#accordion_1">
                        <div class="accordion-body">
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
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading4">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                          Egresos ({{ $fectivo_dolares['total_egresosdiversos'] }})
                        </button>
                      </h2>
                      <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4" data-bs-parent="#accordion_1">
                        <div class="accordion-body">
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
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading5">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                          Compras ({{ $fectivo_dolares['total_compras'] }})
                        </button>
                      </h2>
                      <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="heading5" data-bs-parent="#accordion_1">
                        <div class="accordion-body">
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
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading6">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
                          Devolución de Compras ({{ $fectivo_dolares['total_compradevoluciones'] }})
                        </button>
                      </h2>
                      <div id="collapse6" class="accordion-collapse collapse" aria-labelledby="heading6" data-bs-parent="#accordion_1">
                        <div class="accordion-body">
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
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading7">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="false" aria-controls="collapse7">
                          Ventas ({{ $fectivo_dolares['total_ventas'] }})
                        </button>
                      </h2>
                      <div id="collapse7" class="accordion-collapse collapse" aria-labelledby="heading7" data-bs-parent="#accordion_1">
                        <div class="accordion-body">
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
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading8">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse8" aria-expanded="false" aria-controls="collapse8">
                          Devolución de Ventas ({{ $fectivo_dolares['total_ventadevoluciones'] }})
                        </button>
                      </h2>
                      <div id="collapse8" class="accordion-collapse collapse" aria-labelledby="heading8" data-bs-parent="#accordion_1">
                        <div class="accordion-body">
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
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          </div>
        </div>
<style>
.td-ingreso {
    background-color: #39a7ff !important;
    color: #fff;
    width: 50%;
}
.td-ingreso-total {
    background-color: #1176c7 !important;
    color: #fff;
}
.td-egreso {
    background-color: #ff5939 !important;
    color: #fff;
}
.td-egreso-total {
    background-color: #d42200 !important;
    color: #fff;
}
.td-total {
    font-size: 20px;
    background-color: #09a50f !important;
    color: #fff;
}
.td-ingresodeposito {
    background-color: #516590 !important;
    color: #fff;
}
.td-totaldeposito {
    font-size: 20px;
    background-color: #2c3b5a !important;
    color: #fff;
}
.td-moneda {
    background-color: #f9f9f9 !important;
}
</style> 