<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$idtienda.'/garantias/'.$cliente->id) }}',
          method: 'PUT',
          data:{
              view: 'editar_depositario',
              idtienda: {{$tienda->id}},
              seleccionar_polizaseguro : seleccionar_polizaseguro(),
          }
      },
      function(resultado){
          $('#closedepositario').click();
      },this)" id="form-editar-garantia">
  
    <input type="hidden" id="idresponsable_modificado">
    <div class="modal-header">
        <h5 class="modal-title">Gestión de Depósitario y Póliza de Seguros</h5>
    </div>
    <div class="modal-body">
          <div class="mb-1 mt-2">
            <span class="badge d-block">GESTIÓN DE DEPOSITARIO</span>
          </div>
          <div class="row">
            <div class="col-sm-4">
              <label>Constitución de la Garantía Mobiliaria *</label>
              <select class="form-control" id="constituciongarantia_id" disabled>
                <option></option>
                 @foreach($credito_gestiondepositario as $value)
                    <option value="{{ $value->constituciongarantia_id }}"constituciongarantia_nombre="{{ $value->constituciongarantia_nombre }}" >{{ $value->constituciongarantia_nombre }}</option>
                  @endforeach
              </select>
              <input type="hidden" class="form-control" id="constituciongarantia_nombre">
            </div>
          </div>
          <div class="row">
            <div class="col-sm-8">
              <label>Depositario (Custodia de Garantía) *</label>
              <select class="form-control" id="custodiagarantia_id" disabled>
                <option></option>
              </select>
              <input type="hidden" class="form-control"value="{{$cliente->custodiagarantia_nombre}}"  id="custodiagarantia_nombre">
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <label>Nombre/Rason S.</label>
              <input type="text" class="form-control" value="{{$cliente->gd_nombre}}" id="gd_nombre" disabled>
            </div>
            <div class="col-sm-6">
              <label>DNI/RUC</label>
              <input type="text" class="form-control" value="{{$cliente->gd_doeruc}}" id="gd_doeruc" disabled>
            </div>
            <div class="col-sm-12">
              <label>Dirección</label>
              <input type="text" class="form-control" value="{{$cliente->gd_direccion}}" id="gd_direccion" disabled>
            </div>
            <div class="col-sm-6">
              <label>Reprentante Legal (DNI)</label>
              <input type="text" class="form-control" value="{{$cliente->gd_representante_doeruc}}" id="gd_representante_doeruc" disabled>
            </div>
            <div class="col-sm-6">
              <label>Apellidos y Nombres</label>
              <input type="text" class="form-control" value="{{$cliente->gd_representante_nombre}}" id="gd_representante_nombre" disabled>
            </div>
          </div>
          <div class="mb-1 mt-2">
            <span class="badge d-block">POLIZA DE SEGUROS</span>
          </div>
              <table class="table" id="table-polizaseguro">
                <thead>
                  <tr>
                    <th rowspan="2" class="text-center">N° de Póliza</th>
                    <th rowspan="2" class="text-center">Aseguradora</th>
                    <th rowspan="2" class="text-center">Prima (Precio S/.)</th>
                    <th rowspan="2" class="text-center">Beneficiario  (Derecho a la Indemnización)</th>
                    <th rowspan="2" class="text-center">Asegurado (Objeto de Seguro)</th>
                    <th rowspan="2" class="text-center">Tomador (El que suscribe)</th>
                    <th colspan="2" class="text-center">Vigencia</th>
                    <th rowspan="2" style="width:10px;display:none;" id="polizaseguro_td_agregar"><button type="button" class="btn btn-success" onclick="agrega_polizaseguro()"><i class="fa fa-plus"></i></button></th>
                  </tr>
                  <tr>
                    <th class="text-center">Desde</th>
                    <th class="text-center">Hasta</th>
                  </tr>
                </thead>
                <tbody num="0">
                </tbody>
              </table>
    </div>
    <div class="modal-footer d-none" id="cont-btnguardar">
        <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
    </div>
</form> 
<script>
          
  $('tr.selected').removeClass('selected');
  $('#cont-btnguardar').addClass("d-none");
    $('#btn-autorizar-garantia').addClass("d-none");
    $('#btn-autorizar-depositario').removeClass("d-none")
  
    $('#btn-delete-garantia').addClass("d-none");
    $('#alert-garantia-1').addClass("d-none");
    $('#alert-garantia-2').addClass("d-none");
    $('#alert-garantia-3').addClass("d-none");
  
  function modificar_garantia(val){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/garantias/'.$cliente->id.'/edit?view=modificar_depositario')}}&val="+val,  size: 'modal-sm' });  
  }
  
  function autorizar_edicion_depositario(val){
    $('#cont-btnguardar').removeClass("d-none");
        $('#polizaseguro_td_agregar').css('display','table-cell');

        $('#constituciongarantia_id').removeAttr('disabled');
        $('#custodiagarantia_id').removeAttr('disabled');

        $("#table-polizaseguro > tbody > tr").each(function() {
            var num = $(this).attr('id');  
            $('#polizaseguro_numero_poliza'+num).removeAttr('disabled');
            $('#polizaseguro_aseguradora'+num).removeAttr('disabled');
            $('#polizaseguro_prima_recio'+num).removeAttr('disabled');
            $('#polizaseguro_beneficiario'+num).removeAttr('disabled');
            $('#polizaseguro_asegurado'+num).removeAttr('disabled');
            $('#polizaseguro_tomador'+num).removeAttr('disabled');
            $('#polizaseguro_vigencia_desde'+num).removeAttr('disabled');
            $('#polizaseguro_vigencia_hasta'+num).removeAttr('disabled');

            $('#polizaseguro_td_eliminar'+num).css('display','block');
        });
  }
  // gestion de depositario
  
  
  @include('app.nuevosistema.select2',['input'=>'#constituciongarantia_id'])
  
  $("#constituciongarantia_id").on("change", function(e) {
    
    var constituciongarantia_nombre = $('#constituciongarantia_id :selected').attr('constituciongarantia_nombre');
    $('#constituciongarantia_nombre').val(constituciongarantia_nombre);
    $.ajax({
      url:"{{url('backoffice/0/garantias/show_constituciongarantia')}}",
      type:'GET',
      data: {
          constituciongarantia_id : e.currentTarget.value
      },
      success: function (res){
        //$('#custodiagarantia_id').removeAttr('disabled',false)
        let option_select = `<option></option>`;
        $.each(res, function( key, value ) {
          var persona = value.doeruc!=''?`- (DNI/RUC: ${value.doeruc}) ${value.nombre}`:'';
          option_select += `<option value="${value.custodiagarantia_id}" custodiagarantia_nombre="${value.custodiagarantia_nombre}" doeruc="${value.doeruc}" nombre="${value.nombre}">${value.custodiagarantia_nombre} ${persona}</option>`;
        });
        $('#custodiagarantia_id').html(option_select);
        sistema_select2({ input:'#custodiagarantia_id',val:'{{ $cliente->custodiagarantia_id }}'});
        
      }
    })
    
  })
  @if($cliente->constituciongarantia_id!=0)  
    .val('{{ $cliente->constituciongarantia_id }}').trigger('change');
  @endif
  @include('app.nuevosistema.select2',['input'=>'#custodiagarantia_id'])
  
  $("#custodiagarantia_id").on("select2:select", function(e) {
    
    //$('#constituciongarantia_id').prop('disabled',true);
    //$('#custodiagarantia_id').prop('disabled',true);
    $('#gd_nombre').prop('disabled',true);
    $('#gd_doeruc').prop('disabled',true);
    $('#gd_direccion').prop('disabled',true);
    $('#gd_representante_doeruc').prop('disabled',true);
    $('#gd_representante_nombre').prop('disabled',true);
    
    var custodiagarantia_nombre = $('#custodiagarantia_id :selected').attr('custodiagarantia_nombre');
    
    $('#custodiagarantia_nombre').val(custodiagarantia_nombre);
    
    $.ajax({
      url:"{{url('backoffice/0/garantias/show_custodiagarantia')}}",
      type:'GET',
      data: {
          idcliente : $('#data-cliente-id').val(),
          constituciongarantia_id : $('#constituciongarantia_id').val(),
          custodiagarantia_id : e.params.data.id,
          doeruc : $('#custodiagarantia_id :selected').attr('doeruc'),
          nombre : $('#custodiagarantia_id :selected').attr('nombre'),
      },
      success: function (res){
        $('#gd_nombre').val(res.credito_gestiondepositario!=undefined?res.credito_gestiondepositario.nombre:'');
        $('#gd_doeruc').val(res.credito_gestiondepositario!=undefined?res.credito_gestiondepositario.doeruc:'');
        $('#gd_direccion').val(res.credito_gestiondepositario!=undefined?res.credito_gestiondepositario.direccion:'');
        $('#gd_representante_doeruc').val(res.credito_gestiondepositario!=undefined?res.credito_gestiondepositario.representante_doeruc:'');
        $('#gd_representante_nombre').val(res.credito_gestiondepositario!=undefined?res.credito_gestiondepositario.representante_nombre:'');
        
        if($('#custodiagarantia_id').val()==4){
            $('#gd_nombre').val(res.cliente.nombrecompleto);
            $('#gd_doeruc').val(res.cliente.identificacion);
            $('#gd_direccion').val(res.cliente.direccion);
            $('#gd_representante_doeruc').val(res.cliente_representante.ruc_laboral_pareja);
            $('#gd_representante_nombre').val(res.cliente_representante.razonsocial_laboral_pareja);
        }
        if($('#custodiagarantia_id').val()==3){
            $('#constituciongarantia_id').removeAttr('disabled');
            $('#custodiagarantia_id').removeAttr('disabled');
            $('#gd_nombre').removeAttr('disabled');
            $('#gd_doeruc').removeAttr('disabled');
            $('#gd_direccion').removeAttr('disabled');
            $('#gd_representante_doeruc').removeAttr('disabled');
            $('#gd_representante_nombre').removeAttr('disabled');
        }
      }
    })
    
  });
  
  // poliza seguros
  
  @foreach($credito_polizaseguro as $value)
      agrega_polizaseguro('{{$value->numero_poliza}}',
                                '{{$value->aseguradora}}',
                                '{{$value->prima_recio}}',
                                '{{$value->beneficiario}}',
                                '{{$value->asegurado}}',
                                '{{$value->tomador}}',
                                '{{$value->vigencia_desde}}',
                                '{{$value->vigencia_hasta}}',
                                'disabled');
  @endforeach
  
  function agrega_polizaseguro(numero_poliza='',aseguradora='',prima_recio='',beneficiario='',asegurado='',tomador='',vigencia_desde='',vigencia_hasta='',disabled=''){
    var num   = $("#table-polizaseguro > tbody").attr('num');
    let tabla = `
                <tr id="${num}">
                  <td><input type="text" class="form-control" id="polizaseguro_numero_poliza${num}" value="${numero_poliza}" ${disabled}></td>
                  <td><input type="text" class="form-control" id="polizaseguro_aseguradora${num}" value="${aseguradora}" ${disabled}></td>
                  <td><input type="number" class="form-control" id="polizaseguro_prima_recio${num}" value="${prima_recio}" step="any" ${disabled}></td>
                  <td><input type="text" class="form-control" id="polizaseguro_beneficiario${num}" value="${beneficiario}" ${disabled}></td>
                  <td><input type="text" class="form-control" id="polizaseguro_asegurado${num}" value="${asegurado}" ${disabled}></td>
                  <td><input type="text" class="form-control" id="polizaseguro_tomador${num}" value="${tomador}" ${disabled}></td>
                  <td><input type="date" class="form-control" id="polizaseguro_vigencia_desde${num}" value="${vigencia_desde}" ${disabled}></td>
                  <td><input type="date" class="form-control" id="polizaseguro_vigencia_hasta${num}" value="${vigencia_hasta}" ${disabled}></td>
                  <td ${disabled=='disabled'?'style="display:none;"':''} id="polizaseguro_td_eliminar${num}"><button type="button" onclick="eliminar_polizaseguro(${num})" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                </tr>`;

      $("#table-polizaseguro > tbody").append(tabla);
      $("#table-polizaseguro > tbody").attr('num',parseInt(num)+1); 
  }
  function eliminar_polizaseguro(num){
      $("#table-polizaseguro > tbody > tr#"+num).remove();
  }
  function seleccionar_polizaseguro(){
      var data = [];
      $("#table-polizaseguro > tbody > tr").each(function() {
          var num = $(this).attr('id');    
          data.push({ 
              numero_poliza: $('#polizaseguro_numero_poliza'+num).val(),
              aseguradora: $('#polizaseguro_aseguradora'+num).val(),
              prima_recio: $('#polizaseguro_prima_recio'+num).val(),
              beneficiario: $('#polizaseguro_beneficiario'+num).val(),
              asegurado: $('#polizaseguro_asegurado'+num).val(),
              tomador: $('#polizaseguro_tomador'+num).val(),
              vigencia_desde: $('#polizaseguro_vigencia_desde'+num).val(),
              vigencia_hasta: $('#polizaseguro_vigencia_hasta'+num).val(),
          });
      });
      return JSON.stringify(data);
  }

</script>     