<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class PrestamoSolicitudController extends Controller
{
    public function index(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        $where = [];
        if($request->tipocredito!=''){ $where[] = ['s_prestamo_tipocredito.id',$request->tipocredito]; }
        $where[] = ['s_prestamo_credito.codigo','LIKE','%'.$request->codigocredito.'%'];
        $where[] = ['cliente.identificacion','LIKE','%'.$request->identificacion.'%'];
        $where[] = ['cliente.nombre','LIKE','%'.$request->cliente.'%'];
        if($request->frecuencia!=''){ $where[] = ['s_prestamo_credito.idprestamo_frecuencia',$request->frecuencia]; }
      
        $where1 = [];
        if($request->tipocredito!=''){ $where1[] = ['s_prestamo_tipocredito.id',$request->tipocredito]; }
        $where1[] = ['s_prestamo_credito.codigo','LIKE','%'.$request->codigocredito.'%'];
        $where1[] = ['cliente.identificacion','LIKE','%'.$request->identificacion.'%'];
        $where1[] = ['cliente.apellidos','LIKE','%'.$request->cliente.'%'];
        if($request->frecuencia!=''){ $where1[] = ['s_prestamo_credito.idprestamo_frecuencia',$request->frecuencia]; }
      
        $prestamocreditos = DB::table('s_prestamo_credito')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->leftJoin('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->leftJoin('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_credito.idprestamo_frecuencia')
              ->leftJoin('s_prestamo_tipocredito', 's_prestamo_tipocredito.id', 's_prestamo_credito.idprestamo_tipocredito')
              ->leftJoin('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
              ->where($where)
              ->where('s_prestamo_credito.idtienda', $idtienda)
              ->whereIn('s_prestamo_credito.idestado', [1,3])
              ->where('s_prestamo_credito.idasesor', Auth::user()->id)
              ->orWhere($where1)
              ->where('s_prestamo_credito.idtienda', $idtienda)
              ->whereIn('s_prestamo_credito.idestado', [1,3])
              ->where('s_prestamo_credito.idasesor', Auth::user()->id)
              ->select(
                  's_prestamo_credito.*',
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  's_prestamo_frecuencia.nombre as frecuencianombre',
                  's_prestamo_tipocredito.nombre as tipocreditonombre',
                  'cliente.identificacion as clienteidentificacion',
                  's_moneda.simbolo as monedasimbolo',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
              )
              ->orderBy('s_prestamo_credito.idestadodesembolso','asc')
              ->orderBy('s_prestamo_credito.idestadocredito','asc')
              ->orderBy('s_prestamo_credito.fecharegistro','desc')
              ->paginate(10);
        return view('layouts/backoffice/tienda/sistema/prestamosolicitud/index', [
            'tienda' => $tienda,
            'prestamocreditos' => $prestamocreditos,
        ]);
    }
  
    public function create(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda       = DB::table('tienda')->whereId($idtienda)->first();
      $frecuencias  = DB::table('s_prestamo_frecuencia')->get();
      return view('layouts/backoffice/tienda/sistema/prestamosolicitud/create', [
          'tienda' => $tienda,
          'frecuencias' => $frecuencias,
      ]);
    }
  
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if ($request->input('view') == 'registrar') {
            $rules = [
              'idcliente' => 'required',
            ];
            if($request->input('check_idconyuge')=='on'){
              $rules = array_merge($rules,[
                'idconyuge' => 'required'
              ]);

            }
            if($request->input('check_idgarante')=='on'){
              $rules = array_merge($rules,[
                'idgarante' => 'required'
              ]);
            }
          
            $monto = 0;
            $prestamo_estadocreditogrupal = 0;
            if(configuracion($idtienda,'prestamo_estadocreditogrupal')['valor']==1){
                if($request->input('check_estadocreditogrupal')=='on'){
                    $rules = array_merge($rules,[
                      'montogrupal' => 'required',
                    ]);
                    $monto = $request->montogrupal;
                }else{
                    $prestamo_estadocreditogrupal = 1;
                }
            }else{
                $prestamo_estadocreditogrupal = 1;
            }
          
            if($prestamo_estadocreditogrupal == 1){
                if(configuracion($idtienda,'prestamo_estadoabono')['valor']=='on'){
                    $rules = array_merge($rules,[
                        'abono' => 'required'
                    ]);
                }
                $rules = array_merge($rules,[
                  'monto' => 'required',
                  'numerocuota' => 'required',
                  'fechainicio' => 'required',
                  'idfrecuencia' => 'required',
                  'tasa' => 'required',
                ]);
                $monto = $request->monto;
            }    

                
            $messages = [
              'idcliente.required' => 'El "Cliente" es Obligatorio.',
              'idconyuge.required' => 'El "Cónyuge" es Obligatorio.',
              'idgarante.required' => 'El "Garante" es Obligatorio.',
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
          
                
          
            // tipo de credito
            $tipocreditonombre = 'CRÉDITO NORMAL';
            $idprestamo_tipocredito = 1;
            if(configuracion($idtienda,'prestamo_tipocredito')['resultado']=='CORRECTO'){
                $tipocreditonombre = $request->tipocreditonombre;
                $idprestamo_tipocredito = 5;
            }
    
            $clientes = DB::table('s_prestamo_credito')
                ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
                ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','cliente.idprestamocartera')
                ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
                ->where('s_prestamo_credito.idcliente',$request->idcliente)
                ->where('s_prestamo_credito.idestado', 1)
                ->where('s_prestamo_credito.idtienda', $idtienda)
                ->whereIn('s_prestamo_credito.idestadocredito', [2,3,4])
                ->whereIn('s_prestamo_credito.idestadodesembolso', [0,1])
                ->where('s_prestamo_credito.idestadocobranza', 1)
                ->get();
          
            $tipocreditogenerado = 'CRÉDITO PRINCIPAL';
            if(count($clientes)>0){
                $tipocreditogenerado = 'CRÉDITO PARALELO';   
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

            $idprestamo_credito = DB::table('s_prestamo_credito')->insertGetId([
              'fecharegistro' => Carbon::now(),
              'codigo' => $codigo,
              /*'monto' => $request->input('monto'),
              'numerocuota' => $request->input('numerocuota'),
              'fechainiciocero' => $request->fechainicio,
              'fechainicio' => $cronograma['fechainicio'],
              'ultimafecha' => $cronograma['ultimafecha'],
              'numerodias' => $request->input('numerodias') ?? 0,
              'tasa' => $request->input('tasa'),
              'cuota' => $cronograma['cuota'],
              'comentariosupervisor' => '',
              'tipocredito' => $tipocreditonombre,
              'tipocreditogenerado' => $tipocreditogenerado,
              'excluirsabado' => $request->input('excluirsabado') ?? '',
              'excluirdomingo' => $request->input('excluirdomingo') ?? '',
              'excluirferiado' => $request->input('excluirferiado') ?? '',
              'total_amortizacion' => $cronograma['total_amortizacion'],
              'total_interes' => $cronograma['total_interes'],
              'total_cuota' => $cronograma['total_cuota'],
              'total_gastoadministrativo' => $cronograma['total_gastoadministrativo'],
              'total_segurodesgravamen' => $cronograma['total_segurodesgravamen'],
              'total_cuotafinal' => $cronograma['total_cuotafinal'],
              'total_abono' => $cronograma['total_abono'],
              'total_cuotafinaltotal' => $cronograma['total_cuotafinaltotal'],*/
              'monto' => $monto,
              'numerocuota' => 0,
              'fechainiciocero' => '2000-01-01',
              'fechainicio' => '2000-01-01',
              'ultimafecha' => '2000-01-01',
              'numerodias' => 0,  
              'tasa' => 0,
              'cuota' => 0,
              'comentariosupervisor' => '',
              'tipocredito' => '',
              'tipocreditogenerado' => '',
              'excluirsabado' => '',
              'excluirdomingo' => '',
              'excluirferiado' => '',
              'total_amortizacion' => 0,
              'total_interes' => 0,
              'total_cuota' => 0,
              'total_gastoadministrativo' => 0,
              'total_segurodesgravamen' => 0,
              'total_cuotafinal' => 0,
              'total_abono' => 0,
              'total_cuotafinaltotal' => 0,
              'estadoexpediente' => 'no',
              'facturacion_montorecibido' => 0,
              'facturacion_vuelto' => 0,
              'facturacion_cliente_identificacion' => '',
              'facturacion_cliente_nombre' => '',
              'facturacion_cliente_apellidos' => '',
              'facturacion_cliente_direccion' => '',
              'facturacion_idagencia' => 0,
              'facturacion_idtipocomprobante' => 0,
              'facturacion_idubigeo' => 0,
              'facturacion_idaperturacierre' => 0,
              'cronograma_primeratraso' => 0,
              'cronograma_total_cancelada_atraso' => 0,
              'cronograma_total_cancelada_cuota' => 0,
              'cronograma_total_cancelada_mora' => 0,
              'cronograma_total_cancelada_moradescontado' => 0,
              'cronograma_total_cancelada_moraapagar' => 0,
              'cronograma_total_cancelada_acuenta' => 0,
              'cronograma_total_cancelada_cuotapago' => 0,
              'cronograma_total_vencida_atraso' => 0,
              'cronograma_total_vencida_cuota' => 0,
              'cronograma_total_vencida_mora' => 0,
              'cronograma_total_vencida_moradescontado' => 0,
              'cronograma_total_vencida_moraapagar' => 0,
              'cronograma_total_vencida_acuenta' => 0,
              'cronograma_total_vencida_cuotapago' => 0,
              'cronograma_total_restante_atraso' => 0,
              'cronograma_total_restante_cuota' => 0,
              'cronograma_total_restante_mora' => 0,
              'cronograma_total_restante_moradescontado' => 0,
              'cronograma_total_restante_moraapagar' => 0,
              'cronograma_total_restante_acuenta' => 0,
              'cronograma_total_restante_cuotapago' => 0,
              'cronograma_total_pendiente_atraso' => 0,
              'cronograma_total_pendiente_cuota' => 0,
              'cronograma_total_pendiente_mora' => 0,
              'cronograma_total_pendiente_moradescontado' => 0,
              'cronograma_total_pendiente_moraapagar' => 0,
              'cronograma_total_pendiente_acuenta' => 0,
              'cronograma_total_pendiente_cuotapago' => 0,
              'idmoneda' => 1,
              'idasesor' => Auth::user()->id,
              'idcajero' => 0,
              'idsupervisor' => 0,
              'idcliente' => $request->input('idcliente'),
              'idconyuge' => 0,
              'idgarante' => 0,
              'idprestamo_frecuencia' => 0,
              'idprestamo_tipotasa' => 0,
              'idprestamo_tipocredito' => $idprestamo_tipocredito, // 1=NORMAL, 2=REFINANCIADO,3=REPROGRAMADO,4=AMPLIADO, 5=CAMPAÑA
              'idprestamo_estadocredito' => 2, // 1=NORMAL, 2=GRUPAL
              'idprestamo_creditorefinanciado' => 0,
              'idprestamo_creditoreprogramado' => 0,
              'idprestamo_creditoampliado' => 0,
              'idestadocobranza' => 1, // 1 = PENDIENTE, 2 = CANCELADO
              'idestadocredito' => 1, // pendiente
              'idestadoaprobacion' => 0,
              'idestadodesembolso' => 0,
              'idestadogastoadministrativo' => 0,
              'idtienda' => $idtienda,
              'idestado' => 1,
            ]);
          
            if($prestamo_estadocreditogrupal == 1){
                DB::table('s_prestamo_credito')->whereId($idprestamo_credito)->update([
                  'monto' => $request->input('monto'),
                  'numerocuota' => $request->input('numerocuota'),
                  'fechainiciocero' => $request->fechainicio,
                  'fechainicio' => $cronograma['fechainicio'],
                  'ultimafecha' => $cronograma['ultimafecha'],
                  'numerodias' => $request->input('numerodias') ?? 0,
                  'tasa' => $request->input('tasa'),
                  'cuota' => $cronograma['cuota'],
                  'tipocredito' => $tipocreditonombre,
                  'tipocreditogenerado' => $tipocreditogenerado,
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
                  'idconyuge' => $request->input('idconyuge')!='' ? $request->input('idconyuge') : 0,
                  'idgarante' => $request->input('idgarante')!='' ? $request->input('idgarante') : 0,
                  'idprestamo_frecuencia' => $request->input('idfrecuencia'),
                  'idprestamo_tipotasa' => $cronograma['tipotasa'],
                  'idprestamo_estadocredito' => 1,
                ]);
                foreach($cronograma['cronograma'] as $value) {
                  DB::table('s_prestamo_creditodetalle')->insert([
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
                    'idprestamo_credito' => $idprestamo_credito,
                    'idestadocobranza' => 1,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                  ]);
                }
            }
            
            prestamo_importar_ultimocredito($idtienda,$idprestamo_credito);
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
      
        elseif ($request->input('view') == 'registrar-bien') {

          $rules = [
                'bien_producto' => 'required',
                'bien_valorestimado' => 'required',
                'bien_descripcion' => 'required',
                'bien_idprestamo_documento' => 'required',
            ];
            $messages = [
                'bien_producto.required' => 'El "Producto" es Obligatorio.',
                'bien_descripcion.required' => 'La "Descripción" es Obligatorio.',
                'bien_valorestimado.required' => 'El "Valor Estimado" es Obligatorio.',
                'bien_idprestamo_documento.required' => 'El "Documento" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);

            DB::table('s_prestamo_creditobien')->insert([
                'fecharegistro' => Carbon::now(),
                'producto' => $request->bien_producto,
                'descripcion' => $request->bien_descripcion,
                'valorestimado' => $request->bien_valorestimado,
                'idprestamo_documento' => $request->bien_idprestamo_documento,
                'idprestamo_credito' => $request->idprestamo_credito,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);

        } 
        elseif ($request->input('view') == 'registrar-sustento') {

            $creditosustento = DB::table('s_prestamo_creditosustento')->where('s_prestamo_creditosustento.idprestamo_credito', $request->idprestamo_credito)->first();
            if($creditosustento!=''){
                DB::table('s_prestamo_creditosustento')->whereId($creditosustento->id)->update([
                  'comentarioasesor' => $request->comentarioasesor!=''?$request->comentarioasesor:'',
                  'destinocredito' => $request->destinocredito!=''?$request->destinocredito:'',
                  'riesgonegocio' => $request->riesgonegocio!=''?$request->riesgonegocio:'',
                  'destinoexcendete' => $request->destinoexcendete!=''?$request->destinoexcendete:'',
                  'sustentopropuesta' => $request->sustentopropuesta!=''?$request->sustentopropuesta:'',
                  'idprestamo_credito' => $request->idprestamo_credito,
                  'idprestamo_calificacion' => $request->idcalificacion!='null'?$request->idcalificacion:0,
                  'idprestamo_experienciacredito' => $request->idexperienciacredito!='null'?$request->idexperienciacredito:0,
                  'idprestamo_endeudamientosistema' => $request->idendeudamientosistema!='null'?$request->idendeudamientosistema:0,
                  'idprestamo_inventario' => $request->idinventario!='null'?$request->idinventario:0,
                ]);
            }else{
               DB::table('s_prestamo_creditosustento')->insert([
                  'fecharegistro' => Carbon::now(),
                  'comentarioasesor' => $request->comentarioasesor!=''?$request->comentarioasesor:'',
                  'destinocredito' => $request->destinocredito!=''?$request->destinocredito:'',
                  'riesgonegocio' => $request->riesgonegocio!=''?$request->riesgonegocio:'',
                  'destinoexcendete' => $request->destinoexcendete!=''?$request->destinoexcendete:'',
                  'sustentopropuesta' => $request->sustentopropuesta!=''?$request->sustentopropuesta:'',
                  'idprestamo_credito' => $request->idprestamo_credito,
                  'idprestamo_calificacion' => $request->idcalificacion!=''?$request->idcalificacion:0,
                  'idprestamo_experienciacredito' => $request->idexperienciacredito!=''?$request->idexperienciacredito:0,
                  'idprestamo_endeudamientosistema' => $request->idendeudamientosistema!=''?$request->idendeudamientosistema:0,
                  'idprestamo_inventario' => $request->idinventario!=''?$request->idinventario:0,
                  'idtienda' => $idtienda,
                  'idestado' => 1
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
                                <td style="padding: 8px;text-align: right;">Nº</td>
                                <td style="padding: 8px;text-align: right;">Fecha de Pago</td>
                                <td style="padding: 8px;text-align: right;">Saldo Capital</td>
                                <td style="padding: 8px;text-align: right;">Amortización</td>
                                <td style="padding: 8px;text-align: right;">Interes</td>
                                '.(($cronograma['total_segurodesgravamen']>0)?'<td style="padding: 8px;text-align: right;">Seguro Desgravamen</td>':'').'
                                '.(($cronograma['total_gastoadministrativo']>0)?'<td style="padding: 8px;text-align: right;">Gasto Administrativo</td>':'').'
                                '.((configuracion($tienda->id,'prestamo_estadoacumulado')['valor']==1)?'<td style="padding: 8px;text-align: right;">Acumulado</td>':'').'
                                <td style="padding: 8px;text-align: right;">Cuota</td>
                                '.(($cronograma['total_abono']>0)?'<td style="padding: 8px;text-align: right;">Abono</td>':'').'
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
                              '.((configuracion($tienda->id,'prestamo_estadoacumulado')['valor']==1)?'<td style="padding: 8px;text-align: right;">'.$value['cuotanormal'].' ('.$value['acumulado'].')</td>':'').'
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
                              '.((configuracion($tienda->id,'prestamo_estadoacumulado')['valor']==1)?'<td style="padding: 8px;text-align: right;">'.$cronograma['total_cuotanormal'].' ('.$cronograma['total_acumulado'].')</td>':'').'
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
                'total_acumulado' => $cronograma['total_acumulado'],
                'total_cuotafinal' => $cronograma['total_cuotafinal'],
                'total_abono' => $cronograma['total_abono'],
            ]);
        }
        if ($id == 'show-creditoparalelo') {
          
            $clientes = DB::table('s_prestamo_credito')
                ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
                ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','cliente.idprestamocartera')
                ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
                ->where('s_prestamo_credito.idcliente',$request->idcliente)
                ->where('s_prestamo_credito.idestado', 1)
                ->where('s_prestamo_credito.idtienda', $idtienda)
                ->whereIn('s_prestamo_credito.idestadocredito', [2,3,4])
                ->whereIn('s_prestamo_credito.idestadodesembolso', [0,1])
                ->where('s_prestamo_credito.idestadocobranza', 1)
                ->get();
          
            $html = '<div class="mensaje-info"><b>CRÉDITO PRINCIPAL - TIENE '.count($clientes).' CRÉDITOS</b></div>';
            if(count($clientes)>0){
                $html = '<div class="mensaje-info"><b>CRÉDITO PARALELO - TIENE '.count($clientes).' '.(count($clientes)==1?'CRÉDITO':'CRÉDITOS').'</b></div>';   
            }
   
            return ([
                'html' => $html,
            ]);
        }
        elseif ($id == 'show-imagendomicilio') {
            $prestamodomicilioimagen = DB::table('s_prestamo_creditodomicilioimagen')
              ->where('s_prestamo_creditodomicilioimagen.idprestamo_credito', $request->idprestamocredito)
              ->get();

            $i = 1;
            $html = "";
            foreach($prestamodomicilioimagen as $value) {
                $html .= '<div class="gallery-item">
                    <div class="grid-item-holder">
                        <div class="box-item" style="
                              background-image: url('.url('public/backoffice/tienda/'.$tienda->id.'/creditodomicilio/'.$value->imagen).');
                              background-repeat: no-repeat;
                              background-size: contain;
                              background-position: center;" onclick="$("#imggaleria'.$value->id.'").click()">
                              <form class="js-validation-signin px-30 form-prestamodomicilioimagen'.$value->id.'" action="javascript:;" 
                                  onsubmit="callback({
                                                          route:  \'backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/0\',
                                                          method: \'DELETE\',
                                                          carga: \'#carga-imagendomicilio\',
                                                          data:   {
                                                              view : \'eliminarimagendomicilio\',
                                                              idprestamo_creditodomicilioimagen: '.$value->id.',
                                                          }
                                                      },
                                                      function(resultado){
                                                          imagen_domicilio();
                                                      },this)">
                            </form>
                            <a href="javascript:;" onclick="removeimagendomicilio('.$value->id.')" id="eliminar-imagen">x</a>
                            <div class="orden-imagen">'.$i.'</div>
                        </div>
                    </div>
                </div>';
                $i++;
            }
            return [
                'imagenes' => $html,
            ];
        }
        elseif ($id == 'show-imagennegocio') {
            $prestamolaboralnegocioimagen = DB::table('s_prestamo_creditolaboralnegocioimagen')
              ->where('s_prestamo_creditolaboralnegocioimagen.idprestamo_credito', $request->idprestamocredito)
              ->get();

            $i = 1;
            $html = "";
            foreach($prestamolaboralnegocioimagen as $value) {
                $html .= '<div class="gallery-item">
                    <div class="grid-item-holder">
                        <div class="box-item" style="
                              background-image: url('.url('public/backoffice/tienda/'.$tienda->id.'/creditolaboral/'.$value->imagen).');
                              background-repeat: no-repeat;
                              background-size: contain;
                              background-position: center;" onclick="$("#imggaleria'.$value->id.'").click()">
                            <a href="javascript:;" onclick="removeimagennegocio('.$value->id.')" id="eliminar-imagen">x</a>
                            <div class="orden-imagen">'.$i.'</div>
                        </div>
                    </div>
                </div>';
                $i++;
            }
            return [
                'imagenes' => $html,
            ];
        }
        elseif ($id == 'show-imagenlicenciafuncionamiento') {
            $prestamolaborallicenciafuncionamientoimagen = DB::table('s_prestamo_creditolaborallicenciafuncionamientoimagen')
              ->where('s_prestamo_creditolaborallicenciafuncionamientoimagen.idprestamo_credito', $request->idprestamocredito)
              ->get();

            $i = 1;
            $html = "";
            foreach($prestamolaborallicenciafuncionamientoimagen as $value) {
                $html .= '<div class="gallery-item">
                    <div class="grid-item-holder">
                        <div class="box-item" style="
                              background-image: url('.url('public/backoffice/tienda/'.$tienda->id.'/creditolaboral/'.$value->imagen).');
                              background-repeat: no-repeat;
                              background-size: contain;
                              background-position: center;" onclick="$("#imggaleria'.$value->id.'").click()">
                            <a href="javascript:;" onclick="removeimagenlicenciafuncionamiento('.$value->id.')" id="eliminar-imagen">x</a>
                            <div class="orden-imagen">'.$i.'</div>
                        </div>
                    </div>
                </div>';
                $i++;
            }
            return [
                'imagenes' => $html,
            ];
        }
        elseif ($id == 'show-imagencontratoalquiler') {
            $prestamolaboralcontratoalquilerimagen = DB::table('s_prestamo_creditolaboralcontratoalquilerimagen')
              ->where('s_prestamo_creditolaboralcontratoalquilerimagen.idprestamo_credito', $request->idprestamocredito)
              ->get();

            $i = 1;
            $html = "";
            foreach($prestamolaboralcontratoalquilerimagen as $value) {
                $html .= '<div class="gallery-item">
                    <div class="grid-item-holder">
                        <div class="box-item" style="
                              background-image: url('.url('public/backoffice/tienda/'.$tienda->id.'/creditolaboral/'.$value->imagen).');
                              background-repeat: no-repeat;
                              background-size: contain;
                              background-position: center;" onclick="$("#imggaleria'.$value->id.'").click()">
                            <a href="javascript:;" onclick="removeimagencontratoalquiler('.$value->id.')" id="eliminar-imagen">x</a>
                            <div class="orden-imagen">'.$i.'</div>
                        </div>
                    </div>
                </div>';
                $i++;
            }
            return [
                'imagenes' => $html,
            ];
        }
        elseif ($id == 'show-imagenficharuc') {
            $prestamolaboralficharucimagen = DB::table('s_prestamo_creditolaboralficharucimagen')
              ->where('s_prestamo_creditolaboralficharucimagen.idprestamo_credito', $request->idprestamocredito)
              ->get();

            $i = 1;
            $html = "";
            foreach($prestamolaboralficharucimagen as $value) {
                $html .= '<div class="gallery-item">
                    <div class="grid-item-holder">
                        <div class="box-item" style="
                              background-image: url('.url('public/backoffice/tienda/'.$tienda->id.'/creditolaboral/'.$value->imagen).');
                              background-repeat: no-repeat;
                              background-size: contain;
                              background-position: center;" onclick="$("#imggaleria'.$value->id.'").click()">
                            <a href="javascript:;" onclick="removeimagenficharuc('.$value->id.')" id="eliminar-imagen">x</a>
                            <div class="orden-imagen">'.$i.'</div>
                        </div>
                    </div>
                </div>';
                $i++;
            }
            return [
                'imagenes' => $html,
            ];
        }
        elseif ($id == 'show-imagenreciboagua') {
            $prestamolaboralreciboaguaimagen = DB::table('s_prestamo_creditolaboralreciboaguaimagen')
              ->where('s_prestamo_creditolaboralreciboaguaimagen.idprestamo_credito', $request->idprestamocredito)
              ->get();

            $i = 1;
            $html = "";
            foreach($prestamolaboralreciboaguaimagen as $value) {
                $html .= '<div class="gallery-item">
                    <div class="grid-item-holder">
                        <div class="box-item" style="
                              background-image: url('.url('public/backoffice/tienda/'.$tienda->id.'/creditolaboral/'.$value->imagen).');
                              background-repeat: no-repeat;
                              background-size: contain;
                              background-position: center;" onclick="$("#imggaleria'.$value->id.'").click()">
                            <a href="javascript:;" onclick="removeimagenreciboagua('.$value->id.')" id="eliminar-imagen">x</a>
                            <div class="orden-imagen">'.$i.'</div>
                        </div>
                    </div>
                </div>';
                $i++;
            }
            return [
                'imagenes' => $html,
            ];
        }
        elseif ($id == 'show-imagenreciboluz') {
            $prestamolaboralreciboluzimagen = DB::table('s_prestamo_creditolaboralreciboluzimagen')
              ->where('s_prestamo_creditolaboralreciboluzimagen.idprestamo_credito', $request->idprestamocredito)
              ->get();

            $i = 1;
            $html = "";
            foreach($prestamolaboralreciboluzimagen as $value) {
                $html .= '<div class="gallery-item">
                    <div class="grid-item-holder">
                        <div class="box-item" style="
                              background-image: url('.url('public/backoffice/tienda/'.$tienda->id.'/creditolaboral/'.$value->imagen).');
                              background-repeat: no-repeat;
                              background-size: contain;
                              background-position: center;" onclick="$("#imggaleria'.$value->id.'").click()">
                            <a href="javascript:;" onclick="removeimagenreciboluz('.$value->id.')" id="eliminar-imagen">x</a>
                            <div class="orden-imagen">'.$i.'</div>
                        </div>
                    </div>
                </div>';
                $i++;
            }
            return [
                'imagenes' => $html,
            ];
        }
        elseif ($id == 'show-imagenboletacompra') {
            $prestamolaboralboletacompraimagen = DB::table('s_prestamo_creditolaboralboletacompraimagen')
              ->where('s_prestamo_creditolaboralboletacompraimagen.idprestamo_credito', $request->idprestamocredito)
              ->get();

            $i = 1;
            $html = "";
            foreach($prestamolaboralboletacompraimagen as $value) {
                $html .= '<div class="gallery-item">
                    <div class="grid-item-holder">
                        <div class="box-item" style="
                              background-image: url('.url('public/backoffice/tienda/'.$tienda->id.'/creditolaboral/'.$value->imagen).');
                              background-repeat: no-repeat;
                              background-size: contain;
                              background-position: center;" onclick="$("#imggaleria'.$value->id.'").click()">
                            <a href="javascript:;" onclick="removeimagenboletacompra('.$value->id.')" id="eliminar-imagen">x</a>
                            <div class="orden-imagen">'.$i.'</div>
                        </div>
                    </div>
                </div>';
                $i++;
            }
            return [
                'imagenes' => $html,
            ];
        }

        elseif ($id == 'show-imagenbien') {

            $prestamobienimagen = DB::table('s_prestamo_creditobienimagen')->where('idprestamo_creditobien', $request->idprestamo_creditobien)->get();

            $i = 1;
            $html = "";
            foreach($prestamobienimagen as $value) {
                $html .= '<div class="gallery-item">
                    <div class="grid-item-holder">
                        <div class="box-item" style="
                              background-image: url('.url('public/backoffice/tienda/'.$tienda->id.'/creditobien/'.$value->imagen).');
                              background-repeat: no-repeat;
                              background-size: contain;
                              background-position: center;" onclick="$("#imggaleria'.$value->id.'").click()">
                            <form class="js-validation-signin px-30 form-prestamobienimagen'.$value->id.'" action="javascript:;" 
                                  onsubmit="callback({
                                                          route:  \'backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/0\',
                                                          method: \'DELETE\',
                                                          carga: \'#carga-imagenbien\',
                                                          data:   {
                                                              view : \'eliminarimagenbien\',
                                                              idprestamo_creditobienimagen: '.$value->id.',
                                                          }
                                                      },
                                                      function(resultado){
                                                          imagen_bien('.$value->idprestamo_creditobien.');
                                                      },this)">
                            </form>
                            <a href="javascript:;" onclick="removeimagenbien('.$value->id.')" id="eliminar-imagen">x</a>
                            <div class="orden-imagen">'.$i.'</div>
                        </div>
                    </div>
                </div>';
                $i++;
            }
            return [
                'imagenes' => $html,
            ];
        }
    }
  
    public function edit(Request $request, $idtienda, $id)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')
            ->leftJoin('ubigeo', 'ubigeo.id', 'tienda.idubigeo')
            ->select(
                'tienda.*',
                'ubigeo.nombre as ubigeonombre',
            )
            ->where('tienda.id',$idtienda)
            ->first();
      
        $prestamocredito = DB::table('s_prestamo_credito')
            ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
            ->leftjoin('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_credito.idprestamo_frecuencia')
            ->leftjoin('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
            ->leftjoin('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
            ->leftjoin('users as garante', 'garante.id', 's_prestamo_credito.idgarante')
            ->leftjoin('s_prestamo_creditodomicilio', 's_prestamo_creditodomicilio.idprestamo_credito', 's_prestamo_credito.id')
            ->leftjoin('ubigeo as clienteubigeo', 'clienteubigeo.id', 's_prestamo_creditodomicilio.idubigeo')
            ->leftjoin('ubigeo as garanteubigeo', 'garanteubigeo.id', 'garante.idubigeo')
            ->join('tienda', 'tienda.id', 's_prestamo_credito.idtienda')
            ->leftjoin('users as conyuge', 'conyuge.id', 's_prestamo_credito.idconyuge')
            ->where([
              ['s_prestamo_credito.id', $id],
              ['s_prestamo_credito.idtienda', $idtienda]
            ])
            ->select(
              's_prestamo_credito.*',
              's_prestamo_frecuencia.nombre as frecuencia_nombre',
              's_prestamo_frecuencia.id as idprestamo_frecuencia',
              'tienda.nombre as tiendanombre',
              'cliente.identificacion as clienteidentificacion',
              'cliente.nombre as clientenombre',
              'cliente.apellidos as clienteapellidos',
              's_prestamo_creditodomicilio.direccion as clientedireccion',
              's_prestamo_creditodomicilio.referencia as clientereferencia',
              'clienteubigeo.id as clienteidubigeo',
              'clienteubigeo.nombre as clienteubigeonombre',
              DB::raw('CONCAT(clienteubigeo.distrito, ", ", clienteubigeo.provincia, ", ", clienteubigeo.departamento) as clienteubigeoubicacion'),
              'conyuge.identificacion as conyugeidentificacion',
              'conyuge.nombre as conyugenombre',
              'conyuge.apellidos as conyugeapellidos',
              'garante.identificacion as garanteidentificacion',
              'garante.nombre as garantenombre',
              'garante.apellidos as garanteapellidos',
              'garante.direccion as garantedireccion',
              'garante.referencia as garantereferencia',
              'garanteubigeo.nombre as garanteubigeonombre',
              'asesor.identificacion as asesoridentificacion',
              'asesor.nombre as asesornombre',
              'asesor.apellidos as asesorapellidos',
              's_moneda.simbolo as monedasimbolo',
              DB::raw('IF(asesor.idtipopersona = 1 || asesor.idtipopersona = 3,
                  CONCAT(asesor.identificacion, " - ", asesor.apellidos, ", ", asesor.nombre),
                  CONCAT(asesor.identificacion, " - ", asesor.apellidos)) as asesor_nombre'),
              DB::raw('IF(cliente.idtipopersona = 1 || cliente.idtipopersona = 3,
                  CONCAT(cliente.identificacion, " - ", cliente.apellidos, ", ", cliente.nombre),
                  CONCAT(cliente.identificacion, " - ", cliente.apellidos)) as cliente_nombre'),
              DB::raw('IF(conyuge.idtipopersona = 1 || conyuge.idtipopersona = 3,
                  CONCAT(conyuge.identificacion, " - ", conyuge.apellidos, ", ", conyuge.nombre),
                  CONCAT(conyuge.identificacion, " - ", conyuge.apellidos)) as conyuge_nombre'),
              DB::raw('IF(garante.idtipopersona = 1 || garante.idtipopersona = 3,
                  CONCAT(garante.identificacion, " - ", garante.apellidos, ", ", garante.nombre),
                  CONCAT(garante.identificacion, " - ", garante.apellidos)) as garante_nombre')
            )
            ->first();
      
         
      
        if($request->view == 'editar') {
            $frecuencias  = DB::table('s_prestamo_frecuencia')->get();
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/edit', [
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'frecuencias' => $frecuencias,
            ]);
        }
        elseif ($request->view == 'preaprobar') {
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/preaprobar', [
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
            ]);
        }
        elseif ($request->view == 'detalle') {
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/detalle', [
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
            ]);
        }
        elseif ($request->view == 'eliminar') {
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/eliminar', [
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
            ]);
        }
      
        elseif ($request->view == 'domicilioedit') {
          
            $prestamodomicilio = DB::table('s_prestamo_creditodomicilio')
                ->join('ubigeo', 'ubigeo.id', 's_prestamo_creditodomicilio.idubigeo')
                ->where('s_prestamo_creditodomicilio.idprestamo_credito',  $prestamocredito->id)
                ->select(
                    's_prestamo_creditodomicilio.*',
                    'ubigeo.nombre as nombre_ubigeo',
                    DB::raw('CONCAT(ubigeo.distrito, ", ", ubigeo.provincia, ", ", ubigeo.departamento) as ubigeoubicacion'),
                )
                ->first();
          
            $relaciones = DB::table('s_prestamo_creditorelacion')
                ->join('s_prestamo_tiporelacion', 's_prestamo_tiporelacion.id', 's_prestamo_creditorelacion.idprestamo_tiporelacion')
                ->where([
                    ['s_prestamo_creditorelacion.idprestamo_credito', $prestamocredito->id],
                    ['s_prestamo_creditorelacion.idtienda', $tienda->id],
                ])
                ->select(
                    's_prestamo_creditorelacion.*',
                    's_prestamo_tiporelacion.nombre as nombre_tiporelacion'
                )
                ->orderBy('s_prestamo_creditorelacion.id','asc')
                ->get();
          
            $tiporelaciones = DB::table('s_prestamo_tiporelacion')->get();
          
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/domicilioedit',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'prestamodomicilio' => $prestamodomicilio,
                'relaciones' => $relaciones,
                'tiporelaciones' => $tiporelaciones,
            ]);  
        }
        elseif ($request->view == 'domicilioimagen') {
            $prestamodomicilio = DB::table('s_prestamo_creditodomicilio')
                ->join('ubigeo', 'ubigeo.id', 's_prestamo_creditodomicilio.idubigeo')
                ->where('s_prestamo_creditodomicilio.id', $request->iddomicilio)
                ->select(
                    's_prestamo_creditodomicilio.*',
                    'ubigeo.nombre as nombre_ubigeo'
                )
                ->first();
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/domicilioimagen',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'prestamodomicilio' => $prestamodomicilio,
                'estado' => $request->estado,
            ]);  
        }
      
        elseif ($request->view == 'laboraledit') {
          
            $prestamolaboral = DB::table('s_prestamo_creditolaboral')
                ->leftJoin('s_prestamo_giro', 's_prestamo_giro.id', 's_prestamo_creditolaboral.idprestamo_giro')
                ->leftJoin('ubigeo', 'ubigeo.id', 's_prestamo_creditolaboral.idubigeo')
                ->where('s_prestamo_creditolaboral.idprestamo_credito', $prestamocredito->id)
                ->select(
                    's_prestamo_creditolaboral.*',
                    's_prestamo_giro.nombre as nombre_giro',
                    'ubigeo.nombre as nombre_ubigeo',
                    DB::raw('IF(s_prestamo_creditolaboral.idfuenteingreso = 1,
                        "Dependiente", "Independiente") as fuenteingreso')
                )
                ->first();
          
            $idprestamolavoral = 0;
            if($prestamolaboral!=''){
                $idprestamolavoral = $prestamolaboral->id;
            }
          
            $fuenteingreso = DB::table('s_prestamo_fuenteingreso')->get();
            $giro = DB::table('s_prestamo_giro')->get();

            $laboralventa = DB::table('s_prestamo_creditolaboralventa')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->get();
            $laboralcompra = DB::table('s_prestamo_creditolaboralcompra')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->get();
            $laboralingreso = DB::table('s_prestamo_creditolaboralingreso')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->limit(1)->first();
            $laboralegresogasto = DB::table('s_prestamo_creditolaboralegresogasto')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->get();
            $laboralegresogastofamiliares = DB::table('s_prestamo_creditolaboralegresogastofamiliar')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->get();
            $laboralegresopago = DB::table('s_prestamo_creditolaboralegresopago')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->get();
            $laboralotroingreso = DB::table('s_prestamo_creditolaboralotroingreso')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->get();
            $laboralotrogasto = DB::table('s_prestamo_creditolaboralotrogasto')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->get();
            $laboralservicio = DB::table('s_prestamo_creditolaboralservicio')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->limit(1)->first();
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/laboraledit',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'prestamolaboral' => $prestamolaboral,
                'fuenteingreso' => $fuenteingreso,
                'giro' => $giro,
                'laboralventa' => $laboralventa,
                'laboralcompra' => $laboralcompra,
                'laboralingreso' => $laboralingreso,
                'laboralegresogasto' => $laboralegresogasto,
                'laboralegresogastofamiliares' => $laboralegresogastofamiliares,
                'laboralegresopago' => $laboralegresopago,
                'laboralotroingreso' => $laboralotroingreso,
                'laboralotrogasto' => $laboralotrogasto,
                'laboralservicio' => $laboralservicio
            ]);  
        }
      
        elseif ($request->view == 'bien') {
          
            $bienes = DB::table('s_prestamo_creditobien')
                ->where([
                    ['s_prestamo_creditobien.idprestamo_credito', $prestamocredito->id],
                    ['s_prestamo_creditobien.idtienda', $request->idtienda],
                    ['s_prestamo_creditobien.idestado', 1]
                ])
                ->orderBy('s_prestamo_creditobien.id','desc')
                ->get();
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/bien',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'bienes' => $bienes
            ]);  
        } 
        elseif ($request->view == 'biencreate') {
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/biencreate',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
            ]);  
        }
        elseif ($request->view == 'bienimportar') {
          
            $bienes = DB::table('s_prestamo_creditobien')
                ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_creditobien.idprestamo_credito')
                ->where([
                    ['s_prestamo_creditobien.idprestamo_credito','<>', $prestamocredito->id],
                    ['s_prestamo_credito.idcliente', $prestamocredito->idcliente],
                    ['s_prestamo_creditobien.idtienda', $request->idtienda],
                    ['s_prestamo_creditobien.idestado', 1]
                ])
                ->select('s_prestamo_creditobien.*')
                ->orderBy('s_prestamo_creditobien.id','desc')
                ->get();
          
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/bienimportar',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'bienes' => $bienes,
            ]);  
        }
        elseif ($request->view == 'bienedit') {
            $prestamobien = DB::table('s_prestamo_creditobien')
                ->where('s_prestamo_creditobien.id', $request->idbien)
                ->first();
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/bienedit',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'prestamobien' => $prestamobien,
            ]);  
        }
        elseif ($request->view == 'bienimagen') {
            $prestamobien = DB::table('s_prestamo_creditobien')
                ->where('s_prestamo_creditobien.id', $request->idbien)
                ->first();
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/bienimagen',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'prestamobien' => $prestamobien,
            ]);  
        }
        elseif ($request->view == 'bieneliminar') {
            $prestamobien = DB::table('s_prestamo_creditobien')
                ->where('s_prestamo_creditobien.id', $request->idbien)
                ->first();
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/bieneliminar',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'prestamobien' => $prestamobien,
            ]);  
        }
      
        elseif ($request->view == 'sustento') {
          $sustento = DB::table('s_prestamo_creditosustento')
            ->where('idprestamo_credito', $prestamocredito->id)
            ->first();
          $calificaciones = DB::table('s_prestamo_calificacion')->get();
          $tiporelaciones = DB::table('s_prestamo_tiporelacion')->get();
          
          return view('layouts/backoffice/tienda/sistema/prestamosolicitud/sustento', [
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'sustento' => $sustento,
                'calificaciones' => $calificaciones,
                'tiporelaciones' => $tiporelaciones
          ]);  
        }
      
        elseif ($request->view == 'resultado') {
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/resultado',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
            ]);  
        } 
        elseif ($request->view == 'expediente') {

                $prestamoscredito = DB::table('s_prestamo_credito')
                    ->where('s_prestamo_credito.idestado', 1)
                    ->where('s_prestamo_credito.idtienda', $idtienda)
                    //->where('s_prestamo_credito.idestadocredito', 4)
                    ->where('s_prestamo_credito.idcliente',$prestamocredito->idcliente)
                    ->select(
                          's_prestamo_credito.*',
                    )
                    ->orderBy('s_prestamo_credito.codigo','desc')
                    ->get();

                $credito_tabla = [];
                foreach($prestamoscredito as $valuedetalle){
                    $credito_tabla[] = [
                        'idcredito' => $valuedetalle->id,
                        'creditofechadesembolso' => date_format(date_create($valuedetalle->fechadesembolsado), "d/m/Y h:i A"),
                        'creditocodigo' => str_pad($valuedetalle->codigo, 8, "0", STR_PAD_LEFT),
                        'creditodesembolso' => $valuedetalle->monto,
                    ];
                }
           
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/expediente',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'prestamocreditos' => $credito_tabla,
            ]);  
        } 
        elseif ($request->view == 'expedientedetalleeditar') {

            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/expedientedetalleeditar',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
            ]);  
        } 
        elseif ($request->view == 'expedientedetalle') {
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/expedientedetalle',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
            ]);  
        } 
      
        elseif ($request->view == 'creditopdf-pdf') {
          
          $credito_laboral = DB::table('s_prestamo_creditolaboral')
                ->leftJoin('s_prestamo_giro', 's_prestamo_giro.id', 's_prestamo_creditolaboral.idprestamo_giro')
                ->leftJoin('ubigeo', 'ubigeo.id', 's_prestamo_creditolaboral.idubigeo')
                ->leftJoin('s_prestamo_fuenteingreso', 's_prestamo_fuenteingreso.id', 's_prestamo_creditolaboral.idfuenteingreso')
                ->where([
                    ['s_prestamo_creditolaboral.idprestamo_credito', $prestamocredito->id],
                    ['s_prestamo_creditolaboral.idtienda', $idtienda],
                    ['s_prestamo_creditolaboral.idestado', 1]
                ])
                ->select(
                    's_prestamo_creditolaboral.*',
                    's_prestamo_fuenteingreso.nombre as nombre_fuenteingreso',
                    's_prestamo_giro.nombre as nombre_giro',
                    'ubigeo.nombre as ubigeonombre',
                )
                ->orderBy('s_prestamo_creditolaboral.ingresomensual','desc')
                ->limit(1)
                ->first();
          
          $productos = DB::table('s_prestamo_creditolaboral')
                ->leftJoin('s_prestamo_giro', 's_prestamo_giro.id', 's_prestamo_creditolaboral.idprestamo_giro')
                ->where([
                    ['s_prestamo_creditolaboral.idprestamo_credito', $prestamocredito->id],
                    ['s_prestamo_creditolaboral.idtienda', $idtienda],
                    ['s_prestamo_creditolaboral.idestado', 1]
                ])
                ->select(
                    's_prestamo_giro.nombre as nombre_giro'
                )
                ->orderBy('s_prestamo_creditolaboral.id','desc')
                ->get();
          
          $bienes = DB::table('s_prestamo_creditobien')
                ->where([
                    ['s_prestamo_creditobien.idprestamo_credito', $prestamocredito->id],
                    ['s_prestamo_creditobien.idtienda', $idtienda],
                    ['s_prestamo_creditobien.idestado', 1]
                ])
                ->orderBy('s_prestamo_creditobien.id','desc')
                ->get();

          
          $prestamosustento = DB::table('s_prestamo_creditosustento')
                ->join('s_prestamo_calificacion', 's_prestamo_calificacion.id', 's_prestamo_creditosustento.idprestamo_calificacion')
                ->where('s_prestamo_creditosustento.idprestamo_credito', $prestamocredito->id)
                ->select(
                  's_prestamo_creditosustento.*',
                  's_prestamo_calificacion.nombre as calificacion',
                )
                ->limit(1)
                ->first();
          
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamosolicitud/creditopdf-pdf',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'credito_laboral' => $credito_laboral,
                'bienes' => $bienes,
                'prestamosustento' => $prestamosustento,
                'productos' => $productos,
            ]);  
            return $pdf->stream('SOLICITUD_DE_CREDITO.pdf');
        }
        elseif ($request->view == 'cualitativopdf-pdf') {
          $sustento = DB::table('s_prestamo_creditosustento')
            ->where('idprestamo_credito', $prestamocredito->id)
            ->first();
          
          $calificacion = '';
          if($sustento!=''){
              $calificacion = DB::table('s_prestamo_calificacion')->whereId($sustento->idprestamo_calificacion)->first();
          }
          
          
          $prestamolaboral = DB::table('s_prestamo_creditolaboral')
                ->leftJoin('s_prestamo_giro', 's_prestamo_giro.id', 's_prestamo_creditolaboral.idprestamo_giro')
                ->leftJoin('s_prestamo_fuenteingreso', 's_prestamo_fuenteingreso.id', 's_prestamo_creditolaboral.idfuenteingreso')
                ->where('s_prestamo_creditolaboral.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditolaboral.idtienda',  $tienda->id)
                ->select(
                    's_prestamo_creditolaboral.*',
                    's_prestamo_fuenteingreso.nombre as nombre_fuenteingreso',
                    's_prestamo_giro.nombre as nombre_giro',
                )
                ->first();
          
          $prestamodomicilio = DB::table('s_prestamo_creditodomicilio')
                ->where('s_prestamo_creditodomicilio.idprestamo_credito',  $prestamocredito->id)
                ->where('s_prestamo_creditodomicilio.idtienda',  $tienda->id)
                ->select(
                    's_prestamo_creditodomicilio.*'
                )
                ->first();
          
          $prestamobien = DB::table('s_prestamo_creditobien')
                ->where('s_prestamo_creditobien.idprestamo_credito',  $prestamocredito->id)
                ->where('s_prestamo_creditobien.idtienda',  $tienda->id)
                ->select(
                    's_prestamo_creditobien.*'
                )
                ->limit(1)
                ->orderBy('s_prestamo_creditobien.valorestimado','desc')
                ->first();
          
          $numeroentidades = DB::table('s_prestamo_creditolaboralegresopago')
                ->join('s_prestamo_creditolaboral', 's_prestamo_creditolaboral.id', 's_prestamo_creditolaboralegresopago.s_idprestamo_creditolaboral')
                ->where('s_prestamo_creditolaboral.idprestamo_credito', $prestamocredito->id)
                ->count();
          
          $referencia1 = DB::table('s_prestamo_creditorelacion')
              ->join('s_prestamo_tiporelacion', 's_prestamo_tiporelacion.id', 's_prestamo_creditorelacion.idprestamo_tiporelacion')
              ->where([
                  ['s_prestamo_creditorelacion.idprestamo_credito', $prestamocredito->id],
                  ['s_prestamo_creditorelacion.idtienda', $tienda->id],
              ])
              ->select(
                  's_prestamo_creditorelacion.*',
                  's_prestamo_tiporelacion.nombre as nombre_tiporelacion',
                  's_prestamo_creditorelacion.personanombre as completo_persona',
              )
              ->orderBy('s_prestamo_creditorelacion.id','asc')
              ->offset(0)
              ->limit(1)
              ->first();
          
          $referencia2 = DB::table('s_prestamo_creditorelacion')
              ->join('s_prestamo_tiporelacion', 's_prestamo_tiporelacion.id', 's_prestamo_creditorelacion.idprestamo_tiporelacion')
              ->where([
                  ['s_prestamo_creditorelacion.idprestamo_credito', $prestamocredito->id],
                  ['s_prestamo_creditorelacion.idtienda', $tienda->id],
              ])
              ->select(
                  's_prestamo_creditorelacion.*',
                  's_prestamo_tiporelacion.nombre as nombre_tiporelacion',
                  's_prestamo_creditorelacion.personanombre as completo_persona',
              )
              ->orderBy('s_prestamo_creditorelacion.id','asc')
              ->offset(1)
              ->limit(1)
              ->first();
           
          $referencia3 = DB::table('s_prestamo_creditorelacion')
              ->join('s_prestamo_tiporelacion', 's_prestamo_tiporelacion.id', 's_prestamo_creditorelacion.idprestamo_tiporelacion')
              ->where([
                  ['s_prestamo_creditorelacion.idprestamo_credito', $prestamocredito->id],
                  ['s_prestamo_creditorelacion.idtienda', $tienda->id],
              ])
              ->select(
                  's_prestamo_creditorelacion.*',
                  's_prestamo_tiporelacion.nombre as nombre_tiporelacion',
                  's_prestamo_creditorelacion.personanombre as completo_persona',
              )
              ->orderBy('s_prestamo_creditorelacion.id','asc')
              ->offset(2)
              ->limit(1)
              ->first();
          
          $referencia4 = DB::table('s_prestamo_creditorelacion')
              ->join('s_prestamo_tiporelacion', 's_prestamo_tiporelacion.id', 's_prestamo_creditorelacion.idprestamo_tiporelacion')
              ->where([
                  ['s_prestamo_creditorelacion.idprestamo_credito', $prestamocredito->id],
                  ['s_prestamo_creditorelacion.idtienda', $tienda->id],
              ])
              ->select(
                  's_prestamo_creditorelacion.*',
                  's_prestamo_tiporelacion.nombre as nombre_tiporelacion',
                  's_prestamo_creditorelacion.personanombre as completo_persona',
              )
              ->orderBy('s_prestamo_creditorelacion.id','asc')
              ->offset(3)
              ->limit(1)
              ->first();
          
          $referencia5 = DB::table('s_prestamo_creditorelacion')
              ->join('s_prestamo_tiporelacion', 's_prestamo_tiporelacion.id', 's_prestamo_creditorelacion.idprestamo_tiporelacion')
              ->where([
                  ['s_prestamo_creditorelacion.idprestamo_credito', $prestamocredito->id],
                  ['s_prestamo_creditorelacion.idtienda', $tienda->id],
              ])
              ->select(
                  's_prestamo_creditorelacion.*',
                  's_prestamo_tiporelacion.nombre as nombre_tiporelacion',
                  's_prestamo_creditorelacion.personanombre as completo_persona',
              )
              ->orderBy('s_prestamo_creditorelacion.id','asc')
              ->offset(4)
              ->limit(1)
              ->first();
          
          $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamosolicitud/cualitativopdf-pdf',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'sustento' => $sustento,
                'calificacion' => $calificacion,
                'prestamolaboral' => $prestamolaboral,
                'prestamodomicilio' => $prestamodomicilio,
                'prestamobien' => $prestamobien,
                'numeroentidades' => $numeroentidades,
                'referencia1' => $referencia1,
                'referencia2' => $referencia2,
                'referencia3' => $referencia3,
                'referencia4' => $referencia4,
                'referencia5' => $referencia5,
          ]);  
          return $pdf->stream('ANALISIS_CUALITATIVO.pdf');
        }
        elseif ($request->view == 'evaluacionpdf-pdf') {
          
            $prestamolaboral = DB::table('s_prestamo_creditolaboral')
                ->leftJoin('s_prestamo_giro', 's_prestamo_giro.id', 's_prestamo_creditolaboral.idprestamo_giro')
                ->leftJoin('s_prestamo_fuenteingreso', 's_prestamo_fuenteingreso.id', 's_prestamo_creditolaboral.idfuenteingreso')
                ->where('s_prestamo_creditolaboral.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditolaboral.idtienda',  $tienda->id)
                ->select(
                    's_prestamo_creditolaboral.*',
                    's_prestamo_fuenteingreso.nombre as nombre_fuenteingreso',
                    's_prestamo_giro.nombre as nombre_giro',
                )
                ->first();
          
            $idprestamolavoral = 0;
            if($prestamolaboral!=''){
                $idprestamolavoral = $prestamolaboral->id;
            }
          
            $laboralventa = DB::table('s_prestamo_creditolaboralventa')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->limit(10)->get();
            $laboralcompra = DB::table('s_prestamo_creditolaboralcompra')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->limit(10)->get();
            $laboralingreso = DB::table('s_prestamo_creditolaboralingreso')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->limit(1)->first();
            $laboralegresopago = DB::table('s_prestamo_creditolaboralegresopago')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->limit(10)->get();
            $laboralotroingreso = DB::table('s_prestamo_creditolaboralotroingreso')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->limit(10)->get();
            $laboralotrogasto = DB::table('s_prestamo_creditolaboralotrogasto')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->orderBy('id','asc')->limit(10)->get();
            $laboralservicio = DB::table('s_prestamo_creditolaboralservicio')->where('s_idprestamo_creditolaboral', $idprestamolavoral)->limit(1)->first();
          
            
            $laborallegresogasto = DB::table('s_prestamo_creditolaboralegresogasto')
                ->where('s_idprestamo_creditolaboral', $idprestamolavoral)
                ->orderBy('s_prestamo_creditolaboralegresogasto.id','asc')
                ->get(); 
            $laborallegresogastofamiliar = DB::table('s_prestamo_creditolaboralegresogastofamiliar')
                ->where('s_idprestamo_creditolaboral', $idprestamolavoral)
                ->orderBy('s_prestamo_creditolaboralegresogastofamiliar.id','asc')
                ->get(); 
          
            $prestamocredito_resultado = prestamo_resultado_solicitud($idtienda,$prestamocredito->id);
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamosolicitud/evaluacionpdf-pdf',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'prestamolaboral' => $prestamolaboral,
                'idprestamolavoral' => $idprestamolavoral,
                'laboralventa' => $laboralventa,
                'laboralcompra' => $laboralcompra,
                'laboralingreso' => $laboralingreso,
                'laboralegresopago' => $laboralegresopago,
                'laboralotroingreso' => $laboralotroingreso,
                'laboralotrogasto' => $laboralotrogasto,
                'laboralservicio' => $laboralservicio,
                'laborallegresogasto' => $laborallegresogasto,
                'laborallegresogastofamiliar' => $laborallegresogastofamiliar,
                'prestamocredito_resultado' => $prestamocredito_resultado,
            ]);  
            return $pdf->stream('HOJA_DE_EVALUACION.pdf');
        }
        elseif ($request->view == 'negociopdf-pdf') {
  

            $prestamolaboral = DB::table('s_prestamo_creditolaboral')
                ->leftJoin('s_prestamo_giro', 's_prestamo_giro.id', 's_prestamo_creditolaboral.idprestamo_giro')
                ->leftJoin('s_prestamo_fuenteingreso', 's_prestamo_fuenteingreso.id', 's_prestamo_creditolaboral.idfuenteingreso')
                ->where('s_prestamo_creditolaboral.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditolaboral.idtienda',  $idtienda)
                ->select(
                    's_prestamo_creditolaboral.*',
                    's_prestamo_fuenteingreso.nombre as nombre_fuenteingreso',
                    's_prestamo_giro.nombre as nombre_giro',
                )
                ->first();
          
            $prestamolaboralnegocioimagen1 = DB::table('s_prestamo_creditolaboralnegocioimagen')
                ->where('s_prestamo_creditolaboralnegocioimagen.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditolaboralnegocioimagen.idtienda',  $idtienda)
                ->offset(0)
                ->limit(1)
                ->orderBy('s_prestamo_creditolaboralnegocioimagen.id','asc')
                ->first();
          
            $prestamolaboralnegocioimagen2 = DB::table('s_prestamo_creditolaboralnegocioimagen')
                ->where('s_prestamo_creditolaboralnegocioimagen.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditolaboralnegocioimagen.idtienda',  $idtienda)
                ->offset(1)
                ->limit(1)
                ->orderBy('s_prestamo_creditolaboralnegocioimagen.id','asc')
                ->first();
            $prestamolaboralnegocioimagen3 = DB::table('s_prestamo_creditolaboralnegocioimagen')
                ->where('s_prestamo_creditolaboralnegocioimagen.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditolaboralnegocioimagen.idtienda',  $idtienda)
                ->offset(2)
                ->limit(1)
                ->orderBy('s_prestamo_creditolaboralnegocioimagen.id','asc')
                ->first();
            $prestamolaboralnegocioimagen4 = DB::table('s_prestamo_creditolaboralnegocioimagen')
                ->where('s_prestamo_creditolaboralnegocioimagen.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditolaboralnegocioimagen.idtienda',  $idtienda)
                ->offset(3)
                ->limit(1)
                ->orderBy('s_prestamo_creditolaboralnegocioimagen.id','asc')
                ->first();
            $prestamolaboralnegocioimagen5 = DB::table('s_prestamo_creditolaboralnegocioimagen')
                ->where('s_prestamo_creditolaboralnegocioimagen.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditolaboralnegocioimagen.idtienda',  $idtienda)
                ->offset(4)
                ->limit(1)
                ->orderBy('s_prestamo_creditolaboralnegocioimagen.id','asc')
                ->first();
            $prestamolaboralnegocioimagen6 = DB::table('s_prestamo_creditolaboralnegocioimagen')
                ->where('s_prestamo_creditolaboralnegocioimagen.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditolaboralnegocioimagen.idtienda',  $idtienda)
                ->offset(5)
                ->limit(1)
                ->orderBy('s_prestamo_creditolaboralnegocioimagen.id','asc')
                ->first();
            $prestamolaboralnegocioimagen7 = DB::table('s_prestamo_creditolaboralnegocioimagen')
                ->where('s_prestamo_creditolaboralnegocioimagen.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditolaboralnegocioimagen.idtienda',  $idtienda)
                ->offset(6)
                ->limit(1)
                ->orderBy('s_prestamo_creditolaboralnegocioimagen.id','asc')
                ->first();
            $prestamolaboralnegocioimagen8 = DB::table('s_prestamo_creditolaboralnegocioimagen')
                ->where('s_prestamo_creditolaboralnegocioimagen.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditolaboralnegocioimagen.idtienda',  $idtienda)
                ->offset(7)
                ->limit(1)
                ->orderBy('s_prestamo_creditolaboralnegocioimagen.id','asc')
                ->first();
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamosolicitud/negociopdf-pdf',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'prestamolaboral' => $prestamolaboral,
                'prestamolaboralnegocioimagen1' => $prestamolaboralnegocioimagen1,
                'prestamolaboralnegocioimagen2' => $prestamolaboralnegocioimagen2,
                'prestamolaboralnegocioimagen3' => $prestamolaboralnegocioimagen3,
                'prestamolaboralnegocioimagen4' => $prestamolaboralnegocioimagen4,
                'prestamolaboralnegocioimagen5' => $prestamolaboralnegocioimagen5,
                'prestamolaboralnegocioimagen6' => $prestamolaboralnegocioimagen6,
                'prestamolaboralnegocioimagen7' => $prestamolaboralnegocioimagen7,
                'prestamolaboralnegocioimagen8' => $prestamolaboralnegocioimagen8,
            ]);  
            return $pdf->stream('INFORMACION_DE_NEGOCIO.pdf');
        }
        elseif ($request->view == 'domiciliopdf-pdf') {
  
            $prestamodomicilio = DB::table('s_prestamo_creditodomicilio')
                ->where('s_prestamo_creditodomicilio.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditodomicilio.idtienda',  $idtienda)
                ->select(
                    's_prestamo_creditodomicilio.*'
                )
                ->first();
          
          
            $prestamodomicilioimagen1 = DB::table('s_prestamo_creditodomicilioimagen')
                ->where('s_prestamo_creditodomicilioimagen.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditodomicilioimagen.idtienda',  $idtienda)
                ->offset(0)
                ->limit(1)
                ->orderBy('s_prestamo_creditodomicilioimagen.id','asc')
                ->first();
          
            $prestamodomicilioimagen2 = DB::table('s_prestamo_creditodomicilioimagen')
                ->where('s_prestamo_creditodomicilioimagen.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditodomicilioimagen.idtienda',  $idtienda)
                ->offset(1)
                ->limit(1)
                ->orderBy('s_prestamo_creditodomicilioimagen.id','asc')
                ->first();
          
            $prestamodomicilioimagen3 = DB::table('s_prestamo_creditodomicilioimagen')
                ->where('s_prestamo_creditodomicilioimagen.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditodomicilioimagen.idtienda',  $idtienda)
                ->offset(2)
                ->limit(1)
                ->orderBy('s_prestamo_creditodomicilioimagen.id','asc')
                ->first();
          
            $prestamodomicilioimagen4 = DB::table('s_prestamo_creditodomicilioimagen')
                ->where('s_prestamo_creditodomicilioimagen.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditodomicilioimagen.idtienda',  $idtienda)
                ->offset(3)
                ->limit(1)
                ->orderBy('s_prestamo_creditodomicilioimagen.id','asc')
                ->first();
          
            $prestamodomicilioimagen5 = DB::table('s_prestamo_creditodomicilioimagen')
                ->where('s_prestamo_creditodomicilioimagen.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditodomicilioimagen.idtienda',  $idtienda)
                ->offset(4)
                ->limit(1)
                ->orderBy('s_prestamo_creditodomicilioimagen.id','asc')
                ->first();
          
            $prestamodomicilioimagen6 = DB::table('s_prestamo_creditodomicilioimagen')
                ->where('s_prestamo_creditodomicilioimagen.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditodomicilioimagen.idtienda',  $idtienda)
                ->offset(5)
                ->limit(1)
                ->orderBy('s_prestamo_creditodomicilioimagen.id','asc')
                ->first();
          
            $prestamodomicilioimagen7 = DB::table('s_prestamo_creditodomicilioimagen')
                ->where('s_prestamo_creditodomicilioimagen.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditodomicilioimagen.idtienda',  $idtienda)
                ->offset(6)
                ->limit(1)
                ->orderBy('s_prestamo_creditodomicilioimagen.id','asc')
                ->first();
          
            $prestamodomicilioimagen8 = DB::table('s_prestamo_creditodomicilioimagen')
                ->where('s_prestamo_creditodomicilioimagen.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditodomicilioimagen.idtienda',  $idtienda)
                ->offset(7)
                ->limit(1)
                ->orderBy('s_prestamo_creditodomicilioimagen.id','asc')
                ->first();
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamosolicitud/domiciliopdf-pdf',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'prestamodomicilio' => $prestamodomicilio,
                'prestamodomicilioimagen1' => $prestamodomicilioimagen1,
                'prestamodomicilioimagen2' => $prestamodomicilioimagen2,
                'prestamodomicilioimagen3' => $prestamodomicilioimagen3,
                'prestamodomicilioimagen4' => $prestamodomicilioimagen4,
                'prestamodomicilioimagen5' => $prestamodomicilioimagen5,
                'prestamodomicilioimagen6' => $prestamodomicilioimagen6,
                'prestamodomicilioimagen7' => $prestamodomicilioimagen7,
                'prestamodomicilioimagen8' => $prestamodomicilioimagen8,
            ]);  
            return $pdf->stream('INFORMACION_DE_DOMICILIO.pdf');
        }
        elseif ($request->view == 'garantiapdf-pdf') {
  
            $prestamolaboral = DB::table('s_prestamo_creditolaboral')
                ->where('s_prestamo_creditolaboral.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditolaboral.idtienda',  $idtienda)
                ->select(
                    's_prestamo_creditolaboral.*'
                )
                ->first();
          
            $prestamobien1 = DB::table('s_prestamo_creditobien')
                ->where('s_prestamo_creditobien.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditobien.idtienda',  $idtienda)
                ->limit(3)
                ->orderBy('s_prestamo_creditobien.id','asc')
                ->get();  
            $prestamobien2 = DB::table('s_prestamo_creditobien')
                ->where('s_prestamo_creditobien.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditobien.idtienda',  $idtienda)
                ->offset(3)
                ->limit(3)
                ->orderBy('s_prestamo_creditobien.id','asc')
                ->get();  
            $prestamobien3 = DB::table('s_prestamo_creditobien')
                ->where('s_prestamo_creditobien.idprestamo_credito', $prestamocredito->id)
                ->where('s_prestamo_creditobien.idtienda',  $idtienda)
                ->offset(6)
                ->limit(3)
                ->orderBy('s_prestamo_creditobien.id','asc')
                ->get();
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamosolicitud/garantiapdf-pdf',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'prestamolaboral' => $prestamolaboral,
                'prestamobien1' => $prestamobien1,
                'prestamobien2' => $prestamobien2,
                'prestamobien3' => $prestamobien3,
            ]);  
            return $pdf->stream('GARANTIAS.pdf');
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(), $idtienda);
        
        if ($request->input('view') == 'editar') {
            $rules = [
                'idcliente' => 'required',
            ];
          
            if($request->input('check_idconyuge')=='on'){
                $rules = array_merge($rules,[
                    'idconyuge' => 'required'
                ]);
              
            }
            
            if($request->input('check_idgarante')=='on'){
                $rules = array_merge($rules,[
                    'idgarante' => 'required'
                ]);
            }
          
            $monto = 0;
            $prestamo_estadocreditogrupal = 0;
            if(configuracion($idtienda,'prestamo_estadocreditogrupal')['valor']==1){
                if($request->input('check_estadocreditogrupal')=='on'){
                    $rules = array_merge($rules,[
                      'montogrupal' => 'required',
                    ]);
                    $monto = $request->montogrupal;
                }else{
                    $prestamo_estadocreditogrupal = 1;
                }
            }else{
                $prestamo_estadocreditogrupal = 1;
            }
          
            if($prestamo_estadocreditogrupal == 1){
                if(configuracion($idtienda,'prestamo_estadoabono')['valor']=='on'){
                    $rules = array_merge($rules,[
                        'abono' => 'required'
                    ]);
                }
                $rules = array_merge($rules,[
                  'monto' => 'required',
                  'numerocuota' => 'required',
                  'fechainicio' => 'required',
                  'idfrecuencia' => 'required',
                  'tasa' => 'required',
                ]);
                $monto = $request->monto;
            }  
          
            $messages = [
                'idcliente.required' => 'El "Cliente" es Obligatorio.',
                'idconyuge.required' => 'El "Cónyuge" es Obligatorio.',
                'idgarante.required' => 'El "Garante" es Obligatorio.',
                'monto.required' => 'El "Monto" es Obligatorio.',
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
          
            // tipo de credito
            $tipocreditonombre = 'NORMAL';
            $idprestamo_tipocredito = 1;
            if(configuracion($idtienda,'prestamo_tipocredito')['resultado']=='CORRECTO'){
                $tipocreditonombre = $request->tipocreditonombre;
                $idprestamo_tipocredito = 5;
            }
    
            $clientes = DB::table('s_prestamo_credito')
                ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
                ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','cliente.idprestamocartera')
                ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
                ->where('s_prestamo_credito.idcliente',$request->idcliente)
                ->where('s_prestamo_credito.idestado', 1)
                ->where('s_prestamo_credito.idtienda', $idtienda)
                ->whereIn('s_prestamo_credito.idestadocredito', [2,3,4])
                ->whereIn('s_prestamo_credito.idestadodesembolso', [0,1])
                ->where('s_prestamo_credito.idestadocobranza', 1)
                ->get();
          
            $tipocreditogenerado = 'CRÉDITO PRINCIPAL';
            if(count($clientes)>0){
                $tipocreditogenerado = 'CRÉDITO PARALELO';   
            }
          
            $gastoadministrativo = !is_null($request->gastoadministrativo) ? $request->gastoadministrativo : 0;
            $cronograma = prestamo_cronograma(
                $idtienda,
                $request->monto,
                $request->numerocuota,
                $request->fechainicio,
                $request->idfrecuencia,
                $request->numerodias,
                $request->tasa,
                $gastoadministrativo,
                $request->excluirferiado,
                $request->excluirsabado,
                $request->excluirdomingo,
                $request->abono
            );
       
            //$idestadoexpediente = $request->idestadoexpediente!='undefined'?'si':'no';

            DB::table('s_prestamo_credito')->whereId($id)->update([
                /*'monto' => $request->input('monto'),
                'numerocuota' => $request->input('numerocuota'),
                'fechainicio' => $cronograma['fechainicio'],
                'fechainiciocero' => $request->fechainicio,
                'ultimafecha' => $cronograma['ultimafecha'],
                'numerodias' => $request->input('numerodias') ?? 0,
                'tasa' => $request->input('tasa'),
                'cuota' => $cronograma['cuota'],
                'tipocredito' => $tipocreditonombre,
                'tipocreditogenerado' => $tipocreditogenerado,
                'excluirsabado' => $request->input('excluirsabado') ?? '',
                'excluirdomingo' => $request->input('excluirdomingo') ?? '',
                'excluirferiado' => $request->input('excluirferiado') ?? '',
                'total_amortizacion' => $cronograma['total_amortizacion'],
                'total_interes' => $cronograma['total_interes'],
                'total_cuota' => $cronograma['total_cuota'],
                'total_segurodesgravamen' => $cronograma['total_segurodesgravamen'],
                'total_cuotafinal' => $cronograma['total_cuotafinal'],
                'total_abono' => $cronograma['total_abono'],
                'total_cuotafinaltotal' => $cronograma['total_cuotafinaltotal'],*/
                'monto' => $monto,
                'numerocuota' => 0,
                'fechainiciocero' => '2000-01-01',
                'fechainicio' => '2000-01-01',
                'ultimafecha' => '2000-01-01',
                'numerodias' => 0,  
                'tasa' => 0,
                'cuota' => 0,
                'tipocredito' => '',
                'tipocreditogenerado' => '',
                'excluirsabado' => '',
                'excluirdomingo' => '',
                'excluirferiado' => '',
                'total_amortizacion' => 0,
                'total_interes' => 0,
                'total_cuota' => 0,
                'total_gastoadministrativo' => 0,
                'total_segurodesgravamen' => 0,
                'total_cuotafinal' => 0,
                'total_abono' => 0,
                'total_cuotafinaltotal' => 0,
                'idprestamo_tipocredito' => $idprestamo_tipocredito, // 1=NORMAL, 2=REFINANCIADO,3=REPROGRAMADO,4=AMPLIADO, 5=CAMPAÑA
                'idprestamo_estadocredito' => 2, // 1=NORMAL, 2=GRUPAL
                //'estadoexpediente' => $idestadoexpediente,
                //'idasesor' => Auth::user()->id,
                //'idcliente' => $request->input('idcliente'),
                'idconyuge' => 0,
                'idgarante' => 0,
                'idprestamo_frecuencia' => 0,
                'idprestamo_tipotasa' => 0,
            ]);
          
            DB::table('s_prestamo_creditodetalle')->where('idprestamo_credito',$id)->delete();
            if($prestamo_estadocreditogrupal == 1){
                DB::table('s_prestamo_credito')->whereId($id)->update([
                  'monto' => $request->input('monto'),
                  'numerocuota' => $request->input('numerocuota'),
                  'fechainicio' => $cronograma['fechainicio']=='0000-00-00'?'2000-01-01':$cronograma['fechainicio'],
                  'fechainiciocero' => $request->fechainicio=='0000-00-00'?'2000-01-01':$request->fechainicio,
                  'ultimafecha' => $cronograma['ultimafecha']=='0000-00-00'?'2000-01-01':$cronograma['ultimafecha'],
                  'numerodias' => $request->input('numerodias') ?? 0,
                  'tasa' => $request->input('tasa'),
                  'cuota' => $cronograma['cuota'],
                  'tipocredito' => $tipocreditonombre,
                  'tipocreditogenerado' => $tipocreditogenerado,
                  'excluirsabado' => $request->input('excluirsabado') ?? '',
                  'excluirdomingo' => $request->input('excluirdomingo') ?? '',
                  'excluirferiado' => $request->input('excluirferiado') ?? '',
                  'total_amortizacion' => $cronograma['total_amortizacion'],
                  'total_interes' => $cronograma['total_interes'],
                  'total_cuota' => $cronograma['total_cuota'],
                  'total_segurodesgravamen' => $cronograma['total_segurodesgravamen'],
                  'total_cuotanormal' => $cronograma['total_cuotanormal'],
                  'total_acumulado' => $cronograma['total_acumulado'],
                  'total_cuotafinal' => $cronograma['total_cuotafinal'],
                  'total_abono' => $cronograma['total_abono'],
                  'total_cuotafinaltotal' => $cronograma['total_cuotafinaltotal'],
                  'idconyuge' => $request->input('idconyuge')!='' ? $request->input('idconyuge') : 0,
                  'idgarante' => $request->input('idgarante')!='' ? $request->input('idgarante') : 0,
                  'idprestamo_frecuencia' => $request->input('idfrecuencia'),
                  'idprestamo_tipotasa' => $cronograma['tipotasa'],
                  'idprestamo_estadocredito' => 1,
                ]);
              
                foreach($cronograma['cronograma'] as $value) {
                  DB::table('s_prestamo_creditodetalle')->insert([
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
                    'totalfinal' => $value['cuotafinaltotal'],
                    'atraso' => 0,
                    'mora' => 0,
                    'moradescuento' => 0,
                    'moraapagar' => 0,
                    'cuotapago' => 0,
                    'acuenta' => 0,
                    'cuotaapagar' => 0,
                    'abono' => $value['abono'],
                    'cuotaapagartotal' => 0,
                    'montorefinanciado' => 0,
                    'interesdescontado' => 0,
                    'idprestamo_credito' => $id,
                    'idestadocobranza' => 1,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                  ]);
                }
            }

                
          
            
             
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        /*elseif ($request->input('view') == 'editar-expediente') {

            DB::table('s_prestamo_credito')->whereId($id)->update([
                'estadoexpediente' => $request->idestadoexpediente!='undefined'?'si':'no',
            ]);
         
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }*/
        elseif ($request->input('view') == 'preaprobar') {
            
            $credito = DB::table('s_prestamo_credito')->whereId($id)->first();
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
            if($credito->estadoexpediente!='no'){
                $prestamocredito_resultado = prestamo_resultado_solicitud($idtienda,$id);
                if($prestamocredito_resultado['resultado']=='DESAPROBADO'){
                    return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'El Crédito esta DESAPROBADO, revise la Solicitud de Crédito.'
                    ]);
                }
            }

            DB::table('s_prestamo_credito')->whereId($id)->update([
              'fechapreaprobado' => Carbon::now(),
              'idestadocredito' => 2
            ]);
            
            if($credito->estadoexpediente=='no'){
                // Domicilio
                $prestamo_domicilioimagen = DB::table('s_prestamo_creditodomicilioimagen')->where('idprestamo_credito', $id)->get();
                foreach($prestamo_domicilioimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditodomicilio/');
                }
                DB::table('s_prestamo_creditodomicilioimagen')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->delete(); 
                DB::table('s_prestamo_creditorelacion')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->delete();
                $prestamo_creditodomicilio = DB::table('s_prestamo_creditodomicilio')->where('idtienda',$idtienda)->where('idprestamo_credito',$id)->first();
                if($prestamo_creditodomicilio!=''){
                    uploadfile_eliminar($prestamo_creditodomicilio->imagensuministro,'/public/backoffice/tienda/'.$idtienda.'/creditodomicilio/');
                    uploadfile_eliminar($prestamo_creditodomicilio->imagenfachada,'/public/backoffice/tienda/'.$idtienda.'/creditodomicilio/');
                }
                DB::table('s_prestamo_creditodomicilio')->where('idtienda',$idtienda)->where('idprestamo_credito',$id)->delete();

                // Laboral
                $prestamo_creditolaboral = DB::table('s_prestamo_creditolaboral')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->first();
                if($prestamo_creditolaboral!=''){
                    DB::table('s_prestamo_creditolaboralingreso')->where('s_idprestamo_creditolaboral', $prestamo_creditolaboral->id)->delete();
                    DB::table('s_prestamo_creditolaboralventa')->where('s_idprestamo_creditolaboral', $prestamo_creditolaboral->id)->delete();
                    DB::table('s_prestamo_creditolaboralcompra')->where('s_idprestamo_creditolaboral', $prestamo_creditolaboral->id)->delete();
                    DB::table('s_prestamo_creditolaboralservicio')->where('s_idprestamo_creditolaboral', $prestamo_creditolaboral->id)->delete();
                    DB::table('s_prestamo_creditolaboralegresogasto')->where('s_idprestamo_creditolaboral', $prestamo_creditolaboral->id)->delete();
                    DB::table('s_prestamo_creditolaboralegresogastofamiliar')->where('s_idprestamo_creditolaboral', $prestamo_creditolaboral->id)->delete();
                    DB::table('s_prestamo_creditolaboralegresopago')->where('s_idprestamo_creditolaboral', $prestamo_creditolaboral->id)->delete();
                    DB::table('s_prestamo_creditolaboralotroingreso')->where('s_idprestamo_creditolaboral', $prestamo_creditolaboral->id)->delete();
                    DB::table('s_prestamo_creditolaboralotrogasto')->where('s_idprestamo_creditolaboral', $prestamo_creditolaboral->id)->delete();
                    DB::table('s_prestamo_creditolaboral')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->delete();
                    uploadfile_eliminar($prestamo_creditolaboral->imagensuministro,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                    uploadfile_eliminar($prestamo_creditolaboral->imagenfachada,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                $prestamo_laboralimagen = DB::table('s_prestamo_creditolaboralnegocioimagen')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->get();
                foreach($prestamo_laboralimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaboralnegocioimagen')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->delete(); 
                $prestamo_laboralimagen = DB::table('s_prestamo_creditolaboralnegocioimagen')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->get();
                foreach($prestamo_laboralimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaboralnegocioimagen')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->delete(); 
          
          
                $creditolaborallicenciafuncionamientoimagen = DB::table('s_prestamo_creditolaborallicenciafuncionamientoimagen')->where('idprestamo_credito', $id)->get();
                foreach($creditolaborallicenciafuncionamientoimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaborallicenciafuncionamientoimagen')->where('idprestamo_credito', $id)->delete();

                $creditolaboralcontratoalquilerimagen = DB::table('s_prestamo_creditolaboralcontratoalquilerimagen')->where('idprestamo_credito', $id)->get();
                foreach($creditolaboralcontratoalquilerimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaboralcontratoalquilerimagen')->where('idprestamo_credito', $id)->delete();

                $creditolaboralficharucimagen = DB::table('s_prestamo_creditolaboralficharucimagen')->where('idprestamo_credito', $id)->get();
                foreach($creditolaboralficharucimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaboralficharucimagen')->where('idprestamo_credito', $id)->delete();

                $creditolaboralreciboaguaimagen = DB::table('s_prestamo_creditolaboralreciboaguaimagen')->where('idprestamo_credito', $id)->get();
                foreach($creditolaboralreciboaguaimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaboralreciboaguaimagen')->where('idprestamo_credito', $id)->delete();

                $creditolaboralreciboluzimagen = DB::table('s_prestamo_creditolaboralreciboluzimagen')->where('idprestamo_credito', $id)->get();
                foreach($creditolaboralreciboluzimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaboralreciboluzimagen')->where('idprestamo_credito', $id)->delete();

                $creditolaboralboletacompraimagen = DB::table('s_prestamo_creditolaboralboletacompraimagen')->where('idprestamo_credito', $id)->get();
                foreach($creditolaboralboletacompraimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaboralboletacompraimagen')->where('idprestamo_credito', $id)->delete();

          
          
                // Garantias
                $creditobien = DB::table('s_prestamo_creditobien')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->first();
                if($creditobien!=''){
                    $prestamo_bienimagen = DB::table('s_prestamo_creditobienimagen')->where('idprestamo_creditobien', $creditobien->id)->get();
                    foreach($prestamo_bienimagen as $value){
                        uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditobien/');
                    }
                    DB::table('s_prestamo_creditobienimagen')->where('idprestamo_creditobien', $creditobien->id)->delete();
                } 
                DB::table('s_prestamo_creditobien')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->delete();

                // Sustento
                DB::table('s_prestamo_creditosustento')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->delete();
            }
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha PreAprobado correctamente.'
            ]);
        }

        elseif ($request->input('view') == 'imagenbien') {
            
            foreach($request->file('imagen-bien') as $value){
                $imagen = uploadfile('', '', $value, '/public/backoffice/tienda/'.$idtienda.'/creditobien/');
                $countprestamogaleria = DB::table('s_prestamo_creditobienimagen')->where('s_prestamo_creditobienimagen.idprestamo_creditobien', $request->idprestamo_creditobien)->count();
                $orden = $countprestamogaleria+1;
                DB::table('s_prestamo_creditobienimagen')->insert([
                  'fecharegistro'   => Carbon::now(),
                  'orden'           => $orden,
                  'imagen'          => $imagen,
                  'idtienda'        => $idtienda,
                  'idprestamo_creditobien' => $request->idprestamo_creditobien
                ]);
            }
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'editar-domicilio') {
            $rules = [
                'domicilio_editar_direccion' => 'required',
                //'domicilio_editar_referencia' => 'required',
                'domicilio_editar_idubigeo' => 'required',
                /*'domicilio_editar_reside_desdemes' => 'required',
                'domicilio_editar_reside_desdeanio' => 'required',
                'domicilio_editar_horaubicacion_de' => 'required',
                'domicilio_editar_horaubicacion_hasta' => 'required',
                'domicilio_editar_idtipopropiedad' => 'required',
                'domicilio_editar_iddeudapagoservicio' => 'required',
                'domicilio_editar_mapa_latitud' => 'required',
                'domicilio_editar_mapa_longitud' => 'required',*/
            ];
            $messages = [
                'domicilio_editar_direccion.required' => 'La "Dirección" es Obligatorio.',
                'domicilio_editar_referencia.required' => 'La "Referencia" es Obligatorio.',
                'domicilio_editar_idubigeo.required' => 'El "Ubigeo" es Obligatorio.',
                'domicilio_editar_reside_desdemes.required' => 'El "Mes de residencia" es Obligatorio.',
                'domicilio_editar_reside_desdeanio.required' => 'El "Año de residencia" es Obligatorio.',
                'domicilio_editar_horaubicacion_de.required' => 'La "Hora" es Obligatorio.',
                'domicilio_editar_horaubicacion_hasta.required' => 'La "Hora" es Obligatorio.',
                'domicilio_editar_idtipopropiedad.required' => 'El "Tipo de Propiedad" es Obligatorio.',
                'domicilio_editar_iddeudapagoservicio.required' => 'El "Pago de Servicios" es Obligatorio.',
                'domicilio_editar_mapa_latitud.required' => 'La "Ubicación" es Obligatorio.<br>(Mover el marcador del mapa para seleccionar una ubicación)',
                'domicilio_editar_mapa_longitud.required' => '',
            ];
            $this->validate($request, $rules, $messages);
          
            $referencias = explode('/&/', $request->referencias);
            for($i = 1; $i < count($referencias); $i++){
                $item = explode('/,/',$referencias[$i]);
                if($item[0]==''){
                    return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje' => 'La "Persona" es Obligatorio.'
                    ]);
                }elseif($item[1]==''){
                    return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje' => 'El "Tipo de Relacion" es Obligatorio.'
                    ]);
                }elseif($item[2]==''){
                    return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje' => 'El "Número de Teléfono" es Obligatorio.'
                    ]);
                }elseif($item[3]==''){
                    return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje' => 'El "Comentario" es Obligatorio.'
                    ]);
                }
            }
          
            $creditodomicilio = DB::table('s_prestamo_creditodomicilio')->where('idprestamo_credito',$id)->limit(1)->first();
            if($creditodomicilio!=''){
              
                $imagensuministro = uploadfile($creditodomicilio->imagensuministro, $request->domicilio_imagensuministro_anterior, $request->file('domicilio_imagensuministro'), '/public/backoffice/tienda/'.$idtienda.'/creditodomicilio/');
                $imagenfachada = uploadfile($creditodomicilio->imagenfachada, $request->domicilio_imagenfachada_anterior, $request->file('domicilio_imagenfachada'), '/public/backoffice/tienda/'.$idtienda.'/creditodomicilio/');
              
                DB::table('s_prestamo_creditodomicilio')->whereId($creditodomicilio->id)->update([
                    'fechamodificacion' => Carbon::now(),
                    'direccion' => $request->domicilio_editar_direccion,
                    'reside_desdemes' => $request->domicilio_editar_reside_desdemes!=''?$request->domicilio_editar_reside_desdemes:'',
                    'reside_desdeanio' => $request->domicilio_editar_reside_desdeanio!=''?$request->domicilio_editar_reside_desdeanio:'',
                    'horaubicacion_de' => $request->domicilio_editar_horaubicacion_de!=''?$request->domicilio_editar_horaubicacion_de:'',
                    'horaubicacion_hasta' => $request->domicilio_editar_horaubicacion_hasta!=''?$request->domicilio_editar_horaubicacion_hasta:'',
                    'mapa_latitud' => $request->domicilio_editar_mapa_latitud!=''?$request->domicilio_editar_mapa_latitud:'',
                    'mapa_longitud' => $request->domicilio_editar_mapa_longitud!=''?$request->domicilio_editar_mapa_longitud:'',
                    'referencia' => $request->domicilio_editar_referencia!=''?$request->domicilio_editar_referencia:'',
                    'imagensuministro' => $imagensuministro,
                    'imagenfachada' => $imagenfachada,
                    'idubigeo' => $request->domicilio_editar_idubigeo,
                    'idtipopropiedad' => $request->domicilio_editar_idtipopropiedad!=''?$request->domicilio_editar_idtipopropiedad:0,
                    'iddeudapagoservicio' => $request->domicilio_editar_iddeudapagoservicio!=''?$request->domicilio_editar_iddeudapagoservicio:0,
                ]);
            }else{
                $imagensuministro = uploadfile('', '', $request->file('imagensuministro'), '/public/backoffice/tienda/'.$idtienda.'/creditodomicilio/');
                $imagenfachada = uploadfile('', '', $request->file('imagenfachada'), '/public/backoffice/tienda/'.$idtienda.'/creditodomicilio/');
                DB::table('s_prestamo_creditodomicilio')->insert([
                    'fecharegistro' => Carbon::now(),
                    'direccion' => $request->domicilio_editar_direccion,
                    'reside_desdemes' => $request->domicilio_editar_reside_desdemes!=''?$request->domicilio_editar_reside_desdemes:'',
                    'reside_desdeanio' => $request->domicilio_editar_reside_desdeanio!=''?$request->domicilio_editar_reside_desdeanio:'',
                    'horaubicacion_de' => $request->domicilio_editar_horaubicacion_de!=''?$request->domicilio_editar_horaubicacion_de:'',
                    'horaubicacion_hasta' => $request->domicilio_editar_horaubicacion_hasta!=''?$request->domicilio_editar_horaubicacion_hasta:'',
                    'mapa_latitud' => $request->domicilio_editar_mapa_latitud!=''?$request->domicilio_editar_mapa_latitud:'',
                    'mapa_longitud' => $request->domicilio_editar_mapa_longitud!=''?$request->domicilio_editar_mapa_longitud:'',
                    'referencia' => $request->domicilio_editar_referencia!=''?$request->domicilio_editar_referencia:'',
                    'imagensuministro' => $imagensuministro,
                    'imagenfachada' => $imagenfachada,
                    'idubigeo' => $request->domicilio_editar_idubigeo,
                    'idtipopropiedad' => $request->domicilio_editar_idtipopropiedad!=''?$request->domicilio_editar_idtipopropiedad:0,
                    'iddeudapagoservicio' => $request->domicilio_editar_iddeudapagoservicio!=''?$request->domicilio_editar_iddeudapagoservicio:0,
                    'idprestamo_credito' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
            }
          
            // relaciones
            DB::table('s_prestamo_creditorelacion')->where('idprestamo_credito', $id)->delete();
            $referencias = explode('/&/', $request->referencias);
            for($i = 1; $i < count($referencias); $i++){
                $item = explode('/,/',$referencias[$i]);
                DB::table('s_prestamo_creditorelacion')->insert([
                    'numerotelefono' => $item[2],
                    'comentario' => $item[3],
                    'personanombre' => $item[0],
                    'idprestamo_tiporelacion' => $item[1],
                    'idprestamo_credito' => $id,
                    'idtienda' => $idtienda,
                ]);
            }
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'editar-domicilioimagen') {
          
            foreach($request->file('imagen-domicilio') as $value){
                $imagen = uploadfile('', '', $value, '/public/backoffice/tienda/'.$idtienda.'/creditodomicilio/');
                $countprestamogaleria = DB::table('s_prestamo_creditodomicilioimagen')->where('s_prestamo_creditodomicilioimagen.idprestamo_credito', $id)->count();
                $orden = $countprestamogaleria+1;
                DB::table('s_prestamo_creditodomicilioimagen')->insert([
                  'fecharegistro'     => Carbon::now(),
                  'orden'             => $orden,
                  'imagen'            => $imagen,
                  'idprestamo_credito'=> $id,
                  'idtienda'          => $idtienda,
                  'idestado'          => 1,
                ]);
            }
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }

        elseif ($request->input('view') == 'editar-laboral') {
            $rules = [
                'laboral_editar_idfuenteingreso' => 'required',
                'laboral_editar_idprestamo_giro' => 'required',
                'laboral_editar_idprestamo_actividad' => 'required',
                'laboral_editar_idprestamo_nombrenegocio' => 'required',
            ];
          
            $codigolicenciafuncionamiento = '';
            if($request->check_estadolicenciafuncionamiento=='on'){
                $rules = array_merge($rules,[
                    'laboral_editar_idprestamo_codigolicenciafuncionamiento' => 'required',
                ]);
                $codigolicenciafuncionamiento = $request->laboral_editar_idprestamo_codigolicenciafuncionamiento;
            }
          
            $duenocontratoalquiler = '';
            if($request->check_estadocontratoalquiler=='on'){
                $rules = array_merge($rules,[
                    'laboral_editar_idprestamo_duenocontratoalquiler' => 'required',
                ]);
                $duenocontratoalquiler = $request->laboral_editar_idprestamo_duenocontratoalquiler;
            }
          
            $laboral_idprestamo_rucficharuc = '';
            $laboral_idprestamo_emisioncomprobanteficharuc = 0;
            if($request->check_estadoficharuc=='on'){
                $rules = array_merge($rules,[
                    'laboral_editar_idprestamo_rucficharuc' => 'required',
                    'laboral_editar_idprestamo_emisioncomprobanteficharuc' => 'required',
                ]);
                $laboral_idprestamo_rucficharuc = $request->laboral_editar_idprestamo_rucficharuc;
                $laboral_idprestamo_emisioncomprobanteficharuc = $request->laboral_editar_idprestamo_emisioncomprobanteficharuc;
            }
          
            $codigoreciboagua = '';
            if($request->check_estadoreciboagua=='on'){
                $rules = array_merge($rules,[
                    'laboral_editar_idprestamo_codigoreciboagua' => 'required',
                ]);
                $codigoreciboagua = $request->laboral_editar_idprestamo_codigoreciboagua;
            }
          
            $codigoreciboluz = '';
            if($request->check_estadoreciboluz=='on'){
                $rules = array_merge($rules,[
                    'laboral_editar_idprestamo_codigoreciboluz' => 'required',
                ]);
                $codigoreciboluz = $request->laboral_editar_idprestamo_codigoreciboluz;
            }
            
            $rules =  array_merge($rules,[
                'laboral_editar_labora_desdemes' => 'required',
                'laboral_editar_labora_desdeanio' => 'required',
                'laboral_editar_direccion' => 'required',
                //'laboral_editar_referencia' => 'required',
                'laboral_editar_idubigeo' => 'required',
                /*'laboral_editar_mapa_latitud' => 'required',
                'laboral_editar_mapa_longitud' => 'required',*/
            ]);
          
            $messages = [
                'laboral_editar_idfuenteingreso.required' => 'La "Fuente de Ingreso" es Obligatorio',
                'laboral_editar_idprestamo_giro.required' => 'El "Giro" es Obligatorio',
                'laboral_editar_idprestamo_actividad.required' => 'La "Actividad" es Obligatorio',
                'laboral_editar_idprestamo_nombrenegocio.required' => 'El "Nombre de Negocio" es Obligatorio',
                'laboral_editar_idprestamo_codigolicenciafuncionamiento.required' => 'El "Código de Licencia de Funcionamiento" es Obligatorio',
                'laboral_editar_idprestamo_duenocontratoalquiler.required' => 'El "Dueño de Establecimiento" es Obligatorio',
                'laboral_editar_idprestamo_rucficharuc.required' => 'El "RUC de Negocio" es Obligatorio',
                'laboral_editar_idprestamo_emisioncomprobanteficharuc.required' => 'Los "Comprobantes que Emite" es Obligatorio',
                'laboral_editar_labora_desdemes.required' => 'La "Fecha de Labor" es Obligatorio',
                'laboral_editar_labora_desdeanio.required' => 'La "Fecha de Labor" es Obligatorio',
                'laboral_editar_direccion.required' => 'La "Dirección" es Obligatorio',
                'laboral_editar_referencia.required' => 'La "Referencia" es Obligatorio',
                'laboral_editar_idubigeo.required' => 'El "Ubigeo" es Obligatorio',
                'laboral_editar_mapa_latitud.required' => 'La "Ubicación" es Obligatorio.<br>(Mover el marcador del mapa para seleccionar una ubicación)',
                'laboral_editar_mapa_longitud.required' => '',
                'laboral_editar_idprestamo_codigoreciboagua.required' => 'El "Código de Suministro" es Obligatorio.',
                'laboral_editar_idprestamo_codigoreciboluz.required' => 'El "Código de Suministro" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);
          
            if(($request->laboral_editar_idfuenteingreso==1 && $request->laboral_editar_idprestamo_giro==1) || ($request->laboral_editar_idfuenteingreso==1 && $request->laboral_editar_idprestamo_giro==3) || ($request->laboral_editar_idfuenteingreso==1 && $request->laboral_editar_idprestamo_giro==2)){
                // Ingreso
                $producto_ingreso = explode('/&/', $request->ingresos);
                for($i = 1; $i < count($producto_ingreso); $i++){
                    $item = explode('/,/',$producto_ingreso[$i]);
                    if($item[0]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El Concepto de producto en Ingresos es obligatorio.'
                        ]);
                    }elseif($item[1]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El monto de producto en Ingresos es obligatorio.'
                        ]);
                    }elseif($item[1]<=0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El monto de producto en Ingresos debe ser mayor a "0".'
                        ]);
                    }
                } 
            }
            else if($request->laboral_editar_idfuenteingreso==2 && $request->laboral_editar_idprestamo_giro==1){
                $producto_venta = explode('/&/', $request->ventas);
                for($i = 1; $i < count($producto_venta); $i++){
                    $item = explode('/,/',$producto_venta[$i]);
                    if($item[0]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El producto en Ventas es obligatorio.'
                        ]);
                    }elseif($item[1]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'La cantidad de producto en Ventas es obligatorio.'
                        ]);
                    }elseif($item[1]<=0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'La cantidad de producto en Ventas debe ser mayor a "0".'
                        ]);
                    }
                }

                $producto_compra = explode('/&/', $request->compras);
                for($i = 1; $i < count($producto_compra); $i++){
                    $item = explode('/,/',$producto_compra[$i]);
                    if($item[0]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El producto en Costo de Ventas es obligatorio.'
                        ]);
                    }elseif($item[1]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'La cantidad de producto en Costo de Ventas es obligatorio.'
                        ]);
                    }
                } 
            }
            else if(($request->laboral_editar_idfuenteingreso==2 && $request->laboral_editar_idprestamo_giro==2) || ($request->laboral_editar_idfuenteingreso==2 && $request->laboral_editar_idprestamo_giro==3) ){
                // Servicios
                if($request->servicios!=''){
                    $item = explode('/,/',$request->servicios);
                    if($item[1]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El campo "Bueno" en Servicios es obligatorio.'
                        ]);
                    }elseif($item[1]<=0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El campo "Bueno" en Servicios debe ser mayor a "0".'
                        ]);
                    }elseif($item[2]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El campo "Regular" en Servicios es obligatorio.'
                        ]);
                    }elseif($item[2]<=0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El campo "Regular" en Servicios debe ser mayor a "0".'
                        ]);
                    }elseif($item[3]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El campo "Malo" en Servicios es obligatorio.'
                        ]);
                    }elseif($item[3]<=0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El campo "Malo" en Servicios debe ser mayor a "0".'
                        ]);
                    }
                }
            }      
            
            // Egreso gasto operativo
            $producto_egresogasto = explode('/&/', $request->egresogastos);
            for($i = 1; $i < count($producto_egresogasto); $i++){
                $item = explode('/,/',$producto_egresogasto[$i]);
                if($item[0]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Concepto en Gastos Operativos es obligatorio.'
                    ]);
                }elseif($item[1]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Monto de producto en Gastos Operativos es obligatorio.'
                    ]);
                }elseif($item[1]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Monto de producto en Gastos Operativos debe ser mayor a "0".'
                    ]);
                }
            }
            // Egreso gasto familiar
            $producto_egresogastofamiliares = explode('/&/', $request->egresogastosfamiliares);
            for($i = 1; $i < count($producto_egresogastofamiliares); $i++){
                $item = explode('/,/',$producto_egresogastofamiliares[$i]);
                if($item[0]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Concepto en Gastos Familiares es obligatorio.'
                    ]);
                }elseif($item[1]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Monto de producto en Gastos Familiares es obligatorio.'
                    ]);
                }elseif($item[1]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Monto en producto en Gastos Familiares debe ser mayor a "0".'
                    ]);
                }
            }
          
            // Egreso pago
            $producto_egresopago = explode('/&/', $request->egresopagos);
            for($i = 1; $i < count($producto_egresopago); $i++){
                $item = explode('/,/',$producto_egresopago[$i]);
                if($item[0]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Concepto de producto en Pagos es obligatorio.'
                    ]);
                }elseif($item[1]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Monto de producto en Pagos es obligatorio.'
                    ]);
                }elseif($item[1]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Monto en producto en Pagos debe ser mayor a "0".'
                    ]);
                }
            }
          
            // Otros Ingresos
            $producto_otroingreso = explode('/&/', $request->otroingresos);
            for($i = 1; $i < count($producto_otroingreso); $i++){
                $item = explode('/,/',$producto_otroingreso[$i]);
                if($item[0]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Concepto de producto en Otros Ingresos es obligatorio.'
                    ]);
                }elseif($item[1]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El monto de producto en Otros Ingresos es obligatorio.'
                    ]);
                }elseif($item[1]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El monto de producto en Otros Ingresos debe ser mayor a "0".'
                    ]);
                }
            }
            // Otros gastos
            $producto_otrogasto = explode('/&/', $request->otrogastos);
            for($i = 1; $i < count($producto_otrogasto); $i++){
                $item = explode('/,/',$producto_otrogasto[$i]);
                if($item[0]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Concepto de producto en Otros Gastos es obligatorio.'
                    ]);
                }elseif($item[1]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El monto de producto en Otros Gastos es obligatorio.'
                    ]);
                }elseif($item[1]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El monto de producto en Otros Gastos debe ser mayor a "0".'
                    ]);
                }
            }
          
            $labora_lunes = 'no';
            $labora_martes = 'no';
            $labora_miercoles = 'no';
            $labora_jueves = 'no';
            $labora_viernes = 'no';
            $labora_sabado = 'no';
            $labora_domingo = 'no';
          
            if($request->seleccionar_lunes!=''){
                $labora_lunes = 'si';
            }
            if($request->seleccionar_martes!=''){
                $labora_martes = 'si';
            }
            if($request->seleccionar_miercoles!=''){
                $labora_miercoles = 'si';
            }
            if($request->seleccionar_jueves!=''){
                $labora_jueves = 'si';
            }
            if($request->seleccionar_viernes!=''){
                $labora_viernes = 'si';
            }
            if($request->seleccionar_sabados!=''){
                $labora_sabado = 'si';
            }
            if($request->seleccionar_domingos!=''){
                $labora_domingo = 'si';
            }
          
            $creditolaboral = DB::table('s_prestamo_creditolaboral')->where('idprestamo_credito',$id)->limit(1)->first();
  
            $idprestamocreditolaboral = 0;
            if($creditolaboral!=''){  
              
                $imagensuministro = uploadfile($creditolaboral->imagensuministro, $request->imagensuministro_anterior, $request->file('imagensuministro'), '/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                $imagenfachada = uploadfile($creditolaboral->imagenfachada, $request->imagenfachada_anterior, $request->file('imagenfachada'), '/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
          
                DB::table('s_prestamo_creditolaboral')->whereId($creditolaboral->id)->update([
                    'fechamodificacion' => Carbon::now(),
                    'actividad' => $request->laboral_editar_idprestamo_actividad,
                    'direccion' => $request->laboral_editar_direccion,
                    'labora_desdemes' => $request->laboral_editar_labora_desdemes,
                    'labora_desdeanio' => $request->laboral_editar_labora_desdeanio,
                    'labora_lunes' => $labora_lunes,
                    'labora_martes' => $labora_martes,
                    'labora_miercoles' => $labora_miercoles,
                    'labora_jueves' => $labora_jueves,
                    'labora_viernes' => $labora_viernes,
                    'labora_sabados' => $labora_sabado,
                    'labora_domingos' => $labora_domingo,
                    'mapa_latitud' => $request->laboral_editar_mapa_latitud!=''?$request->laboral_editar_mapa_latitud:'',
                    'mapa_longitud' => $request->laboral_editar_mapa_longitud!=''?$request->laboral_editar_mapa_longitud:'',
                    'referencia' => $request->laboral_editar_referencia!=''?$request->laboral_editar_referencia:'',
                    'imagensuministro' => $imagensuministro,
                    'imagenfachada' => $imagenfachada,
                    'nombrenegocio' => $request->laboral_editar_idprestamo_nombrenegocio,
                    'estadoficharuc' => $request->check_estadoficharuc!=''?$request->check_estadoficharuc:'',
                    'rucficharuc' => $laboral_idprestamo_rucficharuc,
                    'emisioncomprobante' => $laboral_idprestamo_emisioncomprobanteficharuc,
                    'estadolicenciafuncionamiento' => $request->check_estadolicenciafuncionamiento!=''?$request->check_estadolicenciafuncionamiento:'',
                    'codigolicenciafuncionamiento' => $codigolicenciafuncionamiento,
                    'estadoreciboagua' => $request->check_estadoreciboagua!=''?$request->check_estadoreciboagua:'',
                    'codigoreciboagua' => $codigoreciboagua,
                    'estadoreciboluz' => $request->check_estadoreciboluz!=''?$request->check_estadoreciboluz:'',
                    'codigoreciboluz' => $codigoreciboluz,
                    'estadoboletacompra' => $request->check_estadoboletacompra!=''?$request->check_estadoboletacompra:'',
                    'estadocontratoalquiler' => $request->check_estadocontratoalquiler!=''?$request->check_estadocontratoalquiler:'',
                    'duenocontratoalquiler' => $duenocontratoalquiler,
                    'idubigeo' => $request->laboral_editar_idubigeo,
                    'idprestamo_giro' => $request->laboral_editar_idprestamo_giro,
                    'idfuenteingreso' => $request->laboral_editar_idfuenteingreso,
                ]);
                $idprestamocreditolaboral = $creditolaboral->id;
            }else{
              
                $imagensuministro = uploadfile('', '', $request->file('imagensuministro'), '/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                $imagenfachada = uploadfile('', '', $request->file('imagenfachada'), '/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
              
                $idprestamocreditolaboral = DB::table('s_prestamo_creditolaboral')->insertGetId([
                    'fecharegistro' => Carbon::now(),
                    'venta' => 0,
                    'ingreso' => 0,
                    'servicio' => 0,
                    'ingresototal' => 0,
                    'compra' => 0,
                    'utilidad_bruta' => 0,
                    'egresogasto' => 0,
                    'utilidad_operativa' => 0,
                    'egresopago' => 0,
                    'utilidad_neta' => 0,
                    'otroingreso' => 0,
                    'otrogasto' => 0,
                    'egresogastofamiliar' => 0,
                    'ingresomensual' => 0,
                    'actividad' => $request->laboral_editar_idprestamo_actividad,
                    'direccion' => $request->laboral_editar_direccion,
                    'labora_desdemes' => $request->laboral_editar_labora_desdemes,
                    'labora_desdeanio' => $request->laboral_editar_labora_desdeanio,
                    'labora_lunes' => $labora_lunes,
                    'labora_martes' => $labora_martes,
                    'labora_miercoles' => $labora_miercoles,
                    'labora_jueves' => $labora_jueves,
                    'labora_viernes' => $labora_viernes,
                    'labora_sabados' => $labora_sabado,
                    'labora_domingos' => $labora_domingo,
                    'mapa_latitud' => $request->laboral_editar_mapa_latitud!=''?$request->laboral_editar_mapa_latitud:'',
                    'mapa_longitud' => $request->laboral_editar_mapa_longitud!=''?$request->laboral_editar_mapa_longitud:'',
                    'referencia' => $request->laboral_editar_referencia!=''?$request->laboral_editar_referencia:'',
                    'imagensuministro' => $imagensuministro,
                    'imagenfachada' => $imagenfachada,
                    'nombrenegocio' => $request->laboral_editar_idprestamo_nombrenegocio,
                    'estadoficharuc' => $request->check_estadoficharuc!=''?$request->check_estadoficharuc:'',
                    'rucficharuc' => $laboral_idprestamo_rucficharuc,
                    'emisioncomprobante' => $laboral_idprestamo_emisioncomprobanteficharuc,
                    'estadolicenciafuncionamiento' => $request->check_estadolicenciafuncionamiento!=''?$request->check_estadolicenciafuncionamiento:'',
                    'codigolicenciafuncionamiento' => $codigolicenciafuncionamiento,
                    'estadoreciboagua' => $request->check_estadoreciboagua!=''?$request->check_estadoreciboagua:'',
                    'codigoreciboagua' => $codigoreciboagua,
                    'estadoreciboluz' => $request->check_estadoreciboluz!=''?$request->check_estadoreciboluz:'',
                    'codigoreciboluz' => $codigoreciboluz,
                    'estadoboletacompra' => $request->check_estadoboletacompra!=''?$request->check_estadoboletacompra:'',
                    'estadocontratoalquiler' => $request->check_estadocontratoalquiler!=''?$request->check_estadocontratoalquiler:'',
                    'duenocontratoalquiler' => $duenocontratoalquiler,
                    'idubigeo' => $request->laboral_editar_idubigeo,
                    'idprestamo_giro' => $request->laboral_editar_idprestamo_giro,
                    'idfuenteingreso' => $request->laboral_editar_idfuenteingreso,
                    'idprestamo_credito' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
            }
          
            //Eliminar imagenes si esta desactivadas
            if($request->check_estadolicenciafuncionamiento!='on'){
                $creditolaborallicenciafuncionamientoimagen = DB::table('s_prestamo_creditolaborallicenciafuncionamientoimagen')->where('idprestamo_credito', $id)->get();
                foreach($creditolaborallicenciafuncionamientoimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaborallicenciafuncionamientoimagen')->where('idprestamo_credito', $id)->delete();
            }
            if($request->check_estadocontratoalquiler!='on'){
                $creditolaboralcontratoalquilerimagen = DB::table('s_prestamo_creditolaboralcontratoalquilerimagen')->where('idprestamo_credito', $id)->get();
                foreach($creditolaboralcontratoalquilerimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaboralcontratoalquilerimagen')->where('idprestamo_credito', $id)->delete();
            }
            if($request->check_estadoficharuc!='on'){
                $creditolaboralficharucimagen = DB::table('s_prestamo_creditolaboralficharucimagen')->where('idprestamo_credito', $id)->get();
                foreach($creditolaboralficharucimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaboralficharucimagen')->where('idprestamo_credito', $id)->delete();
            }
            if($request->check_estadoreciboagua!='on'){
                $creditolaboralreciboaguaimagen = DB::table('s_prestamo_creditolaboralreciboaguaimagen')->where('idprestamo_credito', $id)->get();
                foreach($creditolaboralreciboaguaimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaboralreciboaguaimagen')->where('idprestamo_credito', $id)->delete();
            }
            if($request->check_estadoreciboluz!='on'){
                $creditolaboralreciboluzimagen = DB::table('s_prestamo_creditolaboralreciboluzimagen')->where('idprestamo_credito', $id)->get();
                foreach($creditolaboralreciboluzimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaboralreciboluzimagen')->where('idprestamo_credito', $id)->delete();
            }
            if($request->check_estadoboletacompra!='on'){
                $creditolaboralboletacompraimagen = DB::table('s_prestamo_creditolaboralboletacompraimagen')->where('idprestamo_credito', $id)->get();
                foreach($creditolaboralboletacompraimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaboralboletacompraimagen')->where('idprestamo_credito', $id)->delete();
            }
            //fin Eliminar imagenes si esta desactivadas
          
            if(($request->laboral_editar_idfuenteingreso==1 && $request->laboral_editar_idprestamo_giro==1) || ($request->laboral_editar_idfuenteingreso==1 && $request->laboral_editar_idprestamo_giro==3) || ($request->laboral_editar_idfuenteingreso==1 && $request->laboral_editar_idprestamo_giro==2)){
                    // Ingreso
                    DB::table('s_prestamo_creditolaboralingreso')->where('s_idprestamo_creditolaboral', $idprestamocreditolaboral)->delete();
                    DB::table('s_prestamo_creditolaboralingreso')->insert([
                      'fecharegistro' => Carbon::now(),
                      'monto' => $request->ingresos,
                      'conceptoingreso' => 'INGRESO MENSUAL',
                      's_idprestamo_creditolaboral' => $idprestamocreditolaboral,
                      'idtienda' => $idtienda,
                      'idestado' => 1
                    ]);
                    
            }
            else if($request->laboral_editar_idfuenteingreso==2 && $request->laboral_editar_idprestamo_giro==1){
                // Ventas
                DB::table('s_prestamo_creditolaboralventa')->where('s_idprestamo_creditolaboral', $idprestamocreditolaboral)->delete();
                $producto_venta = explode('/&/', $request->ventas);
                for($i = 1; $i < count($producto_venta); $i++){
                    $item = explode('/,/',$producto_venta[$i]);
                    DB::table('s_prestamo_creditolaboralventa')->insert([
                      'fecharegistro' => Carbon::now(),
                      'cantidad' => $item[1],
                      'preciounitario' => $item[2],
                      'preciototal' => $item[3],
                      'preciototal_semanal' => $item[4],
                      'preciototal_quincenal' => $item[5],
                      'preciototal_mensual' => $item[6],
                      'producto' => $item[0],
                      's_idprestamo_creditolaboral' => $idprestamocreditolaboral,
                      'idtienda' => $idtienda,
                      'idestado' => 1
                    ]);
                }
                // Compras
                DB::table('s_prestamo_creditolaboralcompra')->where('s_idprestamo_creditolaboral', $idprestamocreditolaboral)->delete();
                $producto_compra = explode('/&/', $request->compras);
                for($i = 1; $i < count($producto_compra); $i++){
                    $item = explode('/,/',$producto_compra[$i]);
                    DB::table('s_prestamo_creditolaboralcompra')->insert([
                      'fecharegistro' => Carbon::now(),
                      'cantidad' => $item[1],
                      'preciounitario' => $item[2],
                      'preciototal' => $item[3],
                      'preciototal_semanal' => $item[4],
                      'preciototal_quincenal' => $item[5],
                      'preciototal_mensual' => $item[6],
                      'producto' => $item[0],
                      's_idprestamo_creditolaboral' => $idprestamocreditolaboral,
                      'idtienda' => $idtienda,
                      'idestado' => 1
                    ]);
                } 
            }
            else if(($request->laboral_editar_idfuenteingreso==2 && $request->laboral_editar_idprestamo_giro==2) || ($request->laboral_editar_idfuenteingreso==2 && $request->laboral_editar_idprestamo_giro==3) ){
                // Servicios
                DB::table('s_prestamo_creditolaboralservicio')->where('s_idprestamo_creditolaboral', $idprestamocreditolaboral)->delete();
                if($request->servicios!=''){
                    $item = explode('/,/',$request->servicios);
                    DB::table('s_prestamo_creditolaboralservicio')->insert([
                      'fecharegistro' => Carbon::now(),
                      'bueno' => $item[1],
                      'regular' => $item[2],
                      'malo' => $item[3],
                      'promedio' => $item[4],
                      'semanal' => $item[5],
                      'quincenal' => $item[6],
                      'mensual' => $item[7],
                      's_idprestamo_creditolaboral' => $idprestamocreditolaboral,
                      'idtienda' => $idtienda,
                      'idestado' => 1
                    ]);
                }
            }
            
            // Egreso gasto operativo
            DB::table('s_prestamo_creditolaboralegresogasto')->where('s_idprestamo_creditolaboral', $idprestamocreditolaboral)->delete();
            $producto_egresogasto = explode('/&/', $request->egresogastos);
            for($i = 1; $i < count($producto_egresogasto); $i++){
                $item = explode('/,/',$producto_egresogasto[$i]);
                DB::table('s_prestamo_creditolaboralegresogasto')->insert([
                  'fecharegistro' => Carbon::now(),
                  'monto' => $item[1],
                  'concepto' => $item[0],
                  's_idprestamo_creditolaboral' => $idprestamocreditolaboral,
                  'idtienda' => $idtienda,
                  'idestado' => 1
                ]);
            }
            // Egreso gasto familiar
            DB::table('s_prestamo_creditolaboralegresogastofamiliar')->where('s_idprestamo_creditolaboral', $idprestamocreditolaboral)->delete();
            $producto_egresogastofamiliares = explode('/&/', $request->egresogastosfamiliares);
            for($i = 1; $i < count($producto_egresogastofamiliares); $i++){
                $item = explode('/,/',$producto_egresogastofamiliares[$i]);
                DB::table('s_prestamo_creditolaboralegresogastofamiliar')->insert([
                  'fecharegistro' => Carbon::now(),
                  'monto' => $item[1],
                  'concepto' => $item[0],
                  's_idprestamo_creditolaboral' => $idprestamocreditolaboral,
                  'idtienda' => $idtienda,
                  'idestado' => 1
                ]);
            }
          
            // Egreso pago
            DB::table('s_prestamo_creditolaboralegresopago')->where('s_idprestamo_creditolaboral', $idprestamocreditolaboral)->delete();
            $producto_egresopago = explode('/&/', $request->egresopagos);
            for($i = 1; $i < count($producto_egresopago); $i++){
                $item = explode('/,/',$producto_egresopago[$i]);
                DB::table('s_prestamo_creditolaboralegresopago')->insert([
                  'fecharegistro' => Carbon::now(),
                  'monto' => $item[1],
                  'conceptoegresopago' => $item[0],
                  's_idprestamo_creditolaboral' => $idprestamocreditolaboral,
                  'idtienda' => $idtienda,
                  'idestado' => 1
                ]);
            }
          
            // Otros Ingresos
            DB::table('s_prestamo_creditolaboralotroingreso')->where('s_idprestamo_creditolaboral', $idprestamocreditolaboral)->delete();
            $producto_otroingreso = explode('/&/', $request->otroingresos);
            for($i = 1; $i < count($producto_otroingreso); $i++){
                $item = explode('/,/',$producto_otroingreso[$i]);
                DB::table('s_prestamo_creditolaboralotroingreso')->insert([
                  'fecharegistro' => Carbon::now(),
                  'monto' => $item[1],
                  'conceptootroingreso' => $item[0],
                  's_idprestamo_creditolaboral' => $idprestamocreditolaboral,
                  'idtienda' => $idtienda,
                  'idestado' => 1
                ]);
            }
            // Otros gastos
            DB::table('s_prestamo_creditolaboralotrogasto')->where('s_idprestamo_creditolaboral', $idprestamocreditolaboral)->delete();
            $producto_otrogasto = explode('/&/', $request->otrogastos);
            for($i = 1; $i < count($producto_otrogasto); $i++){
                $item = explode('/,/',$producto_otrogasto[$i]);
                DB::table('s_prestamo_creditolaboralotrogasto')->insert([
                  'fecharegistro' => Carbon::now(),
                  'monto' => $item[1],
                  'conceptootrogasto' => $item[0],
                  's_idprestamo_creditolaboral' => $idprestamocreditolaboral,
                  'idtienda' => $idtienda,
                  'idestado' => 1
                ]);
            }
          
                
          
            // actualizar resultado

            $total_laboralventa = DB::table('s_prestamo_creditolaboralventa')
                ->where('s_prestamo_creditolaboralventa.s_idprestamo_creditolaboral', $idprestamocreditolaboral)
                ->sum('preciototal_mensual');
            $total_laboralcompra = DB::table('s_prestamo_creditolaboralcompra')
                ->where('s_prestamo_creditolaboralcompra.s_idprestamo_creditolaboral', $idprestamocreditolaboral)
                ->sum('preciototal_mensual');
            $total_laboralingreso = DB::table('s_prestamo_creditolaboralingreso')
                ->where('s_prestamo_creditolaboralingreso.s_idprestamo_creditolaboral', $idprestamocreditolaboral)
                ->sum('monto');
            $total_laboralegresogasto = DB::table('s_prestamo_creditolaboralegresogasto')
                ->where('s_prestamo_creditolaboralegresogasto.s_idprestamo_creditolaboral', $idprestamocreditolaboral)
                ->sum('monto');
            $total_laboralegresogastofamiliar = DB::table('s_prestamo_creditolaboralegresogastofamiliar')
                ->where('s_prestamo_creditolaboralegresogastofamiliar.s_idprestamo_creditolaboral', $idprestamocreditolaboral)
                ->sum('monto');
            $total_laboralegresopago = DB::table('s_prestamo_creditolaboralegresopago')
                ->where('s_prestamo_creditolaboralegresopago.s_idprestamo_creditolaboral', $idprestamocreditolaboral)
                ->sum('monto');
            $total_laboralotroingreso = DB::table('s_prestamo_creditolaboralotroingreso')
                ->where('s_prestamo_creditolaboralotroingreso.s_idprestamo_creditolaboral', $idprestamocreditolaboral)
                ->sum('monto');
            $total_laboralotrogasto = DB::table('s_prestamo_creditolaboralotrogasto')
                ->where('s_prestamo_creditolaboralotrogasto.s_idprestamo_creditolaboral', $idprestamocreditolaboral)
                ->sum('monto');
            $total_laboralservicio = DB::table('s_prestamo_creditolaboralservicio')
                ->where('s_prestamo_creditolaboralservicio.s_idprestamo_creditolaboral', $idprestamocreditolaboral)
                ->sum('mensual');
          
            $ingresototal = number_format($total_laboralventa+$total_laboralingreso+$total_laboralservicio, 2, '.', '');
            $utilidad_bruta = number_format($ingresototal-$total_laboralcompra, 2, '.', '');
            $utilidad_operativa = number_format($utilidad_bruta-$total_laboralegresogasto, 2, '.', '');
            $utilidad_neta = number_format($utilidad_operativa-$total_laboralegresopago, 2, '.', '');
            $excedente_neto_mensual = number_format($utilidad_neta+$total_laboralotroingreso-$total_laboralotrogasto-$total_laboralegresogastofamiliar, 2, '.', '');

            DB::table('s_prestamo_creditolaboral')->whereId($idprestamocreditolaboral)->update([
                'venta' => $total_laboralventa,
                'ingreso' => $total_laboralingreso,
                'servicio' => $total_laboralservicio,
                'ingresototal' => $ingresototal,
                'compra' => $total_laboralcompra,
                'utilidad_bruta' => $utilidad_bruta,
                'egresogasto' => $total_laboralegresogasto,
                'utilidad_operativa' => $utilidad_operativa,
                'egresopago' => $total_laboralegresopago,
                'utilidad_neta' => $utilidad_neta,
                'otroingreso' => $total_laboralotroingreso,
                'otrogasto' => $total_laboralotrogasto,
                'egresogastofamiliar' => $total_laboralegresogastofamiliar,
                'ingresomensual' => $excedente_neto_mensual,
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'editar-imagennegocio') {
          
            foreach($request->file('imagennegocio') as $value){
                $imagen = uploadfile('', '', $value, '/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                $countprestamogaleria = DB::table('s_prestamo_creditolaboralnegocioimagen')->where('s_prestamo_creditolaboralnegocioimagen.idprestamo_credito', $id)->count();
                $orden = $countprestamogaleria+1;
                DB::table('s_prestamo_creditolaboralnegocioimagen')->insert([
                  'fecharegistro'     => Carbon::now(),
                  'orden'             => $orden,
                  'imagen'            => $imagen,
                  'idprestamo_credito'=> $id,
                  'idtienda'          => $idtienda,
                  'idestado'          => 1,
                ]);
            }
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'editar-imagenlicenciafuncionamiento') {
          
            foreach($request->file('imagenlicenciafuncionamiento') as $value){
                $imagen = uploadfile('', '', $value, '/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                $countprestamogaleria = DB::table('s_prestamo_creditolaborallicenciafuncionamientoimagen')->where('s_prestamo_creditolaborallicenciafuncionamientoimagen.idprestamo_credito', $id)->count();
                $orden = $countprestamogaleria+1;
                DB::table('s_prestamo_creditolaborallicenciafuncionamientoimagen')->insert([
                  'fecharegistro'     => Carbon::now(),
                  'orden'             => $orden,
                  'imagen'            => $imagen,
                  'idprestamo_credito'=> $id,
                  'idtienda'          => $idtienda,
                  'idestado'          => 1,
                ]);
            }
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'editar-imagencontratoalquiler') {
          
            foreach($request->file('imagencontratoalquiler') as $value){
                $imagen = uploadfile('', '', $value, '/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                $countprestamogaleria = DB::table('s_prestamo_creditolaboralcontratoalquilerimagen')->where('s_prestamo_creditolaboralcontratoalquilerimagen.idprestamo_credito', $id)->count();
                $orden = $countprestamogaleria+1;
                DB::table('s_prestamo_creditolaboralcontratoalquilerimagen')->insert([
                  'fecharegistro'     => Carbon::now(),
                  'orden'             => $orden,
                  'imagen'            => $imagen,
                  'idprestamo_credito'=> $id,
                  'idtienda'          => $idtienda,
                  'idestado'          => 1,
                ]);
            }
          
                
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'editar-imagenficharuc') {
          
            foreach($request->file('imagenficharuc') as $value){
                $imagen = uploadfile('', '', $value, '/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                $countprestamogaleria = DB::table('s_prestamo_creditolaboralficharucimagen')->where('s_prestamo_creditolaboralficharucimagen.idprestamo_credito', $id)->count();
                $orden = $countprestamogaleria+1;
                DB::table('s_prestamo_creditolaboralficharucimagen')->insert([
                  'fecharegistro'     => Carbon::now(),
                  'orden'             => $orden,
                  'imagen'            => $imagen,
                  'idprestamo_credito'=> $id,
                  'idtienda'          => $idtienda,
                  'idestado'          => 1,
                ]);
            }
          
                
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'editar-imagenreciboagua') {
          
            foreach($request->file('imagenreciboagua') as $value){
                $imagen = uploadfile('', '', $value, '/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                $countprestamogaleria = DB::table('s_prestamo_creditolaboralreciboaguaimagen')->where('s_prestamo_creditolaboralreciboaguaimagen.idprestamo_credito', $id)->count();
                $orden = $countprestamogaleria+1;
                DB::table('s_prestamo_creditolaboralreciboaguaimagen')->insert([
                  'fecharegistro'     => Carbon::now(),
                  'orden'             => $orden,
                  'imagen'            => $imagen,
                  'idprestamo_credito'=> $id,
                  'idtienda'          => $idtienda,
                  'idestado'          => 1,
                ]);
            }
          
                
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'editar-imagenreciboluz') {
          
            foreach($request->file('imagenreciboluz') as $value){
                $imagen = uploadfile('', '', $value, '/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                $countprestamogaleria = DB::table('s_prestamo_creditolaboralreciboluzimagen')->where('s_prestamo_creditolaboralreciboluzimagen.idprestamo_credito', $id)->count();
                $orden = $countprestamogaleria+1;
                DB::table('s_prestamo_creditolaboralreciboluzimagen')->insert([
                  'fecharegistro'     => Carbon::now(),
                  'orden'             => $orden,
                  'imagen'            => $imagen,
                  'idprestamo_credito'=> $id,
                  'idtienda'          => $idtienda,
                  'idestado'          => 1,
                ]);
            }
                
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'editar-imagenboletacompra') {
          
            foreach($request->file('imagenboletacompra') as $value){
                $imagen = uploadfile('', '', $value, '/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                $countprestamogaleria = DB::table('s_prestamo_creditolaboralboletacompraimagen')->where('s_prestamo_creditolaboralboletacompraimagen.idprestamo_credito', $id)->count();
                $orden = $countprestamogaleria+1;
                DB::table('s_prestamo_creditolaboralboletacompraimagen')->insert([
                  'fecharegistro'     => Carbon::now(),
                  'orden'             => $orden,
                  'imagen'            => $imagen,
                  'idprestamo_credito'=> $id,
                  'idtienda'          => $idtienda,
                  'idestado'          => 1,
                ]);
            }
                
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
      
        elseif ($request->input('view') == 'importar-bien') {
                // GARANTIA
                $prestamobien = DB::table('s_prestamo_creditobien')
                    ->whereId($request->idprestamo_creditobien)
                    ->first();
    
                  $idcreditobien = DB::table('s_prestamo_creditobien')->insertGetId([
                    'fecharegistro' => Carbon::now(),
                    'producto' => $prestamobien->producto,
                    'descripcion' => $prestamobien->descripcion,
                    'valorestimado' => $prestamobien->valorestimado,
                    'idprestamo_documento' => $prestamobien->idprestamo_documento,
                    'idprestamo_credito' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                  ]);
                  $prestamolaboralimagen = DB::table('s_prestamo_creditobienimagen')
                    ->where('s_prestamo_creditobienimagen.idprestamo_creditobien', $prestamobien->id)
                    ->orderBy('s_prestamo_creditobienimagen.id','asc')
                    ->get();
                  foreach($prestamolaboralimagen as $valueimagen) {
                      $imagen = duplicar_fichero('/public/backoffice/tienda/'.$idtienda.'/creditobien/',$valueimagen->imagen);
                      DB::table('s_prestamo_creditobienimagen')->insert([
                        'fecharegistro'     => Carbon::now(),
                        'orden'             => $valueimagen->orden,
                        'imagen'            => $imagen,
                        'idprestamo_creditobien'=> $idcreditobien,
                        'idtienda'          => $idtienda,
                      ]);
                  }
               
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'editar-bien') {
            $rules = [
                'bien_editar_producto' => 'required',
                'bien_editar_valorestimado' => 'required',
                'bien_editar_descripcion' => 'required',
                'bien_editar_idprestamo_documento' => 'required',
            ];
            $messages = [
                'bien_editar_producto.required' => 'El "Producto" es Obligatorio.',
                'bien_editar_valorestimado.required' => 'El "Valor Estimado" es Obligatorio.',
                'bien_editar_descripcion.required' => 'La "Descripción" es Obligatorio.',
                'bien_editar_idprestamo_documento.required' => 'El "Documento" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);
          
            DB::table('s_prestamo_creditobien')->whereId($request->idprestamo_creditobien)->update([
                'fechamodificacion' => Carbon::now(),
                'producto' => $request->bien_editar_producto,
                'descripcion' => $request->bien_editar_descripcion,
                'valorestimado' => $request->bien_editar_valorestimado,
                'idprestamo_documento' => $request->bien_editar_idprestamo_documento,
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'eliminar-bien') {
            $prestamo_bienimagen = DB::table('s_prestamo_creditobienimagen')->where('id', $request->idprestamo_creditobienimagen)->first();
            if ($prestamo_bienimagen != '') {
              uploadfile_eliminar($prestamo_bienimagen->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditobien/');
            }
            DB::table('s_prestamo_creditobienimagen')->where('id', $request->idprestamo_creditobienimagen)->delete();
            DB::table('s_prestamo_creditobien')->whereId($request->idprestamo_creditobien)->delete();

          
        }
    }

    public function destroy(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            // Domicilio
            $prestamo_domicilioimagen = DB::table('s_prestamo_creditodomicilioimagen')->where('idprestamo_credito', $id)->get();
            foreach($prestamo_domicilioimagen as $value){
                uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditodomicilio/');
            }
            DB::table('s_prestamo_creditodomicilioimagen')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->delete(); 
            DB::table('s_prestamo_creditorelacion')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->delete();
            $prestamo_creditodomicilio = DB::table('s_prestamo_creditodomicilio')->where('idtienda',$idtienda)->where('idprestamo_credito',$id)->first();
            if($prestamo_creditodomicilio!=''){
                uploadfile_eliminar($prestamo_creditodomicilio->imagensuministro,'/public/backoffice/tienda/'.$idtienda.'/creditodomicilio/');
                uploadfile_eliminar($prestamo_creditodomicilio->imagenfachada,'/public/backoffice/tienda/'.$idtienda.'/creditodomicilio/');
            }
            DB::table('s_prestamo_creditodomicilio')->where('idtienda',$idtienda)->where('idprestamo_credito',$id)->delete();

            // Laboral
            $prestamo_creditolaboral = DB::table('s_prestamo_creditolaboral')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->first();
            if($prestamo_creditolaboral!=''){
                DB::table('s_prestamo_creditolaboralingreso')->where('s_idprestamo_creditolaboral', $prestamo_creditolaboral->id)->delete();
                DB::table('s_prestamo_creditolaboralventa')->where('s_idprestamo_creditolaboral', $prestamo_creditolaboral->id)->delete();
                DB::table('s_prestamo_creditolaboralcompra')->where('s_idprestamo_creditolaboral', $prestamo_creditolaboral->id)->delete();
                DB::table('s_prestamo_creditolaboralservicio')->where('s_idprestamo_creditolaboral', $prestamo_creditolaboral->id)->delete();
                DB::table('s_prestamo_creditolaboralegresogasto')->where('s_idprestamo_creditolaboral', $prestamo_creditolaboral->id)->delete();
                DB::table('s_prestamo_creditolaboralegresogastofamiliar')->where('s_idprestamo_creditolaboral', $prestamo_creditolaboral->id)->delete();
                DB::table('s_prestamo_creditolaboralegresopago')->where('s_idprestamo_creditolaboral', $prestamo_creditolaboral->id)->delete();
                DB::table('s_prestamo_creditolaboralotroingreso')->where('s_idprestamo_creditolaboral', $prestamo_creditolaboral->id)->delete();
                DB::table('s_prestamo_creditolaboralotrogasto')->where('s_idprestamo_creditolaboral', $prestamo_creditolaboral->id)->delete();
                DB::table('s_prestamo_creditolaboral')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->delete();
                uploadfile_eliminar($prestamo_creditolaboral->imagensuministro,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                uploadfile_eliminar($prestamo_creditolaboral->imagenfachada,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
            }
            $prestamo_laboralimagen = DB::table('s_prestamo_creditolaboralnegocioimagen')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->get();
            foreach($prestamo_laboralimagen as $value){
                uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
            }
            DB::table('s_prestamo_creditolaboralnegocioimagen')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->delete(); 
            $prestamo_laboralimagen = DB::table('s_prestamo_creditolaboralnegocioimagen')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->get();
            foreach($prestamo_laboralimagen as $value){
                uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
            }
            DB::table('s_prestamo_creditolaboralnegocioimagen')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->delete(); 
          
          
                $creditolaborallicenciafuncionamientoimagen = DB::table('s_prestamo_creditolaborallicenciafuncionamientoimagen')->where('idprestamo_credito', $id)->get();
                foreach($creditolaborallicenciafuncionamientoimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaborallicenciafuncionamientoimagen')->where('idprestamo_credito', $id)->delete();

                $creditolaboralcontratoalquilerimagen = DB::table('s_prestamo_creditolaboralcontratoalquilerimagen')->where('idprestamo_credito', $id)->get();
                foreach($creditolaboralcontratoalquilerimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaboralcontratoalquilerimagen')->where('idprestamo_credito', $id)->delete();

                $creditolaboralficharucimagen = DB::table('s_prestamo_creditolaboralficharucimagen')->where('idprestamo_credito', $id)->get();
                foreach($creditolaboralficharucimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaboralficharucimagen')->where('idprestamo_credito', $id)->delete();

                $creditolaboralreciboaguaimagen = DB::table('s_prestamo_creditolaboralreciboaguaimagen')->where('idprestamo_credito', $id)->get();
                foreach($creditolaboralreciboaguaimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaboralreciboaguaimagen')->where('idprestamo_credito', $id)->delete();

                $creditolaboralreciboluzimagen = DB::table('s_prestamo_creditolaboralreciboluzimagen')->where('idprestamo_credito', $id)->get();
                foreach($creditolaboralreciboluzimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaboralreciboluzimagen')->where('idprestamo_credito', $id)->delete();

                $creditolaboralboletacompraimagen = DB::table('s_prestamo_creditolaboralboletacompraimagen')->where('idprestamo_credito', $id)->get();
                foreach($creditolaboralboletacompraimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
                }
                DB::table('s_prestamo_creditolaboralboletacompraimagen')->where('idprestamo_credito', $id)->delete();

          
          
            // Garantias
            $creditobien = DB::table('s_prestamo_creditobien')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->first();
            if($creditobien!=''){
                $prestamo_bienimagen = DB::table('s_prestamo_creditobienimagen')->where('idprestamo_creditobien', $creditobien->id)->get();
                foreach($prestamo_bienimagen as $value){
                    uploadfile_eliminar($value->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditobien/');
                }
                DB::table('s_prestamo_creditobienimagen')->where('idprestamo_creditobien', $creditobien->id)->delete();
            } 
            DB::table('s_prestamo_creditobien')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->delete();
          
            // Sustento
            DB::table('s_prestamo_creditosustento')->where('idtienda',$idtienda)->where('idprestamo_credito', $id)->delete();
          
            DB::table('s_prestamo_creditodetalle')->where('idtienda',$idtienda)->where('idprestamo_credito',$id)->delete();
            DB::table('s_prestamo_credito')->where('idtienda',$idtienda)->where('id',$id)->delete();

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        } 
        elseif ($request->input('view') == 'eliminarimagendomicilio') {
          $prestamo_domicilioimagen = DB::table('s_prestamo_creditodomicilioimagen')->where('id', $request->idprestamo_creditodomicilioimagen)->first();
          if ($prestamo_domicilioimagen != '') {
            uploadfile_eliminar($prestamo_domicilioimagen->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditodomicilio/');
          }
          DB::table('s_prestamo_creditodomicilioimagen')->where('id', $request->idprestamo_creditodomicilioimagen)->delete();
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha eliminado correctamente.'
          ]);
        }
        elseif ($request->input('view') == 'eliminarimagennegocio') {
          $laboralnegocioimagen = DB::table('s_prestamo_creditolaboralnegocioimagen')->whereId($id)->first();
          if ($laboralnegocioimagen != '') {
              uploadfile_eliminar($laboralnegocioimagen->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
          }
          DB::table('s_prestamo_creditolaboralnegocioimagen')->whereId($id)->delete();
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha eliminado correctamente.'
          ]);
        }
        elseif ($request->input('view') == 'eliminarimagenlicenciafuncionamiento') {
          $laborallicenciafuncionamientoimagen = DB::table('s_prestamo_creditolaborallicenciafuncionamientoimagen')->whereId($id)->first();
          if ($laborallicenciafuncionamientoimagen != '') {
              uploadfile_eliminar($laborallicenciafuncionamientoimagen->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
          }
          DB::table('s_prestamo_creditolaborallicenciafuncionamientoimagen')->whereId($id)->delete();
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha eliminado correctamente.'
          ]);
        }
        elseif ($request->input('view') == 'eliminarimagencontratoalquiler') {
          $laboralcontratoalquilerimagen = DB::table('s_prestamo_creditolaboralcontratoalquilerimagen')->whereId($id)->first();
          if ($laboralcontratoalquilerimagen != '') {
              uploadfile_eliminar($laboralcontratoalquilerimagen->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
          }
          DB::table('s_prestamo_creditolaboralcontratoalquilerimagen')->whereId($id)->delete();
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha eliminado correctamente.'
          ]);
        }
        elseif ($request->input('view') == 'eliminarimagenficharuc') {
          $laboralficharucimagen = DB::table('s_prestamo_creditolaboralficharucimagen')->whereId($id)->first();
          if ($laboralficharucimagen != '') {
              uploadfile_eliminar($laboralficharucimagen->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
          }
          DB::table('s_prestamo_creditolaboralficharucimagen')->whereId($id)->delete();
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha eliminado correctamente.'
          ]);
        }
        elseif ($request->input('view') == 'eliminarimagenreciboagua') {
          $laboralreciboaguaimagen = DB::table('s_prestamo_creditolaboralreciboaguaimagen')->whereId($id)->first();
          if ($laboralreciboaguaimagen != '') {
              uploadfile_eliminar($laboralreciboaguaimagen->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
          }
          DB::table('s_prestamo_creditolaboralreciboaguaimagen')->whereId($id)->delete();
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha eliminado correctamente.'
          ]);
        }
        elseif ($request->input('view') == 'eliminarimagenreciboluz') {
          $laboralreciboluzimagen = DB::table('s_prestamo_creditolaboralreciboluzimagen')->whereId($id)->first();
          if ($laboralreciboluzimagen != '') {
              uploadfile_eliminar($laboralreciboluzimagen->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
          }
          DB::table('s_prestamo_creditolaboralreciboluzimagen')->whereId($id)->delete();
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha eliminado correctamente.'
          ]);
        }
        elseif ($request->input('view') == 'eliminarimagenboletacompra') {
          $laboralboletacompraimagen = DB::table('s_prestamo_creditolaboralboletacompraimagen')->whereId($id)->first();
          if ($laboralboletacompraimagen != '') {
              uploadfile_eliminar($laboralboletacompraimagen->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditolaboral/');
          }
          DB::table('s_prestamo_creditolaboralboletacompraimagen')->whereId($id)->delete();
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha eliminado correctamente.'
          ]);
        }
        elseif ($request->input('view') == 'eliminarimagenbien') {
          $prestamo_bienimagen = DB::table('s_prestamo_creditobienimagen')->where('id', $request->idprestamo_creditobienimagen)->first();
          if ($prestamo_bienimagen != '') {
            uploadfile_eliminar($prestamo_bienimagen->imagen,'/public/backoffice/tienda/'.$idtienda.'/creditobien/');
          }
          DB::table('s_prestamo_creditobienimagen')->where('id', $request->idprestamo_creditobienimagen)->delete();
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha eliminado correctamente.'
          ]);
        }
    }
}
