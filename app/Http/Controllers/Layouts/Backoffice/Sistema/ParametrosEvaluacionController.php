<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class ParametrosEvaluacionController extends Controller
{
    /**
     * Parametros (s_config) que administra esta pantalla. A diferencia de Penalidades
     * y Comisiones, Giro Economico y Tasas Activas, esta configuracion es GLOBAL: un
     * unico valor por parametro para todas las agencias. Como s_config.idtienda no es
     * nullable y configuracion()/configuracion_update() (app/Helpers/FunctionsSistema.php)
     * se usan en muchos otros lugares con un idtienda real, "global" se logra guardando
     * el mismo valor en la fila de s_config de TODAS las agencias: asi cualquier lectura
     * configuracion($idtienda,...) en el resto del sistema devuelve siempre ese valor,
     * sin tener que tocar esos otros puntos de lectura.
     *
     * El capital asignado a cada agencia (tienda.capital_agencia, tabla de abajo en el
     * formulario) no es un parametro de este catalogo: sigue siendo propio de cada
     * agencia y no se ve afectado por este cambio.
     */
    const PARAMETROS = [
        'provision_gastos_familiares',
        'porcentaje_min_muestra',
        'reporte_institucional',
        'capital_asignado',
        'rango_menor',
        'rango_tope',
        'ciclo_negocio_maximo',
        'tope_vinculacion_riesgo',
        'relacion_couta_ingreso',
        'relacion_cuota_venta',
        'rango_diferencia',
        'rango_tope_dependiente',
        'entidades_maxima',
        'activar_tem',
        'activar_tcem',
    ];

    public function __construct()
    {
        $this->tipo_credito = DB::table('tipo_credito')->get();
    }
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        if($request->input('view') == 'tabla'){
            // Todas las agencias: se usan solo para la tabla de "Asignacion de Capital a
            // Agencia" del formulario, que si sigue siendo por agencia.
            $agencias = DB::table('tienda')
                ->orderBy('tienda.nombre','ASC')
                ->get();

            return view(sistema_view().'/parametrosevaluacion/tabla',[
              'tienda' => $tienda,
              'agencias' => $agencias,
            ]);
        }

    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->view == 'registrar') {
            return view(sistema_view().'/creditoordinario/create',[
                'tienda' => $tienda,
                'tipo_credito' => $this->tipo_credito
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {

    }

    public function show(Request $request, $idtienda, $id)
    {



    }

    public function edit(Request $request, $idtienda, $id)
    {
    }

    public function update(Request $request, $idtienda, $id)
    {
        if($request->input('view') == 'editar') {

            $capital = json_decode($request->input('capital'), true);
            foreach($capital as $value){
              DB::table('tienda')->whereId($value['id'])->update([
                'capital_agencia' => $value['capital']
              ]);
            }

            $activar_tem = $request->activar_tem ? 1 : 0;
            $activar_tcem = $request->activar_tcem ? 1 : 0;

            $valores = [
                'provision_gastos_familiares' => $request->provision_gastos_familiares,
                'porcentaje_min_muestra' => $request->porcentaje_min_muestra,
                'reporte_institucional' => $request->reporte_institucional,
                'capital_asignado' => $request->capital_asignado,
                'rango_menor' => $request->rango_menor,
                'rango_tope' => $request->rango_tope,
                'ciclo_negocio_maximo' => $request->ciclo_negocio_maximo,
                'tope_vinculacion_riesgo' => $request->tope_vinculacion_riesgo,
                'relacion_couta_ingreso' => $request->relacion_couta_ingreso,
                'relacion_cuota_venta' => $request->relacion_cuota_venta,
                'rango_diferencia' => $request->rango_diferencia,
                'rango_tope_dependiente' => $request->rango_tope_dependiente,
                'entidades_maxima' => $request->entidades_maxima,
                'activar_tem' => $activar_tem,
                'activar_tcem' => $activar_tcem,
            ];

            // Global: el mismo valor se guarda en TODAS las agencias (ver nota en PARAMETROS).
            $idtiendas = DB::table('tienda')->pluck('id');

            foreach($idtiendas as $idtienda_config){
                foreach($valores as $nombre => $valor){
                    configuracion_update($idtienda_config,$nombre,$valor);
                }
            }

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
