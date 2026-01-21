    <div class="modal-body">
       <div class="row justify-content-center">
          <div class="col-sm-12 col-md-7">
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Destino de Dep.:</label>
                <div class="col-sm-8">
                    <select class="form-control" id="idfuenteretiro_deposito2" disabled>
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
                  <input type="number" class="form-control" id="monto_deposito2" value="{{$movimientointernodinero->monto}}" step="any" disabled>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label" style="text-align: right;">Bancos:</label>
                <div class="col-sm-8">
                    <select id="idbanco_deposito2" class="form-control" disabled>
                        <option></option>
                        @foreach($bancos as $value)
                        <option value="{{ $value->id }}">{{ $value->nombre }}: ***{{ substr($value->cuenta, -4) }}</option>
                        @endforeach
                    </select>
                </div>
              </div>
          </div>
          <div class="col-sm-12 col-md-5">
              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;">Nro Ope.:</label>
                <div class="col-sm-7">
                    <input type="text" id="numerooperacion_deposito2" class="form-control" value="{{$movimientointernodinero->numerooperacion}}" disabled>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;">Descripción:</label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" id="descripcion_deposito2" value="{{$movimientointernodinero->descripcion}}" disabled>
                </div>
              </div>
              @if($movimientointernodinero->idresponsable==0)
              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;"></label>
                <div class="col-sm-7">
                    <button type="button" class="btn btn-success" onclick="valid_registro_deposito2({{$movimientointernodinero->id}})"><i class="fa-solid fa-check"></i> Confirmar</button>
                </div>
              </div>
              @endif
          </div>
       </div>
    </div>
<script>

  @include('app.nuevosistema.select2',['input'=>'#idfuenteretiro_deposito2', 'val' => $movimientointernodinero->idfuenteretiro ])
  @include('app.nuevosistema.select2',['input'=>'#idbanco_deposito2', 'val' => $movimientointernodinero->idbanco ])
  
  function eliminar_movimientointernodinero_deposito2(){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/cvmovimientointernodineroinsti/'.$movimientointernodinero->id.'/edit?view=eliminar_deposito2')}}",  size: 'modal-sm'  });  
  }

    function valid_registro_deposito2(id){
      var idfuenteretiro_deposito2 = $('#idfuenteretiro_deposito2').val();
      modal({ route:"{{url('backoffice/'.$tienda->id.'/cvmovimientointernodineroinsti')}}/"+id+"/edit?view=valid_registro_deposito2&idfuenteretiro_deposito2="+idfuenteretiro_deposito2,  size: 'modal-sm'  });
    }
</script>