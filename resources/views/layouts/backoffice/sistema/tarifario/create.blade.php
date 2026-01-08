<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/tarifario') }}',
        method: 'POST',
        data:{
            view: 'registrar'
        }
    },
    function(resultado){
        lista_tarifario();
        load_nuevo_tarifario();
    },this)"> 
    <div class="modal-body">
      <div class="row justify-content-center">
        <div class="col-sm-12 col-md-6">
          <div class="row">
            <label class="col-sm-3 col-form-label">Tipo Crédito:</label>
            <div class="col-sm-6">
              <select class="form-control" id="idforma_credito">
                <option></option>
                @foreach($forma_credito as $value)
                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Producto:</label>
            <div class="col-sm-6">
              <select class="form-control" id="idcredito_prendatario">
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Forma de Pago:</label>
            <div class="col-sm-6">
              <select class="form-control" id="idforma_pago_credito">
                @foreach($forma_pago_credito as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Monto (<=):</label>
            <div class="col-sm-6">
              <input type="number" class="form-control" value="0.00" step="any" id="monto">
            </div>
          </div>
           <div class="row">
            <label class="col-sm-3 col-form-label">Cuotas (<=):</label>
            <div class="col-sm-6">
              <input type="number" class="form-control" value="0" id="cuotas">
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">TEM (%):</label>
            <div class="col-sm-6">
              <input type="number" class="form-control" value="0.00" step="any" id="tem">
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Servicios/Otros (%):</label>
            <div class="col-sm-6">
              <input type="number" class="form-control" step="any" value="0.00" id="cargos_otros">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-3"></label>
            <div class="col-sm-6">
              <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> GENERAR TARIFARIO</button>
            </div>
          </div>
          
         
        </div>
      </div>
    </div>
</form>  
<script>
  @include('app.nuevosistema.select2',['input'=>'#idforma_credito'])
  @include('app.nuevosistema.select2',['input'=>'#idcredito_prendatario'])
  @include('app.nuevosistema.select2',['input'=>'#idforma_pago_credito'])
  
  $("#idforma_credito").on("change", function(e) {
    carga_producto_credito();
    lista_tarifario();
  });
  $("#idcredito_prendatario").on("change", function(e) {
    lista_tarifario();
  });
  $("#idforma_pago_credito").on("change", function(e) {
    lista_tarifario();
  });
  function carga_producto_credito(){
    let tipo = $("#idforma_credito").find('option:selected').val();
    $.ajax({
      url:"{{url('backoffice/0/tarifario/showproductocredito')}}",
      type:'GET',
      data: {
          tipo : tipo,
      },
      success: function (res){
        let option_select = `<option></option>`;
        var i = 1;
        $.each(res, function( key, value ) {
          option_select += `<option value="${value.id}">${value.nombre}</option>`;
          i++;
        });
        $('#idcredito_prendatario').html(option_select);
        sistema_select2({ input:'#idcredito_prendatario'});

      }
    })
  }
</script>    