<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class CopiaseguridadController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        if(request('view') == 'tabla'){
            return view(sistema_view().'/copiaseguridad/tabla', compact(
                'tienda',
            ));
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
