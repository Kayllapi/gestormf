<table style="width:100%">
    <thead>
        <tr></tr>
        <tr>
            <th></th>
            <th style="font-weight: 900; background-color:#31353d; color: #ffffff; text-align: center; font-size: 12px; " colspan="15">
              {{ $titulo }}
            </th>
        </tr>
    
        <tr>
            <th></th>
            <th style="font-weight: 900;">Agencia:</th>
            <th style="font-weight: 900;" colspan="14">{{ $agencia?$agencia->nombreagencia:'TODA LAS AGENCIAS' }}</th>
        </tr>
        <tr>
            <th></th>
            <th style="font-weight: 900;">Forma de Crédito:</th>
            <th style="font-weight: 900;" colspan="14">{{ $idformacredito!=0?$idformacredito:'TODO' }}</th>
        </tr>
        <tr>
            <th></th>
            <th style="font-weight: 900;">Ejecutivo:</th>
            <th style="font-weight: 900;" colspan="14">{{ $asesor?$asesor->usuario:'TODO' }}</th>
        </tr>
        <tr></tr>
        <tr>
                  <td></td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">N°</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Cod. Cliente</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">DOC</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Nombre</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Ejec. Origen</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Saldo C. (S/.)</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">F. Pago</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Cuotas</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Form. C.</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Producto</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Fecha Cancelación</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Telefóno</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Direc/Domicilio</td>
                  <td style="border-top: 2px solid #000;border-bottom: 2px solid #000;text-align:center">Direc/Negocio</td>
        </tr>
    </thead>
    <tbody>
      
              <?php  
          
          $where1 = [];
          if($idformacredito!='' && $idformacredito!=0){
              if($idformacredito=='CP'){
                  $where1[] = ['credito.idforma_credito',1];
              }
              elseif($idformacredito=='CNP'){
                  $where1[] = ['credito.idforma_credito',2];
              }
              elseif($idformacredito=='CC'){
                  $where1[] = ['credito.idforma_credito',3];
              }
          }
                
            $html = '';
            $orden = 1;
            foreach($users as $value){
              
               $credito = DB::table('credito')
                    ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                    ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
                    ->leftjoin('users','users.id','credito.idasesor')
                    ->where('credito.idcliente',$value->id)
                    ->whereIn('credito.idestadocredito',[1,2])
                    ->where('credito.estado','DESEMBOLSADO')
                    ->where($where1)
                    ->select(
                        'credito.*',
                        'credito_prendatario.nombre as nombreproductocredito' ,
                        'forma_pago_credito.nombre as frecuencianombre' ,
                        'users.usuario as codigoasesor',
                    )
                    ->orderBy('id','desc')
                    ->first();
              
              $saldo_pendientepago = '';
              $frecuencianombre = '';
              $cuota = '';
              $nombreproductocredito = '';
              $idforma_credito = '';
              $fecha_cancelado = '';
              if($credito!='' || $idformacredito==0){
                  if($credito){
                      $saldo_pendientepago = $credito->saldo_pendientepago;
                      $frecuencianombre = $credito->frecuencianombre;
                      $cuota = $credito->cuotas;
                      $nombreproductocredito = $credito->nombreproductocredito;
                      $idforma_credito = $credito->idforma_credito;
                      $fecha_cancelado = $credito->fecha_cancelado;
                  }
              
                  $users_prestamo = DB::table('s_users_prestamo')->where('s_users_prestamo.id_s_users',$value->id)->first();

                  $direccionnegocio = '';
                  if($users_prestamo){
                      $direccionnegocio = $users_prestamo->direccion_ac_economica;
                  }

                  $cp = '';
                  if($idforma_credito==1){
                      $cp = 'CP';
                  }
                  elseif($idforma_credito==2){
                      $cp = 'CNP';
                  }
                  elseif($idforma_credito==3){
                      $cp = 'CC';
                  }

                  $usersasesor = DB::table('users')->whereId($value->idasesor)->first();
                  $codigoasesor = '';
                  if($usersasesor){
                      $codigoasesor = $usersasesor->usuario;
                  }
                
                  $fecha_cancelado = $fecha_cancelado!=''?$fecha_cancelado:'';

                  $html .= "<tr>
                                <td></td>
                                <td>".$orden."</td>
                                <td>C{$value->codigo}</td>
                                <td>{$value->identificacion}</td>
                                <td>{$value->nombrecompleto}</td>
                                <td>{$codigoasesor}</td>
                                <td>{$saldo_pendientepago}</td>
                                <td>{$frecuencianombre}</td>
                                <td>{$cuota}</td>
                                <td>{$cp}</td>
                                <td>{$nombreproductocredito}</td>
                                <td>{$fecha_cancelado}</td>
                                <td>{$value->numerotelefono}</td>
                                <td>{$value->direccion}</td>
                                <td>{$direccionnegocio}</td>
                            </tr>";
                  $orden++;
              }
            }

                  $html .= '<tr>
                                <td></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                                <td style="border-top: 2px solid #000;"></td>
                            </tr>';
            echo $html;
              ?>
    </tbody>
</table>