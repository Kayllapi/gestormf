<div class="modal-header">
    <h5 class="modal-title">
     Ingresos Extraordianrios
      <a href="javascript:;" 
         class="btn btn-primary" 
         onclick="load_nuevo_ingresoextraordinario()">
        <i class="fa-solid fa-plus"></i> Nuevo
      </a>
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>

<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-result-giro">
          </div>
        </div>
      </div>
      <div class="col-sm-12 mt-1">
        <div class="card">
          <div class="card-body p-2">
            <div class="modal-body pb-0">
              <div class="row">
                <label class="col-sm-3 col-form-label" style="text-align: right;">Fecha inicio</label>
                <div class="col-sm-2">
                  <input type="date" class="form-control" id="fechainicio" value="{{now()->format('Y-m-d')}}">
                </div>
                <label class="col-sm-1 col-form-label" style="text-align: right;">Fecha fin:</label>
                <div class="col-sm-2">
                  <input type="date" class="form-control" id="fechafin" value="{{now()->format('Y-m-d')}}">
                </div>
                <div class="col-sm-3">
                  <button type="button" class="btn btn-primary" onclick="lista_ingresoextraordinario()" style="font-weight: bold;">
                                  <i class="fa-solid fa-search"></i> 
                    Filtrar</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-12 mt-1">
        <div class="card">
          <div class="card-body">
            <div style="overflow-y: scroll;height: calc(100vh - 368px);">
            <table class="table table-striped table-hover" id="table-lista-ingresoextraordinario">
              <thead class="table-dark" style="position: sticky;top: 0;z-index:1;">
                <tr>
                  <th width="10px">N°</th>
                  <th>Operación</th>
                  <th>Monto (S/.)</th>
                  <th>Fecha de gasto</th>
                  <th>Descripción</th>
                  <th>Sustento</th>
                  <th>F. Pago</th>
                  <th>Banco</th>
                  <th>Validación</th>
                  <th>Usuario</th>
                </tr>
              </thead>
              <tbody>
              
              </tbody>
          </div>
            </table>
          </div>
        </div>
      </div>
  </div>
      <div class="text-end mt-1">
        <button type="button" class="btn btn-info" onclick="exportar_pdf()">
          <i class="fa-solid fa-file-pdf"></i> REPORTE</button>
      </div>
</div>
<script>
  lista_ingresoextraordinario();
  function lista_ingresoextraordinario(id){
    var fechainicio = $('#fechainicio').val();
    var fechafin = $('#fechafin').val();
    $.ajax({
      url:"{{url('backoffice/0/ingresoextraordinario/show_table')}}",
      type:'GET',
      data:{
          fechainicio: $('#fechainicio').val(),
          fechafin: $('#fechafin').val(),
      },
      success: function (res){
        $('#table-lista-ingresoextraordinario > tbody').html(res.html);
      }
    })
  }
  load_nuevo_ingresoextraordinario();
  function load_nuevo_ingresoextraordinario(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/ingresoextraordinario/create?view=registrar')}}", result:'#form-result-giro'});
  }
  
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
        
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/ingresoextraordinario/"+id+"/edit?view=editar", result:'#form-result-giro'});
    
  }

    function validar(idingresoextraordinario){
        modal({ route:"{{ url('backoffice/'.$tienda->id) }}/ingresoextraordinario/"+idingresoextraordinario+"/edit?view=validar",  size: 'modal-sm' });
    }
  
   function exportar_pdf(){
      let url = "{{ url('backoffice/'.$tienda->id) }}/ingresoextraordinario/0/edit?view=exportar&fechainicio="+$('#fechainicio').val()+
          "&fechafin="+$('#fechafin').val();
      modal({ route: url,size:'modal-fullscreen' })
   }
</script>

