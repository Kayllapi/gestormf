@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle de Entretenimiento</span>
      <a class="btn btn-success" href="{{ url('backoffice/cineusuario') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div id="carga-activarentretenimiento">
    <div class="profile-edit-container">
        <div class="custom-form">
                      <img src="{{url('/public/backoffice/consumidor/vouchercine/'.$userscine->vaucher)}}" style="
                          border-radius: 5px;
                          max-height: 500px;
                          max-width: 500px;
                          margin-bottom: 5px;
                          background-color: #c7c9cb;  
                          margin-bottom:5px;                                                                                          
                      ">
        </div>
    </div>
</div>
@endsection