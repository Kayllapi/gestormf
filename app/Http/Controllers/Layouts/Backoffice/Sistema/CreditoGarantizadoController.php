<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditoGarantizadoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
          
            $creditos = DB::table('credito')
                ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
                ->join('users as cliente','cliente.id','credito.idcliente')
                ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
                ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
                ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                ->where('credito.estado','DESEMBOLSADO')
                ->select(
                    'credito.*',
                    'cliente.identificacion as identificacion',
                    'cliente.nombrecompleto as nombrecliente',
                )
                ->orderBy('credito.fecha_desembolso','asc')
                ->get();
          
            return view(sistema_view().'/creditogarantizado/tabla',[
                'tienda' => $tienda,
                'creditos' => $creditos,
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
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($id == 'show_credito'){
            $clientes = DB::table('users as cliente')
                ->where(function ($q) use ($request) {
                    $q->where('cliente.identificacion', 'LIKE', '%' . $request->buscar . '%')
                    ->orWhere('cliente.nombrecompleto', 'LIKE', '%' . $request->buscar . '%');
                })
                ->select(
                    'cliente.id as idcliente',
                    'cliente.identificacion as identificacion',
                    'cliente.nombrecompleto as nombrecliente',
                )
                ->get();

            $data = [];
            foreach ($clientes as $value) {
                $data[] = [
                    'id' => $value->idcliente,
                    'text' => $value->identificacion . ' - ' . $value->nombrecliente,
                ];
            }
            return $data;
        }
        elseif($id == 'showlistacreditos'){
            $cliente = DB::table('users as cliente')
                ->whereId($request->idcliente)
                ->select(
                    'cliente.id',
                    'cliente.identificacion',
                    'cliente.nombrecompleto',
                )
                ->first();

            $avales = DB::table('users')
                ->join('credito','credito.idcliente','users.id')
                ->join('credito_garantia','credito_garantia.idcredito','credito.id')
                ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                ->where('credito.idestadocredito',1)
                ->whereIn('credito.estado',['PENDIENTE','PROCESO','APROBADO','DESEMBOLSADO'])
                // ->where('credito_garantia.idgarantias_noprendarias',$id)
                ->where('credito_garantia.idcliente',$request->idcliente)
                ->where('credito_garantia.tipo','AVAL')
                ->select(
                    'users.*',
                    'credito.id as idcredito',
                    'credito.idforma_credito as idforma_credito',
                    'credito.cuenta as cuenta',
                    'credito.monto_solicitado as monto_solicitado',
                    'credito_prendatario.modalidad as modalidadproductocredito',
                )
                ->get();

            $html = '';
            $total_credito_garantizado = 0;
            $total_credito_garantizado_saldo = 0;
            foreach ($avales as $key => $value) {
                $credito_descuentocuotas = DB::table('credito_descuentocuota')
                    ->where('credito_descuentocuota.idcredito',$value->idcredito)
                    ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                    ->first();
                $total_descuento_capital = 0; 
                $total_descuento_interes = 0; 
                $total_descuento_comision = 0; 
                $total_descuento_cargo = 0;  
                $total_descuento_penalidad = 0; 
                $total_descuento_tenencia = 0; 
                $total_descuento_compensatorio = 0; 
                if($credito_descuentocuotas){
                    if(1000>=$credito_descuentocuotas->numerocuota_fin){
                        $total_descuento_capital = $credito_descuentocuotas->capital;
                        $total_descuento_interes = $credito_descuentocuotas->interes;
                        $total_descuento_comision = $credito_descuentocuotas->comision;
                        $total_descuento_cargo = $credito_descuentocuotas->cargo;
                        $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                        $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                        $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
                    }
                }
        
                $cronograma = select_cronograma(
                    $tienda->id,
                    $value->idcredito,
                    $value->idforma_credito,
                    $value->modalidadproductocredito,
                    1000,
                    $total_descuento_capital,
                    $total_descuento_interes,
                    $total_descuento_comision,
                    $total_descuento_cargo,
                    $total_descuento_penalidad,
                    $total_descuento_tenencia,
                    $total_descuento_compensatorio
                );

                $key++;
                $cuenta = $value->cuenta!=0 ? 'C'.str_pad($value->cuenta, 8, '0', STR_PAD_LEFT) : 'EN PROCESO';

                $html .= "<tr>
                        <td>{$key}</td>
                        <td>{$value->nombrecompleto}</td>
                        <td>{$value->identificacion}</td>
                        <td>{$cuenta}</td>
                        <td class='campo_moneda'>{$value->monto_solicitado}</td>
                        <td class='campo_moneda'>{$cronograma['saldo_capital']}</td>
                    </tr>";
                $total_credito_garantizado += $value->monto_solicitado;
                $total_credito_garantizado_saldo += $cronograma['saldo_capital'];
            }
            return array(
                'cliente' => $cliente,
                'html' => $html,
                'total_credito_garantizado' => number_format($total_credito_garantizado, 2, '.', ''),
                'total_credito_garantizado_saldo' => number_format($total_credito_garantizado_saldo, 2, '.', ''),
            );
        }
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
