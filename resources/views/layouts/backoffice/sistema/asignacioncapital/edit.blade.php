    <div class="modal-body">
       <div class="row justify-content-center">
          <div class="col-sm-12 col-md-6">
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Agencia:</label>
                <div class="col-sm-8">
                    <select class="form-control" id="idagencia" disabled>
                      <option></option>
                      @foreach($agencias as $value)
                          <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                      @endforeach
                    </select>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Tipo de operación:</label>
                <div class="col-sm-4">
                    <select class="form-control" id="idtipooperacion" disabled>
                      <option></option>
                      @foreach($tipooperacions as $value)
                          <option value="{{$value->id}}">{{$value->nombre}}</option>
                      @endforeach
                    </select>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Monto S/.:</label>
                <div class="col-sm-2">
                  <input type="number" class="form-control" id="monto" value="{{$asignacioncapital->monto}}" step="any" disabled>
                </div>
                <label class="col-sm-2 col-form-label" style="text-align: right;">Descripción:</label>
                <div class="col-sm-4">
                  <input type="text" class="form-control" id="descripcion" value="{{$asignacioncapital->descripcion}}" disabled>
                </div>
              </div>
          </div>
          <div class="col-sm-12 col-md-6">
            @if($asignacioncapital->idtipooperacion==1 || $asignacioncapital->idtipooperacion==2)

              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;">Destino/Fuente Depósito/Retiro:</label>
                <div class="col-sm-4">
                    <select class="form-control" id="idtipodestino" disabled>
                      <option></option>
                      @foreach($tipodestinos as $value)
                          <option value="{{$value->id}}">{{$value->nombre}}</option>
                      @endforeach
                    </select>
                </div>
              </div>
     
              @if($asignacioncapital->idtipodestino==3)
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;">Bancos:</label>
                  <div class="col-sm-7">
                    <select id="idbanco" class="form-control" disabled>
                        <option></option>
                        @foreach($bancos as $value)
                        <option value="{{ $value->id }}">{{ $value->nombre }}: ***{{ substr($value->cuenta, -5) }}</option>
                        @endforeach
                    </select>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;">Nro Operación:</label>
                  <div class="col-sm-7">
                      <input type="text" id="numerooperacion" class="form-control" value="{{$asignacioncapital->numerooperacion}}" disabled>
                  </div>
                </div>

              @endif
           @endif
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;"></label>
                <div class="col-sm-8">
                    <button type="button" onclick="eliminar_asignacioncapital()" class="btn btn-danger"><i class="fa-solid fa-trash"></i> ELIMINAR</button>
                </div>
              </div>
            
          </div>
       </div>
    </div>
<script>

  @include('app.nuevosistema.select2',['input'=>'#idagencia', 'val' => $asignacioncapital->idtienda ])
  @include('app.nuevosistema.select2',['input'=>'#idtipooperacion', 'val' => $asignacioncapital->idtipooperacion ])
  @include('app.nuevosistema.select2',['input'=>'#idtipodestino', 'val' => $asignacioncapital->idtipodestino ])
  @include('app.nuevosistema.select2',['input'=>'#idbanco', 'val' => $asignacioncapital->idbanco ])
  
  function eliminar_asignacioncapital(){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/asignacioncapital/'.$asignacioncapital->id.'/edit?view=eliminar')}}",  size: 'modal-sm'  });  
  }
</script>