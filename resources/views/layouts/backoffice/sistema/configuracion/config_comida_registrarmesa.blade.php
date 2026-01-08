<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span><b>Piso {{ str_pad($piso->nombre, 2, "0", STR_PAD_LEFT) }} / Ambiente {{ str_pad($ambiente->nombre, 2, "0", STR_PAD_LEFT) }}</b> / Registrar Mesa</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_mesa({{ $tienda->id }},{{ $piso->id }},{{ $ambiente->id }})"><i class="fa fa-angle-left"></i> Atras</a>
    </div>
</div>
<div id="form-registrar-mesa">
  <form action="javascript:;" 
        onsubmit="callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion',
            method: 'POST',
            carga: '#form-registrar-mesa',
            data:   {
                view: 'registrar-mesa',
                idambiente: {{ $ambiente->id }},
                idpiso: {{ $piso->id }},
            }
        },
        function(resultado){
          index_mesa({{ $tienda->id }},{{ $piso->id }},{{ $ambiente->id }});
        },this)">
      <div class="row">
          <div class="col-sm-6">
              <label>Número *</label>
              <input type="number" min="0" step="1" id="mesa_registrar_numero_mesa">
          </div>
      </div>
      <button type="submit" class="btn mx-btn-post">Guardar Mesa</button>
  </form>
</div>