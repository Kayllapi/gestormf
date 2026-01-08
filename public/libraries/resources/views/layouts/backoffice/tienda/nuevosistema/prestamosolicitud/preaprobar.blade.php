<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Preaprobar Crédito</span>
    <a class="btn btn-success" href="javascript:;" onclick="index()"><i class="fa fa-angle-left"></i> Atras</a></a>
  </div>
</div>
@include('app.creditodetalle',[
  'route_post'=>'prestamosolicitud',
  'view'=>'preaprobar',
  'btn_nombre'=>'Pre-Aprobar Crédito'
])