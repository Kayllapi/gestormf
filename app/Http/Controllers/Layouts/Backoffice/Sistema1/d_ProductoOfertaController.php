<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class ProductoOfertaController extends Controller
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
        $where[] = ['s_productooferta.nombre','LIKE','%'.$request->input('nombre').'%'];
        
        
        $s_productooferta = DB::table('s_productooferta')
            ->where('idtienda',$idtienda)
            ->where($where)
            ->select(
                's_productooferta.*'
            )
            ->orderBy('s_productooferta.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/productooferta/index',[
            'tienda' => $tienda,
            's_productooferta' => $s_productooferta
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
        return view('layouts/backoffice/tienda/sistema/productooferta/create',[
            'tienda' => $tienda
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
                'nombre' => 'required'
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);

            $imagen = uploadfile('','',$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');
          
            DB::table('s_productooferta')->insert([
               'nombre' => $request->input('nombre'),
               'imagen' => $imagen,
               'idtienda' => $request->input('idtienda')
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
    public function edit(Request $request, $idtienda, $idproductooferta)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $s_productooferta = DB::table('s_productooferta')->whereId($idproductooferta)->first();
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'editar') {
          
          return view('layouts/backoffice/tienda/sistema/productooferta/edit',[
            's_productooferta' => $s_productooferta,
            'tienda' => $tienda
          ]);
          
        }elseif($request->input('view') == 'eliminar') {
          
          return view('layouts/backoffice/tienda/sistema/productooferta/delete',[
            's_productooferta' => $s_productooferta,
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
    public function update(Request $request, $idtienda, $s_idproductooferta)
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

        $s_productooferta = DB::table('s_productooferta')->whereId($s_idproductooferta)->first();
        $imagen = uploadfile($s_productooferta->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');
        
        DB::table('s_productooferta')->whereId($s_idproductooferta)->update([
           'nombre' => $request->input('nombre'),
           'imagen' => $imagen
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
    public function destroy(Request $request, $idtienda, $s_idproductooferta)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            $s_productooferta = DB::table('s_productooferta')->whereId($s_idproductooferta)->first();
            uploadfile_eliminar($s_productooferta->imagen,'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            DB::table('s_productooferta')
                ->where('idtienda',$idtienda)
                ->where('id',$s_idproductooferta)
                ->delete();
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
