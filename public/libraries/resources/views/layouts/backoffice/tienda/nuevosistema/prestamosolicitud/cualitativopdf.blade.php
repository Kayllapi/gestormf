<div class="list-single-main-wrapper fl-wrap" onclick="cualitativopdf_index()">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>PDF Análisis Cualitativo</span>
  </div>
</div>
@if($cualitativo!=''){
<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/'.$prestamocredito->id.'/edit?view=cualitativopdf-pdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>  
@else
<div class="mensaje-warning">
Aun no a ingresado ninguna información al Análisis Cualitativo
</div>
@endif