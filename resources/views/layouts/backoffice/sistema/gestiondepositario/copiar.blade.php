<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/gestiondepositario/0') }}',
          method: 'PUT',
          data:{
              view: 'copiar',
              idorigen: $('#idorigen').val(),
              iddestino: $('#iddestino').val()
          }
      },
      function(resultado){
          $('#close_copiar').click();
          // Se recarga la pantalla mostrando la agencia DESTINO, que es donde se
          // acaba de copiar. Antes se llamaba ir_inicio(), que sacaba al usuario al
          // inicio del sistema y por eso parecia que la copia no se habia hecho.
          recargar_depositario($('#iddestino').val());
      },this)"> 

    <div class="modal-header">
        <h5 class="modal-title">Copiar Configuración entre Agencias</h5>
        <button type="button" class="btn-close" id="close_copiar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-warning p-1" style="color: #000;">
          Se copiarán los <b>Depositarios y Representante Común</b>.
          Podiendo modificar el tarifario dentro de la agencia.
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
