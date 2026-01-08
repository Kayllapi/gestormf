<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Banco</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_banco()"><i class="fa fa-angle-left"></i> Atras</a></a>
    </div>
</div>
<form action="javascript:;"
      onsubmit="callback({
                              route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
                              method: 'PUT',
                              data:   {
                                  view: 'editar-banco',
                                  idbanco: {{ $banco->id }}
                              }
                          },
                          function(resultado){
                              index_banco();
                          },this)">
    <div class="row">
        <div class="col-sm-6">
            <label>Nombre *</label>
            <input type="text" value="{{$banco->nombre}}" id="banco_nombre"/>
        </div>
    </div>
    <button type="submit" class="btn mx-btn-post">Guardar</button>
</form>