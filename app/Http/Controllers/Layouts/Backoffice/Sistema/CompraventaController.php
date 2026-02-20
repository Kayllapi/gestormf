<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Str;
use PDF;

class CompraventaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);

        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $agencias = DB::table('tienda')->get();
        $apertura_caja = cvapertura($idtienda);
        $arqueocaja = cvarqueocaja($idtienda);

        $validacionDiaria = validacionDiaria($idtienda);

        if(request('view') == 'tabla'){
            return view(sistema_view().'/compraventa/tabla', compact(
                'tienda',
                'agencias',
                'apertura_caja',
                'arqueocaja',
                'validacionDiaria',
            ));
        }
    }

    public function create(Request $request,$idtienda)
    {
        if (request('view') == 'create_compra') {
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $agencias = DB::table('tienda')->get();
            $tipo_garantia = DB::table('tipo_garantia')->get();
            $estado_garantia = DB::table('estado_garantia')->get();
            $bancos = DB::table('banco')->get();

            return view(sistema_view().'/compraventa/create_compra', compact(
                'tienda',
                'agencias',
                'tipo_garantia',
                'estado_garantia',
                'bancos',
            ));
        } elseif (request('view') == 'create_venta') {
            $cvcompra = DB::table('cvcompra')->where('id',request('idcvcompra'))->first();
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $bancos = DB::table('banco')->get();
            
            return view(sistema_view().'/compraventa/create_venta', compact(
                'cvcompra',
                'tienda',
                'bancos',
            ));
        }
    }

    public function store(Request $request, $idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if (request('view') == 'registrar_compra') {
            $rules = [
                'idtienda' => 'required',
                'idtipogarantia' => 'required',
                'descripcion' => 'required',
                'serie_motor_partida' => 'required',
                'modelo_tipo' => 'required',
                'idestado_garantia' => 'required',
                'color' => 'required',
                'valorcompra' => 'required',
                'valorcomercial' => 'required',
                'vendedor_nombreapellidos' => 'required',
                'vendedor_dni' => 'required',
                'idorigen' => 'required',
                'numeroficha' => 'required',
            ];
            $messages = [
                'idtienda.required' => 'El "Agencia" es Obligatorio.',
                'idtipogarantia.required' => 'El "Tipo de Bien" es Obligatorio.',
                'descripcion.required' => 'La "Descripción" es Obligatorio.',
                'serie_motor_partida.required' => 'El "Serie/Motor/N° Partida" es Obligatorio.',
                'modelo_tipo.required' => 'El "Modelo/Tipo" es Obligatorio.',
                'idestado_garantia.required' => 'El "Estado" es Obligatorio.',
                'color.required' => 'El "Color" es Obligatorio.',
                'valorcompra.required' => 'El "Valor Compra (soles)" es Obligatorio.',
                'valorcomercial.required' => 'El "Valor Comercial" es Obligatorio.',
                'vendedor_nombreapellidos.required' => 'El "Apellidos y Nombres (Vendedor)" es Obligatorio.',
                'vendedor_dni.required' => 'El "DNI (Vendedor)" es Obligatorio.',
                'idorigen.required' => 'El "Origen" es Obligatorio.',
                'numeroficha.required' => 'El "N° de Ficha o Comprobante" es Obligatorio.',
            ];

            if (request('compra_idformapago') == 2) {
                $rules['compra_numerooperacion'] = 'required';
                $rules['compra_idbanco'] = 'required';
                $messages['compra_numerooperacion.required'] = 'El "N° de Operación" es Obligatorio.';
                $messages['compra_idbanco.required'] = 'El "Banco" es Obligatorio.';
            }

            $this->validate($request,$rules,$messages);

            $cvconsolidadooperaciones = cvconsolidadooperaciones($tienda,$idtienda,now()->format('Y-m-d'));
            if($request->compra_idformapago==1){
                if((float)$cvconsolidadooperaciones['saldos_caja']<=(float)$request->valorcompra){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'No hay saldo suficiente en CAJA.<br><b>Saldo Actual: S/. '.$cvconsolidadooperaciones['saldos_caja'].'.</b>'
                    ]);
                }
            }
            elseif($request->compra_idformapago==2){
                foreach($cvconsolidadooperaciones['saldos_cuentabanco_bancos'] as $value){
                    if($value['banco_id']==$request->compra_idbanco && (float)$value['banco']<=(float)$request->valorcompra){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay saldo suficiente en Cuenta Bancaria.'
                        ]);
                    }
                }  
            }

            $codigo = DB::table('cvcompra')->max('codigo') + 1;

            // Banco
            $banco = DB::table('banco')->where('id',request('compra_idbanco'))->first();
            $compra_banco = $banco->nombre ?? '';
            $compra_cuenta = $banco->cuenta ?? '';

            // Responsable
            $responsable = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->where('users.id',auth()->user()->id)
                ->select('users.*','permiso.nombre as nombrepermiso', 'permiso.id as idpermiso')
                ->first();
            $compra_responsable = $responsable->nombrecompleto ?? '';


            $idcvcompra = DB::table('cvcompra')->insertGetId([
                'idtipogarantia' => request('idtipogarantia'),
                'fecharegistro' => now(),
                'codigo' => $codigo,
                'descripcion' => request('descripcion'),
                'serie_motor_partida' => request('serie_motor_partida'),
                'chasis' => request('chasis'),
                'modelo_tipo' => request('modelo_tipo'),
                'otros' => request('otros'),
                'idestado_garantia' => request('idestado_garantia'),
                'color' => request('color'),
                'fabricacion' => request('fabricacion'),
                'compra' => request('compra'),
                'placa' => request('placa'),
                'valorcompra' => request('valorcompra'),
                'valorcomercial' => request('valorcomercial'),
                'accesorio_doc' => '',
                'detalle_garantia' => '',
                'vendedor_nombreapellidos' => request('vendedor_nombreapellidos'),
                'vendedor_dni' => request('vendedor_dni'),
                'idorigen' => request('idorigen'),
                'numeroficha' => request('numeroficha'),
                'compra_numerooperacion' => request('compra_numerooperacion') ?? '',
                'compra_banco' => $compra_banco,
                'compra_cuenta' => $compra_cuenta,
                'compra_idbanco' => request('compra_idbanco') ?? 0,
                'compra_idformapago' => request('compra_idformapago'),
                'validar_estado' => 0,
                'validar_responsable' => 0,
                'validar_responsable_permiso' => 0,
                'idcvarqueocaja_cierre' => 0,
                'idresponsable' => $responsable->id,
                'idresponsable_permiso' => $responsable->idpermiso,
                'eliminado_idresponsable' => 0,
                'eliminado_idresponsable_permiso' => 0,
                'idestadocvcompra' => 1,
                'idestadoeliminado' => 1,
                'idtienda' => request('idtienda'),
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.',
                'idcvcompra' => $idcvcompra,
            ]); 
        } elseif (request('view') == 'registrar_venta') {
            $rules = [
                'comprador_nombreapellidos' => 'required',
                'comprador_dni' => 'required',
                'venta_montoventa' => 'required',
            ];
            $messages = [
                'comprador_nombreapellidos.required' => 'El "Apellidos y Nombres (Vendedor)" es Obligatorio.',
                'comprador_dni.required' => 'El "DNI (Vendedor)" es Obligatorio.',
                'venta_montoventa.required' => 'El "Monto de Venta" es Obligatorio.',
            ];

            if (request('venta_idformapago') == 2) {
                $rules['venta_numerooperacion'] = 'required';
                $rules['venta_idbanco'] = 'required';
                $messages['venta_numerooperacion.required'] = 'El "N° de Operación" es Obligatorio.';
                $messages['venta_idbanco.required'] = 'El "Banco" es Obligatorio.';
            }

            $this->validate($request,$rules,$messages);

            if (request("venta_montoventa") < request('venta_precio_venta_descuento')) {
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "Monto de Venta" debe ser >= al "Precio de Venta con Descuento".'
                ]);
            }

            $cvcompra = DB::table('cvcompra')->whereId(request('idcvcompra'))->first();

            // Banco
            $banco = DB::table('banco')->where('id',request('venta_idbanco'))->first();
            $venta_banco = $banco->nombre ?? '';
            $venta_cuenta = $banco->cuenta ?? '';

            $idcvventa = DB::table('cvventa')->insertGetId([
                'fecharegistro' => now(),
                'codigo' => $cvcompra->codigo,
                'venta_precio_venta_descuento' => request('venta_precio_venta_descuento'),
                'venta_montoventa' => request('venta_montoventa'),
                'comprador_nombreapellidos' => request('comprador_nombreapellidos'),
                'comprador_dni' => request('comprador_dni'),
                'venta_numerooperacion' => request('venta_numerooperacion') ?? '',
                'venta_banco' => $venta_banco,
                'venta_cuenta' => $venta_cuenta,
                'venta_idbanco' => request('venta_idbanco') ?? 0,
                'venta_idformapago' => request('venta_idformapago'),
                'venta_idresponsable' => auth()->user()->id,
                'venta_idresponsable_permiso' => 0,
                'validar_estado' => 0,
                'validar_responsable' => 0,
                'validar_responsable_permiso' => 0,
                'idcvarqueocaja_cierre' => 0,
                'idcvcompra' => $cvcompra->id,
                'eliminado_idresponsable' => 0,
                'eliminado_idresponsable_permiso' => 0,
                'idestadoeliminado' => 1,
                'idtienda' => $idtienda,
            ]);

            DB::table('cvcompra')->where('id',$cvcompra->id)->update([
                'idestadocvcompra' => 2,
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.',
                'idcvventa' => $idcvventa,
            ]); 
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        if ($id=='show_table_compra') {
            $where = [];

            if(request('id_agencia_compra') != ''){
                $where[] = ['cvcompra.idtienda', '=', request('id_agencia_compra')];
            }else {
                $where[] = ['cvcompra.idtienda', '=', $idtienda];
            }
            if(request('fecha_inicio_compra') != ''){
                $where[] = ['cvcompra.fecharegistro','>=',request('fecha_inicio_compra').' 00:00:00'];
            }
            if(request('fecha_fin_compra') != ''){
                $where[] = ['cvcompra.fecharegistro','<=',request('fecha_fin_compra').' 23:59:59'];
            }
            if (request('check_compra') == '0') {
                $where[] = ['cvcompra.idestadocvcompra', '1'];
            }

            $cvcompras = DB::table('cvcompra')
                ->join('estado_garantia','estado_garantia.id','cvcompra.idestado_garantia')
                ->join('users','users.id','cvcompra.idresponsable')
                ->leftJoin('users as u2', 'u2.id', 'cvcompra.validar_responsable')
                ->where($where)
                ->where('cvcompra.idestadoeliminado',1)
                ->select(
                    'cvcompra.*',
                    'estado_garantia.nombre as estado',
                    'users.codigo as responsablecodigo',
                    'u2.codigo as validar_responsablecodigo'
                )
                ->orderByDesc('cvcompra.fecharegistro')
                ->get();

            $total = 0;
            $html = '';
            foreach($cvcompras as $value){
                $fecharegistro = date_format(date_create($value->fecharegistro),"d-m-Y h:i:s A");
                $tienda = DB::table('tienda')->where('id',$value->idorigen)->first();
                $origen = $value->idorigen == '0' ? 'OTROS' : ($tienda->nombre??'');
                $lugar_pago = $value->compra_idformapago == '1' ? 'CAJA' : 'BANCO';
                $validacion = '';
                if ($value->compra_idformapago == '2') {
                    if ($value->validar_estado == 1) {
                        $validacion = '<i class="fa-solid fa-check"></i> ('.$value->validar_responsablecodigo.')';
                    } else {
                        $validacion = '<button type="button" class="btn btn-success" onclick="validar_compra('.$value->id.')">
                                        <i class="fa-solid fa-check"></i> Validar
                                    </button>';
                    }
                }

                $estado = $value->idestadocvcompra == '1' ? 'P' : 'V';
                $prefijo = $value->idestadocvcompra == '1' ? 'CB' : 'VB';

                $descripcion = Str::limit($value->descripcion, 25);
                $vendedor = Str::limit($value->vendedor_nombreapellidos, 25);

                $html .= "<tr data-valor-columna='{$value->id}'
                            data-valor-compra_idformapago='{$value->compra_idformapago}'
                            data-valor-validar_estado='{$value->validar_estado}'
                            onclick='show_data_compra(this)'>
                            <td>{$estado}</td>
                            <td>{$prefijo}{$value->codigo}</td>
                            <td>{$fecharegistro}</td>
                            <td>{$descripcion}</td>
                            <td>{$value->serie_motor_partida}</td>
                            <td>{$value->chasis}</td>
                            <td>{$value->modelo_tipo}</td>
                            <td>{$value->otros}</td>
                            <td style='text-align: right;'>{$value->valorcomercial}</td>
                            <td>{$value->estado}</td>
                            <td>{$value->color}</td>
                            <td>{$value->fabricacion}</td>
                            <td>{$value->compra}</td>
                            <td>{$value->placa}</td>
                            <td>{$origen}</td>
                            <td>{$value->numeroficha}</td>
                            <td>{$vendedor}</td>
                            <td>{$value->vendedor_dni}</td>
                            <td>{$lugar_pago}</td>
                            <td>{$validacion}</td>
                            <td>{$value->compra_banco}</td>
                            <td>{$value->compra_numerooperacion}</td>
                            <td>{$value->responsablecodigo}</td>
                        </tr>";
                $total = $total+$value->valorcompra;
            }

            if(count($cvcompras)==0){
                $html.= '<tr><td colspan="23" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
            }

            return array(
                'html' => $html,
                'total' => number_format($total, 2, '.', ''),
            );
        } elseif ($id=='show_table_venta') {
            $where = [];

            if(request('id_agencia_venta') != ''){
                $where[] = ['cvventa.idtienda', '=', request('id_agencia_venta')];
            }
            if(request('fecha_inicio_venta') != ''){
                $where[] = ['cvventa.fecharegistro','>=',request('fecha_inicio_venta').' 00:00:00'];
            } else {
                $where[] = ['cvventa.fecharegistro','>=',date('Y-m-d').' 00:00:00'];
            }
            if(request('fecha_fin_venta') != ''){
                $where[] = ['cvventa.fecharegistro','<=',request('fecha_fin_venta').' 23:59:59'];
            } else {
                $where[] = ['cvventa.fecharegistro','<=',date('Y-m-d').' 23:59:59'];
            }

            $cvventas = DB::table('cvventa')
                ->join('cvcompra', 'cvventa.idcvcompra', 'cvcompra.id')
                ->join('estado_garantia','estado_garantia.id','cvcompra.idestado_garantia')
                ->join('users','users.id','cvventa.venta_idresponsable')
                ->leftJoin('users as u2', 'u2.id', 'cvventa.validar_responsable')
                ->where($where)
                ->where('cvventa.idestadoeliminado',1)
                ->select(
                    'cvcompra.*',
                    'estado_garantia.nombre as estado',
                    'cvventa.id as idventa',
                    'cvventa.codigo as codigoventa',
                    'cvventa.comprador_nombreapellidos as comprador_nombreapellidos',
                    'cvventa.comprador_dni as comprador_dni',
                    'cvventa.venta_idformapago as venta_idformapago',
                    'cvventa.venta_banco as venta_banco',
                    'cvventa.venta_numerooperacion as venta_numerooperacion',
                    'cvventa.fecharegistro as venta_fecharegistro',
                    'cvventa.validar_estado as venta_validar_estado',
                    'cvventa.venta_precio_venta_descuento as venta_precio_venta_descuento',
                    'cvventa.venta_montoventa as venta_montoventa',
                    'users.codigo as responsablecodigo',
                    'u2.codigo as validar_responsablecodigo'
                )
                ->orderByDesc('cvventa.fecharegistro')
                ->get();

            $total = 0;
            $html = '';
            foreach($cvventas as $value){
                $fecharegistro = date_format(date_create($value->venta_fecharegistro),"d-m-Y h:i:s A");
                $tienda = DB::table('tienda')->where('id',$value->idorigen)->first();
                $origen = $value->idorigen == '0' ? 'OTROS' : ($tienda->nombre??'');
                $lugar_pago = $value->venta_idformapago == '1' ? 'CAJA' : 'BANCO';
                $validacion = '';
                if ($value->venta_idformapago == '2') {
                    if ($value->venta_validar_estado == 1) {
                        $validacion = '<i class="fa-solid fa-check"></i> ('.$value->validar_responsablecodigo.')';
                    } else {
                        $validacion = '<button type="button" class="btn btn-success" onclick="validar_venta('.$value->idventa.')">
                                        <i class="fa-solid fa-check"></i> Validar
                                    </button>';
                    }
                }

                $descripcion = Str::limit($value->descripcion, 25);
                $comprador = Str::limit($value->comprador_nombreapellidos, 25);

                $html .= "<tr data-valor-columna='{$value->idventa}' onclick='show_data_venta(this)'>
                            <td>VB{$value->codigoventa}</td>
                            <td>{$fecharegistro}</td>
                            <td>{$descripcion}</td>
                            <td>{$value->serie_motor_partida}</td>
                            <td>{$value->chasis}</td>
                            <td>{$value->modelo_tipo}</td>
                            <td>{$value->otros}</td>
                            <td style='text-align: right;'>{$value->valorcomercial}</td>
                            <td style='text-align: right;'>{$value->venta_precio_venta_descuento}</td>
                            <td style='text-align: right;'>{$value->venta_montoventa}</td>
                            <td>{$value->estado}</td>
                            <td>{$value->color}</td>
                            <td>{$value->fabricacion}</td>
                            <td>{$value->compra}</td>
                            <td>{$value->placa}</td>
                            <td>{$origen}</td>
                            <td>{$value->numeroficha}</td>
                            <td>{$comprador}</td>
                            <td>{$value->comprador_dni}</td>
                            <td>{$lugar_pago}</td>
                            <td>{$validacion}</td>
                            <td>{$value->venta_banco}</td>
                            <td>{$value->venta_numerooperacion}</td>
                            <td>{$value->responsablecodigo}</td>
                        </tr>";
                $total = $total+$value->venta_montoventa;
            }

            if(count($cvventas)==0){
                $html.= '<tr><td colspan="26" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
            }

            return array(
                'html' => $html,
                'total' => number_format($total, 2, '.', ''),
            );
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        if (request('view') == 'edit_compra') {
            $cvcompra = DB::table('cvcompra')->whereId($id)->first();
            $agencias = DB::table('tienda')->get();
            $tipo_garantia = DB::table('tipo_garantia')->get();
            $estado_garantia = DB::table('estado_garantia')->get();
            $bancos = DB::table('banco')->get();

            return view(sistema_view().'/compraventa/edit_compra', compact(
                'tienda',
                'agencias',
                'tipo_garantia',
                'estado_garantia',
                'bancos',
                'cvcompra',
            ));
        } elseif (request('view') == 'edit_validar_compra') {
            $cvcompra = DB::table('cvcompra')->whereId($id)->first();
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1,2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso','permiso.id as idpermiso')
                ->get();

            return view(sistema_view().'/compraventa/edit_validar_compra', compact(
                'tienda',
                'cvcompra',
                'usuarios',
            ));
        } elseif (request('view') == 'edit_validar_editcompra') {
            $cvcompra = DB::table('cvcompra')->whereId($id)->first();
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1,2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso','permiso.id as idpermiso')
                ->get();

            return view(sistema_view().'/compraventa/edit_validar_editcompra', compact(
                'tienda',
                'cvcompra',
                'usuarios',
            ));
        } elseif (request('view') == 'vaucher_compra') {
            $cvcompra = DB::table('cvcompra')->whereId($id)->first();

            return view(sistema_view().'/compraventa/edit_vaucher_compra', compact(
                'tienda',
                'cvcompra',
            ));
        } elseif (request('view') == 'edit_vaucher_comprapdf' ) {
            $cvcompra = DB::table('cvcompra')
                ->join('users','users.id','cvcompra.idresponsable')
                ->where('cvcompra.id',$id)
                ->select(
                    'cvcompra.*',
                    'users.codigo as responsablecodigo'
                )
                ->first();
            $pdf = PDF::loadView(sistema_view().'/compraventa/edit_vaucher_comprapdf', compact(
                'tienda',
                'cvcompra',
            ));
            $pdf->setPaper('A4');
            return $pdf->stream('VOUCHER_COMPRA.pdf');
        } elseif (request('view') == 'edit_vaucher_compra2pdf' ) {
            $cvcompra = DB::table('cvcompra')
                ->join('users','users.id','cvcompra.idresponsable')
                ->where('cvcompra.id',$id)
                ->select(
                    'cvcompra.*',
                    'users.codigo as responsablecodigo'
                )
                ->first();
            $pdf = PDF::loadView(sistema_view().'/compraventa/edit_vaucher_compra2pdf', compact(
                'tienda',
                'cvcompra',
            ));
            $pdf->setPaper('A4');
            return $pdf->stream('VOUCHER_COMPRA.pdf');
        } elseif (request('view') == 'edit_reporte_compra') {
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1,2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso', 'permiso.id as idpermiso')
                ->get();

            return view(sistema_view().'/compraventa/edit_reporte_compra', compact(
                'tienda',
                'usuarios'
            ));
        } elseif (request('view') == 'exportar_compra') {
            $id_agencia_compra = request('id_agencia_compra');
            $fecha_inicio_compra = request('fecha_inicio_compra');
            $fecha_fin_compra = request('fecha_fin_compra');
            $check_compra = request('check_compra');

            return view(sistema_view().'/compraventa/exportar_compra', compact(
                'tienda',
                'id_agencia_compra',
                'fecha_inicio_compra',
                'fecha_fin_compra',
                'check_compra',
            ));
        } elseif (request('view') == 'exportar_comprapdf') {
            $id_agencia_compra = request('id_agencia_compra');
            $fecha_inicio_compra = request('fecha_inicio_compra');
            $fecha_fin_compra = request('fecha_fin_compra');
            $check_compra = request('check_compra');

            $where = [];

            if($id_agencia_compra != ''){
                $where[] = ['cvcompra.idtienda', '=', $id_agencia_compra];
            }else {
                $where[] = ['cvcompra.idtienda', '=', $idtienda];
            }
            if($fecha_inicio_compra != ''){
                $where[] = ['cvcompra.fecharegistro','>=',$fecha_inicio_compra.' 00:00:00'];
            }
            if($fecha_fin_compra != ''){
                $where[] = ['cvcompra.fecharegistro','<=',$fecha_fin_compra.' 23:59:59'];
            }
            if ($check_compra == '0') {
                $where[] = ['cvcompra.idestadocvcompra', '1'];
            }

            $cvcompras = DB::table('cvcompra')
                ->join('estado_garantia','estado_garantia.id','cvcompra.idestado_garantia')
                ->join('users','users.id','cvcompra.idresponsable')
                ->leftJoin('users as u2', 'u2.id', 'cvcompra.validar_responsable')
                ->where($where)
                ->where('cvcompra.idestadoeliminado',1)
                ->select(
                    'cvcompra.*',
                    'estado_garantia.nombre as estado',
                    'users.codigo as responsablecodigo',
                    'u2.codigo as validar_responsablecodigo'
                )
                ->orderBy('cvcompra.fecharegistro')
                ->get();

            $pdf = PDF::loadView(sistema_view().'/compraventa/exportar_comprapdf', compact(
                'tienda',
                'cvcompras',
                'id_agencia_compra',
                'fecha_inicio_compra',
                'fecha_fin_compra',
                'check_compra',
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('REPORTE_COMPRA.pdf');
        } elseif (request('view') == 'eliminar_compra') {
            $cvcompra = DB::table('cvcompra')->whereId($id)->first();
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1,2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso', 'permiso.id as idpermiso')
                ->get();

            return view(sistema_view().'/compraventa/delete_compra', compact(
                'tienda',
                'cvcompra',
                'usuarios'
            ));
        } elseif (request('view') == 'edit_validar_venta') {
            $cvventa = DB::table('cvventa')->whereId($id)->first();
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1,2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso','permiso.id as idpermiso')
                ->get();

            return view(sistema_view().'/compraventa/edit_validar_venta', compact(
                'tienda',
                'cvventa',
                'usuarios',
            ));
        } elseif (request('view') == 'vaucher_venta') {
            $cvventa = DB::table('cvventa')->whereId($id)->first();

            return view(sistema_view().'/compraventa/edit_vaucher_venta', compact(
                'tienda',
                'cvventa',
            ));
        } elseif (request('view') == 'edit_vaucher_ventapdf' ) {
            $cvventa = DB::table('cvventa')
                ->join('cvcompra','cvcompra.id','cvventa.idcvcompra')
                ->join('users','users.id','cvventa.venta_idresponsable')
                ->select(
                    'cvventa.*',
                    'cvcompra.descripcion as descripcioncvcompra',
                    'cvcompra.serie_motor_partida as serie_motor_partidacvcompra',
                    'users.codigo as responsablecodigo',
                    'users.nombrecompleto as responsable_nombrecompleto',
                )
                ->where('cvventa.id',$id)
                ->first();
            $pdf = PDF::loadView(sistema_view().'/compraventa/edit_vaucher_ventapdf', compact(
                'tienda',
                'cvventa',
            ));
            $pdf->setPaper('A4');
            return $pdf->stream('VOUCHER_VENTA.pdf');
        } elseif (request('view') == 'edit_reporte_venta') {
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1,2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso', 'permiso.id as idpermiso')
                ->get();

            return view(sistema_view().'/compraventa/edit_reporte_venta', compact(
                'tienda',
                'usuarios'
            ));
        } elseif (request('view') == 'exportar_venta') {
            $id_agencia_venta = request('id_agencia_venta');
            $fecha_inicio_venta = request('fecha_inicio_venta');
            $fecha_fin_venta = request('fecha_fin_venta');

            return view(sistema_view().'/compraventa/exportar_venta', compact(
                'tienda',
                'id_agencia_venta',
                'fecha_inicio_venta',
                'fecha_fin_venta',
            ));
        } elseif (request('view') == 'exportar_ventapdf') {
            $id_agencia_venta = request('id_agencia_venta');
            $fecha_inicio_venta = request('fecha_inicio_venta');
            $fecha_fin_venta = request('fecha_fin_venta');

            $where = [];

            if($id_agencia_venta != ''){
                $where[] = ['cvventa.idtienda', '=', $id_agencia_venta];
            }
            if($fecha_inicio_venta != ''){
                $where[] = ['cvventa.fecharegistro','>=',$fecha_inicio_venta.' 00:00:00'];
            } else {
                $where[] = ['cvventa.fecharegistro','>=',date('Y-m-d').' 00:00:00'];
            }
            if($fecha_fin_venta != ''){
                $where[] = ['cvventa.fecharegistro','<=',$fecha_fin_venta.' 23:59:59'];
            } else {
                $where[] = ['cvventa.fecharegistro','<=',date('Y-m-d').' 23:59:59'];
            }

            $cvventas = DB::table('cvventa')
                ->join('cvcompra', 'cvventa.idcvcompra', 'cvcompra.id')
                ->join('estado_garantia','estado_garantia.id','cvcompra.idestado_garantia')
                ->join('users','users.id','cvventa.venta_idresponsable')
                ->leftJoin('users as u2', 'u2.id', 'cvventa.validar_responsable')
                ->where($where)
                ->where('cvventa.idestadoeliminado',1)
                ->select(
                    'cvcompra.*',
                    'estado_garantia.nombre as estado',
                    'cvventa.id as idventa',
                    'cvventa.codigo as codigoventa',
                    'cvventa.comprador_nombreapellidos as comprador_nombreapellidos',
                    'cvventa.comprador_dni as comprador_dni',
                    'cvventa.venta_idformapago as venta_idformapago',
                    'cvventa.venta_banco as venta_banco',
                    'cvventa.venta_numerooperacion as venta_numerooperacion',
                    'cvventa.fecharegistro as venta_fecharegistro',
                    'cvventa.validar_estado as venta_validar_estado',
                    'cvventa.venta_precio_venta_descuento as venta_precio_venta_descuento',
                    'cvventa.venta_montoventa as venta_montoventa',
                    'users.codigo as responsablecodigo',
                    'u2.codigo as validar_responsablecodigo'
                )
                ->orderBy('cvventa.fecharegistro')
                ->get();

            $pdf = PDF::loadView(sistema_view().'/compraventa/exportar_ventapdf', compact(
                'tienda',
                'cvventas',
                'id_agencia_venta',
                'fecha_inicio_venta',
                'fecha_fin_venta',
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('REPORTE_VENTA.pdf');
        } elseif (request('view') == 'eliminar_venta') {
            $cvventa = DB::table('cvventa')->whereId($id)->first();
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1,2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso', 'permiso.id as idpermiso')
                ->get();

            return view(sistema_view().'/compraventa/delete_venta', compact(
                'tienda',
                'cvventa',
                'usuarios'
            ));
        }
        else if($request->input('view') == 'validar_limites') {
            $co_actual = cvconsolidadooperaciones($tienda,$request->idagencia,$request->corte);
            if ($co_actual['saldos_caja'] > $tienda->credito_limitemaximo_caja) {
                $calculo = $co_actual['saldos_caja'] - $tienda->credito_limitemaximo_caja;
                $mensaje = 'El saldo de Caja excede el límite máximo permitido. Depositar a Reserva CF: '.$calculo;
                return $mensaje;
            }
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        if (request('view') == 'update_validar_compra') {
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
                ->where('users.id', request('idresponsable'))
                ->where('users.clave', request('responsableclave'))
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

            DB::table('cvcompra')->whereId($id)->update([
               'validar_estado' => 1,
               'validar_responsable' => $idresponsable,
               'validar_responsable_permiso' => request('idpermiso'),
               'validar_fecha' => now(),
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha validado correctamente.',
            ]);
        } elseif (request('view') == 'update_validar_editcompra') {
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
                ->where('users.id', request('idresponsable'))
                ->where('users.clave', request('responsableclave'))
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
        } elseif (request('view') == 'update_compra') {
            $rules = [
                'idtienda' => 'required',
                'idtipogarantia' => 'required',
                'descripcion' => 'required',
                'serie_motor_partida' => 'required',
                'modelo_tipo' => 'required',
                'idestado_garantia' => 'required',
                'color' => 'required',
                'valorcompra' => 'required',
                'valorcomercial' => 'required',
                'vendedor_nombreapellidos' => 'required',
                'vendedor_dni' => 'required',
                'idorigen' => 'required',
                'numeroficha' => 'required',
            ];
            $messages = [
                'idtienda.required' => 'El "Agencia" es Obligatorio.',
                'idtipogarantia.required' => 'El "Tipo de Bien" es Obligatorio.',
                'descripcion.required' => 'La "Descripción" es Obligatorio.',
                'serie_motor_partida.required' => 'El "Serie/Motor/N° Partida" es Obligatorio.',
                'modelo_tipo.required' => 'El "Modelo/Tipo" es Obligatorio.',
                'idestado_garantia.required' => 'El "Estado" es Obligatorio.',
                'color.required' => 'El "Color" es Obligatorio.',
                'valorcompra.required' => 'El "Valor Compra (soles)" es Obligatorio.',
                'valorcomercial.required' => 'El "Valor Comercial" es Obligatorio.',
                'vendedor_nombreapellidos.required' => 'El "Apellidos y Nombres (Vendedor)" es Obligatorio.',
                'vendedor_dni.required' => 'El "DNI (Vendedor)" es Obligatorio.',
                'idorigen.required' => 'El "Origen" es Obligatorio.',
                'numeroficha.required' => 'El "N° de Ficha o Comprobante" es Obligatorio.',
            ];

            if (request('compra_idformapago') == 2) {
                $rules['compra_numerooperacion'] = 'required';
                $rules['compra_idbanco'] = 'required';
                $messages['compra_numerooperacion.required'] = 'El "N° de Operación" es Obligatorio.';
                $messages['compra_idbanco.required'] = 'El "Banco" es Obligatorio.';
            }

            $this->validate($request,$rules,$messages);

            // Banco
            $banco = DB::table('banco')->where('id',request('compra_idbanco'))->first();
            $compra_banco = $banco->nombre ?? '';
            $compra_cuenta = $banco->cuenta ?? '';

            DB::table('cvcompra')->whereId($id)->update([
                'valorcomercial' => request('valorcomercial'),
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]); 
        } elseif (request('view') == 'update_validar_venta') {
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
                ->where('users.id', request('idresponsable'))
                ->where('users.clave', request('responsableclave'))
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

            DB::table('cvventa')->whereId($id)->update([
               'validar_estado' => 1,
               'validar_responsable' => $idresponsable,
               'validar_responsable_permiso' => request('idpermiso'),
               'validar_fecha' => now(),
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha validado correctamente.',
            ]);
        } elseif (request('view') == 'update_reporte_compra') {
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
                ->where('users.id', request('idresponsable'))
                ->where('users.clave', request('responsableclave'))
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
        } elseif (request('view') == 'update_reporte_venta') {
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
                ->where('users.id', request('idresponsable'))
                ->where('users.clave', request('responsableclave'))
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
        }
    } 

    public function destroy(Request $request, $idtienda, $id)
    {
        if( request('view') == 'eliminar_compra' ) {
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
                ->where('users.id', request('idresponsable'))
                ->where('users.clave', request('responsableclave'))
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

            $dt =  DB::table('cvcompra')->whereId($id)->first();
            if ($dt->idcvarqueocaja_cierre != 0) {
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No se puede eliminar la compra porque ya ha sido arqueada.'
                ]);
            }

            DB::table('cvcompra')->whereId($id)->update([
               'fechaeliminado' => now(),
               'eliminado_idresponsable' => $idresponsable,
               'eliminado_idresponsable_permiso' => request('idpermiso'),
               'idestadoeliminado' => 2,
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.',
            ]);
        } elseif (request('view') == 'eliminar_venta') {
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
                ->where('users.id', request('idresponsable'))
                ->where('users.clave', request('responsableclave'))
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

            $dt =  DB::table('cvventa')->whereId($id)->first();
            if ($dt->idcvarqueocaja_cierre != 0) {
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No se puede eliminar la venta porque ya ha sido arqueada.'
                ]);
            }

            DB::table('cvventa')->whereId($id)->update([
               'fechaeliminado' => now(),
               'eliminado_idresponsable' => $idresponsable,
               'eliminado_idresponsable_permiso' => request('idpermiso'),
               'idestadoeliminado' => 2,
            ]);

            $cvventa = DB::table('cvventa')->whereId($id)->first();
            DB::table('cvcompra')->where('id',$cvventa->idcvcompra)->update([
                'idestadocvcompra' => 1,
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.',
            ]);
        }
    }
}
