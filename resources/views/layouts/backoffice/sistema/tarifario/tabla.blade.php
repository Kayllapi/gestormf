<div class="modal-header">
  <h5 class="modal-title">
    Tasas Activas
    <button type="button" class="btn btn-success" id="btn-create-cliente" onclick="load_nuevo_tarifario()"><i class="fa-solid fa-plus"></i> Nuevo</button>
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
                  El tarifario se visualiza, registra y guarda sobre la agencia seleccionada
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
  // la agencia del selector. El filtro se reinicia porque un producto de la
  // agencia anterior no existe en la nueva y dejaria la tabla vacia.
  $("#idagencia").on("change", function () {
    reiniciar_filtro_tarifario();
    load_nuevo_tarifario();
    lista_tarifario();
  });

  function copiar_tarifario(){
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/tarifario/0/edit?view=copiar&idagencia="+$('#idagencia').val(), size: 'modal-sm' })
  }

  // El filtro de la tabla se guarda APARTE del formulario, a proposito.
  // Al editar una tasa, el formulario de edicion trae su propio Producto, Tipo de
  // Credito y Forma de Pago, y si lista_tarifario() los leyera la tabla se
  // quedaria mostrando solo las tasas de ese producto: tras guardar, la lista
  // parecia vacia o con una sola fila y no se veia el resto del tarifario.
  //
  // El filtro lo cambia SOLO el formulario de alta (elegir un producto y ver sus
  // tasas es un flujo util), y se reinicia con reiniciar_filtro_tarifario()
  // cuando el formulario vuelve a estar en blanco.
  //
  // Se declara ANTES del primer lista_tarifario() de mas abajo: el var se izla
  // pero la asignacion no, asi que llamada antes seria undefined.
  var FILTRO_LISTA_TARIFARIO = { tipo:'', idcredito_prendatario:'', idforma_pago_credito:'' };

  function reiniciar_filtro_tarifario(){
    FILTRO_LISTA_TARIFARIO = { tipo:'', idcredito_prendatario:'', idforma_pago_credito:'' };
  }

  lista_tarifario();
  function lista_tarifario(id){
    $.ajax({
      url:"{{url('backoffice/0/tarifario/showtarifario')}}",
      type:'GET',
      data: {
          idcliente : id,
          tipo_producto_credito : '',
          idcredito_prendatario : FILTRO_LISTA_TARIFARIO.idcredito_prendatario,
          idforma_pago_credito : FILTRO_LISTA_TARIFARIO.idforma_pago_credito,
          tipo : FILTRO_LISTA_TARIFARIO.tipo,
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
