<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class CobranzacuotaasesorController extends Controller
{
    public function __construct()
    {
        $this->tipo_credito = DB::table('tipo_credito')->get();
    }
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
          
            $creditos = DB::table('credito')
                ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
                ->join('users as cliente','cliente.id','credito.idcliente')
                ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
                ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
                ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                ->where('credito.estado','DESEMBOLSADO')
                ->select(
                    'credito.*',
                    'cliente.identificacion as identificacion',
                    'cliente.nombrecompleto as nombrecliente',
                )
                ->orderBy('credito.fecha_desembolso','asc')
                ->get();
          
            return view(sistema_view().'/cobranzacuotaasesor/tabla',[
              'tienda' => $tienda,
              'creditos' => $creditos,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        //
    }
  
    public function store(Request $request, $idtienda)
    {
        //
    }

    public function show(Request $request, $idtienda, $id)
    {
        //
    }

    public function edit(Request $request, $idtienda, $id)
    {
        //
    }

    public function update(Request $request, $idtienda, $id)
    {
        //
    }

    public function destroy(Request $request, $idtienda, $id)
    {
        //
    }
}
