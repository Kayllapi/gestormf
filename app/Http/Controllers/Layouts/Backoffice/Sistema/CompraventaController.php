<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class CompraventaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);

        $agencias = DB::table('tienda')->get();

        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/compraventa/tabla', [
                'agencias' => $agencias,
            ]);
        }
    }

    public function create(Request $request,$idtienda)
    {
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
    } 

    public function destroy(Request $request, $idtienda, $id)
    {
    }
}
