<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class ConsolidadoCarteraCreditoController extends Controller
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
          
            return view(sistema_view().'/consolidadocarteracredito/tabla',[
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
      
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showtable'){
          $where = [];

          if($request->idagencia!='' && $request->idagencia!=0){
              $where[] = ['credito.idtienda',$request->idagencia];
          }
          
          if($request->idasesor!='' && $request->idasesor!=0){
              $where[] = ['credito.idasesor',$request->idasesor];
          }

          if($request->inicio){
              $where[] = ['credito.fecha_desembolso','<=',$request->inicio.' 23:59:59'];
          }
          
          if($request->idformacredito!='' && $request->idformacredito!=0){
              if($request->idformacredito=='CP'){
                  $where[] = ['credito.idforma_credito',1];
              }
              elseif($request->idformacredito=='CNP'){
                  $where[] = ['credito.idforma_credito',2];
              }
              elseif($request->idformacredito=='CC'){
                  $where[] = ['credito.idforma_credito',3];
              }
          }
          
          $asesores_credito = DB::table('credito')
              ->leftjoin('users as asesor','asesor.id','credito.idasesor')
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito.idestadocredito',1)
              ->where('credito.saldo_pendientepago','>',0)
              ->where($where)
              ->select(
                  'asesor.id as idasesor',
                  'asesor.usuario as codigoasesor',
              )
              ->orderBy('asesor.usuario','asc')
              ->distinct()
              ->get();
          
          $html = '';
          $total_cartera = 0;
          $total_num_creditos = 0;
          $total_mora_soles = 0;
          $total_mora_porcentaje = 0;
          $total_numero_moracredito = 0;
          
          $clasificacion_normal_cantidad = 0;
          $clasificacion_normal_saldo = 0;
          $clasificacion_normal_creditos = 0;
          
          $clasificacion_cpp_cantidad = 0;
          $clasificacion_cpp_saldo = 0;
          $clasificacion_cpp_creditos = 0;
          
          $clasificacion_deficiente_cantidad = 0;
          $clasificacion_deficiente_saldo = 0;
          $clasificacion_deficiente_creditos = 0;
          
          $clasificacion_dudoso_cantidad = 0;
          $clasificacion_dudoso_saldo = 0;
          $clasificacion_dudoso_creditos = 0;
          
          $clasificacion_perdida_cantidad = 0;
          $clasificacion_perdida_saldo = 0;
          $clasificacion_perdida_creditos = 0;
          
          $total_saldos = 0;
          $total_creditos = 0;
          
          foreach($asesores_credito as $valueasesor){
              $creditos = DB::table('credito')
                  ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                  ->where('credito.estado','DESEMBOLSADO')
                  ->where('credito.idestadocredito',1)
                  ->where('credito.saldo_pendientepago','>',0)
                  ->where($where)
                  ->select(
                      'credito.*',
                      'credito_prendatario.modalidad as modalidadproductocredito',
                  )
                  ->orderBy('credito.fecha_desembolso','asc')
                  ->get();
              $cartera = 0;
              $num_creditos = 0;
              $total_moracredito = 0;
              //$mora_porcentaje = 0;
              $numero_moracredito = 0;
              foreach($creditos as $key => $value){
                  $cronograma = select_cronograma(
                      $value->idtienda,
                      $value->id,
                      $value->idforma_credito,
                      $value->modalidadproductocredito,
                      $value->cuotas,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      0,
                      1,
                      'detalle_cobranza'
                  );
                
                
                  $cartera += $value->saldo_pendientepago;
                  $num_creditos++;
                  //$mora_porcentaje = number_format($cronograma['total_moracredito']/$cartera*100, 2, '.', '');
                  if($cronograma['numero_moracredito']>0){
                      $numero_moracredito ++;
                      $total_moracredito += $value->saldo_pendientepago;
                  }
                
                  if($cronograma['ultimo_atraso']<=8){
                      $clasificacion_normal_cantidad++;
                      $clasificacion_normal_saldo += $value->saldo_pendientepago;
                      $clasificacion_normal_creditos++;

                      $total_saldos += $value->saldo_pendientepago;
                      $total_creditos ++;
                  }
                  elseif($cronograma['ultimo_atraso']>8 && $cronograma['ultimo_atraso']<=30){
                      $clasificacion_cpp_cantidad++;
                      $clasificacion_cpp_saldo += $value->saldo_pendientepago;
                      $clasificacion_cpp_creditos++;

                      $total_saldos += $value->saldo_pendientepago;
                      $total_creditos ++;
                  }
                  elseif($cronograma['ultimo_atraso']>30 && $cronograma['ultimo_atraso']<=60){
                      $clasificacion_deficiente_cantidad++;
                      $clasificacion_deficiente_saldo += $value->saldo_pendientepago;
                      $clasificacion_deficiente_creditos++;

                      $total_saldos += $value->saldo_pendientepago;
                      $total_creditos ++;
                  }
                  elseif($cronograma['ultimo_atraso']>60 && $cronograma['ultimo_atraso']<=120){
                      $clasificacion_dudoso_cantidad++;
                      $clasificacion_dudoso_saldo += $value->saldo_pendientepago;
                      $clasificacion_dudoso_creditos++;

                      $total_saldos += $value->saldo_pendientepago;
                      $total_creditos ++;
                  }
                  elseif($cronograma['ultimo_atraso']>120){
                      $clasificacion_perdida_cantidad++;
                      $clasificacion_perdida_saldo += $value->saldo_pendientepago;
                      $clasificacion_perdida_creditos++;

                      $total_saldos += $value->saldo_pendientepago;
                      $total_creditos ++;
                  }
              }
            
              $total_moracredito = number_format($total_moracredito, 2, '.', '');
              $mora_porcentaje = number_format($total_moracredito/$cartera*100, 2, '.', '');
              
              $html .= "<tr>
                            <td>{$valueasesor->codigoasesor}</td>
                            <td style='text-align:right;'>{$cartera}</td>
                            <td style='text-align:right;'>{$num_creditos}</td>
                            <td style='text-align:right;'>{$total_moracredito}</td>
                            <td style='text-align:right;'>{$mora_porcentaje}</td>
                            <td style='text-align:right;'>{$numero_moracredito}</td>
                        </tr>";
              $total_cartera = $total_cartera+$cartera;
              $total_num_creditos = $total_num_creditos+$num_creditos;
              $total_mora_soles = $total_mora_soles+$total_moracredito;
              $total_mora_porcentaje = $total_mora_porcentaje+$mora_porcentaje;
              $total_numero_moracredito = $total_numero_moracredito+$numero_moracredito;
          }
              $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <th style="text-align:right;">TOTAL S/.</th>
                  <th style="text-align:right;">'.number_format($total_cartera, 2, '.', '').'</th>
                  <th style="text-align:right;">'.$total_num_creditos.'</th>
                  <th style="text-align:right;">'.number_format($total_mora_soles, 2, '.', '').'</th>
                  <th style="text-align:right;">'.number_format($total_mora_porcentaje, 2, '.', '').'</th>
                  <th style="text-align:right;">'.$total_numero_moracredito.'</th>
                </tr>';
          
              
              $html1 = "<tr>
                            <td><b>NORMAL (0)</b></td>
                            <td style='text-align:right;'>{$clasificacion_normal_saldo}</td>
                            <td style='text-align:right;width:100px'>{$clasificacion_normal_creditos}</td>
                        </tr><tr>
                            <td><b>CPP (1)</b></td>
                            <td style='text-align:right;'>{$clasificacion_cpp_saldo}</td>
                            <td style='text-align:right;'>{$clasificacion_cpp_creditos}</td>
                        </tr><tr>
                            <td><b>DEFICIENTE (2)</b></td>
                            <td style='text-align:right;'>{$clasificacion_deficiente_saldo}</td>
                            <td style='text-align:right;'>{$clasificacion_deficiente_creditos}</td>
                        </tr><tr>
                            <td><b>DUDOSO (3)</b></td>
                            <td style='text-align:right;'>{$clasificacion_dudoso_saldo}</td>
                            <td style='text-align:right;'>{$clasificacion_dudoso_creditos}</td>
                        </tr><tr>
                            <td><b>PÉRDIDA (4)</b></td>
                            <td style='text-align:right;'>{$clasificacion_perdida_saldo}</td>
                            <td style='text-align:right;'>{$clasificacion_perdida_creditos}</td>
                        </tr><tr>
                            <th style='text-align:right;'><b>TOTAL</b></th>
                            <th style='text-align:right;'><b>{$total_saldos}</b></th>
                            <th style='text-align:right;'><b>{$total_creditos}</b></th>
                        </tr>";
              $demora1 = 0;
              $demora2 = 0;
              $demora3 = 0;
              if($total_saldos>0){
              $demora1 = number_format(($clasificacion_cpp_saldo+$clasificacion_deficiente_saldo+$clasificacion_dudoso_saldo+$clasificacion_perdida_saldo)/$total_saldos*100, 2, '.', '');
              $demora2 = number_format(($clasificacion_deficiente_saldo+$clasificacion_dudoso_saldo+$clasificacion_perdida_saldo)/$total_saldos*100, 2, '.', '');
              $demora3 = number_format(($clasificacion_dudoso_saldo+$clasificacion_perdida_saldo)/$total_saldos*100, 2, '.', '');
              }
          
              $html2 = "<tr>
                            <td style='text-align:right;'>{$demora1}</td>
                            <td style='text-align:right;width:100px'>(1,2,3,4)</td>
                        </tr><tr>
                            <td style='text-align:right;'>{$demora2}</td>
                            <td style='text-align:right;'>(2,3,4) </td>
                        </tr><tr>
                            <td style='text-align:right;'>{$demora3}</td>
                            <td style='text-align:right;'>(3,4)</td>
                        </tr>";
          return array(
            'html' => $html,
            'html1' => $html1,
            'html2' => $html2
          );
          
        }

    }

    public function edit(Request $request, $idtienda, $id)
    {
      $tienda = DB::table('tienda')->whereId($idtienda)->first();

        if($request->input('view') == 'exportar') {
            return view(sistema_view().'/consolidadocarteracredito/exportar',[
                'tienda' => $tienda,
                'fecha_inicio' => $request->fecha_inicio,
                'idagencia' => $request->idagencia,
                'idformacredito' => $request->idformacredito,
                'idasesor' => $request->idasesor,
                'tipo' => $request->tipo,
            ]);
        }
        else if( $request->input('view') == 'exportar_pdf' ){
              
            
          $where = [];

          if($request->idagencia!='' && $request->idagencia!=0){
              $where[] = ['credito.idtienda',$request->idagencia];
          }

          if($request->idasesor!='' && $request->idasesor!=0){
              $where[] = ['credito.idasesor',$request->idasesor];
          }

          if($request->inicio){
              $where[] = ['credito.fecha_desembolso','<=',$request->inicio.' 23:59:59'];
          }
          
          if($request->idformacredito!='' && $request->idformacredito!=0){
              if($request->idformacredito=='CP'){
                  $where[] = ['credito.idforma_credito',1];
              }
              elseif($request->idformacredito=='CNP'){
                  $where[] = ['credito.idforma_credito',2];
              }
              elseif($request->idformacredito=='CC'){
                  $where[] = ['credito.idforma_credito',3];
              }
          }
          
          $asesores_credito = DB::table('credito')
              ->leftjoin('users as asesor','asesor.id','credito.idasesor')
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito.idestadocredito',1)
              ->where('credito.saldo_pendientepago','>',0)
              ->where($where)
              ->select(
                  'asesor.id as idasesor',
                  'asesor.usuario as codigoasesor',
              )
              ->orderBy('asesor.usuario','asc')
              ->distinct()
              ->get();
            $agencia = DB::table('tienda')->whereId($request->idagencia)->first();
            $asesor = DB::table('users')->whereId($request->idasesor)->first();
        
            $pdf = PDF::loadView(sistema_view().'/consolidadocarteracredito/exportar_pdf',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'asesores_credito' => $asesores_credito,
                'fecha_inicio' => $request->fecha_inicio,
                'idformacredito' => $request->idformacredito,
                'asesor' => $asesor,
                'where' => $where,
            ]); 
            return $pdf->stream('CONSOLIDADO_CARTERA_DE_CREDITO.pdf');
        }  
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        
    
    }

    public function destroy(Request $request, $idtienda, $id)
    {
    }
}
