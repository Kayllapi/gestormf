<div class="modal-header">
    <h5 class="modal-title">
     Movimiento Interno de Efectivo
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>

<div class="modal-body">
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
                        onclick="lista_movimientointernodinero_retiro1(),
                                lista_movimientointernodinero_retiro3(),
                                lista_movimientointernodinero_deposito1(),
                                lista_movimientointernodinero_deposito3()" style="font-weight: bold;">
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
    <div class="mb-1">
        <span class="badge d-block" style="margin-top: 10px;text-align:center;">HABILITACIÓN Y GESTIÓN DE LIQUIDEZ ( I )</span>
    </div>
    <div class="row">
        @if(!$validacionDiaria['arqueocaja'])
                <div class="modal-body" style="position: absolute; z-index: 100;">
                    <div class="alert bg-danger" style="height: 110px;">
                    <br>
                    <i class="fa fa-warning" style="font-size: 35px;"></i> <br>
                    <b>Falta arquear caja {{ $validacionDiaria['fechacorte'] }}!!</b>
                    </div>
                </div>
            @elseif($validacionDiaria['cierre_caja'])
                <div class="modal-body" style="position: absolute; z-index: 100;">
                    <div class="alert bg-danger" style="height: 110px;">
                    <br>
                    <i class="fa fa-warning" style="font-size: 35px;"></i> <br>
                    <b>Falta cerrar caja {{ $validacionDiaria['fechacorte'] }}!!</b>
                    </div>
                </div>
            @elseif (!$apertura_caja)
        <div class="modal-body" style="position: absolute; z-index: 100;">
            <div class="alert bg-danger" style="height: 110px;">
            <br>
            <i class="fa fa-warning" style="font-size: 35px;"></i> <br>
            <b>Falta aperturar caja.</b>
            </div>
        </div>
        @elseif($arqueocaja)
        <div class="modal-body" style="position: absolute; z-index: 100;">
            <div class="alert bg-danger" style="height: 110px;">
            <br>
            <i class="fa fa-warning" style="font-size: 35px;"></i> <br>
            <b>Ya esta arqueado la caja!!</b>
            </div>
        </div>
        @endif
        <div class="col-md-6">
        <div class="mb-1 mt-1">
            <h5 class="modal-title text-center">
            RETIRO
            <a href="javascript:;" 
                class="btn btn-primary" 
                onclick="load_nuevo_movimientointernodinero_retiro1()"
                style="margin-top: -5px;padding: 2px 8px 2px 8px;">
                <i class="fa-solid fa-plus"></i> Nuevo
            </a>
            </h5>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                <div class="card-body p-2" id="form-result-giro_retiro1">
                </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                <div class="card-body" style="overflow-y: scroll;height: 210px;padding: 0;margin-top: 5px;overflow-x: scroll;">

                    <table class="table table-striped table-hover" id="table-lista-movimientointernodinero_retiro1">
                    <thead class="table-dark" style="position: sticky;top: 0; font-weight: bold;">
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
                <div class="card-body p-2" id="form-result-giro_deposito1">
                </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                <div class="card-body" style="overflow-y: scroll;height: 210px;padding: 0;margin-top: 5px;overflow-x: scroll;">

                    <table class="table table-striped table-hover" id="table-lista-movimientointernodinero_deposito1">
                    <thead class="table-dark" style="position: sticky;top: 0; font-weight: bold;">
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
    <div class="mb-1">
        <span class="badge d-block" style="margin-top: 10px;text-align:center;background-color: #ebbe3a;
        font-size: 18px;">APERTURA Y CIERRE DE CAJA</span>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-1">
                <h5 class="modal-title text-center">
                    RETIRO
                    <a href="javascript:;" 
                    class="btn btn-primary" 
                    onclick="load_nuevo_movimientointernodinero_retiro3()"
                    style="margin-top: -5px;padding: 2px 8px 2px 8px;">
                    <i class="fa-solid fa-plus"></i> Nuevo
                    </a>
                </h5>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body p-2" id="form-result-giro_retiro3">
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body" style="overflow-y: scroll;height: 180px;padding: 0;margin-top: 5px;overflow-x: scroll;">
                            <table class="table table-striped table-hover" id="table-lista-movimientointernodinero_retiro3">
                                <thead class="table-dark" style="position: sticky;top: 0; font-weight: bold;">
                                    <tr>
                                    <td>Código</td>
                                    <td>Fuente de Retiro</td>
                                    <td>Monto (S/.)</td>
                                    <td>Descripción</td>
                                    <td>Fecha</td>
                                    <td>Usuario</td>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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
                    <div class="card-body p-2" id="form-result-giro_deposito3">
                    </div>
                    </div>
                </div>
                <!--div class="col-sm-12">
                    <div class="card">
                    <div class="card-body p-2">
                        <div class="modal-body">
                        <div class="row">
                            <label class="col-sm-3 col-form-label" style="text-align: right;">Fecha inicio</label>
                            <div class="col-sm-2">
                            <input type="date" class="form-control" id="fechainicio_deposito3" value="{{now()->format('Y-m-d')}}">
                            </div>
                            <label class="col-sm-2 col-form-label" style="text-align: right;">Fecha fin:</label>
                            <div class="col-sm-2">
                            <input type="date" class="form-control" id="fechafin_deposito3" value="{{now()->format('Y-m-d')}}">
                            </div>
                            <div class="col-sm-3">
                            <button type="button" class="btn btn-primary" onclick="lista_movimientointernodinero_deposito3()" style="font-weight: bold;">
                                            <i class="fa-solid fa-search"></i> 
                                Filtrar</button>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div-->
                <div class="col-sm-12">
                    <div class="card">
                    <div class="card-body" style="overflow-y: scroll;height: 180px;padding: 0;margin-top: 5px;overflow-x: scroll;">

                        <table class="table table-striped table-hover" id="table-lista-movimientointernodinero_deposito3">
                        <thead class="table-dark" style="position: sticky;top: 0; font-weight: bold;">
                            <tr>
                            <td>Código</td>
                            <td>Destino de Depósito</td>
                            <td>Monto (S/.)</td>
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
  /// HABILITACIÓN Y GESTIÓN DE LIQUIDEZ ( I )
  lista_movimientointernodinero_retiro1();
  function lista_movimientointernodinero_retiro1(id){
    load_nuevo_movimientointernodinero_retiro1();
    $.ajax({
        url:"{{url('backoffice/0/cvmovimientointernodinero/show_table_retiro1')}}",
        type:'GET',
        data:{
            fechainicio: $('#fechainicio').val(),
            fechafin: $('#fechafin').val(),
        },
        success: function (res){
            $('#table-lista-movimientointernodinero_retiro1 > tbody').html(res.html);
        }
    })
  }
  load_nuevo_movimientointernodinero_retiro1();
  function load_nuevo_movimientointernodinero_retiro1(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/cvmovimientointernodinero/create?view=registrar_retiro1')}}", result:'#form-result-giro_retiro1'});
  }
  function show_data_retiro1(e) {
    let id = $(e).attr('data-valor-columna');
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/cvmovimientointernodinero/"+id+"/edit?view=editar_retiro1", result:'#form-result-giro_retiro1'});
  }
  
  lista_movimientointernodinero_deposito1();
  function lista_movimientointernodinero_deposito1(id){
    load_nuevo_movimientointernodinero_deposito1();
    $.ajax({
        url:"{{url('backoffice/0/cvmovimientointernodinero/show_table_deposito1')}}",
        type:'GET',
        data:{
            fechainicio: $('#fechainicio').val(),
            fechafin: $('#fechafin').val(),
        },
        success: function (res){
            $('#table-lista-movimientointernodinero_deposito1 > tbody').html(res.html);
        }
    })
  }
  load_nuevo_movimientointernodinero_deposito1();
  function load_nuevo_movimientointernodinero_deposito1(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/cvmovimientointernodinero/create?view=registrar_deposito1')}}", result:'#form-result-giro_deposito1'});
  }
  function show_data_deposito1(e) {
    let id = $(e).attr('data-valor-columna');
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/cvmovimientointernodinero/"+id+"/edit?view=editar_deposito1", result:'#form-result-giro_deposito1'});
  }

  /// CIERRE Y APERTURA DE CAJA
  lista_movimientointernodinero_retiro3();
  function lista_movimientointernodinero_retiro3(id){
    load_nuevo_movimientointernodinero_retiro3();
    $.ajax({
        url:"{{url('backoffice/0/cvmovimientointernodinero/show_table_retiro3')}}",
        type:'GET',
        data:{
            fechainicio: $('#fechainicio').val(),
            fechafin: $('#fechafin').val(),
        },
        success: function (res){
            $('#table-lista-movimientointernodinero_retiro3 > tbody').html(res.html);
        }
    })
  }
  load_nuevo_movimientointernodinero_retiro3();
  function load_nuevo_movimientointernodinero_retiro3(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/cvmovimientointernodinero/create?view=registrar_retiro3')}}", result:'#form-result-giro_retiro3'});
  }
  function show_data_retiro3(e) {
    let id = $(e).attr('data-valor-columna');
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/cvmovimientointernodinero/"+id+"/edit?view=editar_retiro3", result:'#form-result-giro_retiro3'});
  }
  
  lista_movimientointernodinero_deposito3();
  function lista_movimientointernodinero_deposito3(id){
    load_nuevo_movimientointernodinero_deposito3();
    $.ajax({
        url:"{{url('backoffice/0/cvmovimientointernodinero/show_table_deposito3')}}",
        type:'GET',
        data:{
            fechainicio: $('#fechainicio').val(),
            fechafin: $('#fechafin').val(),
        },
        success: function (res){
            $('#table-lista-movimientointernodinero_deposito3 > tbody').html(res.html);
        }
    })
  }
  load_nuevo_movimientointernodinero_deposito3();
  function load_nuevo_movimientointernodinero_deposito3(){
    pagina({ route:"{{url('backoffice/'.$tienda->id.'/cvmovimientointernodinero/create?view=registrar_deposito3')}}", result:'#form-result-giro_deposito3'});
  }
  function show_data_deposito3(e) {
    let id = $(e).attr('data-valor-columna');
    $('tr.selected').removeClass('selected');
    $(e).addClass('selected');
    pagina({ route:"{{url('backoffice')}}/{{$tienda->id}}/cvmovimientointernodinero/"+id+"/edit?view=editar_deposito3", result:'#form-result-giro_deposito3'});
  }
  
   function exportar_pdf(){
      let url = "{{ url('backoffice/'.$tienda->id) }}/cvmovimientointernodinero/0/edit?view=exportar&fechainicio="+$('#fechainicio').val()+
          "&fechafin="+$('#fechafin').val();
      modal({ route: url,size:'modal-fullscreen' })
   }
  
  function valid_reporte(){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/cvmovimientointernodinero/0/edit?view=valid_reporte')}}",  size: 'modal-sm'  });  
  }
</script>

