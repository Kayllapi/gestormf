<div class="modal-header">
    <h5 class="modal-title">
      Cargo
      {{-- <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="buscarcliente()">
        <i class="fa fa-search"></i> Buscar Cliente
      </button>
      
      

      <!-- Modal -->
      <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">Buscar Cliente</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-sm-12">
                  <select class="form-control" id="idclientesearch">
                     <option></option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> --}}
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="modal-body pb-0">
          <div class="row">
                <div class="col-sm-12 col-md-6">
                  <div class="row">
                      <label for="fecha_inicio" class="col-sm-3 col-form-label">AGENCIA</label>
                      <div class="col-sm-9">
                          <input type="text" class="form-control" value="{{$tienda->nombreagencia}}" disabled>
                          <input type="hidden" id="idagencia" value="{{$tienda->id}}">
                          {{-- <select class="form-control" id="idagencia" disabled>
                            <option></option>
                            @foreach($agencias as $value)
                                <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                            @endforeach
                          </select> --}}
                      </div>
                  </div>
                  <div class="row">
                      <label for="fecha_fin" class="col-sm-3 col-form-label">CLIENTE</label>
                      <div class="col-sm-9">
                          <select class="form-control" id="idcliente">
                            <option></option>
                          </select>
                      </div>
                  </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="row">
                      <div class="col-sm-12 col-md-12" style="text-align: right;">
                          <button type="button" class="btn btn-success" onclick="lista_credito_cliente()"><i class="fa-solid fa-search"></i> FILTRAR</button>
                      </div>
                    </div>
                    <div class="row mt-1">
                      <label for="fecha_fin" class="col-sm-4 col-form-label" style="text-align: right;">EJECUTIVO</label>
                      <div class="col-sm-8">
                          <input type="text" class="form-control" id="nombre_asesor" value="" disabled>
                          <input type="hidden" id="idasesor" value="{{Auth::user()->id}}">
                          {{-- <select class="form-control" id="idasesor" disabled>
                              <option></option>
                          </select> --}}
                      </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 offset-md-6">
                    <div class="row mt-1">
                        <label for="nombre_cobranza" class="col-sm-4 col-form-label" style="text-align: right;">COBRANZA</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="nombre_cobranza" value="" disabled>
                        </div>
                    </div>
                </div>
                <!--div class="col-sm-12 col-md-5" style="text-align: right;">
                    <button type="button" class="btn btn-warning" onclick="vistapreliminar()"><i class="fa-solid fa-search"></i> VISTA PRELIMINAR</button>
                </div-->
          </div>
        </div> 
      </div>
    </div>
    <div class="col-sm-12 col-md-4">
      <div class="row d-none data-cliente">
        <div class="col-sm-12">
          <label>Apellidos y Nombres: </label>
          <input type="text" disabled value="" class="form-control" id="data-cliente-nombre">
          <input type="hidden" value="" class="form-control" id="data-cliente-id">
        </div>
        <div class="col-sm-12">
          <label>Documento de Identidad(RUC/DNI/CE): </label>
          <input type="text" disabled value="" class="form-control" id="data-cliente-documento">
        </div>
        
      </div>
      <label>Prestamos: </label>
      <table class="table table-striped table-hover" id="table-detalle-prestamo">
        <thead class="table-dark">
          <tr>
            <th>MONTO</th>
            <th>F.C.</th>
            <th>N° CUENTA</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="2">SIN RESULTADOS</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="col-sm-12 col-md-8">
      <div class="card">
        <div class="card-body p-2" id="form-garantias-result">
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  $('#idcliente').select2({
      ajax: {
          url:"{{url('backoffice/'.$tienda->id.'/cargo/show_credito_asesor')}}",
          dataType: 'json',
          delay: 250,
          data: function (params) {
              return {
                    buscar: params.term
              };
          },
          processResults: function (data) {
              return {
                  results: data
              };
          },
          cache: true
      },
      placeholder: '-- Seleccionar --',
      minimumInputLength: 2,
      theme: 'bootstrap-5',
      dropdownParent: $('#idcliente').parent()
  });
  function lista_credito_cliente(id){
    $('#nombre_asesor').val('');
    $('#nombre_cobranza').val('');
    $('#form-garantias-result').empty();
    $('#table-detalle-prestamo > tbody').empty();
    $('.data-cliente').addClass('d-none');
    $.ajax({
      url:"{{url('backoffice/0/cargo/showlistacreditos')}}",
      type:'GET',
      data: {
          idagencia : $('#idagencia').val(),
          idcliente : $('#idcliente').val(),
          idasesor : $('#idasesor').val(),
      },
      success: function (res){
        
        $('.data-cliente').removeClass('d-none')
        $('#data-cliente-id').val(res.cliente.id);
        $('#data-cliente-nombre').val(res.cliente.nombrecompleto);
        $('#data-cliente-documento').val(res.cliente.identificacion);
        $('#table-detalle-prestamo > tbody').html(res.html);
        $('#nombre_cobranza').val('');
        $('#nombre_asesor').val(res.asesor || '');
        $("#exampleModal").modal('hide');
        //load_create_prestamo(res.cliente.id);
        $('#btn-create-cliente').removeClass('d-none');
      },
      error: function (xhr) {
        mensaje = xhr.responseJSON?.message || 'No se pudo consultar los créditos del cliente.';
        modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });
      }
    })
  }
  
  function show_data(e) {
    $('#nombre_cobranza').val($(e).attr('data-cobranza') || '');
    let id = $(e).attr('data-valor-columna');
    $('#table-detalle-prestamo tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/cargo/"+id+"/edit?view=editar", result:'#form-garantias-result'});  
  }


</script>  

