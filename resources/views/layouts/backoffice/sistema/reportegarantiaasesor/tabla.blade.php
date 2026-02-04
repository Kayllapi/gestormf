<div class="modal-header">
  <h5 class="modal-title">
    Garantias con créditos vigentes y prendarios cancelados por entregar</h5>
  
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
                            <label for="corte" class="col-sm-1 col-form-label">MODALIDAD</label>
                            <div class="col-sm-12 col-md-2">
                                    <select class="form-control" id="idmodalidad">
                                      <option>TODO</option>
                                      <option value="1">GARANTIA PRENDARIA</option>
                                      <option value="2">GARANTIA REGULAR</option>
                                    </select>
                            </div>  
                            <label for="corte" class="col-sm-1 col-form-label">ASESOR/EJEC.</label>
                            <div class="col-sm-12 col-md-3">
                                  <select class="form-control" id="idasesor">
                                      <option></option>
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
                            <div class="col-sm-12 col-md-1">
                                <button type="button" class="btn btn-success" onclick="verpdf()"><i class="fa-solid fa-search"></i> FILTRAR</button>
                            </div>
                            <!--div class="col-sm-12 col-md-2">
                                <div style="text-align: right;">
                                  <button type="button" class="btn btn-info" onclick="exportar_excel()" style="font-weight: bold;">
                                    <i class="fa-solid fa-file-excel" style="color:#000 !important;font-weight: bold;"></i>
                                    EXPORTAR EXCEL</button>
                                </div>
                            </div-->
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
    sistema_select2({ input:'#idmodalidad' });
    sistema_select2({ input:'#idasesor',val:'{{Auth::user()->id}}' });
    verpdf();
    function verpdf(){
        let idmodalidad = $('#idmodalidad').val();
        let idasesor = $('#idasesor').val();
        let idagencia = $('#idagencia').val();
        $('#iframe_acta_aprobacion').attr('src','{{ url('/backoffice/'.$tienda->id.'/reportegarantia/0/edit?view=pdf_reporte') }}&idmodalidad='+idmodalidad+'&idasesor='+idasesor+'&idagencia='+idagencia+'#zoom=100');
    }
  
   function exportar_excel(){
        window.location.href = '{{url('backoffice/'.$tienda->id.'/reportegarantia/0/edit')}}?view=exportar_excel&idmodalidad='+$('#idmodalidad').val()+
              '&idasesor='+$('#idasesor').val()+
              '&idagencia='+$('#idagencia').val()+
              '&idagencia='+$('#idagencia').val()+
              '&tipo=admin';
    }
</script>  

