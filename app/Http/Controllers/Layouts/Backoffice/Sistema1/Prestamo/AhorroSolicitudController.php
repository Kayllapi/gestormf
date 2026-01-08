<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Prestamo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class AhorroSolicitudController extends Controller
{
    public function index(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        $where = [];
        if($request->tipoahorro!=''){ $where[] = ['s_prestamo_tipoahorro.id',$request->tipoahorro]; }
        $where[] = ['s_prestamo_ahorro.codigo','LIKE','%'.$request->codigocredito.'%'];
        $where[] = ['cliente.identificacion','LIKE','%'.$request->identificacion.'%'];
        $where[] = ['cliente.nombre','LIKE','%'.$request->cliente.'%'];
      
        $where1 = [];
        if($request->tipoahorro!=''){ $where1[] = ['s_prestamo_tipoahorro.id',$request->tipoahorro]; }
        $where1[] = ['s_prestamo_ahorro.codigo','LIKE','%'.$request->codigocredito.'%'];
        $where1[] = ['cliente.identificacion','LIKE','%'.$request->identificacion.'%'];
        $where1[] = ['cliente.nombre','LIKE','%'.$request->cliente.'%'];
      
        $prestamoahorros = DB::table('s_prestamo_ahorro')
              ->join('users as asesor', 'asesor.id', 's_prestamo_ahorro.idasesor')
              ->join('users as cliente', 'cliente.id', 's_prestamo_ahorro.idcliente')
              ->join('s_prestamo_tipoahorro', 's_prestamo_tipoahorro.id', 's_prestamo_ahorro.idprestamo_tipoahorro')
              ->join('s_moneda', 's_moneda.id', 's_prestamo_ahorro.idmoneda')
              ->where($where)
              ->where('s_prestamo_ahorro.idtienda', $idtienda)
              ->whereIn('s_prestamo_ahorro.idestado', [1,3])
              ->where('s_prestamo_ahorro.idasesor', Auth::user()->id)
              ->orWhere($where1)
              ->where('s_prestamo_ahorro.idtienda', $idtienda)
              ->whereIn('s_prestamo_ahorro.idestado', [1,3])
              ->where('s_prestamo_ahorro.idasesor', Auth::user()->id)
              ->select(
                  's_prestamo_ahorro.*',
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  's_prestamo_tipoahorro.nombre as tipocreditonombre',
                  'cliente.identificacion as clienteidentificacion',
                  's_moneda.simbolo as monedasimbolo',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
              )
              ->orderBy('s_prestamo_ahorro.id','desc')
              ->paginate(10);
        return view('layouts/backoffice/tienda/sistema/prestamo/ahorrosolicitud/index', [
            'tienda' => $tienda,
            'prestamoahorros' => $prestamoahorros,
        ]);
    }
  
    public function create(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda       = DB::table('tienda')->whereId($idtienda)->first();
      $frecuencias  = DB::table('s_prestamo_frecuencia')->get();
      $tipoahorros  = DB::table('s_prestamo_tipoahorro')->get();
      return view('layouts/backoffice/tienda/sistema/prestamo/ahorrosolicitud/create', [
          'tienda' => $tienda,
          'frecuencias' => $frecuencias,
          'tipoahorros' => $tipoahorros,
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
            if($request->input('check_idbeneficiario')=='on'){
              $rules = array_merge($rules,[
                'idbeneficiario' => 'required'
              ]);
            }

            $rules = array_merge($rules,[
              'tipocreditonombre' => 'required',
            ]);
            
            $ahorrolibre_tiponombre = '';
            $ahorrolibre_monto = '0.00';
            $ahorrolibre_producto = '';
            if($request->input('tipocreditonombre')==1){
              $rules = array_merge($rules,[
                'ahorrofijo_monto' => 'required',
                'ahorrofijo_tiempo' => 'required',
                'ahorrofijo_fechainicio' => 'required',
                'ahorrofijo_tasa' => 'required',
              ]);
            }
            elseif($request->input('tipocreditonombre')==2){
              $rules = array_merge($rules,[
                'ahorroprogramado_monto' => 'required',
                'ahorroprogramado_idfrecuencia' => 'required',
                'ahorroprogramado_numerocuota' => 'required',
                'ahorroprogramado_fechainicio' => 'required',
                'ahorroprogramado_tasa' => 'required',
              ]);
            }
            elseif($request->input('tipocreditonombre')==3){
              $rules = array_merge($rules,[
                'ahorrolibre_fechainicio' => 'required'
              ]);
                if(configuracion($idtienda,'prestamo_ahorro_tipoahorrolibre')['resultado']=='CORRECTO'){
                    $rules = array_merge($rules,[
                        'ahorrolibre_tiponombre' => 'required',
                    ]);
                  
                    if($request->input('ahorrolibre_tiponombre')=='NINGUNO'){
                    }else{
                        $rules = array_merge($rules,[
                            'ahorrolibre_monto' => 'required',
                            'ahorrolibre_producto' => 'required',
                        ]);
                        $ahorrolibre_monto = $request->ahorrolibre_monto;
                        $ahorrolibre_producto = $request->ahorrolibre_producto;
                    }
                    $ahorrolibre_tiponombre = $request->ahorrolibre_tiponombre;
                }
            }
            $messages = [
              'idcliente.required' => 'El "Cliente" es Obligatorio.',
              'idconyuge.required' => 'El "Cónyuge" es Obligatorio.',
              'idbeneficiario.required' => 'El "Beneficiario" es Obligatorio.',
              'tipocreditonombre.required' => 'El "Tipo de Ahorro" es Obligatorio.',
              'ahorrofijo_monto.required' => 'El "Monto" es Obligatorio.',
              'ahorrofijo_tiempo.required' => 'El "Tiempo" es Obligatorio.',
              'ahorrofijo_fechainicio.required' => 'El "Fecha de Inicio" es Obligatorio.',
              'ahorrofijo_tasa.required' => 'El "Interes" es Obligatorio.',
              'ahorroprogramado_monto.required' => 'El "Monto" es Obligatorio.',
              'ahorroprogramado_numerocuota.required' => 'El "Nro. de Cuota" es Obligatorio.',
              'ahorroprogramado_fechainicio.required' => 'La "Fecha de Inicio" es Obligatorio.',
              'ahorroprogramado_idfrecuencia.required' => 'La "Frecuencia" es Obligatorio.',
              'ahorroprogramado_tasa.required' => 'El "Interes" es Obligatorio.',
              'ahorrolibre_fechainicio.required' => 'El "Fecha de Inicio" es Obligatorio.',
              'ahorrolibre_tiponombre.required' => 'El "Tipo de Ahorro Libre" es Obligatorio.',
              'ahorrolibre_monto.required' => 'El "Monto a Ahorrar" es Obligatorio.',
              'ahorrolibre_producto.required' => 'El "Producto a Ahorrar" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);
      
            $monto = 0;
            $numerocuota = 0;
            $fechainicio = 0;
            $fecharetiro = '';
            $ultimafecha = '';
            $numerodias = 0;
            $tasa = 0;
            $excluirsabado = '';
            $excluirdomingo = '';
            $excluirferiado = '';
            $idfrecuencia = 0;
            $tipotasa = 0;
            $total_cuota = 0;
            $total_interesganado = 0;
            $total_total = 0;
            if($request->input('tipocreditonombre')==1){
                $cronograma = ahorro_cronograma(
                    $idtienda,
                    $request->tipocreditonombre,
                    $request->ahorrofijo_monto,
                    $request->ahorrofijo_tiempo,
                    $request->ahorrofijo_fechainicio,
                    4,
                    0,
                    $request->ahorrofijo_tasa,
                    '',
                    '',
                    ''
                );
              
                $monto = $request->ahorrofijo_monto;
                $numerocuota = 1;
                $fechainicio = $request->ahorrofijo_fechainicio;
                $fecharetiro = $cronograma['fecharetiro'];
                $ultimafecha = $cronograma['ultimafecha'];
                $tasa = $request->ahorrofijo_tasa;
                $idfrecuencia = 4;
                $tipotasa = $cronograma['tipotasa'];
                $total_cuota = $cronograma['total_cuota'];
                $total_interesganado = $cronograma['total_interesganado'];
                $total_total = $cronograma['total_total'];
            }
            elseif($request->input('tipocreditonombre')==2){
                $cronograma = ahorro_cronograma(
                    $idtienda,
                    $request->tipocreditonombre,
                    $request->ahorroprogramado_monto,
                    $request->ahorroprogramado_numerocuota,
                    $request->ahorroprogramado_fechainicio,
                    $request->ahorroprogramado_idfrecuencia,
                    $request->ahorroprogramado_numerodias,
                    $request->ahorroprogramado_tasa,
                    $request->ahorroprogramado_excluirferiado,
                    $request->ahorroprogramado_excluirsabado,
                    $request->ahorroprogramado_excluirdomingo
                );
              
                $monto = $request->ahorroprogramado_monto;
                $numerocuota = $request->ahorroprogramado_numerocuota;
                $fechainicio = $request->ahorroprogramado_fechainicio;
                $fecharetiro = $cronograma['fecharetiro'];
                $ultimafecha = $cronograma['ultimafecha'];
                $numerodias = $request->ahorroprogramado_numerodias;
                $tasa = $request->ahorroprogramado_tasa;
                $excluirferiado = $request->ahorroprogramado_excluirferiado ?? '';
                $excluirsabado = $request->ahorroprogramado_excluirsabado ?? '';
                $excluirdomingo = $request->ahorroprogramado_excluirdomingo ?? '';
                $idfrecuencia = $request->ahorroprogramado_idfrecuencia;
                $tipotasa = $cronograma['tipotasa'];
                $total_cuota = $cronograma['total_cuota'];
                $total_interesganado = $cronograma['total_interesganado'];
                $total_total = $cronograma['total_total'];
            }
            elseif($request->input('tipocreditonombre')==3){
                $fechainicio = $request->ahorrolibre_fechainicio;
            }
          
            // obtener ultimo código
            $prestamoahorro = DB::table('s_prestamo_ahorro')
                ->where('s_prestamo_ahorro.idtienda',$idtienda)
                ->orderBy('s_prestamo_ahorro.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($prestamoahorro!=''){
                $codigo = $prestamoahorro->codigo+1;
            }
            // fin obtener ultimo código

            $idprestamo_ahorro = DB::table('s_prestamo_ahorro')->insertGetId([
              'fecharegistro' => Carbon::now(),
              'codigo' => $codigo,
              'monto' => $monto,
              'numerocuota' => $numerocuota,
              'fechainicio' => $fechainicio,
              'ultimafecha' => $ultimafecha,
              'fecharetiro' => $fecharetiro,
              'numerodias' => $numerodias,
              'tasa' => $tasa,
              'tiempo' => 0,
              'ahorrolibre_tiponombre' => $ahorrolibre_tiponombre,
              'ahorrolibre_monto' => $ahorrolibre_monto,
              'ahorrolibre_producto' => $ahorrolibre_producto,
              'comentariosupervisor' => '',
              'excluirsabado' => $excluirsabado,
              'excluirdomingo' => $excluirdomingo,
              'excluirferiado' => $excluirferiado,
              'total_cuota' => $total_cuota,
              'total_interesganado' => $total_interesganado,
              'total_total' => $total_total,
              'facturacion_cliente_identificacion' => '',
              'facturacion_cliente_nombre' => '',
              'facturacion_cliente_apellidos' => '',
              'facturacion_cliente_direccion' => '',
              'facturacion_idagencia' => 0,
              'facturacion_idtipocomprobante' => 0,
              'facturacion_idubigeo' => 0,
              'facturacion_idaperturacierre' => 0,
              'idmoneda' => 1,
              'idasesor' => Auth::user()->id,
              'idcajero' => 0,
              'idsupervisor' => 0,
              'idcliente' => $request->input('idcliente'),
              'idconyuge' => $request->input('idconyuge')!='' ? $request->input('idconyuge') : 0,
              'idbeneficiario' => $request->input('idbeneficiario')!='' ? $request->input('idbeneficiario') : 0,
              'idprestamo_frecuencia' => $idfrecuencia,
              'idprestamo_tipotasa' => $tipotasa,
              'idprestamo_tipoahorro' => $request->input('tipocreditonombre'), // 1=FIJO, 2=PROGRAMADO,3=LIBRE
              'idestadocobrarganancia' => 0,
              'idestadoahorro' => 1, // pendiente
              'idestadoaprobacion' => 0,
              'idestadoconfirmacion' => 0,
              'idestadorecaudacion' => 1, // 1 = PENDIENTE, 2 = CANCELADO
              'idtienda' => $idtienda,
              'idestado' => 1,
            ]);

            if($request->input('tipocreditonombre')==1 or $request->input('tipocreditonombre')==2){
                foreach($cronograma['cronograma'] as $value) {
                      DB::table('s_prestamo_ahorrodetalle')->insert([
                        'numero' => $value['numero'],
                        'fechaahorro' => $value['fechanormal'],
                        'saldocapital' => $value['saldocapital'],
                        'cuota' => $value['cuota'],
                        'interesganado' => $value['interesganado'],
                        'total' => $value['total'],
                        'atraso' => 0,
                        'mora' => 0,
                        'moradescuento' => 0,
                        'moraapagar' => 0,
                        'cuotapago' => 0,
                        'acuenta' => 0,
                        'cuotaapagar' => 0,
                        'interesdescontado' => 0,
                        'idprestamo_ahorro' => $idprestamo_ahorro,
                        'idestadorecaudacion' => 1,
                        'idtienda' => $idtienda,
                        'idestado' => 1
                      ]);
                }
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

            $cronograma = ahorro_cronograma(
                $idtienda,
                $request->tipoahorro,
                $request->monto,
                $request->numerocuota,
                $request->fechainicio,
                $request->frecuencia,
                $request->numerodias,
                $request->tasa,
                $request->excluirferiado,
                $request->excluirsabado,
                $request->excluirdomingo
            );
            $html = '';
            if($cronograma['resultado']=='CORRECTO'){
                if($request->tipoahorro==1){
                    $fechanombre = 'Fecha de Ganancia';
                }elseif($request->tipoahorro==2){
                    $fechanombre = 'Fecha de Recaudación';
                }
                    $html = '<table class="table" id="table-creditocalendario">
                            <thead style="background: #31353d; color: #fff;">
                                <tr>
                                    <td style="padding: 8px;text-align: right;">Nº</td>
                                    <td style="padding: 8px;text-align: right;">'.$fechanombre.'</td>
                                    <td style="padding: 8px;text-align: right;">Ganancia Acumulada</td>
                                    <td style="padding: 8px;text-align: right;">Cuota</td>
                                    <td style="padding: 8px;text-align: right;">Interes Ganado</td>
                                    <td style="padding: 8px;text-align: right;">Total</td>
                                </tr>
                            </thead>
                            <tbody>';
                    foreach ($cronograma['cronograma'] as $value) {
                        $html .= '<tr>
                                  <td style="padding: 8px;text-align: right;width: 50px;">'.$value['numero'].'</td>
                                  <td style="padding: 8px;text-align: right;">'.$value['fecha'].'</td>
                                  <td style="padding: 8px;text-align: right;">'.$value['saldocapital'].'</td>
                                  <td style="padding: 8px;text-align: right;">'.$value['cuota'].'</td>
                                  <td style="padding: 8px;text-align: right;">'.$value['interesganado'].'</td>
                                  <td style="padding: 8px;text-align: right;">'.$value['total'].'</td>
                              </tr>';
                    }
                    $html .= '<tr style="background-color: #31353c;color: white;">
                                  <td style="padding: 8px;text-align: right;width: 50px;" colspan="3">TOTAL</td>
                                  <td style="padding: 8px;text-align: right;">'.$cronograma['total_cuota'].'</td>
                                  <td style="padding: 8px;text-align: right;">'.$cronograma['total_interesganado'].'</td>
                                  <td style="padding: 8px;text-align: right;">'.$cronograma['total_total'].'</td>
                              </tr></tbody>
                        </table>';
                    
            }else{
                $html = '<div class="mensaje-danger">'.$cronograma['mensaje'].'</b></div>';      
            }

            return ([
                'resultado' => $cronograma['resultado'],
                'mensaje' => $cronograma['mensaje'],
                'html' => $html,
                'fecharetiro' => $cronograma['fecharetiro'],
                'total_cuota' => $cronograma['total_cuota'],
                'total_interesganado' => $cronograma['total_interesganado'],
                'total_total' => $cronograma['total_total'],
            ]);
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

        $prestamoahorro = DB::table('s_prestamo_ahorro')
            ->leftjoin('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_ahorro.idprestamo_frecuencia')
            ->join('s_moneda', 's_moneda.id', 's_prestamo_ahorro.idmoneda')
            ->join('users as asesor', 'asesor.id', 's_prestamo_ahorro.idasesor')
            ->join('users as cliente', 'cliente.id', 's_prestamo_ahorro.idcliente')
            ->leftjoin('users as beneficiario', 'beneficiario.id', 's_prestamo_ahorro.idbeneficiario')
            ->leftjoin('ubigeo as clienteubigeo', 'clienteubigeo.id', 'cliente.idubigeo')
            ->leftjoin('ubigeo as beneficiarioubigeo', 'beneficiarioubigeo.id', 'beneficiario.idubigeo')
            ->join('tienda', 'tienda.id', 's_prestamo_ahorro.idtienda')
            ->leftjoin('users as conyuge', 'conyuge.id', 's_prestamo_ahorro.idconyuge')
            ->leftJoin('s_prestamo_tipoahorro', 's_prestamo_tipoahorro.id', 's_prestamo_ahorro.idprestamo_tipoahorro')
            ->where([
              ['s_prestamo_ahorro.id', $id],
              ['s_prestamo_ahorro.idtienda', $idtienda]
            ])
            ->select(
              's_prestamo_ahorro.*',
              's_prestamo_frecuencia.nombre as frecuencia_nombre',
              's_prestamo_frecuencia.id as idprestamo_frecuencia',
              'tienda.nombre as tiendanombre',
              'cliente.identificacion as clienteidentificacion',
              'cliente.nombre as clientenombre',
              'cliente.apellidos as clienteapellidos',
              'cliente.direccion as clientedireccion',
              'cliente.referencia as clientereferencia',
              'clienteubigeo.id as clienteidubigeo',
              'clienteubigeo.nombre as clienteubigeonombre',
              DB::raw('CONCAT(clienteubigeo.distrito, ", ", clienteubigeo.provincia, ", ", clienteubigeo.departamento) as clienteubigeoubicacion'),
              'conyuge.identificacion as conyugeidentificacion',
              'conyuge.nombre as conyugenombre',
              'conyuge.apellidos as conyugeapellidos',
              'beneficiario.identificacion as beneficiarioidentificacion',
              'beneficiario.nombre as beneficiarionombre',
              'beneficiario.apellidos as beneficiarioapellidos',
              'beneficiario.direccion as beneficiariodireccion',
              'beneficiario.referencia as beneficiarioreferencia',
              'beneficiarioubigeo.nombre as beneficiarioubigeonombre',
              'asesor.identificacion as asesoridentificacion',
              'asesor.nombre as asesornombre',
              'asesor.apellidos as asesorapellidos',
              's_moneda.simbolo as monedasimbolo',
              's_prestamo_tipoahorro.nombre as tipoahorronombre',
              DB::raw('IF(asesor.idtipopersona = 1 || asesor.idtipopersona = 3,
                  CONCAT(asesor.identificacion, " - ", asesor.apellidos, ", ", asesor.nombre),
                  CONCAT(asesor.identificacion, " - ", asesor.apellidos)) as asesor_nombre'),
              DB::raw('IF(cliente.idtipopersona = 1 || cliente.idtipopersona = 3,
                  CONCAT(cliente.identificacion, " - ", cliente.apellidos, ", ", cliente.nombre),
                  CONCAT(cliente.identificacion, " - ", cliente.apellidos)) as cliente_nombre'),
              DB::raw('IF(conyuge.idtipopersona = 1 || conyuge.idtipopersona = 3,
                  CONCAT(conyuge.identificacion, " - ", conyuge.apellidos, ", ", conyuge.nombre),
                  CONCAT(conyuge.identificacion, " - ", conyuge.apellidos)) as conyuge_nombre'),
              DB::raw('IF(beneficiario.idtipopersona = 1 || beneficiario.idtipopersona = 3,
                  CONCAT(beneficiario.identificacion, " - ", beneficiario.apellidos, ", ", beneficiario.nombre),
                  CONCAT(beneficiario.identificacion, " - ", beneficiario.apellidos)) as beneficiario_nombre'),
            )
            ->first();

        if($request->view == 'editar') {
            $frecuencias  = DB::table('s_prestamo_frecuencia')->get();
            $tipoahorros  = DB::table('s_prestamo_tipoahorro')->get();
            return view('layouts/backoffice/tienda/sistema/prestamo/ahorrosolicitud/edit', [
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
                'frecuencias' => $frecuencias,
                'tipoahorros' => $tipoahorros,
            ]);
        }
        elseif ($request->view == 'preaprobar') {
            return view('layouts/backoffice/tienda/sistema/prestamo/ahorrosolicitud/preaprobar', [
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
            ]);
        }
        elseif ($request->view == 'detalle') {
            return view('layouts/backoffice/tienda/sistema/prestamo/ahorrosolicitud/detalle', [
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
            ]);
        }
        elseif ($request->view == 'eliminar') {
            return view('layouts/backoffice/tienda/sistema/prestamo/ahorrosolicitud/eliminar', [
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
            ]);
        }
        elseif ($request->view == 'domicilioedit') {
            $prestamodomicilio = DB::table('s_prestamo_ahorrodomicilio')
                ->join('ubigeo', 'ubigeo.id', 's_prestamo_ahorrodomicilio.idubigeo')
                ->where('s_prestamo_ahorrodomicilio.idprestamo_ahorro',  $prestamoahorro->id)
                ->select(
                    's_prestamo_ahorrodomicilio.*',
                    'ubigeo.nombre as nombre_ubigeo',
                    DB::raw('CONCAT(ubigeo.distrito, ", ", ubigeo.provincia, ", ", ubigeo.departamento) as ubigeoubicacion'),
                )
                ->first();
          
            $relaciones = DB::table('s_prestamo_ahorrosocio')
                ->join('s_prestamo_tiporelacion', 's_prestamo_tiporelacion.id', 's_prestamo_ahorrosocio.idprestamo_tiporelacion')
                ->where([
                    ['s_prestamo_ahorrosocio.idprestamo_ahorro', $prestamoahorro->id],
                    ['s_prestamo_ahorrosocio.idtienda', $tienda->id],
                ])
                ->select(
                    's_prestamo_ahorrosocio.*',
                    's_prestamo_tiporelacion.nombre as nombre_tiporelacion'
                )
                ->orderBy('s_prestamo_ahorrosocio.id','asc')
                ->get();
          
            $tiporelaciones = DB::table('s_prestamo_tiporelacion')->get();
          
            return view('layouts/backoffice/tienda/sistema/prestamo/ahorrosolicitud/domicilioedit',[
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
                'prestamodomicilio' => $prestamodomicilio,
                'relaciones' => $relaciones,
                'tiporelaciones' => $tiporelaciones,
            ]);  
        }
      
        elseif ($request->view == 'laboraledit') {
            $prestamolaboral = DB::table('s_prestamo_ahorrolaboral')
                ->leftJoin('s_prestamo_giro', 's_prestamo_giro.id', 's_prestamo_ahorrolaboral.idprestamo_giro')
                ->leftJoin('s_prestamo_fuenteingreso', 's_prestamo_fuenteingreso.id', 's_prestamo_ahorrolaboral.idfuenteingreso')
                ->leftJoin('ubigeo', 'ubigeo.id', 's_prestamo_ahorrolaboral.idubigeo')
                ->where('s_prestamo_ahorrolaboral.idprestamo_ahorro', $prestamoahorro->id)
                ->select(
                    's_prestamo_ahorrolaboral.*',
                    's_prestamo_giro.nombre as nombre_giro',
                    'ubigeo.nombre as nombre_ubigeo',
                    's_prestamo_fuenteingreso.nombre as fuenteingreso'
                )
                ->first();
          
            $idprestamolavoral = 0;
            if($prestamolaboral!=''){
                $idprestamolavoral = $prestamolaboral->id;
            }
          
            $fuenteingreso = DB::table('s_prestamo_fuenteingreso')->get();
            $giro = DB::table('s_prestamo_giro')->get();
            $tipogastos = DB::table('s_prestamo_tipogasto')->get();
            $tipogastofamiliares = DB::table('s_prestamo_tipogastofamiliar')->get();

            return view('layouts/backoffice/tienda/sistema/prestamo/ahorrosolicitud/laboraledit',[
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
                'prestamolaboral' => $prestamolaboral,
                'fuenteingreso' => $fuenteingreso,
                'giro' => $giro,
                'tipogastos' => $tipogastos,
                'tipogastofamiliares' => $tipogastofamiliares,
            ]);  
        }
        
        elseif ($request->view == 'resultado') {
            return view('layouts/backoffice/tienda/sistema/prestamo/ahorrosolicitud/resultado',[
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
            ]);  
        } 
        elseif ($request->view == 'expediente') {

                $prestamosahorro = DB::table('s_prestamo_ahorro')
                    ->where('s_prestamo_ahorro.idestado', 1)
                    ->where('s_prestamo_ahorro.idtienda', $idtienda)
                    ->where('s_prestamo_ahorro.idestadoahorro', 4)
                    ->where('s_prestamo_ahorro.idcliente',$prestamoahorro->idcliente)
                    ->select(
                          's_prestamo_ahorro.*',
                    )
                    ->orderBy('s_prestamo_ahorro.fechaconfirmado','desc')
                    ->get();

                $ahorro_tabla = [];
                foreach($prestamosahorro as $valuedetalle){
                    $ahorro_tabla[] = [
                        'idahorro' => $valuedetalle->id,
                        'ahorrofechadesembolso' => date_format(date_create($valuedetalle->fechaconfirmado), "d/m/Y h:i A"),
                        'ahorrocodigo' => str_pad($valuedetalle->codigo, 8, "0", STR_PAD_LEFT),
                        'ahorrodesembolso' => $valuedetalle->monto,
                    ];
                }
           
            return view('layouts/backoffice/tienda/sistema/prestamo/ahorrosolicitud/expediente',[
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
                'prestamoahorros' => $ahorro_tabla,
            ]);  
        } 
        elseif ($request->view == 'expedientedetalleeditar') {

            return view('layouts/backoffice/tienda/sistema/prestamo/ahorrosolicitud/expedientedetalleeditar',[
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
            ]);  
        } 
        elseif ($request->view == 'expedientedetalle') {
            return view('layouts/backoffice/tienda/sistema/prestamo/ahorrosolicitud/expedientedetalle',[
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
            ]);  
        } 
      
        elseif ($request->view == 'ahorropdf-pdf') {
          
          $ahorro_laboral = DB::table('s_prestamo_ahorrolaboral')
                ->leftJoin('s_prestamo_giro', 's_prestamo_giro.id', 's_prestamo_ahorrolaboral.idprestamo_giro')
                ->leftJoin('ubigeo', 'ubigeo.id', 's_prestamo_ahorrolaboral.idubigeo')
                ->leftJoin('s_prestamo_fuenteingreso', 's_prestamo_fuenteingreso.id', 's_prestamo_ahorrolaboral.idfuenteingreso')
                ->where([
                    ['s_prestamo_ahorrolaboral.idprestamo_ahorro', $prestamoahorro->id],
                    ['s_prestamo_ahorrolaboral.idtienda', $idtienda],
                    ['s_prestamo_ahorrolaboral.idestado', 1]
                ])
                ->select(
                    's_prestamo_ahorrolaboral.*',
                    's_prestamo_fuenteingreso.nombre as nombre_fuenteingreso',
                    's_prestamo_giro.nombre as nombre_giro',
                    'ubigeo.nombre as ubigeonombre',
                )
                ->limit(1)
                ->first();
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamo/ahorrosolicitud/ahorropdf-pdf',[
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
                'ahorro_laboral' => $ahorro_laboral,
            ]);  
            return $pdf->stream('SOLICITUD_DE_AHORRO.pdf');
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
            if($request->input('check_idbeneficiario')=='on'){
              $rules = array_merge($rules,[
                'idbeneficiario' => 'required'
              ]);
            }

            $rules = array_merge($rules,[
              'tipocreditonombre' => 'required',
            ]);
          
            $ahorrolibre_tiponombre = '';
            $ahorrolibre_monto = '0.00';
            $ahorrolibre_producto = '';
          
            if($request->input('tipocreditonombre')==1){
              $rules = array_merge($rules,[
                'ahorrofijo_monto' => 'required',
                'ahorrofijo_tiempo' => 'required',
                'ahorrofijo_fechainicio' => 'required',
                'ahorrofijo_tasa' => 'required',
              ]);
            }
            elseif($request->input('tipocreditonombre')==2){
              $rules = array_merge($rules,[
                'ahorroprogramado_monto' => 'required',
                'ahorroprogramado_idfrecuencia' => 'required',
                'ahorroprogramado_numerocuota' => 'required',
                'ahorroprogramado_fechainicio' => 'required',
                'ahorroprogramado_tasa' => 'required',
              ]);
            }
            elseif($request->input('tipocreditonombre')==3){
              $rules = array_merge($rules,[
                'ahorrolibre_fechainicio' => 'required'
              ]);
                if(configuracion($idtienda,'prestamo_ahorro_tipoahorrolibre')['resultado']=='CORRECTO'){
                    $rules = array_merge($rules,[
                        'ahorrolibre_tiponombre' => 'required',
                    ]);
                  
                    if($request->input('ahorrolibre_tiponombre')=='NINGUNO'){
                    }else{
                        $rules = array_merge($rules,[
                            'ahorrolibre_monto' => 'required',
                            'ahorrolibre_producto' => 'required',
                        ]);
                        $ahorrolibre_monto = $request->ahorrolibre_monto;
                        $ahorrolibre_producto = $request->ahorrolibre_producto;
                    }
                    $ahorrolibre_tiponombre = $request->ahorrolibre_tiponombre;
                }
            }
            $messages = [
              'idcliente.required' => 'El "Cliente" es Obligatorio.',
              'idconyuge.required' => 'El "Cónyuge" es Obligatorio.',
              'idbeneficiario.required' => 'El "Beneficiario" es Obligatorio.',
              'tipocreditonombre.required' => 'El "Tipo de Ahorro" es Obligatorio.',
              'ahorrofijo_monto.required' => 'El "Monto" es Obligatorio.',
              'ahorrofijo_tiempo.required' => 'El "Tiempo" es Obligatorio.',
              'ahorrofijo_fechainicio.required' => 'El "Fecha de Inicio" es Obligatorio.',
              'ahorrofijo_tasa.required' => 'El "Interes" es Obligatorio.',
              'ahorroprogramado_monto.required' => 'El "Monto" es Obligatorio.',
              'ahorroprogramado_numerocuota.required' => 'El "Nro. de Cuota" es Obligatorio.',
              'ahorroprogramado_fechainicio.required' => 'La "Fecha de Inicio" es Obligatorio.',
              'ahorroprogramado_idfrecuencia.required' => 'La "Frecuencia" es Obligatorio.',
              'ahorroprogramado_tasa.required' => 'El "Interes" es Obligatorio.',
              'ahorrolibre_fechainicio.required' => 'El "Fecha de Inicio" es Obligatorio.',
              'ahorrolibre_tiponombre.required' => 'El "Tipo de Ahorro Libre" es Obligatorio.',
              'ahorrolibre_monto.required' => 'El "Monto a Ahorrar" es Obligatorio.',
              'ahorrolibre_producto.required' => 'El "Producto a Ahorrar" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);
      
            $monto = 0;
            $numerocuota = 0;
            $fechainicio = 0;
            $fecharetiro = '';
            $ultimafecha = '';
            $numerodias = 0;
            $tasa = 0;
            $excluirsabado = '';
            $excluirdomingo = '';
            $excluirferiado = '';
            $idfrecuencia = 0;
            $tipotasa = 0;
            $total_cuota = 0;
            $total_interesganado = 0;
            $total_total = 0;
            if($request->input('tipocreditonombre')==1){
                $cronograma = ahorro_cronograma(
                    $idtienda,
                    $request->tipocreditonombre,
                    $request->ahorrofijo_monto,
                    $request->ahorrofijo_tiempo,
                    $request->ahorrofijo_fechainicio,
                    4,
                    0,
                    $request->ahorrofijo_tasa,
                    '',
                    '',
                    ''
                );
              
                $monto = $request->ahorrofijo_monto;
                $numerocuota = 1;
                $fechainicio = $request->ahorrofijo_fechainicio;
                $fecharetiro = $cronograma['fecharetiro'];
                $ultimafecha = $cronograma['ultimafecha'];
                $tasa = $request->ahorrofijo_tasa;
                $idfrecuencia = 4;
                $tipotasa = $cronograma['tipotasa'];
                $total_cuota = $cronograma['total_cuota'];
                $total_interesganado = $cronograma['total_interesganado'];
                $total_total = $cronograma['total_total'];
            }
            elseif($request->input('tipocreditonombre')==2){
                $cronograma = ahorro_cronograma(
                    $idtienda,
                    $request->tipocreditonombre,
                    $request->ahorroprogramado_monto,
                    $request->ahorroprogramado_numerocuota,
                    $request->ahorroprogramado_fechainicio,
                    $request->ahorroprogramado_idfrecuencia,
                    $request->ahorroprogramado_numerodias,
                    $request->ahorroprogramado_tasa,
                    $request->ahorroprogramado_excluirferiado,
                    $request->ahorroprogramado_excluirsabado,
                    $request->ahorroprogramado_excluirdomingo
                );
              
                $monto = $request->ahorroprogramado_monto;
                $numerocuota = $request->ahorroprogramado_numerocuota;
                $fechainicio = $request->ahorroprogramado_fechainicio;
                $fecharetiro = $cronograma['fecharetiro'];
                $ultimafecha = $cronograma['ultimafecha'];
                $numerodias = $request->ahorroprogramado_numerodias;
                $tasa = $request->ahorroprogramado_tasa;
                $excluirferiado = $request->ahorroprogramado_excluirferiado ?? '';
                $excluirsabado = $request->ahorroprogramado_excluirsabado ?? '';
                $excluirdomingo = $request->ahorroprogramado_excluirdomingo ?? '';
                $idfrecuencia = $request->ahorroprogramado_idfrecuencia;
                $tipotasa = $cronograma['tipotasa'];
                $total_cuota = $cronograma['total_cuota'];
                $total_interesganado = $cronograma['total_interesganado'];
                $total_total = $cronograma['total_total'];
            }
            elseif($request->input('tipocreditonombre')==3){
                $fechainicio = $request->ahorrolibre_fechainicio;
            }
          
            $idestadoexpediente = $request->idestadoexpediente!='undefined'?'si':'no';
 
            DB::table('s_prestamo_ahorro')->whereId($id)->update([  
              'monto' => $monto,
              'numerocuota' => $numerocuota,
              'fechainicio' => $fechainicio,
              'ultimafecha' => $ultimafecha,
              'fecharetiro' => $fecharetiro,
              'numerodias' => $numerodias,
              'tasa' => $tasa,
              'ahorrolibre_tiponombre' => $ahorrolibre_tiponombre,
              'ahorrolibre_monto' => $ahorrolibre_monto,
              'ahorrolibre_producto' => $ahorrolibre_producto,
              'comentariosupervisor' => '',
              'excluirsabado' => $excluirsabado,
              'excluirdomingo' => $excluirdomingo,
              'excluirferiado' => $excluirferiado,
              'total_cuota' => $total_cuota,
              'total_interesganado' => $total_interesganado,
              'total_total' => $total_total,
              'idasesor' => Auth::user()->id,
              'idcliente' => $request->input('idcliente'),
              'idconyuge' => $request->input('idconyuge')!='' ? $request->input('idconyuge') : 0,
              'idbeneficiario' => $request->input('idbeneficiario')!='' ? $request->input('idbeneficiario') : 0,
              'idprestamo_frecuencia' => $idfrecuencia,
              'idprestamo_tipotasa' => $tipotasa,
              'idprestamo_tipoahorro' => $request->input('tipocreditonombre'), // 1=FIJO, 2=PROGRAMADO,3=LIBRE
            ]);

            DB::table('s_prestamo_ahorrodetalle')->where('idprestamo_ahorro',$id)->delete();
            if($request->input('tipocreditonombre')==1 or $request->input('tipocreditonombre')==2){
                foreach($cronograma['cronograma'] as $value) {
                      DB::table('s_prestamo_ahorrodetalle')->insert([
                        'numero' => $value['numero'],
                        'fechaahorro' => $value['fechanormal'],
                        'saldocapital' => $value['saldocapital'],
                        'cuota' => $value['cuota'],
                        'interesganado' => $value['interesganado'],
                        'total' => $value['total'],
                        'atraso' => 0,
                        'mora' => 0,
                        'moradescuento' => 0,
                        'moraapagar' => 0,
                        'cuotapago' => 0,
                        'acuenta' => 0,
                        'cuotaapagar' => 0,
                        'interesdescontado' => 0,
                        'idprestamo_ahorro' => $id,
                        'idestadorecaudacion' => 1,
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
        elseif ($request->input('view') == 'editar-expediente') {

            DB::table('s_prestamo_ahorro')->whereId($id)->update([
                'estadoexpediente' => $request->idestadoexpediente!='undefined'?'si':'no',
            ]);
         
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'preaprobar') {

            DB::table('s_prestamo_ahorro')->whereId($id)->update([
              'fechapreaprobado' => Carbon::now(),
              'idestadoahorro' => 2
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha PreAprobado correctamente.'
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
         
          
            $creditodomicilio = DB::table('s_prestamo_ahorrodomicilio')->where('idprestamo_ahorro',$id)->limit(1)->first();
            if($creditodomicilio!=''){
                DB::table('s_prestamo_ahorrodomicilio')->whereId($creditodomicilio->id)->update([
                    'fechamodificacion' => Carbon::now(),
                    'direccion' => $request->domicilio_editar_direccion,
                    'reside_desdemes' => $request->domicilio_editar_reside_desdemes!=''?$request->domicilio_editar_reside_desdemes:'',
                    'reside_desdeanio' => $request->domicilio_editar_reside_desdeanio!=''?$request->domicilio_editar_reside_desdeanio:'',
                    'horaubicacion_de' => $request->domicilio_editar_horaubicacion_de!=''?$request->domicilio_editar_horaubicacion_de:'',
                    'horaubicacion_hasta' => $request->domicilio_editar_horaubicacion_hasta!=''?$request->domicilio_editar_horaubicacion_hasta:'',
                    'mapa_latitud' => $request->domicilio_editar_mapa_latitud!=''?$request->domicilio_editar_mapa_latitud:'',
                    'mapa_longitud' => $request->domicilio_editar_mapa_longitud!=''?$request->domicilio_editar_mapa_longitud:'',
                    'referencia' => $request->domicilio_editar_referencia!=''?$request->domicilio_editar_referencia:'',
                    'idubigeo' => $request->domicilio_editar_idubigeo,
                    'idtipopropiedad' => $request->domicilio_editar_idtipopropiedad!=''?$request->domicilio_editar_idtipopropiedad:0,
                    'iddeudapagoservicio' => $request->domicilio_editar_iddeudapagoservicio!=''?$request->domicilio_editar_iddeudapagoservicio:0,
                ]);
            }else{
                DB::table('s_prestamo_ahorrodomicilio')->insert([
                    'fecharegistro' => Carbon::now(),
                    'direccion' => $request->domicilio_editar_direccion,
                    'reside_desdemes' => $request->domicilio_editar_reside_desdemes!=''?$request->domicilio_editar_reside_desdemes:'',
                    'reside_desdeanio' => $request->domicilio_editar_reside_desdeanio!=''?$request->domicilio_editar_reside_desdeanio:'',
                    'horaubicacion_de' => $request->domicilio_editar_horaubicacion_de!=''?$request->domicilio_editar_horaubicacion_de:'',
                    'horaubicacion_hasta' => $request->domicilio_editar_horaubicacion_hasta!=''?$request->domicilio_editar_horaubicacion_hasta:'',
                    'mapa_latitud' => $request->domicilio_editar_mapa_latitud!=''?$request->domicilio_editar_mapa_latitud:'',
                    'mapa_longitud' => $request->domicilio_editar_mapa_longitud!=''?$request->domicilio_editar_mapa_longitud:'',
                    'referencia' => $request->domicilio_editar_referencia!=''?$request->domicilio_editar_referencia:'',
                    'idubigeo' => $request->domicilio_editar_idubigeo,
                    'idtipopropiedad' => $request->domicilio_editar_idtipopropiedad!=''?$request->domicilio_editar_idtipopropiedad:0,
                    'iddeudapagoservicio' => $request->domicilio_editar_iddeudapagoservicio!=''?$request->domicilio_editar_iddeudapagoservicio:0,
                    'idprestamo_ahorro' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
            }
          
            // relaciones
            DB::table('s_prestamo_ahorrosocio')->where('idprestamo_ahorro', $id)->delete();
            $referencias = explode('/&/', $request->referencias);
            for($i = 1; $i < count($referencias); $i++){
                $item = explode('/,/',$referencias[$i]);
                DB::table('s_prestamo_ahorrosocio')->insert([
                    'numerotelefono' => $item[2],
                    'comentario' => $item[3],
                    'personanombre' => $item[0],
                    'idprestamo_tiporelacion' => $item[1],
                    'idprestamo_ahorro' => $id,
                    'idtienda' => $idtienda,
                ]);
            }
                
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'editar-laboral') {
            $rules = [
                'laboral_editar_idfuenteingreso' => 'required',
                'laboral_editar_idprestamo_giro' => 'required',
                'laboral_editar_idprestamo_actividad' => 'required',
                'laboral_editar_idprestamo_nombrenegocio' => 'required',
                'laboral_editar_labora_desdemes' => 'required',
                'laboral_editar_labora_desdeanio' => 'required',
                'laboral_editar_direccion' => 'required',
                //'laboral_editar_referencia' => 'required',
                'laboral_editar_idubigeo' => 'required',
                /*'laboral_editar_mapa_latitud' => 'required',
                'laboral_editar_mapa_longitud' => 'required',*/
            ];
          
            $messages = [
                'laboral_editar_idfuenteingreso.required' => 'La "Fuente de Ingreso" es Obligatorio',
                'laboral_editar_idprestamo_giro.required' => 'El "Giro" es Obligatorio',
                'laboral_editar_idprestamo_actividad.required' => 'La "Actividad" es Obligatorio',
                'laboral_editar_idprestamo_nombrenegocio.required' => 'El "Nombre de Negocio" es Obligatorio',
                'laboral_editar_labora_desdemes.required' => 'La "Fecha de Labor" es Obligatorio',
                'laboral_editar_labora_desdeanio.required' => 'La "Fecha de Labor" es Obligatorio',
                'laboral_editar_direccion.required' => 'La "Dirección" es Obligatorio',
                'laboral_editar_referencia.required' => 'La "Referencia" es Obligatorio',
                'laboral_editar_idubigeo.required' => 'El "Ubigeo" es Obligatorio',
                'laboral_editar_mapa_latitud.required' => 'La "Ubicación" es Obligatorio.<br>(Mover el marcador del mapa para seleccionar una ubicación)',
                'laboral_editar_mapa_longitud.required' => '',
            ];
            $this->validate($request, $rules, $messages);
  
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
          
            $creditolaboral = DB::table('s_prestamo_ahorrolaboral')->where('idprestamo_ahorro',$id)->limit(1)->first();
  
            if($creditolaboral!=''){  
          
                DB::table('s_prestamo_ahorrolaboral')->whereId($creditolaboral->id)->update([
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
                    'idubigeo' => $request->laboral_editar_idubigeo,
                    'idprestamo_giro' => $request->laboral_editar_idprestamo_giro,
                    'idfuenteingreso' => $request->laboral_editar_idfuenteingreso,
                ]);
            }else{
              
                DB::table('s_prestamo_ahorrolaboral')->insert([
                    'fecharegistro' => Carbon::now(),
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
                    'nombrenegocio' => $request->laboral_editar_idprestamo_nombrenegocio,
                    'idubigeo' => $request->laboral_editar_idubigeo,
                    'idprestamo_giro' => $request->laboral_editar_idprestamo_giro,
                    'idfuenteingreso' => $request->laboral_editar_idfuenteingreso,
                    'idprestamo_ahorro' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
            }   
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
    }

    public function destroy(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
          
            DB::table('s_prestamo_ahorrosocio')->where('idtienda',$idtienda)->where('idprestamo_ahorro', $id)->delete();
            DB::table('s_prestamo_ahorrodomicilio')->where('idtienda',$idtienda)->where('idprestamo_ahorro',$id)->delete();
            DB::table('s_prestamo_ahorrolaboral')->where('idtienda',$idtienda)->where('idprestamo_ahorro', $id)->delete();
            DB::table('s_prestamo_ahorrodetalle')->where('idtienda',$idtienda)->where('idprestamo_ahorro',$id)->delete();
            DB::table('s_prestamo_ahorro')->where('idtienda',$idtienda)->whereId($id)->delete();

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        } 
    }
}
