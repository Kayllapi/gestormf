<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use App\Exports\ReportecarteracreditoExport;
use Maatwebsite\Excel\Facades\Excel;

class VinculacionRiesgoUnicoController extends Controller
{
    public function __construct()
    {
        //
    }
    public function index(Request $request,$idtienda)
    {
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            
            $agencias = DB::table('tienda')->get();
          
            return view(sistema_view().'/vinculacionriesgounico/tabla',[
              'tienda' => $tienda,
              'agencias' => $agencias,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
    }
  
    public function store(Request $request, $idtienda)
    {
      
        if($request->input('view') == 'registrar') {
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showtable'){

            $cliente = DB::table('users')
              ->where('users.id',$request->idcliente)
              ->select('users.id','users.direccion as direcciondomicilio','db_idubigeo')->first();
          
            $s_users_prestamo = DB::table('s_users_prestamo')
              ->where('id_s_users',$request->idcliente)
              ->select('s_users_prestamo.direccion_ac_economica as direccionnegocio','db_idubigeo_ac_economica', 'idubigeo_ac_economica')->first();
            $clientes_mismo_negocio = \App\Support\DireccionesNegocio::clientesSimilares($s_users_prestamo->direccionnegocio ?? null, $s_users_prestamo->idubigeo_ac_economica ?? null);
          
           $credito_garantias = DB::table('credito_garantia')
              ->join('credito','credito.id','credito_garantia.idcredito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->where('credito.idestadocredito',1)
              ->whereIn('credito.estado',['APROBADO','DESEMBOLSADO'])
              ->where('credito_garantia.idcliente',$request->idcliente)
              ->where('cliente.direccion',$cliente->direcciondomicilio)
              ->where('credito_garantia.tipo','AVAL')
              ->select(
                  'credito.*',
                  'credito.id as idcredito',
                  'credito.cuenta as cuenta',
                  'cliente.id as idcliente',
                  'cliente.identificacion as identificacioncliente',
                  'cliente.nombrecompleto as nombrecliente',
                  'cliente.direccion as direccioncliente',
              )
              ->get();
          
          $html = '';
          $total = 0;
          $familia_por_cliente = [];
          $numero_fila = 0;
          foreach($credito_garantias as $key => $value){
            
               $total_valor = 0;
            
               $credito_garantias_mismadireccion = DB::table('credito')
                  //->join('credito','credito.id','credito_garantia.idcredito')
                  ->join('s_users_prestamo','s_users_prestamo.id_s_users','credito.idcliente')
                  ->where('credito.idestadocredito',1)
                  ->whereIn('credito.estado',['APROBADO','DESEMBOLSADO'])
                  ->whereIn('credito.idcliente', $clientes_mismo_negocio)
                  ->where('credito.id',$value->idcredito)
                  ->select(
                      //'credito_garantia.*',
                      'credito.*',
                  )
                  ->get();
            
              $cuenta_mismadireccion = '';
              $valor_mismadireccion = '';
              foreach($credito_garantias_mismadireccion as $valuemismadireccion){
                  $cuenta_mismadireccion = $cuenta_mismadireccion.'C'.$valuemismadireccion->cuenta.'<br>';
                  $valor_mismadireccion = $valor_mismadireccion.($valuemismadireccion->monto_solicitado!=''?$valuemismadireccion->monto_solicitado:'0.00').'<br>';
                  $total_valor += (float) $valuemismadireccion->monto_solicitado;
              }
            
               $credito_garantias_direccionusuario = DB::table('users')
                  ->where('users.idtipousuario',1)
                  ->where('users.direccion','LIKE','%'.$s_users_prestamo->direccionnegocio.'%')
                  ->first();
            
              $cuenta_direccionusuario = '';
              $valor_direccionusuario = '';
            
              if($credito_garantias_direccionusuario){
                  $cuenta_direccionusuario = 'C'.$value->cuenta;
                  $valor_direccionusuario = $value->monto_solicitado;
              }
              if (in_array($value->idcliente, $clientes_mismo_negocio) && $value->idcliente != $request->idcliente) {
                  if ($cuenta_direccionusuario !== '') {
                      $familia_por_cliente[$value->idcliente][$cuenta_direccionusuario] = $valor_direccionusuario;
                  }
                  continue;
              }
              $total_valor = number_format($total_valor, 2, '.', '');
              $html .= "<tr id='show_data_select' idcredito='{$value->id}'>
                            <td>".(++$numero_fila)."</td>
                            <td>{$value->identificacioncliente}</td>
                            <td>{$value->nombrecliente}</td>

                            <td style='text-align: center;width: 100px;'>{$cuenta_mismadireccion}</td>
                            <td style='text-align: right;'>{$valor_mismadireccion}</td>
                            <td style='text-align: center;width: 100px;'>{$cuenta_direccionusuario}</td>
                            <td style='text-align: right;'>{$valor_direccionusuario}</td>
                            <td></td><td></td><td style='text-align: right;width: 100px;'>{$total_valor}</td>
                        </tr>";
              $total += $total_valor;
          }
          
         if($s_users_prestamo){
            
         $credito_garantias_mismadireccions = DB::table('credito')
            //->join('credito','credito.id','credito_garantia.idcredito')
            ->join('users as cliente','cliente.id','credito.idcliente')
            ->join('s_users_prestamo','s_users_prestamo.id_s_users','credito.idcliente')
            ->where('credito.idestadocredito',1)
            ->whereIn('credito.estado',['APROBADO','DESEMBOLSADO'])
            ->whereIn('credito.idcliente', $clientes_mismo_negocio)
            ->where('cliente.id','<>',$request->idcliente)
            ->select(
                  'cliente.id as idcliente',
                  'cliente.identificacion as identificacioncliente',
                  'cliente.nombrecompleto as nombrecliente',
            )
            ->distinct()
            ->get();
          
          foreach($credito_garantias_mismadireccions as $key => $value){
            
               $total_valor = 0;
              
            
               $credito_garantias_mismadireccion = DB::table('credito')
                  //->join('credito','credito.id','credito_garantia.idcredito')
                  ->join('users as cliente','cliente.id','credito.idcliente')
                  ->join('s_users_prestamo','s_users_prestamo.id_s_users','credito.idcliente')
                  ->where('credito.idestadocredito',1)
                  ->whereIn('credito.estado',['APROBADO','DESEMBOLSADO'])
                  ->whereIn('credito.idcliente', $clientes_mismo_negocio)
                  ->where('cliente.id',$value->idcliente)
                  ->select(
                      //'credito_garantia.*',
                      'credito.*',
                  )
                  ->get();
            
              $cuenta_mismadireccion = '';
              $valor_mismadireccion = '';
              foreach($credito_garantias_mismadireccion as $valuemismadireccion){
                  $cuenta_mismadireccion = $cuenta_mismadireccion.'C'.$valuemismadireccion->cuenta.'<br>';
                  $valor_mismadireccion = $valor_mismadireccion.($valuemismadireccion->monto_solicitado!=''?$valuemismadireccion->monto_solicitado:'0.00').'<br>';
                  $total_valor += (float) $valuemismadireccion->monto_solicitado;
              }

              $familia = $familia_por_cliente[$value->idcliente] ?? [];
              $cuenta_familia = implode('<br>', array_map('e', array_keys($familia)));
              $valor_familia = implode('<br>', array_map('e', array_values($familia)));
              $total_valor = number_format($total_valor, 2, '.', '');
              $html .= "<tr id='show_data_select'>
                            <td>".(++$numero_fila)."</td>
                            <td>{$value->identificacioncliente}</td>
                            <td>{$value->nombrecliente}</td>

                            <td style='text-align: center;width: 100px;'>{$cuenta_mismadireccion}</td>
                            <td style='text-align: right;'>{$valor_mismadireccion}</td>
                            <td style='text-align: center;width: 100px;'>{$cuenta_familia}</td>
                            <td style='text-align: right;'>{$valor_familia}</td>
                            <td></td><td></td><td style='text-align: right;width: 100px;'>{$total_valor}</td>
                        </tr>";
              $total += $total_valor;
          }
   
              $html .= '
                <tr class="tabla-total">
                  <th colspan="9" class="text-end">TOTAL S/.</th>
                  <th class="text-end">'.number_format($total, 2, '.', '').'</th>
                </tr>';
          return array(
            'html' => $html
          );
           
         }
          
        }
      
      
        else if($id == 'showcliente'){
            $cliente = DB::table('users')
            ->leftJoin('s_users_prestamo','s_users_prestamo.id_s_users','users.id')
              ->leftJoin('f_fuenteingreso','f_fuenteingreso.id','s_users_prestamo.idfuenteingreso')
              ->where('users.id',$request->idcliente)
              ->select(
                'users.id',
                'users.direccion as direcciondomicilio',
                'users.db_idubigeo',
                's_users_prestamo.idfuenteingreso',
                's_users_prestamo.db_idfuenteingreso',
                's_users_prestamo.idgiro_ac_economica',
                's_users_prestamo.db_idgiro_ac_economica',
                's_users_prestamo.cargo_laboral_cliente',
                's_users_prestamo.razonsocial_laboral_cliente',
                'f_fuenteingreso.nombre as fuente_ingreso'
                )
              ->first();
            $s_users_prestamo = DB::table('s_users_prestamo')
              ->where('id_s_users',$request->idcliente)
              ->select('s_users_prestamo.direccion_ac_economica as direccionnegocio','db_idubigeo_ac_economica', 'idubigeo_ac_economica')->first();

            $fuenteingreso = $cliente ? ($cliente->fuente_ingreso != null && $cliente->fuente_ingreso != '' ? $cliente->fuente_ingreso : $cliente->db_idfuenteingreso) : '';
            return response()->json([
                'direcciondomicilio' => $cliente->direcciondomicilio.', '.$cliente->db_idubigeo,
                'direccionnegocio'   => $s_users_prestamo->direccionnegocio.', '.$s_users_prestamo->db_idubigeo_ac_economica,
                'fuenteingreso' => $fuenteingreso,
                'idfuenteingreso' => $cliente->idfuenteingreso ?? null,
                'giro_economico' => [1 => 'COMERCIO', 2 => 'SERVICIO', 3 => 'PRODUCCION'][$cliente->idgiro_ac_economica ?? 0] ?? ($cliente->db_idgiro_ac_economica ?? ''),
                'cargo' => $cliente->cargo_laboral_cliente ?? '',
                'empresa' => $cliente->razonsocial_laboral_cliente ?? '',
            ]);
        }
        
        if($id == 'show_credito'){
          $creditos = DB::table('users')
                            ->where('users.identificacion','LIKE','%'.$request->buscar.'%')
                            ->orWhere('users.nombrecompleto','LIKE','%'.$request->buscar.'%')
                            ->select(
                                'users.id as idcliente',
                                'users.identificacion as identificacion',
                                'users.nombrecompleto as nombrecliente',
                            )
                            ->get();
     
            $data = [];
            foreach($creditos as $value){
                $data[] = [
                    'id' => $value->idcliente,
                    'text' => $value->identificacion.' - '.$value->nombrecliente,
                ];
            }
          return $data;
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
 
      if($request->input('view') == 'exportar') {
            abort_unless(DB::table('users')->where('id', $request->idcliente)->exists(), 404, 'Seleccione un cliente válido para generar el reporte.');
            return view(sistema_view().'/vinculacionriesgounico/exportar',[
                'tienda' => $tienda,
                'idcliente' => $request->idcliente,
            ]);
        }
        else if( $request->input('view') == 'exportar_pdf' ){
          $cliente = DB::table('users')
              ->where('users.id', $request->idcliente)
              ->select('users.id', 'users.identificacion', 'users.nombrecompleto', 'users.direccion as direcciondomicilio', 'db_idubigeo')
              ->first();
          abort_unless($cliente, 404, 'Seleccione un cliente válido para generar el reporte.');
            
          $credito_garantias = DB::table('credito_garantia')
              ->join('credito','credito.id','credito_garantia.idcredito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->where('credito.idestadocredito',1)
              ->whereIn('credito.estado',['APROBADO','DESEMBOLSADO'])
              ->where('credito_garantia.idcliente',$request->idcliente)
              ->where('credito_garantia.tipo','AVAL')
              ->select(
                  'credito_garantia.*',
                  'credito.id as idcredito',
                  'credito.cuenta as cuenta',
                  'cliente.id as idcliente',
                  'cliente.identificacion as identificacioncliente',
                  'cliente.nombrecompleto as nombrecliente',
                  'cliente.direccion as direccioncliente',
              )
              ->get();
          
            $s_users_prestamo = DB::table('s_users_prestamo')
              ->where('id_s_users',$request->idcliente)
              ->select('s_users_prestamo.direccion_ac_economica as direccionnegocio','db_idubigeo_ac_economica', 'idubigeo_ac_economica')->first();
            $clientes_mismo_negocio = \App\Support\DireccionesNegocio::clientesSimilares($s_users_prestamo->direccionnegocio ?? null, $s_users_prestamo->idubigeo_ac_economica ?? null);
        
            $pdf = PDF::loadView(sistema_view().'/vinculacionriesgounico/exportar_pdf',[
                'tienda' => $tienda,
                'credito_garantias' => $credito_garantias,
                'idcliente' => $request->idcliente,
                's_users_prestamo' => $s_users_prestamo,
                'clientes_mismo_negocio' => $clientes_mismo_negocio,
                'cliente' => $cliente->identificacion.' - '.$cliente->nombrecompleto,
                'direcciondomicilio' => $cliente->direcciondomicilio.', '.$cliente->db_idubigeo,
                'direccionnegocio'   => trim(($s_users_prestamo->direccionnegocio ?? '').', '.($s_users_prestamo->db_idubigeo_ac_economica ?? ''), ', '),
            ]); 
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('CARTERA_DE_CREDITO.pdf');
        }  
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        
    
    }

    public function destroy(Request $request, $idtienda, $id)
    {
    }
}
