<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/cvcontrolaperturaopecaja/0') }}',
          method: 'PUT',
          data:{
              view: 'validar',
              fecha_corte: '{{$fecha_corte}}',
          }
      },
      function(resultado){
          lista_credito();
          $('#modal-close-controlaperturaopecaja-valid').click();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Validar</h5>
        <button type="button" class="btn-close" id="modal-close-controlaperturaopecaja-valid" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
      @if($estado_cierre_institucional=='PENDIENTE')
                      <p class="text-center" 
                           style="background-color: #ffc9ca !important;
                                  padding: 15px;
                                  border-radius: 5px;
                                  color: #93222c !important;
                                  width: 90%;
                                  margin: auto;">Hay una aperturas pendientes!!</p>
      @elseif($estado_cierre_institucional=='NOEXISTE')
                      <p class="text-center" 
                           style="background-color: #ffc9ca !important;
                                  padding: 15px;
                                  border-radius: 5px;
                                  color: #93222c !important;
                                  width: 90%;
                                  margin: auto;">No hay ninguna apertura!!</p>
      @else
        <div class="mt-2 bg-primary subtitulo">Aprobación</div>
              <div class="mb-1">
                  <label>Responsable *</label>
                  <select class="form-select" id="idresponsable">
                      <option value=""></option>
                      @foreach($usuarios as $value)
                      <option value="{{$value->id}}" idpermiso="{{$value->idpermiso}}">{{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                      @endforeach
                  </select>
              </div>
              <input type="hidden" id="idresponsable_permiso">
              <div class="mb-1">
                  <label>Contraseña *</label>
                  <input type="password" class="form-control" id="responsableclave">
              </div>
      @endif
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Validar</button>
    </div>
</form>  
<script>
    sistema_select2({ input:'#idresponsable' });
    
    $("#idresponsable").on("change", function(e) {
        $('#idresponsable_permiso').val($('#idresponsable :selected').attr('idpermiso'));
    });
</script> 