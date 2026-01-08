@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Solicitar Ampliación</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoampliacion') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div id="carga-ampliacion">
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
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamoampliacion/'+e.currentTarget.value+'/edit?view=ampliacion',result:'#cont-clientecredito'});
    });
</script>
@endsection