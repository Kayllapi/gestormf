<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$idtienda.'/valorizaciondescuento/'.$valorizacion->id) }}',
          method: 'PUT',
          data:{
              view: 'editar',
              idtienda: {{$tienda->id}}
          }
      },
      function(resultado){
          lista_valorizacion_garantia();
          load_create_tipogarantia()
      },this)">
    <div class="modal-body">

        <div class="row">
          <div class="col-sm-12 col-md-3 mb-1">
              <label>Metodo de Valorización *</label>
              <select class="form-control" id="iddescuento_joya">
                  <option></option>
                @foreach($descuento_joya as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
              </select>
                
          </div>
          <div class="col-sm-12 col-md-6 mb-1">
                <label id="title-antiguedad">Detalle Descuento</label>
                <input type="text" class="form-control" id="detalle_descuento" value="{{ $valorizacion->detalle_descuento }}">
          </div>
          <div class="col-sm-12 col-md-3 mb-1">
                <label id="title-valor">Descuento (%/g)</label>
                <input type="text" class="form-control" id="descuento" value="{{ $valorizacion->descuento }}">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" onclick="eliminar_garantia()"><i class="fa-solid fa-trash"></i> Eliminar Valorización</button>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form> 
<script>

      $('#cont_irainicio').css('display','block');
  
    function eliminar_garantia(){
      modal({ route:"{{url('backoffice/'.$tienda->id.'/valorizaciondescuento/'.$valorizacion->id.'/edit?view=eliminar')}}" });  
    }
    @include('app.nuevosistema.select2',['input'=>'#iddescuento_joya', 'val' => $valorizacion->iddescuento_joya])
    
  
    $("#idtipo_joyas").on("change", function(e) {
      
      lista_valorizacion_garantia();
    });
    $("#iddescuento_joya").on("change", function(e) {
      lista_valorizacion_garantia();
    });


</script>     