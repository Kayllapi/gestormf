<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Prestamo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class AhorroConfirmacionController extends Controller
{
    public function index(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $where = [];
        if($request->tipoahorro!=''){ $where[] = ['s_prestamo_tipoahorro.id',$request->tipoahorro]; }
        $where[] = ['s_prestamo_ahorro.codigo','LIKE','%'.$request->codigoahorro.'%'];
        $where[] = ['cliente.identificacion','LIKE','%'.$request->identificacion.'%'];
        $where[] = ['cliente.nombre','LIKE','%'.$request->cliente.'%'];
      
        $where1 = [];
        if($request->tipoahorro!=''){ $where1[] = ['s_prestamo_tipoahorro.id',$request->tipoahorro]; }
        $where1[] = ['s_prestamo_ahorro.codigo','LIKE','%'.$request->codigoahorro.'%'];
        $where1[] = ['cliente.identificacion','LIKE','%'.$request->identificacion.'%'];
        $where1[] = ['cliente.nombre','LIKE','%'.$request->cliente.'%'];
      
        $prestamoahorros = DB::table('s_prestamo_ahorro')
              ->join('users as asesor', 'asesor.id', 's_prestamo_ahorro.idasesor')
              ->join('users as cliente', 'cliente.id', 's_prestamo_ahorro.idcliente')
              ->join('s_prestamo_tipoahorro', 's_prestamo_tipoahorro.id', 's_prestamo_ahorro.idprestamo_tipoahorro')
              ->join('s_moneda', 's_moneda.id', 's_prestamo_ahorro.idmoneda')
              ->where($where)
              ->where('s_prestamo_ahorro.idtienda', $idtienda)
              ->where('s_prestamo_ahorro.idestado', 1)
              ->whereIn('s_prestamo_ahorro.idestadoahorro', [4,5])
              ->where('s_prestamo_ahorro.idcajero', Auth::user()->id)
              ->orWhere($where1)
              ->where('s_prestamo_ahorro.idtienda', $idtienda)
              ->where('s_prestamo_ahorro.idestado', 1)
              ->whereIn('s_prestamo_ahorro.idestadoahorro', [4,5])
              ->where('s_prestamo_ahorro.idcajero', Auth::user()->id)
              ->orWhere($where1)
              ->where('s_prestamo_ahorro.idtienda', $idtienda)
              ->where('s_prestamo_ahorro.idestado', 1)
              ->whereIn('s_prestamo_ahorro.idestadoahorro', [3])
              ->select(
                  's_prestamo_ahorro.*',
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  's_prestamo_tipoahorro.nombre as tipoahorronombre',
                  'cliente.identificacion as clienteidentificacion',
                  's_moneda.simbolo as monedasimbolo',
                  DB::raw('IF(cliente.idtipopersona = 1 || cliente.idtipopersona = 3,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
              )
              ->orderBy('s_prestamo_ahorro.id','desc')
              ->paginate(10);

        return view('layouts/backoffice/tienda/sistema/prestamo/ahorroconfirmacion/index',[
            'tienda' => $tienda,
            'prestamoahorros' => $prestamoahorros
        ]);
    }

    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    public function show(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    public function edit(Request $request, $idtienda, $id)
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
          $prestamoahorro = DB::table('s_prestamo_ahorro')
              ->leftjoin('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_ahorro.idprestamo_frecuencia')
              ->join('s_moneda', 's_moneda.id', 's_prestamo_ahorro.idmoneda')
              ->join('users as asesor', 'asesor.id', 's_prestamo_ahorro.idasesor')
              ->join('users as cliente', 'cliente.id', 's_prestamo_ahorro.idcliente')
              ->leftjoin('users as conyuge', 'conyuge.id', 's_prestamo_ahorro.idconyuge')
              ->join('users as supervisor', 'supervisor.id', 's_prestamo_ahorro.idsupervisor')
              ->leftjoin('ubigeo as cliubigeo', 'cliubigeo.id', 'cliente.idubigeo')
              ->leftjoin('ubigeo as conyugeubigeo', 'conyugeubigeo.id', 'conyuge.idubigeo')
              ->leftjoin('users as cajero', 'cajero.id', 's_prestamo_ahorro.idcajero')
              ->leftjoin('users as beneficiario', 'beneficiario.id', 's_prestamo_ahorro.idbeneficiario')
              ->leftjoin('ubigeo as clienteubigeo', 'clienteubigeo.id', 'cliente.idubigeo')
              ->leftjoin('ubigeo as beneficiarioubigeo', 'beneficiarioubigeo.id', 'beneficiario.idubigeo')
              ->leftJoin('s_agencia as agencia', 'agencia.id', 's_prestamo_ahorro.facturacion_idagencia')
              ->leftJoin('ubigeo as agenciaubigeo', 'agenciaubigeo.id', 'agencia.idubigeo')
              ->leftjoin('s_prestamo_estadocivil as clienteestadocivil', 'clienteestadocivil.id', 'cliente.idestadocivil')
              ->leftJoin('s_tipocomprobante as tipocomprobante', 'tipocomprobante.id', 's_prestamo_ahorro.facturacion_idtipocomprobante')
              ->join('tienda', 'tienda.id', 's_prestamo_ahorro.idtienda')
              ->leftJoin('s_prestamo_tipoahorro', 's_prestamo_tipoahorro.id', 's_prestamo_ahorro.idprestamo_tipoahorro')
              ->where([
                ['s_prestamo_ahorro.id', $id],
                ['s_prestamo_ahorro.idtienda', $idtienda]
              ])
              ->select(
                  's_prestamo_ahorro.*',
                  's_prestamo_frecuencia.nombre as frecuencia_nombre',
                  's_prestamo_frecuencia.id as idprestamo_frecuencia',
                  's_moneda.simbolo as monedasimbolo',
                  's_moneda.nombre as monedanombre',
                  'tienda.nombre as tiendanombre',
                  'cliubigeo.id as cliente_idubigeo',
                  'cliubigeo.nombre as cliente_ubigeonombre',
                  'cliente.identificacion as clienteidentificacion',
                  'cliente.nombre as clientenombre',
                  'cliente.apellidos as clienteapellidos',
                  'cliente.numerotelefono as cliente_numerotelefono',
                  'cliente.direccion as cliente_direccion',
                  'clienteestadocivil.nombre as cliente_estadocivil',
                  'conyuge.identificacion as conyugeidentificacion',
                  'conyuge.nombre as conyugenombre',
                  'conyuge.apellidos as conyugeapellidos',
                  'conyuge.direccion as conyugedireccion',
                  'conyugeubigeo.nombre as conyugeubigeonombre',
              DB::raw('CONCAT(clienteubigeo.distrito, ", ", clienteubigeo.provincia, ", ", clienteubigeo.departamento) as clienteubigeoubicacion'),
              'conyuge.identificacion as conyugeidentificacion',
              'conyuge.nombre as conyugenombre',
              'conyuge.apellidos as conyugeapellidos',
              'beneficiario.identificacion as beneficiarioidentificacion',
              'beneficiario.nombre as beneficiarionombre',
              'beneficiario.apellidos as beneficiarioapellidos',
              'beneficiario.direccion as beneficiariodireccion',
              'beneficiario.referencia as beneficiarioreferencia',
              'beneficiarioubigeo.nombre as beneficiarioubigeonombre',
              'asesor.identificacion as asesoridentificacion',
              'asesor.nombre as asesornombre',
              'asesor.apellidos as asesorapellidos',
                  'asesor.numerotelefono as asesor_numerotelefono',
              's_moneda.simbolo as monedasimbolo',
              's_prestamo_tipoahorro.nombre as tipoahorronombre',
              DB::raw('IF(asesor.idtipopersona = 1 || asesor.idtipopersona = 3,
                  CONCAT(asesor.apellidos, ", ", asesor.nombre),
                  CONCAT(asesor.apellidos)) as asesor_nombre'),
              DB::raw('IF(cliente.idtipopersona = 1 || cliente.idtipopersona = 3,
                  CONCAT(cliente.identificacion, " - ", cliente.apellidos, ", ", cliente.nombre),
                  CONCAT(cliente.identificacion, " - ", cliente.apellidos)) as cliente_nombre'),
              DB::raw('IF(conyuge.idtipopersona = 1 || conyuge.idtipopersona = 3,
                  CONCAT(conyuge.identificacion, " - ", conyuge.apellidos, ", ", conyuge.nombre),
                  CONCAT(conyuge.identificacion, " - ", conyuge.apellidos)) as conyuge_nombre'),
              DB::raw('IF(beneficiario.idtipopersona = 1 || beneficiario.idtipopersona = 3,
                  CONCAT(beneficiario.identificacion, " - ", beneficiario.apellidos, ", ", beneficiario.nombre),
                  CONCAT(beneficiario.identificacion, " - ", beneficiario.apellidos)) as beneficiario_nombre'),
                  DB::raw('IF(supervisor.idtipopersona = 1 || supervisor.idtipopersona = 3,
                      CONCAT(supervisor.nombre, ", ", supervisor.apellidos),
                      CONCAT(supervisor.apellidos)) as supervisor_nombre'),
                  DB::raw('IF(cajero.idtipopersona = 1 || cajero.idtipopersona = 3,
                      CONCAT(cajero.nombre, ", ", cajero.apellidos),
                      CONCAT(cajero.apellidos)) as cajero_nombre'),
                'clienteubigeo.nombre as facturacion_cliente_ubigeonombre',
                'clienteubigeo.codigo as facturacion_cliente_ubigeocodigo',
                'agencia.ruc as facturacion_agenciaruc',
                'agencia.razonsocial as facturacion_agenciarazonsocial',
                'agencia.nombrecomercial as facturacion_agencianombrecomercial',
                'agencia.direccion as facturacion_agenciadireccion',
                'agencia.logo as facturacion_agencialogo',
                'agencia.representante_dni as facturacion_representante_dni',
                'agencia.representante_nombre as facturacion_representante_nombre',
                'agencia.representante_apellidos as facturacion_representante_apellidos',
                'agencia.representante_cargo as facturacion_representante_cargo',
                'agenciaubigeo.nombre as facturacion_agenciaubigeonombre',
                'tipocomprobante.nombre as facturacion_tipocomprobantenombre',
            )
            ->first();
      
        if ($request->input('view') == 'confirmar') {
            $agencias = DB::table('s_agencia')->where('s_agencia.idtienda', $idtienda)->get();
            $tipocomprobante = DB::table('s_tipocomprobante')->get();
            $monedas = DB::table('s_moneda')->get();
            return view('layouts/backoffice/tienda/sistema/prestamo/ahorroconfirmacion/confirmar', [
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
                'agencias' => $agencias,
                'tipocomprobante' => $tipocomprobante,
                'monedas' => $monedas,
            ]);
        }
        elseif ($request->input('view') == 'anularaprobacion') {
            return view('layouts/backoffice/tienda/sistema/prestamo/ahorroconfirmacion/anularaprobacion', [
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
            ]);
        }
        elseif ($request->input('view') == 'detalleaprobacion') {
            return view('layouts/backoffice/tienda/sistema/prestamo/ahorroconfirmacion/detalleaprobacion', [
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
            ]);
        }
        elseif ($request->input('view') == 'anularconfirmacion') {
            return view('layouts/backoffice/tienda/sistema/prestamo/ahorroconfirmacion/anularconfirmacion', [
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
            ]);
        }
        elseif ($request->input('view') == 'detalleconfirmacion') {
            return view('layouts/backoffice/tienda/sistema/prestamo/ahorroconfirmacion/detalleconfirmacion', [
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
            ]);
        }
        elseif ($request->input('view') == 'ticket') {
            return view('layouts/backoffice/tienda/sistema/prestamo/ahorroconfirmacion/ticket', [
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
            ]);
        }
        elseif ($request->input('view') == 'ticketpdf') {
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamo/ahorroconfirmacion/ticketpdf', [
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
            ]);
            return $pdf->stream('Ticket.pdf');
        }
        elseif ($request->input('view') == 'documento') {
            $documentos = DB::table('s_prestamo_documento')->where('s_prestamo_documento.idtienda', $idtienda)->get();
            return view('layouts/backoffice/tienda/sistema/prestamo/ahorroconfirmacion/documento',  [
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
                'documentos' => $documentos,
            ]);
        }
        elseif ($request->input('view') == 'documentopdf') {
            $prestamodocumento = DB::table('s_prestamo_documento')
                ->where([
                  ['s_prestamo_documento.idtienda', $idtienda],
                  ['s_prestamo_documento.id', $request->iddocumento]
                ])
                ->first();

            // fecha de confirmacion
            $diassemana = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            $w = date("w", strtotime($prestamoahorro->fechaconfirmado));
            $d = date("d", strtotime($prestamoahorro->fechaconfirmado));
            $n = date("n", strtotime($prestamoahorro->fechaconfirmado));
            $y = date("Y", strtotime($prestamoahorro->fechaconfirmado));
            $ahorro_fechaconfirmacion = $diassemana[$w]." ".$d." de ".$meses[$n-1]. " de ".$y;
            // fecha ultima cuota
            $w = date("w", strtotime($prestamoahorro->ultimafecha));
            $d = date("d", strtotime($prestamoahorro->ultimafecha));
            $n = date("n", strtotime($prestamoahorro->ultimafecha));
            $y = date("Y", strtotime($prestamoahorro->ultimafecha));
      
            $ahorro_ultimacuota = $diassemana[$w]." ".$d." de ".$meses[$n-1]. " de ".$y;

            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamo/ahorroconfirmacion/documentopdf', [
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
                'prestamodocumento' => $prestamodocumento,
                'ahorro_fechaconfirmacion' => $ahorro_fechaconfirmacion,
                'ahorro_ultimacuota' => $ahorro_ultimacuota,
            ]);
          return $pdf->stream('Documento.pdf');
        }
        elseif ($request->input('view') == 'cronograma') {
            return view('layouts/backoffice/tienda/sistema/prestamo/ahorroconfirmacion/cronograma', [
                'tienda' => $tienda,
                'prestamoahorro' => $prestamoahorro,
            ]);
        }
        elseif ($request->input('view') == 'cronogramapdf') {
            $prestamoahorrodetalle = DB::table('s_prestamo_ahorrodetalle')
                ->where('s_prestamo_ahorrodetalle.idprestamo_ahorro', $prestamoahorro->id)
                ->orderBy('s_prestamo_ahorrodetalle.numero','asc')
                ->get();

            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamo/ahorroconfirmacion/cronogramapdf', [
                'prestamoahorro' => $prestamoahorro,
                'prestamoahorrodetalle' => $prestamoahorrodetalle,
                'tienda' => $tienda
            ]);
            return $pdf->stream('Cronograma.pdf');
        }
        elseif ($request->input('view') == 'tarjeta') {
            return view('layouts/backoffice/tienda/sistema/prestamo/ahorroconfirmacion/tarjeta', [
                'prestamoahorro' => $prestamoahorro,
                'tienda' => $tienda
            ]);
        }
        elseif ($request->input('view') == 'tarjetapdf') {
            $prestamoahorrodetalle = DB::table('s_prestamo_ahorrodetalle')
                ->where('s_prestamo_ahorrodetalle.idprestamo_ahorro', $prestamoahorro->id)
                ->orderBy('s_prestamo_ahorrodetalle.numero','asc')
                ->get();
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamo/ahorroconfirmacion/tarjetapdf', [
                'prestamoahorro' => $prestamoahorro,
                'prestamoahorrodetalle' => $prestamoahorrodetalle,
                'tienda' => $tienda
            ]);
            return $pdf->stream('Cronograma.pdf');
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(), $idtienda);

        if ($request->input('view') == 'confirmar') {

            $rules = [
                'cliente_direccion' => 'required',
                'idubigeo' => 'required',
                'idagencia' => 'required',
                'idmoneda' => 'required',
                'idtipocomprobante' => 'required',
            ];
            
            $idcobrarganancia = 0;
            if($request->idprestamo_tipoahorro==1){
                $rules = array_merge($rules,[
                  'idcobrarganancia' => 'required'
                ]);
                $idcobrarganancia = $request->idcobrarganancia;
            }
          
            $messages = [
                'cliente_direccion.required' => 'La "Dirección" es Obligatorio.',
                'idubigeo.required' => 'El "Ubigeo" es Obligatorio.',
                'idagencia.required' => 'La "Agencia" es Obligatorio.',
                'idtipocomprobante.required' => 'El "Tipo de Comprobante" es Obligatorio.',
                'facturacion_montorecibido.required' => 'El "Monto recibido" es Obligatorio.',
                'idmoneda.required' => 'La "Moneda" es Obligatorio.',
                'idcobrarganancia.required' => 'El "Cobrar Ganancia" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);
          
            // aperturacaja
            $idaperturacierre = 0;
            $caja = caja($idtienda, Auth::user()->id);
            if($caja['resultado'] != 'ABIERTO'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Caja debe estar Aperturada.'
                ]);
            }
            $idaperturacierre = $caja['apertura']->id;
            // fin aperturacaja
          
            $cliente = DB::table('users')
              ->leftjoin('ubigeo', 'ubigeo.id', 'users.idubigeo')
              ->where('users.id', $request->idcliente)
              ->select(
                'users.*',
                'ubigeo.nombre as ubigeo_nombre',
                'ubigeo.codigo as ubigeo_codigo'
              )
              ->first();
          
            DB::table('s_prestamo_ahorro')->whereId($id)->update([
                'fechaconfirmado' => Carbon::now(),
                'facturacion_cliente_identificacion' => $cliente->identificacion,
                'facturacion_cliente_nombre' => $cliente->nombre,
                'facturacion_cliente_apellidos' => $cliente->apellidos,
                'facturacion_cliente_direccion' => $request->cliente_direccion,
                'facturacion_idagencia' => $request->idagencia,
                'facturacion_idtipocomprobante' => $request->idtipocomprobante,
                'facturacion_idubigeo' => $request->idubigeo,
                'facturacion_idaperturacierre' => $idaperturacierre,
                'idcajero' => Auth::user()->id,
                'idestadocobrarganancia' => $idcobrarganancia,
                'idestadoahorro' => 4,
                'idestadoconfirmacion' => 1,
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha confirmado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'anularaprobacion') {
            DB::table('s_prestamo_ahorro')->whereId($id)->update([
                'fechaanuladoaprobacion' => Carbon::now(),
                'idestadoconfirmacion' => 2
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha Anulado la Aprobación correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'anularconfirmacion') {
            DB::table('s_prestamo_ahorro')->whereId($id)->update([
                'fechaanuladoconfirmacion' => Carbon::now(),
                'idestadoconfirmacion' => 3
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha Anulado la Confirmación correctamente.'
            ]);
        }
    }

    public function destroy(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }
}
