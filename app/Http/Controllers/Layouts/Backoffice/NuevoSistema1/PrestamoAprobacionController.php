<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class PrestamoAprobacionController extends Controller
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
      
        return view('layouts/backoffice/tienda/sistema/prestamoaprobacion/index',[
          'tienda' => $tienda
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
      if ($id == 'show-creditodesembolsado') {
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
              's_prestamo_credito.fechapreaprobado as fechapreaprobado',
              's_prestamo_credito.fechaaprobado as fechaaprobado',
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
              
                $fecharegistro = $value->fechapreaprobado;
                if($value->idestadocredito==2){
                $opcion = '<li><a href="javascript:;" onclick="aprobar_preaprobado('.$idtienda.','.$value->idcredito.')"><i class="fa fa-edit"></i> Aprobar</a></li>
                            <li><a href="javascript:;" onclick="desaprobar_preaprobado('.$idtienda.','.$value->idcredito.')"><i class="fa fa-ban"></i> Desaprobar</a></li>
                            <li><a href="javascript:;" onclick="detalle_preaprobado('.$idtienda.','.$value->idcredito.')"><i class="fa fa-list"></i> Detalle</a></li>';
                $fecharegistro = $value->fechapreaprobado;
                }elseif($value->idestadocredito==3){
                $opcion = '<li><a href="javascript:;" onclick="detalle_aprobado('.$idtienda.','.$value->idcredito.')"><i class="fa fa-list"></i> Detalle</a></li>';
                $fecharegistro = $value->fechaaprobado;
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
      $configuracion_prestamo = configuracion_prestamo($idtienda);
      $prestamocredito = DB::table('s_prestamo_credito')
          ->join('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_credito.idprestamo_frecuencia')
          ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
          ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
          ->leftjoin('users as conyuge', 'conyuge.id', 's_prestamo_credito.idconyuge')
          ->where('s_prestamo_credito.id', $id)
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
                  CONCAT(conyuge.identificacion, " - ", conyuge.apellidos)) as conyuge_nombre')
          )
          ->first();
      $prestamocreditodetalle = DB::table('s_prestamo_creditodetalle')
        ->where('s_prestamo_creditodetalle.idprestamo_credito', $prestamocredito->id)
        ->get();

      if ($request->input('view') == 'aprobar') {
        return view('layouts/backoffice/tienda/sistema/prestamoaprobacion/aprobar', compact(
          'tienda',
          'prestamocredito',
          'prestamocreditodetalle',
          'configuracion_prestamo'
        ));
      }
      elseif ($request->input('view') == 'desaprobar') {
        return view('layouts/backoffice/tienda/sistema/prestamoaprobacion/desaprobar', compact(
          'tienda',
          'prestamocredito',
          'prestamocreditodetalle',
          'configuracion_prestamo'
        ));
      }
      elseif ($request->input('view') == 'detalle') {
        return view('layouts/backoffice/tienda/sistema/prestamoaprobacion/detalle', compact(
          'tienda',
          'prestamocredito',
          'prestamocreditodetalle',
          'configuracion_prestamo'
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
         idestado
         * 1 = correcto
         * 2 = anulado
        */
        if ($request->input('view') == 'aprobar') {
            DB::table('s_prestamo_credito')->whereId($id)->update([
                'fechaaprobado' => Carbon::now(),
                'idsupervisor' => Auth::user()->id,
                'idestadocredito' => 3
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha Aprobado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'desaprobar') {
            DB::table('s_prestamo_credito')->whereId($id)->update([
                'fechadesaprobado' => Carbon::now(),
                'idsupervisor' => Auth::user()->id,
                'idestadocredito' => 1
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha Desaprobado correctamente.'
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
    }
}
