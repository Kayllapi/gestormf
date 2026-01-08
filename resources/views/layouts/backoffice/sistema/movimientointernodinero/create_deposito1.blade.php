<div id="carga_movimientointernodinero_deposito1"> 
<form action="javascript:;" 
    id="form_movimientointernodinero_deposito1"> 
    <div class="modal-body">
        <div class="row justify-content-center">
          <div class="col-sm-12 col-md-7">
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Destino de Dep.:</label>
                <div class="col-sm-8">
                    <select class="form-control" id="idfuenteretiro_deposito1" disabled>
                      <option></option>
                      @foreach($fuenteretiros as $value)
                          <option value="{{$value->id}}">{{$value->nombre}}</option>
                      @endforeach
                    </select>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Monto S/.:</label>
                <div class="col-sm-8">
                  <input type="number" class="form-control" id="monto_deposito1" step="any" disabled>
                </div>
              </div>
              <div style="display:none;" id="cont_banco_deposito1">
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Bancos:</label>
                <div class="col-sm-8">
                    <select id="idbanco_deposito1" class="form-control" disabled>
                        <option></option>
                        @foreach($bancos as $value)
                        <option value="{{ $value->id }}">{{ $value->nombre }}: ***{{ substr($value->cuenta, -4) }}</option>
                        @endforeach
                    </select>
                </div>
              </div>
              </div>
          </div>
          <div class="col-sm-12 col-md-5">
              <div style="display:none;" id="cont_numerooperacion_deposito1">
              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;" disabled>Nro Ope.:</label>
                <div class="col-sm-7">
                    <input type="text" id="numerooperacion_deposito1" class="form-control">
                </div>
              </div>
              </div>
              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;">Descripción:</label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" id="descripcion_deposito1" disabled>
                </div>
              </div>
              <!--div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;"></label>
                <div class="col-sm-7">
                    <button type="button" class="btn btn-primary" onclick="valid_registro_deposito1()"><i class="fa-solid fa-floppy-disk"></i> Registrar</button>
                </div>
              </div-->
          </div>
        </div>
    </div>
</form>  
</div>
<script>
  
    sistema_select2({ input:'#idfuenteretiro_deposito1' });
    sistema_select2({ input:'#idbanco_deposito1' });

    $("#idfuenteretiro_deposito1").on("change", function(e) {
        $('#cont_banco_deposito1').css('display','none');
        $('#cont_numerooperacion_deposito1').css('display','none');
        if(e.currentTarget.value==2 ||e.currentTarget.value==4){
            $('#cont_banco_deposito1').css('display','block');
            $('#cont_numerooperacion_deposito1').css('display','block');
        }
    });

    function valid_registro_deposito1(){
      var idfuenteretiro_deposito1 = $('#idfuenteretiro_deposito1').val();
      callback({
          route: '{{ url('backoffice/'.$tienda->id.'/movimientointernodinero') }}',
          method: 'POST',
          form: '#form_movimientointernodinero_deposito1',
          carga: '#carga_movimientointernodinero_deposito1',
          data:{
              view: 'registrar_deposito1'
          }
      },
      function(resultado){
          removecarga({input:'#carga_movimientointernodinero_deposito1'});
          modal({ route:"{{url('backoffice/'.$tienda->id.'/movimientointernodinero/0/edit?view=valid_registro_deposito1')}}&idfuenteretiro_deposito1="+idfuenteretiro_deposito1,  size: 'modal-sm'  });  
      })
    }
  
    function submit_registro_deposito1(){
      callback({
          route: '{{ url('backoffice/'.$tienda->id.'/movimientointernodinero') }}',
          method: 'POST',
          form: '#form_movimientointernodinero_deposito1',
          carga: '#carga_movimientointernodinero_deposito1',
          data:{
              view: 'registrar_deposito1_insert'
          }
      },
      function(resultado){
          lista_movimientointernodinero_deposito1();
          load_nuevo_movimientointernodinero_deposito1();
      })
    }
</script>