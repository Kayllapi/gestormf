<div class="modal-header">
  <h5 class="modal-title">Reporte Consolidado de Operaciones</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()"></button>
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
                                    <select class="form-control" id="idagencia" disabled>
                                      <option></option>
                                      @foreach($agencias as $value)
                                          <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                                      @endforeach
                                    </select>
                                </div>
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                              <div class="row">
                                <label for="corte" class="col-sm-3 col-form-label">CORTE</label>
                                <div class="col-sm-9">
                                  <input type="date" class="form-control" id="corte" value="{{ date('Y-m-d') }}">
                                </div>
                              </div>
                            </div>  
                            <div class="col-sm-12 col-md-2">
                                <button type="button" class="btn btn-success" onclick="verpdf()"><i class="fa-solid fa-search"></i> FILTRAR</button>
                            </div>
                            <div class="col-sm-12 col-md-3" style="text-align: right;">
                                <button type="button" class="btn btn-primary" onclick="arqueocaja()">
                                  ARQUEO DE CAJA</button>
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
        <div class="card" id="cont_iframe_acta_aprobacion">
        <iframe id="iframe_acta_aprobacion" frameborder="0" width="100%" height="600px"></iframe>
        </div>
      </div>
</div>
<script>
    sistema_select2({ input:'#idagencia',val:'{{$tienda->id}}' });
    verpdf();
    function verpdf(){
        let corte = $('#corte').val();
        let idagencia = $('#idagencia').val();
        $('#cont_iframe_acta_aprobacion').html(' <iframe id="iframe_acta_aprobacion" src="{{ url('/backoffice/'.$tienda->id.'/cvreporteconsolidadoopeadmin/0/edit?view=pdf_reporte') }}&corte='+corte+'&idagencia='+idagencia+'#zoom=100" frameborder="0" width="100%" height="600px"></iframe>');
    }
    function arqueocaja(){
        let corte = $('#corte').val();
        let idagencia = $('#idagencia').val();
        let url = "{{ url('backoffice/'.$tienda->id) }}/cvreporteconsolidadoopecaja/0/edit?view=arqueocaja&corte="+corte+"&idagencia="+idagencia;
        modal({ route: url })
    }
</script>  

