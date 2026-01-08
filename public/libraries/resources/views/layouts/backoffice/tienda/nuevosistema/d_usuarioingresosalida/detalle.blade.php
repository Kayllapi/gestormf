@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle del Horario de Entrada y Salida</span>
        <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/usuarioingresosalida') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
 <div class="profile-edit-container">
    <div class="custom-form">
      <div class="row">
        <div class="col-md-12">
           <div class="col-md-3">
                 <label>Fecha Registro</label>
                <input type="text" value="{{ strtoupper($horario->fecharegistro) }}" disabled/> 
           </div>
           <div class="col-md-3">
                 <label>DNI</label>
                 <input type="text" value="{{ strtoupper($horario->identificacionusuario) }}" disabled/>
           </div>
           <div class="col-md-6">
                <label>Usuario</label>
                <input type="text" value="{{ strtoupper($horario->apellidosusuario) }},{{ strtoupper($horario->nombreusuario) }}" disabled/>
            </div>
        </div>
       </div>
    </div>
</div>    
@endsection