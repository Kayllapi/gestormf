    <div class="modal-header">
        <h5 class="modal-title">CUENTAS POR COBRAR</h5>
        <button type="button" class="btn-close" id="close_opcionescredito" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        
      <div id="cont_cronograma"></div>
      
      <div class="row mt-1">
        <div class="col" style="flex: 1 0 0%;">
          <div id="success-message" class="alert alert-success d-none" style="text-align:left;"></div>
        </div>
        <div class="col" style="flex: 0 0 0%;">
          <button type="button" class="btn btn-danger" id="close_confirmacionproceso" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i> SALIR</button>
        </div>
      </div>
    </div>  

<script>

  show_data_cargo();
  
  function show_data_cargo() {
    $.ajax({
      url:"{{url('backoffice/0/cargo/show_cargo_cobranza')}}",
      type:'GET',
      data: {
          idcredito : {{$credito->id}},
          idestado : 1,
          opcion_pago : '{{$opcion_pago}}',
      },
      success: function (res){
        $('#cont_cronograma').html(res.html);
        sync_cargo_ids_seleccionados();
      }
    }) 
  }

  function get_cargo_ids_seleccionados(){
    const ids = [];
    $('#cont_cronograma').find('input.credito-cargo-check:checked').each(function(){
      const v = parseInt($(this).val(), 10);
      if(!isNaN(v)) ids.push(v);
    });
    return ids;
  }

  function sync_cargo_ids_seleccionados(){
    const ids = get_cargo_ids_seleccionados();
    window.idcredito_cargo_ids_selected = ids;
    $('#idcredito_cargo_ids_selected').val(ids.join(','));
  }

  function seleccionar_cargo_cobranza(e){
    // llamada desde el HTML armado en show_cargo_cobranza
    sync_cargo_ids_seleccionados();
  }
</script>