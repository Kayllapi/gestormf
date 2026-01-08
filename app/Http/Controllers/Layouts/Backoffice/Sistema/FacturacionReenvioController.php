<?php


namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use Auth;
use Hash;
use DB;
use PDF; 
use Mail;
use NumeroALetras;

class FacturacionReenvioController extends Controller
{
    public function index(Request $request,$idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      
      $tienda = DB::table('tienda')->whereId($idtienda)->first();

      if($request->input('view') == 'tabla'){
          return view(sistema_view().'/facturacionreenvio/tabla',[
              'tienda' => $tienda,
          ]);
       }
    }

    public function create(Request $request,$idtienda)
    {
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      if( $request->view == 'validacion'){
        return view(sistema_view().'/facturacionreenvio/validacion',[
          'tienda' => $tienda,
        ]);
      }
    }

    public function store(Request $request, $idtienda)
    {
      if( $request->view == 'validacomprobante' ){
        
        $where[] = ['s_facturacionboletafactura.idtienda', $idtienda];
        $where[] = ['s_facturacionboletafactura.venta_tipodocumento', $request->tipodocumento];
        $where[] = ['s_facturacionboletafactura.emisor_ruc', $request->ruc_emisor];
        $where[] = ['s_facturacionboletafactura.venta_serie', $request->serie_comprobante];
        $where[] = ['s_facturacionboletafactura.venta_correlativo', $request->numero_comprobante];
        $where[] = ['s_facturacionboletafactura.venta_montoimpuestoventa', $request->monto_comprobante];

        $cpe = DB::table('s_facturacionboletafactura')
                ->join('s_facturacionrespuesta', 's_facturacionrespuesta.id', 's_facturacionboletafactura.idfacturacionrespuesta')
                
                ->where($where)
                ->whereRaw(" DATE_FORMAT(s_facturacionboletafactura.venta_fechaemision, '%Y-%m-%d') = '{$request->fechaemision_comprobante}' ")
                ->select(
                  's_facturacionboletafactura.*',
                  's_facturacionrespuesta.estado as estado',
                  's_facturacionrespuesta.codigo as codigo'
                )
                ->first();
        if($cpe){
          $respuesta = consultaCdr($cpe->id);
         
         if ($respuesta['resultado'] == 'ERROR') {
           return $respuesta;
         }else { 
           return $respuesta;
         }
        }else{
          return [
            'resultado'                     => 'ERROR',
            'mensaje'                       => 'Comprobante no Encontrado',
          ];
        }

      }
    }

    public function show(Request $request, $idtienda, $id)
    {
       $request->user()->authorizeRoles($request->path(),$idtienda);
       $tienda = DB::table('tienda')->whereId($idtienda)->first();
       if ($id == 'show-reenviar') {
        
         $respuesta = consultaCdr($request->idfactura);
         
         if ($respuesta['resultado'] == 'ERROR') {
//            $reenvio = facturador_facturaboleta($request->idfactura);
//            return $reenvio;
           return $respuesta;
         }else { 
           return $respuesta;
         }
         
       }else if ($id == 'show-index') {
          is_null($request->fechainicio) || $where[] = ['s_facturacionboletafactura.venta_fechaemision','>=',$request->input('fechainicio').' 00:00:00'];
          is_null($request->fechafin) || $where[] = ['s_facturacionboletafactura.venta_fechaemision','<=',$request->input('fechafin').' 23:59:59'];
       
          $facturas = [];
          if($request->fechainicio!='' and $request->fechafin!=''){
            $facturas = DB::table('s_facturacionboletafactura')
                ->join('s_facturacionrespuesta', 's_facturacionrespuesta.id', 's_facturacionboletafactura.idfacturacionrespuesta')
                //->whereIn('s_facturacionrespuesta.estado', ['RECHAZADA', 'OBSERVACIONES', 'EXCEPCION'])
                //->where('s_facturacionrespuesta.codigo', '<>', '1033')
                ->where('s_facturacionboletafactura.idtienda', $idtienda)
                ->where($where)
                ->select(
                  's_facturacionboletafactura.*',
                  's_facturacionrespuesta.estado as estado',
                  's_facturacionrespuesta.codigo as codigo'
                )
                ->get();
          }
         
          return view(sistema_view().'/facturacionreenvio/tabla-contenido',[
              'tienda' => $tienda,
              'facturas' => $facturas,
          ]);
       }
    }

    public function edit(Request $request, $idtienda, $idmarca)
    {

    }

    public function update(Request $request, $idtienda, $s_idmarca)
    {

    }

    public function destroy(Request $request, $idtienda, $s_idmarca)
    {

    }
}
