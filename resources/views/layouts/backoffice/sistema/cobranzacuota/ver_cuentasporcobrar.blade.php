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
      url:"{{url('backoffice/0/cargo/show_cargo')}}",
      type:'GET',
      data: {
          idcredito : {{$credito->id}},
          idestado : 1,
      },
      success: function (res){
        $('#cont_cronograma').html(res.html);
      }
    }) 
  }
</script>