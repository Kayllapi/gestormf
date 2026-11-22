<div class="modal-header">
  <h5 class="modal-title">Refinanciamiento de Créditos Asesor/Ejecutivo</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-credito-result">
             
            <div class="modal-body pb-0">
              
                <div class="row">
                      <div class="col-sm-12 col-md-6">
                        <div class="row">
                            <label for="fecha_inicio" class="col-sm-3 col-form-label">AGENCIA</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{$tienda->nombreagencia}}" disabled>
                                <input type="hidden" id="idagencia" value="{{$tienda->id}}">
                                {{-- <select class="form-control" id="idagencia" disabled>
                                  <option></option>
                                  @foreach($agencias as $value)
                                      <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                                  @endforeach
                                </select> --}}
                            </div>
                        </div>
                        <div class="row">
                            <label for="fecha_fin" class="col-sm-3 col-form-label">CLIENTE</label>
                            <div class="col-sm-9 refinanciamiento-cliente">
                                <select class="form-control" id="idcliente">
                                  <option></option>
                                </select>
                            </div>
                        </div>
                      </div>
                      <div class="col-sm-12 col-md-6">
                          <div class="row">
                            <div class="col-sm-12 col-md-12" style="text-align: right;">
                                <button type="button" class="btn btn-success" onclick="lista_credito()"><i class="fa-solid fa-search"></i> FILTRAR</button>
                            </div>
                          </div>
                          <div class="row mt-1">
                            <label for="fecha_fin" class="col-sm-4 col-form-label" style="text-align: right;">EJECUTIVO</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="nombreasesor" value="{{Auth::user()->nombrecompleto}}" disabled>
                                <input type="hidden" id="idasesor" value="{{Auth::user()->id}}">
                                {{-- <select class="form-control" id="idasesor" disabled>
                                    <option></option>
                                </select> --}}
                            </div>
                          </div>
                      </div>
                      <!--div class="col-sm-12 col-md-5" style="text-align: right;">
                          <button type="button" class="btn btn-warning" onclick="vistapreliminar()"><i class="fa-solid fa-search"></i> VISTA PRELIMINAR</button>
                      </div-->
                </div>
              
            </div> 
          </div>
        </div>
      </div>
  </div>
      <div class="col-sm-12 mt-1">
        <div class="card">
          <div class="card-body p-2">
            <div class="modal-body p-0" style="overflow-y: scroll;height: calc(-238px + 100vh);">
            <table class="table table-striped table-hover" id="table-lista-credito">
              <thead class="table-dark" style="position: sticky;top: 0;">
                <tr style="font-weight: bold;">
                  <td>N°</td>
                  <td>CLIENTE</td>
                  <td>AVAL</td>
                  <td>N°. CUENTA</td>
                  <td>F.C.</td>
                  <td>DESEM.</td>
                  <td>CUOTAS</td>
                  <td>F. PAGO</td>
                  <td>F. DESEMBOLSO</td>
                  <td>CAJERO</td>
                  <td>OPE. EN</td>
                  <td>MODA. CRÉDITO</td>
                  <td>ASESOR</td>
                  <td>OPCIÓN</td>
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
<script>

  // sistema_select2({ input:'#idagencia',val:'{{$tienda->id}}' });
  $('#idcliente').select2({
    theme: 'bootstrap-5', dropdownParent: $('#idcliente').parent(),
    placeholder: '-- Seleccionar --', minimumInputLength: 2,
    ajax: {
      url: @json(url('backoffice/'.$tienda->id.'/refinanciamientoasesor/clientes')),
      dataType: 'json', delay: 250,
      data: params => ({buscar: params.term}),
      processResults: data => data
    }
  }).on('select2:select', function(e) {
    if (e.params.data.observado || e.params.data.tiene_credito_castigado) {
      const mensaje = [
        e.params.data.observado ? 'Cliente Observado' : '',
        e.params.data.tiene_credito_castigado ? 'Crédito Castigado' : ''
      ].filter(Boolean).join(' / ');
      modal({ route: @json(url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')) + '&mensaje=' + encodeURIComponent(mensaje), size: 'modal-sm' });
    }
    if (e.params.data.observado && !e.params.data.tiene_creditos_activos) {
      $('#idcliente').val(null).trigger('change');
      $('#nombreasesor').val(@json(Auth::user()->nombrecompleto));
      $('#idasesor').val(@json(Auth::user()->id));
      $('#table-lista-credito > tbody').empty();
      return;
    }
    $('#nombreasesor').val(e.params.data.nombreasesor);
    $('#idasesor').val(e.params.data.idasesor || '');
    $('#table-lista-credito > tbody').empty();
  }).on('select2:clear', function() {
    $('#nombreasesor').val(@json(Auth::user()->nombrecompleto));
    $('#idasesor').val(@json(Auth::user()->id));
    $('#table-lista-credito > tbody').empty();
  });
  // sistema_select2({ input:'#idasesor',val:'{{Auth::user()->id}}' });

  // mostrarAsesor();
  // function mostrarAsesor(){
  //     $.ajax({
  //         url:"{{url('backoffice/'.$tienda->id.'/garantiaremateagencia/show_asesor')}}",
  //         type:'GET',
  //         data: {
  //             idtienda : $('#idagencia').val(),
  //         },
  //         success: function (respuesta){
  //             $('#idasesor').html(respuesta);  
  //             sistema_select2({ input:'#idasesor',val:'{{Auth::user()->id}}' });
  //         }
  //     })
  // }
  
  //lista_credito();
  function lista_credito(){
    //let estado_credito = $('input[name="estado_credito"]:checked').val();
    
    /*if($('#idcliente').val()==''){
        return false;
    }*/
    
    $.ajax({
      url:"{{url('backoffice/0/refinanciamiento/showtable')}}",
      type:'GET',
      data: {
          idagencia : $('#idagencia').val(),
          idcliente : $('#idcliente').val(),
          idasesor : $('#idasesor').val(),
          inicio : $('#fecha_inicio').val(),
          fin : $('#fecha_fin').val(),
          tipo : 'asesor',
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
    //$('tr.selected').removeClass('selected');
    //$(e).addClass('selected');
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/propuestacredito/"+id+"/edit?view=opciones" });  
  }
  function show_refinanciar(e) {
    let id = $(e).attr('refinanciar-valor-columna');
    //$('tr.selected').removeClass('selected');
    //$(e).addClass('selected');
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/refinanciamiento/"+id+"/edit?view=refinanciar", size: 'modal-fullscreen' });  
  }
 
   function vistapreliminar(){
      let idcredito = $('#table-lista-credito > tbody > tr.selected').attr('idcredito');
                        
      if(idcredito == "" || idcredito == undefined ){
        alert('Debe de seleccionar un crédito.');   
        return false;
      }
      let url = "{{ url('backoffice/'.$tienda->id) }}/desembolsado/"+idcredito+"/edit?view=desembolsar";
      modal({ route: url, size: 'modal-fullscreen' })
   }
</script>  

