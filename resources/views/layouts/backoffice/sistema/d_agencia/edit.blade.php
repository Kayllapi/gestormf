<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/agencia/'.$s_agencia->id) }}',
          method: 'PUT',
          data:{
              view: 'editar',
              encabezados: seleccionar_data('encabezados'),
              footers: seleccionar_data('footer'),
          }
      },
      function(resultado){
          $('#modal-close-agencia-editar').click(); 
          $('#tabla-agencia').DataTable().ajax.reload();
      },this)">
    <div class="modal-header">
        <h5 class="modal-title">Editar Agencia</h5>
        <button type="button" class="btn-close" id="modal-close-agencia-editar" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        
          <div class="tab-pane fade show active" id="nav-target-1" role="tabpanel" aria-labelledby="nav-1" tabindex="0">
              <div class="row">
                  <div class="col-md-12">
                      
                      <div class="mb-1">
                          <label>Nombre Agencia *</label>
                          <input type="text" class="form-control" value="{{ $s_agencia->nombrecomercial }}" id="nombrecomercial">
                      </div>
                      <div class="mb-1">
                          <label>Dirección *</label>
                          <input type="text" class="form-control" value="{{ $s_agencia->direccion }}" id="direccion">
                      </div>
                      <div class="mb-1">
                          <label data-bs-toggle="popover" 
                                 data-bs-placement="right" 
                                 data-bs-content="Puedes buscar por Distrito, Provincia ó Departamento.">Ubicación (Ubigeo) * 
                              <i class="fa-solid fa-circle-info"></i>
                          </label>
                          <select class="form-select" id="idubigeo">
                              <option value="{{$s_agencia->idubigeo}}">{{$s_agencia->ubigeonombre}}</option>
                          </select>
                      </div>
                      
                      <div class="mb-1">
                          <label>Teléfono *</label>
                          <input type="text" class="form-control" value="{{ $s_agencia->telefono }}" id="telefono">
                      </div>
                  </div>
                  <div class="col-md-6 d-none">
                      <div class="mb-1">
                          <label>Logo</label>
                          <div class="fuzone" id="cont-fileupload" style="height: 150px;">
                              <div class="fuzone-text"><i class="fa-solid fa-cloud-arrow-up"></i>Haga clic aquí ó suelte para cargar</div>
                              <input type="file" class="upload" id="imagen">
                              <div id="resultado-logo"></div>
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
      $headers_cpe = is_null($s_agencia->header_cpe) ? [] : json_decode($s_agencia->header_cpe);
      $footers_cpe = is_null($s_agencia->footer_cpe) ? [] : json_decode($s_agencia->footer_cpe);
    @endphp
    @foreach($headers_cpe as $value)
        agregar_fila('encabezados','{{$value->texto}}');
    @endforeach
    @foreach($footers_cpe as $value)
        agregar_fila('footer','{{$value->texto}}');
    @endforeach


    uploadfile({
      input:"#imagen",
      cont:"#cont-fileupload",
      result:"#resultado-logo",
      ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/') }}",
      image: "{{ $s_agencia->logo }}"
    });
  
    @include('app.nuevosistema.select2',['json'=>'ubigeo','input'=>'#idubigeo'])
    @include('app.nuevosistema.select2',['json'=>'estado','input'=>'#idestadofacturacion','val'=>$s_agencia->idestadofacturacion])

    $("#idestadofacturacion").on("change", function(e) {
        $('#cont-facturacion_estado1').css('display','block');
        $('#cont-facturacion_estado2').css('display','block');
        if(e.currentTarget.value == 2) {
            $('#cont-facturacion_estado1').css('display','none');
            $('#cont-facturacion_estado2').css('display','none');
        }
    });

    function agregar_fila(tabla,valor=''){
        
        var num   = $("#tabla-"+tabla+" > tbody").attr('num');
        // var cant  = $("#tabla-"+tabla+" > tbody > tr").length;
      
        var tdeliminar = `<td></td>`;
        // if(cant>0){
            tdeliminar = `<td><a href="javascript:;" onclick="eliminar_fila(${num},'${tabla}')" class="btn btn-danger "><i class="fa-solid fa-trash"></i></td>`;
        // }
      
      
        var html='<tr id="'+num+'">'+
                      '<td><input class="form-control" id="'+tabla+'texto'+num+'" value="'+valor+'"></td>'+
                      tdeliminar+
                  '</tr>';

        $("#tabla-"+tabla+" > tbody").append(html);
        $("#tabla-"+tabla+" > tbody").attr('num',parseInt(num)+1);  
        
    }
    function eliminar_fila(num,tabla){
        $("#tabla-"+tabla+" > tbody > tr#"+num).remove();
    }
    function seleccionar_data(tabla){
        var data = [];
        $("#tabla-"+tabla+" > tbody > tr").each(function() {
            var num = $(this).attr('id');    
            data.push({ 
                texto: $('#'+tabla+'texto'+num).val(),
            });
        });
        return JSON.stringify(data);
    }
</script>  