<?php

namespace App\Http\Controllers\Layouts\Buscador\Tienda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF; 

class ComentarioController extends Controller
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
      
        $tiendacomentarios = DB::table('tiendacomentario')
            ->join('users','users.id','=','tiendacomentario.idusers')
            ->where('tiendacomentario.idtienda',$tienda->id)
            ->select(
                'tiendacomentario.*',
                'users.nombre as usersnombre',
                'users.apellidos as usersapellidos',
                'users.apellidos as usersapellidos',
                'users.imagen as usersimagen'
            )
            ->orderBy('tiendacomentario.fechaaprobacion','desc')
            ->get();
        return view('layouts/buscador/tienda/comentario/index',[
            'tienda' => $tienda,
            'tiendacomentarios' => $tiendacomentarios,
        ]);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request,$linktienda,$data0,$data1=0,$data2=0)
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
