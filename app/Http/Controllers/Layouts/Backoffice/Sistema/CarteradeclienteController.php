<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use App\Exports\ReportecarteraclienteExport;
use Maatwebsite\Excel\Facades\Excel;

class CarteradeclienteController extends Controller
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
          
            return view(sistema_view().'/carteradecliente/tabla',[
              'tienda' => $tienda,
              'agencias' => $agencias,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
      
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

    /**
     * "99" es el marcador de "INACTIVOS GLOBAL AGENCIA" del selector de Ejecutivo
     * (MasterController::show_asesor): esos clientes tienen idasesor=0, no 99.
     * Mismo criterio que AsignaciondecarteraController ya usa para su propio filtro.
     */
    protected function where_idasesor($idasesor)
    {
        return ['users.idasesor', $idasesor==99 ? 0 : $idasesor];
    }

    /**
     * Asesor a mostrar en el encabezado del pdf/excel. Para "99" no hay un usuario
     * real que buscar, se muestra la misma etiqueta del selector.
     */
    protected function asesor_filtro($idasesor)
    {
        if($idasesor==99){
            return (object) ['usuario' => 'INACTIVOS GLOBAL AGENCIA'];
        }
        return DB::table('users')->whereId($idasesor)->first();
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showcliente'){
          $where = [];
          $where[] = ['users.idtienda',$request->idagencia];

          if($request->idasesor!='' && $request->idasesor!=0){
              $where[] = $this->where_idasesor($request->idasesor);
          }
          
          $where1 = [];
          if($request->idformacredito!='' && $request->idformacredito!=0){
              if($request->idformacredito=='CP'){
                  $where1[] = ['credito.idforma_credito',1];
              }
              elseif($request->idformacredito=='CNP'){
                  $where1[] = ['credito.idforma_credito',2];
              }
              elseif($request->idformacredito=='CC'){
                  $where1[] = ['credito.idforma_credito',3];
              }
          }
         
          
          $users = DB::table('users')
              //->leftjoin('credito','credito.idasesor','users.id')
              ->where('users.idtipousuario',2)
              ->where($where)
              ->select(
                  'users.*',
              )
              ->orderBy('users.nombre','asc')
              ->get();
            $tabla = [];
            $orden = 1;
            foreach($users as $value){
              
                $credito = DB::table('credito')
                    ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                    ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
                    ->leftjoin('users','users.id','credito.idasesor')
                    ->where('credito.idcliente',$value->id)
                    //->where('credito.idestadocredito',1)
                    ->whereIn('credito.idestadocredito',[1,2])
                    ->where('credito.estado','DESEMBOLSADO')
                    ->where($where1)
                    ->select(
                        'credito.*',
                        'credito_prendatario.nombre as nombreproductocredito' ,
                        'forma_pago_credito.nombre as frecuencianombre' ,
                        'users.usuario as codigoasesor',
                    )
                    ->orderBy('id','desc')
                    ->first();
              
              $creditocredito = '';
              $saldo_pendientepago = '';
              $frecuencianombre = '';
              $cuota = '';
              $nombreproductocredito = '';
              $idforma_credito = '';
              $fecha_cancelado = '';
              if($credito!='' || $request->idformacredito==0){
                  if($credito){
                      $creditocredito = $credito->cuenta;
                      $saldo_pendientepago = $credito->saldo_pendientepago;
                      $frecuencianombre = $credito->frecuencianombre;
                      $cuota = $credito->cuotas;
                      $nombreproductocredito = $credito->nombreproductocredito;
                      $idforma_credito = $credito->idforma_credito;
                      $fecha_cancelado = $credito->fecha_cancelado;
                  }
              
                  $users_prestamo = DB::table('s_users_prestamo')->where('s_users_prestamo.id_s_users',$value->id)->first();

                  $direccionnegocio = '';
                  if($users_prestamo){
                      $direccionnegocio = $users_prestamo->direccion_ac_economica;
                  }

                  $cp = '';
                  if($idforma_credito==1){
                      $cp = 'CP';
                  }
                  elseif($idforma_credito==2){
                      $cp = 'CNP';
                  }
                  elseif($idforma_credito==3){
                      $cp = 'CC';
                  }

                  $usersasesor = DB::table('users')->whereId($value->idasesor)->first();
                  $codigoasesor = '';
                  if($usersasesor){
                      $codigoasesor = $usersasesor->usuario;
                  }

                  $tabla[]=[
                      'id'      => $value->id,
                      'orden'   => $orden,
                      'codigo'     => $value->codigo,
                      'doc'     => $value->identificacion,
                      'nombre'  => $value->nombrecompleto,
                      'asesororigen'  => $codigoasesor,
                      // mismo campo (users.origen_global) que usa UsuarioglobalController. El
                      // fallback a 'INACTIVIDAD' solo aplica si el cliente esta realmente en el
                      // pool global (idasesor=0, igual que UsuarioglobalController filtra): un
                      // cliente con asesor asignado nunca paso por ahi, y no tiene motivo.
                      'motivo'  => $value->idasesor==0
                          ? ($value->origen_global!=''?$value->origen_global:'INACTIVIDAD')
                          : ($value->origen_global!=''?$value->origen_global:'--'),
                      'saldo'  => $saldo_pendientepago!=''?$saldo_pendientepago:'0.00',
                      'formapago'  => $frecuencianombre!=''?$frecuencianombre:'--',
                      'cuota'  => $cuota!=''?$cuota:'--',
                      'fomac'  => $cp!=''?$cp:'--',
                      'producto'  => $nombreproductocredito!=''?$nombreproductocredito:'--',
                      'fechacancelado'  => $fecha_cancelado!=''?$fecha_cancelado:'--',
                      'telefono'  => $value->numerotelefono!=''?$value->numerotelefono:'--',
                      'direcciondomicilio'  => $value->direccion,
                      'direccionnegocio'  => $direccionnegocio,
                  ];
                  $orden++;
              }
            }
            
            return response()->json([
                'data'            => $tabla,
            ]);
          
        } 
    }

    public function edit(Request $request, $idtienda, $id)
    {
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
 
        if($request->input('view') == 'exportar') {
            return view(sistema_view().'/carteradecliente/exportar',[
                'tienda' => $tienda,
                'idagencia' => $request->idagencia,
                'idasesor' => $request->idasesor,
                'idformacredito' => $request->idformacredito,
            ]);
        }
        else if( $request->input('view') == 'exportar_pdf' ){
              
            
          
          $where = [];
          $where[] = ['users.idtienda',$request->idagencia];

          if($request->idasesor!='' && $request->idasesor!=0){
              $where[] = $this->where_idasesor($request->idasesor);
          }

          $users = DB::table('users')
              ->where('users.idtipousuario',2)
              ->where($where)
              ->orderBy('users.nombre','asc')
              ->get();

            $agencia = DB::table('tienda')->whereId($request->idagencia)->first();
            $asesor = $this->asesor_filtro($request->idasesor);

            $pdf = PDF::loadView(sistema_view().'/carteradecliente/exportar_pdf',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'users' => $users,
                'asesor' => $asesor,
                'idformacredito' => $request->idformacredito,
            ]); 
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('CARTERA_DE_CREDITO.pdf');
        }  
        else if( $request->input('view') == 'exportar_excel' ){
              
            
          
          $where = [];
          $where[] = ['users.idtienda',$request->idagencia];

          if($request->idasesor!='' && $request->idasesor!=0){
              $where[] = $this->where_idasesor($request->idasesor);
          }

          $users = DB::table('users')
              ->where('users.idtipousuario',2)
              ->where($where)
              ->orderBy('users.nombre','asc')
              ->get();

            $agencia = DB::table('tienda')->whereId($request->idagencia)->first();
            $asesor = $this->asesor_filtro($request->idasesor);


            return Excel::download(
                new ReportecarteraclienteExport(
                    $tienda,
                    $agencia,
                    $users,
                    $request->idformacredito,
                    $asesor,
                    'REPORTE DE CARTERA DE CLIENTE'
                ),
                'reporte_cartera_cliente.xls'
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
