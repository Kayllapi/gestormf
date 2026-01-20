    <div class="modal-body">
       <div class="row justify-content-center">
          <div class="col-sm-12 col-md-6">
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Monto S/.:</label>
                <div class="col-sm-2">
                  <input type="number" class="form-control" id="monto" value="{{$gastoadministrativooperativo->monto}}" step="any" disabled>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Descripción:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="descripcion" value="{{$gastoadministrativooperativo->descripcion}}" disabled>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Comprobante:</label>
                <div class="col-sm-4">
                  <select class="form-control" id="sustento_comprobante" disabled>
                    <option></option>
                    @foreach($s_sustento_comprobante as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">N° y Detalle de Comp.:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="sustento_descripcion" value="{{$gastoadministrativooperativo->sustento_descripcion}}" disabled>
                </div>
              </div>
          </div>
          <div class="col-sm-12 col-md-6">
            
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Pago por:</label>
                <div class="col-sm-4">
                    <select id="idformapago" class="form-control" disabled>
                        <option></option>
                        @foreach($credito_tipoformapago as $value)
                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                        @endforeach
                    </select>
                </div>
              </div>
     
              @if($gastoadministrativooperativo->idformapago==2)
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
                      <input type="text" id="numerooperacion" class="form-control" value="{{$gastoadministrativooperativo->numerooperacion}}" disabled>
                  </div>
                </div>

              @endif
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;"></label>
                <div class="col-sm-8">
                    <button type="button" onclick="eliminar_gastoadministrativooperativo()" class="btn btn-danger"><i class="fa-solid fa-trash"></i> ELIMINAR</button>
                </div>
              </div>
            
          </div>
       </div>
    </div>
<script>

  @include('app.nuevosistema.select2',['input'=>'#sustento_comprobante', 'val' => $gastoadministrativooperativo->s_idsustento_comprobante ])
  @include('app.nuevosistema.select2',['input'=>'#idformapago', 'val' => $gastoadministrativooperativo->idformapago ])
  @include('app.nuevosistema.select2',['input'=>'#idbanco', 'val' => $gastoadministrativooperativo->idbanco ])
  
  function eliminar_gastoadministrativooperativo(){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/cvgastoadministrativooperativo/'.$gastoadministrativooperativo->id.'/edit?view=eliminar')}}",  size: 'modal-sm'  });  
  }
</script>