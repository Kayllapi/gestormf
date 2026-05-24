<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class GestioncobranzaasesorController extends Controller
{
    public function __construct()
    {
        $this->modalidad_credito = DB::table('modalidad_credito')->get();
        $this->forma_credito = DB::table('forma_credito')->get();
        $this->tipo_operacion_credito = DB::table('tipo_operacion_credito')->get();
        $this->forma_pago_credito = DB::table('forma_pago_credito')->get();
        $this->tipo_destino_credito = DB::table('tipo_destino_credito')->get();
    }
    public function index(Request $request,$idtienda)
    {
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            
            $agencias = DB::table('tienda')->get();
          
            return view(sistema_view().'/gestioncobranzaasesor/tabla',[
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
