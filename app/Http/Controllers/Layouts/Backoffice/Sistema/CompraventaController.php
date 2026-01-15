<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Str;

class CompraventaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);

        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $agencias = DB::table('tienda')->get();
        $cvcompras = DB::table('cvcompra')
            ->where('idtienda',$idtienda)
            ->where('idestadoeliminado',1)
            ->where('idestadocvcompra',1)
            ->orderByDesc('fecharegistro')
            ->get();
        $cvventas = DB::table('cvventa')
            ->where('idtienda',$idtienda)
            ->where('idestadoeliminado',1)
            ->orderByDesc('fecharegistro')
            ->get();

        if(request('view') == 'tabla'){
            return view(sistema_view().'/compraventa/tabla', compact(
                'tienda',
                'agencias',
                'cvcompras',
                'cvventas',
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

            $codigo = DB::table('cvcompra')->max('codigo') + 1;

            // Banco
            $banco = DB::table('banco')->where('id',request('compra_idbanco'))->first();
            $compra_banco = $banco->nombre ?? '';
            $compra_cuenta = $banco->cuenta ?? '';

            DB::table('cvcompra')->insert([
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
                'idresponsable' => auth()->user()->id,
                'idresponsable_permiso' => 0,
                'eliminado_idresponsable' => 0,
                'eliminado_idresponsable_permiso' => 0,
                'idestadocvcompra' => 1,
                'idestadoeliminado' => 1,
                'idtienda' => request('idtienda'),
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]); 
        } elseif (request('view') == 'registrar_venta') {
            $rules = [
                'comprador_nombreapellidos' => 'required',
                'comprador_dni' => 'required',
                'venta_precio_venta_descuento' => 'required',
                'venta_montoventa' => 'required',
            ];
            $messages = [
                'comprador_nombreapellidos.required' => 'El "Apellidos y Nombres (Vendedor)" es Obligatorio.',
                'comprador_dni.required' => 'El "DNI (Vendedor)" es Obligatorio.',
                'venta_precio_venta_descuento.required' => 'El "Precio de Venta con Descuento" es Obligatorio.',
                'venta_montoventa.required' => 'El "Monto de Venta" es Obligatorio.',
            ];

            if (request('venta_idformapago') == 2) {
                $rules['venta_numerooperacion'] = 'required';
                $rules['venta_idbanco'] = 'required';
                $messages['venta_numerooperacion.required'] = 'El "N° de Operación" es Obligatorio.';
                $messages['venta_idbanco.required'] = 'El "Banco" es Obligatorio.';
            }

            $this->validate($request,$rules,$messages);

            $cvcompra = DB::table('cvcompra')->whereId(request('idcvcompra'))->first();

            // Banco
            $banco = DB::table('banco')->where('id',request('venta_idbanco'))->first();
            $venta_banco = $banco->nombre ?? '';
            $venta_cuenta = $banco->cuenta ?? '';

            DB::table('cvventa')->insert([
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

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]); 
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        if ($id=='show_table_compra') {
            $where = [];

            if(request('id_agencia_compra') != ''){
                $where[] = ['idtienda', '=', request('id_agencia_compra')];
            }else {
                $where[] = ['idtienda', '=', $idtienda];
            }
            if(request('fecha_inicio_compra') != ''){
                $where[] = ['fecharegistro','>=',request('fecha_inicio_compra').' 00:00:00'];
            }
            if(request('fecha_fin_compra') != ''){
                $where[] = ['fecharegistro','<=',request('fecha_fin_compra').' 23:59:59'];
            }

            if (request('check_compra') == '0') {
                $where[] = ['idestadocvcompra', '1'];
            }

            $cvcompras = DB::table('cvcompra')
                ->where($where)
                ->where('idestadoeliminado',1)
                ->orderByDesc('fecharegistro')
                ->get();

            $total = 0;
            $html = '';
            foreach($cvcompras as $value){
                $fecharegistro = date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A");
                $origen = $value->idorigen == '1' ? 'SERFIP' : 'OTROS';
                $lugar_pago = $value->compra_idformapago == '1' ? 'CAJA' : 'BANCO';
                $validacion = '';
                if ($value->compra_idformapago == '2') {
                    $validacion = '<button type="button" class="btn btn-primary">
                                    <i class="fa-solid fa-check"></i> Validar
                                </button>';
                }

                $estado = $value->idestadocvcompra == '1' ? 'P' : 'V';
                $prefijo = $value->idestadocvcompra == '1' ? 'CB' : 'VB';

                $descripcion = Str::limit($value->descripcion, 25);
                $vendedor = Str::limit($value->vendedor_nombreapellidos, 25);

                $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data_compra(this)'>
                            <td>{$estado}</td>
                            <td>{$prefijo}{$value->codigo}</td>
                            <td>{$descripcion}</td>
                            <td>{$value->serie_motor_partida}</td>
                            <td>{$value->modelo_tipo}</td>
                            <td>{$fecharegistro}</td>
                            <td style='text-align: right;'>{$value->valorcompra}</td>
                            <td style='text-align: right;'>{$value->valorcomercial}</td>
                            <td>{$value->chasis}</td>
                            <td>{$origen}</td>
                            <td>{$value->numeroficha}</td>
                            <td>{$vendedor}</td>
                            <td>{$value->vendedor_dni}</td>
                            <td>{$lugar_pago}</td>
                            <td>{$validacion}</td>
                            <td>{$value->compra_banco}</td>
                            <td>{$value->compra_numerooperacion}</td>
                        </tr>";
                $total = $total+$value->valorcompra;
            }

            if(count($cvcompras)==0){
                $html.= '<tr><td colspan="17" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
            }

            return array(
                'html' => $html,
                'total' => number_format($total, 2, '.', ''),
            );
        } elseif ($id=='show_table_venta') {
            $where = [];

            if(request('id_agencia_venta') != ''){
                $where[] = ['idtienda', '=', request('id_agencia_venta')];
            }
            if(request('fecha_inicio_venta') != ''){
                $where[] = ['fecharegistro','>=',request('fecha_inicio_venta').' 00:00:00'];
            }
            if(request('fecha_fin_venta') != ''){
                $where[] = ['fecharegistro','<=',request('fecha_fin_venta').' 23:59:59'];
            }

            $cvventas = DB::table('cvventa')
                ->where($where)
                ->where('idestadoeliminado',1)
                ->orderByDesc('fecharegistro')
                ->get();

            $total = 0;
            $html = '';
            foreach($cvventas as $value){
                $fecharegistro = date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A");
                $comprador = Str::limit($value->comprador_nombreapellidos, 25);
                $lugar_pago = $value->venta_idformapago == '1' ? 'CAJA' : 'BANCO';
                $validacion = '';
                if ($value->venta_idformapago == '2') {
                    $validacion = '<button type="button" class="btn btn-primary">
                                    <i class="fa-solid fa-check"></i> Validar
                                </button>';
                }

                $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data_venta(this)'>
                            <td>VB{$value->codigo}</td>
                            <td>{$comprador}</td>
                            <td>{$value->comprador_dni}</td>
                            <td>{$fecharegistro}</td>
                            <td style='text-align: right;'>{$value->venta_precio_venta_descuento}</td>
                            <td style='text-align: right;'>{$value->venta_montoventa}</td>
                            <td>{$lugar_pago}</td>
                            <td>{$validacion}</td>
                            <td>{$value->venta_banco}</td>
                            <td>{$value->venta_numerooperacion}</td>
                        </tr>";
                $total = $total+$value->venta_precio_venta_descuento;
            }

            if(count($cvventas)==0){
                $html.= '<tr><td colspan="10" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
            }

            return array(
                'html' => $html,
                'total' => number_format($total, 2, '.', ''),
            );
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        if (request('view') == 'editar_compra') {
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $agencias = DB::table('tienda')->get();
            $tipo_garantia = DB::table('tipo_garantia')->get();
            $estado_garantia = DB::table('estado_garantia')->get();
            $bancos = DB::table('banco')->get();
            $cvcompra = DB::table('cvcompra')->whereId($id)->first();

            return view(sistema_view().'/compraventa/edit_compra', compact(
                'tienda',
                'agencias',
                'tipo_garantia',
                'estado_garantia',
                'bancos',
                'cvcompra',
            ));
        } elseif (request('view') == 'eliminar_compra') {
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $cvcompra = DB::table('cvcompra')->whereId($id)->first();
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1,2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();

            return view(sistema_view().'/compraventa/delete_compra', compact(
                'tienda',
                'cvcompra',
                'usuarios'
            ));
        } elseif (request('view') == 'eliminar_venta') {
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $cvventa = DB::table('cvventa')->whereId($id)->first();
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1,2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();

            return view(sistema_view().'/compraventa/delete_venta', compact(
                'tienda',
                'cvventa',
                'usuarios'
            ));
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        if (request('view') == 'editar_compra') {
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
                'idtipogarantia' => request('idtipogarantia'),
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
                'idresponsable' => auth()->user()->id,
                'idresponsable_permiso' => 0,
                'eliminado_idresponsable' => 0,
                'eliminado_idresponsable_permiso' => 0,
                'idestadocvcompra' => 1,
                'idestadoeliminado' => 1,
                'idtienda' => request('idtienda'),
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]); 
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

            DB::table('cvcompra')->whereId($id)->update([
               'fechaeliminado' => now(),
               'eliminado_idresponsable' => $idresponsable,
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

            DB::table('cvventa')->whereId($id)->update([
               'fechaeliminado' => now(),
               'eliminado_idresponsable' => $idresponsable,
               'idestadoeliminado' => 2,
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.',
            ]);
        }
    }
}
