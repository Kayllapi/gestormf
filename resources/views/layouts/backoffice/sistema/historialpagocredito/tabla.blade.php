<div class="modal-header">
  <h5 class="modal-title">Historial de Pago Detallado de Créditos</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()" style="font-size: 20px;"></button>
</div>
<div class="modal-body">
  <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body p-2" id="form-credito-result">
             
            <div class="modal-body">
              
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <div class="row">
                           <div class="col-sm-12 col-md-4">
                              <div class="row">
                                <label for="fecha_inicio" class="col-sm-3 col-form-label">AGENCIA</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="idagencia">
                                      <option></option>
                                      @foreach($agencias as $value)
                                          <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                                      @endforeach
                                    </select>
                                </div>
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-5">
                              <div class="row">
                                <label for="corte" class="col-sm-2 col-form-label">PERIODO</label>
                                <div class="col-sm-4">
                                  <input type="date" class="form-control" id="fechainicio" value="{{ date('Y-m-d') }}">
                                </div>
                                <label for="corte" class="col-sm-1 col-form-label">AL</label>
                                <div class="col-sm-4">
                                  <input type="date" class="form-control" id="fechafin" value="{{ date('Y-m-d') }}">
                                </div>
                              </div>
                            </div>  
                            <div class="col-sm-12 col-md-1">
                                <button type="button" class="btn btn-success" onclick="verpdf()"><i class="fa-solid fa-search"></i> FILTRAR</button>
                            </div>
                            <div class="col-sm-12 col-md-2">
                                <div style="text-align: right;">
                                  <button type="button" class="btn btn-info" onclick="exportar_excel()" style="font-weight: bold;">
                                    <i class="fa-solid fa-file-excel" style="color:#000 !important;font-weight: bold;"></i>
                                    EXPORTAR EXCEL</button>
                                </div>
                            </div>
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
        <iframe id="iframe_acta_aprobacion" frameborder="0" width="100%" height="600px"></iframe>
        </div>
      </div>
</div>
<script>
    sistema_select2({ input:'#idagencia',val:'{{$tienda->id}}' });
    verpdf();
    function verpdf(){
        let fechainicio = $('#fechainicio').val();
        let fechafin = $('#fechafin').val();
        let idagencia = $('#idagencia').val();
        $('#iframe_acta_aprobacion').attr('src','{{ url('/backoffice/'.$tienda->id.'/historialpagocredito/0/edit?view=pdf_reporte') }}&fechainicio='+fechainicio+'&fechafin='+fechafin+'&idagencia='+idagencia+'#zoom=100');
    }
  
   function exportar_excel(){
        window.location.href = '{{url('backoffice/'.$tienda->id.'/historialpagocredito/0/edit')}}?view=exportar_excel&fechainicio='+$('#fechainicio').val()+
              '&fechafin='+$('#fechafin').val()+
              '&idagencia='+$('#idagencia').val()+
              '&idagencia='+$('#idagencia').val()+
              '&tipo=admin';
    }
</script>  

