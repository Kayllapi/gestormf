<div id="carga_movimientointernodinero_deposito3"> 
<form action="javascript:;" 
    id="form_movimientointernodinero_deposito3"> 
    <div class="modal-body">
        <div class="row justify-content-center">
          <div class="col-sm-12 col-md-7">
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Destino de Dep.:</label>
                <div class="col-sm-8">
                    <select class="form-control" id="idfuenteretiro_deposito3" disabled>
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
                  <input type="number" class="form-control" id="monto_deposito3" step="any" disabled>
                </div>
              </div>
          </div>
          <div class="col-sm-12 col-md-5">
              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;">Descripción:</label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" id="descripcion_deposito3" disabled>
                </div>
              </div>
              <!--div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;"></label>
                <div class="col-sm-7">
                    <button type="button" class="btn btn-primary" onclick="valid_registro_deposito3()"><i class="fa-solid fa-floppy-disk"></i> Registrar</button>
                </div>
              </div-->
          </div>
        </div>
    </div>
</form>  
</div>
<script>
  
    sistema_select2({ input:'#idfuenteretiro_deposito3' });
    sistema_select2({ input:'#idbanco_deposito3' });

    function valid_registro_deposito3(){
      var idfuenteretiro_deposito3 = $('#idfuenteretiro_deposito3').val();
      callback({
          route: '{{ url('backoffice/'.$tienda->id.'/movimientointernodinero') }}',
          method: 'POST',
          form: '#form_movimientointernodinero_deposito3',
          carga: '#carga_movimientointernodinero_deposito3',
          data:{
              view: 'registrar_deposito3'
          }
      },
      function(resultado){
          removecarga({input:'#carga_movimientointernodinero_deposito3'});
          modal({ route:"{{url('backoffice/'.$tienda->id.'/movimientointernodinero/0/edit?view=valid_registro_deposito3')}}&idfuenteretiro_deposito3="+idfuenteretiro_deposito3,  size: 'modal-sm'  });  
      })
    }
  
    function submit_registro_deposito3(){
      callback({
          route: '{{ url('backoffice/'.$tienda->id.'/movimientointernodinero') }}',
          method: 'POST',
          form: '#form_movimientointernodinero_deposito3',
          carga: '#carga_movimientointernodinero_deposito3',
          data:{
              view: 'registrar_deposito3_insert'
          }
      },
      function(resultado){
          lista_movimientointernodinero_deposito3();
          load_nuevo_movimientointernodinero_deposito3();
      })
    }
</script>