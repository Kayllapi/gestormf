<div id="carga_movimientointernodinero_retiro1"> 
<form action="javascript:;" 
    id="form_movimientointernodinero_retiro1"> 
    <input type="hidden" id="idresponsable_retiro1">
    <input type="hidden" id="idresponsable_permiso_retiro1">
    <div class="modal-body">
        <div class="row justify-content-center">
          <div class="col-sm-12 col-md-7">
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Fuente de Ret.:</label>
                <div class="col-sm-8">
                    <select class="form-control" id="idfuenteretiro_retiro1">
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
                  <input type="number" class="form-control" id="monto_retiro1" step="any">
                </div>
              </div>
              <div style="display:none;" id="cont_banco_retiro1">
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Bancos:</label>
                <div class="col-sm-8">
                    <select id="idbanco_retiro1" class="form-control">
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
              <div style="display:none;" id="cont_numerooperacion_retiro1">
              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;">Nro Ope.:</label>
                <div class="col-sm-7">
                    <input type="text" id="numerooperacion_retiro1" class="form-control">
                </div>
              </div>
              </div>
              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;">Descripción:</label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" id="descripcion_retiro1">
                </div>
              </div>
              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;"></label>
                <div class="col-sm-7">
                  {{-- @if ($apertura_caja) --}}
                    <button type="button" class="btn btn-primary" onclick="valid_registro_retiro1()"><i class="fa-solid fa-floppy-disk"></i> Registrar</button>
                  {{-- @endif --}}
                </div>
              </div>
          </div>
        </div>
    </div>
</form> 
</div>
<script>
  
    sistema_select2({ input:'#idfuenteretiro_retiro1' });
    sistema_select2({ input:'#idbanco_retiro1' });

    $("#idfuenteretiro_retiro1").on("change", function(e) {
        $('#cont_banco_retiro1').css('display','none');
        $('#cont_numerooperacion_retiro1').css('display','none');
        if(e.currentTarget.value==7 ||e.currentTarget.value==9){
            $('#cont_banco_retiro1').css('display','block');
            $('#cont_numerooperacion_retiro1').css('display','block');
        }
    });

    function valid_registro_retiro1(){
      var idfuenteretiro_retiro1 = $('#idfuenteretiro_retiro1').val();
      callback({
          route: '{{ url('backoffice/'.$tienda->id.'/cvmovimientointernodinero') }}',
          method: 'POST',
          form: '#form_movimientointernodinero_retiro1',
          carga: '#carga_movimientointernodinero_retiro1',
          data:{
              view: 'registrar_retiro1'
          }
      },
      function(resultado){
          removecarga({input:'#carga_movimientointernodinero_retiro1'});
          modal({ route:"{{url('backoffice/'.$tienda->id.'/cvmovimientointernodinero/0/edit?view=valid_registro_retiro1')}}&idfuenteretiro_retiro1="+idfuenteretiro_retiro1,  size: 'modal-sm'  });  
      })
    }
  
    function submit_registro_retiro1(){
      callback({
          route: '{{ url('backoffice/'.$tienda->id.'/cvmovimientointernodinero') }}',
          method: 'POST',
          form: '#form_movimientointernodinero_retiro1',
          carga: '#carga_movimientointernodinero_retiro1',
          data:{
              view: 'registrar_retiro1_insert'
          }
      },
      function(resultado){
          lista_movimientointernodinero_retiro1();
          load_nuevo_movimientointernodinero_retiro1();
          lista_movimientointernodinero_deposito1();
          load_nuevo_movimientointernodinero_deposito1();
      })
    }
</script>