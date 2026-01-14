<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class CompraventaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);

        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $agencias = DB::table('tienda')->get();

        if(request('view') == 'tabla'){
            return view(sistema_view().'/compraventa/tabla', compact(
                'tienda',
                'agencias',
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
        }
    }

    public function store(Request $request, $idtienda)
    {
        if (request('view') == 'registrar') {
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
                'compra_idbanco' => request('compra_idbanco'),
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
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        if ($id=='show_table_compra') {
            $where = [];

            // request('check_compra')
            if(request('id_agencia_compra') != ''){
                $where[] = ['idtienda', '=', request('id_agencia_compra')];
            }
            if(request('fecha_inicio_compra') != ''){
                $where[] = ['fecharegistro','>=',request('fecha_inicio_compra').' 00:00:00'];
            }
            if(request('fecha_fin_compra') != ''){
                $where[] = ['fecharegistro','<=',request('fecha_fin_compra').' 23:59:59'];
            }

            $cvcompras = DB::table('cvcompra')
                ->where($where)
                ->orderBy('fecharegistro')
                ->get();
  
            $total = 0;
            $html = '';
            foreach($cvcompras as $value){
                $fecharegistro = date_format(date_create($value->fecharegistro),"d-m-Y H:i:s A");
                $origen = $value->idorigen == '1' ? 'SERFIP' : 'OTROS';

                $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data_compra(this)'>
                            <td></td>
                            <td>CB{$value->codigo}</td>
                            <td>{$value->descripcion}</td>
                            <td>{$value->serie_motor_partida}</td>
                            <td>{$value->modelo_tipo}</td>
                            <td>{$fecharegistro}</td>
                            <td>{$value->valorcompra}</td>
                            <td>{$value->valorcomercial}</td>
                            <td>{$value->chasis}</td>
                            <td>{$origen}</td>
                            <td>{$value->numeroficha}</td>
                        </tr>";
                $total = $total+$value->valorcompra;
            }

            if(count($cvcompras)==0){
                $html.= '<tr><td colspan="11" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
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
                'compra_idbanco' => request('compra_idbanco'),
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
    }
}
