<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class BackupController extends Controller
{
    public function descargar()
    {
        $dbHost = config('database.connections.mysql.host');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbName = config('database.connections.mysql.database');

        $fecha = now()->format('Y-m-d_H-i-s');
        $archivo = "backup_{$dbName}_{$fecha}.sql";
        $ruta = storage_path("app/$archivo");

        // COMANDO MYSQLDUMP
        $comando = sprintf(
            'mysqldump -h%s -u%s -p%s %s > %s',
            $dbHost,
            $dbUser,
            $dbPass,
            $dbName,
            $ruta
        );
        exec($comando);

        /*$mysqldump = 'C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqldump.exe';
        $comando = "\"$mysqldump\" -h$dbHost -u$dbUser -p$dbPass $dbName > \"$ruta\"";
        exec($comando, $output);*/

        return Response::download($ruta)->deleteFileAfterSend(true);
    }
}
