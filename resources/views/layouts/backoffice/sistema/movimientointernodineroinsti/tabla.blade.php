<div class="modal-header">
    <h5 class="modal-title">
     Movimiento Interno de Efectivo Institucional
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()" style="font-size: 20px;"></button>
</div>

<div class="modal-body">
  <div class="mb-1">
    <span class="badge d-block" style="margin-top: 10px;text-align:center;">HABILITACIÓN Y GESTIÓN DE LIQUIDEZ ( II )</span>
  </div>
  <div class="row">
          <div class="col-sm-12">
            <div class="card">
              <div class="card-body p-2">
                <div class="modal-body">
                  <div class="row">
                    <label class="col-sm-3 col-form-label" style="text-align: right;">Fecha inicio</label>
                    <div class="col-sm-2">
                      <input type="date" class="form-control" id="fechainicio" value="{{now()->format('Y-m-d')}}">
                    </div>
                    <label class="col-sm-2 col-form-label" style="text-align: right;">Fecha fin:</label>
                    <div class="col-sm-2">
                      <input type="date" class="form-control" id="fechafin" value="{{now()->format('Y-m-d')}}">
                    </div>
                    <div class="col-sm-1">
                      <button type="button" class="btn btn-primary" 
                              onclick="lista_movimientointernodinero_retiro2(),
                                lista_movimientointernodinero_deposito2()" style="font-weight: bold;">
                                      <i class="fa-solid fa-search"></i> 
                        Filtrar</button>
                    </div>
                    <div class="col-sm-2">
                        <div style="text-align: right;">
                          <button type="button" class="btn btn-info" onclick="valid_reporte()" style="font-weight: bold;">
                            <i class="fa-solid fa-file-pdf" style="color:#000 !important;font-weight: bold;"></i> REPORTE PDF</button>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
    <div class="col-md-6">
      <div class="mb-1">
      <h5 class="modal-title text-center">
        RETIRO
        <a href="javascript:;" 
           class="btn btn-primary" 
           onclick="load_nuevo_movimientointernodinero_retiro2()"
           style="margin-top: -5px;padding: 2px 8px 2px 8px;">
          <i class="fa-solid fa-plus"></i> Nuevo
        </a>
      </h5>
      </div>
      <div class="row">
          <div class="col-sm-12">
            <div class="card">
              <div class="card-body p-2" id="form-result-giro_retiro2">
              </div>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="card">
              <div class="card-body" style="overflow-y: scroll;height: 150px;padding: 0;margin-top: 5px;overflow-x: scroll;">

                <table class="table table-striped table-hover table-bordered" id="table-lista-movimientointernodinero_retiro2">
                  <thead class="table-dark" style="position: sticky;top: 0;">
                    <tr>
                      <td>Código</td>
                      <td>Fuente de Retiro</td>
                      <td>Monto (S/.)</td>
                      <td>Banco</td>
                      <td>N° operación (banco)</td>
                      <td>Descripción</td>
                      <td>Fecha</td>
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
    </div>
    <div class="col-md-6">
      <div class="mb-1">
      <h5 class="modal-title text-center">
        DEPÓSITO
      </h5>
      </div>
      <div class="row">
          <div class="col-sm-12">
            <div class="card">
              <div class="card-body p-2" id="form-result-giro_deposito2">
              </div>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="card">
              <div class="card-body" style="overflow-y: scroll;height: 150px;padding: 0;margin-top: 5px;overflow-x: scroll;">

                <table class="table table-striped table-hover table-bordered" id="table-lista-movimientointernodinero_deposito2">
                  <thead class="table-dark" style="position: sticky;top: 0;">
                    <tr>
                      <td>Código</td>
                      <td>Destino de Depósito</td>
                      <td>Monto (S/.)</td>
                      <td>Banco</td>
                      <td>N° operación (banco)</td>
                      <td>Descripción</td>
                      <td>Fecha</td>
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
    </div>
  </div>
</div>
<script>
  /// HABILITACIÓN Y GESTIÓN DE LIQUIDEZ ( II )
  lista_movimientointernodinero_retiro2();
  function lista_movimientointernodinero_retiro2(id){
    load_nuevo_movimientointernodinero_retiro2();
    $.ajax({
        url:"{{url('backoffice/0/movimientointernodineroinsti/show_table_retiro2')}}",
        type:'GET',
        data:{
            fechainicio: $('#fechainicio').val(),
            fechafin: $('#fechafin').val(),
        },
        success: function (res){
            $('#table-lista-movimientointernodinero_retiro2 > tbody').html(res.html);
        }
    })
  }
  load_nuevo_movimientointernodinero_retiro2();
  function load_nuevo_movimientointernodinero_retiro2(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/movimientointernodineroinsti/create?view=registrar_retiro2')}}", result:'#form-result-giro_retiro2'});
  }
  function show_data_retiro2(e) {
    let id = $(e).attr('data-valor-columna');
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/movimientointernodineroinsti/"+id+"/edit?view=editar_retiro2", result:'#form-result-giro_retiro2'});
  }
  
  lista_movimientointernodinero_deposito2();
  function lista_movimientointernodinero_deposito2(id){
    load_nuevo_movimientointernodinero_deposito2();
    $.ajax({
        url:"{{url('backoffice/0/movimientointernodineroinsti/show_table_deposito2')}}",
        type:'GET',
        data:{
            fechainicio: $('#fechainicio').val(),
            fechafin: $('#fechafin').val(),
        },
        success: function (res){
            $('#table-lista-movimientointernodinero_deposito2 > tbody').html(res.html);
        }
    })
  }
  load_nuevo_movimientointernodinero_deposito2();
  function load_nuevo_movimientointernodinero_deposito2(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/movimientointernodineroinsti/create?view=registrar_deposito2')}}", result:'#form-result-giro_deposito2'});
  }
  function show_data_deposito2(e) {
    let id = $(e).attr('data-valor-columna');
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/movimientointernodineroinsti/"+id+"/edit?view=editar_deposito2", result:'#form-result-giro_deposito2'});
  }
   function exportar_pdf(){
      let url = "{{ url('backoffice/'.$tienda->id) }}/movimientointernodineroinsti/0/edit?view=exportar&fechainicio="+$('#fechainicio').val()+
          "&fechafin="+$('#fechafin').val();
      modal({ route: url,size:'modal-fullscreen' })
   }
  
  function valid_reporte(){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/movimientointernodineroinsti/0/edit?view=valid_reporte')}}",  size: 'modal-sm'  });  
  }
</script>

