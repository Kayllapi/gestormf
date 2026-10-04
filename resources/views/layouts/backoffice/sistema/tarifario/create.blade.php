<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/tarifario') }}',
        method: 'POST',
        data:{
            view: 'registrar',
            idagencia: idagencia_tarifario()
        }
    },
    function(resultado){
        // El formulario vuelve a estar en blanco, asi que el filtro tambien: si se
        // listara antes de reiniciarlo, la tabla saldria filtrada por la tasa recien
        // guardada y no se veria el resto del tarifario de la agencia.
        reiniciar_filtro_tarifario();
        lista_tarifario();
        load_nuevo_tarifario();
    },this)"> 
    <div class="modal-body">
      <div class="row justify-content-center">
        <div class="col-sm-12 col-md-6">
          <div class="row">
            <label class="col-sm-3 col-form-label">Modalidad de Crédito:</label>
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
            <label class="col-sm-3 col-form-label" id="label-tem">TEM/TNC (%):</label>
            <div class="col-sm-6">
              <input type="number" class="form-control" value="0.00" step="any" id="tem">
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Ss. Recaudo (%):</label>
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

  // La agencia se lee SIEMPRE del selector #idagencia, nunca del valor con el
  // que se renderizo este formulario ({{ (int) $agencia->id }}).
  //
  // El formulario se vuelve a pedir cada vez que se cambia de agencia y las
  // peticiones no se cancelan entre si, asi que dos formularios pueden cruzarse:
  // el de la agencia nueva se dibuja primero y luego llega el de la anterior y
  // lo pisa. Con la agencia cocida, el catalogo de productos quedaba el de la
  // agencia anterior mientras la tabla consultaba la nueva, y al elegir un
  // producto la tabla salia vacia (idagencia 198 con un producto de la 194).
  // Leyendo el selector, el catalogo siempre es el de la agencia en pantalla.
  // El valor renderizado queda solo como respaldo si el formulario se pintara
  // fuera de la pantalla de tarifario, donde no existe #idagencia.
  function idagencia_tarifario(){
    let seleccionada = ($('#idagencia').length) ? $('#idagencia').val() : '';
    return (seleccionada) ? seleccionada : {{ (int) $agencia->id }};
  }
  
  // El formulario de ALTA es el que filtra la tabla: elegir un producto y ver sus
  // tasas es el flujo de trabajo. Se escribe en FILTRO_LISTA_TARIFARIO (definido en
  // tabla.blade.php) en vez de releer el formulario en lista_tarifario(), para que
  // el formulario de EDICION no deje la tabla filtrada por su propia tasa.
  $("#idforma_credito").on("change", function(e) {
    carga_producto_credito();
    actualizar_nombre_tasa();
    FILTRO_LISTA_TARIFARIO.tipo = $(this).val();
    // el catalogo de productos se recarga con el tipo: el producto anterior ya no
    // pertenece a esta lista, asi que el filtro se limpia.
    FILTRO_LISTA_TARIFARIO.idcredito_prendatario = '';
    lista_tarifario();
  });
  $("#idcredito_prendatario").on("change", function(e) {
    actualizar_nombre_tasa();
    FILTRO_LISTA_TARIFARIO.idcredito_prendatario = $(this).val();
    lista_tarifario();
  });
  $("#idforma_pago_credito").on("change", function(e) {
    FILTRO_LISTA_TARIFARIO.idforma_pago_credito = $(this).val();
    lista_tarifario();
  });
  function carga_producto_credito(){
    let tipo = $("#idforma_credito").find('option:selected').val();
    $.ajax({
      url:"{{url('backoffice/0/tarifario/showproductocredito')}}",
      type:'GET',
      data: {
          tipo : tipo,
          idagencia : idagencia_tarifario(),
      },
      success: function (res){
        let option_select = `<option></option>`;
        var i = 1;
        $.each(res, function( key, value ) {
          option_select += `<option value="${value.id}" data-modalidad="${value.modalidad}">${value.nombre}</option>`;
          i++;
        });
        $('#idcredito_prendatario').html(option_select);
        sistema_select2({ input:'#idcredito_prendatario'});
        actualizar_nombre_tasa();

      }
    })
  }

  // Ajusta el nombre de la tasa segun la modalidad de calculo del producto:
  //  - Interes Simple    => TNM
  //  - Interes Compuesto => TEM
  //  - Sin producto      => TEM/TNC (se muestra todo)
  function actualizar_nombre_tasa(){
    let modalidad = ($("#idcredito_prendatario").find('option:selected').data('modalidad') || '').toString().toUpperCase();
    let etiqueta = 'TEM/TNC';
    if(modalidad.indexOf('SIMPLE') !== -1){
      etiqueta = 'TNM';
    } else if(modalidad.indexOf('COMPUESTO') !== -1){
      etiqueta = 'TEM';
    }
    $('#th-tem').text(etiqueta + ' %');
    $('#label-tem').text(etiqueta + ' (%):');
  }
  actualizar_nombre_tasa();
</script>    