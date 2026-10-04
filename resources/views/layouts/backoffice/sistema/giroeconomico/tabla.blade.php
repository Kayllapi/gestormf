<div class="modal-header">
    <h5 class="modal-title">
      Giro Económico
      <a href="javascript:;" 
         class="btn btn-success" 
         onclick="load_nuevo_giro()">
        <i class="fa-solid fa-plus"></i> Registrar
      </a>
      @if($agencias->count()>1)
      <button type="button" class="btn btn-primary" onclick="copiar_giro()">
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
                  El catálogo se visualiza, registra y guarda sobre la agencia seleccionada
                </span>
              </div>
            </div>
            <div id="form-result-giro">
          </div>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body" style="
            overflow-y: scroll;
            height: calc(100vh - 295px);
            padding-top: 0px;
            padding-bottom: 0px;">
            
            <table class="table table-striped table-hover" id="table-lista-giro">
              <thead class="table-dark" style="position: sticky;top: 0;">
                <tr>
                  <td width="10px"></td>
                  <td>Tipo de Giro</td>
                  <td>Giro Económico</td>
                  <td>Margen de Vta. Máximo (%)</td>
                  <td>Estado</td>
                  <td></td>
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
  sistema_select2({ input:'#idagencia' });
  // Cambiar de agencia recarga el formulario de alta y la lista: las dos leen
  // la agencia del selector. El filtro se reinicia porque un tipo de giro puede
  // no tener giros en la agencia nueva.
  $("#idagencia").on("change", function () {
    reiniciar_filtro_giro();
    load_nuevo_giro();
    lista_giro();
  });

  function copiar_giro(){
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/giroeconomico/0/edit?view=copiar&idagencia="+$('#idagencia').val(), size: 'modal-sm' })
  }

  // El filtro de la tabla se guarda APARTE del formulario, a proposito.
  // Al editar un giro, el formulario de edicion trae su propio Tipo y Estado, y
  // si lista_giro() los leyera se quedaria mostrando solo los giros de ese tipo
  // (Servicio y Produccion tienen 2 cada uno, Comercio 14). Guardandolo aqui el
  // filtro solo cambia cuando el usuario lo cambia a mano.
  //
  // Se declara ANTES del primer lista_giro() de mas abajo: el var se izla pero
  // la asignacion no, asi que si se llamara antes de esta linea la variable
  // todavia seria undefined.
  var FILTRO_LISTA_GIRO = { idtipo_giro_economico: '', estado: '' };

  function reiniciar_filtro_giro(){
    FILTRO_LISTA_GIRO = { idtipo_giro_economico: '', estado: '' };
  }

  lista_giro();
  function lista_giro(id){
    $.ajax({
      url:"{{url('backoffice/0/giroeconomico/show_table')}}",
      type:'GET',
      data: {
          idtipo_giro_economico : FILTRO_LISTA_GIRO.idtipo_giro_economico,
          estado : FILTRO_LISTA_GIRO.estado,
          idagencia : $('#idagencia').val(),
      },
      success: function (res){
        $('#table-lista-giro > tbody').html(res.html);
      }
    })
  }
  load_nuevo_giro();
  function load_nuevo_giro(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/giroeconomico/create?view=registrar&idagencia=')}}"+$('#idagencia').val(), result:'#form-result-giro'});
  }
  
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
        
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/giroeconomico/"+id+"/edit?view=editar&idagencia="+$('#idagencia').val(), result:'#form-result-giro'});
    
  }
</script>
