<?php

use Illuminate\Support\Facades\Route;

Route::resource('/', 'Layouts\InicioController');
//Route::get('/login', 'Layouts\Buscador\Tienda\LoginController@index');

Route::resource('/inicio', 'Layouts\InicioController');

Auth::routes(); //Auth::routes(['verify' => true]);

Route::group(['middleware' => ['auth','verified']], function () {
    
    $modulos = Cache::remember('modulo', 1, function() {
        return DB::table('modulo')
            ->where('modulo.vista','<>','')
            ->where('modulo.controlador','<>','')
            ->where('modulo.idestado',1)
            ->get();
    });
  
    //BACKOFFICE
    Route::resource('/backoffice/inicio', 'Layouts\Backoffice\InicioController');
    Route::resource('/backoffice/perfil', 'Layouts\Backoffice\PerfilController');
    Route::resource('/backoffice/modulo', 'Layouts\Backoffice\ModuloController');
    Route::resource('/backoffice/permiso', 'Layouts\Backoffice\PermisoController');
  
        Route::resource('/backoffice/{idtienda}/inicio', 'Layouts\Backoffice\Sistema\MasterController');
        foreach($modulos as $value) {
            Route::resource($value->vista, $value->controlador);
        }
});
