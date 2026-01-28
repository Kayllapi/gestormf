<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/garantiasnoprendario') }}',
        method: 'POST',
        data:{
            view: 'registrar'
        }
    },
    function(resultado){
        $('#tabla-garantias').DataTable().ajax.reload();
        load_create_garantianoprendaria();
        lista_garantias_cliente({{ $idcliente }});
    },this)"> 
    <div class="modal-header">
        <h5 class="modal-title">Registrar Garantía Regular</h5>
    </div>
    <div class="modal-body">
        <div class="row">
          <div class="col-sm-12 col-md-12 d-none">
            <label>Cliente</label>
            <select class="form-control" id="idcliente" disabled>
              <option></option>
            </select>
          </div>
          
          
          <div class="col-sm-12 col-md-4">
            <label>Tipo Garantía Regular</label>
            <select class="form-control" id="idtipo_garantia_noprendaria">
              <option></option>
              @foreach($tipo_garantia_noprendaria as $value)
                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-sm-12 col-md-4">
            <label>Sub Tipo I</label>
            <select class="form-control" id="idsubtipo_garantia_noprendaria">
              <option></option>
            </select>
          </div>
          <div class="col-sm-12 col-md-4">
            <label>Sub Tipo II</label>
            <select class="form-control" id="idsubtipo_garantia_noprendaria_ii">
              <option></option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6">
            <label>Descripción de garantía en Propuesta</label>
            <textarea class="form-control" id="descripcion" cols="30" rows="5"></textarea>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-4">
            <label>Valor de mercado (S/)</label>
            <input type="number" step="any" id="valor_mercado" class="form-control" value="0.00">
          </div>
          <div class="col-sm-12 col-md-4 garantia-preferida">
            <label>Valor comercial (Tasado) (S/)</label>
            <input type="number" step="any" id="valor_comercial" class="form-control" value="0.00">
          </div>
          <div class="col-sm-12 col-md-4 garantia-preferida">
            <label>Valor de realización (Tasado) (S/)</label>
            <input type="number" step="any" id="valor_realizacion" class="form-control" value="0.00">
          </div>
          
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form>  
<script>
  $('tr.selected').removeClass('selected');
          $('#cont-ultimamodificacion').addClass('d-none');
    $('#btn-autorizar-garantia').addClass("d-none");
  $('#btn-delete-garantia').addClass("d-none");
  sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idcliente', val: '{{ $idcliente }}' });
  @include('app.nuevosistema.select2',['input'=>'#idtipo_garantia_noprendaria']);
  $("#idtipo_garantia_noprendaria").on("change", function(e) {
    
    if(e.currentTarget.value == 1){
      $('.garantia-preferida').removeClass('d-none');
    }else{
      $('.garantia-preferida').addClass('d-none');
    }
    $('#idsubtipo_garantia_noprendaria').html('');
    $('#idsubtipo_garantia_noprendaria_ii').html('');
    carga_subtipo1(e.currentTarget.value);
  });
  
  function carga_subtipo1(idtipo_garantia_noprendaria){
    $.ajax({
      url:"{{url('backoffice/0/garantiasnoprendario/show_subtipo_garantia_noprendaria')}}",
      type:'GET',
      data: {
          idtipo_garantia_noprendaria : idtipo_garantia_noprendaria
      },
      success: function (res){
        let option_select = `<option></option>`;
        var i = 1;
        $.each(res, function( key, value ) {
          option_select += `<option value="${value.id}" num="${i}">${i}.- ${value.nombre}</option>`;
          i++;
        });
        $('#idsubtipo_garantia_noprendaria').html(option_select);
        sistema_select2({ input:'#idsubtipo_garantia_noprendaria'});

      }
    })
  }
  $("#idsubtipo_garantia_noprendaria").on("change", function(e) {
    $('#idsubtipo_garantia_noprendaria_ii').html('');
    var num = $("#idsubtipo_garantia_noprendaria").find('option:selected').attr('num');
    carga_subtipo2(e.currentTarget.value,num);
  });
  
  function carga_subtipo2(idsubtipo_garantia_noprendaria,num){
    $.ajax({
      url:"{{url('backoffice/0/garantiasnoprendario/show_subtipo_garantia_noprendaria_ii')}}",
      type:'GET',
      data: {
          idsubtipo_garantia_noprendaria : idsubtipo_garantia_noprendaria
      },
      success: function (res){
        let option_select = `<option></option>`;
        var i = 1;
        $.each(res, function( key, value ) {
          option_select += `<option value="${value.id}">${num}.${i}.- ${value.nombre}</option>`;
          i++;
        });
        $('#idsubtipo_garantia_noprendaria_ii').html(option_select);
        sistema_select2({ input:'#idsubtipo_garantia_noprendaria_ii'});

      }
    })
  }


  
  
</script>    