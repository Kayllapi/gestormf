<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use App\Exports\ReportecarteracreditoExport;
use Maatwebsite\Excel\Facades\Excel;

class AsignaciondecarteraController extends Controller
{
    public function __construct()
    {
        //
    }
    public function index(Request $request,$idtienda)
    {
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            
            $agencias = DB::table('tienda')->get();
          
            return view(sistema_view().'/asignaciondecartera/tabla',[
              'tienda' => $tienda,
              'agencias' => $agencias,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
    }
  
    public function store(Request $request, $idtienda)
    {
      
        
        if ($request->input('view') == 'enviar') {
            
            $rules['idagencia']                   =  'required';
            $rules['idasesororigen']              =  'required';
            $rules['idasesordestino']             =  'required';
            $rules['check_origen']             =  'required';
            $messages['idagencia.required']       = 'El campo "Agencia" es obligatorio.';
            $messages['idasesororigen.required']  = 'El campo "Asesor de Origen" es obligatorio.';
            $messages['idasesordestino.required']  = 'El campo "Asesor de Destino" es obligatorio.';
            $messages['check_origen.required']  = 'Debe seleccionar minimo un cliente.';

            $this->validate($request,$rules,$messages);
          
          
            if($request->idasesororigen==$request->idasesordestino){
                return response()->json([
                    'resultado'           => 'ERROR',
                    'mensaje'             => 'No puede enviar al mismo asesor.'
                ]);
            }

						foreach(json_decode($request->check_origen) as $value){
									DB::table('users')->whereId($value)->update([
											'idasesor' => $request->idasesordestino,
											'idtienda' => $request->idagencia,
									]);
						}

            return response()->json([
                'resultado'           => 'CORRECTO',
                'mensaje'             => 'Se ha asignado correctamente!!'
            ]);

        } 
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showcliente_origen'){
          $where = [];
          $where[] = ['users.idtienda',$request->idagencia];
          $where[] = ['users.idasesor',$request->idasesororigen];
          //$where[] = ['users.nombre',$request->input('columns[2][search][value]')];
          //dd($request->input('columns[2][search][value]'));
          $users = DB::table('users')
              ->where('users.idtipousuario',2)
              ->where($where)
              ->orderBy('users.nombre','asc')
              ->get();
              //->paginate($request->length,'*',null,($request->start/$request->length)+1);
         
            $tabla = [];
            $orden = 1;
            foreach($users as $value){
              
                $credito = DB::table('credito')
                    ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                    ->where('credito.idcliente',$value->id)
                    ->where('credito.idestadocredito',1)
                    ->count();
              
              $tabla[]=[
                  'id'      => $value->id,
                  'orden'   => $orden,
                  'doc'     => $value->identificacion,
                  'nombre'  => $value->nombrecompleto,
                  'estado'  => $credito>0?'ACTIVO':'INACTIVO',
              ];
              $orden++;
            }
            
            return response()->json([
                /*'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $users->total(),*/
                'data'            => $tabla,
            ]);
          
        }
        elseif($id == 'showcliente_destino'){
          $where = [];
          $where[] = ['users.idtienda',$request->idagencia];
          $where[] = ['users.idasesor',$request->idasesordestino];
          
          $users = DB::table('users')
              ->where('users.idtipousuario',2)
              ->where($where)
              ->orderBy('users.nombre','asc')
              ->get();
              //->paginate($request->length,'*',null,($request->start/$request->length)+1);
         
            $tabla = [];
            $orden = 1;
            foreach($users as $value){
                
              $tabla[]=[
                  'id'      => $value->id,
                  'orden'   => $orden,
                  'doc'     => $value->identificacion,
                  'nombre'  => $value->nombrecompleto,
              ];
              $orden++;
            }
            
            return response()->json([
                /*'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $users->total(),*/
                'data'            => $tabla,
            ]);
          
        }
        if($id == 'show_destino'){
            
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[3,4,7])
                ->select('users.*','users_permiso.idpermiso as idpermiso','permiso.nombre as nombrepermiso')
                //->where('users.id','<>',$request->idasesororigen)
                ->where('users_permiso.id','<>',$request->idusers_permiso)
                ->get();
            //$data = [];
            $data_html = '<option></option>';
            foreach($usuarios as $value){
                /*$data[] = [
                    'id' => $value->id,
                    'text' => $value->nombrecompleto.' ('.$value->nombrepermiso.')',
                ];*/
                $data_html = $data_html.'<option value="'.$value->id.'">'.$value->nombrecompleto.' ('.$value->nombrepermiso.')</option>';
            }
          
            
            return response()->json($data_html);
        }
        
    }

    public function edit(Request $request, $idtienda, $id)
    {
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
 
      $credito = DB::table('credito')
                    ->join('users as cliente','cliente.id','credito.idcliente')
                    ->leftjoin('users as asesor','asesor.id','credito.idasesor')
                    ->leftjoin('users as aval','aval.id','credito.idaval')
                    ->join('forma_credito','forma_credito.id','credito.idforma_credito')
                    ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
                    ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
                    ->join('tipo_destino_credito','tipo_destino_credito.id','credito.idtipo_destino_credito')
                    ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
                    /*->join('tarifario','tarifario.id','credito.idtarifario')
                    ->join('credito_prendatario','credito_prendatario.id','tarifario.idcredito_prendatario')*/
                    ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                    ->where('credito.id',$id)

                    ->select(
                        'credito.*',
                        'cliente.codigo as codigo_cliente',
                        'cliente.identificacion as docuementocliente',
                        'cliente.nombrecompleto as nombreclientecredito',
                        'aval.identificacion as documentoaval',
                        'aval.nombrecompleto as nombreavalcredito',
                        'forma_credito.nombre as forma_credito_nombre',
                        'tipo_operacion_credito.nombre as tipo_operacion_credito_nombre',
                        'modalidad_credito.nombre as modalidad_credito_nombre',
                        'forma_pago_credito.nombre as forma_pago_credito_nombre',
                        'tipo_destino_credito.nombre as tipo_destino_credito_nombre',
                        /*'tarifario.monto as monto_max_credito',
                        'tarifario.cuotas as coutas_max_credito',
                        'tarifario.tem as tem_producto',
                        'tarifario.tipo_producto_credito as tipo_producto_credito',*/
                        'credito_prendatario.nombre as nombreproductocredito',
                        'credito_prendatario.modalidad as modalidad_calculo',
                        'credito_prendatario.conevaluacion as conevaluacion',
                  'asesor.usuario as codigoasesor',
                    )
                    ->orderBy('credito.id','desc')
                    ->first();

      if( $request->input('view') == 'desembolsar' ){
                




        $usuario = DB::table('users')
              ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
              ->leftJoin('ubigeo as ubigeonacimiento','ubigeonacimiento.id','users.idubigeo_nacimiento')
              ->leftJoin('role_user','role_user.user_id','users.id')
              ->leftJoin('roles','roles.id','role_user.role_id')
              ->where('users.id', $credito->idcliente)
              ->select(
                  'users.*',
                  'roles.id as idroles',
                  'roles.description as descriptionrole',
                  'ubigeo.nombre as ubigeonombre',
                  'ubigeonacimiento.nombre as ubigeonacimientonombre'
              )
              ->first();
      
        $asesor = DB::table('users')->where('users.id',$credito->idasesor)->first();
 
        $users_prestamo = DB::table('s_users_prestamo')->where('s_users_prestamo.id_s_users',$credito->idcliente)->first();
        $nivel_aprobacion = DB::table('nivelaprobacion')
                              ->where('nivelaprobacion.idtipocredito',$credito->idforma_credito)
                              ->where('nivelaprobacion.riesgocredito1','<',$credito->monto_solicitado)
                              ->where('nivelaprobacion.riesgocredito2','>=',$credito->monto_solicitado)
                              ->first();
        
        $credito_aprobacion = DB::table('credito_aprobacion')
                              ->leftJoin('permiso','permiso.id','credito_aprobacion.idpermiso')
                              ->leftJoin('users','users.id','credito_aprobacion.idusers')
                              ->where('credito_aprobacion.idcredito',$credito->id)
                              ->select(
                                'credito_aprobacion.*',
                                'permiso.nombre as nombre_permiso',
                                'users.nombrecompleto as nombre_usuario',
                                'users.nombre as nombre',
                                'users.apellidopaterno as apellidopaterno',
                                'users.clave as clave_usuario'
                              )
                              ->orderBy('permiso.rango','asc')
                              ->get();
        
        
            $garantias = DB::table('credito_garantia')
                ->leftJoin('garantias','garantias.id','credito_garantia.idgarantias')
                ->where('idcredito', $credito->id)
                ->where('credito_garantia.tipo', 'CLIENTE')
                ->select(
                  'credito_garantia.id as id'
                )
                ->get();
        
        return view(sistema_view().'/asignaciondecartera/desembolsar',[
              'tienda' => $tienda,
              'credito' => $credito,
              'usuario' => $usuario,
              'nivel_aprobacion' => $nivel_aprobacion,
              'credito_aprobacion' => $credito_aprobacion,
              'estado' => $request->input('tipo'),
              'garantias' => $garantias,
        ]);
      }
      
        else if($request->input('view') == 'exportar') {
            return view(sistema_view().'/asignaciondecartera/exportar',[
                'tienda' => $tienda,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'idagencia' => $request->idagencia,
                'idformacredito' => $request->idformacredito,
                'idasesor' => $request->idasesor,
                'tipo' => $request->tipo,
            ]);
        }
        else if( $request->input('view') == 'exportar_pdf' ){
              
            
          $where = [];
          if($request->idagencia!='' && $request->idagencia!=0){
              $where[] = ['credito.idtienda',$request->idagencia];
          }

          if($request->idasesor!='' && $request->idasesor!=0){
              $where[] = ['credito.idasesor',$request->idasesor];
          }
          
          if($request->fecha_inicio){
              $where[] = ['credito.fecha_desembolso','<=',$request->fecha_inicio.' 23:59:59'];
          }
          
          if($request->idformacredito!='' && $request->idformacredito!=0){
              if($request->idformacredito=='CP'){
                  $where[] = ['credito.idforma_credito',1];
              }
              elseif($request->idformacredito=='CNP'){
                  $where[] = ['credito.idforma_credito',2];
              }
              elseif($request->idformacredito=='CC'){
                  $where[] = ['credito.idforma_credito',3];
              }
          }
          
          
          $creditos = DB::table('credito')
              ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->join('ubigeo','ubigeo.id','cliente.idubigeo')
              ->leftjoin('users as cajero','cajero.id','credito.idcajero')
              ->leftjoin('users as asesor','asesor.id','credito.idasesor')
              ->leftjoin('users as administrador','administrador.id','credito.idadministrador')
              ->leftjoin('users as aval','aval.id','credito.idaval')
              ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
              ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito.idestadocredito',1)
              ->where('credito.saldo_pendientepago','>',0)
              ->where($where)
              ->select(
                  'credito.*',
                  'cliente.identificacion as identificacioncliente',
                  'cliente.nombrecompleto as nombrecliente',
                  'cliente.numerotelefono as telefonocliente',
                  'cliente.direccion as direccioncliente',
                  'aval.nombrecompleto as nombreaval',
                  'credito_prendatario.nombre as nombreproductocredito' ,
                  'credito_prendatario.modalidad as modalidadproductocredito',
                  'modalidad_credito.nombre as nombremodalidadcredito' ,
                  'forma_pago_credito.nombre as frecuencianombre' ,
                  'cajero.usuario as codigocajero',
                  'asesor.usuario as codigoasesor',
                  'administrador.nombrecompleto as nombreadministrador',
                  'ubigeo.nombre as ubigeonombre',
              )
              ->orderBy('credito.fecha_desembolso','asc')
              ->get();
          
            $agencia = DB::table('tienda')->whereId($request->idagencia)->first();
            $asesor = DB::table('users')->whereId($request->idasesor)->first();
        
            $pdf = PDF::loadView(sistema_view().'/asignaciondecartera/exportar_pdf',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'creditos' => $creditos,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'idformacredito' => $request->idformacredito,
                'asesor' => $asesor,
            ]); 
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('CARTERA_DE_CREDITO.pdf');
        }  
        else if( $request->input('view') == 'exportar_excel' ){
              
            
          $where = [];
          if($request->idagencia!='' && $request->idagencia!=0){
              $where[] = ['credito.idtienda',$request->idagencia];
          }

          if($request->idasesor!='' && $request->idasesor!=0){
              $where[] = ['credito.idasesor',$request->idasesor];
          }
          
          if($request->fecha_inicio){
              $where[] = ['credito.fecha_desembolso','<=',$request->fecha_inicio.' 23:59:59'];
          }
          
          if($request->idformacredito!='' && $request->idformacredito!=0){
              if($request->idformacredito=='CP'){
                  $where[] = ['credito.idforma_credito',1];
              }
              elseif($request->idformacredito=='CNP'){
                  $where[] = ['credito.idforma_credito',2];
              }
              elseif($request->idformacredito=='CC'){
                  $where[] = ['credito.idforma_credito',3];
              }
          }
          
          
          $creditos = DB::table('credito')
              ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->join('ubigeo','ubigeo.id','cliente.idubigeo')
              ->leftjoin('users as cajero','cajero.id','credito.idcajero')
              ->leftjoin('users as asesor','asesor.id','credito.idasesor')
              ->leftjoin('users as administrador','administrador.id','credito.idadministrador')
              ->leftjoin('users as aval','aval.id','credito.idaval')
              ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
              ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito.idestadocredito',1)
              ->where('credito.saldo_pendientepago','>',0)
              ->where($where)
              ->select(
                  'credito.*',
                  'cliente.identificacion as identificacioncliente',
                  'cliente.nombrecompleto as nombrecliente',
                  'cliente.numerotelefono as telefonocliente',
                  'cliente.direccion as direccioncliente',
                  'aval.nombrecompleto as nombreaval',
                  'credito_prendatario.nombre as nombreproductocredito' ,
                  'credito_prendatario.modalidad as modalidadproductocredito',
                  'modalidad_credito.nombre as nombremodalidadcredito' ,
                  'forma_pago_credito.nombre as frecuencianombre' ,
                  'cajero.usuario as codigocajero',
                  'asesor.usuario as codigoasesor',
                  'administrador.nombrecompleto as nombreadministrador',
                  'ubigeo.nombre as ubigeonombre',
              )
              ->orderBy('credito.fecha_desembolso','asc')
              ->get();
          
            $agencia = DB::table('tienda')->whereId($request->idagencia)->first();
            $asesor = DB::table('users')->whereId($request->idasesor)->first();
        
            return Excel::download(
                new ReportecarteracreditoExport(
                    $tienda,
                    $agencia,
                    $creditos,
                    $request->fecha_inicio,
                    $request->idformacredito,
                    $asesor,
                    'REPORTE DE CARTERA DE CRÉDITO'
                ),
                'reporte_cartera_credito.xls'
            );
        } 
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        
    
    }

    public function destroy(Request $request, $idtienda, $id)
    {
    }
}
