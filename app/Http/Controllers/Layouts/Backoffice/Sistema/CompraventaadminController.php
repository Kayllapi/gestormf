<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class CompraventaadminController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $agencias = DB::table('tienda')->get();
        $apertura_caja = cvapertura($idtienda);
        $arqueocaja = cvarqueocaja($idtienda);

        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/compraventaadmin/tabla', compact('tienda','agencias','apertura_caja','arqueocaja'));
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
