<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class CalculoCompuestoController extends Controller
{
    public function __construct()
    {
        $this->tipo_credito = DB::table('tipo_credito')->get();
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
            return view(sistema_view().'/calculocompuesto/tabla',[
              'tienda' => $tienda,
              'forma_pago_credito' => $this->forma_pago_credito,
              
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
       
      
        if($request->view == 'registrar') {
            return view(sistema_view().'/calculocompuesto/create',[
              'tienda' => $tienda,
              'modalidad_credito' => $this->modalidad_credito,
              'tipo_operacion_credito' => $this->tipo_operacion_credito,
              'forma_credito' => $this->forma_credito,
              'tipo_destino_credito' => $this->tipo_destino_credito,
            ]);
        }
    }
  
  

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'show_producto_credito'){
          
          $producto_credito = DB::table('credito_prendatario')
                              ->where('credito_prendatario.modalidad',$request->input('modalidad'))
                              ->where('credito_prendatario.estado','ACTIVO')
                              ->select('credito_prendatario.*')
                              ->orderBy('credito_prendatario.id', 'asc')
                              ->get();

          return $producto_credito;
          
        }
        else if($id == 'showtasa'){
          $monto = $request->input('monto');
          $cuota = $request->input('numerocuota');
          $tasaCercana = DB::table('tarifario')
            ->where('tarifario.idcredito_prendatario',$request->input('producto'))
            ->where('tarifario.idforma_pago_credito',$request->input('frecuencia'))
            ->select('tarifario.*')
            ->selectRaw('ABS(monto - ?) + ABS(cuotas - ?) as diferencia_total', [$monto, $cuota])
            ->orderBy('diferencia_total')
            ->first();
          
          return $tasaCercana;
        }
        else if($id=='cronograma'){
          
          
         $credito = DB::table('credito')
                  ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                  ->where('credito.idcredito_prendatario',$request->input('producto'))
                  ->select(
                      'credito.*',
                      'credito_prendatario.modalidad as modalidad_calculo',
                  )
                  ->first();
          
          $montomaximo = DB::table('tarifario')
                ->where('tarifario.idcredito_prendatario',$credito->idcredito_prendatario)
                ->where('tarifario.idforma_pago_credito',$request->input('frecuencia'))
                ->orderBy('tarifario.monto','desc')
                ->limit(1)
                ->first();
          
          if($montomaximo!=''){
              if($request->input('monto')>$montomaximo->monto){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'El monto máximo según el tarifario es '.$montomaximo->monto.'.',
                  ]);
              }
          }
          
          
          $cuotamaximo = DB::table('tarifario')
                ->where('tarifario.idcredito_prendatario',$credito->idcredito_prendatario)
                ->where('tarifario.idforma_pago_credito',$request->input('frecuencia'))
                ->orderBy('tarifario.cuotas','desc')
                ->limit(1)
                ->first();
          if($cuotamaximo!=''){
              if($request->input('numerocuota')>$cuotamaximo->cuotas){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'La cuota máxima según el tarifario es '.$cuotamaximo->cuotas.'.',
                  ]);
              }
          }
          
          
          $tasatarifario = DB::table('tarifario')
                ->where('tarifario.idcredito_prendatario',$credito->idcredito_prendatario)
                ->where('tarifario.idforma_pago_credito',$request->input('frecuencia'))
                ->where('tarifario.monto','>=',$request->input('monto'))
                ->where('tarifario.cuotas','>=',$request->input('numerocuota'))
                ->orderBy('tarifario.cuotas','asc')
                ->orderBy('tarifario.monto','asc')
                ->limit(1)
                ->first();
          $tasa_tem = $request->input('tasa');
          $tasa_tem_minima = 0;
          $comision_cargo = 0;
          if($tasatarifario!=''){
              $comision_cargo = $tasatarifario->cargos_otros;
              $tasa_tem_minima = $tasatarifario->tem;
              if($request->input('tasa')!='' && $request->input('tasa')>0 && $request->input('tasa') < $tasatarifario->tem){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'El tasa mínima según el tarifario es '.$tasatarifario->tem.'.',
                  ]);
              }else{
                  if($request->input('tasa')=='' or $request->input('tasa')==0){
                      $tasa_tem = $tasatarifario->tem;
                  }
              }
          }else{
              return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje'   => 'No se asignado ningún tarifario para esta frecuencia de pago!!.',
              ]);
          }
          $frecuenciaDiasMap = [
            1 => 26,
            2 => 4,
            3 => 2,
            4 => 1,
          ];
          $dias = $frecuenciaDiasMap[$request->input('frecuencia')];
          $tasa_tip = number_format(($tasa_tem / $dias) * $request->input('numerocuota'), 2, '.', '');
          if($credito->modalidad_calculo == 'Interes Compuesto'){
              $tasa_tip = $tasa_tem;
          }

          $cronograma = genera_cronograma(
                $request->input('monto'),
                $request->input('numerocuota'),
                $request->input('fechainicio'),
                $request->input('frecuencia'),
                $tasa_tip,
                $request->input('tipotasa'),
                $request->input('dia_gracia'),
                $comision_cargo,
                $request->input('cargo')
          );
          
          $html = '';
          foreach($cronograma['cronograma'] as $value){
            
            $html .= '<tr>
                        <td>'.$value['numero'].'</td>
                        <td>'.$value['fecha'].'</td>
                        <td>'.$value['saldo'].'</td>
                        <td>'.$value['amortizacion'].'</td>
                        <td>'.$value['interes'].'</td>
                        <td>'.$value['comision'].'</td>
                        <td>'.$value['cargo'].'</td>
                        <td>'.$value['cuotafinal'].'</td>
                      </tr>';
          }
          $html .= '<tr>
                        <td style="background-color: #144081;"></td>
                        <td style="background-color: #144081;"></td>
                        <td style="background-color: #144081;color: white;font-weight: bold;">TOTAL</td>
                        <td style="background-color: #144081;color: white;font-weight: bold;">'.$cronograma['total_amortizacion'].'</td>
                        <td style="background-color: #144081;color: white;font-weight: bold;">'.$cronograma['total_interes'].'</td>
                        <td style="background-color: #144081;color: white;font-weight: bold;">'.$cronograma['total_comision'].'</td>
                        <td style="background-color: #144081;color: white;font-weight: bold;">'.$cronograma['total_cargo'].'</td>
                        <td style="background-color: #144081;color: white;font-weight: bold;">'.$cronograma['total_cuotafinal'].'</td>
                      </tr>';
          return array(
            'cronograma' => $html,
            'tasa_tem' => $tasa_tem,
            'tasa_tem_minima' => $tasa_tem_minima,
            'tasa_tip' => $tasa_tip,
            'cargootros' => $comision_cargo,
            'interes_total' => $cronograma['total_interes'],
            'cargo_total' => $cronograma['total_comisioncargo'],
            'total_pagar' => $cronograma['total_cuotafinal']
          );
        }
        else if($id="showtarifarioproducto"){
           $tarifario_producto = DB::table('tarifario')
                            ->join('forma_pago_credito','forma_pago_credito.id','tarifario.idforma_pago_credito')
                            ->join('credito_prendatario','credito_prendatario.id','tarifario.idcredito_prendatario')
                            ->where('tarifario.idcredito_prendatario',$request->idproducto)
                            ->where('tarifario.idforma_pago_credito',$request->idforma_pago_credito)
                            ->select(
                                'tarifario.*',
                                 'credito_prendatario.nombre as nombreproducto',
                                'forma_pago_credito.nombre as nombreformapago',          
                            )
                            ->orderBy('tarifario.id','desc')
                            ->get();
          $data = "";
          foreach($tarifario_producto as $value){
            $data .= "<tr>
                        <td>{$value->nombreformapago}</td>
                        <td>{$value->monto}</td>
                        <td>{$value->cuotas}</td>
                        <td>{$value->tem}</td>
                        <td>{$value->nombreproducto}</td>
                      </tr>";
          }
          return  $data;
        }

    }

    
}
