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

class CarteraCreditoController extends Controller
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
          
            return view(sistema_view().'/carteracredito/tabla',[
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
      
        if($request->input('view') == 'registrar') {
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showtable'){
          $where = [];
          if($request->idagencia!='' && $request->idagencia!=0){
              $where[] = ['credito.idtienda',$request->idagencia];
          }

          if($request->idasesor!='' && $request->idasesor!=0){
              $where[] = ['credito.idasesor',$request->idasesor];
          }

          if($request->inicio){
              $where[] = ['credito.fecha_desembolso','<=',$request->inicio.' 23:59:59'];
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
              //->where('credito.saldo_pendientepago','>',0)
              ->where($where)
              ->select(
                  'credito.*',
                  'cliente.identificacion as identificacioncliente',
                  'cliente.nombrecompleto as nombrecliente',
                  'cliente.numerotelefono as telefonocliente',
                  'cliente.direccion as direccioncliente',
                  'aval.identificacion as identificacionaval',
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
          
          $html = '';
          $total_desembolsado = 0;
          $total_saldo = 0;
          $total_deuda = 0;
          foreach($creditos as $key => $value){
              
              /*$creditorefinanciado = DB::table('credito')
                  ->where('idcredito_refinanciado',$value->id)
                  ->first();*/
            
              $cp = '';
              if($value->idforma_credito==1){
                  $cp = 'CP';
              }
              elseif($value->idforma_credito==2){
                  $cp = 'CNP';
              }
              elseif($value->idforma_credito==3){
                  $cp = 'CC';
              }

              $cronograma = select_cronograma(
                  $request->idagencia,
                  $value->id,
                  $value->idforma_credito,
                  $value->modalidadproductocredito,
                  $value->cuotas,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  1,
                  'detalle_cobranza'
              );

              $clasificacion = '';

              if($cronograma['ultimo_atraso']<=8){
                  $clasificacion = 'NORMAL';
              }
              elseif($cronograma['ultimo_atraso']>8 && $cronograma['ultimo_atraso']<=30){
                  $clasificacion = 'CPP';
              }
              elseif($cronograma['ultimo_atraso']>30 && $cronograma['ultimo_atraso']<=60){
                  $clasificacion = 'DIFICIENTE';
              }
              elseif($cronograma['ultimo_atraso']>60 && $cronograma['ultimo_atraso']<=120){
                  $clasificacion = 'DUDOSO';
              }
              elseif($cronograma['ultimo_atraso']>120){
                  $clasificacion = 'PÉRDIDA';
              }

              $html .= "<tr id='show_data_select' idcredito='{$value->id}'>
                            <td>".($key+1)."</td>
                            <td>C{$value->cuenta}</td>
                            <td>{$value->identificacioncliente}</td>
                            <td>{$value->nombrecliente}</td>
                            <td>{$value->identificacionaval}</td>
                            <td>{$value->nombreaval}</td>
                            <td>{$value->fecha_desembolso}</td>
                            <td style='text-align:right;'>{$value->monto_solicitado}</td>
                            <td style='text-align:right;'>{$value->saldo_pendientepago}</td>
                            <td style='text-align:right;'>{$cronograma['cuota_pendiente']}</td>
                            <td>{$value->frecuencianombre}</td>
                            <td style='text-align:right;'>{$value->cuotas}</td>
                            <td>$cp</td>
                            <td>{$cronograma['ultimo_atraso']}</td>
                            <td>{$clasificacion}</td>
                            <td>{$value->nombreproductocredito}</td>
                            <td>{$value->nombremodalidadcredito}</td>
                            <td>{$value->telefonocliente}</td>
                            <td>{$value->direccioncliente}, {$value->ubigeonombre}</td>
                        </tr>";
              $total_desembolsado += $value->monto_solicitado;
              $total_saldo += $value->saldo_pendientepago;
              $total_deuda += $value->total_pendientepago;
          }
          if(count($creditos)==0){
              $html.= '<tr><td colspan="17" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }
              $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="7" style="background-color: #144081 !important;text-align:right;color:#fff !important;">TOTAL S/.</td>
                  <td style="background-color: #144081 !important;text-align:right;color:#fff !important;">'.number_format($total_desembolsado, 2, '.', '').'</td>
                  <td style="background-color: #144081 !important;text-align:right;color:#fff !important;">'.number_format($total_saldo, 2, '.', '').'</td>
                  <td style="background-color: #144081 !important;text-align:right;color:#fff !important;">'.number_format($total_deuda, 2, '.', '').'</td>
                  <td colspan="9" style="background-color: #144081 !important;"></td>
                </tr>';
          return array(
            'html' => $html
          );
          
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
        
        return view(sistema_view().'/carteracredito/desembolsar',[
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
            return view(sistema_view().'/carteracredito/exportar',[
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
        
            $pdf = PDF::loadView(sistema_view().'/carteracredito/exportar_pdf',[
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
                  'aval.identificacion as identificacionaval',
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
