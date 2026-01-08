<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class CategoriaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $where = [];
        $where[] = ['s_categoria.nombre','LIKE','%'.$request->input('nombre').'%'];
        
        $s_categorias = DB::table('s_categoria')
            ->where('idtienda',$idtienda)
            ->where($where)
            ->where('s_idcategoria',0)
            ->select(
                's_categoria.*'
            )
            ->orderBy('s_categoria.id','desc')
            ->paginate(10);
        return view('layouts/backoffice/tienda/sistema/categoria/index',[
            'tienda' => $tienda,
            's_categorias' => $s_categorias
        ]);
    }

    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/sistema/categoria/create',[
            'tienda' => $tienda
        ]);
    }

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
          
            DB::table('s_categoria')->insert([
                'fecharegistro' => Carbon::now(),
                'orden' => 0,
                'nombre' => $request->input('nombre'),
                'imagen' => $imagen,
                's_idcategoria' => 0,
                'idtienda' => $idtienda,
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

    public function edit(Request $request, $idtienda, $idcategoria)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $s_categoria = DB::table('s_categoria')->whereId($idcategoria)->first();
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'editar') {
            return view('layouts/backoffice/tienda/sistema/categoria/edit',[
                's_categoria' => $s_categoria,
                'tienda' => $tienda
            ]);
          
        }elseif($request->input('view') == 'eliminar') {
            return view('layouts/backoffice/tienda/sistema/categoria/delete',[
                's_categoria' => $s_categoria,
                'tienda' => $tienda
            ]);
        }elseif($request->input('view') == 'indexsubcategoria'){
            $s_categorias = DB::table('s_categoria')
                ->where('idtienda',$idtienda)
                ->where('s_categoria.nombre','LIKE','%'.$request->input('nombre').'%')
                ->where('s_idcategoria',$idcategoria)
                ->select(
                    's_categoria.*'
                )
                ->orderBy('s_categoria.id','desc')
                ->paginate(10);
            return view('layouts/backoffice/tienda/sistema/categoria/indexsubcategoria',[
                'tienda' => $tienda,
                's_categorias' => $s_categorias,
                's_categoria' => $s_categoria
            ]);
        }elseif($request->input('view') == 'registrarsubcategoria') {
            return view('layouts/backoffice/tienda/sistema/categoria/registrarsubcategoria',[
                's_categoria' => $s_categoria,
                'tienda' => $tienda
            ]);
        }elseif($request->input('view') == 'editarsubcategoria') {
            $s_categoria_1 = DB::table('s_categoria')->whereId($request->input('idcategoria_1'))->first();
            return view('layouts/backoffice/tienda/sistema/categoria/editarsubcategoria',[
                's_categoria' => $s_categoria,
                'tienda' => $tienda,
                's_categoria_1' => $s_categoria_1
            ]);
          
        }elseif($request->input('view') == 'eliminarsubcategoria') {
            $s_categoria_1 = DB::table('s_categoria')->whereId($request->input('idcategoria_1'))->first();
            return view('layouts/backoffice/tienda/sistema/categoria/eliminarsubcategoria',[
                's_categoria' => $s_categoria,
                'tienda' => $tienda,
                's_categoria_1' => $s_categoria_1
            ]);
        }elseif($request->input('view') == 'indexsubsubcategoria'){
            $s_categorias = DB::table('s_categoria')
                ->where('idtienda',$idtienda)
                ->where('s_categoria.nombre','LIKE','%'.$request->input('nombre').'%')
                ->where('s_idcategoria',$idcategoria)
                ->select(
                    's_categoria.*'
                )
                ->orderBy('s_categoria.id','desc')
                ->paginate(10);
            $s_categoria_1 = DB::table('s_categoria')->whereId($request->input('idcategoria_1'))->first();
            return view('layouts/backoffice/tienda/sistema/categoria/indexsubsubcategoria',[
                'tienda' => $tienda,
                's_categorias' => $s_categorias,
                's_categoria' => $s_categoria,
                's_categoria_1' => $s_categoria_1
            ]);
        }elseif($request->input('view') == 'registrarsubsubcategoria') {
            $s_categoria_1 = DB::table('s_categoria')->whereId($request->input('idcategoria_1'))->first();
            return view('layouts/backoffice/tienda/sistema/categoria/registrarsubsubcategoria',[
                's_categoria' => $s_categoria,
                'tienda' => $tienda,
                's_categoria_1' => $s_categoria_1
            ]);
        }elseif($request->input('view') == 'editarsubsubcategoria') {
            $s_categoria_1 = DB::table('s_categoria')->whereId($request->input('idcategoria_1'))->first();
            $s_categoria_2 = DB::table('s_categoria')->whereId($request->input('idcategoria_2'))->first();
            return view('layouts/backoffice/tienda/sistema/categoria/editarsubsubcategoria',[
                's_categoria' => $s_categoria,
                'tienda' => $tienda,
                's_categoria_1' => $s_categoria_1,
                's_categoria_2' => $s_categoria_2
            ]);
          
        }elseif($request->input('view') == 'eliminarsubsubcategoria') {
            $s_categoria_1 = DB::table('s_categoria')->whereId($request->input('idcategoria_1'))->first();
            $s_categoria_2 = DB::table('s_categoria')->whereId($request->input('idcategoria_2'))->first();
            return view('layouts/backoffice/tienda/sistema/categoria/eliminarsubsubcategoria',[
                's_categoria' => $s_categoria,
                'tienda' => $tienda,
                's_categoria_1' => $s_categoria_1,
                's_categoria_2' => $s_categoria_2
            ]);
        }
    }

    public function update(Request $request, $idtienda, $s_idcategoria)
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
            $s_categoria = DB::table('s_categoria')->whereId($s_idcategoria)->first();
          
            //imagen
            /*$image = $request->file('imagen');
            $image_name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/backoffice/tienda/'.$idtienda.'/sistema/');
            $resize_image = Image::make($image->getRealPath());
            $resize_image->resize(900, 900, function($constraint){
                $constraint->aspectRatio();
            })->save($destinationPath . '/' . $image_name);*/
            /*$destinationPath = public_path('/images');
            $image->move($destinationPath, $image_name);*/

            // fin imagen
          
            $imagen = uploadfile($s_categoria->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');
          
            DB::table('s_categoria')->whereId($s_idcategoria)->update([
               'nombre' => $request->input('nombre'),
               'imagen' => $imagen
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'registrarsubcategoria') {
            $rules = [
                'nombre' => 'required'
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
            $imagen = uploadfile('','',$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            DB::table('s_categoria')->insert([
                'fecharegistro' => Carbon::now(),
                'orden' => 0,
                'nombre' => $request->input('nombre'),
                'imagen' => $imagen,
                's_idcategoria' => $s_idcategoria,
                'idtienda' => $idtienda,
                'idestado' => 1,
            ]);
            return response()->json([
			    'resultado' => 'CORRECTO',
			    'mensaje'   => 'Se ha registrado correctamente.'
	        ]);
        }
        elseif($request->input('view') == 'editarsubcategoria') {
            $rules = [
              'nombre' => 'required'
            ];
            $messages = [
              'nombre.required' => 'El "Nombre" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
            $s_categoria = DB::table('s_categoria')->whereId($s_idcategoria)->first();
            $imagen = uploadfile($s_categoria->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            DB::table('s_categoria')->whereId($s_idcategoria)->update([
               'nombre' => $request->input('nombre'),
               'imagen' => $imagen
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'registrarsubsubcategoria') {
            $rules = [
                'nombre' => 'required'
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
            $imagen = uploadfile('','',$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            DB::table('s_categoria')->insert([
                'fecharegistro' => Carbon::now(),
                'orden' => 0,
                'nombre' => $request->input('nombre'),
                'imagen' => $imagen,
                's_idcategoria' => $s_idcategoria,
                'idtienda' => $idtienda,
                'idestado' => 1,
            ]);
            return response()->json([
			    'resultado' => 'CORRECTO',
			    'mensaje'   => 'Se ha registrado correctamente.'
	        ]);
        }
        elseif($request->input('view') == 'editarsubsubcategoria') {
            $rules = [
              'nombre' => 'required'
            ];
            $messages = [
              'nombre.required' => 'El "Nombre" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
            $s_categoria = DB::table('s_categoria')->whereId($s_idcategoria)->first();
            $imagen = uploadfile($s_categoria->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            DB::table('s_categoria')->whereId($s_idcategoria)->update([
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
    public function destroy(Request $request, $idtienda, $s_idcategoria)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            $countcategorias_1 = DB::table('s_categoria')
                ->where('idtienda',$idtienda)
                ->where('s_idcategoria',$s_idcategoria)
                ->count();
            if($countcategorias_1>0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Hay Sub categorias, no se puede eliminar.'
                ]);
            }
            $s_categoria = DB::table('s_categoria')->whereId($s_idcategoria)->first();
            uploadfile_eliminar($s_categoria->imagen,'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            DB::table('s_categoria')
                ->where('idtienda',$idtienda)
                ->where('id',$s_idcategoria)
                ->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }elseif($request->input('view') == 'eliminarsubcategoria') {
            $countcategorias_1 = DB::table('s_categoria')
                ->where('idtienda',$idtienda)
                ->where('s_idcategoria',$s_idcategoria)
                ->count();
            if($countcategorias_1>0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Hay Sub categorias, no se puede eliminar.'
                ]);
            }
            $s_categoria = DB::table('s_categoria')->whereId($s_idcategoria)->first();
            uploadfile_eliminar($s_categoria->imagen,'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            DB::table('s_categoria')
                ->where('idtienda',$idtienda)
                ->where('id',$s_idcategoria)
                ->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }elseif($request->input('view') == 'eliminarsubsubcategoria') {
            $s_categoria = DB::table('s_categoria')->whereId($s_idcategoria)->first();
            uploadfile_eliminar($s_categoria->imagen,'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            DB::table('s_categoria')
                ->where('idtienda',$idtienda)
                ->where('id',$s_idcategoria)
                ->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
