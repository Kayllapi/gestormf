<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/credito/'.$credito->id) }}',
        method: 'PUT',
        data:{
            view: 'editar',
        }
    },
    function(resultado){
        lista_credito();
        load_nuevo_credito();
    },this)"> 
    <div class="modal-body">
      <div class="row justify-content-center">
        <div class="col-sm-12 col-md-7">
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">Nombre del Titular / Razón Social:</label>
            <div class="col-sm-8">
              <select class="form-control" id="idcliente">
                <option></option>
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;"></label>
            <div class="col-sm-8">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="conyugue_titular" id="participarconyugue_titular" <?php echo $credito->participarconyugue_titular=='on'?'checked':''?>>
                <label class="form-check-label" for="participarconyugue_titular" style="margin-top: 0;">
                  Participar con Conyugue
                </label>
              </div>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">Aval:</label>
            <div class="col-sm-8">
              <select class="form-control" id="idaval">
                <option></option>
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;"></label>
            <div class="col-sm-8">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="conyugue_aval" id="participarconyugue_aval" <?php echo $credito->participarconyugue_aval=='on'?'checked':''?>>
                <label class="form-check-label" for="participarconyugue_aval" style="margin-top: 0;">
                  Participar con Conyugue
                </label>
              </div>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">Forma de Crédito:</label>
            <div class="col-sm-8">
              <select class="form-control" id="idforma_credito">
                <option></option>
                @foreach($forma_credito as $value)
                  
                  @if( val_acceso_especial('show-credito-prendario') && $value->id == 1)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                  @endif
                  @if( val_acceso_especial('show-credito-no-prendario') && $value->id == 2)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                  @endif
                @endforeach
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">Producto:</label>
            <div class="col-sm-8">
              <select class="form-control" id="idcredito_prendatario">
                <option value=""></option>
<!--                 <option value="{{ $credito->idcredito_prendatario }}">{{ $credito->nombreproductocredito }}</option> -->
              </select>
            </div>
          </div>
         
        </div>
        <div class="col-sm-12 col-md-5">
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">Tipo de Cliente:</label>
            <div class="col-sm-8">
              <select class="form-control" id="idtipo_operacion_credito">
                <option></option>
                @foreach($tipo_operacion_credito as $value)
                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">Destino de Crédito:</label>
            <div class="col-sm-8">
              <select class="form-control" id="idtipo_destino_credito">
                <option></option>
                @foreach($tipo_destino_credito as $value)
                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label" style="text-align: right;">Modalidad de Crédito:</label>
            <div class="col-sm-8">
              <select class="form-control" id="idmodalidad_credito">
                <option></option>
                @foreach($modalidad_credito as $value)
                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
              </select>
            </div>
          </div>
          
          <div class="row mt-1">
            <label class="col-sm-4"></label>
            <div class="col-sm-8">
              <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CRÉDITO</button>
<!--               <button type="button" class="btn btn-danger me-1" onclick="eliminar_credito('eliminar')">ELIMINAR CRÉDITO</button> -->
            </div>
          </div>
          
         
        </div>
      </div>
    </div>
</form>  
<script>
  sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idcliente', val: '{{ $credito->idcliente }}' });
  sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idaval', val: '{{ $credito->idaval }}' });
  
  @include('app.nuevosistema.select2',['input'=>'#idforma_credito', 'val' => $credito->idforma_credito ])
  @include('app.nuevosistema.select2',['input'=>'#idcredito_prendatario' ])
  
  @include('app.nuevosistema.select2',['input'=>'#idforma_pago_credito', 'val' => $credito->idforma_pago_credito ])
  @include('app.nuevosistema.select2',['input'=>'#idtipo_destino_credito', 'val' => $credito->idtipo_destino_credito ])
  @include('app.nuevosistema.select2',['input'=>'#idtipo_operacion_credito', 'val' => $credito->idtipo_operacion_credito ])
  @include('app.nuevosistema.select2',['input'=>'#idmodalidad_credito', 'val' => $credito->idmodalidad_credito ])
  
  $("#idforma_credito").on("change", function(e) {
    show_producto_credito();
  });
  show_producto_credito();
  function show_producto_credito(){
     let tipo = $("#idforma_credito").find('option:selected').val();
      $.ajax({
        url:"{{url('backoffice/0/credito/show_producto_credito')}}",
        type:'GET',
        data: {
          tipo: tipo
        },
        success: function (res){
          
          let option_select = `<option></option>`;
          var i = 1;
          $.each(res, function( key, value ) {
            option_select += `<option value="${value.id}">${value.nombre}</option>`;
            i++;
          });
          $('#idcredito_prendatario').html(option_select);
          sistema_select2({ input:'#idcredito_prendatario', val: '{{ $credito->idcredito_prendatario }}'});

        }
      })
    }
//   function eliminar_credito(vista){
//     modal({ route:"{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=')}}"+vista });  
//   }
</script>    