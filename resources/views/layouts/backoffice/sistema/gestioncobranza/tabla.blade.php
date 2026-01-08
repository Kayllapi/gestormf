<div class="modal-header">
  <h5 class="modal-title">Gestión de Cobranza Institucional</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()" style="font-size: 20px;"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-credito-result">
             
            <div class="modal-body">
              
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
                                <label for="fecha_fin" class="col-sm-3 col-form-label">EJECUTIVO</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="idasesor">
                                      <option value="0">TODOS</option>
                                      <?php
                                      $usuarios = DB::table('users')
                                          ->join('users_permiso','users_permiso.idusers','users.id')
                                          ->join('permiso','permiso.id','users_permiso.idpermiso')
                                          ->whereIn('users_permiso.idpermiso',[3,4,7])
                                          ->where('users_permiso.idtienda',$tienda->id)
                                          ->select('users.*','permiso.nombre as nombrepermiso')
                                          ->get();
                                      ?>
                                      @foreach($usuarios as $value)
                                      <option value="{{$value->id}}">{{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                                      @endforeach
                                    </select>
                                </div>
                              </div>
                            </div>
                        </div>
                        <div class="row">
                           <div class="col-sm-12 col-md-5">
                              <div class="row">
                                <label for="fecha_fin" class="col-sm-6 col-form-label">DÍAS VENCIDOS <span style="float:right;">DE</span></label>
                                <div class="col-sm-6">
                                    <input type="number" class="form-control" value="" id="dias_retencion_desde">
                                </div>
                              </div>
                           </div>
                           <div class="col-sm-12 col-md-5">
                              <div class="row">
                                <label for="fecha_fin" class="col-sm-3 col-form-label"><span style="float:right;">HASTA</span></label>
                                <div class="col-sm-6">
                                    <input type="number" class="form-control" value="" id="dias_retencion_hasta">
                                </div>
                              </div>
                           </div>
                        </div>
                                
                    </div>
                    <div class="col-sm-12 col-md-5" style="background-color: #bababa;border-radius: 5px;padding: 10px;">
                      <div style="margin-bottom: 5px;">LEYENDA DÍAS VENCIDOS</div>
                      <div>
                        <div style="float: left;">0 DÍAS</div>
                        <div style="float: left;background-color: #fff;height: 10px;width: 20px;margin: 5px;margin-right: 20px;"></div> 
                        <div style="float: left;">1-{{configuracion($tienda->id,'dias_tolerancia_garantia')['valor']}} DÍAS</div>
                        <div style="float: left;background-color: #b6e084;height: 10px;width: 20px;margin: 5px;margin-right: 20px;"></div> 
                        <div style="float: left;">> A {{configuracion($tienda->id,'dias_tolerancia_garantia')['valor']}} DÍAS</div>
                        <div style="float: left;background-color: #ff9d9d;height: 10px;width: 20px;margin: 5px;margin-right: 20px;"></div> 
                        <div style="float: left;">COMPRO.</div>
                        <div style="float: left;background-color: #f86b6b;height: 10px;width: 20px;margin: 5px;margin-right: 20px;"></div> 
                        <div style="float: left;">COMP. VENC.</div>
                        <div style="float: left;background-color: #ffb549;height: 10px;width: 20px;margin: 5px;"></div> 
                      </div>
                  </div>
                </div>
              
            </div> 
          </div>
        </div>
      </div>
  </div>
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body" style="overflow-y: scroll;height: 300px;padding: 0;margin-top: 5px;overflow-x: scroll;">

            <table class="table table-striped table-hover" id="table-lista-credito">
              <thead class="table-dark" style="position: sticky;top: 0;">
                <tr>
                  <td style="width:10px;"></td>
                  <td>N°</td>
                  <td>GP</td>
                  <td>CUENTA</td>
                  <td>DOI/RUC</td>
                  <td>Apellidos y Nombres</td>
                  <td>Fecha Desemb.</td>
                  <td>Monto Crédito (S/.)</td>
                  <td>F. Pago</td>
                  <td><span style="text-decoration: underline; font-weight: bold;">Saldo Cuotas Venc. (S/.)</span></td>
                  <td><span style="text-decoration: underline;font-weight: bold;">Días Atraso</span></td>
                  <td>Form. C.</td>
                  <td>Nro. de Cuotas Cumplido y Venc.</td>
                  <td>Tele./Celu.</td>
                  <td>F. Compromiso</td>
                  <td>Anotación</td>
                  <td>Direc/Domicilio</td>
                  <td>Calificación</td>
                  <td>Producto</td>
                  <td>Modalidad</td>
                  <td>DOI/RUC (Aval)</td>
                  <td>Ape. Nom. Aval</td>
                  <td>Ejecutivo</td>
                </tr>
              </thead>
              <tbody>
              
              </tbody>
            </table>
          </div>
        </div>
      </div>
                              <div style="text-align: right;">
                                <button type="button" class="btn btn-info" onclick="exportar_pdf()" style="font-weight: bold;">
                                  <i class="fa-solid fa-file-pdf" style="color:#000 !important;font-weight: bold;"></i> REPORTE PDF</button>
                              </div>
</div>
<style>
table .dropdown {
    position: inherit;
}
</style>
<script>

  sistema_select2({ input:'#idagencia',val:'{{$tienda->id}}' });
  sistema_select2({ input:'#idasesor' });
  sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idcliente' });
  
  lista_credito();
  function lista_credito(){
    //let estado_credito = $('input[name="estado_credito"]:checked').val();
    
    /*if($('#idcliente').val()==''){
        return false;
    }*/
    
    $.ajax({
      url:"{{url('backoffice/0/gestioncobranza/showtable')}}",
      type:'GET',
      data: {
          idagencia : $('#idagencia').val(),
          idasesor : $('#idasesor').val(),
          dias_retencion_desde : $('#dias_retencion_desde').val(),
          dias_retencion_hasta : $('#dias_retencion_hasta').val(),
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
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/gestioncobranza/"+id+"/edit?view=compromisopago" });  
  }
  function show_estadocuenta(e) {
    let id = $(e).attr('estadocuenta-valor-columna');
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/gestioncobranza/"+id+"/edit?view=estadocuenta", size: 'modal-fullscreen' });  
  }
  function show_notificacion(e) {
    let id = $(e).attr('notificacion-valor-columna');
    modal({ route:"{{url('backoffice')}}/{{$tienda->id}}/gestioncobranza/"+id+"/edit?view=notificacion", size: 'modal-fullscreen' });  
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
  
  

   function exportar_pdf(){
      let url = "{{ url('backoffice/'.$tienda->id) }}/gestioncobranza/0/edit?view=exportar&dias_retencion_desde="+$('#dias_retencion_desde').val()+
          "&dias_retencion_hasta="+$('#dias_retencion_hasta').val()+
          "&idagencia="+$('#idagencia').val()+
          "&idasesor="+$('#idasesor').val();
      modal({ route: url,size:'modal-fullscreen' })
   }
  
   function exportar_excel(){
        window.location.href = '{{url('backoffice/'.$tienda->id.'/gestioncobranza/0/edit')}}?view=exportar_excel&dias_retencion_desde='+$('#dias_retencion_desde').val()+
              '&dias_retencion_hasta='+$('#dias_retencion_hasta').val()+
              '&idagencia='+$('#idagencia').val();
    }
  
</script>  

