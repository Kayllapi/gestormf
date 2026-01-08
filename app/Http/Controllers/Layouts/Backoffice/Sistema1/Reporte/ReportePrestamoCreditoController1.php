<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class ReportePrestamoCreditoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$idtienda)
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
      
        if($request->input('view') == 'tablapdf') {
            $where = [];
            $where[] = ['asesor.id',Auth::user()->id];
            if($request->input('idcliente')!=''){
                $where[] = ['cliente.id',$request->input('idcliente')];
            }
            if($request->input('fechainicio')!=''){
                $where[] = ['s_prestamo_credito.fechadesembolsado','>=',$request->input('fechainicio').' 00:00:00'];
            }
            if($request->input('fechafin')!=''){
                $where[] = ['s_prestamo_credito.fechadesembolsado','<=',$request->input('fechafin').' 24:00:00'];
            }
          
            $prestamoscredito = DB::table('s_prestamo_credito')
                ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
                ->join('users as asesor','asesor.id','s_prestamo_credito.idasesor')
                ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
                ->where('s_prestamo_credito.idestado', 1)
                ->where('s_prestamo_credito.idtienda', $idtienda)
                ->where('s_prestamo_credito.idestadocobranza','<>', 2)
                ->where('s_prestamo_credito.idestadocredito', 4)
                ->where('s_prestamo_credito.idestadoaprobacion', 1)
                ->where('s_prestamo_credito.idestadodesembolso', 1)
                ->where($where)
                ->select(
                    's_prestamo_credito.*',
                    'cliente.identificacion as cliente_identificacion',
                    'cliente.numerotelefono as cliente_numerotelefono',
                    'cliente.direccion as cliente_direccion',
                    DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente'),
                    's_moneda.simbolo as monedasimbolo',
                )
                ->orderBy('s_prestamo_credito.fechadesembolsado','desc')
                ->get();
   
            $cliente = DB::table('users')->where('idtienda',$idtienda)->whereId($request->input('idcliente'))->first();
           

            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/reporteprestamocredito/tablapdf',[
                'tienda' => $tienda,
                'cliente' => $cliente,
                'fechainicio' => $request->input('fechainicio'),
                'fechafin' => $request->input('fechafin'),
                'prestamoscredito' => $prestamoscredito,
            ]);
            return $pdf->stream('Reporte_Cobranza.pdf');
        }
        else{
            return view('layouts/backoffice/tienda/sistema/reporteprestamocredito/index',[
              'tienda' => $tienda,
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
