<div class="modal-header">
  <h5 class="modal-title">Historial de Operaciones Extornadas</h5>
  
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
                                    <select class="form-control" id="idagencia">
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
                                <label for="fecha_inicio" class="col-sm-2 col-form-label">DEL</label>
                                <div class="col-sm-10">
                                  <input type="date" class="form-control" id="fecha_inicio" value="{{ date('Y-m-d') }}">
                                </div>
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                              <div class="row">
                                <label for="fecha_fin" class="col-sm-2 col-form-label">AL</label>
                                <div class="col-sm-10">
                                  <input type="date" class="form-control" id="fecha_fin" value="{{ date('Y-m-d') }}">
                                </div>
                              </div>
                            </div>   
                            <div class="col-sm-12 col-md-1">
                                <button type="button" class="btn btn-success" onclick="verpdf()"><i class="fa-solid fa-search"></i> FILTRAR</button>
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
        <iframe id="iframe_acta_aprobacion" frameborder="0" width="100%" style="height: calc(100vh - 200px);"></iframe>
        </div>
      </div>
</div>
<script>
  sistema_select2({ input:'#idagencia',val:'{{$tienda->id}}' });
verpdf();
function verpdf(){
    let fecha_inicio = $('#fecha_inicio').val();
    let fecha_fin = $('#fecha_fin').val();
    let idagencia = $('#idagencia').val();
    $('#iframe_acta_aprobacion').attr('src','{{ url('/backoffice/'.$tienda->id.'/operacionextornada/0/edit?view=pdf_extorno') }}&fecha_inicio='+fecha_inicio+'&fecha_fin='+fecha_fin+'&idagencia='+idagencia+'#zoom=90');
}
</script>  

