<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/giroeconomico/0') }}',
          method: 'PUT',
          data:{
              view: 'copiar',
              idorigen: $('#idorigen').val(),
              iddestino: $('#iddestino').val()
          }
      },
      function(resultado){
          $('#close_copiar').click();
          reiniciar_filtro_giro();
          load_nuevo_giro();
          lista_giro();
      },this)">  

    <div class="modal-header">
        <h5 class="modal-title">Copiar Giro Económico entre Agencias</h5>
        <button type="button" class="btn-close" id="close_copiar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-warning p-1" style="color: #000;">
          Se copiarán los <b>Giros Económicos</b>.
          Podiendo modificar los valores dentro de la agencia.
        </div>
        <div class="alert alert-info p-1" style="color: #000; margin-top: 5px;">
            <b>Nota:</b> Los datos modificados/registrados en agencia destino se reemplazarán por completo.
        </div>
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
