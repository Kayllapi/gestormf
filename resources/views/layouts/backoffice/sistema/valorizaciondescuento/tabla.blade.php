<div class="modal-header">
    <h5 class="modal-title">
      Valorización de Descuento de Joyas
      <button type="button" class="btn btn-success" onclick="load_create_tipogarantia()">NUEVO / Actualizar</button>
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
    
    <div class="row">
      <div class="col-sm-12" id="form-valorizacion-result">

      </div>
      <div class="col-sm-12 col-md-12" style="
        overflow-y: scroll;
        height: calc(100vh - 240px);
        padding-top: 0px;
        padding-bottom: 0px;">
        <table class="table table-striped table-hover" id="table-valorizacion-descuento">
          <thead class="table-dark" style="position: sticky;top: 0;">
            <tr>
              <th>Metodo de Valorización</th>
              <th>Detalle Descuento</th>
              <th>Descuento (%/g)</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="3">SIN RESULTADOS</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
</div>
<script>


    @include('app.nuevosistema.select2',['input'=>'#idtipo_joyassearch'])
    load_create_tipogarantia();
    function load_create_tipogarantia(){
      pagina({ route:"{{url('backoffice/'.$tienda->id.'/valorizaciondescuento/create?view=registrar')}}", result:'#form-valorizacion-result'});
        //load_create_tipogarantia();
    }
  
    function lista_valorizacion_garantia(){
      var idtipo_joyas = $("#idtipo_joyas").find('option:selected').val();
      var iddescuento_joya = $("#iddescuento_joya").find('option:selected').val();
      $.ajax({
        url:"{{url('backoffice/0/valorizaciondescuento/showlistavalorizaciondescuento')}}",
        type:'GET',
        data: {
            idtipo_joyas : idtipo_joyas,
            iddescuento_joya : iddescuento_joya
        },
        success: function (res){
          
          $('#table-valorizacion-descuento > tbody').html(res.html);
          
        }
      })
    }
  function show_editar_valorizacion(e){
    let id = $(e).attr('data-valor-columna');
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/valorizaciondescuento/"+id+"/edit?view=editar", result:'#form-valorizacion-result'});
  }
</script>

