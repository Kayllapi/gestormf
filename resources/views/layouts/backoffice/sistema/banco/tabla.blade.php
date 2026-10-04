<div class="modal-header">
  <h5 class="modal-title">
    Bancos
    <button type="button" class="btn btn-success" id="btn-create-cliente" onclick="load_nuevo_banco()"><i class="fa-solid fa-plus"></i> Nuevo</button>
    @if($agencias->count()>1)
    <button type="button" class="btn btn-primary" onclick="copiar_banco()"><i class="fa fa-copy"></i> Copiar Configuración</button>
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
                  Los bancos se registran y guardan sobre la agencia seleccionada
                </span>
              </div>
            </div>
            <div id="form-credito-result">
          </div>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            
            <table class="table table-striped table-hover" id="table-lista-banco">
              <thead class="table-dark">
                <tr>
                  <td>N°</td>
                  <td>Banco</td>
                  <td>Cuenta</td>
                  <td>Estado</td>
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

  function copiar_banco(){
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/banco/0/edit?view=copiar&idagencia="+$('#idagencia').val(), size: 'modal-sm' })
  }

  // Cambiar de agencia recarga el formulario de alta y la lista: las dos leen
  // la agencia del selector.
  $("#idagencia").on("change", function () {
    load_nuevo_banco();
    lista_banco();
  });

  lista_banco();
  function lista_banco(){
    $.ajax({
      url:"{{url('backoffice/0/banco/showbanco')}}",
      type:'GET',
      data: {
          idagencia : $('#idagencia').val(),
      },
      success: function (res){
        $('#table-lista-banco > tbody').html(res.html);
      }
    })
  }
  
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
        
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/banco/"+id+"/edit?view=editar&idagencia="+$('#idagencia').val(), result:'#form-credito-result'});
    
  }
  load_nuevo_banco();
  function load_nuevo_banco(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/banco/create?view=registrar&idagencia=')}}"+$('#idagencia').val(), result:'#form-credito-result'});
  }

</script>  
