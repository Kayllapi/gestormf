<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class EcommercePortadaController extends Controller
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
        $where[] = ['s_ecommerceportada.nombre','LIKE','%'.$request->input('nombre').'%'];
      
        $s_ecommerceportada = DB::table('s_ecommerceportada')
            ->where('idtienda',$idtienda)
            ->where($where)
            ->select(
                's_ecommerceportada.*'
            )
            ->orderBy('s_ecommerceportada.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/ecommerceportada/index',[
            'tienda' => $tienda,
            's_ecommerceportada' => $s_ecommerceportada
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
        return view('layouts/backoffice/tienda/sistema/ecommerceportada/create',[
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
                'titulo' => 'required',
                'descripcion' => 'required',
                'idestado' => 'required'
            ];
            $messages = [
                'titulo.required' => 'El "Título" es Obligatorio.',
                'descripcion.required' => 'La "Descripción" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);

            $imagen = uploadfile('','',$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/ecommerceportada/');
            
            DB::table('s_ecommerceportada')->insert([
               'fecharegistro' => Carbon::now(),
               'orden' => 0,
               'titulo' => $request->input('titulo'),
               'nombre' => $request->input('titulo'),
               'descripcion' => $request->input('descripcion'),
               'imagen' => $imagen,
               's_idproducto' => ($request->input('idproducto')==null||$request->input('idproducto')=='null')?0:$request->input('idproducto'),
               's_idestado' => $request->input('idestado'),
               'idusuarioresponsable' => Auth::user()->id,
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
    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($id=='showlistarproducto'){
            $s_productos = DB::table('s_producto')
                ->join('s_categoria as categoria1','categoria1.id','s_producto.s_idcategoria1')
                ->where('s_producto.idtienda',$idtienda)
                ->where('s_producto.nombre','LIKE','%'.$request->input('buscar').'%')
                ->where('categoria1.nombre','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  's_producto.id as id',
                   DB::raw('CONCAT(s_producto.nombre," / ",s_producto.precioalpublico) as text')
                )
                ->get();
            return $s_productos;
        }elseif($id=='showseleccionarproducto'){
            $datosProducto = DB::table('s_producto')
                ->where('s_producto.id',$request->input('idproducto'))
                ->orWhere('s_producto.codigo',$request->input('codigoproducto'))
                ->first();
            return [ 
              'producto' => $datosProducto,
              'stock' => stock_producto($datosProducto->id)['stock']
            ];
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $s_ecommerceportada = DB::table('s_ecommerceportada')
            ->leftJoin('s_producto','s_producto.id','s_ecommerceportada.s_idproducto')
            ->where('s_ecommerceportada.id',$idmarca)
            ->select(
                's_ecommerceportada.*',
                's_producto.nombre as productonombre'
            )
            ->first();
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'editar') {
          
          return view('layouts/backoffice/tienda/sistema/ecommerceportada/edit',[
            's_ecommerceportada' => $s_ecommerceportada,
            'tienda' => $tienda
          ]);
          
        }elseif($request->input('view') == 'eliminar') {
          
          return view('layouts/backoffice/tienda/sistema/ecommerceportada/delete',[
            's_ecommerceportada' => $s_ecommerceportada,
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
    public function update(Request $request, $idtienda, $s_idecommerceportada)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'editar') {
            $rules = [
                'titulo' => 'required',
                'descripcion' => 'required',
                'idestado' => 'required'
            ];
            $messages = [
                'titulo.required' => 'El "Título" es Obligatorio.',
                'descripcion.required' => 'La "Descripción" es Obligatorio.',
                'idestado.required' => 'El "Estado" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);

            $s_ecommerceportada = DB::table('s_ecommerceportada')->whereId($s_idecommerceportada)->first();
            $imagen = uploadfile($s_ecommerceportada->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/ecommerceportada/');

            DB::table('s_ecommerceportada')->whereId($s_idecommerceportada)->update([
               'titulo' => $request->input('titulo'),
               'nombre' => $request->input('titulo'),
               'descripcion' => $request->input('descripcion'),
               'imagen' => $imagen,
               's_idproducto' => ($request->input('idproducto')==null||$request->input('idproducto')=='null')?0:$request->input('idproducto'),
               's_idestado' => $request->input('idestado')
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
    public function destroy(Request $request, $idtienda, $s_idecommerceportada)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            $s_ecommerceportada = DB::table('s_ecommerceportada')->whereId($s_idecommerceportada)->first();
            uploadfile_eliminar($s_ecommerceportada->imagen,'/public/backoffice/tienda/'.$idtienda.'/ecommerceportada/');
            DB::table('s_ecommerceportada')
                ->where('idtienda',$idtienda)
                ->where('id',$s_idecommerceportada)
                ->delete();
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
