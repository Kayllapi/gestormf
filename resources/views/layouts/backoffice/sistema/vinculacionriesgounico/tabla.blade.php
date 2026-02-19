<div class="modal-header">
  <h5 class="modal-title">Vinculación por Riesgo Unico</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-credito-result">
             
            <div class="modal-body pb-0">
              
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <div class="row">
                           <div class="col-sm-6 col-md-6">
                              <div class="row">
                                <label for="fecha_inicio" class="col-sm-4 col-form-label">CLIENTE/AVAL</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="idcliente">
                                      <option></option>
                                    </select>
                                </div>
                              </div>
                            </div>
                           <div class="col-sm-6 col-md-6">
                              <div class="row">
                                <label for="fecha_fin" class="col-sm-4 col-form-label">DIRECCIÓN DE DOMICILIO</label>
                                <div class="col-sm-8">
                                    <input type="text" disabled value="" class="form-control" id="data-direccion-domicilio" style="background-color: white;">
                                </div>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                          <div class="col-sm-6 col-md-6" style="text-align: left;">
                              <button type="button" class="btn btn-success" onclick="lista_credito()"><i class="fa-solid fa-search"></i> FILTRAR</button>
                          </div>
                           <div class="col-sm-6 col-md-6">
                              <div class="row">
                                <label for="fecha_fin" class="col-sm-4 col-form-label">DIRECCIÓN DE NEGOCIO</label>
                                <div class="col-sm-8">
                                    <input type="text" disabled value="" class="form-control" id="data-direccion-negocio" style="background-color: white;">
                                </div>
                              </div>
                            </div>
                        </div>
                        <div class="row">
                        </div>
                                
                    </div>
                </div>
              
            </div> 
          </div>
        </div>
      </div>
  </div>
      <div class="col-sm-12 mt-1 mb-1">
        <div class="card">
          <div class="card-body">
            <div class="modal-body p-0" style="overflow-y: scroll;height: calc(-271px + 100vh);">
            <table class="table table-striped table-hover" id="table-lista-credito">
              <thead class="table-dark" style="position: sticky;top: 0;"> 
                <tr>
                  <th style="text-align: center;" rowspan="2" colspan="3">VINCULADOS</th>
                  <th style="text-align: center;" colspan="8">RIESGO Saldo de Créd.(S/.)</th>
                  <th style="text-align: center;" rowspan="3">TOTAL</th>
                </tr> 
                <tr>
                  <th style="text-align: center;" colspan="4">POR PROPIEDAD Y AVAL</th>
                  <th style="text-align: center;" colspan="2">POR NEGOCIO</th>
                  <th style="text-align: center;" colspan="2">FAMILIARES EN LA EMPRESA</th>
                </tr>
                <tr>
                  <th style="text-align: center;">N°</th>
                  <th style="text-align: center;">RUC/DNI/CE</th>
                  <th style="text-align: center;">Nombres y Apellidos</th>
                  <th style="text-align: center;">Cnta</th>
                  <th style="text-align: center;">Avalados por Cliente al Vinculado con  MISMO DOMICILIO</th>
                  <th style="text-align: center;">Cnta</th>
                  <th style="text-align: center;">Avalados por Vinculado al Cliente  con MISMO DOMICILIO </th>
                  <th style="text-align: center;">Cnta</th>
                  <th style="text-align: center;">Misma dirección de negocio del vinculado</th>
                  <th style="text-align: center;">Cnta</th>
                  <th style="text-align: center;">Usuario Vinculado con mismo domicilio del Cliente</th>
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
                                <button type="button" class="btn btn-info" onclick="exportar_pdf()">
                                  <i class="fa-solid fa-file-pdf" style="color:#000 !important;font-weight: bold;"></i> REPORTE PDF</button>
                              </div>

</div>

<style>
.table-dark {
    border-color: #afafaf;
}
</style>
<script>


  $('#idcliente').select2({
      ajax: {
          url:"{{url('backoffice/'.$tienda->id.'/vinculacionriesgounico/show_credito')}}",
          dataType: 'json',
          delay: 250,
          data: function (params) {
              return {
                    buscar: params.term
              };
          },
          processResults: function (data) {
              return {
                  results: data
              };
          },
          cache: true
      },
      placeholder: '-- Seleccionar --',
      minimumInputLength: 2,
      theme: 'bootstrap-5',
      dropdownParent: $('#idcliente').parent().parent()
  });
  
  $("#idcliente").on("change", function(e) {
      lista_credito_cliente(e.currentTarget.value);
  });
  
  function lista_credito_cliente(id){
    $.ajax({
      url:"{{url('backoffice/0/vinculacionriesgounico/showcliente')}}",
      type:'GET',
      data: {
          idcliente : id
      },
      success: function (res){
        $('#data-direccion-domicilio').val(res.direcciondomicilio);
        $('#data-direccion-negocio').val(res.direccionnegocio);
      }
    })
  }
  
  //lista_credito();
  function lista_credito(){
    //let estado_credito = $('input[name="estado_credito"]:checked').val();
    
    $.ajax({
      url:"{{url('backoffice/0/vinculacionriesgounico/showtable')}}",
      type:'GET',
      data: {
          idcliente : $('#idcliente').val(),
      },
      success: function (res){
        $('#table-lista-credito > tbody').html(res.html);
        $("tr#show_data_select").on("click", function() {
            $('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        });
      }
    })
  }
  function show_data(e) {
    let id = $(e).attr('data-valor-columna');
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/propuestacredito/"+id+"/edit?view=opciones" });  
  }

   function exportar_pdf(){
      let url = "{{ url('backoffice/'.$tienda->id) }}/vinculacionriesgounico/0/edit?view=exportar&idcliente="+$('#idcliente').val()+
          "&tipo=admin";
      modal({ route: url,size:'modal-fullscreen' })
   }
</script>  

