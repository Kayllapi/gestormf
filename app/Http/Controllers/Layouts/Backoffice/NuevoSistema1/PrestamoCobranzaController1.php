<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class PrestamoCobranzaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $prestamocobranzas = DB::table('s_prestamo_cobranza')
          ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_cobranza.idprestamo_credito')
          ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
          ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
          ->where([
            ['s_prestamo_cobranza.idtienda', $idtienda]
          ])
          ->select(
            's_prestamo_cobranza.*',
            'cliente.nombre as cliente_nombre',
            'asesor.nombre as asesor_nombre',
            DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente')
          )
          ->orderBy('s_prestamo_cobranza.id','desc')
          ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/prestamocobranza/index', [
            'tienda'      => $tienda,
            'prestamocobranzas' => $prestamocobranzas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda   = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/sistema/prestamocobranza/create', [
            'tienda' => $tienda
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
          
            $rules = [
                'idprestamo_credito' => 'required',
                'idtipopago' => 'required',
            ];
          
            if($request->input('idtipopago')==1){
                $rules = array_merge($rules,[
                    'hastacuota' => 'required',
                    'montorecibido' => 'required'
                ]);
              
            }
            if($request->input('check_moradescuento')=='on'){
                $rules = array_merge($rules,[
                    'moradescuento' => 'required',
                    'moradescuento_detalle' => 'required'
                ]);
            }
            if($request->input('idtipopago')==2){
                $rules = array_merge($rules,[
                    'montocompleto' => 'required'
                ]);
            }
          
            $messages = [
                'idprestamo_credito.required' => 'El "Cliente" es Obligatorio.',
                'idtipopago.required' => 'El "Tipo de Pago" es Obligatorio.',
                'moradescuento.required' => 'El "Total de Moras" es Obligatorio.',
                'moradescuento_detalle.required' => 'El "Motivo de descuento" es Obligatorio.',
                'montocompleto.required' => 'El "Monto Recibido" es Obligatorio.',
                'hastacuota.required' => 'El "Hasta Cuota" es Obligatorio.',
                'montorecibido.required' => 'El "Monto Recibido" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);
          
            $cronograma = prestamo_cobranza_cronograma($idtienda,$request->idprestamo_credito,$request->moradescuento,$request->montocompleto,$request->idtipopago,$request->hastacuota);
          
            $moradescuento = 0;
            if($request->input('check_moradescuento')=='on'){
                if($cronograma['select_mora'] < $request->moradescuento) {
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Descontar Mora" debe ser menor o igual que al "Total de Moras".'
                    ]);
                }
                $moradescuento = $request->moradescuento;
            }
          
            
            $montocompleto = 0;
            $montorecibido = 0;
            $hastacuota = 0;
            if($request->idtipopago == 1) {
                if($cronograma['select_cuotaapagarredondeado'] > $request->montorecibido) {
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Monto Recibido" debe ser mayor o igual que el Redondeado.'
                    ]);
                }
                $montorecibido = $request->montorecibido;
                $hastacuota = $request->hastacuota;
            }elseif($request->idtipopago == 2) {
                $montocompleto = $request->montocompleto;
            }
                
                $idprestamocobranza = DB::table('s_prestamo_cobranza')->insertGetId([
                    'fecharegistro' => Carbon::now(),
                    'codigo' => Carbon::now()->format('ymdhms'),
                    'cronograma_moradescuento' => $moradescuento,
                    'cronograma_montocompleto' => $montocompleto,
                    'cronograma_montorecibido' => $montorecibido,
                    'cronograma_vuelto' => $request->vuelto!=''?$request->vuelto:0,
                    'cronograma_idtipopago' => $request->idtipopago,
                    'cronograma_hastacuota' => $hastacuota,
                    'select_cuota' => $cronograma['select_cuota'],
                    'select_atraso' => $cronograma['select_atraso'],
                    'select_mora' => $cronograma['select_mora'],
                    'select_moradescontado' => $cronograma['select_moradescontado'],
                    'select_moraapagar' => $cronograma['select_moraapagar'],
                    'select_cuotapago' => $cronograma['select_cuotapago'],
                    'select_acuenta' => $cronograma['select_acuenta'],
                    'select_cuotaapagar' => $cronograma['select_cuotaapagar'],
                    'select_cuotaapagarredondeado' => $cronograma['select_cuotaapagarredondeado'],
                    'total_cuota' => $cronograma['total_pendiente_cuota'],
                    'total_atraso' => $cronograma['total_pendiente_atraso'],
                    'total_mora' => $cronograma['total_pendiente_mora'],
                    'total_moradescontado' => $cronograma['total_pendiente_moradescontado'],
                    'total_moraapagar' => $cronograma['total_pendiente_moraapagar'],
                    'total_cuotapago' => $cronograma['total_pendiente_cuotapago'],
                    'total_acuenta' => $cronograma['total_pendiente_acuenta'],
                    'total_cuotaapagar' => $cronograma['total_pendiente_cuotaapagar'],
                    'idprestamo_credito' => $request->idprestamo_credito,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
       
                foreach ($cronograma['cuotas_pendientes_seleccionados'] as $value) {
                    DB::table('s_prestamo_cobranzadetalle')->insert([
                        'idprestamo_cobranza' => $idprestamocobranza,
                        'idprestamo_creditodetalle' => $value['idprestamo_creditodetalle'],
                    ]);
                    /* idestadocobranza
                     * 1 = cuota pendiente
                     * 2 = cuota cancelado
                    */
                    $idestadocobranza = 2;
                    if($value['estado'] == 'ACUENTA'){
                        $idestadocobranza = 1;
                    }
                    DB::table('s_prestamo_creditodetalle')->whereId($value['idprestamo_creditodetalle'])->update([
                        'atraso' => $value['atraso'],
                        'moradescuento' => $value['moradescontado'],
                        'moraapagar' => $value['moraapagar'],
                        'cuotapago' => $value['cuotapago'],
                        'acuenta' => $value['acuenta'],
                        'cuotaapagar' => $value['cuotaapagar'],
                        'idestadocobranza' => $idestadocobranza
                    ]);
                }

          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      if ($id == 'show-creditosolicitud') {

          $cronograma = prestamo_cobranza_cronograma($idtienda,$request->idprestamo_credito,$request->moradescuento,$request->montocompleto,$request->idtipopago,$request->hastacuota);

          // CUOTAS PENDIENTES
          $cobranzapendiente = '<table class="table" id="table-cobranzapendiente">
                                    <thead style="background: #31353d; color: #fff;">
                                        <tr>
                                            <td style="padding: 8px;text-align: right;">Nº</td>
                                            <td style="padding: 8px;text-align: right;">Vencimiento</td>
                                            <td style="padding: 8px;text-align: right;">Cuota</td>
                                            <td style="padding: 8px;text-align: right;">Atraso</td>
                                            <td style="padding: 8px;text-align: right;">Mora</td>
                                            <td style="padding: 8px;text-align: right;">Mora D.</td>
                                            <td style="padding: 8px;text-align: right;">Mora P.</td>
                                            <td style="padding: 8px;text-align: right;">Total</td>
                                            <td style="padding: 8px;text-align: right;">A cuenta</td>
                                            <td style="padding: 8px;text-align: right;">Pagar</td>
                                        </tr>
                                    </thead>
                                    <tbody>';

          foreach($cronograma['cuotas_pendientes'] as $value) {

              $cobranzapendiente .= '<tr style="background-color: '.$value['tabla_colortr'].';" '.$value['tabla_class'].'>
                                            <td style="padding: 8px;text-align: right;width: 10px;">'.$value['tabla_numero'].'</td>
                                            <td style="padding: 8px;text-align: right;width: 90px;">'.$value['tabla_fechavencimiento'].'</td>
                                            <td style="padding: 8px;text-align: right;">'.$value['tabla_cuota'].'</td>
                                            <td style="padding: 8px;text-align: right;">'.$value['tabla_atraso'].' días</td>
                                            <td style="padding: 8px;text-align: right;">'.$value['tabla_mora'].'</td>
                                            <td style="padding: 8px;text-align: right;background-color: #ff1f43;color: white;">'.$value['tabla_moradescontado'].'</td>
                                            <td style="padding: 8px;text-align: right;background-color: #0ec529;color: white;">'.$value['tabla_moraapagar'].'</td>
                                            <td style="padding: 8px;text-align: right;background-color: orange;color: white;">'.$value['tabla_cuotatotal'].'</td>
                                            <td style="padding: 8px;text-align: right;">'.$value['tabla_acuenta'].'</td>
                                            <td style="padding: 8px;text-align: right;">'.$value['tabla_cuotaapagar'].'</td>
                                        </tr>';

          }
          $cobranzapendiente .= '</tbody>
                                 <tfoot style="background: #31353d; color: #fff;">
                                    <tr>
                                        <td style="padding: 8px;text-align: right;" colspan="2">TOTAL</td>
                                        <td style="padding: 8px;text-align: right;">'.$cronograma['total_pendiente_cuota'].'</td>
                                        <td style="padding: 8px;text-align: right;">'.$cronograma['total_pendiente_atraso'].' días</td>
                                        <td style="padding: 8px;text-align: right;">'.$cronograma['total_pendiente_mora'].'</td>
                                        <td style="padding: 8px;text-align: right;">'.$cronograma['total_pendiente_moradescontado'].'</td>
                                        <td style="padding: 8px;text-align: right;">'.$cronograma['total_pendiente_moraapagar'].'</td>
                                        <td style="padding: 8px;text-align: right;">'.$cronograma['total_pendiente_cuotapago'].'</td>
                                        <td style="padding: 8px;text-align: right;">'.$cronograma['total_pendiente_acuenta'].'</td>
                                        <td style="padding: 8px;text-align: right;">'.$cronograma['total_pendiente_cuotaapagar'].'</td>
                                    </tr>
                                 </tfoot>
                              </table>';


          // CUOTAS CANCELADAS

          $cobranzacancelada = '<table class="table" id="table-cobranzacancelada">
                                    <thead style="background: #31353d; color: #fff;">
                                        <tr>
                                            <td style="padding: 8px;text-align: right;">Nº</td>
                                            <td style="padding: 8px;text-align: right;">Vencimiento</td>
                                            <td style="padding: 8px;text-align: right;">Cuota</td>
                                            <td style="padding: 8px;text-align: right;">Atraso</td>
                                            <td style="padding: 8px;text-align: right;">Mora</td>
                                            <td style="padding: 8px;text-align: right;">Mora D.</td>
                                            <td style="padding: 8px;text-align: right;">Mora P.</td>
                                            <td style="padding: 8px;text-align: right;">Total</td>
                                            <td style="padding: 8px;text-align: right;">A cuenta</td>
                                            <td style="padding: 8px;text-align: right;">Pagado</td>
                                        </tr>
                                    </thead>
                                    <tbody>';

          foreach($cronograma['cuotas_canceladas'] as $value) {

              $cobranzacancelada .= '<tr>
                                            <td style="padding: 8px;text-align: right;width: 10px;">'.$value['tabla_numero'].'</td>
                                            <td style="padding: 8px;text-align: right;width: 90px;">'.$value['tabla_fechavencimiento'].'</td>
                                            <td style="padding: 8px;text-align: right;">'.$value['tabla_cuota'].'</td>
                                            <td style="padding: 8px;text-align: right;">'.$value['tabla_atraso'].' días</td>
                                            <td style="padding: 8px;text-align: right;">'.$value['tabla_mora'].'</td>
                                            <td style="padding: 8px;text-align: right;background-color: #ff1f43;color: white;">'.$value['tabla_moradescontado'].'</td>
                                            <td style="padding: 8px;text-align: right;background-color: #0ec529;color: white;">'.$value['tabla_moraapagar'].'</td>
                                            <td style="padding: 8px;text-align: right;background-color: orange;color: white;">'.$value['tabla_cuotatotal'].'</td>
                                            <td style="padding: 8px;text-align: right;">'.$value['tabla_acuenta'].'</td>
                                            <td style="padding: 8px;text-align: right;">'.$value['tabla_cuotaapagar'].'</td>
                                        </tr>';

          }
          $cobranzacancelada .= '</tbody>
                                 <tfoot style="background: #31353d; color: #fff;">
                                    <tr>
                                        <td style="padding: 8px;text-align: right;" colspan="2">TOTAL</td>
                                        <td style="padding: 8px;text-align: right;">'.$cronograma['total_cancelada_cuota'].'</td>
                                        <td style="padding: 8px;text-align: right;">'.$cronograma['total_cancelada_atraso'].' días</td>
                                        <td style="padding: 8px;text-align: right;">'.$cronograma['total_cancelada_mora'].'</td>
                                        <td style="padding: 8px;text-align: right;">'.$cronograma['total_cancelada_moradescontado'].'</td>
                                        <td style="padding: 8px;text-align: right;">'.$cronograma['total_cancelada_moraapagar'].'</td>
                                        <td style="padding: 8px;text-align: right;">'.$cronograma['total_cancelada_cuotapago'].'</td>
                                        <td style="padding: 8px;text-align: right;">'.$cronograma['total_cancelada_acuenta'].'</td>
                                        <td style="padding: 8px;text-align: right;">'.$cronograma['total_cancelada_cuotaapagar'].'</td>
                                    </tr>
                                 </tfoot>
                              </table>';

          // PAGOS REALIZADOS

          $s_prestamo_cobranzadetalle = DB::table('s_prestamo_cobranza')
              ->where('s_prestamo_cobranza.idprestamo_credito', $request->idprestamo_credito)
              ->orderBy('s_prestamo_cobranza.id', 'asc')
              ->get();


          $pagorealizado = '<table class="table" id="table-cobranzapendiente">
                              <thead style="background: #31353d; color: #fff;">
                                  <tr>
                                      <td style="padding: 8px;">Código</td>
                                      <td style="padding: 8px;">Fecha de Pago</td>
                                      <td style="padding: 8px;">Total</td>
                                      <td style="padding: 8px;" width="10px">Estado</td>
                                      <td style="padding: 8px;" width="10px"></td>
                                  </tr>
                              </thead>
                              <tbody>';

        $cuotaapagarredondeado = '0.00';

          foreach($s_prestamo_cobranzadetalle as $value) {

              $stado = '';
              $input_anular = '';
              if($value->idestado==1){
                $stado = '<div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fas fa-check"></i> Correcto</span></div> ';
                $input_anular = '<li>
                                    <a href="javascript:;" onclick="anular_pagorealizado('.$value->id.');">
                                      <i class="fa fa-ban"></i> Anular
                                    </a>
                                  </li>';
              }elseif($value->idestado==2){
               $stado = '<div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span></div>';
              }

              $pagorealizado .= '<tr>
                                    <td style="padding: 8px;">'.$value->codigo.'</td>
                                    <td style="padding: 8px;">'.date_format(date_create($value->fecharegistro),"d/m/Y h:i:s A").'</td>
                                    <td style="padding: 8px;text-align: right;width: 60px;"">'.$value->select_cuotaapagarredondeado.'</td>
                                    <td>'.$stado.'</td>
                                    <td>
                                    <div class="header-user-menu menu-option" id="menu-opcion">
                                        <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                                        <ul>
                                            <li>
                                              <a href="javascript:;" onclick="detalle_pagorealizado('.$value->id.');">
                                                <i class="fa fa-list-alt"></i> Detalle
                                              </a>
                                            </li>
                                            <li>
                                              <a href="javascript:;" onclick="ticket_pagorealizado('.$value->id.');">
                                                <i class="fa fa-receipt"></i> Ticket
                                              </a>
                                            </li>
                                            '.$input_anular.'
                                        </ul>
                                    </div>
                                    </td>
                                </tr>';
              $cuotaapagarredondeado = $cuotaapagarredondeado+$value->select_cuotaapagarredondeado;
          }
          $pagorealizado .= '</tbody>
                                 <tfoot style="background: #31353d; color: #fff;">
                                    <tr>
                                        <td style="padding: 8px;text-align: right;" colspan="2">TOTAL</td>
                                        <td style="padding: 8px;text-align: right;">'.number_format($cuotaapagarredondeado, 2, '.', '').'</td>
                                        <td style="padding: 8px;text-align: right;"></td>
                                        <td style="padding: 8px;text-align: right;"></td>
                                    </tr>
                                 </tfoot>
                              </table>';

          $select_cuotaapagar = $cronograma['select_cuotaapagar'];
          $select_cuotaapagarredondeado = $cronograma['select_cuotaapagarredondeado'];
          if($request->idtipopago==2){
              $select_cuotaapagar = number_format($cronograma['select_cuotaapagar']+$cronograma['select_acuenta'], 2, '.', '');
              $select_cuotaapagarredondeado = number_format(round($select_cuotaapagar, 1), 2, '.', '');
          }

          // fin
          return [
              'hastacuota' =>$cronograma['html_cuotasrestantes'],

              'select_cuota' => $cronograma['select_cuota'],
              'select_mora' => $cronograma['select_mora'],
              'select_moraapagar' => $cronograma['select_moraapagar'],
              'select_acuenta' => $cronograma['select_acuenta'],
              'select_cuotaapagar' => $select_cuotaapagar,
              'select_cuotaapagarredondeado' => $select_cuotaapagarredondeado,

              'cobranzapendiente' => $cobranzapendiente,
              'cobranzacancelada' => $cobranzacancelada,
              'pagorealizado' => $pagorealizado,

              // Resumen Credito
              'credito-fechadesembolso' => date_format(date_create($cronograma['creditosolicitud']->fechadesembolsado),"d/m/Y h:i:s A"),
              'credito-monto' => $cronograma['creditosolicitud']->monto,
              'credito-interes' => $cronograma['creditosolicitud']->total_interes,
              'credito-montototal' => $cronograma['creditosolicitud']->total_cuota,

              'credito-deudaactual' => number_format($cronograma['total_vencida_cuota'], 2, '.', ''),
              'credito-moraactual'  => number_format($cronograma['total_vencida_mora'], 2, '.', ''),
              'credito-totalactual' => number_format($cronograma['total_vencida_cuotapago'], 2, '.', ''),

              'credito-deudarestante' => number_format($cronograma['total_restante_cuota'], 2, '.', ''),
              'credito-morarestante'  => number_format($cronograma['total_restante_mora'], 2, '.', ''),
              'credito-totalrestante' => number_format($cronograma['total_restante_cuotapago'], 2, '.', ''),

              'credito-deudapagada' => number_format($cronograma['total_cancelada_cuotapago'], 2, '.', ''),
              'credito-morapagada'  => number_format($cronograma['total_cancelada_moraapagar'], 2, '.', ''),
              'credito-totalpagada' => number_format($cronograma['total_cancelada_acuenta']+$cronograma['total_cancelada_cuotaapagar'], 2, '.', ''),
          ];
      }
      elseif ($id == 'show-detalle_pagorealizado') {
        $cobranza = DB::table('s_prestamo_cobranza')
          ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_cobranza.idprestamo_credito')
          ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
          ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
          ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_credito.idcajero')
          ->join('s_agencia', 's_agencia.id', 's_prestamo_credito.idagencia')
          ->where('s_prestamo_cobranza.id', $request->idcobranza)
          ->select(
            's_prestamo_cobranza.*',
            'cliente.identificacion as cliente_identificacion',
            'cliente.nombre as cliente_nombre',
            'cliente.apellidos as cliente_apellidos',
            'asesor.nombre as asesor_nombre',
            'asesor.apellidos as asesor_apellidos',
            'cajero.nombre as cajero_nombre',
            'cajero.apellidos as cajero_apellidos',
            's_agencia.id as idagencia',
            's_agencia.nombrecomercial as agencia_nombre'
          )
          ->first();
        $cobranzadetalle = DB::table('s_prestamo_cobranzadetalle')
          ->where('s_prestamo_cobranzadetalle.idprestamo_cobranza', $request->idcobranza)
          ->get();
        $agencia = DB::table('s_agencia')
          ->leftJoin('ubigeo','ubigeo.id','s_agencia.idubigeo')
          ->where('s_agencia.id', $cobranza->idagencia)
          ->select(
            's_agencia.*',
            'ubigeo.nombre as ubigeonombre'
          )
          ->first();
        $html = '<div class="list-single-main-wrapper fl-wrap">
                  <div class="breadcrumbs gradient-bg fl-wrap">
                    <span>Detalle</span>
                    <a class="btn btn-success" href="javascript:;" onclick="index_pagorealizado()"><i class="fa fa-angle-left"></i> Atras</a></a>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <label>Código</label>
                    <input type="text" value="'.$cobranza->codigo.'" disabled>
                    <label>Fecha de Pago</label>
                    <input type="text" value="'.date_format(date_create($cobranza->fecharegistro),"d/m/Y h:i:s A").'" disabled>
                    <label>Agencia</label>
                    <input type="text" value="'.$agencia->nombrecomercial.'" disabled>
                  </div>
                  <div class="col-sm-6">
                    <label>Cliente</label>
                    <input type="text" value="'.$cobranza->cliente_identificacion.' - '.$cobranza->cliente_apellidos.', '.$cobranza->cliente_nombre.'" disabled>
                    <label>Asesor</label>
                    <input type="text" value="'.$cobranza->asesor_apellidos.', '.$cobranza->asesor_nombre.'" disabled>
                    <label>Ventanilla</label>
                    <input type="text" value="'.$cobranza->cajero_apellidos.', '.$cobranza->cajero_nombre.'" disabled>
                  </div>
                </div>
                <table class="table">
                  <thead style="background: #31353d; color: #fff;">
                    <tr>
                      <td style="padding: 8px; text-align: center;">Nº</td>
                      <td style="padding: 8px; text-align: center;">Cuota</td>
                      <td style="padding: 8px; text-align: center;">Mora</td>
                      <td style="padding: 8px; text-align: center;">Total</td>
                    </tr>
                  </thead>
                  <tbody>';
        foreach ($cobranzadetalle as $value) {
          $credito = DB::table('s_prestamo_creditodetalle')
            ->whereId($value->idprestamo_creditodetalle)
            ->first();
          $html .= '<tr>
                      <td style="padding: 8px; text-align: center;">'.$credito->numero.'</td>
                      <td style="padding: 8px; text-align: center;">'.$credito->cuota.'</td>
                      <td style="padding: 8px; text-align: center;">'.$credito->moraapagar.'</td>
                      <td style="padding: 8px; text-align: center;">'.$credito->total.'</td>
                    </tr>';
        }
        $html .= '<tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL CUOTA:</td>
                    <td style="white-space: nowrap; padding: 8px; text-align: center;">'.$cobranza->total_cuota.'</td>
                  </tr>
                  <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL MORA:</td>
                    <td style="white-space: nowrap; padding: 8px; text-align: center;">'.$cobranza->total_mora.'</td>
                  </tr>
                  <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL:</td>
                    <td style="white-space: nowrap; padding: 8px; text-align: center;">'.$cobranza->total_cuotaapagar.'</td>
                  </tr>
                </tbody>
              </table>';
        return $html;
      }
      elseif ($id == 'show-ticket_pagorealizado') {
        $html = '<div class="list-single-main-wrapper fl-wrap">
                    <div class="breadcrumbs gradient-bg fl-wrap">
                      <span>Ticket</span>
                      <a class="btn btn-success" href="javascript:;" onclick="index_pagorealizado()"><i class="fa fa-angle-left"></i> Atras</a></a>
                    </div>
                    <iframe src="'.url('backoffice/tienda/sistema/'.$idtienda.'/prestamocobranza/'.$request->idcobranza.'/edit?view=ticket_pagorealizado-pdf').'#zoom=130" frameborder="0" width="100%" height="600px"></iframe>
                </div>';
        return $html;
      }
      elseif ($id == 'show-anular_pagorealizado') {
        $cobranza = DB::table('s_prestamo_cobranza')
          ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_cobranza.idprestamo_credito')
          ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
          ->join('s_agencia', 's_agencia.id', 's_prestamo_credito.idagencia')
          ->where('s_prestamo_cobranza.id', $request->idcobranza)
          ->select(
            's_prestamo_cobranza.*',
            'cliente.identificacion as cliente_identificacion',
            'cliente.nombre as cliente_nombre',
            'cliente.apellidos as cliente_apellidos',
            's_agencia.id as idagencia',
            's_agencia.nombrecomercial as agencia_nombre'
          )
          ->first();
        $cobranzadetalle = DB::table('s_prestamo_cobranzadetalle')
          ->where('s_prestamo_cobranzadetalle.idprestamo_cobranza', $cobranza->id)
          ->get();
        $agencia = DB::table('s_agencia')
          ->leftJoin('ubigeo','ubigeo.id','s_agencia.idubigeo')
          ->where('s_agencia.id', $cobranza->idagencia)
          ->select(
            's_agencia.*',
            'ubigeo.nombre as ubigeonombre'
          )
          ->first();
        $html = '<div class="list-single-main-wrapper fl-wrap">
                  <div class="breadcrumbs gradient-bg fl-wrap">
                    <span>Detalle</span>
                    <a class="btn btn-success" href="javascript:;" onclick="index_pagorealizado()"><i class="fa fa-angle-left"></i> Atras</a></a>
                  </div>
                </div>
                <form action="javascript:;" 
                      onsubmit="callback({
                                          route:  \'backoffice/tienda/sistema/'.$idtienda.'/prestamocobranza/'.$cobranza->id.'\',
                                          method: \'PUT\',
                                          data:   {
                                            view: \'anular_pagorealizado\'
                                          }
                                        },
                                        function(resultado){
                                          index_pagorealizado();
                                        },this)">
                  <div class="row">
                    <div class="col-sm-6">
                      <label>Fecha</label>
                      <input type="text" value="'.$cobranza->fecharegistro.'" disabled>
                      <label>Agencia</label>
                      <input type="text" value="'.$agencia->nombrecomercial.'" disabled>
                      <label>Cliente</label>
                      <input type="text" value="'.$cobranza->cliente_identificacion.' - '.$cobranza->cliente_apellidos.', '.$cobranza->cliente_nombre.'" disabled>
                    </div>
                    <div class="col-sm-6">
                      <label>Credito</label>
                      <input type="text" value="'.$cobranza->codigo.'" disabled>
                      <label>Operación</label>
                      <input type="text" value="'.$cobranza->codigo.'" disabled>
                    </div>
                  </div>
                  <table class="table">
                    <thead style="background: #31353d; color: #fff;">
                      <tr>
                        <td style="padding: 8px; text-align: center;">Nº</td>
                        <td style="padding: 8px; text-align: center;">Cuota</td>
                        <td style="padding: 8px; text-align: center;">Mora</td>
                        <td style="padding: 8px; text-align: center;">Total</td>
                      </tr>
                    </thead>
                    <tbody>';
        foreach ($cobranzadetalle as $value) {
          $credito = DB::table('s_prestamo_creditodetalle')
            ->whereId($value->idprestamo_creditodetalle)
            ->first();
          $html .= '<tr>
                      <td style="padding: 8px; text-align: center;">'.$credito->numero.'</td>
                      <td style="padding: 8px; text-align: center;">'.$credito->cuota.'</td>
                      <td style="padding: 8px; text-align: center;">'.$credito->moraapagar.'</td>
                      <td style="padding: 8px; text-align: center;">'.$credito->total.'</td>
                    </tr>';
        }
        $html .= '<tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL CUOTA:</td>
                    <td style="white-space: nowrap; padding: 8px; text-align: center;">'.$cobranza->total_cuota.'</td>
                  </tr>
                  <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL MORA:</td>
                    <td style="white-space: nowrap; padding: 8px; text-align: center;">'.$cobranza->total_mora.'</td>
                  </tr>
                  <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL:</td>
                    <td style="white-space: nowrap; padding: 8px; text-align: center;">'.$cobranza->total_cuotaapagar.'</td>
                  </tr>
                </tbody>
              </table>
              <button type="submit" class="btn mx-btn-post">Anular</button>
            </form>';
        return $html;
      }
      elseif ($id == 'show-creditocliente') {
        $clientes = DB::table('s_prestamo_credito')
            ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
            ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
            ->where('cliente.identificacion','LIKE', '%'.$request->buscar.'%')
            ->where('s_prestamo_credito.idestado', 1)
            ->whereIn('s_prestamo_credito.idestadocredito', [4, 5])
            ->orWhere('cliente.nombre','LIKE', '%'.$request->buscar.'%')
            ->where('s_prestamo_credito.idestado', 1)
            ->whereIn('s_prestamo_credito.idestadocredito', [4, 5])
            ->orWhere('cliente.apellidos','LIKE', '%'.$request->buscar.'%')
            ->where('s_prestamo_credito.idestado', 1)
            ->whereIn('s_prestamo_credito.idestadocredito', [4, 5])
            ->select(
                's_prestamo_credito.id as id',
                DB::raw('CONCAT( IF(s_prestamo_credito.idestadocredito = 4 && s_prestamo_credito.idestado = 1, "PENDIENTE", "CANCELADO"), " / ",
                    cliente.identificacion, " / ",
                    cliente.apellidos, ", ", cliente.nombre, " / ",
                    s_moneda.simbolo, " ", s_prestamo_credito.monto ) as text')
            )
            ->get();
        return $clientes;
      }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      $cobranza = DB::table('s_prestamo_cobranza')
          ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_cobranza.idprestamo_credito')
          ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
          ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
          ->leftJoin('users as cajero', 'cajero.id', 's_prestamo_credito.idcajero')
          ->join('s_agencia', 's_agencia.id', 's_prestamo_credito.idagencia')
          ->where('s_prestamo_cobranza.id', $id)
          ->select(
            's_prestamo_cobranza.*',
            'cliente.identificacion as cliente_identificacion',
            'cliente.nombre as cliente_nombre',
            'cliente.apellidos as cliente_apellidos',
            'asesor.nombre as asesor_nombre',
            'asesor.apellidos as asesor_apellidos',
            'cajero.nombre as cajero_nombre',
            'cajero.apellidos as cajero_apellidos',
            's_agencia.id as idagencia',
            's_agencia.nombrecomercial as agencia_nombre'
          )
          ->first();
      $configuracion = configuracion($idtienda);
      $cobranzadetalle = DB::table('s_prestamo_cobranzadetalle')
        ->where('s_prestamo_cobranzadetalle.idprestamo_cobranza', $id)
        ->get();
      $agencia = DB::table('s_agencia')
        ->leftJoin('ubigeo','ubigeo.id','s_agencia.idubigeo')
        ->where('s_agencia.id', $cobranza->idagencia)
        ->select(
          's_agencia.*',
          'ubigeo.nombre as ubigeonombre'
        )
        ->first();

      if ($request->input('view') == 'ticket') {
        return view('layouts/backoffice/tienda/sistema/prestamocobranza/ticket', compact(
          'cobranza',
          'tienda'
        ));
      }
      elseif ($request->input('view') == 'ticketpdf') {
        $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamocobranza/ticketpdf', compact(
          'configuracion',
          'cobranza',
          'cobranzadetalle',
          'agencia',
          'tienda'
        ));
        return $pdf->stream('Ticket Cobranza.pdf');
      }
      elseif ($request->view == 'ticket_pagorealizado-pdf') {
        $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamocobranza/ticket_pagorealizado-pdf', compact(
          'configuracion',
          'cobranza',
          'cobranzadetalle',
          'agencia',
          'tienda'
        ));
        return $pdf->stream('Ticket Cobranza.pdf');
      }
      elseif ($request->view == 'detalle') {
        return view('layouts/backoffice/tienda/sistema/prestamocobranza/detalle', compact(
          'cobranza',
          'cobranzadetalle',
          'agencia',
          'tienda'
        ));
      }
      elseif ($request->view == 'anular') {
        return view('layouts/backoffice/tienda/sistema/prestamocobranza/anular', compact(
          'cobranza',
          'cobranzadetalle',
          'agencia',
          'tienda'
        ));
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(), $idtienda);
      if ($request->input('view') == 'editar') {
        $rules = [
          'dia' => 'required',
          'mes' => 'required',
          'motivo' => 'required'
        ];
        $messages = [
          'dia.required' => 'El "Dia" es Obligatorio.',
          'mes.required' => 'El "Mes" es Obligatorio.',
          'motivo.required' => 'El "Motivo" es Obligatorio.'
        ];
        $this->validate($request, $rules, $messages);

        DB::table('s_prestamo_cobranza')->whereId($id)->update([
          'dia' => $request->input('dia'),
          'mes' => $request->input('mes'),
          'motivo' => $request->input('motivo')
        ]);
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha actualizado correctamente.'
        ]);
      }
      elseif ($request->input('view') == 'anular_pagorealizado') {
        DB::table('s_prestamo_cobranza')->whereId($id)->update([
          'idestado' => 2,
        ]);
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha anulado correctamente.'
        ]);
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'eliminar') {
            DB::table('s_prestamo_cobranza')->where('idtienda',$idtienda)->where('id',$id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
