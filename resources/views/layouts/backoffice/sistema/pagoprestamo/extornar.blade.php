<div id="carga_cambiar_estado">
<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/pagoprestamo/'.$credito_cobranzacuota->id) }}',
          method: 'PUT',
          data:{
              view: 'extornar'
          }
      },
      function(resultado){
          $('#modal-close-pagoprestamo-extornar').click(); 
          lista_credito();
      },this)">
    <style>
      .form-check-label {
          margin-top: 5px;
          margin-left: 5px;
      }
    </style>
    <div class="modal-header">
        <h5 class="modal-title">Extornar Pago</h5>
        <button type="button" class="btn-close" id="modal-close-pagoprestamo-extornar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i> ¿Esta seguro de extornar el pago?<br>
          <b>"{{$credito_cobranzacuota->nombrecliente}}"</b>
        </div>
        <label class="mt-1" style="background-color: #636363;
          color: #fff;
          width: 100%;
          border-radius: 5px;
          padding: 0px 5px;
          margin-bottom: 5px;">Aprobación</label>
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
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-ban"></i> Extornar</button>
    </div>
</form>   
</div>
<script>
    sistema_select2({ input:'#idresponsable' });
</script>