<div class="row" id="detallecierre">
     <div class="col-6">

      <table style="width: 100%;text-align: left;">
            <tr>
              <th colspan="3" style="background-color: #afaeae;">GENERAL</th>
            </tr>
            <tr>
              <th width="10px">Fecha de registro</th>
              <th width="1px">:</th>
              <th>{{ $s_aperturacierre->fecharegistro != null ? date_format(date_create($s_aperturacierre->fecharegistro), 'd/m/Y - h:i:s A' ) : '---' }}</th>
            </tr>
            <tr>
              <th width="10px">Fecha de Confirmacion</th>
              <th width="1px">:</th>
              <th>{{ $s_aperturacierre->fechaconfirmacion != null ? date_format(date_create($s_aperturacierre->fechaconfirmacion), 'd/m/Y - h:i:s A' ) : '---' }}</th>
            </tr>
            <tr>
              <th>Tipo</th>
              <th>:</th>
              <th>{{ $s_aperturacierre->tipo }}</th>
            </tr>
            <tr>
              <th>Responsable</th>
              <th>:</th>
              <th>{{ $s_aperturacierre->responsablenombre }}</th>
            </tr>
            <tr>
              <th>Moneda</th>
              <th>:</th>
              <th>{{ $s_aperturacierre->monedanombre }} </th>
            </tr>
            <tr>
              <th>Monto</th>
              <th>:</th>
              <th>{{ $s_aperturacierre->monto }} </th>
            </tr>
             <tr>
              <th>Concepto</th>
              <th>:</th>
              <th>{{ $s_aperturacierre->concepto }} </th>
            </tr>
      </table>
    </div>
  <div class="col-6" id="detalleapertura">

      <table style="width: 100%;text-align: left;">
            <tr>
              <th colspan="3" style="background-color: #afaeae;">GENERAL</th>
            </tr>
            <tr>
              <th width="10px">Fecha de registro</th>
              <th width="1px">:</th>
              <th>{{ $s_aperturacierre->fecharegistro != null ? date_format(date_create($s_aperturacierre->fecharegistro), 'd/m/Y - h:i:s A' ) : '---' }}</th>
            </tr>
            <tr>
              <th width="10px">Fecha de Confirmacion</th>
              <th width="1px">:</th>
              <th>{{ $s_aperturacierre->fechaconfirmacion != null ? date_format(date_create($s_aperturacierre->fechaconfirmacion), 'd/m/Y - h:i:s A' ) : '---' }}</th>
            </tr>
            <tr>
              <th>Tipo</th>
              <th>:</th>
              <th>{{ $s_aperturacierre->tipo }}</th>
            </tr>
            <tr>
              <th>Responsable</th>
              <th>:</th>
              <th>{{ $s_aperturacierre->responsablenombre }}</th>
            </tr>
            <tr>
              <th>Moneda</th>
              <th>:</th>
              <th>{{ $s_aperturacierre->monedanombre }} </th>
            </tr>
            <tr>
              <th>Monto</th>
              <th>:</th>
              <th>{{ $s_aperturacierre->monto }} </th>
            </tr>
             <tr>
              <th>Concepto</th>
              <th>:</th>
              <th>{{ $s_aperturacierre->concepto }} </th>
            </tr>
      </table>
    </div>
</div>
