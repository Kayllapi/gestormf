<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;


use PDF;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\NuevoSistema\ReporteRespuestaSunat;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class  ReportesunatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        $comprobantes = $this->getComprobantes($request, $tienda)->get();
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/reportesunat/tabla',[
                'tienda' => $tienda,
                'comprobantes' => $comprobantes,
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

        $comprobantes = $this->getComprobantes($request, $tienda)->get();
        if ($id == 'showtabla') {
            return view(sistema_view().'/reportesunat/tabla-data',[
                'tienda' => $tienda,
                'comprobantes' => $comprobantes,
            ]);
        } else if($id == 'showtablapdf') {
          
            $pdf = PDF::setPaper('legal', 'landscape')
            ->loadView(sistema_view().'/reportesunat/tablapdf',[
                'tienda'      => $tienda,
                'comprobantes' => $comprobantes,
            ]);

            return $pdf->stream('REPORTE_DE_FACTUAS_BOLETAS.pdf');
        } else if ($id == 'showexcel') {
            return Excel::download(
                new ReporteRespuestaSunat(
                    $comprobantes,
                    $request->fechainicio,
                    $request->fechafin,
                    'REPORTE RESPUESTA SUNAT'
                ),
                'reporte_respuesta_sunat.xls'
            );
        }
    }

    public function getComprobantes(Request $request, $tienda)
    {
        $where  = [];
        if($request->input('comprobante')== '01' or $request->input('comprobante')== '03' ){
            $where[] = ['s_facturacionboletafactura.venta_tipodocumento',$request->input('comprobante')];
        }elseif($request->input('comprobante')== '07' ){
            $where[] = ['s_facturacionnotacredito.notacredito_tipodocumento',$request->input('comprobante')];
        }elseif($request->input('comprobante')== '08' ){
            $where[] = ['s_facturacionnotadebito.notadebito_tipodocumento',$request->input('comprobante')];
        }elseif($request->input('comprobante')== '09' ){
            $where[] = ['s_facturacionguiaremision.despacho_tipodocumento',$request->input('comprobante')];
        }elseif($request->input('comprobante')== 'RA' ){
            $where[] = ['s_facturacionrespuesta.s_idfacturacioncomunicacionbaja','>',0];
        }elseif($request->input('comprobante')== 'RC' ){
            $where[] = ['s_facturacionrespuesta.s_idfacturacionresumendiario','>',0];
        }
      
        if($request->input('idestado')!=''){
            $where[] = ['s_facturacionrespuesta.estado',$request->input('idestado')];
        }
      
        if($request->input('fechainicio')!=''){
            $where[] = ['s_facturacionrespuesta.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
            $where[] = ['s_facturacionrespuesta.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
        }
      
        if($request->input('idagencia')!=''){
            $where[] = ['s_facturacioncomunicacionbaja.idagencia',$request->input('idagencia')];
        }
      
      return DB::table('s_facturacionrespuesta')
              ->leftJoin('s_facturacionboletafactura','s_facturacionboletafactura.id','s_facturacionrespuesta.s_idfacturacionboletafactura')
              ->leftJoin('s_facturacionnotacredito','s_facturacionnotacredito.id','s_facturacionrespuesta.s_idfacturacionnotacredito')
              ->leftjoin('s_facturacionnotadebito','s_facturacionnotadebito.id','s_facturacionrespuesta.s_idfacturacionnotadebito')
              ->leftjoin('s_facturacionguiaremision','s_facturacionguiaremision.id','s_facturacionrespuesta.s_idfacturacionguiaremision')
              ->leftjoin('s_facturacionresumendiario','s_facturacionresumendiario.id','s_facturacionrespuesta.s_idfacturacionresumendiario')
              ->leftjoin('s_facturacionresumendiariodetalle','s_facturacionresumendiariodetalle.idfacturacionresumendiario','s_facturacionresumendiario.id')
              ->leftjoin('s_facturacioncomunicacionbaja','s_facturacioncomunicacionbaja.id','s_facturacionrespuesta.s_idfacturacioncomunicacionbaja')
              ->leftjoin('s_facturacioncomunicacionbajadetalle','s_facturacioncomunicacionbajadetalle.idfacturacioncomunicacionbaja','s_facturacioncomunicacionbaja.id')
              ->where($where)
              ->where('s_facturacionrespuesta.idtienda', $tienda->id)
              ->select(
                       's_facturacionrespuesta.*',
                       's_facturacionboletafactura.emisor_razonsocial as emisor_razonsocial',
                       's_facturacionboletafactura.venta_tipodocumento as venta_tipodocumento',
                       's_facturacionnotacredito.notacredito_tipodocumento as notacredito_tipodocumento',
                       's_facturacionnotadebito.notadebito_tipodocumento as notadebito_tipodocumento',
                       's_facturacionguiaremision.despacho_tipodocumento as despacho_tipodocumento',
                       's_facturacionresumendiariodetalle.tipodocumento as tipodocumentoresumen',
                       's_facturacioncomunicacionbajadetalle.tipodocumento as tipodocumentobaja'
                )
             ->orderBy('s_facturacionrespuesta.id','desc');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }
}
