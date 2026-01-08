@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte para Infocorp</span>
    </div>
</div>
    <div class="row">
         <div class="col-md-6">
            <label>Listar por *</label>
            <select id="listarpor">
                <option></option>
                <option value="1">EQUIFAX</option>
                <option value="2">SENTINEL</option>
            </select>
         </div>
    </div>
    <div class="row">
         <div class="col-md-6">
          <a href="javascript:;" onclick="reporte('reporte')" class="btn mx-btn-post" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar Reporte</a>
         </div>
         <div class="col-md-6">
          <a href="javascript:;" onclick="reporte('excel')" class="btn mx-btn-post" style="margin-bottom:10px;"><i class="fa fa-check"></i> Exportar Excel</a>
         </div>
    </div>
<div id="iframe-tablapdf" style="overflow-x: scroll; height: 500px;"></div>
@endsection
@section('subscripts')
<script>
function reporte(tipo){
    if($('#listarpor').val()==''){
        $('#iframe-tablapdf').html('<div class="mensaje-danger">El campo "Listar por" es obligatorio.</div>');
        return false;
    }
  
    if(tipo=='reporte'){
      load('#iframe-tablapdf');
      $.ajax({
          url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reporte/reporteprestamoinfocorp/0/edit')}}",
          type:'GET',
          data: {
              view : tipo,
              listarpor : $('#listarpor').val()
          },
          success: function (respuesta){
              $("#iframe-tablapdf").html(respuesta);
          }
      })
    }else if(tipo=='excel'){
        window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/reporte/reporteprestamoinfocorp/0/edit')}}?view='+tipo+'&listarpor='+$('#listarpor').val();
    }
}
$("#listarpor").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
});
</script>
@endsection