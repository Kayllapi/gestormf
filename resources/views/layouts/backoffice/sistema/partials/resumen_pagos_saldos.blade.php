                <div class="mb-1 mt-1">
                  <span class="badge d-block">RESUMEN DE PAGOS Y SALDOS</span>
                </div>
               <b> N° DE CUENTA: <span id="numerodecuenta" style="font-weight: normal;"></span></b><br>
               <b> CLASIFICACIÓN: <span id="clasificacion" style="font-weight: normal;"></span></b><br>
               <b> ASES./EJEC.: <span id="asesor" style="color: #002a8d;font-weight: normal;"></span></b><br>
               <b> COBRANZA: <span id="resumen_cobranza" style="color: #002a8d;font-weight: normal;"></span></b>
                <table class="table">
                  <thead>
                      <tr>
                        <th style="text-align: center;background-color: #bcbcbc !important;color: #000 !important;">Estado de cuotas <span id="estadocuotas" style="background-color: #ffc107;"></span></th>
                        <th style="text-align: center;background-color: #bcbcbc !important;color: #000 !important;width: 50px;">N°</th>
                        <th style="text-align: center;background-color: #bcbcbc !important;color: #000 !important;width: 50px;">Saldo (S/.)</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr>
                        <td style="width: 150px;background-color: #efefef !important;color: #04c050 !important;font-weight: bold;">Cancelados</td>
                        <td id="numero_cuota_cancelada" style="color: #04c050 !important;text-align: right;">0</td>
                        <td id="cuota_pagada" style="color: #04c050 !important;text-align: right;">0.00</td>
                      </tr>
                      <tr>
                        <td style="background-color: #efefef !important;font-weight: bold;">Pendientes</td>
                        <td id="numero_cuota_pendiente" style="text-align: right;">0</td>
                        <td id="cuota_pendiente"  style="text-align: right;">0.00</td>
                      </tr>
                      <tr>
                        <td style="background-color: #efefef !important;color: #dc3545 !important;font-weight: bold;">Cumplido y Vencidos</td>
                        <td id="numero_cuota_vencida" style="color: #dc3545 !important;text-align: right;">0</td>
                        <td id="saldo_vencido" style="color: #dc3545 !important;text-align: right;">0.00</td>
                      </tr>
                  </tbody>
                </table>
                <table class="table table-bordered mt-2">
                  <tbody>
                      <tr>
                        <td style="background-color: #bcbcbc !important;color: #000 !important;
    font-weight: bold;" >Saldo capital de deuda (S/.)</td>
                        <td id="saldo_capital" style="text-align: right;width: 50px;" colspan="2">0.00</td>
                      </tr>
                  </tbody>
                </table>
                <div style="text-align: right;">
                  <span id="cont_listanegra"></span>
                </div>
