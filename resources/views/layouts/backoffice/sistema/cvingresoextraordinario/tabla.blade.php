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
      @if(!$validacionDiaria['arqueocaja'])
            <div class="modal-body" style="position: absolute; z-index: 100;">
                <div class="alert bg-danger" style="height: 120px;">
                <br>
                <i class="fa fa-warning" style="font-size: 35px;"></i> <br>
                <b>Falta arquear caja {{ $validacionDiaria['fechacorte'] }}!!</b>
                </div>
            </div>
        @elseif($validacionDiaria['cierre_caja'])
            <div class="modal-body" style="position: absolute; z-index: 100;">
                <div class="alert bg-danger" style="height: 120px;">
                <br>
                <i class="fa fa-warning" style="font-size: 35px;"></i> <br>
                <b>Falta cerrar caja {{ $validacionDiaria['fechacorte'] }}!!</b>
                </div>
            </div>
        @elseif (!$apertura_caja)
        <div class="modal-body" style="position: absolute; z-index: 100;">
            <div class="alert bg-danger" style="height: 120px;">
              <br>
            <i class="fa fa-warning" style="font-size: 35px;"></i> <br>
            <b>Falta aperturar caja.</b>
            </div>
        </div>
      @elseif($arqueocaja)
        <div class="modal-body" style="position: absolute; z-index: 100;">
            <div class="alert bg-danger" style="height: 120px;">
              <br>
            <i class="fa fa-warning" style="font-size: 35px;"></i> <br>
            <b>Ya esta arqueado la caja!!</b>
            </div>
        </div>
      @endif
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-result-giro">
          </div>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2">
            <div class="modal-body">
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
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body" style="overflow-y: scroll;height: 300px;padding: 0;margin-top: 5px;overflow-x: scroll;">
            
            <table class="table table-striped table-hover" id="table-lista-ingresoextraordinario">
              <thead class="table-dark" style="position: sticky;top: 0; font-weight: bold;">
                <tr>
                  <td width="10px">N°</td>
                  <td>Operación</td>
                  <td>Monto (S/.)</td>
                  <td>Fecha de gasto</td>
                  <td>Descripción</td>
                  <td>Sustento</td>
                  <td>F. Pago</td>
                  <td>Banco</td>
                  <td>Validación</td>
                  <td>Usuario</td>
                </tr>
              </thead>
              <tbody>
              
              </tbody>
            </table>
          </div>
        </div>
      </div>
  </div>
  <div style="text-align: right;">
    <button type="button" class="btn btn-info" onclick="exportar_pdf()" style="font-weight: bold;">
      <i class="fa-solid fa-file-pdf" style="color:#000 !important;font-weight: bold;"></i> REPORTE PDF</button>
  </div>
</div>
<script>
  lista_ingresoextraordinario();
  function lista_ingresoextraordinario(id){
    var fechainicio = $('#fechainicio').val();
    var fechafin = $('#fechafin').val();
    $.ajax({
      url:"{{url('backoffice/0/cvingresoextraordinario/show_table')}}",
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
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/cvingresoextraordinario/create?view=registrar')}}", result:'#form-result-giro'});
  }
  
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
        
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/cvingresoextraordinario/"+id+"/edit?view=editar", result:'#form-result-giro'});
    
  }

    function validar(idingresoextraordinario){
        modal({ route:"{{ url('backoffice/'.$tienda->id) }}/cvingresoextraordinario/"+idingresoextraordinario+"/edit?view=validar",  size: 'modal-sm' });
    }
  
   function exportar_pdf(){
      let url = "{{ url('backoffice/'.$tienda->id) }}/cvingresoextraordinario/0/edit?view=exportar&fechainicio="+$('#fechainicio').val()+
          "&fechafin="+$('#fechafin').val();
      modal({ route: url,size:'modal-fullscreen' })
   }
</script>

