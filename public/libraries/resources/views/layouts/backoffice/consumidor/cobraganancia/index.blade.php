@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>COBRO DE GANANCIAS</span>
      <a class="btn btn-warning" href="{{ url('backoffice/cobraganancia/create') }}"><i class="fa fa-angle-right"></i> Cobrar</a></a>
    </div>
</div>

@endsection