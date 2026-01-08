<form action="javascript:;" 
      onsubmit="callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamomorasolicitud',
            method: 'POST',
            carga:  '#carga-mora',
            data:   {
                view: 'registrar',
                idprestamo_credito: {{$s_prestamo_credito->id}},
                hastacuota: {{$s_prestamo_credito->numerocuota}}
            }
        },
        function(resultado){
            location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomorasolicitud') }}';
        }, this)">
  <div class="col-sm-6">
      <div class="row">
        <div class="col-sm-6">
              <label>Total de Moras</label>
              <input type="text" id="cuotas_total_mora" value="0.00"disabled>
              <label>Total Mora a Pagar</label>
              <input type="text" id="total_moraapagar" value="0.00"disabled>
        </div>
        <div class="col-sm-6">
              <label>Mora Pendiente</label>
              <input type="text" id="cuotas_total_mora_pendiente" value="0.00" style="background-color: #ffb0b0;border-color: #ff1f44;" disabled>
              <label>Mora Aprobado</label>
              <input type="text" id="cuotas_total_mora_aprobado" value="0.00" style="background-color: #b7fec1;border-color: #11c529;" disabled>
        </div>
      </div>
      <div id="cont-mora1" >
      <div class="list-single-main-wrapper fl-wrap">
          <div class="breadcrumbs gradient-bg fl-wrap">
            <span>Solicitar</span>
          </div>
      </div>
          <div class="row">
            <div class="col-sm-6">
                  <label>Mora a Solicitar *</label>
                  <input type="number" id="moradescuento" placeholder="0.00" min="0" step="0.01" disabled>
                  <label>Motivo de descuento *</label>
                  <textarea id="moradescuento_detalle" style="height:85px;" onkeyup="texto_mayucula(this)" disabled></textarea>
            </div>
            <div class="col-sm-6">
                  <label>Foto de sustento *</label>
                  <div class="fuzone" id="cont-imagendocumento">
                      <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                      <input type="file" class="upload" id="imagendocumento">
                  </div>
                  <div id="resultado-imagendocumento" style="display: none;"></div>
            </div>
          </div>
          <button type="submit" class="btn mx-btn-post" style="margin-bottom: 5px;">Solicitar Descuento de Mora</button>
      </div>
      <!--div id="cont-mora2" style="display:none;">
          <div class="mensaje-warning">
              <b>El Crédito ya tiene una mora para descontar!!</b><br>          
          </div>
      </div-->
  </div>  
</form>

<div class="col-sm-6">
    <div id="cont-cobranzapendiente"></div>
</div>

<style>

  #cont-imagendocumento {
      height:224px;
  }
  #resultado-imagendocumento {
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center;
      height:224px;
      width:100%;
      background-color: #eae7e7;
      border-radius: 5px;
      border: 1px solid #aaa;
      float: left;
      margin-bottom: 10px;
  }
  #resultado-imagendocumento-cerrar {
      margin-top:10px;
      margin-left:10px;
      font-size:18px;
      background-color:#c12e2e;
      padding:0px;
      padding-left:9px;
      padding-right:9px;
      padding-bottom: 3px;
      border-radius:15px;
      color:#fff;
      font-weight:bold;
      cursor:pointer;
      position: absolute;
      z-index: 100;
  }
</style>
<!-- Detalle  -->
<script>
  

    let time_moradescuento;
    document.getElementById("moradescuento").addEventListener('keydown', () => {
      clearTimeout(time_moradescuento)
      time_moradescuento = setTimeout(() => {
        mostrar_cuotapendiente();
        clearTimeout(time_moradescuento)
      },700)
    });
  
     mostrar_cuotapendiente();
    function mostrar_cuotapendiente(){
        $.ajax({
            url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomorasolicitud/create') }}",
            type: 'GET',
            data: {
                view: 'cuotapendiente',
                idcredito: {{$s_prestamo_credito->id}},
                moradescuento: $('#moradescuento').val(),
                hastacuota: '{{ $s_prestamo_credito->numerocuota }}',
                checked_moradescuento: 'on'
            },
            beforeSend: function (data) {
                load('#cont-cobranzapendiente');
            },
            success: function (res) {
                $('#cont-cobranzapendiente').html(res);
            }
        });
    }
  
  //documento
  subir_archivo({
          input:"#imagendocumento"
      }, 
      function(resultado){ 
           mostrar_documento(resultado.archivo);
      }
  );
  
  function mostrar_documento(archivo){
          $('#cont-imagendocumento').css('display','none');
          $('#resultado-imagendocumento').attr('style','background-image: url('+archivo+')');
          $('#resultado-imagendocumento').append('<div id="resultado-imagendocumento-cerrar" onclick="limpiar_documento()">x</div>');
  }
  function limpiar_documento(){
          $('#cont-imagendocumento').css('display','block');
          $('#resultado-imagendocumento').removeAttr('style');
          $('#resultado-imagendocumento').css('display','none');
          $('#resultado-imagendocumento').html('');
          $('#imagendocumento').val(null);
          $('#imagendocumento_anterior').val('');
  }
</script>