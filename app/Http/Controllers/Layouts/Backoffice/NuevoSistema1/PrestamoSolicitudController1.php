<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
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
          'excluirsabado' => $request->input('excluirsabado') ?? '',
          'excluirdomingo' => $request->input('excluirdomingo') ?? '',
          'excluirferiado' => $request->input('excluirferiado') ?? '',
          'total_amortizacion' => $cronograma['total_amortizacion'],
          'total_interes' => $cronograma['total_interes'],
          'total_cuota' => $cronograma['total_cuota'],
          'total_segurodesgravamen' => $cronograma['total_segurodesgravamen'],
          'total_cuotafinal' => $cronograma['total_cuotafinal'],
          'idmoneda' => 1,
          'idagencia' => 0,
          'idtipocomprobante' => 0,
          'idasesor' => Auth::user()->id,
          'idsupervisor' => 0,
          'idcajero' => 0,
          'idcliente' => $request->input('idcliente'),
          'idconyuge' => $request->input('idconyuge')!='' ? $request->input('idconyuge') : 0,
          'idgarante' => $request->input('idgarante')!='' ? $request->input('idgarante') : 0,
          'idprestamo_frecuencia' => $request->input('idfrecuencia'),
          'idprestamo_tipotasa' => $request->idtasa,
          'idaperturacierre' => 0,
          'idtienda' => $idtienda,
          'idestadocredito' => 1,
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
        DB::table('s_prestamo_domicilio')->insert([
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
            'idusuario' => $request->idusuario,
            'idtienda' => $idtienda,
            'idestado' => 1
        ]);
        return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha registrado correctamente.'
        ]);
      }
      elseif ($request->view == 'agregar-domicilio') {
        DB::table('s_prestamo_creditodomicilio')->insert([
          'idprestamo_domicilio' => $request->iddomicilio,
          'idprestamo_credito' => $request->idcredito
        ]);
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha agregado correctamente.'
        ]);
      }

      elseif ($request->input('view') == 'registrar-laboral') {
        $rules = [
            'laboral_idfuenteingreso' => 'required',
            'laboral_idprestamo_giro' => 'required',
            'laboral_idprestamo_actividad' => 'required',
            'laboral_labora_desdemes' => 'required',
            'laboral_labora_desdeanio' => 'required',
            'laboral_ingresomensual' => 'required',
            'laboral_idubigeo' => 'required',
            'laboral_direccion' => 'required',
        ];
        $messages = [
            'laboral_idfuenteingreso.required' => 'La "Fuente de Ingreso" es Obligatorio',
            'laboral_idprestamo_giro.required' => 'El "Giro" es Obligatorio',
            'laboral_idprestamo_actividad.required' => 'La "Actividad" es Obligatorio',
            'laboral_labora_desdemes.required' => 'La "Fecha de Labor" es Obligatorio',
            'laboral_labora_desdeanio.required' => 'La "Fecha de Labor" es Obligatorio',
            'laboral_ingresomensual.required' => 'El "Ingreso Mensual" es Obligatorio',
            'laboral_idubigeo.required' => 'El "Ubigeo" es Obligatorio',
            'laboral_direccion.required' => 'La "Dirección" es Obligatorio',
        ];
        $this->validate($request, $rules, $messages);

        /* idfuenteingreso
         * 1 = Dependiente
         * 2 = Independiente
         *
         * idestado
         * 1 = activo
         * 2 = anulado
         */
        DB::table('s_prestamo_labor')->insert([
            'fecharegistro' => Carbon::now(),
            'direccion' => $request->laboral_direccion,
            'ingresomensual' => $request->laboral_ingresomensual,
            'labora_desdemes' => $request->laboral_labora_desdemes,
            'labora_desdeanio' => $request->laboral_labora_desdeanio,
            'mapa_latitud' => $request->laboral_mapa_latitud,
            'mapa_longitud' => $request->laboral_mapa_longitud,
            'referencia' => $request->laboral_referencia!=''?$request->laboral_referencia:'',
            'idubigeo' => $request->laboral_idubigeo,
            'idprestamo_giro' => $request->laboral_idprestamo_giro,
            'idprestamo_actividad' => $request->laboral_idprestamo_actividad,
            'idfuenteingreso' => $request->laboral_idfuenteingreso,
            'idusuario' => $request->idusuario,
            'idtienda' => $idtienda,
            'idestado' => 1
        ]);
        return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha registrado correctamente.'
        ]);
      }
      elseif ($request->view == 'agregar-labor') {
        DB::table('s_prestamo_creditolabor')->insert([
          'idprestamo_labor' => $request->idlabor,
          'idprestamo_credito' => $request->idcredito
        ]);
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha agregado correctamente.'
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
        DB::table('s_prestamo_bien')->insert([
            'fecharegistro' => Carbon::now(),
            'descripcion' => $request->bien_descripcion,
            'valorestimado' => $request->bien_valorestimado,
            'idprestamo_tipobien' => $request->bien_idprestamo_tipobien,
            'idusuario' => $request->idusuario,
            'idtienda' => $idtienda,
            'idestado' => 1
        ]);
        return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha registrado correctamente.'
        ]);
      }
      elseif ($request->view == 'agregar-bien') {
        DB::table('s_prestamo_creditobien')->insert([
          'idprestamo_bien' => $request->idbien,
          'idprestamo_credito' => $request->idcredito
        ]);
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha agregado correctamente.'
        ]);
      }
      
      elseif ($request->input('view') == 'registrar-relacion') {
        $rules = [
            'relacion_idpersona' => 'required',
            'relacion_idprestamo_tiporelacion' => 'required',
        ];
        $messages = [
            'relacion_idpersona.required' => 'La "Persona" es Obligatorio.',
            'relacion_idprestamo_tiporelacion.required' => 'El "Tipo de Relacion" es Obligatorio.',
        ];
        $this->validate($request, $rules, $messages);

        if($request->relacion_idpersona == $request->idusuario) {
            return response()->json([
                'resultado' => 'ERROR',
                'mensaje'   => 'No puedes asignar la persona al mismo usuario!!.'
            ]);
        }

        $s_prestamo_relacion = DB::table('s_prestamo_relacion')
            ->where([
                ['s_prestamo_relacion.idusuario',$request->idusuario],
                ['s_prestamo_relacion.idpersona',$request->relacion_idpersona],
                ['s_prestamo_relacion.idestado',1]
            ])
            ->first();
        if($s_prestamo_relacion!='') {
            return response()->json([
                'resultado' => 'ERROR',
                'mensaje'   => 'La "Persona" ya esta asignada!!.'
            ]);
        }
        // fin

        /* idestado
         * 1 = activo
         * 2 = anulado
         */
        DB::table('s_prestamo_relacion')->insert([
            'fecharegistro' => Carbon::now(),
            'idpersona' => $request->relacion_idpersona,
            'idprestamo_tiporelacion' => $request->relacion_idprestamo_tiporelacion,
            'idusuario' => $request->idusuario,
            'idtienda' => $idtienda,
            'idestado' => 1
        ]);
        return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha registrado correctamente.'
        ]);
      }
      elseif ($request->view == 'agregar-relacion') {
        DB::table('s_prestamo_creditorelacion')->insert([
          'idprestamo_relacion' => $request->idrelacion,
          'idprestamo_credito' => $request->idcredito
        ]);
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha agregado correctamente.'
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
                              '.(($cronograma['total_segurodesgravamen']>0)?'<td style="padding: 8px;text-align: right;">Seguro</td>':'').'
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
                            <td style="padding: 8px;text-align: right;">'.$value['cuotafinal'].'</td>
                        </tr>';
              }
              $html .= '<tr style="background-color: #31353c;color: white;">
                            <td style="padding: 8px;text-align: right;width: 50px;" colspan="3">TOTAL</td>
                            <td style="padding: 8px;text-align: right;">'.$cronograma['total_amortizacion'].'</td>
                            <td style="padding: 8px;text-align: right;">'.$cronograma['total_interes'].'</td>
                            '.(($cronograma['total_segurodesgravamen']>0)?'<td style="padding: 8px;text-align: right;">'.$cronograma['total_segurodesgravamen'].'</td>':'').'
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

            return json_encode(
              array(
                'draw' => $request->input('draw'),
                'recordsTotal' => $prestamocreditos_desembolsados->total(),
                'recordsFiltered' => $prestamocreditos_desembolsados->total(),
                'data' => $prestamocreditos_desembolsados->items()
              )
            );
      }
      
      elseif ($id == 'show-domiciliogeneral') {
        $direcciones = DB::table('s_prestamo_creditodomicilio')
          ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_creditodomicilio.idprestamo_credito')
          ->join('s_prestamo_domicilio', 's_prestamo_domicilio.id', 's_prestamo_creditodomicilio.idprestamo_domicilio')
          ->join('ubigeo', 'ubigeo.id', 's_prestamo_domicilio.idubigeo')
          ->where([
            ['s_prestamo_credito.idcliente', $request->idusuario],
            ['s_prestamo_credito.idtienda', $idtienda],
          ])
          ->select(
            's_prestamo_domicilio.*',
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

            $tabla[] = [
                'iddomicilio' => $value->id,
                'ubigeonombre' => $value->ubigeonombre,
                'direccion' => $value->direccion,
                'referencia' => $value->referencia,
                'residesde' => mesesEs($value->reside_desdemes).', '.$value->reside_desdeanio,
                'horaubicacion' => date_format(date_create($value->horaubicacion_de),"h:i:s A").' - '.date_format(date_create($value->horaubicacion_hasta),"h:i:s A"),
                'tipopropiedad' => $tipopropiedad,
                'referencia' => $value->referencia,
            ];
        }
        return json_encode([
            'draw' => $request->input('draw'),
            'recordsTotal' => $direcciones->total(),
            'recordsFiltered' => $direcciones->total(),
            'data' => $tabla
        ]);
      }
      elseif ($id == 'show-domicilio') {
        $direcciones = DB::table('s_prestamo_domicilio')
            ->join('ubigeo', 'ubigeo.id', 's_prestamo_domicilio.idubigeo')
            ->where([
                ['s_prestamo_domicilio.idusuario', $request->idusuario],
                ['s_prestamo_domicilio.idtienda', $idtienda],
                ['s_prestamo_domicilio.idestado', 1]
            ])
            ->select(
                's_prestamo_domicilio.*',
                'ubigeo.nombre as ubigeonombre'
            )
            ->orderBy('s_prestamo_domicilio.id','desc')
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

            $tabla[] = [
                'iddomicilio' => $value->id,
                'ubigeonombre' => $value->ubigeonombre,
                'direccion' => $value->direccion,
                'referencia' => $value->referencia,
                'residesde' => mesesEs($value->reside_desdemes).', '.$value->reside_desdeanio,
                'horaubicacion' => date_format(date_create($value->horaubicacion_de),"h:i:s A").' - '.date_format(date_create($value->horaubicacion_hasta),"h:i:s A"),
                'tipopropiedad' => $tipopropiedad,
                'referencia' => $value->referencia,
            ];
        }
        return json_encode([
            'draw' => $request->input('draw'),
            'recordsTotal' => $direcciones->total(),
            'recordsFiltered' => $direcciones->total(),
            'data' => $tabla
        ]);
      }
      
      elseif ($id == 'show-laboralgeneral') {
        $labores = DB::table('s_prestamo_creditolabor')
          ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_creditolabor.idprestamo_credito')
          ->join('s_prestamo_labor', 's_prestamo_labor.id', 's_prestamo_creditolabor.idprestamo_labor')
          ->join('ubigeo', 'ubigeo.id', 's_prestamo_labor.idubigeo')
          ->join('s_prestamo_actividad', 's_prestamo_actividad.id', 's_prestamo_labor.idprestamo_actividad')
          ->where([
            ['s_prestamo_credito.idcliente', $request->idusuario],
            ['s_prestamo_credito.idtienda', $idtienda]
          ])
          ->select(
            's_prestamo_labor.*',
            'ubigeo.nombre as nombre_ubigeo',
            's_prestamo_actividad.nombre as nombre_actividad'
          )
          ->orderBy('s_prestamo_creditolabor.id','desc')
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

            $tabla[] = [
                'idlaboral' => $value->id,
                'fuenteingreso' => $fuenteingreso,
                'actividad' => $value->nombre_actividad,
                'laboradesde' => mesesEs($value->labora_desdemes).', '.$value->labora_desdeanio,
                'ingresomensual' => $value->ingresomensual,
                'ubigeo' => $value->nombre_ubigeo,
                'direccion' => $value->direccion,
                'referencia' => $value->referencia,
            ];
        }

        return json_encode([
            'draw' => $request->input('draw'),
            'recordsTotal' => $labores->total(),
            'recordsFiltered' => $labores->total(),
            'data' => $tabla
        ]);
      }
      elseif ($id == 'show-laboral') {
        $labores = DB::table('s_prestamo_labor')
            ->join('ubigeo', 'ubigeo.id', 's_prestamo_labor.idubigeo')
            ->join('s_prestamo_actividad', 's_prestamo_actividad.id', 's_prestamo_labor.idprestamo_actividad')
            ->where([
                ['s_prestamo_labor.idusuario', $request->idusuario],
                ['s_prestamo_labor.idtienda', $idtienda],
                ['s_prestamo_labor.idestado', 1]
            ])
            ->select(
                's_prestamo_labor.*',
                'ubigeo.nombre as nombre_ubigeo',
                's_prestamo_actividad.nombre as nombre_actividad'
            )
            ->orderBy('s_prestamo_labor.id','desc')
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

            $tabla[] = [
                'idlaboral' => $value->id,
                'fuenteingreso' => $fuenteingreso,
                'actividad' => $value->nombre_actividad,
                'laboradesde' => mesesEs($value->labora_desdemes).', '.$value->labora_desdeanio,
                'ingresomensual' => $value->ingresomensual,
                'ubigeo' => $value->nombre_ubigeo,
                'direccion' => $value->direccion,
                'referencia' => $value->referencia,
            ];
        }

        return json_encode([
            'draw' => $request->input('draw'),
            'recordsTotal' => $labores->total(),
            'recordsFiltered' => $labores->total(),
            'data' => $tabla
        ]);
      }
      
      elseif ($id == 'show-biengeneral') {
        $bienes = DB::table('s_prestamo_creditobien')
          ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_creditobien.idprestamo_credito')
          ->join('s_prestamo_bien', 's_prestamo_bien.id', 's_prestamo_creditobien.idprestamo_bien')
          ->join('s_prestamo_tipobien', 's_prestamo_tipobien.id', 's_prestamo_bien.idprestamo_tipobien')
          ->where([
            ['s_prestamo_credito.idcliente', $request->idusuario],
            ['s_prestamo_credito.idtienda', $idtienda]
          ])
          ->select(
            's_prestamo_bien.*',
            's_prestamo_tipobien.nombre as nombre_tipobien'
          )
          ->orderBy('s_prestamo_creditobien.id','desc')
          ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));

        $tabla = [];
        foreach($bienes as $value){
            $tabla[] = [
                'idbien' => $value->id,
                'tipobien' => $value->nombre_tipobien,
                'descripcion' => $value->descripcion,
                'valorestimado' => $value->valorestimado,
            ];
        }

        return json_encode([
            'draw' => $request->input('draw'),
            'recordsTotal' => $bienes->total(),
            'recordsFiltered' => $bienes->total(),
            'data' => $tabla
        ]);
      }
      elseif ($id == 'show-bien') {
        $bienes = DB::table('s_prestamo_bien')
            ->join('s_prestamo_tipobien', 's_prestamo_tipobien.id', 's_prestamo_bien.idprestamo_tipobien')
            ->where([
                ['s_prestamo_bien.idusuario', $request->idusuario],
                ['s_prestamo_bien.idtienda', $request->idtienda],
                ['s_prestamo_bien.idestado', 1]
            ])
            ->select(
                's_prestamo_bien.*',
                's_prestamo_tipobien.nombre as nombre_tipobien'
            )
            ->orderBy('s_prestamo_bien.id','desc')
            ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));

        $tabla = [];
        foreach($bienes as $value){
            $tabla[] = [
                'idbien' => $value->id,
                'tipobien' => $value->nombre_tipobien,
                'descripcion' => $value->descripcion,
                'valorestimado' => $value->valorestimado,
            ];
        }

        return json_encode([
            'draw' => $request->input('draw'),
            'recordsTotal' => $bienes->total(),
            'recordsFiltered' => $bienes->total(),
            'data' => $tabla
        ]);
      }
      
      elseif ($id == 'show-relaciongeneral') {
        $relaciones = DB::table('s_prestamo_creditorelacion')
          ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_creditorelacion.idprestamo_credito')
          ->join('s_prestamo_relacion', 's_prestamo_relacion.id', 's_prestamo_creditorelacion.idprestamo_relacion')
          ->join('users as persona', 'persona.id', 's_prestamo_relacion.idpersona')
          ->join('s_prestamo_tiporelacion', 's_prestamo_tiporelacion.id', 's_prestamo_relacion.idprestamo_tiporelacion')
          ->where([
            ['s_prestamo_credito.idcliente', $request->idusuario],
            ['s_prestamo_credito.idtienda', $idtienda]
          ])
          ->select(
            's_prestamo_relacion.*',
            's_prestamo_tiporelacion.nombre as nombre_tiporelacion',
            DB::raw('IF(persona.idtipopersona = 1 || persona.idtipopersona = 3,
                CONCAT(persona.identificacion, " - ", persona.apellidos, ", ", persona.nombre),
                CONCAT(persona.identificacion, " - ", persona.apellidos)) as completo_persona')
          )
          ->orderBy('s_prestamo_creditorelacion.id','desc')
          ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));

        $tabla = [];
        foreach($relaciones as $value){
            $tabla[] = [
                'idrelacion' => $value->id,
                'persona' => $value->completo_persona,
                'tiporelacion' => $value->nombre_tiporelacion,
            ];
        }

        return json_encode([
            'draw' => $request->input('draw'),
            'recordsTotal' => $relaciones->total(),
            'recordsFiltered' => $relaciones->total(),
            'data' => $tabla
        ]);
      }
      elseif ($id == 'show-relacion') {
        $relaciones = DB::table('s_prestamo_relacion')
            ->join('users as persona', 'persona.id', 's_prestamo_relacion.idpersona')
            ->join('s_prestamo_tiporelacion', 's_prestamo_tiporelacion.id', 's_prestamo_relacion.idprestamo_tiporelacion')
            ->where([
                ['s_prestamo_relacion.idusuario', $request->idusuario],
                ['s_prestamo_relacion.idtienda', $request->idtienda],
                ['s_prestamo_relacion.idestado', 1]
            ])
            ->select(
                's_prestamo_relacion.*',
                's_prestamo_tiporelacion.nombre as nombre_tiporelacion',
                DB::raw('IF(persona.idtipopersona = 1 || persona.idtipopersona = 3,
                    CONCAT(persona.identificacion, " - ", persona.apellidos, ", ", persona.nombre),
                    CONCAT(persona.identificacion, " - ", persona.apellidos)) as completo_persona')
            )
            ->orderBy('s_prestamo_relacion.id','desc')
            ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));

        $tabla = [];
        foreach($relaciones as $value){
            $tabla[] = [
                'idrelacion' => $value->id,
                'persona' => $value->completo_persona,
                'tiporelacion' => $value->nombre_tiporelacion,
            ];
        }

        return json_encode([
            'draw' => $request->input('draw'),
            'recordsTotal' => $relaciones->total(),
            'recordsFiltered' => $relaciones->total(),
            'data' => $tabla
        ]);
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
          ->leftjoin('users as conyuge', 'conyuge.id', 's_prestamo_credito.idconyuge')
          ->leftjoin('users as garante', 'garante.id', 's_prestamo_credito.idgarante')
          ->where([
            ['s_prestamo_credito.id', $id],
            ['s_prestamo_credito.idtienda', $idtienda]
          ])
          ->select(
            's_prestamo_credito.*',
            's_prestamo_frecuencia.nombre as frecuencia_nombre',
            's_prestamo_frecuencia.id as idprestamo_frecuencia',
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
        $tienda       = DB::table('tienda')->whereId($idtienda)->first();
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
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
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
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
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
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
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

      elseif ($request->view == 'domiciliogeneral') {
        return view('layouts/backoffice/tienda/sistema/prestamosolicitud/domiciliogeneral', compact(
          'tienda',
          'prestamocredito'
        ));
      }
      elseif ($request->view == 'domicilio') {
        return view('layouts/backoffice/tienda/sistema/prestamosolicitud/domicilio', compact(
          'tienda',
          'prestamocredito'
        ));
      }
      elseif ($request->view == 'domiciliocreate') {
        $ubigeo = DB::table('ubigeo')->get();
        return view('layouts/backoffice/tienda/sistema/prestamosolicitud/domiciliocreate', compact(
          'tienda',
          'ubigeo',
          'prestamocredito'
        ));
      }
      
      elseif ($request->view == 'laboralgeneral') {
        return view('layouts/backoffice/tienda/sistema/prestamosolicitud/laboralgeneral', compact(
          'tienda',
          'prestamocredito'
        ));
      } 
      elseif ($request->view == 'laboral') {
        return view('layouts/backoffice/tienda/sistema/prestamosolicitud/laboral', compact(
          'tienda',
          'prestamocredito'
        ));
      } 
      elseif ($request->view == 'laboralcreate') {
        $giro = DB::table('s_prestamo_giro')->get();
        $ubigeo = DB::table('ubigeo')->get();
        return view('layouts/backoffice/tienda/sistema/prestamosolicitud/laboralcreate', compact(
          'tienda',
          'ubigeo',
          'giro',
          'prestamocredito'
        )); 
      }
      
      elseif ($request->view == 'biengeneral') {
        return view('layouts/backoffice/tienda/sistema/prestamosolicitud/biengeneral', compact(
          'tienda',
          'prestamocredito'
        ));
      } 
      elseif ($request->view == 'bien') {
        return view('layouts/backoffice/tienda/sistema/prestamosolicitud/bien', compact(
          'tienda',
          'prestamocredito'
        ));
      } 
      elseif ($request->view == 'biencreate') {
        $tipobien = DB::table('s_prestamo_tipobien')->get();
        return view('layouts/backoffice/tienda/sistema/prestamosolicitud/biencreate', compact(
          'tienda',
          'tipobien',
          'prestamocredito'
        ));
      }
      
      elseif ($request->view == 'relaciongeneral') {
        return view('layouts/backoffice/tienda/sistema/prestamosolicitud/relaciongeneral', compact(
          'tienda',
          'prestamocredito'
        ));
      } 
      elseif ($request->input('view')=='relacion') {
        return view('layouts/backoffice/tienda/sistema/prestamosolicitud/relacion', compact(
          'tienda',
          'prestamocredito'
        ));
      } 
      elseif ($request->input('view')=='relacioncreate') {
        $tiporelacion = DB::table('s_prestamo_tiporelacion')->get();
        return view('layouts/backoffice/tienda/sistema/prestamosolicitud/relacioncreate', compact(
          'tienda',
          'tiporelacion',
          'prestamocredito'
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
                'idgarante' => $request->input('idgarante')!='' ? $request->input('idgarante') : 0,
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
    }
}
