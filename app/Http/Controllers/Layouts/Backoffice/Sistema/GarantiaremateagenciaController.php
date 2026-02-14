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

class GarantiaremateagenciaController extends Controller
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
          
            return view(sistema_view().'/garantiaremateagencia/tabla',[
              'tienda' => $tienda,
              'agencias' => $agencias,
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

        if($id == 'showcliente_asignar'){
          $where = [];
          $where[] = ['credito.idtienda',$request->idagencia];

          if($request->idasesor!='' && $request->idasesor!=0){
              $where[] = ['credito.idasesor',$request->idasesor];
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
          
          $creditos = DB::table('credito')
              ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->join('ubigeo','ubigeo.id','cliente.idubigeo')
              //->join('credito_garantia','credito_garantia.idcliente','credito.idcliente')
              ->leftjoin('users as cajero','cajero.id','credito.idcajero')
              ->leftjoin('users as asesor','asesor.id','credito.idasesor')
              ->leftjoin('users as administrador','administrador.id','credito.idadministrador')
              ->leftjoin('users as aval','aval.id','credito.idaval')
              ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
              ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito.idestadocredito',1)
              ->whereNotIn('credito.id', function ($query) {
                  $query->select('idcredito')
                        ->from('credito_garantia')
                        ->where('estado_listagarantia', '<>', 0);
              })
              ->where($where)
              ->select(
                  'credito.*',
                  'cliente.identificacion as identificacioncliente',
                  'cliente.nombrecompleto as nombrecliente',
                  'cliente.numerotelefono as telefonocliente',
                  'cliente.direccion as direccioncliente',
                  'aval.identificacion as identificacionaval',
                  'aval.nombrecompleto as nombreaval',
                  'credito_prendatario.nombre as nombreproductocredito' ,
                  'credito_prendatario.modalidad as modalidadproductocredito',
                  'modalidad_credito.nombre as nombremodalidadcredito' ,
                  'forma_pago_credito.nombre as frecuencianombre' ,
                  'cajero.usuario as codigocajero',
                  'asesor.codigo as codigoasesor',
                  'administrador.nombrecompleto as nombreadministrador',
                  'ubigeo.nombre as ubigeonombre',
              )
              ->orderBy('credito.fecha_desembolso','asc')
              ->get();
          
          $dias_tolerancia = configuracion($request->idagencia,'dias_tolerancia_garantia')['valor'];
          $html = '';
          $data = [];
          foreach($creditos as $key => $value){
            
              // descuento cuota
              $credito_descuentocuotas = DB::table('credito_descuentocuota')
                    ->where('credito_descuentocuota.idcredito',$value->id)
                    ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                    ->first();
              $total_descuento_capital = 0; 
              $total_descuento_interes = 0; 
              $total_descuento_comision = 0; 
              $total_descuento_cargo = 0;  
              $total_descuento_penalidad = 0; 
              $total_descuento_tenencia = 0; 
              $total_descuento_compensatorio = 0; 
              $total_descuento_total = 0; 
              if($credito_descuentocuotas){
                  if($request->numerocuota>=$credito_descuentocuotas->numerocuota_fin){
                      $total_descuento_capital = $credito_descuentocuotas->capital;
                      $total_descuento_interes = $credito_descuentocuotas->interes;
                      $total_descuento_comision = $credito_descuentocuotas->comision;
                      $total_descuento_cargo = $credito_descuentocuotas->cargo;
                      $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                      $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                      $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
                      $total_descuento_total = $credito_descuentocuotas->total;
                  }
              }
            
              $cronograma = select_cronograma(
                  $value->idtienda,
                  $value->id,
                  $value->idforma_credito,
                  $value->modalidadproductocredito,
                  $value->cuotas,
                  $total_descuento_capital,
                  $total_descuento_interes,
                  $total_descuento_comision,
                  $total_descuento_cargo,
                  $total_descuento_penalidad,
                  $total_descuento_tenencia,
                  $total_descuento_compensatorio,
                  0,
                  1,
                  'detalle_cobranza'
              );
            
              if((0<$cronograma['ultimo_atraso'])){
                  
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
                
                  $clasificacion = '';

                  if($cronograma['ultimo_atraso']<=8){
                      $clasificacion = 'NORMAL';
                  }
                  elseif($cronograma['ultimo_atraso']>8 && $cronograma['ultimo_atraso']<=30){
                      $clasificacion = 'CPP';
                  }
                  elseif($cronograma['ultimo_atraso']>30 && $cronograma['ultimo_atraso']<=60){
                      $clasificacion = 'DIFICIENTE';
                  }
                  elseif($cronograma['ultimo_atraso']>60 && $cronograma['ultimo_atraso']<=120){
                      $clasificacion = 'DUDOSO';
                  }
                  elseif($cronograma['ultimo_atraso']>120){
                      $clasificacion = 'PÉRDIDA';
                  }
                
                  $color_estado = '';

                  if($cronograma['ultimo_atraso']>0 && $cronograma['ultimo_atraso']<=$dias_tolerancia){
                      $color_estado = 'background-color:#b6e084;';
                  }
                  elseif($cronograma['ultimo_atraso']>$dias_tolerancia){
                      $color_estado = 'background-color:#ff9d9d;';
                  }
                  elseif($cronograma['ultimo_atraso']==0){
                      $color_estado = 'background-color:#fff;';
                  }

                  $credito_compromisopago = DB::table('credito_compromisopago')
                      ->where('idcredito',$value->id)
                      ->orderBy('id','desc')
                      ->limit(1)
                      ->first();

                  if($credito_compromisopago!=''){
                      if($credito_compromisopago->fechacompromiso<=Carbon::now()->format('Y-m-d')){
                            $color_estado = 'background-color:#ffb549;';
                      }else{
                            $color_estado = 'background-color:#f86b6b;';
                      } 
                  }
                
                  //  adelanto
                  $credito_cobranzacuotas = DB::table('credito_cobranzacuota')
                      ->where('credito_cobranzacuota.idcredito',$value->id)
                      ->get();

                  $totaladelanto = 0;
                  $ultimafechaadelanto = 0;
                  foreach($credito_cobranzacuotas as $valueade){
                      $totaladelanto = $valueade->total_pagar;
                      $ultimafechaadelanto = date_format(date_create($valueade->fecharegistro),'d-m-Y h:i:s A');
                  }

                  $fechacobranza_fecharegistro = '';
                  if($totaladelanto>0){
                      $fechacobranza_fecharegistro = $ultimafechaadelanto;
                  }
                  // fin adelanto

                  $data[] = [
                      'id' => $value->id,
                      'estado' => $value->estado,
                      'key' => ($key+1),
                      'cuenta' => 'C'.$value->cuenta,
                      'identificacioncliente' => $value->identificacioncliente,
                      'nombrecliente' => $value->nombrecliente,
                      'identificacionaval' => $value->identificacionaval,
                      'nombreaval' => $value->nombreaval,
                      'fecha_desembolso' => date_format(date_create($value->fecha_desembolso),'d-m-Y H:i:s A'),
                      'monto_solicitado' => $value->monto_solicitado,
                      'saldo_pendientepago' => $value->saldo_pendientepago,
                      'cuota_vencida' => $cronograma['cuota_vencida'],
                      'frecuencianombre' => $value->frecuencianombre,
                      'cuotas' => $cronograma['numero_cuota_vencida'],
                      'cp' => $cp,
                      'ultimo_atraso' => $cronograma['ultimo_atraso'],
                      'clasificacion' => $clasificacion,
                      'nombreproductocredito' => $value->nombreproductocredito,
                      'nombremodalidadcredito' => $value->nombremodalidadcredito,
                      'telefonocliente' => $value->telefonocliente,
                      'direccioncliente' => $value->direccioncliente.', '.$value->ubigeonombre,
                    
                      //'total_pendientepago' => $value->total_pendientepago,
                      'fechacompromiso' => ($credito_compromisopago!=''?date_format(date_create($credito_compromisopago->fechacompromiso),'d-m-Y'):''),
                      'comentario' => ($credito_compromisopago!=''?$credito_compromisopago->comentario:''),
                      //'fechacobranza_fecharegistro' => $fechacobranza_fecharegistro,
                      //'fecha_ultimopago' => date_format(date_create($value->fecha_ultimopago),'d-m-Y'),
                      'codigoasesor' => $value->codigoasesor,
                      'style' => $color_estado,
                  ];
                     
                       
              }   
          }
            
          $creditos_ordenado = sistema_order_array($data, 'ultimo_atraso',SORT_DESC);
          
          $i = 1;
          $data_ordenado = [];
          foreach($creditos_ordenado as $value){
                  $data_ordenado[] = [
                      'id' => $value['id'],
                      'estado' => $value['estado'],
                      'key' => $i,
                      'cuenta' => $value['cuenta'],
                      'identificacioncliente' => $value['identificacioncliente'],
                      'nombrecliente' => $value['nombrecliente'],
                      'identificacionaval' => $value['identificacionaval'],
                      'nombreaval' => $value['nombreaval'],
                      'fecha_desembolso' => $value['fecha_desembolso'],
                      'monto_solicitado' => $value['monto_solicitado'],
                      'saldo_pendientepago' => $value['saldo_pendientepago'],
                      'cuota_vencida' => $value['cuota_vencida'],
                      'frecuencianombre' => $value['frecuencianombre'],
                      'cuotas' => $value['cuotas'],
                      'cp' => $value['cp'],
                      'ultimo_atraso' => $value['ultimo_atraso'],
                      'clasificacion' => $value['clasificacion'],
                      'nombreproductocredito' => $value['nombreproductocredito'],
                      'nombremodalidadcredito' => $value['nombremodalidadcredito'],
                      'telefonocliente' => $value['telefonocliente'],
                      'direccioncliente' => $value['direccioncliente'],
                      'fechacompromiso' => $value['fechacompromiso'],
                      'comentario' => $value['comentario'],
                      'codigoasesor' => $value['codigoasesor'],
                      'style' => $value['style'],
                  ];
              $i++;  
          }
            return response()->json([
                'data'            => $data_ordenado,
            ]);
          
        }
        elseif($id == 'showcliente_destino'){
          $where = [];
          //$where[] = ['credito_garantia.idtienda',$request->idagencia];
          
          $credito_garantias = DB::table('credito_garantia')
              ->join('credito','credito.id','credito_garantia.idcredito')
              ->join('users as cliente','cliente.id','credito_garantia.idcliente')
              ->where('credito_garantia.estado_listagarantia','<>',0)
              ->where('credito.idestadocredito',1)
              ->where('credito.estado','DESEMBOLSADO')
              ->where($where)
              ->select(
                'credito_garantia.*',
                'cliente.nombrecompleto as clientenombrecompleto',
                'cliente.identificacion as dni'
              )
              ->orderBy('credito_garantia.fecharegistro_listaremate','asc')
              ->get();
         
            $tabla = [];
            $orden = 1;
            foreach($credito_garantias as $value){
                
              $tabla[]=[
                  'id'      => $value->idcredito,
                  'cliente'   => $value->clientenombrecompleto,
                  'dni'     => $value->dni,
                  'tipo_garantia'  => $value->garantias_tipogarantia,
                  'descripcion'  => $value->descripcion,
                  'modelo'  => $value->garantias_modelo_tipo,
                  'valorcomercial'  => $value->valor_comercial,
                  'accesorios'  => $value->garantias_accesorio_doc,
                  'cobertura'  => $value->valor_realizacion,
                  'color'  => $value->garantias_color,
                  'codigo_garantia'  => $value->garantias_codigo,
              ];
              $orden++;
            }
            
            return response()->json([
                'data'            => $tabla,
            ]);
          
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'autorizar'){
            $usuarios = DB::table('users')
                  ->join('users_permiso','users_permiso.idusers','users.id')
                  ->join('permiso','permiso.id','users_permiso.idpermiso')
                  ->where('users_permiso.idpermiso',1)
                  ->where('users_permiso.idtienda',$idtienda)
                  ->select('users.*','permiso.nombre as nombrepermiso')
                  ->get();
            return view(sistema_view().'/garantiaremateagencia/autorizar',[
              'tienda' => $tienda,
              'usuarios' => $usuarios,
            ]);
        }
        elseif($request->input('view') == 'quitar'){
            $usuarios = DB::table('users')
                  ->join('users_permiso','users_permiso.idusers','users.id')
                  ->join('permiso','permiso.id','users_permiso.idpermiso')
                  ->where('users_permiso.idpermiso',1)
                  ->where('users_permiso.idtienda',$idtienda)
                  ->select('users.*','permiso.nombre as nombrepermiso')
                  ->get();
            return view(sistema_view().'/garantiaremateagencia/quitar',[
              'tienda' => $tienda,
              'usuarios' => $usuarios,
            ]);
        }
        elseif($request->input('view') == 'ver_garantia'){
            $credito_garantias = DB::table('credito_garantia')
              ->join('users as cliente','cliente.id','credito_garantia.idcliente')
              //->where('credito_garantia.estado_listagarantia','<>',0)
              ->where('credito_garantia.idcredito',$request->idcredito)
              ->select(
                'credito_garantia.*',
                'cliente.nombrecompleto as clientenombrecompleto',
                'cliente.identificacion as dni'
              )
              ->orderBy('credito_garantia.fecharegistro_listaremate','asc')
              ->get();
            return view(sistema_view().'/garantiaremateagencia/ver_garantia',[
              'tienda' => $tienda,
              'credito_garantias' => $credito_garantias,
            ]);
        }
        elseif($request->input('view') == 'ver_liquidacion_garantia'){
            $credito = DB::table('credito')->whereId($request->idcredito)->first();
            $credito_garantias = DB::table('credito_garantia')
              ->join('users as cliente','cliente.id','credito_garantia.idcliente')
              ->where('credito_garantia.idcredito',$request->idcredito)
              ->select(
                'credito_garantia.*',
                'cliente.nombrecompleto as clientenombrecompleto',
                'cliente.identificacion as dni'
              )
              ->orderBy('credito_garantia.fecharegistro_listaremate','asc')
              ->get();
            return view(sistema_view().'/garantiaremateagencia/ver_liquidacion_garantia',[
                'tienda' => $tienda,
                'credito' => $credito,
                'credito_garantias' => $credito_garantias,
            ]);
        }
        elseif($request->input('view') == 'ver_generarficha_liquidacion'){
            $credito = DB::table('credito')->whereId($request->idcredito)->first();
            return view(sistema_view().'/garantiaremateagencia/ver_generarficha_liquidacion',[
                'tienda' => $tienda,
                'credito' => $credito,
            ]);
        }
        elseif($request->input('view') == 'ver_registrarprecio_liquidacion'){
            $credito = DB::table('credito')->whereId($request->idcredito)->first();
            return view(sistema_view().'/garantiaremateagencia/ver_registrarprecio_liquidacion',[
                'tienda' => $tienda,
                'credito' => $credito,
            ]);
        }
        elseif (request('view') == 'ver_generarficha_liquidacionpdf') {
            $credito = DB::table('credito')->whereId($request->idcredito)->first();
            $credito_garantias = DB::table('credito_garantia')
              ->join('users as cliente','cliente.id','credito_garantia.idcliente')
              ->where('credito_garantia.idcredito',$request->idcredito)
              ->select(
                'credito_garantia.*',
                'cliente.nombrecompleto as clientenombrecompleto',
                'cliente.identificacion as dni'
              )
              ->orderBy('credito_garantia.fecharegistro_listaremate','asc')
              ->get();
            $pdf = PDF::loadView(sistema_view().'/garantiaremateagencia/ver_generarficha_liquidacionpdf', compact(
                'tienda',
                'credito',
                'credito_garantias',
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('GENERAR FICHA.pdf');
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        if($request->input('view') == 'autorizar') {
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
            if($usuario!=''){
                $idresponsable = $usuario->id;
            }else{
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El usuario y/o la contraseña es incorrecta!!.'
                ]);
            }
          
            $rules['idagencia']                   =  'required';
            //$rules['idresponsable_modificado']                   =  'required';
            $rules['check_origen']                =  'required';
            $messages['idagencia.required']       = 'El campo "Agencia" es obligatorio.';
            //$messages['idresponsable_modificado.required']       = 'No hay autorización!!.';
            $messages['check_origen.required']    = 'Debe seleccionar minimo un cliente.';

            $this->validate($request,$rules,$messages);
						foreach(json_decode($request->check_origen) as $value){
                $credito_garantias = DB::table('credito_garantia')
                    ->where('credito_garantia.idcredito',$value)
                    ->get();
                foreach($credito_garantias as $valuegarantia){
                    DB::table('credito_garantia')->whereId($valuegarantia->id)->update([
                        'fecharegistro_listaremate' => now(),
                        'idresponsable_listaremate' => $idresponsable,
                        'estado_listagarantia' => 1,
                    ]);
                }
						}

            return response()->json([
                'resultado'           => 'CORRECTO',
                'mensaje'             => 'Se ha asignado correctamente!!'
            ]);
          
            /*return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha autorizado correctamente.',
                'idresponsable'   => $idresponsable
            ]);*/
        }
        elseif($request->input('view') == 'quitar') {
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
            if($usuario!=''){
                $idresponsable = $usuario->id;
            }else{
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El usuario y/o la contraseña es incorrecta!!.'
                ]);
            }
          
            $rules['idagencia']                   =  'required';
            //$rules['idresponsable_modificado']    =  'required';
            $rules['check_destino']                =  'required';
            $messages['idagencia.required']       = 'El campo "Agencia" es obligatorio.';
            //$messages['idresponsable_modificado.required']       = 'No hay autorización!!.';
            $messages['check_destino.required']    = 'Debe seleccionar minimo un cliente.';

            $this->validate($request,$rules,$messages);

					  foreach(json_decode($request->check_destino) as $value){
              
                $credito_garantias = DB::table('credito_garantia')
                    ->where('credito_garantia.idcredito',$value)
                    ->get();
              
                foreach($credito_garantias as $valuegarantia){
                    DB::table('credito_garantia')->whereId($valuegarantia->id)->update([
                        'fecharegistro_listaremate' => now(),
                        'idresponsable_listaremate' => $idresponsable,
                        'estado_listagarantia' => 0,
                    ]);
                }
						}
          
            return response()->json([
                'resultado'           => 'CORRECTO',
                'mensaje'             => 'Se ha asignado correctamente!!'
            ]);
          
            /*return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha autorizado correctamente.',
                'idresponsable'   => $idresponsable
            ]);*/
        }
    }

    public function destroy(Request $request, $idtienda, $id)
    {
    }
}
