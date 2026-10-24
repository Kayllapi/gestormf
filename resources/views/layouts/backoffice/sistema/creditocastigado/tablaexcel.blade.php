<table style="width:100%">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <th style="font-weight: 900; background-color:#31353d; color: #ffffff; text-align: center; font-size: 12px; " colspan="16">
              {{ $titulo }}
            </th>
        </tr>
    
        <tr>
            <th></th>
            <th style="font-weight: 900;">Agencia:</th>
            <th style="font-weight: 900;" colspan="15">{{ $agencia?$agencia->nombreagencia:'TODA LAS AGENCIAS' }}</th>
        </tr>
        <tr>
            <th></th>
            <th style="font-weight: 900;">Forma de Crédito:</th>
            <th style="font-weight: 900;" colspan="15">{{ $idformacredito!=0?$idformacredito:'TODO' }}</th>
        </tr>
        <tr>
            <th></th>
            <th style="font-weight: 900;">Ejecutivo:</th>
            <th style="font-weight: 900;" colspan="15">{{ $asesor?$asesor->usuario:'TODO' }}</th>
        </tr>
        <tr>
            <th></th>
            <th style="font-weight: 900;">Corte:</th>
            <th style="font-weight: 900;" colspan="15">{{$fecha_inicio}}</th>
        </tr>
        <tr></tr>
        <tr>
                  <td></td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">N°</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">CUENTA</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">DOI/RUC</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Apellidos y Nombres</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Fecha Desemb.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">MONTO (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Saldo C. (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">F. Pago</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Cuotas</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Form. C.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Días de atraso</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Calificación</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Producto</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Modalidad</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Tele./Celu.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Direc/Domicilio</td>
        </tr>
    </thead>
    <tbody>
            
              <?php  
          $html = '';
          $total_desembolsado = 0;
          $total_saldo = 0;
          foreach($creditos as $key => $value){

              $cp = '';
              if($value->idforma_credito==1){
                  $cp = 'CP';
              }
              elseif($value->idforma_credito==2){
                  $cp = 'CNP';
              }
              elseif($value->idforma_credito==3){
                  $cp = 'CC';
              }
            
              $cronograma = select_cronograma(
                  $tienda->id,
                  $value->id,
                  $value->idforma_credito,
                  $value->modalidadproductocredito,
                  $value->cuotas,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  1,
                  'detalle_cobranza'
              );
            
              $clasificacion = '';
            
              if($cronograma['ultimo_atraso']<=8){
                  $clasificacion = 'NORMAL';
              }
              elseif($cronograma['ultimo_atraso']>8 && $cronograma['ultimo_atraso']<=30){
                  $clasificacion = 'CPP';
              }
              elseif($cronograma['ultimo_atraso']>30 && $cronograma['ultimo_atraso']<=60){
                  $clasificacion = 'DIFICIENTE';
              }
              elseif($cronograma['ultimo_atraso']>60 && $cronograma['ultimo_atraso']<=120){
                  $clasificacion = 'DUDOSO';
              }
              elseif($cronograma['ultimo_atraso']>120){
                  $clasificacion = 'PÉRDIDA';
              }
              
              $html .= "<tr>
                            <td></td>
                            <td>".($key+1)."</td>
                            <td>C{$value->cuenta}</td>
                            <td>{$value->identificacioncliente}</td>
                            <td>{$value->nombrecliente}</td>
                            <td>{$value->fecha_desembolso}</td>
                            <td style='text-align:right;'>{$value->monto_solicitado}</td>
                            <td style='text-align:right;'>{$value->saldo_pendientepago}</td>
                            <td>{$value->frecuencianombre}</td>
                            <td style='text-align:right;'>{$value->cuotas}</td>
                            <td>$cp</td>
                            <td>{$cronograma['ultimo_atraso']}</td>
                            <td>{$clasificacion}</td>
                            <td>{$value->nombreproductocredito}</td>
                            <td>{$value->nombremodalidadcredito}</td>
                            <td>{$value->telefonocliente}</td>
                            <td>{$value->direccioncliente}, {$value->ubigeonombre}</td>
                        </tr>";
              $total_desembolsado += $value->monto_solicitado;
              $total_saldo += $value->saldo_pendientepago;
          }
          if(count($creditos)==0){
              $html.= '<tr><td></td><td colspan="16" style="border-bottom: 2px solid #000;text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }
              $html .= '
                <tr>
                  <td></td>
                  <td colspan="5" style="border-bottom: 2px solid #000;border-top: 2px solid #000;text-align:right;">TOTAL S/.</td>
                  <td style="border-bottom: 2px solid #000;border-top: 2px solid #000;text-align:right;">'.number_format($total_desembolsado, 2, '.', '').'</td>
                  <td style="border-bottom: 2px solid #000;border-top: 2px solid #000;text-align:right;">'.number_format($total_saldo, 2, '.', '').'</td>
                  <td colspan="9" style="border-bottom: 2px solid #000;border-top: 2px solid #000;"></td>
                </tr>';
            echo $html;
              ?>
    </tbody>
</table>