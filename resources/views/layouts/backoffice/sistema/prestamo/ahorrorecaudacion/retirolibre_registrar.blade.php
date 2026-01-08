<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Realizar Retiro</span>
      <a class="btn btn-success" href="javascript:;" onclick="retirolibre_index()"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form action="javascript:;" 
        onsubmit="callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamo/ahorrorecaudacion/{{ $idprestamo_ahorro }}',
            method: 'PUT',
            data:   {
              view: 'retirolibre_registrar'
            }
        },
        function(resultado){
            retirolibre_index();
            resumen_index();
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