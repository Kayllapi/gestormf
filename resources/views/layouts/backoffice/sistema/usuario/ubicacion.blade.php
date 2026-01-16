<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/usuario/'.$usuario->id) }}',
          method: 'PUT',
          data:{
              view: 'editar_ubicacion',
          }
      },
      function(resultado){
          $('#modal-close-usuario-editar').click();  
          $('#tabla-usuario').DataTable().ajax.reload(); 
      },this)" id="form-editar-cliente"> 
    <div class="modal-header">
        <h5 class="modal-title">EDITAR UBICACIÓN DE CLIENTE</h5>
        <button type="button" class="btn-close" id="modal-close-usuario-editar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="mb-1">
            <label style="margin-bottom: 5px;">Ubicación (Mover Marcador) 
            <a href="javascript:;" 
               class="btn btn-primary" 
               onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/usuario/'.$usuario->id.'/edit?view=imprimir_ubicacion')}}'})">
              <i class="fa-solid fa-file-pdf"></i> Imprimir
            </a>
            <button type="button" onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/usuario/create?view=autorizacion')}}',size:'modal-sm'})" class="btn btn-success"><i class="fa fa-pencil"></i> Editar</button></button>
            </label>
            <div id="domicilio_mapa" class="mapa" style="height: 400px;"></div>
            <input type="hidden" class="form-control" id="domicilio_mapa_latitud" value="{{$usuario->mapa_latitud}}"/>
            <input type="hidden" class="form-control" id="domicilio_mapa_longitud" value="{{$usuario->mapa_longitud}}"/>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="boton_guardarubicacion"
                @if($usuario->mapa_latitud!='' && $usuario->mapa_longitud!='')
                disabled
                @endif
                ><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form> 
<script>
    @if($usuario->mapa_latitud!='' && $usuario->mapa_longitud!='')
        singleMap({
            'map' : '#domicilio_mapa',
            'lat' : '{{$usuario->mapa_latitud}}',
            'lng' : '{{$usuario->mapa_longitud}}',
            'result_lat' : '#domicilio_mapa_latitud',
            'result_lng' : '#domicilio_mapa_longitud'
        });
    @else
        seleccionar_ubicacion('{{$usuario->ubigeonombre}}');
    @endif
    function seleccionar_ubicacion(address) {
        singleMap_address({
            'map' : '#domicilio_mapa',
            'address' : address,
            'result_lat' : '#domicilio_mapa_latitud',
            'result_lng' : '#domicilio_mapa_longitud'
        });
    }
</script>