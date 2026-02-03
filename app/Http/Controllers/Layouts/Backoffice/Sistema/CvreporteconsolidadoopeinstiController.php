<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class CvreporteconsolidadoopeinstiController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            $agencias = DB::table('tienda')->get();
            return view(sistema_view().'/cvreporteconsolidadoopeinsti/tabla',[
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
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view') == 'pdf_reporte'){
            $co_actual = cvconsolidadooperaciones($tienda,$request->idagencia,$request->corte);
            // $co_actual = cvconsolidadooperacionesNuevo($request->idagencia,$request->corte);
            $date = Carbon::createFromFormat('Y-m-d', $request->corte);
            $date->subDay(); // Subtracts 1 day
            $co_anterior = DB::table('cvarqueocaja')
                ->where('idagencia',$request->idagencia)
                // ->where('corte',$date->format('Y-m-d'))
                ->orderByDesc('id')
                ->first();

            $pdf = PDF::loadView(sistema_view().'/cvreporteconsolidadoopeinsti/pdf_reporte', compact(
                'co_actual',
                'co_anterior',
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('REPORTE_CONSOLIDADO_OPE_INST.pdf');
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        
    }


    public function destroy(Request $request, $idtienda, $id)
    {

    }
}
