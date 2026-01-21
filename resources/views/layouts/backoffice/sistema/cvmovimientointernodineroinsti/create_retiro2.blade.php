<div id="carga_movimientointernodinero_retiro2">
<form action="javascript:;" 
    id="form_movimientointernodinero_retiro2"> 
    <input type="hidden" id="idresponsable_retiro2">
    <input type="hidden" id="idresponsable_permiso_retiro2">
    <div class="modal-body">
        <div class="row justify-content-center">
          <div class="col-sm-12 col-md-7">
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Fuente de Ret.:</label>
                <div class="col-sm-8">
                    <select class="form-control" id="idfuenteretiro_retiro2">
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
                  <input type="number" class="form-control" id="monto_retiro2" step="any">
                </div>
              </div>
              <div style="display:none;" id="cont_banco_retiro2">
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Bancos:</label>
                <div class="col-sm-8">
                    <select id="idbanco_retiro2" class="form-control">
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
              <div style="display:none;" id="cont_numerooperacion_retiro2">
              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;">Nro Ope.:</label>
                <div class="col-sm-7">
                    <input type="text" id="numerooperacion_retiro2" class="form-control">
                </div>
              </div>
              </div>
              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;">Descripción:</label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" id="descripcion_retiro2">
                </div>
              </div>
              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;"></label>
                <div class="col-sm-7">
                    <button type="button" class="btn btn-primary" onclick="valid_registro_retiro2()"><i class="fa-solid fa-floppy-disk"></i> Registrar</button>
                </div>
              </div>
          </div>
        </div>
    </div>
</form>  
</div>
<script>
  
    sistema_select2({ input:'#idfuenteretiro_retiro2' });
    sistema_select2({ input:'#idbanco_retiro2' });

    $("#idfuenteretiro_retiro2").on("change", function(e) {
        $('#cont_banco_retiro2').css('display','none');
        $('#cont_numerooperacion_retiro2').css('display','none');
        if(e.currentTarget.value==10){
            $('#cont_banco_retiro2').css('display','block');
            $('#cont_numerooperacion_retiro2').css('display','block');
        }
    });

    function valid_registro_retiro2(){
      var idfuenteretiro_retiro2 = $('#idfuenteretiro_retiro2').val();
      callback({
          route: '{{ url('backoffice/'.$tienda->id.'/cvmovimientointernodineroinsti') }}',
          method: 'POST',
          form: '#form_movimientointernodinero_retiro2',
          carga: '#carga_movimientointernodinero_retiro2',
          data:{
              view: 'registrar_retiro2'
          }
      },
      function(resultado){
          removecarga({input:'#carga_movimientointernodinero_retiro2'});
          modal({ route:"{{url('backoffice/'.$tienda->id.'/cvmovimientointernodineroinsti/0/edit?view=valid_registro_retiro2')}}&idfuenteretiro_retiro2="+idfuenteretiro_retiro2,  size: 'modal-sm'  });  
      })
    }
  
    function submit_registro_retiro2(){
      callback({
          route: '{{ url('backoffice/'.$tienda->id.'/cvmovimientointernodineroinsti') }}',
          method: 'POST',
          form: '#form_movimientointernodinero_retiro2',
          carga: '#carga_movimientointernodinero_retiro2',
          data:{
              view: 'registrar_retiro2_insert'
          }
      },
      function(resultado){
          lista_movimientointernodinero_retiro2();
          load_nuevo_movimientointernodinero_retiro2();
          lista_movimientointernodinero_deposito2();
          load_nuevo_movimientointernodinero_deposito2();
      })
    }
</script>