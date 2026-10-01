<div class="modal-header">
  <h5 class="modal-title">
    Tasas Activas
    <button type="button" class="btn btn-success" id="btn-create-cliente" onclick="load_nuevo_tarifario()"><i class="fa-solid fa-plus"></i> Nuevo</button>
    {{-- La copia entre agencias solo tiene sentido si hay mas de una administrable --}}
    @if($agencias->count()>1)
    <button type="button" class="btn btn-primary" onclick="copiar_tarifario()">
      <i class="fa fa-copy"></i> Copiar Configuración
    </button>
    @endif
  </h5>

  <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2">
            <div class="row">
              <label for="idagencia" class="col-sm-2 col-form-label" style="text-align: right;">AGENCIA</label>
              <div class="col-sm-5">
                <select class="form-control" id="idagencia">
                  @foreach($agencias as $value)
                    <option value="{{ $value->id }}" {{ (int) $value->id == (int) $idtienda_seleccionada ? 'selected' : '' }}>{{ $value->nombreagencia }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-sm-5" style="text-align: right;">
                <span class="badge" style="background-color: #bcbcbc;color: #000;">
                  El tarifario se visualiza, se registra y se guarda sobre la agencia seleccionada
                </span>
              </div>
            </div>
            <div id="form-credito-result"></div>
          </div>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body" style="
            overflow-y: scroll;
            height: calc(100vh - 450px);
            padding-top: 0px;
            padding-bottom: 0px;">

            <table class="table table-striped table-hover" id="table-lista-credito">
              <thead class="table-dark" style="position: sticky;top: 0;">
                <tr>
                  <td>N°</td>
                  <td>CÓDIGO</td>
                  <td>MONTO (<=)</td>
                  <td>CUOTA (<=)</td>
                  <td id="th-tem">TEM/TNC %</td>
                  <td>Ss. RECAUDO %</td>
                  <td>FORMA DE PAGO</td>
                  <td>TIPO DE CRÉDITO</td>
                  <td>PRODUCTO</td>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
        </div>
      </div>
  </div>
</div>
<script>
  sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idclientesearch' });

  // Cambiar de agencia recarga el formulario de alta y la lista: las dos leen
  // la agencia del selector.
  $("#idagencia").on("change", function () {
    load_nuevo_tarifario();
    lista_tarifario();
  });

  function copiar_tarifario(){
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/tarifario/0/edit?view=copiar&idagencia="+$('#idagencia').val(), size: 'modal-sm' })
  }

  lista_tarifario();
  function lista_tarifario(id){
    let tipo_producto_credito = $('#tipo_producto_credito').val();
    let idcredito_prendatario = $('#idcredito_prendatario').val();
    let idforma_pago_credito = $('#idforma_pago_credito').val();

    let tipo = $("#idforma_credito").find('option:selected').val();

    $.ajax({
      url:"{{url('backoffice/0/tarifario/showtarifario')}}",
      type:'GET',
      data: {
          idcliente : id,
          tipo_producto_credito : tipo_producto_credito,
          idcredito_prendatario : idcredito_prendatario,
          idforma_pago_credito : idforma_pago_credito,
          tipo : tipo,
          idagencia : $('#idagencia').val(),

      },
      success: function (res){
        $('#table-lista-credito > tbody').html(res.html);
      }
    })
  }
  
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
        
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
//     modal({route:"{{url('backoffice')}}/{{$tienda->id}}/tarifario/"+id+"/edit?view=editar"}); 
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/tarifario/"+id+"/edit?view=editar&idagencia="+$('#idagencia').val(), result:'#form-credito-result'});
    
  }
  load_nuevo_tarifario();
  function load_nuevo_tarifario(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/tarifario/create?view=registrar&idagencia=')}}"+$('#idagencia').val(), result:'#form-credito-result'});
  }
//   load_nuevo_tarifario();

</script>  
