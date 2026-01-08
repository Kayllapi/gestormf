  <div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Anular Cobranza</span>
      <a class="btn btn-success" href="javascript:;" onclick="mostrar_pagorealizado()"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
  </div>
  <form action="javascript:;" 
        onsubmit="callback({
                            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamocobranza/{{ $cobranza->id }}',
                            method: 'PUT',
                            data:   {
                              view: 'anular_pagorealizado'
                            }
                          },
                          function(resultado){
                              pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamocobranza/{{ $cobranza->idprestamo_credito }}/edit?view=cobranza',result:'#cont-clientecredito'});
                              removecarga({input:'#carga-cobranza'});
                          },this)">
    
    <div class="row">
      <div class="col-sm-6">
        <label>Código</label>
        <input type="text" value="{{ $cobranza->codigo }}" disabled>
        <label>Fecha de Pago</label>
        <input type="text" value="{{ date_format(date_create($cobranza->fecharegistro), "d/m/Y h:i:s A") }}" disabled>
        <label>Agencia</label>
        <input type="text" value="{{ $agencia->nombrecomercial }}" disabled>
      </div>
      <div class="col-sm-6">
        <label>Cliente</label>
        <input type="text" value="{{ $cobranza->cliente_identificacion }} - {{ $cobranza->cliente }}" disabled>
        <label>Asesor</label>
        <input type="text" value="{{ $cobranza->asesor_apellidos }}, {{ $cobranza->asesor_nombre }} " disabled>
        <label>Ventanilla</label>
        <input type="text" value="{{ $cobranza->cajero_apellidos }}, {{ $cobranza->cajero_nombre }} " disabled>
      </div>
    </div>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de Anular la Cobranza?</b>
    </div>
    <button type="submit" class="btn  mx-btn-post">Anular</button>
  </form>