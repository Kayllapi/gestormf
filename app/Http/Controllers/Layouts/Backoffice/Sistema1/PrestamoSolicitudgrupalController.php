<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class PrestamoSolicitudgrupalController extends Controller
{
    public function index(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        $where = [];
        if($request->tipocredito!=''){ $where[] = ['s_prestamo_tipocredito.id',$request->tipocredito]; }
        $where[] = ['s_prestamo_creditogrupal.codigo','LIKE','%'.$request->codigocredito.'%'];
        if($request->frecuencia!=''){ $where[] = ['s_prestamo_creditogrupal.idprestamo_frecuencia',$request->frecuencia]; }
      
        $where1 = [];
        if($request->tipocredito!=''){ $where1[] = ['s_prestamo_tipocredito.id',$request->tipocredito]; }
        $where1[] = ['s_prestamo_creditogrupal.codigo','LIKE','%'.$request->codigocredito.'%'];
        if($request->frecuencia!=''){ $where1[] = ['s_prestamo_creditogrupal.idprestamo_frecuencia',$request->frecuencia]; }
      
        $prestamocreditos = DB::table('s_prestamo_creditogrupal')
              ->join('users as asesor', 'asesor.id', 's_prestamo_creditogrupal.idasesor')
              ->join('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_creditogrupal.idprestamo_frecuencia')
              ->join('s_prestamo_tipocredito', 's_prestamo_tipocredito.id', 's_prestamo_creditogrupal.idprestamo_tipocredito')
              ->join('s_moneda', 's_moneda.id', 's_prestamo_creditogrupal.idmoneda')
              ->where($where)
              ->where('s_prestamo_creditogrupal.idtienda', $idtienda)
              ->whereIn('s_prestamo_creditogrupal.idestado', [1,3])
              ->where('s_prestamo_creditogrupal.idasesor', Auth::user()->id)
              ->orWhere($where1)
              ->where('s_prestamo_creditogrupal.idtienda', $idtienda)
              ->whereIn('s_prestamo_creditogrupal.idestado', [1,3])
              ->where('s_prestamo_creditogrupal.idasesor', Auth::user()->id)
              ->select(
                  's_prestamo_creditogrupal.*',
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  's_prestamo_frecuencia.nombre as frecuencianombre',
                  's_prestamo_tipocredito.nombre as tipocreditonombre',
                  's_moneda.simbolo as monedasimbolo',
              )
              ->orderBy('s_prestamo_creditogrupal.idestadodesembolso','asc')
              ->orderBy('s_prestamo_creditogrupal.idestadocredito','asc')
              ->orderBy('s_prestamo_creditogrupal.fecharegistro','desc')
              ->paginate(10);
        return view('layouts/backoffice/tienda/sistema/prestamosolicitudgrupal/index', [
            'tienda' => $tienda,
            'prestamocreditos' => $prestamocreditos,
        ]);
    }
  
    public function create(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda       = DB::table('tienda')->whereId($idtienda)->first();
      $frecuencias  = DB::table('s_prestamo_frecuencia')->get();
      return view('layouts/backoffice/tienda/sistema/prestamosolicitudgrupal/create', [
          'tienda' => $tienda,
          'frecuencias' => $frecuencias,
      ]);
    }
  
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        
        if ($request->input('view') == 'registrar') {

            $rules = [
              'nombregrupo' => 'required',
              'monto' => 'required',
              'numerocuota' => 'required',
              'fechainicio' => 'required',
              'idfrecuencia' => 'required',
              'tasa' => 'required',
            ];
          
            if(configuracion($idtienda,'prestamo_estadoabono')['valor']=='on'){
                $rules = array_merge($rules,[
                    'abono' => 'required'
                ]);
            }
                
            $messages = [
              'nombregrupo.required' => 'El "Nombre de Grupo" es Obligatorio.',
              'monto.required' => 'El "Monto" es Obligatorio.',
              'montogrupal.required' => 'El "Monto" es Obligatorio.',
              'numerocuota.required' => 'El "Nro. de Cuota" es Obligatorio.',
              'fechainicio.required' => 'La "Fecha de Inicio" es Obligatorio.',
              'idfrecuencia.required' => 'La "Frecuencia" es Obligatorio.',
              'tasa.required' => 'El "Interes" es Obligatorio.',
              'abono.required' => 'El "Abono" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);
          
            // Días de Gracia
            $sdiasgracia = 0;
            if(configuracion($idtienda,'prestamo_estadodias_gracia')['valor']=='on'){
                if($request->idfrecuencia == 1){
                    $sdiasgracia = configuracion($idtienda,'prestamo_dias_gracia_diario')['valor'];
                }elseif($request->idfrecuencia == 2){
                    $sdiasgracia = configuracion($idtienda,'prestamo_dias_gracia_semanal')['valor']; 
                }elseif($request->idfrecuencia == 3){
                    $sdiasgracia = configuracion($idtienda,'prestamo_dias_gracia_quincenal')['valor']; 
                }elseif($request->idfrecuencia == 4){
                    $sdiasgracia = configuracion($idtienda,'prestamo_dias_gracia_mensual')['valor'];  
                }elseif($request->idfrecuencia == 5){
                    $sdiasgracia = configuracion($idtienda,'prestamo_dias_gracia_programado')['valor']; 
                }
                $fechainiciodiasgracia = date('Y-m-d',strtotime('+'.$sdiasgracia.'day',strtotime(Carbon::now()->format('Y-m-d'))));
                if($request->fechainicio>$fechainiciodiasgracia){
                    return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'Solo hay como màximo '.$sdiasgracia.' dìas de gracia.'
                    ]);
                }
            }
    
            // obtener ultimo código
            $prestamocredito = DB::table('s_prestamo_credito')
                ->where('s_prestamo_credito.idtienda',$idtienda)
                ->orderBy('s_prestamo_credito.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($prestamocredito!=''){
                $codigo = $prestamocredito->codigo+1;
            }
            // fin obtener ultimo código
          
            $cronograma = prestamo_cronograma(
                $idtienda,
                $request->monto,
                $request->numerocuota,
                $request->fechainicio,
                $request->idfrecuencia,
                $request->numerodias,
                $request->tasa,
                $request->gastoadministrativo!=null?$request->gastoadministrativo:0,
                $request->excluirferiado,
                $request->excluirsabado,
                $request->excluirdomingo,
                $request->abono
            );

          
            $idprestamo_credito = DB::table('s_prestamo_creditogrupal')->insertGetId([
              'fecharegistro' => Carbon::now(),
              'codigo' => $codigo,
              'nombre' => $request->input('nombregrupo'),
              'monto' => $request->input('monto'),
              'numerocuota' => $request->input('numerocuota'),
              'fechainiciocero' => $request->fechainicio,
              'fechainicio' => $cronograma['fechainicio'],
              'ultimafecha' => $cronograma['ultimafecha'],
              'numerodias' => $request->input('numerodias') ?? 0,
              'tasa' => $request->input('tasa'),
              'cuota' => $cronograma['cuota'],
              'comentariosupervisor' => '',
              'tipocredito' => 'CRÉDITO GRUPAL NORMAL',
              'tipocreditogenerado' => 1,
              'excluirsabado' => $request->input('excluirsabado') ?? '',
              'excluirdomingo' => $request->input('excluirdomingo') ?? '',
              'excluirferiado' => $request->input('excluirferiado') ?? '',
              'total_amortizacion' => $cronograma['total_amortizacion'],
              'total_interes' => $cronograma['total_interes'],
              'total_cuota' => $cronograma['total_cuota'],
              'total_gastoadministrativo' => $cronograma['total_gastoadministrativo'],
              'total_segurodesgravamen' => $cronograma['total_segurodesgravamen'],
              'total_cuotanormal' => $cronograma['total_cuotanormal'],
              'total_acumulado' => $cronograma['total_acumulado'],
              'total_cuotafinal' => $cronograma['total_cuotafinal'],
              'total_abono' => $cronograma['total_abono'],
              'total_cuotafinaltotal' => $cronograma['total_cuotafinaltotal'],
              'idmoneda' => 1,
              'idasesor' => Auth::user()->id,
              'idcajero' => 0,
              'idsupervisor' => 0,
              'idprestamo_frecuencia' => $request->input('idfrecuencia'),
              'idprestamo_tipotasa' => $cronograma['tipotasa'],
              'idprestamo_tipocredito' => 6, // 1=NORMAL, 2=REFINANCIADO,3=REPROGRAMADO,4=AMPLIADO, 5=CAMPAÑA, 6=GRUPAL
              'idprestamo_estadocredito' => 1, // 1=NORMAL, 2=GRUPAL
              'idestadocobranza' => 1, // 1 = PENDIENTE, 2 = CANCELADO
              'idestadocredito' => 1, // pendiente
              'idestadoaprobacion' => 0,
              'idestadodesembolso' => 0,
              'idestadogastoadministrativo' => 0,
              'idtienda' => $idtienda,
              'idestado' => 1,
            ]);
          
            foreach($cronograma['cronograma'] as $value) {
              DB::table('s_prestamo_creditogrupaldetalle')->insert([
                'numero' => $value['numero'],
                'fechavencimiento' => $value['fechanormal'],
                'saldocapital' => $value['saldo'],
                'saldomontototal' => $value['saldototal'],
                'amortizacion' => $value['amortizacion'],
                'interes' => $value['interes'],
                'cuota' => $value['cuota'],
                'seguro' => $value['segurodesgravamen'],
                'gastoadministrativo' => $value['gastoadministrativo'],
                'cuotanormal' => $value['cuotanormal'],
                'acumulado' => $value['acumulado'],
                'total' => $value['cuotafinal'],
                'abono' => $value['abono'],
                'totalfinal' => $value['cuotafinaltotal'],
                'atraso' => 0,
                'mora' => 0,
                'moradescuento' => 0,
                'moraapagar' => 0,
                'cuotapago' => 0,
                'acuenta' => 0,
                'cuotaapagar' => 0,
                'cuotaapagartotal' => 0,
                'montorefinanciado' => 0,
                'interesdescontado' => 0,
                'idprestamo_creditogrupal' => $idprestamo_credito,
                'idestadocobranza' => 1,
                'idtienda' => $idtienda,
                'idestado' => 1
              ]);
            }
            
            // asignar identificacion de creditos idividuales a grupales
            $clientes = explode('/&/', $request->input('clientes'));
            for($i = 1; $i < count($clientes); $i++){
                $item = explode('/,/',$clientes[$i]);
                DB::table('s_prestamo_credito')->whereId($item[0])->update([
                  'idprestamo_creditogrupal' => $idprestamo_credito,
                  'idprestamo_comite' => $item[1]!=''?($item[1]!='undefined'?$item[1]:0):0,
                ]);
            } 
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($request->idtienda)->first();
      
        if ($id == 'show-creditocalendario') {

            $cronograma = prestamo_cronograma(
                $idtienda,
                $request->monto,
                $request->numerocuota,
                $request->fechainicio,
                $request->frecuencia,
                $request->numerodias,
                $request->tasa,
                $request->gastoadministrativo!=null?$request->gastoadministrativo:0,
                $request->excluirferiado,
                $request->excluirsabado,
                $request->excluirdomingo,
                $request->abono
            );

            if($cronograma['resultado']=='CORRECTO'){
                $html = '<table class="table" id="table-creditocalendario">
                        <thead style="background: #31353d; color: #fff;">
                            <tr>
                                <td style="padding: 8px;text-align: center;">Nº</td>
                                <td style="padding: 8px;text-align: center;">Fecha de Pago</td>
                                <td style="padding: 8px;text-align: center;">Saldo Capital</td>
                                <td style="padding: 8px;text-align: center;">Amortiz.</td>
                                <td style="padding: 8px;text-align: center;">Interes</td>
                                '.(($cronograma['total_segurodesgravamen']>0)?'<td style="padding: 8px;text-align: center;">Seguro Desgrav.</td>':'').'
                                '.(($cronograma['total_gastoadministrativo']>0)?'<td style="padding: 8px;text-align: center;">Gasto Admin.</td>':'').'
                                <td style="padding: 8px;text-align: center;">Cuota</td>
                                '.(($cronograma['total_abono']>0)?'<td style="padding: 8px;text-align: center;">Abono</td>':'').'
                            </tr>
                        </thead>
                        <tbody>';
                foreach ($cronograma['cronograma'] as $value) {
                    $html .= '<tr>
                              <td style="padding: 8px;text-align: right;width: 50px;">'.$value['numero'].'</td>
                              <td style="padding: 8px;text-align: right;width: 120px;">'.$value['fecha'].'</td>
                              <td style="padding: 8px;text-align: right;">'.$value['saldo'].'</td>
                              <td style="padding: 8px;text-align: right;">'.$value['amortizacion'].'</td>
                              <td style="padding: 8px;text-align: right;">'.$value['interes'].'</td>
                              '.(($cronograma['total_segurodesgravamen']>0)?'<td style="padding: 8px;text-align: right;">'.$value['segurodesgravamen'].'</td>':'').'
                              '.(($cronograma['total_gastoadministrativo']>0)?'<td style="padding: 8px;text-align: right;">'.$value['gastoadministrativo'].'</td>':'').'
                              <td style="padding: 8px;text-align: right;">'.$value['cuotafinal'].'</td>
                              '.(($cronograma['total_abono']>0)?'<td style="padding: 8px;text-align: right;">'.$value['abono'].'</td>':'').'
                          </tr>';
                }
                $html .= '<tr style="background-color: #31353c;color: white;">
                              <td style="padding: 8px;text-align: right;width: 50px;" colspan="3">TOTAL</td>
                              <td style="padding: 8px;text-align: right;">'.$cronograma['total_amortizacion'].'</td>
                              <td style="padding: 8px;text-align: right;">'.$cronograma['total_interes'].'</td>
                              '.(($cronograma['total_segurodesgravamen']>0)?'<td style="padding: 8px;text-align: right;">'.$cronograma['total_segurodesgravamen'].'</td>':'').'
                              '.(($cronograma['total_gastoadministrativo']>0)?'<td style="padding: 8px;text-align: right;">'.$cronograma['total_gastoadministrativo'].'</td>':'').'
                              <td style="padding: 8px;text-align: right;">'.$cronograma['total_cuotafinal'].'</td>
                              '.(($cronograma['total_abono']>0)?'<td style="padding: 8px;text-align: right;">'.$cronograma['total_abono'].'</td>':'').'
                          </tr></tbody>
                    </table>';
            }else{
                $html = '<div class="mensaje-danger">'.$cronograma['mensaje'].'</b></div>';      
            }

            return ([
                'resultado' => $cronograma['resultado'],
                'mensaje' => $cronograma['mensaje'],
                'html' => $html,
                'total_interes' => $cronograma['total_interes'],
                'total_segurodesgravamen' => $cronograma['total_segurodesgravamen'],
                'total_gastoadministrativo' => $cronograma['total_gastoadministrativo'],
                'total_cuotafinal' => $cronograma['total_cuotafinal'],
                'total_abono' => $cronograma['total_abono'],
            ]);
        }


        elseif ($id == 'show-creditocliente') {

            $prestamocredito = DB::table('s_prestamo_credito')
                ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
                ->where([
                  ['s_prestamo_credito.id', $request->idcredito],
                  ['s_prestamo_credito.idtienda', $idtienda]
                ])
                ->select(
                  's_prestamo_credito.*',
                  'cliente.identificacion as clienteidentificacion',
                  'cliente.nombre as clientenombre',
                  'cliente.apellidopaterno as clienteapellidopaterno',
                  'cliente.apellidomaterno as clienteapellidomaterno',
                )
                ->first();
          
            return [
                'prestamocredito' => $prestamocredito,
            ];
        }
    }
  
    public function edit(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')
            ->leftJoin('ubigeo', 'ubigeo.id', 'tienda.idubigeo')
            ->select(
                'tienda.*',
                'ubigeo.nombre as ubigeonombre',
            )
            ->where('tienda.id',$idtienda)
            ->first();
      
        $prestamocreditogrupal = DB::table('s_prestamo_creditogrupal')
            ->join('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_creditogrupal.idprestamo_frecuencia')
            ->join('s_moneda', 's_moneda.id', 's_prestamo_creditogrupal.idmoneda')
            ->join('users as asesor', 'asesor.id', 's_prestamo_creditogrupal.idasesor')
            ->join('tienda', 'tienda.id', 's_prestamo_creditogrupal.idtienda')
            ->where([
              ['s_prestamo_creditogrupal.id', $id],
              ['s_prestamo_creditogrupal.idtienda', $idtienda]
            ])
            ->select(
              's_prestamo_creditogrupal.*',
              's_prestamo_frecuencia.nombre as frecuencia_nombre',
              's_prestamo_frecuencia.id as idprestamo_frecuencia',
              'tienda.nombre as tiendanombre',
              'asesor.identificacion as asesoridentificacion',
              'asesor.nombre as asesornombre',
              'asesor.apellidos as asesorapellidos',
              's_moneda.simbolo as monedasimbolo',
              DB::raw('IF(asesor.idtipopersona = 1 || asesor.idtipopersona = 3,
                  CONCAT(asesor.identificacion, " - ", asesor.apellidos, ", ", asesor.nombre),
                  CONCAT(asesor.identificacion, " - ", asesor.apellidos)) as asesor_nombre'),
            )
            ->first();
        if($request->view == 'editar') {
            $frecuencias  = DB::table('s_prestamo_frecuencia')->get();
            $prestamo_creditos = DB::table('s_prestamo_credito')
                ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
                ->where('s_prestamo_credito.idprestamo_creditogrupal',$prestamocreditogrupal->id)
                ->select(
                    's_prestamo_credito.*',
                    'cliente.identificacion as clienteidentificacion',
                    DB::raw('IF(cliente.idtipopersona=1,
                    CONCAT(cliente.apellidos,", ",cliente.nombre),
                    CONCAT(cliente.apellidos)) as cliente'),
                )
                ->get();
            return view('layouts/backoffice/tienda/sistema/prestamosolicitudgrupal/edit', [
                'tienda' => $tienda,
                'frecuencias' => $frecuencias,
                'prestamocreditogrupal' => $prestamocreditogrupal,
                'prestamo_creditos' => $prestamo_creditos,
            ]);
        }
        elseif ($request->view == 'preaprobar') {
            return view('layouts/backoffice/tienda/sistema/prestamosolicitudgrupal/preaprobar', [
                'tienda' => $tienda,
                'prestamocreditogrupal' => $prestamocreditogrupal,
            ]);
        }
        elseif ($request->view == 'detalle') {
            return view('layouts/backoffice/tienda/sistema/prestamosolicitudgrupal/detalle', [
                'tienda' => $tienda,
                'prestamocreditogrupal' => $prestamocreditogrupal,
            ]);
        }
        elseif ($request->view == 'eliminar') {
            return view('layouts/backoffice/tienda/sistema/prestamosolicitudgrupal/eliminar', [
                'tienda' => $tienda,
                'prestamocreditogrupal' => $prestamocreditogrupal,
            ]);
        }
      
        elseif ($request->view == 'resultado') {
            return view('layouts/backoffice/tienda/sistema/prestamosolicitudgrupal/resultado',[
                'tienda' => $tienda,
                'prestamocreditogrupal' => $prestamocreditogrupal,
            ]);  
        } 
        elseif ($request->view == 'expedientedetalle') {
            return view('layouts/backoffice/tienda/sistema/prestamosolicitudgrupal/expedientedetalle',[
                'tienda' => $tienda,
                'prestamocreditogrupal' => $prestamocreditogrupal,
            ]);  
        } 
    }

    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(), $idtienda);
        
        if ($request->input('view') == 'editar') {
            $rules = [
              'nombregrupo' => 'required',
              'monto' => 'required',
              'numerocuota' => 'required',
              'fechainicio' => 'required',
              'idfrecuencia' => 'required',
              'tasa' => 'required',
            ];
          
            if(configuracion($idtienda,'prestamo_estadoabono')['valor']=='on'){
                $rules = array_merge($rules,[
                    'abono' => 'required'
                ]);
            }
                
            $messages = [
              'nombregrupo.required' => 'El "Nombre de Grupo" es Obligatorio.',
              'monto.required' => 'El "Monto" es Obligatorio.',
              'montogrupal.required' => 'El "Monto" es Obligatorio.',
              'numerocuota.required' => 'El "Nro. de Cuota" es Obligatorio.',
              'fechainicio.required' => 'La "Fecha de Inicio" es Obligatorio.',
              'idfrecuencia.required' => 'La "Frecuencia" es Obligatorio.',
              'tasa.required' => 'El "Interes" es Obligatorio.',
              'abono.required' => 'El "Abono" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);
          
            // Días de Gracia
            $sdiasgracia = 0;
            if(configuracion($idtienda,'prestamo_estadodias_gracia')['valor']=='on'){
                if($request->idfrecuencia == 1){
                    $sdiasgracia = configuracion($idtienda,'prestamo_dias_gracia_diario')['valor'];
                }elseif($request->idfrecuencia == 2){
                    $sdiasgracia = configuracion($idtienda,'prestamo_dias_gracia_semanal')['valor']; 
                }elseif($request->idfrecuencia == 3){
                    $sdiasgracia = configuracion($idtienda,'prestamo_dias_gracia_quincenal')['valor']; 
                }elseif($request->idfrecuencia == 4){
                    $sdiasgracia = configuracion($idtienda,'prestamo_dias_gracia_mensual')['valor'];  
                }elseif($request->idfrecuencia == 5){
                    $sdiasgracia = configuracion($idtienda,'prestamo_dias_gracia_programado')['valor']; 
                }
                $fechainiciodiasgracia = date('Y-m-d',strtotime('+'.$sdiasgracia.'day',strtotime(Carbon::now()->format('Y-m-d'))));
                if($request->fechainicio>$fechainiciodiasgracia){
                    return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'Solo hay como màximo '.$sdiasgracia.' dìas de gracia.'
                    ]);
                }
            }  
         
            $cronograma = prestamo_cronograma(
                $idtienda,
                $request->monto,
                $request->numerocuota,
                $request->fechainicio,
                $request->idfrecuencia,
                $request->numerodias,
                $request->tasa,
                !is_null($request->gastoadministrativo) ? $request->gastoadministrativo : 0,
                $request->excluirferiado,
                $request->excluirsabado,
                $request->excluirdomingo,
                $request->abono
            );
          
            DB::table('s_prestamo_creditogrupal')->whereId($id)->update([
              'nombre' => $request->input('nombregrupo'),
              'monto' => $request->input('monto'),
              'numerocuota' => $request->input('numerocuota'),
              'fechainiciocero' => $request->fechainicio,
              'fechainicio' => $cronograma['fechainicio'],
              'ultimafecha' => $cronograma['ultimafecha'],
              'numerodias' => $request->input('numerodias') ?? 0,
              'tasa' => $request->input('tasa'),
              'cuota' => $cronograma['cuota'],
              'excluirsabado' => $request->input('excluirsabado') ?? '',
              'excluirdomingo' => $request->input('excluirdomingo') ?? '',
              'excluirferiado' => $request->input('excluirferiado') ?? '',
              'total_amortizacion' => $cronograma['total_amortizacion'],
              'total_interes' => $cronograma['total_interes'],
              'total_cuota' => $cronograma['total_cuota'],
              'total_gastoadministrativo' => $cronograma['total_gastoadministrativo'],
              'total_segurodesgravamen' => $cronograma['total_segurodesgravamen'],
              'total_cuotanormal' => $cronograma['total_cuotanormal'],
              'total_acumulado' => $cronograma['total_acumulado'],
              'total_cuotafinal' => $cronograma['total_cuotafinal'],
              'total_abono' => $cronograma['total_abono'],
              'total_cuotafinaltotal' => $cronograma['total_cuotafinaltotal'],
              'idprestamo_frecuencia' => $request->input('idfrecuencia'),
              'idprestamo_tipotasa' => $cronograma['tipotasa']
            ]);
          
            DB::table('s_prestamo_creditogrupaldetalle')->where('idprestamo_creditogrupal',$id)->delete();
            
            foreach($cronograma['cronograma'] as $value) {
              DB::table('s_prestamo_creditogrupaldetalle')->insert([
                'numero' => $value['numero'],
                'fechavencimiento' => $value['fechanormal'],
                'saldocapital' => $value['saldo'],
                'saldomontototal' => $value['saldototal'],
                'amortizacion' => $value['amortizacion'],
                'interes' => $value['interes'],
                'cuota' => $value['cuota'],
                'seguro' => $value['segurodesgravamen'],
                'gastoadministrativo' => $value['gastoadministrativo'],
                'cuotanormal' => $value['cuotanormal'],
                'acumulado' => $value['acumulado'],
                'total' => $value['cuotafinal'],
                'abono' => $value['abono'],
                'totalfinal' => $value['cuotafinaltotal'],
                'atraso' => 0,
                'mora' => 0,
                'moradescuento' => 0,
                'moraapagar' => 0,
                'cuotapago' => 0,
                'acuenta' => 0,
                'cuotaapagar' => 0,
                'cuotaapagartotal' => 0,
                'montorefinanciado' => 0,
                'interesdescontado' => 0,
                'idprestamo_creditogrupal' => $id,
                'idestadocobranza' => 1,
                'idtienda' => $idtienda,
                'idestado' => 1
              ]);
            }
             
            // asignar identificacion de creditos idividuales a grupales
            DB::table('s_prestamo_credito')
                ->where('s_prestamo_credito.idtienda',$idtienda)
                ->where('s_prestamo_credito.idprestamo_creditogrupal',$id)
                ->update([
                  'idprestamo_creditogrupal' => 0
                ]);
          
            $clientes = explode('/&/', $request->input('clientes'));
            for($i = 1; $i < count($clientes); $i++){
                $item = explode('/,/',$clientes[$i]);
                DB::table('s_prestamo_credito')->whereId($item[0])->update([
                  'idprestamo_creditogrupal' => $id,
                  'idprestamo_comite' => $item[1]!=''?($item[1]!='undefined'?$item[1]:0):0,
                ]);
            } 
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }

        elseif ($request->input('view') == 'preaprobar') {
            
            $credito = DB::table('s_prestamo_creditogrupal')->whereId($id)->first();
            $cronograma = prestamo_cronograma(
              $idtienda,
              $credito->monto,
              $credito->numerocuota,
              $credito->fechainicio,
              $credito->idprestamo_frecuencia,
              $credito->numerodias,
              $credito->tasa,
              $request->gastoadministrativo!=null?$request->gastoadministrativo:0,
              $credito->excluirferiado,
              $credito->excluirsabado,
              $credito->excluirdomingo
            );
          
            if($cronograma['resultado']=='ERROR'){
                return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje'   => $cronograma['mensaje']
                ]);
            }

            DB::table('s_prestamo_creditogrupal')->whereId($id)->update([
              'fechapreaprobado' => Carbon::now(),
              'idestadocredito' => 2
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha PreAprobado correctamente.'
            ]);
        }
        
    }

    public function destroy(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
          
            DB::table('s_prestamo_credito')
                ->where('s_prestamo_credito.idtienda',$idtienda)
                ->where('s_prestamo_credito.idprestamo_creditogrupal',$id)
                ->update([
                  'idprestamo_creditogrupal' => 0
                ]);
            DB::table('s_prestamo_creditogrupaldetalle')->where('idtienda',$idtienda)->where('idprestamo_creditogrupal',$id)->delete();
            DB::table('s_prestamo_creditogrupal')->where('idtienda',$idtienda)->where('id',$id)->delete();

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        } 
    }
}
