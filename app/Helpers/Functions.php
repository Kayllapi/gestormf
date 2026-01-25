<?php
function consolidadooperaciones($tienda,$idagencia,$fechacorte){
           
          $agencia = DB::table('tienda')->whereId($idagencia)->first();
          $bancos = DB::table('banco')->where('estado','ACTIVO')->get();
          
          // Ingreso y Egreso por Caja
          $where = [];
          if($idagencia!=''){
              $where[] = ['credito.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              $where[] = ['credito_cobranzacuota.fecharegistro','>=',$fechacorte.' 00:00:00'];
              $where[] = ['credito_cobranzacuota.fecharegistro','<=',$fechacorte.' 23:59:59'];
          }
          $ingresoyegresocaja_ingreso_crediticio_cnps = DB::table('credito_cobranzacuota')
              ->join('credito','credito.id','credito_cobranzacuota.idcredito')
              ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
              ->where('credito_cobranzacuota.idestadoextorno',0)
              ->where('credito.idforma_credito',2)
              ->where('credito_cobranzacuota.idformapago',1)
              ->where($where)
              ->select(
                  'credito_cobranzacuota.*',
              )
              ->orderBy('credito_cobranzacuota.id','asc')
              ->get();
          
          $ingresoyegresocaja_ingreso_crediticio_cnp_capital = 0;
          $ingresoyegresocaja_ingreso_crediticio_cnp_interes = 0;
          $ingresoyegresocaja_ingreso_crediticio_cnp_desgravcargo = 0;
          $ingresoyegresocaja_ingreso_crediticio_cnp_tenencxc = 0;
          $ingresoyegresocaja_ingreso_crediticio_cnp = 0;
          $ingresoyegresocaja_ingreso_crediticio = 0;
          
          foreach($ingresoyegresocaja_ingreso_crediticio_cnps as $value){
              $ingresoyegresocaja_ingreso_crediticio_cnp_capital += $value->total_pagar_amortizacion;
              $ingresoyegresocaja_ingreso_crediticio_cnp_interes += $value->total_pagar_interes;
              $ingresoyegresocaja_ingreso_crediticio_cnp_desgravcargo += $value->total_pagar_comision+$value->total_pagar_cargo;
              $ingresoyegresocaja_ingreso_crediticio_cnp_tenencxc += $value->total_pagar_tenencia+$value->total_pagar_penalidad+$value->total_pagar_compensatorio;
          }
          
          $ingresoyegresocaja_ingreso_crediticio_cnp = $ingresoyegresocaja_ingreso_crediticio_cnp_capital+
                                              $ingresoyegresocaja_ingreso_crediticio_cnp_interes+
                                              $ingresoyegresocaja_ingreso_crediticio_cnp_desgravcargo+
                                              $ingresoyegresocaja_ingreso_crediticio_cnp_tenencxc;
          
          
          $where = [];
          if($idagencia!=''){
              $where[] = ['credito.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              $where[] = ['credito_cobranzacuota.fecharegistro','>=',$fechacorte.' 00:00:00'];
              $where[] = ['credito_cobranzacuota.fecharegistro','<=',$fechacorte.' 23:59:59'];
          }
          
          $ingresoyegresocaja_ingreso_crediticio_cps = DB::table('credito_cobranzacuota')
              ->join('credito','credito.id','credito_cobranzacuota.idcredito')
              ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
              ->where('credito_cobranzacuota.idestadoextorno',0)
              ->where('credito.idforma_credito',1)
              ->where('credito_cobranzacuota.idformapago',1)
              ->where($where)
              ->select(
                  'credito_cobranzacuota.*',
              )
              ->orderBy('credito_cobranzacuota.id','asc')
              ->get();
          
          $ingresoyegresocaja_ingreso_crediticio_cp_capital = 0;
          $ingresoyegresocaja_ingreso_crediticio_cp_interes = 0;
          $ingresoyegresocaja_ingreso_crediticio_cp_desgravcargo = 0;
          $ingresoyegresocaja_ingreso_crediticio_cp_tenencxc = 0;
          $ingresoyegresocaja_ingreso_crediticio_cp = 0;
          
          foreach($ingresoyegresocaja_ingreso_crediticio_cps as $value){
              $ingresoyegresocaja_ingreso_crediticio_cp_capital += $value->total_pagar_amortizacion;
              $ingresoyegresocaja_ingreso_crediticio_cp_interes += $value->total_pagar_interes;
              $ingresoyegresocaja_ingreso_crediticio_cp_desgravcargo += $value->total_pagar_comision+$value->total_pagar_cargo;
              $ingresoyegresocaja_ingreso_crediticio_cp_tenencxc += $value->total_pagar_tenencia+$value->total_pagar_penalidad+$value->total_pagar_compensatorio;
          }
          
          $ingresoyegresocaja_ingreso_crediticio_cp = $ingresoyegresocaja_ingreso_crediticio_cp_capital+
                                              $ingresoyegresocaja_ingreso_crediticio_cp_interes+
                                              $ingresoyegresocaja_ingreso_crediticio_cp_desgravcargo+
                                              $ingresoyegresocaja_ingreso_crediticio_cp_tenencxc;
          
          $ingresoyegresocaja_ingreso_crediticio = $ingresoyegresocaja_ingreso_crediticio_cnp+
                                                    $ingresoyegresocaja_ingreso_crediticio_cp;
          
          $where = [];
          if($idagencia!=''){
              $where[] = ['credito.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              //$where[] = ['credito_formapago.fechapago','>=',$fechacorte.' 00:00:00'];
              $where[] = ['credito_formapago.fechapago','<=',$fechacorte.' 23:59:59'];
          }
          $ingresoyegresocaja_ingreso_crediticio_transitorio = DB::table('credito_formapago')
              ->join('credito','credito.id','credito_formapago.idcredito')
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito.idestadorefinanciamiento',1)
              ->where($where)
              ->sum('credito.monto_solicitado');
          
          $ingresoyegresocaja_ingreso_ahorro_plazofijo = 0;
          $ingresoyegresocaja_ingreso_ahorro_ahorroc = 0;
          
          $ingresoyegresocaja_ingreso_ahorro = $ingresoyegresocaja_ingreso_ahorro_plazofijo+
                                                $ingresoyegresocaja_ingreso_ahorro_ahorroc;
          
          $where = [];
          if($idagencia!=''){
              $where[] = ['asignacioncapital.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              $where[] = ['asignacioncapital.fecharegistro','>=',$fechacorte.' 00:00:00'];
              $where[] = ['asignacioncapital.fecharegistro','<=',$fechacorte.' 23:59:59'];
          }
          $ingresoyegresocaja_ingreso_incrementocapital = DB::table('asignacioncapital')
                ->where('asignacioncapital.idtipodestino',1)
                ->where('asignacioncapital.idestadoeliminado',1)
                ->where('asignacioncapital.idtipooperacion',1)
                ->where($where)
                ->sum('asignacioncapital.monto');
            
          $where = [];
          if($idagencia!=''){
              $where[] = ['ingresoextraordinario.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              $where[] = ['ingresoextraordinario.fechapago','>=',$fechacorte.' 00:00:00'];
              $where[] = ['ingresoextraordinario.fechapago','<=',$fechacorte.' 23:59:59'];
          }
          $ingresoyegresocaja_ingreso_ingresosextraordinarios = DB::table('ingresoextraordinario')
                ->where('ingresoextraordinario.idformapago',1) 
                ->where('ingresoextraordinario.idestadoeliminado',1) 
                ->where($where)
                ->sum('ingresoextraordinario.monto');
          
          //-------
            
          $where = [];
          if($idagencia!=''){
              $where[] = ['credito.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              $where[] = ['credito_formapago.fechapago','>=',$fechacorte.' 00:00:00'];
              $where[] = ['credito_formapago.fechapago','<=',$fechacorte.' 23:59:59'];
          }
          $ingresoyegresocaja_egreso_crediticio = DB::table('credito_formapago')
              ->join('credito','credito.id','credito_formapago.idcredito')
              ->where('credito_formapago.idformapago',1)
              ->where('credito.estado','DESEMBOLSADO')
              ->where($where)
              ->sum('credito.monto_solicitado');
          
          $ingresoyegresocaja_egreso_ahorro_plazofijo = 0;
          $ingresoyegresocaja_egreso_ahorro_intplazofijo = 0;
          $ingresoyegresocaja_egreso_ahorro_ahorrocte = 0;
          $ingresoyegresocaja_egreso_ahorro_intcte = 0;
          
          $ingresoyegresocaja_egreso_ahorro = $ingresoyegresocaja_egreso_ahorro_plazofijo+
                                                $ingresoyegresocaja_egreso_ahorro_intplazofijo+
                                                $ingresoyegresocaja_egreso_ahorro_ahorrocte+
                                                $ingresoyegresocaja_egreso_ahorro_intcte;
          
          $where = [];
          if($idagencia!=''){
              $where[] = ['asignacioncapital.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              $where[] = ['asignacioncapital.fecharegistro','>=',$fechacorte.' 00:00:00'];
              $where[] = ['asignacioncapital.fecharegistro','<=',$fechacorte.' 23:59:59'];
          }
          $ingresoyegresocaja_egreso_reduccioncapital = DB::table('asignacioncapital')
                ->whereIn('asignacioncapital.idtipodestino',[0,1])
                ->where('asignacioncapital.idestadoeliminado',1)
                ->where('asignacioncapital.idtipooperacion',2)
                ->where($where)
                ->sum('asignacioncapital.monto');
          
          $where = [];
          if($idagencia!=''){
              $where[] = ['gastoadministrativooperativo.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              $where[] = ['gastoadministrativooperativo.fechapago','>=',$fechacorte.' 00:00:00'];
              $where[] = ['gastoadministrativooperativo.fechapago','<=',$fechacorte.' 23:59:59'];
          }
          $ingresoyegresocaja_egreso_gastosadministrativosyoperativos = DB::table('gastoadministrativooperativo')
                ->where('gastoadministrativooperativo.idformapago',1) 
                ->where('gastoadministrativooperativo.idestadoeliminado',1) 
                ->where($where)
                ->sum('gastoadministrativooperativo.monto');
          
          // Ingreso y Egreso por Cuenta Banco
          $validacion_operaciones_cuenta_banco_cant = 0;
  
          $where = [];
          if($idagencia!=''){
              $where[] = ['credito.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              $where[] = ['credito_cobranzacuota.fecharegistro','>=',$fechacorte.' 00:00:00'];
              $where[] = ['credito_cobranzacuota.fecharegistro','<=',$fechacorte.' 23:59:59'];
          }
          $ingresoyegresobanco_ingreso_crediticio_cnpcp = 0;
          $ingresoyegresobanco_ingreso_crediticio_cnpcps_bancos = [];
          $ingresoyegresobanco_ingreso_crediticio_cnpcps_validacion = '';
          $validacion_0 = '';
          $validacion_cantidad = 0;
          $ingresoyegresobanco_ingreso_crediticio_cnpcps_validacion_cantidad = 0;
          foreach($bancos as $valuebancos){
              $bancosdatas = DB::table('credito_cobranzacuota')
                  ->join('credito','credito.id','credito_cobranzacuota.idcredito')
                  ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
                  ->where('credito_cobranzacuota.idestadoextorno',0)
                  ->where('credito_cobranzacuota.idformapago',2)
                  ->where('credito_cobranzacuota.idbanco',$valuebancos->id)
                  ->where($where)
                  ->select(
                      'credito_cobranzacuota.*',
                  )
                  ->orderBy('credito_cobranzacuota.id','asc')
                  ->get();  
          
              $banco_capital = 0;
              $banco_interes = 0;
              $banco_desgravcargo = 0;
              $banco_tenencxc = 0;
              $banco = 0;
              $validacion_1 = '';
              foreach($bancosdatas as $value){
                  if($value->validar_estado==1 && $validacion_1 == ''){
                      $validacion_1 = 'CHECK';
                  }
                  $banco_capital += $value->total_pagar_amortizacion;
                  $banco_interes += $value->total_pagar_interes;
                  $banco_desgravcargo += $value->total_pagar_comision+$value->total_pagar_cargo;
                  $banco_tenencxc += $value->total_pagar_tenencia+$value->total_pagar_penalidad+$value->total_pagar_compensatorio;
              }
                  
              if($validacion_1=='CHECK' && $validacion_0 == ''){
                  $validacion_0 = 'CHECK';  
                  $validacion_operaciones_cuenta_banco_cant += 1;
              }
            
              if(count($bancosdatas)>0){
                  $validacion_cantidad += 1;
                  $ingresoyegresobanco_ingreso_crediticio_cnpcps_validacion_cantidad += 1;
              }
            
              $ingresoyegresobanco_ingreso_crediticio_cnpcp += number_format($banco_capital+$banco_interes+$banco_desgravcargo+$banco_tenencxc, 2, '.', '');
            
              $ingresoyegresobanco_ingreso_crediticio_cnpcps_bancos[] = [
                  'banco_nombre' => $valuebancos->nombre,
                  'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
                  'banco_capital' => number_format($banco_capital, 2, '.', ''),
                  'banco_interes' => number_format($banco_interes, 2, '.', ''),
                  'banco_desgravcargo' => number_format($banco_desgravcargo, 2, '.', ''),
                  'banco_tenencxc' => number_format($banco_tenencxc, 2, '.', ''),
                  'banco' => number_format($banco_capital+$banco_interes+$banco_desgravcargo+$banco_tenencxc, 2, '.', ''),
                  'validacion' => $validacion_1
              ];
          }
          $ingresoyegresobanco_ingreso_crediticio_cnpcps_validacion = $validacion_0;
          
          $where = [];
          if($idagencia!=''){
              $where[] = ['asignacioncapital.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              $where[] = ['asignacioncapital.fecharegistro','>=',$fechacorte.' 00:00:00'];
              $where[] = ['asignacioncapital.fecharegistro','<=',$fechacorte.' 23:59:59'];
          }
          $ingresoyegresobanco_ingreso_incrementocapital = 0;
          $ingresoyegresobanco_ingreso_incrementocapital_bancos = [];
          $ingresoyegresobanco_ingreso_incrementocapital_validacion = '';
          $validacion_0 = '';
          $ingresoyegresobanco_ingreso_incrementocapital_validacion_cantidad = 0;
          foreach($bancos as $valuebancos){
            
              $db_ingresoyegresobanco_ingreso_incrementocapital = DB::table('asignacioncapital')
                  ->where('asignacioncapital.idtipodestino',3)
                  ->where('asignacioncapital.idestadoeliminado',1)
                  ->where('asignacioncapital.idtipooperacion',1)
                  ->where('asignacioncapital.idbanco',$valuebancos->id)
                  ->where($where)
                  ->get();
            
              $validacion_1 = '';
              $ingresoyegresobanco_ingreso_incrementocapital_monto = 0;
              foreach($db_ingresoyegresobanco_ingreso_incrementocapital as $valuecrediticio){
                  if($valuecrediticio->validar_estado==1 && $validacion_1 == ''){
                      $validacion_1 = 'CHECK';
                  }
                  $ingresoyegresobanco_ingreso_incrementocapital_monto += $valuecrediticio->monto;
              }
                  
              if($validacion_1=='CHECK' && $validacion_0 == ''){
                  $validacion_0 = 'CHECK';
                  $validacion_operaciones_cuenta_banco_cant += 1;
              }
            
              if(count($db_ingresoyegresobanco_ingreso_incrementocapital)>0){
                  $validacion_cantidad += 1;
                  $ingresoyegresobanco_ingreso_incrementocapital_validacion_cantidad += 1;
              }
            
              $ingresoyegresobanco_ingreso_incrementocapital += number_format($ingresoyegresobanco_ingreso_incrementocapital_monto, 2, '.', '');
            
              $ingresoyegresobanco_ingreso_incrementocapital_bancos[] = [
                  'banco_nombre' => $valuebancos->nombre,
                  'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
                  'banco' => number_format($ingresoyegresobanco_ingreso_incrementocapital_monto, 2, '.', ''),
                  'validacion' => $validacion_1
              ];
          }
          $ingresoyegresobanco_ingreso_incrementocapital_validacion = $validacion_0;
          
          $where = [];
          if($idagencia!=''){
              $where[] = ['ingresoextraordinario.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              $where[] = ['ingresoextraordinario.fechapago','>=',$fechacorte.' 00:00:00'];
              $where[] = ['ingresoextraordinario.fechapago','<=',$fechacorte.' 23:59:59'];
          }
          $ingresoyegresobanco_ingreso_ingresosextraordinarios = 0;
          $ingresoyegresobanco_ingreso_ingresosextraordinarios_bancos = [];
          $ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion = '';
          $validacion_0 = '';
          $ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion_cantidad = 0;
          foreach($bancos as $valuebancos){
            
              $db_ingresoyegresobanco_ingreso_ingresosextraordinarios = DB::table('ingresoextraordinario')
                  ->where('ingresoextraordinario.idformapago',2) 
                  ->where('ingresoextraordinario.idestadoeliminado',1) 
                  ->where('ingresoextraordinario.idbanco',$valuebancos->id)
                  ->where($where)
                  ->get();
            
              $validacion_1 = '';
              $ingresoyegresobanco_ingreso_ingresosextraordinarios_monto = 0;
              foreach($db_ingresoyegresobanco_ingreso_ingresosextraordinarios as $valuecrediticio){
                  if($valuecrediticio->validar_estado==1 && $validacion_1 == ''){
                      $validacion_1 = 'CHECK';
                  }
                  $ingresoyegresobanco_ingreso_ingresosextraordinarios_monto += $valuecrediticio->monto;
              }
                  
              if($validacion_1=='CHECK' && $validacion_0 == ''){
                  $validacion_0 = 'CHECK';
                  $validacion_operaciones_cuenta_banco_cant += 1;
              }
            
              if(count($db_ingresoyegresobanco_ingreso_ingresosextraordinarios)>0){
                  $validacion_cantidad += 1;
                  $ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion_cantidad += 1;
              }
            
              $ingresoyegresobanco_ingreso_ingresosextraordinarios += number_format($ingresoyegresobanco_ingreso_ingresosextraordinarios_monto, 2, '.', '');
            
              $ingresoyegresobanco_ingreso_ingresosextraordinarios_bancos[] = [
                  'banco_nombre' => $valuebancos->nombre,
                  'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
                  'banco' => number_format($ingresoyegresobanco_ingreso_ingresosextraordinarios_monto, 2, '.', ''),
                  'validacion' => $validacion_1
              ];
          }
          $ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion = $validacion_0;
          
          //-------
          
          $where = [];
          if($idagencia!=''){
              $where[] = ['credito.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              $where[] = ['credito_formapago.fechapago','>=',$fechacorte.' 00:00:00'];
              $where[] = ['credito_formapago.fechapago','<=',$fechacorte.' 23:59:59'];
          }
          $ingresoyegresobanco_egreso_crediticio = 0;
          $ingresoyegresobanco_egreso_crediticio_bancos = [];
          $ingresoyegresobanco_egreso_crediticio_validacion = '';
          $validacion_0 = '';
          $ingresoyegresobanco_egreso_crediticio_validacion_cantidad = 0;
          foreach($bancos as $valuebancos){
            
              $db_desembolsos_ingresoyegresobanco_egreso_crediticio = DB::table('credito_formapago')
                  ->join('credito','credito.id','credito_formapago.idcredito')
                  ->where('credito_formapago.idformapago',2)
                  ->where('credito.estado','DESEMBOLSADO')
                  ->where('credito_formapago.idbanco',$valuebancos->id)
                  ->where($where)
                  ->get();
            
              $validacion_1 = '';
              $desembolsos_ingresoyegresobanco_egreso_crediticio = 0;
              foreach($db_desembolsos_ingresoyegresobanco_egreso_crediticio as $valuecrediticio){
                  if($valuecrediticio->validar_estado==1 && $validacion_1 == ''){
                      $validacion_1 = 'CHECK';
                  }
                  $desembolsos_ingresoyegresobanco_egreso_crediticio += $valuecrediticio->monto_solicitado;
              }
                  
              if($validacion_1=='CHECK' && $validacion_0 == ''){
                  $validacion_0 = 'CHECK';
                  $validacion_operaciones_cuenta_banco_cant += 1;
              }
            
              if(count($db_desembolsos_ingresoyegresobanco_egreso_crediticio)>0){
                  $validacion_cantidad += 1;
                  $ingresoyegresobanco_egreso_crediticio_validacion_cantidad += 1;
              }
            
              $ingresoyegresobanco_egreso_crediticio += number_format($desembolsos_ingresoyegresobanco_egreso_crediticio, 2, '.', '');
              
              $ingresoyegresobanco_egreso_crediticio_bancos[] = [
                  'banco_nombre' => $valuebancos->nombre,
                  'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
                  'banco' => number_format($desembolsos_ingresoyegresobanco_egreso_crediticio, 2, '.', ''),
                  'validacion' => $validacion_1,
              ];
          }
          $ingresoyegresobanco_egreso_crediticio_validacion = $validacion_0;
          
          $where = [];
          if($idagencia!=''){
              $where[] = ['asignacioncapital.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              $where[] = ['asignacioncapital.fecharegistro','>=',$fechacorte.' 00:00:00'];
              $where[] = ['asignacioncapital.fecharegistro','<=',$fechacorte.' 23:59:59'];
          }
          $ingresoyegresobanco_egreso_reduccioncapital = 0;
          $ingresoyegresobanco_egreso_reduccioncapital_bancos = [];
          $ingresoyegresobanco_egreso_reduccioncapital_validacion = '';
          $validacion_0 = '';
          $ingresoyegresobanco_egreso_reduccioncapital_validacion_cantidad = 0;
          foreach($bancos as $valuebancos){
            
              $db_ingresoyegresobanco_egreso_reduccioncapital = DB::table('asignacioncapital')
                  ->where('asignacioncapital.idtipodestino',3)
                  ->where('asignacioncapital.idestadoeliminado',1)
                  ->where('asignacioncapital.idtipooperacion',2)
                  ->where('asignacioncapital.idbanco',$valuebancos->id)
                  ->where($where)
                  ->get();
            
              $validacion_1 = '';
              $ingresoyegresobanco_egreso_reduccioncapital_monto = 0;
              foreach($db_ingresoyegresobanco_egreso_reduccioncapital as $valuecrediticio){
                  if($valuecrediticio->validar_estado==1 && $validacion_1 == ''){
                      $validacion_1 = 'CHECK';
                  }
                  $ingresoyegresobanco_egreso_reduccioncapital_monto += $valuecrediticio->monto;
              }
                  
              if($validacion_1=='CHECK' && $validacion_0 == ''){
                  $validacion_0 = 'CHECK';
                  $validacion_operaciones_cuenta_banco_cant += 1;
              }
            
              if(count($db_ingresoyegresobanco_egreso_reduccioncapital)>0){
                  $validacion_cantidad += 1;
                  $ingresoyegresobanco_egreso_reduccioncapital_validacion_cantidad += 1;
              }
            
              $ingresoyegresobanco_egreso_reduccioncapital += number_format($ingresoyegresobanco_egreso_reduccioncapital_monto, 2, '.', '');
            
              $ingresoyegresobanco_egreso_reduccioncapital_bancos[] = [
                  'banco_nombre' => $valuebancos->nombre,
                  'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
                  'banco' => number_format($ingresoyegresobanco_egreso_reduccioncapital_monto, 2, '.', ''),
                  'validacion' => $validacion_1,
              ];
          }
          $ingresoyegresobanco_egreso_reduccioncapital_validacion = $validacion_0;
          
          $where = [];
          if($idagencia!=''){
              $where[] = ['gastoadministrativooperativo.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              $where[] = ['gastoadministrativooperativo.fechapago','>=',$fechacorte.' 00:00:00'];
              $where[] = ['gastoadministrativooperativo.fechapago','<=',$fechacorte.' 23:59:59'];
          }
          $ingresoyegresobanco_egreso_gastosadministrativosyoperativos = 0;
          $ingresoyegresobanco_egreso_gastosadministrativosyoperativos_bancos = [];
          $ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion = '';
          $validacion_0 = '';
          $ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion_cantidad = 0;
          foreach($bancos as $valuebancos){
            
              $db_ingresoyegresocaja_egreso_gastosadministrativosyoperativos = DB::table('gastoadministrativooperativo')
                  ->where('gastoadministrativooperativo.idformapago',2) 
                  ->where('gastoadministrativooperativo.idestadoeliminado',1) 
                  ->where('gastoadministrativooperativo.idbanco',$valuebancos->id)
                  ->where($where)
                  ->get();
            
              $validacion_1 = '';
              $ingresoyegresocaja_egreso_gastosadministrativosyoperativos_monto = 0;
              foreach($db_ingresoyegresocaja_egreso_gastosadministrativosyoperativos as $valuecrediticio){
                  if($valuecrediticio->validar_estado==1 && $validacion_1 == ''){
                      $validacion_1 = 'CHECK';
                  }
                  $ingresoyegresocaja_egreso_gastosadministrativosyoperativos_monto += $valuecrediticio->monto;
              }
                  
              if($validacion_1=='CHECK' && $validacion_0 == ''){
                  $validacion_0 = 'CHECK';
                  $validacion_operaciones_cuenta_banco_cant += 1;
              }
            
              if(count($db_ingresoyegresocaja_egreso_gastosadministrativosyoperativos)>0){
                  $validacion_cantidad += 1;
                  $ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion_cantidad += 1;
              }
            
              $ingresoyegresobanco_egreso_gastosadministrativosyoperativos += number_format($ingresoyegresocaja_egreso_gastosadministrativosyoperativos_monto, 2, '.', '');
            
              $ingresoyegresobanco_egreso_gastosadministrativosyoperativos_bancos[] = [
                  'banco_nombre' => $valuebancos->nombre,
                  'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
                  'banco' => number_format($ingresoyegresocaja_egreso_gastosadministrativosyoperativos_monto, 2, '.', ''),
                  'validacion' => $validacion_1,
              ];
          }
          $ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion = $validacion_0;
  
          // HABILITACIÓN Y GESTIÓN DE LIQUIDEZ ( I )
          $valid_habilitacion = 0;
          
          $where = [];
          if($idagencia!=''){
              $where[] = ['movimientointernodinero.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              $where[] = ['movimientointernodinero.fecharegistro','<=',$fechacorte.' 23:59:59'];
          }
          
          $ret_reservacf_caja = DB::table('movimientointernodinero')
             ->where('movimientointernodinero.idestadoeliminado',1)
              ->where('movimientointernodinero.idfuenteretiro',6)
              ->where($where)
              ->select(
                  'movimientointernodinero.*',
              )
              ->sum('movimientointernodinero.monto');
          
          $ret_banco_caja = 0;
          $ret_banco_caja_bancos = [];
          $dep_caja_banco = 0;
          $dep_caja_banco_bancos = [];
          foreach($bancos as $valuebancos){
              $movimientointernodineros = DB::table('movimientointernodinero')
                  ->where('movimientointernodinero.idestadoeliminado',1)
                  ->where('movimientointernodinero.idfuenteretiro',7)
                  ->where('movimientointernodinero.idtipomovimientointerno',1)
                  ->where('movimientointernodinero.idbanco',$valuebancos->id)
                  ->where($where)
                  ->sum('movimientointernodinero.monto');
              $movimientointernodineros1 = DB::table('movimientointernodinero')
                  ->where('movimientointernodinero.idestadoeliminado',1)
                  ->where('movimientointernodinero.idfuenteretiro',2)
                  ->where('movimientointernodinero.idtipomovimientointerno',2)
                  ->where('movimientointernodinero.idresponsable','<>',0)
                  ->where('movimientointernodinero.idbanco',$valuebancos->id)
                  ->where($where)
                  ->sum('movimientointernodinero.monto');
              $ret_banco_caja += number_format($movimientointernodineros, 2, '.', '');
              $dep_caja_banco += number_format($movimientointernodineros1, 2, '.', '');
            
              $ret_banco_caja_bancos[] = [
                  'banco_id' => $valuebancos->id,
                  'banco_nombre' => $valuebancos->nombre,
                  'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
                  'banco' => number_format($movimientointernodineros, 2, '.', ''),
                  'banco_dep' => number_format($movimientointernodineros1, 2, '.', ''),
              ];
            
              if($ret_banco_caja!=$dep_caja_banco){
                  $valid_habilitacion++;
              }
          }
          
          $ret_caja_reservacf = DB::table('movimientointernodinero')
              ->where('movimientointernodinero.idestadoeliminado',1)
              ->where('movimientointernodinero.idfuenteretiro',8)
              ->where($where)
              ->sum('movimientointernodinero.monto');
          
          $ret_caja_banco = 0;
          $ret_caja_banco_bancos = [];
          $dep_banco_caja = 0;
          $dep_banco_caja_bancos = [];
          foreach($bancos as $valuebancos){
              $movimientointernodineros = DB::table('movimientointernodinero')
                  ->where('movimientointernodinero.idestadoeliminado',1)
                  ->where('movimientointernodinero.idfuenteretiro',9)
                  ->where('movimientointernodinero.idtipomovimientointerno',1)
                  ->where('movimientointernodinero.idbanco',$valuebancos->id)
                  ->where($where)
                  ->sum('movimientointernodinero.monto');
              $movimientointernodineros1 = DB::table('movimientointernodinero')
                  ->where('movimientointernodinero.idestadoeliminado',1)
                  ->where('movimientointernodinero.idfuenteretiro',4)
                  ->where('movimientointernodinero.idtipomovimientointerno',2)
                  ->where('movimientointernodinero.idresponsable','<>',0)
                  ->where('movimientointernodinero.idbanco',$valuebancos->id)
                  ->where($where)
                  ->sum('movimientointernodinero.monto');
            
              $ret_caja_banco += number_format($movimientointernodineros, 2, '.', '');
              $dep_banco_caja += number_format($movimientointernodineros1, 2, '.', '');
              $ret_caja_banco_bancos[] = [
                  'banco_nombre' => $valuebancos->nombre,
                  'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
                  'banco' => number_format($movimientointernodineros, 2, '.', ''),
                  'banco_dep' => number_format($movimientointernodineros1, 2, '.', ''),
              ];
            
              if($ret_caja_banco!=$dep_banco_caja){
                  $valid_habilitacion++;
              }
          }
          //----
          $dep_caja_reservacf = DB::table('movimientointernodinero')
              ->where('movimientointernodinero.idestadoeliminado',1)
              ->where('movimientointernodinero.idfuenteretiro',1)
              ->where('movimientointernodinero.idresponsable','<>',0)
              ->where($where)
              ->sum('movimientointernodinero.monto');
            
          if($ret_reservacf_caja!=$dep_caja_reservacf){
              $valid_habilitacion++;
          }
          
          $dep_reservacf_caja = DB::table('movimientointernodinero')
              ->where('movimientointernodinero.idestadoeliminado',1)
              ->where('movimientointernodinero.idfuenteretiro',3)
              ->where('movimientointernodinero.idresponsable','<>',0)
              ->where($where)
              ->sum('movimientointernodinero.monto');
  
          if($ret_caja_reservacf!=$dep_reservacf_caja){
              $valid_habilitacion++;
          }
          
          $habilitacion_gestion_liquidez1 = $ret_reservacf_caja+
                                            $ret_banco_caja+
                                            $ret_caja_reservacf+
                                            $ret_caja_banco-
                                            $dep_caja_reservacf-
                                            $dep_caja_banco-
                                            $dep_reservacf_caja-
                                            $dep_banco_caja;
          
          // HABILITACIÓN Y GESTIÓN DE LIQUIDEZ ( II )
          
          $ret_banco_reservacf = 0;
          $ret_banco_reservacf_bancos = [];
          $dep_reservacf_banco = 0;
          $dep_reservacf_banco_bancos = [];
          foreach($bancos as $valuebancos){
              $movimientointernodineros = DB::table('movimientointernodinero')
                  ->where('movimientointernodinero.idestadoeliminado',1)
                  ->where('movimientointernodinero.idfuenteretiro',10)
                  ->where('movimientointernodinero.idtipomovimientointerno',3)
                  ->where('movimientointernodinero.idbanco',$valuebancos->id)
                  ->where($where)
                  ->sum('movimientointernodinero.monto');
              $movimientointernodineros1 = DB::table('movimientointernodinero')
                  ->where('movimientointernodinero.idestadoeliminado',1)
                  ->where('movimientointernodinero.idfuenteretiro',5)
                  ->where('movimientointernodinero.idtipomovimientointerno',4)
                  ->where('movimientointernodinero.idresponsable','<>',0)
                  ->where('movimientointernodinero.idbanco',$valuebancos->id)
                  ->where($where)
                  ->sum('movimientointernodinero.monto');
              $ret_banco_reservacf += number_format($movimientointernodineros, 2, '.', '');
              $dep_reservacf_banco += number_format($movimientointernodineros1, 2, '.', '');
              $ret_banco_reservacf_bancos[] = [
                  'banco_nombre' => $valuebancos->nombre,
                  'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
                  'banco' => number_format($movimientointernodineros, 2, '.', ''),
                  'banco_dep' => number_format($movimientointernodineros1, 2, '.', ''),
              ];
            
              if($ret_banco_reservacf!=$dep_reservacf_banco){
                  $valid_habilitacion++;
              }
          }
          
          $habilitacion_gestion_liquidez2 = $ret_banco_reservacf-$dep_reservacf_banco;
          
          // CIERRE Y APERTURA DE CAJA
          
          $ret_reservacf_caja_total = DB::table('movimientointernodinero')
              ->where('movimientointernodinero.idestadoeliminado',1)
              ->where('movimientointernodinero.idfuenteretiro',6)
              ->where('movimientointernodinero.idtipomovimientointerno',5)
              ->where($where)
              ->sum('movimientointernodinero.monto');
          $ret_caja_reservacf_total = DB::table('movimientointernodinero')
              ->where('movimientointernodinero.idestadoeliminado',1)
              ->where('movimientointernodinero.idfuenteretiro',8)
              ->where('movimientointernodinero.idtipomovimientointerno',5)
              ->where($where)
              ->sum('movimientointernodinero.monto');
          
          $dep_caja_reservacf_total = DB::table('movimientointernodinero')
              ->where('movimientointernodinero.idestadoeliminado',1)
              ->where('movimientointernodinero.idfuenteretiro',1)
              ->where('movimientointernodinero.idtipomovimientointerno',6)
              ->where('movimientointernodinero.idresponsable','<>',0)
              ->where($where)
              ->sum('movimientointernodinero.monto');
          $dep_reservacf_caja_total = DB::table('movimientointernodinero')
              ->where('movimientointernodinero.idestadoeliminado',1)
              ->where('movimientointernodinero.idfuenteretiro',3)
              ->where('movimientointernodinero.idtipomovimientointerno',6)
              ->where('movimientointernodinero.idresponsable','<>',0)
              ->where($where)
              ->sum('movimientointernodinero.monto');
  
  
          if($ret_reservacf_caja_total!=$dep_caja_reservacf_total){
              $valid_habilitacion++;
          }
          if($ret_caja_reservacf_total!=$dep_reservacf_caja_total){
              $valid_habilitacion++;
          }
          
          $cierre_caja_apertura = $ret_reservacf_caja_total+
                                  $ret_caja_reservacf_total-
                                  $dep_caja_reservacf_total-
                                  $dep_reservacf_caja_total;
          
          // --------------------------------------- //
          // ----------- SALDOS  FINALES ----------- //
          // --------------------------------------- //
          
          $where = [];
          if($idagencia!=''){
              $where[] = ['arqueocaja.idagencia',$idagencia];
          }
          if($fechacorte!=''){
              $where[] = ['arqueocaja.corte',$fechacorte];
          }
          $arqueo_caja = DB::table('arqueocaja')
              ->where('arqueocaja.idestado',1)
              ->where($where)
              ->sum('arqueocaja.total');
  
          $where1 = [];
          $where2 = [];
          $where3 = [];
          $where4 = [];
          $where5 = [];
          $where6 = [];
          if($idagencia!=''){
              $where1[] = ['credito_cobranzacuota.idtienda',$idagencia];
              $where2[] = ['credito.idtienda',$idagencia];
              $where3[] = ['asignacioncapital.idtienda',$idagencia];
              $where4[] = ['movimientointernodinero.idtienda',$idagencia];
              $where5[] = ['gastoadministrativooperativo.idtienda',$idagencia];
              $where6[] = ['ingresoextraordinario.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              $where1[] = ['credito_cobranzacuota.fecharegistro','<=',$fechacorte.' 23:59:59'];
              $where2[] = ['credito_formapago.fechapago','<=',$fechacorte.' 23:59:59'];
              $where3[] = ['asignacioncapital.fecharegistro','<=',$fechacorte.' 23:59:59'];
              $where4[] = ['movimientointernodinero.fecharegistro','<=',$fechacorte.' 23:59:59'];
              $where5[] = ['gastoadministrativooperativo.fechapago','<=',$fechacorte.' 23:59:59'];
              $where6[] = ['ingresoextraordinario.fechapago','<=',$fechacorte.' 23:59:59'];
          }

          $saldos_cuentabanco = 0;
          $saldos_cuentabanco_bancos = [];
          foreach($bancos as $valuebancos){
            
              $saldos_capitalasignada_1 = DB::table('asignacioncapital')
                  ->where('asignacioncapital.idestadoeliminado',1)
                  ->whereIn('asignacioncapital.idtipooperacion',[1,4])
                  ->where('asignacioncapital.idbanco',$valuebancos->id)
                  ->where($where3)
                  ->sum('asignacioncapital.monto');
            
              $saldos_capitalasignada_2 = DB::table('asignacioncapital')
                  ->where('asignacioncapital.idestadoeliminado',1)
                  ->whereIn('asignacioncapital.idtipooperacion',[2])
                  ->where('asignacioncapital.idbanco',$valuebancos->id)
                  ->where($where3)
                  ->sum('asignacioncapital.monto'); 
            
              $cobranzas = DB::table('credito_cobranzacuota')
                  ->join('credito','credito.id','credito_cobranzacuota.idcredito')
                  ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
                  ->where('credito_cobranzacuota.idestadoextorno',0)
                  ->where('credito_cobranzacuota.idformapago',2)
                  ->where('credito_cobranzacuota.idbanco',$valuebancos->id)
                  ->where($where1)
                  ->sum('credito_cobranzacuota.total_recibido'); 
            
              $desembolsos = DB::table('credito_formapago')
                  ->join('credito','credito.id','credito_formapago.idcredito')
                  ->where('credito_formapago.idformapago',2)
                  ->where('credito.estado','DESEMBOLSADO')
                  ->where('credito_formapago.idbanco',$valuebancos->id)
                  ->where($where2)
                  ->sum('credito.monto_solicitado');
            
              $movimientointernodineros1 = DB::table('movimientointernodinero')
                  ->where('movimientointernodinero.idestadoeliminado',1)
                  ->where('movimientointernodinero.idfuenteretiro',7)
                  ->where('movimientointernodinero.idbanco',$valuebancos->id)
                  ->where($where4)
                  ->sum('movimientointernodinero.monto');
            
              $movimientointernodineros2 = DB::table('movimientointernodinero')
                  ->where('movimientointernodinero.idestadoeliminado',1)
                  ->where('movimientointernodinero.idfuenteretiro',9)
                  ->where('movimientointernodinero.idbanco',$valuebancos->id)
                  ->where($where4)
                  ->sum('movimientointernodinero.monto');
            
              $movimientointernodineros5 = DB::table('movimientointernodinero')
                  ->where('movimientointernodinero.idestadoeliminado',1)
                  ->where('movimientointernodinero.idfuenteretiro',10)
                  ->where('movimientointernodinero.idbanco',$valuebancos->id)
                  ->where($where4)
                  ->sum('movimientointernodinero.monto');
            
              $gastosadministrativosyoperativos_monto = DB::table('gastoadministrativooperativo')
                  ->where('gastoadministrativooperativo.idformapago',2) 
                  ->where('gastoadministrativooperativo.idestadoeliminado',1) 
                  ->where('gastoadministrativooperativo.idbanco',$valuebancos->id)
                  ->where($where5)
                  ->sum('gastoadministrativooperativo.monto');
            
              $ingresosextraordinarios_monto = DB::table('ingresoextraordinario')
                  ->where('ingresoextraordinario.idformapago',2) 
                  ->where('ingresoextraordinario.idestadoeliminado',1) 
                  ->where('ingresoextraordinario.idbanco',$valuebancos->id)
                  ->where($where6)
                  ->sum('ingresoextraordinario.monto');
            
              $saldos_cuentabanco += number_format($saldos_capitalasignada_1-
                                                   $saldos_capitalasignada_2+
                                                   $cobranzas-
                                                   $desembolsos-
                                                   $movimientointernodineros1+
                                                   $movimientointernodineros2-
                                                   $movimientointernodineros5-
                                                   $gastosadministrativosyoperativos_monto+
                                                   $ingresosextraordinarios_monto
                                                   , 2, '.', '');
              $saldos_cuentabanco_bancos[] = [
                  'banco_id' => $valuebancos->id,
                  'banco_nombre' => $valuebancos->nombre,
                  'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
                  'banco' => number_format($saldos_capitalasignada_1-
                                           $saldos_capitalasignada_2+
                                           $cobranzas-$desembolsos-
                                           $movimientointernodineros1+
                                           $movimientointernodineros2-
                                           $movimientointernodineros5-
                                           $gastosadministrativosyoperativos_monto+
                                           $ingresosextraordinarios_monto
                                           , 2, '.', ''),
              ];
          }
          
          $where = [];
          $where1 = [];
          $where2 = [];
          $where3 = [];
          $where4 = [];
          if($idagencia!=''){
              $where[] = ['asignacioncapital.idtienda',$idagencia];
              $where1[] = ['credito.idtienda',$idagencia];
              $where2[] = ['ingresoextraordinario.idtienda',$idagencia];
              $where3[] = ['credito.idtienda',$idagencia];
              $where4[] = ['gastoadministrativooperativo.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              $where[] = ['asignacioncapital.fecharegistro','<=',$fechacorte.' 23:59:59'];
              $where1[] = ['credito_cobranzacuota.fecharegistro','<=',$fechacorte.' 23:59:59'];
              $where2[] = ['ingresoextraordinario.fechapago','<=',$fechacorte.' 23:59:59'];
              $where3[] = ['credito_formapago.fechapago','<=',$fechacorte.' 23:59:59'];
              $where4[] = ['gastoadministrativooperativo.fechapago','<=',$fechacorte.' 23:59:59'];
          }
 
          $asignacioncapital_deposito_reserva = DB::table('asignacioncapital')
              ->where('asignacioncapital.idtipodestino',2)
              ->where('asignacioncapital.idestadoeliminado',1)
              ->whereIn('asignacioncapital.idtipooperacion',[1,4])
              ->where($where)
              ->sum('asignacioncapital.monto');
          $asignacioncapital_retiro_reserva = DB::table('asignacioncapital')
              ->where('asignacioncapital.idtipodestino',2)
              ->where('asignacioncapital.idestadoeliminado',1)
              ->where('asignacioncapital.idtipooperacion',2)
              ->where($where)
              ->sum('asignacioncapital.monto');
          $saldos_reserva = $asignacioncapital_deposito_reserva-
              $asignacioncapital_retiro_reserva-
              $ret_reservacf_caja+
              $ret_caja_reservacf+
              $ret_banco_reservacf;
       
          $asignacioncapital_deposito_caja = DB::table('asignacioncapital')
              ->where('asignacioncapital.idtipodestino',1)
              ->where('asignacioncapital.idestadoeliminado',1)
              ->whereIn('asignacioncapital.idtipooperacion',[1,4])
              ->where($where)
              ->sum('asignacioncapital.monto');
          $asignacioncapital_retiro_caja = DB::table('asignacioncapital')
              ->where('asignacioncapital.idtipodestino',1)
              ->where('asignacioncapital.idestadoeliminado',1)
              ->where('asignacioncapital.idtipooperacion',2)
              ->where($where)
              ->sum('asignacioncapital.monto');
          $ingresoyegresocaja_ingreso_crediticio_cps = DB::table('credito_cobranzacuota')
              ->join('credito','credito.id','credito_cobranzacuota.idcredito')
              ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
              ->where('credito_cobranzacuota.idestadoextorno',0)
              ->where('credito_cobranzacuota.idformapago',1)
              ->where($where1)
              ->select(
                  'credito_cobranzacuota.*',
              )
              ->get();
          $ingresoyegresocaja_ingreso_crediticio_saldofinal = 0;
          foreach($ingresoyegresocaja_ingreso_crediticio_cps as $value){
              $ingresoyegresocaja_ingreso_crediticio_saldofinal += $value->total_pagar_amortizacion+
                  $value->total_pagar_interes+
                  $value->total_pagar_comision+$value->total_pagar_cargo+
                  $value->total_pagar_tenencia+$value->total_pagar_penalidad+$value->total_pagar_compensatorio;
          }
          $ingresoyegresocaja_ingreso_incrementocapital_saldofinal = DB::table('asignacioncapital')
                ->where('asignacioncapital.idtipodestino',1)
                ->where('asignacioncapital.idestadoeliminado',1)
                ->where('asignacioncapital.idtipooperacion',1)
                ->where($where)
                ->sum('asignacioncapital.monto');
          $ingresoyegresocaja_ingreso_ingresosextraordinarios_saldofinal = DB::table('ingresoextraordinario')
                ->where('ingresoextraordinario.idformapago',1) 
                ->where('ingresoextraordinario.idestadoeliminado',1) 
                ->where($where2)
                ->sum('ingresoextraordinario.monto');
          $ingresoyegresocaja_egreso_crediticio_saldofinal = DB::table('credito_formapago')
              ->join('credito','credito.id','credito_formapago.idcredito')
              ->where('credito_formapago.idformapago',1)
              ->where('credito.estado','DESEMBOLSADO')
              ->where($where3)
              ->sum('credito.monto_solicitado');
          $ingresoyegresocaja_egreso_reduccioncapital_saldocapital = DB::table('asignacioncapital')
                ->whereIn('asignacioncapital.idtipodestino',[0,1])
                ->where('asignacioncapital.idestadoeliminado',1)
                ->where('asignacioncapital.idtipooperacion',2)
                ->where($where)
                ->sum('asignacioncapital.monto');
          $ingresoyegresocaja_egreso_gastosadministrativosyoperativos_saldocapital = DB::table('gastoadministrativooperativo')
                ->where('gastoadministrativooperativo.idformapago',1) 
                ->where('gastoadministrativooperativo.idestadoeliminado',1) 
                ->where($where4)
                ->sum('gastoadministrativooperativo.monto');
  
          $saldos_caja = $asignacioncapital_deposito_caja-
              $asignacioncapital_retiro_caja+
              $ret_reservacf_caja+
              $ret_banco_caja-
              $ret_caja_reservacf-
              $ret_caja_banco+
              $ingresoyegresocaja_ingreso_crediticio_saldofinal+
              $ingresoyegresocaja_ingreso_ahorro+
              $ingresoyegresocaja_ingreso_incrementocapital_saldofinal+
              $ingresoyegresocaja_ingreso_ingresosextraordinarios_saldofinal-
              $ingresoyegresocaja_egreso_crediticio_saldofinal-
              $ingresoyegresocaja_egreso_ahorro-
              $ingresoyegresocaja_egreso_reduccioncapital_saldocapital-
              $ingresoyegresocaja_egreso_gastosadministrativosyoperativos_saldocapital;
              /*+
              $dep_caja_reservacf+
              $dep_caja_banco-
              $dep_reservacf_caja-
              $dep_banco_caja;*/
          
          
          $where = [];
          if($idagencia!=''){
              $where[] = ['credito.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              $where[] = ['credito.fecha_desembolso','<=',$fechacorte];
          }
          
          $saldos_creditovigente_cnp = DB::table('credito')
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito.idestadocredito',1)
              ->where('credito.idforma_credito',2)
              ->where($where)
              ->sum('credito.saldo_pendientepago');
          $saldos_creditovigente_cp = DB::table('credito')
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito.idestadocredito',1)
              ->where('credito.idforma_credito',1)
              ->where($where)
              ->sum('credito.saldo_pendientepago');
          $saldos_creditovigente = $saldos_creditovigente_cnp+
                                    $saldos_creditovigente_cp;
          
          $saldos_interescreditovigentexcobrar_cnp = DB::table('credito_cronograma')
              ->join('credito','credito.id','credito_cronograma.idcredito')
              ->where('credito_cronograma.idestadocronograma_pago',0)
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito.idestadocredito',1)
              ->where('credito.idforma_credito',2)
              ->where($where)
              ->sum('credito_cronograma.interes');
          $saldos_interescreditovigentexcobrar_cp = DB::table('credito_cronograma')
              ->join('credito','credito.id','credito_cronograma.idcredito')
              ->where('credito_cronograma.idestadocronograma_pago',0)
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito.idestadocredito',1)
              ->where('credito.idforma_credito',1)
              ->where($where)
              ->sum('credito_cronograma.interes');
          $saldos_interescreditovigentexcobrar = $saldos_interescreditovigentexcobrar_cnp+
                                                  $saldos_interescreditovigentexcobrar_cp;
          $saldos_ahorros = 0;
          $saldos_interesgeneradosxpagar_ahorropf = 0;
          $saldos_interesgeneradosxpagar_interescuentaahorropdfprogramadas = 0;
          $saldos_interesgeneradosxpagar_ahorrocorriente = 0;
          $saldos_interesgeneradosxpagar_interescuentaahorrocgeneradas = 0;
          $saldos_interesgeneradosxpagar = $saldos_interesgeneradosxpagar_ahorropf+
                                            $saldos_interesgeneradosxpagar_interescuentaahorropdfprogramadas+
                                            $saldos_interesgeneradosxpagar_ahorrocorriente+
                                            $saldos_interesgeneradosxpagar_interescuentaahorrocgeneradas;
          
          $where = [];
          if($idagencia!=''){
              $where[] = ['asignacioncapital.idtienda',$idagencia];
          }
          if($fechacorte!=''){
              $where[] = ['asignacioncapital.fecharegistro','<=',$fechacorte.' 23:59:59'];
          }
          /*$ret_correc = DB::table('asignacioncapital')
              ->where('asignacioncapital.idestadoeliminado',1)
              ->where('asignacioncapital.idtipooperacion',3)
              ->where($where)
              ->sum('asignacioncapital.monto');
          $saldos_capitalasignada = $saldos_cuentabanco+$saldos_reserva+$saldos_caja-$ret_correc;*/
          
          $monto_suma = DB::table('asignacioncapital')
              ->where('asignacioncapital.idestadoeliminado',1)
              ->whereIn('asignacioncapital.idtipooperacion',[1,4])
              ->where('asignacioncapital.idresponsable_recfinal','<>',0)
              ->where('asignacioncapital.idtienda',$idagencia)
              ->where($where)
              ->sum('asignacioncapital.monto');

          $monto_resta = DB::table('asignacioncapital')
              ->where('asignacioncapital.idestadoeliminado',1)
              ->whereIn('asignacioncapital.idtipooperacion',[2,3])
              ->where('asignacioncapital.idresponsable_recfinal','<>',0)
              ->where('asignacioncapital.idtienda',$idagencia)
              ->where($where)
              ->sum('asignacioncapital.monto');
          $saldos_capitalasignada = $monto_suma-$monto_resta;
          
          $total_efectivo_ejercicio = $saldos_cuentabanco+$saldos_reserva+$saldos_caja+$saldos_creditovigente;
          $incremental_capital_asignado = $total_efectivo_ejercicio-$saldos_capitalasignada;
          
          $spread_financiero_proyectado = $saldos_interesgeneradosxpagar;
          $indicador_reserva_legal = ($saldos_cuentabanco+$saldos_caja);
  
          $validacion_operaciones_cuenta_banco = '';
          //dd($validacion_operaciones_cuenta_banco_cant);
          if($validacion_operaciones_cuenta_banco_cant==0 && 
              $validacion_cantidad==0){
              //dd($valid_habilitacion);
              if($valid_habilitacion>0){
                  $validacion_operaciones_cuenta_banco = 'PENDIENTE';
              }else{
                  $validacion_operaciones_cuenta_banco = 'SIN OPERACIONES';
              }
          }
          /*elseif(($validacion_operaciones_cuenta_banco_cant>0 && 
                 $validacion_operaciones_cuenta_banco_cant<$validacion_cantidad) ||
                ){*/
          elseif($validacion_operaciones_cuenta_banco_cant!=$validacion_cantidad){
              $validacion_operaciones_cuenta_banco = 'PENDIENTE';
          }
          elseif($validacion_operaciones_cuenta_banco_cant==$validacion_cantidad){
              $validacion_operaciones_cuenta_banco = 'VERIFICADO';
          }
  
          $efectivo_caja_corte = $saldos_caja;
          $efectivo_caja_arqueo = 0;
          $resultado = $efectivo_caja_arqueo-$efectivo_caja_corte;
          
          $data = [
              'tienda' => $tienda,
              'agencia' => $agencia,
              'bancos' => $bancos,
              'corte' => date("d-m-Y",strtotime(date($fechacorte))),
            
              'ingresoyegresocaja_ingreso_crediticio_cnp_capital' => number_format($ingresoyegresocaja_ingreso_crediticio_cnp_capital, 2, '.', ''),
              'ingresoyegresocaja_ingreso_crediticio_cnp_interes' => number_format($ingresoyegresocaja_ingreso_crediticio_cnp_interes, 2, '.', ''),
              'ingresoyegresocaja_ingreso_crediticio_cnp_desgravcargo' => number_format($ingresoyegresocaja_ingreso_crediticio_cnp_desgravcargo, 2, '.', ''),
              'ingresoyegresocaja_ingreso_crediticio_cnp_tenencxc' => number_format($ingresoyegresocaja_ingreso_crediticio_cnp_tenencxc, 2, '.', ''),
              'ingresoyegresocaja_ingreso_crediticio_cnp' => number_format($ingresoyegresocaja_ingreso_crediticio_cnp, 2, '.', ''),   
              'ingresoyegresocaja_ingreso_crediticio_cp_capital' => number_format($ingresoyegresocaja_ingreso_crediticio_cp_capital, 2, '.', ''),
              'ingresoyegresocaja_ingreso_crediticio_cp_interes' => number_format($ingresoyegresocaja_ingreso_crediticio_cp_interes, 2, '.', ''),
              'ingresoyegresocaja_ingreso_crediticio_cp_desgravcargo' => number_format($ingresoyegresocaja_ingreso_crediticio_cp_desgravcargo, 2, '.', ''),
              'ingresoyegresocaja_ingreso_crediticio_cp_tenencxc' => number_format($ingresoyegresocaja_ingreso_crediticio_cp_tenencxc, 2, '.', ''),
              'ingresoyegresocaja_ingreso_crediticio_cp' => number_format($ingresoyegresocaja_ingreso_crediticio_cp, 2, '.', ''),
              'ingresoyegresocaja_ingreso_ahorro_plazofijo' => number_format($ingresoyegresocaja_ingreso_ahorro_plazofijo, 2, '.', ''),
              'ingresoyegresocaja_ingreso_ahorro_ahorroc' => number_format($ingresoyegresocaja_ingreso_ahorro_ahorroc, 2, '.', ''),
              'ingresoyegresocaja_ingreso_ahorro' => number_format($ingresoyegresocaja_ingreso_ahorro, 2, '.', ''),
              'ingresoyegresocaja_ingreso_incrementocapital' => number_format($ingresoyegresocaja_ingreso_incrementocapital, 2, '.', ''),
              'ingresoyegresocaja_ingreso_ingresosextraordinarios' => number_format($ingresoyegresocaja_ingreso_ingresosextraordinarios, 2, '.', ''),
              'ingresoyegresocaja_ingreso_crediticio' => number_format($ingresoyegresocaja_ingreso_crediticio, 2, '.', ''),
              'ingresoyegresocaja_ingreso_crediticio_transitorio' => number_format($ingresoyegresocaja_ingreso_crediticio_transitorio, 2, '.', ''),
            
              'ingresoyegresocaja_egreso_crediticio' => number_format($ingresoyegresocaja_egreso_crediticio, 2, '.', ''),
              'ingresoyegresocaja_egreso_ahorro_plazofijo' => number_format($ingresoyegresocaja_egreso_ahorro_plazofijo, 2, '.', ''),
              'ingresoyegresocaja_egreso_ahorro_intplazofijo' => number_format($ingresoyegresocaja_egreso_ahorro_intplazofijo, 2, '.', ''),
              'ingresoyegresocaja_egreso_ahorro_ahorrocte' => number_format($ingresoyegresocaja_egreso_ahorro_ahorrocte, 2, '.', ''),
              'ingresoyegresocaja_egreso_ahorro_intcte' => number_format($ingresoyegresocaja_egreso_ahorro_intcte, 2, '.', ''),
              'ingresoyegresocaja_egreso_ahorro' => number_format($ingresoyegresocaja_egreso_ahorro, 2, '.', ''),
              'ingresoyegresocaja_egreso_reduccioncapital' => number_format($ingresoyegresocaja_egreso_reduccioncapital, 2, '.', ''),
              'ingresoyegresocaja_egreso_gastosadministrativosyoperativos' => number_format($ingresoyegresocaja_egreso_gastosadministrativosyoperativos, 2, '.', ''),

              'ingresoyegresobanco_ingreso_crediticio_cnpcp' => number_format($ingresoyegresobanco_ingreso_crediticio_cnpcp, 2, '.', ''),
              'ingresoyegresobanco_ingreso_crediticio_cnpcps_bancos' => $ingresoyegresobanco_ingreso_crediticio_cnpcps_bancos,
              'ingresoyegresobanco_ingreso_crediticio_cnpcps_validacion' => $ingresoyegresobanco_ingreso_crediticio_cnpcps_validacion,
              'ingresoyegresobanco_ingreso_crediticio_cnpcps_validacion_cantidad' => $ingresoyegresobanco_ingreso_crediticio_cnpcps_validacion_cantidad,
              'ingresoyegresobanco_ingreso_incrementocapital' => number_format($ingresoyegresobanco_ingreso_incrementocapital, 2, '.', ''),
              'ingresoyegresobanco_ingreso_incrementocapital_bancos' => $ingresoyegresobanco_ingreso_incrementocapital_bancos,
              'ingresoyegresobanco_ingreso_incrementocapital_validacion' => $ingresoyegresobanco_ingreso_incrementocapital_validacion,
              'ingresoyegresobanco_ingreso_incrementocapital_validacion_cantidad' => $ingresoyegresobanco_ingreso_incrementocapital_validacion_cantidad,
              'ingresoyegresobanco_ingreso_ingresosextraordinarios' => number_format($ingresoyegresobanco_ingreso_ingresosextraordinarios, 2, '.', ''),
              'ingresoyegresobanco_ingreso_ingresosextraordinarios_bancos' => $ingresoyegresobanco_ingreso_ingresosextraordinarios_bancos,
              'ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion' => $ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion,
              'ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion_cantidad' => $ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion_cantidad,

            
              'ingresoyegresobanco_egreso_crediticio' => number_format($ingresoyegresobanco_egreso_crediticio, 2, '.', ''),
              'ingresoyegresobanco_egreso_crediticio_bancos' => $ingresoyegresobanco_egreso_crediticio_bancos,
              'ingresoyegresobanco_egreso_crediticio_validacion' => $ingresoyegresobanco_egreso_crediticio_validacion,
              'ingresoyegresobanco_egreso_crediticio_validacion_cantidad' => $ingresoyegresobanco_egreso_crediticio_validacion_cantidad,
              'ingresoyegresobanco_egreso_reduccioncapital' => number_format($ingresoyegresobanco_egreso_reduccioncapital, 2, '.', ''),
              'ingresoyegresobanco_egreso_reduccioncapital_bancos' => $ingresoyegresobanco_egreso_reduccioncapital_bancos,
              'ingresoyegresobanco_egreso_reduccioncapital_validacion' => $ingresoyegresobanco_egreso_reduccioncapital_validacion,
              'ingresoyegresobanco_egreso_reduccioncapital_validacion_cantidad' => $ingresoyegresobanco_egreso_reduccioncapital_validacion_cantidad,
              'ingresoyegresobanco_egreso_gastosadministrativosyoperativos' => number_format($ingresoyegresobanco_egreso_gastosadministrativosyoperativos, 2, '.', ''),
              'ingresoyegresobanco_egreso_gastosadministrativosyoperativos_bancos' => $ingresoyegresobanco_egreso_gastosadministrativosyoperativos_bancos,
              'ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion' => $ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion,
              'ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion_cantidad' => $ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion_cantidad,
            
              'ret_reservacf_caja' => number_format($ret_reservacf_caja, 2, '.', ''),
              'ret_banco_caja' => number_format($ret_banco_caja, 2, '.', ''),
              'ret_banco_caja_bancos' => $ret_banco_caja_bancos,
              'ret_caja_reservacf' => number_format($ret_caja_reservacf, 2, '.', ''),
              'ret_caja_banco' => number_format($ret_caja_banco, 2, '.', ''),
              'ret_caja_banco_bancos' => $ret_caja_banco_bancos,
              'ret_banco_reservacf' => number_format($ret_banco_reservacf, 2, '.', ''),
              'ret_banco_reservacf_bancos' => $ret_banco_reservacf_bancos,
              'ret_reservacf_caja_total' => number_format($ret_reservacf_caja_total, 2, '.', ''),
              'ret_caja_reservacf_total' => number_format($ret_caja_reservacf_total, 2, '.', ''),

              'dep_caja_reservacf' => number_format($dep_caja_reservacf, 2, '.', ''),
              'dep_caja_banco' => number_format($dep_caja_banco, 2, '.', ''),
              'dep_caja_banco_bancos' => $dep_caja_banco_bancos,
              'dep_reservacf_caja' => number_format($dep_reservacf_caja, 2, '.', ''),
              'dep_banco_caja' => number_format($dep_banco_caja, 2, '.', ''),
              'dep_banco_caja_bancos' => $dep_banco_caja_bancos,
              'dep_reservacf_banco' => number_format($dep_reservacf_banco, 2, '.', ''),
              'dep_reservacf_banco_bancos' => $dep_reservacf_banco_bancos,
              'dep_caja_reservacf_total' => number_format($dep_caja_reservacf_total, 2, '.', ''),
              'dep_reservacf_caja_total' => number_format($dep_reservacf_caja_total, 2, '.', ''),
            
              'habilitacion_gestion_liquidez1' => number_format($habilitacion_gestion_liquidez1, 2, '.', ''),
              'habilitacion_gestion_liquidez2' => number_format($habilitacion_gestion_liquidez2, 2, '.', ''),
              'cierre_caja_apertura' => number_format($cierre_caja_apertura, 2, '.', ''),
     
              'saldos_capitalasignada' => number_format($saldos_capitalasignada, 2, '.', ''),
              'saldos_cuentabanco' => number_format($saldos_cuentabanco, 2, '.', ''),
              'saldos_cuentabanco_bancos' => $saldos_cuentabanco_bancos,
              'saldos_reserva' => number_format($saldos_reserva, 2, '.', ''),
              'saldos_caja' => number_format($saldos_caja, 2, '.', ''),
              'arqueo_caja' => number_format($arqueo_caja, 2, '.', ''),
              'saldos_creditovigente_cnp' => number_format($saldos_creditovigente_cnp, 2, '.', ''),
              'saldos_creditovigente_cp' => number_format($saldos_creditovigente_cp, 2, '.', ''),
              'saldos_creditovigente' => number_format($saldos_creditovigente, 2, '.', ''),
              'saldos_interescreditovigentexcobrar_cnp' => number_format($saldos_interescreditovigentexcobrar_cnp, 2, '.', ''),
              'saldos_interescreditovigentexcobrar_cp' => number_format($saldos_interescreditovigentexcobrar_cp, 2, '.', ''),
              'saldos_interescreditovigentexcobrar' => number_format($saldos_interescreditovigentexcobrar, 2, '.', ''),
              'saldos_ahorros' => number_format($saldos_ahorros, 2, '.', ''),
              'saldos_interesgeneradosxpagar_ahorropf' => number_format($saldos_interesgeneradosxpagar_ahorropf, 2, '.', ''),
              'saldos_interesgeneradosxpagar_interescuentaahorropdfprogramadas' => number_format($saldos_interesgeneradosxpagar_interescuentaahorropdfprogramadas, 2, '.', ''),
              'saldos_interesgeneradosxpagar_ahorrocorriente' => number_format($saldos_interesgeneradosxpagar_ahorrocorriente, 2, '.', ''),
              'saldos_interesgeneradosxpagar_interescuentaahorrocgeneradas' => number_format($saldos_interesgeneradosxpagar_interescuentaahorrocgeneradas, 2, '.', ''),
              'saldos_interesgeneradosxpagar' => number_format($saldos_interesgeneradosxpagar, 2, '.', ''),

              'total_efectivo_ejercicio' => number_format($total_efectivo_ejercicio, 2, '.', ''),
              'incremental_capital_asignado' => number_format($incremental_capital_asignado, 2, '.', ''),
            
              'spread_financiero_proyectado' => number_format($spread_financiero_proyectado, 2, '.', ''),
              'indicador_reserva_legal' => number_format($indicador_reserva_legal, 2, '.', ''),
            
              'validacion_operaciones_cuenta_banco' => $validacion_operaciones_cuenta_banco,
              'efectivo_caja_corte' => number_format($efectivo_caja_corte, 2, '.', ''),
              'efectivo_caja_arqueo' => number_format($efectivo_caja_arqueo, 2, '.', ''),
              'resultado' => number_format($resultado, 2, '.', ''),
   
          ];
    return $data;
}
function cvconsolidadooperaciones($tienda,$idagencia,$fechacorte){
    $agencia = DB::table('tienda')->whereId($idagencia)->first();
    $bancos = DB::table('banco')->where('estado','ACTIVO')->get();

    // Ingreso y Egreso por Caja
    $where = [];
    if($idagencia!=''){
        $where[] = ['credito.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['credito_cobranzacuota.fecharegistro','>=',$fechacorte.' 00:00:00'];
        $where[] = ['credito_cobranzacuota.fecharegistro','<=',$fechacorte.' 23:59:59'];
    }
    $ingresoyegresocaja_ingreso_crediticio_cnps = DB::table('credito_cobranzacuota')
        ->join('credito','credito.id','credito_cobranzacuota.idcredito')
        ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
        ->where('credito_cobranzacuota.idestadoextorno',0)
        ->where('credito.idforma_credito',2)
        ->where('credito_cobranzacuota.idformapago',1)
        ->where($where)
        ->select(
            'credito_cobranzacuota.*',
        )
        ->orderBy('credito_cobranzacuota.id','asc')
        ->get();
    
    $ingresoyegresocaja_ingreso_crediticio_cnp_capital = 0;
    $ingresoyegresocaja_ingreso_crediticio_cnp_interes = 0;
    $ingresoyegresocaja_ingreso_crediticio_cnp_desgravcargo = 0;
    $ingresoyegresocaja_ingreso_crediticio_cnp_tenencxc = 0;
    $ingresoyegresocaja_ingreso_crediticio_cnp = 0;
    $ingresoyegresocaja_ingreso_crediticio = 0;
    
    foreach($ingresoyegresocaja_ingreso_crediticio_cnps as $value){
        $ingresoyegresocaja_ingreso_crediticio_cnp_capital += $value->total_pagar_amortizacion;
        $ingresoyegresocaja_ingreso_crediticio_cnp_interes += $value->total_pagar_interes;
        $ingresoyegresocaja_ingreso_crediticio_cnp_desgravcargo += $value->total_pagar_comision+$value->total_pagar_cargo;
        $ingresoyegresocaja_ingreso_crediticio_cnp_tenencxc += $value->total_pagar_tenencia+$value->total_pagar_penalidad+$value->total_pagar_compensatorio;
    }
    
    $ingresoyegresocaja_ingreso_crediticio_cnp = $ingresoyegresocaja_ingreso_crediticio_cnp_capital+
    $ingresoyegresocaja_ingreso_crediticio_cnp_interes+
    $ingresoyegresocaja_ingreso_crediticio_cnp_desgravcargo+
    $ingresoyegresocaja_ingreso_crediticio_cnp_tenencxc;
    
    
    $where = [];
    if($idagencia!=''){
        $where[] = ['credito.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['credito_cobranzacuota.fecharegistro','>=',$fechacorte.' 00:00:00'];
        $where[] = ['credito_cobranzacuota.fecharegistro','<=',$fechacorte.' 23:59:59'];
    }
    
    $ingresoyegresocaja_ingreso_crediticio_cps = 0;
    /* DB::table('credito_cobranzacuota')
        ->join('credito','credito.id','credito_cobranzacuota.idcredito')
        ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
        ->where('credito_cobranzacuota.idestadoextorno',0)
        ->where('credito.idforma_credito',1)
        ->where('credito_cobranzacuota.idformapago',1)
        ->where($where)
        ->select(
            'credito_cobranzacuota.*',
        )
        ->orderBy('credito_cobranzacuota.id','asc')
        ->get(); */
    
    $ingresoyegresocaja_ingreso_crediticio_cp_capital = 0;
    $ingresoyegresocaja_ingreso_crediticio_cp_interes = 0;
    $ingresoyegresocaja_ingreso_crediticio_cp_desgravcargo = 0;
    $ingresoyegresocaja_ingreso_crediticio_cp_tenencxc = 0;
    $ingresoyegresocaja_ingreso_crediticio_cp = 0;
    
    /* foreach($ingresoyegresocaja_ingreso_crediticio_cps as $value){
        $ingresoyegresocaja_ingreso_crediticio_cp_capital += $value->total_pagar_amortizacion;
        $ingresoyegresocaja_ingreso_crediticio_cp_interes += $value->total_pagar_interes;
        $ingresoyegresocaja_ingreso_crediticio_cp_desgravcargo += $value->total_pagar_comision+$value->total_pagar_cargo;
        $ingresoyegresocaja_ingreso_crediticio_cp_tenencxc += $value->total_pagar_tenencia+$value->total_pagar_penalidad+$value->total_pagar_compensatorio;
    } */
    
    $ingresoyegresocaja_ingreso_crediticio_cp = $ingresoyegresocaja_ingreso_crediticio_cp_capital+
                                        $ingresoyegresocaja_ingreso_crediticio_cp_interes+
                                        $ingresoyegresocaja_ingreso_crediticio_cp_desgravcargo+
                                        $ingresoyegresocaja_ingreso_crediticio_cp_tenencxc;
    
    $ingresoyegresocaja_ingreso_crediticio = $ingresoyegresocaja_ingreso_crediticio_cnp+
                                            $ingresoyegresocaja_ingreso_crediticio_cp;
    
    $where = [];
    if($idagencia!=''){
        $where[] = ['credito.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        //$where[] = ['credito_formapago.fechapago','>=',$fechacorte.' 00:00:00'];
        $where[] = ['credito_formapago.fechapago','<=',$fechacorte.' 23:59:59'];
    }
    $ingresoyegresocaja_ingreso_crediticio_transitorio = 0;
    /*DB::table('credito_formapago')
        ->join('credito','credito.id','credito_formapago.idcredito')
        ->where('credito.estado','DESEMBOLSADO')
        ->where('credito.idestadorefinanciamiento',1)
        ->where($where)
        ->sum('credito.monto_solicitado');*/

    $where = [];
    if($idagencia!=''){
        $where[] = ['cvventa.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['cvventa.fecharegistro','<=',$fechacorte.' 23:59:59'];
    }
    $ingresoyegresocaja_ingreso_cvventa = DB::table('cvventa')
        ->where('cvventa.idestadoeliminado',1)
        ->where('cvventa.venta_idformapago', 1) // caja
        ->where($where)
        ->sum('cvventa.venta_montoventa');
    $ingresoyegresocaja_ingreso_cvventa_valorcompra = DB::table('cvventa')
        ->join('cvcompra', 'cvcompra.id', 'cvventa.idcvcompra')
        ->where('cvventa.idestadoeliminado',1)
        ->where('cvventa.venta_idformapago', 1) // caja
        ->where($where)
        ->sum('cvcompra.valorcompra');

    $where = [];
    if($idagencia!=''){
        $where[] = ['cvcompra.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['cvcompra.fecharegistro','<=',$fechacorte.' 23:59:59'];
    }
    $ingresoyegresocaja_egreso_cvcompra = DB::table('cvcompra')
        ->where('cvcompra.idestadoeliminado',1)
        ->where('cvcompra.compra_idformapago', 1) // caja
        ->where($where)
        ->sum('cvcompra.valorcompra');
    
    $ingresoyegresocaja_ingreso_ahorro_plazofijo = 0;
    $ingresoyegresocaja_ingreso_ahorro_ahorroc = 0;
    
    $ingresoyegresocaja_ingreso_ahorro = $ingresoyegresocaja_ingreso_ahorro_plazofijo+
                                        $ingresoyegresocaja_ingreso_ahorro_ahorroc;
    
    $where = [];
    if($idagencia!=''){
        $where[] = ['cvasignacioncapital.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['cvasignacioncapital.fecharegistro','>=',$fechacorte.' 00:00:00'];
        $where[] = ['cvasignacioncapital.fecharegistro','<=',$fechacorte.' 23:59:59'];
    }
    $ingresoyegresocaja_ingreso_incrementocapital = DB::table('cvasignacioncapital')
        ->where('cvasignacioncapital.idtipodestino',1)
        ->where('cvasignacioncapital.idestadoeliminado',1)
        ->where('cvasignacioncapital.idresponsable_recfinal', '<>', 0)
        ->where('cvasignacioncapital.idtipooperacion',1)
        ->where($where)
        ->sum('cvasignacioncapital.monto');

    $where = [];
    if($idagencia!=''){
        $where[] = ['cvingresoextraordinario.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['cvingresoextraordinario.fechapago','>=',$fechacorte.' 00:00:00'];
        $where[] = ['cvingresoextraordinario.fechapago','<=',$fechacorte.' 23:59:59'];
    }
    $ingresoyegresocaja_ingreso_ingresosextraordinarios = DB::table('cvingresoextraordinario')
        ->where('cvingresoextraordinario.idformapago',1) 
        ->where('cvingresoextraordinario.idestadoeliminado',1) 
        ->where($where)
        ->sum('cvingresoextraordinario.monto');
    
    //-------
    
    $where = [];
    if($idagencia!=''){
        $where[] = ['credito.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['credito_formapago.fechapago','>=',$fechacorte.' 00:00:00'];
        $where[] = ['credito_formapago.fechapago','<=',$fechacorte.' 23:59:59'];
    }
    $ingresoyegresocaja_egreso_crediticio = DB::table('credito_formapago')
        ->join('credito','credito.id','credito_formapago.idcredito')
        ->where('credito_formapago.idformapago',1)
        ->where('credito.estado','DESEMBOLSADO')
        ->where($where)
        ->sum('credito.monto_solicitado');
    
    $ingresoyegresocaja_egreso_ahorro_plazofijo = 0;
    $ingresoyegresocaja_egreso_ahorro_intplazofijo = 0;
    $ingresoyegresocaja_egreso_ahorro_ahorrocte = 0;
    $ingresoyegresocaja_egreso_ahorro_intcte = 0;
    
    $ingresoyegresocaja_egreso_ahorro = $ingresoyegresocaja_egreso_ahorro_plazofijo+
                                        $ingresoyegresocaja_egreso_ahorro_intplazofijo+
                                        $ingresoyegresocaja_egreso_ahorro_ahorrocte+
                                        $ingresoyegresocaja_egreso_ahorro_intcte;
    
    $where = [];
    if($idagencia!=''){
        $where[] = ['cvasignacioncapital.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['cvasignacioncapital.fecharegistro','>=',$fechacorte.' 00:00:00'];
        $where[] = ['cvasignacioncapital.fecharegistro','<=',$fechacorte.' 23:59:59'];
    }
    $ingresoyegresocaja_egreso_reduccioncapital = DB::table('cvasignacioncapital')
        ->whereIn('cvasignacioncapital.idtipodestino',[0,1])
        ->where('cvasignacioncapital.idestadoeliminado',1)
        ->where('cvasignacioncapital.idresponsable_recfinal', '<>', 0)
        ->where('cvasignacioncapital.idtipooperacion',2)
        ->where($where)
        ->sum('cvasignacioncapital.monto');
    
    $where = [];
    if($idagencia!=''){
        $where[] = ['cvgastoadministrativooperativo.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['cvgastoadministrativooperativo.fechapago','>=',$fechacorte.' 00:00:00'];
        $where[] = ['cvgastoadministrativooperativo.fechapago','<=',$fechacorte.' 23:59:59'];
    }
    $ingresoyegresocaja_egreso_gastosadministrativosyoperativos = DB::table('cvgastoadministrativooperativo')
        ->where('cvgastoadministrativooperativo.idformapago',1) 
        ->where('cvgastoadministrativooperativo.idestadoeliminado',1) 
        ->where($where)
        ->sum('cvgastoadministrativooperativo.monto');
    
    // Ingreso y Egreso por Cuenta Banco
    $validacion_operaciones_cuenta_banco_cant = 0;

    $where = [];
    if($idagencia!=''){
        $where[] = ['credito.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['credito_cobranzacuota.fecharegistro','>=',$fechacorte.' 00:00:00'];
        $where[] = ['credito_cobranzacuota.fecharegistro','<=',$fechacorte.' 23:59:59'];
    }
    $ingresoyegresobanco_ingreso_crediticio_cnpcp = 0;
    $ingresoyegresobanco_ingreso_crediticio_cnpcps_bancos = [];
    $ingresoyegresobanco_ingreso_crediticio_cnpcps_validacion = '';
    $validacion_0 = '';
    $validacion_cantidad = 0;
    $ingresoyegresobanco_ingreso_crediticio_cnpcps_validacion_cantidad = 0;
    foreach($bancos as $valuebancos){
        $bancosdatas = DB::table('credito_cobranzacuota')
            ->join('credito','credito.id','credito_cobranzacuota.idcredito')
            ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
            ->where('credito_cobranzacuota.idestadoextorno',0)
            ->where('credito_cobranzacuota.idformapago',2)
            ->where('credito_cobranzacuota.idbanco',$valuebancos->id)
            ->where($where)
            ->select(
                'credito_cobranzacuota.*',
            )
            ->orderBy('credito_cobranzacuota.id','asc')
            ->get();  
    
        $banco_capital = 0;
        $banco_interes = 0;
        $banco_desgravcargo = 0;
        $banco_tenencxc = 0;
        $banco = 0;
        $validacion_1 = '';
        foreach($bancosdatas as $value){
            if($value->validar_estado==1 && $validacion_1 == ''){
                $validacion_1 = 'CHECK';
            }
            $banco_capital += $value->total_pagar_amortizacion;
            $banco_interes += $value->total_pagar_interes;
            $banco_desgravcargo += $value->total_pagar_comision+$value->total_pagar_cargo;
            $banco_tenencxc += $value->total_pagar_tenencia+$value->total_pagar_penalidad+$value->total_pagar_compensatorio;
        }
            
        if($validacion_1=='CHECK' && $validacion_0 == ''){
            $validacion_0 = 'CHECK';  
            $validacion_operaciones_cuenta_banco_cant += 1;
        }
    
        if(count($bancosdatas)>0){
            $validacion_cantidad += 1;
            $ingresoyegresobanco_ingreso_crediticio_cnpcps_validacion_cantidad += 1;
        }
    
        $ingresoyegresobanco_ingreso_crediticio_cnpcp += number_format($banco_capital+$banco_interes+$banco_desgravcargo+$banco_tenencxc, 2, '.', '');
    
        $ingresoyegresobanco_ingreso_crediticio_cnpcps_bancos[] = [
            'banco_nombre' => $valuebancos->nombre,
            'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
            'banco_capital' => number_format($banco_capital, 2, '.', ''),
            'banco_interes' => number_format($banco_interes, 2, '.', ''),
            'banco_desgravcargo' => number_format($banco_desgravcargo, 2, '.', ''),
            'banco_tenencxc' => number_format($banco_tenencxc, 2, '.', ''),
            'banco' => number_format($banco_capital+$banco_interes+$banco_desgravcargo+$banco_tenencxc, 2, '.', ''),
            'validacion' => $validacion_1
        ];
    }
    $ingresoyegresobanco_ingreso_crediticio_cnpcps_validacion = $validacion_0;

    // ======== VENTA y COMPRA en BANCO
    $where = [];
    if($idagencia!=''){
        $where[] = ['cvventa.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['cvventa.fecharegistro','<=',$fechacorte.' 23:59:59'];
    }
    $ingresoyegresobanco_ingreso_cvventa = DB::table('cvventa')
        ->where('cvventa.idestadoeliminado',1)
        ->where('cvventa.venta_idformapago', 2) // banco
        ->where($where)
        ->sum('cvventa.venta_montoventa');

    $ingresoyegresobanco_ingreso_cvventas = [];
    foreach($bancos as $valuebancos){
        $db_cvventa = DB::table('cvventa')
            ->where('cvventa.idestadoeliminado',1)
            ->where('cvventa.venta_idformapago', 2) // banco
            ->where($where)
            ->where('cvventa.venta_idbanco',$valuebancos->id)
            ->get();
    
        $compra_ingresoyegresobanco_egreso_cvventa = 0;
        foreach($db_cvventa as $valueventa){
            $compra_ingresoyegresobanco_egreso_cvventa += $valueventa->venta_montoventa;
        }
        $ingresoyegresobanco_ingreso_cvventas[] = [
            'banco_nombre' => $valuebancos->nombre,
            'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
            'banco' => number_format($compra_ingresoyegresobanco_egreso_cvventa, 2, '.', ''),
            'validacion' => $validacion_1,
        ];
    }

    $where = [];
    if($idagencia!=''){
        $where[] = ['cvcompra.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['cvcompra.fecharegistro','<=',$fechacorte.' 23:59:59'];
    }
    $ingresoyegresobanco_egreso_cvcompra = DB::table('cvcompra')
        ->where('cvcompra.idestadoeliminado',1)
        ->where('cvcompra.compra_idformapago', 2) // banco
        ->where($where)
        ->sum('cvcompra.valorcompra');

    // $ingresoyegresobanco_egreso_crediticio = 0;
    $ingresoyegresobanco_egreso_cvcompras = [];
    // $ingresoyegresobanco_egreso_crediticio_validacion = '';
    // $validacion_0 = '';
    // $ingresoyegresobanco_egreso_crediticio_validacion_cantidad = 0;

    foreach($bancos as $valuebancos){
        $db_cvcompra = DB::table('cvcompra')
            ->where('cvcompra.idestadoeliminado',1)
            ->where('cvcompra.compra_idformapago', 2) // banco
            ->where($where)
            ->where('cvcompra.compra_idbanco',$valuebancos->id)
            ->get();
    
        // $validacion_1 = '';
        $compra_ingresoyegresobanco_egreso_cvcompra = 0;
        foreach($db_cvcompra as $valuecompra){
            // if($valuecompra->validar_estado==1 && $validacion_1 == ''){
            //     $validacion_1 = 'CHECK';
            // }
            $compra_ingresoyegresobanco_egreso_cvcompra += $valuecompra->valorcompra;
        }
            
        // if($validacion_1=='CHECK' && $validacion_0 == ''){
        //     $validacion_0 = 'CHECK';
        //     $validacion_operaciones_cuenta_banco_cant += 1;
        // }
    
        // if(count($db_cvcompra)>0){
        //     $validacion_cantidad += 1;
        //     $ingresoyegresobanco_egreso_crediticio_validacion_cantidad += 1;
        // }
    
        // $ingresoyegresobanco_egreso_crediticio += number_format($compra_ingresoyegresobanco_egreso_cvcompra, 2, '.', '');
        
        $ingresoyegresobanco_egreso_cvcompras[] = [
            'banco_nombre' => $valuebancos->nombre,
            'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
            'banco' => number_format($compra_ingresoyegresobanco_egreso_cvcompra, 2, '.', ''),
            'validacion' => $validacion_1,
        ];
    }
    // $ingresoyegresobanco_egreso_crediticio_validacion = $validacion_0;
    // === FIN VENTA
    
    $where = [];
    if($idagencia!=''){
        $where[] = ['cvasignacioncapital.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['cvasignacioncapital.fecharegistro','>=',$fechacorte.' 00:00:00'];
        $where[] = ['cvasignacioncapital.fecharegistro','<=',$fechacorte.' 23:59:59'];
    }
    $ingresoyegresobanco_ingreso_incrementocapital = 0;
    $ingresoyegresobanco_ingreso_incrementocapital_bancos = [];
    $ingresoyegresobanco_ingreso_incrementocapital_validacion = '';
    $validacion_0 = '';
    $ingresoyegresobanco_ingreso_incrementocapital_validacion_cantidad = 0;
    foreach($bancos as $valuebancos){
    
        $db_ingresoyegresobanco_ingreso_incrementocapital = DB::table('cvasignacioncapital')
            ->where('cvasignacioncapital.idtipodestino',3)
            ->where('cvasignacioncapital.idresponsable_recfinal', '<>', 0)
            ->where('cvasignacioncapital.idestadoeliminado',1)
            ->where('cvasignacioncapital.idtipooperacion',1)
            ->where('cvasignacioncapital.idbanco',$valuebancos->id)
            ->where($where)
            ->get();
    
        $validacion_1 = '';
        $ingresoyegresobanco_ingreso_incrementocapital_monto = 0;
        foreach($db_ingresoyegresobanco_ingreso_incrementocapital as $valuecrediticio){
            if($valuecrediticio->validar_estado==1 && $validacion_1 == ''){
                $validacion_1 = 'CHECK';
            }
            $ingresoyegresobanco_ingreso_incrementocapital_monto += $valuecrediticio->monto;
        }
            
        if($validacion_1=='CHECK' && $validacion_0 == ''){
            $validacion_0 = 'CHECK';
            $validacion_operaciones_cuenta_banco_cant += 1;
        }
    
        if(count($db_ingresoyegresobanco_ingreso_incrementocapital)>0){
            $validacion_cantidad += 1;
            $ingresoyegresobanco_ingreso_incrementocapital_validacion_cantidad += 1;
        }
    
        $ingresoyegresobanco_ingreso_incrementocapital += number_format($ingresoyegresobanco_ingreso_incrementocapital_monto, 2, '.', '');
    
        $ingresoyegresobanco_ingreso_incrementocapital_bancos[] = [
            'banco_nombre' => $valuebancos->nombre,
            'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
            'banco' => number_format($ingresoyegresobanco_ingreso_incrementocapital_monto, 2, '.', ''),
            'validacion' => $validacion_1
        ];
    }
    $ingresoyegresobanco_ingreso_incrementocapital_validacion = $validacion_0;
    
    $where = [];
    if($idagencia!=''){
        $where[] = ['cvingresoextraordinario.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['cvingresoextraordinario.fechapago','>=',$fechacorte.' 00:00:00'];
        $where[] = ['cvingresoextraordinario.fechapago','<=',$fechacorte.' 23:59:59'];
    }
    $ingresoyegresobanco_ingreso_ingresosextraordinarios = 0;
    $ingresoyegresobanco_ingreso_ingresosextraordinarios_bancos = [];
    $ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion = '';
    $validacion_0 = '';
    $ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion_cantidad = 0;
    foreach($bancos as $valuebancos){
    
        $db_ingresoyegresobanco_ingreso_ingresosextraordinarios = DB::table('cvingresoextraordinario')
            ->where('cvingresoextraordinario.idformapago',2) 
            ->where('cvingresoextraordinario.idestadoeliminado',1) 
            ->where('cvingresoextraordinario.idbanco',$valuebancos->id)
            ->where($where)
            ->get();
    
        $validacion_1 = '';
        $ingresoyegresobanco_ingreso_ingresosextraordinarios_monto = 0;
        foreach($db_ingresoyegresobanco_ingreso_ingresosextraordinarios as $valuecrediticio){
            if($valuecrediticio->validar_estado==1 && $validacion_1 == ''){
                $validacion_1 = 'CHECK';
            }
            $ingresoyegresobanco_ingreso_ingresosextraordinarios_monto += $valuecrediticio->monto;
        }
            
        if($validacion_1=='CHECK' && $validacion_0 == ''){
            $validacion_0 = 'CHECK';
            $validacion_operaciones_cuenta_banco_cant += 1;
        }
    
        if(count($db_ingresoyegresobanco_ingreso_ingresosextraordinarios)>0){
            $validacion_cantidad += 1;
            $ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion_cantidad += 1;
        }
    
        $ingresoyegresobanco_ingreso_ingresosextraordinarios += number_format($ingresoyegresobanco_ingreso_ingresosextraordinarios_monto, 2, '.', '');
    
        $ingresoyegresobanco_ingreso_ingresosextraordinarios_bancos[] = [
            'banco_nombre' => $valuebancos->nombre,
            'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
            'banco' => number_format($ingresoyegresobanco_ingreso_ingresosextraordinarios_monto, 2, '.', ''),
            'validacion' => $validacion_1
        ];
    }
    $ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion = $validacion_0;
    
    //-------
    
    $where = [];
    if($idagencia!=''){
        $where[] = ['credito.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['credito_formapago.fechapago','>=',$fechacorte.' 00:00:00'];
        $where[] = ['credito_formapago.fechapago','<=',$fechacorte.' 23:59:59'];
    }
    $ingresoyegresobanco_egreso_crediticio = 0;
    $ingresoyegresobanco_egreso_crediticio_bancos = [];
    $ingresoyegresobanco_egreso_crediticio_validacion = '';
    $validacion_0 = '';
    $ingresoyegresobanco_egreso_crediticio_validacion_cantidad = 0;
    foreach($bancos as $valuebancos){
    
        $db_desembolsos_ingresoyegresobanco_egreso_crediticio = DB::table('credito_formapago')
            ->join('credito','credito.id','credito_formapago.idcredito')
            ->where('credito_formapago.idformapago',2)
            ->where('credito.estado','DESEMBOLSADO')
            ->where('credito_formapago.idbanco',$valuebancos->id)
            ->where($where)
            ->get();
    
        $validacion_1 = '';
        $desembolsos_ingresoyegresobanco_egreso_crediticio = 0;
        foreach($db_desembolsos_ingresoyegresobanco_egreso_crediticio as $valuecrediticio){
            if($valuecrediticio->validar_estado==1 && $validacion_1 == ''){
                $validacion_1 = 'CHECK';
            }
            $desembolsos_ingresoyegresobanco_egreso_crediticio += $valuecrediticio->monto_solicitado;
        }
            
        if($validacion_1=='CHECK' && $validacion_0 == ''){
            $validacion_0 = 'CHECK';
            $validacion_operaciones_cuenta_banco_cant += 1;
        }
    
        if(count($db_desembolsos_ingresoyegresobanco_egreso_crediticio)>0){
            $validacion_cantidad += 1;
            $ingresoyegresobanco_egreso_crediticio_validacion_cantidad += 1;
        }
    
        $ingresoyegresobanco_egreso_crediticio += number_format($desembolsos_ingresoyegresobanco_egreso_crediticio, 2, '.', '');
        
        $ingresoyegresobanco_egreso_crediticio_bancos[] = [
            'banco_nombre' => $valuebancos->nombre,
            'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
            'banco' => number_format($desembolsos_ingresoyegresobanco_egreso_crediticio, 2, '.', ''),
            'validacion' => $validacion_1,
        ];
    }
    $ingresoyegresobanco_egreso_crediticio_validacion = $validacion_0;
    
    $where = [];
    if($idagencia!=''){
        $where[] = ['cvasignacioncapital.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['cvasignacioncapital.fecharegistro','>=',$fechacorte.' 00:00:00'];
        $where[] = ['cvasignacioncapital.fecharegistro','<=',$fechacorte.' 23:59:59'];
    }
    $ingresoyegresobanco_egreso_reduccioncapital = 0;
    $ingresoyegresobanco_egreso_reduccioncapital_bancos = [];
    $ingresoyegresobanco_egreso_reduccioncapital_validacion = '';
    $validacion_0 = '';
    $ingresoyegresobanco_egreso_reduccioncapital_validacion_cantidad = 0;
    foreach($bancos as $valuebancos){
    
        $db_ingresoyegresobanco_egreso_reduccioncapital = DB::table('cvasignacioncapital')
            ->where('cvasignacioncapital.idtipodestino',3)
            ->where('cvasignacioncapital.idestadoeliminado',1)
            ->where('cvasignacioncapital.idresponsable_recfinal', '<>', 0)
            ->where('cvasignacioncapital.idtipooperacion',2)
            ->where('cvasignacioncapital.idbanco',$valuebancos->id)
            ->where($where)
            ->get();
    
        $validacion_1 = '';
        $ingresoyegresobanco_egreso_reduccioncapital_monto = 0;
        foreach($db_ingresoyegresobanco_egreso_reduccioncapital as $valuecrediticio){
            if($valuecrediticio->validar_estado==1 && $validacion_1 == ''){
                $validacion_1 = 'CHECK';
            }
            $ingresoyegresobanco_egreso_reduccioncapital_monto += $valuecrediticio->monto;
        }
            
        if($validacion_1=='CHECK' && $validacion_0 == ''){
            $validacion_0 = 'CHECK';
            $validacion_operaciones_cuenta_banco_cant += 1;
        }
    
        if(count($db_ingresoyegresobanco_egreso_reduccioncapital)>0){
            $validacion_cantidad += 1;
            $ingresoyegresobanco_egreso_reduccioncapital_validacion_cantidad += 1;
        }
    
        $ingresoyegresobanco_egreso_reduccioncapital += number_format($ingresoyegresobanco_egreso_reduccioncapital_monto, 2, '.', '');
    
        $ingresoyegresobanco_egreso_reduccioncapital_bancos[] = [
            'banco_nombre' => $valuebancos->nombre,
            'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
            'banco' => number_format($ingresoyegresobanco_egreso_reduccioncapital_monto, 2, '.', ''),
            'validacion' => $validacion_1,
        ];
    }
    $ingresoyegresobanco_egreso_reduccioncapital_validacion = $validacion_0;
    
    $where = [];
    if($idagencia!=''){
        $where[] = ['cvgastoadministrativooperativo.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['cvgastoadministrativooperativo.fechapago','>=',$fechacorte.' 00:00:00'];
        $where[] = ['cvgastoadministrativooperativo.fechapago','<=',$fechacorte.' 23:59:59'];
    }
    $ingresoyegresobanco_egreso_gastosadministrativosyoperativos = 0;
    $ingresoyegresobanco_egreso_gastosadministrativosyoperativos_bancos = [];
    $ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion = '';
    $validacion_0 = '';
    $ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion_cantidad = 0;
    foreach($bancos as $valuebancos){
    
        $db_ingresoyegresocaja_egreso_gastosadministrativosyoperativos = DB::table('cvgastoadministrativooperativo')
            ->where('cvgastoadministrativooperativo.idformapago',2) 
            ->where('cvgastoadministrativooperativo.idestadoeliminado',1) 
            ->where('cvgastoadministrativooperativo.idbanco',$valuebancos->id)
            ->where($where)
            ->get();
    
        $validacion_1 = '';
        $ingresoyegresocaja_egreso_gastosadministrativosyoperativos_monto = 0;
        foreach($db_ingresoyegresocaja_egreso_gastosadministrativosyoperativos as $valuecrediticio){
            if($valuecrediticio->validar_estado==1 && $validacion_1 == ''){
                $validacion_1 = 'CHECK';
            }
            $ingresoyegresocaja_egreso_gastosadministrativosyoperativos_monto += $valuecrediticio->monto;
        }
            
        if($validacion_1=='CHECK' && $validacion_0 == ''){
            $validacion_0 = 'CHECK';
            $validacion_operaciones_cuenta_banco_cant += 1;
        }
    
        if(count($db_ingresoyegresocaja_egreso_gastosadministrativosyoperativos)>0){
            $validacion_cantidad += 1;
            $ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion_cantidad += 1;
        }
    
        $ingresoyegresobanco_egreso_gastosadministrativosyoperativos += number_format($ingresoyegresocaja_egreso_gastosadministrativosyoperativos_monto, 2, '.', '');
    
        $ingresoyegresobanco_egreso_gastosadministrativosyoperativos_bancos[] = [
            'banco_nombre' => $valuebancos->nombre,
            'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
            'banco' => number_format($ingresoyegresocaja_egreso_gastosadministrativosyoperativos_monto, 2, '.', ''),
            'validacion' => $validacion_1,
        ];
    }
    $ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion = $validacion_0;

    // HABILITACIÓN Y GESTIÓN DE LIQUIDEZ ( I )
    $valid_habilitacion = 0;
    
    $where = [];
    if($idagencia!=''){
        $where[] = ['cvmovimientointernodinero.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['cvmovimientointernodinero.fecharegistro','<=',$fechacorte.' 23:59:59'];
    }
    
    $ret_reservacf_caja = DB::table('cvmovimientointernodinero')
        ->where('cvmovimientointernodinero.idestadoeliminado',1)
        ->where('cvmovimientointernodinero.idfuenteretiro',6)
        ->where($where)
        ->select(
            'cvmovimientointernodinero.*',
        )
        ->sum('cvmovimientointernodinero.monto');
    
    $ret_banco_caja = 0;
    $ret_banco_caja_bancos = [];
    $dep_caja_banco = 0;
    $dep_caja_banco_bancos = [];
    foreach($bancos as $valuebancos){
        $movimientointernodineros = DB::table('cvmovimientointernodinero')
            ->where('cvmovimientointernodinero.idestadoeliminado',1)
            ->where('cvmovimientointernodinero.idfuenteretiro',7)
            ->where('cvmovimientointernodinero.idtipomovimientointerno',1)
            ->where('cvmovimientointernodinero.idbanco',$valuebancos->id)
            ->where($where)
            ->sum('cvmovimientointernodinero.monto');
        $movimientointernodineros1 = DB::table('cvmovimientointernodinero')
            ->where('cvmovimientointernodinero.idestadoeliminado',1)
            ->where('cvmovimientointernodinero.idfuenteretiro',2)
            ->where('cvmovimientointernodinero.idtipomovimientointerno',2)
            ->where('cvmovimientointernodinero.idresponsable','<>',0)
            ->where('cvmovimientointernodinero.idbanco',$valuebancos->id)
            ->where($where)
            ->sum('cvmovimientointernodinero.monto');
        $ret_banco_caja += number_format($movimientointernodineros, 2, '.', '');
        $dep_caja_banco += number_format($movimientointernodineros1, 2, '.', '');
    
        $ret_banco_caja_bancos[] = [
            'banco_id' => $valuebancos->id,
            'banco_nombre' => $valuebancos->nombre,
            'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
            'banco' => number_format($movimientointernodineros, 2, '.', ''),
            'banco_dep' => number_format($movimientointernodineros1, 2, '.', ''),
        ];
    
        if($ret_banco_caja!=$dep_caja_banco){
            $valid_habilitacion++;
        }
    }
    
    $ret_caja_reservacf = DB::table('cvmovimientointernodinero')
        ->where('cvmovimientointernodinero.idestadoeliminado',1)
        ->where('cvmovimientointernodinero.idfuenteretiro',8)
        ->where($where)
        ->sum('cvmovimientointernodinero.monto');
    
    $ret_caja_banco = 0;
    $ret_caja_banco_bancos = [];
    $dep_banco_caja = 0;
    $dep_banco_caja_bancos = [];
    foreach($bancos as $valuebancos){
        $movimientointernodineros = DB::table('cvmovimientointernodinero')
            ->where('cvmovimientointernodinero.idestadoeliminado',1)
            ->where('cvmovimientointernodinero.idfuenteretiro',9)
            ->where('cvmovimientointernodinero.idtipomovimientointerno',1)
            ->where('cvmovimientointernodinero.idbanco',$valuebancos->id)
            ->where($where)
            ->sum('cvmovimientointernodinero.monto');
        $movimientointernodineros1 = DB::table('cvmovimientointernodinero')
            ->where('cvmovimientointernodinero.idestadoeliminado',1)
            ->where('cvmovimientointernodinero.idfuenteretiro',4)
            ->where('cvmovimientointernodinero.idtipomovimientointerno',2)
            ->where('cvmovimientointernodinero.idresponsable','<>',0)
            ->where('cvmovimientointernodinero.idbanco',$valuebancos->id)
            ->where($where)
            ->sum('cvmovimientointernodinero.monto');
    
        $ret_caja_banco += number_format($movimientointernodineros, 2, '.', '');
        $dep_banco_caja += number_format($movimientointernodineros1, 2, '.', '');
        $ret_caja_banco_bancos[] = [
            'banco_nombre' => $valuebancos->nombre,
            'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
            'banco' => number_format($movimientointernodineros, 2, '.', ''),
            'banco_dep' => number_format($movimientointernodineros1, 2, '.', ''),
        ];
    
        if($ret_caja_banco!=$dep_banco_caja){
            $valid_habilitacion++;
        }
    }
    //----
    $dep_caja_reservacf = DB::table('cvmovimientointernodinero')
        ->where('cvmovimientointernodinero.idestadoeliminado',1)
        ->where('cvmovimientointernodinero.idfuenteretiro',1)
        ->where('cvmovimientointernodinero.idresponsable','<>',0)
        ->where($where)
        ->sum('cvmovimientointernodinero.monto');
    
    if($ret_reservacf_caja!=$dep_caja_reservacf){
        $valid_habilitacion++;
    }
    
    $dep_reservacf_caja = DB::table('cvmovimientointernodinero')
        ->where('cvmovimientointernodinero.idestadoeliminado',1)
        ->where('cvmovimientointernodinero.idfuenteretiro',3)
        ->where('cvmovimientointernodinero.idresponsable','<>',0)
        ->where($where)
        ->sum('cvmovimientointernodinero.monto');

    if($ret_caja_reservacf!=$dep_reservacf_caja){
        $valid_habilitacion++;
    }
    
    $habilitacion_gestion_liquidez1 = $ret_reservacf_caja+
                                    $ret_banco_caja+
                                    $ret_caja_reservacf+
                                    $ret_caja_banco-
                                    $dep_caja_reservacf-
                                    $dep_caja_banco-
                                    $dep_reservacf_caja-
                                    $dep_banco_caja;
    
    // HABILITACIÓN Y GESTIÓN DE LIQUIDEZ ( II )
    
    $ret_banco_reservacf = 0;
    $ret_banco_reservacf_bancos = [];
    $dep_reservacf_banco = 0;
    $dep_reservacf_banco_bancos = [];
    foreach($bancos as $valuebancos){
        $movimientointernodineros = DB::table('cvmovimientointernodinero')
            ->where('cvmovimientointernodinero.idestadoeliminado',1)
            ->where('cvmovimientointernodinero.idfuenteretiro',10)
            ->where('cvmovimientointernodinero.idtipomovimientointerno',3)
            ->where('cvmovimientointernodinero.idbanco',$valuebancos->id)
            ->where($where)
            ->sum('cvmovimientointernodinero.monto');
        $movimientointernodineros1 = DB::table('cvmovimientointernodinero')
            ->where('cvmovimientointernodinero.idestadoeliminado',1)
            ->where('cvmovimientointernodinero.idfuenteretiro',5)
            ->where('cvmovimientointernodinero.idtipomovimientointerno',4)
            ->where('cvmovimientointernodinero.idresponsable','<>',0)
            ->where('cvmovimientointernodinero.idbanco',$valuebancos->id)
            ->where($where)
            ->sum('cvmovimientointernodinero.monto');
        $ret_banco_reservacf += number_format($movimientointernodineros, 2, '.', '');
        $dep_reservacf_banco += number_format($movimientointernodineros1, 2, '.', '');
        $ret_banco_reservacf_bancos[] = [
            'banco_nombre' => $valuebancos->nombre,
            'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
            'banco' => number_format($movimientointernodineros, 2, '.', ''),
            'banco_dep' => number_format($movimientointernodineros1, 2, '.', ''),
        ];
    
        if($ret_banco_reservacf!=$dep_reservacf_banco){
            $valid_habilitacion++;
        }
    }
    
    $habilitacion_gestion_liquidez2 = $ret_banco_reservacf-$dep_reservacf_banco;
    
    // CIERRE Y APERTURA DE CAJA
    
    $ret_reservacf_caja_total = DB::table('cvmovimientointernodinero')
        ->where('cvmovimientointernodinero.idestadoeliminado',1)
        ->where('cvmovimientointernodinero.idfuenteretiro',6)
        ->where('cvmovimientointernodinero.idtipomovimientointerno',5)
        ->where($where)
        ->sum('cvmovimientointernodinero.monto');
    $ret_caja_reservacf_total = DB::table('cvmovimientointernodinero')
        ->where('cvmovimientointernodinero.idestadoeliminado',1)
        ->where('cvmovimientointernodinero.idfuenteretiro',8)
        ->where('cvmovimientointernodinero.idtipomovimientointerno',5)
        ->where($where)
        ->sum('cvmovimientointernodinero.monto');
    
    $dep_caja_reservacf_total = DB::table('cvmovimientointernodinero')
        ->where('cvmovimientointernodinero.idestadoeliminado',1)
        ->where('cvmovimientointernodinero.idfuenteretiro',1)
        ->where('cvmovimientointernodinero.idtipomovimientointerno',6)
        ->where('cvmovimientointernodinero.idresponsable','<>',0)
        ->where($where)
        ->sum('cvmovimientointernodinero.monto');
    $dep_reservacf_caja_total = DB::table('cvmovimientointernodinero')
        ->where('cvmovimientointernodinero.idestadoeliminado',1)
        ->where('cvmovimientointernodinero.idfuenteretiro',3)
        ->where('cvmovimientointernodinero.idtipomovimientointerno',6)
        ->where('cvmovimientointernodinero.idresponsable','<>',0)
        ->where($where)
        ->sum('cvmovimientointernodinero.monto');


    if($ret_reservacf_caja_total!=$dep_caja_reservacf_total){
        $valid_habilitacion++;
    }
    if($ret_caja_reservacf_total!=$dep_reservacf_caja_total){
        $valid_habilitacion++;
    }
    
    $cierre_caja_apertura = $ret_reservacf_caja_total+
                            $ret_caja_reservacf_total-
                            $dep_caja_reservacf_total-
                            $dep_reservacf_caja_total;
    
    // --------------------------------------- //
    // ----------- SALDOS  FINALES ----------- //
    // --------------------------------------- //
    
    $where = [];
    if($idagencia!=''){
        $where[] = ['arqueocaja.idagencia',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['arqueocaja.corte',$fechacorte];
    }
    $arqueo_caja = DB::table('arqueocaja')
        ->where('arqueocaja.idestado',1)
        ->where($where)
        ->sum('arqueocaja.total');

    $where1 = [];
    $where2 = [];
    $where3 = [];
    $where4 = [];
    $where5 = [];
    $where6 = [];
    if($idagencia!=''){
        $where1[] = ['credito_cobranzacuota.idtienda',$idagencia];
        $where2[] = ['credito.idtienda',$idagencia];
        $where3[] = ['cvasignacioncapital.idtienda',$idagencia];
        $where4[] = ['cvmovimientointernodinero.idtienda',$idagencia];
        $where5[] = ['cvgastoadministrativooperativo.idtienda',$idagencia];
        $where6[] = ['cvingresoextraordinario.idtienda',$idagencia];
        $where7[] = ['cvcompra.idtienda',$idagencia];
        $where8[] = ['cvventa.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where1[] = ['credito_cobranzacuota.fecharegistro','<=',$fechacorte.' 23:59:59'];
        $where2[] = ['credito_formapago.fechapago','<=',$fechacorte.' 23:59:59'];
        $where3[] = ['cvasignacioncapital.fecharegistro','<=',$fechacorte.' 23:59:59'];
        $where4[] = ['cvmovimientointernodinero.fecharegistro','<=',$fechacorte.' 23:59:59'];
        $where5[] = ['cvgastoadministrativooperativo.fechapago','<=',$fechacorte.' 23:59:59'];
        $where6[] = ['cvingresoextraordinario.fechapago','<=',$fechacorte.' 23:59:59'];
        $where7[] = ['cvcompra.fecharegistro','<=',$fechacorte.' 23:59:59'];
        $where8[] = ['cvventa.fecharegistro','<=',$fechacorte.' 23:59:59'];
    }

    $saldos_cuentabanco = 0;
    $saldos_cuentabanco_bancos = [];
    foreach($bancos as $valuebancos){
    
        $saldos_capitalasignada_1 = DB::table('cvasignacioncapital')
            ->where('cvasignacioncapital.idestadoeliminado',1)
            ->where('cvasignacioncapital.idresponsable_recfinal', '<>', 0)
            ->whereIn('cvasignacioncapital.idtipooperacion',[1,4])
            ->where('cvasignacioncapital.idbanco',$valuebancos->id)
            ->where($where3)
            ->sum('cvasignacioncapital.monto');
    
        $saldos_capitalasignada_2 = DB::table('cvasignacioncapital')
            ->where('cvasignacioncapital.idestadoeliminado',1)
            ->where('cvasignacioncapital.idresponsable_recfinal', '<>', 0)
            ->whereIn('cvasignacioncapital.idtipooperacion',[2])
            ->where('cvasignacioncapital.idbanco',$valuebancos->id)
            ->where($where3)
            ->sum('cvasignacioncapital.monto'); 
    
        $cobranzas = 0; 
        $desembolsos = 0;
    
        $movimientointernodineros1 = DB::table('cvmovimientointernodinero')
            ->where('cvmovimientointernodinero.idestadoeliminado',1)
            ->where('cvmovimientointernodinero.idfuenteretiro',7)
            ->where('cvmovimientointernodinero.idbanco',$valuebancos->id)
            ->where($where4)
            ->sum('cvmovimientointernodinero.monto');
    
        $movimientointernodineros2 = DB::table('cvmovimientointernodinero')
            ->where('cvmovimientointernodinero.idestadoeliminado',1)
            ->where('cvmovimientointernodinero.idfuenteretiro',9)
            ->where('cvmovimientointernodinero.idbanco',$valuebancos->id)
            ->where($where4)
            ->sum('cvmovimientointernodinero.monto');
    
        $movimientointernodineros5 = DB::table('cvmovimientointernodinero')
            ->where('cvmovimientointernodinero.idestadoeliminado',1)
            ->where('cvmovimientointernodinero.idfuenteretiro',10)
            ->where('cvmovimientointernodinero.idbanco',$valuebancos->id)
            ->where($where4)
            ->sum('cvmovimientointernodinero.monto');
    
        $gastosadministrativosyoperativos_monto = DB::table('cvgastoadministrativooperativo')
            ->where('cvgastoadministrativooperativo.idformapago',2) 
            ->where('cvgastoadministrativooperativo.idestadoeliminado',1) 
            ->where('cvgastoadministrativooperativo.idbanco',$valuebancos->id)
            ->where($where5)
            ->sum('cvgastoadministrativooperativo.monto');
    
        $ingresosextraordinarios_monto = DB::table('cvingresoextraordinario')
            ->where('cvingresoextraordinario.idformapago',2) 
            ->where('cvingresoextraordinario.idestadoeliminado',1) 
            ->where('cvingresoextraordinario.idbanco',$valuebancos->id)
            ->where($where6)
            ->sum('cvingresoextraordinario.monto');

        $cvcompra_monto = DB::table('cvcompra')
            ->where('cvcompra.idestadoeliminado',1) 
            ->where('cvcompra.compra_idformapago',2) 
            ->where('cvcompra.compra_idbanco',$valuebancos->id)
            ->where($where7)
            ->sum('cvcompra.valorcompra');

        $cvventa_monto = DB::table('cvventa')
            ->where('cvventa.idestadoeliminado',1) 
            ->where('cvventa.venta_idformapago',2)
            ->where('cvventa.venta_idbanco',$valuebancos->id)
            ->where($where8)
            ->sum('cvventa.venta_montoventa');
    
        $saldos_cuentabanco += number_format($saldos_capitalasignada_1-
                                            $saldos_capitalasignada_2+
                                            $cobranzas-
                                            $desembolsos-
                                            $movimientointernodineros1+
                                            $movimientointernodineros2-
                                            $movimientointernodineros5-
                                            $gastosadministrativosyoperativos_monto+
                                            $ingresosextraordinarios_monto-
                                            $cvcompra_monto+
                                            $cvventa_monto
                                            , 2, '.', '');

        $saldos_cuentabanco_bancos[] = [
            'banco_id' => $valuebancos->id,
            'banco_nombre' => $valuebancos->nombre,
            'banco_cuenta' => '(******'.substr($valuebancos->cuenta, -5).')',
            'banco' => number_format($saldos_capitalasignada_1-
                                    $saldos_capitalasignada_2+
                                    $cobranzas-$desembolsos-
                                    $movimientointernodineros1+
                                    $movimientointernodineros2-
                                    $movimientointernodineros5-
                                    $gastosadministrativosyoperativos_monto+
                                    $ingresosextraordinarios_monto-
                                    $cvcompra_monto+
                                    $cvventa_monto
                                    , 2, '.', ''),
        ];
    }
    
    $where = [];
    $where1 = [];
    $where2 = [];
    $where3 = [];
    $where4 = [];
    if($idagencia!=''){
        $where[] = ['cvasignacioncapital.idtienda',$idagencia];
        $where1[] = ['credito.idtienda',$idagencia];
        $where2[] = ['cvingresoextraordinario.idtienda',$idagencia];
        $where3[] = ['credito.idtienda',$idagencia];
        $where4[] = ['cvgastoadministrativooperativo.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['cvasignacioncapital.fecharegistro','<=',$fechacorte.' 23:59:59'];
        $where1[] = ['credito_cobranzacuota.fecharegistro','<=',$fechacorte.' 23:59:59'];
        $where2[] = ['cvingresoextraordinario.fechapago','<=',$fechacorte.' 23:59:59'];
        $where3[] = ['credito_formapago.fechapago','<=',$fechacorte.' 23:59:59'];
        $where4[] = ['cvgastoadministrativooperativo.fechapago','<=',$fechacorte.' 23:59:59'];
    }

    $asignacioncapital_deposito_reserva = DB::table('cvasignacioncapital')
        ->where('cvasignacioncapital.idtipodestino',2)
        ->where('cvasignacioncapital.idestadoeliminado',1)
        ->where('cvasignacioncapital.idresponsable_recfinal', '<>', 0)
        ->whereIn('cvasignacioncapital.idtipooperacion',[1,4])
        ->where($where)
        ->sum('cvasignacioncapital.monto');
    $asignacioncapital_retiro_reserva = DB::table('cvasignacioncapital')
        ->where('cvasignacioncapital.idtipodestino',2)
        ->where('cvasignacioncapital.idestadoeliminado',1)
        ->where('cvasignacioncapital.idresponsable_recfinal', '<>', 0)
        ->where('cvasignacioncapital.idtipooperacion',2)
        ->where($where)
        ->sum('cvasignacioncapital.monto');
    $saldos_reserva = $asignacioncapital_deposito_reserva-
        $asignacioncapital_retiro_reserva-
        $ret_reservacf_caja+
        $ret_caja_reservacf+
        $ret_banco_reservacf;

    $asignacioncapital_deposito_caja = DB::table('cvasignacioncapital')
        ->where('cvasignacioncapital.idtipodestino',1)
        ->where('cvasignacioncapital.idestadoeliminado',1)
        ->where('cvasignacioncapital.idresponsable_recfinal', '<>', 0)
        ->where('cvasignacioncapital.idtipooperacion',4)
        ->where($where)
        ->sum('cvasignacioncapital.monto');
    $asignacioncapital_retiro_caja = DB::table('cvasignacioncapital')
        ->where('cvasignacioncapital.idtipodestino',1)
        ->where('cvasignacioncapital.idestadoeliminado',1)
        ->where('cvasignacioncapital.idresponsable_recfinal', '<>', 0)
        ->where('cvasignacioncapital.idtipooperacion',2)
        ->where($where)
        ->sum('cvasignacioncapital.monto');
    /* $ingresoyegresocaja_ingreso_crediticio_cps = DB::table('credito_cobranzacuota')
        ->join('credito','credito.id','credito_cobranzacuota.idcredito')
        ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
        ->where('credito_cobranzacuota.idestadoextorno',0)
        ->where('credito_cobranzacuota.idformapago',1)
        ->where($where1)
        ->select(
            'credito_cobranzacuota.*',
        )
        ->get(); */
    $ingresoyegresocaja_ingreso_crediticio_saldofinal = 0;
    /* foreach($ingresoyegresocaja_ingreso_crediticio_cps as $value){
        $ingresoyegresocaja_ingreso_crediticio_saldofinal += $value->total_pagar_amortizacion+
            $value->total_pagar_interes+
            $value->total_pagar_comision+$value->total_pagar_cargo+
            $value->total_pagar_tenencia+$value->total_pagar_penalidad+$value->total_pagar_compensatorio;
    } */
    $ingresoyegresocaja_ingreso_incrementocapital_saldofinal = DB::table('cvasignacioncapital')
        ->where('cvasignacioncapital.idtipodestino',1)
        ->where('cvasignacioncapital.idestadoeliminado',1)
        ->where('cvasignacioncapital.idresponsable_recfinal', '<>', 0)
        ->where('cvasignacioncapital.idtipooperacion',1)
        ->where($where)
        ->sum('cvasignacioncapital.monto');
    $ingresoyegresocaja_ingreso_ingresosextraordinarios_saldofinal = DB::table('cvingresoextraordinario')
        ->where('cvingresoextraordinario.idformapago',1) 
        ->where('cvingresoextraordinario.idestadoeliminado',1) 
        ->where($where2)
        ->sum('cvingresoextraordinario.monto');
    $ingresoyegresocaja_egreso_crediticio_saldofinal = 0;
    /* DB::table('credito_formapago')
        ->join('credito','credito.id','credito_formapago.idcredito')
        ->where('credito_formapago.idformapago',1)
        ->where('credito.estado','DESEMBOLSADO')
        ->where($where3)
        ->sum('credito.monto_solicitado'); */
    $ingresoyegresocaja_egreso_reduccioncapital_saldocapital = 0;
    /* DB::table('cvasignacioncapital')
        ->whereIn('cvasignacioncapital.idtipodestino',[0,1])
        ->where('cvasignacioncapital.idestadoeliminado',1)
        ->where('cvasignacioncapital.idresponsable_recfinal', '<>', 0)
        ->where('cvasignacioncapital.idtipooperacion',2)
        ->where($where)
        ->sum('cvasignacioncapital.monto'); */
    $ingresoyegresocaja_egreso_gastosadministrativosyoperativos_saldocapital = DB::table('cvgastoadministrativooperativo')
        ->where('cvgastoadministrativooperativo.idformapago',1) 
        ->where('cvgastoadministrativooperativo.idestadoeliminado',1) 
        ->where($where4)
        ->sum('cvgastoadministrativooperativo.monto');

    /* dd($asignacioncapital_deposito_caja,
        $asignacioncapital_retiro_caja,
        $ret_reservacf_caja,
        $ret_banco_caja,
        $ret_caja_reservacf,
        $ret_caja_banco,
        $ingresoyegresocaja_ingreso_crediticio_saldofinal,
        $ingresoyegresocaja_ingreso_ahorro,
        $ingresoyegresocaja_ingreso_incrementocapital_saldofinal,
        $ingresoyegresocaja_ingreso_ingresosextraordinarios_saldofinal,
        $ingresoyegresocaja_egreso_crediticio_saldofinal,
        $ingresoyegresocaja_egreso_ahorro,
        $ingresoyegresocaja_egreso_reduccioncapital_saldocapital,
        $ingresoyegresocaja_egreso_gastosadministrativosyoperativos_saldocapital); */
    $saldos_caja = $asignacioncapital_deposito_caja-
        $asignacioncapital_retiro_caja+
        $ret_reservacf_caja+
        $ret_banco_caja-
        $ret_caja_reservacf-
        $ret_caja_banco+
        $ingresoyegresocaja_ingreso_crediticio_saldofinal+
        $ingresoyegresocaja_ingreso_ahorro+
        $ingresoyegresocaja_ingreso_incrementocapital_saldofinal+
        $ingresoyegresocaja_ingreso_ingresosextraordinarios_saldofinal-
        $ingresoyegresocaja_egreso_crediticio_saldofinal-
        $ingresoyegresocaja_egreso_ahorro-
        $ingresoyegresocaja_egreso_reduccioncapital_saldocapital-
        $ingresoyegresocaja_egreso_gastosadministrativosyoperativos_saldocapital-
        $ingresoyegresocaja_egreso_cvcompra+
        $ingresoyegresocaja_ingreso_cvventa;
        /*+
        $dep_caja_reservacf+
        $dep_caja_banco-
        $dep_reservacf_caja-
        $dep_banco_caja;*/
    
    
    $where = [];
    if($idagencia!=''){
        $where[] = ['credito.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['credito.fecha_desembolso','<=',$fechacorte];
    }
    
    $saldos_creditovigente_cnp = 0;
    /*DB::table('credito')
        ->where('credito.estado','DESEMBOLSADO')
        ->where('credito.idestadocredito',1)
        ->where('credito.idforma_credito',2)
        ->where($where)
        ->sum('credito.saldo_pendientepago');*/
    $saldos_creditovigente_cp = 0;
    /*DB::table('credito')
        ->where('credito.estado','DESEMBOLSADO')
        ->where('credito.idestadocredito',1)
        ->where('credito.idforma_credito',1)
        ->where($where)
        ->sum('credito.saldo_pendientepago');*/
    $saldos_creditovigente = $saldos_creditovigente_cnp+
                            $saldos_creditovigente_cp;
    
    $saldos_bienescomprados = $ingresoyegresocaja_egreso_cvcompra - $ingresoyegresocaja_ingreso_cvventa_valorcompra;
    
    $saldos_interescreditovigentexcobrar_cnp = 0;
    /*DB::table('credito_cronograma')
        ->join('credito','credito.id','credito_cronograma.idcredito')
        ->where('credito_cronograma.idestadocronograma_pago',0)
        ->where('credito.estado','DESEMBOLSADO')
        ->where('credito.idestadocredito',1)
        ->where('credito.idforma_credito',2)
        ->where($where)
        ->sum('credito_cronograma.interes');*/
    $saldos_interescreditovigentexcobrar_cp = 0;
    /* DB::table('credito_cronograma')
        ->join('credito','credito.id','credito_cronograma.idcredito')
        ->where('credito_cronograma.idestadocronograma_pago',0)
        ->where('credito.estado','DESEMBOLSADO')
        ->where('credito.idestadocredito',1)
        ->where('credito.idforma_credito',1)
        ->where($where)
        ->sum('credito_cronograma.interes'); */
    $saldos_interescreditovigentexcobrar = $saldos_interescreditovigentexcobrar_cnp+
                                            $saldos_interescreditovigentexcobrar_cp;
    $saldos_ahorros = 0;
    $saldos_interesgeneradosxpagar_ahorropf = 0;
    $saldos_interesgeneradosxpagar_interescuentaahorropdfprogramadas = 0;
    $saldos_interesgeneradosxpagar_ahorrocorriente = 0;
    $saldos_interesgeneradosxpagar_interescuentaahorrocgeneradas = 0;
    $saldos_interesgeneradosxpagar = $saldos_interesgeneradosxpagar_ahorropf+
                                    $saldos_interesgeneradosxpagar_interescuentaahorropdfprogramadas+
                                    $saldos_interesgeneradosxpagar_ahorrocorriente+
                                    $saldos_interesgeneradosxpagar_interescuentaahorrocgeneradas;
    
    $where = [];
    if($idagencia!=''){
        $where[] = ['cvasignacioncapital.idtienda',$idagencia];
    }
    if($fechacorte!=''){
        $where[] = ['cvasignacioncapital.fecharegistro','<=',$fechacorte.' 23:59:59'];
    }
    /*$ret_correc = DB::table('asignacioncapital')
        ->where('asignacioncapital.idestadoeliminado',1)
        ->where('asignacioncapital.idtipooperacion',3)
        ->where($where)
        ->sum('asignacioncapital.monto');
    $saldos_capitalasignada = $saldos_cuentabanco+$saldos_reserva+$saldos_caja-$ret_correc;*/
    
    $monto_suma = DB::table('cvasignacioncapital')
        ->where('cvasignacioncapital.idestadoeliminado',1)
        ->whereIn('cvasignacioncapital.idtipooperacion',[1,4])
        ->where('cvasignacioncapital.idresponsable_recfinal','<>',0)
        ->where('cvasignacioncapital.idtienda',$idagencia)
        ->where($where)
        ->sum('cvasignacioncapital.monto');

    $monto_resta = DB::table('cvasignacioncapital')
        ->where('cvasignacioncapital.idestadoeliminado',1)
        ->whereIn('cvasignacioncapital.idtipooperacion',[2,3])
        ->where('cvasignacioncapital.idresponsable_recfinal','<>',0)
        ->where('cvasignacioncapital.idtienda',$idagencia)
        ->where($where)
        ->sum('cvasignacioncapital.monto');
    $saldos_capitalasignada = $monto_suma-$monto_resta;
    
    $total_efectivo_ejercicio = $saldos_cuentabanco+$saldos_reserva+$saldos_caja+$saldos_creditovigente;
    $incremental_capital_asignado = $total_efectivo_ejercicio-$saldos_capitalasignada;
    
    $spread_financiero_proyectado = $saldos_interesgeneradosxpagar;
    $indicador_reserva_legal = ($saldos_cuentabanco+$saldos_caja);

    $validacion_operaciones_cuenta_banco = '';
    //dd($validacion_operaciones_cuenta_banco_cant);
    if($validacion_operaciones_cuenta_banco_cant==0 && 
        $validacion_cantidad==0){
        //dd($valid_habilitacion);
        if($valid_habilitacion>0){
            $validacion_operaciones_cuenta_banco = 'PENDIENTE';
        }else{
            $validacion_operaciones_cuenta_banco = 'SIN OPERACIONES';
        }
    }
    /*elseif(($validacion_operaciones_cuenta_banco_cant>0 && 
            $validacion_operaciones_cuenta_banco_cant<$validacion_cantidad) ||
        ){*/
    elseif($validacion_operaciones_cuenta_banco_cant!=$validacion_cantidad){
        $validacion_operaciones_cuenta_banco = 'PENDIENTE';
    }
    elseif($validacion_operaciones_cuenta_banco_cant==$validacion_cantidad){
        $validacion_operaciones_cuenta_banco = 'VERIFICADO';
    }

    $efectivo_caja_corte = $saldos_caja;
    $efectivo_caja_arqueo = 0;
    $resultado = $efectivo_caja_arqueo-$efectivo_caja_corte;
    
    $data = [
        'tienda' => $tienda,
        'agencia' => $agencia,
        'bancos' => $bancos,
        'corte' => date("d-m-Y",strtotime(date($fechacorte))),
    
        'ingresoyegresocaja_ingreso_crediticio_cnp_capital' => number_format($ingresoyegresocaja_ingreso_crediticio_cnp_capital, 2, '.', ''),
        'ingresoyegresocaja_ingreso_crediticio_cnp_interes' => number_format($ingresoyegresocaja_ingreso_crediticio_cnp_interes, 2, '.', ''),
        'ingresoyegresocaja_ingreso_crediticio_cnp_desgravcargo' => number_format($ingresoyegresocaja_ingreso_crediticio_cnp_desgravcargo, 2, '.', ''),
        'ingresoyegresocaja_ingreso_crediticio_cnp_tenencxc' => number_format($ingresoyegresocaja_ingreso_crediticio_cnp_tenencxc, 2, '.', ''),
        'ingresoyegresocaja_ingreso_crediticio_cnp' => number_format($ingresoyegresocaja_ingreso_crediticio_cnp, 2, '.', ''),   
        'ingresoyegresocaja_ingreso_crediticio_cp_capital' => number_format($ingresoyegresocaja_ingreso_crediticio_cp_capital, 2, '.', ''),
        'ingresoyegresocaja_ingreso_crediticio_cp_interes' => number_format($ingresoyegresocaja_ingreso_crediticio_cp_interes, 2, '.', ''),
        'ingresoyegresocaja_ingreso_crediticio_cp_desgravcargo' => number_format($ingresoyegresocaja_ingreso_crediticio_cp_desgravcargo, 2, '.', ''),
        'ingresoyegresocaja_ingreso_crediticio_cp_tenencxc' => number_format($ingresoyegresocaja_ingreso_crediticio_cp_tenencxc, 2, '.', ''),
        'ingresoyegresocaja_ingreso_crediticio_cp' => number_format($ingresoyegresocaja_ingreso_crediticio_cp, 2, '.', ''),
        'ingresoyegresocaja_ingreso_ahorro_plazofijo' => number_format($ingresoyegresocaja_ingreso_ahorro_plazofijo, 2, '.', ''),
        'ingresoyegresocaja_ingreso_ahorro_ahorroc' => number_format($ingresoyegresocaja_ingreso_ahorro_ahorroc, 2, '.', ''),
        'ingresoyegresocaja_ingreso_ahorro' => number_format($ingresoyegresocaja_ingreso_ahorro, 2, '.', ''),
        'ingresoyegresocaja_ingreso_incrementocapital' => number_format($ingresoyegresocaja_ingreso_incrementocapital, 2, '.', ''),
        'ingresoyegresocaja_ingreso_ingresosextraordinarios' => number_format($ingresoyegresocaja_ingreso_ingresosextraordinarios, 2, '.', ''),
        'ingresoyegresocaja_ingreso_crediticio' => number_format($ingresoyegresocaja_ingreso_crediticio, 2, '.', ''),
        'ingresoyegresocaja_ingreso_crediticio_transitorio' => number_format($ingresoyegresocaja_ingreso_crediticio_transitorio, 2, '.', ''),
        'ingresoyegresocaja_ingreso_cvventa' => number_format($ingresoyegresocaja_ingreso_cvventa, 2, '.', ''),
        
        'ingresoyegresocaja_egreso_crediticio' => number_format($ingresoyegresocaja_egreso_crediticio, 2, '.', ''),
        'ingresoyegresocaja_egreso_ahorro_plazofijo' => number_format($ingresoyegresocaja_egreso_ahorro_plazofijo, 2, '.', ''),
        'ingresoyegresocaja_egreso_ahorro_intplazofijo' => number_format($ingresoyegresocaja_egreso_ahorro_intplazofijo, 2, '.', ''),
        'ingresoyegresocaja_egreso_ahorro_ahorrocte' => number_format($ingresoyegresocaja_egreso_ahorro_ahorrocte, 2, '.', ''),
        'ingresoyegresocaja_egreso_ahorro_intcte' => number_format($ingresoyegresocaja_egreso_ahorro_intcte, 2, '.', ''),
        'ingresoyegresocaja_egreso_ahorro' => number_format($ingresoyegresocaja_egreso_ahorro, 2, '.', ''),
        'ingresoyegresocaja_egreso_reduccioncapital' => number_format($ingresoyegresocaja_egreso_reduccioncapital, 2, '.', ''),
        'ingresoyegresocaja_egreso_gastosadministrativosyoperativos' => number_format($ingresoyegresocaja_egreso_gastosadministrativosyoperativos, 2, '.', ''),
        'ingresoyegresocaja_egreso_cvcompra' => number_format($ingresoyegresocaja_egreso_cvcompra, 2, '.', ''),

        'ingresoyegresobanco_ingreso_crediticio_cnpcp' => number_format($ingresoyegresobanco_ingreso_crediticio_cnpcp, 2, '.', ''),
        'ingresoyegresobanco_ingreso_crediticio_cnpcps_bancos' => $ingresoyegresobanco_ingreso_crediticio_cnpcps_bancos,
        'ingresoyegresobanco_ingreso_crediticio_cnpcps_validacion' => $ingresoyegresobanco_ingreso_crediticio_cnpcps_validacion,
        'ingresoyegresobanco_ingreso_crediticio_cnpcps_validacion_cantidad' => $ingresoyegresobanco_ingreso_crediticio_cnpcps_validacion_cantidad,
        'ingresoyegresobanco_ingreso_incrementocapital' => number_format($ingresoyegresobanco_ingreso_incrementocapital, 2, '.', ''),
        'ingresoyegresobanco_ingreso_incrementocapital_bancos' => $ingresoyegresobanco_ingreso_incrementocapital_bancos,
        'ingresoyegresobanco_ingreso_incrementocapital_validacion' => $ingresoyegresobanco_ingreso_incrementocapital_validacion,
        'ingresoyegresobanco_ingreso_incrementocapital_validacion_cantidad' => $ingresoyegresobanco_ingreso_incrementocapital_validacion_cantidad,
        'ingresoyegresobanco_ingreso_ingresosextraordinarios' => number_format($ingresoyegresobanco_ingreso_ingresosextraordinarios, 2, '.', ''),
        'ingresoyegresobanco_ingreso_ingresosextraordinarios_bancos' => $ingresoyegresobanco_ingreso_ingresosextraordinarios_bancos,
        'ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion' => $ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion,
        'ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion_cantidad' => $ingresoyegresobanco_ingreso_ingresosextraordinarios_validacion_cantidad,
        'ingresoyegresobanco_ingreso_cvventa' => number_format($ingresoyegresobanco_ingreso_cvventa, 2, '.', ''),
        'ingresoyegresobanco_ingreso_cvventas' => $ingresoyegresobanco_ingreso_cvventas,

        'ingresoyegresobanco_egreso_crediticio' => number_format($ingresoyegresobanco_egreso_crediticio, 2, '.', ''),
        'ingresoyegresobanco_egreso_crediticio_bancos' => $ingresoyegresobanco_egreso_crediticio_bancos,
        'ingresoyegresobanco_egreso_crediticio_validacion' => $ingresoyegresobanco_egreso_crediticio_validacion,
        'ingresoyegresobanco_egreso_crediticio_validacion_cantidad' => $ingresoyegresobanco_egreso_crediticio_validacion_cantidad,
        'ingresoyegresobanco_egreso_reduccioncapital' => number_format($ingresoyegresobanco_egreso_reduccioncapital, 2, '.', ''),
        'ingresoyegresobanco_egreso_reduccioncapital_bancos' => $ingresoyegresobanco_egreso_reduccioncapital_bancos,
        'ingresoyegresobanco_egreso_reduccioncapital_validacion' => $ingresoyegresobanco_egreso_reduccioncapital_validacion,
        'ingresoyegresobanco_egreso_reduccioncapital_validacion_cantidad' => $ingresoyegresobanco_egreso_reduccioncapital_validacion_cantidad,
        'ingresoyegresobanco_egreso_gastosadministrativosyoperativos' => number_format($ingresoyegresobanco_egreso_gastosadministrativosyoperativos, 2, '.', ''),
        'ingresoyegresobanco_egreso_gastosadministrativosyoperativos_bancos' => $ingresoyegresobanco_egreso_gastosadministrativosyoperativos_bancos,
        'ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion' => $ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion,
        'ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion_cantidad' => $ingresoyegresobanco_egreso_gastosadministrativosyoperativos_validacion_cantidad,
        'ingresoyegresobanco_egreso_cvcompra' => number_format($ingresoyegresobanco_egreso_cvcompra, 2, '.', ''),
        'ingresoyegresobanco_egreso_cvcompras' => $ingresoyegresobanco_egreso_cvcompras,
    
        'ret_reservacf_caja' => number_format($ret_reservacf_caja, 2, '.', ''),
        'ret_banco_caja' => number_format($ret_banco_caja, 2, '.', ''),
        'ret_banco_caja_bancos' => $ret_banco_caja_bancos,
        'ret_caja_reservacf' => number_format($ret_caja_reservacf, 2, '.', ''),
        'ret_caja_banco' => number_format($ret_caja_banco, 2, '.', ''),
        'ret_caja_banco_bancos' => $ret_caja_banco_bancos,
        'ret_banco_reservacf' => number_format($ret_banco_reservacf, 2, '.', ''),
        'ret_banco_reservacf_bancos' => $ret_banco_reservacf_bancos,
        'ret_reservacf_caja_total' => number_format($ret_reservacf_caja_total, 2, '.', ''),
        'ret_caja_reservacf_total' => number_format($ret_caja_reservacf_total, 2, '.', ''),

        'dep_caja_reservacf' => number_format($dep_caja_reservacf, 2, '.', ''),
        'dep_caja_banco' => number_format($dep_caja_banco, 2, '.', ''),
        'dep_caja_banco_bancos' => $dep_caja_banco_bancos,
        'dep_reservacf_caja' => number_format($dep_reservacf_caja, 2, '.', ''),
        'dep_banco_caja' => number_format($dep_banco_caja, 2, '.', ''),
        'dep_banco_caja_bancos' => $dep_banco_caja_bancos,
        'dep_reservacf_banco' => number_format($dep_reservacf_banco, 2, '.', ''),
        'dep_reservacf_banco_bancos' => $dep_reservacf_banco_bancos,
        'dep_caja_reservacf_total' => number_format($dep_caja_reservacf_total, 2, '.', ''),
        'dep_reservacf_caja_total' => number_format($dep_reservacf_caja_total, 2, '.', ''),
    
        'habilitacion_gestion_liquidez1' => number_format($habilitacion_gestion_liquidez1, 2, '.', ''),
        'habilitacion_gestion_liquidez2' => number_format($habilitacion_gestion_liquidez2, 2, '.', ''),
        'cierre_caja_apertura' => number_format($cierre_caja_apertura, 2, '.', ''),

        'saldos_capitalasignada' => number_format($saldos_capitalasignada, 2, '.', ''),
        'saldos_cuentabanco' => number_format($saldos_cuentabanco, 2, '.', ''),
        'saldos_cuentabanco_bancos' => $saldos_cuentabanco_bancos,
        'saldos_reserva' => number_format($saldos_reserva, 2, '.', ''),
        'saldos_caja' => number_format($saldos_caja, 2, '.', ''),
        'arqueo_caja' => number_format($arqueo_caja, 2, '.', ''),
        'saldos_creditovigente_cnp' => number_format($saldos_creditovigente_cnp, 2, '.', ''),
        'saldos_creditovigente_cp' => number_format($saldos_creditovigente_cp, 2, '.', ''),
        'saldos_creditovigente' => number_format($saldos_creditovigente, 2, '.', ''),
        'saldos_bienescomprados' => number_format($saldos_bienescomprados, 2, '.', ''),
        'saldos_interescreditovigentexcobrar_cnp' => number_format($saldos_interescreditovigentexcobrar_cnp, 2, '.', ''),
        'saldos_interescreditovigentexcobrar_cp' => number_format($saldos_interescreditovigentexcobrar_cp, 2, '.', ''),
        'saldos_interescreditovigentexcobrar' => number_format($saldos_interescreditovigentexcobrar, 2, '.', ''),
        'saldos_ahorros' => number_format($saldos_ahorros, 2, '.', ''),
        'saldos_interesgeneradosxpagar_ahorropf' => number_format($saldos_interesgeneradosxpagar_ahorropf, 2, '.', ''),
        'saldos_interesgeneradosxpagar_interescuentaahorropdfprogramadas' => number_format($saldos_interesgeneradosxpagar_interescuentaahorropdfprogramadas, 2, '.', ''),
        'saldos_interesgeneradosxpagar_ahorrocorriente' => number_format($saldos_interesgeneradosxpagar_ahorrocorriente, 2, '.', ''),
        'saldos_interesgeneradosxpagar_interescuentaahorrocgeneradas' => number_format($saldos_interesgeneradosxpagar_interescuentaahorrocgeneradas, 2, '.', ''),
        'saldos_interesgeneradosxpagar' => number_format($saldos_interesgeneradosxpagar, 2, '.', ''),

        'total_efectivo_ejercicio' => number_format($total_efectivo_ejercicio, 2, '.', ''),
        'incremental_capital_asignado' => number_format($incremental_capital_asignado, 2, '.', ''),
    
        'spread_financiero_proyectado' => number_format($spread_financiero_proyectado, 2, '.', ''),
        'indicador_reserva_legal' => number_format($indicador_reserva_legal, 2, '.', ''),
    
        'validacion_operaciones_cuenta_banco' => $validacion_operaciones_cuenta_banco,
        'efectivo_caja_corte' => number_format($efectivo_caja_corte, 2, '.', ''),
        'efectivo_caja_arqueo' => number_format($efectivo_caja_arqueo, 2, '.', ''),
        'resultado' => number_format($resultado, 2, '.', ''),

    ];
    return $data;
}
function generateRandomString($length = 10) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
return $randomString;
}
// CONSUMIDOR
function consumidor_planadquirido($idusers){
    $planadquirido = DB::table('consumidor_planadquirido') 
        ->join('consumidor_plan','consumidor_plan.id','consumidor_planadquirido.idplan')
        ->where('consumidor_planadquirido.idusers',$idusers)
        ->where('consumidor_planadquirido.idestado',1)
        ->select(
          'consumidor_planadquirido.*',
          'consumidor_plan.nombre as plannombre',
          'consumidor_plan.bono_primeravez as bono_primeravez',
          'consumidor_plan.bono_segundavez as bono_segundavez'
        )
        ->limit(1)
        ->orderBy('consumidor_planadquirido.id','desc')
        ->first();
  
    $estado_planadquirido = 'NINGUNO';
  
    if($planadquirido!=''){
        if($planadquirido->idestadoplanadquirido==1){
            $estado_planadquirido = 'PENDIENTE';
        }elseif($planadquirido->idestadoplanadquirido==2 && $planadquirido->fechafin>=Carbon\Carbon::now()->format("Y-m-d")){
            $estado_planadquirido = 'CORRECTO'; 
        }else{
            $estado_planadquirido = 'VENCIDO';
        }
    }
  
    $icono = url('public/backoffice/sistema/sitioweb/iconored/negro.png');
    if($estado_planadquirido=='PENDIENTE' or $estado_planadquirido=='PENDIENTE_ADELANTADO'){
        $icono = url('public/backoffice/sistema/sitioweb/iconored/celeste.png');
    }elseif($estado_planadquirido=='CORRECTO' or $estado_planadquirido=='CORRECTO_ADELANTADO'){
        $icono = url('public/backoffice/sistema/sitioweb/iconored/verde.png');
    }elseif($estado_planadquirido=='VENCIDO'){
        $icono = url('public/backoffice/sistema/sitioweb/iconored/rojo.png');
    }elseif($estado_planadquirido=='RED'){
        $icono = url('public/backoffice/sistema/sitioweb/iconored/azul.png');
    } 
  
    return [
        'data' => $planadquirido,
        'estado' => $estado_planadquirido,
        'icono' => $icono,
    ];
}
function consumidor_red_abajo($iduserspadre,$data = [],$nivel = 0){
    $red = DB::table('consumidor_red')
        ->leftJoin('users as patrocinador','patrocinador.id','consumidor_red.iduserspatrocinador')
        ->leftJoin('users as padre','padre.id','consumidor_red.iduserspadre')
        ->leftJoin('users as hijo','hijo.id','consumidor_red.idusershijo')
        ->where('consumidor_red.iduserspadre',$iduserspadre)
        ->select(
            'consumidor_red.*',
            'hijo.nombre as hijo_nombre',
            'hijo.apellidos as hijo_apellidos',
            'hijo.numerotelefono as hijo_numerotelefono',
            'hijo.email as hijo_email',
        )
        ->get();
    
    $nivel++;
    foreach($red as $value){
      
        $planadquirido = consumidor_planadquirido($value->idusershijo);
      
        $cantidadirectos  = DB::table('consumidor_red')
            ->where('consumidor_red.idestadored',2)
            ->where('consumidor_red.iduserspadre',$value->idusershijo)
            ->count();
      
        // date_format(date_create($planadquirido['data']->fechafin),"d/m/Y")
        // $planadquirido['data']->plannombre
      
        $data[] = [
            'idestadored' => $value->idestadored,
            'idplanadquirido' => $value->id,
            'iduserspatrocinador' => $value->iduserspatrocinador,
            'iduserspadre' => $value->iduserspadre,
            'idusershijo' => $value->idusershijo,
            'fechaplanadquirido' => $planadquirido['data']!=''?$planadquirido['data']->fechaconfirmacion:'',
            'cantidadirectos' => $cantidadirectos,
            'nivel' => $nivel,
          
            'id' => $value->idusershijo, 
            'icon' => false, 
            'state' => [ 
                'opened' => false 
            ], 
            "parent" => $value->iduserspadre, 
            "text" => '<a href="javascript:;"><img src="'.$planadquirido['icono'].'" class="avatar" >'.$value->hijo_nombre.' ('.$value->hijo_email.')</a>'
        ];
        $data = consumidor_red_abajo($value->idusershijo,$data,$nivel);
    }

    return $data;
} 
function consumidor_red_arriba($idusershijo,$data = [],$cant = 0){
  
    $red = DB::table('consumidor_red')
        ->where('consumidor_red.idusershijo',$idusershijo)
        ->first();
  
    if($red!=''){

        $data[] = [
            'idred' => $red->id,
            'iduserspatrocinador' => $red->iduserspatrocinador,
            'iduserspadre' => $red->iduserspadre,
            'idusershijo' => $red->idusershijo,
            'nivel' => $cant,
        ];

        $cant++;
        if($cant<=11){
            $data = consumidor_red_arriba($red->iduserspadre,$data,$cant);
        }

    }      
    return $data;
} 
function consumidor_puntoskay(){
  //INGRESO 
  $ingreso_puntoscomprados = DB::table('consumidor_puntoskay')
                      ->where('consumidor_puntoskay.idusers',Auth::user()->id)
                      ->where('consumidor_puntoskay.idestadosolicitud',1)
                      ->where('consumidor_puntoskay.idestadopuntoskay',2)
                      ->where('consumidor_puntoskay.idestado',1)
                      ->sum('consumidor_puntoskay.cantidad');
  
  $kayvisitasusuariofontpage =  DB::table('kayvisitasusuariofontpage')
                       ->where('idusersrecepcion',Auth::user()->id)
                       ->where('idestado',1)
                       ->sum('puntoskay');
  $ingreso_kays = $ingreso_puntoscomprados+$kayvisitasusuariofontpage;
  
  // SALIDA
  $kayvisitastiendafontpage =  DB::table('kayvisitastiendafontpage')
                       ->where('idusers',Auth::user()->id)
                       ->where('idestado',1)
                       ->sum('totalpuntoskay');
  
  $kayplanadquirido = DB::table('consumidor_planadquirido') 
                ->where('consumidor_planadquirido.idusers',Auth::user()->id)
                ->where('consumidor_planadquirido.fechaanulacion',NULL)
                ->sum('consumidor_planadquirido.costo');
  $egresokays = $kayvisitastiendafontpage+$kayplanadquirido;
  
  $totalpuntos = $ingreso_kays-$egresokays;
  
  return [
      'total' => $totalpuntos
  ];
}
/*function consumidor_contar_red($iduserspadre,$cant){
    $reds = DB::table('consumidor_planadquirido')
        ->where('consumidor_planadquirido.iduserspadre',$iduserspadre)
        ->where('consumidor_planadquirido.fechaconfirmacion','<>','')
        ->select('consumidor_planadquirido.idusershijo as idusershijo')
        //->orderBy('red.id','asc')
        ->distinct()
        ->get();


        foreach($reds as $value){
            $cant = $cant+1;
            $cant = consumidor_contar_red($value->idusershijo,$cant);
        }
  
        return $cant;
  
}*/
/*function consumidor_reparticion_bono($idusers){
    $planadquirido = consumidor_planadquirido($idusers);
  
    if($planadquirido['data']!=''){

            $countred = DB::table('consumidor_planadquirido') 
                ->where('consumidor_planadquirido.idusershijo',$idusers)
                ->count();
      
            $red = [];
            if($countred==1){
                $primeravez = DB::table('consumidor_bono')->whereId(1)->first();
                $montorecibe = ($planadquirido['data']->costo*$primeravez->porcentaje)/100;
              
                // estado de plan
                $montoperdida = 0.0;
                if($planadquirido['estado']=='PENDIENTE' or $planadquirido['estado']=='PENDIENTE_ADELANTADO'){
                    $montoperdida = $montorecibe;
                    $montorecibe = 0.0;
                }elseif($planadquirido['estado']=='VENCIDO'){
                    $montoperdida = $montorecibe;
                    $montorecibe = 0.0;
                }
              
                $red  = [
                    "idusersrecibe" => $planadquirido['data']->idreduserspatrocinador, 
                    "idusersda" => $idusers, 
                    "monto" => $montorecibe, 
                    "montoperdida" => $montoperdida
                ];
            }else{
                $otrasveces = DB::table('consumidor_bono')->whereId(2)->first();
                $red = consumidor_reparticion_bono_red($planadquirido['data']->idreduserspadre,$planadquirido['data']->costo,$otrasveces->porcentaje,$idusers,10);
            }
            return [
                "red" => $red, 
                "cantidadveces" => $countred, 
            ];
     
    }else{
        $countred = DB::table('consumidor_planadquirido') 
                ->where('consumidor_planadquirido.idusershijo',$idusers)
                ->count();
        return [
            "red" => [], 
            "cantidadveces" => $countred
        ];
    }
}
function consumidor_reparticion_bono_red($idpadre,$bonomonto,$bonoporcentaje,$idusers,$niveles,$red = []){
    
    if($niveles>0){
      
        $planadquiridopadre = DB::table('consumidor_planadquirido') 
            ->where('consumidor_planadquirido.idusershijo',$idpadre)
            ->select(
              'consumidor_planadquirido.costo as costo'
            )
            ->limit(1)
            ->orderBy('consumidor_planadquirido.id','desc')
            ->first();
      
        $bonomontonew = 0;
        if($planadquiridopadre!=''){
            $bonomontonew = $planadquiridopadre->costo;
        }
      
        if($bonomonto>$bonomontonew){
            $bonomonto = $bonomontonew;
        }
  
        $montorecibe = ($bonomonto*$bonoporcentaje)/100;
      
        // estado de plan
        $planadquirido = planadquirido($idpadre);
        $montoperdida = 0.0;
        if($planadquirido['estado']=='PENDIENTE' or $planadquirido['estado']=='PENDIENTE_ADELANTADO'){
            $montoperdida = $montorecibe;
            $montorecibe = 0.0;
        }elseif($planadquirido['estado']=='VENCIDO'){
            $montoperdida = $montorecibe;
            $montorecibe = 0.0;
        }
        
        //dd($bonomonto);
      
        $red[]  = [
            "idusersrecibe" => $idpadre, 
            "idusersda" => $idusers, 
            "monto" => $montorecibe, 
            "montoperdida" => $montoperdida
        ];
        $padre = DB::table('consumidor_planadquirido') 
            ->where('consumidor_planadquirido.idusershijo',$idpadre)
            ->first();
        //dd($idpadre);
        if($padre!=''){
            return consumidor_reparticion_bono_red($padre->iduserspadre,$bonomonto,$bonoporcentaje,$idusers,$niveles-1,$red);
        }else{
            $niveles = 0;
            return $red;
        }
    }else{
        return '---';
    }
}*/
/*function consumidor_padre_red($directos,$cant_directos){
    $idusershijo = 0;
    $array_directos = [];
    foreach($directos as $value){
        $directos2 = DB::table('consumidor_planadquirido')
            ->where('consumidor_planadquirido.iduserspadre',$value['idusershijo'])
            ->where('consumidor_planadquirido.fechaconfirmacion','<>','')
            ->select('consumidor_planadquirido.id','consumidor_planadquirido.idusershijo as idusershijo')
            ->orderBy('consumidor_planadquirido.id','asc')
            ->distinct()
            ->get();
        foreach($directos2 as $value2){
            $array_directos[] = [
                'idusershijo' => $value2->idusershijo
            ];
        }
        if(count($directos2)<$cant_directos){
            $idusershijo = $value['idusershijo'];
            break;
        }
    }
    if($idusershijo==0){
        return consumidor_padre_red($array_directos,$cant_directos);
    }else{
        return $idusershijo;
    }
}*/
/*function consumidor_red($idpadre, $red = []){
   $red  = DB::table('consumidor_planadquirido')
                    ->join('users','users.id','consumidor_planadquirido.idusershijo')
                    ->where('consumidor_planadquirido.iduserspadre',$idpadre)
                    ->select(
                      'users.nombre as nombre',
                      'users.apellidos as apellidos',
                      'users.numerotelefono as numerotelefono',
                      'users.email as email',
                      'consumidor_planadquirido.iduserspatrocinador as rediduserspatrocinador',
                      'consumidor_planadquirido.iduserspadre as rediduserspadre',
                      'consumidor_planadquirido.idusershijo as redidusershijo'
                    )
                    ->distinct()
                    ->get();
  
  foreach($red as $value){
    $planadquirido = consumidor_planadquirido($value->redidusershijo);   

    $counthijo  = DB::table('consumidor_planadquirido')
                    ->where('consumidor_planadquirido.iduserspadre',$value->redidusershijo)
                    ->count();

    $red[]  = [
                //"idred" => $value->idred,
                "id" => $value->redidusershijo, 
                'icon' => false, 
                'state' => [ 'opened' => false ] , 
                "parent" => $value->rediduserspadre, 
                "text" => '<a href="javascript:;"
                    onclick="selectafiliado(this,\''.$value->redidusershijo.'\',\''.$value->nombre.'\',\''.$value->apellidos.'\',\''.$value->numerotelefono.'\',\''.$value->email.'\',\''.$counthijo.'\')">
                    <img src="'.$planadquirido['icono'].'" class="avatar">'.$value->nombre.' '.$value->apellidos.' '.$planadquirido->nombre.'</a>'
              ];
    
    //dd($hijo);
    if($counthijo>0){
      $red = consumidor_red($value->redidusershijo, $red);
    }
  }
  return $red;
}*/

/*function configuracion_comercio($idtienda){
  
    $configuracion_comercio = DB::table('s_configuracioncomercio')
            ->where('s_configuracioncomercio.idtienda',$idtienda)
            ->first();

    $resultado = 'ERROR';
  
    $idcomercio = 0;
    $estadostock = null;
    $nivelventa = null;
    $estadoventa = null;
    $estadounidadmedida = null;
    $estadodescuento = null;
    $estadopreciounitario = null;
    $idtipoentregapordefecto = null;
  
  
    if($configuracion_comercio!=''){
        if($configuracion_comercio->id!=null){
            $resultado = 'CORRECTO';
            $idcomercio = $configuracion_comercio->id;
        }
        if($configuracion_comercio->estadostock!=null){
            $resultado = 'CORRECTO';
            $estadostock = $configuracion_comercio->estadostock;
        }
        if($configuracion_comercio->nivelventa!=null){
            $resultado = 'CORRECTO';
            $nivelventa = $configuracion_comercio->nivelventa;
        }
        if($configuracion_comercio->estadoventa!=null){
            $resultado = 'CORRECTO';
            $estadoventa = $configuracion_comercio->estadoventa;
        }
        if($configuracion_comercio->estadounidadmedida!=null){
            $resultado = 'CORRECTO';
            $estadounidadmedida = $configuracion_comercio->estadounidadmedida;
        }
        if($configuracion_comercio->estadodescuento!=null){
            $resultado = 'CORRECTO';
            $estadodescuento = $configuracion_comercio->estadodescuento;
        }
        if($configuracion_comercio->estadopreciounitario!=null){
            $resultado = 'CORRECTO';
            $estadopreciounitario = $configuracion_comercio->estadopreciounitario;
        }
        if($configuracion_comercio->idtipoentregapordefecto!=null){
            $resultado = 'CORRECTO';
            $idtipoentregapordefecto = $configuracion_comercio->idtipoentregapordefecto;
        }
    }
    
    return [
        'resultado' => $resultado,
        'idcomercio' => $idcomercio,
        'estadostock' => $estadostock,
        'nivelventa' => $nivelventa,
        'estadoventa' => $estadoventa,
        'estadounidadmedida' => $estadounidadmedida,
        'estadodescuento' => $estadodescuento,
        'estadopreciounitario' => $estadopreciounitario,
        'idtipoentregapordefecto' => $idtipoentregapordefecto,
    ];
	
}*/
function configuracion_facturacion($idtienda){

    $configuracion_facturacion = DB::table('s_configuracionfacturacion')
            ->leftJoin('users as cliente','cliente.id','s_configuracionfacturacion.idclientepordefecto')
            ->leftJoin('ubigeo','ubigeo.id','cliente.idubigeo')
            ->where('s_configuracionfacturacion.idtienda',$idtienda)
            ->select(
                's_configuracionfacturacion.*',
                'cliente.direccion as direccion',
                // DB::raw('IF(cliente.idtipopersona=1,
                // CONCAT(cliente.apellidos,", ",cliente.nombre),
                // CONCAT(cliente.apellidos)) as cliente'),
                'cliente.nombrecompleto as cliente',
                'ubigeo.id as idubigeo',
                'ubigeo.nombre as ubigeo',
            )
            ->first();

    $resultado = 'ERROR';
  
    $idfacturacion = 0;
    $igv = null;
    $anchoticket = null;
    $idclientepordefecto = null;
    $idempresapordefecto = null;
    $idcomprobantepordefecto = null;
    $idmonedapordefecto = null;
  
    $clientepordefecto = null;
    $clienteidubigeopordefecto = null;
    $clienteubigeopordefecto = null;
    $clientedireccionpordefecto = null;
  
    if($configuracion_facturacion!=''){
        if($configuracion_facturacion->id!=null){
            $resultado = 'CORRECTO';
            $idfacturacion = $configuracion_facturacion->id;
        }
        if($configuracion_facturacion->igv!=null){
            $resultado = 'CORRECTO';
            $igv = $configuracion_facturacion->igv;
        }
        if($configuracion_facturacion->anchoticket!=null){
            $resultado = 'CORRECTO';
            $anchoticket = $configuracion_facturacion->anchoticket;
        }
        if($configuracion_facturacion->idclientepordefecto!=null){
            $resultado = 'CORRECTO';
            $idclientepordefecto = $configuracion_facturacion->idclientepordefecto;
            $clientepordefecto = $configuracion_facturacion->cliente;
            $clienteidubigeopordefecto = $configuracion_facturacion->idubigeo;
            $clienteubigeopordefecto = $configuracion_facturacion->ubigeo;
            $clientedireccionpordefecto = $configuracion_facturacion->direccion;
        }
        if($configuracion_facturacion->idempresapordefecto!=null){
            $resultado = 'CORRECTO';
            $idempresapordefecto = $configuracion_facturacion->idempresapordefecto;
        }
        if($configuracion_facturacion->idcomprobantepordefecto!=null){
            $resultado = 'CORRECTO';
            $idcomprobantepordefecto = $configuracion_facturacion->idcomprobantepordefecto;
        }
        if($configuracion_facturacion->idmonedapordefecto!=null){
            $resultado = 'CORRECTO';
            $idmonedapordefecto = $configuracion_facturacion->idmonedapordefecto;
        }
    }
    
    return [
        'resultado' => $resultado,
        'idfacturacion' => $idfacturacion,
        'igv' => $igv,
        'anchoticket' => $anchoticket,
        'idclientepordefecto' => $idclientepordefecto,
        'idempresapordefecto' => $idempresapordefecto,
        'idcomprobantepordefecto' => $idcomprobantepordefecto,
        'idmonedapordefecto' => $idmonedapordefecto,
        'clientepordefecto' => $clientepordefecto,
        'clienteidubigeopordefecto' => $clienteidubigeopordefecto,
        'clienteubigeopordefecto' => $clienteubigeopordefecto,
        'clientedireccionpordefecto' => $clientedireccionpordefecto,
    ];
	
}

function negocio_planadquirido($tienda,$idusers=0){
    $planadquirido = DB::table('pagotienda') 
        ->where('pagotienda.idtienda',$tienda)
        ->limit(1)
        ->orderBy('pagotienda.id','desc')
        ->first();
    $fechaactual = Carbon\Carbon::now()->format("Y-m-d");
    $estado_planadquirido = '';
    if($planadquirido==''){
        if($idusers==1){
            $estado_planadquirido = 'CORRECTO';
        }else{
            $estado_planadquirido = 'NINGUNO';
        }
    }else{
        if($planadquirido->idestado==1 && $planadquirido->fechainicio<=$fechaactual && $planadquirido->fechafin>=$fechaactual){
            $estado_planadquirido = 'PENDIENTE';
        }elseif($planadquirido->idestado==2 && $planadquirido->fechainicio<=$fechaactual && $planadquirido->fechafin>=$fechaactual){
            $estado_planadquirido = 'CORRECTO';
        }else{
            $estado_planadquirido = 'VENCIDO';
        }
    }
    return [
        'data' => $planadquirido,
        'estado' => $estado_planadquirido
    ];
}
function stock_oferta($idoferta){
    $oferta = DB::table('oferta')
      ->whereId($idoferta)
      ->first();
    $countreservaoferta1 = DB::table('reservaoferta')
      ->where('idoferta',$idoferta)
      ->where('idestadooferta',1)
      ->sum('reservaoferta.cantidad');
    $countreservaoferta2 = DB::table('reservaoferta')
      ->where('idoferta',$idoferta)
      ->where('idestadooferta',2)
      ->sum('reservaoferta.cantidad');
    $countreservaoferta = $countreservaoferta1+$countreservaoferta2;
    if($oferta->stock==0){
        $stockactual = 10000000000;
    }else{
        $stockactual = $oferta->stock-$countreservaoferta;
    }
    return [
        'stockactual' => $stockactual,
        'stockconsumido' => $countreservaoferta2,
        'stockreserva' => $countreservaoferta1
    ];
}
// fin presentaciones
/* ---------------------  Subir Imagen -------------------*/
function subir_archivo($ruta,$file_imagen_nueva,$text_imagen_actual,$text_imagen_anterior){

    $nombre_imagen_list = [];
    if($file_imagen_nueva!=null){
        $num = 0;
        foreach($file_imagen_nueva as $value){
            if ($value->isValid()) { 
                $nueva_ruta = getcwd().$ruta;
                $nombre_imagen =  Carbon\Carbon::now()->format('dmYhms').rand(100000, 999999).'.png';
                resize_img($value->getRealPath(),1500,1500,$nueva_ruta,$nombre_imagen);
                $nombre_imagen_list[] = [
                    'num' => $num,
                    'imagen' => $nombre_imagen
                ];  
                $num++; 
            }
        }  
    }   
   
    if(count($nombre_imagen_list)>1){
        $nombre_imagen_list = $nombre_imagen_list;
    }elseif(count($nombre_imagen_list)==1){
        $nombre_imagen_list = $nombre_imagen_list[0]['imagen'];
        if($text_imagen_anterior==''){
            uploadfile_eliminar($text_imagen_actual,$ruta);
        }
    }else{
        $nombre_imagen_list = $text_imagen_anterior;
        if($text_imagen_anterior==''){
            uploadfile_eliminar($text_imagen_actual,$ruta);
            $nombre_imagen_list = '';
        }
        
    }
    return $nombre_imagen_list;
}


function uploadfile($text_imagen_eliminar,$text_imagen_anterior,$file_imagen_nueva,$ruta,$ancho=900,$altura=900){
    if($text_imagen_anterior!='') {
        $imagen = $text_imagen_anterior;
    }else{
        if($text_imagen_eliminar!=''){
            uploadfile_eliminar($text_imagen_eliminar,$ruta);
        }
        $imagen = '';
        if($file_imagen_nueva!='') {
            if(is_array($file_imagen_nueva)){
                foreach($file_imagen_nueva as $value){
                    if ($value->isValid()) { 
                        $estructura = getcwd().$ruta;
                        $extension = $value->extension();
                        $imagen =  Carbon\Carbon::now()->format('dmYhms').rand(100000, 999999).'.'.$extension;
                        resize_img($value->getRealPath(),$ancho,$altura,$estructura,$imagen);
                    }
                } 
            }else{
                if ($file_imagen_nueva->isValid()) { 
                    $estructura = getcwd().$ruta;
                    $extension = $file_imagen_nueva->extension();
                    $imagen =  Carbon\Carbon::now()->format('dmYhms').rand(100000, 999999).'.'.$extension;
                    resize_img($file_imagen_nueva->getRealPath(),$ancho,$altura,$estructura,$imagen);
                }
            }
                
        }
    }
    return $imagen;
}
function uploadfile_eliminar($text_imagen_eliminar,$ruta){
    $rutaimagen = getcwd().$ruta.$text_imagen_eliminar;
    if(file_exists($rutaimagen) && $text_imagen_eliminar!='') {
        unlink($rutaimagen);
    }
}
function resize_img($make,$ancho,$altura,$estructura,$imagen){
    if(!file_exists($estructura)){
        mkdir($estructura, 0777, true);
    }
    $resize_image = Image::make($make);
    $resize_image->resize($ancho, $altura, function($constraint){
        $constraint->aspectRatio();
        $constraint->upsize();
    })->save($estructura.$imagen); 
}
/*function resize_img_copy($make,$ancho,$altura,$estructura,$imagen){
    if(!file_exists($estructura)){
        mkdir($estructura, 0777, true);
    }
    $image = Image::make($make);
    $image->destination($estructura.$imagen);
    $image->Manipulate->Resize($ancho,$altura);
    $image->output();
    $image->clean();
}*/
function duplicar_fichero($ruta,$fichero,$rutanueva=''){
    if($rutanueva==''){
        $rutanueva = $ruta;
    }
    if($fichero!=''&& file_exists(getcwd().$ruta.$fichero)){
        $nuevo_fichero =  Carbon\Carbon::now()->format('dmYhms').rand(100000, 999999).'.png';
        copy(getcwd().$ruta.$fichero, getcwd().$rutanueva.$nuevo_fichero);
        return $nuevo_fichero;
    }else{
        return '';
    }
}
function obtener_dominio_perzonalizado(){
    $http_host = '';
    if(isset($_SERVER["HTTP_HOST"])){
        $http_host = $_SERVER["HTTP_HOST"]; 
        $htttp_list = explode('www.', $_SERVER["HTTP_HOST"]);
        if(count($htttp_list)>1){
            $http_host = $htttp_list[1];
        }
    }  
  
    $tienda = DB::table('tienda')
          ->where('dominio_personalizado','<>','')
          ->where('dominio_personalizado',$http_host)
          ->limit(1)
          ->first();
  
    return $tienda;
}
/* ---------------------  LINK COMPARTIDO -------------------*/
function validar_linkpuntoskay($cod_user,$referencia,$idtienda){
    $user = DB::table('users')
        ->where('email',$cod_user)
        ->limit(1)
        ->first();
    if($user!=''){
        $countkayvisitasusuariofontpage = DB::table('kayvisitasusuariofontpage')
            ->where('fecha',Carbon\Carbon::now()->format('Y-m-d'))
            ->where('ipaddress',$_SERVER['REMOTE_ADDR'])
            ->where('idtienda',$idtienda)
            ->where('idusersrecepcion',$user->id)
            ->where('idestado',1)
            ->count();
        if($countkayvisitasusuariofontpage==0){
            $kayvisitastiendafontpage = DB::table('kayvisitastiendafontpage')
                ->where('idtienda',$idtienda)
                ->where('fechaconfirmacion','<>','')
                ->where('idestado',1)
                ->limit(1)
                ->first();
            if($kayvisitastiendafontpage!=''){
                $kays = $kayvisitastiendafontpage->puntoskay/$kayvisitastiendafontpage->cantidad;
                $idusersenvio = $user->id;
            }else{
                $configkayvisitastienda = DB::table('configkayvisitastienda')
                    ->whereId(1)
                    ->first();
                $kays = $configkayvisitastienda->puntoskay/$configkayvisitastienda->cantidad;
                $idusersenvio = 1;
            }
            DB::table('kayvisitasusuariofontpage')->insert([
                'fecha' => Carbon\Carbon::now(),
                'fecharegistro' => Carbon\Carbon::now(),
                'ipaddress' => $_SERVER['REMOTE_ADDR'],
                'link' => url()->full(),
                'referencia' => $referencia,
                'puntoskay' => $kays,
                'idtienda' => $idtienda,
                'idusersenvio' => $idusersenvio,
                'idusersrecepcion' => $user->id,
                'idestado' => 1
            ]);    
        }
    }
}

function redimensionar($imagefile, $width, $height, $displ='center'){
    $isize = getimagesize($imagefile);
    //print_r($isize['mime']);
    if($isize['mime']=='image/jpg'){
        $image = imagecreatefromjpg($imagefile);
    }elseif($isize['mime']=='image/jpeg'){
        $image = imagecreatefromjpeg($imagefile);
    }elseif($isize['mime']=='image/gif'){
        $image = imagecreatefromgif($imagefile);
    }elseif($isize['mime']=='image/png'){
        $image = imagecreatefrompng($imagefile);
    }elseif($isize['mime']=='image/webp'){
        $image = imagecreatefromwebp($imagefile);
    }

    $origw = imagesx($image);
    $origh = imagesy($image);
    $ratiow = $width / $origw;
    $ratioh = $height / $origh;
    $ratio = max($ratioh, $ratiow);
    $neww = $origw * $ratio;
    $newh = $origh * $ratio;
    $cropw = $neww-$width;
    $croph = $newh-$height;
    if ($displ=='center'){
        $displ=0.5;
    }elseif ($displ=='min'){
        $displ=0;
    }elseif ($displ=='max'){
        $displ=1;
    }
    $new = imageCreateTrueColor($width, $height);
    imagecopyresampled($new, $image, -$cropw*$displ, -$croph*$displ, 0, 0, $width+$cropw, $height+$croph, $origw, $origh);
    header('Content-type: image/jpeg');
    imagejpeg($new);
}
function eliminardirectorio($src) {
    if(file_exists($src)) {
        $dir = opendir($src);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                $full = $src . '/' . $file;
                if ( is_dir($full) ) {
                    eliminardirectorio($full);
                }
                else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($src);
    }
}
/* ---------------------  cunsulat DNI Y RUC -------------------*/
use Peru\Sunat\RucFactory;
use Peru\Jne\DniFactory;

function consumeApiConsultWithCurl($url, $token, $data = null)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url . $data,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 2,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
          'Authorization: Bearer ' . $token
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response);
}

function consultaDniRuc($numeroIdentificacion, $idTipoPersona) {
    $token_api = '66680370a5b34bb07f5d2abd588089d5540536e7db4580f3146b89f71267d982';
    $url_api_dni = 'https://www.apisperu.net/api/dni/';
    $url_api_ruc = 'https://www.apisperu.net/api/ruc/';
  
    if ($idTipoPersona == 1) {
        if($numeroIdentificacion==0){
            return [
                'nombres'         => '',
                'apellidoPaterno' => '',
                'apellidoMaterno' => '',
                'nombrecompleto' => '',
                'direccion' => '',
            ];
        }else{
            $factory = new DniFactory();
            $cs      = $factory->create();
            $person  = $cs->get($numeroIdentificacion);

         
            $direcion = 'S/N';
            $users = DB::table('users')->where('users.identificacion',$numeroIdentificacion)->first();
            if($users!=''){
                if($users->direccion=='' or $users->direccion=='-'){
                    $direcion = 'S/N';
                }else{
                    $direcion = $users->direccion;
                }
            }
          
            if (!$person) {
                $person = consumeApiConsultWithCurl($url_api_dni, $token_api, $numeroIdentificacion);
                if (!$person) {
                    return [
                        'resultado' => 'ERROR',
                        'mensaje' => 'No existen resultados para este Numero de DNI.'
                    ];
                    exit();
                }
              
              
         
                $direcion = 'S/N';
                $users = DB::table('users')->where('users.identificacion',$numeroIdentificacion)->first();
                if($users!=''){
                    if($users->direccion=='' or $users->direccion=='-'){
                        $direcion = 'S/N';
                    }else{
                        $direcion = $users->direccion;
                    }
                }
              
                $person = [
                    'nombres'         => $person->data->nombres,
                    'apellidoPaterno' => $person->data->apellido_paterno,
                    'apellidoMaterno' => $person->data->apellido_materno,
                    'nombrecompleto'  => $person->data->apellido_materno.' '.$person->data->apellido_materno.', '.$person->data->nombres,
                    'direccion'       => $direcion,
                    /*'departamento'    => 'NONE',
                    'provincia'       => 'NONE',
                    'distrito'        => 'NONE',
                    'codigo'          => 'NONE',*/
                ];
            }else{
          
            $person = [
                'nombres'         => $person->nombres,
                'apellidoPaterno' => $person->apellidoPaterno,
                'apellidoMaterno' => $person->apellidoMaterno,
                'nombrecompleto'  => $person->apellidoPaterno.' '.$person->apellidoMaterno.', '.$person->nombres,
                'direccion'       => $direcion,
            ];
            }
            return $person;
        }
            
    }else if ($idTipoPersona == 2) {
        if($numeroIdentificacion==0){
            return [
                'ruc'             => '',
                'razonSocial'     => '',
                'nombreComercial' => '',
                'direccion'       => '',
                'departamento'    => '',
                'provincia'       => '',
                'distrito'        => '',
                'idubigeo'        => '',
                'ubigeo'          => '',
            ];
        }else{
            $factory = new RucFactory();
            $cs      = $factory->create();
            $company = $cs->get($numeroIdentificacion);

            if (!$company) {
                $company = consumeApiConsultWithCurl($url_api_ruc, $token_api, $numeroIdentificacion);

                if (!$company->success) {
                    return [
                        'resultado' => 'ERROR',
                        'mensaje' => 'No existen resultados para este Numero de RUC.'
                    ];
                    exit();
                }
                return [
                    'ruc'             => $company->data->ruc,
                    'razonSocial'     => $company->data->razonSocial,
                    'nombreComercial' => $company->data->nombre_o_razon_social,
                    'direccion'       => $company->data->direccionCompleta,
                    'nombrecompleto'  => $person->data->razonSocial,
                ];
            }    

            $companyUbigeo = $company->distrito.' - '.$company->provincia.' - '.$company->departamento;

            $ubigeo = DB::table('ubigeo')->where('ubigeo.nombre', 'like', '%'.$companyUbigeo.'%')->first();

            $direcion = $company->direccion;
            if($company->direccion=='' or $company->direccion=='-'){
                $users = DB::table('users')->where('users.identificacion',$company->ruc)->first();
                if($users!=''){
                    if($users->direccion=='' or $users->direccion=='-'){
                        $direcion = 'S/N';
                    }else{
                        $direcion = $users->direccion;
                    }
                }
            }
          
            return [
                'ruc'             => $company->ruc,
                'razonSocial'     => $company->razonSocial,
                'nombreComercial' => ($company->nombreComercial=='-'?'':$company->nombreComercial),
                'nombrecompleto'  => $company->razonSocial,
                'direccion'       => $direcion,
                'departamento'    => !is_null($company->departamento) ? $company->departamento : 'NONE',
                'provincia'       => !is_null($company->provincia) ? $company->provincia : 'NONE',
                'distrito'        => !is_null($company->distrito) ? $company->distrito : 'NONE',
                'codigo'          => !is_null($ubigeo) ? $ubigeo->codigo : '',
                'idubigeo'        => !is_null($ubigeo) ? $ubigeo->id : '',
                'ubigeo'          => !is_null($ubigeo) ? $ubigeo->nombre : '',
            ];
        }
    }
}
/* ---------------------  FIN cunsulat DNI Y RUC -------------------*/
/* ---------------------  CARGAR PRODUCTOS JSON -------------------*/
/*function load_json_productos($idtienda){
        $productos = DB::table('s_producto')
            ->join('tienda','tienda.id','s_producto.idtienda')
            ->leftJoin('s_categoria','s_categoria.id','s_producto.s_idcategoria1')
            ->leftJoin('s_categoria as subcategoria','subcategoria.id','s_producto.s_idcategoria2')
            ->leftJoin('s_marca','s_marca.id','s_producto.s_idmarca')
            ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
            ->where('s_producto.idtienda',$idtienda)
            ->where('s_producto.s_idestado',1)
            ->select(
              's_producto.id as id',
              's_producto.codigo as codigo',
              's_producto.nombre as nombre',
              's_producto.precioalpublico as precioalpublico',
              's_producto.s_idestadodetalle as idestadodetalle',
              's_producto.s_idestado as idestado',
              's_producto.s_idestadotiendavirtual as idestadotv',
               DB::raw('CONCAT(unidadmedida.nombre," x ",s_producto.por) as unidadmedida'),
               DB::raw('CONCAT(s_producto.nombre," / ",s_producto.precioalpublico) as text'),
               'tienda.id as idtienda',
               'tienda.nombre as tiendanombre',
               'tienda.link as tiendalink',
               's_marca.nombre as marcanombre',
               's_categoria.nombre as categorianombre',
               DB::raw('(SELECT imagen FROM s_productogaleria WHERE s_idproducto=s_producto.id ORDER BY orden ASC LIMIT 1) as imagen')
            )
            ->orderBy('s_producto.id','desc')
            ->get();
  
        //$json_string = json_encode($productos);
        $directorio = getcwd().'/public/backoffice/tienda/'.$idtienda.'/productojson';
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777);
        }
        $file = $directorio.'/productos.json';
  
        $json_string = json_encode(
          array(
            'data' => $productos
          )
        );
        file_put_contents($file, $json_string);
}
function load_json_productos_descuento($idtienda){
        $descuento_productos = descuento_producto($idtienda)['data'];
        $json_string = json_encode($descuento_productos);
        $directorio = getcwd().'/public/backoffice/tienda/'.$idtienda.'/productojson';
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777);
        }
        $file = $directorio.'/productos_descuento.json';
  
        $json_string = json_encode(
          array(
            'data' => $descuento_productos
          )
        );
        file_put_contents($file, $json_string);
}*/
/* --------------------- FIN CARGAR PRODUCTOS JSON -------------------*/

/* ---------- Convertir Mes a Texto en Español ---------- */
function mesesEs($mes) {
    $month = [
        '1' => 'Enero',
        '2' => 'Febrero',
        '3' => 'Marzo',
        '4' => 'Abril',
        '5' => 'Mayo',
        '6' => 'Junio',
        '7' => 'Julio',
        '8' => 'Agosto',
        '9' => 'Setiembre',
        '10' => 'Octubre',
        '11' => 'Noviembre',
        '12' => 'Diciembre'
    ];
    return $month[$mes] ?? '';
}

function generar_url($str)
{
    $res = strtolower(str_replace(' ','-',$str));
    $res = preg_replace('/[0-9\@\.\;\" "]+/', '', $res);
    return $res;
}
