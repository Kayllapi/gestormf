<?php

namespace App\Http\Controllers\Layouts\Buscador\Tienda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF; 

class InformacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$linktienda)
    { 
        $tienda = tienda_link($linktienda);
        if($tienda==''){
            return redirect('/');
        }
        $tiendagalerias = DB::table('tiendagaleria')
            ->where('idtienda',$tienda->id)
            ->orderBy('fecharegistro','desc')
            ->get();
        $tiendavideos = DB::table('tiendavideo')
            ->where('idtienda', $tienda->id)
            ->get();
        $recomendaciones = DB::table('recomendacion')
            ->where('idtienda',$tienda->id)
            ->where('idtiporecomendacion',1)
            ->count();
        $s_categorias = DB::table('s_categoria')
            ->where('idtienda',$tienda->id)
            ->where('s_idcategoria',0)
            ->orderBy('s_categoria.nombre','asc')
            ->get();
        return view('layouts/buscador/tienda/informacion/index',[
            'tienda' => $tienda,
            'tiendagalerias' => $tiendagalerias,
            'tiendavideos' => $tiendavideos,
            'recomendaciones' => $recomendaciones,
            's_categorias' => $s_categorias,
        ]);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request,$linktienda)
    {
        //  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$linktienda)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$linktienda, $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $linktienda, $id)
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
