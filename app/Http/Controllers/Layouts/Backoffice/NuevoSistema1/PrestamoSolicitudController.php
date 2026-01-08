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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda = DB::table('tienda')->whereId($idtienda)->first();

      return view('layouts/backoffice/tienda/sistema/prestamosolicitud/index', compact(
        'tienda'
      ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda       = DB::table('tienda')->whereId($idtienda)->first();
      $frecuencias  = DB::table('s_prestamo_frecuencia')->get();
      $tipopersonas = DB::table('tipopersona')->get();
      $configuracion_prestamo = configuracion_prestamo($idtienda);
      return view('layouts/backoffice/tienda/sistema/prestamosolicitud/create', compact(
        'tienda',
        'frecuencias',
        'tipopersonas',
        'configuracion_prestamo'
      ));
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

        $rules = array_merge($rules,[
          'monto' => 'required',
          'numerocuota' => 'required',
          'fechainicio' => 'required',
          'idfrecuencia' => 'required',
          'idtasa' => 'required',
          'tasa' => 'required',
        ]);
        $messages = [
          'idcliente.required' => 'El "Cliente" es Obligatorio.',
          'idconyuge.required' => 'El "Cónyuge" es Obligatorio.',
          'idgarante.required' => 'El "Garante" es Obligatorio.',
          'monto.required' => 'El "Monto" es Obligatorio.',
          'numerocuota.required' => 'El "Nro. de Cuota" es Obligatorio.',
          'fechainicio.required' => 'La "Fecha de Inicio" es Obligatorio.',
          'idfrecuencia.required' => 'La "Frecuencia" es Obligatorio.',
          'idtasa.required' => 'La "Tasa" es Obligatorio.',
          'tasa.required' => 'El "Interes" es Obligatorio.',
        ];
        $this->validate($request, $rules, $messages);

        /*
         idestadocredito
         * 1 = credito pendiente
         * 2 = credito pre aprobado
         * 3 = aprobado
         * 4 = desembolsado
         * 5 = cancelado
         idestado
         * 1 = correcto
         * 2 = anulado
         idmoneda
         * 1 = soles
         * 2 = dolares
        */
        $cronograma = prestamo_cronograma(
          $idtienda,
          $request->monto,
          $request->numerocuota,
          $request->fechainicio,
          $request->idfrecuencia,
          $request->numerodias,
          $request->idtasa,
          $request->tasa,
          $request->gastoadministrativo!=null?$request->gastoadministrativo:0,
          $request->excluirferiado,
          $request->excluirsabado,
          $request->excluirdomingo
        );

        $idprestamo_credito = DB::table('s_prestamo_credito')->insertGetId([
          'fecharegistro' => Carbon::now(),
          'codigo' => Carbon::now()->format('ymdhis').rand(100, 999),
          'monto' => $request->input('monto'),
          'numerocuota' => $request->input('numerocuota'),
          'fechainicio' => $request->input('fechainicio'),
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
          'total_cuotafinal' => $cronograma['total_cuotafinal'],
          'idmoneda' => 1,
          'idasesor' => Auth::user()->id,
          'idcajero' => 0,
          'idsupervisor' => 0,
          'idcliente' => $request->input('idcliente'),
          'idconyuge' => $request->input('idconyuge')!='' ? $request->input('idconyuge') : 0,
          'idprestamo_frecuencia' => $request->input('idfrecuencia'),
          'idprestamo_tipotasa' => $request->idtasa,
          'idtienda' => $idtienda,
          'idestadocredito' => 1,
          'idestadogastoadministrativo' => 0,
          'idestado' => 1,
        ]);
        
        foreach($cronograma['cronograma'] as $value) {
              DB::table('s_prestamo_creditodetalle')->insert([
                'numero' => $value['numero'],
                'fechavencimiento' => $value['fechanormal'],
                'saldocapital' => $value['saldo'],
                'amortizacion' => $value['amortizacion'],
                'interes' => $value['interes'],
                'cuota' => $value['cuota'],
                'seguro' => $value['segurodesgravamen'],
                'gastoadministrativo' => 0,
                'total' => $value['cuotafinal'],
                'atraso' => 0,
                'moradescuento' => 0,
                'moraapagar' => 0,
                'cuotapago' => 0,
                'acuenta' => 0,
                'cuotaapagar' => 0,
                'idprestamo_credito' => $idprestamo_credito,
                'idestadocobranza' => 1,
                'idestado' => 1
              ]);
        }
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha registrado correctamente.'
        ]);
      }
      
      
        elseif ($request->input('view') == 'registrar-domicilio') {

            $rules = [
                'domicilio_idubigeo' => 'required',
                'domicilio_direccion' => 'required',
                'domicilio_reside_desdemes' => 'required',
                'domicilio_reside_desdeanio' => 'required',
                'domicilio_horaubicacion_de' => 'required',
                'domicilio_horaubicacion_hasta' => 'required',
                'domicilio_idtipopropiedad' => 'required',
                'domicilio_mapa_latitud' => 'required',
                'domicilio_mapa_longitud' => 'required',
            ];
            $messages = [
                'domicilio_direccion.required' => 'La "Dirección" es Obligatorio.',
                'domicilio_idubigeo.required' => 'El "Ubigeo" es Obligatorio.',
                'domicilio_referencia.required' => 'La "Referencia" es Obligatorio.',
                'domicilio_reside_desdemes.required' => 'El "Mes de residencia" es Obligatorio.',
                'domicilio_reside_desdeanio.required' => 'El "Año de residencia" es Obligatorio.',
                'domicilio_horaubicacion_de.required' => 'La "Hora" es Obligatorio.',
                'domicilio_horaubicacion_hasta.required' => 'La "Hora" es Obligatorio.',
                'domicilio_idtipopropiedad.required' => 'El "Tipo de Propiedad" es Obligatorio.',
                'domicilio_mapa_latitud.required' => 'La "Ubicación" es Obligatorio.',
                'domicilio_mapa_longitud.required' => 'La "Ubicación" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);

            /* idestado
             * 1 = activo
             * 2 = anulado
             */
            DB::table('s_prestamo_creditodomicilio')->insert([
                'fecharegistro' => Carbon::now(),
                'direccion' => $request->domicilio_direccion,
                'reside_desdemes' => $request->domicilio_reside_desdemes,
                'reside_desdeanio' => $request->domicilio_reside_desdeanio,
                'horaubicacion_de' => $request->domicilio_horaubicacion_de,
                'horaubicacion_hasta' => $request->domicilio_horaubicacion_hasta,
                'mapa_latitud' => $request->domicilio_mapa_latitud,
                'mapa_longitud' => $request->domicilio_mapa_longitud,
                'referencia' => $request->domicilio_referencia!=''?$request->domicilio_referencia:'',
                'idubigeo' => $request->domicilio_idubigeo,
                'idtipopropiedad' => $request->domicilio_idtipopropiedad,
                'idprestamo_credito' => $request->idprestamo_credito,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);

        } 
        elseif ($request->input('view') == 'registrar-laboral') {

            $rules = [
                'laboral_idfuenteingreso' => 'required',
                'laboral_idprestamo_giro' => 'required',
                'laboral_idprestamo_actividad' => 'required',
                'laboral_labora_desdemes' => 'required',
                'laboral_labora_desdeanio' => 'required',
                'laboral_idubigeo' => 'required',
                'laboral_direccion' => 'required',
            ];
            $messages = [
                'laboral_idfuenteingreso.required' => 'La "Fuente de Ingreso" es Obligatorio',
                'laboral_idprestamo_giro.required' => 'El "Giro" es Obligatorio',
                'laboral_idprestamo_actividad.required' => 'La "Actividad" es Obligatorio',
                'laboral_labora_desdemes.required' => 'La "Fecha de Labor" es Obligatorio',
                'laboral_labora_desdeanio.required' => 'La "Fecha de Labor" es Obligatorio',
                'laboral_idubigeo.required' => 'El "Ubigeo" es Obligatorio',
                'laboral_direccion.required' => 'La "Dirección" es Obligatorio',
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
            /* idfuenteingreso
             * 1 = Dependiente
             * 2 = Independiente
             *
             * idestado
             * 1 = activo
             * 2 = anulado
             */
            DB::table('s_prestamo_creditolaboral')->insert([
                'fecharegistro' => Carbon::now(),
                'venta' => 0,
                'compra' => 0,
                'ingreso' => 0,
                'egresogasto' => 0,
                'egresopago' => 0,
                'servicio' => 0,
                'ingresomensual' => 0,
                'direccion' => $request->laboral_direccion,
                'labora_desdemes' => $request->laboral_labora_desdemes,
                'labora_desdeanio' => $request->laboral_labora_desdeanio,
                'labora_lunes' => $labora_lunes,
                'labora_martes' => $labora_martes,
                'labora_miercoles' => $labora_miercoles,
                'labora_jueves' => $labora_jueves,
                'labora_viernes' => $labora_viernes,
                'labora_sabados' => $labora_sabado,
                'labora_domingos' => $labora_domingo,
                'mapa_latitud' => $request->laboral_mapa_latitud,
                'mapa_longitud' => $request->laboral_mapa_longitud,
                'referencia' => $request->laboral_referencia!=''?$request->laboral_referencia:'',
                'idubigeo' => $request->laboral_idubigeo,
                'idprestamo_giro' => $request->laboral_idprestamo_giro,
                'idprestamo_actividad' => $request->laboral_idprestamo_actividad,
                'idfuenteingreso' => $request->laboral_idfuenteingreso,
                'idprestamo_credito' => $request->idprestamo_credito,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);

        } 
        elseif ($request->input('view') == 'registrar-bien') {

          $rules = [
                'bien_idprestamo_tipobien' => 'required',
                'bien_valorestimado' => 'required',
                'bien_descripcion' => 'required',
            ];
            $messages = [
                'bien_idprestamo_tipobien.required' => 'El "Tipo de Bien" es Obligatorio.',
                'bien_descripcion.required' => 'La "Descripción" es Obligatorio.',
                'bien_valorestimado.required' => 'El "Valor Estimado" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);

            /* idestado
             * 1 = activo
             * 2 = anulado
             */
            DB::table('s_prestamo_creditobien')->insert([
                'fecharegistro' => Carbon::now(),
                'descripcion' => $request->bien_descripcion,
                'valorestimado' => $request->bien_valorestimado,
                'idprestamo_tipobien' => $request->bien_idprestamo_tipobien,
                'idprestamo_credito' => $request->idprestamo_credito,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);

        } 
        elseif ($request->input('view') == 'registrar-cualitativo') {

            $rules = [
              'destino' => 'required',
              'descripcion' => 'required',
              'idcalificacion' => 'required',
    //           'archivo' => 'required',
              'comentario' => 'required',
            ];
            $messages = [
              'destino.required' => 'El "Destino" es Obligatorio.',
              'descripcion.required' => 'La "Descripción" es Obligatorio.',
              'idcalificacion.required' => 'La "Calificación" es Obligatorio.',
    //           'archivo.required' => 'El "Archivo" es Obligatorio.',
              'comentario.required' => 'El "Comentario" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);
          
            $post_respuesta = json_decode($request->respuestas);
            foreach ($post_respuesta as $value) {
              if ($value->valorbueno == 0 && $value->valorregular == 0 && $value->valormalo == 0) {
                return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje' => 'Debe responder todas las preguntas.'
                ]);
              }
            }
          
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
                }
            }
          
            $avales = explode('/&/', $request->avales);
            for($i = 1; $i < count($avales); $i++){
                $item = explode('/,/',$avales[$i]);
                if($item[0]==''){
                    return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje' => 'La "Persona" es Obligatorio.'
                    ]);
                }
            }

            // eliminando datos
            $creditocualitativo = DB::table('s_prestamo_creditocualitativo')->where('idprestamo_credito', $request->idprestamo_credito)->first();
            if($creditocualitativo!=''){
                DB::table('s_prestamo_creditocualitativodetalle')->where('idprestamo_cualitativo', $creditocualitativo->id)->delete();
                DB::table('s_prestamo_creditocualitativo')->where('idprestamo_credito', $request->idprestamo_credito)->delete();
            }
                
            // fin eliminando datos

            $idprestamo_cualitativo = DB::table('s_prestamo_creditocualitativo')->insertGetId([
              'fecharegistro' => Carbon::now(),
              'destino' => $request->destino,
              'descripcion' => $request->descripcion,
              'archivo' => '',
              'comentario' => $request->comentario,
              'idprestamo_credito' => $request->idprestamo_credito,
              'idprestamo_calificacion' => $request->idcalificacion,
              'idtienda' => $idtienda,
              'idestado' => 1
            ]);


            foreach ($post_respuesta as $value) {
              DB::table('s_prestamo_creditocualitativodetalle')->insert([
                'valorbueno' => $value->valorbueno,
                'valorregular' => $value->valorregular,
                'valormalo' => $value->valormalo,
                'idprestamo_cualitativopregunta' => $value->idcualitativopregunta,
                'idprestamo_cualitativo' => $idprestamo_cualitativo,
              ]);
            }

            // relaciones
            DB::table('s_prestamo_creditorelacion')->where('idprestamo_credito', $request->idprestamo_credito)->delete();
            $referencias = explode('/&/', $request->referencias);
            for($i = 1; $i < count($referencias); $i++){
                $item = explode('/,/',$referencias[$i]);
                DB::table('s_prestamo_creditorelacion')->insert([
                    'numerotelefono' => $item[2],
                    'idpersona' => $item[0],
                    'idprestamo_tiporelacion' => $item[1],
                    'idprestamo_credito' => $request->idprestamo_credito,
                    'idtienda' => $idtienda,
                ]);
            }
          
            // avales
            DB::table('s_prestamo_creditoaval')->where('idprestamo_credito', $request->idprestamo_credito)->delete();
            $avales = explode('/&/', $request->avales);
            for($i = 1; $i < count($avales); $i++){
                $item = explode('/,/',$avales[$i]);
                DB::table('s_prestamo_creditoaval')->insert([
                    'idpersona' => $item[0],
                    'idprestamo_credito' => $request->idprestamo_credito,
                    'idtienda' => $idtienda,
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
        if ($id == 'show-creditocalendario') {
            $configuracion_prestamo = configuracion_prestamo($idtienda);

            $cronograma = prestamo_cronograma(
                $idtienda,
                $request->monto,
                $request->numerocuota,
                $request->fechainicio,
                $request->frecuencia,
                $request->numerodias,
                $request->tipotasa,
                $request->tasa,
                $request->gastoadministrativo!=null?$request->gastoadministrativo:0,
                $request->excluirferiado,
                $request->excluirsabado,
                $request->excluirdomingo
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
                                <td style="padding: 8px;text-align: right;">Cuota</td>

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
                          </tr>';
                }
                $html .= '<tr style="background-color: #31353c;color: white;">
                              <td style="padding: 8px;text-align: right;width: 50px;" colspan="3">TOTAL</td>
                              <td style="padding: 8px;text-align: right;">'.$cronograma['total_amortizacion'].'</td>
                              <td style="padding: 8px;text-align: right;">'.$cronograma['total_interes'].'</td>
                              '.(($cronograma['total_segurodesgravamen']>0)?'<td style="padding: 8px;text-align: right;">'.$cronograma['total_segurodesgravamen'].'</td>':'').'
                              '.(($cronograma['total_gastoadministrativo']>0)?'<td style="padding: 8px;text-align: right;">'.$cronograma['total_gastoadministrativo'].'</td>':'').'
                              <td style="padding: 8px;text-align: right;">'.$cronograma['total_cuotafinal'].'</td>
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
                'total_cuotafinal' => $cronograma['total_cuotafinal']
            ]);
        }
        elseif ($id == 'show-creditodesembolsado') {
            $buscar_fechainicio = $request->input('columns')[3]['search']['value'];
            $buscar_idprestamo_frecuencia = $request->input('columns')[4]['search']['value'];
            $buscar_idprestamo_tipotasa = $request->input('columns')[5]['search']['value'];
            $buscar_asesor_nombre = $request->input('columns')[7]['search']['value'];
            $buscar_cliente = $request->input('columns')[8]['search']['value'];
          
            $where = [];
            if($buscar_fechainicio!=''){
                $where[] = ['s_prestamo_credito.fechainicio',$buscar_fechainicio];
            }
            if($buscar_idprestamo_frecuencia!=''){
                $where[] = ['s_prestamo_frecuencia.id',$buscar_idprestamo_frecuencia];
            }
            if($buscar_idprestamo_tipotasa!=''){
                $where[] = ['s_prestamo_credito.idprestamo_tipotasa',$buscar_idprestamo_tipotasa];
            }
            $where[] = ['asesor.nombre','LIKE','%'.$buscar_asesor_nombre.'%'];
            $where[] = ['cliente.nombre','LIKE','%'.$buscar_cliente.'%'];
        
            if($request->input('view')=='pendiente'){
                $where[] = ['s_prestamo_credito.idestadocredito', 1];
            }elseif($request->input('view')=='preaprobado'){
                $where[] = ['s_prestamo_credito.idestadocredito', 2];
            }elseif($request->input('view')=='aprobado'){
                $where[] = ['s_prestamo_credito.idestadocredito', 3];
            }elseif($request->input('view')=='desembolsado'){
                $where[] = ['s_prestamo_credito.idestadocredito', 4];
            }
        
            $where1 = [];
            if($buscar_fechainicio!=''){
                $where1[] = ['s_prestamo_credito.fechainicio',$buscar_fechainicio];
            }
            if($buscar_idprestamo_frecuencia!=''){
                $where1[] = ['s_prestamo_frecuencia.id',$buscar_idprestamo_frecuencia];
            }
            if($buscar_idprestamo_tipotasa!=''){
                $where1[] = ['s_prestamo_credito.idprestamo_tipotasa',$buscar_idprestamo_tipotasa];
            }
            $where1[] = ['asesor.nombre','LIKE','%'.$buscar_asesor_nombre.'%'];
            $where1[] = ['cliente.apellidos','LIKE','%'.$buscar_cliente.'%'];
            if($request->input('view')=='pendiente'){
                $where1[] = ['s_prestamo_credito.idestadocredito', 1];
            }elseif($request->input('view')=='preaprobado'){
                $where1[] = ['s_prestamo_credito.idestadocredito', 2];
            }elseif($request->input('view')=='aprobado'){
                $where1[] = ['s_prestamo_credito.idestadocredito', 3];
            }elseif($request->input('view')=='desembolsado'){
                $where1[] = ['s_prestamo_credito.idestadocredito', 4];
            }
        
          $prestamocreditos_desembolsados = DB::table('s_prestamo_credito')
              ->join('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_credito.idprestamo_frecuencia')
              ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->where($where)
              ->where('s_prestamo_credito.idtienda', $idtienda)
              ->where('s_prestamo_credito.idasesor', Auth::user()->id)
              ->orWhere($where1)
              ->where('s_prestamo_credito.idtienda', $idtienda)
              ->where('s_prestamo_credito.idasesor', Auth::user()->id)
              ->select(
                  's_prestamo_credito.id as idcredito',
                  's_prestamo_credito.idtienda as idtienda',
                  's_prestamo_credito.fecharegistro as fecharegistro',
                  's_prestamo_credito.fechaaprobado as fechaaprobado',
                  's_prestamo_credito.fechapreaprobado as fechapreaprobado',
                  's_prestamo_credito.fechadesembolsado as fechadesembolsado',
                  's_prestamo_credito.monto as monto',
                  's_prestamo_credito.numerocuota as numerocuota',
                  's_prestamo_credito.fechainicio as fechainicio',
                  's_prestamo_credito.idprestamo_tipotasa as idprestamo_tipotasa',
                  's_prestamo_credito.tasa as tasa',
                  's_prestamo_credito.idestadocredito as idestadocredito',
                  's_prestamo_credito.idestado as idestado',
                  's_prestamo_frecuencia.nombre as frecuencia_nombre',
                  'asesor.nombre as asesor_nombre',
                  DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
              )
              ->orderBy('s_prestamo_credito.id','desc')
              ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));

            $tabla = [];
            foreach($prestamocreditos_desembolsados as $value){
                $tipotasa = '';
                if($value->idprestamo_tipotasa==1){
                    $tipotasa = 'Fija';
                }elseif($value->idprestamo_tipotasa==2){
                    $tipotasa = 'Efectiva';
                }
              
                $estado = '';
                if($value->idestado==1){
                    $estado = '<span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Activado</span>';
                }elseif($value->idestado==2){
                    $estado = '<span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span>';
                }
              
                $fecharegistro = $value->fecharegistro;
                if($value->idestadocredito==1){
                    if($value->idestado==1){
                        $opcion = '<li><a href="javascript:;" onclick="editar_pendiente('.$idtienda.','.$value->idcredito.')"><i class="fa fa-edit"></i> Editar</a></li>
                                    <li><a href="javascript:;" onclick="preaprobar_pendiente('.$idtienda.','.$value->idcredito.')"><i class="fa fa-check"></i> Pre-Aprobar</a></li>
                                    <li><a href="javascript:;" onclick="detalle_pendiente('.$idtienda.','.$value->idcredito.')"><i class="fa fa-list"></i> Detalle</a></li>
                                    <li><a href="javascript:;" onclick="anular_pendiente('.$idtienda.','.$value->idcredito.')"><i class="fa fa-ban"></i> Anular</a></li>';
                    }elseif($value->idestado==2){
                        $opcion = '<li><a href="javascript:;" onclick="detalle_preaprobado('.$idtienda.','.$value->idcredito.')"><i class="fa fa-list"></i> Detalle</a></li>';
                    }
                        
                $fecharegistro = $value->fecharegistro;
                }elseif($value->idestadocredito==2){
                $opcion = '<li><a href="javascript:;" onclick="detalle_preaprobado('.$idtienda.','.$value->idcredito.')"><i class="fa fa-list"></i> Detalle</a></li>';
                $fecharegistro = $value->fechaaprobado;
                }elseif($value->idestadocredito==3){
                $opcion = '<li><a href="javascript:;" onclick="detalle_aprobado('.$idtienda.','.$value->idcredito.')"><i class="fa fa-list"></i> Detalle</a></li>';
                $fecharegistro = $value->fechapreaprobado;
                }elseif($value->idestadocredito==4){
                $opcion = '<li><a href="javascript:;" onclick="detalle_desembolsado('.$idtienda.','.$value->idcredito.')"><i class="fa fa-list"></i> Detalle</a></li>';
                $fecharegistro = $value->fechadesembolsado;
                }
              
                $tabla[] = [
                    'fecharegistro' => date_format(date_create($fecharegistro), "d/m/Y h:i:s A"),
                    'monto' => $value->monto,
                    'numerocuota' => $value->numerocuota,
                    'fechainicio' => date_format(date_create($value->fechainicio), "d/m/Y"),
                    'frecuencia_nombre' => $value->frecuencia_nombre,
                    'tipotasa' => $tipotasa,
                    'tasa' => $value->tasa,
                    'asesor_nombre' => $value->asesor_nombre,
                    'cliente' => $value->cliente,
                    'estado' => $estado,
                    'opcion' => $opcion
                ];
            }
          
            return json_encode([
                'draw' => $request->input('draw'),
                'recordsTotal' => $prestamocreditos_desembolsados->total(),
                'recordsFiltered' => $prestamocreditos_desembolsados->total(),
                'data' => $tabla
            ]);
        }
      
        elseif ($id == 'show-actividad') {
            $actividades = DB::table('s_prestamo_actividad')
                ->where([
                    ['s_prestamo_actividad.idprestamo_giro', $request->idprestamo_giro]
                ])
                ->get();
            $html = "<option></option>";
            foreach ($actividades as $value) {
                $html .= "<option value='$value->id'>$value->nombre</option>";
            }
            return [
                'actividades' => $html
            ];
        }
        elseif ($id == 'show-domicilio') {
            $direcciones = DB::table('s_prestamo_creditodomicilio')
                ->join('ubigeo', 'ubigeo.id', 's_prestamo_creditodomicilio.idubigeo')
                ->where([
                    ['s_prestamo_creditodomicilio.idprestamo_credito', $request->idprestamo_credito],
                    ['s_prestamo_creditodomicilio.idtienda', $request->idtienda],
                    ['s_prestamo_creditodomicilio.idestado', 1]
                ])
                ->select(
                    's_prestamo_creditodomicilio.*',
                    'ubigeo.nombre as ubigeonombre'
                )
                ->orderBy('s_prestamo_creditodomicilio.id','desc')
                ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));
    
            $tabla = [];
            foreach($direcciones as $value){
                $tipopropiedad = '';
                if ($value->idtipopropiedad == 1){
                    $tipopropiedad = 'Alquilado';
                }
                elseif ($value->idtipopropiedad == 2){
                    $tipopropiedad = 'Familiar';
                }
                elseif ($value->idtipopropiedad == 3){
                    $tipopropiedad = 'Propio';
                }
              
                if($request->estado=='lectura'){
                $opcion = '<li><a href="javascript:;" onclick="domicilio_imagen('.$value->id.')"><i class="fa fa-images"></i> Imágenes de Referencia</a></li>
                            <li><a href="javascript:;" onclick="domicilio_detalle('.$value->id.')"><i class="fa fa-list-alt"></i> Detalle</a></li>';
  
                }else{
                $opcion = '<li><a href="javascript:;" onclick="domicilio_edit('.$value->id.')"><i class="fa fa-edit"></i> Editar</a></li>
                            <li><a href="javascript:;" onclick="domicilio_imagen('.$value->id.')"><i class="fa fa-images"></i> Imágenes de Referencia</a></li>
                            <li><a href="javascript:;" onclick="domicilio_detalle('.$value->id.')"><i class="fa fa-list-alt"></i> Detalle</a></li>
                            <li><a href="javascript:;" onclick="domicilio_eliminar('.$value->id.')"><i class="fa fa-trash"></i> Eliminar</a></li>';
                }
                $tabla[] = [
                    'iddomicilio' => $value->id,
                    'ubigeonombre' => $value->ubigeonombre,
                    'direccion' => $value->direccion,
                    'referencia' => $value->referencia,
                    'residesde' => mesesEs($value->reside_desdemes).', '.$value->reside_desdeanio,
                    'horaubicacion' => date_format(date_create($value->horaubicacion_de),"h:i:s A").' - '.date_format(date_create($value->horaubicacion_hasta),"h:i:s A"),
                    'tipopropiedad' => $tipopropiedad,
                    'referencia' => $value->referencia,
                    'opcion' => $opcion,
                ];
            }
          
            return json_encode([
                'draw' => $request->input('draw'),
                'recordsTotal' => $direcciones->total(),
                'recordsFiltered' => $direcciones->total(),
                'data' => $tabla
            ]);
        }
        elseif ($id == 'show-imagendomicilio') {

            $tienda = DB::table('tienda')->whereId($request->idtienda)->first();
            $prestamodomicilio = DB::table('s_prestamo_creditodomicilio')
              ->whereId($request->idprestamo_creditodomicilio)
              ->first();
            $prestamodomicilioimagen = DB::table('s_prestamo_creditodomicilioimagen')
              ->where('s_prestamo_creditodomicilioimagen.idprestamo_creditodomicilio', $request->idprestamo_creditodomicilio)
              ->get();

            $i = 1;
            $html = "";
            foreach($prestamodomicilioimagen as $value) {
                $delete = '';
                if($request->estado!='lectura'){
                    $delete = '<form class="js-validation-signin px-30 form-prestamodomicilioimagen'.$value->id.'" action="javascript:;" 
                                  onsubmit="callback({
                                                          route:  \'backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/0\',
                                                          method: \'DELETE\',
                                                          data:   {
                                                              view : \'eliminarimagendomicilio\',
                                                              idprestamo_creditodomicilioimagen: '.$value->id.',
                                                          }
                                                      },
                                                      function(resultado){
                                                          imagen_domicilio('.$value->idprestamo_creditodomicilio.');
                                                      },this)">
                            </form>
                            <a href="javascript:;" onclick="removeimagendomicilio('.$value->id.')" id="eliminar-imagen">x</a>';
                }
                  
                $html .= '<div class="gallery-item">
                    <div class="grid-item-holder">
                        <div class="box-item" style="
                              background-image: url('.url('public/backoffice/tienda/'.$tienda->id.'/creditodomicilio/'.$value->imagen).');
                              background-repeat: no-repeat;
                              background-size: contain;
                              background-position: center;" onclick="$("#imggaleria'.$value->id.'").click()">
                              '.$delete.'
                            <div class="orden-imagen">'.$i.'</div>
                        </div>
                    </div>
                </div>';
                $i++;
            }
            return [
                'prestamodomicilio' => $prestamodomicilio,
                'tienda' => $tienda,
                'imagenes' => $html,
            ];
        }
        elseif ($id == 'show-laboral') {
            $labores = DB::table('s_prestamo_creditolaboral')
                ->join('ubigeo', 'ubigeo.id', 's_prestamo_creditolaboral.idubigeo')
                ->leftJoin('s_prestamo_actividad', 's_prestamo_actividad.id', 's_prestamo_creditolaboral.idprestamo_actividad')
                ->leftJoin('s_prestamo_giro', 's_prestamo_giro.id', 's_prestamo_actividad.idprestamo_giro')
                ->where([
                    ['s_prestamo_creditolaboral.idprestamo_credito', $request->idprestamo_credito],
                    ['s_prestamo_creditolaboral.idtienda', $request->idtienda],
                    ['s_prestamo_creditolaboral.idestado', 1]
                ])
                ->select(
                    's_prestamo_creditolaboral.*',
                    'ubigeo.nombre as nombre_ubigeo',
                    's_prestamo_actividad.nombre as nombre_actividad',
                    's_prestamo_giro.nombre as nombre_giro'
                )
                ->orderBy('s_prestamo_creditolaboral.id','desc')
                ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));
          
            $tabla = [];
            foreach($labores as $value){
                $fuenteingreso = '';
                if ($value->idfuenteingreso == 1){
                    $fuenteingreso = 'Dependiente';
                }
                elseif ($value->idfuenteingreso == 2){
                    $fuenteingreso = 'Independiente';
                }
              
                if($request->estado=='lectura'){
                $opcion = '<li><a href="javascript:;" onclick="laboral_detalle('.$value->id.')"><i class="fa fa-list-alt"></i> Detalle</a></li>';
  
                }else{
                $opcion = '<li><a href="javascript:;" onclick="laboral_edit('.$value->id.')"><i class="fa fa-edit"></i> Editar</a></li>
                            <li><a href="javascript:;" onclick="laboral_detalle('.$value->id.')"><i class="fa fa-list-alt"></i> Detalle</a></li>
                            <li><a href="javascript:;" onclick="laboral_eliminar('.$value->id.')"><i class="fa fa-trash"></i> Eliminar</a></li>';
                }
              
                $tabla[] = [
                    'idlaboral' => $value->id,
                    'fuenteingreso' => $fuenteingreso,
                    'giro' => $value->nombre_giro,
                    'actividad' => $value->nombre_actividad,
                    'ingresomensual' => $value->ingresomensual,
                    'laboradesde' => mesesEs($value->labora_desdemes).', '.$value->labora_desdeanio,
                    'ubigeo' => $value->nombre_ubigeo,
                    'direccion' => $value->direccion,
                    'referencia' => $value->referencia,
                    'opcion' => $opcion,
                ];
            }
          
            return json_encode([
                'draw' => $request->input('draw'),
                'recordsTotal' => $labores->total(),
                'recordsFiltered' => $labores->total(),
                'data' => $tabla
            ]);
        }
        elseif ($id == 'show-bien') {
            $bienes = DB::table('s_prestamo_creditobien')
                ->join('s_prestamo_tipobien', 's_prestamo_tipobien.id', 's_prestamo_creditobien.idprestamo_tipobien')
                ->where([
                    ['s_prestamo_creditobien.idprestamo_credito', $request->idprestamo_credito],
                    ['s_prestamo_creditobien.idtienda', $request->idtienda],
                    ['s_prestamo_creditobien.idestado', 1]
                ])
                ->select(
                    's_prestamo_creditobien.*',
                    's_prestamo_tipobien.nombre as nombre_tipobien'
                )
                ->orderBy('s_prestamo_creditobien.id','desc')
                ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));
          
            $tabla = [];
            foreach($bienes as $value){
              
                if($request->estado=='lectura'){
                $opcion = '<li><a href="javascript:;" onclick="bien_imagen('.$value->id.')"><i class="fa fa-images"></i> Imágenes</a></li>
                            <li><a href="javascript:;" onclick="bien_detalle('.$value->id.')"><i class="fa fa-list-alt"></i> Detalle</a></li>';
  
                }else{
                $opcion = '<li><a href="javascript:;" onclick="bien_edit('.$value->id.')"><i class="fa fa-edit"></i> Editar</a></li>
                            <li><a href="javascript:;" onclick="bien_imagen('.$value->id.')"><i class="fa fa-images"></i> Imágenes</a></li>
                            <li><a href="javascript:;" onclick="bien_detalle('.$value->id.')"><i class="fa fa-list-alt"></i> Detalle</a></li>
                            <li><a href="javascript:;" onclick="bien_eliminar('.$value->id.')"><i class="fa fa-trash"></i> Eliminar</a></li>';
                }

                $tabla[] = [
                    'idbien' => $value->id,
                    'tipobien' => $value->nombre_tipobien,
                    'descripcion' => $value->descripcion,
                    'valorestimado' => $value->valorestimado,
                    'opcion' => $opcion,
                ];
            }
          
            return json_encode([
                'draw' => $request->input('draw'),
                'recordsTotal' => $bienes->total(),
                'recordsFiltered' => $bienes->total(),
                'data' => $tabla
            ]);
        }
        elseif ($id == 'show-imagenbien') {

            $tienda = DB::table('tienda')->whereId($request->idtienda)->first();
            $prestamobien = DB::table('s_prestamo_creditobien')
                ->join('s_prestamo_tipobien', 's_prestamo_tipobien.id', 's_prestamo_creditobien.idprestamo_tipobien')
                ->where('s_prestamo_creditobien.id', $request->idprestamo_creditobien)
                ->select(
                    's_prestamo_creditobien.*',
                    's_prestamo_tipobien.nombre as nombre_tipobien'
                )
                ->first();
            $prestamobienimagen = DB::table('s_prestamo_creditobienimagen')->where('idprestamo_creditobien', $request->idprestamo_creditobien)->get();

            $i = 1;
            $html = "";
            foreach($prestamobienimagen as $value) {
                $delete = '';
                if($request->estado!='lectura'){
                    $delete = '<form class="js-validation-signin px-30 form-prestamobienimagen'.$value->id.'" action="javascript:;" 
                                  onsubmit="callback({
                                                          route:  \'backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/0\',
                                                          method: \'DELETE\',
                                                          data:   {
                                                              view : \'eliminarimagenbien\',
                                                              idprestamo_creditobienimagen: '.$value->id.',
                                                          }
                                                      },
                                                      function(resultado){
                                                          imagen_bien('.$value->idprestamo_creditobien.');
                                                      },this)">
                            </form>
                            <a href="javascript:;" onclick="removeimagenbien('.$value->id.')" id="eliminar-imagen">x</a>';
                }
                $html .= '<div class="gallery-item">
                    <div class="grid-item-holder">
                        <div class="box-item" style="
                              background-image: url('.url('public/backoffice/tienda/'.$tienda->id.'/creditobien/'.$value->imagen).');
                              background-repeat: no-repeat;
                              background-size: contain;
                              background-position: center;" onclick="$("#imggaleria'.$value->id.'").click()">
                            '.$delete.'
                            <div class="orden-imagen">'.$i.'</div>
                        </div>
                    </div>
                </div>';
                $i++;
            }
            return [
                'prestamobien' => $prestamobien,
                'tienda' => $tienda,
                'imagenes' => $html,
            ];
        }
      
      
        elseif ($id == 'show-select-tiporelacion') {
            
            $tiporelaciones = DB::table('s_prestamo_tiporelacion')
                ->select(
                  's_prestamo_tiporelacion.id as id',
                  's_prestamo_tiporelacion.nombre as text'
                )
                ->get();
          
            return $tiporelaciones;
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

        $prestamocredito = DB::table('s_prestamo_credito')
            ->join('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_credito.idprestamo_frecuencia')
            ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
            ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
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
              'cliente.idubigeo as clienteidubigeo',
              'cliente.direccion as clientedireccion',
              'cliente.referencia as clientereferencia',
              'conyuge.identificacion as conyugeidentificacion',
              'conyuge.nombre as conyugenombre',
              'conyuge.apellidos as conyugeapellidos',
              DB::raw('IF(asesor.idtipopersona = 1 || asesor.idtipopersona = 3,
                  CONCAT(asesor.identificacion, " - ", asesor.apellidos, ", ", asesor.nombre),
                  CONCAT(asesor.identificacion, " - ", asesor.apellidos)) as asesor_nombre'),
              DB::raw('IF(cliente.idtipopersona = 1 || cliente.idtipopersona = 3,
                  CONCAT(cliente.identificacion, " - ", cliente.apellidos, ", ", cliente.nombre),
                  CONCAT(cliente.identificacion, " - ", cliente.apellidos)) as cliente_nombre'),
              DB::raw('IF(conyuge.idtipopersona = 1 || conyuge.idtipopersona = 3,
                  CONCAT(conyuge.identificacion, " - ", conyuge.apellidos, ", ", conyuge.nombre),
                  CONCAT(conyuge.identificacion, " - ", conyuge.apellidos)) as conyuge_nombre')
            )
            ->first();

        if($request->view == 'editar') {
          $frecuencias  = DB::table('s_prestamo_frecuencia')->get();
          $tipopersonas = DB::table('tipopersona')->get();
          $configuracion_prestamo = configuracion_prestamo($idtienda);
          return view('layouts/backoffice/tienda/sistema/prestamosolicitud/edit', compact(
            'tienda',
            'frecuencias',
            'tipopersonas',
            'prestamocredito',
            'configuracion_prestamo'
          ));
        }
        elseif ($request->view == 'preaprobar') {
          $configuracion_prestamo = configuracion_prestamo($idtienda);
          $prestamocreditodetalle = DB::table('s_prestamo_creditodetalle')
            ->where('s_prestamo_creditodetalle.idprestamo_credito', $prestamocredito->id)
            ->get();
          return view('layouts/backoffice/tienda/sistema/prestamosolicitud/preaprobar', compact(
            'tienda',
            'prestamocredito',
            'prestamocreditodetalle',
            'configuracion_prestamo'
          ));
        }
        elseif ($request->view == 'detalle') {
          $configuracion_prestamo = configuracion_prestamo($idtienda);
          $prestamocreditodetalle = DB::table('s_prestamo_creditodetalle')
            ->where('s_prestamo_creditodetalle.idprestamo_credito', $prestamocredito->id)
            ->get();
          return view('layouts/backoffice/tienda/sistema/prestamosolicitud/detalle', compact(
            'tienda',
            'prestamocredito',
            'prestamocreditodetalle',
            'configuracion_prestamo'
          ));
        }
        elseif ($request->view == 'anular') {
        $configuracion_prestamo = configuracion_prestamo($idtienda);
        $prestamocreditodetalle = DB::table('s_prestamo_creditodetalle')
          ->where('s_prestamo_creditodetalle.idprestamo_credito', $prestamocredito->id)
          ->get();
        return view('layouts/backoffice/tienda/sistema/prestamosolicitud/anular', compact(
          'tienda',
          'prestamocredito',
          'prestamocreditodetalle',
          'configuracion_prestamo'
        ));
      }
      
        elseif ($request->view == 'domicilio') {
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/domicilio',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'estado' => $request->estado
            ]);  
        } 
        elseif ($request->view == 'domiciliocreate') {
            $ubigeo = DB::table('ubigeo')->get();
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/domiciliocreate',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'ubigeo' => $ubigeo
            ]);  
        }
        elseif ($request->view == 'domicilioedit') {
            $prestamodomicilio = DB::table('s_prestamo_creditodomicilio')
                ->join('ubigeo', 'ubigeo.id', 's_prestamo_creditodomicilio.idubigeo')
                ->where('s_prestamo_creditodomicilio.id', $request->iddomicilio)
                ->select(
                    's_prestamo_creditodomicilio.*',
                    'ubigeo.nombre as nombre_ubigeo'
                )
                ->first();
            $ubigeo = DB::table('ubigeo')->get();
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/domicilioedit',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'prestamodomicilio' => $prestamodomicilio,
                'ubigeo' => $ubigeo
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
        elseif ($request->view == 'domiciliodetalle') {
            $prestamodomicilio = DB::table('s_prestamo_creditodomicilio')
                ->join('ubigeo', 'ubigeo.id', 's_prestamo_creditodomicilio.idubigeo')
                ->where('s_prestamo_creditodomicilio.id', $request->iddomicilio)
                ->select(
                    's_prestamo_creditodomicilio.*',
                    'ubigeo.nombre as nombre_ubigeo'
                )
                ->first();
            $ubigeo = DB::table('ubigeo')->get();
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/domiciliodetalle',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'prestamodomicilio' => $prestamodomicilio,
                'ubigeo' => $ubigeo
            ]);  
        }
        elseif ($request->view == 'domicilioeliminar') {
            $prestamodomicilio = DB::table('s_prestamo_creditodomicilio')
                ->join('ubigeo', 'ubigeo.id', 's_prestamo_creditodomicilio.idubigeo')
                ->where('s_prestamo_creditodomicilio.id', $request->iddomicilio)
                ->select(
                    's_prestamo_creditodomicilio.*',
                    'ubigeo.nombre as nombre_ubigeo'
                )
                ->first();
            $ubigeo = DB::table('ubigeo')->get();
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/domicilioeliminar',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'prestamodomicilio' => $prestamodomicilio,
                'ubigeo' => $ubigeo
            ]);  
        }
      
        elseif ($request->view == 'laboral') {
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/laboral',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'estado' => $request->estado
            ]);  
        } 
        elseif ($request->view == 'laboralcreate') {
            $giro = DB::table('s_prestamo_giro')->get();
            $ubigeo = DB::table('ubigeo')->get();
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/laboralcreate',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'ubigeo' => $ubigeo,
                'giro' => $giro
            ]);  
        }
        elseif ($request->view == 'laboraledit') {
          $prestamolaboral = DB::table('s_prestamo_creditolaboral')
            ->leftJoin('s_prestamo_giro', 's_prestamo_giro.id', 's_prestamo_creditolaboral.idprestamo_giro')
            ->leftJoin('s_prestamo_actividad', 's_prestamo_actividad.id', 's_prestamo_creditolaboral.idprestamo_actividad')
            ->join('ubigeo', 'ubigeo.id', 's_prestamo_creditolaboral.idubigeo')
            ->where('s_prestamo_creditolaboral.id', $request->idlaboral)
            ->select(
                's_prestamo_creditolaboral.*',
                's_prestamo_giro.nombre as nombre_giro',
                's_prestamo_actividad.nombre as nombre_actividad',
                'ubigeo.nombre as nombre_ubigeo',
                DB::raw('IF(s_prestamo_creditolaboral.idfuenteingreso = 1,
                    "Dependiente", "Independiente") as fuenteingreso')
            )
            ->first();
          $ubigeo = DB::table('ubigeo')->get();
          $giro = DB::table('s_prestamo_giro')->get();
          $productos = DB::table('s_prestamo_producto')->get();
          $conceptoingresos = DB::table('s_prestamo_conceptoingreso')->get();
          $conceptoegresogastos = DB::table('s_prestamo_conceptoegresogasto')->get();
          $conceptoegresopagos = DB::table('s_prestamo_conceptoegresopago')->get();

          $laboralventa = DB::table('s_prestamo_creditolaboralventa')->where('s_idprestamo_creditolaboral', $request->idlaboral)->orderBy('id','asc')->get();
          $laboralcompra = DB::table('s_prestamo_creditolaboralcompra')->where('s_idprestamo_creditolaboral', $request->idlaboral)->orderBy('id','asc')->get();
          $laboralingreso = DB::table('s_prestamo_creditolaboralingreso')->where('s_idprestamo_creditolaboral', $request->idlaboral)->orderBy('id','asc')->get();
          $laboralegresogasto = DB::table('s_prestamo_creditolaboralegresogasto')->where('s_idprestamo_creditolaboral', $request->idlaboral)->orderBy('id','asc')->get();
          $laboralegresopago = DB::table('s_prestamo_creditolaboralegresopago')->where('s_idprestamo_creditolaboral', $request->idlaboral)->orderBy('id','asc')->get();
          $laboralservicio = DB::table('s_prestamo_creditolaboralservicio')->where('s_idprestamo_creditolaboral', $request->idlaboral)->limit(1)->first();
          return view('layouts/backoffice/tienda/sistema/prestamosolicitud/laboraledit',[
            'tienda' => $tienda,
            'prestamocredito' => $prestamocredito,
            'prestamolaboral' => $prestamolaboral,
            'ubigeo' => $ubigeo,
            'giro' => $giro,
            'productos' => $productos,
            'conceptoingresos' => $conceptoingresos,
            'conceptoegresogastos' => $conceptoegresogastos,
            'conceptoegresopagos' => $conceptoegresopagos,
            'laboralventa' => $laboralventa,
            'laboralcompra' => $laboralcompra,
            'laboralingreso' => $laboralingreso,
            'laboralegresogasto' => $laboralegresogasto,
            'laboralegresopago' => $laboralegresopago,
            'laboralservicio' => $laboralservicio
          ]);  
        }
        elseif ($request->view == 'laboraldetalle') {
            $prestamolaboral = DB::table('s_prestamo_creditolaboral')
            ->leftJoin('s_prestamo_giro', 's_prestamo_giro.id', 's_prestamo_creditolaboral.idprestamo_giro')
            ->leftJoin('s_prestamo_actividad', 's_prestamo_actividad.id', 's_prestamo_creditolaboral.idprestamo_actividad')
            ->join('ubigeo', 'ubigeo.id', 's_prestamo_creditolaboral.idubigeo')
            ->where('s_prestamo_creditolaboral.id', $request->idlaboral)
            ->select(
                's_prestamo_creditolaboral.*',
                's_prestamo_giro.nombre as nombre_giro',
                's_prestamo_actividad.nombre as nombre_actividad',
                'ubigeo.nombre as nombre_ubigeo',
                DB::raw('IF(s_prestamo_creditolaboral.idfuenteingreso = 1,
                    "Dependiente", "Independiente") as fuenteingreso')
            )
            ->first();
          $ubigeo = DB::table('ubigeo')->get();
          $giro = DB::table('s_prestamo_giro')->get();
          $productos = DB::table('s_prestamo_producto')->get();
          $conceptoingresos = DB::table('s_prestamo_conceptoingreso')->get();
          $conceptoegresogastos = DB::table('s_prestamo_conceptoegresogasto')->get();
          $conceptoegresopagos = DB::table('s_prestamo_conceptoegresopago')->get();

          $laboralventa = DB::table('s_prestamo_creditolaboralventa')
              ->leftJoin('s_prestamo_producto', 's_prestamo_producto.id', 's_prestamo_creditolaboralventa.s_idprestamo_producto')
              ->where('s_idprestamo_creditolaboral', $request->idlaboral)
              ->select(
                  's_prestamo_creditolaboralventa.*',
                  's_prestamo_producto.nombre as productonombre',
              )
              ->orderBy('id','asc')
              ->get();
          $laboralcompra = DB::table('s_prestamo_creditolaboralcompra')
              ->leftJoin('s_prestamo_producto', 's_prestamo_producto.id', 's_prestamo_creditolaboralcompra.s_idprestamo_producto')
              ->where('s_idprestamo_creditolaboral', $request->idlaboral)
              ->select(
                  's_prestamo_creditolaboralcompra.*',
                  's_prestamo_producto.nombre as productonombre',
              )
              ->orderBy('id','asc')
              ->get();
          $laboralingreso = DB::table('s_prestamo_creditolaboralingreso')
              ->leftJoin('s_prestamo_conceptoingreso', 's_prestamo_conceptoingreso.id', 's_prestamo_creditolaboralingreso.s_idprestamo_conceptoingreso')
              ->where('s_idprestamo_creditolaboral', $request->idlaboral)
              ->select(
                  's_prestamo_creditolaboralingreso.*',
                  's_prestamo_conceptoingreso.nombre as conceptoingresonombre',
              )
              ->orderBy('id','asc')
              ->get();
          $laboralegresogasto = DB::table('s_prestamo_creditolaboralegresogasto')
              ->leftJoin('s_prestamo_conceptoegresogasto', 's_prestamo_conceptoegresogasto.id', 's_prestamo_creditolaboralegresogasto.s_idprestamo_conceptoegresogasto')
              ->where('s_idprestamo_creditolaboral', $request->idlaboral)
              ->select(
                  's_prestamo_creditolaboralegresogasto.*',
                  's_prestamo_conceptoegresogasto.nombre as conceptoegresogastonombre',
              )
              ->orderBy('id','asc')
              ->get();
          $laboralegresopago = DB::table('s_prestamo_creditolaboralegresopago')
              ->leftJoin('s_prestamo_conceptoegresopago', 's_prestamo_conceptoegresopago.id', 's_prestamo_creditolaboralegresopago.s_idprestamo_conceptoegresopago')
              ->where('s_idprestamo_creditolaboral', $request->idlaboral)
              ->select(
                  's_prestamo_creditolaboralegresopago.*',
                  's_prestamo_conceptoegresopago.nombre as conceptoegresopagonombre',
              )
              ->orderBy('id','asc')
              ->get();
          $laboralservicio = DB::table('s_prestamo_creditolaboralservicio')->where('s_idprestamo_creditolaboral', $request->idlaboral)->limit(1)->first();
          return view('layouts/backoffice/tienda/sistema/prestamosolicitud/laboraldetalle',[
            'tienda' => $tienda,
            'prestamocredito' => $prestamocredito,
            'prestamolaboral' => $prestamolaboral,
            'ubigeo' => $ubigeo,
            'giro' => $giro,
            'productos' => $productos,
            'conceptoingresos' => $conceptoingresos,
            'conceptoegresogastos' => $conceptoegresogastos,
            'conceptoegresopagos' => $conceptoegresopagos,
            'laboralventa' => $laboralventa,
            'laboralcompra' => $laboralcompra,
            'laboralingreso' => $laboralingreso,
            'laboralegresogasto' => $laboralegresogasto,
            'laboralegresopago' => $laboralegresopago,
            'laboralservicio' => $laboralservicio
          ]); 
        }
        elseif ($request->view == 'laboraleliminar') {
            $prestamolaboral = DB::table('s_prestamo_creditolaboral')
            ->leftJoin('s_prestamo_giro', 's_prestamo_giro.id', 's_prestamo_creditolaboral.idprestamo_giro')
            ->leftJoin('s_prestamo_actividad', 's_prestamo_actividad.id', 's_prestamo_creditolaboral.idprestamo_actividad')
            ->join('ubigeo', 'ubigeo.id', 's_prestamo_creditolaboral.idubigeo')
            ->where('s_prestamo_creditolaboral.id', $request->idlaboral)
            ->select(
                's_prestamo_creditolaboral.*',
                's_prestamo_giro.nombre as nombre_giro',
                's_prestamo_actividad.nombre as nombre_actividad',
                'ubigeo.nombre as nombre_ubigeo',
                DB::raw('IF(s_prestamo_creditolaboral.idfuenteingreso = 1,
                    "Dependiente", "Independiente") as fuenteingreso')
            )
            ->first();
          $ubigeo = DB::table('ubigeo')->get();
          $giro = DB::table('s_prestamo_giro')->get();
          $productos = DB::table('s_prestamo_producto')->get();
          $conceptoingresos = DB::table('s_prestamo_conceptoingreso')->get();
          $conceptoegresogastos = DB::table('s_prestamo_conceptoegresogasto')->get();
          $conceptoegresopagos = DB::table('s_prestamo_conceptoegresopago')->get();

          $laboralventa = DB::table('s_prestamo_creditolaboralventa')
              ->leftJoin('s_prestamo_producto', 's_prestamo_producto.id', 's_prestamo_creditolaboralventa.s_idprestamo_producto')
              ->where('s_idprestamo_creditolaboral', $request->idlaboral)
              ->select(
                  's_prestamo_creditolaboralventa.*',
                  's_prestamo_producto.nombre as productonombre',
              )
              ->orderBy('id','asc')
              ->get();
          $laboralcompra = DB::table('s_prestamo_creditolaboralcompra')
              ->leftJoin('s_prestamo_producto', 's_prestamo_producto.id', 's_prestamo_creditolaboralcompra.s_idprestamo_producto')
              ->where('s_idprestamo_creditolaboral', $request->idlaboral)
              ->select(
                  's_prestamo_creditolaboralcompra.*',
                  's_prestamo_producto.nombre as productonombre',
              )
              ->orderBy('id','asc')
              ->get();
          $laboralingreso = DB::table('s_prestamo_creditolaboralingreso')
              ->leftJoin('s_prestamo_conceptoingreso', 's_prestamo_conceptoingreso.id', 's_prestamo_creditolaboralingreso.s_idprestamo_conceptoingreso')
              ->where('s_idprestamo_creditolaboral', $request->idlaboral)
              ->select(
                  's_prestamo_creditolaboralingreso.*',
                  's_prestamo_conceptoingreso.nombre as conceptoingresonombre',
              )
              ->orderBy('id','asc')
              ->get();
          $laboralegresogasto = DB::table('s_prestamo_creditolaboralegresogasto')
              ->leftJoin('s_prestamo_conceptoegresogasto', 's_prestamo_conceptoegresogasto.id', 's_prestamo_creditolaboralegresogasto.s_idprestamo_conceptoegresogasto')
              ->where('s_idprestamo_creditolaboral', $request->idlaboral)
              ->select(
                  's_prestamo_creditolaboralegresogasto.*',
                  's_prestamo_conceptoegresogasto.nombre as conceptoegresogastonombre',
              )
              ->orderBy('id','asc')
              ->get();
          $laboralegresopago = DB::table('s_prestamo_creditolaboralegresopago')
              ->leftJoin('s_prestamo_conceptoegresopago', 's_prestamo_conceptoegresopago.id', 's_prestamo_creditolaboralegresopago.s_idprestamo_conceptoegresopago')
              ->where('s_idprestamo_creditolaboral', $request->idlaboral)
              ->select(
                  's_prestamo_creditolaboralegresopago.*',
                  's_prestamo_conceptoegresopago.nombre as conceptoegresopagonombre',
              )
              ->orderBy('id','asc')
              ->get();
          $laboralservicio = DB::table('s_prestamo_creditolaboralservicio')->where('s_idprestamo_creditolaboral', $request->idlaboral)->limit(1)->first();
          return view('layouts/backoffice/tienda/sistema/prestamosolicitud/laboraleliminar',[
            'tienda' => $tienda,
            'prestamocredito' => $prestamocredito,
            'prestamolaboral' => $prestamolaboral,
            'ubigeo' => $ubigeo,
            'giro' => $giro,
            'productos' => $productos,
            'conceptoingresos' => $conceptoingresos,
            'conceptoegresogastos' => $conceptoegresogastos,
            'conceptoegresopagos' => $conceptoegresopagos,
            'laboralventa' => $laboralventa,
            'laboralcompra' => $laboralcompra,
            'laboralingreso' => $laboralingreso,
            'laboralegresogasto' => $laboralegresogasto,
            'laboralegresopago' => $laboralegresopago,
            'laboralservicio' => $laboralservicio
          ]); 
        }
      
        elseif ($request->view == 'bien') {
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/bien',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'estado' => $request->estado
            ]);  
        } 
        elseif ($request->view == 'biencreate') {
            $tipobien = DB::table('s_prestamo_tipobien')->get();
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/biencreate',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'tipobien' => $tipobien
            ]);  
        }
        elseif ($request->view == 'bienedit') {
            $prestamobien = DB::table('s_prestamo_creditobien')
                ->join('s_prestamo_tipobien', 's_prestamo_tipobien.id', 's_prestamo_creditobien.idprestamo_tipobien')
                ->where('s_prestamo_creditobien.id', $request->idbien)
                ->select(
                    's_prestamo_creditobien.*',
                    's_prestamo_tipobien.nombre as nombre_tipobien'
                )
                ->first();
            $tipobien = DB::table('s_prestamo_tipobien')->get();
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/bienedit',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'prestamobien' => $prestamobien,
                'tipobien' => $tipobien
            ]);  
        }
        elseif ($request->view == 'bienimagen') {
            $prestamobien = DB::table('s_prestamo_creditobien')
                ->join('s_prestamo_tipobien', 's_prestamo_tipobien.id', 's_prestamo_creditobien.idprestamo_tipobien')
                ->where('s_prestamo_creditobien.id', $request->idbien)
                ->select(
                    's_prestamo_creditobien.*',
                    's_prestamo_tipobien.nombre as nombre_tipobien'
                )
                ->first();
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/bienimagen',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'prestamobien' => $prestamobien,
                'estado' => $request->estado,
            ]);  
        }
        elseif ($request->view == 'biendetalle') {
            $prestamobien = DB::table('s_prestamo_creditobien')
                ->join('s_prestamo_tipobien', 's_prestamo_tipobien.id', 's_prestamo_creditobien.idprestamo_tipobien')
                ->where('s_prestamo_creditobien.id', $request->idbien)
                ->select(
                    's_prestamo_creditobien.*',
                    's_prestamo_tipobien.nombre as nombre_tipobien'
                )
                ->first();
            $tipobien = DB::table('s_prestamo_tipobien')->get();
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/biendetalle',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'prestamobien' => $prestamobien,
                'tipobien' => $tipobien
            ]);  
        }
        elseif ($request->view == 'bieneliminar') {
            $prestamobien = DB::table('s_prestamo_creditobien')
                ->join('s_prestamo_tipobien', 's_prestamo_tipobien.id', 's_prestamo_creditobien.idprestamo_tipobien')
                ->where('s_prestamo_creditobien.id', $request->idbien)
                ->select(
                    's_prestamo_creditobien.*',
                    's_prestamo_tipobien.nombre as nombre_tipobien'
                )
                ->first();
            $tipobien = DB::table('s_prestamo_tipobien')->get();
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/bieneliminar',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'prestamobien' => $prestamobien,
                'tipobien' => $tipobien
            ]);  
        }
      
        elseif ($request->view == 'cualitativo') {
          $preguntas = DB::table('s_prestamo_creditocualitativopreguntas')->get();
          $cualitativo = DB::table('s_prestamo_creditocualitativo')
            ->where('idprestamo_credito', $prestamocredito->id)
            ->first();
          $cualitativodetalles = DB::table('s_prestamo_creditocualitativodetalle')
            ->join('s_prestamo_creditocualitativopreguntas', 's_prestamo_creditocualitativopreguntas.id', 's_prestamo_creditocualitativodetalle.idprestamo_cualitativopregunta')
            ->where('s_prestamo_creditocualitativodetalle.idprestamo_cualitativo', ($cualitativo->id ?? 0))
            ->select(
              's_prestamo_creditocualitativodetalle.*',
              's_prestamo_creditocualitativopreguntas.nombre as nombre',
              's_prestamo_creditocualitativopreguntas.descripcion1 as descripcion1',
              's_prestamo_creditocualitativopreguntas.descripcion2 as descripcion2',
              's_prestamo_creditocualitativopreguntas.descripcion3 as descripcion3'
            )
            ->get();
          $calificaciones = DB::table('s_prestamo_calificacion')->get();
          
          $relaciones = DB::table('s_prestamo_creditorelacion')
                ->join('users as persona', 'persona.id', 's_prestamo_creditorelacion.idpersona')
                ->join('s_prestamo_tiporelacion', 's_prestamo_tiporelacion.id', 's_prestamo_creditorelacion.idprestamo_tiporelacion')
                ->where([
                    ['s_prestamo_creditorelacion.idprestamo_credito', $prestamocredito->id],
                    ['s_prestamo_creditorelacion.idtienda', $tienda->id],
                ])
                ->select(
                    's_prestamo_creditorelacion.*',
                    's_prestamo_tiporelacion.nombre as nombre_tiporelacion',
                    DB::raw('IF(persona.idtipopersona = 1 || persona.idtipopersona = 3,
                        CONCAT(persona.identificacion, " - ", persona.apellidos, ", ", persona.nombre),
                        CONCAT(persona.identificacion, " - ", persona.apellidos)) as completo_persona')
                )
                ->orderBy('s_prestamo_creditorelacion.id','desc')
                ->get();
          
          $avales = DB::table('s_prestamo_creditoaval')
                ->join('users as persona', 'persona.id', 's_prestamo_creditoaval.idpersona')
                ->where([
                    ['s_prestamo_creditoaval.idprestamo_credito', $prestamocredito->id],
                    ['s_prestamo_creditoaval.idtienda', $tienda->id],
                ])
                ->select(
                    's_prestamo_creditoaval.*',
                    DB::raw('IF(persona.idtipopersona = 1 || persona.idtipopersona = 3,
                        CONCAT(persona.identificacion, " - ", persona.apellidos, ", ", persona.nombre),
                        CONCAT(persona.identificacion, " - ", persona.apellidos)) as completo_persona')
                )
                ->orderBy('s_prestamo_creditoaval.id','desc')
                ->get();
          
          $tiporelaciones = DB::table('s_prestamo_tiporelacion')->get();
          
          return view('layouts/backoffice/tienda/sistema/prestamosolicitud/cualitativo', [
                'tienda' => $tienda,
                'preguntas' => $preguntas,
                'prestamocredito' => $prestamocredito,
                'cualitativo' => $cualitativo,
                'cualitativodetalles' => $cualitativodetalles,
                'calificaciones' => $calificaciones,
                'relaciones' => $relaciones,
                'tiporelaciones' => $tiporelaciones,
                'avales' => $avales,
                'estado' => $request->estado
          ]);  
        }
      
        elseif ($request->view == 'resultado') {
          
            $total_laboralventa = DB::table('s_prestamo_creditolaboralventa')
                ->join('s_prestamo_creditolaboral', 's_prestamo_creditolaboral.id', 's_prestamo_creditolaboralventa.s_idprestamo_creditolaboral')
                ->where('s_prestamo_creditolaboral.idprestamo_credito', $prestamocredito->id)
                ->sum('preciototal_mensual');
            $total_laboralcompra = DB::table('s_prestamo_creditolaboralcompra')
                ->join('s_prestamo_creditolaboral', 's_prestamo_creditolaboral.id', 's_prestamo_creditolaboralcompra.s_idprestamo_creditolaboral')
                ->where('s_prestamo_creditolaboral.idprestamo_credito', $prestamocredito->id)
                ->sum('preciototal_mensual');
            $total_laboralingreso = DB::table('s_prestamo_creditolaboralingreso')
                ->join('s_prestamo_creditolaboral', 's_prestamo_creditolaboral.id', 's_prestamo_creditolaboralingreso.s_idprestamo_creditolaboral')
                ->where('s_prestamo_creditolaboral.idprestamo_credito', $prestamocredito->id)
                ->sum('monto');
            $total_laboralegresogasto = DB::table('s_prestamo_creditolaboralegresogasto')
                ->join('s_prestamo_creditolaboral', 's_prestamo_creditolaboral.id', 's_prestamo_creditolaboralegresogasto.s_idprestamo_creditolaboral')
                ->where('s_prestamo_creditolaboral.idprestamo_credito', $prestamocredito->id)
                ->sum('monto');
            $total_laboralegresopago = DB::table('s_prestamo_creditolaboralegresopago')
                ->join('s_prestamo_creditolaboral', 's_prestamo_creditolaboral.id', 's_prestamo_creditolaboralegresopago.s_idprestamo_creditolaboral')
                ->where('s_prestamo_creditolaboral.idprestamo_credito', $prestamocredito->id)
                ->sum('monto');
            $total_laboralservicio = DB::table('s_prestamo_creditolaboralservicio')
                ->join('s_prestamo_creditolaboral', 's_prestamo_creditolaboral.id', 's_prestamo_creditolaboralservicio.s_idprestamo_creditolaboral')
                ->where('s_prestamo_creditolaboral.idprestamo_credito', $prestamocredito->id)
                ->sum('mensual');
          
            $ingreso = $total_laboralventa+$total_laboralingreso+$total_laboralservicio-$total_laboralcompra-$total_laboralegresogasto-$total_laboralegresopago;
          
            return view('layouts/backoffice/tienda/sistema/prestamosolicitud/resultado',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'total_laboralventa' => number_format($total_laboralventa, 2, '.', ''),
                'total_laboralcompra' => number_format($total_laboralcompra, 2, '.', ''),
                'total_laboralingreso' => number_format($total_laboralingreso, 2, '.', ''),
                'total_laboralegresogasto' => number_format($total_laboralegresogasto, 2, '.', ''),
                'total_laboralegresopago' => number_format($total_laboralegresopago, 2, '.', ''),
                'total_laboralservicio' => number_format($total_laboralservicio, 2, '.', ''),
                'ingreso' => number_format($ingreso, 2, '.', '')
            ]);  
        } 
      
        elseif ($request->view == 'creditopdf') {
          return view('layouts/backoffice/tienda/sistema/prestamosolicitud/creditopdf', compact(
            'tienda',
            'prestamocredito'
          ));
        }
        elseif ($request->view == 'creditopdf-pdf') {
          
          $productos = DB::table('s_prestamo_creditolaboral')
                ->leftJoin('s_prestamo_actividad', 's_prestamo_actividad.id', 's_prestamo_creditolaboral.idprestamo_actividad')
                ->leftJoin('s_prestamo_giro', 's_prestamo_giro.id', 's_prestamo_actividad.idprestamo_giro')
                ->where([
                    ['s_prestamo_creditolaboral.idprestamo_credito', $prestamocredito->id],
                    ['s_prestamo_creditolaboral.idtienda', $tienda->id],
                    ['s_prestamo_creditolaboral.idestado', 1]
                ])
                ->select(
                    's_prestamo_giro.nombre as nombre_giro'
                )
                ->orderBy('s_prestamo_creditolaboral.id','desc')
                ->get();
          
          $cantidadrecurrente = DB::table('s_prestamo_credito')
                ->where('s_prestamo_credito.idcliente', $prestamocredito->idcliente)
                ->where('s_prestamo_credito.idestado', 4)
                ->count();
          
          $prestamocualitativo = DB::table('s_prestamo_creditocualitativo')
                ->join('s_prestamo_calificacion', 's_prestamo_calificacion.id', 's_prestamo_creditocualitativo.idprestamo_calificacion')
                ->where('s_prestamo_creditocualitativo.idprestamo_credito', $prestamocredito->id)
                ->select(
                  's_prestamo_creditocualitativo.*',
                  's_prestamo_calificacion.nombre as calificacion',
                )
                ->limit(1)
                ->first();
          
          $avales = DB::table('s_prestamo_creditoaval')
                ->join('users as aval', 'aval.id', 's_prestamo_creditoaval.idpersona')
                ->where([
                    ['s_prestamo_creditoaval.idprestamo_credito', $prestamocredito->id],
                    ['s_prestamo_creditoaval.idtienda', $tienda->id],
                ])
                ->select(
                    's_prestamo_creditoaval.*',
                    'aval.identificacion as avalidentificacion',
                    'aval.nombre as avalnombre',
                    'aval.apellidos as avalapellidos'
                )
                ->orderBy('s_prestamo_creditoaval.id','desc')
                ->get();
          
          $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamosolicitud/creditopdf-pdf',[
                'tienda' => $tienda,
                'prestamocredito' => $prestamocredito,
                'cantidadrecurrente' => $cantidadrecurrente,
                'prestamocualitativo' => $prestamocualitativo,
                'avales' => $avales,
                'productos' => $productos
          ]);  
          return $pdf->stream('Ticket.pdf');
        }
      
        elseif ($request->view == 'cualitativopdf') {
          $cualitativo = DB::table('s_prestamo_creditocualitativo')
            ->where('idprestamo_credito', $prestamocredito->id)
            ->first();
          return view('layouts/backoffice/tienda/sistema/prestamosolicitud/cualitativopdf', compact(
            'tienda',
            'prestamocredito',
            'cualitativo'
          ));
        }
        elseif ($request->view == 'cualitativopdf-pdf') {
          $preguntas = DB::table('s_prestamo_creditocualitativopreguntas')->get();
          $cualitativo = DB::table('s_prestamo_creditocualitativo')
            ->where('idprestamo_credito', $prestamocredito->id)
            ->first();
         
          $cualitativodetalles = DB::table('s_prestamo_creditocualitativodetalle')
            ->join('s_prestamo_creditocualitativopreguntas', 's_prestamo_creditocualitativopreguntas.id', 's_prestamo_creditocualitativodetalle.idprestamo_cualitativopregunta')
            ->where('s_prestamo_creditocualitativodetalle.idprestamo_cualitativo', ($cualitativo->id ?? 0))
            ->select(
              's_prestamo_creditocualitativodetalle.*',
              's_prestamo_creditocualitativopreguntas.nombre as nombre',
              's_prestamo_creditocualitativopreguntas.descripcion1 as descripcion1',
              's_prestamo_creditocualitativopreguntas.descripcion2 as descripcion2',
              's_prestamo_creditocualitativopreguntas.descripcion3 as descripcion3'
            )
            ->get();
          $calificacion = DB::table('s_prestamo_calificacion')->whereId($cualitativo->idprestamo_calificacion)->first();
          
          // Referencias
          $relaciones = DB::table('s_prestamo_creditorelacion')
            ->join('users as persona', 'persona.id', 's_prestamo_creditorelacion.idpersona')
            ->join('s_prestamo_tiporelacion', 's_prestamo_tiporelacion.id', 's_prestamo_creditorelacion.idprestamo_tiporelacion')
            ->where([
                ['s_prestamo_creditorelacion.idprestamo_credito', $prestamocredito->id],
                ['s_prestamo_creditorelacion.idtienda', $tienda->id],
            ])
            ->select(
                's_prestamo_creditorelacion.*',
                's_prestamo_tiporelacion.nombre as nombre_tiporelacion',
                'persona.identificacion as identificacion_persona',
                DB::raw('IF(persona.idtipopersona = 1 || persona.idtipopersona = 3,
                    CONCAT(persona.apellidos, ", ", persona.nombre),
                    CONCAT(persona.apellidos)) as completo_persona')
            )
            ->orderBy('s_prestamo_creditorelacion.id','desc')
            ->get();
          
          $agencia = DB::table('s_agencia')->where('idtienda', $tienda->id)->first();
          
          $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamosolicitud/cualitativopdf-pdf',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'prestamocredito' => $prestamocredito,
                'cualitativodetalles' => $cualitativodetalles,
                'preguntas' => $preguntas,
                'cualitativo' => $cualitativo,
                'calificacion' => $calificacion,
                'relaciones' => $relaciones,
          ]);  
          return $pdf->stream('Analisis Cualitativo.pdf');
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
        /*
         idestadocredito
         * 1 = credito pendiente
         * 2 = credito pre aprobado
         * 3 = aprobado
         * 4 = desembolsado
         * 5 = cancelado
         idestado
         * 1 = correcto
         * 2 = anulado
         idmoneda
         * 1 = soles
         * 2 = dolares
        */

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
          
            $rules = array_merge($rules,[
                'monto' => 'required',
                'numerocuota' => 'required',
                'fechainicio' => 'required',
                'idfrecuencia' => 'required',
                'idtasa' => 'required',
                'tasa' => 'required',
            ]);
          
            $messages = [
                'idcliente.required' => 'El "Cliente" es Obligatorio.',
                'idconyuge.required' => 'El "Cónyuge" es Obligatorio.',
                'idgarante.required' => 'El "Garante" es Obligatorio.',
                'monto.required' => 'El "Monto" es Obligatorio.',
                'numerocuota.required' => 'El "Nro. de Cuota" es Obligatorio.',
                'fechainicio.required' => 'La "Fecha de Inicio" es Obligatorio.',
                'idfrecuencia.required' => 'La "Frecuencia" es Obligatorio.',
                'idtasa.required' => 'La "Tasa" es Obligatorio.',
                'tasa.required' => 'El "Interes" es Obligatorio.',
            ];
                    
            $this->validate($request, $rules, $messages);
            
            $cronograma = prestamo_cronograma(
                $idtienda,
                $request->monto,
                $request->numerocuota,
                $request->fechainicio,
                $request->idfrecuencia,
                $request->numerodias,
                $request->idtasa,
                $request->tasa,
                $request->gastoadministrativo!=null?$request->gastoadministrativo:0,
                $request->excluirferiado,
                $request->excluirsabado,
                $request->excluirdomingo
            );

            DB::table('s_prestamo_credito')->whereId($id)->update([
                'monto' => $request->input('monto'),
                'numerocuota' => $request->input('numerocuota'),
                'fechainicio' => $request->input('fechainicio'),
                'numerodias' => $request->input('numerodias') ?? 0,
                'tasa' => $request->input('tasa'),
                'cuota' => $cronograma['cuota'],
                'excluirsabado' => $request->input('excluirsabado') ?? '',
                'excluirdomingo' => $request->input('excluirdomingo') ?? '',
                'excluirferiado' => $request->input('excluirferiado') ?? '',
                'total_amortizacion' => $cronograma['total_amortizacion'],
                'total_interes' => $cronograma['total_interes'],
                'total_cuota' => $cronograma['total_cuota'],
                'total_segurodesgravamen' => $cronograma['total_segurodesgravamen'],
                'total_cuotafinal' => $cronograma['total_cuotafinal'],
                'idasesor' => Auth::user()->id,
                'idcliente' => $request->input('idcliente'),
                'idconyuge' => $request->input('idconyuge')!='' ? $request->input('idconyuge') : 0,
                'idprestamo_frecuencia' => $request->input('idfrecuencia'),
                'idprestamo_tipotasa' => $request->input('idtasa'),
            ]);
            /*
             idestadocobranza
             * 1 = cuota pendiente
             * 2 = cuota cancelado
            */
            DB::table('s_prestamo_creditodetalle')->where('idprestamo_credito',$id)->delete();
            foreach($cronograma['cronograma'] as $value) {
              DB::table('s_prestamo_creditodetalle')->insert([
                'numero' => $value['numero'],
                'fechavencimiento' => $value['fechanormal'],
                'saldocapital' => $value['saldo'],
                'amortizacion' => $value['amortizacion'],
                'interes' => $value['interes'],
                'cuota' => $value['cuota'],
                'seguro' => $value['segurodesgravamen'],
                'total' => $value['cuotafinal'],
                'atraso' => 0,
                'moradescuento' => 0,
                'moraapagar' => 0,
                'cuotapago' => 0,
                'acuenta' => 0,
                'cuotaapagar' => 0,
                'idprestamo_credito' => $id,
                'idestadocobranza' => 1,
                'idestado' => 1
              ]);
            }
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'preaprobar') {
            $credito = DB::table('s_prestamo_credito')->whereId($id)->first();
            $cronograma = prestamo_cronograma(
              $idtienda,
              $credito->monto,
              $credito->numerocuota,
              $credito->fechainicio,
              $credito->idprestamo_frecuencia,
              $credito->numerodias,
              $credito->idprestamo_tipotasa,
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

            DB::table('s_prestamo_credito')->whereId($id)->update([
              'fechapreaprobado' => Carbon::now(),
              'idestadocredito' => 2
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha PreAprobado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'anular') {
            DB::table('s_prestamo_credito')->whereId($id)->update([
                'fechaanulado' => Carbon::now(),
                'idestado' => 2
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha Anulado correctamente.'
            ]);
        }
      
      
        elseif ($request->input('view') == 'imagenbien') {
            $imagen = uploadfile('', '', $request->file('imagen-bien'), '/public/backoffice/tienda/'.$idtienda.'/creditobien/');
            $countprestamogaleria = DB::table('s_prestamo_creditobienimagen')->where('s_prestamo_creditobienimagen.idprestamo_creditobien', $request->idprestamo_creditobien)->count();

            $orden = $countprestamogaleria+1;
        
            DB::table('s_prestamo_creditobienimagen')->insert([
              'fecharegistro'   => Carbon::now(),
              'orden'           => $orden,
              'imagen'          => $imagen,
              'idtienda'        => $idtienda,
              'idprestamo_creditobien' => $request->idprestamo_creditobien
            ]);
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'imagendomicilio') {
            $imagen = uploadfile('', '', $request->file('imagen-domicilio'), '/public/backoffice/tienda/'.$idtienda.'/creditodomicilio/');
            $countprestamogaleria = DB::table('s_prestamo_creditodomicilioimagen')->where('s_prestamo_creditodomicilioimagen.idprestamo_creditodomicilio', $request->idprestamo_creditodomicilio)->count();

            $orden = $countprestamogaleria+1;
       
            DB::table('s_prestamo_creditodomicilioimagen')->insert([
              'fecharegistro'   => Carbon::now(),
              'orden'           => $orden,
              'imagen'          => $imagen,
              'idtienda'        => $idtienda,
              'idprestamo_creditodomicilio' => $request->idprestamo_creditodomicilio,
            ]);
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'editar-domicilio') {
            $rules = [
                'domicilio_editar_direccion' => 'required',
                'domicilio_editar_idubigeo' => 'required',
                'domicilio_editar_referencia' => 'required',
                'domicilio_editar_reside_desdemes' => 'required',
                'domicilio_editar_reside_desdeanio' => 'required',
                'domicilio_editar_horaubicacion_de' => 'required',
                'domicilio_editar_horaubicacion_hasta' => 'required',
                'domicilio_editar_idtipopropiedad' => 'required',
                'domicilio_editar_mapa_latitud' => 'required',
                'domicilio_editar_mapa_longitud' => 'required',
            ];
            $messages = [
                'domicilio_editar_direccion.required' => 'La "Dirección" es Obligatorio.',
                'domicilio_editar_idubigeo.required' => 'El "Ubigeo" es Obligatorio.',
                'domicilio_editar_referencia.required' => 'La "Referencia" es Obligatorio.',
                'domicilio_editar_reside_desdemes.required' => 'El "Mes de residencia" es Obligatorio.',
                'domicilio_editar_reside_desdeanio.required' => 'El "Año de residencia" es Obligatorio.',
                'domicilio_editar_horaubicacion_de.required' => 'La "Hora" es Obligatorio.',
                'domicilio_editar_horaubicacion_hasta.required' => 'La "Hora" es Obligatorio.',
                'domicilio_editar_idtipopropiedad.required' => 'El "Tipo de Propiedad" es Obligatorio.',
                'domicilio_editar_mapa_latitud.required' => 'La "Ubicación" es Obligatorio.',
                'domicilio_editar_mapa_longitud.required' => 'La "Ubicación" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);
          
            DB::table('s_prestamo_creditodomicilio')->whereId($request->idprestamo_creditodomicilio)->update([
                'fechamodificacion' => Carbon::now(),
                'direccion' => $request->domicilio_editar_direccion,
                'referencia' => $request->domicilio_editar_referencia ?? '',
                'reside_desdemes' => $request->domicilio_editar_reside_desdemes,
                'reside_desdeanio' => $request->domicilio_editar_reside_desdeanio,
                'horaubicacion_de' => $request->domicilio_editar_horaubicacion_de,
                'horaubicacion_hasta' => $request->domicilio_editar_horaubicacion_hasta,
                'mapa_latitud' => $request->domicilio_editar_mapa_latitud,
                'mapa_longitud' => $request->domicilio_editar_mapa_longitud,
                'referencia' => $request->domicilio_editar_referencia,
                'idubigeo' => $request->domicilio_editar_idubigeo,
                'idtipopropiedad' => $request->domicilio_editar_idtipopropiedad,
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'eliminar-domicilio') {
            DB::table('s_prestamo_creditodomicilio')->whereId($request->idprestamo_creditodomicilio)->update([
                'fechaanulacion' => Carbon::now(),
                'idestado' => 2,
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'editar-laboral') {
            $rules = [
                'laboral_editar_idfuenteingreso' => 'required',
                'laboral_editar_idprestamo_giro' => 'required',
                'laboral_editar_idprestamo_actividad' => 'required',
                'laboral_editar_labora_desdemes' => 'required',
                'laboral_editar_labora_desdeanio' => 'required',
                'laboral_editar_direccion' => 'required',
                'laboral_editar_idubigeo' => 'required',
            ];
            $messages = [
                'laboral_editar_idfuenteingreso.required' => 'La "Fuente de Ingreso" es Obligatorio',
                'laboral_editar_idprestamo_giro.required' => 'El "Giro" es Obligatorio',
                'laboral_editar_idprestamo_actividad.required' => 'La "Actividad" es Obligatorio',
                'laboral_editar_labora_desdemes.required' => 'La "Fecha de Labor" es Obligatorio',
                'laboral_editar_labora_desdeanio.required' => 'La "Fecha de Labor" es Obligatorio',
                'laboral_editar_direccion.required' => 'La "Dirección" es Obligatorio',
                'laboral_editar_idubigeo.required' => 'El "Ubigeo" es Obligatorio',
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
          

            DB::table('s_prestamo_creditolaboral')->whereId($request->idprestamo_creditolaboral)->update([
                'fechamodificacion' => Carbon::now(),
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
                'mapa_latitud' => $request->laboral_editar_mapa_latitud,
                'mapa_longitud' => $request->laboral_editar_mapa_longitud,
                'referencia' => $request->laboral_editar_referencia!=''?$request->laboral_editar_referencia:'',
                'idubigeo' => $request->laboral_editar_idubigeo,
                'idprestamo_giro' => $request->laboral_editar_idprestamo_giro,
                'idprestamo_actividad' => $request->laboral_editar_idprestamo_actividad,
                'idfuenteingreso' => $request->laboral_editar_idfuenteingreso,
            ]);

            // Ventas
            DB::table('s_prestamo_creditolaboralventa')->where('s_idprestamo_creditolaboral', $request->idprestamo_creditolaboral)->delete();
            $producto_venta = explode('/&/', $request->ventas);
            /**
             * [0] idproducto
             * [1] cantidad
             * [2] preciounitario
             * [3] total
             */
            for($i = 1; $i < count($producto_venta); $i++){
                $item = explode('/,/',$producto_venta[$i]);
                DB::table('s_prestamo_creditolaboralventa')->insert([
                  'cantidad' => $item[1],
                  'preciounitario' => $item[2],
                  'preciototal' => $item[3],
                  'preciototal_semanal' => $item[4],
                  'preciototal_quincenal' => $item[5],
                  'preciototal_mensual' => $item[6],
                  's_idprestamo_producto' => $item[0],
                  's_idprestamo_creditolaboral' => $request->idprestamo_creditolaboral
                ]);
            }
          
            // Compras
            DB::table('s_prestamo_creditolaboralcompra')->where('s_idprestamo_creditolaboral', $request->idprestamo_creditolaboral)->delete();
            $producto_compra = explode('/&/', $request->compras);
            /**
             * [0] idproducto
             * [1] cantidad
             * [2] preciounitario
             * [3] total
             */
            for($i = 1; $i < count($producto_compra); $i++){
                $item = explode('/,/',$producto_compra[$i]);
                DB::table('s_prestamo_creditolaboralcompra')->insert([
                  'cantidad' => $item[1],
                  'preciounitario' => $item[2],
                  'preciototal' => $item[3],
                  'preciototal_semanal' => $item[4],
                  'preciototal_quincenal' => $item[5],
                  'preciototal_mensual' => $item[6],
                  's_idprestamo_producto' => $item[0],
                  's_idprestamo_creditolaboral' => $request->idprestamo_creditolaboral
                ]);
            } 
          
            // Ingreso
            DB::table('s_prestamo_creditolaboralingreso')->where('s_idprestamo_creditolaboral', $request->idprestamo_creditolaboral)->delete();
            $producto_ingreso = explode('/&/', $request->ingresos);
            /**
             * [0] idconcepto
             * [1] monto
             */
            for($i = 1; $i < count($producto_ingreso); $i++){
                $item = explode('/,/',$producto_ingreso[$i]);
                DB::table('s_prestamo_creditolaboralingreso')->insert([
                  'monto' => $item[1],
                  's_idprestamo_conceptoingreso' => $item[0],
                  's_idprestamo_creditolaboral' => $request->idprestamo_creditolaboral
                ]);
            } 
            
            // Egreso gasto
            DB::table('s_prestamo_creditolaboralegresogasto')->where('s_idprestamo_creditolaboral', $request->idprestamo_creditolaboral)->delete();
            $producto_egresogasto = explode('/&/', $request->egresogastos);
            /**
             * [0] idconcepto
             * [1] monto
             */
            for($i = 1; $i < count($producto_egresogasto); $i++){
                $item = explode('/,/',$producto_egresogasto[$i]);
                DB::table('s_prestamo_creditolaboralegresogasto')->insert([
                  'monto' => $item[1],
                  's_idprestamo_conceptoegresogasto' => $item[0],
                  's_idprestamo_creditolaboral' => $request->idprestamo_creditolaboral
                ]);
            }
          
            // Egreso pago
            DB::table('s_prestamo_creditolaboralegresopago')->where('s_idprestamo_creditolaboral', $request->idprestamo_creditolaboral)->delete();
            $producto_egresopago = explode('/&/', $request->egresopagos);
            /**
             * [0] idconcepto
             * [1] monto
             */
            for($i = 1; $i < count($producto_egresopago); $i++){
                $item = explode('/,/',$producto_egresopago[$i]);
                DB::table('s_prestamo_creditolaboralegresopago')->insert([
                  'monto' => $item[1],
                  's_idprestamo_conceptoegresopago' => $item[0],
                  's_idprestamo_creditolaboral' => $request->idprestamo_creditolaboral
                ]);
            }
          
            // Servicios
            DB::table('s_prestamo_creditolaboralservicio')->where('s_idprestamo_creditolaboral', $request->idprestamo_creditolaboral)->delete();
            if($request->servicios!=''){
                $item = explode('/,/',$request->servicios);
                DB::table('s_prestamo_creditolaboralservicio')->insert([
                  'bueno' => $item[1],
                  'regular' => $item[2],
                  'malo' => $item[3],
                  'promedio' => $item[4],
                  'semanal' => $item[5],
                  'quincenal' => $item[6],
                  'mensual' => $item[7],
                  's_idprestamo_creditolaboral' => $request->idprestamo_creditolaboral
                ]);
            }
          
            // actualizar resultado

            $total_laboralventa = DB::table('s_prestamo_creditolaboralventa')
                ->where('s_prestamo_creditolaboralventa.s_idprestamo_creditolaboral', $request->idprestamo_creditolaboral)
                ->sum('preciototal_mensual');
            $total_laboralcompra = DB::table('s_prestamo_creditolaboralcompra')
                ->where('s_prestamo_creditolaboralcompra.s_idprestamo_creditolaboral', $request->idprestamo_creditolaboral)
                ->sum('preciototal_mensual');
            $total_laboralingreso = DB::table('s_prestamo_creditolaboralingreso')
                ->where('s_prestamo_creditolaboralingreso.s_idprestamo_creditolaboral', $request->idprestamo_creditolaboral)
                ->sum('monto');
            $total_laboralegresogasto = DB::table('s_prestamo_creditolaboralegresogasto')
                ->where('s_prestamo_creditolaboralegresogasto.s_idprestamo_creditolaboral', $request->idprestamo_creditolaboral)
                ->sum('monto');
            $total_laboralegresopago = DB::table('s_prestamo_creditolaboralegresopago')
                ->where('s_prestamo_creditolaboralegresopago.s_idprestamo_creditolaboral', $request->idprestamo_creditolaboral)
                ->sum('monto');
            $total_laboralservicio = DB::table('s_prestamo_creditolaboralservicio')
                ->where('s_prestamo_creditolaboralservicio.s_idprestamo_creditolaboral', $request->idprestamo_creditolaboral)
                ->sum('mensual');
          
            $ingreso = $total_laboralventa+$total_laboralingreso+$total_laboralservicio-$total_laboralcompra-$total_laboralegresogasto-$total_laboralegresopago;
          
            DB::table('s_prestamo_creditolaboral')->whereId($request->idprestamo_creditolaboral)->update([
                'venta' => $total_laboralventa,
                'compra' => $total_laboralcompra,
                'ingreso' => $total_laboralingreso,
                'egresogasto' => $total_laboralegresogasto,
                'egresopago' => $total_laboralegresopago,
                'servicio' => $total_laboralservicio,
                'ingresomensual' => $ingreso,
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'eliminar-laboral') {
            DB::table('s_prestamo_creditolaboral')->whereId($request->idprestamo_creditolaboral)->update([
                'fechaanulacion' => Carbon::now(),
                'idestado' => 2,
            ]);
        }
        elseif ($request->input('view') == 'editar-bien') {
            $rules = [
                'bien_editar_idprestamo_tipobien' => 'required',
                'bien_editar_valorestimado' => 'required',
                'bien_editar_descripcion' => 'required',
            ];
            $messages = [
                'bien_editar_idprestamo_tipobien.required' => 'El "Tipo de Bien" es Obligatorio.',
                'bien_editar_valorestimado.required' => 'El "Valor Estimado" es Obligatorio.',
                'bien_editar_descripcion.required' => 'La "Descripción" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);
          
            DB::table('s_prestamo_creditobien')->whereId($request->idprestamo_creditobien)->update([
                'fechamodificacion' => Carbon::now(),
                'descripcion' => $request->bien_editar_descripcion,
                'valorestimado' => $request->bien_editar_valorestimado,
                'idprestamo_tipobien' => $request->bien_editar_idprestamo_tipobien,
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'eliminar-bien') {
            DB::table('s_prestamo_creditobien')->whereId($request->idprestamo_creditobien)->update([
                'fechaanulacion' => Carbon::now(),
                'idestado' => 2,
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
