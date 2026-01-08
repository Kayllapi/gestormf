@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Cerrar Caja',
    'botones'=>[
        'atras:/'.$tienda->id.'/cajaapertura: Ir Atras'
    ]
])
@if($verificar_aperturacierre!='')
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> Hay una apertura de caja exiliar pendiente!
    </div>
@else
<form action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/cajaapertura/{{ $s_aperturacierre->id }}',
        method: 'PUT',
        data:{
            view: 'confirmarcierre',
            totalsoles: $('#cierretotalefectivosoles').html(),
            totaldolares: $('#cierretotalefectivodolares').html()
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura') }}';                                                                            
    },this)">
    @if(configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==1)
        @include('app.sistema_efectivo',['tienda'=>$tienda,'idaperturacierre'=>$s_aperturacierre->id])
    @elseif(configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==2)
        @if($s_aperturacierre->config_sistema_moneda_usar==1)
                <label>Monto a Cerrar en Soles *</label>
                <input type="number" min="0" step="0.01" id="montocierre_soles" placeholder="0.00">
        @elseif($s_aperturacierre->config_sistema_moneda_usar==2)
                <label>Monto a Cerrar en Dolares *</label>
                <input type="number" min="0" step="0.01" id="montocierre_dolares" placeholder="0.00">
        @elseif($s_aperturacierre->config_sistema_moneda_usar==3)
        <div class="row">
            <div class="col-sm-6">
                <label>Monto a Cerrar en Soles *</label>
                <input type="number" min="0" step="0.01" id="montocierre_soles" placeholder="0.00">
            </div>
            <div class="col-sm-6">
                <label>Monto a Cerrar en Dolares *</label>
                <input type="number" min="0" step="0.01" id="montocierre_dolares" placeholder="0.00">
            </div>
        </div>
        @endif
    @elseif(configuracion($tienda->id,'prestamo_tipocierrecaja')['valor']==3)
        @if($s_aperturacierre->config_sistema_moneda_usar==1)
        <div class="row">
          <div class="col-md-6">
            <table class="table">
              <thead class="thead-dark">
                <tr>
                  <th colspan="3" style="text-align: center;">SOLES</th>
                </tr>
              </thead>
              <tbody>
                  <tr>
                    <th colspan="3" class="td-tipo">Billetes</th> 
                  </tr> 
                  <tr>
                    <td id="cierremontosoles1" class="td-text" style="text-align: right;">200.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles1" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles1" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontosoles2" class="td-text" style="text-align: right;">100.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles2" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles2" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontosoles3" class="td-text" style="text-align: right;">50.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles3" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles3" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontosoles4" class="td-text" style="text-align: right;">20.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles4" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles4" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontosoles5" class="td-text" style="text-align: right;">10.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles5" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles5" class="td-text">0.00</td>
                  </tr>

                  <tr class="warning">
                    <td colspan="2" class="td-subtotal">Total Billetes</td> 
                    <td id="cierretotalbilletessoles" class="td-subtotal">0.00</td> 
                  </tr>
                 </tbody>
            </table>
          </div>
          <div class="col-md-6">
            <table class="table">
              <thead class="thead-dark">
                <tr>
                  <th colspan="3" style="text-align: center;">SOLES</th>
                </tr>
              </thead>
              <tbody>
                  <tr>
                    <th colspan="3" class="td-tipo">Monedas</th> 
                  </tr> 
                  <tr>
                    <td id="cierremontosoles6" class="td-text" style="text-align: right;">5.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles6" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles6" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontosoles7" class="td-text" style="text-align: right;">2.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles7" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles7" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontosoles8" class="td-text" style="text-align: right;">1.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles8" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles8" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontosoles9" class="td-text" style="text-align: right;">0.50</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles9" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles9" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontosoles10" class="td-text" style="text-align: right;">0.20</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles10" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles10" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontosoles11" class="td-text" style="text-align: right;">0.10</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles11" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles11" class="td-text">0.00</td>
                  </tr>
                  <tr class="warning">
                    <td colspan="2" class="td-subtotal">Total Monedas</td> 
                    <td id="cierretotalmonedassoles" class="td-subtotal">0.00</td> 
                  </tr>
                 </tbody>
            </table>
          </div>
        </div>
        
        <div class="mensaje-success" style="background-color: #096315;">
          Total Efectivo S/:
          <div id="cierretotalefectivosoles" style="font-size: 20px;font-weight: bold;">0.00</div> 
        </div>
        @elseif($s_aperturacierre->config_sistema_moneda_usar==2)
        <div class="row">
          <div class="col-md-6">
            <table class="table">
              <thead class="thead-dark">
                <tr>
                  <th colspan="3" style="text-align: center;">DOLARES</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th colspan="3" class="td-tipo">Billetes</th> 
                </tr> 
                  <tr>
                    <td id="cierremontodolares1" class="td-text" style="text-align: right;">100.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares1" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares1" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares2" class="td-text" style="text-align: right;">50.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares2" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares2" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares3" class="td-text" style="text-align: right;">20.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares3" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares3" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares4" class="td-text" style="text-align: right;">10.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares4" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares4" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares5" class="td-text" style="text-align: right;">5.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares5" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares5" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares6" class="td-text" style="text-align: right;">2.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares6" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares6" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares7" class="td-text" style="text-align: right;">1.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares7" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares7" class="td-text">0.00</td>
                  </tr>
                  <tr class="warning">
                    <td colspan="2" class="td-subtotal">Total Billetes</td> 
                    <td id="cierretotalbilletesdolares" class="td-subtotal">0.00</td> 
                  </tr>
                 </tbody>
            </table>
          </div>
          <div class="col-md-6">
            <table class="table">
              <thead class="thead-dark">
                <tr>
                  <th colspan="3" style="text-align: center;">DOLARES</th>
                </tr>
              </thead>
              <tbody>
                  <tr>
                    <th colspan="3" class="td-tipo">Monedas</th> 
                  </tr> 
                  <tr>
                    <td id="cierremontodolares9" class="td-text" style="text-align: right;">0.50</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares9" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares9" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares10" class="td-text" style="text-align: right;">0.25</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares10" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares10" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares11" class="td-text" style="text-align: right;">0.10</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares11" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares11" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares12" class="td-text" style="text-align: right;">0.05</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares12" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares12" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares13" class="td-text" style="text-align: right;">0.01</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares13" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares13" class="td-text">0.00</td>
                  </tr>
                  <tr class="warning">
                    <td colspan="2" class="td-subtotal">Total Monedas</td> 
                    <td id="cierretotalmonedasdolares" class="td-subtotal">0.00</td> 
                  </tr>
                 </tbody>
            </table>
          </div>
        </div>
        
        <div class="mensaje-success" style="background-color: #096315;">
          Total Efectivo $:
          <div id="cierretotalefectivodolares" style="font-size: 20px;font-weight: bold;">0.00</div> 
        </div>
        @elseif($s_aperturacierre->config_sistema_moneda_usar==3)
        <div class="row">
          <div class="col-md-6">
            <table class="table">
              <thead class="thead-dark">
                <tr>
                  <th colspan="3" style="text-align: center;">SOLES</th>
                </tr>
              </thead>
              <tbody>
                  <tr>
                    <th colspan="3" class="td-tipo">Billetes</th> 
                  </tr> 
                  <tr>
                    <td id="cierremontosoles1" class="td-text" style="text-align: right;">200.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles1" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles1" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontosoles2" class="td-text" style="text-align: right;">100.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles2" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles2" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontosoles3" class="td-text" style="text-align: right;">50.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles3" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles3" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontosoles4" class="td-text" style="text-align: right;">20.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles4" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles4" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontosoles5" class="td-text" style="text-align: right;">10.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles5" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles5" class="td-text">0.00</td>
                  </tr>

                  <tr class="warning">
                    <td colspan="2" class="td-subtotal">Total Billetes</td> 
                    <td id="cierretotalbilletessoles" class="td-subtotal">0.00</td> 
                  </tr>
                  <tr>
                    <th colspan="3" class="td-tipo">Monedas</th> 
                  </tr> 
                  <tr>
                    <td id="cierremontosoles6" class="td-text" style="text-align: right;">5.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles6" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles6" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontosoles7" class="td-text" style="text-align: right;">2.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles7" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles7" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontosoles8" class="td-text" style="text-align: right;">1.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles8" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles8" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontosoles9" class="td-text" style="text-align: right;">0.50</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles9" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles9" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontosoles10" class="td-text" style="text-align: right;">0.20</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles10" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles10" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontosoles11" class="td-text" style="text-align: right;">0.10</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidadsoles11" onclick="billetaje_soles();" onkeyup="billetaje_soles();"></td> 
                    <td id="cierretotalsoles11" class="td-text">0.00</td>
                  </tr>
                  <tr class="warning">
                    <td colspan="2" class="td-subtotal">Total Monedas</td> 
                    <td id="cierretotalmonedassoles" class="td-subtotal">0.00</td> 
                  </tr>
                 </tbody>
            </table>
              
            <div class="mensaje-success" style="background-color: #096315;">
              Total Efectivo 
              S/.:
              <div id="cierretotalefectivosoles" style="font-size: 20px;font-weight: bold;">0.00</div> 
            </div>
          </div>
          <div class="col-md-6">
            <table class="table">
              <thead class="thead-dark">
                <tr>
                  <th colspan="3" style="text-align: center;">DOLARES</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th colspan="3" class="td-tipo">Billetes</th> 
                </tr> 
                  <tr>
                    <td id="cierremontodolares1" class="td-text" style="text-align: right;">100.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares1" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares1" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares2" class="td-text" style="text-align: right;">50.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares2" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares2" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares3" class="td-text" style="text-align: right;">20.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares3" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares3" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares4" class="td-text" style="text-align: right;">10.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares4" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares4" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares5" class="td-text" style="text-align: right;">5.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares5" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares5" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares6" class="td-text" style="text-align: right;">2.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares6" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares6" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares7" class="td-text" style="text-align: right;">1.00</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares7" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares7" class="td-text">0.00</td>
                  </tr>

                  <tr class="warning">
                    <td colspan="2" class="td-subtotal">Total Billetes</td> 
                    <td id="cierretotalbilletesdolares" class="td-subtotal">0.00</td> 
                  </tr>
                  <tr>
                    <th colspan="3" class="td-tipo">Monedas</th> 
                  </tr> 
                  <tr>
                    <td id="cierremontodolares9" class="td-text" style="text-align: right;">0.50</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares9" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares9" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares10" class="td-text" style="text-align: right;">0.25</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares10" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares10" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares11" class="td-text" style="text-align: right;">0.10</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares11" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares11" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares12" class="td-text" style="text-align: right;">0.05</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares12" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares12" class="td-text">0.00</td>
                  </tr>
                  <tr>
                    <td id="cierremontodolares13" class="td-text" style="text-align: right;">0.01</td> 
                      <td><input type="number" placeholder="0" min="0" id="cierrecantidaddolares13" onclick="billetaje_dolares();" onkeyup="billetaje_dolares();"></td> 
                    <td id="cierretotaldolares13" class="td-text">0.00</td>
                  </tr>
                  <tr class="warning">
                    <td colspan="2" class="td-subtotal">Total Monedas</td> 
                    <td id="cierretotalmonedasdolares" class="td-subtotal">0.00</td> 
                  </tr>
                 </tbody>
            </table>
            <div class="mensaje-success" style="background-color: #096315;">
              Total Efectivo $:
              <div id="cierretotalefectivodolares" style="font-size: 20px;font-weight: bold;">0.00</div> 
            </div>
          </div>
        </div>
        @endif
        <style>
          .td-tipo {
            border: 0px !important;
            background-color: #1176c7;
            color: #fff;
            padding: 10px !important;
            text-align: center;
          }
          .td-subtotal {
            background-color: #11c529;
            color: #fff;
            padding: 10px !important;
            text-align: right;
          }
          .td-total {
            border: 0px !important;
            background-color: #096315;
            color: #fff;
            padding: 10px !important;
            text-align: right;
          }
          .td-text {
            padding: 10px !important;
          } 
        </style>
    @else
        @include('app.sistema_efectivo',['tienda'=>$tienda,'idaperturacierre'=>$s_aperturacierre->id])
    @endif 
  
    <button type="submit" class="btn mx-btn-post" style="width:100%;"><i class="fa fa-check"></i> Cerrar Caja</button>
</form>   
@endif
@endsection

@section('subscripts')
<script>
billetaje_soles();
billetaje_dolares();
function billetaje_soles() {
   var cierremonto1 = parseFloat($('#cierremontosoles1').html());
   var cierremonto2 = parseFloat($('#cierremontosoles2').html());
   var cierremonto3 = parseFloat($('#cierremontosoles3').html());
   var cierremonto4 = parseFloat($('#cierremontosoles4').html());
   var cierremonto5 = parseFloat($('#cierremontosoles5').html());
   var cierremonto6 = parseFloat($('#cierremontosoles6').html());
   var cierremonto7 = parseFloat($('#cierremontosoles7').html());
   var cierremonto8 = parseFloat($('#cierremontosoles8').html());
   var cierremonto9 = parseFloat($('#cierremontosoles9').html());
   var cierremonto10 = parseFloat($('#cierremontosoles10').html());
   var cierremonto11 = parseFloat($('#cierremontosoles11').html());
   var cierrecantidad1 = parseFloat($('#cierrecantidadsoles1').val());
   var cierrecantidad2 = parseFloat($('#cierrecantidadsoles2').val());
   var cierrecantidad3 = parseFloat($('#cierrecantidadsoles3').val());
   var cierrecantidad4 = parseFloat($('#cierrecantidadsoles4').val());
   var cierrecantidad5 = parseFloat($('#cierrecantidadsoles5').val());
   var cierrecantidad6 = parseFloat($('#cierrecantidadsoles6').val());
   var cierrecantidad7 = parseFloat($('#cierrecantidadsoles7').val());
   var cierrecantidad8 = parseFloat($('#cierrecantidadsoles8').val());
   var cierrecantidad9 = parseFloat($('#cierrecantidadsoles9').val());
   var cierrecantidad10 = parseFloat($('#cierrecantidadsoles10').val());
   var cierrecantidad11 = parseFloat($('#cierrecantidadsoles11').val());
   var cierretotal1 = cierremonto1*($('#cierrecantidadsoles1').val()==''?'0.00':cierrecantidad1);
   var cierretotal2 = cierremonto2*($('#cierrecantidadsoles2').val()==''?'0.00':cierrecantidad2);
   var cierretotal3 = cierremonto3*($('#cierrecantidadsoles3').val()==''?'0.00':cierrecantidad3);
   var cierretotal4 = cierremonto4*($('#cierrecantidadsoles4').val()==''?'0.00':cierrecantidad4);
   var cierretotal5 = cierremonto5*($('#cierrecantidadsoles5').val()==''?'0.00':cierrecantidad5);
   var cierretotal6 = cierremonto6*($('#cierrecantidadsoles6').val()==''?'0.00':cierrecantidad6);
   var cierretotal7 = cierremonto7*($('#cierrecantidadsoles7').val()==''?'0.00':cierrecantidad7);
   var cierretotal8 = cierremonto8*($('#cierrecantidadsoles8').val()==''?'0.00':cierrecantidad8);
   var cierretotal9 = cierremonto9*($('#cierrecantidadsoles9').val()==''?'0.00':cierrecantidad9);
   var cierretotal10 = cierremonto10*($('#cierrecantidadsoles10').val()==''?'0.00':cierrecantidad10);
   var cierretotal11 = cierremonto11*($('#cierrecantidadsoles11').val()==''?'0.00':cierrecantidad11);
   $('#cierretotalsoles1').html(cierretotal1.toFixed(2));
   $('#cierretotalsoles2').html(cierretotal2.toFixed(2));
   $('#cierretotalsoles3').html(cierretotal3.toFixed(2));
   $('#cierretotalsoles4').html(cierretotal4.toFixed(2));
   $('#cierretotalsoles5').html(cierretotal5.toFixed(2));
   $('#cierretotalsoles6').html(cierretotal6.toFixed(2));
   $('#cierretotalsoles7').html(cierretotal7.toFixed(2));
   $('#cierretotalsoles8').html(cierretotal8.toFixed(2));
   $('#cierretotalsoles9').html(cierretotal9.toFixed(2));
   $('#cierretotalsoles10').html(cierretotal10.toFixed(2));
   $('#cierretotalsoles11').html(cierretotal11.toFixed(2));
  
   var cierretotalbilletes = cierretotal1+cierretotal2+cierretotal3+cierretotal4+cierretotal5;
   var cierretotalmonedas =  cierretotal6+cierretotal7+cierretotal8+cierretotal9+cierretotal10+cierretotal11;
   var cierretotalefectivo = cierretotalbilletes+cierretotalmonedas;

   $('#cierretotalbilletessoles').html(cierretotalbilletes.toFixed(2));
   $('#cierretotalmonedassoles').html(cierretotalmonedas.toFixed(2));
   $('#cierretotalefectivosoles').html(cierretotalefectivo.toFixed(2));
}
function billetaje_dolares() {
   var cierremonto1 = parseFloat($('#cierremontodolares1').html());
   var cierremonto2 = parseFloat($('#cierremontodolares2').html());
   var cierremonto3 = parseFloat($('#cierremontodolares3').html());
   var cierremonto4 = parseFloat($('#cierremontodolares4').html());
   var cierremonto5 = parseFloat($('#cierremontodolares5').html());
   var cierremonto6 = parseFloat($('#cierremontodolares6').html());
   var cierremonto7 = parseFloat($('#cierremontodolares7').html());
   var cierremonto8 = parseFloat($('#cierremontodolares8').html());
   var cierremonto9 = parseFloat($('#cierremontodolares9').html());
   var cierremonto10 = parseFloat($('#cierremontodolares10').html());
   var cierremonto11 = parseFloat($('#cierremontodolares11').html());
   var cierremonto12 = parseFloat($('#cierremontodolares12').html());
   var cierremonto13 = parseFloat($('#cierremontodolares13').html());
   var cierrecantidad1 = parseFloat($('#cierrecantidaddolares1').val());
   var cierrecantidad2 = parseFloat($('#cierrecantidaddolares2').val());
   var cierrecantidad3 = parseFloat($('#cierrecantidaddolares3').val());
   var cierrecantidad4 = parseFloat($('#cierrecantidaddolares4').val());
   var cierrecantidad5 = parseFloat($('#cierrecantidaddolares5').val());
   var cierrecantidad6 = parseFloat($('#cierrecantidaddolares6').val());
   var cierrecantidad7 = parseFloat($('#cierrecantidaddolares7').val());
   var cierrecantidad9 = parseFloat($('#cierrecantidaddolares9').val());
   var cierrecantidad10 = parseFloat($('#cierrecantidaddolares10').val());
   var cierrecantidad11 = parseFloat($('#cierrecantidaddolares11').val());
   var cierrecantidad12 = parseFloat($('#cierrecantidaddolares12').val());
   var cierrecantidad13 = parseFloat($('#cierrecantidaddolares13').val());
   var cierretotal1 = cierremonto1*($('#cierrecantidaddolares1').val()==''?'0.00':cierrecantidad1);
   var cierretotal2 = cierremonto2*($('#cierrecantidaddolares2').val()==''?'0.00':cierrecantidad2);
   var cierretotal3 = cierremonto3*($('#cierrecantidaddolares3').val()==''?'0.00':cierrecantidad3);
   var cierretotal4 = cierremonto4*($('#cierrecantidaddolares4').val()==''?'0.00':cierrecantidad4);
   var cierretotal5 = cierremonto5*($('#cierrecantidaddolares5').val()==''?'0.00':cierrecantidad5);
   var cierretotal6 = cierremonto6*($('#cierrecantidaddolares6').val()==''?'0.00':cierrecantidad6);
   var cierretotal7 = cierremonto7*($('#cierrecantidaddolares7').val()==''?'0.00':cierrecantidad7);
   var cierretotal9 = cierremonto9*($('#cierrecantidaddolares9').val()==''?'0.00':cierrecantidad9);
   var cierretotal10 = cierremonto10*($('#cierrecantidaddolares10').val()==''?'0.00':cierrecantidad10);
   var cierretotal11 = cierremonto11*($('#cierrecantidaddolares11').val()==''?'0.00':cierrecantidad11);
   var cierretotal12 = cierremonto12*($('#cierrecantidaddolares12').val()==''?'0.00':cierrecantidad12);
   var cierretotal13 = cierremonto13*($('#cierrecantidaddolares13').val()==''?'0.00':cierrecantidad13);
   $('#cierretotaldolares1').html(cierretotal1.toFixed(2));
   $('#cierretotaldolares2').html(cierretotal2.toFixed(2));
   $('#cierretotaldolares3').html(cierretotal3.toFixed(2));
   $('#cierretotaldolares4').html(cierretotal4.toFixed(2));
   $('#cierretotaldolares5').html(cierretotal5.toFixed(2));
   $('#cierretotaldolares6').html(cierretotal6.toFixed(2));
   $('#cierretotaldolares7').html(cierretotal7.toFixed(2));
   $('#cierretotaldolares9').html(cierretotal9.toFixed(2));
   $('#cierretotaldolares10').html(cierretotal10.toFixed(2));
   $('#cierretotaldolares11').html(cierretotal11.toFixed(2));
   $('#cierretotaldolares12').html(cierretotal12.toFixed(2));
   $('#cierretotaldolares13').html(cierretotal13.toFixed(2));
  
   var cierretotalbilletes = cierretotal1+cierretotal2+cierretotal3+cierretotal4+cierretotal5+cierretotal6+cierretotal7;
   var cierretotalmonedas =  cierretotal9+cierretotal10+cierretotal11+cierretotal12+cierretotal13;
   var cierretotalefectivo = cierretotalbilletes+cierretotalmonedas;

   $('#cierretotalbilletesdolares').html(cierretotalbilletes.toFixed(2));
   $('#cierretotalmonedasdolares').html(cierretotalmonedas.toFixed(2));
   $('#cierretotalefectivodolares').html(cierretotalefectivo.toFixed(2));
}
</script>
@endsection
