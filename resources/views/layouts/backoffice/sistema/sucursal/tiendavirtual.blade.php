<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/sucursal/'.$s_sucursal->id) }}',
          method: 'PUT',
          data:{
              view: 'redsocial',
              idprincipal: {{$id}},
              redessociales: seleccionar_redsocial(),
          }
      },
      function(resultado){
          $('#modal-close-redsocial-editar').click(); 
          $('#tabla-redsocial').DataTable().ajax.reload();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Tienda Virtual</h5>
        <button type="button" class="btn-close" id="modal-close-redsocial-editar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-1" data-bs-toggle="tab" data-bs-target="#nav-target-1" type="button" role="tab" aria-controls="nav-1" aria-selected="true">Redes Sociales</button>
          </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
          <div class="tab-pane fade show active" id="nav-target-1" role="tabpanel" aria-labelledby="nav-1" tabindex="0">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-1">
                            <table class="table" id="tabla-tiendaavirtual">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Link</th>
                                        <th width="39px" style="text-align: center;">
                                            <a href="javascript:;" onclick="agregar_redsocial()">
                                                <i class="fa-solid fa-circle-plus"></i>
                                            </a>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody num="0">
                                  
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
          </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form> 
<script>
    @forelse($redesSociales as $value)
        agregar_redsocial('{{$value->redes_sociales}}', '{{$value->nombre}}');
    @empty
    @endforelse
    
    function agregar_redsocial(redSocial, link){
        
        var num   = $("#tabla-tiendaavirtual > tbody").attr('num');
        var cant  = $("#tabla-tiendaavirtual > tbody > tr").length;
      
        var tdeliminar = '<td></td>';
        // if(cant>0){
            tdeliminar = '<td><a href="javascript:;" onclick="eliminar_redsocial('+num+')" class="btn btn-danger "><i class="fa-solid fa-trash"></i></td>';
        // }
      
      
        var tabla='<tr id="'+num+'">'+
                      '<td><select class="form-control" id="redes_sociales'+num+'">'+
                        '<option value="facebook">Facebook</option>'+
                        '<option value="youtube">Youtube</option>'+
                        '<option value="instagram">Instagram</option>'+
                       '</select></td>'+
                      '<td><input type="text" class="form-control" id="nombre_red'+num+'" value="'+link+'"></td>'+
                      tdeliminar+
                  '</tr>';

        $("#tabla-tiendaavirtual > tbody").append(tabla);
        $("#tabla-tiendaavirtual > tbody").attr('num',parseInt(num)+1);
        
        @include('app.nuevosistema.select2',['json'=>'redessociales','input'=>'#redes_sociales/+num+/','val'=>'/+redSocial+/'])

        
    }
    function eliminar_redsocial(num){
        $("#tabla-tiendaavirtual > tbody > tr#"+num).remove();
    }

    function seleccionar_redsocial(){
        var data = [];
        $("#tabla-tiendaavirtual > tbody > tr").each(function() {
            var num = $(this).attr('id');    
            data.push({ 
                redes_sociales: $('#redes_sociales'+num+' option:selected').val(),
                nombre: $('#nombre_red'+num).val(),
            });
        });
        return JSON.stringify(data);
    }
</script>     