<div class="modal-header">
  <h5 class="modal-title">Control de Apertura y Cierre de Ope. de Caja</h5>
  
  <button type="button" class="btn-close" onclick="ir_inicio()"></button>
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
                           <div class="col-sm-12 col-md-6">
                              <div class="row">
                                <label for="fecha_corte" class="col-sm-2 col-form-label">FECHA</label>
                                <div class="col-sm-4">
                                    <input type="date" class="form-control" id="fecha_corte" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-3" style="text-align: right;">
                                    <button type="button" class="btn btn-success" onclick="lista_credito()"><i class="fa-solid fa-search"></i> FILTRAR</button>
                                </div>
                              </div>
                            </div>
                        </div>
                                
                    </div>
                      <div class="col-sm-12 col-md-5" style="text-align: right;">
                        <div>
                          @if($estado_cierre_institucional=='PENDIENTE')
                            <button type="button" class="btn btn-warning mb-1" style="font-weight: bold;width: 190px;">
                              SIN CIERRE INSTI.
                            </button>
                          @elseif($estado_cierre_institucional=='NOEXISTE')
                            <button type="button" class="btn btn-warning mb-1" style="font-weight: bold;width: 190px;">
                              SIN CIERRE INSTI.
                            </button>
                          @else
                            <button type="button" class="btn btn-success mb-1" style="font-weight: bold;width: 190px;">
                              <i class="fa-solid fa-check" style="font-weight: bold;"></i> CON CIERRE INSTI.
                            </button>
                          @endif
                            <button type="button" class="btn btn-primary mb-1" onclick="cierre()" style="font-weight: bold;width: 190px;">
                              <i class="fa-solid fa-check" style="font-weight: bold;"></i> CIERRE INSTITUCIONAL
                            </button>
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
          <div class="card-body" id="tabla-controlaperturaopecaja" style="overflow-y: scroll;height: 260px;padding-top: 0px;padding-bottom: 0px;">
          </div>
          <div class="card-body" id="tabla-controlaperturaopecaja1">
          </div>
        </div>
      </div>
      <div style="text-align: right;">
        <button type="button" class="btn btn-info" onclick="reporte()" style="font-weight: bold;">
          <i class="fa-solid fa-file-pdf" style="color:#000 !important;font-weight: bold;"></i> REPORTE PDF</button>
      </div>
</div>
<style>
  .tabla_bg_bordered {
        --bs-table-accent-bg: var(--bs-table-striped-bg);
  }
</style>
<script>

  sistema_select2({ input:'#idagencia',val:'{{$tienda->id}}' });
  sistema_select2({ idtienda:{{$tienda->id}}, json:'tienda:usuario', input:'#idcliente' });
  sistema_select2({ input:'#idasesor' });
  
  lista_credito();
  function lista_credito(){
    
    $.ajax({
      url:"{{url('backoffice/0/cvcontrolaperturaopecaja/showtable')}}",
      type:'GET',
      data: {
          idagencia : $('#idagencia').val(),
          fecha : $('#fecha_corte').val(),
      },
      success: function (res){
        $('#tabla-controlaperturaopecaja').html(res.html);
        $('#tabla-controlaperturaopecaja1').html(res.html1);
        $("tr#show_data_select").on("click", function() {
            $('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        });
      }
    })
  }
 
    function cierre(){
        var fecha_corte = $('#fecha_corte').val();
        modal({ route:"{{ url('backoffice/'.$tienda->id) }}/cvcontrolaperturaopecaja/0/edit?view=cierre&fecha_corte="+fecha_corte,  size: 'modal-sm' });
    }
 
   function reporte(){
      let url = "{{ url('backoffice/'.$tienda->id) }}/cvcontrolaperturaopecaja/0/edit?view=reporte";
      modal({ route: url,size:'modal-fullscreen' })
   }
</script>  

