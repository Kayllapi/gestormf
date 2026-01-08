@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Solicitar Descuento de Mora</span>
      <a class="btn btn-success" href="{{ redirect()->getUrlGenerator()->previous() }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div id="carga-mora">
  <div class="row">
      <div class="col-sm-12">
          <label>Crédito del Cliente</label>
          <select id="idcliente">
            <option></option>
          </select>
      </div>
      <div id="cont-clientecredito"></div>
  </div>
</div>
@endsection
@section('subscripts')
<script>
    $('#idcliente').select2({
        @include('app.prestamo_select2_creditocliente',['idasesor' => Auth::user()->id])
    }).on("change", function(e) {
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamomorasolicitud/create?view=mora&idcredito='+e.currentTarget.value,result:'#cont-clientecredito'});
    });
</script>
@endsection