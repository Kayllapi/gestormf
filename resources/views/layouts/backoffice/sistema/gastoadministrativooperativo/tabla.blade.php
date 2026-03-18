<div class="modal-header">
    <h5 class="modal-title">
      Gasto Administrativo y Operativo
      <a href="javascript:;" 
         class="btn btn-primary" 
         onclick="load_nuevo_gastoadministrativooperativo()">
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
                  <button type="button" class="btn btn-primary" onclick="lista_gastoadministrativooperativo()" style="font-weight: bold;">
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
            <div style="overflow-y: scroll;height: calc(100vh - 401px);">
            <table class="table table-striped table-hover" id="table-lista-gastoadministrativooperativo">
              <thead class="table-dark" style="position: sticky;top: 0;z-index:1;">
                <tr>
                  <th rowspan="2" width="10px">N°</th>
                  <th rowspan="2">Operación</th>
                  <th rowspan="2">Monto (S/.)</th>
                  <th rowspan="2">Fecha de gasto</th>
                  <th rowspan="2">Descripción</th>
                  <th style="text-align: center;background-color: #9d9d9d !important;" colspan="2">Sustento</th>
                  <th rowspan="2">F. Pago</th>
                  <th rowspan="2">Banco</th>
                  <th rowspan="2">Validación</th>
                  <th rowspan="2">Usuario</th>
                </tr>
                <tr>
                  <th>Comprobante</th>
                  <th>N° y Detalle de Comp.</th>
                </tr>
              </thead>
              <tbody>
              
              </tbody>
            </table>
            </div>
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
  lista_gastoadministrativooperativo();
  function lista_gastoadministrativooperativo(id){
    var fechainicio = $('#fechainicio').val();
    var fechafin = $('#fechafin').val();
    $.ajax({
      url:"{{url('backoffice/0/gastoadministrativooperativo/show_table')}}",
      type:'GET',
      data:{
          fechainicio: $('#fechainicio').val(),
          fechafin: $('#fechafin').val(),
      },
      success: function (res){
        $('#table-lista-gastoadministrativooperativo > tbody').html(res.html);
      }
    })
  }
  load_nuevo_gastoadministrativooperativo();
  function load_nuevo_gastoadministrativooperativo(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/gastoadministrativooperativo/create?view=registrar')}}", result:'#form-result-giro'});
  }
  
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
        
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/gastoadministrativooperativo/"+id+"/edit?view=editar", result:'#form-result-giro'});
    
  }
  
    function validar(idgastoadministrativooperativo){
        modal({ route:"{{ url('backoffice/'.$tienda->id) }}/gastoadministrativooperativo/"+idgastoadministrativooperativo+"/edit?view=validar",  size: 'modal-sm' });
    }
  
   function exportar_pdf(){
      let url = "{{ url('backoffice/'.$tienda->id) }}/gastoadministrativooperativo/0/edit?view=exportar&fechainicio="+$('#fechainicio').val()+
          "&fechafin="+$('#fechafin').val();
      modal({ route: url,size:'modal-fullscreen' })
   }
</script>

