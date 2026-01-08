<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Ticket</span>
      <a class="btn btn-success" href="javascript:;" onclick="mostrar_pagorealizado()"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza/'.$s_prestamo_credito->id.'/edit?view=ticketpdf&idcobranza='.$cobranza->id) }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>