<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NuevoSistema\ReporteFacturacionGuiaRemisionExport;

class  ReportefacturacionguiaremisionController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        $guiasDeRemision = $this->getComprobantes($request, $tienda)->get();

        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/reporte/reportefacturacionguiaremision/tabla', [
                'tienda' => $tienda,
                'guias_de_remision' => $guiasDeRemision,
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
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')
            ->leftJoin('ubigeo', 'ubigeo.id', 'tienda.idubigeo')
            ->select(
                'tienda.*',
                'ubigeo.nombre as ubigeonombre',
            )
            ->where('tienda.id',$idtienda)
            ->first();
         
        $guiasDeRemision = $this->getComprobantes($request, $tienda)->get();
  
        if ($id == 'showtabla') {
            return view(sistema_view().'/reporte/reportefacturacionguiaremision/tabla-data', [
                'tienda' => $tienda,
                'guias_de_remision' => $guiasDeRemision 
            ]);
        } else if($id == 'showtablapdf') {
            $pdf = PDF::setPaper('legal', 'landscape')
            ->loadView(sistema_view().'/reporte/reportefacturacionguiaremision/tablapdf', [
                'tienda'      => $tienda,
                'guias_de_remision' => $guiasDeRemision 
            ]);

            return $pdf->stream('REPORTE_DE_FACTUAS_BOLETAS.pdf');
        } else if ($id == 'showexcel') {
            return Excel::download(
                new ReporteFacturacionGuiaRemisionExport(
                    $guiasDeRemision,
                    $request->fechainicio,
                    $request->fechafin,
                    'REPORTE DE GUIA DE REMISION'
                ),
                'reporte_guia_remision.xls'
            );
        }
    }

    public function getComprobantes(Request $request, $tienda)
    {
        $where = [];
        if($request->input('fechainicio')!=''){
            $where[] = ['s_facturacionguiaremision.venta_fechaemision','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
            $where[] = ['s_facturacionguiaremision.venta_fechaemision','<=',$request->input('fechafin').' 23:59:59'];
        }

        $guiasDeRemision  = DB::table('s_facturacionguiaremision')
            ->join('users as responsable', 'responsable.id', 's_facturacionguiaremision.idusuarioresponsable')
            ->leftJoin('users as transportista', 'transportista.id', 's_facturacionguiaremision.idusuariochofer')
            ->join('s_sunat_motivotraslado', 's_sunat_motivotraslado.codigo', 's_facturacionguiaremision.envio_modtraslado')
            ->where('s_facturacionguiaremision.idtienda', $tienda->id)
            //->where('s_facturacionguiaremision.idsucursal', Auth::user()->idsucursal)
            ->where($where)
            ->select(
                's_facturacionguiaremision.*',
                'responsable.nombre as responsablenombre',
                's_sunat_motivotraslado.nombre as motivotrasladonombre',
                'transportista.nombrecompleto as transportista'
            )
            ->orderBy('s_facturacionguiaremision.id','desc');

        return $guiasDeRemision;
    }

    public function edit(Request $request, $idtienda, $idmarca)
    {
        
    }

    public function update(Request $request, $idtienda, $idmarca)
    {
        
    }

    public function destroy(Request $request, $idtienda, $idmarca)
    {
        
    }
}
