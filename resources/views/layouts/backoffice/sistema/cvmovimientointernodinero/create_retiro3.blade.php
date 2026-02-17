<div id="carga_movimientointernodinero_retiro3">
<form action="javascript:;" 
    id="form_movimientointernodinero_retiro3"> 
    <input type="hidden" id="idresponsable_retiro3">
    <input type="hidden" id="idresponsable_permiso_retiro3">
    <div class="modal-body">
        <div class="row justify-content-center">
          <div class="col-sm-12 col-md-7">
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Fuente de Ret.:</label>
                <div class="col-sm-8">
                    <select class="form-control" id="idfuenteretiro_retiro3">
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
                  <input type="number" class="form-control" id="monto_retiro3" step="any">
                </div>
              </div>
              @php
                  $esHoy = \Carbon\Carbon::parse($validacionDiaria['aperturacaja_existe_ultima']->fecharegistro)->isToday();
              @endphp
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Fecha 
                  <span style="background-color: #ffc107;">Regul.</span> Cierre:</label>
                <div class="col-sm-8">
                  <input type="date" class="form-control" id="fecharegularizacion" 
                  @if($esHoy)
                    disabled
                  @elseif(!$validacionDiaria['cierre_caja'])
                  @elseif (!$apertura_caja)
                      disabled
                  @endif>
                </div>
              </div>
          </div>
          <div class="col-sm-12 col-md-5">
              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;">Descripción:</label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" id="descripcion_retiro3">
                </div>
              </div>
              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;"></label>
                <div class="col-sm-7">
                    <button type="button" class="btn btn-primary" onclick="valid_registro_retiro3()"><i class="fa-solid fa-floppy-disk"></i> Registrar</button>
                </div>
              </div>
          </div>
        </div>
    </div>
</form> 
</div>
<script>
  
    sistema_select2({ input:'#idfuenteretiro_retiro3' });
    sistema_select2({ input:'#idbanco_retiro3' });

    function valid_registro_retiro3(){
      var idfuenteretiro_retiro3 = $('#idfuenteretiro_retiro3').val();
      callback({
          route: '{{ url('backoffice/'.$tienda->id.'/cvmovimientointernodinero') }}',
          method: 'POST',
          form: '#form_movimientointernodinero_retiro3',
          carga: '#carga_movimientointernodinero_retiro3',
          data:{
              view: 'registrar_retiro3'
          }
      },
      function(resultado){
          removecarga({input:'#carga_movimientointernodinero_retiro3'});
          modal({ route:"{{url('backoffice/'.$tienda->id.'/cvmovimientointernodinero/0/edit?view=valid_registro_retiro3')}}&idfuenteretiro_retiro3="+idfuenteretiro_retiro3,  size: 'modal-sm'  });  
      })
    }
  
    function submit_registro_retiro3(){
      callback({
          route: '{{ url('backoffice/'.$tienda->id.'/cvmovimientointernodinero') }}',
          method: 'POST',
          form: '#form_movimientointernodinero_retiro3',
          carga: '#carga_movimientointernodinero_retiro3',
          data:{
              view: 'registrar_retiro3_insert'
          }
      },
      function(resultado){
          lista_movimientointernodinero_retiro3();
          load_nuevo_movimientointernodinero_retiro3();
          lista_movimientointernodinero_deposito3();
          load_nuevo_movimientointernodinero_deposito3();
      })
    }
</script>