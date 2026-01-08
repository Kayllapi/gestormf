<div class="list-single-main-wrapper fl-wrap" onclick="creditopdf_index()">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>PDF Solicitud</span>
  </div>
</div>
<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/'.$prestamocredito->id.'/edit?view=creditopdf-pdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>