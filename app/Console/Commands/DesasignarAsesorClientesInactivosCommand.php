<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DesasignarAsesorClientesInactivosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clientes:desasignar-asesor
                            {--dias=90 : Dias sin un credito para considerar inactivo al cliente}
                            {--incluir-sin-credito : Tambien desasigna a los clientes que nuncaSolicitaron un credito}
                            {--tienda= : Limitar a una tienda especifica}
                            {--dry-run : Solo muestra que clientes se desasignarian sin actualizar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Desasigna (users.idasesor = 0) a los clientes que no tienen un credito generado en los ultimos N dias';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dias = (int) $this->option('dias');
        $incluir_sin_credito = (bool) $this->option('incluir-sin-credito');
        $idtienda = $this->option('tienda');
        $dry_run = (bool) $this->option('dry-run');

        $fecha_limite = Carbon::now()->subDays($dias);

        $this->info("Desasignando clientes sin credito desde el {$fecha_limite->format('d/m/Y H:i:s')}".($dry_run?' (DRY RUN)':''));

        $total = 0;

        $this->consultar($fecha_limite, $incluir_sin_credito, $idtienda)
            ->chunkById(500, function($usuarios) use (&$total, $dry_run) {

                $ids = $usuarios->pluck('id')->toArray();
                if(empty($ids)){
                    return true;
                }

                if(!$dry_run){
                    DB::table('users')
                        ->whereIn('users.id',$ids)
                        ->where('users.idasesor','>',0)
                        ->update(['users.idasesor' => 0]);
                }

                foreach($usuarios as $value){
                    $total++;
                    $fecha_referencia = $value->ultimacredito!=null?$value->ultimacredito:$value->fechamodificacion;
                    $dias_ = $fecha_referencia?(int)Carbon::parse($fecha_referencia)->diffInDays(Carbon::now()):0;
                    $this->line(sprintf(
                        '%-8s %-12s %-40s asesor %-5s ultimo credito: %s (%s dias)',
                        $value->id,
                        $value->codigo,
                        substr($value->nombrecompleto,0,40),
                        $value->idasesor,
                        $value->ultimacredito?date_format(date_create($value->ultimacredito),'d/m/Y'):'sin creditos',
                        $dias_,
                    ));
                }

                return true;
            }, 'users.id', 'id');

        $this->info("Total de clientes ".($dry_run?'a desasignar':'desasignados').": {$total}");

        return 0;
    }

    /**
     * Clientes activos que ya tienen un asesor asignado y cuyo ultimo credito es anterior a la fecha limite.
     *
     * @param  \Carbon\Carbon  $fecha_limite
     * @param  bool  $incluir_sin_credito
     * @param  $idtienda
     * @return \Illuminate\Database\Query\Builder
     */
    private function consultar($fecha_limite, $incluir_sin_credito, $idtienda)
    {
        $ultimo_credito = '(select max(credito.fecha) from credito
                            where (credito.idcliente = users.id or credito.idaval = users.id)
                            and credito.estado != "ELIMINADO")';

        $query = DB::table('users')
            ->where('users.idestado',1)
            ->where('users.idtipousuario',2)
            ->where('users.idasesor','>',0)
            ->select(
                'users.id',
                'users.codigo',
                'users.identificacion',
                'users.nombrecompleto',
                'users.idtienda',
                'users.idasesor',
                'users.created_at',
                'users.fechamodificacion',
                DB::raw($ultimo_credito.' as ultimacredito'),
            );

        // created_at esta vacio en la mayoria de registros, se usa fechamodificacion
        $fecha_registro = 'ifnull(users.created_at, users.fechamodificacion)';

        if($incluir_sin_credito){
            // ultimo credito anterior a la fecha limite o nunca tuvo credito y esta registrado hace mas de N dias
            $query->whereRaw('('.$ultimo_credito.' is null and '.$fecha_registro.' < ?)',[$fecha_limite]);
            $query->orWhereRaw('('.$ultimo_credito.' is not null and '.$ultimo_credito.' < ?)',[$fecha_limite]);
        }
        else{
            $query->whereRaw($ultimo_credito.' is not null and '.$ultimo_credito.' < ?',[$fecha_limite]);
        }

        if($idtienda!='' && $idtienda!=null){
            $query->where('users.idtienda',$idtienda);
        }

        return $query->orderBy('users.id','asc');
    }
}