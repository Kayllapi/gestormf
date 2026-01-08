  <div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Confirmar Entrega</span>
      <a class="btn btn-success" href="javascript:;" onclick="mostrar_garantias()"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
  </div>
  <form action="javascript:;" 
        onsubmit="callback({
                            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamocobranza/{{ $prestamobien->id }}',
                            method: 'PUT',
                            data:   {
                              view: 'entregar_garantias'
                            }
                          },
                          function(resultado){
                              mostrar_garantias();
                          },this)">
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de confirmar la entrega de la garantia?</b>
    </div>
    <button type="submit" class="btn  mx-btn-post">Confirmar Entrega</button>
  </form>