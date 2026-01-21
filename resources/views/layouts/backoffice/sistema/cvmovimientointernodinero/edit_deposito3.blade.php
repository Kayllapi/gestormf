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
                  <input type="number" class="form-control" id="monto_deposito3" value="{{$movimientointernodinero->monto}}" step="any" disabled>
                </div>
              </div>
          </div>
          <div class="col-sm-12 col-md-5">
              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;">Descripción:</label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" id="descripcion_deposito3" value="{{$movimientointernodinero->descripcion}}" disabled>
                </div>
              </div>
              @if($movimientointernodinero->idresponsable==0)
              <div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;"></label>
                <div class="col-sm-7">
                    <button type="button" class="btn btn-success" onclick="valid_registro_deposito3({{$movimientointernodinero->id}})"><i class="fa-solid fa-check"></i> Confirmar</button>
                </div>
              </div>
              @endif
              <!--div class="row">
                <label class="col-sm-5 col-form-label" style="text-align: right;"></label>
                <div class="col-sm-7">
                    <button type="button" onclick="eliminar_movimientointernodinero_deposito3()" class="btn btn-danger"><i class="fa-solid fa-trash"></i> ELIMINAR</button>
                </div>
              </div-->
          </div>
       </div>
    </div>
<script>

  @include('app.nuevosistema.select2',['input'=>'#idfuenteretiro_deposito3', 'val' => $movimientointernodinero->idfuenteretiro ])
  @include('app.nuevosistema.select2',['input'=>'#idbanco_deposito3', 'val' => $movimientointernodinero->idbanco ])
  
  function eliminar_movimientointernodinero_deposito3(){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/cvmovimientointernodinero/'.$movimientointernodinero->id.'/edit?view=eliminar_deposito3')}}",  size: 'modal-sm'  });  
  }
    function valid_registro_deposito3(id){
      var idfuenteretiro_deposito3 = $('#idfuenteretiro_deposito3').val();
      modal({ route:"{{url('backoffice/'.$tienda->id.'/cvmovimientointernodinero')}}/"+id+"/edit?view=valid_registro_deposito3&idfuenteretiro_deposito3="+idfuenteretiro_deposito3,  size: 'modal-sm'  });
    }
</script>