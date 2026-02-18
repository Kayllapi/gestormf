<div class="modal-header">
  <h5 class="modal-title">Refinanciamiento de Crédito</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-credito-result">
             
            <div class="modal-body pb-0">
              
                <div class="row">
                    <div class="col-sm-12 col-md-7">
                        <div class="row">
                           <div class="col-sm-12 col-md-10">
                              <div class="row">
                                <label for="fecha_inicio" class="col-sm-3 col-form-label">AGENCIA</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="idagencia" disabled>
                                      <option></option>
                                      @foreach($agencias as $value)
                                          <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                                      @endforeach
                                    </select>
                                </div>
                              </div>
                            </div>
                          <div class="col-sm-12 col-md-2" style="text-align: right;">
                              <button type="button" class="btn btn-success" onclick="lista_credito()"><i class="fa-solid fa-search"></i> FILTRAR</button>
                          </div>
                        </div>
                        <div class="row">
                           <div class="col-sm-12 col-md-10">
                              <div class="row">
                                <label for="fecha_fin" class="col-sm-3 col-form-label">CLIENTE</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="idcliente">
                                      <option></option>
                                    </select>
                                </div>
                              </div>
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

  sistema_select2({ input:'#idagencia',val:'{{$tienda->id}}' });
  sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idcliente' });
  
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
          //idasesor : $('#idasesor').val(),
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

