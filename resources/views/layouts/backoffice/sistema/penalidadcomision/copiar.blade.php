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
        <div class="alert alert-warning p-1" style="color: #000;">
          Se copiarán los parámetros de <b>Penalidades y Comisiones</b>.
          Podiendo modificar los valores dentro de la agencia.
        </div>
        <div class="alert alert-danger p-1" style="color: #000; margin-top: 5px;">
            <b>Nota:</b> Los datos modificados/registrados en agencia destino se reemplazarán por completo al momento de copiar configuración.
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
  sistema_select2({ input:'#idorigen' });
  sistema_select2({ input:'#iddestino' });

  // Evita que destino quede igual a origen. Se reevalua cada vez que cambia el origen:
  // antes solo se calculaba una vez al abrir el modal, por lo que la primera agencia de
  // la lista quedaba inhabilitada como destino para siempre y elegir otro origen no
  // actualizaba las opciones deshabilitadas de destino.
  function penalidadcomision_sync_destino(){
      var origen = $('#idorigen').val();

      $('#iddestino option').prop('disabled',false);
      $('#iddestino option[value="'+origen+'"]').prop('disabled',true);

      if($('#iddestino').val()==origen){
          $('#iddestino').val($('#iddestino option:not([disabled])').first().val()).trigger('change');
      }
  }

  $('#idorigen').on('change',penalidadcomision_sync_destino);
  penalidadcomision_sync_destino();
</script>