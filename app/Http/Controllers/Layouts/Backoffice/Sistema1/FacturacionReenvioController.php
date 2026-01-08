<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class FacturacionReenvioController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
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
        
        return view('layouts/backoffice/tienda/sistema/facturacionreenvio/index',[
            'tienda' => $tienda,
            'facturas' => $facturas,
        ]);
    }

    public function create(Request $request,$idtienda)
    {

    }

    public function store(Request $request, $idtienda)
    {

    }

    public function show(Request $request, $idtienda, $id)
    {
       $request->user()->authorizeRoles($request->path(),$idtienda);
      
       if ($id == 'show-reenviar') {
        
         $respuesta = consultaCdr($request->idfactura);
         
         if ($respuesta['resultado'] == 'ERROR') {
//            $reenvio = facturador_facturaboleta($request->idfactura);
//            return $reenvio;
           return $respuesta;
         }else { 
           return $respuesta;
         }
         
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
