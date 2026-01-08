@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Refinanciación</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamorefinanciacion') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div id="carga-refinanciacion">
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
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamorefinanciacion/'+e.currentTarget.value+'/edit?view=refinanciacion',result:'#cont-clientecredito'});
    });
</script>
@endsection