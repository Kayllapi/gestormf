<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/penalidadcomision/0') }}',
          method: 'PUT',
          data:{
              view: 'copiar',
              idorigen: $('#idorigen').val(),
              iddestino: $('#iddestino').val()
          }
      },
      function(resultado){
          $('#close_copiar').click();
          editar_select();
      },this)"> 

    <div class="modal-header">
        <h5 class="modal-title">Copiar Configuración entre Agencias</h5>
        <button type="button" class="btn-close" id="close_copiar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-warning p-1">
          Se copiarán los parámetros de <b>Penalidades y Comisiones</b> (días de tolerancia, tasa
          moratoria, tipo de cambio, costos de gestión de garantía, etc.) de la agencia origen a la
          agencia destino.
        </div>
        <ul class="mb-1" style="font-size: 13px;">
          <li>Si la agencia destino <b>no tiene</b> el parámetro, se registra.</li>
          <li>Si la agencia destino <b>ya tiene</b> el parámetro, se <b>actualiza</b> (se sobrescribe).</li>
          <li>Los parámetros que la agencia <b>origen no tenga</b> configurados se omiten.</li>
          <li>No se copian las penalidades por tipo de garantía: son un valor común a todas las agencias.</li>
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