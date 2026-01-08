<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Transporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class TransporteVentapasajeController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $where = [];
        $where[] = ['s_transporteventapasaje.numeroasiento','LIKE','%'.$request->input('numeroasiento').'%'];
        
        
        $s_transporteventapasaje = DB::table('s_transporteventapasaje')
            ->where('idtienda',$idtienda)
            ->where($where)
            ->select(
                's_transporteventapasaje.*'
            )
            ->orderBy('s_transporteventapasaje.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/transporte/transporteventapasaje/index',[
            'tienda' => $tienda,
            's_transporteventapasaje' => $s_transporteventapasaje
        ]);
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $tipopersonas = DB::table('tipopersona')->get();
        $s_transporterutas = DB::table('s_transporteruta')
            ->where('idtienda',$idtienda)
            ->orderBy('s_transporteruta.id','desc')
            ->get();
      
        return view('layouts/backoffice/tienda/sistema/transporte/transporteventapasaje/create',[
            'tienda' => $tienda,
            'tipopersonas' => $tipopersonas,
            'transporterutas' => $s_transporterutas
        ]);
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            $rules = [];
            $numerotelefono = '';
            $apellidopaterno = '';
            $apellidomaterno = '';
            if ($request->input('idtipopersona') == 1) {
                $rules = [
                    'dni' => 'required|numeric|digits:8',
                ];
                $identificacion = $request->input('dni');
            }
            elseif ($request->input('idtipopersona') == 2) {
                $rules = [
                    'ruc' => 'required|numeric|digits:11',
                    'idubigeo' => 'required',
                    'direccion' => 'required',
                ];
                $identificacion = $request->input('ruc');
            }
            elseif ($request->input('idtipopersona') == 3) {
                $rules = [
                    'carnetextranjeria' => 'required',
                ];
                $identificacion = $request->input('carnetextranjeria');
            }
            $rules = array_merge($rules,[
                'numeroasiento' => 'required',
                'idorigen' => 'required',
                'iddestino' => 'required',
                'idcomprobante' => 'required',
                'precio' => 'required',
            ]);
            $messages = [
                'dni.required' => 'El "DNI" es Obligatorio.',
                'ruc.required' => 'El "RUC" es Obligatorio.',
                'idubigeo.required' => 'El "Ubicación (Ubigeo)" es Obligatorio.',
                'direccion.required' => 'La "Dirección" es Obligatorio.',
                'carnetextranjeria.required' => 'El "Carnet Extranjería" es Obligatorio.',
                'numeroasiento.required' => 'El "Número de Asiento" es Obligatorio.',
                'idorigen.required' => 'El "Origen" es Obligatorio.',
                'iddestino.required' => 'El "Destino" es Obligatorio.',
                'idcomprobante.required' => 'El "Comprobante" es Obligatorio.',
                'precio.required' => 'El "Precio" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('s_transporteventapasaje')->insert([
                'fecharegistro' => Carbon::now(),
                'nombre' => $request->input('nombre'),
                'idtienda' => $request->input('idtienda'),
                'idestado' => 1,
            ]);
            return response()->json([
  							'resultado' => 'CORRECTO',
  							'mensaje'   => 'Se ha registrado correctamente.'
  					]);
        }
    }

    public function show(Request $request, $idtienda)
    {
       $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    public function edit(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $s_transporteventapasaje = DB::table('s_transporteventapasaje')->whereId($idmarca)->first();
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'editar') {
          
          return view('layouts/backoffice/tienda/sistema/transporte/transporteventapasaje/edit',[
            's_transporteventapasaje' => $s_transporteventapasaje,
            'tienda' => $tienda
          ]);
          
        }elseif($request->input('view') == 'eliminar') {
          
          return view('layouts/backoffice/tienda/sistema/transporte/transporteventapasaje/delete',[
            's_transporteventapasaje' => $s_transporteventapasaje,
            'tienda' => $tienda
          ]);
          
        }
    }

    public function update(Request $request, $idtienda, $s_idmarca)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      
      if($request->input('view') == 'editar') {
        $rules = [
          'nombre' => 'required'
        ];
        $messages = [
          'nombre.required' => 'El "Nombre" es Obligatorio.'
        ];
        $this->validate($request,$rules,$messages);

        $s_transporteventapasaje = DB::table('s_transporteventapasaje')->whereId($s_idmarca)->first();
        
        DB::table('s_transporteventapasaje')->whereId($s_idmarca)->update([
           'nombre' => $request->input('nombre')
        ]);
        return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha actualizado correctamente.'
        ]);
      }
    }

    public function destroy(Request $request, $idtienda, $s_idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            $s_transporteventapasaje = DB::table('s_transporteventapasaje')->whereId($s_idmarca)->first();
            uploadfile_eliminar($s_transporteventapasaje->imagen,'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            DB::table('s_transporteventapasaje')
                ->where('idtienda',$idtienda)
                ->where('id',$s_idmarca)
                ->delete();
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
