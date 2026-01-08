<form action="javascript:;" 
    id="form_asignacioncapital"> 
    <input type="hidden" id="idresponsable_registro">
    <input type="hidden" id="idresponsable_registro_idpermiso">
    <div class="modal-body">
       <div class="row justify-content-center">
          <div class="col-sm-12 col-md-6">
              <div class="row">
                <label class="col-sm-3 col-form-label" style="text-align: right;">Agencia:</label>
                <div class="col-sm-9">
                    <select class="form-control" id="idagencia">
                      <option></option>
                      @foreach($agencias as $value)
                          <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                      @endforeach
                    </select>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-3 col-form-label" style="text-align: right;">Tipo de operación:</label>
                <div class="col-sm-3">
                    <select class="form-control" id="idtipooperacion">
                      <option></option>
                      @foreach($tipooperacions as $value)
                          <option value="{{$value->id}}">{{$value->nombre}}</option>
                      @endforeach
                    </select>
                </div>
                <div class="col-md-6" style="line-height: 1.1;">
                  <span style="color:#b71c1b;"> <span style=" text-decoration: underline;">Ret. Correc.:</span> Usar para corregir saldo negativo en incremental de Rep. institucional</span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-3 col-form-label" style="text-align: right;">Monto S/.:</label>
                <div class="col-sm-2">
                  <input type="number" class="form-control" id="monto" step="any">
                </div>
              </div>
              <div class="row">
                <label class="col-sm-3 col-form-label" style="text-align: right;">Descripción:</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="descripcion">
                </div>
              </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div id="cont_tipooperacion" style="display:none;">
              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;">Destino de Depósito / Fuente de Retiro:</label>
                <div class="col-sm-4">
                    <select class="form-control" id="idtipodestino">
                      <option></option>
                      @foreach($tipodestinos as $value)
                          <option value="{{$value->id}}">{{$value->nombre}}</option>
                      @endforeach
                    </select>
                </div>
              </div>
     
              <div id="cont_banco_n" style="display:none;">
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;">Bancos:</label>
                  <div class="col-sm-7">
                    <select id="idbanco" class="form-control" disabled>
                        <option></option>
                        @foreach($bancos as $value)
                        <option value="{{ $value->id }}">{{ $value->nombre }}: ***{{ substr($value->cuenta, -4) }}</option>
                        @endforeach
                    </select>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;">Nro Operación:</label>
                  <div class="col-sm-7">
                      <input type="text" id="numerooperacion" class="form-control" disabled>
                  </div>
                </div>

              </div>
            </div>
              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;"></label>
                <div class="col-sm-7">
                    <button type="button" class="btn btn-primary" onclick="valid_registro()"><i class="fa-solid fa-floppy-disk"></i> Registrar</button>
                </div>
              </div>
          </div>
       </div>
    </div>
</form>  
<script>
  
    sistema_select2({ input:'#idagencia',val:'{{$tienda->id}}' });
    sistema_select2({ input:'#idtipooperacion' });
    sistema_select2({ input:'#idtipodestino' });
    sistema_select2({ input:'#idbanco' });

  $("#idtipodestino").on("change", function(e) {
    
      $('#cont_banco_n').css('display','none');
      $('#numerooperacion').attr('disabled',true);
      $('#idbanco').attr('disabled',true);
      if(e.currentTarget.value==3){
          $('#cont_banco_n').css('display','block');
          $('#numerooperacion').attr('disabled',false);
          $('#idbanco').attr('disabled',false);
      }
  });
  
  $("#idtipooperacion").on("change", function(e) {
    
      $('#cont_tipooperacion').css('display','block');
      if(e.currentTarget.value==3){
          $('#cont_tipooperacion').css('display','none');
      }
  });

    function valid_registro(){
        var idtipooperacion = $('#idtipooperacion').val();
        callback({
            route: '{{ url('backoffice/'.$tienda->id.'/asignacioncapital') }}',
            method: 'POST',
            form: '#form_asignacioncapital',
            data:{
                view: 'registrar'
            }
        },
        function(resultado){
            modal({ route:"{{url('backoffice/'.$tienda->id.'/asignacioncapital/0/edit?view=valid_registro')}}&idtipooperacion="+idtipooperacion,  size: 'modal-sm'  });
        })        
    }
  
    function submit_registro(){
        callback({
            route: '{{ url('backoffice/'.$tienda->id.'/asignacioncapital') }}',
            method: 'POST',
            form: '#form_asignacioncapital',
            data:{
                view: 'registrar_insert'
            }
        },
        function(resultado){
            lista_asignacioncapital();
            load_nuevo_asignacioncapital();
        }) 
    }
</script>