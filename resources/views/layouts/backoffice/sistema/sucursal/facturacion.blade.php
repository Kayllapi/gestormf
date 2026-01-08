<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/sucursal/'.$s_sucursal->id) }}',
          method: 'PUT',
          data:{
              view: 'facturacion',
              idprincipal: {{$id}},
              agencias: seleccionar_agencia(),
          }
      },
      function(resultado){
          $('#modal-close-sucursal-editar').click(); 
          $('#tabla-sucursal').DataTable().ajax.reload();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Facturación</h5>
        <button type="button" class="btn-close" id="modal-close-sucursal-editar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-1" data-bs-toggle="tab" data-bs-target="#nav-target-1" type="button" role="tab" aria-controls="nav-1" aria-selected="true">Facturación</button>
          </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
          <div class="tab-pane fade show active" id="nav-target-1" role="tabpanel" aria-labelledby="nav-1" tabindex="0">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-1">
                            <label>Serie *</label>
                            <input type="text" class="form-control" value="{{ $s_sucursal->facturacionserie }}" id="facturacionserie">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-1">
                            <label>Correlativo *</label>
                            <input type="text" class="form-control" value="{{ $s_sucursal->facturacioncorrelativo }}" id="facturacioncorrelativo">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-1">
                            <table class="table" id="tabla-agenciafacturacion">
                                <thead>
                                    <tr>
                                        <th>Agencia</th>
                                        <th width="39px" style="text-align: center;">
                                            <a href="javascript:;" onclick="agregar_agencia()">
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
    @php 
      $agencias = is_null($s_sucursal->facturacionagencia) ? [] : json_decode($s_sucursal->facturacionagencia);
    @endphp
    @foreach($agencias as $value)
        agregar_agencia('{{$value->idagencia}}');
    @endforeach

    function agregar_agencia(idagencia){
        
        var num   = $("#tabla-agenciafacturacion > tbody").attr('num');
        var cant  = $("#tabla-agenciafacturacion > tbody > tr").length;
      
        var tdeliminar = '<td></td>';
        // if(cant>0){
            tdeliminar = '<td><a href="javascript:;" onclick="eliminar_agencia('+num+')" class="btn btn-danger "><i class="fa-solid fa-trash"></i></td>';
        // }
      
      
        var tabla='<tr id="'+num+'">'+
                      '<td><select class="form-control" id="idagencia'+num+'"><option></option></select></td>'+
                      tdeliminar+
                  '</tr>';

        $("#tabla-agenciafacturacion > tbody").append(tabla);
        $("#tabla-agenciafacturacion > tbody").attr('num',parseInt(num)+1);  
        @include('app.nuevosistema.select2',['json'=>'tienda:agencia','input'=>'#idagencia/+num+/','val'=>'/+idagencia+/' ])
        
    }
    function eliminar_agencia(num){
        $("#tabla-agenciafacturacion > tbody > tr#"+num).remove();
    }
    function seleccionar_agencia(){
        var data = [];
        $("#tabla-agenciafacturacion > tbody > tr").each(function() {
            var num = $(this).attr('id');    
            data.push({ 
                idagencia: $('#idagencia'+num+' option:selected').val(),
                agencia: $('#idagencia'+num+' option:selected').text(),
            });
        });
        return JSON.stringify(data);
    }
</script>     