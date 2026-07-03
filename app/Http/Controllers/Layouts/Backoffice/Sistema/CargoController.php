<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class CargoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view') == 'tabla'){
            $agencias = DB::table('tienda')->get();
            return view(sistema_view().'/cargo/tabla',[
              'tienda' => $tienda,
              'agencias' => $agencias,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->view == 'registrar') {
          
            $tipocargos = DB::table('credito_tipocargo')->get();
          
                
            return view(sistema_view().'/cargo/create',[
                'tienda' => $tienda,
                'idcredito' => $request->idcredito,
                'tipocargos' => $tipocargos,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        if($request->input('view') == 'registrar') {
            $rules = [
                'idtipocargo' => 'required',           
                'importe' => 'required',              
                'descripcion' => 'required',                              
            ];
            $messages = [
                'idtipocargo.required' => 'El "Tipo Cargo" es Obligatorio.',
                'importe.required' => 'El "Importe" es Obligatorio.',
                'descripcion.required' => 'La "Descripción" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
            
            /*$credito_cargo = DB::table('credito_cargo')
                ->where('credito_cargo.idcredito',$request->idcredito)
                ->where('credito_cargo.idestadocredito_cargo',1)
                ->first();
            if($credito_cargo){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Ya existe un cargo pendiente!!.'
                ]);
            }*/
            
            $credito_cargo = DB::table('credito_cargo')
                ->orderBy('credito_cargo.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($credito_cargo!=''){
                $codigo = $credito_cargo->codigo+1;
            }
          
            DB::table('credito_cargo')->insert([
               'fecharegistro'        => Carbon::now(),
               'codigo'              => $codigo,
               'idcredito_tipocargo'  => $request->input('idtipocargo'),
               'importe'              => $request->input('importe'),
               'descripcion'          => $request->input('descripcion'),
               'idcredito'            => $request->input('idcredito'),
               'idestadocredito_cargo'=> 1,
               'idresponsable'        => Auth::user()->id,
               'idtienda'             => $idtienda,
               'idestado'             => 1,
            ]);

          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        if($id == 'show_credito'){
          
          $creditos = DB::table('credito')
            ->join('users as cliente','cliente.id','credito.idcliente')
            ->where('credito.estado','DESEMBOLSADO')
            ->where(function ($q) use ($request) {
                $q->where('cliente.identificacion', 'LIKE', '%' . $request->buscar . '%')
                  ->orWhere('cliente.nombrecompleto', 'LIKE', '%' . $request->buscar . '%');
            })
            ->select(
                'cliente.id as idcliente',
                'cliente.identificacion as identificacion',
                'cliente.nombrecompleto as nombrecliente',
                DB::raw('MAX(credito.fecha_desembolso) as fecha_desembolso')
            )
            ->groupBy(
                'cliente.id',
                'cliente.identificacion',
                'cliente.nombrecompleto'
            )
            ->orderBy('fecha_desembolso','asc')
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
        else if($id == 'showlistacreditos'){
            $where = [];
            if($request->idagencia!=''){
                $where[] = ['credito.idtienda',$request->idagencia];
            }
            if($request->idcliente!=''){
                $where[] = ['credito.idcliente',$request->idcliente];
            }
            if($request->idasesor!='' && $request->idasesor!='0'){
                $where[] = ['credito.idasesor',$request->idasesor];
            }

          $cliente = DB::table('users')->whereId($request->idcliente)->select('users.id','users.nombrecompleto','users.identificacion')->first();
          $creditos = DB::table('credito')
                            ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
                            ->join('users as cliente','cliente.id','credito.idcliente')
                            ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
                            ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
                            ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                            ->where('credito.estado','DESEMBOLSADO')
                            ->where('cliente.id',$request->idcliente)
                            ->where('credito.idestadocredito',1)
                            ->where($where)
                            ->select(
                                'credito.*',
                                'cliente.identificacion as identificacion',
                                'cliente.nombrecompleto as nombrecliente',
                            )
                            ->orderBy('credito.fecha_desembolso','asc')
                            ->get();

                              
          $html = '';
          $asesor = '';
          $idasesor = '';
          foreach($creditos as $value){
              $cp = '';
              if($value->idforma_credito==1){
                  $cp = 'CP';
              }
              elseif($value->idforma_credito==2){
                  $cp = 'CNP';
              }
              elseif($value->idforma_credito==3){
                  $cp = 'CC';
              }
              $cuenta = str_pad($value->cuenta, 8, "0", STR_PAD_LEFT);
              $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data(this)'>
                            <td style='text-align: right;width: 70px;'>S/ {$value->monto_solicitado}</td>
                            <td style='width: 20px;'>{$cp}</td>
                            <td>C{$cuenta}</td>
                        </tr>";
                $idasesor = $value->idasesor;
          }

          if ($idasesor!='' && $idasesor!=0 && $idasesor!=null) {
            $usuario = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[3,4,7])
                ->where('users.id', $idasesor)
                ->select('users.nombrecompleto','permiso.nombre as nombrepermiso')
                ->first();
            $asesor = "$usuario->nombrecompleto ($usuario->nombrepermiso)";
          }
          return array(
            'cliente' => $cliente,
            'html' => $html,
            'asesor' => $asesor
          );
          
        }
        else if($id == 'show_cargo'){
   
            $where = [];
            //if($request->idestado==1){
                $where[] = ['credito_cargo.idestadocredito_cargo',1];
            //}
            
          $credito_cargo = DB::table('credito_cargo')
              ->join('credito_tipocargo','credito_tipocargo.id','credito_cargo.idcredito_tipocargo')
              ->where('credito_cargo.idcredito',$request->idcredito)
              ->where($where)
              ->select(
                  'credito_cargo.*',
                  'credito_tipocargo.nombre as tipocargonombre',
              )
              ->orderBy('credito_cargo.id','asc')
              ->get();
          
          $html = '<table class="table table-bordered" id="table-detalle-tipocargo">
              <thead>
              <tr>
                <th>Tipo Cargos</th>
                <th>Importe</th>
                <th>Fecha</th>
                <th>Descripción</th>
              </tr>
              </thead>
              <tbody>';
          
          foreach($credito_cargo as $value){
              $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data_descuentodecuotas(this)'>
                            <td>{$value->tipocargonombre}</td>
                            <td>{$value->importe}</td>
                            <td>{$value->fecharegistro}</td>
                            <td>{$value->descripcion}</td>
                        </tr>";
          }
          $html .= "</tbody></table>";
          return array(
            'html' => $html
          );
          
        }
        else if($id == 'show_cargo_cobranza'){
            $where = [];
            $where[] = ['credito_cargo.idestadocredito_cargo',1];
            
          $credito_cargo = DB::table('credito_cargo')
              ->join('credito_tipocargo','credito_tipocargo.id','credito_cargo.idcredito_tipocargo')
              ->where('credito_cargo.idcredito',$request->idcredito)
              ->where($where)
              ->select(
                  'credito_cargo.*',
                  'credito_tipocargo.nombre as tipocargonombre',
              )
              ->orderBy('credito_cargo.id','asc')
              ->get();
          
          $html = '<table class="table table-bordered" id="table-detalle-tipocargo">
              <thead>
              <tr>
                <th></th>
                <th>Tipo Cargos</th>
                <th>Importe</th>
                <th>Fecha</th>
                <th>Descripción</th>
              </tr>
              </thead>
              <tbody>';

            $label = '';
            foreach($credito_cargo as $value){
              if($request->opcion_pago != 'PAGO_TOTAL'){
                  $label = '<label class="chk">
                              <input type="checkbox"
                                  class="credito-cargo-check"
                                  value="'.$value->id.'"
                                  onclick="seleccionar_cargo_cobranza(this)"
                                  checked>
                              <span class="checkmark"></span>
                          </label>';
              }
              $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data_descuentodecuotas(this)'>
                            <td>".$label."</td>
                            <td>{$value->tipocargonombre}</td>
                            <td>{$value->importe}</td>
                            <td>{$value->fecharegistro}</td>
                            <td>{$value->descripcion}</td>
                        </tr>";
          }
          $html .= "</tbody></table>";
          return array(
            'html' => $html
          );
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      $credito = DB::table('credito')
                    ->join('users as cliente','cliente.id','credito.idcliente')
                    ->leftjoin('users as aval','aval.id','credito.idaval')
                    ->join('forma_credito','forma_credito.id','credito.idforma_credito')
                    ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
                    ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
                    ->join('tipo_destino_credito','tipo_destino_credito.id','credito.idtipo_destino_credito')
                    ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
                    ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                    ->where('credito.id',$id)

                    ->select(
                        'credito.*',
                        'cliente.codigo as codigo_cliente',
                        'cliente.identificacion as docuementocliente',
                        'cliente.nombrecompleto as nombreclientecredito',
                        'aval.identificacion as documentoaval',
                        'aval.nombrecompleto as nombreavalcredito',
                        'forma_credito.nombre as forma_credito_nombre',
                        'tipo_operacion_credito.nombre as tipo_operacion_credito_nombre',
                        'modalidad_credito.nombre as modalidad_credito_nombre',
                        'forma_pago_credito.nombre as forma_pago_credito_nombre',
                        'tipo_destino_credito.nombre as tipo_destino_credito_nombre',
                        'credito_prendatario.nombre as nombreproductocredito',
                        'credito_prendatario.modalidad as modalidad_calculo',
                        'credito_prendatario.conevaluacion as conevaluacion',
                    )
                    ->orderBy('credito.id','desc')
                    ->first();
      
      if($request->input('view') == 'editar') {
        
        return view(sistema_view().'/cargo/edit',[
            'tienda' => $tienda,
            'credito' => $credito,
        ]);
      }
      else if($request->input('view') == 'eliminar'){
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->where('users_permiso.idpermiso',1)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
        return view(sistema_view().'/cargo/delete',[
            'tienda' => $tienda,
            'credito' => $credito,
            'idcredito_descuentocuota' => $id,
                'usuarios' => $usuarios,
        ]);
      }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        //
    
    }


    public function destroy(Request $request, $idtienda, $id)
    {

      if( $request->input('view') == 'eliminar' ){
        
            $rules = [         
                'idresponsable' => 'required',          
                'responsableclave' => 'required',                       
            ];
          
            $messages = [
                'idresponsable.required' => 'El "Responsable" es Obligatorio.',
                'responsableclave.required' => 'La "Contraseña" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
        
            $usuario = DB::table('users')
                ->where('users.id',$request->idresponsable)
                ->where('users.clave',$request->responsableclave)
                ->first();
            $idresponsable = 0;
            if($usuario==''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El usuario y/o la contraseña es incorrecta!!.'
                ]);
                $idresponsable = $usuario->id;
            }else{
              
                DB::table('credito_cargo')->whereId($id)->delete();
                return response()->json([
                    'resultado'           => 'CORRECTO',
                    'mensaje'             => 'Se ha elimino correctamente.',
                ]);
            }
          
        
        
      }
    }
}
