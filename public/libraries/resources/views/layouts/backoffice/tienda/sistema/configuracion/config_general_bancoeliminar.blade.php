<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Eliminar Banco</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_banco()"><i class="fa fa-angle-left"></i> Atras</a></a>
    </div>
</div>
<form action="javascript:;"
      onsubmit="callback({
                              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
                              method: 'DELETE',
                              data:   {
                                  view: 'eliminar-banco',
                                  idbanco: {{ $banco->id }}
                              }
                          },
                          function(resultado){
                              index_banco();
                          },this)">
    <div class="row">
        <div class="col-sm-6">
            <label>Nombre</label>
            <input type="text" value="{{$banco->nombre}}" id="banco_nombre" disabled/>
        </div>
    </div>
        <div class="mensaje-danger">
          <i class="fa fa-warning"></i> ¿Esta seguro Eliminar?
        </div>
    <button type="submit" class="btn mx-btn-post">Eliminar</button>
</form>