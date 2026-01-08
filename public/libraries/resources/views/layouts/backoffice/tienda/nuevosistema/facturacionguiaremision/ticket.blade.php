<?php
$facturacionrespuesta= DB::table('s_facturacionrespuesta')
    ->where('s_facturacionrespuesta.s_idfacturacionguiaremision',$facturacionguiaremision->id)
    ->orderBy('s_facturacionrespuesta.id','desc')
    ->limit(1)
    ->first();
?>
  @if(isset($facturacionrespuesta))
    @if($facturacionrespuesta->estado=='ACEPTADA')
        <div class="mensaje-info">
          {{$facturacionrespuesta->mensaje}}<br>
        </div>
        <div class="custom-form" style="margin-bottom: 5px;">
            <a href="{{ url('public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/guiaremision/'.$facturacionrespuesta->nombre.'.xml') }}" download class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
            <i class="fa fa-download"></i> Descargar XML</a>
            <a href="{{ url('public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/guiaremision/R-'.$facturacionrespuesta->nombre.'.zip') }}" download class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
            <i class="fa fa-download"></i> Descargar CDR</a>
            <a href="javascript:;" onclick="openDocumento('ticketpdf')" class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
            <i class="fa fa-file-pdf-o"></i> Ver PDF Ticket</a>
            <a href="javascript:;" onclick="openDocumento('a4pdf')" class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
            <i class="fa fa-file-pdf-o"></i> Ver PDF A4</a>
            <a href="javascript:;" class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;" id="modal-enviarcorreo" onclick="enviarcorreo()">
            <i class="fa fa-paper-plane"></i> Enviar a Correo</a>
        </div>
        <iframe id="content-ticketpdf" style="display: block" src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionguiaremision/'.$facturacionguiaremision->id.'/edit?view=ticketpdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>
        <iframe id="content-a4pdf" style="display: none" src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionguiaremision/'.$facturacionguiaremision->id.'/edit?view=a4pdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>
    @else
        @if($facturacionrespuesta->estado=='OBSERVACIONES')
          <div class="mensaje-info">
            El Comprobante tiene Observaciones:<br>
            {{$facturacionrespuesta->mensaje}}<br>
            <b>¿Deseas reenviar el comprobante?</b><br>
          </div>
        @elseif($facturacionrespuesta->estado=='RECHAZADA')
          <div class="mensaje-info">
            El Comprobante fue rechazado:<br>
            {{$facturacionrespuesta->mensaje}}<br>
            <b>¿Deseas reenviar el comprobante?</b><br>
          </div>
        @elseif($facturacionrespuesta->estado=='EXCEPCION')
          <div class="mensaje-info">
            El CDR es inválido debe tratarse de un error-excepción:<br>
            {{$facturacionrespuesta->mensaje}}<br>
            <b>¿Deseas reenviar el comprobante?</b><br>
          </div>
        @else
          <div class="mensaje-info">
            El envio del comprobante tiene un error!!<br>
            <b>¿Deseas reenviar el comprobante?</b><br>
          </div>
        @endif
          <form class="js-validation-signin px-30" 
                action="javascript:;" 
                onsubmit="callback({
                  route: 'backoffice/tienda/nuevosistema/{{ $tienda->id }}/facturacionguiaremision/{{$facturacionguiaremision->id}}',
                  method: 'PUT',
                  data:{
                      view: 'reenviarcomprobante'
                  } 
              },
              function(resultado){
                 location.reload(); 
              },this)">
              <div class="profile-edit-container">
                      <div class="custom-form" >
                          <button type="submit" class="btn  big-btn  color-bg flat-btn" id="button-carga" style="width: 100%;">Reenviar a SUNAT</button>
                      </div>
              </div>
          </form>
    @endif
@else
          <div class="mensaje-info">
            El Comprobante no fue enviado correctamente<br>
            <b>¿Deseas reenviar el comprobante?</b><br>
          </div>
          <form class="js-validation-signin px-30" 
                action="javascript:;" 
                onsubmit="callback({
                  route: 'backoffice/tienda/nuevosistema/{{ $tienda->id }}/facturacionguiaremision/{{$facturacionguiaremision->id}}',
                  method: 'PUT',
                  data:{
                      view: 'reenviarcomprobante'
                  } 
              },
              function(resultado){
                 location.reload(); 
              },this)">
              <div class="profile-edit-container">
                      <div class="custom-form" >
                          <button type="submit" class="btn  big-btn  color-bg flat-btn" id="button-carga" style="width: 100%;">Enviar a SUNAT</button>
                      </div>
              </div>
          </form>
@endif  
<script>
  function openDocumento(documento) {
    $('#content-a4pdf').css('display', 'none');
    $('#content-ticketpdf').css('display', 'none');
    if (documento == 'ticketpdf') {
      $('#content-ticketpdf').css('display', 'block');
    } else if (documento == 'a4pdf') {
      $('#content-a4pdf').css('display', 'block');
    }
  }
</script>
<!--  modal enviarcorreo --> 
<div class="main-register-wrap modal-enviarcorreo">
    <div class="main-overlay"></div>
    <div class="main-register-holder">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Enviar a Correo Electrónico</h3>
            <div class="mx-modal-cuerpo" id="contenido-enviarcorreo">
              <div id="mx-carga-enviarcorreo">
              <form class="js-validation-signin px-30" 
                  action="javascript:;" 
                  onsubmit="callback({
                    route: 'backoffice/tienda/nuevosistema/{{ $tienda->id }}/facturacionguiaremision/0',
                    method: 'PUT',
                    carga: '#mx-carga-enviarcorreo',
                    data:{
                        view: 'enviarcorreo',
                        idfacturacionguiaremision: {{$facturacionguiaremision->id}}
                    }
                },
                function(resultado){
                    $('#contenido-enviarcorreo').css('display','none');
                    confirm({
                        input:'#contenido-confirmar-enviarcorreo',
                        resultado:'CORRECTO',
                        mensaje:'Se ha enviado correctamente!.',
                        cerrarmodal:'.modal-enviarcorreo'
                    });       
                },this)">
                <div class="profile-edit-container">
                    <div class="mensaje-info">
                      Enviar estos documentos: 
                      <b>XML y PDF.</b>
                    </div>
                    <div class="custom-form">
                              <label>Correo Electrónico *</label>
                              <input type="text" id="enviarcorreo_email"/>
                    </div>
                </div>
                <div class="profile-edit-container">
                    <div class="custom-form">
                        <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;">Enviar</button>
                    </div>
                </div> 
            </form> 
            </div>
            </div>
            <div class="mx-modal-cuerpo" id="contenido-confirmar-enviarcorreo"></div>
        </div>
    </div>
</div>
<!--  fin modal enviarcorreo --> 
<script>
  modal({click:'#modal-enviarcorreo'});
  function enviarcorreo(){
      $('#contenido-enviarcorreo').css('display','block'); 
      $('#contenido-confirmar-enviarcorreo').html(''); 
      $('#enviarcorreo_email').val(''); 
      removecarga({input:'#mx-carga-enviarcorreo'});
  }
</script>
