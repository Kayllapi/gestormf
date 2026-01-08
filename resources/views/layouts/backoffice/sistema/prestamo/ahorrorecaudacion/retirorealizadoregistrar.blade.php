  <div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Realizar Retiro</span>
      <a class="btn btn-success" href="javascript:;" onclick="mostrar_retirorealizado()"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
  </div>
  <form action="javascript:;" 
        onsubmit="callback({
                            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamo/ahorrorecaudacion/{{ $s_prestamo_ahorro->id }}',
                            method: 'PUT',
                            data:   {
                              view: 'retirorealizadoregistrar'
                            }
                          },
                          function(resultado){
                              mostrar_retirorealizado();
                          },this)">
          <div class="row">
             <div class="col-md-12">
                <label>Monto a Retirar *</label>
                <input type="text" value="" id="montoretiro" onkeyup="texto_mayucula(this)"/>
             </div>
             <div class="col-md-12">
             </div>
           </div>
    <button type="submit" class="btn  mx-btn-post">Realizar Retiro</button>
  </form>