<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class CargoAsesorController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view') == 'tabla'){
            $permiso = user_permiso();
            abort_unless($permiso, 403, 'No tiene un cargo asignado.');
            return view(sistema_view().'/cargoasesor/tabla',[
              'tienda' => $tienda,
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
