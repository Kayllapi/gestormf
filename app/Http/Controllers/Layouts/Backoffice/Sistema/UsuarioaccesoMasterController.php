<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UsuarioaccesoMasterController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(usuario_es_master(), 403);

            return $next($request);
        });
    }

    public function index(Request $request, $idtienda)
    {
        return view(sistema_view().'/usuarioaccesomaster/tabla', [
            'tienda' => DB::table('tienda')->whereId($idtienda)->firstOrFail(),
            'puede_registrar' => true,
        ]);
    }

    public function create(Request $request, $idtienda)
    {
        return view(sistema_view().'/usuarioaccesomaster/create', [
            'tienda' => DB::table('tienda')->whereId($idtienda)->firstOrFail(),
            'tiendas' => DB::table('tienda')->where('idestado', 1)->orderBy('nombreagencia')->get(),
            'estadocivil' => DB::table('f_estadocivil')->get(),
        ]);
    }

    public function store(Request $request, $idtienda)
    {
        $request->validate([
            'view' => 'required|in:registrar',
            'idagencia' => ['required', 'integer', Rule::exists('tienda', 'id')->where('idestado', 1)],
            'usuario' => 'required|string|max:100|regex:/^[A-Za-z0-9_.]+$/|unique:users,usuario',
            'password' => ['bail', 'required', 'string', 'max:50', function ($attribute, $value, $fail) {
                if (strlen($value) > 72) {
                    $fail('La contrasena es demasiado larga.');
                }
            }],
            'idestadousuario' => 'required|integer|in:1,2',
            'apellido_parterno' => 'required|string|max:65',
            'apellido_marterno' => 'required|string|max:65',
            'nombres' => 'required|string|max:65',
            'identificacion' => 'required|string|max:15',
            'direccion' => 'required|string|max:255',
            'idubigeo' => 'required|integer|exists:ubigeo,id',
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'celular' => 'required|string|max:255',
            'idestadodivil' => 'required|integer|exists:f_estadocivil,id',
            'profesion' => 'required|string|max:255',
        ], [
            'idagencia.required' => 'Seleccione la agencia del Gerente General.',
            'idagencia.exists' => 'La agencia seleccionada no existe o esta desactivada.',
            'usuario.unique' => 'El usuario ya existe. Ingrese otro login.',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $agencia = DB::table('tienda')->whereId($request->idagencia)->lockForUpdate()->first();
                abort_unless($agencia && (int) $agencia->idestado === 1, 422, 'La agencia no esta activa.');
                // El cargo se decide en el servidor, nunca desde los accesos enviados por el navegador.
                $gerente = DB::table('permiso')->where('rango', 1)->orderBy('id')->first();
                if (! $gerente) {
                    return response()->json(['resultado' => 'ERROR', 'mensaje' => 'No esta configurado el cargo Gerente General.']);
                }
                $id = DB::table('users')->insertGetId([
                    'codigo' => '00', 'idtipopersona' => 1,
                    'nombre' => $request->nombres, 'apellidopaterno' => $request->apellido_parterno,
                    'apellidomaterno' => $request->apellido_marterno,
                    'nombrecompleto' => $request->apellido_parterno.' '.$request->apellido_marterno.', '.$request->nombres,
                    'razonsocial' => '', 'identificacion' => $request->identificacion,
                    'email' => '', 'imagen' => '', 'numerotelefono' => $request->celular,
                    'direccion' => $request->direccion, 'mapa_latitud' => '', 'mapa_longitud' => '',
                    'email_verified_at' => now(), 'usuario' => $request->usuario,
                    'clave' => $request->password, 'password' => Hash::make($request->password),
                    'idubigeo' => $request->idubigeo, 'iduserspadre' => 0, 'idtipousuario' => 1,
                    'idtienda' => $agencia->id, 'idestadousuario' => $request->idestadousuario,
                    'idestado' => 1, 'es_master' => false, 'profesion' => $request->profesion,
                    'nivelcredito' => '', 'ecaja' => '', 'idgenero' => 0,
                    'fechanacimiento' => $request->fecha_nacimiento,
                    'idestadocivil' => $request->idestadodivil, 'idnivelestudio' => 0,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                DB::table('users')->whereId($id)->update(['codigo' => 'U'.str_pad($id, 8, '0', STR_PAD_LEFT)]);
                DB::table('users_permiso')->insert([
                    'idusers' => $id, 'idtienda' => $agencia->id, 'idpermiso' => $gerente->id,
                    'idestado' => 1, 'idsession' => 2,
                ]);

                return response()->json(['resultado' => 'CORRECTO', 'mensaje' => 'Gerente General registrado correctamente.']);
            });
        } catch (UniqueConstraintViolationException $e) {
            return response()->json(['resultado' => 'ERROR', 'mensaje' => 'El usuario ya existe. Ingrese otro login.']);
        } catch (QueryException $e) {
            // Registrar solo el codigo: el SQL de insercion contiene credenciales.
            Log::error('No se pudo registrar el Gerente General.', ['sqlstate' => $e->getCode()]);

            return response()->json(['resultado' => 'ERROR', 'mensaje' => 'No se pudo registrar el usuario. No se guardaron cambios.']);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        abort_unless($id === 'show_table', 404);
        $query = DB::table('users')
            ->join('users_permiso', 'users_permiso.idusers', 'users.id')
            ->join('permiso', 'permiso.id', 'users_permiso.idpermiso')
            ->join('tienda', 'tienda.id', 'users_permiso.idtienda')
            ->where('permiso.rango', 1)->where('users.idtipousuario', 1)
            ->where('users.idestado', 1)->where('users.es_master', false);
        $total = (clone $query)->count();
        foreach (['users.codigo', 'users.identificacion', 'users.nombrecompleto', 'users.usuario',
            'tienda.nombre', 'tienda.nombreagencia', 'permiso.nombre'] as $columna => $campo) {
            $busqueda = $request->input('columns.'.$columna.'.search.value');
            if (is_string($busqueda) && $busqueda !== '') {
                $query->where($campo, 'like', '%'.$busqueda.'%');
            }
        }
        $estado = $request->input('columns.7.search.value');
        if (in_array((string) $estado, ['1', '2'], true)) {
            $query->where('users.idestadousuario', $estado);
        }
        $filtrados = (clone $query)->count();
        $filas = $query->select('users.id', 'users.codigo', 'users.identificacion', 'users.nombrecompleto',
            'users.usuario', 'users.idestadousuario', 'tienda.nombre as empresa',
            'tienda.nombreagencia as agencia', 'permiso.nombre as cargo')
            ->orderByDesc('users.id')->offset(max(0, (int) $request->input('start', 0)))
            ->limit(max(1, min(100, (int) $request->input('length', 10))))->get();

        return response()->json([
            'draw' => (int) $request->input('draw', 1), 'recordsTotal' => $total,
            'recordsFiltered' => $filtrados, 'data' => $filas->map(fn ($fila) => [
                'id' => $fila->id, 'codigo' => e($fila->codigo), 'identificacion' => e($fila->identificacion),
                'cliente' => e($fila->nombrecompleto), 'usuario' => e($fila->usuario),
                'empresa' => e($fila->empresa), 'agencia' => e($fila->agencia), 'cargo' => e($fila->cargo),
                'idestadousuario' => $fila->idestadousuario, 'opcion' => [],
            ]),
        ]);
    }

    public function edit(Request $request, $idtienda, $id)
    {
        abort(405);
    }

    public function update(Request $request, $idtienda, $id)
    {
        abort(405);
    }

    public function destroy(Request $request, $idtienda, $id)
    {
        abort(405);
    }
}
