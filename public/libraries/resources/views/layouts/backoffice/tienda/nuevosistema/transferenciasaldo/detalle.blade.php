<?php
if ($transferenciasaldo->idestado==1){
    $estado = 'Pendiente';

}elseif ($transferenciasaldo->idestado==2){
    $estado = 'Confirmado';

}elseif ($transferenciasaldo->idestado==3){
    $estado = 'Anulado';
}
?>
       <table class="tabla-detalle" style="margin-bottom:8px" >
                      <th colspan="7">GENERAL</th>
                  <tr>
                    <td width="13%">Codigo</td>
                    <td width="1px">:</td>
                    <td width="500px">{{ str_pad($transferenciasaldo->codigo, 6, "0", STR_PAD_LEFT) }} </td>
                    <td width="13%">Fecha Solicitud</td>
                    <td width="1px">:</td>
                    <td>{{ date_format(date_create($transferenciasaldo->fechasolicitud), "d/m/Y - h:i A" ) }}</td>
                  </tr>
                  <tr>
                    <td width="13%">Caja Origen</td>
                    <td width="1px">:</td>
                    <td width="500px">{{ $transferenciasaldo->cajaorigen_nombre."($transferenciasaldo->responsableorigen_nombre)" }}</td>
                    <td width="13%">Fecha Recepcion</td>
                    <td width="1px">:</td>
                    <td width="">{{ date_format(date_create($transferenciasaldo->fecharecepcion), "d/m/Y - h:i A") }}</td>
                  </tr>
                  <tr>
                    <td width="13%">Caja Destino</td>
                    <td width="1px">:</td>
                    <td width="500px">{{ $transferenciasaldo->cajadestino_nombre."($transferenciasaldo->responsabledestino_nombre)" }}</td>
                    <td width="13%">Estado</td>
                    <td width="1px">:</td>
                    <td width="">{{ $estado }}</td>
                  </tr>
                  <tr>
                    <td width="13%">Motivo</td>
                    <td width="1px">:</td>
                    <td width="400px">{{ $transferenciasaldo->motivo }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    </tr>
            </table>
