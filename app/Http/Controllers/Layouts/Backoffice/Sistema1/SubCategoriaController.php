<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class SubCategoriaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $where = [];
        $where[] = ['s_categoria.nombre','LIKE','%'.$request->input('nombre').'%']; 
  
      
       $s_categorias = DB::table('s_categoria as subcategoria')
              ->leftJoin('s_categoria','s_categoria.id','=','subcategoria.s_idcategoria')
              ->where('subcategoria.idtienda',$idtienda)
              ->where($where)
              ->where('subcategoria.s_idcategoria','<>',0)
               ->whereIn('subcategoria.idestado',[1,2])
              ->select(
                  'subcategoria.*',
                  's_categoria.nombre as categoria',
              )
              ->orderBy('s_categoria.id','desc')
              ->paginate(10);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/sistema/subcategoria/index',
                    compact('tienda','s_categorias','idtienda'));
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $categorias = DB::table('s_categoria')
            ->where('s_categoria.idtienda',$idtienda)
            ->where('s_categoria.idestado',1)
            ->where('s_categoria.s_idcategoria',0)
            ->orderBy('s_categoria.id','desc')
            ->get();
        return view('layouts/backoffice/tienda/sistema/subcategoria/create',[
            'tienda' => $tienda,
            'categorias' => $categorias
        ]);
    }
  
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {

            $rules = [
                'idcategoria' => 'required',
                'nombre' => 'required'
            ];
            $messages = [
                'idcategoria.required' => 'La "Categoria" es Obligatorio.',
                'nombre.required' => 'El "Nombre" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
           
            $imagen = uploadfile('','',$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');
          
            DB::table('s_categoria')->insert([
                'fecharegistro' => Carbon::now(),
                'orden' => 0,
                'nombre' => $request->input('nombre'),
                'imagen' => $imagen,
                's_idcategoria' => $request->input('idcategoria'),
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if ($id == 'show-moduloactualizar'){
        }
    }

    public function edit(Request $request, $idtienda, $idcategoria)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $s_categoria = DB::table('s_categoria as subcateoria')
            ->leftJoin('s_categoria as categoria','categoria.id','subcateoria.s_idcategoria')
            ->where('subcateoria.id',$idcategoria)
            ->select(
                'subcateoria.*',
                'categoria.nombre as categorianombre'
            )
            ->first();
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view') == 'editar') {
            $categorias = DB::table('s_categoria')
                ->where('s_categoria.idtienda',$idtienda)
                ->whereIn('s_categoria.idestado',[1,2])
                ->where('s_categoria.s_idcategoria',0)
                ->orderBy('s_categoria.id','desc')
                ->get();
            return view('layouts/backoffice/tienda/sistema/subcategoria/edit',[
                's_categoria' => $s_categoria,
                'categorias' => $categorias,
                'tienda' => $tienda
            ]);
          
        }
        elseif($request->input('view') == 'detalle') {
          return view('layouts/backoffice/tienda/sistema/subcategoria/detalle',[
            's_categoria' => $s_categoria,
            'tienda' => $tienda
          ]);
          
        }
        elseif($request->input('view') == 'eliminar') {
            return view('layouts/backoffice/tienda/sistema/subcategoria/delete',[
                's_categoria' => $s_categoria,
                'tienda' => $tienda
            ]);
        }
    }

    public function update(Request $request, $idtienda, $s_idcategoria)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);

        if($request->input('view') == 'editar') {
            $rules = [
                'idcategoria' => 'required',
                'nombre' => 'required',
                'idestado' => 'required'
            ];
            $messages = [
                'idcategoria.required' => 'La "Categoria" es Obligatorio.',
                'nombre.required' => 'El "Nombre" es Obligatorio.',
              'idestado.required' => 'El "Estado" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
            $s_categoria = DB::table('s_categoria')->whereId($s_idcategoria)->first();
          
            $imagen = uploadfile($s_categoria->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');
       
           DB::table('s_categoria')->whereId($s_idcategoria)->update([
               'nombre' => $request->input('nombre'),
               'imagen' => $imagen,               
               'idestado'  =>$request->input('idestado'),
               's_idcategoria' => $request->input('idcategoria'),
            ]);
            $tienda = DB::table('s_categoria')->whereId($s_idcategoria)->first();
          dd($tienda);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
      
    }

    public function destroy(Request $request, $idtienda, $s_idcategoria)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {

            DB::table('s_categoria')
                ->where('id',$s_idcategoria)
                ->where('idtienda',$idtienda)
                ->update([
                  'fechaeliminado' => Carbon::now(),
                  'idestado'=> 3
                ]);
          
           
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
