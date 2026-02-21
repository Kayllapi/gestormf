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

class CarteradeclienteasesorController extends Controller
{
    public function __construct()
    {
    }
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view') == 'tabla'){
            $agencias = DB::table('tienda')->get();
            return view(sistema_view().'/carteradeclienteasesor/tabla',[
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
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showcliente'){
          $where = [];
          $where[] = ['users.idtienda',$request->idagencia];

          if($request->idasesor!='' && $request->idasesor!=0){
              $where[] = ['users.idasesor',$request->idasesor];
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
    }

    public function update(Request $request, $idtienda, $id)
    {
    }

    public function destroy(Request $request, $idtienda, $id)
    {
    }
}
