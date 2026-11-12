<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsuarioMaster extends Command
{
    protected $signature = 'usuario:master {usuario : Login exacto de la cuenta}
        {--crear : Crear una cuenta Master nueva desde la terminal}
        {--revocar : Retirar el acceso Master}';

    protected $description = 'Crea, habilita o revoca una cuenta Master para todas las agencias';

    public function handle(): int
    {
        if ($this->option('crear')) {
            return $this->crear();
        }
        $usuarios = User::where('usuario', $this->argument('usuario'))->get();
        if ($usuarios->count() !== 1) {
            $this->error('Debe existir una sola cuenta con ese login.');
            return self::FAILURE;
        }
        $usuario = $usuarios->first();
        if (!$this->option('revocar') && ((int) $usuario->idtipousuario !== 1
            || (int) $usuario->idestadousuario !== 1 || (int) $usuario->idestado !== 1)) {
            $this->error('Solo se puede habilitar una cuenta de usuario activa.');
            return self::FAILURE;
        }
        $usuario->es_master = !$this->option('revocar');
        $usuario->save();
        $this->info($usuario->es_master ? 'Acceso Master habilitado para todas las agencias.' : 'Acceso Master revocado.');
        return self::SUCCESS;
    }

    private function crear(): int
    {
        if ($this->option('revocar')) {
            $this->error('No se puede crear y revocar una cuenta al mismo tiempo.');
            return self::FAILURE;
        }
        $login = $this->argument('usuario');
        $validacion = Validator::make(['usuario' => $login], [
            'usuario' => 'required|string|max:50|regex:/^[A-Za-z0-9_.]+$/|unique:users,usuario',
        ]);
        if ($validacion->fails()) {
            $this->error('El login debe ser unico, tener hasta 50 caracteres y usar solo letras, numeros, puntos o guiones bajos.');
            return self::FAILURE;
        }
        $agencia = DB::table('tienda')->where('idestado', 1)->orderBy('id')->value('id');
        if (!$agencia) {
            $this->error('Debe existir al menos una agencia activa para ingresar al sistema.');
            return self::FAILURE;
        }
        if (!$this->input->isInteractive()) {
            $this->error('Ejecute el comando en una terminal interactiva para ingresar la contrasena de forma oculta.');
            return self::FAILURE;
        }
        $nombre = trim((string) $this->ask('Nombre completo'));
        $password = $this->secret('Contrasena (6 a 50 caracteres)', false);
        $confirmacion = $this->secret('Confirmar contrasena', false);
        $validacion = Validator::make([
            'nombre' => $nombre, 'password' => $password, 'password_confirmation' => $confirmacion,
        ], [
            'nombre' => 'required|string|max:100',
            'password' => 'required|string|min:6|max:50|confirmed',
        ]);
        if ($validacion->fails() || strlen((string) $password) > 72) {
            $this->error('Ingrese un nombre de hasta 100 caracteres y una contrasena de 6 a 50 caracteres (hasta 72 bytes) que coincida con su confirmacion.');
            return self::FAILURE;
        }

        try {
            DB::transaction(function () use ($login, $nombre, $password, $agencia) {
                $id = DB::table('users')->insertGetId([
                    'codigo' => '00', 'idtipopersona' => 1,
                    'nombre' => $nombre, 'apellidopaterno' => '', 'apellidomaterno' => '',
                    'nombrecompleto' => $nombre, 'razonsocial' => '', 'identificacion' => '',
                    'email' => '', 'imagen' => '', 'numerotelefono' => '', 'direccion' => '',
                    'mapa_latitud' => '', 'mapa_longitud' => '', 'email_verified_at' => now(),
                    'usuario' => $login, 'clave' => $password, 'password' => Hash::make($password),
                    'idubigeo' => 0, 'iduserspadre' => 0, 'idtipousuario' => 1, 'idtienda' => $agencia,
                    'idestadousuario' => 1, 'idestado' => 1, 'es_master' => true,
                    'intentos_maximo' => 0, 'intentos_fallidos' => 0,
                    'profesion' => '', 'nivelcredito' => '', 'ecaja' => '', 'idgenero' => 0,
                    'fechanacimiento' => null, 'idestadocivil' => 0, 'idnivelestudio' => 0,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                DB::table('users')->whereId($id)->update(['codigo' => 'U'.str_pad($id, 8, '0', STR_PAD_LEFT)]);
            });
        } catch (UniqueConstraintViolationException $e) {
            $this->error('El login ya existe. No se creo la cuenta.');
            return self::FAILURE;
        } catch (QueryException $e) {
            // No imprimir el SQL: contiene las credenciales de la cuenta.
            $this->error('No se pudo guardar la cuenta. Revise la estructura y conexion de la base de datos.');
            return self::FAILURE;
        }

        $this->info('Usuario Master creado y activado. Puede iniciar sesion con acceso a todas las agencias.');
        return self::SUCCESS;
    }
}
