<div class="modal-header">
    <h5 class="modal-title">
      Gestion de Depositario y Rep. Común
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/gestiondepositario/0') }}',
        method: 'PUT',
          data:{
              view: 'editar',
              seleccionar_conentregaposesion : seleccionar_conentregaposesion(),
              seleccionar_sinentregaposesion : seleccionar_sinentregaposesion(),
              seleccionar_representantecomun : seleccionar_representantecomun(),
          }
      },
      function(resultado){
          
      },this)"> 
      <div class="mb-1 mt-2">
        <span class="badge d-block">Constitución de la Garantía Mobiliaria: <span style="background-color: #d9e211;
    color: #000;
    padding-left: 5px;
    padding-right: 5px;
    border-radius: 5px;">Con entrega de posesión</span></span>
      </div>
          <table class="table table-bordered" id="table-conentregaposesion">
            <thead>
              <tr>
                <th colspan=6 style="text-align:center;">Depositario</th>
                <th rowspan=3 style="width:100px;text-align:center;">Estado</th>
                <th rowspan=3 style="width:10px;"><button type="button" class="btn btn-success" onclick="agrega_conentregaposesion()"><i class="fa fa-plus"></i></button></th>
              </tr>
              <tr>
                <th rowspan=2 style="text-align:center;width:170px;">Custodia de Garantía </th>
                <th rowspan=2 style="text-align:center;">Nombre/Rason S.</th>
                <th rowspan=2 style="text-align:center;width:100px;">DNI/RUC</th>
                <th rowspan=2 style="text-align:center;">Dirección</th>
                <th colspan=2 style="text-align:center;">Reprentante Legal</th>
              </tr>
              <tr>
                <th style="width:80px;">DNI</th>
                <th style="width:200px;">Apellidos y Nombres</th>
              </tr>
            </thead>
            <tbody num="0">
            </tbody>
          </table>
      <div class="mb-1 mt-2">
        <span class="badge d-block">Constitución de la Garantía Mobiliaria: <span style="background-color: #d9e211;
    color: #000;
    padding-left: 5px;
    padding-right: 5px;
    border-radius: 5px;">Sin entrega de posesión</span></span>
      </div>
          <table class="table table-bordered" id="table-sinentregaposesion">
            <thead>
              <tr>
                <th colspan=6 style="text-align:center;">Depositario</th>
                <th rowspan=3 style="width:100px;text-align:center;">Estado</th>
                <th rowspan=3 style="width:10px;"><button type="button" class="btn btn-success" onclick="agrega_sinentregaposesion()"><i class="fa fa-plus"></i></button></th>
              </tr>
              <tr>
                <th rowspan=2 style="text-align:center;width:170px;">Custodia de Garantía </th>
                <th rowspan=2 style="text-align:center;">Nombre/Rason S.</th>
                <th rowspan=2 style="text-align:center;width:100px;">DNI/RUC</th>
                <th rowspan=2 style="text-align:center;">Dirección</th>
                <th colspan=2 style="text-align:center;">Reprentante Legal</th>
              </tr>
              <tr>
                <th style="width:80px;">DNI</th>
                <th style="width:200px;">Apellidos y Nombres</th>
              </tr>
            </thead>
            <tbody num="0">
            </tbody>
          </table><br><br>
      <div class="mb-1 mt-4">
        <span class="badge d-block">Reprentante Común</span>
      </div>
          <table class="table table-bordered" id="table-representantecomun">
            <thead>
              <tr>
                <th rowspan=2 style="text-align:center;">Nombre y Apellidos</th>
                <th rowspan=2 style="width:100px;text-align:center;">DNI</th>
                <th colspan=2 style="text-align:center;">Domicilio</th>
                <th rowspan=2 style="width:100px;text-align:center;">Estado</th>
                <th rowspan=2 style="width:10px;"><button type="button" class="btn btn-success" onclick="agrega_representantecomun()"><i class="fa fa-plus"></i></button></th>
              </tr>
              <tr>
                <th rowspan=2 style="text-align:center;">Dirección</th>
                <th rowspan=2 style="width:300px;text-align:center;">Distrito - Provincia - Departamento</th>
              </tr>
            </thead>
            <tbody num="0">
            </tbody>
          </table>
      <div class="row mt-1 justify-content-center">
        <div class="col-sm-12 col-md-2">
          <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
        </div>
      </div>
  </form>
</div>
<style>
.select2-container--bootstrap-5 .select2-selection {
    background-color: #dfdf79;
}
</style>
<script>
  
  @foreach($credito_gestiondepositario1 as $value)
      agrega_conentregaposesion('{{$value->custodiagarantia_id}}',
                                '{{$value->nombre}}',
                                '{{$value->doeruc}}',
                                '{{$value->direccion}}',
                                '{{$value->representante_doeruc}}',
                                '{{$value->representante_nombre}}',
                                '{{$value->estado_id}}');
  @endforeach
  
  function agrega_conentregaposesion(custodiagarantia='',nombre='',doeruc='',direccion='',representante_doeruc='',representante_nombre='',estado=''){
    var num   = $("#table-conentregaposesion > tbody").attr('num');
    let tabla = `
                <tr id="${num}">
                  <td>
                    <select class="form-control color_cajatexto" id="conposesion_custodiagarantia${num}">
                      <option value=""></option>
                      <option value="1" nombre="ACREEDOR" ${custodiagarantia=='1'?'selected':''}>ACREEDOR</option>
                      <option value="2" nombre="Convenio con ACREEDOR" ${custodiagarantia=='2'?'selected':''}>Convenio con ACREEDOR</option>
                    </select>
                  </td>
                  <td><input type="text" class="form-control color_cajatexto" id="conposesion_nombre${num}" value="${nombre}"></td>
                  <td><input type="text" class="form-control color_cajatexto" id="conposesion_doeruc${num}" value="${doeruc}"></td>
                  <td><input type="text" class="form-control color_cajatexto" id="conposesion_direccion${num}" value="${direccion}"></td>
                  <td><input type="text" class="form-control color_cajatexto" id="conposesion_representante_doeruc${num}" value="${representante_doeruc}"></td>
                  <td><input type="text" class="form-control color_cajatexto" id="conposesion_representante_nombre${num}" value="${representante_nombre}"></td>
                  <td>
                    <select class="form-control color_cajatexto" id="conposesion_estado${num}">
                      <option value=""></option>
                      <option value="1" nombre="Activo" ${estado=='1'?'selected':''}>Activo</option>
                      <option value="2" nombre="Inactivo" ${estado=='2'?'selected':''}>Inactivo</option>
                    </select>
                  </td>
                  <td><button type="button" onclick="eliminar_conentregaposesion(${num})" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                </tr>`;

      $("#table-conentregaposesion > tbody").append(tabla);
      $("#table-conentregaposesion > tbody").attr('num',parseInt(num)+1); 
  }
  function eliminar_conentregaposesion(num){
      $("#table-conentregaposesion > tbody > tr#"+num).remove();
  }
  function seleccionar_conentregaposesion(){
      var data = [];
      $("#table-conentregaposesion > tbody > tr").each(function() {
          var num = $(this).attr('id');    
          data.push({ 
              custodiagarantia_id: $('#conposesion_custodiagarantia'+num+' :selected').val(),
              custodiagarantia_nombre: $('#conposesion_custodiagarantia'+num+' :selected').attr('nombre'),
              nombre: $('#conposesion_nombre'+num).val(),
              doeruc: $('#conposesion_doeruc'+num).val(),
              direccion: $('#conposesion_direccion'+num).val(),
              representante_doeruc: $('#conposesion_representante_doeruc'+num).val(),
              representante_nombre: $('#conposesion_representante_nombre'+num).val(),
              estado_id: $('#conposesion_estado'+num+' :selected').val(),
              estado_nombre: $('#conposesion_estado'+num+' :selected').attr('nombre'),
              constituciongarantia_id: 1,
              constituciongarantia_nombre: 'Con entrega de posesión',
          });
      });
      return JSON.stringify(data);
  }
  
  @foreach($credito_gestiondepositario2 as $value)
      agrega_sinentregaposesion('{{$value->custodiagarantia_id}}',
                                '{{$value->nombre}}',
                                '{{$value->doeruc}}',
                                '{{$value->direccion}}',
                                '{{$value->representante_doeruc}}',
                                '{{$value->representante_nombre}}',
                                '{{$value->estado_id}}');
  @endforeach
  
  function agrega_sinentregaposesion(custodiagarantia='',nombre='',doeruc='',direccion='',representante_doeruc='',representante_nombre='',estado=''){
    var num   = $("#table-sinentregaposesion > tbody").attr('num');
    let tabla = `
                <tr id="${num}">
                  <td>
                    <select class="form-control color_cajatexto" id="sinposesion_custodiagarantia${num}">
                      <option value=""></option>
                      <option value="2" nombre="Convenio con ACREEDOR" ${custodiagarantia=='2'?'selected':''}>Convenio con ACREEDOR</option>
                      <option value="3" nombre="Otro" ${custodiagarantia=='3'?'selected':''}>Otro</option>
                      <option value="4" nombre="EL/LOS PRESTATARIO(S)" ${custodiagarantia=='4'?'selected':''}>EL/LOS PRESTATARIO(S)</option>
                    </select>
                  </td>
                  <td><input type="text" class="form-control color_cajatexto" id="sinposesion_nombre${num}" value="${nombre}"></td>
                  <td><input type="text" class="form-control color_cajatexto" id="sinposesion_doeruc${num}" value="${doeruc}"></td>
                  <td><input type="text" class="form-control color_cajatexto" id="sinposesion_direccion${num}" value="${direccion}"></td>
                  <td><input type="text" class="form-control color_cajatexto" id="sinposesion_representante_doeruc${num}" value="${representante_doeruc}"></td>
                  <td><input type="text" class="form-control color_cajatexto" id="sinposesion_representante_nombre${num}" value="${representante_nombre}"></td>
                  <td>
                    <select class="form-control color_cajatexto" id="sinposesion_estado${num}">
                      <option value=""></option>
                      <option value="1" nombre="Activo" ${estado=='1'?'selected':''}>Activo</option>
                      <option value="2" nombre="Inactivo" ${estado=='2'?'selected':''}>Inactivo</option>
                    </select>
                  </td>
                  <td><button type="button" onclick="eliminar_sinentregaposesion(${num})" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                </tr>`;

      $("#table-sinentregaposesion > tbody").append(tabla);
      $("#table-sinentregaposesion > tbody").attr('num',parseInt(num)+1); 
  }
  function eliminar_sinentregaposesion(num){
      $("#table-sinentregaposesion > tbody > tr#"+num).remove();
  }
  function seleccionar_sinentregaposesion(){
      var data = [];
      $("#table-sinentregaposesion > tbody > tr").each(function() {
          var num = $(this).attr('id');    
          data.push({ 
              custodiagarantia_id: $('#sinposesion_custodiagarantia'+num+' :selected').val()!=undefined?$('#sinposesion_custodiagarantia'+num+' :selected').val():'',
              custodiagarantia_nombre: $('#sinposesion_custodiagarantia'+num+' :selected').attr('nombre'),
              nombre: $('#sinposesion_nombre'+num).val(),
              doeruc: $('#sinposesion_doeruc'+num).val(),
              direccion: $('#sinposesion_direccion'+num).val(),
              representante_doeruc: $('#sinposesion_representante_doeruc'+num).val(),
              representante_nombre: $('#sinposesion_representante_nombre'+num).val(),
              estado_id: $('#sinposesion_estado'+num+' :selected').val()!=undefined?$('#sinposesion_estado'+num+' :selected').val():'',
              estado_nombre: $('#sinposesion_estado'+num+' :selected').attr('nombre'),
              constituciongarantia_id: 2,
              constituciongarantia_nombre: 'Sin entrega de posesión',
          });
      });
      return JSON.stringify(data);
  }
  
  //Reprentante Común
  
  @foreach($credito_representantecomun as $value)
      agrega_representantecomun('{{$value->nombre}}',
                                '{{$value->doi}}',
                                '{{$value->direccion}}',
                                '{{$value->ubigeo_id}}',
                                '{{$value->ubigeo_nombre}}',
                                '{{$value->estado_id}}');
  @endforeach
  
  function agrega_representantecomun(nombre='',doi='',direccion='',ubigeo='',ubigeonombre='',estado=''){
    var num   = $("#table-representantecomun > tbody").attr('num');
    let tabla = `
                <tr id="${num}">
                  <td><input type="text" class="form-control color_cajatexto" id="representantecomun_nombre${num}" value="${nombre}"></td>
                  <td><input type="text" class="form-control color_cajatexto" id="representantecomun_doi${num}" value="${doi}"></td>
                  <td><input type="text" class="form-control color_cajatexto" id="representantecomun_direccion${num}" value="${direccion}"></td>
                  <td>
                    <select class="form-control color_cajatexto" id="representantecomun_ubigeo${num}">
                      <option value=""></option>
                    </select>
                    <input type="hidden" value="${ubigeonombre}" id="representantecomun_ubigeo_nombre${num}">
                  </td>
                  <td>
                    <select class="form-control color_cajatexto" id="representantecomun_estado${num}">
                      <option value=""></option>
                      <option value="1" nombre="Activo" ${estado=='1'?'selected':''}>Activo</option>
                      <option value="2" nombre="Inactivo" ${estado=='2'?'selected':''}>Inactivo</option>
                    </select>
                  </td>
                  <td><button type="button" onclick="eliminar_representantecomun(${num})" class="btn btn-danger "><i class="fa-solid fa-trash"></i></button></td>
                </tr>`;

      $("#table-representantecomun > tbody").append(tabla);
      $("#table-representantecomun > tbody").attr('num',parseInt(num)+1); 
  
      if(ubigeo==0 || ubigeo==''){
          sistema_select2({ json:'ubigeo', input:'#representantecomun_ubigeo'+num});
      }else{
          sistema_select2({ json:'ubigeo', input:'#representantecomun_ubigeo'+num, val:ubigeo});
      }
    
      
       $('#representantecomun_ubigeo'+num).on("select2:select", function(e) {
            $('#representantecomun_ubigeo_nombre'+num).val(e.params.data.nombre);
        });
  }
  function eliminar_representantecomun(num){
      $("#table-representantecomun > tbody > tr#"+num).remove();
  }
  function seleccionar_representantecomun(){
      var data = [];
      $("#table-representantecomun > tbody > tr").each(function() {
          var num = $(this).attr('id');    
          console.log($('#representantecomun_ubigeo'+num+' :selected').val())
          data.push({ 
              nombre: $('#representantecomun_nombre'+num).val(),
              doi: $('#representantecomun_doi'+num).val(),
              direccion: $('#representantecomun_direccion'+num).val(),
              ubigeo_id: $('#representantecomun_ubigeo'+num+' :selected').val()!=''?$('#representantecomun_ubigeo'+num+' :selected').val():0,
              ubigeo_nombre: $('#representantecomun_ubigeo_nombre'+num).val(),
              estado_id: $('#representantecomun_estado'+num+' :selected').val()!=''?$('#representantecomun_estado'+num+' :selected').val():0,
              estado_nombre: $('#representantecomun_estado'+num+' :selected').attr('nombre'),
          });
      });
      return JSON.stringify(data);
  }
</script>
