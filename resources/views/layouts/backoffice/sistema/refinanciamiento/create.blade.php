<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/credito') }}',
        method: 'POST',
        data:{
            view: 'registrar'
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
            <label class="col-sm-4 col-form-label" style="text-align: right;">Crédito:</label>
            <div class="col-sm-8">
              <i class="fa fa-search" style="float: right;margin-right: -16px;margin-top: 10px;"></i>
              <select class="form-control" id="idcliente">
                <option></option>
              </select>
            </div>
          </div>

          <div class="row mt-1">
            <label class="col-sm-4"></label>
            <div class="col-sm-8">
              <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Refinanciar Crédito</button>
            </div>
          </div>
          
         
        </div>
      </div>
    </div>
</form>  
<script>
  sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idcliente' });
  sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idaval' });
  
  sistema_select2({ input:'#idforma_credito' });
  sistema_select2({ input:'#idcredito_prendatario' });
  sistema_select2({ input:'#idforma_pago_credito' });
  sistema_select2({ input:'#idtipo_destino_credito' });
  sistema_select2({ input:'#idtipo_operacion_credito' });
  sistema_select2({ input:'#idmodalidad_credito' });
  
  $("#idcliente").on("change", function(e) {
      let idcliente = $("#idcliente").find('option:selected').val();
      $.ajax({
        url:"{{url('backoffice/0/credito/show_verificarcliente')}}",
        type:'GET',
        data: {
          idcliente: idcliente
        },
        success: function (res){
          
          if(res['resultado']=='EN LISTA NEGRA'){
              alert('El usuario esta en lista negra por "'+res['motivo']+'".');
              $('#idcliente').val(null).trigger("change");
          }

        }
      })
  });
  
  $("#idforma_credito").on("change", function(e) {
      let idforma_credito = $("#idforma_credito").find('option:selected').val();
      show_producto_credito();
    
      var html_idmodalidad_credito = '<option></option>';
      
      if(idforma_credito==1){
           html_idmodalidad_credito += '<option value="1">Regular</option>';
      }else{
          @foreach($modalidad_credito as $value)
           html_idmodalidad_credito += '<option value="{{ $value->id }}">{{ $value->nombre }}</option>';
          @endforeach
      }
                           
      $('#idmodalidad_credito').html(html_idmodalidad_credito);
  });
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
          sistema_select2({ input:'#idcredito_prendatario'});

        }
      })
    }
</script>    