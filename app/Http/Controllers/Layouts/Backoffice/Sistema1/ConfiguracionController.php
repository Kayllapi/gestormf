<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class ConfiguracionController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
      
        return view('layouts/backoffice/tienda/sistema/configuracion/index', [
            'tienda' => $tienda
        ]);
    }

    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if ($request->view == 'prestamo_registrardiaferiado') {
          return view('layouts/backoffice/tienda/sistema/configuracion/config_prestamo_diaferiadoregistrar', [
            'tienda' => $tienda
          ]);
        }
        elseif ($request->view == 'prestamo_registrardocumento') {
          return view('layouts/backoffice/tienda/sistema/configuracion/config_prestamo_documentoregistrar', [
            'tienda' => $tienda
          ]);
        }
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if ($request->view == 'registrar-diaferiado') {
            $rules = [
                'diaferiado_registrar_dia' => 'required',
                'diaferiado_registrar_mes' => 'required',
                'diaferiado_registrar_motivo' => 'required'
            ];
            $messages = [
                'diaferiado_registrar_dia.required' => 'El "Dia" es Obligatorio.',
                'diaferiado_registrar_mes.required' => 'El "Mes" es Obligatorio.',
                'diaferiado_registrar_motivo.required' => 'El "Motivo" es Obligatorio.'
            ];
            $this->validate($request, $rules, $messages);

            DB::table('s_prestamo_diaferiado')->insert([
                'dia' => $request->diaferiado_registrar_dia,
                'mes' => $request->diaferiado_registrar_mes,
                'motivo' => $request->diaferiado_registrar_motivo,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif ($request->view == 'registrar-documento') {
            $rules = [
                'documento_registrar_nombre'    => 'required',
                'documento_registrar_contenido' => 'required'
            ];
            $messages = [
                'documento_registrar_nombre.required'    => 'El "Nombre" es Obligatorio.',
                'documento_registrar_contenido.required' => 'El "Documento" es Obligatorio.'
            ];
            $this->validate($request, $rules, $messages);

            DB::table('s_prestamo_documento')->insert([
                'nombre'    => $request->documento_registrar_nombre,
                'contenido' => $request->documento_registrar_contenido,
                'idmostrar' => $request->documento_registrar_mostrar,
                'idtienda'  => $idtienda,
                'idestado'  => 1
            ]);
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif ($request->view == 'registrar-banco') {
            $rules = [
                'banco_nombre'    => 'required',
            ];
            $messages = [
                'banco_nombre.required'    => 'El "Nombre" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);

            DB::table('s_banco')->insert([
                'fecharegistro' => Carbon::now(),
                'nombre'    => $request->banco_nombre,
                'idtienda'  => $idtienda,
                'idestado'  => 1
            ]);
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif ($request->view == 'registrar-cuentabancaria') {
            $rules = [
                'cuentabancaria_idbanco'    => 'required',
                'cuentabancaria_numerocuenta'    => 'required',
            ];
            $messages = [
                'cuentabancaria_idbanco.required'    => 'El "Banco" es Obligatorio.',
                'cuentabancaria_numerocuenta.required'    => 'El "Número de Cuenta" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);

            DB::table('s_cuentabancaria')->insert([
                'fecharegistro' => Carbon::now(),
                'numerocuenta'    => $request->cuentabancaria_numerocuenta,
                's_idbanco'    => $request->cuentabancaria_idbanco,
                'idtienda'  => $idtienda,
                'idestado'  => 1
            ]);
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif ($request->view == 'registrar-piso') {
            $rules = [
                'nombre' => 'required',
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);

            /* idestado
            * 1 = activado
            * 1 = activado
            */
            DB::table('s_comida_piso')->insert([
                'fecharegistro'  => Carbon::now(),
                'nombre' => $request->nombre,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif ($request->view == 'registrar-ambiente') {
            $rules = [
                'nombre' => 'required',
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);

            /* idestado
            * 1 = activado
            * 1 = activado
            */
            DB::table('s_comida_ambiente')->insert([
                'fecharegistro'  => Carbon::now(),
                'nombre' => $request->nombre,
                'idpiso' => $request->idpiso,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif ($request->view == 'registrar-mesa') {
            $rules = [
                'mesa_registrar_numero_mesa' => 'required',
            ];
            $messages = [
                'mesa_registrar_numero_mesa.required' => 'El "Número de Mesa" es Obligatorio.',
            ];
            $this->validate($request, $rules, $messages);
          
            $mesas = DB::table('s_comida_mesa')
              ->where([
                ['numero_mesa', $request->mesa_registrar_numero_mesa],
                ['idambiente', $request->idambiente],
                ['idpiso', $request->idpiso],
                ['idtienda', $idtienda] 
              ])
              ->first();
          
            if (!is_null($mesas)) {
              return response()->json([
                'resultado' => 'ERROR',
                'mensaje'   => "El Número de Mesa ya Existe, ingrese otro por número por favor"
              ]);
            }
          

            DB::table('s_comida_mesa')->insert([
                'fecharegistro'  => Carbon::now(),
                'numero_mesa' => $request->mesa_registrar_numero_mesa,
                'idambiente' => $request->idambiente,
                'idpiso' => $request->idpiso,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if ($id == 'show-indexpermiso') {
          $prestamopermisos = DB::table('s_prestamo_permiso')
            ->where('s_prestamo_permiso.idtienda', $request->idtienda)
            ->orderBy('s_prestamo_permiso.mes','asc')
            ->orderBy('s_prestamo_permiso.dia','asc')
            ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));

          $tabla = [];
          foreach ($prestamopermisos as $value) {
            $mes_final = mesesEs($value->mes) ?? '';
            $permiso = str_pad($value->dia, 2, "0", STR_PAD_LEFT).', '.$mes_final;
            $opcion = '<li><a href="javascript:;" onclick="editar_permiso('.$idtienda.','.$value->id.')"><i class="fa fa-edit"></i> Editar</a></li>
                       <li><a href="javascript:;" onclick="eliminar_permiso('.$idtienda.','.$value->id.')"><i class="fa fa-ban"></i> Eliminar</a></li>';

            $tabla[] = [
              'permiso' => $permiso,
              'motivo' => $value->motivo,
              'opcion' => $opcion
            ];
          }

          return json_encode([
              'draw' => $request->input('draw'),
              'recordsTotal' => $prestamopermisos->total(),
              'recordsFiltered' => $prestamopermisos->total(),
              'data' => $tabla
          ]);
        }
        elseif ($id == 'show-indexdiaferiado') {
          $prestamodiaferiados = DB::table('s_prestamo_diaferiado')
            ->where('s_prestamo_diaferiado.idtienda', $request->idtienda)
            ->orderBy('s_prestamo_diaferiado.mes','asc')
            ->orderBy('s_prestamo_diaferiado.dia','asc')
            ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));

          $tabla = [];
          foreach ($prestamodiaferiados as $value) {
            $mes_final = mesesEs($value->mes) ?? '';
            $diaferiado = str_pad($value->dia, 2, "0", STR_PAD_LEFT).', '.$mes_final;
            $opcion = '<li><a href="javascript:;" onclick="editar_diaferiado('.$idtienda.','.$value->id.')"><i class="fa fa-edit"></i> Editar</a></li>
                       <li><a href="javascript:;" onclick="eliminar_diaferiado('.$idtienda.','.$value->id.')"><i class="fa fa-ban"></i> Eliminar</a></li>';

            $tabla[] = [
              'diaferiado' => $diaferiado,
              'motivo' => $value->motivo,
              'opcion' => $opcion
            ];
          }

          return json_encode([
              'draw' => $request->input('draw'),
              'recordsTotal' => $prestamodiaferiados->total(),
              'recordsFiltered' => $prestamodiaferiados->total(),
              'data' => $tabla
          ]);
        }
        elseif ($id == 'show-indexdocumento') {
          $prestamodocumentos = DB::table('s_prestamo_documento')
                ->where('s_prestamo_documento.idtienda', $idtienda)
                ->orderBy('s_prestamo_documento.id','desc')
                ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));
          
          $tabla = [];
          foreach ($prestamodocumentos as $value) {
            $opcion = '<li><a href="javascript:;" onclick="editar_documento('.$idtienda.','.$value->id.')"><i class="fa fa-edit"></i> Editar</a></li>
                       <li><a href="javascript:;" onclick="eliminar_documento('.$idtienda.','.$value->id.')"><i class="fa fa-ban"></i> Eliminar</a></li>';

            $tabla[] = [
              'nombre' => $value->nombre,
              'mostrar' => $value->idmostrar == 1 ? 'Desembolso de Crédito' : 'Cobranza de Crédito',
              'opcion' => $opcion
            ];
          }
          
          return json_encode([
              'draw' => $request->input('draw'),
              'recordsTotal' => $prestamodocumentos->total(),
              'recordsFiltered' => $prestamodocumentos->total(),
              'data' => $tabla
          ]);
        }
        elseif ($id == 'show-indexpiso') {
            $pisos = DB::table('s_comida_piso')
                ->where('s_comida_piso.idtienda', $idtienda)
                ->orderBy('s_comida_piso.id','desc')
                ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));

            $tabla = [];
            foreach($pisos as $value){
                $estado = '';
                if ($value->idestado == 1) {
                  $estado = '<span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Activado</span>';
                } elseif ($value->idestado == 2) {
                  $estado = '<span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Desactivado</span>';
                }
              
                $ambientes = DB::table('s_comida_ambiente')
                    ->where([
                      ['s_comida_ambiente.idpiso', $value->id],
                      ['s_comida_ambiente.idtienda', $idtienda]
                    ])
                    ->orderBy('s_comida_ambiente.nombre','asc')
                    ->get();
              
                $data_ambientes = '';
                foreach($ambientes as $valueambiente){
                    $mesas = DB::table('s_comida_mesa')
                        ->where([
                          ['s_comida_mesa.idambiente', $valueambiente->id],
                          ['s_comida_mesa.idtienda', $idtienda]
                        ])
                        ->orderBy('s_comida_mesa.numero_mesa','asc')
                        ->get();

                    $data_mesas = '';
                    foreach($mesas as $valuemesa){
                        $data_ambientes = $data_ambientes.'<span class="badge badge-pill badge-secondary">'.str_pad($valueambiente->nombre, 2, "0", STR_PAD_LEFT).'/'.str_pad($valuemesa->numero_mesa, 2, "0", STR_PAD_LEFT).'</span> ';
                    }
                }

                $opcion = '<li><a href="javascript:;" onclick="editar_piso('.$idtienda.','.$value->id.')"><i class="fa fa-edit"></i> Editar</a></li>
                            <li><a href="javascript:;" onclick="index_ambiente('.$idtienda.','.$value->id.')"><i class="fa fa-store"></i> Ambientes</a></li>
                            <li><a href="javascript:;" onclick="anular_piso('.$idtienda.','.$value->id.')"><i class="fa fa-trash"></i> Eliminar</a></li>';
 

                $tabla[] = [
                    'piso' => str_pad($value->nombre, 2, "0", STR_PAD_LEFT),
                    'ambientes' => $data_ambientes,
                    'estado' => $estado,
                    'opcion' => $opcion
                ];
            }

            return json_encode([
                'draw' => $request->input('draw'),
                'recordsTotal' => $pisos->total(),
                'recordsFiltered' => $pisos->total(),
                'data' => $tabla
            ]);
        }
        elseif ($id == 'show-indexambiente') {
            $ambientes = DB::table('s_comida_ambiente')
                ->where([
                  ['s_comida_ambiente.idpiso', $request->idpiso],
                  ['s_comida_ambiente.idtienda', $idtienda]
                ])
                ->orderBy('s_comida_ambiente.id','desc')
                ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));

            $tabla = [];
            foreach($ambientes as $value){
                $estado = '';
                if ($value->idestado == 1) {
                  $estado = '<span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Activado</span>';
                } elseif ($value->idestado == 2) {
                  $estado = '<span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Desactivado</span>';
                }
              
                $mesas = DB::table('s_comida_mesa')
                    ->where([
                      ['s_comida_mesa.idambiente', $value->id],
                      ['s_comida_mesa.idtienda', $idtienda]
                    ])
                    ->orderBy('s_comida_mesa.numero_mesa','asc')
                    ->get();
              
                $data_mesas = '';
                foreach($mesas as $valuemesa){
                    $data_mesas = $data_mesas.'<span class="badge badge-pill badge-secondary">'.str_pad($valuemesa->numero_mesa, 2, "0", STR_PAD_LEFT).'</span> ';
                }

                $opcion = '<li><a href="javascript:;" onclick="editar_ambiente('.$idtienda.','.$request->idpiso.','.$value->id.')"><i class="fa fa-edit"></i> Editar</a></li>
                            <li><a href="javascript:;" onclick="index_mesa('.$idtienda.','.$request->idpiso.','.$value->id.')"><i class="fa fa-table"></i> Mesas</a></li>
                            <li><a href="javascript:;" onclick="anular_ambiente('.$idtienda.','.$request->idpiso.','.$value->id.')"><i class="fa fa-trash"></i> Eliminar</a></li>';
           

                $tabla[] = [
                    'ambiente' => str_pad($value->nombre, 2, "0", STR_PAD_LEFT),
                    'mesas' => $data_mesas,
                    'estado' => $estado,
                    'opcion' => $opcion
                ];
            }

            return json_encode([
                'draw' => $request->input('draw'),
                'recordsTotal' => $ambientes->total(),
                'recordsFiltered' => $ambientes->total(),
                'data' => $tabla
            ]);
        }
        elseif ($id == 'show-indexmesa') {
            $mesas = DB::table('s_comida_mesa')
                ->where([
                  ['s_comida_mesa.idambiente', $request->idambiente],
                  ['s_comida_mesa.idtienda', $idtienda]
                ])
                ->orderBy('s_comida_mesa.numero_mesa','desc')
                ->paginate($request->input('length'), ['*'], 'page', (($request->input('start')/$request->input('length'))+1));
          
            $tabla = [];
            foreach($mesas as $value){
                $estado = '';
                if ($value->idestado == 1) {
                  $estado = '<span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Activado</span>';
                } elseif ($value->idestado == 2) {
                  $estado = '<span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Desactivado</span>';
                }
              
                $opcion = '<li><a href="javascript:;" onclick="editar_mesa('.$idtienda.','.$request->idpiso.','.$request->idambiente.','.$value->id.')"><i class="fa fa-edit"></i> Editar</a></li>
                          <li><a href="javascript:;" onclick="anular_mesa('.$idtienda.','.$request->idpiso.','.$request->idambiente.','.$value->id.')"><i class="fa fa-trash"></i> Eliminar</a></li>';
              
                $tabla[] = [
                    'numero_mesa' => str_pad($value->numero_mesa, 2, "0", STR_PAD_LEFT),
                    'estado' => $estado,
                    'opcion' => $opcion
                ];
            }
          
            return json_encode([
                'draw' => $request->input('draw'),
                'recordsTotal' => $mesas->total(),
                'recordsFiltered' => $mesas->total(),
                'data' => $tabla
            ]);
        }
    }

    public function edit(Request $request, $idtienda, $idconfiguracion)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if ($request->view == 'config_tiendavirtual') {
          $configuracion = configuracion($idtienda);
          $tipoentregas = DB::table('s_tipoentrega')->get();
          return view('layouts/backoffice/tienda/sistema/configuracion/config_tiendavirtual',[
            'tienda' => $tienda,
            'configuracion' => $configuracion,
            'tipoentregas' => $tipoentregas,
          ]);
        }
        elseif ($request->view == 'config_general') {
            return view('layouts/backoffice/tienda/sistema/configuracion/config_general',[
                'tienda' => $tienda,
            ]);
        }
        elseif ($request->view == 'config_almacen') {
            return view('layouts/backoffice/tienda/sistema/configuracion/config_almacen',[
                'tienda' => $tienda,
            ]);
        }
        elseif ($request->view == 'config_finanza') {
            $agencias = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
            $comprobantes = DB::table('s_tipocomprobante')->get();
            $tipoentregas = DB::table('s_tipoentrega')->get();
            $roles = DB::table('roles')
                ->where('idcategoria',$tienda->idcategoria)
                ->orderBy('roles.description','asc')
                ->get();
            return view('layouts/backoffice/tienda/sistema/configuracion/config_finanza',[
                'tienda' => $tienda,
                'agencias' => $agencias,
                'comprobantes' => $comprobantes,
                'tipoentregas' => $tipoentregas,
                'roles' => $roles,
            ]);
        }
        elseif ($request->view == 'config_credito') {
            $prestamotipotasas = DB::table('s_prestamo_tipotasa')->get();
            return view('layouts/backoffice/tienda/sistema/configuracion/config_credito',[
                'tienda' => $tienda,
                'prestamotipotasas' => $prestamotipotasas,
            ]);
        }
        elseif ($request->view == 'config_ahorro') {
            $prestamotipotasas = DB::table('s_prestamo_tipotasa')->get();
            return view('layouts/backoffice/tienda/sistema/configuracion/config_ahorro',[
                'tienda' => $tienda,
                'prestamotipotasas' => $prestamotipotasas,
            ]);
        }
        elseif ($request->view == 'config_facturacion') {
            $agencias = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
            $comprobantes = DB::table('s_tipocomprobante')->get();
            $monedas = DB::table('s_moneda')->get();
            return view('layouts/backoffice/tienda/sistema/configuracion/config_facturacion',[
              'tienda' => $tienda,
              'agencias' => $agencias,
              'comprobantes' => $comprobantes,
              'monedas' => $monedas,
            ]);
        }
        elseif ($request->view == 'config_prestamo') {
            $agencias = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
            return view('layouts/backoffice/tienda/sistema/configuracion/config_prestamo',[
                'tienda' => $tienda,
                'agencias' => $agencias,
            ]);
        }
        elseif ($request->view == 'config_comida') {
            return view('layouts/backoffice/tienda/sistema/configuracion/config_comida',[
              'tienda' => $tienda,
            ]);
        }
      
      
        elseif ($request->view == 'config_producto') {
          $configuracion = configuracion($idtienda);
          return view('layouts/backoffice/tienda/sistema/configuracion/config_producto',[
            'tienda' => $tienda,
            'configuracion' => $configuracion,
          ]);
        }
        elseif ($request->view == 'prestamo_editardiaferiado') {
          $prestamodiaferiado = DB::table('s_prestamo_diaferiado')
              ->where([
                  ['s_prestamo_diaferiado.idtienda', $idtienda],
                  ['s_prestamo_diaferiado.id', $idconfiguracion]
              ])
              ->first();
          return view('layouts/backoffice/tienda/sistema/configuracion/config_prestamo_diaferiadoedit',[
            'tienda' => $tienda,
            'prestamodiaferiado' => $prestamodiaferiado,
          ]);
        }
        elseif ($request->view == 'prestamo_eliminardiaferiado') {
          $prestamodiaferiado = DB::table('s_prestamo_diaferiado')
              ->where([
                  ['s_prestamo_diaferiado.idtienda', $idtienda],
                  ['s_prestamo_diaferiado.id', $idconfiguracion]
              ])
              ->first();
          return view('layouts/backoffice/tienda/sistema/configuracion/config_prestamo_diaferiadoeliminar',[
            'tienda' => $tienda,
            'prestamodiaferiado' => $prestamodiaferiado,
          ]);
        }
        elseif ($request->view == 'prestamo_editardocumento') {
          $prestamodocumento = DB::table('s_prestamo_documento')
                ->where([
                    ['s_prestamo_documento.idtienda', $idtienda],
                    ['s_prestamo_documento.id', $idconfiguracion]
                ])
                ->first();
          return view('layouts/backoffice/tienda/sistema/configuracion/config_prestamo_documentoedit',[
            'tienda' => $tienda,
            'prestamodocumento' => $prestamodocumento,
          ]);
        }
        elseif ($request->view == 'prestamo_eliminardocumento') {
          $prestamodocumento = DB::table('s_prestamo_documento')
                ->where([
                    ['s_prestamo_documento.idtienda', $idtienda],
                    ['s_prestamo_documento.id', $idconfiguracion]
                ])
                ->first();
          return view('layouts/backoffice/tienda/sistema/configuracion/config_prestamo_documentoeliminar',[
            'tienda' => $tienda,
            'prestamodocumento' => $prestamodocumento,
          ]);
        }
      
        elseif ($request->view == 'comida_indexpiso') {
          return view('layouts/backoffice/tienda/sistema/configuracion/config_comida_indexpiso', [
            'tienda' => $tienda
          ]);
        }
        elseif ($request->view == 'comida_registrarpiso') {
          return view('layouts/backoffice/tienda/sistema/configuracion/config_comida_registrarpiso', [
            'tienda' => $tienda
          ]);
        }
        elseif ($request->view == 'comida_editarpiso') {
          $piso = DB::table('s_comida_piso')
            ->where([
              ['id', $idconfiguracion],
              ['idtienda', $idtienda]
            ])
            ->first();
          return view('layouts/backoffice/tienda/sistema/configuracion/config_comida_editarpiso', [
            'tienda' => $tienda,
            'piso' => $piso
          ]);
        }
        elseif ($request->view == 'comida_anularpiso') {
          $piso = DB::table('s_comida_piso')
            ->where([
              ['id', $idconfiguracion],
              ['idtienda', $idtienda]
            ])
            ->first();
          return view('layouts/backoffice/tienda/sistema/configuracion/config_comida_anularpiso', [
            'tienda' => $tienda,
            'piso' => $piso
          ]);
        }
      
        elseif ($request->view == 'comida_indexambiente') {
          $piso = DB::table('s_comida_piso')
            ->where([
              ['s_comida_piso.id', $request->idpiso],
              ['s_comida_piso.idtienda', $idtienda]
            ])
            ->first();
          return view('layouts/backoffice/tienda/sistema/configuracion/config_comida_indexambiente', [
            'tienda' => $tienda,
            'piso' => $piso
          ]);
        }
        elseif ($request->view == 'comida_registrarambiente') {
          $piso = DB::table('s_comida_piso')
            ->where([
              ['s_comida_piso.id', $request->idpiso],
              ['s_comida_piso.idtienda', $idtienda]
            ])
            ->first();
          return view('layouts/backoffice/tienda/sistema/configuracion/config_comida_registrarambiente', [
            'tienda' => $tienda,
            'piso' => $piso
          ]);
        }
        elseif ($request->view == 'comida_editarambiente') {
          $ambiente = DB::table('s_comida_ambiente')
            ->where([
              ['s_comida_ambiente.id', $idconfiguracion],
              ['s_comida_ambiente.idtienda', $idtienda]
            ])
            ->first();
          $piso = DB::table('s_comida_piso')
            ->where([
              ['s_comida_piso.id', $request->idpiso],
              ['s_comida_piso.idtienda', $idtienda]
            ])
            ->first();
          return view('layouts/backoffice/tienda/sistema/configuracion/config_comida_editarambiente', [
            'tienda' => $tienda,
            'ambiente' => $ambiente,
            'piso' => $piso
          ]);
        }
        elseif ($request->view == 'comida_anularambiente') {
          $ambiente = DB::table('s_comida_ambiente')
            ->where([
              ['s_comida_ambiente.id', $idconfiguracion],
              ['s_comida_ambiente.idtienda', $idtienda]
            ])
            ->first();
          $piso = DB::table('s_comida_piso')
            ->where([
              ['s_comida_piso.id', $request->idpiso],
              ['s_comida_piso.idtienda', $idtienda]
            ])
            ->first();
          return view('layouts/backoffice/tienda/sistema/configuracion/config_comida_anularambiente', [
            'tienda' => $tienda,
            'ambiente' => $ambiente,
            'piso' => $piso
          ]);
        }
      
        elseif ($request->view == 'comida_indexmesa') {
          $piso = DB::table('s_comida_piso')
            ->where([
              ['s_comida_piso.id', $request->idpiso],
              ['s_comida_piso.idtienda', $idtienda]
            ])
            ->first();
          $ambiente = DB::table('s_comida_ambiente')
            ->where([
              ['s_comida_ambiente.id', $request->idambiente],
              ['s_comida_ambiente.idtienda', $idtienda]
            ])
            ->first();
          return view('layouts/backoffice/tienda/sistema/configuracion/config_comida_indexmesa', [
            'tienda' => $tienda,
            'piso' => $piso,
            'ambiente' => $ambiente
          ]);
        }
        elseif ($request->view == 'comida_registrarmesa') {
          $piso = DB::table('s_comida_piso')
            ->where([
              ['s_comida_piso.id', $request->idpiso],
              ['s_comida_piso.idtienda', $idtienda]
            ])
            ->first();
          $ambiente = DB::table('s_comida_ambiente')
            ->where([
              ['s_comida_ambiente.id', $request->idambiente],
              ['s_comida_ambiente.idtienda', $idtienda]
            ])
            ->first();
          return view('layouts/backoffice/tienda/sistema/configuracion/config_comida_registrarmesa', [
            'tienda' => $tienda,
            'piso' => $piso,
            'ambiente' => $ambiente
          ]);
        }
        elseif ($request->view == 'comida_editarmesa') {
          $piso = DB::table('s_comida_piso')
            ->where([
              ['s_comida_piso.id', $request->idpiso],
              ['s_comida_piso.idtienda', $idtienda]
            ])
            ->first();
          $ambiente = DB::table('s_comida_ambiente')
            ->where([
              ['s_comida_ambiente.id', $request->idambiente],
              ['s_comida_ambiente.idtienda', $idtienda]
            ])
            ->first();
          $comidamesa = DB::table('s_comida_mesa')
            ->where([
                ['s_comida_mesa.idtienda', $idtienda],
                ['s_comida_mesa.id', $idconfiguracion]
            ])
            ->first();
          return view('layouts/backoffice/tienda/sistema/configuracion/config_comida_editarmesa', [
            'tienda' => $tienda,
            'piso' => $piso,
            'ambiente' => $ambiente,
            'comidamesa' => $comidamesa
          ]);
        }
        elseif ($request->view == 'comida_anularmesa') {
          $piso = DB::table('s_comida_piso')
            ->where([
              ['s_comida_piso.id', $request->idpiso],
              ['s_comida_piso.idtienda', $idtienda]
            ])
            ->first();
          $ambiente = DB::table('s_comida_ambiente')
            ->where([
              ['s_comida_ambiente.id', $request->idambiente],
              ['s_comida_ambiente.idtienda', $idtienda]
            ])
            ->first();
          $comidamesa = DB::table('s_comida_mesa')
            ->where([
                ['s_comida_mesa.idtienda', $idtienda],
                ['s_comida_mesa.id', $idconfiguracion]
            ])
            ->first();
          return view('layouts/backoffice/tienda/sistema/configuracion/config_comida_anularmesa', [
            'tienda' => $tienda,
            'piso' => $piso,
            'ambiente' => $ambiente,
            'comidamesa' => $comidamesa
          ]);
        }
    }

    public function update(Request $request, $idtienda, $idconfiguracion)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if ($request->input('view') == 'config_general') {
          
          //$configuracion = DB::table('s_configuracion')->whereId($idconfiguracion)->first();

          /*if($idconfiguracion!=0){
              DB::table('s_configuracion')->whereId($idconfiguracion)->update([
                 'imagenlogin' => $imagenlogin,
                 'imagensistema' => $imagensistema,
              ]);
          }else{
              DB::table('s_configuracion')->insert([
                 'imagenlogin' => $imagenlogin,
                 'imagensistema' => $imagensistema,
                 'idtienda' => $idtienda,
              ]);
          }*/
          
          $imagenlogin = uploadfile(configuracion($idtienda,'sistema_imagenfondologin')['valor'],$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/imagenlogin/',2000,2000);
          $imagensistema = uploadfile(configuracion($idtienda,'sistema_imagenfondosistema')['valor'],$request->input('imagenportadaant'),$request->file('imagenportada'),'/public/backoffice/tienda/'.$idtienda.'/imagensistema/',2000,1000);
          
          configuracion_update($idtienda,'sistema_color',$request->sistema_color);
          configuracion_update($idtienda,'sistema_anchoticket',$request->sistema_anchoticket);
          configuracion_update($idtienda,'sistema_anchotarjetapago',$request->sistema_anchotarjetapago);
          configuracion_update($idtienda,'sistema_tipoletra',$request->sistema_tipoletra);
          configuracion_update($idtienda,'sistema_plantilla',$request->sistema_plantilla);
          configuracion_update($idtienda,'sistema_imagenfondologin',$imagenlogin);
          configuracion_update($idtienda,'sistema_imagenfondosistema',$imagensistema);

          return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha actualizado correctamente.'
          ]);
        }
        elseif ($request->input('view') == 'config_credito') {
            $rules = [];
            $messages = [];
            if($request->prestamo_estadodias_gracia == 'on'){
                $rules = array_merge($rules,[
                    'prestamo_dias_gracia_diario' => 'required',
                    'prestamo_dias_gracia_semanal' => 'required',
                    'prestamo_dias_gracia_quincenal' => 'required',
                    'prestamo_dias_gracia_mensual' => 'required',
                    'prestamo_dias_gracia_programado' => 'required',
                ]);
            }
            if($request->prestamo_estadoseguro_degravamen == 'on'){
                $rules = array_merge($rules,[
                    'prestamo_seguro_degravamen_diario' => 'required',
                    'prestamo_seguro_degravamen_semanal' => 'required',
                    'prestamo_seguro_degravamen_quincenal' => 'required',
                    'prestamo_seguro_degravamen_mensual' => 'required',
                    'prestamo_seguro_degravamen_programado' => 'required',
                ]);
            }
            if($request->prestamo_estadomora == 'on'){
                $rules = array_merge($rules,[
                    'prestamo_morapordefecto' => 'required',
                ]);
                if($request->prestamo_morapordefecto == 1){
                    $rules = array_merge($rules,[
                        'prestamo_moratipo' => 'required',
                    ]);
                    if($request->prestamo_moratipo == 1){
                        $rules = array_merge($rules,[
                            'prestamo_mora_diario' => 'required',
                            'prestamo_mora_semanal' => 'required',
                            'prestamo_mora_quincenal' => 'required',
                            'prestamo_mora_mensual' => 'required',
                            'prestamo_mora_programado' => 'required',
                        ]);
                    }
                    elseif($request->prestamo_moratipo == 2){
                        $morarangos = json_decode($request->prestamo_morarango);
                        foreach($morarangos as $value){
                            $rules = array_merge($rules,[
                                'morarango'.$value->num => 'required',
                                'morarangomonto'.$value->num => 'required',
                            ]);
                            $messages = array_merge($messages,[
                                'morarango'.$value->num.'.required' => 'El "Monto" es Obligatorio.',
                                'morarangomonto'.$value->num.'.required' => 'El "Monto x Día" es Obligatorio.',
                            ]);
                        } 
                    }
                }
                elseif($request->prestamo_morapordefecto == 2){
                    $rules = array_merge($rules,[
                        'prestamo_mora_diario_efectiva' => 'required',
                        'prestamo_mora_semanal_efectiva' => 'required',
                        'prestamo_mora_quincenal_efectiva' => 'required',
                        'prestamo_mora_mensual_efectiva' => 'required',
                        'prestamo_mora_programado_efectiva' => 'required',
                    ]);
                }
                    
            }
            $messages = array_merge($messages,[
                'prestamo_dias_gracia_diario.required' => 'El "Días de Gracia Diario" es Obligatorio.',
                'prestamo_dias_gracia_semanal.required' => 'El "Días de Gracia Semanal" es Obligatorio.',
                'prestamo_dias_gracia_quincenal.required' => 'El "Días de Gracia Mensual" es Obligatorio.',
                'prestamo_dias_gracia_mensual.required' => 'El "Días de Gracia Quincenal" es Obligatorio.',
                'prestamo_dias_gracia_programado.required' => 'El "Días de Gracia Programado" es Obligatorio.',
                'prestamo_seguro_degravamen_diario.required' => 'El "Seguro Degravamen Diario" es Obligatorio.',
                'prestamo_seguro_degravamen_semanal.required' => 'El "Seguro Degravamen Semanal" es Obligatorio.',
                'prestamo_seguro_degravamen_quincenal.required' => 'El "Seguro Degravamen Mensual" es Obligatorio.',
                'prestamo_seguro_degravamen_mensual.required' => 'El "Seguro Degravamen Quincenal" es Obligatorio.',
                'prestamo_seguro_degravamen_programado.required' => 'El "Seguro Degravamen Programado" es Obligatorio.',
                'prestamo_morapordefecto.required' => 'El "Mora por Defecto" es Obligatorio.',
                'prestamo_moratipo.required' => 'El "Mora por Defecto" es Obligatorio.',
                'prestamo_mora_diario.required' => 'La "Mora Diario" es Obligatorio.',
                'prestamo_mora_semanal.required' => 'La "Mora Semanal" es Obligatorio.',
                'prestamo_mora_quincenal.required' => 'La "Mora Quincenal" es Obligatorio.',
                'prestamo_mora_mensual.required' => 'La "Mora Mensual" es Obligatorio.',
                'prestamo_mora_programado.required' => 'La "Mora Mensual" es Obligatorio.',
                'prestamo_moratipo.required' => 'El "Mora por Defecto" es Obligatorio.',
                'prestamo_mora_diario_efectiva.required' => 'La "Mora Diario" es Obligatorio.',
                'prestamo_mora_semanal_efectiva.required' => 'La "Mora Semanal" es Obligatorio.',
                'prestamo_mora_quincenal_efectiva.required' => 'La "Mora Quincenal" es Obligatorio.',
                'prestamo_mora_mensual_efectiva.required' => 'La "Mora Mensual" es Obligatorio.',
                'prestamo_mora_programado_efectiva.required' => 'La "Mora Mensual" es Obligatorio.',
            ]);
            $this->validate($request,$rules,$messages);
          
            configuracion_update($idtienda,'prestamo_tipocredito',$request->prestamo_tipocredito);
            configuracion_update($idtienda,'prestamo_estadoacumulado',$request->prestamo_estadoacumulado);
            configuracion_update($idtienda,'prestamo_estadocreditogrupal',$request->prestamo_estadocreditogrupal);
            configuracion_update($idtienda,'prestamo_estadocreditoprendario',$request->prestamo_estadocreditoprendario);
          
            configuracion_update($idtienda,'prestamo_estadotasa',$request->prestamo_estadotasa);
            configuracion_update($idtienda,'prestamo_tasapordefecto',$request->prestamo_tasapordefecto);
            if($request->prestamo_estadodias_gracia == 'on'){
                configuracion_update($idtienda,'prestamo_estadodias_gracia',$request->prestamo_estadodias_gracia);
                configuracion_update($idtienda,'prestamo_dias_gracia_diario',$request->prestamo_dias_gracia_diario);
                configuracion_update($idtienda,'prestamo_dias_gracia_semanal',$request->prestamo_dias_gracia_semanal);
                configuracion_update($idtienda,'prestamo_dias_gracia_quincenal',$request->prestamo_dias_gracia_quincenal);
                configuracion_update($idtienda,'prestamo_dias_gracia_mensual',$request->prestamo_dias_gracia_mensual);
                configuracion_update($idtienda,'prestamo_dias_gracia_programado',$request->prestamo_dias_gracia_programado);
            }else{
                configuracion_delete($idtienda,'prestamo_estadodias_gracia');
                configuracion_delete($idtienda,'prestamo_dias_gracia_diario');
                configuracion_delete($idtienda,'prestamo_dias_gracia_semanal');
                configuracion_delete($idtienda,'prestamo_dias_gracia_quincenal');
                configuracion_delete($idtienda,'prestamo_dias_gracia_mensual');
                configuracion_delete($idtienda,'prestamo_dias_gracia_programado');
            }
            if($request->prestamo_estadoseguro_degravamen == 'on'){
                configuracion_update($idtienda,'prestamo_estadoseguro_degravamen',$request->prestamo_estadoseguro_degravamen);
                configuracion_update($idtienda,'prestamo_seguro_degravamen_diario',$request->prestamo_seguro_degravamen_diario);
                configuracion_update($idtienda,'prestamo_seguro_degravamen_semanal',$request->prestamo_seguro_degravamen_semanal);
                configuracion_update($idtienda,'prestamo_seguro_degravamen_quincenal',$request->prestamo_seguro_degravamen_quincenal);
                configuracion_update($idtienda,'prestamo_seguro_degravamen_mensual',$request->prestamo_seguro_degravamen_mensual);
                configuracion_update($idtienda,'prestamo_seguro_degravamen_programado',$request->prestamo_seguro_degravamen_programado);
            }else{
                configuracion_delete($idtienda,'prestamo_estadoseguro_degravamen');
                configuracion_delete($idtienda,'prestamo_seguro_degravamen_diario');
                configuracion_delete($idtienda,'prestamo_seguro_degravamen_semanal');
                configuracion_delete($idtienda,'prestamo_seguro_degravamen_quincenal');
                configuracion_delete($idtienda,'prestamo_seguro_degravamen_mensual');
                configuracion_delete($idtienda,'prestamo_seguro_degravamen_programado');
            }
            if($request->prestamo_estadoabono == 'on'){
                configuracion_update($idtienda,'prestamo_estadoabono',$request->prestamo_estadoabono);
            }else{
                configuracion_delete($idtienda,'prestamo_estadoabono');
            }
          
            // mora
            if($request->prestamo_estadomora == 'on'){
                configuracion_update($idtienda,'prestamo_estadomora',$request->prestamo_estadomora);
                configuracion_update($idtienda,'prestamo_morapordefecto',$request->prestamo_morapordefecto);
                if($request->prestamo_morapordefecto == 1){
                    configuracion_update($idtienda,'prestamo_moratipo',$request->prestamo_moratipo);
                    if($request->prestamo_moratipo == 1){
                        configuracion_update($idtienda,'prestamo_mora_diario',$request->prestamo_mora_diario);
                        configuracion_update($idtienda,'prestamo_mora_semanal',$request->prestamo_mora_semanal);
                        configuracion_update($idtienda,'prestamo_mora_quincenal',$request->prestamo_mora_quincenal);
                        configuracion_update($idtienda,'prestamo_mora_mensual',$request->prestamo_mora_mensual);
                        configuracion_update($idtienda,'prestamo_mora_programado',$request->prestamo_mora_programado);
                      
                         configuracion_delete($idtienda,'prestamo_morarango');
                        configuracion_delete($idtienda,'prestamo_mora_diario_efectiva');
                        configuracion_delete($idtienda,'prestamo_mora_semanal_efectiva');
                        configuracion_delete($idtienda,'prestamo_mora_quincenal_efectiva');
                        configuracion_delete($idtienda,'prestamo_mora_mensual_efectiva');
                        configuracion_delete($idtienda,'prestamo_mora_programado_efectiva');
                    }
                    elseif($request->prestamo_moratipo == 2){
                        configuracion_update($idtienda,'prestamo_morarango',$request->prestamo_morarango);
                      
                        configuracion_delete($idtienda,'prestamo_mora_diario');
                        configuracion_delete($idtienda,'prestamo_mora_semanal');
                        configuracion_delete($idtienda,'prestamo_mora_quincenal');
                        configuracion_delete($idtienda,'prestamo_mora_mensual');
                        configuracion_delete($idtienda,'prestamo_mora_programado');
                        configuracion_delete($idtienda,'prestamo_mora_diario_efectiva');
                        configuracion_delete($idtienda,'prestamo_mora_semanal_efectiva');
                        configuracion_delete($idtienda,'prestamo_mora_quincenal_efectiva');
                        configuracion_delete($idtienda,'prestamo_mora_mensual_efectiva');
                        configuracion_delete($idtienda,'prestamo_mora_programado_efectiva');
                    }
                }
                elseif($request->prestamo_morapordefecto == 2){

                        configuracion_update($idtienda,'prestamo_mora_diario_efectiva',$request->prestamo_mora_diario_efectiva);
                        configuracion_update($idtienda,'prestamo_mora_semanal_efectiva',$request->prestamo_mora_semanal_efectiva);
                        configuracion_update($idtienda,'prestamo_mora_quincenal_efectiva',$request->prestamo_mora_quincenal_efectiva);
                        configuracion_update($idtienda,'prestamo_mora_mensual_efectiva',$request->prestamo_mora_mensual_efectiva);
                        configuracion_update($idtienda,'prestamo_mora_programado_efectiva',$request->prestamo_mora_programado_efectiva);
                    
                        configuracion_delete($idtienda,'prestamo_moratipo');
                        configuracion_delete($idtienda,'prestamo_morarango');
                        configuracion_delete($idtienda,'prestamo_mora_diario');
                        configuracion_delete($idtienda,'prestamo_mora_semanal');
                        configuracion_delete($idtienda,'prestamo_mora_quincenal');
                        configuracion_delete($idtienda,'prestamo_mora_mensual');
                        configuracion_delete($idtienda,'prestamo_mora_programado');
                }
            }else{
                configuracion_delete($idtienda,'prestamo_morapordefecto');
                configuracion_delete($idtienda,'prestamo_moratipo');
                configuracion_delete($idtienda,'prestamo_morarango');
                configuracion_delete($idtienda,'prestamo_mora_diario');
                configuracion_delete($idtienda,'prestamo_mora_semanal');
                configuracion_delete($idtienda,'prestamo_mora_quincenal');
                configuracion_delete($idtienda,'prestamo_mora_mensual');
                configuracion_delete($idtienda,'prestamo_mora_programado');
                configuracion_delete($idtienda,'prestamo_mora_diario_efectiva');
                configuracion_delete($idtienda,'prestamo_mora_semanal_efectiva');
                configuracion_delete($idtienda,'prestamo_mora_quincenal_efectiva');
                configuracion_delete($idtienda,'prestamo_mora_mensual_efectiva');
                configuracion_delete($idtienda,'prestamo_mora_programado_efectiva');
            }

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'config_ahorro') {
            $rules = [];
            $messages = [];
            if($request->prestamo_ahorro_estadomora == 'on'){
                $rules = array_merge($rules,[
                    'prestamo_ahorro_morapordefecto' => 'required',
                ]);
                if($request->prestamo_ahorro_morapordefecto == 1){
                    $rules = array_merge($rules,[
                        'prestamo_ahorro_moratipo' => 'required',
                    ]);
                    if($request->prestamo_ahorro_moratipo == 1){
                        $rules = array_merge($rules,[
                            'prestamo_ahorro_mora_diario' => 'required',
                            'prestamo_ahorro_mora_semanal' => 'required',
                            'prestamo_ahorro_mora_quincenal' => 'required',
                            'prestamo_ahorro_mora_mensual' => 'required',
                            'prestamo_ahorro_mora_programado' => 'required',
                        ]);
                    }
                    elseif($request->prestamo_ahorro_moratipo == 2){
                        $morarangos = json_decode($request->prestamo_ahorro_morarango);
                        foreach($morarangos as $value){
                            $rules = array_merge($rules,[
                                'morarango'.$value->num => 'required',
                                'morarangomonto'.$value->num => 'required',
                            ]);
                            $messages = array_merge($messages,[
                                'morarango'.$value->num.'.required' => 'El "Monto" es Obligatorio.',
                                'morarangomonto'.$value->num.'.required' => 'El "Monto x Día" es Obligatorio.',
                            ]);
                        } 
                    }
                }
                elseif($request->prestamo_ahorro_morapordefecto == 2){
                    $rules = array_merge($rules,[
                        'prestamo_ahorro_mora_diario_efectiva' => 'required',
                        'prestamo_ahorro_mora_semanal_efectiva' => 'required',
                        'prestamo_ahorro_mora_quincenal_efectiva' => 'required',
                        'prestamo_ahorro_mora_mensual_efectiva' => 'required',
                        'prestamo_ahorro_mora_programado_efectiva' => 'required',
                    ]);
                }
                    
            }
          
            $messages = array_merge($messages,[
                'prestamo_ahorro_morapordefecto.required' => 'El "Mora por Defecto" es Obligatorio.',
                'prestamo_ahorro_moratipo.required' => 'El "Mora por Defecto" es Obligatorio.',
                'prestamo_ahorro_mora_diario.required' => 'La "Mora Diario" es Obligatorio.',
                'prestamo_ahorro_mora_semanal.required' => 'La "Mora Semanal" es Obligatorio.',
                'prestamo_ahorro_mora_quincenal.required' => 'La "Mora Quincenal" es Obligatorio.',
                'prestamo_ahorro_mora_mensual.required' => 'La "Mora Mensual" es Obligatorio.',
                'prestamo_ahorro_mora_programado.required' => 'La "Mora Mensual" es Obligatorio.',
                'prestamo_ahorro_moratipo.required' => 'El "Mora por Defecto" es Obligatorio.',
                'prestamo_ahorro_mora_diario_efectiva.required' => 'La "Mora Diario" es Obligatorio.',
                'prestamo_ahorro_mora_semanal_efectiva.required' => 'La "Mora Semanal" es Obligatorio.',
                'prestamo_ahorro_mora_quincenal_efectiva.required' => 'La "Mora Quincenal" es Obligatorio.',
                'prestamo_ahorro_mora_mensual_efectiva.required' => 'La "Mora Mensual" es Obligatorio.',
                'prestamo_ahorro_mora_programado_efectiva.required' => 'La "Mora Mensual" es Obligatorio.',
            ]);
            $this->validate($request,$rules,$messages);
          
            configuracion_update($idtienda,'prestamo_ahorro_tasapordefecto',$request->prestamo_ahorro_tasapordefecto);
            configuracion_update($idtienda,'prestamo_ahorro_tipoahorrolibre',$request->prestamo_ahorro_tipoahorrolibre);
            configuracion_update($idtienda,'prestamo_ahorro_estadoahorro',$request->prestamo_ahorro_estadoahorro);
            
            // mora
            if($request->prestamo_ahorro_estadomora == 'on'){
                configuracion_update($idtienda,'prestamo_ahorro_estadomora',$request->prestamo_ahorro_estadomora);
                configuracion_update($idtienda,'prestamo_ahorro_morapordefecto',$request->prestamo_ahorro_morapordefecto);
                if($request->prestamo_ahorro_morapordefecto == 1){
                    configuracion_update($idtienda,'prestamo_ahorro_moratipo',$request->prestamo_ahorro_moratipo);
                    if($request->prestamo_ahorro_moratipo == 1){
                        configuracion_update($idtienda,'prestamo_ahorro_mora_diario',$request->prestamo_ahorro_mora_diario);
                        configuracion_update($idtienda,'prestamo_ahorro_mora_semanal',$request->prestamo_ahorro_mora_semanal);
                        configuracion_update($idtienda,'prestamo_ahorro_mora_quincenal',$request->prestamo_ahorro_mora_quincenal);
                        configuracion_update($idtienda,'prestamo_ahorro_mora_mensual',$request->prestamo_ahorro_mora_mensual);
                        configuracion_update($idtienda,'prestamo_ahorro_mora_programado',$request->prestamo_ahorro_mora_programado);
                      
                         configuracion_delete($idtienda,'prestamo_ahorro_morarango');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_diario_efectiva');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_semanal_efectiva');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_quincenal_efectiva');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_mensual_efectiva');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_programado_efectiva');
                    }
                    elseif($request->prestamo_ahorro_moratipo == 2){
                        configuracion_update($idtienda,'prestamo_ahorro_morarango',$request->prestamo_ahorro_morarango);
                      
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_diario');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_semanal');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_quincenal');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_mensual');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_programado');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_diario_efectiva');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_semanal_efectiva');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_quincenal_efectiva');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_mensual_efectiva');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_programado_efectiva');
                    }
                }
                elseif($request->prestamo_ahorro_morapordefecto == 2){

                        configuracion_update($idtienda,'prestamo_ahorro_mora_diario_efectiva',$request->prestamo_ahorro_mora_diario_efectiva);
                        configuracion_update($idtienda,'prestamo_ahorro_mora_semanal_efectiva',$request->prestamo_ahorro_mora_semanal_efectiva);
                        configuracion_update($idtienda,'prestamo_ahorro_mora_quincenal_efectiva',$request->prestamo_ahorro_mora_quincenal_efectiva);
                        configuracion_update($idtienda,'prestamo_ahorro_mora_mensual_efectiva',$request->prestamo_ahorro_mora_mensual_efectiva);
                        configuracion_update($idtienda,'prestamo_ahorro_mora_programado_efectiva',$request->prestamo_ahorro_mora_programado_efectiva);
                    
                        configuracion_delete($idtienda,'prestamo_ahorro_moratipo');
                        configuracion_delete($idtienda,'prestamo_ahorro_morarango');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_diario');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_semanal');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_quincenal');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_mensual');
                        configuracion_delete($idtienda,'prestamo_ahorro_mora_programado');
                }
            }else{
                configuracion_delete($idtienda,'prestamo_ahorro_morapordefecto');
                configuracion_delete($idtienda,'prestamo_ahorro_moratipo');
                configuracion_delete($idtienda,'prestamo_ahorro_morarango');
                configuracion_delete($idtienda,'prestamo_ahorro_mora_diario');
                configuracion_delete($idtienda,'prestamo_ahorro_mora_semanal');
                configuracion_delete($idtienda,'prestamo_ahorro_mora_quincenal');
                configuracion_delete($idtienda,'prestamo_ahorro_mora_mensual');
                configuracion_delete($idtienda,'prestamo_ahorro_mora_programado');
                configuracion_delete($idtienda,'prestamo_ahorro_mora_diario_efectiva');
                configuracion_delete($idtienda,'prestamo_ahorro_mora_semanal_efectiva');
                configuracion_delete($idtienda,'prestamo_ahorro_mora_quincenal_efectiva');
                configuracion_delete($idtienda,'prestamo_ahorro_mora_mensual_efectiva');
                configuracion_delete($idtienda,'prestamo_ahorro_mora_programado_efectiva');
            }
             return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'config_almacen') {

            // Productos
            configuracion_update($idtienda,'sistema_tipocodigoproducto',$request->sistema_tipocodigoproducto);
            configuracion_update($idtienda,'sistema_estadostock',$request->sistema_estadostock);
            configuracion_update($idtienda,'sistema_estadodescuento',$request->sistema_estadodescuento);
            configuracion_update($idtienda,'sistema_estadounidadmedida',$request->sistema_estadounidadmedida);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'config_finanza') {
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            // Apertura de Caja
            configuracion_update($idtienda,'sistema_moneda_usar',$request->sistema_moneda_usar);
            if($request->sistema_moneda_usar==3){
                configuracion_update($idtienda,'sistema_monedapordefecto',$request->sistema_monedapordefecto);
            }else{
                configuracion_delete($idtienda,'sistema_monedapordefecto');
            }
            
            // Cierre de Caja
            configuracion_update($idtienda,'prestamo_tipocierrecaja',$request->prestamo_tipocierrecaja);
            // Venta
            configuracion_update($idtienda,'sistema_nivelventa',$request->sistema_nivelventa);
            configuracion_update($idtienda,'sistema_estadoventa',$request->sistema_estadoventa);
            configuracion_update($idtienda,'sistema_estadopreciounitario',$request->sistema_estadopreciounitario);
            configuracion_update($idtienda,'sistema_tipoentregapordefecto',$request->sistema_tipoentregapordefecto);
            configuracion_update($idtienda,'sistema_estadodescuentoventatotal',$request->sistema_estadodescuentoventatotal);
            configuracion_update($idtienda,'sistema_estadoformapago',$request->sistema_estadoformapago);
            configuracion_update($idtienda,'sistema_mensajeadicionalticket_1',$request->sistema_mensajeadicionalticket_1);
            configuracion_update($idtienda,'sistema_mensajeadicionalticket_2',$request->sistema_mensajeadicionalticket_2);
            configuracion_update($idtienda,'sistema_mensajeadicionalticket_3',$request->sistema_mensajeadicionalticket_3);
            // Movimiento
            configuracion_update($idtienda,'prestamo_tipomovimiento',$request->prestamo_tipomovimiento);
            // Desembolso
            configuracion_update($idtienda,'prestamo_tarjetapago_ubicacionlogo',$request->prestamo_tarjetapago_ubicacionlogo);
            configuracion_update($idtienda,'prestamo_tarjetapago_anchoimpresion',$request->prestamo_tarjetapago_anchoimpresion);
            configuracion_update($idtienda,'prestamo_tarjetapago_mensajeadicionalticket_1',$request->prestamo_tarjetapago_mensajeadicionalticket_1);
            configuracion_update($idtienda,'prestamo_tarjetapago_mensajeadicionalticket_2',$request->prestamo_tarjetapago_mensajeadicionalticket_2);
            configuracion_update($idtienda,'prestamo_tarjetapago_mensajeadicionalticket_3',$request->prestamo_tarjetapago_mensajeadicionalticket_3);
            // Cobranza
            configuracion_update($idtienda,'prestamo_estadodescuentointeres',$request->prestamo_estadodescuentointeres);
            configuracion_update($idtienda,'prestamo_redondeoefectivo',$request->prestamo_redondeoefectivo);
            configuracion_update($idtienda,'prestamo_formatoticket',$request->prestamo_formatoticket);
            configuracion_update($idtienda,'prestamo_mensajeadicionalticket_1',$request->prestamo_mensajeadicionalticket_1);
            configuracion_update($idtienda,'prestamo_mensajeadicionalticket_2',$request->prestamo_mensajeadicionalticket_2);
            configuracion_update($idtienda,'prestamo_mensajeadicionalticket_3',$request->prestamo_mensajeadicionalticket_3);
            
            if($request->prestamo_estadogasto_administrativo == 'on'){
                configuracion_update($idtienda,'prestamo_estadogasto_administrativo',$request->prestamo_estadogasto_administrativo);
                configuracion_update($idtienda,'prestamo_gasto_administrativo_uno',$request->prestamo_gasto_administrativo_uno);
                configuracion_update($idtienda,'prestamo_gasto_administrativo_dos',$request->prestamo_gasto_administrativo_dos);
                configuracion_update($idtienda,'prestamo_gasto_administrativo_tres',$request->prestamo_gasto_administrativo_tres);
                configuracion_update($idtienda,'prestamo_gasto_administrativo_cuatro',$request->prestamo_gasto_administrativo_cuatro);
                configuracion_update($idtienda,'prestamo_gasto_administrativo_cinco',$request->prestamo_gasto_administrativo_cinco);
            }else{
                configuracion_delete($idtienda,'prestamo_estadogasto_administrativo');
                configuracion_delete($idtienda,'prestamo_gasto_administrativo_uno');
                configuracion_delete($idtienda,'prestamo_gasto_administrativo_dos');
                configuracion_delete($idtienda,'prestamo_gasto_administrativo_tres');
                configuracion_delete($idtienda,'prestamo_gasto_administrativo_cuatro');
                configuracion_delete($idtienda,'prestamo_gasto_administrativo_cinco');
            }
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'config_facturacion') {

            configuracion_update($idtienda,'facturacion_igv',$request->facturacion_igv);
            configuracion_update($idtienda,'facturacion_anchoticket',$request->facturacion_anchoticket);
            configuracion_update($idtienda,'facturacion_monedapordefecto',$request->facturacion_monedapordefecto);
            configuracion_update($idtienda,'facturacion_clientepordefecto',$request->facturacion_clientepordefecto);
            configuracion_update($idtienda,'facturacion_empresapordefecto',$request->facturacion_empresapordefecto);
            configuracion_update($idtienda,'facturacion_comprobantepordefecto',$request->facturacion_comprobantepordefecto);
            configuracion_update($idtienda,'facturacion_tipoletra',$request->facturacion_tipoletra);
            configuracion_update($idtienda,'facturacion_estadofacturacion',$request->facturacion_estadofacturacion);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        } 
        elseif ($request->input('view') == 'config_prestamo') {
            $rules = [
              'tarjetapago_ubicacionlogo' => 'required',
              'tarjetapago_anchoimpresion' => 'required',
              'idestadotasa' => 'required',
              'idtasapordefecto' => 'required',
            ];
            if($request->idestadodias_gracia == 'on'){
                $rules = array_merge($rules,[
                    'dias_gracia_diario' => 'required',
                    'dias_gracia_semanal' => 'required',
                    'dias_gracia_quincenal' => 'required',
                    'dias_gracia_mensual' => 'required',
                    'dias_gracia_programado' => 'required',
                ]);
            }
            if($request->idestadoseguro_degravamen == 'on'){
                $rules = array_merge($rules,[
                    'seguro_degravamen' => 'required',
                    'seguro_degravamen_semanal' => 'required',
                    'seguro_degravamen_quincenal' => 'required',
                    'seguro_degravamen_mensual' => 'required',
                    'seguro_degravamen_programado' => 'required',
                ]);
            }
            if($request->idestadogasto_administrativo == 'on'){
                $rules = array_merge($rules,[
                    'gasto_administrativo_uno' => 'required',
                    'gasto_administrativo_dos' => 'required',
                    'gasto_administrativo_tres' => 'required',
                    'gasto_administrativo_cuatro' => 'required',
                    'gasto_administrativo_cinco' => 'required',
                ]);
            }
            if($request->idestadomora == 'on'){
                $rules = array_merge($rules,[
                    'idmorapordefecto' => 'required',
                    'mora_diario' => 'required',
                    'mora_semanal' => 'required',
                    'mora_quincenal' => 'required',
                    'mora_mensual' => 'required',
                    'mora_programado' => 'required',
                ]);
            }
            $messages = [
                'idestadotasa.required' => 'El "Estado de Tasa" es Obligatorio.',
                'idtasapordefecto.required' => 'La "Tasa" es Obligatorio.',
                'tarjetapago_ubicacionlogo.required' => 'La "Ubicación de Logo" es Obligatorio.',
                'tarjetapago_anchoimpresion.required' => 'El "Ancho de Impresión" es Obligatorio.',
                'dias_gracia_diario.required' => 'El "Días de Gracia Diario" es Obligatorio.',
                'dias_gracia_semanal.required' => 'El "Días de Gracia Semanal" es Obligatorio.',
                'dias_gracia_quincenal.required' => 'El "Días de Gracia Mensual" es Obligatorio.',
                'dias_gracia_mensual.required' => 'El "Días de Gracia Quincenal" es Obligatorio.',
                'dias_gracia_programado.required' => 'El "Días de Gracia Programado" es Obligatorio.',
                'seguro_degravamen.required' => 'El "Seguro Degravamen Diario" es Obligatorio.',
                'seguro_degravamen_semanal.required' => 'El "Seguro Degravamen Semanal" es Obligatorio.',
                'seguro_degravamen_quincenal.required' => 'El "Seguro Degravamen Mensual" es Obligatorio.',
                'seguro_degravamen_mensual.required' => 'El "Seguro Degravamen Quincenal" es Obligatorio.',
                'seguro_degravamen_programado.required' => 'El "Seguro Degravamen Programado" es Obligatorio.',
                'gasto_administrativo_uno.required' => 'El "1er Gasto Administrativo" es Obligatorio.',
                'gasto_administrativo_dos.required' => 'El "2do Gasto Administrativo" es Obligatorio.',
                'gasto_administrativo_tres.required' => 'El "3er Gasto Administrativo" es Obligatorio.',
                'gasto_administrativo_cuatro.required' => 'El "4to Gasto Administrativo" es Obligatorio.',
                'gasto_administrativo_cinco.required' => 'El "5to Gasto Administrativo" es Obligatorio.',
                'idmorapordefecto.required' => 'El "Mora por Defecto" es Obligatorio.',
                'mora_diario.required' => 'La "Mora Diario" es Obligatorio.',
                'mora_semanal.required' => 'La "Mora Semanal" es Obligatorio.',
                'mora_quincenal.required' => 'La "Mora Quincenal" es Obligatorio.',
                'mora_mensual.required' => 'La "Mora Mensual" es Obligatorio.',
                'mora_programado.required' => 'La "Mora Mensual" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            if($idconfiguracion!=0){
                DB::table('s_configuracionprestamo')->update([
                   'tarjetapago_ubicacionlogo' => ($request->input('tarjetapago_ubicacionlogo')==null or $request->input('tarjetapago_ubicacionlogo')=='null')?0:$request->input('tarjetapago_ubicacionlogo'),
                   'tarjetapago_anchoimpresion' => ($request->input('tarjetapago_anchoimpresion')==null or $request->input('tarjetapago_anchoimpresion')=='null')?0:$request->input('tarjetapago_anchoimpresion'),
                   'dias_gracia_diario' => ($request->input('dias_gracia_diario')==null or $request->input('dias_gracia_diario')=='null')?0:$request->input('dias_gracia_diario'),
                   'dias_gracia_semanal' => ($request->input('dias_gracia_semanal')==null or $request->input('dias_gracia_semanal')=='null')?0:$request->input('dias_gracia_semanal'),
                   'dias_gracia_quincenal' => ($request->input('dias_gracia_quincenal')==null or $request->input('dias_gracia_quincenal')=='null')?0:$request->input('dias_gracia_quincenal'),
                   'dias_gracia_mensual' => ($request->input('dias_gracia_mensual')==null or $request->input('dias_gracia_mensual')=='null')?0:$request->input('dias_gracia_mensual'),
                   'dias_gracia_programado' => ($request->input('dias_gracia_programado')==null or $request->input('dias_gracia_programado')=='null')?0:$request->input('dias_gracia_programado'),
                   'seguro_degravamen' => ($request->input('seguro_degravamen')==null or $request->input('seguro_degravamen')=='null')?0:$request->input('seguro_degravamen'),
                   'seguro_degravamen_semanal' => ($request->input('seguro_degravamen_semanal')==null or $request->input('seguro_degravamen_semanal')=='null')?0:$request->input('seguro_degravamen_semanal'),
                   'seguro_degravamen_quincenal' => ($request->input('seguro_degravamen_quincenal')==null or $request->input('seguro_degravamen_quincenal')=='null')?0:$request->input('seguro_degravamen_quincenal'),
                   'seguro_degravamen_mensual' => ($request->input('seguro_degravamen_mensual')==null or $request->input('seguro_degravamen_mensual')=='null')?0:$request->input('seguro_degravamen_mensual'),
                   'seguro_degravamen_programado' => ($request->input('seguro_degravamen_programado')==null or $request->input('seguro_degravamen_programado')=='null')?0:$request->input('seguro_degravamen_programado'),
                   'gasto_administrativo_uno' => ($request->input('gasto_administrativo_uno')==null or $request->input('gasto_administrativo_uno')=='null')?0:$request->input('gasto_administrativo_uno'),
                   'gasto_administrativo_dos' => ($request->input('gasto_administrativo_dos')==null or $request->input('gasto_administrativo_dos')=='null')?0:$request->input('gasto_administrativo_dos'),
                   'gasto_administrativo_tres' => ($request->input('gasto_administrativo_tres')==null or $request->input('gasto_administrativo_tres')=='null')?0:$request->input('gasto_administrativo_tres'),
                   'gasto_administrativo_cuatro' => ($request->input('gasto_administrativo_cuatro')==null or $request->input('gasto_administrativo_cuatro')=='null')?0:$request->input('gasto_administrativo_cuatro'),
                   'gasto_administrativo_cinco' => ($request->input('gasto_administrativo_cinco')==null or $request->input('gasto_administrativo_cinco')=='null')?0:$request->input('gasto_administrativo_cinco'),
                   'mora_diario' => ($request->input('mora_diario')==null or $request->input('mora_diario')=='null')?0:$request->input('mora_diario'),
                   'mora_semanal' => ($request->input('mora_semanal')==null or $request->input('mora_semanal')=='null')?0:$request->input('mora_semanal'),
                   'mora_quincenal' => ($request->input('mora_quincenal')==null or $request->input('mora_quincenal')=='null')?0:$request->input('mora_quincenal'),
                   'mora_mensual' => ($request->input('mora_mensual')==null or $request->input('mora_mensual')=='null')?0:$request->input('mora_mensual'),
                   'mora_programado' => ($request->input('mora_programado')==null or $request->input('mora_programado')=='null')?0:$request->input('mora_programado'),
                   'idestadodias_gracia' => $request->input('idestadodias_gracia')=='undefined'?2:1,
                   'idestadoseguro_degravamen' => $request->input('idestadoseguro_degravamen')=='undefined'?2:1,
                   'idestadogasto_administrativo' => $request->input('idestadogasto_administrativo')=='undefined'?2:1,
                   'idtasapordefecto' => ($request->input('idtasapordefecto')==null or $request->input('idtasapordefecto')=='null')?0:$request->input('idtasapordefecto'),
                   'idestadotasa' => ($request->input('idestadotasa')==null or $request->input('idestadotasa')=='null')?0:$request->input('idestadotasa'),
                   'idmorapordefecto' => ($request->input('idmorapordefecto')==null or $request->input('idmorapordefecto')=='null')?0:$request->input('idmorapordefecto'),
                   'idestadomora' => $request->input('idestadomora')=='undefined'?2:1,
                ]);
            } else {
                DB::table('s_configuracionprestamo')->insert([
                   'tarjetapago_ubicacionlogo' => $request->input('tarjetapago_ubicacionlogo')!='null'?$request->input('tarjetapago_ubicacionlogo'):0,
                   'tarjetapago_anchoimpresion' => $request->input('tarjetapago_anchoimpresion')!='null'?$request->input('tarjetapago_anchoimpresion'):0,
                   'dias_gracia_diario' => $request->input('dias_gracia_diario')!='null'?$request->input('dias_gracia_diario'):0,
                   'dias_gracia_semanal' => $request->input('dias_gracia_semanal')!='null'?$request->input('dias_gracia_semanal'):0,
                   'dias_gracia_quincenal' => $request->input('dias_gracia_quincenal')!='null'?$request->input('dias_gracia_quincenal'):0,
                   'dias_gracia_mensual' => $request->input('dias_gracia_mensual')!='null'?$request->input('dias_gracia_mensual'):0,
                   'dias_gracia_programado' => $request->input('dias_gracia_programado')!='null'?$request->input('dias_gracia_programado'):0,
                   'seguro_degravamen' => $request->input('seguro_degravamen')!='null'?$request->input('seguro_degravamen'):0,
                   'seguro_degravamen_semanal' => $request->input('seguro_degravamen_semanal')!='null'?$request->input('seguro_degravamen_semanal'):0,
                   'seguro_degravamen_quincenal' => $request->input('seguro_degravamen_quincenal')!='null'?$request->input('seguro_degravamen_quincenal'):0,
                   'seguro_degravamen_mensual' => $request->input('seguro_degravamen_mensual')!='null'?$request->input('seguro_degravamen_mensual'):0,
                   'seguro_degravamen_programado' => $request->input('seguro_degravamen_programado')!='null'?$request->input('seguro_degravamen_programado'):0,
                   'gasto_administrativo_uno' => $request->input('gasto_administrativo_uno')!='null'?$request->input('gasto_administrativo_uno'):0,
                   'gasto_administrativo_dos' => $request->input('gasto_administrativo_dos')!='null'?$request->input('gasto_administrativo_dos'):0,
                   'gasto_administrativo_tre' => $request->input('gasto_administrativo_tre')!='null'?$request->input('gasto_administrativo_tre'):0,
                   'gasto_administrativo_cuatro' => $request->input('gasto_administrativo_cuatro')!='null'?$request->input('gasto_administrativo_cuatro'):0,
                   'gasto_administrativo_cinco' => $request->input('gasto_administrativo_cinco')!='null'?$request->input('gasto_administrativo_cinco'):0,
                   'mora_diario' => $request->input('mora_diario')!='null'?$request->input('mora_diario'):0,
                   'mora_semanal' => $request->input('mora_semanal')!='null'?$request->input('mora_semanal'):0,
                   'mora_quincenal' => $request->input('mora_quincenal')!='null'?$request->input('mora_quincenal'):0,
                   'mora_mensual' => $request->input('mora_mensual')!='null'?$request->input('mora_mensual'):0,
                   'mora_programado' => $request->input('mora_programado')!='null'?$request->input('mora_programado'):0,
                   'idestadodias_gracia' => $request->input('idestadodias_gracia')!='undefined'?1:2,
                   'idestadoseguro_degravamen' => $request->input('idestadoseguro_degravamen')!='undefined'?1:2,
                   'idestadogasto_administrativo' => $request->input('idestadogasto_administrativo')!='undefined'?1:2,
                   'idtasapordefecto' => $request->input('idtasapordefecto')!='null'?$request->input('idtasapordefecto'):0,
                   'idestadotasa' => $request->input('idestadotasa')!='null'?$request->input('idestadotasa'):0,
                   'idmorapordefecto' => $request->input('idmorapordefecto')!='null'?$request->input('idmorapordefecto'):0,
                   'idestadomora' => $request->input('idestadomora')!='undefined'?1:2,
                   'idtienda' => $idtienda,
                ]);
            }

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'config_comida') {
          
          configuracion_update($idtienda,'comida_cantidadmesa',$request->comida_cantidadmesa);

          return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha actualizado correctamente.'
          ]);
        }
        elseif ($request->input('view') == 'config_comida_nuevo') {
//           dd($request->piso_ambiente_mesa);
//            configuracion_update($idtienda,'comida_cantidadmesa',$request->piso_ambiente_mesa);
          
           return response()->json([
              'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
           ]);
        }
      
        elseif ($request->input('view') == 'editar-diaferiado') {
            $rules = [
                'diaferiado_editar_dia' => 'required',
                'diaferiado_editar_mes' => 'required',
                'diaferiado_editar_motivo' => 'required'
            ];
            $messages = [
                'diaferiado_editar_dia.required' => 'El "Dia" es Obligatorio.',
                'diaferiado_editar_mes.required' => 'El "Mes" es Obligatorio.',
                'diaferiado_editar_motivo.required' => 'El "Motivo" es Obligatorio.'
            ];
            $this->validate($request, $rules, $messages);
            DB::table('s_prestamo_diaferiado')->whereId($request->idprestamo_diaferiado)->update([
                'dia' => $request->diaferiado_editar_dia,
                'mes' => $request->diaferiado_editar_mes,
                'motivo' => $request->diaferiado_editar_motivo
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'editar-documento') {
            $rules = [
                'documento_editar_nombre'    => 'required',
                'documento_editar_contenido' => 'required'
            ];
            $messages = [
                'documento_editar_nombre.required'    => 'El "Nombre" es Obligatorio.',
                'documento_editar_contenido.required' => 'El "Documento" es Obligatorio.'
            ];
            $this->validate($request, $rules, $messages);
          
            DB::table('s_prestamo_documento')->whereId($request->idprestamo_documento)->update([
                'nombre'    => $request->documento_editar_nombre,
                'contenido' => $request->documento_editar_contenido,
                'idmostrar' => $request->documento_editar_mostrar,
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
      
        elseif ($request->input('view') == 'comida-editar-piso') {
            $rules = [
                'nombre' => 'required',
                'idestado' => 'required'
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.'
            ];
            $this->validate($request, $rules, $messages);
          
            DB::table('s_comida_piso')->whereId($request->idpiso)->update([
                'nombre' => $request->nombre,
                'idestado' => $request->idestado
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'comida-anular-piso') {
            
            $count_ambientes = DB::table('s_comida_ambiente')
                    ->where([
                      ['s_comida_ambiente.idpiso', $request->idpiso],
                      ['s_comida_ambiente.idtienda', $idtienda]
                    ])
                    ->count();
            if($count_ambientes>0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No puede eliminar el piso, hasta eliminar todo las ambientes enlazadas.'
                ]);
            }
          
            DB::table('s_comida_piso')
                ->where([
                    ['s_comida_piso.idtienda', $idtienda],
                    ['s_comida_piso.id', $request->idpiso]
                ])
                ->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
      
        elseif ($request->input('view') == 'comida-editar-ambiente') {
            $rules = [
                'nombre' => 'required',
                'idestado' => 'required'
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.'
            ];
            $this->validate($request, $rules, $messages);
          
            DB::table('s_comida_ambiente')->whereId($request->idambiente)->update([
                'nombre' => $request->nombre,
                'idestado' => $request->idestado
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'comida-anular-ambiente') {
          
            $count_mesas = DB::table('s_comida_mesa')
                    ->where([
                      ['s_comida_mesa.idambiente', $request->idambiente],
                      ['s_comida_mesa.idtienda', $idtienda]
                    ])
                    ->count();
            if($count_mesas>0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No puede eliminar el ambiente, hasta eliminar toda las mesas enlazadas.'
                ]);
            }
            DB::table('s_comida_ambiente')
                ->where([
                    ['s_comida_ambiente.idtienda', $idtienda],
                    ['s_comida_ambiente.id', $request->idambiente]
                ])
                ->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
      
        elseif ($request->input('view') == 'comida-editar-mesa') {
            $rules = [
                'mesa_editar_numero_mesa' => 'required',
                'idestado' => 'required'
            ];
            $messages = [
                'mesa_editar_numero_mesa.required' => 'El "Número de Mesa" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.'
            ];
            $this->validate($request, $rules, $messages);
          

            $mesas = DB::table('s_comida_mesa')
              ->where([
                ['id','<>', $request->idcomida_mesa],
                ['numero_mesa', $request->mesa_editar_numero_mesa],
                ['idambiente', $request->idambiente],
                ['idpiso', $request->idpiso],
                ['idtienda', $idtienda] 
              ])
              ->first();
          
            if (!is_null($mesas)) {
              return response()->json([
                'resultado' => 'ERROR',
                'mensaje'   => "El Número de Mesa ya Existe, ingrese otro por número por favor"
              ]);
            }

            DB::table('s_comida_mesa')->whereId($request->idcomida_mesa)->update([
                'numero_mesa' => $request->mesa_editar_numero_mesa,
                'idestado' => $request->idestado
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'comida-anular-mesa') {
            DB::table('s_comida_mesa')
                ->where([
                    ['s_comida_mesa.idtienda', $idtienda],
                    ['s_comida_mesa.id', $request->idcomida_mesa]
                ])
                ->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    } 

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda, $idconfiguracion)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if ($request->input('view') == 'eliminar-diaferiado') {
            DB::table('s_prestamo_diaferiado')
                ->where([
                    ['s_prestamo_diaferiado.idtienda', $idtienda],
                    ['s_prestamo_diaferiado.id', $request->idprestamo_diaferiado]
                ])
                ->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'eliminar-documento') {
            DB::table('s_prestamo_documento')
                ->where([
                    ['s_prestamo_documento.idtienda', $idtienda],
                    ['s_prestamo_documento.id', $request->idprestamo_documento]
                ])
                ->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
