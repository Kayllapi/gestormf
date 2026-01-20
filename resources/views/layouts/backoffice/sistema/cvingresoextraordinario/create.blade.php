<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/cvingresoextraordinario') }}',
        method: 'POST',
        data:{
            view: 'registrar'
        }
    },
    function(resultado){
        lista_ingresoextraordinario();
        load_nuevo_ingresoextraordinario();
    },this)"> 
    <div class="modal-body">
       <div class="row justify-content-center">
          <div class="col-sm-12 col-md-6">
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Monto S/.:</label>
                <div class="col-sm-2">
                  <input type="number" class="form-control" id="monto" step="any">
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Descripción:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="descripcion">
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Sustento:</label>
                <div class="col-sm-4">
                  <select class="form-control" id="sustento_comprobante">
                    <option></option>
                    @foreach($s_sustento_comprobante as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
          </div>
          <div class="col-sm-12 col-md-6">
            
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Pago por:</label>
                <div class="col-sm-4">
                    <select id="idformapago" class="form-control">
                        <option></option>
                        @foreach($credito_tipoformapago as $value)
                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                        @endforeach
                    </select>
                </div>
              </div>
     
              <div id="cont_banco_n" style="display:none;">
                <div class="row">
                  <label class="col-sm-4 col-form-label" style="text-align: right;">Bancos:</label>
                  <div class="col-sm-8">
                    <select id="idbanco" class="form-control" disabled>
                        <option></option>
                        @foreach($bancos as $value)
                        <option value="{{ $value->id }}">{{ $value->nombre }}: ***{{ substr($value->cuenta, -5) }}</option>
                        @endforeach
                    </select>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-4 col-form-label" style="text-align: right;">Nro Operación:</label>
                  <div class="col-sm-8">
                      <input type="text" id="numerooperacion" class="form-control" disabled>
                  </div>
                </div>

              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;"></label>
                <div class="col-sm-8">
                    <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Registrar</button>
                </div>
              </div>
            
          </div>
       </div>
    </div>
</form>  
<script>
  
    sistema_select2({ input:'#sustento_comprobante' });
    sistema_select2({ input:'#idformapago', val: 1 });
    sistema_select2({ input:'#idbanco' });

  $("#idformapago").on("change", function(e) {
    
      $('#cont_banco_n').css('display','none');
      $('#numerooperacion').attr('disabled',true);
      $('#idbanco').attr('disabled',true);
      if(e.currentTarget.value==2){
          $('#cont_banco_n').css('display','block');
          $('#numerooperacion').attr('disabled',false);
          $('#idbanco').attr('disabled',false);
      }
  });
</script>