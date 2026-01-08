<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span><b>Piso {{ str_pad($piso->nombre, 2, "0", STR_PAD_LEFT) }}</b> / Registrar Ambiente</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_ambiente({{ $tienda->id }}, {{ $piso->id }})"><i class="fa fa-angle-left"></i> Atras</a>
    </div>
</div>
<div id="form-registrar-ambiente">
  <form action="javascript:;" 
        onsubmit="callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion',
            method: 'POST',
            carga: '#form-registrar-ambiente',
            data:   {
                view: 'registrar-ambiente',
                idpiso: {{ $piso->id }}
            }
        },
        function(resultado){
          index_ambiente({{ $tienda->id }}, {{ $piso->id }});
        },this)">
      <div class="row">
          <div class="col-sm-12">
              <label>Número *</label>
              <input type="number" id="nombre">
          </div>
      </div>
      <button type="submit" class="btn mx-btn-post">Registrar Ambiente</button>
  </form>
</div>
