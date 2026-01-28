<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class GarantiaentregadaController extends Controller
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
          
            return view(sistema_view().'/garantiaentregada/tabla',[
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
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showtable'){
          $where = [];
          $where[] = ['credito.idtienda',$idtienda];
          if($request->idcliente!=''){
              $where[] = ['credito.idcliente',$request->idcliente];
          }
          $where[] = ['credito_garantia.fechaentrega','>=',$request->inicio.' 00:00:00'];
          $where[] = ['credito_garantia.fechaentrega','<=',$request->fin.' 23:59:59'];
          
          $credito_garantias = DB::table('credito_garantia')
              ->join('credito','credito.id','credito_garantia.idcredito')
              ->where($where)
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito_garantia.idestadoentrega',2)
              ->where('credito.idestadocredito',2)
              ->where('credito.idforma_credito',1)
              ->select(
                  'credito_garantia.*',
                  'credito.clientenombrecompleto as clientenombrecompleto',
                  'credito.clienteidentificacion as clienteidentificacion',
                  'credito.fecha_cancelado as fecha_cancelado',
                  'credito.monto_solicitado as monto_solicitado',
              )
             ->get();
          
          $html = '<table class="table table-striped table-hover" id="table-lista-credito">
              <thead class="table-dark" style="position: sticky;top: 0;">
                <tr>
                  <th style="text-align:center">N°</th>
                  <th style="text-align:center">FECHA</th>
                  <th style="text-align:center">CLIENTE</th>
                  <th style="text-align:center">RUC/DNI/CE</th>
                  <th style="text-align:center">GARANTIA</th>
                  <th style="text-align:center">MONTO</th>
                  <th style="text-align:center">TIPO DE GARANTIA</th>
                  <th style="text-align:center">SERIE</th>
                  <th style="text-align:center">PLACA</th>
                  <th style="text-align:center">MODELO</th>
                  <th style="text-align:center">VALOR COMERCIAL</th>
                </tr>
              </thead>
              <tbody>';
          
          foreach($credito_garantias as $key => $value){
              $fecha_cancelado = date_format(date_create($value->fechaentrega),'d-m-Y H:i:s A');
              $html .= "<tr id='show_data_select' idcredito_garantia='{$value->id}'>
                            <td>".($key+1)."</td>
                            <td>{$fecha_cancelado}</td>
                            <td>{$value->clientenombrecompleto}</td>
                            <td>{$value->clienteidentificacion}</td>
                            <td>{$value->descripcion}</td>
                            <td style='text-align:right'>{$value->monto_solicitado}</td>
                            <td>{$value->garantias_tipogarantia}</td>
                            <td>{$value->garantias_serie_motor_partida}</td>
                            <td>{$value->garantias_placa}</td>
                            <td>{$value->garantias_modelo_tipo}</td>
                            <td style='text-align:right'>{$value->valor_comercial}</td>
                        </tr>";
          
          }
          if(count($credito_garantias)==0){
              $html.= '<tr><td colspan="16" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }
              $html .= '
            </table>';
          return array(
            'html' => $html
          );
          
        }

    }

    public function edit(Request $request, $idtienda, $id)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        $credito_garantia = DB::table('credito_garantia')
              ->where('credito_garantia.id',$id)
              ->select(
                  'credito_garantia.*',
              )
             ->first();
                
        if($request->input('view') == 'ticket_garantia') {
            return view(sistema_view().'/garantiaentregada/ticket_garantia',[
              'tienda' => $tienda,
              'credito_garantia' => $credito_garantia,
            ]);
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
    }

    public function destroy(Request $request, $idtienda, $id)
    {
      
    }
}
