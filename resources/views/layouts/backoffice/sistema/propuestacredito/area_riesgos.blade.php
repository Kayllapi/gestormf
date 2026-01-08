<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/propuestacredito/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'area_riesgos',
          }
      },
      function(res){
        removecarga({input:'#mx-carga'})
        $('#success-message').removeClass('d-none');
        $('#success-message').text(res.mensaje);
        setTimeout(function() {
          $('#success-message').addClass('d-none');
        }, 5000);
        document.getElementById('iframe_acta_aprobacion').contentWindow.location.reload();
      },this)"> 
    <div class="modal-header" style="border-bottom: 0;">
        <h5 class="modal-title">OPINIÓN DE AREA DE RIESGOS</h5>
        <button type="button" class="btn-close text-white" id="modal-close-garantia-cliente" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body modal-body-cualitativa" style="min-height: 400px;">
          @if($credito->areariesgos!='')
          <div class="row">
            <div class="col-sm-4">
            </div>
            <div class="col-sm-4">
              <div class="mb-1">
                  <label>Responsables (Gestor de Riesgos) *</label>
                  <select class="form-select" id="idresponsable" disabled>
                      <option value=""></option>
                      @foreach($usuarios as $value)
                      <option value="{{$value->id}}">{{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                      @endforeach
                  </select>
              </div>
            </div>
          </div>
          @else
          <div class="row">
            <div class="col-sm-4">
            </div>
            <div class="col-sm-4">
              <div class="mb-1">
                  <label>Responsables (Gestor de Riesgos) *</label>
                  <select class="form-select" id="idresponsable">
                      <option value=""></option>
                      @foreach($usuarios as $value)
                      <option value="{{$value->id}}">{{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                      @endforeach
                  </select>
              </div>
              <div class="mb-1">
                  <label>Contraseña *</label>
                  <input type="password" class="form-control" id="responsableclave">
              </div>
              <button type="button" class="btn btn-success" onclick="validar_identificacion('EXEPCIONES_AUTORIZACIONES')"><i class="fa-solid fa-check"></i> Validar Identificación</button>
            </div>
          </div>
          <div id="cont_resultado" class="mt-1"></div>
          @endif
      
      <div id="cont_editar" <?php echo $credito->idusuario_areariesgos==0?'style="display:none;"':'' ?> >
          <div class="row">
            <div class="col-sm-12">
              <textarea class="form-control color_cajatexto" id="areariesgos" cols="30" rows="10">{{ $credito->areariesgos }}</textarea>
            </div>
          </div>

        <div class="row mt-1">

          <div class="col" style="flex: 0 0 0%;">
            <button type="submit" class="btn btn-success" id="btn-save-cuantitativa"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS</button>
          </div>
          <div class="col" style="flex: 1 0 0%;">
            <div id="success-message" class="alert alert-success d-none" style="text-align:left;"></div>
          </div>
          <div class="col" style="flex: 0 0 0%;">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i> SALIR</button>
          </div>
        </div>
      </div>
    </div>
</form> 
  
<script>
  @if($credito->idusuario_areariesgos!=0)
    sistema_select2({ input:'#idresponsable', val:'{{$credito->idusuario_areariesgos}}' });
  @else
    sistema_select2({ input:'#idresponsable' });
  @endif
  
    function validar_identificacion(permiso){
       load('#cont_resultado')
        $.ajax({
            url:"{{url('backoffice/0/propuestacredito/show_validaridentificacion')}}",
            type:'GET',
            data: {
                idresponsable : $('#idresponsable option:selected').val(),
                responsableclave : $('#responsableclave').val(),
                permiso : permiso
            },
            success: function (res){
                if(res['resultado']=='CORRECTO'){
                    $('#cont_editar').css('display','block');
                    $('#cont_resultado').html('');
                }else{
                    $('#cont_resultado').html('<div class="alert alert-danger"><b>Hay un error de Identificación</b></div>');
                }
            }
        });
    }
</script>