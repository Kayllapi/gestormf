<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Aprobar Crédito</span>
    <a class="btn btn-success" href="javascript:;" onclick="index()"><i class="fa fa-angle-left"></i> Atras</a></a>
  </div>
</div>

@include('app.creditodetalle',[
  'route_post'=>'prestamoaprobacion',
  'view'=>'aprobar',
  'btn_nombre'=>'Aprobar Crédito'
])