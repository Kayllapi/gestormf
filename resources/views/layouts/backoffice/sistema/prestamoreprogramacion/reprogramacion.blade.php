<form action="javascript:;" 
      onsubmit="callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamoreprogramacion',
            method: 'POST',
            data:   {
                view: 'registrar',
                idprestamo_credito: $('#idcliente').val()
            }
        },
        function(resultado){
          location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoreprogramacion') }}';
        }, this)">
<div class="col-sm-6">
    <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
          <span>Reprogramar Crédito</span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label>Frecuencia</label>
            <select id="idfrecuencia" disabled>
                <option></option>
                @foreach($frecuencias as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
            </select>
            <label>Fecha de Inicio *</label>
            <input type="date" id="fechainicio" onchange="mostrar_credito_reprogramado()"/>
              <label>Motivo de reprogramación *</label>
              <textarea id="reprogramar_motivo" style="height:85px;" onkeyup="texto_mayucula(this)"></textarea>
        </div>
        <div class="col-md-6">
                  <label>Foto de sustento *</label>
                  <div class="fuzone" id="cont-reprogramar_documento">
                      <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                      <input type="file" class="upload" id="reprogramar_documento">
                  </div>
                  <div id="resultado-reprogramar_documento" style="display: none;"></div>
        </div>
    </div> 
    <button type="submit" class="btn mx-btn-post" style="margin-bottom: 5px;">Guardar Reprogramación</button>
</div>
<div class="col-sm-6">
    <div id="cont-credito_reprogramado"></div>
</div>  
</form>
<style>

  #cont-reprogramar_documento {
      height:223px;
  }
  #resultado-reprogramar_documento {
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center;
      height:223px;
      width:100%;
      background-color: #eae7e7;
      border-radius: 5px;
      border: 1px solid #aaa;
      float: left;
      margin-bottom: 10px;
  }
  #resultado-reprogramar_documento-cerrar {
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
<script>
    tab({click:'#tab-credito'});
  
    $('#idfrecuencia').select2({
        placeholder: '-- Seleccionar Frecuencia --',
        minimumResultsForSearch: -1,
    }).val({{ $s_prestamo_credito->idprestamo_frecuencia }}).trigger('change');
  
    mostrar_credito_reprogramado();
  
    function mostrar_credito_reprogramado(){
        $.ajax({
            url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoreprogramacion/'.$s_prestamo_credito->id.'/edit') }}",
            type: 'GET',
            data: {
                view: 'credito_reprogramado',
                fechainicio: $('#fechainicio').val()
            },
            beforeSend: function (data) {
                load('#cont-credito_reprogramado');
            },
            success: function (res) {
                $('#cont-credito_reprogramado').html(res);
            }
        });
    }
  
  //documento
  subir_archivo({
          input:"#reprogramar_documento"
      }, 
      function(resultado){ 
           mostrar_documento(resultado.archivo);
      }
  );
  
  function mostrar_documento(archivo){
          $('#cont-reprogramar_documento').css('display','none');
          $('#resultado-reprogramar_documento').attr('style','background-image: url('+archivo+')');
          $('#resultado-reprogramar_documento').append('<div id="resultado-reprogramar_documento-cerrar" onclick="limpiar_documento()">x</div>');
  }
  function limpiar_documento(){
          $('#cont-reprogramar_documento').css('display','block');
          $('#resultado-reprogramar_documento').removeAttr('style');
          $('#resultado-reprogramar_documento').css('display','none');
          $('#resultado-reprogramar_documento').html('');
          $('#reprogramar_documento').val(null);
          $('#reprogramar_documento_anterior').val('');
  }
</script>