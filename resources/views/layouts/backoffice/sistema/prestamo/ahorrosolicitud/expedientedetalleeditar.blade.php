<div id="carga-ahorro">
    <div class="box" style="width: 300px;">
      <input class="Switcher__checkbox sr-only" id="idestadoexpediente" type="checkbox" <?php echo ($prestamoahorro->estadoexpediente=='si' or $prestamoahorro->estadoexpediente=='')? 'checked="checked"':'' ?>>
      <label class="Switcher" for="idestadoexpediente">
        <div class="Switcher__trigger" data-value="Sin Expediente"></div>
        <div class="Switcher__trigger" data-value="Con Expediente"></div>
      </label>
    </div>
    <div id="cont-estadoexpediente1" <?php echo ($prestamoahorro->estadoexpediente=='si' or $prestamoahorro->estadoexpediente=='')=='si'? 'style="display:block;"': 'style="display:none;"' ?>>
        <div id="resultado-ahorro"></div>   
        <div class="tabs-container" id="tab-detalle-cliente-editar">
            <ul class="tabs-menu">
              <li class="current"><a href="#tab-detalle-cliente-editar-1">Domicilio</a></li>
              <li><a href="#tab-detalle-cliente-editar-2">Laboral</a></li>
              <li><a href="#tab-detalle-cliente-editar-6">Resultado</a></li>
            </ul>
            <div class="tab">
              <div id="tab-detalle-cliente-editar-1" class="tab-content" style="display: block;">
                <div id="cont-domicilios"></div>
              </div>
              <div id="tab-detalle-cliente-editar-2" class="tab-content" style="display: none;">
                <div id="cont-laborales"></div>
              </div>
              <div id="tab-detalle-cliente-editar-6" class="tab-content" style="display: none;">
                <div id="cont-resultado"></div>
              </div>
            </div>
          </div> 
    </div>
    <div id="cont-estadoexpediente2" <?php echo ($prestamoahorro->estadoexpediente=='si' or $prestamoahorro->estadoexpediente=='')=='si'? 'style="display:none;"': 'style="display:block;"' ?>>
        <div class="resultado-aprobado">AHORRO APROBADO</div>
    </div> 
</div>            
<!-- Tabulador de pestañas -->
<script>
  tab({click:'#tab-detalle-cliente-editar'});
  $("#idestadoexpediente").change(function() {
      var idestadoexpediente = $("#idestadoexpediente:checked").val();
      if(idestadoexpediente=='on'){
          $('#cont-estadoexpediente1').css('display','block');
          $('#cont-estadoexpediente2').css('display','none');
      }else{
          $('#cont-estadoexpediente1').css('display','none');
          $('#cont-estadoexpediente2').css('display','block');
      }
      guardar_ahorroexpediente(idestadoexpediente);
  });
  
  function guardar_ahorroexpediente(idestadoexpediente){
      //$( ".form-guardar_ahorroexpediente" ).submit();
    
      callback({
          route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamo/ahorrosolicitud/{{$prestamoahorro->id}}',
          method: 'PUT',
          carga: '#carga-ahorro',
          data:   {
              view: 'editar-expediente',
              idestadoexpediente: idestadoexpediente
          }
      },
      function(resultado){
          removecarga({input:'#carga-ahorro'});
          //location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrosolicitud') }}';
      })
  }
</script>      
<script>
    domicilio_edit();
    function domicilio_edit(){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorrosolicitud/{{ $prestamoahorro->id }}/edit?view=domicilioedit',result:'#cont-domicilios'});
    }
    laboral_edit();
    function laboral_edit(){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorrosolicitud/{{ $prestamoahorro->id }}/edit?view=laboraledit',result:'#cont-laborales'});
    }
    resultado_index();
    function resultado_index(){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorrosolicitud/{{ $prestamoahorro->id }}/edit?view=resultado',result:'#cont-resultado'});
    }
</script>   