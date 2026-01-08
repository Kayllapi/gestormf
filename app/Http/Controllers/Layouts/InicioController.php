<?php
namespace App\Http\Controllers\Layouts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;


class InicioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('layouts/inicio/index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        echo "cargando datos!!";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'subscribe-email' => 'required|unique:suscripciones,email',
        ];
        $messages = [
            'subscribe-email.required' => 'El "Correo Electronico" es Obligatorio.',
            'subscribe-email.unique' => 'Este "Correo Electronico" ya tiene una suscripción.',
        ];
        $this->validate($request,$rules,$messages);
      
        DB::table('suscripciones')->insert([
          'email' => $request->input('subscribe-email'),
          'fecharegistro' => Caron::now(),
          'estado' => 1
        ]);
       
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje' => 'Suscripción enviada.'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //sistema
        if($id == 'showlistarubigeo'){
            $ubigeos = DB::table('ubigeo')
                ->where('ubigeo.departamento','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('ubigeo.provincia','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('ubigeo.distrito','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('ubigeo.nombre','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('ubigeo.codigo','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'ubigeo.id as id',
                  'ubigeo.distrito as distrito',
                  'ubigeo.provincia as provincia',
                  'ubigeo.departamento as departamento',
                   DB::raw('CONCAT(ubigeo.codigo," - ",ubigeo.nombre) as text')
                )
                ->get();
            return $ubigeos;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
