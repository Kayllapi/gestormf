<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class ConfiguracionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/nuevosistema/configuracion/index', [
            'tienda' => $tienda
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        /*if ($request->view == 'prestamo_registrardiaferiado') {
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_prestamo_diaferiadoregistrar', [
            'tienda' => $tienda
          ]);
        }
        elseif ($request->view == 'prestamo_registrardocumento') {
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_prestamo_documentoregistrar', [
            'tienda' => $tienda
          ]);
        }*/
      
        
        if ($request->view == 'config_general') {
          $configuracion = configuracion_general($idtienda);
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_general',[
            'tienda' => $tienda,
            'configuracion' => $configuracion,
          ]);
        }
        elseif ($request->view == 'config_comercio') {
          $configuracion = configuracion_comercio($idtienda);
          $agencias = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
          $comprobantes = DB::table('s_tipocomprobante')->get();
          $tipoentregas = DB::table('s_tipoentrega')->get();
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_comercio',[
            'tienda' => $tienda,
            'configuracion' => $configuracion,
            'agencias' => $agencias,
            'comprobantes' => $comprobantes,
            'tipoentregas' => $tipoentregas,
          ]);
        }
        elseif ($request->view == 'config_facturacion') {
          $configuracion = configuracion_facturacion($idtienda);
          $agencias = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
          $comprobantes = DB::table('s_tipocomprobante')->get();
          $monedas = DB::table('s_moneda')->get();
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_facturacion',[
            'tienda' => $tienda,
            'configuracion' => $configuracion,
            'agencias' => $agencias,
            'comprobantes' => $comprobantes,
            'monedas' => $monedas,
          ]);
        }
        elseif ($request->view == 'config_prestamo') {
            $configuracion = configuracion_prestamo($idtienda);
            $agencias = DB::table('s_agencia')->where('idtienda',$idtienda)->get();
            return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_prestamo',[
                'tienda' => $tienda,
                'configuracion' => $configuracion,
                'agencias' => $agencias,
            ]);
        }
        elseif ($request->view == 'config_comida') {
            $configuracion = configuracion_comida($idtienda);
            return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_comida',[
              'tienda' => $tienda,
              'configuracion' => $configuracion,
            ]);
        }
        elseif ($request->view == 'config_ventacomida') {
          $configuracion = configuracion_comida($idtienda);
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_ventacomida',[
            'tienda' => $tienda,
            'configuracion' => $configuracion
          ]);
        }
        elseif ($request->view == 'config_producto') {
          $configuracion = configuracion($idtienda);
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_producto',[
            'tienda' => $tienda,
            'configuracion' => $configuracion,
          ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if ($id == 'show-indexdiaferiado') {
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $idconfiguracion)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if ($request->view == 'config_tiendavirtual') {
          $configuracion = configuracion($idtienda);
          $tipoentregas = DB::table('s_tipoentrega')->get();
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_tiendavirtual',[
            'tienda' => $tienda,
            'configuracion' => $configuracion,
            'tipoentregas' => $tipoentregas,
          ]);
        }
        elseif ($request->view == 'prestamo_editardiaferiado') {
          $prestamodiaferiado = DB::table('s_prestamo_diaferiado')
              ->where([
                  ['s_prestamo_diaferiado.idtienda', $idtienda],
                  ['s_prestamo_diaferiado.id', $idconfiguracion]
              ])
              ->first();
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_prestamo_diaferiadoedit',[
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
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_prestamo_diaferiadoeliminar',[
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
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_prestamo_documentoedit',[
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
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_prestamo_documentoeliminar',[
            'tienda' => $tienda,
            'prestamodocumento' => $prestamodocumento,
          ]);
        }
      
        elseif ($request->view == 'comida_indexpiso') {
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_comida_indexpiso', [
            'tienda' => $tienda
          ]);
        }
        elseif ($request->view == 'comida_registrarpiso') {
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_comida_registrarpiso', [
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
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_comida_editarpiso', [
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
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_comida_anularpiso', [
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
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_comida_indexambiente', [
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
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_comida_registrarambiente', [
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
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_comida_editarambiente', [
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
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_comida_anularambiente', [
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
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_comida_indexmesa', [
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
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_comida_registrarmesa', [
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
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_comida_editarmesa', [
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
          return view('layouts/backoffice/tienda/nuevosistema/configuracion/config_comida_anularmesa', [
            'tienda' => $tienda,
            'piso' => $piso,
            'ambiente' => $ambiente,
            'comidamesa' => $comidamesa
          ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idtienda, $idconfiguracion)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if ($request->input('view') == 'config_general') {
          
          $configuracion = DB::table('s_configuracion')->whereId($idconfiguracion)->first();
          
          $imagenlogin = uploadfile($configuracion->imagenlogin,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/imagenlogin/',2000,2000);
          $imagensistema = uploadfile($configuracion->imagensistema,$request->input('imagenportadaant'),$request->file('imagenportada'),'/public/backoffice/tienda/'.$idtienda.'/imagennuevosistema/',2000,1000);

          if($idconfiguracion!=0){
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
          }

          return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha actualizado correctamente.'
          ]);
        }
        elseif ($request->input('view') == 'config_comercio') {
          $rules = [
              'nivelventa' => 'required',
              'estadoventa' => 'required',
              'estadounidadmedida' => 'required',
              'estadodescuento' => 'required',
          ];
          $messages = [
              'nivelventa.required' => 'El "Nivel de Venta" es Obligatorio.',
              'estadoventa.required' => 'El "Estado de Venta por Defecto" es Obligatorio.',
              'estadounidadmedida.required' => 'El "Nivel de Venta" es Obligatorio.',
              'estadodescuento.required' => 'El "Estado de Venta por Defecto" es Obligatorio.',
          ];
          $this->validate($request,$rules,$messages);

          if($idconfiguracion!=0){
              DB::table('s_configuracioncomercio')->whereId($idconfiguracion)->update([
                 'estadostock' => ($request->input('estadostock')==null or $request->input('estadostock')=='null')?2:$request->input('estadostock'),
                 'nivelventa' => ($request->input('nivelventa')==null or $request->input('nivelventa')=='null')?2:$request->input('nivelventa'),
                 'estadoventa' => ($request->input('estadoventa')==null or $request->input('estadoventa')=='null')?2:$request->input('estadoventa'),
                 'estadounidadmedida' => ($request->input('estadounidadmedida')==null or $request->input('estadounidadmedida')=='null')?2:$request->input('estadounidadmedida'),
                 'estadodescuento' => ($request->input('estadodescuento')==null or $request->input('estadodescuento')=='null')?2:$request->input('estadodescuento'),
                 'idtipoentregapordefecto' => ($request->input('idtipoentregapordefecto')==null or $request->input('idtipoentregapordefecto')=='null')?0:$request->input('idtipoentregapordefecto'),
              ]);
          }else{
              DB::table('s_configuracioncomercio')->insert([
                 'estadostock' => $request->input('estadostock')!='null'?$request->input('estadostock'):2,
                 'nivelventa' => $request->input('nivelventa')!='null'?$request->input('nivelventa'):2,
                 'estadoventa' => $request->input('estadoventa')!='null'?$request->input('estadoventa'):2,
                 'estadounidadmedida' => $request->input('estadounidadmedida')!='null'?$request->input('estadounidadmedida'):2,
                 'estadodescuento' => $request->input('estadodescuento')!='null'?$request->input('estadodescuento'):2,
                 'idtipoentregapordefecto' => $request->input('idtipoentregapordefecto')!='null'?$request->input('idtipoentregapordefecto'):0,
                 'idtienda' => $idtienda,
              ]);
          }

          return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha actualizado correctamente.'
          ]);
        }
        elseif ($request->input('view') == 'config_facturacion') {
          $rules = [
              'igv' => 'required',
              'anchoticket' => 'required',
              'idmonedapordefecto' => 'required',
          ];
          $messages = [
              'igv.required' => 'El "IGV" es Obligatorio.',
              'anchoticket.required' => 'El "Ancho de Ticket" es Obligatorio.',
              'idmonedapordefecto.required' => 'La "Moneda por defecto" es Obligatorio.',
          ];
          $this->validate($request,$rules,$messages);
          if($idconfiguracion!=0){
              DB::table('s_configuracionfacturacion')->whereId($idconfiguracion)->update([
                 'igv' => ($request->input('igv')==null or $request->input('igv')=='null')?0:$request->input('igv'),
                 'anchoticket' => ($request->input('anchoticket')==null or $request->input('anchoticket')=='null')?0:$request->input('anchoticket'),
                 'idclientepordefecto' => ($request->input('idclientepordefecto')==null or $request->input('idclientepordefecto')=='null')?0:$request->input('idclientepordefecto'),
                 'idempresapordefecto' => ($request->input('idempresapordefecto')==null or $request->input('idempresapordefecto')=='null')?0:$request->input('idempresapordefecto'),
                 'idcomprobantepordefecto' => ($request->input('idcomprobantepordefecto')==null or $request->input('idcomprobantepordefecto')=='null')?0:$request->input('idcomprobantepordefecto'),
                 'idmonedapordefecto' => ($request->input('idmonedapordefecto')==null or $request->input('idmonedapordefecto')=='null')?0:$request->input('idmonedapordefecto'),
              ]);
          }else{
              DB::table('s_configuracionfacturacion')->insert([
                 'igv' => $request->input('igv')!='null'?$request->input('igv'):0,
                 'anchoticket' => $request->input('anchoticket')!='null'?$request->input('anchoticket'):0,
                 'idclientepordefecto' => $request->input('idclientepordefecto')!='null'?$request->input('idclientepordefecto'):0,
                 'idempresapordefecto' => $request->input('idempresapordefecto')!='null'?$request->input('idempresapordefecto'):0,
                 'idcomprobantepordefecto' => $request->input('idcomprobantepordefecto')!='null'?$request->input('idcomprobantepordefecto'):0,
                 'idmonedapordefecto' => $request->input('idmonedapordefecto')!='null'?$request->input('idmonedapordefecto'):0,
                 'idestado' => 1,
                 'idtienda' => $idtienda,
              ]);
          }

          return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha actualizado correctamente.'
          ]);
        } 
        elseif ($request->input('view') == 'config_prestamo') {
            $rules = [
              'diagracia' => 'required',
              'idestadotasa' => 'required',
              'idtasapordefecto' => 'required',
            ];
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
                'diagracia.required' => 'El "Día de Gracia" es Obligatorio.',
                'idestadotasa.required' => 'El "Estado de Tasa" es Obligatorio.',
                'idtasapordefecto.required' => 'La "Tasa" es Obligatorio.',
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
                   'diagracia' => ($request->input('diagracia')==null or $request->input('diagracia')=='null')?0:$request->input('diagracia'),
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
                   'idestadoseguro_degravamen' => $request->input('idestadoseguro_degravamen')=='undefined'?2:1,
                   'idestadogasto_administrativo' => $request->input('idestadogasto_administrativo')=='undefined'?2:1,
                   'idtasapordefecto' => ($request->input('idtasapordefecto')==null or $request->input('idtasapordefecto')=='null')?0:$request->input('idtasapordefecto'),
                   'idestadotasa' => ($request->input('idestadotasa')==null or $request->input('idestadotasa')=='null')?0:$request->input('idestadotasa'),
                   'idmorapordefecto' => ($request->input('idmorapordefecto')==null or $request->input('idmorapordefecto')=='null')?0:$request->input('idmorapordefecto'),
                   'idestadomora' => $request->input('idestadomora')=='undefined'?2:1,
                ]);
            } else {
                DB::table('s_configuracionprestamo')->insert([
                   'diagracia' => $request->input('diagracia')!='null'?$request->input('diagracia'):0,
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
        elseif ($request->input('view') == 'config_ventacomida') {
          $rules = [
              'ventacomida_numeromesa' => 'required',
          ];
          $messages = [
              'ventacomida_numeromesa.required' => 'El "Ancho de Ticket" es Obligatorio.',
          ];
          $this->validate($request,$rules,$messages);

          if($idconfiguracion!=0){
              DB::table('s_configuracion')->whereId($idconfiguracion)->update([
                 'fechamodificacion' => Carbon::now(),
                 'ventacomida_estado' => ($request->input('ventacomida_estado')==null or $request->input('ventacomida_estado')=='null')?2:$request->input('ventacomida_estado'),
                 'ventacomida_numeromesa' => $request->input('ventacomida_numeromesa')
              ]);
          }else{
              DB::table('s_configuracion')->insert([
                 'fechamodificacion' => Carbon::now(),
                 /*'venta_idclientepordefecto' => 0,
                 'venta_idempresapordefecto' => 0,
                 'venta_idtipoentregapordefecto' => 0,
                 'venta_idcomprobantepordefecto' => 0,
                 'venta_estadostock' => 2,
                 'venta_estadoventa' => 2,
                 'venta_anchoticket' => 7.9,*/
                 'ventacomida_estado' => $request->input('ventacomida_estado')!='null'?$request->input('ventacomida_estado'):2,
                 'ventacomida_numeromesa' => $request->input('ventacomida_numeromesa'),
                 //'producto_estadounidadmedida' => 2,
                 'idtienda' => $idtienda,
              ]);
          }

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
                'contenido' => $request->documento_editar_contenido
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
