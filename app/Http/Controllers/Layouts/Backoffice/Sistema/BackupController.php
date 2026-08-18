<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class BackupController extends Controller
{
    public function descargar()
    {
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbName = config('database.connections.mysql.database');

        $fecha = now()->format('Y-m-d_H-i-s');
        $rutaSql = storage_path("app/backup_{$dbName}_{$fecha}.sql");
        $rutaError = $rutaSql . '.err';

        // --no-tablespaces: evita que mysqldump exija el privilegio PROCESS (algunos usuarios de
        // BD en produccion no lo tienen); no afecta el respaldo de los datos, solo omite metadata
        // de tablespaces que no hace falta para restaurar.
        // NOTA (local/Windows): requiere que "mysqldump" este en el PATH del sistema.
        $comando = 'mysqldump --no-tablespaces'
            . ' -h' . escapeshellarg($dbHost)
            . ' -P' . escapeshellarg($dbPort)
            . ' -u' . escapeshellarg($dbUser)
            // Si no hay password configurado (comun en desarrollo local), se omite el flag -p por
            // completo: pasarlo con un valor vacio ("-p''") hace que mysqldump se quede esperando
            // la contraseña por entrada interactiva en vez de tratarlo como "sin contraseña".
            . ($dbPass !== '' ? ' -p' . escapeshellarg($dbPass) : '')
            . ' ' . escapeshellarg($dbName)
            . ' > ' . escapeshellarg($rutaSql)
            . ' 2> ' . escapeshellarg($rutaError);
        exec($comando, $salida, $codigoSalida);

        $errores = trim((string) @file_get_contents($rutaError));
        @unlink($rutaError);

        if ($codigoSalida !== 0 || !file_exists($rutaSql) || filesize($rutaSql) === 0) {
            @unlink($rutaSql);
            abort(500, 'No se pudo generar el respaldo de la base de datos.' . ($errores !== '' ? ' Detalle: ' . $errores : ''));
        }

        // Comprimido con la extension zlib de PHP (gzencode) en vez de canalizar a un binario
        // "gzip" externo: funciona igual en Windows (local) y Linux (produccion) sin depender de
        // que ese binario este disponible en el PATH.
        $rutaGz = $rutaSql . '.gz';
        file_put_contents($rutaGz, gzencode(file_get_contents($rutaSql), 9));
        unlink($rutaSql);

        return Response::download($rutaGz, "backup_{$dbName}_{$fecha}.sql.gz")->deleteFileAfterSend(true);
    }
}
