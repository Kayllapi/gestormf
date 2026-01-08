<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Ticket de Retiro</span>
      <a class="btn btn-success" href="javascript:;" onclick="retirolibre_index()"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrorecaudacion/'.$s_prestamo_ahorro->id.'/edit?view=ticketpdf_retirolibre&idprestamo_ahorroretirolibre='.$idprestamo_ahorroretirolibre) }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>