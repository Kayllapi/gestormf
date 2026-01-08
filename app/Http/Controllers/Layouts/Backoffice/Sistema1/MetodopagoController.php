<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class MetodopagoController extends Controller
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
      
        $where = [];
        $where[] = ['s_tipometodopago.nombre','LIKE','%'.$request->input('tipometodopagonombre').'%'];
        
        $s_metodopago = DB::table('s_metodopago')
            ->join('s_tipometodopago','s_tipometodopago.id','=','s_metodopago.s_idtipometodopago')
            ->where('idtienda',$idtienda)
            ->where($where)
            ->select(
                's_metodopago.*',
                's_tipometodopago.nombre as tipometodopagonombre'
            )
            ->orderBy('s_metodopago.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/metodopago/index',[
            'tienda' => $tienda,
            's_metodopago' => $s_metodopago
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
        $s_tipometodopagos = DB::table('s_tipometodopago')->get();
        return view('layouts/backoffice/tienda/sistema/metodopago/create',[
            'tienda' => $tienda,
            's_tipometodopagos' => $s_tipometodopagos
        ]);
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
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'idtipometodopago' => 'required',
                'key_public' => 'required',
                'key_private' => 'required'
            ];
            $messages = [
                'idtipometodopago.required' => 'El "Método de pago" es Obligatorio.',
                'key_public.required' => 'La "Key Público" es Obligatorio.',
                'key_private.required' => 'El "Key Privado" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('s_metodopago')->insert([
                'fecharegistro' => Carbon::now(),
                'key_public' => $request->input('key_public'),
                'key_private' => $request->input('key_private'),
                's_idtipometodopago' => $request->input('idtipometodopago'),
                's_idestado' => 2,
                'idtienda' => $idtienda
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
    public function show(Request $request, $idtienda)
    {
       $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $s_idmetodopago)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $s_metodopago = DB::table('s_metodopago')->whereId($s_idmetodopago)->first();
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'editar') {
            $s_tipometodopagos = DB::table('s_tipometodopago')->get();
            return view('layouts/backoffice/tienda/sistema/metodopago/edit',[
                's_tipometodopagos' => $s_tipometodopagos,
                's_metodopago' => $s_metodopago,
                'tienda' => $tienda
            ]);
          
        }elseif($request->input('view') == 'eliminar') {
            $s_tipometodopagos = DB::table('s_tipometodopago')->get();
            return view('layouts/backoffice/tienda/sistema/metodopago/delete',[
                's_tipometodopagos' => $s_tipometodopagos,
                's_metodopago' => $s_metodopago,
                'tienda' => $tienda
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
    public function update(Request $request, $idtienda, $s_idmetodopago)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'editar') {
            $rules = [
                'idtipometodopago' => 'required',
                'idestado' => 'required',
                'key_public' => 'required',
                'key_private' => 'required'
            ];
            $messages = [
                'idtipometodopago.required' => 'El "Método de pago" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.',
                'key_public.required' => 'La "Key Público" es Obligatorio.',
                'key_private.required' => 'El "Key Privado" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
            
            DB::table('s_metodopago')->whereId($s_idmetodopago)->update([
                'fecharegistro' => Carbon::now(),
                'key_public' => $request->input('key_public'),
                'key_private' => $request->input('key_private'),
                's_idtipometodopago' => $request->input('idtipometodopago'),
                's_idestado' => $request->input('idestado'),
                'idtienda' => $idtienda
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda, $s_idmetodopago)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            $s_metodopago = DB::table('s_metodopago')->whereId($s_idmetodopago)->first();
            uploadfile_eliminar($s_metodopago->imagen,'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            DB::table('s_metodopago')
                ->where('idtienda',$idtienda)
                ->where('id',$s_idmetodopago)
                ->delete();
            return response()->json([
				'resultado' => 'CORRECTO',
				'mensaje'   => 'Se ha eliminado correctamente.'
			]);
        }
    }
}
