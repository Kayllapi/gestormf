<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Piso</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_piso()"><i class="fa fa-angle-left"></i> Atras</a>
    </div>
</div>
<div id="form-registrar-piso">
  <form action="javascript:;" 
        onsubmit="callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion',
            method: 'POST',
            carga: '#form-registrar-piso',
            data:   {
                view: 'registrar-piso'
            }
        },
        function(resultado){
          index_piso();
        },this)">
      <div class="row">
          <div class="col-sm-12">
              <label>Número *</label>
              <input type="number" id="nombre">
          </div>
      </div>
      <button type="submit" class="btn mx-btn-post">Registrar Piso</button>
  </form>
</div>