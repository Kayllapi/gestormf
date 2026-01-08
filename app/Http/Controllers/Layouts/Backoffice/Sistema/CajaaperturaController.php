<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class CajaaperturaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/cajaapertura/tabla',[
                'tienda' => $tienda,
            ]);
        }
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->view == 'registrar') {
            return view(sistema_view().'/cajaapertura/apertura',[
                'tienda' => $tienda,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'apertura') {
            $rules = [
                'idcaja' => 'required',
            ];
            if($request->montoasignar!=''){
                $rules = array_merge($rules,[
                    'montoasignar' => 'numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                ]);
            }
            if($request->montoasignar_dolares!=''){
                $rules = array_merge($rules,[
                    'montoasignar_dolares' => 'numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                ]);
            }
            $rules = array_merge($rules,[
                'idusers' => 'required',
            ]);
            $messages = [
                'idcaja.required'               => 'La "Caja" es Obligatorio.',
                'montoasignar.numeric'          => 'El "Asignar en Soles", debe ser númerico.',
                'montoasignar.regex'            => 'El "Asignar en Soles", debe ser máximo de 2 decimales.',
                'montoasignar.gte'              => 'El "Asignar en Soles", debe ser mayor ó igual 0.',
                'montoasignar_dolares.numeric'  => 'El "Asignar en Dolares", debe ser númerico.',
                'montoasignar_dolares.regex'    => 'El "Asignar en Dolares", debe ser máximo de 2 decimales.',
                'montoasignar_dolares.gte'      => 'El "Asignar en Dolares", debe ser mayor ó igual 0.',
                'idusers.required'              => 'La "Persona a asignar" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $s_aperturacierre = DB::table('s_aperturacierre')
                ->where('s_aperturacierre.s_idestadoaperturacierre',1)
                ->where('s_aperturacierre.idtienda',$idtienda)
                ->where('s_aperturacierre.idsucursal',Auth::user()->idsucursal)
                ->where('s_aperturacierre.s_idusersrecepcion',$request->input('idusers'))
                ->orWhere('s_aperturacierre.s_idestadoaperturacierre',2)
                ->where('s_aperturacierre.idtienda',$idtienda)
                ->where('s_aperturacierre.idsucursal',Auth::user()->idsucursal)
                ->where('s_aperturacierre.s_idusersrecepcion',$request->input('idusers'))
                ->limit(1)
                ->first();
            if($s_aperturacierre!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La persona a asignar ya esta asignado, ingrese otro porfavor.'
                ]);
            }
          
            /*$efectivocaja_soles = sistema_caja_efectivo([
                'idtienda'  => $idtienda,
                'idcaja'    => $request->input('idcaja'),
                'idmoneda'  => 1,
            ]);
            $efectivocaja_dolares = sistema_caja_efectivo([
                'idtienda'  => $idtienda,
                'idcaja'    => $request->input('idcaja'),
                'idmoneda'  => 2,
            ]);*/
          
            $montoasignar = $request->input('montoasignar')!=''?$request->input('montoasignar'):0;
            $montoasignar_dolares = $request->input('montoasignar_dolares')!=''?$request->input('montoasignar_dolares'):0;

            /*if($efectivocaja_soles['total']<$montoasignar){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No hay Saldo en Soles Suficiente, ingrese otro monto porfavor.'
                ]);
            }
            if($efectivocaja_dolares['total']<$montoasignar_dolares){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No hay Saldo en Dolares Suficiente, ingrese otro monto porfavor.'
                ]);
            }*/

            /* =================================================  RESPONSABLE */
            $idusersresponsable = Auth::user()->id;
            $db_idusersresponsable = '';
            if($idusersresponsable!=0){
                $s_users = DB::table('users')->whereId($idusersresponsable)->first();
                $db_idusersresponsable = $s_users->nombrecompleto;
            }

            /* =================================================  RECEPCION */
            $idusersrecepcion = $request->idusers;
            $db_idusersrecepcion = '';
            if($idusersrecepcion!=0){
                $s_users = DB::table('users')->whereId($idusersrecepcion)->first();
                $db_idusersrecepcion = $s_users->nombrecompleto;
            }

            /* =================================================  TIPO CAJA */
            $idtipocaja = 1;
            $s_tipocaja = DB::table('s_tipocaja')->whereId(1)->first();
            $db_idtipocaja = $s_tipocaja->nombre;

            /* =================================================  CAJA */
            $idcaja = $request->idcaja;
            $db_idcaja = '';
            if($idcaja!=0){
                $s_caja = DB::table('s_caja')->whereId($idcaja)->first();
                $db_idcaja = $s_caja->nombre;
            }
      
          
            $s_idaperturacierre = DB::table('s_aperturacierre')->insertGetId([
                'fecharegistro'                 => Carbon::now(),
                'montoasignar'                  => $montoasignar,
                'montoasignar_dolares'          => $montoasignar_dolares,
                'montocierre'                   => '0.00',
                'montocierre_dolares'           => '0.00',
                'montocierre_recibido'          => '0.00',
                'montocierre_recibido_dolares'  => '0.00',
                'montocobradoauxiliar'          => '0.00',
                'montocobradoauxiliar_dolares'  => '0.00',
                'db_idusersresponsable'         => $db_idusersresponsable,
                'db_idusersrecepcion'           => $db_idusersrecepcion,
                'db_idtipocaja'                 => $db_idtipocaja,
                'db_idcaja'                     => $db_idcaja,
              
                's_idusersresponsable'          => $idusersresponsable,
                's_idusersrecepcion'            => $idusersrecepcion,
                's_idtipocaja'                  => $idtipocaja, // 1=norma, 2=axiliar
                's_idaperturacierre'            => 0,
                's_idestadoaperturacierre'      => 2,
                's_idcaja'                      => $idcaja,
                'idsucursal'                    => Auth::user()->idsucursal,
                'idtienda'                      => $idtienda,
                'idestado'                      => 1,
            ]);
          
            
            if(Auth::user()->id==$idusersrecepcion){
                DB::table('s_aperturacierre')->whereId($s_idaperturacierre)->update([
                    'fechaconfirmacion' => Carbon::now()
                ]);
            }
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif ($request->view == 'cierre') {
          dd(123);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        if($id == 'show_table'){
             $aperturacierres = DB::table('s_aperturacierre')
                /*->where('s_aperturacierre.db_idtipoproductomovimiento','LIKE','%'.$request['columns'][0]['search']['value'].'%')
                ->where('s_aperturacierre.motivo','LIKE','%'.$request['columns'][1]['search']['value'].'%')
                ->where('s_aperturacierre.db_idproducto','LIKE','%'.$request['columns'][2]['search']['value'].'%')
                ->where('s_aperturacierre.db_idunidadmedida','LIKE','%'.$request['columns'][3]['search']['value'].'%')*/
                ->where('s_aperturacierre.idtienda',$idtienda)
                ->where('s_aperturacierre.idsucursal',Auth::user()->idsucursal)
                ->orderBy('s_aperturacierre.id','desc')
                ->paginate($request->length,'*',null,($request->start/$request->length)+1);

            $moneda_soles = DB::table('s_moneda')->whereId(1)->first();
            $moneda_dolares = DB::table('s_moneda')->whereId(2)->first();

            $tabla = [];
            foreach($aperturacierres as $value){

                $estado = '';
                if($value->s_idestadoaperturacierre==1 && $value->s_idusersresponsable==Auth::user()->id){
                    $estado = 'APERTURA EN PROCESO';
                }elseif($value->s_idestadoaperturacierre==2 && ($value->s_idusersresponsable==Auth::user()->id || $value->s_idusersrecepcion==Auth::user()->id) && $value->fechaconfirmacion==''){
                    $estado = 'APERTURA PENDIENTE';
                }elseif($value->s_idestadoaperturacierre==2 && ($value->s_idusersresponsable==Auth::user()->id || $value->s_idusersrecepcion==Auth::user()->id) && $value->fechaconfirmacion!=''){
                    $estado = 'APERTURADO';
                }elseif($value->s_idestadoaperturacierre==3 && ($value->s_idusersresponsable==Auth::user()->id || $value->s_idusersrecepcion==Auth::user()->id) && $value->fechacierreconfirmacion==''){
                    $estado = 'CIERRE PENDIENTE';
                }elseif($value->s_idestadoaperturacierre==3 && ($value->s_idusersresponsable==Auth::user()->id || $value->s_idusersrecepcion==Auth::user()->id) && $value->fechacierreconfirmacion!=''){
                    $estado = 'CERRADO';
                }

                $opcion = [];
                if($value->s_idestadoaperturacierre==1 && $value->s_idusersresponsable==Auth::user()->id){
                    $opcion[] = [
                        'nombre'  => 'Confirmar Apertura',
                        'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=apertura_confirmar',
                        'icono'   => 'check'
                    ];
                    $opcion[] = [
                        'nombre'  => 'Eliminar Apertura',
                        'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=apertura_eliminar',
                        'icono'   => 'trash'
                    ];
                }elseif($value->s_idestadoaperturacierre==2 && $value->s_idusersresponsable==Auth::user()->id && $value->s_idusersrecepcion!=Auth::user()->id && $value->fechaconfirmacion==''){
                    $opcion[] = [
                        'nombre'  => 'Cancelar Apertura',
                        'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=apertura_cancelar',
                        'icono'   => 'ban'
                    ];
                    $opcion[] = [
                        'nombre'  => 'Detalle de Apertura',
                        'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=apertura_detalle',
                        'icono'   => 'list'
                    ];
                    $opcion[] = [
                        'nombre'  => 'PDF',
                        'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=pdf',
                        'icono'   => 'file'
                    ];
                }elseif($value->s_idestadoaperturacierre==2 && $value->s_idusersrecepcion==Auth::user()->id && $value->fechaconfirmacion==''){
                    $opcion[] = [
                        'nombre'  => 'Recepcionar Apertura',
                        'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=confirmarrecepcion',
                        'icono'   => 'check'
                    ];
                }elseif($value->s_idestadoaperturacierre==2 && $value->s_idusersresponsable==Auth::user()->id && $value->s_idusersrecepcion!=Auth::user()->id && $value->fechaconfirmacion!=''){
                    $opcion[] = [
                        'nombre'  => 'Detalle del Día',
                        'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=detallediario',
                        'icono'   => 'list'
                    ];
                    $opcion[] = [
                        'nombre'  => 'Detalle de Apertura',
                        'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=apertura_detalle',
                        'icono'   => 'list'
                    ];
                    $opcion[] = [
                        'nombre'  => 'PDF',
                        'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=pdf',
                        'icono'   => 'file'
                    ];
                }elseif($value->s_idestadoaperturacierre==2 && $value->s_idusersrecepcion==Auth::user()->id && $value->fechaconfirmacion!=''){
                    $opcion[] = [
                        'nombre'  => 'Cerrar Caja',
                        'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=cierre_confirmar',
                        'icono'   => 'check'
                    ];
                    if($value->s_idusersresponsable==Auth::user()->id){
                        $opcion[] = [
                            'nombre'  => 'Detalle del Día',
                            'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=detallediario',
                            'icono'   => 'list'
                        ];
                        $opcion[] = [
                            'nombre'  => 'Detalle de Apertura',
                            'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=apertura_detalle',
                            'icono'   => 'list'
                        ];
                        $opcion[] = [
                            'nombre'  => 'PDF',
                            'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=pdf',
                            'icono'   => 'file'
                        ];
                    }
                }elseif($value->s_idestadoaperturacierre==3 && $value->s_idusersresponsable==Auth::user()->id && $value->fechacierreconfirmacion==''){
                    $opcion[] = [
                        'nombre'  => 'Revisar Cierre',
                        'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=confirmarrecepcioncierre',
                        'icono'   => 'check'
                    ];
                }elseif($value->s_idestadoaperturacierre==3 && ($value->s_idusersresponsable==Auth::user()->id || $value->s_idusersrecepcion==Auth::user()->id) && $value->fechacierreconfirmacion!=''){
                    $opcion[] = [
                        'nombre'  => 'Detalle del Día',
                        'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=detallediario',
                        'icono'   => 'list'
                    ];
                    $opcion[] = [
                        'nombre'  => 'Detalle de Apertura',
                        'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=apertura_detalle',
                        'icono'   => 'list'
                    ];
                    $opcion[] = [
                        'nombre'  => 'Detalle de Cierre',
                        'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=detallecierre',
                        'icono'   => 'list'
                    ];
                    $opcion[] = [
                        'nombre'  => 'PDF',
                        'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=pdf',
                        'icono'   => 'file'
                    ];
                }elseif($value->s_idestadoaperturacierre==3 && ($value->s_idusersresponsable==Auth::user()->id || $value->s_idusersrecepcion==Auth::user()->id) && $value->fechacierreconfirmacion==''){
                    $opcion[] = [
                        'nombre'  => 'Detalle del Día',
                        'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=detallediario',
                        'icono'   => 'list'
                    ];
                    $opcion[] = [
                        'nombre'  => 'Detalle de Apertura',
                        'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=apertura_detalle',
                        'icono'   => 'list'
                    ];
                    $opcion[] = [
                        'nombre'  => 'PDF',
                        'onclick' => '/'.$idtienda.'/cajaapertura/'.$value->id.'/edit?view=pdf',
                        'icono'   => 'file'
                    ];
                }

                $tabla[] = [
                    'id' => $value->id,
                    //'text' => $value->cajanombre.' (S/. '.$efectivocaja_soles['total'].' - $ '.$efectivocaja_dolares['total'].')',
                    'responsable' => $value->db_idusersresponsable,
                    'recepcion' => $value->db_idusersrecepcion,
                    'caja' => $value->db_idcaja.($value->s_idtipocaja==2?'('.$value->db_idtipocaja.')':''),
                    'apertura' => $moneda_soles->simbolo.' '.$value->montoasignar.'<br>'.$moneda_dolares->simbolo.' '.$value->montoasignar_dolares,
                    'cierre' => $moneda_soles->simbolo.' '.$value->montocierre.'<br>'.$moneda_dolares->simbolo.' '.$value->montocierre_dolares,
                    'fecha_apertura' => $value->fechaconfirmacion!=''?date_format(date_create($value->fechaconfirmacion),"d/m/Y h:i:s A"):'---',
                    'fecha_cierre' => $value->fechacierre!=''?date_format(date_create($value->fechacierre),"d/m/Y h:i:s A"):'---',
                    'estado' => $estado,
                    'opcion' => $opcion
                ];

            }

            return response()->json([
                'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $aperturacierres->total(),
                'data'            => $tabla,
            ]); 
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')
          ->leftJoin('ubigeo', 'ubigeo.id', 'tienda.idubigeo')
          ->where('tienda.id', $idtienda)
          ->select(
              'tienda.*',
              'ubigeo.nombre as ubigeonombre',
          )
          ->first();
      
        $s_aperturacierre = DB::table('s_aperturacierre')
            ->where('s_aperturacierre.id',$id)
            ->first();
      
        if($request->input('view') == 'apertura_confirmar') {
            return view(sistema_view().'/cajaapertura/apertura_confirmar',[
                'tienda' => $tienda,
                's_aperturacierre' => $s_aperturacierre,
            ]);
        }
        elseif($request->input('view') == 'apertura_cancelar') {
            return view(sistema_view().'/cajaapertura/apertura_cancelar',[
                'tienda' => $tienda,
                's_aperturacierre' => $s_aperturacierre,
            ]);
        }
        elseif($request->input('view') == 'apertura_eliminar') {
            return view(sistema_view().'/cajaapertura/apertura_eliminar',[
                'tienda' => $tienda,
                's_aperturacierre' => $s_aperturacierre,
            ]);
        }
        elseif($request->input('view') == 'apertura_detalle') {
            return view(sistema_view().'/cajaapertura/apertura_detalle',[
                'tienda' => $tienda,
                's_aperturacierre' => $s_aperturacierre,
            ]);
        }
        elseif($request->input('view') == 'detallecierre') {
            return view(sistema_view().'/cajaapertura/cierre_detalle',[
                'tienda' => $tienda,
                's_aperturacierre' => $s_aperturacierre,
            ]);
        }
        
        elseif($request->input('view') == 'cierre_confirmar') {
            $verificar_aperturacierre_auxiliar = DB::table('s_aperturacierre')
                ->where('s_aperturacierre.s_idestadoaperturacierre',1)
                ->where('s_aperturacierre.s_idusersresponsable',Auth::user()->id)
                ->where('s_aperturacierre.id','<>',$id)
                ->where('s_aperturacierre.s_idaperturacierre','<>',0)
                ->orWhere('s_aperturacierre.s_idestadoaperturacierre',2)
                ->where('s_aperturacierre.s_idusersresponsable',Auth::user()->id)
                ->where('s_aperturacierre.id','<>',$id)
                ->where('s_aperturacierre.s_idaperturacierre','<>',0)
                ->orWhere('s_aperturacierre.s_idestadoaperturacierre',3)
                ->where('s_aperturacierre.s_idusersresponsable',Auth::user()->id)
                ->where('s_aperturacierre.id','<>',$id)
                ->where('s_aperturacierre.s_idaperturacierre','<>',0)
                ->whereNull('s_aperturacierre.fechacierreconfirmacion')
                ->limit(1)
                ->first();
            return view(sistema_view().'/cajaapertura/cierre_confirmar',[
                'tienda' => $tienda,
                's_aperturacierre' => $s_aperturacierre,
                'verificar_aperturacierre_auxiliar' => $verificar_aperturacierre_auxiliar,
            ]);
        }
        elseif($request->input('view') == 'pdf') {
            return view(sistema_view().'/cajaapertura/modalpdf',[
                'tienda' => $tienda,
                's_aperturacierre' => $s_aperturacierre,
            ]);
        }
        elseif ($request->input('view') == 'pdf-apertura') {
            $aperturacierre = DB::table('s_aperturacierre')
              ->whereId($id)
              ->first();
          
            //$caja = caja($tienda->id,$aperturacierre->idusersrecepcion);
            $efectivocaja_soles = sistema_efectivo([
              'idtienda' => $tienda->id,
              'idapertura' => $aperturacierre->id,
              'idmoneda' => 1,
            ]); 
            $efectivocaja_dolares = sistema_efectivo([
              'idtienda' => $tienda->id,
              'idapertura' => $aperturacierre->id,
              'idmoneda' => 2,
            ]);
            //dd($efectivocaja_soles);
           $pdf = PDF::loadView(sistema_view().'/cajaapertura/reportepdf', [
              'tienda' => $tienda,
              'aperturacierre' => $aperturacierre,
              'efectivo_soles' => $efectivocaja_soles,
              'efectivo_dolares' => $efectivocaja_dolares,
           ]);
          
            return $pdf->stream('REPORT_DE_APERTURAS_DE_CAJA.pdf');
        }
        elseif ($request->input('view') == 'detallediario') {
            $aperturacierre = DB::table('s_aperturacierre')
              ->whereId($id)
              ->first();
          
            //$caja = caja($tienda->id,$aperturacierre->idusersrecepcion);
            $efectivocaja_soles = sistema_efectivo([
              'idtienda' => $tienda->id,
              'idapertura' => $aperturacierre->id,
              'idmoneda' => 1,
            ]); 
            
            $efectivocaja_dolares = sistema_efectivo([
              'idtienda' => $tienda->id,
              'idapertura' => $aperturacierre->id,
              'idmoneda' => 2,
            ]);
          
            return view(sistema_view().'/cajaapertura/detallediario',[
                'tienda' => $tienda,
                'aperturacierre' => $aperturacierre,
                'efectivo_soles' => $efectivocaja_soles,
                'efectivo_dolares' => $efectivocaja_dolares,
            ]);
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view') == 'apertura_confirmar') {

            $rules = [];
            if($request->montoasignar!=''){
                $rules = array_merge($rules,[
                    'montoasignar' => 'numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                ]);
            }
            if($request->montoasignar_dolares!=''){
                $rules = array_merge($rules,[
                    'montoasignar_dolares' => 'numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gte:0',
                ]);
            }
            $messages = [
                'montoasignar.numeric'          => 'El "Asignar en Soles", debe ser númerico.',
                'montoasignar.regex'            => 'El "Asignar en Soles", debe ser máximo de 2 decimales.',
                'montoasignar.gte'              => 'El "Asignar en Soles", debe ser mayor ó igual 0.',
                'montoasignar_dolares.numeric'  => 'El "Asignar en Dolares", debe ser númerico.',
                'montoasignar_dolares.regex'    => 'El "Asignar en Dolares", debe ser máximo de 2 decimales.',
                'montoasignar_dolares.gte'      => 'El "Asignar en Dolares", debe ser mayor ó igual 0.',
            ];
            $this->validate($request,$rules,$messages);
          
            $efectivocaja_soles = sistema_efectivocaja($idtienda,$request->input('idcaja'),1);
            $efectivocaja_dolares = sistema_efectivocaja($idtienda,$request->input('idcaja'),2);
            $montoasignar = $request->input('montoasignar')!=''?$request->input('montoasignar'):0;
            $montoasignar_dolares = $request->input('montoasignar_dolares')!=''?$request->input('montoasignar_dolares'):0;

            if($efectivocaja_soles['total']<$montoasignar){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No hay Saldo en Soles Suficiente, ingrese otro monto porfavor.'
                ]);
            }
            if($efectivocaja_dolares['total']<$montoasignar_dolares){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No hay Saldo en Dolares Suficiente, ingrese otro monto porfavor.'
                ]);
            }
          

            DB::table('s_aperturacierre')->whereId($id)->update([
                'fechaconfirmarenvio' => Carbon::now(),
                'montoasignar' => $montoasignar,
                'montoasignar_dolares' => $montoasignar_dolares,
                'idusersresponsable' => Auth::user()->id,
                'idestadoaperturacierre' => 2,
            ]);
          
            json_cajaapertura($idtienda,Auth::user()->idsucursal);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'apertura_cancelar') {

            DB::table('s_aperturacierre')->whereId($id)->update([
                'fechaanularenvio' => Carbon::now(),
                'idestadoaperturacierre' => 1
            ]);
          
            json_cajaapertura($idtienda,Auth::user()->idsucursal);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha cancelado correctamente.'
            ]);
        }  
        elseif($request->input('view') == 'confirmarcierre') {
            $aperturacierre = DB::table('s_aperturacierre')
                ->whereId($id)
                ->first();
          
            $fectivosoles = efectivo($idtienda,$id,1);
            $fectivodolares = efectivo($idtienda,$id,2);
            $montocierre_recibido = $fectivosoles['total'];
            $montocierre_recibido_dolares = $fectivodolares['total'];
            if($aperturacierre->config_prestamo_tipocierrecaja==1){
            }elseif($aperturacierre->config_prestamo_tipocierrecaja==3){
                if($aperturacierre->config_sistema_moneda_usar==1){
                $montocierre_recibido = $request->totalsoles;
                $montocierre_recibido_dolares = 0;
                }elseif($aperturacierre->config_sistema_moneda_usar==2){
                $montocierre_recibido = 0;
                $montocierre_recibido_dolares = $request->totaldolares;
                }elseif($aperturacierre->config_sistema_moneda_usar==3){
                $montocierre_recibido = $request->totalsoles;
                $montocierre_recibido_dolares = $request->totaldolares;
                }
            }
          
            if($tienda->idcategoria==30){
                $cant_ordenpedidos = DB::table('s_comida_ordenpedido')
                                ->where('s_comida_ordenpedido.idtienda', $idtienda)
                                ->where('s_comida_ordenpedido.idestado', 1)
                                ->where('s_comida_ordenpedido.idestadoordenpedido', 1)
                                ->count();
          
                if($cant_ordenpedidos>0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'No puede cerrar la caja, por que hay Ordenes de Pedidos Pendientes.'
                    ]);
                }
            }

            if($aperturacierre->montocobradoauxiliar>0){
            if($montocierre_recibido!=$aperturacierre->montocobradoauxiliar){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Total Efectivo S/" debe ser igual al "Monto total cobrado en Soles".'
                    ]);
            }
            }
            
            if($aperturacierre->montocobradoauxiliar_dolares>0){
            if($montocierre_recibido_dolares!=$aperturacierre->montocobradoauxiliar_dolares){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Total Efectivo $" debe ser igual al "Monto total cobrado en Dolares".'
                    ]);
            }
            }

            $fecharegistro = Carbon::now();
          
            DB::table('s_aperturacierre')->whereId($id)->update([
                'fechacierre' => $fecharegistro,
                'montocierre' => $fectivosoles['total'],
                'montocierre_dolares' => $fectivodolares['total'],
                'montocierre_recibido' => $montocierre_recibido,
                'montocierre_recibido_dolares' => $montocierre_recibido_dolares,
                'config_prestamo_tipocierrecaja' => configuracion($idtienda,'prestamo_tipocierrecaja')['valor'],
                'idestadoaperturacierre' => 3
            ]);
          
            $s_aperturacierre = DB::table('s_aperturacierre')
                ->where('s_aperturacierre.id',$id)
                ->first();
          
            if(Auth::user()->id==$s_aperturacierre->idusersresponsable){
                DB::table('s_aperturacierre')->whereId($id)->update([
                    'fechacierreconfirmacion' => Carbon::now()
                ]);
            }
          
            if(configuracion($idtienda,'prestamo_tipocierrecaja')['valor']==3){
          
                $cierrecantidadsoles1 = $request->cierrecantidadsoles1!=''?$request->cierrecantidadsoles1:0;
                $cierrecantidadsoles2 = $request->cierrecantidadsoles2!=''?$request->cierrecantidadsoles2:0;
                $cierrecantidadsoles3 = $request->cierrecantidadsoles3!=''?$request->cierrecantidadsoles3:0;
                $cierrecantidadsoles4 = $request->cierrecantidadsoles4!=''?$request->cierrecantidadsoles4:0;
                $cierrecantidadsoles5 = $request->cierrecantidadsoles5!=''?$request->cierrecantidadsoles5:0;
                $cierrecantidadsoles6 = $request->cierrecantidadsoles6!=''?$request->cierrecantidadsoles6:0;
                $cierrecantidadsoles7 = $request->cierrecantidadsoles7!=''?$request->cierrecantidadsoles7:0;
                $cierrecantidadsoles8 = $request->cierrecantidadsoles8!=''?$request->cierrecantidadsoles8:0;
                $cierrecantidadsoles9 = $request->cierrecantidadsoles9!=''?$request->cierrecantidadsoles9:0;
                $cierrecantidadsoles10 = $request->cierrecantidadsoles10!=''?$request->cierrecantidadsoles10:0;
                $cierrecantidadsoles11 = $request->cierrecantidadsoles11!=''?$request->cierrecantidadsoles11:0;
              
                $cierrecantidaddolares1 = $request->cierrecantidaddolares1!=''?$request->cierrecantidaddolares1:0;
                $cierrecantidaddolares2 = $request->cierrecantidaddolares2!=''?$request->cierrecantidaddolares2:0;
                $cierrecantidaddolares3 = $request->cierrecantidaddolares3!=''?$request->cierrecantidaddolares3:0;
                $cierrecantidaddolares4 = $request->cierrecantidaddolares4!=''?$request->cierrecantidaddolares4:0;
                $cierrecantidaddolares5 = $request->cierrecantidaddolares5!=''?$request->cierrecantidaddolares5:0;
                $cierrecantidaddolares6 = $request->cierrecantidaddolares6!=''?$request->cierrecantidaddolares6:0;
                $cierrecantidaddolares7 = $request->cierrecantidaddolares7!=''?$request->cierrecantidaddolares7:0;
                $cierrecantidaddolares9 = $request->cierrecantidaddolares9!=''?$request->cierrecantidaddolares9:0;
                $cierrecantidaddolares10 = $request->cierrecantidaddolares10!=''?$request->cierrecantidaddolares10:0;
                $cierrecantidaddolares11 = $request->cierrecantidaddolares11!=''?$request->cierrecantidaddolares11:0;
                $cierrecantidaddolares12 = $request->cierrecantidaddolares12!=''?$request->cierrecantidaddolares12:0;
                $cierrecantidaddolares13 = $request->cierrecantidaddolares13!=''?$request->cierrecantidaddolares13:0;
              
                
                  
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 200,
                    'cantidad' => $cierrecantidadsoles1,
                    'total' => 200*$cierrecantidadsoles1,
                    'idmoneda' => 1,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 100,
                    'cantidad' => $cierrecantidadsoles2,
                    'total' => 100*$cierrecantidadsoles2,
                    'idmoneda' => 1,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 50,
                    'cantidad' => $cierrecantidadsoles3,
                    'total' => 50*$cierrecantidadsoles3,
                    'idmoneda' => 1,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 20,
                    'cantidad' => $cierrecantidadsoles4,
                    'total' => 20*$cierrecantidadsoles4,
                    'idmoneda' => 1,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 10,
                    'cantidad' => $cierrecantidadsoles5,
                    'total' => 10*$cierrecantidadsoles5,
                    'idmoneda' => 1,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 5,
                    'cantidad' => $cierrecantidadsoles6,
                    'total' => 5*$cierrecantidadsoles6,
                    'idmoneda' => 1,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 2,
                    'cantidad' => $cierrecantidadsoles7,
                    'total' => 2*$cierrecantidadsoles7,
                    'idmoneda' => 1,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 1,
                    'cantidad' => $cierrecantidadsoles8,
                    'total' => 1*$cierrecantidadsoles8,
                    'idmoneda' => 1,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 0.5,
                    'cantidad' => $cierrecantidadsoles9,
                    'total' => 0.5*$cierrecantidadsoles9,
                    'idmoneda' => 1,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 0.2,
                    'cantidad' => $cierrecantidadsoles10,
                    'total' => 0.2*$cierrecantidadsoles10,
                    'idmoneda' => 1,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 0.1,
                    'cantidad' => $cierrecantidadsoles11,
                    'total' => 0.1*$cierrecantidadsoles11,
                    'idmoneda' => 1,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
              
                
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 100,
                    'cantidad' => $cierrecantidaddolares1,
                    'total' => 100*$cierrecantidaddolares1,
                    'idmoneda' => 2,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 50,
                    'cantidad' => $cierrecantidaddolares2,
                    'total' => 50*$cierrecantidaddolares2,
                    'idmoneda' => 2,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 20,
                    'cantidad' => $cierrecantidaddolares3,
                    'total' => 20*$cierrecantidaddolares3,
                    'idmoneda' => 2,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 10,
                    'cantidad' => $cierrecantidaddolares4,
                    'total' => 10*$cierrecantidaddolares4,
                    'idmoneda' => 2,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 5,
                    'cantidad' => $cierrecantidaddolares5,
                    'total' => 5*$cierrecantidaddolares5,
                    'idmoneda' => 2,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 2,
                    'cantidad' => $cierrecantidaddolares6,
                    'total' => 2*$cierrecantidaddolares6,
                    'idmoneda' => 2,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 1,
                    'cantidad' => $cierrecantidaddolares7,
                    'total' => 1*$cierrecantidaddolares7,
                    'idmoneda' => 2,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 0.5,
                    'cantidad' => $cierrecantidaddolares9,
                    'total' => 0.5*$cierrecantidaddolares9,
                    'idmoneda' => 2,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 0.25,
                    'cantidad' => $cierrecantidaddolares10,
                    'total' => 0.25*$cierrecantidaddolares10,
                    'idmoneda' => 2,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 0.10,
                    'cantidad' => $cierrecantidaddolares11,
                    'total' => 0.10*$cierrecantidaddolares11,
                    'idmoneda' => 2,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 0.05,
                    'cantidad' => $cierrecantidaddolares12,
                    'total' => 0.05*$cierrecantidaddolares12,
                    'idmoneda' => 2,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 0.01,
                    'cantidad' => $cierrecantidaddolares13,
                    'total' => 0.01*$cierrecantidaddolares13,
                    'idmoneda' => 2,
                    'idaperturacierre' => $id,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
            }

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha confirmado correctamente.'
            ]);
        }
    }


    public function destroy(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        
        if($request->input('view') == 'apertura_eliminar') {

            DB::table('s_aperturacierre')
                ->where('s_aperturacierre.idtienda',$idtienda)
                ->where('s_aperturacierre.id',$id)
                ->delete();
          
            json_cajaapertura($idtienda,Auth::user()->idsucursal);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha cancelado correctamente.'
            ]);
        } 
    }
}
