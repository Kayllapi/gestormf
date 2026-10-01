<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/tarifario/0') }}',
          method: 'PUT',
          data:{
              view: 'copiar',
              idorigen: $('#idorigen').val(),
              iddestino: $('#iddestino').val()
          }
      },
      function(resultado){
          $('#close_copiar').click();
          load_nuevo_tarifario();
          lista_tarifario();
      },this)"> 

    <div class="modal-header">
        <h5 class="modal-title">Copiar Tasas entre Agencias</h5>
        <button type="button" class="btn-close" id="close_copiar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-warning p-1">
          Se copiarán las <b>Tasas Activas</b> (montos, cuotas, TEM y Ss. Recaudo) de la agencia
          origen a la agencia destino, junto con los <b>Productos de Crédito</b> que esas tasas
          necesiten.
        </div>
        <ul class="mb-1" style="font-size: 13px;">
          <li>Cada tasa se empareja por <b>Producto + Forma de Pago + Monto + Cuotas</b>.</li>
          <li>Si la agencia destino <b>no tiene</b> esa tasa, se registra.</li>
          <li>Si la agencia destino <b>ya tiene</b> esa tasa, se <b>actualiza</b> (se sobrescribe
              el TEM y el Ss. Recaudo).</li>
          <li>Los productos se emparejan por <b>nombre</b>: si el destino no tiene el producto, se
              crea la copia ahí. Los productos <b>nunca se borran</b>, porque los créditos ya
              emitidos los referencian.</li>
          <li>Las tasas que ya tenga el destino y <b>no existan en el origen</b> no se tocan.</li>
        </ul>

        <div class="row mt-1">
            <label class="col-sm-4 col-form-label" style="text-align: right;">Agencia ORIGEN:</label>
            <div class="col-sm-8">
                <select class="form-control" id="idorigen">
                  @foreach($agencias as $value)
                    <option value="{{ $value->id }}">{{ $value->nombreagencia }}</option>
                  @endforeach
                </select>
            </div>
        </div>
        <div class="row mt-1">
            <label class="col-sm-4 col-form-label" style="text-align: right;">Agencia DESTINO:</label>
            <div class="col-sm-8">
                <select class="form-control" id="iddestino">
                  @foreach($agencias as $value)
                    <option value="{{ $value->id }}">{{ $value->nombreagencia }}</option>
                  @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success"><i class="fa-solid fa-copy"></i> COPIAR</button>
    </div>
</form>  

<script>
  (function(){
      var origen  = $('#idorigen').val();
      var destino = $('#iddestino').val();

      // Evita que el destino venga preseleccionado igual al origen.
      $('#iddestino option').each(function(){
          if($(this).val()==origen){
              $(this).prop('disabled',true);
          }
      });
      if(destino==origen){
          $('#iddestino option:not([disabled])').first().prop('selected',true);
      }
  })();
</script>
