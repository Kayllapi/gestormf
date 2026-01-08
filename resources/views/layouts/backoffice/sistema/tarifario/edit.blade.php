<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/tarifario/'.$tarifario->id) }}',
        method: 'PUT',
          data:{
              view: 'editar',
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
                 <option value="{{ $tarifario->idcredito_prendatario }}">{{ $tarifario->nombrecredito }}</option>
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
              <input type="number" class="form-control" id="monto" value="{{ $tarifario->monto }}">
            </div>
          </div>
           <div class="row">
            <label class="col-sm-3 col-form-label">Cuotas (<=):</label>
            <div class="col-sm-6">
              <input type="number" class="form-control" step="any" id="cuotas" value="{{ $tarifario->cuotas }}">
            </div>
          </div>
           <div class="row">
            <label class="col-sm-3 col-form-label">TEM (%):</label>
            <div class="col-sm-6">
              <input type="number" class="form-control" step="any" id="tem" value="{{ $tarifario->tem }}">
            </div>
          </div>
          <div class="row">
            <label class="col-sm-3 col-form-label">Servicios/Otros (%):</label>
            <div class="col-sm-6">
              <input type="number" class="form-control" step="any" id="cargos_otros" value="{{ $tarifario->cargos_otros }}">
            </div>
          </div>
          <div class="row mt-1">
            <label class="col-sm-3"></label>
            <div class="col-sm-6">
              <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
              <button type="button" onclick="eliminar_tarifario()" class="btn btn-danger"><i class="fa-solid fa-trash"></i> ELIMINAR</button>
            </div>
          </div>
          
         
        </div>
      </div>
    </div>
</form>  
<script>
  @include('app.nuevosistema.select2',['input'=>'#idforma_credito', 'val' => $tarifario->idforma_credito ])
  @include('app.nuevosistema.select2',['input'=>'#idcredito_prendatario', 'val' => $tarifario->idcredito_prendatario ])
  @include('app.nuevosistema.select2',['input'=>'#idforma_pago_credito', 'val' => $tarifario->idforma_pago_credito ])
  
  $("#idforma_credito").on("change", function(e) {
    carga_producto_credito();
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
  function eliminar_tarifario(){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/tarifario/'.$tarifario->id.'/edit?view=eliminar')}}" });  
  }
</script>    