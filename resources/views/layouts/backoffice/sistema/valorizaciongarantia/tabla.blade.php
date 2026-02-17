<div class="modal-header">
    <h5 class="modal-title">
      Valorización de Garantia
<!--       <a href="javascript:;" 
         class="btn btn-primary" 
         onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/valorizaciongarantia/create?view=registrar')}}'})">
        <i class="fa-solid fa-plus"></i> Registrar
      </a> -->
      <button type="button" class="btn btn-success" onclick="load_create_tipogarantia()">NUEVO / Actualizar</button>
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()" style="font-size: 20px;"></button>
</div>

<?php
  $option_select = $tipo_garantia->map(function ($item) {
      return [
          'id' => $item->id,
          'text' => $item->nombre,
      ];
  });
  $select_garantia = $option_select->toArray();
?>
<div class="modal-body">
    <div class="row justify-content-center d-none">
      <div class="col-sm-12 col-md-4">
        <div class="mb-3 row">
          <label class="col-sm-4 col-form-label">TIPO DE GARANTIA </label>
          <div class="col-sm-8">
            <select class="form-control" id="idtipogarantiasearch">
              <option></option>
              @foreach($tipo_garantia as $value)
                <option value="{{ $value->id }}" antiguedad="{{ $value->antiguedad}}" valor="{{ $value->valor}}" cobertura="{{ $value->cobertura}}">{{ $value->nombre }}</option>
              @endforeach
            </select>
          </div>
        </div>

      </div>
    </div>
    <div class="row">
      <div class="col-sm-12" id="form-valorizacion-result">

      </div>
      <div class="col-sm-12 col-md-12" style="
        overflow-y: scroll;
        height: calc(100vh - 290px);
        padding-top: 0px;
        padding-bottom: 0px;">
        <table class="table table-striped table-hover" id="table-detalle-valorizacion-garantia">
          <thead class="table-dark" style="position: sticky;top: 0;">
            <tr>
              <th>Método Valorización</th>
              <th>Antiguedad de Compra/Producción</th>
              <th>VALOR COMERCIAL (%) del precio de compra</th>
              <th>COBERTURA (%) de valor comercial</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="4">SIN RESULTADOS</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
</div>
<script>

    @include('app.nuevosistema.select2',['input'=>'#idtipogarantiasearch'])
    load_create_tipogarantia();
    function load_create_tipogarantia(){
      pagina({ route:"{{url('backoffice/'.$tienda->id.'/valorizaciongarantia/create?view=registrar')}}", result:'#form-valorizacion-result'});
    }
  
    function lista_valorizacion_garantia(){
      var idtipogarantia = $("#idtipogarantia").find('option:selected').val();
      var idmetodovalorizacion = $("#idmetodovalorizacion").find('option:selected').val();
      $.ajax({
        url:"{{url('backoffice/0/valorizaciongarantia/showlistavalorizaciongarantia')}}",
        type:'GET',
        data: {
            idtipogarantia : idtipogarantia,
            idmetodovalorizacion : idmetodovalorizacion
        },
        success: function (res){
          $('#table-detalle-valorizacion-garantia > tbody').html(res.html);
          
        }
      })
    }
  function show_editar_valorizacion(e){
    let id = $(e).attr('data-valor-columna');
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/valorizaciongarantia/"+id+"/edit?view=editar", result:'#form-valorizacion-result'});
  }
</script>

