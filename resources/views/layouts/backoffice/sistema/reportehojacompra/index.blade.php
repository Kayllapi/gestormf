@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Hoja de Compra</span>
    </div>
</div>
    <div class="custom-form">
      <div class="row">
         <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                    <label>Fecha inicio</label>
                    <input type="date" id="fechainicio">
                    <label>Fecha fin</label>
                    <input type="date" id="fechafin">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <a href="javascript:;" onclick="tabla_pdf()" class="btn  big-btn  color-bg flat-btn" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
                </div>
            </div>
          </div>
         <div class="col-md-8">
           <div id="cont-iframe-hojacompra"></div>
         </div>
      </div>
    </div>
@endsection
@section('subscripts')
<script>
     function tabla_pdf(){
        var fechainicio = $('#fechainicio').val();
        var fechafin = $('#fechafin').val();
        if(fechainicio==''){
            $('#cont-iframe-hojacompra').html('<div class="mensaje-danger">La Fecha Inicio es Obligatorio!!</div>');
            return false;
        }
        if(fechafin==''){
            $('#cont-iframe-hojacompra').html('<div class="mensaje-danger">La Fecha Fin es Obligatorio!!</div>');
            return false;
        }
        $('#cont-iframe-hojacompra').html('<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reportehojacompra/0/edit') }}?view=tablapdf&fechainicio='+fechainicio+'&fechafin='+fechafin+'#zoom=130" frameborder="0" width="100%" height="600px"></iframe>')
    }
</script>
@endsection