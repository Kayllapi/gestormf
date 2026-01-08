<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/usuarioacceso/'.$usuario->id) }}',
          method: 'PUT',
          data: {
              view : 'permiso',
              idmodulos : seleccionar_modulos()
          }
      },
      function(resultado){
          $('#modal-close-usuarioacceso-permiso').click(); 
          $('#tabla-usuarioacceso').DataTable().ajax.reload();
          mostrar_sucursales();
      },this)"> 
    <div class="modal-header">
        <h5 class="modal-title">Editar Permisos</h5>
        <button type="button" class="btn-close" id="modal-close-usuarioacceso-permiso" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body"> 
        <div class="mb-1">
            <label>Sucursal *</label>
            <select class="form-select" id="idsucursal">
                <option></option>
            </select>
        </div>
        <div id="cont-permisomodulo"></div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form>

<style> 
  #tabla-usuarioacceso-permisos >:not(caption)>*>* {
      background-color: #ffffff;
  }
</style>
<script>
    //mostrar_permisomodulo();
  
    @include('app.nuevosistema.select2',['json'=>'tienda:sucursal','input'=>'#idsucursal','val'=>Auth::user()->idsucursal])
  
    $("#idsucursal").on("change", function(e) {
        mostrar_permisomodulo();
    });
  
    function seleccionar_modulos(){
        var idmodulos = '';
        $('.idpermiso[type=checkbox]:checked').each(function() {
            idmodulos = idmodulos+','+$(this).val();
        });
        return idmodulos;
    }
    
    function mostrar_permisomodulo(){
        load('#cont-permisomodulo');
        $.ajax({
            url:"{{url('backoffice/'.$tienda->id.'/usuarioacceso/'.$usuario->id.'/edit')}}",
            type:'GET',
            data: {
                view : 'permisomodulo',
                idsucursal : $('#idsucursal').val(),
            },
            success: function (respuesta){
                $('#cont-permisomodulo').html(respuesta); 
            }
        })
    }
</script>