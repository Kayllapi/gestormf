<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use PDF;
use DB;

class PrestamoDesembolsoController extends Controller
{
    public function index(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $where = [];
        if($request->tipocredito!=''){ $where[] = ['idprestamo_tipocredito',$request->tipocredito]; }
        //$where[] = ['s_prestamo_credito.codigo','LIKE','%'.$request->codigocredito.'%'];
        $where[] = ['cliente.identificacion','LIKE','%'.$request->identificacion.'%'];
        $where[] = ['cliente.nombre','LIKE','%'.$request->cliente.'%'];
        if($request->frecuencia!=''){ $where[] = ['idprestamo_frecuencia',$request->frecuencia]; }
      
        $where1 = [];
        if($request->tipocredito!=''){ $where1[] = ['idprestamo_tipocredito',$request->tipocredito]; }
        //$where1[] = ['s_prestamo_credito.codigo','LIKE','%'.$request->codigocredito.'%'];
        $where1[] = ['cliente.identificacion','LIKE','%'.$request->identificacion.'%'];
        $where1[] = ['cliente.apellidos','LIKE','%'.$request->cliente.'%'];
        if($request->frecuencia!=''){ $where1[] = ['idprestamo_frecuencia',$request->frecuencia]; }
      
        /*$prestamocreditos = DB::table('s_prestamo_creditogrupal')
              ->join('users as asesor', 'asesor.id', 's_prestamo_creditogrupal.idasesor')
              ->join('users as cliente', 'cliente.id', 's_prestamo_creditogrupal.idcliente')
              ->join('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_creditogrupal.idprestamo_frecuencia')
              ->join('s_prestamo_tipocredito', 's_prestamo_tipocredito.id', 's_prestamo_creditogrupal.idprestamo_tipocredito')
              ->join('s_moneda', 's_moneda.id', 's_prestamo_creditogrupal.idmoneda')
              ->where($where)
              ->where('s_prestamo_creditogrupal.idtienda', $idtienda)
              ->where('s_prestamo_creditogrupal.idestado', 1)
              ->whereIn('s_prestamo_creditogrupal.idestadocredito', [4,5])
              ->where('s_prestamo_creditogrupal.idcajero', Auth::user()->id)
              ->orWhere($where1)
              ->where('s_prestamo_creditogrupal.idtienda', $idtienda)
              ->where('s_prestamo_creditogrupal.idestado', 1)
              ->whereIn('s_prestamo_creditogrupal.idestadocredito', [4,5])
              ->where('s_prestamo_creditogrupal.idcajero', Auth::user()->id)
              ->orWhere($where1)
              ->where('s_prestamo_creditogrupal.idtienda', $idtienda)
              ->where('s_prestamo_creditogrupal.idestado', 1)
              ->whereIn('s_prestamo_creditogrupal.idestadocredito', [3])
              ->select(
                  's_prestamo_creditogrupal.id as id',
                  's_prestamo_creditogrupal.codigo as codigo',
                  's_prestamo_creditogrupal.monto as monto',
                  's_prestamo_creditogrupal.numerocuota as numerocuota',
                  's_prestamo_creditogrupal.fechadesembolsado as fechadesembolsado',
                  's_prestamo_creditogrupal.idestadodesembolso as idestadodesembolso',
                  's_prestamo_creditogrupal.idestadocredito as idestadocredito',
                  's_prestamo_creditogrupal.fechaaprobado as fechaaprobado',
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  's_prestamo_frecuencia.nombre as frecuencianombre',
                  's_prestamo_tipocredito.nombre as tipocreditonombre',
                  'cliente.identificacion as clienteidentificacion',
                  's_moneda.simbolo as monedasimbolo',
                  DB::raw('IF(cliente.idtipopersona = 1 || cliente.idtipopersona = 3,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
              );*/
      
        $prestamocreditos = DB::table('s_prestamo_credito')
              ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->join('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_credito.idprestamo_frecuencia')
              ->join('s_prestamo_tipocredito', 's_prestamo_tipocredito.id', 's_prestamo_credito.idprestamo_tipocredito')
              ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
              ->where($where)
              ->where('s_prestamo_credito.idtienda', $idtienda)
              ->where('s_prestamo_credito.idestado', 1)
              ->whereIn('s_prestamo_credito.idestadocredito', [4,5])
              ->where('s_prestamo_credito.idcajero', Auth::user()->id)
              ->orWhere($where1)
              ->where('s_prestamo_credito.idtienda', $idtienda)
              ->where('s_prestamo_credito.idestado', 1)
              ->whereIn('s_prestamo_credito.idestadocredito', [4,5])
              ->where('s_prestamo_credito.idcajero', Auth::user()->id)
              ->orWhere($where1)
              ->where('s_prestamo_credito.idtienda', $idtienda)
              ->where('s_prestamo_credito.idestado', 1)
              ->whereIn('s_prestamo_credito.idestadocredito', [3])
              //->union($prestamocreditos)
              ->select(
                  's_prestamo_credito.id as id',
                  's_prestamo_credito.codigo as codigo',
                  's_prestamo_credito.monto as monto',
                  's_prestamo_credito.numerocuota as numerocuota',
                  's_prestamo_credito.fechadesembolsado as fechadesembolsado',
                  's_prestamo_credito.idestadodesembolso as idestadodesembolso',
                  's_prestamo_credito.idestadocredito as idestadocredito',
                  's_prestamo_credito.fechaaprobado as fechaaprobado',
                  's_prestamo_credito.facturacion_montorecibido as facturacion_montorecibido',
                  's_prestamo_credito.facturacion_vuelto as facturacion_vuelto',
                  'asesor.nombre as asesor_nombre',
                  'asesor.apellidos as asesor_apellidos',
                  's_prestamo_frecuencia.nombre as frecuencianombre',
                  's_prestamo_tipocredito.nombre as tipocreditonombre',
                  'cliente.identificacion as clienteidentificacion',
                  's_moneda.simbolo as monedasimbolo',
                  DB::raw('IF(cliente.idtipopersona = 1 || cliente.idtipopersona = 3,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
              )
              ->orderBy('idestadodesembolso','asc')
              ->orderBy('idestadocredito','asc')
              ->orderBy('fechaaprobado','desc')
              ->paginate(10);

        return view('layouts/backoffice/tienda/sistema/prestamodesembolso/index',[
            'tienda' => $tienda,
            'prestamocreditos' => $prestamocreditos
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
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')
            ->leftJoin('ubigeo', 'ubigeo.id', 'tienda.idubigeo')
            ->select(
                'tienda.*',
                'ubigeo.nombre as ubigeonombre',
            )
            ->where('tienda.id',$idtienda)
            ->first();
        $prestamodesembolso = DB::table('s_prestamo_credito')
              ->leftjoin('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
              ->leftjoin('users as supervisor', 'supervisor.id', 's_prestamo_credito.idsupervisor')
              ->leftjoin('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
              ->leftjoin('users as conyuge', 'conyuge.id', 's_prestamo_credito.idconyuge')
              ->leftjoin('users as garante', 'garante.id', 's_prestamo_credito.idgarante')
              ->leftjoin('s_prestamo_frecuencia', 's_prestamo_frecuencia.id', 's_prestamo_credito.idprestamo_frecuencia')
              ->leftjoin('s_prestamo_tipocredito', 's_prestamo_tipocredito.id', 's_prestamo_credito.idprestamo_tipocredito')
              ->leftjoin('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
              ->leftjoin('ubigeo as cliubigeo', 'cliubigeo.id', 'cliente.idubigeo')
              ->leftjoin('s_prestamo_estadocivil as clienteestadocivil', 'clienteestadocivil.id', 'cliente.idestadocivil')
              ->leftjoin('ubigeo as garanteubigeo', 'garanteubigeo.id', 'garante.idubigeo')
              ->leftjoin('ubigeo as conyugeubigeo', 'conyugeubigeo.id', 'conyuge.idubigeo')
              ->leftjoin('ubigeo as asesorubigeo', 'asesorubigeo.id', 'asesor.idubigeo')
          
              ->leftjoin('ubigeo as clienteubigeo', 'clienteubigeo.id', 's_prestamo_credito.facturacion_idubigeo')
              ->leftJoin('s_agencia as agencia', 'agencia.id', 's_prestamo_credito.facturacion_idagencia')
              ->leftJoin('ubigeo as agenciaubigeo', 'agenciaubigeo.id', 'agencia.idubigeo')
              ->leftJoin('s_tipocomprobante as tipocomprobante', 'tipocomprobante.id', 's_prestamo_credito.facturacion_idtipocomprobante')
              ->leftjoin('users as cajero', 'cajero.id', 's_prestamo_credito.idcajero')
          
              ->leftjoin('s_prestamo_creditolaboral', 's_prestamo_creditolaboral.idprestamo_credito', 's_prestamo_credito.id')
              ->leftJoin('s_prestamo_giro', 's_prestamo_giro.id', 's_prestamo_creditolaboral.idprestamo_giro')
              ->leftJoin('ubigeo as negocioubigeo', 'negocioubigeo.id', 's_prestamo_creditolaboral.idubigeo')
          
              ->where('s_prestamo_credito.id', $id)
              ->select(
                  's_prestamo_credito.*',
                  's_prestamo_frecuencia.nombre as frecuencia_nombre',
                  's_prestamo_tipocredito.nombre as tipocreditonombre',
                  's_moneda.simbolo as monedasimbolo',
                  's_moneda.nombre as monedanombre',
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
                  'conyuge.numerotelefono as conyugenumerotelefono',
                  'garante.identificacion as garanteidentificacion',
                  'garante.nombre as garantenombre',
                  'garante.apellidos as garanteapellidos',
                  'garante.direccion as garantedireccion',
                  'garante.numerotelefono as garantenumerotelefono',
                  'asesor.identificacion as asesoridentificacion',
                  'asesor.nombre as asesornombre',
                  'asesor.apellidos as asesor_apellidos',
                  'asesor.numerotelefono as asesor_numerotelefono',
                  'asesor.direccion as asesordireccion',
                  DB::raw('IF(cliente.idtipopersona = 1 || cliente.idtipopersona = 3,
                      CONCAT(cliente.nombre, ", ", cliente.apellidos),
                      CONCAT(cliente.apellidos)) as cliente_nombre'),
                  DB::raw('IF(cajero.idtipopersona = 1 || cajero.idtipopersona = 3,
                      CONCAT(cajero.nombre, ", ", cajero.apellidos),
                      CONCAT(cajero.apellidos)) as cajero_nombre'),
                  DB::raw('IF(asesor.idtipopersona = 1 || asesor.idtipopersona = 3,
                      CONCAT(asesor.nombre, ", ", asesor.apellidos),
                      CONCAT(asesor.apellidos)) as asesor_nombre'),
                  DB::raw('IF(supervisor.idtipopersona = 1 || supervisor.idtipopersona = 3,
                      CONCAT(supervisor.nombre, ", ", supervisor.apellidos),
                      CONCAT(supervisor.apellidos)) as supervisor_nombre'),
                  
                'clienteubigeo.nombre as facturacion_cliente_ubigeonombre',
                'clienteubigeo.codigo as facturacion_cliente_ubigeocodigo',
                'conyugeubigeo.nombre as conyugeubigeonombre',
                'garanteubigeo.nombre as garanteubigeonombre',
                'asesorubigeo.nombre as asesorubigeonombre',
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
          
                's_prestamo_creditolaboral.nombrenegocio as negocio_nombre',
                's_prestamo_creditolaboral.actividad as negocio_actividad',
                's_prestamo_creditolaboral.direccion as negocio_direccion',
                's_prestamo_giro.nombre as negocio_giro',
                'negocioubigeo.nombre as negocio_ubigeo',

              )
              ->first();
      
        if ($request->input('view') == 'desembolsar') {
            $agencias = DB::table('s_agencia')->where('s_agencia.idtienda', $idtienda)->get();
            $tipocomprobante = DB::table('s_tipocomprobante')->get();
            $monedas = DB::table('s_moneda')->get();
          
            //validar si existe cobranzas pendientes del cliente
            $prestamo_cobranza = DB::table('s_prestamo_cobranza')
                ->where('s_prestamo_cobranza.idcliente', $prestamodesembolso->idcliente)
                ->where('s_prestamo_cobranza.idestadocobranza', 1)
                ->where('s_prestamo_cobranza.idestado', 1)
                ->limit(1)
                ->first();
          
            return view('layouts/backoffice/tienda/sistema/prestamodesembolso/desembolsar', [
                'tienda' => $tienda,
                'prestamodesembolso' => $prestamodesembolso,
                'prestamocredito' => $prestamodesembolso,
                'agencias' => $agencias,
                'tipocomprobante' => $tipocomprobante,
                'monedas' => $monedas,
                'prestamo_cobranza' => $prestamo_cobranza,
            ]);
        }
        elseif ($request->input('view') == 'anularaprobacion') {
            return view('layouts/backoffice/tienda/sistema/prestamodesembolso/anularaprobacion', [
                'tienda' => $tienda,
                'prestamodesembolso' => $prestamodesembolso,
                'prestamocredito' => $prestamodesembolso,
            ]);
        }
        elseif ($request->input('view') == 'detalleaprobacion') {
            return view('layouts/backoffice/tienda/sistema/prestamodesembolso/detalleaprobacion', [
                'tienda' => $tienda,
                'prestamodesembolso' => $prestamodesembolso,
                'prestamocredito' => $prestamodesembolso,
            ]);
        }
        elseif ($request->input('view') == 'anulardesembolso') {
            return view('layouts/backoffice/tienda/sistema/prestamodesembolso/anulardesembolso', [
                'tienda' => $tienda,
                'prestamodesembolso' => $prestamodesembolso,
                'prestamocredito' => $prestamodesembolso,
            ]);
        }
        elseif ($request->input('view') == 'detalledesembolso') {
            return view('layouts/backoffice/tienda/sistema/prestamodesembolso/detalledesembolso', [
                'tienda' => $tienda,
                'prestamodesembolso' => $prestamodesembolso,
                'prestamocredito' => $prestamodesembolso,
            ]);
        }
        elseif ($request->input('view') == 'ticket') {
            return view('layouts/backoffice/tienda/sistema/prestamodesembolso/ticket', [
                'tienda' => $tienda,
                'prestamodesembolso' => $prestamodesembolso,
            ]);
        }
        elseif ($request->input('view') == 'ticketpdf') {
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamodesembolso/ticketpdf', [
                'tienda' => $tienda,
                'prestamodesembolso' => $prestamodesembolso,
            ]);
            return $pdf->stream('Ticket.pdf');
        }
        elseif ($request->input('view') == 'ticketgastoadministrativo') {
            return view('layouts/backoffice/tienda/sistema/prestamodesembolso/ticketgastoadministrativo', [
                'tienda' => $tienda,
                'prestamodesembolso' => $prestamodesembolso,
            ]);
        }
        elseif ($request->input('view') == 'ticketgastoadministrativopdf') {
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamodesembolso/ticketgastoadministrativopdf', [
                'tienda' => $tienda,
                'prestamodesembolso' => $prestamodesembolso,
            ]);
            return $pdf->stream('Ticket.pdf');
        }
        elseif ($request->input('view') == 'documento') {
            $documentos = DB::table('s_prestamo_documento')
                ->where('s_prestamo_documento.idmostrar', 1)
                ->where('s_prestamo_documento.idtienda', $idtienda)
                ->get();
            return view('layouts/backoffice/tienda/sistema/prestamodesembolso/documento',  [
                'tienda' => $tienda,
                'prestamodesembolso' => $prestamodesembolso,
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

            // fecha de desembolso
            $diassemana = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            $w = date("w", strtotime($prestamodesembolso->fechadesembolsado));
            $d = date("d", strtotime($prestamodesembolso->fechadesembolsado));
            $n = date("n", strtotime($prestamodesembolso->fechadesembolsado));
            $y = date("Y", strtotime($prestamodesembolso->fechadesembolsado));
            $credito_fechadesembolso = $diassemana[$w]." ".$d." de ".$meses[$n-1]. " de ".$y;
            // fecha ultima cuota
            $w = date("w", strtotime($prestamodesembolso->ultimafecha));
            $d = date("d", strtotime($prestamodesembolso->ultimafecha));
            $n = date("n", strtotime($prestamodesembolso->ultimafecha));
            $y = date("Y", strtotime($prestamodesembolso->ultimafecha));
      
            $credito_ultimacuota = $diassemana[$w]." ".$d." de ".$meses[$n-1]. " de ".$y;
            // garantias
            $bienes = DB::table('s_prestamo_creditobien')
                ->where([
                    ['s_prestamo_creditobien.idprestamo_credito', $prestamodesembolso->id],
                    ['s_prestamo_creditobien.idestado', 1]
                ])
                ->orderBy('s_prestamo_creditobien.id','desc')
                ->get();
          
            $style_td_border = 'border: 1px solid '.(configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_color')['valor']:'#31353d').'60 !important;';
            $style_td_fondo = 'background-color: '.(configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_color')['valor']:'#31353d').'60 !important;';
            $credito_garantias = '<table class="table_pdf" style="width:100%;">';
            $credito_garantias_total = 0;
            $i = 1;
            foreach($bienes as $value){
                $doc = '';
                if($value->idprestamo_documento==1){
                    $doc = 'SIN DOCUMENTOS';
                }elseif($value->idprestamo_documento==2){
                    $doc = 'COPIA/LEGALIZADO';
                }elseif($value->idprestamo_documento==3){
                    $doc = 'ORIGINAL';
                }
              
   
                $result = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[A-Z0-9+&@#\/%=~_|]/i', '<a href="\0" target="_blank">Ir Enlace</a>', $value->descripcion);

              
            
                $credito_garantias =  $credito_garantias.'<tr>
                        <td rowspan="4" style="text-align: center;'.$style_td_border.$style_td_fondo.'"><b>'.$i.'</b></td>
                        <td style="'.$style_td_border.'"><b>PRODUCTO:</b> <span style="font-weight: normal;">'.$value->producto.'<span></td>
                      </tr>
                      <tr>
                        <td style="'.$style_td_border.'"><b>DESCRIPCIÓN:</b> <span style="font-weight: normal;">'.$result.'<span></td>
                      </tr>
                      <tr>
                        <td style="'.$style_td_border.'"><b>DOCUMENTO:</b> <span style="font-weight: normal;">'.$doc.'<span></td>
                      </tr>
                      <tr>
                        <td style="'.$style_td_border.'"><b>VALOR ESTIMADO:</b> <span style="font-weight: normal;">'.$prestamodesembolso->monedasimbolo.' '.$value->valorestimado.'<span></td>
                      </tr>';
              $credito_garantias_total = $credito_garantias_total+$value->valorestimado;  
              $i++;
            }
            $credito_garantias = $credito_garantias.'</table>';
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamodesembolso/documentopdf', [
                'tienda' => $tienda,
                'prestamodesembolso' => $prestamodesembolso,
                'prestamodocumento' => $prestamodocumento,
                'credito_fechadesembolso' => $credito_fechadesembolso,
                'credito_ultimacuota' => $credito_ultimacuota,
                'credito_garantias' => $credito_garantias,
                'credito_garantias_total' => $credito_garantias_total,
            ]);
          return $pdf->stream('Documento.pdf');
        }
        elseif ($request->input('view') == 'cronograma') {
            return view('layouts/backoffice/tienda/sistema/prestamodesembolso/cronograma', [
                'tienda' => $tienda,
                'prestamodesembolso' => $prestamodesembolso,
            ]);
        }
        elseif ($request->input('view') == 'cronogramapdf') {
            $prestamodesembolsodetalle = DB::table('s_prestamo_creditodetalle')
                ->where('s_prestamo_creditodetalle.idprestamo_credito', $prestamodesembolso->id)
                ->orderBy('s_prestamo_creditodetalle.numero','asc')
                ->get();

            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamodesembolso/cronogramapdf', [
                'prestamodesembolso' => $prestamodesembolso,
                'prestamodesembolsodetalle' => $prestamodesembolsodetalle,
                'tienda' => $tienda
            ]);
            return $pdf->stream('Cronograma.pdf');
        }
        elseif ($request->input('view') == 'tarjeta') {
            return view('layouts/backoffice/tienda/sistema/prestamodesembolso/tarjeta', [
                'prestamodesembolso' => $prestamodesembolso,
                'tienda' => $tienda
            ]);
        }
        elseif ($request->input('view') == 'tarjetapdf') {
            $prestamodesembolsodetalle = DB::table('s_prestamo_creditodetalle')
                ->where('s_prestamo_creditodetalle.idprestamo_credito', $prestamodesembolso->id)
                ->orderBy('s_prestamo_creditodetalle.numero','asc')
                ->get();
          
            $cronograma = prestamo_cobranza_cronograma($idtienda,$prestamodesembolso->id,0,0,1,$prestamodesembolso->numerocuota);

            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/prestamodesembolso/tarjetapdf', [
                'prestamodesembolso' => $prestamodesembolso,
                'prestamodesembolsodetalle' => $prestamodesembolsodetalle,
                'cronograma' => $cronograma,
                'tienda' => $tienda
            ]);
            return $pdf->stream('Cronograma.pdf');
        }
    }

  
    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(), $idtienda);

        if ($request->input('view') == 'desembolsar') {
            $rules = [
                'cliente_direccion' => 'required',
                'idubigeo' => 'required',
                'idagencia' => 'required',
                'idmoneda' => 'required',
                'idtipocomprobante' => 'required',
            ];
            
            if(configuracion($idtienda,'prestamo_estadogasto_administrativo')['valor']=='on'){
                if($request->input('check_gastoadministrativo')!='on'){
                  $rules = array_merge($rules,[
                    'facturacion_montorecibido' => 'required'
                  ]);
                }
            }
          
            $messages = [
                'cliente_direccion.required' => 'La "Dirección" es Obligatorio.',
                'idubigeo.required' => 'El "Ubigeo" es Obligatorio.',
                'idagencia.required' => 'La "Agencia" es Obligatorio.',
                'idtipocomprobante.required' => 'El "Tipo de Comprobante" es Obligatorio.',
                'facturacion_montorecibido.required' => 'El "Monto recibido" es Obligatorio.',
                'idmoneda.required' => 'La "Moneda" es Obligatorio.',
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
          
            $credito = DB::table('s_prestamo_credito')
                  ->whereId($id)
                  ->first();
            $total_gastoadministrativo = 0;
            $facturacion_montorecibido = 0;
            $idestadogastoadministrativo = 0;
            if($request->input('check_gastoadministrativo')=='on'){
                $idestadogastoadministrativo = 2;
                $total_gastoadministrativo = $request->input('total_gastoadministrativo');
            }else{
                $idestadogastoadministrativo = 1;
                if($request->input('facturacion_montorecibido')!=null){
                    $facturacion_montorecibido = $request->input('facturacion_montorecibido');
                }
                if($facturacion_montorecibido<$request->input('total_gastoadministrativo')){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El monto recibido no puede ser menor al Gasto Administrativo, ingrese otro monto por favor.'
                    ]);
                }
            }
          
            $efectivo = efectivo($idtienda,$idaperturacierre,$request->input('idmoneda'));
            if(($credito->monto+$total_gastoadministrativo)>$efectivo['total']){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No hay suficiente saldo en caja!.'
                ]);
            }
          
            $cliente = DB::table('users')
              ->leftjoin('ubigeo', 'ubigeo.id', 'users.idubigeo')
              ->where('users.id', $request->idcliente)
              ->select(
                'users.*',
                'ubigeo.nombre as ubigeo_nombre',
                'ubigeo.codigo as ubigeo_codigo'
              )
              ->first();
            
            DB::table('s_prestamo_credito')->whereId($id)->update([
                'fechadesembolsado' => Carbon::now(),
                'total_gastoadministrativo' => $total_gastoadministrativo,
                'facturacion_montorecibido' => $facturacion_montorecibido, // gasto admistrativo
                'facturacion_vuelto' => $request->input('facturacion_vuelto')!=null?$request->input('facturacion_vuelto'):0,
                'facturacion_cliente_identificacion' => $cliente->identificacion,
                'facturacion_cliente_nombre' => $cliente->nombre,
                'facturacion_cliente_apellidos' => $cliente->apellidos,
                'facturacion_cliente_direccion' => $request->cliente_direccion,
                'facturacion_idagencia' => $request->idagencia,
                'facturacion_idtipocomprobante' => $request->idtipocomprobante,
                'facturacion_idubigeo' => $request->idubigeo,
                'facturacion_idaperturacierre' => $idaperturacierre,
                'idcajero' => Auth::user()->id,
                'idestadocredito' => 4,
                'idestadodesembolso' => 1,
                'idestadogastoadministrativo' => $idestadogastoadministrativo,
            ]);
          
            if(configuracion($idtienda,'prestamo_estadogasto_administrativo')['valor']=='on'){
            if($request->input('check_gastoadministrativo')=='on'){
                DB::table('s_prestamo_credito')->whereId($id)->update([
                    'total_cuotafinal' => $credito->total_cuotafinal+$total_gastoadministrativo,
                ]);
                $cronograma = prestamo_cronograma(
                    $idtienda,
                    $credito->monto,
                    $credito->numerocuota,
                    $credito->fechainicio,
                    $credito->idprestamo_frecuencia,
                    $credito->numerodias,
                    $credito->tasa,
                    $request->input('total_gastoadministrativo'),
                    $credito->excluirferiado,
                    $credito->excluirsabado,
                    $credito->excluirdomingo
                );
                DB::table('s_prestamo_creditodetalle')->where('idprestamo_credito',$id)->delete();
                foreach($cronograma['cronograma'] as $value) {
                  DB::table('s_prestamo_creditodetalle')->insert([
                    'numero' => $value['numero'],
                    'fechavencimiento' => $value['fechanormal'],
                    'saldocapital' => $value['saldo'],
                    'saldomontototal' => $value['saldototal'],
                    'amortizacion' => $value['amortizacion'],
                    'interes' => $value['interes'],
                    'cuota' => $value['cuota'],
                    'seguro' => $value['segurodesgravamen'],
                    'gastoadministrativo' => $value['gastoadministrativo'],
                    'total' => $value['cuotafinal'],
                    'abono' => $value['abono'],
                    'totalfinal' => $value['cuotafinaltotal'],
                    'atraso' => 0,
                    'mora' => 0,
                    'moradescuento' => 0,
                    'moraapagar' => 0,
                    'cuotapago' => 0,
                    'acuenta' => 0,
                    'cuotaapagar' => 0,
                    'cuotaapagartotal' => 0,
                    'montorefinanciado' => 0,
                    'interesdescontado' => 0,
                    'idprestamo_credito' => $id,
                    'idestadocobranza' => 1,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                  ]);
                }
            }
            }

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha desembolsado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'anularaprobacion') {
            DB::table('s_prestamo_credito')->whereId($id)->update([
                'fechaanuladoaprobacion' => Carbon::now(),
                'idestadodesembolso' => 2
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha Anulado la Aprobación correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'anulardesembolso') {
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
            DB::table('s_prestamo_credito')->whereId($id)->update([
                'fechaanuladodesembolso' => Carbon::now(),
                'idestadodesembolso' => 3
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha Anulado el Desembolso correctamente.'
            ]);
        }
    }

    public function destroy(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }
}
