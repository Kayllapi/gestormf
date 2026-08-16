<div id="carga_cobranzacuota_autorizar_pagoanticipado">
<form action="javascript:;"
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/cobranzacuota/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'autorizar_pagoanticipado'
          }
      },
      function(resultado){
          $('#modal-close-cobranzacuota-autorizar').click();
          modal({
              route: '{{ url('backoffice/'.$tienda->id.'/cobranzacuota') }}/{{ $credito->id }}/edit?view=cobrar'+
                  '&opcion=PAGO_ANTICIPADO'+
                  '&numerocuota={{ $numerocuota ?? 0 }}'+
                  '&opcion_pago=PAGO_ANTICIPADO'+
                  '&idcredito_cargo_ids={{ urlencode($idcredito_cargo_ids ?? '') }}',
              size: 'modal-sm'
          });
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Autorización - Pago Anticipado</h5>
        <button type="button" class="btn-close" id="modal-close-cobranzacuota-autorizar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i> El "Pago Anticipado" requiere autorización.<br>
          <b>Cuenta: C{{ str_pad($credito->cuenta, 8, "0", STR_PAD_LEFT) }}</b>
        </div>
        <div class="row">
            <label class="mt-2 bg-primary subtitulo">Aprobación</label>
            <div class="mb-1">
                <label>Responsable *</label>
                <select class="form-select" id="idresponsable">
                    <option value=""></option>
                    @foreach($usuarios as $value)
                    <option value="{{$value->id}}">{{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-1">
                <label>Contraseña *</label>
                <input type="password" class="form-control" id="responsableclave">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Autorizar</button>
    </div>
</form>
</div>
<script>
    sistema_select2({ input:'#idresponsable' });
</script>
