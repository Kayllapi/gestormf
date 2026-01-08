@extends('layouts.master')
@section('cuerpo')
<?php
/*$nompatrocinador = '';
$idpatrocinador = 0;
if(isset($_GET['user'])){
    $patrocinador = DB::table('users')
        ->where('usuario','<>','')
        ->where('usuario','<>','admin')
        ->where('usuario',$_GET['user'])
        ->first();
    if($patrocinador!=''){
        $idpatrocinador = $patrocinador->id;;
        $nompatrocinador = $patrocinador->nombre;
    }
}*/
?>
@include('app.consumidor.planes') 
<!--div class="mensaje-danger" style="margin-bottom: 0px;border-radius: 0px;">
    <i class="fa fa-exclamation-triangle"></i> No existe el patrocinador, por favor vuelva a solicitar el link de registro a su patrocinador.
</div-->
@endsection
