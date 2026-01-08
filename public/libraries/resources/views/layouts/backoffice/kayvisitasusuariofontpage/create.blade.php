@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>GENERAR VISITAS PARA FONTPAGE</span>
      <a class="btn btn-success" href="{{ url('backoffice/kayvisitasusuariofontpage') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div style="margin-bottom: 10px;margin-top: 10px;float: left;">Link para Ganar Monedas KAY:</div>
<div style="border: 1px dashed #666;
            float: left;
            width: 100%;
            padding: 10px;">
    <div style="font-weight: bold;" id="confirm-link">https://kayllapi.com/buscador/tienda?user={{ Auth::user()->email }}</div>
</div>
<div style="margin-bottom: 10px;margin-top: 10px;float: left;">Link Amigable para Ganar Monedas KAY:</div>
<div style="border: 1px dashed #666;
            float: left;
            width: 100%;
            padding: 10px;">
    <div style="font-weight: bold;" id="confirm-link">
      
⬇Encuentra las mejores tiendas de productos y/o servicios Aquí⬇<br>
👉 {{ url('/') }}/buscador/tienda?user={{ Auth::user()->email }} 👈
 
</div>
@endsection