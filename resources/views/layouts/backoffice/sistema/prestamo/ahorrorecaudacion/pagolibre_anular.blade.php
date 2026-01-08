<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Anular Recaudación</span>
      <a class="btn btn-success" href="javascript:;" onclick="pagolibre_index()"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>

<form action="javascript:;" 
    onsubmit="callback({
        route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamo/ahorrorecaudacion/{{ $s_prestamo_ahorrorecaudacionlibre->id }}',
        method: 'PUT',
        data:   {
          view: 'pagolibre_anular'
        }
    },
    function(resultado){
        pagolibre_index();
        resumen_index();
    },this)">
    
    <div class="row">
      <div class="col-sm-6">
        <label>Código</label>
        <input type="text" value="{{ str_pad($s_prestamo_ahorrorecaudacionlibre->codigo, 8, "0", STR_PAD_LEFT) }}" disabled>
        <label>Fecha de Pago</label>
        <input type="text" value="{{ date_format(date_create($s_prestamo_ahorrorecaudacionlibre->fechaconfirmado), "d/m/Y h:i:s A") }}" disabled>
      </div>
      <div class="col-sm-6">
        <label>Cliente</label>
        <input type="text" value="{{ $s_prestamo_ahorrorecaudacionlibre->cliente_apellidos }}, {{ $s_prestamo_ahorrorecaudacionlibre->cliente_nombre }}" disabled>
        <label>Ventanilla</label>
        <input type="text" value="{{ $s_prestamo_ahorrorecaudacionlibre->cajero_apellidos }}, {{ $s_prestamo_ahorrorecaudacionlibre->cajero_nombre }} " disabled>
      </div>
    </div>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de Anular la Recaudación?</b>
    </div>
    <button type="submit" class="btn  mx-btn-post">Anular</button>
</form>