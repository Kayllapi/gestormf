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
              <label class="chk">
                  <input type="checkbox" name="conyugue_titular" id="participarconyugue_titular">
                  <span class="checkmark"></span>
                  Participa Pareja <label style="color: #c40000;">(Créd. Prendarios y No Prendarios - CP y CNP)</label>
              </label>
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
              <label class="chk">
                  <input type="checkbox" name="conyugue_aval" id="participarconyugue_aval">
                  <span class="checkmark"></span>
                  Participa Pareja <label style="color: #c40000;">(Solo para Créditos No Prendarios - CNP)</label>
              </label>
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
                <option></option>
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
              </select>
            </div>
          </div>
          
          <div class="row mt-1">
            <label class="col-sm-4"></label>
            <div class="col-sm-8">
              <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Generar Crédito</button>
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
  
  // Mora del titular (días de atraso más alto entre sus créditos desembolsados).
  // Si supera 8 días, no se puede generar un crédito No Prendario (CNP); solo Prendario (CP).
  var mora_titular = 0;
  var titular_bloquea_cnp = false;

  function validar_cnp_por_mora(){
      let idforma_credito = $("#idforma_credito").find('option:selected').val();
      if(idforma_credito == 2 && titular_bloquea_cnp){
          let mensaje = 'El cliente tiene un crédito con '+mora_titular+' días de atraso (mayor a 8). No puede generar un crédito No Prendario (CNP); solo Prendario (CP).';
          modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });
          $('#idforma_credito').val(null).trigger("change");
      }
  }

  $("#idcliente").on("change", function(e) {
      let idcliente = $("#idcliente").find('option:selected').val();
      verificar_credito_refinanciado();
      $.ajax({
        url:"{{url('backoffice/0/credito/show_verificarcliente')}}",
        type:'GET',
        data: {
          idcliente: idcliente
        },
        success: function (res){
          
          if(res['resultado']=='EN LISTA NEGRA'){
              mensaje = 'Cliente Observado';
              modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });
              $('#idcliente').val(null).trigger("change");
          }
          
          mora_titular = parseInt(res['mora']) || 0;
          titular_bloquea_cnp = (res['bloquea_cnp'] == true || res['bloquea_cnp'] == 1);

          valida_identificacion(res['identificacion'])
          validar_cnp_por_mora();
        }
      })
  });
  function valida_identificacion(identificacion){
      // documento de relleno (00000000) no se valida
      if(/^0+$/.test(identificacion)){
          return;
      }

      $.ajax({
          url:"{{url('backoffice/'.$tienda->id.'/usuario/show_valida_identificacion')}}",
          type:'GET',
          data: {
              identificacion : identificacion
          },
          success: function (respuesta){
              if(respuesta.existe){
                  var mensaje = respuesta.mensaje;
                  modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+encodeURIComponent(mensaje), size: 'modal-sm' });
              }
          }
      });
  }
  
  $("#idforma_credito").on("change", function(e) {
      let idforma_credito = $("#idforma_credito").find('option:selected').val();
      verificar_credito_refinanciado();
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

      validar_cnp_por_mora();
  });
   function verificar_credito_refinanciado(){
     let idcliente = $("#idcliente").find('option:selected').val();
     let idforma_credito = $("#idforma_credito").find('option:selected').val();

     if(idcliente == undefined || idcliente == '' || idforma_credito != 2){
        return;
     }

     $.ajax({
        url:"{{url('backoffice/0/credito/show_verificarcreditorefinanciado')}}",
        type:'GET',
        data: {
          idcliente: idcliente,
          idforma_credito: idforma_credito
        },
        success: function (res){

          if(res['resultado']=='TIENE REFINANCIADO'){
              mensaje = 'El cliente no puede generar un crédito No Prendario, tiene un crédito refinanciado (N° de cuenta '+res['cuenta']+').';
              modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });
              $('#idforma_credito').val(null).trigger("change");
          }

        }
     })
   }
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