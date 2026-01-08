<div id="carga_desembolsar_credito">
<form action="javascript:;" 
      onsubmit="callback({
              route: '{{ url('backoffice/'.$tienda->id.'/desembolso/'.$credito->id) }}',
              method: 'PUT',
              id: '#form_desembolsar_credito',
              carga: '#carga_desembolsar_credito',
              data:{
                  view: 'realizar_desembolo',
              }
          },
          function(res){
              lista_credito();
              verpdf('pdf_ticket');
              $('#cont_botonesdesembolso').css('display','block'); 
              $('#cont_garantias').css('display','block'); 
              $('#btn_desembolsar').css('display','none'); 
              $('#modal-close-desembolar-credito').click();
              removecarga({input:'#carga_desembolsar_credito'});
          },this)"> 

  
    <div class="modal-header" style="border-bottom: 0;">
        <h5 class="modal-title">DESEMBOLSAR CRÉDITO </h5>
        <button type="button" class="btn-close text-white" id="modal-close-desembolar-credito" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body modal-body-cualitativa">
 
          <div class="row">
            <label class="col-sm-12 col-form-label">Desembolso por:</label>
            <div class="col-sm-12">
              <select id="idformapago" class="form-control">
                  <option></option>
                  <option value="1">CAJA</option>
                  <option value="2">BANCO</option>
              </select>
            </div>
          </div>
          <div id="cont_banco_n" style="display:none;">
          <div class="row">
            <label class="col-sm-12 col-form-label">Bancos:</label>
            <div class="col-sm-12">
              <select id="idbanco" class="form-control" disabled>
                  <option></option>
                  @foreach($bancos as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}: ***{{ substr($value->cuenta, -5) }}</option>
                  @endforeach
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-12 col-form-label">Nro Operación:</label>
            <div class="col-sm-12">
              <input type="text" id="numerooperacion" class="form-control" disabled>
            </div>
          </div>
          </div>
          @if($credito->idmodalidad_credito==2)
          <div class="row">
            <div class="col-sm-4">
              <div class="row">
                  <label class="col-sm-12 col-form-label">Monto Desembolso:</label>
                  <div class="col-sm-12">
                    <input type="text" id="monto_desembolsado" value="{{$credito->monto_solicitado}}" class="form-control" disabled>
                  </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="row">
                  <label class="col-sm-12 col-form-label">Descuento de Saldo:</label>
                  <div class="col-sm-12">
                    <input type="text" id="descuento_saldo" 
                           value="{{number_format($credito->monto_solicitado-$credito_propuesta->neto_destino_credito, 2, '.', '')}}" class="form-control" disabled>
                  </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="row">
                  <label class="col-sm-12 col-form-label">Neto a Entregar:</label>
                  <div class="col-sm-12">
                    <input type="text" id="neto_entregar" value="{{$credito_propuesta->neto_destino_credito}}" class="form-control" disabled>
                  </div>
              </div>
            </div>
          </div>
          @endif
      
      <div class="row mt-1">
        <div class="col" style="flex: 0 0 0%;">
          <button type="submit" class="btn btn-success"><i class="fa-solid fa-check"></i> EJECUTAR DESEMBOLSO</button>
        </div>
        <div class="col" style="flex: 1 0 0%;">
          <div id="success-message" class="alert alert-success d-none" style="text-align:left;"></div>
        </div>
        <div class="col" style="flex: 0 0 0%;">
          <button type="button" class="btn btn-danger" id="close_confirmacionproceso" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i> SALIR</button>
        </div>
      </div>
</form> 
</div>
<script>
  
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